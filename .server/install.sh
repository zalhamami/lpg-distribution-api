#!/bin/bash
echo "[Install] Humming Bird API Install Script"
echo

read -p "Run the script [y/N]? " run_script
run_script=${run_script:="N"}
run_script_u=${run_script^^}
if [[ "${run_script_u:0:1}" != "Y" ]]
then
  exit
fi

echo "Installing all the necessary packages"
apt update
apt upgrade
apt install -y software-properties-common
add-apt-repository ppa:ondrej/php
add-apt-repository universe
apt update
apt install -y curl \
    unzip \
    nginx \
    mysql-server \
    php7.3-fpm \
    php7.3-mysql \
    php7.3-mbstring \
    php7.3-xml \
    php7.3-bcmath \
    imagemagick \
    php7.3-imagick \
    php7.3-gd \
    php7.3-zip
echo

read -p "Composer Signature: " signature
curl -sS https://getcomposer.org/installer -o composer-setup.php
HASH=$signature
php -r "if (hash_file('SHA384', 'composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer

echo "Setting up ufw"
echo
ufw allow OpenSSH
ufw allow 'Nginx HTTP'
ufw enable
echo "Finished"
echo

.server/nginx/setup.sh
.server/dbsetup.sh

echo
echo "Installing application packages"
mkdir database/factories
composer install
cp .env.example .env
php artisan key:generate
echo

echo "Setting up storage permission"
chmod -R 777 storage/
chmod -R 777 bootstrap/cache/
echo

echo "Setup finished"
echo "Next step:"
echo "1. Set value on the environment variables"
echo "2. Run: php artisan migrate --seed"
echo "3. Run: php artisan passport:install"
echo "4. Point the domain to public ip address of this instance"
echo "5. Run secure script in .server/secure.sh"
echo
