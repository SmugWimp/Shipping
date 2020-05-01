<?php
	/*This file contains PHP functions for the application to interact with the MySQL database.
	Each function establishes a connection to the database and interacts using predefined querries and stored procedures*/

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

	//File with general php functions
	require_once('PHPFunc.php');
	require_once('dbcred.php');

	//Establish global variables
	static $host = 'localhost';
	static $db = 'Shipping';
	
	
	//Function for general login
	function connectdb(){      		
		//Get the DB Parameters
		$mydbparms = getDbparms();
		$success = false;
		//Try to connect to database
		$mysqli = new mysqli($mydbparms->getHost(), $mydbparms->getUsername(), 
	                        $mydbparms->getPassword(),$mydbparms->getDb());
	
		if ($mysqli->connect_error) {
			die('Connect Error (' . $mysqli->connect_errno . ') '
	            . $mysqli->connect_error);      
		}else{
		}
		return $mysqli;
	}
	
	//Function to retrieve preset db login info
	function getDbparms(){
		//Assign values to Parameters Class
		$myDbparms = new DbparmsClass(DB_USER, DB_PASS, DB_HOST, DB_NAME);
	
		//Display the Paramters values
		return $myDbparms;
	}

	//Class to construct database parameters with getters & setters
	class DBparmsClass{
		
	    //Property declarations  
	    private $username="";
	    private $password="";
	    private $host="";
	    private $db="";
	   
	    //Constructor
	    public function __construct($myusername,$mypassword,$myhost,$mydb){
			$this->username = $myusername;
			$this->password = $mypassword;
			$this->host = $myhost;
			$this->db = $mydb;
	    }
	    
	    //Getter methods 
		  public function getUsername (){
	    	return $this->username;
	    } 
		  public function getPassword (){
	    	return $this->password;
	    } 
		  public function getHost (){
	    	return $this->host;
	    } 
		  public function getDb (){
	    	return $this->db;
	    } 	 
	
	    //Setter methods 
	    public function setUsername ($myusername){
	    	$this->username = $myusername;    	
	    }
	    public function setPassword ($mypassword){
	    	$this->password = $mypassword;    	
	    }
	    public function setHost ($myhost){
	    	$this->host = $myhost;    	
	    }
	    public function setDb ($mydb){
	    	$this->db = $mydb;    	
	    }    
	}
	
	//Function to connect individual users
	function indconnectdb($uname, $pass){
		$mysqli= new mysqli($host, $uname, $pass, $db);
		
		if ($mysqli->connect_error){
			die(Header('Location: FailedLogin.php'));
		}
		return $mysqli;
	}
	
	//Function to enter a new shipment 
	function insertShipment($client, $carrier, $items, $shipdate, $deliverydate, $tracknum, $status, $notes){
		//echo("<p>" . $client . " " . $carrier . " " . $items . " " . $shipdate . " " . $deliverydate . " " . $tracknum . " " . $status . " " . $notes . "</p>");
		//Establish variables
		$success=false;
		$date = date_create();
		$dateNow = date_format($date,"Y-m-d"); 		

	    //Find clientID and carrierID based on information submitted using function from PHPFunc   
		$clientID = findClient($client);
		$carrierID = findCarrier($carrier);
				
		//Connect to the database and execute stored procedure
		$mysqli = connectdb();
		
		$mySQLStatement = "INSERT INTO Shipment (ClientID, CarrierID, ItemsShipping, EstShipDate, EstDelivery, TrackingNum, Status, Notes, DateEntered) VALUES ('$clientID', '$carrierID', '$items', '$shipdate', '$deliverydate', '$tracknum', '$status', '$notes', '$dateNow')"; 
		
		$result = mysqli_query($mysqli,$mySQLStatement);
		//$insertstmt=$mysqli->prepare("CALL insertShip(?,?,?,?,?,?,?,?)");
		//$insertstmt->bind_param("ssssssss", $clientID, $carrierID, $items, $shipdate, $deliverydate, $tracknum, $status, $datenow);
		//$insertstmt->execute();

		// Execute query and determine success
		//$result=$insertstmt->affected_rows;
		
		if($result > 0){
			$success=true;
		}

		//Close query and connection
		//$insertstmt->close();
		$mysqli->close();
		return $success;
	}

	//Function to add new client to the database
	function addClient($business){
		//Establish varaibles
		$success=false;

		//Establish connection
		$mysqli = connectdb();
		$insertstmt=$mysqli->prepare("CALL newClient(?)");
		$insertstmt->bind_param("s", $business);
		$insertstmt->execute();

		//Execute query and determine success
		$result = $insertstmt -> affected_rows;
		
		if($result > 0){
			
			$success=true;
			
		}
		
		//Close query and connection
		$insertstmt->close();
		$mysqli->close();
		return $success;
	}
	
	//Function to return a list of shipments based on user search criteria
	function searchShipments($limit, $order, $client, $carrier, $startdate, $enddate, $status, $tracknum){
	
		//Establish variables
		$count=0;

		//Prepare main select statement
		$select = "SELECT Shipment.ShipmentID, Client.ClientID, Client.BusinessName, Shipment.ItemsShipping, Shipment.EstDelivery, Shipment.Status, Carrier.CarrierID,
			Carrier.CarrierName, Shipment.TrackingNum, Shipment.Notes, Shipment.DateEntered FROM ((Shipment INNER JOIN Client ON Client.ClientID=Shipment.ClientID)
			JOIN Carrier ON Carrier.CarrierID=Shipment.CarrierID) ";

		//Determine which fields have been filled on form
		if($client){
			//Detemine clientID using function from PHPFunc
			$clientID = findClient($client);
			$select= $select. "WHERE Shipment.ClientID='".$clientID."' ";
			$count++;
		}
		if($carrier){
			//Determine carrierID  using function from PHPFunc
			$carrierID = findCarrier($carrier);
			//Determine if previous additions were made to the query
			if($count>0){
				$select= $select. "AND Shipment.CarrierID='".$carrierID."' ";
				$count++;
			}else{
				$select = $select. "WHERE Shipment.CarrierID='".$carrierID."' ";
				$count++;
			}
		}
		if($startdate){
			if($count>0){
				$select = $select. "AND Shipment.DateEntered > '".$startDate."' ";
				$count++;
			}else{
				$select = $select. "WHERE Shipment.DateEntered > '".$startDate."' ";
				$count++;
			}
		}
		if($enddate){
			if($count>0){
				$select = $select. "AND Shipment.DateEntered < '".$endDate."' ";
				$count++;
			}else{
				$select = $select. "WHERE Shipment.DateEntered < '".$endDate."' ";
				$count++;
			}
		}
		if($status){
			if($count>0){
				$select = $select. "AND Shipment.Status='".$status."' ";
				$count++;
			}else{
				$select = $select. "WHERE Shipment.Status='".$status."' ";
				$count++;
			}
		}
		if($tracknum){
			if($count>0){
				$select = $select. "AND Shipment.TrackingNum='".$tracknum."' ";
			}else{
				$select = $select. "WHERE Shipment.TrackingNum='".$tracknum."' ";
			}
		}
		//Assign value to limit
		$select = $select. "ORDER BY DateEntered ".$order." LIMIT ".$limit.";";
		
		//Establish connection
		$mysqli=connectdb();
		//Retrieve search results
		if($result=$mysqli->query($select)){
			while($row=$result->fetch_assoc()){
				$listShipments[]=$row['ShipmentID'];
			}
		}
		
		//Close query and connection
		$mysqli->close();
		return $listShipments;
	}
	
	//Function to display search results
	function viewShipments($shipID){
		
		//Clear values for $allShipments
		$allShipments="";

		//Establish connection and execute stored procedure		
		$mysqli=connectdb();
		$selectstmt=$mysqli->prepare("CALL viewShips(?)");
		$selectstmt->bind_param("s", $shipID);
		$selectstmt->execute();

		//Store query results in an array
		$result=$selectstmt->get_result();
		if($result->num_rows>0){     
			while($row = $result->fetch_assoc()){
				$shipmentID=$row['ShipmentID'];
				$clientID=$row['ClientID'];
				$client=$row['BusinessName'];
				$items=$row['ItemsShipping'];
				$estdel=$row['EstDelivery'];
				$status=$row['Status'];
				$carrierID=$row['CarrierID'];
				$carrier=$row['CarrierName'];
				$tracknum=$row['TrackingNum'];
				$notes=$row['Notes'];
				$entered=$row['DateEntered'];
				
				//Create an object for reference
				$allShipments= new ShipmentClass($shipmentID, $clientID, $client, $items, $estdel, $status, $carrierID, $carrier, $tracknum, $notes, $entered);
			}
		}
		//Close querry and connection
		$selectstmt->close();
		$mysqli->close();
		return $allShipments;
	}
	
	//Function to populate client dropdown
	function statusDropdown() {
		
		echo "<option value='In Transit'>In Transit</option>";
		echo "<option value='Delivered'>Delivered</option>";
		echo "<option value='On Hold'>On Hold</option>";		
	}
	
	function clientDropdown(){

		//Establish connection and perform stored procedure
		$mysqli = connectdb();
		
		 $newResult = mysqli_query($mysqli,"SELECT * FROM client");
		//$newResult = mysqli_query($mysqli,"CALL showClient()");
		
		while($row = mysqli_fetch_array($newResult)){
			echo "<option value='" .$row['BusinessName']. "'>" .$row['BusinessName']. "</option>";
		}
		
		
		//$selectstmt = $mysqli -> prepare("CALL showClient()");
		//$selectstmt -> execute();
		//$result = $selectstmt -> get_result();
		
		//Populate options to dropdown
		//while($row = $result->fetch_assoc()){
		//	echo "<option value='" .$row['BusinessName']. "'>" .$row['BusinessName']. "</option>";
		//}
		//Close query and connection
		//$selectstmt->close();
		$mysqli->close();
	}
	
	//Function to populate carrier dropdown
	function carrierDropdown(){

		$mysqli = connectdb();
		
		 $newResult = mysqli_query($mysqli,"SELECT * FROM carrier");
		
		while($row = mysqli_fetch_array($newResult)){
			echo "<option value='" .$row['CarrierName']. "'>" .$row['CarrierName']. "</option>";
		}

		//Establish connection and perform stored procedure
		//$mysqli=connectdb();
		//$selectstmt=$mysqli->prepare("CALL showCarriers()");
		//$selectstmt->execute();
		//$result=$selectstmt->get_result();

		//Populate options to dropdown
		//while($row=$results->fetch_assoc()){
		//	echo "<option value='" . $row['CarrierName'] . "'>" .$row['CarrierName'] ."</option>";
		//}
		//Close query and connection
		//$selectstmt->close();
		$mysqli->close();
	}
	
	//Function to determine clientID
	function findClient($business){

		$mysqli = connectdb();
		$newResult = mysqli_query($mysqli,"SELECT * FROM client WHERE BusinessName = '$business'");
		while($row = mysqli_fetch_array($newResult)){
			$clientID = $row['ClientID']; //   $row['CarrierName']. "'>" .$row['CarrierName']. "</option>";
		}
		//Prepare initial query
		//$mysqli=connectdb();
		//$selectstmt=$mysqli->prepare("CALL FindClient(?)");
		//$selectstmt->bind_param("s", $business);
		//$selectstmt->execute();
		/*
		if($result=$selectstmt->get_result(){
			
			while($row = $results->fetch_assoc()){
				
				$clientID=$row['ClientID'];
			}

		}
		*/
		//Close query and connection
		//$selectstmt->close();
		$mysqli->close();
		return $clientID;
	}

	
	//Function to determine carrierID
	function findCarrier($carrier){

		$mysqli = connectdb();
		$newResult = mysqli_query($mysqli,"SELECT * FROM carrier WHERE CarrierName = '$carrier'");
		while($row = mysqli_fetch_array($newResult)){
			$carrierID = $row['CarrierID']; //   $row['CarrierName']. "'>" .$row['CarrierName']. "</option>";
		}
		//Prepare initial query
		//$mysqli=connectdb();
		//$selectstmt=$mysqli->prepare("CALL FindCarrier(?)");
		//$selectstmt->bind_param("s",$carrier);
		//$selectstmt->execute();
		//$newResult = $selectstmt -> get_result();
		
		
		//if($result=$selectstmt->get_result(){
		//if ($result = $newResult) {
		
		//while($row=$results->fetch_assoc()){
		//	$carrierID=$row['CarrierID'];
		//}
		//}
		//Close query and connection
		//$selectstmt->close();
		$mysqli->close();
		return $carrierID;
	}

	function updateShip($tracknum, $client, $carrier, $estdel, $status){
		
		//Establish variables and main query
		$count=0;
		$success = false;
		$update = "UPDATE Shipment SET";
		
		//Determine which fields are filled and append the query
//		if($tracknum) {
//			$update = $update . " TrackingNum='" . $tracknum . "'";
//			$count++;
//		} else {
//			//echo("<p>notracknum</p>");
//		}

		if($client){
			$clientID = findClient($client);
			$update = $update . " ClientID='" . $clientID . "'";
			$count++;
		} else {
			//echo("<p>noclient</p>");
		}
		if($carrier){
			$carrierID = findCarrier($carrier);
			if($count > 0) {
				$update = $update. ",";
			}
			$update = $update . " CarrierID = '" . $carrierID . "'";
			$count++;
		} else {
			//echo("<p>nocarrier</p>");
		}
		if($estdel){
			if($count>0){
				$update = $update . ",";
			}
			$update = $update . " EstDelivery = '" . $estdel . "'";
			$count++;
		} else {
			//echo("<p>nodelivery</p>");
		}
		if($status){
			if($count>0){
				$update=$update. ",";
			}
			$update=$update. " Status = '" . $status . "'";
			$count++;
		}
		
		// echo("<p>" . $update . "</p>");
		$update=$update. " WHERE TrackingNum = '" . $tracknum . "';";
		
		//Connect to database and perform query
		$mysqli=connectdb();
		if($result=$mysqli->query($update)){
			$success = true;
		}
		//Close the query and connection
		$mysqli->close();
		return $success;
	}
?>