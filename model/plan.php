<?php

class Plan
{
    private $_token;
    private $_last_saved = '';
    private $_saved = '0';
    private $_fall = '';
    private $_winter = '';
    private $_spring = '';
    private $_summer = '';

    function __construct($token)
    {
    		// we pass a token string or entire database obj
    		if (is_string($token)) {
    			
    				$this->_token = $token;

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
    				$this->_token = $token['token'];
    				$this->_last_saved = $token['last_saved'];
    				$this->_saved = $token['saved'];
    				$this->_fall = $token['fall'];
    				$this->_winter = $token['winter'];
    				$this->_spring = $token['spring'];
    				$this->_summer = $token['summer'];
    		}
    }

    function getFall()
    {
    		return $this->_fall;
    }

    function getWinter()
    {
    		return $this->_winter;
    }

    function getSpring()
    {
    		return $this->_spring;
    }

    function getSummer()
    {
    		return $this->_summer;
    }

    function getToken()
    {
    		return $this->_token;
    }

    function getTime()
    {
    		return $this->_last_saved;
    }

    function getSaved()
    {
    		return $this->_saved;
    }

		function setSaved($saved)
    {
    		$this->_saved = $saved;
    }   

    function echoAll()
    {
    		echo $this->_fall . ' ' . $this->_winter . ' ' . $this->_spring . ' ' . $this->_summer;  
    }
}