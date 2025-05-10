<?php
$sql = "SELECT * FROM room WHERE request_type = 'offer' ORDER BY RAND() LIMIT 1;";

$result = $conn->query($sql);
$row = $result->fetch_assoc();
echo json_encode($row);
