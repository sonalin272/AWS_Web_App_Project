#!/bin/bash
#Execution of shell script
#sh create-app-env.sh 
#*****************************************************************************************************************************

aws rds create-db-instance --db-name dev --db-instance-identifier snimbalk-db --allocated-storage 5 --db-instance-class db.t2.micro --engine mysql --master-username root --master-user-password Goodluck16 --availability-zone us-west-2b --engine-version 5.6.27 --backup-retention-period 7

aws rds wait db-instance-available --db-instance-identifier snimbalk-db 

#aws rds delete-db-instance --db-instance-identifier snimbalk-db --skip-final-snapshot


