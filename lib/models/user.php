<?php

/**
 * User Model
 *
 * This class contains all of the functions used for creating, managing and deleting
 * users.
 *
 * @package    jQuery Mobile PHP MVC Micro Framework
 * @author     Monji Dolon <md@devgrow.com>
 * @copyright  2011-2012 Monji Dolon
 * @license    http://www.gnu.org/licenses/gpl.html  GNU General Public License (GPL) v3
 * @link       http://devgrow.com/jquery-mobile-php-mvc-framework/
 */

class UserModel {

    var $user_id;
    var $name;
    var $email;
    var $password;
    var $ok;
    var $msg;
    var $is_logged;

    /**
     * Set all internal variables to 'Guest' status, then check to see if
     * a user session or cookie exists.
     */
    function __construct(){
        global $db;
        
        $this->user_id = 0;
        $this->email = "Guest";
        $this->name = "Guest";
        $this->ok = false;

        if(!$this->check_session()) $this->check_cookie();

        return $this->ok;
    }
    
    /**
     * This function checks to see whether or not a PHP Session is set.
     */
    function check_session(){
        if(!empty($_SESSION['auth_email']) && !empty($_SESSION['auth_secret']))
            return $this->check($_SESSION['auth_email'], $_SESSION['auth_secret']);
        else
            return false;
    }


    /**
     * Check to see if any cookies exist on the user's computer/browser.
     */
    function check_cookie(){
        if(!empty($_COOKIE['auth_email']) && !empty($_COOKIE['auth_secret']))
            return $this->check($_COOKIE['auth_email'], $_COOKIE['auth_secret']);
        else
            return false;
    }
    
    /**
     * Create a user and by default, log them in once the account has been created.
     *
     * @param   $info       An array that contains the following info about the user:
     *                       - name, email, password, password2 (password repeated), status (optional)
     * @param   $login      Bool, whether or not to log the user in after creating account.
     */
    function create($info,$login = true){
        global $db;

        // Escape the info fields and hash the password using the salt specified in config.php
        $name = mysql_real_escape_string($info['name']);
        $email = mysql_real_escape_string($info['email']);
        $password = md5(mysql_real_escape_string($info['password']) . PASSWORD_SALT);

        // If user status isn't set, assume default status (1)
        $status = $info['status'] ? mysql_real_escape_string($info['status']) : 1;

        // Store the IP address that the user create's the account with.
        $create_ip = $_SERVER['REMOTE_ADDR'];

        // Reset flag used for error detection.
        $this->ok = false;
        
        // Validate all of the user input fields.
        if(!$info['name'] || !$info['email'] || !$info['password'] || !$info['password2']){
            $this->msg = "Error! All fields are required.";
            return false;
        }elseif($info['password'] != $info['password2']){
            $this->msg = "Error! Passwords do not match.";
            return false;
        }elseif(!$this->validEmail($info['email'])){
            $this->msg = "Error! Please enter a valid e-mail address.";
            return false;
        }
        
        // Check to see if a user with that email address already exists.       
        $query = $db->prepare("SELECT id, password FROM users WHERE email = '".mysql_real_escape_string($email)."'");
        $query->execute();
        if($query->rowCount() == 1){
            $this->msg = "Error! E-mail address is already in use.";
        }else{
            // User doesn't exist, so create a new account!
            $query = $db->prepare("INSERT INTO users (name,email,password,status,create_ip) VALUES ('$name','$email','$password','$status','$create_ip')");
            $query->execute();
            $this->msg = "User successfully added.";
            $this->ok = true;
            if($login) $this->login($info['email'],$info['password']);
            return true;
        }
        return false;
    }
    
    /**
     * Update a user's information.
     *
     * @param   $info       An array that contains the following info about the user:
     *                       - name, email, password, password2 (password repeated), status (optional)
     */
    function update($info) {
        global $db;

        // Reset our error detection flag, which is used to set the status message later on.
        $this->ok = false;
        
        // Escape variables that are present by default.
        $name = mysql_real_escape_string($info['name']);
        $email = mysql_real_escape_string($info['email']);
        
        // Validate email address again.
        if(!$this->validEmail($info['email'])) {
            $this->msg = "Error! Please enter a valid e-mail address.";
            return false;
        }

        // Start building the SQL query with the data submitted so far.
        $sql = "name='$name', email='$email'";

        // If a password has been entered, validate it, re-hash it and add it to the SQL query.
        if($info['password']){
            if($info['password'] != $info['password2']){
                $this->msg = "Error! Passwords do not match.";
                return false;
            }
            $password = md5(mysql_real_escape_string($info['password']) . PASSWORD_SALT);
            $sql .= ", password='$password'";
        }

        // Create the finalized SQL query that will update our database.
        $sql = "UPDATE users SET ".$sql." WHERE id = '".$this->user_id."'";
        $query = $db->prepare($sql);

        // Successfully updated the user data.
        if($query->execute()) {
            // Let the user know via a cheeky message (OK not really cheeky).
            $this->msg = "Info successfully updated.";

            // Set user status flag back to true, peace has been restored.
            $this->ok = true;

            // Set new email and password info in the session and cookies.
            $_SESSION['auth_email'] = $email;
            if($info['password']) $_SESSION['auth_secret'] = $password;
            setcookie("auth_email", $email, time()+60*60*24*30, "/", COOKIE_DOMAIN);
            if($info['password']) setcookie("auth_secret", $password, time()+60*60*24*30, "/", COOKIE_DOMAIN);

            // Update local variables to reflect new changes.
            $this->name = $name;
            $this->email = $email;

            return true;
        } else {
            // There seems to have been a problem with the query somewhere.
            $this->msg = "There was a problem, please try again.";
        }
        return false;
    }

     /**
     * Function used to let hte user login, checking their email and password against
     * what's stored in the database.
     *
     * @param   $email      The user's email address.
     * @param   $password   The user's password, directly from POST.
     */
    function login($email, $password) {
        global $db;

        // One of the fields is missing, deliver an error message.
        if(!$email || !$password) {
            $this->msg = "Error! Both E-mail and Password are required to login.";
            return false;
        }

        // Get user data using the email address supplied.
        $query = $db->prepare("SELECT id, password, name FROM users WHERE email = '".mysql_real_escape_string($email)."'");
        $query->execute();

        // Set our user flag to false.
        $this->ok = false;

        // Fetch all results and process the data if the row exists.
        $results = $query->fetchAll();
        if(count(results) == 1) {
            // Get the salted and hashed password stored in the database.
            $db_password = $results[0]['password'];

            // Salt the current password and if it matches the stored password,
            // proceed with logging in the user.
            if(md5($password . PASSWORD_SALT) == $db_password) {

                // Set session and cookie information.
                $_SESSION['auth_email'] = $email;
                $_SESSION['auth_secret'] = md5($results[0]['id'] . $results[0]['email']);
                setcookie("auth_email", $email, time()+60*60*24*30, "/", COOKIE_DOMAIN);
                setcookie("auth_secret", md5($results[0]['id'] . $results[0]['email']), time()+60*60*24*30, "/", COOKIE_DOMAIN);

                // Set local variables with the user's info.
                $this->user_id = $results[0]['id'];
                $this->name = $results[0]['name'];
                $this->email = $email;
                $this->ok = true;
                $this->is_logged = true;

                // Set status message.
                $this->msg = "Login Successful!";

                return true;
            } else {
                $this->msg = "Error! Password is incorrect.";
            }
        } else {
            $this->msg = "Error! User does not exist.";
        }
        return false;
    }
    
    /**
     * This function checks the session/cookie info to see if it's real by comparing it
     * to what is stored in the database.
     *
     * @param   $email      The user's email address stored in session/cookie.
     * @param   $secret     The user's secret hash, a combination of their user id (from DB)
     *                      and their email address.
     */
    function check($email, $secret) {
        global $db;

        // Get the user's info from the database.
        $query = $db->prepare("SELECT id, password, name FROM users WHERE email = '".mysql_real_escape_string($email)."'");
        $query->execute();

        $results = $query->fetchAll();
        if(count($results) == 1)
        {
            if(md5($results[0]['id'] . $results[0]['email']) == $secret) {
                $this->user_id = $results[0]['id'];
                $this->email = $email;
                $this->name = $results[0]['name'];
                $this->ok = true;
                $this->is_logged = true;
                return true;
            }
        }           
        return false;
    }

    /**
     * Check to see if the user is logged in based on their session data.
     */
    function is_logged(){
        if($this->check($_SESSION['auth_email'], $_SESSION['auth_secret'])) return true;
        else return false;
    }
    
    /**
     * Get a user's information from the database.
     *
     * @param   $field      The field value to retrieve (if left blank, will return complete row)
     * @param   $email      The user's email address. If not specified, will load current user's info.
     */
    function get_info($field = "*", $email = null){
        global $db;

        if(!$email) $email = $this->email;
        $query = $db->query("SELECT $field FROM users WHERE email = '$email'");
        $query->execute();
        $results = $db->fetchAll();
        if($field == "*") return $results[0];
        else return $results[0][$field];
    }
    
    /**
     * Log out the current user by setting all the local variables to their
     * default values and resetting our PHP session and cookie info.
     */ 
    function logout(){
        $this->user_id = 0;
        $this->email = "Guest";
        $this->name = "Guest";
        $this->ok = true;
        $this->msg = "You have been logged out!";
        $this->is_logged = false;
        
        $_SESSION['auth_email'] = "";
        $_SESSION['auth_secret'] = "";
        setcookie("auth_email", "", time() - 3600, "/", COOKIE_DOMAIN);
        setcookie("auth_secret", "", time() - 3600, "/", COOKIE_DOMAIN);
    }

    /**
     * Validate the user's email address.
     * Courtesy LinuxJournal.com : http://www.linuxjournal.com/article/9585?page=0,3
     *
     * @param   $email      The email address to validate.
     */
    function validEmail($email){
        $isValid = true;
        $atIndex = strrpos($email, "@");
        if (is_bool($atIndex) && !$atIndex){
            $isValid = false;
        }
        else{
            $domain = substr($email, $atIndex+1);
            $local = substr($email, 0, $atIndex);
            $localLen = strlen($local);
            $domainLen = strlen($domain);
            if ($localLen < 1 || $localLen > 64){
                $isValid = false;
            }else if ($domainLen < 1 || $domainLen > 255){
                $isValid = false;
            }else if ($local[0] == '.' || $local[$localLen-1] == '.'){
                $isValid = false;
            }else if (preg_match('/\\.\\./', $local)){
                $isValid = false;
            }else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)){
                $isValid = false;
            }else if (preg_match('/\\.\\./', $domain)){
                $isValid = false;
            }else if(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local))){
                if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local))){
                    $isValid = false;
                }
            }
            if ($isValid && !(checkdnsrr($domain,"MX") ||  checkdnsrr($domain,"A"))){
                $isValid = false;
            }
        }
        return $isValid;
    }
}
?>