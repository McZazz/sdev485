<?php
/**
 * @author: Kevin Price
 * @date: Jan 12, 2023
 * @filename: dataLayer.php
 * @description: PDO data layer for F3
 */

require $_SERVER['DOCUMENT_ROOT'].'/../local_db_creds.php'; ////////////////////////// use for local dev
// require $_SERVER['DOCUMENT_ROOT'].'/../pdo-config.php'; ////////////////////////// use for deployment

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
    function addNewToken()
    {
        // clean out unused tokens older than 24 hours
        $this->deleteIfUnusedAfter24Hrs();

        $newToken = $this->createUniqueToken();

        // database can't take any more tokens, show 404
        if ($newToken == false) {
            return false;
        }

        // add plan to db with new token
        $sql = "INSERT INTO adviseit_tokens (token, last_saved, created, advisor, saved)
                VALUES (:token, NOW(), NOW(), :advisor, 0)";

        $advisor = '';

        $statement = $this->_db->prepare($sql);
        $statement->bindParam(':token', $newToken);
        $statement->bindParam(':advisor', $advisor);
        $statement->execute();

        $insertSuccess = $this->getToken($newToken);

        // swapping the meaning of $insertSuccess, it's a success if false
        // because we need to know its already in the db after creating it
        if ($insertSuccess == false) {
            return false;
        } else {
            $dtg = new DateTime($insertSuccess['created']);
            $month = intval($dtg->format('m'));
            $year = '20' . $dtg->format('y');

            if ($month >= 1 && $month <= 6) {
                $year = intval($year) - 1;
                $year = $year;
            }

            $plan = new Plan($year);
            
            // created date, jan - june means subtract 1 year
            $plans = new Token($newToken, '', '');
            $plans->setIsNew(true);
            $plans->addPlan($plan);

            return $plans;
        }
    }


    /**
     * Gets all plans for admin view
     * @return array, array of all plans
     */
    function getPlansForAdmin()
    {
        // do sql to get plans
        $sql = "SELECT created, token, advisor
                FROM adviseit_tokens WHERE saved = 1";

        $statement = $this->_db->prepare($sql);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        // if there are rows, we have plans
        if (sizeof($result) > 0) {
            // format all dtg for y m d and return
            $result = $this->formatDatetime($result, 'Y-m-d');
            return $result;
        }

        // return empty array in all other cases
        return [];
    }


    /**
     * Format datetime group of plans for display
     * @param $plans, array, array of plans form db
     * @param $format, string, format for date formatting
     */
    function formatDatetime($plans, $format)
    {
        $result = [];

        // iterate all plans
        foreach ($plans as $plan) {
            // format them one by one
            $dtg = new DateTime($plan['created']);
            $plan['created'] = $dtg->format($format);
            $result[] = $plan;
        }

        // return the formatted array
        return $result;
    }


    /**
     * Inserts one plan into the database with specified data
     * @param $token String, plan data
     * @param $year String, plan data
     * @param $fall String, plan data
     * @param $winter String, plan data
     * @param $spring String, plan data
     * @param $summer String, plan data
     */
    function insertOnePlan($token, $year, $fall, $winter, $spring, $summer)
    {
        // set year of database
        $planyear = 'adviseit_'.$year;

        $sql = "INSERT INTO " . $planyear . " (token, fall, winter, spring, summer)
                VALUES (:token, :fall, :winter, :spring, :summer)";

        $statement = $this->_db->prepare($sql);
        $statement->bindParam(':token', $token);
        $statement->bindParam(':fall', $fall);
        $statement->bindParam(':winter', $winter);
        $statement->bindParam(':spring', $spring);
        $statement->bindParam(':summer', $summer);

        // execute sql
        $statement->execute();
    }


    /**
     * Save / update a pre-existing plan
     * @param $plans Token object with many plans
     */
    function updatePlans($plans)
    {
        // get token data from token table
        $token_table = $this->getToken($plans->getToken());

        // if save button was never hit, we do an insert
        if ($token_table['saved'] == 0) {

            foreach ($plans->getPlansArray() as &$plan) {

                $token = $plans->getToken();
                $year = $plan->getYear();
                $fall = $plan->getFall();
                $winter = $plan->getWinter();
                $spring = $plan->getSpring();
                $summer = $plan->getSummer();

                // use insert function
                $this->insertOnePlan($token, $year, $fall, $winter, $spring, $summer);
            }
        } else {
            
            // this is a plan has all plans already saved, so only need update statement here
            foreach ($plans->getPlansArray() as &$plan) {

                // setup the plan table name
                $planyear = 'adviseit_'.$plan->getYear();
                // create sql statement to update plan
                $sql = "UPDATE " . $planyear . " SET fall = :fall, winter = :winter, spring = :spring, summer = :summer 
                        WHERE token = :token";

                $token = $plans->getToken();
                $fall = $plan->getFall();
                $winter = $plan->getWinter();
                $spring = $plan->getSpring();
                $summer = $plan->getSummer();

                $statement = $this->_db->prepare($sql);
                $statement->bindParam(':token', $token);
                $statement->bindParam(':fall', $fall);
                $statement->bindParam(':winter', $winter);
                $statement->bindParam(':spring', $spring);
                $statement->bindParam(':summer', $summer);

                // execute sql
                $statement->execute();
            }
        }
    }


    /**
     * Save / update a pre-existing plan
     * @param $token_obj Token object
     */
    function updateToken($token_obj)
    {
        // create sql statement to update Token obj
        $sql = "UPDATE adviseit_tokens
                SET advisor = :advisor, saved = :saved, last_saved = NOW() 
                WHERE token = :token";

        $token = $token_obj->getToken();
        $advisor = $token_obj->getAdvisor();
        $saved = '1';

        $statement = $this->_db->prepare($sql);
        $statement->bindParam(':token', $token);
        $statement->bindParam(':advisor', $advisor);
        $statement->bindParam(':saved', $saved);

        // execute sql
        $statement->execute();
    }


    /**
     * getToken getter, gets token and it's data from database
     * @param $token String, 6 digit token
     * @return boolean: false if plan was not present, array of values if present
     */
    function getToken($token)
    {
        $sql = "SELECT token, created, last_saved, advisor, saved
                FROM adviseit_tokens WHERE token = :token";

        $statement = $this->_db->prepare($sql);
        $statement->bindParam(':token', $token);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        // invalid token
        if (sizeof($result) == 0) {
            return false;
        }

        // return token and its data
        return $result[0];
    }


    /**
     * returns all years labels available in database
     * @return array, array of strings
     */
    function getYears()
    {
        return array(
            '2020',
            '2021',
            '2022',
            '2023',
            '2024',
            '2025',
            '2026',
            '2027',
            '2028',
            '2029',
            '2030',
            '2031',
            '2032',
            '2033',
            '2034',
            '2035',
            '2036',
            '2037',
            '2038',
            '2039'
        );
    }


    /**
     * Get Token obj from database
     * @param $token, string of a 6 char token
     * @return Token object
     */
    function getTokenObj($token)
    {
        // get token data from database
        $token_from_db = $this->getToken($token);

        // no match, return false
        if ($token_from_db == false) {
            return false;
        }

        // setup Token obj, and set the saved info
        $token_obj = new Token($token_from_db['token'], $token_from_db['advisor'], $token_from_db['last_saved']);
        $token_obj->setlastSaved($token_from_db['last_saved']);
        $token_obj->setSaved($token_from_db['saved']);

        // add all plans available to it in an array inside it
        $token_obj = $this->getAllPlans($token_obj);

        return $token_obj;
    }


    /**
     * getPlan getter, gets entire plan from db as Plan object
     * @param $token_obj Token object
     * @return boolean: false if plan was not present, Token object if token present
     */
    function getAllPlans($token_obj)
    {
        // set token var
        $token = $token_obj->getToken();
        // remove plans
        $token_obj->emptyPlans();

        // iterate plan tables and select from available ones
        foreach ($this->getYears() as $year) {
            // create table name
            $year_table = 'adviseit_'.$year;

            $sql = "SELECT fall, winter, spring, summer, token
                    FROM " . $year_table . " WHERE token = :token";

            $statement = $this->_db->prepare($sql);
            $statement->bindParam(':token', $token);
            $statement->execute();

            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            // valid plan, load a Plan into the token_obj
            if (sizeof($result) != 0) {
                $result = $result[0];
                // load with info that wasn't in db
                $result['year'] = $year;

                $token_obj->addPlan(new Plan($result));
            }
        }

        // return valid Token obj
        return $token_obj;
    }


    /**
     * Check database for user authenticity fo creds given
     * @param $user, string, user name
     * @param $hash, sha256 hash of user's password
     */
    function authUser($user, $hash) 
    {   
        // do sql of username / password
        $sql = "SELECT username FROM admins WHERE hash = :hash";

        $statement = $this->_db->prepare($sql);
        $statement->bindParam(':hash', $hash);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        // invalid admin user if no return
        if (sizeof($result) == 0) {
            return false;
        }

        // return exists, cehck if user matches
        if ($result[0]['username'] == $user) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Checks if token is unique
     * @param $token, String: 6 digit token
     * @return boolean, true if unique, false if not
     */
    function tokenIsUnique($token)
    {
        // create and run sql to check if token is present
        $sql = "SELECT token FROM adviseit_tokens WHERE token = :token";

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
        $sql = "DELETE FROM adviseit_tokens WHERE saved = 0 AND last_saved + INTERVAL 1 DAY < NOW()";
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
