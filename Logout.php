<?php
	
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>
<html>
<head>
</head>
<body>
	<?php
		require_once('Functions/PHPFunc.php');
		
		logout($_SESSION['uname']);
		include('Login.html');
	?>
</body>
</html>