<?php
$stmt = $conn->prepare("DELETE FROM room WHERE room_id = ?;");
$stmt->bind_param("s", $roomId);
$stmt->execute();
