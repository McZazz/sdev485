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
    private $_ADVISOR_LEN = 50;
    private $_QUARTER_LEN = 1000;

    private $_token;
    private $_last_saved = '';
    private $_saved = '0';
    private $_fall = '';
    private $_winter = '';
    private $_spring = '';
    private $_summer = '';
    private $_is_new = false;
    private $_advisor = '';
    private $_year = '';

    /**
     * Constructor
     * @param $token, String, token for new Plans. Or an existing
     * PDO return directly from the database for updated plans
     */
    function __construct($year)
    {
		// we pass a token string or entire database obj
		if (is_int($year)) {
            // its a string, so we need to get from POST
            $this->_year = $year;

            // check POST for correct keys, update if available
	        // if (isset($_POST['fall'])) {
	        //     $this->_fall = substr($_POST['fall'], 0, $this->_QUARTER_LEN);
	        // }
	        // if (isset($_POST['winter'])) {
	        //     $this->_winter = substr($_POST['winter'], 0, $this->_QUARTER_LEN);
	        // }                
	        // if (isset($_POST['spring'])) {
	        //     $this->_spring = substr($_POST['spring'], 0, $this->_QUARTER_LEN);
	        // }                
	        // if (isset($_POST['summer'])) {
	        //     $this->_summer = substr($_POST['summer'], 0, $this->_QUARTER_LEN);
	        // }
            // if (isset($_POST['advisor'])) {
            //     $this->_advisor = substr($_POST['advisor'], 0, $this->_ADVISOR_LEN);
            // }

		} else {
            // PDO db return, fill into this new obj
			$this->_token = $token['token'];
			$this->_last_saved = $token['last_saved'];
            $this->_advisor = $token['advisor'];
			$this->_saved = $token['saved'];
			$this->_fall = $token['fall'];
			$this->_winter = $token['winter'];
			$this->_spring = $token['spring'];
			$this->_summer = $token['summer'];
            $this->_year = $token['year'];
		}
    }


    // public static function getAllPlansFromPOST()
    // {

    //     if (isset($_POST['fall']) && isset($_POST['winter']) && isset($_POST['spring']) && isset($_POST['summer']) && isset($_POST['year']) && isset($_POST['advisor'])) {

    //         $plans_arr = array();

    //         for ($i = 0; $i < sizeof($_POST['fall']); $i++) {
    //             $new_plan = new Plan($_SESSION['plan'][0]->getToken(), $_POST['year']);
    //             $new_plan->setFall($_POST['fall'][$i]);
    //             $new_plan->setWinter($_POST['winter'][$i]);
    //             $new_plan->setSpring($_POST['spring'][$i]);
    //             $new_plan->setSummer($_POST['summer'][$i]);
    //             $new_plan->setAdvisor($_POST['advisor']);

    //             $plans_arr[] = $new_plan;
    //         }

    //         return $plans_arr;

    //     } else {
    //         return false;
    //     }
    // }


    /**
     * Get Fall data
     * @return String, data
     */
    function getYear()
    {
        return $this->_year;
    }


    /**
     * Set year data
     * @param $year String, data
     */
    function setYear($year)
    {
        $this->_year = $year;
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
     * Get Fall data
     * @return String, data
     */
    function setFall($fall)
    {
        $this->_fall = $fall;
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
     * Get Winter data
     * @return String, data
     */
    function setWinter($winter)
    {
        $this->_winter = $winter;
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
     * Get Spring data
     * @return String, data
     */
    function setSpring($spring)
    {
        $this->_spring = $spring;
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
     * Get Summer data
     * @param $summer, String
     */
    function setSummer($summer)
    {
        $this->_summer = $summer;
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
     * Get advisor
     * @return String, data
     */
    function getAdvisor()
    {
        return $this->_advisor;
    }


    /**
     * Get advisor
     * @return String, data
     */
    function setAdvisor($advisor)
    {
        $this->_advisor = $advisor;
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

