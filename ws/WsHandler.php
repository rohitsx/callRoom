<?php


namespace App;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\RoomHandler;

class WsHandler implements MessageComponentInterface
{
	protected $clients;
	protected $clientMap;

	public function __construct()
	{
		$this->clients = new \SplObjectStorage;
		$this->clientMap = [];
	}

	private function get_ids(ConnectionInterface $conn)
	{
		$queryString = $conn->httpRequest->getUri()->getQuery();
		parse_str($queryString, $queryParams);
		$roomId = $queryParams['room-id'];
		$room = new RoomHandler($roomId, $conn->resourceId);
		return [$room, $conn->resourceId, $roomId];
	}

	public function onOpen(ConnectionInterface $conn)
	{
		[$room, $clientId, $roomId] = $this->get_ids($conn);

		$this->clients->attach($conn);
		$this->clientMap[$clientId] = $conn;

		$result = $room->handler_user_pairing();
		if ($result) {
			$conn->send(json_encode(["paired" => $result, "politeInstance" => true]));

			if (isset($this->clientMap[$result])) {
				$pair_ws = $this->clientMap[$result];
				$pair_ws->send(json_encode(["paired" => $clientId, "politeInstance" => false]));

				$room->drop_user($result);
			} else {
				$conn->send(json_encode(["error" => "Pairing failed, client not found"]));
			}
		} else {
			$conn->send(json_encode(["message" => "No Pair Found"]));
		}

		echo "New connection! $clientId, $roomId\n";
	}

	public function onMessage(ConnectionInterface $from, $msg)
	{
		$data = json_decode($msg, true);

		if ($data === null) {
			echo "JSON decode error: " . json_last_error_msg() . "\n";
			return;
		}

		if (isset($data['to'])) {
			$conn = $this->clientMap[$data['to']] ?? null;
			if ($conn) {
				$conn->send(json_encode($data));
			}
		}


		if (isset($data['endCall'])) {
			$conn = $this->clientMap[$data['endCall']] ?? null;
			if ($conn) {
				$conn->send(json_encode(["endCall" => true]));
			}
		}
	}

	public function onClose(ConnectionInterface $conn)
	{
		[$room, $clientId] = $this->get_ids($conn);
		$room->drop_user($clientId);
		$this->clients->detach($conn);
		unset($this->clientMap[$conn->resourceId]);

		echo "Connection {$conn->resourceId} has disconnected\n";
	}

	public function onError(ConnectionInterface $conn, \Exception $e)
	{
		echo "An error has occurred: {$e->getMessage()}\n";

		$conn->close();
	}
}
