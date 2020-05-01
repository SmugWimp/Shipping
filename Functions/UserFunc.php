<?php
	//File containt PHP fucntions to interact with MySQL database
	require_once('SQLFunc.php');
	
	//Define global variables
	$host= 'localhost';
	
	//Function to add a new user
	function addUser($uname, $pass, $level){
		//Deinfe variables and SQL statements
		$success=false;
		$host= 'localhost';
		$pass=password_hash($pass, PASSWORD_DEFAULT);
		$select="SELECT * FROM Shipping.Users WHERE Username='".$uname."';";
		$newUser="CREATE USER '".$uname."'@'" . $host . "' IDENTIFIED BY '".$pass."';";
		$usertable="INSERT INTO Shipping.Users VALUES ('". $uname ."','". $pass ."','". $level ."');";
		
		//establish connection and perform queries
		$mysqli=connectdb();
		if($check=$mysqli->query($select)){
			$count=mysqli_num_rows($check);
			if($count==0){
				$results=$mysqli->query($newUser);
				switch($level){
					//Prepare statement for correct user access level
					case '1':
						$access = "GRANT SELECT on Shipping.* to '" .$uname ."'@'". $host ."';";
						break;
					case '2':
						$access="GRANT SELECT, INSERT, UPDATE on Shipping.* to '".$uname."'@'".$host."';";
						break;
					case '3':
						$access="GRANT ALL on Shipping.* to '".$uname."'@'".$host."';";
						break;
				}
				$privileges=$mysqli->query($access);
				if($tableup=$mysqli->query($usertable)){
					$success=true;
				}
			}
		}
		//close all queries and connections
		$mysqli->close();
		return $success;
	}
	
	//Function to change user password
	function passChange($uname, $pass, $newpass){
		//Establish variable and SQL statements
		$success=false;
		$host= 'localhost';
		$haspass= password_hash($newpass, PASSWORD_DEFAULT);
		$alterUser="ALTER USER '".$uname."'@'".$host."' IDENTIFIED BY '".$newpass."';";
		$passconf = "SELECT * FROM Shipping.Users WHERE Username='".$uname."';";
		$usertable="UPDATE Shipping.Users SET Password='".$haspass."' WHERE Username='".$uname."';";
		//Establish connection and perform queries
		$mysqli=connectdb();
		if($check=$mysqli->query($passconf)){
			$passcheck = password_verify($pass, $row['Password']);
			if($passcheck==true){
				if($results=$mysqli->query($usertable)){
					$lines=$results->rows_affected;
						if($lines==1){
							$success=true;
						}
				}
			}
		}
		$mysqli->query($alterUser);
		//close all queries and connections
		$mysqli->close();
		return $success;
	}
	
	//Function to delete users from database
	function deleteUser($uname){
		//Prepare queries to remove user and revoke privileges
		$host= 'localhost';

		$privileges="REVOKE ALL on Shipping.* FROM '".$uname."'@'".$host."';";
		$dropUser="DROP USER '".$uname."'@'".$host."';";
		//Establish connection and perform queries
		$mysqli=connectdb();
		$resultP=$mysqli->query($privileges);
		$lines=$resultP->rows_affected;
		if($lines==1){
			echo "Privileges revoked";
			$finalresults=$mysqli->query($dropUser);
			$finalLines=$finalresults->rows_affected;
			if($finalLines==1){
				echo "User deleted";
			}else{
				echo "User not deleted";
			}
		}else{
			echo "Privileges intact";
		}
	}
?>