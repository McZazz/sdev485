<?php

class Token
{
	private $_token;
	private $_last_saved;
	private $_advisor;
	private $_saved = '0';
	private $_is_new = false;
	private $_plans = array();

	function __construct($token='', $advisor='', $last_saved='')
	{
		$this->_token = $token;
		$this->_advisor = $advisor;
		$this->_last_saved = $last_saved;
	}

	function addPlansFromPost($plans_arr_from_session)
	{
        $cntr = 0;
        $this->_plans = array();
        foreach ($plans_arr_from_session as $plan) {

        	$year_str = $plan->getYear();

            $new_plan = new Plan(intval($year_str));

            if (isset($_POST['fall_'.$cntr])) {
                $new_plan->setFall($_POST['fall_'.$year_str]);
            }
            if (isset($_POST['winter_'.$year_str])) {
                $new_plan->setWinter($_POST['winter_'.$year_str]);
            }
            if (isset($_POST['spring_'.$year_str])) {
                $new_plan->setSpring($_POST['spring_'.$year_str]);
            }
            if (isset($_POST['summer_'.$year_str])) {
                $new_plan->setSummer($_POST['summer_'.$year_str]);
            }

            $this->addPlan($new_plan);
            $cntr++;
        }
	}

	function addPlan($plan)
	{
		$this->_plans[] = $plan;
	}

	function emptyPlans()
	{
		$this->_plans = array();
	}

	function isNew()
	{
		return $this->_is_new;
	}

	function setIsNew($is_new)
	{
		$this->_is_new = $is_new;
	}

	function getPlansArray()
	{
		return $this->_plans;
	}

    /**
     * Get Fall data
     * @return String, data
     */
    function getToken()
    {
        return $this->_token;
    }

    /**
     * Get Fall data
     * @return String, data
     */
    function getAdvisor()
    {
        return $this->_advisor;
    }

    function getSaved()
    {
    	return $this->_saved;
    }

    function setSaved($saved)
    {
    	$this->_saved = $saved;
    }

    /**
     * Get Fall data
     * @return String, data
     */
    function getLastSaved()
    {
        return $this->_last_saved;
    }

    function setLastSaved($last_saved)
    {
        $this->_last_saved = $last_saved;
    }
}