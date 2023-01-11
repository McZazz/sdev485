<?php

/**
* Controller class for routes
*/
class Controller
{
    private $_f3;

    function __construct($f3)
    {
        $this->_f3 = $f3;
    }

    /**
    * route to home page
    */
    function route_home()
    {
        // goto home
        $view = new Template();
        echo $view->render('views/home.html');
    }
}