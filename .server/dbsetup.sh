echo "MySQL Database Connection Configuration"
read -p "DB_HOST: " db_host
read -p "DB_NAME: " db_name
read -p "DB_USERNAME: " db_username
read -p "DB_PASSWORD: " db_password

echo "Creating database based on variables"
mysql << EOF
CREATE DATABASE IF NOT EXISTS $db_name;
EOF
echo "Database name created"
mysql << EOF
CREATE USER "$db_username"@"$db_host" IDENTIFIED BY "$db_password";
EOF
echo "Database user created"
mysql << EOF
GRANT ALL PRIVILEGES ON * . * TO "$db_username"@"$db_host";
EOF
echo "Database permission created"
mysql << EOF
FLUSH PRIVILEGES;
EOF
echo "Database flush privileges successful"
echo
