<?php
session_start();
require 'vendor/autoload.php';
if (1==1 or isset($_FILES['file'])and isset($_POST['buttonFile']))
{
    $filename = $_FILES['file'];
    $user = 'snimbalk';//$_SESSION['username'];
    
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
    'Bucket' => 'raw-smn',
    'Key' => basename($uploadfile),
    'SourceFile' => $uploadfile]);
    $url = $s_result['ObjectURL'];
  //   echo $url; 
   //Create client to connect to RDS
    $rds_client = new Aws\Rds\RdsClient([
	'version' => 'latest',
	'region'  => 'us-west-2']);
    $endpoint = "";
    $rds_result = $rds_client->describeDBInstances( array('DBInstanceIdentifier' => 'snimbalk-db',));
    $endpoint=$rds_result['DBInstances'][0]['Endpoint']['Address'];
    //Connect to database
    $link = mysqli_connect($endpoint, 'root', 'Goodluck16','school') or die("Error " . mysqli_error($link));
	//Check connection
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
// Prepared statement
if (!($stmt = $link->prepare("INSERT INTO records (ID,email,phone,s3_raw_url,s3_finished_url,status,receipt) VALUES (NULL,?,?,?,?,?,?)"))) {
    echo "Prepare failed: (" . $stmt->errno . ") " . $stmt->error;
}
$email=$user;
$phone='1234567';
$rawurl=$url;
$finishedurl=' ';
$status=0;
$receipt=md5($url);
// prepared statements will not accept literals (pass by reference) in bind_params, you need to declare variables
$stmt->bind_param("ssssis",$email,$phone,$rawurl,$finishedurl,$status,$receipt);

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}
else
{
printf("%d Row inserted.\n", $stmt->affected_rows);
#Get q url n attributes
$sqs_client = new Aws\Sqs\SqsClient([
    'version' => 'latest',
    'region'  => 'us-west-2'
]);
$q_url = $sqs_client->getQueueUrl([
    'QueueName' => 'snimbalk_queue', // REQUIRED
]);
echo "url:" . $q_url['QueueUrl'];
$q_result = $sqs_client->sendMessage([
    'DelaySeconds' => 1,
    'MessageBody' => $receipt, // REQUIRED
    'QueueUrl' => $q_url['QueueUrl'], // REQUIRED
]);
echo $q_result;
}

/* explicit close recommended */
$stmt->close();

	//Select result
	$s_query = "SELECT id,email,phone,s3_raw_url FROM records where email='$user'";
	if ($result = mysqli_query($link, $s_query)) 
	{
		$row = mysqli_fetch_row($result);
	   //     printf ("%s %s %s \n", $row[1], $row[2], $row[3]);		
		//Close statement
		mysqli_free_result($result);
	}
	else {echo "failed..";}
	mysqli_close($link);
}
?>
<html>
<head>
</head>
<body>
<?php
//print_r($filename);
printf ("filename: %s", $filename['name'], "\n");
printf ("username: %s" , $row[1],"\n");
printf ("phone: %s", $row[2], "\n");
printf ("Link to uploaded image: %s" , $row[3], "\n");
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

