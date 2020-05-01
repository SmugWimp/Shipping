<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

	// session_start();  
	/*This file contains several PHP functions to be used throughout the application*/
	require_once('SQLFunc.php');
	
	//Function to sanitize user input
	function check_input($data){
	    $data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		if(substr_count(strtolower($data), " or ")>0){
			$data=null;
		}
	    return $data;
	}
	
	//Function to authenticate user login
	function userAuth($uname,$pass){
	
		$count=0;
		//Establish query
		$Myquery = "SELECT * FROM Users WHERE Username='".$uname."';"; 
		
		//Connect to the database and perform query
		$mysqli = connectdb();
		$result=$mysqli->query($Myquery);     
		while($row = $result->fetch_assoc() ){
			$passcheck = password_verify($pass, $row["Password"]);
			if($passcheck == true){
				$count=1;
			}
		}
		//close query and connection
		$result->close();	      
		$mysqli->close();
		return $count;
	       
	}
	
	//Function to verify user's access level
	function verifyLevel($uname){

		//Establish query
		$selectquery="SELECT * FROM Users WHERE Username='".$uname."';";
		
		//Connect to database and perform query
		$mysqli=connectdb();
		$result=$mysqli->query($selectquery);
		while($row=$result->fetch_assoc()){
			$level=$row['AccessLevel'];
		}
		//close query and connection
		$result->close();
		$mysqli->close();
		return $level;
	}
	
	//Function to start Session
	function login($uname, $pass, $level){
		$_SESSION['uname'] = $uname;
		$_SESSION['pass'] = $pass;
		$_SESSION['level'] = $level;
		$time = $_SERVER['REQUEST_TIME'];
		$timeout_duration = 1800;  
		$_SESSION['LAST_ACTIVITY'] = $time;
	}

	//Function to logout user
	function logout($uname){
		unset($_SESSION['uname']);
		unset($_SESSION['pass']);
		echo"<h1>$uname, you have been successfully logged out.</h1>";
		session_destroy();
	}
	
	//Class to organize shipments into objects for review
	class ShipmentClass{
		//Property declaration    
		private	$shipID = "";
		private $clientID = ""; 
		private $client = "";	 
		private $items = "";	 
		private $estdel = "";	 
		private $status = "";	 
		private $carrierID = "";	 
		private $carrier = "";	 
		private $tracknum = "";	 
		private $notes =  "";	 
		private $entered = "";
   
		//Constructor
		public function __construct($shipmentID, $clientID, $client, $items, $estdel, $status, $carrierID, $carrier, $tracknum, $notes, $entered){
			$this->shipmentID = $shipmentID;
			$this->clientID = $clientID;
			$this->client = $client;
			$this->items = $items;      
			$this->estdel = $estdel;
			$this->status = $status;
			$this->carrierID = $carrierID;
			$this->carrier = $carrier;
			$this->tracknum = $tracknum;
			$this->notes = $notes;
			$this->entered = $entered;
		}
		 //Getter methods 
		public function getShipmentID(){
			return $this->shipmentID;
		} 
		public function getClientID(){
			return $this->clientID;
		} 
		public function getClient(){
			return $this->client;
		} 
		public function getItems(){
			return $this->items;
		} 
		public function getEstdel(){
			return $this->estdel;
		} 	 
		public function getStatus(){
			return $this->status;
		} 	 
		public function getCarrierID(){
			return $this->carrierID;
		} 	 
		public function getCarrier(){
			return $this->carrier;
		} 	 
		public function getTracknum(){
			return $this->tracknum;
		} 	 
		public function getNotes(){
			return $this->notes;
		} 	 
		public function getEntered(){
			return $this->entered;
		} 	   

		//Setter methods 
		public function setShipmentID($value){
			$this->shipmentID = $value;    	
		}
		public function setClientID($value){
			$this->clientID = $value;    	
		}
		public function setClient($value){
			$this->client = $value;    	
		}
		public function setItems($value){
			$this->items = $value;    	
		}
		public function setEstdel($value){
			$this->estdel = $value;    	
		}  
		public function setStatus($value){
			$this->status = $value;    	
		}  
		public function setCarrierID($value){
			$this->carrierID = $value;    	
		}  
		public function setCarrier($value){
			$this->carrier = $value;    	
		}  
		public function setTracknum($value){
			$this->tracknum = $value;    	
		}  
		public function setNotes($value){
			$this->notes = $value;    	
		}  
		public function setEntered($value){
			$this->entered = $value;    	
		}  
	}
?>