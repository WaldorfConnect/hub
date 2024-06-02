<?php

use App\Filters\AdminFilter;
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

$routes->get('/user/profile', 'UserProfileController::profile', ['filter' => LoggedInFilter::class]);
$routes->post('/user/profile', 'UserProfileController::handleProfile', ['filter' => LoggedInFilter::class]);
$routes->post('/user/profile/resend', 'UserProfileController::handleProfileResendConfirmationEmail', ['filter' => LoggedInFilter::class]);
$routes->get('/user/confirm', 'UserController::handleConfirm');

$routes->get('/user/settings', 'UserSettingsController::settings', ['filter' => LoggedInFilter::class]);
$routes->post('/user/settings', 'UserSettingsController::handleSettings', ['filter' => LoggedInFilter::class]);

$routes->get('/user/security', 'UserSecurityController::security', ['filter' => LoggedInFilter::class]);
$routes->post('/user/security', 'UserSecurityController::handleSecurity', ['filter' => LoggedInFilter::class]);
$routes->post('/user/security/totp', 'UserSecurityController::handleTOTPEnable', ['filter' => LoggedInFilter::class]);
$routes->get('/user/security/reset_password', 'UserSecurityController::resetPassword', ['filter' => LoggedOutFilter::class]);
$routes->post('/user/security/reset_password', 'UserSecurityController::handleResetPassword', ['filter' => LoggedOutFilter::class]);

$routes->get('/organisations', 'OrganisationController::list', ['filter' => LoggedInFilter::class]);

$routes->get('/organisation/(:num)', 'OrganisationController::organisation/$1', ['filter' => LoggedInFilter::class]);
$routes->post('/organisation/(:num)/join', 'OrganisationController::handleJoin/$1', ['filter' => LoggedInFilter::class]);
$routes->post('/organisation/(:num)/leave', 'OrganisationController::handleLeave/$1', ['filter' => LoggedInFilter::class]);
$routes->post('/organisation/(:num)/add_member', 'OrganisationController::handleAddMember/$1', ['filter' => LoggedInFilter::class]);
$routes->post('/organisation/(:num)/add_workgroup', 'OrganisationController::handleAddWorkgroup/$1', ['filter' => LoggedInFilter::class]);
$routes->get('/organisation/(:num)/edit', 'OrganisationController::edit/$1', ['filter' => LoggedInFilter::class]);
$routes->post('/organisation/(:num)/edit', 'OrganisationController::handleEdit/$1', ['filter' => LoggedInFilter::class]);
$routes->post('/organisation/(:num)/accept', 'OrganisationController::handleAcceptJoin/$1', ['filter' => LoggedInFilter::class]);
$routes->post('/organisation/(:num)/deny', 'OrganisationController::handleDenyJoin/$1', ['filter' => LoggedInFilter::class]);
$routes->post('/organisation/(:num)/delete', 'OrganisationController::handleDelete/$1', ['filter' => LoggedInFilter::class]);
$routes->post('/organisation/(:num)/membership_status', 'OrganisationController::handleChangeMembershipStatus/$1', ['filter' => LoggedInFilter::class]);
$routes->post('/organisation/(:num)/kick_user', 'OrganisationController::handleKickUser/$1', ['filter' => LoggedInFilter::class]);

$routes->get('/admin', 'AdminController::index', ['filter' => AdminFilter::class]);
$routes->get('/admin/debug', 'AdminController::debug', ['filter' => AdminFilter::class]);

$routes->get('/admin/users', 'AdminUserController::users', ['filter' => AdminFilter::class]);
$routes->post('/admin/user/activate', 'AdminUserController::activateUser', ['filter' => AdminFilter::class]);
$routes->post('/admin/user/deactivate', 'AdminUserController::deactivateUser', ['filter' => AdminFilter::class]);
$routes->post('/admin/user/accept', 'AdminUserController::acceptUser', ['filter' => AdminFilter::class]);
$routes->post('/admin/user/delete', 'AdminUserController::handleDeleteUser', ['filter' => AdminFilter::class]);
$routes->get('/admin/user/edit/(:num)', 'AdminUserController::editUser/$1', ['filter' => AdminFilter::class]);
$routes->post('/admin/user/edit', 'AdminUserController::handleEditUser', ['filter' => AdminFilter::class]);

$routes->get('/admin/organisations', 'AdminOrganisationController::organisations', ['filter' => AdminFilter::class]);
$routes->get('/admin/organisation/create', 'AdminOrganisationController::createOrganisation', ['filter' => AdminFilter::class]);
$routes->post('/admin/organisation/create', 'AdminOrganisationController::handleCreateOrganisation', ['filter' => AdminFilter::class]);
$routes->post('/admin/organisation/delete', 'AdminOrganisationController::handleDeleteOrganisation', ['filter' => AdminFilter::class]);

$routes->get('/admin/regions', 'AdminRegionController::regions', ['filter' => AdminFilter::class]);
$routes->get('/admin/region/create', 'AdminRegionController::createRegion', ['filter' => AdminFilter::class]);
$routes->post('/admin/region/create', 'AdminRegionController::handleCreateRegion', ['filter' => AdminFilter::class]);
$routes->post('/admin/region/delete', 'AdminRegionController::handleDeleteRegion', ['filter' => AdminFilter::class]);
$routes->get('/admin/region/edit/(:num)', 'AdminRegionController::editRegion/$1', ['filter' => AdminFilter::class]);
$routes->post('/admin/region/edit', 'AdminRegionController::handleEditRegion', ['filter' => AdminFilter::class]);

$routes->get('/oidc/authorize', 'OIDCController::authorize', ['filter' => LoggedInFilter::class]);
$routes->post('/oidc/access_token', 'OIDCController::accessToken');
$routes->get('/oidc/logout', 'OIDCController::logout');

$routes->get('/search', 'SearchController::index', ['filter' => LoggedInFilter::class]);

$routes->get('/notifications', 'NotificationController::index', ['filter' => LoggedInFilter::class]);
$routes->post('/notification/(:num)/delete', 'NotificationController::handleDelete/$1', ['filter' => LoggedInFilter::class]);

$routes->cli('/cron_mail', 'CronController::mail');
$routes->cli('/cron_notifications', 'CronController::notifications');
$routes->cli('/cron_ldap', 'CronController::ldap');
$routes->cli('/cron_nextcloud', 'CronController::nextcloud');