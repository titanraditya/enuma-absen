<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::index', ['filter' => 'login']);
$routes->post('/login', 'Auth::login');
$routes->get('/profile', 'Auth::profile', ['filter' => 'auth']);
$routes->post('/profile/changepass', 'Auth::changepass', ['filter' => 'auth']);
$routes->get('/logout', 'Auth::logout');

//dashboard
$routes->get('/dashboard', 'Dashboard::index', ['filter' => 'auth']);

//log
$routes->group('log', ['filter' => 'user-pengawas'], function($routes) {
    $routes->get('/', 'Log::index');
    $routes->post('/', 'Log::store');
    $routes->get('getdata', 'Log::getData');
});
$routes->get('log/getdata/(:any)', 'Log::getDataId/$1',['filter' => 'auth']);

//category
$routes->group('category', ['filter' => 'admin-pengawas'], function($routes) {
    $routes->get('/', 'Category::index');
    $routes->get('add', 'Category::add');
    $routes->post('add', 'Category::addpost');
    $routes->post('addpengawas', 'Category::addpengawas');
    $routes->post('deletepengawas', 'Category::deletepengawas');
    $routes->get('detail/(:any)', 'Category::detail/$1');
    $routes->get('user/detail/(:any)', 'User::detail/$1');
    $routes->post('user/ijin', 'Attendance::ijin');
    $routes->post('user/cancelijin', 'Attendance::cancelijin');
    $routes->get('edit/(:any)', 'Category::edit/$1');
    $routes->post('edit/(:any)', 'Category::editpost/$1');
    $routes->post('delete', 'Category::delete');
});

//schedule
$routes->group('schedule', ['filter' => 'admin-pengawas'], function($routes) {
    $routes->post('store', 'Schedule::store');
    $routes->get('check/(:any)', 'Schedule::check/$1');
    $routes->post('edit', 'Schedule::editpost');
    $routes->post('delete', 'Schedule::delete');
});

//attendance
$routes->group('attendance', ['filter' => 'auth'], function($routes) {
    $routes->get('data/(:any)/(:any)', 'Attendance::userattendance/$1/$2/$3');
    $routes->post('printxls', 'Attendance::printxls');
    $routes->post('printpdf', 'Attendance::printpdf');
});

//user
$routes->group('user', ['filter' => 'admin'], function($routes) {
    $routes->get('/', 'User::index');
    $routes->get('add', 'User::add');
    $routes->post('add', 'User::addPost');
    $routes->get('edit/(:any)', 'User::edit/$1');
    $routes->post('edit/(:any)', 'User::editpost/$1');
    $routes->post('delete', 'User::delete');
});

//admin
$routes->group('admin', ['filter' => 'admin'], function($routes) {
    $routes->get('/', 'Admin::index');
    $routes->get('add', 'Admin::add');
    $routes->post('add', 'Admin::addpost');
    $routes->get('edit/(:any)', 'Admin::edit/$1');
    $routes->post('edit/(:any)', 'Admin::editpost/$1');
    $routes->post('delete', 'Admin::delete');
});

//API
$routes->get('/api/users/(:num)', 'Api::user/$1', ['filter' => 'iotAuth']);
$routes->post('/api/attendance', 'Api::storeAttendance', ['filter' =>'iotAuth']);
