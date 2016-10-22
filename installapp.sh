#!/bin/bash
sudo apt-get update -y
sudo apt-get install -y apache2
sudo systemctl enable apache2
sudo systemctl start apache2
echo "Hello" > /var/www/html/hello.txt
git clone git@github.com:illinoistech-itm/snimbalk.git /home/ubuntu/snimbalk
