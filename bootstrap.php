<?php


// REST
if (COCKPIT_API_REQUEST) {

    $app->on('cockpit.rest.init', function($routes) {
        $routes['layout-components'] = 'LayoutComponents\\Controller\\RestApi';
    });
}

// ADMIN
if (COCKPIT_ADMIN && !COCKPIT_API_REQUEST) {

    include_once(__DIR__.'/admin.php');
}
