## SimpleBudget

SimpleBudget is a simple budgeting software built with `larave v5.4` backend and `angularjs v1.5.7` frontend, The server's API can also be utilized by other clients with an API token.

NOTE:
    - SimpleBudget has been tested only with `php v5.6`
    - To develop `SimpleBudget` i looked at [gnucash](https://gnucash.org/) to understand how it does accounts, transactions and budgeting.

## Features
    - Create user accounts
    - Create api tokens
    - Create accounts (including child accounts) 
    - Create budgets for accounts
    - Create account transactions
    - Get user, accounts (including child accounts), transaction, budgets info
    - Edit accounts, transactions, budgets
    - Delete accounts, transaction, bugets
    - Revoke api tokens
    - Get notifications when certain events occurs (e.g gone past a budget)

## API Documentation

The API Documentation can be found [here](https://budget.meshanthony.name.ng/api/docs) (swagger-ui)

## Installation

### Install `php`

`php v5.6` is going to be installed as it is the minimum version supported by `laravel v5.4` and maximum supported by `laravel v5.0` which this project initially started with.

**For Arch Linux**

You can find `php v5.6` on the [AUR](https://aur.archlinux.org/packages/php56) and `makepkg` or just use `yay`
```bash
sudo yay -S php56
```

**For debian/ubuntu**

You can find installation instructions for **debian** [here](https://docs.vultr.com/how-to-install-php-5-6-on-debian-12)

You can find installation instructions for **ubuntu** [here](https://docs.vultr.com/how-to-install-php-5-6-on-ubuntu-22-04)

After following those instructions run
```bash
sudo apt install php5.6-fpm php5.6-mcrypt php5.6-pdo php5.6-mysql

# soft link so we can use `php56` as our binary not `php5.6`
sudo ln -s $(which php5.6) /usr/bin/php56
```

### Install `composer v2.2`

**REMEMBER** where you see
```bash
php composer-setup.php
```
replace with
```bash
php composer-setup.php --2.2
```
to install `composer v2.2`

Now you can follow the composer [installation instructions](https://getcomposer.org/download/)

### Install `redis` for cache and session

```bash
sudo apt install redis
```

### Install `mysql` or `mariadb` database server

`mysql` is going to be installed as it is the database server was instructed to be used with this project.

**For debian/ubuntu**

`mysql v8.0` is going to be installed. As of writing this `mysql v8.4-lts` is available but `v8.4` removes the `default_authentication_plugin` config option that would allow `mysql_native_password` plugin to be set instead of ` caching_sha2_password` which is not supported by some clients for authentication, This project's version of `php-pdo` used by `laravel v5.4` Eloquent ORM does not support it. If anyone can get it running do create a pull request to update the readme with the instructions to set it up.

```bash
# use mysql apt config tool to get their PPA setup (Recommended)
wget "https://dev.mysql.com/get/mysql-apt-config_0.8.34-1_all.deb"
```

Note on installing the mysql apt config tool, 
you must seclect `mysql-8.0` and choose the legacy authentication method when prompted (it automatically sets the `default_authentication_plugin` to `mysql_native_password`)

```bash
# install the mysql apt config tool
sudo dpkg -i mysql-apt-config*.deb

# update package list and install
sudo apt update && sudo apt install mysql-server -y
```

**For Arch Linux**

Just use mariadb ⬇️

`mariadb` is a fork of `mysql` that is available on most Linux distro's package repository.

Read the difference between [mariadb and mysql](https://www.geeksforgeeks.org/mysql/difference-between-mysql-and-mariadb/)

**For Arch Linux**

```bash
sudo pacman -S mariadb
sudo mariadb-install-db --user=mysql --basedir=/usr --datadir=/var/lib/mysql
sudo systemctl enable --now mariadb.service
```

**For debian/ubuntu**

```bash
sudo apt install mariadb-server
```

**To configure database (mysql or mariadb)**

```bash
# for mysql
sudo mysql -u root -p

# for mariadb
sudo mariadb -u root -p
```
then in the mysql/mariadb command line

```sql
CREATE USER 'budget'@'localhost' IDENTIFIED BY '<YOUR_PASSWORD>';
CREATE DATABASE budget;
GRANT ALL PRIVILEGES ON budget.* TO 'budget'@'localhost';
```
exit the mysql/mariadb command line with a `\q` command

### Install a webserver

`nginx` is going to be installed as i believe it is a more [efficient](https://markaicode.com/nginx-vs-apache-2025-performance-comparison/) webserver than `apache` and is what i use personally.

**For Arch Linux**

```bash
sudo pacman -S nginx
```
**For debian/ubuntu**

```bash
sudo apt install nginx
```

**To configure**

```bash
sudo mkdir -p /var/www/html/simplebudget

# make sure there is no default server config
rm /etc/nginx/sites-enabled/default

sudo nano /etc/nginx/sites-enabled/simplebudget.conf
```
and paste this ⬇️
```
server{
    listen 80;
    root /var/www/html/simplebudget/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
      include /etc/nginx/fastcgi.conf;
      fastcgi_pass unix:/run/php/php5.6-fpm.sock;
      fastcgi_hide_header X-Powered-By;
    }
}
```
save `ctrl+o then Enter` and exit `ctrl+x`

### Clone the repo

```bash
git clone https://github.com/SkyNotion/simplebudget.git
cd simplebudget
```
add enviromental variables (e.g user password for database)
```bash
# copy example eviromental variables file
cp .env.example .env
# add your own env variables
nano .env
```
install dependencies
```bash
php56 $(which composer) install
```
create database tables
```bash
php56 artisan migrate
```
setup `laravel/passport`
```bash
php56 artisan passport:install --force
```
make it visible to the webserver
```bash
# copy the folder to the server root in specified in our nginx configuration
sudo cp . /var/www/html/simplebudget

# change ownership to webserver user (usually www-data for nginx and php-fpm)
sudo chown -R www-data:www-data /var/www/html/simplebudget

# verify nginx config
sudo nginx -t

# restart nginx
sudo systemctl restart nginx.service
```

## Install script for debian/ubuntu

To get the server up and running on `debian\ubuntu` you can just use the install script

```bash
# making sure the repo is cloned
git clone https://github.com/SkyNotion/simplebudget.git

cd simplebudget

# give the script executable permissions
chmod +x install.sh

# install simplebudget
# add the password to use for your database as an argument (required)
sudo ./install.sh "<YOUR_DATABASE_USER_PASSWORD>"
```

## Check if the server is active

copy this link http://localhost/api/docs and paste it in your browser paste then press enter, The API 
documentation should come up

