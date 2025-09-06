#!/bin/bash

if [ $(id -u) -ne 0 ]; then
	echo "This script must be run as root"
	exit
fi

if [ ! $(command -v apt) > /dev/null ]; then
	echo "APT package manager not found!, This script works only on debian/ubuntu"
	exit
fi

if [ ! $1 ]; then
	echo "You must provide a database password"
	exit
fi

apt update -y

echo "Adding PPA for php5.6"
DISTRO=$(cat /etc/os-release | egrep -o '^ID=([a-zA-Z0-9]+)' | cut -d '=' -f 2)

if [ "$DISTRO" = "debian" ]; then
	apt install -y software-properties-common ca-certificates lsb-release apt-transport-https
	wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
	echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php.list
elif [ "$DISTRO" = "ubuntu" ]; then
	add-apt-repository ppa:ondrej/php
else
	echo "$DISTRO is not supported"
	exit
fi

echo "Added keyring for php5.6 PPA"

apt update -y

echo "Installing php v5.6"
apt install php5.6 php5.6-common php5.6-curl php5.6-xml php5.6-json php5.6-gd php5.6-mbstring php5.6-zip php5.6-fpm php5.6-mcrypt php5.6-pdo php5.6-mysql -y

# soft link so we can use `php56` as our binary not `php5.6`
ln -s $(which php5.6) /usr/bin/php56

echo "Installing composer v2.2"
# install composer v2.2
EXPECTED_CHECKSUM="$(php56 -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"
php56 -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
ACTUAL_CHECKSUM="$(php56 -r "echo hash_file('sha384', 'composer-setup.php');")"

if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]
then
    >&2 echo 'ERROR: Invalid composer installer checksum'
    rm composer-setup.php
    exit 1
fi

php56 composer-setup.php --2.2 --quiet

if [ ! -f "composer.phar" ]; then
	echo "Failed to install composer"
fi

mv composer.phar /usr/local/bin/composer
rm composer-setup.php

echo "Installing databases"

# install mariadb and redis
apt install mariadb-server redis -y

echo "Configuring database"

read -d '' DATABASE_INIT << EOF
CREATE USER 'budget'@'localhost' IDENTIFIED BY '$1';
CREATE DATABASE budget;
GRANT ALL PRIVILEGES ON budget.* TO 'budget'@'localhost';
EOF

echo "$DATABASE_INIT" | mariadb -u root

echo "Configured database"

# install nginx
echo "Installing webserver"

apt install nginx -y
mkdir -p /var/www/html/simplebudget_server
read -d '' SERVER_CONFIG << EOF
server{
        listen 80;
        root /var/www/html/simplebudget_server/public;
        index index.php;

        location / {
            try_files \$uri \$uri/ /index.php?\$query_string;
        }

        location ~ \.php$ {
          include /etc/nginx/fastcgi.conf;
          fastcgi_pass unix:/run/php/php5.6-fpm.sock;
          fastcgi_hide_header X-Powered-By;
        }
}
EOF

if [ -f /etc/nginx/sites-enabled/default ]; then
	rm /etc/nginx/sites-enabled/default
fi
echo "$SERVER_CONFIG" > /etc/nginx/sites-enabled/simplebudget_server.conf

echo "Configured webserver"

read -d '' ENV_TEMPLATE << EOF
APP_ENV=local
APP_DEBUG=false
APP_KEY=$(uuidgen | tr -d '-')

DB_HOST=localhost
DB_DATABASE=budget
DB_USERNAME=budget
DB_PASSWORD=$1

CACHE_DRIVER=redis
SESSION_DRIVER=file
QUEUE_DRIVER=sync

MAIL_DRIVER=smtp
MAIL_HOST=mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
EOF

echo "$ENV_TEMPLATE" > .env

echo "Installing composer dependencies"

if [ -d "vendor" ]; then
	rm -rf vendor
fi

sudo -u \#1000 php56 $(which composer) install

echo "Creating database tables"
php56 artisan migrate

echo "Copying server files to webserver root directory"
# move the folder to the server root in specified in our nginx configuration
cp -r . /var/www/html/simplebudget_server

# change ownership to webserver user (usually www-data for nginx and php fpm)
chown -R www-data:www-data /var/www/html/simplebudget_server

# verify nginx config
nginx -t

echo "Restarting webserver"
# restart nginx
systemctl restart nginx.service

echo "Setup complete"
echo "View server API documentation at http://localhost/budget/docs"