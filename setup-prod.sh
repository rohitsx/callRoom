#!/bin/bash

# Add current user to the docker group
echo "Adding user to docker group..."
sudo usermod -aG docker $USER
echo "You may need to log out and log back in for this to take effect."

# Create required directories
mkdir -p ./certbot/conf
mkdir -p ./certbot/www
mkdir -p ./nginx/ssl

# Remove any existing containers to start fresh
echo "Stopping any existing containers..."
sudo docker compose -f docker-compose.prod.yml down

# Run certbot standalone to get the certificate
echo "Obtaining SSL certificate using standalone mode..."
sudo docker run --rm -it \
  -v "$PWD/certbot/conf:/etc/letsencrypt" \
  -v "$PWD/certbot/www:/var/www/certbot" \
  -p 80:80 \
  certbot/certbot certonly --standalone \
  --email rohitbindw@gmail.com \
  --agree-tos \
  --no-eff-email \
  -d callroom.devrohit.tech

# Generate strong DH parameters for enhanced security
if [ ! -f "./certbot/conf/ssl-dhparams.pem" ]; then
  echo "Generating SSL parameters..."
  sudo docker run --rm -v "$PWD/certbot/conf:/etc/letsencrypt" certbot/certbot \
    openssl dhparam -out /etc/letsencrypt/ssl-dhparams.pem 2048
fi

# Create recommended SSL options file if it doesn't exist
if [ ! -f "./certbot/conf/options-ssl-nginx.conf" ]; then
  echo "Creating SSL options file..."
  cat > ./certbot/conf/options-ssl-nginx.conf << 'EOL'
ssl_session_cache shared:le_nginx_SSL:10m;
ssl_session_timeout 1440m;
ssl_session_tickets off;

ssl_protocols TLSv1.2 TLSv1.3;
ssl_prefer_server_ciphers off;
ssl_ciphers "ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:DHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES256-GCM-SHA384";
EOL
fi

# Start all services
echo "Starting all services..."
sudo docker compose -f docker-compose.prod.yml up -d

echo "Setup complete! Your site should be running at https://callroom.devrohit.tech"
echo "You can check the status of your containers with: sudo docker ps"
