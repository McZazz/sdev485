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

    function test()
    {
        $this->_db->deleteUnusedToken();
    }

    function route_create_new()
    {
		// $prevTokens = $this->_db->getTokens();
		$isUnique = $this->_db->addNewPlan();

		if ($isUnique['status'] == true) {
			$this->_f3->reroute('plan?token=' . $isUnique['token']);
		} else {
			$view = new Template();
    	echo $view->render('views/error.html');
		} 

		// echo print_r($prevTokens);
    }

    function route_plan()
    {
        // goto home
        $view = new Template();
        echo $view->render('views/plan.html');
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