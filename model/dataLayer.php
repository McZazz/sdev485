<?php

require $_SERVER['DOCUMENT_ROOT'].'/../local_db_creds.php'; // local

/**
* datalayer
*/
class DataLayer
{
    private $_db;

    /**
    * Constructor of datalayer
    */
    function __construct()
    {
        try {
            // create PDO object
            $this->_db = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        }   catch (PDOException $e) {
            // set to false, so controller can check
            $this->_db = false;
        }
    }
}