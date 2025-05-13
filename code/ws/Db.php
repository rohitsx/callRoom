<?php

function get_db_connection()
{

	$servername = "mysql";
	$username = "myuser";
	$password = "pass123";
	$dbname = "mydb";


	$conn = new \mysqli($servername, $username, $password, $dbname);

	if ($conn->connect_error) {
		die("db connection failed: " . $conn->connect_error);
	}

	try {
		return $conn;
	} catch (exception $e) {
		echo ('message: ' . $e->getmessage());
	}
}
