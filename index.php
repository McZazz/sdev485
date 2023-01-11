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
    $GLOBALS['controller']->route_home();
});

// run f3 and clear output buffer
$f3->run();
ob_flush();