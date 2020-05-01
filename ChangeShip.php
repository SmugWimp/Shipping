<?php session_start(); ?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="Design.css">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Update Shipment</title>
</head>
<body>
	<?php
		//include all necessary files
		require_once('Functions/PHPFunc.php');
		require_once('Functions/UserFunc.php');
		
		//Determine is session is expired
		if(isset($_SESSION['uname'])){
			//verify access level
			$uname = $_SESSION['uname'];
			// echo("<p>" . $_SESSION['level'] . "</p>");
			if($_SESSION['level']>=2){
				include('NavBar.html');
				include('UpdateShipForm.php');
				//determin if form was submitted
				if(isset($_POST['Submit'])){
					
					//check and set all form varaibles
					$client = check_input($_POST['client']);
					$carrier = check_input($_POST['carrier']);
					$estdel = check_input($_POST['estdel']);
					$status = check_input($_POST['status']);
					$tracknum = check_input($_POST['tracknum']);
					
					//update record and determine if successful
					if(updateShip($tracknum, $client, $carrier, $estdel, $status)){	
						echo "<h3>Shipment record with the tracking number $tracknum successfully updated.</h3>";
					}else{
						//Notify user of general error
						echo "<h2>An error occured when searching for shipment (Track#: $tracknum)</h3>";
						echo "<p>Please try again, change some parameters, or contact your system administrator.</p>";
					}
				}
			}else{
				//Notify user of insufficient permissions
				echo"<h2>You do not have permission to modify the user account $uname.</h2>";
				echo"<p> Please verify you have authority to modify this account or contact your administrator.</p>";
			}
		}else{
			include('Login.html');
			echo"<h2>Your session has expired due to innactivity</h2>";
			echo"<p>Please enter your credentials below to log in.</p>";
		}
	?>
</body>
</html>