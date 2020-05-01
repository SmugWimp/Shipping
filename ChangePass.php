<?php 

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

 ?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="Design.css">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Update User Form</title>
</head>
<body>
	<?php
		require_once('Functions/PHPFunc.php');
		require_once('Functions/UserFunc.php');
		//Determine if session is active
		if(isset($_SESSION['uname'])){
			include('NavBar.html');
			include('UpdateUserForm.html');
			//Determine if form has been submitted
			if(isset($_POST['submit'])){
				//Ensure all fields are filled
				if(isset($_POST['uname']) && isset($_POST['pass']) && isset($_POST['newpass1']) && isset($_POST['newpass2'])){
					//verify user has proper permissions
					if($_SESSION['level']>2 || $_POST['uname']==$_SESSION['uname']){
						//verify password matches the confirmed password
						if($_POST['newpass1'] == $_POST['newpass2']){
							$uname=check_input($_POST['uname']);
							$pass=check_input($_POST['pass']);
							$newpass=check_input($_POST['newpass1']);
							//Determine if query is successful
							if(passChange($uname, $pass, $newpass)){
								//Display success message
								echo "<h3>User credentials were successfully updated for $uname.</h3>";
							}else{
								//Notify user of general error
								echo"<h2>An error occured when updating $uname's credentials.</h3>";
								echo"<p>Please try again or contact your system administrator.</p>";
							}
						}else{
							//Display error for new password and confirmation not matching
							echo"<h2>Please verify both new passwords match.</h2>";
						}
					}else{
						//Notify user of insufficient permissions
						echo"<h2>You do not have permission to modify the user account $uname.</h2>";
						echo"<p> Please verify you have authority to modify this account or contact your administrator.</p>";
					}
				}else{
					//Notoify user all fields not filled
					echo"<h2>Please verify all fields are filled.</h2>";
				}
			}
		}else{
			//display login form for expired sessions
			include('Login.html');
			echo"<h2>Your session has expired due to innactivity</h2>";
			echo"<p>Please enter your credentials below to log in.</p>";
		}
	?>
</body>
</html>