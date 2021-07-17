#!/bin/bash
echo "Securing domain name system with SSL"
read -p "Domain: " domain

add-apt-repository ppa:certbot/certbot
apt install -y python3-certbot-nginx
ufw allow 'Nginx Full'
ufw delete allow  'Nginx HTTP'
certbot --nginx -d $domain
certbot renew --dry-run
echo
