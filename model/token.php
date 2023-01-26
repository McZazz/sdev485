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

	function addPlan($plan)
	{
		$this->_plans[] = $plan;
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

    /**
     * Get Fall data
     * @return String, data
     */
    function getLastSaved()
    {
        return $this->_last_saved;
    }
}