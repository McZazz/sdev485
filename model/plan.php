<?php

class Plan
{
    private $_fall = '';
    private $_winter = '';
    private $_spring = '';
    private $_summer = '';
    private $_token;
    private $_time = '';

    function __construct($token)
    {

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
    		return $this->_time;
    }

    function echoAll()
    {
    		echo $this->_fall . ' ' . $this->_winter . ' ' . $this->_spring . ' ' . $this->_summer;  
    }
}