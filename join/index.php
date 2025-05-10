<!DOCTYPE html>
<html>

<head>
	<title>Join Room</title>
</head>

<body>
	<?php
	$roomId = $_GET['room-id'];
	echo "<h1>Room $roomId</h1>";
	echo "<script>const roomId = " . json_encode($roomId) . ";</script>";
	?>
	<video id="localVideo" autoplay playsinline muted />
	<script src="../assets/js/joinRoom.js"></script>
</body>

</html>
