<?php
include 'db.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
$route = str_replace('/api', '', $_SERVER['REQUEST_URI']);

$sdp = json_encode($input["sdp"]);
$roomId = $input["roomId"];
$type = $input["type"];


try {
	switch ($route) {
		case "/add-sdp":
			include "./routehandler/addSdp.php";
			break;
		case "/join-room":
			include "./routehandler/joinRoom.php";
			break;
		case "/check-answer":
			include "./routehandler/checkAnswer.php";
			break;
		case "/leave-room":
			include "./routehandler/leaveRoom.php";
			break;
	}
} catch (Exception $e) {
	echo $e->getMessage();
}


$conn->close();
