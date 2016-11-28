<?php
session_start();
require 'vendor/autoload.php';
include('profile.php');
if (isset($_FILES['file'])and isset($_POST['buttonFile']))
{
    $filename = $_FILES['file'];
    $user = $_SESSION['username'];    
    $uploaddir = '/tmp/';
    $uploadfile = $uploaddir . basename($_FILES['file']['name']);

    // Print whether file upload was successful or not
    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile))
    {
	echo "File is valid, and was successfully uploaded.\n";
	}
    else
	{
	echo "Possible file upload attack!\n";
    }
    //Create client to s3 
    $s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-west-2'
    ]);

    $s_result = $s3->putObject([
    'ACL' => 'public-read',
    'Bucket' => $raw_bucket,
    'Key' => basename($uploadfile),
    'SourceFile' => $uploadfile]);
    $url = $s_result['ObjectURL'];

   //Create client to connect to RDS
    $rds_client = new Aws\Rds\RdsClient([
	'version' => 'latest',
	'region'  => 'us-west-2']);
    $endpoint = "";
    $rds_result = $rds_client->describeDBInstances( array('DBInstanceIdentifier' => $db_identifier,));
    $endpoint=$rds_result['DBInstances'][0]['Endpoint']['Address'];
    //Connect to database
    $link = mysqli_connect($endpoint,$db_user,$db_password,$db_name) or die("Error " . mysqli_error($link));
    //Check connection
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
    // Prepared statement
    if (!($stmt = $link->prepare("INSERT INTO RECORDS (ID,email,phone,filename,s3_raw_url,s3_finished_url,status,receipt) VALUES (NULL,?,?,?,?,?,?,?)"))) {
	printf("Prepare failed: %s.\n", mysqli_stmt_error($result));
	exit();     
    }
	$email=$user;
	$phone='15129476633';
	$filename=basename($uploadfile);
	$rawurl=$url;
	$finishedurl='';
	$status=0;
	$receipt=md5($url);
	// prepared statements will not accept literals (pass by reference) in bind_params, you need to declare variables
	$stmt->bind_param("sssssis",$email,$phone,$filename,$rawurl,$finishedurl,$status,$receipt);

	if (!$stmt->execute()) {
    		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	else
	{
		printf("%d Row inserted.\n", $stmt->affected_rows);
		//Get queue url and attributes
		$sqs_client = new Aws\Sqs\SqsClient([
    		'version' => 'latest',
    		'region'  => 'us-west-2']);
	
		$q_url = $sqs_client->getQueueUrl([
    		'QueueName' => $q_name, // REQUIRED
		]);
		//echo "url:" . $q_url['QueueUrl'];
		$q_result = $sqs_client->sendMessage([
    		'DelaySeconds' => 1,
    		'MessageBody' => $receipt, // REQUIRED
    		'QueueUrl' => $q_url['QueueUrl'], // REQUIRED
		]);
	}

	/* explicit close recommended */
	$stmt->close();

	/*Select result
	$s_query = "SELECT id,email,phone,s3_raw_url FROM records where email='$user' and filename='$filename'";
	if ($result = mysqli_query($link, $s_query)) 
	{
		$row = mysqli_fetch_row($result);
		//Close statement
		mysqli_free_result($result);
	}
	else {
		printf("Error: %s.\n", mysqli_stmt_error($result));
	}*/
	mysqli_close($link);
}
?>
<html>
<head>
</head>
<body>
<?php
printf ("filename: %s", $filename, "\n");
printf ("username: %s" , $user,"\n");
printf ("phone: %s", $phone, "\n");
printf ("URL of uploaded image: %s" , $url, "\n");
?>
<div style="min-height:300px;padding-left:20%;padding-right:20%;padding-top:5%">
   <form enctype="multipart/form-data" id="uploader" action="upload.php" method="POST">
      <!-- MAX_FILE_SIZE must precede the file input field -->
      <input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
      <!-- Name of input element determines name in $_FILES array -->
      <input type="submit" id="uploadFile" name="uploadFile" value="BaCK"/>
   </form>
</div>
</body>
</html>

