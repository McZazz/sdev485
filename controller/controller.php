<?php
/**
 * @author: Kevin Price
 * @date: Jan 12, 2023
 * @filename: controller.php
 * @description: Php F3 controller
 */

/**
 * Controller class for routes
 * @param $f3 Base
 */
class Controller
{
    private $_f3;
    private $_db;
    private $_SERVER_ROOT = 'http://localhost/sdev485/'; //////////////////////////////// local
    // private $_SERVER_ROOT = 'https://kprice.greenriverdev.com/485/'; ///////////////// server

    /**
     * Constructor 
     */
    function __construct($f3)
    {
        $this->_f3 = $f3;
        $this->_db = new DataLayer();
    }


    /**
     * Route to home page
     */
    function route_home()
    {
        // goto home
        $view = new Template();
        echo $view->render('views/home.html');
    }


    /**
     * Route for creating new token
     */
    function route_create_new()
    {
		$newPlan = $this->_db->addNewToken();


        if ($newPlan == false) {
            // go home, database is full of 6 char tokens
            $this->_f3->reroute('/');
        }

        // echo $_SESSION['plan']->getToken();
        // goto plan page with this token in url
        $_SESSION['plan'] = $newPlan;
        // echo print_r($_SESSION['plan'][0]->getToken());
		$this->_f3->reroute('/' . $_SESSION['plan'][0]->getToken());
    }


    /**
     * Route for creating / going to prior year
     */
    function route_prior_year()
    {
        echo "goto / create prior year";
    }


    /**
     * Route for creating / going to next year
     */
    function route_next_year()
    {
        echo "goto / create next year";
    }


    /**
     * Route for displaing plan page, 
     * redirected from route_create_new for new tokens only
     */
    function route_plan()
    {
        // POST means we are saving / updating an existing token
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // reacquire fields, instantiation automatically gets from post
            $_SESSION['plan'] = new Plan($_SESSION['plan']->getToken(), $_SESSION['plan']->getYear());

            // update records in db
            $this->_db->updateYear($_SESSION['plan']);




            // // get new dtg
            // $_SESSION['plan'] = $this->_db->getPlan($_SESSION['plan']->getToken());
            // // make sure it shows as saved
            // $_SESSION['plan']->setSaved('1');
            // // show saved message on front end
            // $this->_f3->set('opened', 't');


        } else {
            // GET
            // this fires when getting a previusly saved token, or after a new token is made, it's rerouted to here
            if ($this->_f3->get('PARAMS.token') != '' && null != $this->_f3->get('PARAMS.token')) {

                if ((isset($_SESSION['plan']) && $_SESSION['plan'][0]->isNew() == false) || !isset($_SESSION['plan'])) {
                    // purge unused tokens if this is unused. 24 hour grace period applies.
                    $this->_db->deleteIfUnusedAfter24Hrs();
                }

                // getting plan on return to site
                if (!isset($_SESSION['plan'])) {
                    $plan = $this->_db->getPlan($_SESSION['plan']->getToken());

                    // if token is false, reroute to home
                    if ($plan == false) {
                        $this->_f3->reroute('/');
                    }

                    // set for use in templating
                    $_SESSION['plan'] = $plan;

                }
            }
        }

        $this->_f3->set('root', $this->_SERVER_ROOT); ////////////////////// use

        $view = new Template(); ////////////////////// use
        echo $view->render('views/plan.html'); ////////////////////// use
    }


    /**
     * Login button press route handling
     */
    function login() 
    {
        $username = '';
        $password = '';

        // get username and pass from POST
        if (isset($_POST['username'])) {
            $username = $_POST['username'];
        }
        if (isset($_POST['password'])) {
            // hash the pass immediately out of POST
            $password = hash('sha256', $_POST['password']);
        }

        // check database for user valid name and pass
        $userIsValid = $this->_db->authUser($username, $password);

        // if user is valid, go to admin route
        if ($userIsValid) {
            $_SESSION['loggedin'] = true;
            $this->_f3->reroute('/admin');
        } else {
            // if user is not valid, re-render home page with modal visible,
            // showing invalid credentials flag
            $this->_f3->set('invalid_login', true);
            $view = new Template();
            echo $view->render('views/home.html');
        }
    }


    /**
     * Admin route handling
     */
    function admin()
    {
        // make sure user is logged in to go to admin page
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {

            // get all plans
            $plans = $this->_db->getPlansForAdmin();
            $this->_f3->set('plans', $plans);
            // set server root for link
            $this->_f3->set('root', $this->_SERVER_ROOT);

            $view = new Template();
            echo $view->render('views/admin.html');
        } else {
            // otherwise go to home page
            $this->_f3->reroute('/');
        }
    }


    /**
     * Re-route to 404
     */
    function error_reroute()
    {
        // goto 404 route
        $this->_f3->reroute('/error404');
    }


    /**
     * Route 404
     */
    function error()
    {
        // goto error view
        $view = new Template();
        echo $view->render('views/error.html');
    }
}