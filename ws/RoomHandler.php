<?php

namespace App;

require_once "Db.php";


class RoomHandler
{
	private $roomId;
	private $clientId;
	public function __construct($roomId, $clientId)
	{
		$this->roomId = $roomId;
		$this->clientId = $clientId;
	}

	private function find_pair()
	{
		$conn = get_db_connection();

		$stmt = $conn->prepare("SELECT * FROM room WHERE room_id = ? AND client_id != ?");
		$stmt->bind_param("si", $this->roomId, $this->clientId);
		$stmt->execute();
		$result = $stmt->get_result()->fetch_row();

		$conn->close();

		return $result;
	}

	public function add_user()
	{
		$conn = get_db_connection();
		$stmt = $conn->prepare(
			"INSERT INTO room (room_id, client_id) VALUES (?, ?);"
		);

		$stmt->bind_param("ss", $this->roomId, $this->clientId);
		$stmt->execute();
		echo "Added user \n";

		$conn->close();
	}

	public function drop_user($clientId)
	{
		$conn = get_db_connection();
		$stmt = $conn->prepare("DELETE FROM room WHERE client_id = ?");

		$stmt->bind_param("i", $clientId);
		$stmt->execute();
		echo "User removed from room\n";

		$conn->close();
	}


	public function handler_user_pairing()
	{
		$user_exist = $this->find_pair();
		return $user_exist ? $user_exist[2] : $this->add_user();
	}
}
