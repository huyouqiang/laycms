<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Model::index');
$routes->get('/model/index', 'Model::index');
$routes->get('/model/settings', 'Model::settings');
$routes->get('/model/fieldlist/(:num)', 'Model::fieldList/$1');
$routes->get('/model/data/(:any)', 'Model::data/$1');
$routes->get('/model/rowdel/(:any)/(:num)', 'Model::rowDel/$1/$2');
$routes->post('/model/rowupdate/(:any)/(:num)', 'Model::rowUpdate/$1/$2');
$routes->get('/model/rowform/(:any)/(:num)', 'Model::rowForm/$1/$2');
$routes->post('/model/modeljson', 'Model::modelJson');
$routes->post('/model/uploadfile', 'Model::uploadFile');

