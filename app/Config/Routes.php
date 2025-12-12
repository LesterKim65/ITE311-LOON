<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default routes
$routes->get('/', 'Home::index');
$routes->get('/home', 'Home::index');
$routes->get('/about', 'Home::about');
$routes->get('/contact', 'Home::contact');

$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::register');
// Support optional trailing slash
$routes->get('/register/', 'Auth::register');
$routes->post('/register/', 'Auth::register');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::login');
$routes->get('/dashboard', 'Auth::dashboard');
$routes->get('/logout', 'Auth::logout');

// Manage Students routes
$routes->get('/manage-students', 'Auth::manageStudents');
$routes->post('/update-student-status', 'Auth::updateStudentStatus');
$routes->post('/remove-student-from-course', 'Auth::removeStudentFromCourse');

// Enrollment Requests routes
$routes->get('/enrollment-requests', 'Auth::handleEnrollmentRequests');
$routes->post('/approve-enrollment', 'Auth::approveEnrollment');
$routes->post('/reject-enrollment', 'Auth::rejectEnrollment');

$routes->get('/courses', 'Course::index');
$routes->get('/courses/search', 'Course::search');
$routes->post('/courses/search', 'Course::search');

$routes->post('/course/enroll', 'Course::enroll');

$routes->get('/admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->post('/admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->get('/admin/course/(:num)/materials', 'Materials::view/$1');
$routes->get('/materials/delete/(:num)', 'Materials::delete/$1');
$routes->post('/materials/delete/(:num)', 'Materials::delete/$1');
$routes->delete('/materials/delete/(:num)', 'Materials::delete/$1');
$routes->get('/materials/download/(:num)', 'Materials::download/$1');
$routes->get('/materials/course/(:num)/materials', 'Materials::getMaterialsByCourse/$1');

// Debug routes
$routes->get('/debug/upload', 'Materials::debugUpload');
$routes->post('/debug/upload', 'Materials::debugUpload');
$routes->get('/simple-debug', 'Materials::simpleDebug');
$routes->post('/simple-debug', 'Materials::simpleDebug');

// TEMP debug route

// Notification routes
$routes->get('/notifications', 'Notifications::get');
$routes->post('/notifications/mark_read/(:num)', 'Notifications::mark_as_read/$1');

// Manage Users routes
$routes->get('/manage-users', 'ManageUsers::index');
$routes->post('/manage-users/add', 'ManageUsers::add');
$routes->post('/manage-users/update', 'ManageUsers::update');
$routes->post('/manage-users/delete', 'ManageUsers::delete');
$routes->post('/manage-users/restore', 'ManageUsers::restore');
$routes->post('/manage-users/change-role', 'ManageUsers::changeRole');
$routes->post('/manage-users/change-password', 'ManageUsers::changePassword');

// Assignment routes
$routes->get('/assignments', 'Assignments::list');
$routes->get('/assignments/create/(:num)', 'Assignments::create/$1');
$routes->post('/assignments/create/(:num)', 'Assignments::create/$1');
$routes->get('/assignments/course/(:num)', 'Assignments::listByCourse/$1');
$routes->get('/assignments/submit/(:num)', 'Assignments::submit/$1');
$routes->post('/assignments/submit/(:num)', 'Assignments::submit/$1');
$routes->get('/assignments/submissions/(:num)', 'Assignments::viewSubmissions/$1');
$routes->get('/assignments/grade/(:num)', 'Assignments::grade/$1');
$routes->post('/assignments/grade/(:num)', 'Assignments::grade/$1');
$routes->get('/assignments/download-attachment/(:num)', 'Assignments::downloadAttachment/$1');
$routes->get('/assignments/download-submission/(:num)', 'Assignments::downloadSubmission/$1');

// Admin Course Management routes
$routes->get('/admin/courses', 'AdminCourse::index');
$routes->get('/admin/courses/search', 'AdminCourse::search');
$routes->post('/admin/courses/search', 'AdminCourse::search');
$routes->post('/admin/courses/create', 'AdminCourse::create');
$routes->post('/admin/courses/update/(:num)', 'AdminCourse::update/$1');
$routes->get('/admin/courses/get/(:num)', 'AdminCourse::getCourse/$1');

// Admin Assignment Management routes
$routes->get('/admin/assignments', 'AdminAssignments::index');
