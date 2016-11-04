#!/bin/bash
sudo apt-get update -y
sudo apt-get install -y python-setuptools python-pip
sudo pip install awscli
sudo apt-get install -y apache2 php curl php-curl zip unzip php-mysql php-xml libapache2-mod-php
sudo systemctl enable apache2
sudo systemctl start apache2
export COMPOSER_HOME=/root && /usr/bin/composer.phar self-update 1.0.0-alpha11
sudo curl -sS https://getcomposer.org/installer | php
sudo php composer.phar require aws/aws-sdk-php
sudo mv /vendor /var/www/html
echo "Hello" > /var/www/html/hello.txt
git clone git@github.com:illinoistech-itm/snimbalk.git /home/ubuntu/snimbalk
