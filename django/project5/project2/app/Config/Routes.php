<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->set404Override(function () {
    echo view('errors/not_found');
});

$routes->get('/portofolio', 'Portofolio::index');
