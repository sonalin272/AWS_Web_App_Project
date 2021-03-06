<?php
include('profile.php');
require 'vendor/autoload.php';
//Get queue url and attributes
$sqs_client = new Aws\Sqs\SqsClient([
'version' => 'latest',
'region'  => 'us-west-2']);

//Create client to connect to RDS
$rds_client = new Aws\Rds\RdsClient([
'version' => 'latest',
'region'  => 'us-west-2']);

$endpoint = "";
$rds_result = $rds_client->describeDBInstances( array('DBInstanceIdentifier' => $db_identifier,));
$endpoint=$rds_result['DBInstances'][0]['Endpoint']['Address'];
//Connect to database
$link = mysqli_connect($endpoint,$db_user,$db_password,$db_name) or die("Error " . mysqli_connect_error());

//Get queue URL
$q_url = $sqs_client->getQueueUrl([
'QueueName' => $q_name, // REQUIRED
]);
//echo "url:" . $q_url['QueueUrl'];

//Check the count of messages
$msg_attributes = $sqs_client->getQueueAttributes([
    'AttributeNames' => ['ApproximateNumberOfMessages'],
    'QueueUrl' => $q_url['QueueUrl'], // REQUIRED
]);
$msg_count = $msg_attributes['Attributes']['ApproximateNumberOfMessages'];

if ($msg_count > 0) {
    $q_result = $sqs_client->receiveMessage([
    'MaxNumberOfMessages' => 1,
    'QueueUrl' => $q_url['QueueUrl'], // REQUIRED
    'VisibilityTimeout' => 5,
	]);
    $receipt_handle = $q_result['Messages'][0]['ReceiptHandle'];
    $receipt = $q_result['Messages'][0]['Body'];
	//echo $receipt;

    //Select result from records table
    $query = "SELECT * FROM RECORDS WHERE receipt = '$receipt'";
    if ($result = mysqli_query($link, $query))
    {
		$row = $result->fetch_assoc();
		echo "s3 raw URL : " . $row['s3_raw_url'];
		$raw_url = $row['s3_raw_url'];
		$filename = $row['filename'];
		mysqli_free_result($result);
     }
     else {
		printf("Error in execution : %s.\n", mysqli_error($link));
		exit();
     }
     
// Create an Imagick instance for sketch

$img = new Imagick($raw_url);
//$img->sketchImage(10, 0, 45);
$img->sepiaToneImage(90);
$imagename = "rendered"; //Unique name for output image
$ext = pathinfo($filename, PATHINFO_EXTENSION); //Get file extension
$image = $imagename . '.' . $ext;
$destpath = '/tmp/' . $image;
$img->writeImage($destpath); 

    //Put the modified imae into finished bucket
    //Create client to s3
    $s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-west-2'
    ]);

    $s_result = $s3->putObject([
    'ACL' => 'public-read',
    'Bucket' => $finished_bucket,
    'Key' => $filename,
    'SourceFile' => $destpath]);
    $s_url = $s_result['ObjectURL'];
    echo $s_url;

    //Update database to reflect status and finished URL.
    if ($result = mysqli_query($link, "UPDATE RECORDS SET s3_finished_url='$s_url',status=1 where filename='$filename'")) {
         printf("Value updated..");
    }
    else {
        printf("Error in execution : %s.\n", mysqli_error($link));
	exit();
    }   

    //Delete message from queue
    $sqs_result = $sqs_client->deleteMessage([
    'QueueUrl' => $q_url['QueueUrl'], // REQUIRED
    'ReceiptHandle' => $receipt_handle, // REQUIRED
    ]); 

    //Send notification to the customer
	//Create client to connect to SNS
	$sns_client = new Aws\Sns\SnsClient([
	'version' => 'latest',
	'region'  => 'us-west-2']);
	
	//$sns_result = $sns_client->listTopics([]);
	//$topic_arn = $sns_result['Topics'][0]['TopicArn'];
	
	//Select result from config table
	$query = "SELECT * FROM CONFIG where ID = 2";
	if ($result = mysqli_query($link, $query))
	{
            $row = $result->fetch_assoc();    
	echo "SNS URL : " . $row['val'];
            $topic_arn = $row['val'];
	mysqli_free_result($result);
 	}
	else {
            printf("Error in execution : %s.\n", mysqli_error($link));
            exit();
 	}
	//Publish message
	$sns_res = $sns_client->publish([
    	'Message' => 'Please view your processed image: '. $s_url, // REQUIRED
    	'Subject' => 'Image is processed!!',
    	'TopicArn' => $topic_arn,
	]);
}
else {
        echo "No messages in the queue!!!";
}
$link->close();

?>
