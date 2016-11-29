#!/bin/bash
#Execution of shell script
#sh destroy-env.sh

#******************************************************************************************

load_balancer_name=`aws elb describe-load-balancers --query 'LoadBalancerDescriptions[*].LoadBalancerName'`
launch_config_name=`aws autoscaling describe-launch-configurations --query 'LaunchConfigurations[*].LaunchConfigurationName'`
autoscaling_grp_name=`aws autoscaling describe-auto-scaling-groups --query 'AutoScalingGroups[*].AutoScalingGroupName'`

#Delete Autoscaling Groups
echo  "\n List of autoscaling groups: \n $autoscaling_grp_name \n"
for i in $autoscaling_grp_name
do
	aws autoscaling update-auto-scaling-group --auto-scaling-group-name $i --min-size 0 --desired-capacity 0
	aws autoscaling delete-auto-scaling-group --auto-scaling-group-name $i --force-delete  
	echo "Autoscaling group $i is deleted..."
	sleep 5
done

#Delete launch configurations
echo "\n List of launch configurations: \n $launch_config_name \n"
for i in $launch_config_name
do
	aws autoscaling delete-launch-configuration --launch-configuration-name $i
	echo "Launch configuration $i is deleted..."
	sleep 5
done

#Delete load balancers
echo "\n List of load balancers: \n $load_balancer_name \n"
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
echo "\n All instances are terminated... \n"

#Delete database
db_ids=`aws rds describe-db-instances --query 'DBInstances[*].DBInstanceIdentifier'`
echo "\n List of db identifiers: \n $db_ids \n"
for i in $db_ids
do
        aws rds delete-db-instance --db-instance-identifier $i --skip-final-snapshot
        echo "DB $i is deleted..."
        sleep 1
done

#Delete SQS
q_name=`aws sqs list-queues --query 'QueueUrls'`
echo "\n List of Queues: \n $q_name \n"
for i in $q_name
do
        aws sqs delete-queue --queue-url $i
        echo "Queue $i is deleted..."
        sleep 1
done

#Delete SNS topic and unsubscribe to the topic
sub_arn=`aws sns list-subscriptions --query 'Subscriptions[*].SubscriptionArn'`
echo "\n List of subscriptions: \n $sub_arn \n"
for i in $sub_arn
do
        aws sns unsubscribe --subscription-arn $i
        echo "$i is unsubscribed..."
        sleep 1
done
topic_arn=`aws sns list-topics --query 'Topics[*].TopicArn'`
echo "\n List of topics: \n $topic_arn \n"
for i in $topic_arn
do
        aws sns delete-topic --topic-arn $i
        echo "Topic $i is deleted..."
        sleep 1
done

#Delete s3 buckets
aws s3 rb s3://raw-smn --force
aws s3 rb s3://finished-smn --force
aws s3 rb s3://snimbalk-bucket --force
echo "Buckets are deleted..."

echo "Process is completed..."
