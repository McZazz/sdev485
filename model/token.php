<?php
/**
 * @author: Kevin Price
 * @date: Jan 26, 2023
 * @filename: token.php
 * @description: Token object for student advising data: 
 * advisor, token, saved info and holds Plan objects in an array
 */
class Token
{
	private $_QUARTER_LEN = 1000;
	private $_ADVISOR_LEN = 50;

	private $_token;
	private $_last_saved;
	private $_advisor;
	private $_saved = '0';
	private $_is_new = false;
	private $_plans = array();

	/**
	 * @param $token String data 6 char token
	 * @param $advisor String data advisor name
	 * @param $last_saved String data DateTime of when last saved
	 */
	function __construct($token='', $advisor='', $last_saved='')
	{
		$this->_token = $token;
		$this->_advisor = $advisor;
		$this->_last_saved = $last_saved;
	}


	/**
	 * Updates form POST data
	 * @param $plans_arr_from_session, array of plans got form SESSION
	 */
	function addPlansFromPost($plans_arr_from_session)
	{
		// empty out old array
        $this->_plans = array();
        // iterate all plans from session
        foreach ($plans_arr_from_session as $plan) {
        	// get year string and create a new Plan obj
        	$year_str = $plan->getYear();
            $new_plan = new Plan(intval($year_str));

            // get POST data for all
            if (isset($_POST['fall_'.$year_str])) {
            	$fall = substr($_POST['fall_'.$year_str], 0, $this->_QUARTER_LEN);
                $new_plan->setFall($fall);
            }
            if (isset($_POST['winter_'.$year_str])) {
            	$winter = substr($_POST['winter_'.$year_str], 0, $this->_QUARTER_LEN);
                $new_plan->setWinter($winter);
            }
            if (isset($_POST['spring_'.$year_str])) {
            	$spring = substr($_POST['spring_'.$year_str], 0, $this->_QUARTER_LEN);
                $new_plan->setSpring($spring);
            }
            if (isset($_POST['summer_'.$year_str])) {
            	$summer = substr($_POST['summer_'.$year_str], 0, $this->_QUARTER_LEN);
                $new_plan->setSummer($summer);
            }

            // add the plan to this Token obj
            $this->addPlan($new_plan);
        }
	}


	/**
	 * Add plan obj to this Token obj
	 * @param $plan, Plan object
	 */
	function addPlan($plan)
	{
		$this->_plans[] = $plan;
	}


	/**
	 * Delete all plans in this Token obj
	 */
	function emptyPlans()
	{
		$this->_plans = array();
	}


	/**
	 * Getter for new state
	 * @return boolean, true if plan is new, false if not
	 */
	function isNew()
	{
		return $this->_is_new;
	}


	/**
	 * Set is new state
	 * @param $is_new, boolean, true if it's new, false if not
	 */
	function setIsNew($is_new)
	{
		$this->_is_new = $is_new;
	}


	/**
	 * Get all plans in theis Token object
	 * @return array of Plan objects
	 */
	function getPlansArray()
	{
		return $this->_plans;
	}


    /**
     * Get Token data
     * @return String, 6 char token
     */
    function getToken()
    {
        return $this->_token;
    }


    /**
     * Get Advisor data
     * @return String, name of advisor
     */
    function getAdvisor()
    {
        return $this->_advisor;
    }


    /**
     * Get sate of if it's saved or not
     * @return string, 0 if not saved, 1 if saved
     */
    function getSaved()
    {
    	return $this->_saved;
    }


    /**
     * Set saved state info
     * @param string, 0 if not saved, 1 if saved
     */
    function setSaved($saved)
    {
    	$this->_saved = $saved;
    }


    /**
     * Get DateTime formatted string of when Token / plans last saved
     * @return String, DateTime formatted string
     */
    function getLastSaved()
    {
        return $this->_last_saved;
    }


    /**
     * Set DateTime formatted string of when Token / Plans last saved
     * @param String DateTime formatted
     */
    function setLastSaved($last_saved)
    {
        $this->_last_saved = $last_saved;
    }
}