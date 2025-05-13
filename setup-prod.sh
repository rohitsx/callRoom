#!/bin/bash

# Create required directories
mkdir -p ./certbot/conf
mkdir -p ./certbot/www
mkdir -p ./nginx/ssl

# Check if we already have SSL certificates
if [ ! -d "./certbot/conf/live/callroom.devrohit.tech" ]; then
  echo "No existing certificates found. We'll need to generate them."
  
  # Start nginx for certificate validation
  docker-compose -f docker-compose.prod.yml up -d nginx
  
  # Wait for nginx to start
  echo "Waiting for nginx to start..."
  sleep 5
  
  # Get the certificates
  docker-compose -f docker-compose.prod.yml run --rm certbot certonly --webroot \
    --webroot-path=/var/www/certbot \
    --email rohitbindw@gmail.com \
    --agree-tos \
    --no-eff-email \
    -d callroom.devrohit.tech
    
  # Generate strong DH parameters for enhanced security
  if [ ! -f "./certbot/conf/ssl-dhparams.pem" ]; then
    echo "Generating SSL parameters..."
    docker run --rm -v "$PWD/certbot/conf:/etc/letsencrypt" certbot/certbot \
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
  
  # Restart nginx to apply SSL configs
  docker-compose -f docker-compose.prod.yml restart nginx
else
  echo "Certificates already exist, no need to generate new ones."
fi

# Start all services
echo "Starting all services..."
docker-compose -f docker-compose.prod.yml up -d

echo "Setup complete! Your site should be running at https://callroom.devrohit.tech"
