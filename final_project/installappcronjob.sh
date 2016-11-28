#!/bin/bash
# Install required packages
sleep 20
sudo run-one-until-success apt-get update -y
sudo run-one-until-success apt-get install -y python-setuptools python-pip
sudo pip install awscli
sudo run-one-until-success apt-get install -y apache2 php curl php-curl zip unzip php-mysql php-xml libapache2-mod-php mysql-client-5.7 php-gd
sudo systemctl enable apache2
sudo systemctl start apache2
export COMPOSER_HOME=/root && /usr/bin/composer.phar self-update 1.0.0-alpha11
sudo curl -sS https://getcomposer.org/installer | php
sudo php composer.phar require aws/aws-sdk-php
sudo mv /vendor /var/www/html
#Clone git repository on the instance and copy all files
git clone git@github.com:illinoistech-itm/snimbalk.git /home/ubuntu/snimbalk
sudo cp /home/ubuntu/snimbalk/final_project/*.php /var/www/html/*.php
sudo cp /home/ubuntu/snimbalk/final_project/img /var/www/html/img
sudo cp /home/ubuntu/snimbalk/final_project/css /var/www/html/css
sudo cp /home/ubuntu/snimbalk/final_project/js /var/www/html/js
#Schedule edit.php
sudo echo "* * * * * /usr/bin/php /var/www/html/edit.php" >> mycron
sudo crontab mycron
 
sudo service apache2 restart
echo "Hello" > /var/www/html/hello.txt