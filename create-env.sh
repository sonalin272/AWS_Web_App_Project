#!/bin/bash
#Execution of shell script
#sh create-env.sh ami-06b94666 snimbalk sg-fd8c4384 3 snimbalk-load-balancer snimbalk-launch-config snimbalk-auto-scaling-group firsttoken
#*****************************************************************************************************************************

#Variable declaration
image_id=$1
key_name=$2
security_grp_id=$3
number_of_instances=$4
client_token=$8
availability_zones="us-west-2b"
load_balancer_name=$5
launch_config_name=$6
autoscaling_grp_name=$7
min_size=2
max_size=5
desired_size=4

if [ $# -ne 8 ] ; then
echo  " Please provide correct number of arguments..Expected argument count is 8."
echo "e.g : sh create-env.sh ami-06b94666 snimbalk sg-fd8c4384 3 snimbalk-load-balancer snimbalk-launch-config snimbalk-auto-scaling-group xxx"
else
echo " $@ "

#Create instances
aws ec2 run-instances --image-id $image_id --key-name $key_name  --security-group-ids $security_grp_id --instance-type t2.micro --count $number_of_instances --placement AvailabilityZone=$availability_zones --client-token $client_token --user-data file://installapp.sh

#Wait till instances start running
aws ec2 wait instance-running --filters "Name=client-token,Values=$client_token"
echo "Instances are running...."

#Create load balancer
aws elb create-load-balancer --load-balancer-name $load_balancer_name --security-groups $security_grp_id --listeners "Protocol=HTTP,LoadBalancerPort=80,InstanceProtocol=HTTP,InstancePort=80" --availability-zones $availability_zones
echo "Load balancer $5 is created..."

#Attach instances to load balancer
ID=`aws ec2 describe-instances --filters "Name=client-token,Values=$client_token" --query 'Reservations[*].Instances[].InstanceId'`
aws elb register-instances-with-load-balancer --load-balancer-name $load_balancer_name --instances $ID

#Launch configuration for autoscaling
aws autoscaling create-launch-configuration --launch-configuration-name $launch_config_name --image-id $image_id --key-name $key_name --security-groups $security_grp_id --instance-type t2.micro --user-data file://installapp.sh

#Create autoscaling group
aws autoscaling create-auto-scaling-group --auto-scaling-group-name $autoscaling_grp_name --launch-configuration-name $launch_config_name --load-balancer-names $load_balancer_name --availability-zones $availability_zones --min-size 0 --max-size $max_size --desired-capacity 0
echo "Autoscaling group $7 is created.."

aws autoscaling attach-instances --instance-ids $ID --auto-scaling-group-name $autoscaling_grp_name

aws autoscaling update-auto-scaling-group --auto-scaling-group-name $autoscaling_grp_name --launch-configuration-name $launch_config_name --min-size $min_size --desired-capacity $desired_size	

echo "Process completed...."

fi
