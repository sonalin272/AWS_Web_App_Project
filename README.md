# Repository Name : snimbalk
# Project folder : snimbalk/final_project
# Execution steps: 
1. Ensure that IAM profile, security key, developer role, security group exists and accordingly replace values while passing arguments to create-env.sh
2. Place below 6 files from /snimbalk/final_project folder of git repository into /home/ubuntu/ directory.
	create-app-env.sh, create-tables.php, create-env.sh, installapp.sh, installappcronjob.sh, destroy-env.sh
3. sh create-app-env.sh
4. sh create-env.sh ami-438b2a23 snimbalk sg-fd8c4384 snimbalk-load-balancer snimbalk-launch-config snimbalk-auto-scaling-group 4
5. Wait for 1 min to get environment ready and then open web app url(index.php) in the browser to access application
	Controller username and password -
	Username: controller
	Password : admin
6. sh destroy-env.sh
-----------------------------------------------------------------------------------------------------------------------------------------------

#create-app-env.sh 
It creates required aws services like SNS, SQS, RDS and s3 buckets. Below are the names of services which gets created by script -
S3 buckets : raw-smn, finished-smn, snimbalk-bucket,
SQS : snimbalk-queue
SNS :  snimbalk-topic 
RDS : DB identifier - snimbalk-db, DB name - dev, User - root, Password - goodluck16

#create-tables.php 
This script gets called from create-app-env.sh script. It creates below required tables -
RECORDS, CONFIG, USERS

#create-env.sh
This script creates distributed environment; it includes autoscaling group, load balancer and instancs. Ensure that IAM profile, security key, developer role, security group exists before executing this script.Below are the arguments need to be passed to the script-
sh create-env.sh <IAM> <key> <security_group> <load_balancer> <launch_config_name> <autoscaling_group_name> <instance_count>

#Installapp.sh 
It gets called from create-env.sh script. It clones git repository on newly created instances and installs required packages on the instances. php scripts, images, js files will get placed in appropriate folders on the instaces.

#Installappcronjob.sh 
It gets called from create-env.sh script. It clones git repository on newly created instance and installs required packages on the instance. It schedules cron job to execute edit.php.

#Destroy-env.sh 
It destroyes the environment and deletes all services.

-------------------------------------------------------------------------------------------------

#PHP Scripts (scripts will get copied to /var/www/html folder after creation of instances)

#profile.php
It includes declaration of all variables like SQS name, SNS name, RDS details, S3 bucket names and it is used by all php scripts.

#index.php
It is first page of application which allows user to login to the app.

#login.php
It validates the user credntials with db and accordingly gives access to the user.

#welcome.php
It lists all facilities/activities that user can perform.

#gallery.php
It displays raw and finished images for user in lightbox image gallery.

#upload.php
It allows user to upload images for further processing.

#uploader.php
It inserts details of each image uploaded by user into db table and sends the message to queue. Also ,it inserts original image into raw bucket.

#edit.php
It fetches each record inserted in db and raw image from s3 bucket, and competes image processing. After completion of process it deletes message from queue and sends notification to user.

#admin.php
Only admin i.e 'controller' user can perform below operations -
Store backup of DB
Restore DB
Disable/Enable upload functionality

