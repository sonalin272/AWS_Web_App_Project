#!/bin/bash
#Example of shell script execution
#sh destroy-env.sh

#******************************************************************************************

#Detach load balancer from autoscaling goup
load_balancer_name=`aws elb describe-load-balancers --query 'LoadBalancerDescriptions[*].LoadBalancerName'`
autoscaling_grp_name=`aws autoscaling describe-auto-scaling-groups --query 'AutoScalingGroups[*].AutoScalingGroupName'`
aws autoscaling detach-load-balancers --load-balancer-names $load_balancer_name --auto-scaling-group-name $autoscaling_grp_name
echo "Load balancer detached..."

#Delete autoscaling group
aws autoscaling delete-auto-scaling-group --auto-scaling-group-name $autoscaling_grp_name  --force-delete
echo "Autoscaling group is deleted..."

#Delete launch configurations
launch_config_name=`aws autoscaling describe-auto-scaling-groups --query 'AutoScalingGroups[*].LaunchConfigurationName'`
aws autoscaling delete-launch-configuration --launch-configuration-name $launch_config_name
echo "Lauch configurations are deleted..."

#Dettach instances from load balancer
ID=`aws elb describe-load-balancers --query 'LoadBalancerDescriptions[*].Instances[].InstanceId'`
aws elb deregister-instances-from-load-balancer --load-balancer-name $load_balancer_name --instances $ID
echo "Instances are detached from load balancer..."

#Delete listeners
aws elb delete-load-balancer-listeners --load-balancer-name $load_balancer_name --load-balancer-ports 80

#Delete policy
policy_name=`aws elb describe-load-balancers --query 'LoadBalancerDescriptions[*].Policies[].OtherPolicies'`
aws elb delete-load-balancer-policy --load-balancer-name $load_balancer_name --policy-name $policy_name

#Delete load-balancer
aws elb delete-load-balancer --load-balancer-name $load_balancer_name
echo "Load balancer is deleted..."

#Terminate instances
all_instances=`aws ec2 describe-instances --query 'Reservations[*].Instances[].InstanceId'`
aws ec2 terminate-instances --instance-ids $all_instances
echo "All instances are terminated..."
echo "Process completed..."
