<?php
/**
 * @author: Kevin Price
 * @date: Jan 12, 2023
 * @filename: dataLayer.php
 * @description: PDO data layer for F3
 */

require $_SERVER['DOCUMENT_ROOT'].'/../local_db_creds.php'; // use for local dev
// require $_SERVER['DOCUMENT_ROOT'].'/../pdo-config.php'; // use for deployment

/**
 * DataLayer class for PDO database management 
 */
class DataLayer
{
    private $_db;

    /**
     * Constructor
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


    /**
     * Create new plan row in db and return the token
     * @return array, with 'token' and 'status' keys 
     */
    function addNewPlan()
    {
        // clean out unused tokens older than 24 hours
        $this->deleteIfUnusedAfter24Hrs();

        $newToken = $this->createUniqueToken();

        // database can't take any more tokens, show 404
        if ($newToken == false) {
            return false;
        }

        // add plan to db with new token
        $sql = "INSERT INTO plan (token, last_saved, saved)
                VALUES (:token, NOW(), 0)";
        $saved = '0';

        $statement = $this->_db->prepare($sql);
        $statement->bindParam(':token', $newToken);
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


    /**
     * Save / update a pre-existing plan
     * @param $plan Plan object
     */
    function updatePlan($plan)
    {
        // create sql statement to update plan
        $sql = "UPDATE plan 
                SET fall = :fall, winter = :winter, spring = :spring, summer = :summer, advisor = :advisor, saved = :saved, last_saved = NOW() 
                WHERE token = :token";
        $token = $plan->getToken();
        $advisor = $plan->getAdvisor();
        $saved = '1';
        $fall = $plan->getFall();
        $winter = $plan->getWinter();
        $spring = $plan->getSpring();
        $summer = $plan->getSummer();

        $statement = $this->_db->prepare($sql);
        $statement->bindParam(':token', $token);
        $statement->bindParam(':advisor', $advisor);
        $statement->bindParam(':saved', $saved);
        $statement->bindParam(':fall', $fall);
        $statement->bindParam(':winter', $winter);
        $statement->bindParam(':spring', $spring);
        $statement->bindParam(':summer', $summer);

        // execute sql
        $statement->execute();
    }


    /**
     * getPlan getter, gets entire plan from db as Plan object
     * @param $token String, 6 digit token
     * @return boolean: false if plan was not present, Plan object if plan present
     */
    function getPlan($token)
    {
        $sql = "SELECT last_saved, token, advisor, fall, winter, spring, summer, saved
                FROM plan WHERE token = :token";

        $statement = $this->_db->prepare($sql);
        $statement->bindParam(':token', $token);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        // invalid token
        if (sizeof($result) == 0) {
            return false;
        }

        // return valid plan
        return new Plan($result[0]);
    }


    /**
     * Checks if token is unique
     * @param $token, String: 6 digit token
     * @return boolean, true if unique, false if not
     */
    function tokenIsUnique($token)
    {
        // create and run sql to check if token is present
        $sql = "SELECT token FROM plan WHERE token = :token";

        $statement = $this->_db->prepare($sql);
        $statement->bindParam(':token', $token);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        // empty return means token is unique
        if (sizeof($result) == 0) {
            return true;
        } else {
            return false;
        }

    }


    /**
     * Deletes unsaved tokens after 24 hours
     */
    function deleteIfUnusedAfter24Hrs()
    {
        // sql statement deltes all plans not saved after 24 hours
        $sql = "DELETE FROM plan WHERE saved = 0 AND last_saved + INTERVAL 1 DAY < NOW()";
        $statement = $this->_db->prepare($sql);
        $statement->execute();    
    }


    /**
     * Create random token
     * @param $len, integer, sets length of token
     * @return String, token
     */
    function createToken($len)
    {
        // pool of chars for tokens
        $chars = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890123456789012345678901234567890123456789012';

        $token = '';
        // use cryptographically secure random ints to select each char
        for ($i = 0; $i < $len; $i++) {
            $token .= $chars[random_int(0, strlen($chars)-1)];
        }

        return $token;
    }


    /**
     * Create unique token, checked against db
     * @return String, unique token
     */
    function createUniqueToken()
    {
        $cntr = 0;

        // loop until unique token is made
        while (true) {
            $newToken = $this->createToken(6);
            $isUnique = $this->tokenIsUnique($newToken);

            // if token is unique, return it
            if ($isUnique) {
                return $newToken;
            }

            $cntr++;
            if ($cntr == 5000) {
                // just in case it's full
                return false;
            }
        }
    }
}