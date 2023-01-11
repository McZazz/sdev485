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

    function addNewPlan()
    {

        $cntr = 0;


        while (true) {
            $newToken = $this->createToken(6);
            $isUnique = $this->tokenIsUnique($newToken);

            if ($isUnique) {
                break;
            }

            $cntr++;
            if ($cntr == 5000) {
                // just in case it's full
                return NULL;
            }
        }

        // add plan
        $sql = "INSERT INTO plan (token, last_saved, saved)
                VALUES (:token, NOW(), 0)";

        $saved = '0';
        $timeNow = 'NOW()';

        $statement = $this->_db->prepare($sql);
        $statement->bindParam(':token', $newToken);
        // $statement->bindParam(':last_saved', $timeNow);
        // $statement->bindParam(':saved', $saved);
        echo 'eeeee';
        $statement->execute();

        $newId = $this->_db->lastInsertId();

        return $newId;

    }

    function tokenIsUnique($token)
    {
        $prevTokens = $this->getTokens();

        // check that token is unique
        foreach ($prevTokens as $row) {
            echo $row['token'] . '<br>';
            if ($row['token'] == $token) {
                return false;
            }
        }

        return true;
    }

    function getTokens()
    {
        if ($this->_db) {
            // prepared statement
            $sql = "SELECT token FROM plan";
            $statement = $this->_db->prepare($sql);
            $statement->execute();

            // return 
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

    function createToken($len)
    {
        $chars = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890123456789012345678901234567890123456789012';

        $token = '';

        for ($i = 0; $i < $len; $i++) {
            $token .= $chars[rand(0, strlen($chars)-1)];
        }

        return $token;
    }

}