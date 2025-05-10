<!DOCTYPE html>
<html>

<head>
	<title>Create or Join Room</title>
</head>

<body>
	<div>
		<h1>Create Room</h1>
		<form id="create-form" action="/room" method="get" onsubmit="return validateCreateForm()">
			<label for="create-room-id">Room Name</label><br>
			<input type="text" id="create-room-id" name="room-id" placeholder="Enter a unique room name (e.g., tech-talk-01)"><br>
			<input type="submit">
		</form>

		<h1>Join Room</h1>
		<form id="join-form" action="/join" method="get" onsubmit="return validateJoinForm()">
			<label for="join-room-id">Join Name</label><br>
			<input type="text" id="join-room-id" name="room-id" placeholder="Enter a unique room name (e.g., tech-talk-01)"><br>
			<input type="submit">
		</form>
	</div>

	<script>
		function validateCreateForm() {
			const roomId = document.getElementById('create-room-id').value.trim();
			if (!roomId) {
				alert('Please enter a room name to create.');
				return false;
			}
			return true;
		}

		function validateJoinForm() {
			const roomId = document.getElementById('join-room-id').value.trim();
			if (!roomId) {
				alert('Please enter a room name to join.');
				return false;
			}
			return true;
		}
	</script>
</body>

</html>
