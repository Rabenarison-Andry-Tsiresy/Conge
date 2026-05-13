<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::index');

$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::attempt');
$routes->get('logout', 'AuthController::logout');

$routes->group('employe', static function (RouteCollection $routes): void {
	$routes->get('', 'EmployeController::dashboard');
	$routes->get('demandes', 'EmployeController::index');
	$routes->get('demandes/create', 'EmployeController::create');
	$routes->post('demandes', 'EmployeController::store');
	$routes->post('demandes/(:num)/cancel', 'EmployeController::cancel/$1');
});

$routes->group('rh', static function (RouteCollection $routes): void {
	$routes->get('', 'RhController::index');
	$routes->get('soldes', 'RhController::soldes');
	$routes->post('demandes/(:num)/approve', 'RhController::approve/$1');
	$routes->post('demandes/(:num)/refuse', 'RhController::refuse/$1');
});

$routes->group('admin', static function (RouteCollection $routes): void {
	$routes->get('', 'AdminController::dashboard');
	$routes->get('demandes', 'AdminController::demandes');
	$routes->post('demandes/(:num)/approve', 'RhController::approve/$1');
	$routes->post('demandes/(:num)/refuse', 'RhController::refuse/$1');
	$routes->get('employes', 'AdminController::employes');
	$routes->post('employes', 'AdminController::storeEmploye');
	$routes->get('departements', 'AdminController::departements');
	$routes->post('departements', 'AdminController::storeDepartement');
	$routes->get('types-conge', 'AdminController::typesConge');
	$routes->post('types-conge', 'AdminController::storeTypeConge');
	$routes->get('soldes', 'AdminController::soldes');
});
