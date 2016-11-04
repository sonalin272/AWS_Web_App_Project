#!/bin/bash
#Execution of shell script
#sh create-app-env.sh raw-smn finished-smn
#*****************************************************************************************************************************
#Variable declaration
str='s3://'
raw_bucket=$str$1
final_bucket=$str$2

#Create database
aws rds create-db-instance --db-name school --db-instance-identifier snimbalk-db --allocated-storage 5 --db-instance-class db.t2.micro --engine mysql --master-username root --master-user-password Goodluck16 --availability-zone us-west-2b --engine-version 5.6.27 --backup-retention-period 7

aws rds wait db-instance-available --db-instance-identifier snimbalk-db 

#Create SNS
#Create topic
aws sns create-topic --name snimbalk
#Subscribe
aws sns subscribe --topic-arn arn:aws:sns:us-west-2:299205658970:snimbalk --protocol email --notification-endpoint sonalin272@gmail.com

#Create SQS
aws sqs create-queue --queue-name snimbalk_queue	

#Create buckets
aws s3 mb $raw_bucket --region us-west-2
aws s3 mb $final_bucket --region us-west-2
#aws rds delete-db-instance --db-instance-identifier snimbalk-db --skip-final-snapshot
