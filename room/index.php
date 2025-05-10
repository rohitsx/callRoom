<!DOCTYPE html>
<html>
<title>Create Room</title>

<body>
	<div>
		<?php
		$roomId = $_GET['room-id'];
		echo "<h1>Room $roomId</h1>";
		echo "<script>const roomId = " . json_encode($roomId) . ";</script>";
		?>
		<video id="localVideo" autoplay playsinline muted />

	</div>
	<script src="../assets/js/room.js"></script>
	<script>
		data = {
			roomId: roomId,
			message: "User left the room"
		}
		window.addEventListener("beforeunload", function(e) {
			navigator.sendBeacon("http://localhost:8000/api/leave-room", JSON.stringify(data));
			e.preventDefault();
			e.returnValue = '';
		});
	</script>
</body>

</html>
