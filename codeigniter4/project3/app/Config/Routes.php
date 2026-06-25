<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/about', 'Page::about');
$routes->get('/contact', 'Page::contact');
$routes->get('/faqs', 'Page::faqs');

$routes->get('/post', 'Post::index');
$routes->get('/post/(:any)', 'Post::viewPost/$1');
$routes->get('/login', 'AuthUi::login');
$routes->get('/register', 'AuthUi::register');

$routes->group('admin', function($routes){
$routes->get('post', 'PostAdmin::index', ['filter' => 'login']);
	$routes->get('post/(:segment)/preview', 'PostAdmin::preview/$1');
	$routes->post('post/(:segment)/status', 'PostAdmin::toggleStatus/$1');
	$routes->post('post/(:segment)/delete', 'PostAdmin::delete/$1');
	$routes->add('post/new', 'PostAdmin::create');
	$routes->add('post/(:segment)/edit', 'PostAdmin::edit/$1');
});
