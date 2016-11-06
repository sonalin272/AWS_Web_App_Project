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
git clone git@github.com:illinoistech-itm/snimbalk.git /home/ubuntu/snimbalk
sudo cp /home/ubuntu/snimbalk/s3test.php /var/www/html/s3test.php
sudo cp /home/ubuntu/snimbalk/createdb.php /var/www/html/createdb.php
sudo cp /home/ubuntu/snimbalk/dbtest.php /var/www/html/dbtest.php
sudo service apache2 restart
echo "Hello" > /var/www/html/hello.txt
