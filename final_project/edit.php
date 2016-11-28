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
     
     //Process image
     // load the "stamp" and photo to apply the water mark to
     $stamp = imagecreatefrompng('IIT-logo.png'); 
     $im = imagecreatefrompng($raw_url);  
     //Set the margins for the stamp and get the height and width of the stamp image
     $marge_right=10;
     $marge_bottom=10;
     $sx = imagesx($stamp);
     $sy = imagesy($stamp);
     echo $sy . "\n";

    //Copy the stamp image onto our photo using the margin offsets and the photo 
    // width to calculate positioning of the stamp
    imagecopy($im,$stamp,imagesx($im) - $sx -$marge_right, imagesy($im) - $sy -$marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));

    //output and free memory
    imagepng($im,'/tmp/rendered.png');
    imagedestroy($im);
 
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
    'SourceFile' => '/tmp/rendered.png']);
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
	
	$sns_result = $sns_client->listTopics([]);
	$topic_arn = $sns_result['Topics'][0]['TopicArn'];
	
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
