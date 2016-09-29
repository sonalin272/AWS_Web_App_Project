#!/bin/bash
#Example of shell script execution
#sh destroy-env.sh

#******************************************************************************************

load_balancer_name=`aws elb describe-load-balancers --query 'LoadBalancerDescriptions[*].LoadBalancerName'`
launch_config_name=`aws autoscaling describe-auto-scaling-groups --query 'AutoScalingGroups[*].LaunchConfigurationName'`
autoscaling_grp_name=`aws autoscaling describe-auto-scaling-groups --query 'AutoScalingGroups[*].AutoScalingGroupName'`

#Detach load balancer from autoscaling goup
aws autoscaling detach-load-balancers --load-balancer-names $load_balancer_name --auto-scaling-group-name $autoscaling_grp_name
echo "Load balancer detached..."

#Set desired capacity to 0
aws autoscaling update-auto-scaling-group --auto-scaling-group-name $autoscaling_grp_name --launch-configuration-name $launch_config_name --min-size 0 --desired-capacity 0

#Detach instances from load balancer
ID=`aws elb describe-load-balancers --query 'LoadBalancerDescriptions[*].Instances[].InstanceId'`
aws elb deregister-instances-from-load-balancer --load-balancer-name $load_balancer_name --instances $ID
echo "Instances are detached from load balancer..."

#Terminate instances
all_instances=`aws ec2 describe-instances --query 'Reservations[*].Instances[].InstanceId'`
aws ec2 terminate-instances --instance-ids $all_instances

aws ec2 wait instance-terminated --instance-ids $all_instances
echo "All instances are terminated..."

#Delete autoscaling group
aws autoscaling delete-auto-scaling-group --auto-scaling-group-name $autoscaling_grp_name --force-delete  
echo "Autoscaling group is deleted..."

#Delete launch configurations
aws autoscaling delete-launch-configuration --launch-configuration-name $launch_config_name
echo "Lauch configurations are deleted..."

#Delete listeners
aws elb delete-load-balancer-listeners --load-balancer-name $load_balancer_name --load-balancer-ports 80


#Delete load-balancer
aws elb delete-load-balancer --load-balancer-name $load_balancer_name
echo "Load balancer is deleted..."

echo "Process completed..."
