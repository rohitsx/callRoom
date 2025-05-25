# callRoom - Simple WebRTC Video Calling App

A lightweight, peer-to-peer video and audio calling application built with PHP, JavaScript (WebRTC), Ratchet (WebSockets), and MySQL, all containerized with Docker. Users can create or join rooms by name for direct video communication.

## Demo

Watch a quick demo of the application in action:

[![CallRoom Demo Video](https://img.youtube.com/vi/zQv_ktDfduM/0.jpg)](https://www.youtube.com/watch?v=zQv_ktDfduM)
_(Click the image to watch the video)_

## Features

- **Peer-to-Peer Video/Audio Calls:** Leverages WebRTC for direct P2P communication.
- **Room-Based Calling:** Users join calls by specifying a room name.
- **Dynamic Pairing:** The WebSocket server pairs the first two users who join the same room.
- **Reliable Connection:** Implements "polite" and "impolite" peer logic for robust WebRTC negotiation, minimizing connection failures.
- **Reusable Rooms:** Rooms can be joined multiple times (though designed for 2 participants at a time per room instance).
- **Lightweight Stack:** Uses HTML, CSS, JavaScript, PHP, Ratchet, and MySQL.
- **Dockerized:** Easy to set up and run using Docker Compose.
- **Beginner-Friendly:** Includes a detailed deployment guide (this README!).

## Tech Stack

### Frontend

- HTML5
- CSS3 (Custom styling with a retro theme)
- JavaScript (ES6 Modules)
- WebRTC API

### Backend

- PHP
- Ratchet (for WebSocket server)
- MySQL (for room and client management)

### Web Server

- Nginx (as a reverse proxy and for serving static/PHP files)

### Containerization

- Docker
- Docker Compose

## Prerequisites

### For Dockerized Setup (Recommended)

- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)

### For Manual Setup

- PHP (>= 8.1 recommended, with `mysqli` extension)
- [Composer](https://getcomposer.org/)
- MySQL Server
- A web server capable of running PHP (e.g., Nginx, Apache, or PHP's built-in server for development)
- A command-line interface / terminal

## Getting Started

### 1. Clone the Repository

```bash
git clone <your-repository-url>
cd callRoom
```

### 2. Using Docker (Recommended)

This is the easiest way to get the application up and running.

**Build and Run Containers:**

From the project root directory (callRoom/), run:

```bash
docker compose -f docker-compose.dev.yml up --build -d
```

- `--build`: Forces Docker to rebuild the images if there are changes in Dockerfiles or related code.
- `-d`: Runs the containers in detached mode (in the background).

**Access the Application:**

Open your web browser and navigate to: http://localhost

**To Stop the Application:**

```bash
docker compose -f docker-compose.dev.yml down
```

### 3. Manual Setup (For Development/Understanding)

If you prefer to run the components manually without Docker:

**Setup MySQL Database:**

1. Ensure your MySQL server is running.
2. Create a database (e.g., `mydb`).
3. Create a user (e.g., `myuser` with password `pass123`) and grant it privileges on the `mydb` database.
4. Note: If you use different credentials, update them in `code/ws/Db.php`.

**Import the table schema:**

```bash
mysql -u your_mysql_user -p mydb < mysql/init.sql
```

(Replace `your_mysql_user` with your MySQL username; you'll be prompted for the password).

**Install PHP Dependencies:**

Navigate to the code directory and install dependencies using Composer:

```bash
cd code
composer install
cd ..
```

**Run the WebSocket Server:**

In a new terminal window, navigate to the code directory and start the WebSocket server:

```bash
cd code
php ws/Ws.php
```

You should see a message like "WebSocket server running on 0.0.0.0:8080". Keep this terminal open.

**Run the PHP Web Server:**

In another new terminal window, navigate to the code directory and start PHP's built-in web server:

```bash
cd code
php -S localhost:8000
```

Keep this terminal open.

**Access the Application:**

Open your web browser and navigate to: http://localhost:8000

**Important for Manual Setup:**

The Nginx configuration in the Docker setup proxies `/ws` requests to the WebSocket server. When running manually without Nginx, the frontend JavaScript in `code/room/js/makeCall.js` directly connects to `localhost:8080`. This is handled by:

```javascript
const wsHost = isProduction ? "callroom.devrohit.tech" : "localhost:8080";
```

Ensure your STUN server configuration in `code/room/js/pc.js` (`iceServers: [{ urls: "stun:stun.my-stun-server.tld" }]`) points to a valid STUN server. You can use public ones like `stun:stun.l.google.com:19302`.

## Project Structure

```
callRoom/
├── code/                   # PHP application code, frontend assets
│   ├── assets/
│   │   └── main.css        # Main stylesheet
│   ├── call-ended/
│   │   └── index.html      # Page shown after a call ends
│   ├── composer.json       # PHP dependencies (for Ratchet)
│   ├── dockerfile          # Dockerfile for the PHP-FPM & WebSocket service
│   ├── index.html          # Home page to enter/create a room
│   ├── room/
│   │   ├── index.php       # Main video call page for a room
│   │   └── js/
│   │       ├── makeCall.js # Core WebRTC and WebSocket logic
│   │       ├── pc.js       # RTCPeerConnection setup
│   │       └── stream.js   # Local media stream (camera/mic) setup
│   └── ws/                 # WebSocket server logic (Ratchet)
│       ├── Db.php          # Database connection helper
│       ├── RoomHandler.php # Handles room logic (pairing, DB interaction)
│       ├── WsHandler.php   # Main WebSocket message component
│       └── Ws.php          # WebSocket server bootstrap script
├── docker-compose.dev.yml  # Docker Compose file for development
├── mysql/
│   └── init.sql            # MySQL database schema initialization
├── nginx/
│   ├── default.conf        # Nginx configuration
│   └── dockerfile          # Dockerfile for the Nginx service
└── README.md               # This file
```

## Configuration

### Database Credentials

- **Docker:** `docker-compose.dev.yml` (environment variables for db service).
- **Manual:** `code/ws/Db.php`.

### WebSocket Port

- Defined in `docker-compose.dev.yml` (for websocket service, mapped to host port 8080).
- Hardcoded to 8080 in `code/ws/Ws.php`.
- Nginx proxies `/ws` to `websocket:8080`.

### STUN Server

Configured in `code/room/js/pc.js` (`iceServers` array). The default `stun:stun.my-stun-server.tld` is a placeholder. For testing, you can use public STUN servers like `stun:stun.l.google.com:19302`.

Example: `iceServers: [{ urls: "stun:stun.l.google.com:19302" }]`

## How It Works (Brief Overview)

1. **User Enters Room:** A user types a room name on `index.html` and is redirected to `/room/index.php?room-id=....`

2. **WebSocket Connection:** `makeCall.js` establishes a WebSocket connection to the server (`/ws` endpoint, which Nginx proxies to the Ratchet server). The username (randomly generated if not set) and `room-id` are passed as query parameters.

3. **User Pairing (Server-Side):**

   - `WsHandler::onOpen()` receives the new connection.
   - `RoomHandler` checks if another user is already waiting in the same `room_id` in the room MySQL table.
   - If a user is found, they are "paired". The server sends a `paired` message to both clients, indicating the `resourceId` of their peer. The first user in the room is marked as "polite" for WebRTC negotiation. The existing user is then removed from the room table to allow new pairs.
   - If no user is found, the current user's `client_id` and `room_id` are added to the room table, and they wait.

4. **WebRTC Signaling:**

   - Once paired, clients initiate the WebRTC connection process.
   - Signaling messages (offers, answers, ICE candidates) are exchanged via the WebSocket server. Each message includes a `to` field specifying the `resourceId` of the recipient peer.
   - The "polite" peer handles offer collisions gracefully.

5. **Peer-to-Peer Media Stream:** After successful signaling, a direct P2P connection is established between the two clients, and video/audio streams are exchanged.

6. **Ending Call:** When a user clicks "End Call" or closes the tab, a message is sent to the peer to close the RTCPeerConnection, and the WebSocket connection is closed.

## Troubleshooting

### Check Docker Logs

If using Docker, check the logs for each service:

```bash
docker compose -f docker-compose.dev.yml logs php
docker compose -f docker-compose.dev.yml logs websocket
docker compose -f docker-compose.dev.yml logs nginx
docker compose -f docker-compose.dev.yml logs db
```

### Browser Developer Console

Open your browser's developer console (usually F12) to check for JavaScript errors or WebSocket connection issues.

### STUN Server

Ensure your STUN server is correctly configured and accessible. WebRTC needs STUN/TURN servers to facilitate connections, especially across NATs.

### Firewall

Ensure your firewall isn't blocking connections on ports 80, 8080 (for WebSockets), or the UDP ports used by WebRTC.

## Contributing

Feel free to fork this project, submit pull requests, or open issues for bugs and feature requests.
