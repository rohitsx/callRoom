import playVideoFromCamera from "./lib/stream.js";

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

	return JSON.parse(await userOffer.text());
}

async function main() {
	const { peerConnection } = await playVideoFromCamera();

	const { sdp } = await findOffer();
	if (!sdp) throw new Error("sdp did not recived");

	peerConnection.setRemoteDescription(
		new RTCSessionDescription(JSON.parse(sdp)),
	);
	const answer = await peerConnection.createAnswer();
	await peerConnection.setLocalDescription(answer);

	await fetch("http://localhost:8000/api/add-sdp", {
		method: "POST",
		headers: {
			"Content-Type": "application/json",
		},
		body: JSON.stringify({
			sdp: answer,
			type: "answer",
			roomId,
		}),
	});
}

main();
