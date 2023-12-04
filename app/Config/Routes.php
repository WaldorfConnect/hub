<?php

use App\Filters\AdminFilter;
use App\Filters\GlobalAdminFilter;
use App\Filters\LoggedInFilter;
use App\Filters\LoggedOutFilter;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'IndexController::index', ['filter' => LoggedInFilter::class]);

$routes->get('/login', 'AuthenticationController::login', ['filter' => LoggedOutFilter::class]);
$routes->post('/login', 'AuthenticationController::handleLogin', ['filter' => LoggedOutFilter::class]);

$routes->get('/logout', 'AuthenticationController::logout', ['filter' => LoggedInFilter::class]);

$routes->get('/register', 'AuthenticationController::register', ['filter' => LoggedOutFilter::class]);
$routes->post('/register', 'AuthenticationController::handleRegister', ['filter' => LoggedOutFilter::class]);
$routes->post('/register/resend', 'AuthenticationController::handleRegisterResendConfirmationEmail', ['filter' => LoggedOutFilter::class]);

$routes->get('/user/reset_password', 'UserController::resetPassword', ['filter' => LoggedOutFilter::class]);
$routes->post('/user/reset_password', 'UserController::handleResetPassword', ['filter' => LoggedOutFilter::class]);

$routes->get('/user/profile', 'UserController::profile', ['filter' => LoggedInFilter::class]);
$routes->post('/user/profile', 'UserController::handleProfile', ['filter' => LoggedInFilter::class]);
$routes->post('/user/profile/resend', 'UserController::handleProfileResendConfirmationEmail', ['filter' => LoggedInFilter::class]);

$routes->get('/user/confirm', 'UserController::handleConfirm');

$routes->get('/organisations', 'OrganisationController::list', ['filter' => LoggedInFilter::class]);
$routes->get('/organisation/(:num)', 'OrganisationController::organisation/$1', ['filter' => LoggedInFilter::class]);

$routes->post('/organisation/join', 'OrganisationController::handleJoin', ['filter' => LoggedInFilter::class]);
$routes->post('/organisation/accept', 'OrganisationController::handleAcceptJoin', ['filter' => LoggedInFilter::class]);
$routes->post('/organisation/deny', 'OrganisationController::handleDenyJoin', ['filter' => LoggedInFilter::class]);
$routes->post('/organisation/change_user_status', 'OrganisationController::handleChangeUserStatus', ['filter' => LoggedInFilter::class]);
$routes->post('/organisation/kick_user', 'OrganisationController::handleKickUser', ['filter' => LoggedInFilter::class]);

$routes->get('/admin', 'AdminController::index', ['filter' => AdminFilter::class]);
$routes->get('/admin/debug', 'AdminController::debug', ['filter' => GlobalAdminFilter::class]);

$routes->get('/admin/users', 'AdminController::users', ['filter' => AdminFilter::class]);
$routes->post('/admin/user/accept', 'AdminController::acceptUser', ['filter' => AdminFilter::class]);
$routes->post('/admin/user/deny', 'AdminController::denyUser', ['filter' => AdminFilter::class]);
$routes->post('/admin/user/delete', 'AdminController::handleDeleteUser', ['filter' => AdminFilter::class]);
$routes->get('/admin/user/edit/(:num)', 'AdminController::editUser/$1', ['filter' => AdminFilter::class]);
$routes->post('/admin/user/edit', 'AdminController::handleEditUser', ['filter' => AdminFilter::class]);

$routes->get('/admin/organisations', 'AdminController::organisations', ['filter' => AdminFilter::class]);
$routes->get('/admin/organisation/create', 'AdminController::createOrganisation', ['filter' => AdminFilter::class]);
$routes->post('/admin/organisation/create', 'AdminController::handleCreateOrganisation', ['filter' => AdminFilter::class]);
$routes->post('/admin/organisation/delete', 'AdminController::handleDeleteOrganisation', ['filter' => AdminFilter::class]);
$routes->get('/admin/organisation/edit/(:num)', 'AdminController::editOrganisation/$1', ['filter' => AdminFilter::class]);
$routes->post('/admin/organisation/edit', 'AdminController::handleEditOrganisation', ['filter' => AdminFilter::class]);

$routes->get('/admin/regions', 'AdminController::regions', ['filter' => GlobalAdminFilter::class]);
$routes->get('/admin/region/create', 'AdminController::createRegion', ['filter' => GlobalAdminFilter::class]);
$routes->post('/admin/region/create', 'AdminController::handleCreateRegion', ['filter' => GlobalAdminFilter::class]);
$routes->post('/admin/region/delete', 'AdminController::handleDeleteRegion', ['filter' => GlobalAdminFilter::class]);
$routes->get('/admin/region/edit/(:num)', 'AdminController::editRegion/$1', ['filter' => GlobalAdminFilter::class]);
$routes->post('/admin/region/edit', 'AdminController::handleEditRegion', ['filter' => GlobalAdminFilter::class]);

$routes->cli('/cron', 'CronController::index');