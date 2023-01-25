<?php
/**
 * @author: Kevin Price
 * @date: Jan 12, 2023
 * @filename: index.php
 * @description: index of routes for F3 MVC
 */

// output buffering
ob_start();

// errors
ini_set('display_errors', 1);
error_reporting(E_ALL);

// start session after loading f3
require_once('vendor/autoload.php');
session_start();

// f3 init
$f3 = Base::instance();
$controller = new Controller($f3);


/**
 * Home route GET
 */
$f3->route('GET /', function()
{
    $GLOBALS['controller']->route_home();
});


/**
 * Home route POST
 */
$f3->route('POST /create-new', function()
{
    $GLOBALS['controller']->route_create_new();
});


/**
 * Route for plan entry and viewing
 */
$f3->route('GET|POST /@token', function()
{
    $GLOBALS['controller']->route_plan();
});


/**
 * Route for creating / navigating to prior year plans
 */
$f3->route('GET|POST /prior-year', function()
{
    $GLOBALS['controller']->route_prior_year();
});


/**
 * Route for creating / navigating to next year plans
 */
$f3->route('GET|POST /next-year', function()
{
    $GLOBALS['controller']->route_next_year();
});


/**
 * Route for login button press
 */
$f3->route('POST /login', function()
{
    $GLOBALS['controller']->login();
});


/**
 * Route for admin page
 */
$f3->route('GET /admin', function()
{
    $GLOBALS['controller']->admin();
});


/**
 * Route for displaying 404
 */
$f3->route('GET|POST /error404', function()
{
    $GLOBALS['controller']->error();
});


/**
 * route for all multi-folder 404 requests
 */
$f3->set('ONERROR', function()
{
	$GLOBALS['controller']->error_reroute();
});


// run f3 and clear output buffer
$f3->run();
ob_flush();