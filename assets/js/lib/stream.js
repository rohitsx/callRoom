export default async function playVideoFromCamera() {
	try {
		const constraints = {
			video: true,
			audio: true,
		};
		const stream = await navigator.mediaDevices.getUserMedia(constraints);
		const videoElement = document.querySelector("video#localVideo");
		videoElement.srcObject = stream;

		const configuration = {
			iceServers: [{ urls: "stun:stun.l.google.com:19302" }],
		};
		const peerConnection = new RTCPeerConnection(configuration);
		stream.getTracks().forEach((track) => {
			peerConnection.addTrack(track, stream);
		});

		const remoteVideo = document.querySelector("#remoteVideo");
		peerConnection.addEventListener("track", async (event) => {
			console.log(remoteVideo);
			const [remoteStream] = event.streams;
			remoteVideo.srcObject = remoteStream;
		});

		peerConnection.addEventListener("connectionstatechange", () => {
			console.log(peerConnection.connectionState);
		});

		return { peerConnection, stream };
	} catch (error) {
		console.error("Error opening video camera.", error);
	}
}
