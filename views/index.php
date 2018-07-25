
<div>
    <ul class="uk-breadcrumb">
        <li><a href="@route('/settings')">@lang('Settings')</a></li>
        <li class="uk-active"><span>@lang('Layout Components')</span></li>
    </ul>
</div>

<div riot-view>

    <div class="uk-margin uk-clearfix" if="{ App.Utils.count(components) && !component }">

        <div class="uk-form-icon uk-form uk-text-muted">

            <i class="uk-icon-filter"></i>
            <input class="uk-form-large uk-form-blank" type="text" ref="txtfilter" placeholder="@lang('Filter components...')" onkeyup="{ updatefilter }">

        </div>

        <div class="uk-float-right">
            <a class="uk-button uk-button-large uk-button-primary uk-width-1-1" onclick="{addComponent}"><i class="uk-icon-plus-circle uk-icon-justify"></i>  @lang('Component')</a>
        </div>

    </div>


    <div class="uk-width-medium-1-1 uk-viewport-height-1-3 uk-container-center uk-text-center uk-flex uk-flex-middle uk-flex-center" if="{ !App.Utils.count(components) && !component }">

        <div class="uk-animation-scale">

            <p>
                <img class="uk-svg-adjust uk-text-muted" src="@url('assets:app/media/icons/component.svg')" width="80" height="80" alt="Layout Components" data-uk-svg />
            </p>
            <hr>
            <span class="uk-text-large"><strong>@lang('No Components').</strong>
            <a onclick="{addComponent}">@lang('Create one')</a></span>

        </div>

    </div>

    <div class="" if="{App.Utils.count(components) && !component}">

        <div class="uk-tab uk-margin uk-flex uk-flex-center uk-noselect" if="{groups.length}">
            <li class="{!group && 'uk-active'}"><a onclick="{selectGroup}">@lang('All')</a></li>
            <li class="{g==group && 'uk-active'}" each="{g in groups}"><a onclick="{parent.selectGroup}">{g}</a></li>
        </div>


        <div class="uk-grid uk-grid-small2 uk-grid-match uk-grid-width-medium-1-4 uk-animation-fade">

            <div class="uk-grid-margin" each="{ comp in components}" show="{ infilter(comp.name, comp.meta) }">
                <div class="uk-panel uk-panel-box uk-panel-card uk-flex uk-flex-middle">
                    <div class="uk-margin-small-right">
                        <img riot-src="{ '@url('assets:app/media/icons/')' + (comp.meta.icon || 'component.svg') }" width="20" height="20" alt="Layout Component" />
                    </div>
                    <div class="uk-flex-item-1 uk-margin-small-right">
                        <a class="uk-link-muted" onclick="{parent.editComponent}">{ comp.meta.label || comp.name}</a>
                    </div>
                    <span class="uk-badge uk-margin-small-right" show="{comp.meta.group}">{comp.meta.group}</span>
                    <a onclick="{parent.removeComponent}"><i class="uk-icon-trash-o uk-text-danger"></i></a>
                </div>
            </div>

        </div>

    </div>


    <div class="uk-width-medium-2-3 uk-container-center uk-margin-top {component && 'uk-animation-slide-bottom'}" if="{component}">

        <form class="uk-form" onsubmit="{ save }">

            <h2 class="uk-text-bold uk-flex uk-flex-middle">
                <img class="uk-margin-small-right" riot-src="{ '@url('assets:app/media/icons/')' + (component.meta.icon || 'component.svg') }" width="25" height="25" alt="Layout Component" />
                <span show="{component.mode=='add'}">@lang('Add Component')</span>
                <span show="{component.mode=='edit'}">@lang('Edit Component')</span>
            </h2>

            <div class="uk-margin">
                <input class="uk-flex-item-1 uk-form-large uk-form-blank uk-text-primary" type="text" bind="component.name" pattern="[a-zA-Z0-9_]+" required placeholder="@lang('Component name')">
            </div>

            <div class="uk-grid uk-grid-width-medium-1-2">
                <div>
                    <label class="uk-text-small uk-text-bold">@lang('Icon')</label>
                    <div data-uk-dropdown="pos:'right-center', mode:'click'">
                        <a>
                            <img class="uk-display-block uk-margin uk-container-center" riot-src="{ '@url('assets:app/media/icons/')' + (component.meta.icon || 'component.svg') }" alt="icon" width="30" />
                        </a>
                        <div class="uk-dropdown uk-dropdown-scrollable uk-dropdown-width-2">
                            <div class="uk-grid uk-grid-gutter">
                                <div>
                                    <a class="uk-dropdown-close" onclick="{ selectIcon }" icon=""><img src="@url('collections:icon.svg')" width="30" icon=""></a>
                                </div>
                                @foreach($app->helper("fs")->ls('*.svg', 'assets:app/media/icons') as $icon)
                                <div>
                                    <a class="uk-dropdown-close" onclick="{ selectIcon }" icon="{{ $icon->getFilename() }}"><img src="@url($icon->getRealPath())" width="30" icon="{{ $icon->getFilename() }}"></a>
                                </div>
                                @endforeach
                            </div>
                       </div>
                    </div>
                </div>
            </div>

            <div class="uk-grid uk-grid-width-medium-1-2">
                <div>
                    <label class="uk-text-small uk-text-bold">@lang('Label')</label>
                    <input class="uk-width-1-1 uk-margin-small-top" type="text" bind="component.meta.label">
                </div>

                <div>
                    <label class="uk-text-small uk-text-bold">@lang('Group')</label>
                    <input class="uk-width-1-1 uk-margin-small-top" type="text" bind="component.meta.group">
                </div>
            </div>


            <div class="uk-margin">
                <label class="uk-text-small uk-text-bold">@lang('Fields')</label>
                <cp-fieldsmanager class="uk-display-block uk-margin-small-top" bind="component.meta.fields" localize="{false}"></cp-fieldsmanager>
            </div>

            <div class="uk-margin-large-top">
                <button class="uk-button uk-button-large uk-button-primary uk-margin-right">@lang('Save')</button>
                <a onclick="{closeEdit}">@lang('Cancel')</a>
            </div>
        </form>
    </div>


    <script type="view/script">

        var $this = this;

        this.mixin(RiotBindMixin);

        this.$components = {{ json_encode($components) }};
        this.components = [];
        this.groups = [];
        this.group = null;
        this.component = null;

        this.on('mount', function() {

            this.getComponents();

            App.$(this.root).on('animationend', function(e) {
                e.target.classList.remove('uk-animation-slide-bottom')
            });

            this.update();
        });

        addComponent() {

            this.component = {
                mode: 'add',
                name: '',
                meta: {
                    label: '',
                    fields: []
                }
            };

        }

        editComponent(e) {

            var c = e.item.comp;

            this.component = {
                mode: 'edit',
                oldname: c.name,
                name: c.name,
                meta: App.$.extend({}, c.meta)
            };
        }

        removeComponent(e) {

            var name = e.item.comp.name;

            App.ui.confirm('Are you sure?', function() {

                delete $this.$components[name];

                $this.store();
                $this.getComponents();
                $this.update();
            });
        }

        closeEdit() {
            this.component = null;
        }

        selectGroup(e) {
            this.group = e.item && e.item.g
        }

        updatefilter(e) {

        }

        infilter(name, meta, value, label) {

            if (this.group && meta.group !== this.group) {
                return false;
            }

            if (!this.refs.txtfilter.value) {
                return true;
            }

            label = meta.label || '';
            value = this.refs.txtfilter.value.toLowerCase();

            return [name.toLowerCase(), label.toLowerCase()].join(' ').indexOf(value) !== -1;
        }

        selectIcon(e) {
            this.component.meta.icon = e.target.getAttribute('icon');
        }

        save(e) {

            e.preventDefault();

            if (!this.component.meta.fields.length) {
                App.ui.notify('No fields defined', 'danger');
                return;
            }

            this.$components[this.component.name] = this.component.meta;

            if (this.component.mode == 'edit' && this.component.name != this.component.oldname) {
                delete this.$components[this.component.oldname];
            }

            this.getComponents();

            $this.component = null;

            this.store();
        }

        store() {

            App.request('/layout-components/store',{components:this.$components}).then(function() {
                App.ui.notify('Components updated', 'success');
            });
        }


        getComponents() {

            this.groups = [];
            this.components = [];

            Object.keys(this.$components).sort().forEach(function(name){

                if ($this.$components[name].group) {
                    $this.groups.push($this.$components[name].group);
                }

                $this.components.push({
                    name: name,
                    meta: $this.$components[name]
                });
            });

            this.groups = _.uniq(this.groups);

        }


    </script>

</div>
