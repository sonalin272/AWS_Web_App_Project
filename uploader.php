<?php
session_start();
require 'vendor/autoload.php';
if (isset($_FILES['file'])and isset($_POST['buttonFile']))
{
    $filename = $_FILES['file'];
    $user = $_SESSION['username'];
   // move_uploaded_file($_FILES['file']['tmp_name'], $_FILES['file']['name']);
    
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
     echo $url; 
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
	//Select result
	$s_query = "SELECT id,email,phone,s3_raw_url FROM records where email='$user'";
	if ($result = mysqli_query($link, $s_query)) 
	{
		$row = mysqli_fetch_row($result);
	        //printf ("%s %s %s \n", $row[1], $row[2], $row[3]);		
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
echo "filename: " . $filename['name'];
echo "username: " . $row[1];
echo "phone:" . $row[2];
echo "Link to uploaded image:" . $row[3];
?>
</body>
</html>

