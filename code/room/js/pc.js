export default function start() {
	const remoteVideo = document.getElementById("remoteVideo");
	const pc = new RTCPeerConnection({
		iceServers: [{ urls: "stun:stun.my-stun-server.tld" }],
	});

	pc.ontrack = ({ track, streams }) => {
		track.onunmute = () => {
			if (remoteVideo.srcObject) {
				return;
			}
			console.log("recived track");
			remoteVideo.srcObject = streams[0];
		};
	};

	return { pc };
}
