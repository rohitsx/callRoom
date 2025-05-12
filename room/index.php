<!DOCTYPE html>
<html>

<head>
	<title>Create Room</title>
	<link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet" />
	<link rel="stylesheet" href="../assets/main.css" />
</head>

<body>
	<div class="container room-page">
		<?php
		$roomId = $_GET['room-id'];
		echo "<h1>Room $roomId</h1>";
		echo "<script>const roomId = " . json_encode($roomId) . ";</script>";
		?>
		<div class="video-container">
			<video id="localVideo" autoplay playsinline muted></video>
			<video id="remoteVideo" autoplay playsinline></video>
		</div>

		<a href="/" class="back-button">End Call</a>
	</div>
	<script src="/room/js/makeCall.js" type="module"></script>
</body>

</html>
