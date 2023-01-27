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
        // create new plan with new token
		$newPlan = $this->_db->addNewToken();

        if ($newPlan == false) {
            // go home, database is full of 6 char tokens
            $this->_f3->reroute('/');
        }

        // goto plan page with this token in url
        $_SESSION['plan'] = $newPlan;
        $_SESSION['is_new'] = true;
		$this->_f3->reroute('/' . $_SESSION['plan']->getToken());
    }


    /**
     * Route for creating / going to prior year
     */
    function route_prior_year()
    {
        // update database from POST
        $old_token_obj = $this->getTokenObjFromPOST();
        $this->_db->updatePlans($old_token_obj);
        $this->_db->updateToken($old_token_obj);

        // get plans and set new year
        $prior_year = $_SESSION['plan']->getPlansArray()[0]->getYear();
        $prior_year = intval($prior_year);
        $prior_year--;

        // as long as there are dbs available, make prior plan year
        if ($prior_year != 2019) {
            $token = $_SESSION['plan']->getToken();
            $this->_db->insertOnePlan($token, $prior_year, '', '', '', '');
        }

        $this->_f3->reroute('/' . $_SESSION['plan']->getToken());
    }


    /**
     * Route for creating / going to next year
     */
    function route_next_year()
    {

        // update POST data in SESSION object and update in databas
        $old_token_obj = $this->getTokenObjFromPOST();
        $this->_db->updatePlans($old_token_obj);
        $this->_db->updateToken($old_token_obj);

        // get plans and set year to one in the future
        $priors = $_SESSION['plan']->getPlansArray();
        $next_year = $priors[sizeof($priors)-1]->getYear();
        $next_year = intval($next_year);
        $next_year++;

        // as long as we have databases available, add a new plan
        if ($next_year != 2040) {
            $token = $_SESSION['plan']->getToken();
            $this->_db->insertOnePlan($token, $next_year, '', '', '', '');
        }

        // make sure page will scroll down
        $_SESSION['scrolldown'] = 't';

        $this->_f3->reroute('/' . $_SESSION['plan']->getToken());
    }


    /**
     * Gets the token object form POST, filled with updated POST data
     * @return Token object, from SESSION
     */
    function getTokenObjFromPOST()
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


    function route_save()
    {
        // POST means we are saving / updating an existing token
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // reacquire fields, instantiation automatically gets from post
            $new_token_obj = $this->getTokenObjFromPOST();

            // update records in db
            $this->_db->updatePlans($new_token_obj);
            $this->_db->updateToken($new_token_obj);
            $_SESSION['plan'] = $this->_db->getTokenObj($new_token_obj->getToken());

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
    }


    /**
     * Route for displaing plan page, 
     * redirected from route_create_new for new tokens only
     */
    function route_plan()
    {
        // POST means we are saving / updating an existing token
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            // GET
            // this fires when getting a previusly saved token, or after a new token is made, it's rerouted to here
            if ($this->_f3->get('PARAMS.token') != '' && null != $this->_f3->get('PARAMS.token')) {

                if ((isset($_SESSION['plan']) && $_SESSION['plan']->isNew() == false) || !isset($_SESSION['plan'])) {
                    // purge unused tokens if this is unused. 24 hour grace period applies.
                    $this->_db->deleteIfUnusedAfter24Hrs();
                }

                if (isset($_SESSION['is_new']) && $_SESSION['is_new'] == false || !isset($_SESSION['is_new'])) {
                    // gets data from db after a save
                    // this also gets data from db after adding new years above and below
                    $plan = $this->_db->getTokenObj($this->_f3->get('PARAMS.token'));

                    // if token is false, reroute to home
                    if ($plan == false) {
                        $this->_f3->reroute('/');
                    }

                    // set for use in templating
                    $_SESSION['plan'] = $plan;
                }
            }
        }

        // make sure proper server environment is set for links
        $_SESSION['root'] = $this->_SERVER_ROOT; 

        $view = new Template(); 
        echo $view->render('views/plan.html'); 
        
        // reset so we don't always scroll down
        $_SESSION['scrolldown'] = 'f';
        // reset so we don't get empty plan pages
        $_SESSION['is_new'] = false;
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