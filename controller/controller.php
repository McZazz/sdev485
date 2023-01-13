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
        $this->_db->tokenIsUnique2('3211PP');
    }

    function route_create_new()
    {
		// $prevTokens = $this->_db->getTokens();
		$isUnique = $this->_db->addNewPlan();

		if ($isUnique['status'] == true) {

            // create plan model obj. all quarter fields blank
            $_SESSION['plan'] = new Plan($isUnique['token']);
            $_SESSION['plan']->setIsNew(true);

            // $this->_f3->set('new', 't');

			$this->_f3->reroute('/' . $_SESSION['plan']->getToken());
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
            // get new dtg
            $_SESSION['plan'] = $this->_db->getPlan($_SESSION['plan']->getToken());
            // make sure it shows as saved
            $_SESSION['plan']->setSaved('1');
            // show saved message
            $this->_f3->set('opened', 't');

        } else {
            // this fires when creating new token, and when getting a previusly saved token
            if ($this->_f3->get('PARAMS.token') != '' && null != $this->_f3->get('PARAMS.token')) {

                if ($_SESSION['plan']->isNew() == false) {
                    // purge unused tokens if this is unused. 24 hour grace period applies.
                    $this->_db->deleteIfUnusedAfter24Hrs();
                }
                
                $plan = $this->_db->getPlan($this->_f3->get('PARAMS.token'));

                // if plan is false, reroute to home
                if ($plan == false) {
                    $this->_f3->reroute('/');
                }

                $_SESSION['plan'] = $plan;
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