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

            // create plan model obj. all quarter fields blank
            $_SESSION['plan'] = new Plan($isUnique['token']);

			$this->_f3->reroute('plan/' . $_SESSION['plan']->getToken());
            // $this->_f3->reroute('plan');
		} else {
			$view = new Template();
            echo $view->render('views/error.html');
		} 

		// echo print_r($prevTokens);
    }

    function route_plan()
    {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // reacquire fields, instantiation automatically gets from post
            $_SESSION['plan'] = new Plan($_SESSION['plan']->getToken());

            // update record in db
            $this->_db->updatePlan($_SESSION['plan']);
            // make sure it shows as saved
            $_SESSION['plan']->setSaved('1');

        } else {
            // we only need this if opening a new plan from route_create_new()
            if ($_SESSION['plan']->getSaved() == '1') {
                // echo $this->_f3->get('PARAMS.token');
                $_SESSION['plan'] = $this->_db->getPlan($this->_f3->get('PARAMS.token'));
                
            }
        }

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