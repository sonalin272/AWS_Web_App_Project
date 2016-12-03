#!/bin/bash
#Execution of shell script
#sh create-env.sh ami-438b2a23 snimbalk sg-fd8c4384 snimbalk-load-balancer snimbalk-launch-config snimbalk-auto-scaling-group 4
#********************************************************************************************************************

#Variable declaration
image_id=$1
key_name=$2
security_grp_id=$3
availability_zones="us-west-2b"
load_balancer_name=$4
launch_config_name=$5
autoscaling_grp_name=$6
min_size=2
max_size=5
desired_size=$7
#client_token=$8

if [ $# -ne 7 ] ; then
	echo   "\n Please provide correct number of arguments..Expected argument count is 7."
	echo  "e.g : sh create-env.sh ami-06b94666 snimbalk sg-fd8c4384 snimbalk-load-balancer snimbalk-launch-config snimbalk-auto-scaling-group 2 \n"
else
	#Create load balancer
	aws elb create-load-balancer --load-balancer-name $load_balancer_name --security-groups $security_grp_id --listeners "Protocol=HTTP,LoadBalancerPort=80,InstanceProtocol=HTTP,InstancePort=80" --availability-zones $availability_zones
	echo "\n Load balancer $load_balancer_name is created... \n"

	#Configure the elb configure-health-check and attach cookie stickiness policy

	echo  "Configuring health and cookie stickiness policies for load balancer \n"

	aws elb configure-health-check --load-balancer-name $load_balancer_name --health-check Target=HTTP:80/index.php,Interval=30,UnhealthyThreshold=2,HealthyThreshold=2,Timeout=3

	aws elb create-lb-cookie-stickiness-policy --load-balancer-name $load_balancer_name --policy-name snimbalk-lb-cookie-policy --cookie-expiration-period 60

	aws elb set-load-balancer-policies-of-listener --load-balancer-name $load_balancer_name --load-balancer-port 80 --policy-names snimbalk-lb-cookie-policy

	#Launch configuration for autoscaling	
	aws autoscaling create-launch-configuration --launch-configuration-name $launch_config_name --image-id $image_id --key-name $key_name --security-groups $security_grp_id --instance-type t2.micro --user-data file://installapp.sh --iam-instance-profile developer

	#Create autoscaling group
	aws autoscaling create-auto-scaling-group --auto-scaling-group-name $autoscaling_grp_name --launch-configuration-name $launch_config_name --load-balancer-names $load_balancer_name --availability-zones $availability_zones --min-size $min_size --max-size $max_size --desired-capacity $desired_size
	echo  "Autoscaling group $autoscaling_grp_name is created.... \n"

	#Wait till all instances start running
        aws ec2 wait instance-running --filters "Name=image-id,Values=$image_id" "Name=instance-state-code,Values=0,16"
		
	#Create instance to execute edit.php
	echo "Cron job will be scheduled on below instance.. \n"
	aws ec2 run-instances --image-id $image_id --key-name $key_name  --security-group-ids $security_grp_id --instance-type t2.micro --count 1 --placement AvailabilityZone=$availability_zones --user-data file://installappcronjob.sh --iam-instance-profile Name=developer
	
	#Wait till instances start running
	aws ec2 wait instance-running --filters "Name=image-id,Values=$image_id" "Name=instance-state-code,Values=0,16"
	echo "Below instances are running...."	

	ID=`aws ec2 describe-instances --filters "Name=image-id,Values=$image_id" "Name=instance-state-code,Values=16" --query 'Reservations[*].Instances[].InstanceId'`
	echo $ID
	echo  "\n Process is completed...."
fi
