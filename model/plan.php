<?php
/**
 * @author: Kevin Price
 * @date: Jan 12, 2023
 * @filename: plan.php
 * @description: Plan object for student advising plans data
 */

/**
 * Plan object for student advising plans data
 */
class Plan
{
    private $_token;
    private $_last_saved = '';
    private $_saved = '0';
    private $_fall = '';
    private $_winter = '';
    private $_spring = '';
    private $_summer = '';
    private $_is_new = false;

    /**
     * Constructor
     * @param $token, String, token for new Plans. Or an existing
     * PDO return directly from the database for updated plans
     */
    function __construct($token)
    {
		// we pass a token string or entire database obj
		if (is_string($token)) {

			$this->_token = $token;

            // cehck POST for correct keys, update if available
	        if (isset($_POST['fall'])) {
	            $this->_fall = $_POST['fall'];
	        }
	        if (isset($_POST['winter'])) {
	            $this->_winter = $_POST['winter'];
	        }                
	        if (isset($_POST['spring'])) {
	            $this->_spring = $_POST['spring'];
	        }                
	        if (isset($_POST['summer'])) {
	            $this->_summer = $_POST['summer'];
	        }
		} else {
            // PDO db return, fill into this new obj
			$this->_token = $token['token'];
			$this->_last_saved = $token['last_saved'];
			$this->_saved = $token['saved'];
			$this->_fall = $token['fall'];
			$this->_winter = $token['winter'];
			$this->_spring = $token['spring'];
			$this->_summer = $token['summer'];
		}
    }


    /**
     * Get Fall data
     * @return String, data
     */
    function getFall()
    {
		return $this->_fall;
    }


    /**
     * Get Winter data
     * @return String, data
     */
    function getWinter()
    {
		return $this->_winter;
    }


    /**
     * Get Spring data
     * @return String, data
     */
    function getSpring()
    {
		return $this->_spring;
    }


    /**
     * Get Summer data
     * @return String, data
     */
    function getSummer()
    {
		return $this->_summer;
    }


    /**
     * Get token
     * @return String, data
     */
    function getToken()
    {
		return $this->_token;
    }


    /**
     * Get saved / unsaved state
     * @return boolean tiny int, 0 if unsaved, 1 if saved
     */
    function getSaved()
    {
		return $this->_saved;
    }


    /**
     * Set saved / unsaved state
     * @param boolean tiny int, 0 if unsaved, 1 if saved
     */
	function setSaved($saved)
    {
		$this->_saved = $saved;
    }   


    /**
     * Get time of last save
     * @return String, data, DateTime formatted
     */
	function getLastSaved()
    {
		return $this->_last_saved;
    }  


    /**
     * Get is new state
     * @param boolean, true if Plan is new, false if not
     */
    function isNew()
    {
        return $this->_is_new;
    }


    /**
     * Set is new state
     * @param $state, boolean, true if Plan is new, false if not
     */
    function setIsNew($state) 
    {
        $this->_is_new = $state;
    }
}