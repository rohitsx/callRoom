async function findOffer() {
	const userOffer = await fetch("http://localhost:8000/api/join-room", {
		method: "POST",
		headers: {
			"Content-Type": "application/json",
		},
		body: JSON.stringify({
			roomId,
		}),
	});

	return ({ room_id, sdp } = JSON.parse(await userOffer.text()));
}

findOffer();
