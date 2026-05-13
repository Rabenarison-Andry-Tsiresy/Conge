<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// =============================================================================
// PUBLIC
// =============================================================================

$routes->get('/',      'EmployeController::login');
$routes->get('login',  'EmployeController::login');
$routes->post('login', 'EmployeController::login');
$routes->get('logout', 'EmployeController::logout');

// =============================================================================
// EMPLOYÉ
// =============================================================================

$routes->group('employe', ['filter' => 'AuthFilter:employe'], function ($routes) {

    $routes->get('dashboard',  'EmployeController::consulterDemandes');

    // Demandes
    $routes->get('demandes',               'EmployeController::consulterDemandes');
    $routes->match(['get', 'post'], 'demandes/soumettre', 'EmployeController::soumettreDemande');
    $routes->post('demandes/(:num)/annuler', 'EmployeController::cancelDemande/$1');

    // Solde
    $routes->get('solde', 'EmployeController::getSoldeConge');

    // Profil
    $routes->match(['get', 'post'], 'profil', 'EmployeController::editProfil');
});

// =============================================================================
// RESPONSABLE RH
// =============================================================================

$routes->group('rh', ['filter' => 'AuthFilter:rh'], function ($routes) {

    $routes->get('dashboard', 'ResponsableRHController::consulterDemandes');

    // Demandes
    $routes->get('demandes',                        'ResponsableRHController::consulterDemandes');
    $routes->get('demandes/filtre',                 'ResponsableRHController::filtreDemande');
    $routes->post('demandes/(:num)/approuver',      'ResponsableRHController::approuverDemande/$1');
    $routes->post('demandes/(:num)/refuser',        'ResponsableRHController::refuserDemande/$1');

    // Soldes employés
    $routes->get('employe/(:num)/solde',            'ResponsableRHController::getSoldeEmploye/$1');
    $routes->post('employe/(:num)/solde/update',    'ResponsableRHController::updateSoldeConge/$1');
});

// =============================================================================
// ADMIN
// =============================================================================

$routes->group('admin', ['filter' => 'AuthFilter:admin'], function ($routes) {

    $routes->get('dashboard', 'AdminController::dashboard');

    // Employés
    $routes->get('employes',                            'AdminController::employes');
    $routes->match(['get', 'post'], 'employes/create',          'AdminController::createEmploye');
    $routes->match(['get', 'post'], 'employes/(:num)/edit',     'AdminController::updateEmploye/$1');
    $routes->post('employes/(:num)/deactivate',         'AdminController::deactivateEmploye/$1');
    $routes->post('employes/(:num)/delete',             'AdminController::deleteEmploye/$1');

    // Départements
    $routes->get('departements',                        'AdminController::departements');
    $routes->match(['get', 'post'], 'departements/create',      'AdminController::createDepartement');
    $routes->match(['get', 'post'], 'departements/(:num)/edit', 'AdminController::updateDepartement/$1');
    $routes->post('departements/(:num)/delete',         'AdminController::deleteDepartement/$1');

    // Types de congé
    $routes->get('types-conge',                         'AdminController::typesConge');
    $routes->match(['get', 'post'], 'types-conge/create',       'AdminController::createTypeConge');
    $routes->match(['get', 'post'], 'types-conge/(:num)/edit',  'AdminController::updateTypeConge/$1');
    $routes->post('types-conge/(:num)/delete',          'AdminController::deleteTypeConge/$1');
});