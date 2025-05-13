#!/bin/bash

# This script troubleshoots common issues with the Docker and SSL setup

echo "=== Troubleshooting Script ==="
echo ""

# Check if DNS is resolving correctly
echo "1. Checking DNS resolution for callroom.devrohit.tech..."
HOST_IP=$(dig +short callroom.devrohit.tech)
SERVER_IP=$(curl -s ifconfig.me)

if [ -z "$HOST_IP" ]; then
  echo "WARNING: Domain callroom.devrohit.tech doesn't resolve to any IP address."
  echo "Action: Verify your DNS settings to make sure they point to your server."
else
  echo "Domain resolves to: $HOST_IP"
  echo "Current server IP: $SERVER_IP"
  
  if [ "$HOST_IP" != "$SERVER_IP" ]; then
    echo "WARNING: Domain doesn't point to this server's IP address."
    echo "Action: Update your DNS records to point to this server: $SERVER_IP"
  else
    echo "DNS configuration looks good!"
  fi
fi

echo ""

# Check if ports 80 and 443 are open
echo "2. Checking if ports 80 and 443 are accessible..."
nc -z -w1 callroom.devrohit.tech 80
if [ $? -eq 0 ]; then
  echo "Port 80 is accessible"
else
  echo "WARNING: Port 80 is not accessible."
  echo "Action: Check your firewall settings to allow traffic on port 80."
fi

nc -z -w1 callroom.devrohit.tech 443
if [ $? -eq 0 ]; then
  echo "Port 443 is accessible"
else
  echo "WARNING: Port 443 is not accessible or not yet configured."
  echo "Action: Check your firewall settings to allow traffic on port 443."
fi

echo ""

# Check Docker permissions
echo "3. Checking Docker permissions..."
docker ps >/dev/null 2>&1
if [ $? -eq 0 ]; then
  echo "Docker permissions look good!"
else
  echo "WARNING: You don't have permission to run Docker commands without sudo."
  echo "Action: Either use sudo for all docker commands or run: sudo usermod -aG docker $USER"
  echo "        After running the usermod command, log out and log back in."
fi

echo ""

# Check if certificate directories exist
echo "4. Checking certificate directory structure..."
if [ -d "./certbot/conf/live/callroom.devrohit.tech" ]; then
  echo "Certificate directory exists!"
  
  # Check if certificates exist
  if [ -f "./certbot/conf/live/callroom.devrohit.tech/fullchain.pem" ] && [ -f "./certbot/conf/live/callroom.devrohit.tech/privkey.pem" ]; then
    echo "SSL certificates found."
  else
    echo "WARNING: Certificate directory exists but certificates are missing."
    echo "Action: Try running the setup script again to obtain certificates."
  fi
else
  echo "WARNING: Certificate directory doesn't exist."
  echo "Action: Run the setup script to obtain certificates."
fi

echo ""
echo "Troubleshooting complete! Review any warnings above."
echo "If you need to restart the setup process, run: ./setup-prod-fixed.sh"
