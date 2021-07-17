#!/bin/bash
echo "Nginx Configuration"
read -p "Domain: " domain
read -p "Project Folder: " project_folder

echo "Creating nginx reverse proxy"
# Create the Nginx server block file:
sed=`which sed`
block="/etc/nginx/sites-available/$domain"
sudo cp .server/nginx/config.stub $block
CURRENT_DIR=`dirname $0`
sudo $sed -i "s/{{DOMAIN}}/$domain/g" $block
sudo $sed -i "s/{{PROJECT_FOLDER}}/$project_folder/g" $block
ln -s $block /etc/nginx/sites-enabled/
nginx -t
echo

# Test configuration and reload if successful
echo "Restarting nginx service"
service nginx reload
echo "Nginx configuration finished"
echo
