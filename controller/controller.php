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
            $_SESSION['token'] = $isUnique['token'];
			$this->_f3->reroute('plan/' . $isUnique['token']);
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
            if (isset($_SESSION['token'])) {

                $fall = '';
                $winter = '';
                $spring = '';
                $summer = '';

                if (isset($_POST['fall'])) {
                    $fall = $_POST['fall'];
                }
                if (isset($_POST['winter'])) {
                    $winter = $_POST['winter'];
                }                
                if (isset($_POST['spring'])) {
                    $spring = $_POST['spring'];
                }                
                if (isset($_POST['summer'])) {
                    $summer = $_POST['summer'];
                }

                echo $fall . ' ' . $winter . ' ' . $spring . ' ' . $summer;

            // echo 'saving stuff ' .$_SESSION['token'];
            // $this->_f3->reroute('plan/' . $_SESSION['token']);
            }
        } else {
            $view = new Template();
            echo $view->render('views/plan.html');
        }
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