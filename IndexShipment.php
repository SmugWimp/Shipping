<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
 ?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="Design.css">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Index Shipments</title>
</head>
<body>
	<?php
		//Require all files needed
		require_once('Functions/PHPFunc.php');
		require_once('Functions/SQLFunc.php');
		
		//determine if user is logged in
		if(isset($_SESSION['uname'])){
			//include navbar and search form
			include('NavBar.html');
			include('ViewForm.php');
			
			//Determine if form is submitted
			if(isset($_POST['Submit'])){
				
				//set variables from form
				$client = check_input($_POST['client']);
				$carrier = check_input($_POST['carrier']);
				$startdate = check_input($_POST['startdate']);
				$enddate = check_input($_POST['enddate']);
				$status = check_input($_POST['status']);
				$tracknum = check_input($_POST['tracknum']);
				$limit = $_POST['limit'];
				$order = $_POST['order'];
				
				//pass form elements through search function
				$shipID = searchShipments($limit, $order, $client, $carrier, $startdate, $enddate, $status, $tracknum);
				
				//determine if there are any results
				$count = count($shipID);
				if($count>0){
	?>
		<!--Create a table to house the results-->
		<table id="searchresult">
			<thead>
				<tr>
					<th colspan=7>Shipment Search Results</th>
				</tr>
			
				<tr>
					<td class="a">Client</td>
					<td>Items Shipping</td>
					<td>Est Delivery Date</td>
					<td>Delivery Status</td>
					<td>Carrier</td>
					<td class='f'>Tracking Number</td>
					<td class="g">Notes</td>
				</tr>
			</thead>
	<?php
					//retrieve and display information
					$shipview = array();
					foreach($shipID as $id){
						$data = ViewShipments($id);
						$shipview[]=$data;
					}
					foreach($shipview as $s){
						$shipmentID = $s->getShipmentID();
						$clientID = $s->getClientID();
						$client = $s->getClient();
						$items = $s->getItems();
						$estdel = $s->getEstdel();
						$status = $s->getStatus();
						$carrierID = $s->getCarrierID();
						$carrier = $s->getCarrier();
						$tracknum = $s->getTracknum();
						$notes = $s->getNotes();
						$entered = $s->getEntered();
						echo"<tr>
								<td class='a'>$client</td>
								<td>$items</td>
								<td>$estdel</td>
								<td>$status</td>
								<td>$carrier</td>
								<td class='f'>$tracknum</td>
								<td class='g'>$notes</td>
							</tr>";
					}
					echo"</table>";
					echo"<p>&nbsp;</p>";	
					echo"<p>&nbsp;</p>";	

				}else{
	?>
			<h2>No Results Found</h2>
			<p>Please retry your search or contact your system administrator</p>
	<?php
				}
			}
		}else{
			include('Login.html');
			echo"<h2>Your session has expired due to innactivity</h2>";
			echo"<p>Please enter your credentials below to log in.</p>";
		}
	?>
	<p>&nbsp;</p>
</body>
</html>