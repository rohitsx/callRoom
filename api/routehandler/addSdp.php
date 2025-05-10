<?php
$stmt = $conn->prepare(
	"INSERT INTO room (room_id, request_type, sdp) VALUES (?, ?, ?)
    ON DUPLICATE KEY UPDATE sdp = VALUES(sdp), request_type = VALUES(request_type);"
);

$stmt->bind_param("sss", $roomId, $type, $sdp);
$stmt->execute();
echo "Added Sdp";
