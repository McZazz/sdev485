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
    function routeHome()
    {
        $_SESSION['plan'] = '';
        $_SESSION['opened'] = 'f';

        // make sure proper server environment is set for links
        // goto home
        $this->_db->deleteIfUnusedAfter24hrs();
        $view = new Template();
        echo $view->render('views/home.html');
    }


    /**
     * Route for creating new token
     */
    function routeCreateNew()
    {
        // create new plan with new token
		$insert_success = $this->_db->addNewToken();

        if ($insert_success == false) {
            // go home, database is full of 6 char tokens
            $this->_f3->reroute('/');
        }

        // goto plan page with this token in url
		$this->_f3->reroute('/' . $insert_success);
    }


    /**
     * Gets the token object form POST, filled with updated POST data
     * @return Token object, from SESSION
     */
    function getTokenObjFromPost()
    {
        // get original Token 
        $old_token_obj = $_SESSION['plan']->getToken();

        // get advisor if set, otherwise set as empty str
        if (isset($_POST['advisor'])) {
            $advisor = substr($_POST['advisor'], 0, 50);
        } else {
            $advisor = '';
        }

        // create token obj
        $new_token_obj = new Token($old_token_obj, $advisor);
        // get POST data
        $new_token_obj->addPlansFromPost($_SESSION['plan']->getPlansArray());

        return $new_token_obj;
    }


    /**
     * Route for creating / going to prior year
     */
    function routePriorYear()
    {
        $token = $_SESSION['plan']->getToken();

        $_SESSION['scrolldown'] = 'f';
        $_SESSION['opened'] = 't';

        // update database from POST
        $old_token_obj = $this->getTokenObjFromPost();
        $this->_db->updatePlans($old_token_obj);
        $this->_db->updateToken($old_token_obj);

        // get plans and set new year
        $prior_year = $_SESSION['plan']->getPlansArray()[0]->getYear();
        $prior_year = intval($prior_year);
        $prior_year--;

        // as long as there are dbs available, make prior plan year
        if ($prior_year != 2019) {
            $this->_db->insertOnePlan($token, $prior_year, '', '', '', '');
        }

        $this->_f3->reroute('/' . $token);


    }


    function routeSave()
    {
        // reacquire fields, instantiation automatically gets from post
        $new_token_obj = $this->getTokenObjFromPost();

        // update records in db
        $this->_db->updatePlans($new_token_obj);
        $this->_db->updateToken($new_token_obj);

        // token was invalid, go home
        if ($_SESSION['plan'] == false) {
            $this->_f3->reroute('/');
        }

        // prevent large plans from appearing to scroll up
        $_SESSION['scrolldown'] = 't';
        // show saved message on front end
        $_SESSION['opened'] = 't';

        $this->_f3->reroute('/' . $_SESSION['plan']->getToken());
    }


    /**
     * Route for creating / going to next year
     */
    function routeNextYear()
    {
        $token = $_SESSION['plan']->getToken();

        // update POST data in SESSION object and update in databas
        $old_token_obj = $this->getTokenObjFromPost();
        $this->_db->updatePlans($old_token_obj);
        $this->_db->updateToken($old_token_obj);

        // get plans and set year to one in the future
        $priors = $_SESSION['plan']->getPlansArray();
        $next_year = $priors[sizeof($priors)-1]->getYear();
        $next_year = intval($next_year);
        $next_year++;

        // as long as we have databases available, add a new plan
        if ($next_year != 2040) {
            $this->_db->insertOnePlan($token, $next_year, '', '', '', '');
        }

        // make sure page will scroll down
        $_SESSION['scrolldown'] = 't';
        $_SESSION['opened'] = 't';

                $_SESSION['plan'] = $this->_db->getTokenObj($token);

                // set for use in templating
        $this->_f3->set('root', $this->_SERVER_ROOT);

        $_SESSION['thwart_f3_builtin_10_percent_of_the_time_crash_on_reroute_mechanism'] = true;
        
        $this->_f3->reroute('/' . $token);
        // $view = new Template(); 
        // echo $view->render('views/plan.html'); 
    }


    /**
     * Route for displaing plan page, 
     * redirected from routeCreateNew for new tokens only
     */
    function routePlan()
    {

        // if (isset($_SESSION['thwart_f3_builtin_crash_on_reroute_mechanism']) && $_SESSION['thwart_f3_builtin_crash_on_reroute_mechanism'] == true) {
            // $_SESSION['thwart_f3_builtin_crash_on_reroute_mechanism'] == false;
        // } else {
            $_SESSION['plan'] = $this->_db->getTokenObj($this->_f3->get('PARAMS.token'));

            // if token is false, reroute to home
            if ($_SESSION['plan'] == false) {
                $this->_f3->reroute('/');
            }

            if ($_SESSION['plan']->getSaved() == '0') {
                $unsaved = $this->_db->makeUnsavedPlan($_SESSION['plan']->getLastSaved());
                $_SESSION['plan']->addPlan($unsaved);
            }
        // }


        // set for use in templating
        $this->_f3->set('root', $this->_SERVER_ROOT);
        
        $view = new Template(); 
        echo $view->render('views/plan.html'); 
    }


    /**
     * Login button press route handling
     */
    function login() 
    {
        // $_SESSION['is_new'] = false;
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
        // $_SESSION['is_new'] = false;
        $_SESSION['opened'] = 'f';
        // make sure user is logged in to go to admin page
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
            $_SESSION['plan'] == '';
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
    function errorReroute()
    {
        // $_SESSION['is_new'] = false;
        // goto 404 route
        $this->_f3->reroute('/error404');
    }


    /**
     * Route 404
     */
    function error()
    {
        // $_SESSION['is_new'] = false;
        // goto error view
        $view = new Template();
        echo $view->render('views/error.html');
    }
}