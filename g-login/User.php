<?php
class User {
	private $dbHost     = "localhost";
    private $dbUsername = "hk3693_pklist";
    private $dbPassword = "Chitra@3693";
    private $dbName     = "hk3693_brandoholic";
    private $userTbl    = 'users';
	
	function __construct(){
        if(!isset($this->db)){
            // Connect to the database
            $conn = new mysqli($this->dbHost, $this->dbUsername, $this->dbPassword, $this->dbName);
            if($conn->connect_error){
                die("Failed to connect with MySQL: " . $conn->connect_error);
            }else{
                $this->db = $conn;
            }
        }
    }
	
	function checkUser($userData = array()){
        if(!empty($userData)){
            //Check whether user data already exists in database
            $prevQuery = "SELECT * FROM ".$this->userTbl." WHERE email = '".$userData['email']."'";
            $prevResult = $this->db->query($prevQuery);
            if($prevResult->num_rows > 0){
                //Update user data if already exists
                $query = "UPDATE ".$this->userTbl." SET fname = '".$userData['first_name']."', lname = '".$userData['last_name']."', email = '".$userData['email']."', country = '".$userData['country']."' , gender = '".$userData['gender']."', oauth_provider = '".$userData['oauth_provider']."' , oauth_uid = '".$userData['oauth_uid']."'  WHERE email = '".$userData['email']."'";
                $update = $this->db->query($query);
            }else{
                //Insert user data
                $query = "INSERT INTO ".$this->userTbl." SET oauth_provider = '".$userData['oauth_provider']."', oauth_uid = '".$userData['oauth_uid']."', country = '".$userData['country']."',  fname = '".$userData['first_name']."', lname = '".$userData['last_name']."', email = '".$userData['email']."', gender = '".$userData['gender']."',  date = '".date("Y-m-d H:i:s")."', source = 'Google'";
                $insert = $this->db->query($query);
            }
            
            //Get user data from the database
            $result = $this->db->query($prevQuery);
            $userData = $result->fetch_assoc();
        }
        
        //Return user data
        return $userData;
    }
}
?>