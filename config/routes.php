<?php

use Cake\Routing\Router;
use Cake\Routing\RouteBuilder;

Router::plugin('Payment', function (RouteBuilder $routes) {
    $routes->fallbacks();
});
