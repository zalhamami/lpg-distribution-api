#!/bin/bash
echo "[Upgrade] Humming Bird API Upgrade Script"
echo
echo "This script should only be run if you want to upgrade the application."
echo

read -p "Run the script [y/N]? " run_script
run_script=${run_script:="N"}
run_script_u=${run_script^^}
if [[ "${run_script_u:0:1}" != "Y" ]]
then
  exit
fi

git pull origin master
composer install
php artisan migrate
