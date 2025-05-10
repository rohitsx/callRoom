<?php
$stmt = $conn->prepare(
	"INSERT INTO room (room_id, request_type, sdp, started_pairing) VALUES (?, ?, ?, false)
ON DUPLICATE KEY UPDATE sdp = VALUES(sdp)
"
);
$stmt->bind_param("sss", $roomId, $type, $sdp);
$stmt->execute();
