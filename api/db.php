<?php
$servername = "127.0.0.1";
$username = "myuser";
$password = "pass123";
$dbname = "mydb";


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
	die("db Connection failed: " . $conn->connect_error);
}

try {
	$sql = "CREATE TABLE room (
    id SERIAL PRIMARY KEY,
    room_id VARCHAR(255) NOT NULL UNIQUE,
    request_type ENUM('offer', 'answer') NOT NULL,
    sdp TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
		);
	";

	if ($conn->query($sql) !== TRUE) {
		error_log("Error creating table: " . $conn->error);
		exit();
	}

	error_log("Table room created successfully");
} catch (Exception $e) {
	error_log('Message: ' . $e->getMessage());
}
