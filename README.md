# Repository Name : snimbalk
# Project folder : snimbalk/final_project
# Execution steps: 

1. Ensure that IAM profile, security key, 'developer' role, security group exists and accordingly replace values while passing arguments to create-env.sh

2. Place below 6 files from /snimbalk/final_project folder of git repository into your /home/ubuntu/ directory.
	create-app-env.sh, create-tables.php, create-env.sh, installapp.sh, installappcronjob.sh, destroy-env.sh

3. Please replace below positional parameter by your mobile number if you want to receive notifiction on your mobile .'hajeck@iit.edu' is already added in the script so kindly click on confirm option once you receive an email from aws after execution of below script.
	sh create-app-env.sh 15129476633 

	Kindly put eartrumpet.png, Knuth.jpg images into 'raw-smn' and eartrumpet-bw.png, Knuth-bw.jpg images into 'finished-smn' buckets. This will display images in the 'controller' user's gallery to start with an application.If you change bucket names in create-app-env.sh and profile.php scripts then update urls in the insert statements of create-tables.php as well else images will be blank in gallery.

4. sh create-env.sh ami-438b2a23 snimbalk sg-fd8c4384 snimbalk-load-balancer snimbalk-launch-config snimbalk-auto-scaling-group 4

5. Wait for 1 min to get environment ready and then open web app url(index.php) in the browser to access application
	Admin username and password -
	Username: controller
	Password : admin
	You username and password -
	username: hajek@iit.edu
	password: ilovebunny
	(Username and password details are also listed in create-tables.php)
6. sh destroy-env.sh
---------------------------------------------------------------------------------------------------------------------------------

#create-app-env.sh 
It installs php, mysql on ubuntu virtual machine before calling cretae-tables.php script.
It creates required aws services like SNS, SQS, RDS and s3 buckets. Below are the names of services which gets created by script -
S3 buckets : raw-smn, finished-smn, snimbalk-bucket
SQS : snimbalk-queue
SNS :  snimbalk-topic 
RDS : DB identifier - snimbalk-db, DB name - dev, User - root, Password - goodluck16

Note: By default s3 buckets, SQS, SNS and RDS will get created by above names. If you want to change any of these names then those changes must reflect in profile.php file on /snimbalk/final_project github repo i.e before it gets cloned on all instances via installapp.sh script else manually update profile.php on all instances to reflect changed names. Basically, names in create-app-env.sh and profile.php should be in sync.

#create-tables.php 
This script gets called from create-app-env.sh script. It creates below required tables and inserts data into them-
RECORDS, CONFIG, USERS

Note: Record table inserts 2 records in advance for controller user. While inserting records, it uses hardcoded urls as below - 
https://s3-us-west-2.amazonaws.com/raw-smn/eartrumpet.png
https://s3-us-west-2.amazonaws.com/finished-smn/eartrumpet-bw.png
https://s3-us-west-2.amazonaws.com/raw-smn/Knuth.jpg
https://s3-us-west-2.amazonaws.com/finished-smn/Knuth-bw.jpg

Kindly put eartrumpet.png, Knuth.jpg images into 'raw-smn' and eartrumpet-bw.png, Knuth-bw.jpg images into 'finished-smn' buckets.
If you change bucket names in create-app-env.sh and profile.php scripts then update above urls as well in create-tables.php else images will be blank.

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

