<?php

function get_db_connection()
{

	$servername = "127.0.0.1";
	$username = "myuser";
	$password = "pass123";
	$dbname = "mydb";


	$conn = new \mysqli($servername, $username, $password, $dbname);

	if ($conn->connect_error) {
		die("db connection failed: " . $conn->connect_error);
	}

	try {
		/* $sql = "CREATE TABLE room ( */
		/*   id SERIAL PRIMARY KEY, */
		/*   room_id VARCHAR(255) NOT NULL UNIQUE, */
		/*   client_id INTEGER NOT NULL, */
		/*   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP */
		/* );	"; */
		/**/
		/* if ($conn->query($sql) !== true) { */
		/* 	echo ("error creating table: " . $conn->error); */
		/* 	exit(); */
		/* } */
		/**/
		/* echo ("table room created successfully"); */
		return $conn;
	} catch (exception $e) {
		echo ('message: ' . $e->getmessage());
	}
}
