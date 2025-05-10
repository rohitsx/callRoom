import playVideoFromCamera from "./lib/stream.js";

async function makeCall() {
	const { peerConnection } = await playVideoFromCamera();

	const offer = await peerConnection.createOffer();
	await peerConnection.setLocalDescription(offer);

	await fetch("http://localhost:8000/api/add-sdp", {
		method: "POST",
		headers: {
			"Content-Type": "application/json",
		},
		body: JSON.stringify({
			sdp: offer,
			type: "offer",
			roomId,
		}),
	});

	async function pollForAnswer() {
		const data = await fetch("http://localhost:8000/api/check-answer", {
			method: "POST",
			headers: {
				"Content-Type": "application/json",
			},
			body: JSON.stringify({ roomId }),
		});
		const res = await data.text();

		if (res !== "null") {
			const { sdp } = JSON.parse(res);
			const remoteDesc = new RTCSessionDescription(JSON.parse(sdp));
			await peerConnection.setRemoteDescription(remoteDesc);
		} else {
			console.log("polling for res", res);
			setTimeout(() => pollForAnswer(roomId), 2000);
		}
	}

	await pollForAnswer();
}

makeCall();
