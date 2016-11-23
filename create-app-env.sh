#!/bin/bash
#Execution of shell script
#sh create-app-env.sh raw-smn finished-smn snimbalk-db dev root goodluck16 snimbalk_topic snimbalk_queue
#***********************************************************

#Variable declaration
str="s3://"
raw_bucket="$str$1"
finished_bucket="$str$2"
db_identifier=$3
db_name=$4
username=$5
password=$6
topic_name=$7
queue_name=$8
db_bucket="s3://my-db-bucket"
availability_zones="us-west-2b"

#App installation
sudo apt-get update -y
sudo apt-get install -y python-setuptools python-pip
sudo apt-get install -y php curl php-curl zip unzip php-mysql php-xml libapache2-mod-php mysql-client-5.7 
sudo curl -sS https://getcomposer.org/installer | php
sudo php composer.phar require aws/aws-sdk-php

#Create database
aws rds create-db-instance --db-name $db_name --db-instance-identifier $db_identifier --allocated-storage 5 --db-instance-class db.t2.micro --engine mysql --master-username $username --master-user-password $password --availability-zone $availability_zones --engine-version 5.6.27 --backup-retention-period 7

aws rds wait db-instance-available --db-instance-identifier $db_identifier
echo "Database is created..."

#Call php script to create tables
php create-tables.php

#Create SNS topic
topicarn=`aws sns create-topic --name $topic_name`
echo "$topicarn"
echo "SNS topic is created..."
#Subscribe to the topic
aws sns subscribe --topic-arn $topicarn --protocol email --notification-endpoint sonalin272@gmail.com
aws sns subscribe --topic-arn $topicarn --protocol sms --notification-endpoint 15129476633

#Create SQS
aws sqs create-queue --queue-name $queue_name	
echo "Queue is created..."

#Create buckets
aws s3 mb $raw_bucket --region us-west-2
aws s3 mb $finished_bucket --region us-west-2
aws s3 mb $db_bucket --region us-west-2

echo "S3 buckets are created..."


