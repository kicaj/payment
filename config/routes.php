<?php
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

return static function (RouteBuilder $routes) {
    $routes->plugin('Payment', function (RouteBuilder $routes) {
        $routes->fallbacks(DashedRoute::class);
    });
};
