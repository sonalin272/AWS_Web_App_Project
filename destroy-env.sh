#!/bin/bash
#Execution of shell script
#sh destroy-env.sh

#******************************************************************************************

load_balancer_name=`aws elb describe-load-balancers --query 'LoadBalancerDescriptions[*].LoadBalancerName'`
launch_config_name=`aws autoscaling describe-launch-configurations --query 'LaunchConfigurations[*].LaunchConfigurationName'`
autoscaling_grp_name=`aws autoscaling describe-auto-scaling-groups --query 'AutoScalingGroups[*].AutoScalingGroupName'`

#Delete Autoscaling Groups
echo -e "\n List of autoscaling groups: \n $autoscaling_grp_name \n"
for i in $autoscaling_grp_name
do
	aws autoscaling update-auto-scaling-group --auto-scaling-group-name $i --min-size 0 --desired-capacity 0
	aws autoscaling delete-auto-scaling-group --auto-scaling-group-name $i --force-delete  
	echo "Autoscaling group $i is deleted..."
	sleep 5
done

#Delete launch configurations
echo -e "\n List of launch configurations: \n $launch_config_name \n"
for i in $launch_config_name
do
	aws autoscaling delete-launch-configuration --launch-configuration-name $i
	echo "Launch configuration $i is deleted..."
	sleep 5
done

#Delete load balancers
echo -e "\n List of load balancers: \n $load_balancer_name \n"
for i in $load_balancer_name
do
	aws elb delete-load-balancer --load-balancer-name $i
	echo "Load balancer $i is deleted..."
  	sleep 1
done

#Terminate instances
all_instances=`aws ec2 describe-instances --query 'Reservations[*].Instances[].InstanceId'`
aws ec2 terminate-instances --instance-ids $all_instances

aws ec2 wait instance-terminated --instance-ids $all_instances
echo -e "\n All instances are terminated... \n"

echo "Process is completed..."
