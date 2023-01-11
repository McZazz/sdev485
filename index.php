<?php

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
 * home route
 */
$f3->route('GET /', function()
{
		// token, last_saved, fall, winter, spring, summer

		//echo bin2hex(random_bytes(3));
    $GLOBALS['controller']->route_home();
});

/**
 * home route
 */
$f3->route('POST /', function()
{
    $GLOBALS['controller']->route_create_new();
});


$f3->route('GET /test', function()
{
    $GLOBALS['controller']->test();
});

/**
 * home route
 */
$f3->route('GET|POST /plan/@token', function()
{
    $GLOBALS['controller']->route_plan();
});

$f3->set('ONERROR', function()
{
	$GLOBALS['controller']->error();
});

// run f3 and clear output buffer
$f3->run();
ob_flush();