import start from "./pc.js";
import playVideoFromCamera from "./stream.js";

async function makeCall() {
	const { pc } = start();
	const { stream } = await playVideoFromCamera();

	let makingOffer = false;
	let ignoreOffer = false;
	let isSettingRemoteAnswerPending = false;
	let polite = false;
	let to;

	const wsUrl = `ws://localhost:8080?username=${localStorage.getItem("username")}&room-id=${roomId}`;
	var conn = new WebSocket(wsUrl);

	conn.onmessage = async (m) => {
		const { paired, description, candidate, politeInstance, endCall } =
			JSON.parse(m.data);

		politeInstance && (polite = politeInstance);
		paired && (to = paired);
		description && console.log(description.type);
		candidate && console.log("candidate");

		if (paired) {
			pc.onnegotiationneeded = async () => {
				try {
					makingOffer = true;
					await pc.setLocalDescription();
					conn.send(JSON.stringify({ description: pc.localDescription, to }));
				} catch (err) {
					console.error(err);
				} finally {
					makingOffer = false;
				}
			};
			pc.onicecandidate = ({ candidate }) => {
				console.log("ice send", to);
				conn.send(JSON.stringify({ candidate, to }));
			};

			pc.addEventListener("connectionstatechange", () => {
				console.log("connection stated", pc.connectionState);
			});

			stream.getTracks().forEach((track) => pc.addTrack(track, stream));

			return;
		}
		if (description) {
			const readyForOffer =
				!makingOffer &&
				(pc.signalingState === "stable" || isSettingRemoteAnswerPending);
			const offerCollision = description.type === "offer" && !readyForOffer;

			ignoreOffer = !polite && offerCollision;
			if (ignoreOffer) return;

			isSettingRemoteAnswerPending = description.type == "answer";
			await pc.setRemoteDescription(description);
			isSettingRemoteAnswerPending = false;
			if (description.type === "offer") {
				await pc.setLocalDescription();
				conn.send(JSON.stringify({ description: pc.localDescription, to }));
				return;
			}
		}
		if (candidate) {
			try {
				await pc.addIceCandidate(candidate);
			} catch (err) {
				if (!ignoreOffer) {
					throw err;
				}
			}
		}

		if (endCall) {
			pc.close();
			conn.close();
			window.location.href = "/call-ended";
		}
	};

	window.addEventListener("beforeunload", () => {
		if (conn && conn.readyState === WebSocket.OPEN) {
			conn.send(JSON.stringify({ endCall: to }));
			conn.close();
		}
		if (pc) {
			pc.close();
		}
	});
}

makeCall();
