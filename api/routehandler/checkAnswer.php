<?php

$sql = "SELECT * FROM room WHERE room_id='$roomId' AND request_type='answer';";

$result = $conn->query($sql);
$row = $result->fetch_assoc();

echo json_encode($row);
