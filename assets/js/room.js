let stream;
let videoElement;
async function playVideoFromCamera() {
	try {
		const constraints = {
			video: true,
			audio: true,
		};
		stream = await navigator.mediaDevices.getUserMedia(constraints);
		videoElement = document.querySelector("video#localVideo");
		videoElement.srcObject = stream;
	} catch (error) {
		console.error("Error opening video camera.", error);
	}
}

async function makeCall() {
	const configuration = {
		iceServers: [
			{
				urls: "stun:stun.l.google.com:19302",
			},
		],
	};
	const peerConnection = new RTCPeerConnection(configuration);
	const offer = await peerConnection.createOffer();
	await peerConnection.setLocalDescription(offer);

	const userAnswer = await fetch("http://localhost:8000/api/create-room", {
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
	console.log(await userAnswer.text());
}
//playVideoFromCamera();
makeCall();
