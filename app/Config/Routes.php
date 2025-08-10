<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Service::index');
$routes->get('/model/settings', 'Service::settings');
$routes->get('/model/fieldlist/(:num)', 'Service::fieldList/$1');
$routes->get('/model/data/(:any)', 'Service::data/$1');
$routes->get('/model/rowdel/(:any)/(:num)', 'Service::rowDel/$1/$2');
$routes->post('/model/rowupdate/(:any)/(:num)', 'Service::rowUpdate/$1/$2');
$routes->get('/model/rowform/(:any)/(:num)', 'Service::rowForm/$1/$2');
$routes->post('/model/modeljson', 'Service::modelJson');
$routes->post('/model/uploadfile', 'Service::uploadFile');
$routes->get('/login', 'Service::login');
$routes->get('/users', 'Service::users');
$routes->get('/backup', 'Service::backup');
$routes->get('/systemConfig', 'Service::systemConfig');
$routes->get('/noPermission', 'Service::noPermission');

