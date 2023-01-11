<?php

/**
* Controller class for routes
*/
class Controller
{
    private $_f3;
    private $_db;

    function __construct($f3)
    {
        $this->_f3 = $f3;
        $this->_db = new DataLayer();
        define('SERVER_ORIGIN', '://localhost/sdev485/'); // local
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

    function route_create_new()
    {
    		// $prevTokens = $this->_db->getTokens();
    		$isUnique = $this->_db->addNewPlan();

    		if ($isUnique['status'] == true) {
    			echo 'true ' . $isUnique['token'];
    		}
    		else if ($isUnique['status'] == false) {
    			echo 'false';
    		} else {
    			echo 'broke';
    		}

    		// echo print_r($prevTokens);
    }

    /**
    * route 404
    */
    function error()
    {
        // goto home
        $view = new Template();
        echo $view->render('views/error.html');
    }
}