<?php
	define("DB_HOST", "localhost");
	define("DB_NAME", "shipping");
	define("DB_USER", "root");
	define("DB_PASS", "");
	//
	// Pacific/Guam
	putenv("TZ=Pacific/Guam");
	date_default_timezone_set("Pacific/Guam");
?>
