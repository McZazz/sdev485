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
                VALUES (:token, :last_saved, 0)";

        $saved = '0';
        $timeNow = new DateTime();
        $timeNow = $timeNow->format('Y-m-d H:i:s');

        $statement = $this->_db->prepare($sql);
        $statement->bindParam(':token', $newToken);
        $statement->bindParam(':last_saved', $timeNow);
        // $statement->bindParam(':saved', $saved);
        $statement->execute();

        $insertSuccess = $this->tokenIsUnique($newToken);

        // swapping the meaning of $insertSuccess, it's a success if false
        // because we need to know its already in the db after creating it
        if ($this->tokenIsUnique($newToken)) {
            return array('token'=>$newToken, 'status'=>false);
        } else {
            return array('token'=>$newToken, 'status'=>true);
        }

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

    function rowIsUnusedAfter24Hrs($row)
    {
        $dtg = new DateTime($row['last_saved']);
        echo 'orig ' . print_r($dtg) . '<br>';
        $dtg->modify('+1 day');
        echo 'day later ' . print_r($dtg) . '<br>';

        $timeNow = new DateTime();
        echo 'time now ' . print_r($timeNow) . '<br>';
        // $dateTime->format('Y-m-d H:i:s');
        
    }

    function deleteUnusedToken()
    {
        $prevTokens = $this->getTokens();
        foreach ($prevTokens as $row) {
            // echo print_r($row);
            // $dtg1 = new DateTime($row['last_saved']);
            // echo print_r($dtg1) . ' eeee<br>';
            $this->rowIsUnusedAfter24Hrs($row);

        }
    }

    function getTokens()
    {
        if ($this->_db) {
            // prepared statement
            $sql = "SELECT token, last_saved, saved FROM plan";
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