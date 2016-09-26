#!/bin/bash
#Execution of shell script
#sh create-env.sh ami-06b94666 
#*********************************************************************************************

#Variable declaration
image_id=$1
key_name="snimbalk"
security_grp_id="sg-fd8c4384"
number_of_instances=3
client_token="550e8400-e29b-41d4-a716-446655440020"
availability_zones="us-west-2b"
load_balancer_name="snimbalk-load-balancer"
policy_name="my-ProxyProtocol-policy"
launch_config_name="snimbalk-launch-config"
autoscaling_grp_name="snimbalk-auto-scaling-group"
min_size=2
max_size=5
desired_size=4

if [ $# -ne 1 ] ; then
echo -e  " Please provide correct number of arguments"
else
echo " $1 "

#Create instances
aws ec2 run-instances --image-id $image_id --key-name $key_name  --security-group-ids $security_grp_id --instance-type t2.micro --count $number_of_instances --placement AvailabilityZone=$availability_zones --client-token $client_token --user-data file://installapp.sh

#Wait till instances start running
aws ec2 wait instance-running --filters "Name=client-token,Values=$client_token"
echo "Instances are running...."

#Create load balancer
aws elb create-load-balancer --load-balancer-name $load_balancer_name --security-groups $security_grp_id --listeners "Protocol=HTTP,LoadBalancerPort=80,InstanceProtocol=HTTP,InstancePort=80" --availability-zones $availability_zones

#Create load balancer policy
aws elb create-load-balancer-policy --load-balancer-name $load_balancer_name --policy-name $policy_name --policy-type-name ProxyProtocolPolicyType --policy-attributes AttributeName=ProxyProtocol,AttributeValue=true

#Attach instances to load balancer
ID=`aws ec2 describe-instances --filters "Name=client-token,Values=$client_token" --query 'Reservations[*].Instances[].InstanceId'`
aws elb register-instances-with-load-balancer --load-balancer-name $load_balancer_name --instances $ID

#Launch configuration for autoscaling
aws autoscaling create-launch-configuration --launch-configuration-name $launch_config_name --image-id $image_id --key-name $key_name --security-groups $security_grp_id --instance-type t2.micro --user-data file://installapp.sh

#Create autoscaling group
aws autoscaling create-auto-scaling-group --auto-scaling-group-name $autoscaling_grp_name --launch-configuration-name $launch_config_name --load-balancer-names $load_balancer_name --availability-zones $availability_zones --min-size $min_size --max-size $max_size --desired-capacity $desired_size	

echo "Process completed...."

fi
