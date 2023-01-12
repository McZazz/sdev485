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

        $this->deleteIfUnusedAfter24Hrs();

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
        // $timeNow = new DateTime();
        // $timeNow = $timeNow->format('Y-m-d H:i:s');

        $statement = $this->_db->prepare($sql);
        $statement->bindParam(':token', $newToken);
        // $statement->bindParam(':last_saved', $timeNow);
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

    function updatePlan($plan)
    {
        $sql = "UPDATE plan 
                SET fall = :fall, winter = :winter, spring = :spring, summer = :summer, saved = :saved, last_saved = NOW() 
                WHERE token = :token";
        $token = $plan->getToken();
        $saved = '1';
        $fall = $plan->getFall();
        $winter = $plan->getWinter();
        $spring = $plan->getSpring();
        $summer = $plan->getSummer();

        $statement = $this->_db->prepare($sql);
        $statement->bindParam(':token', $token);
        $statement->bindParam(':saved', $saved);
        $statement->bindParam(':fall', $fall);
        $statement->bindParam(':winter', $winter);
        $statement->bindParam(':spring', $spring);
        $statement->bindParam(':summer', $summer);

        $statement->execute();
    }

    function getPlan($token)
    {
        $sql = "SELECT last_saved, token, fall, winter, spring, summer, saved
                FROM plan WHERE token = :token";

        $statement = $this->_db->prepare($sql);
        $statement->bindParam(':token', $token);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        // invalid token
        // if ()

        return new Plan($result[0]);

    }

    // function tokenIsUnique($token)
    // {

    //     /////////////////////////////////////////////////
    //     $prevTokens = $this->getTokens();

    //     if (sizeof($prevTokens) == 0) {
    //         return true;
    //     }

    //     // check that token is unique
    //     foreach ($prevTokens as $row) {
    //         if ($row['token'] == $token) {
    //             return false;
    //         }
    //     }

    //     return true;
    //     ////////////////////////////////////////////////
    // }


    function tokenIsUnique($token)
    {

        $sql = "SELECT token FROM plan WHERE token = :token";

        $statement = $this->_db->prepare($sql);
        $statement->bindParam(':token', $token);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (sizeof($result) == 0) {
            return true;
        } else {
            return false;
        }

    }


    function deleteIfUnusedAfter24Hrs()
    {
        $sql = "DELETE FROM plan WHERE saved = 0 AND last_saved + INTERVAL 1 DAY < NOW()";
        $statement = $this->_db->prepare($sql);
        $statement->execute();    
    }

    function getTokens()
    {
        if ($this->_db) {

            // prepared statement
            $sql = "SELECT token, last_saved, saved FROM plan";
            $statement = $this->_db->prepare($sql);
            $statement->execute();

            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            // return 
            return $result;
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