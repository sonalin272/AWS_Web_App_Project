<?php
session_start();
require 'vendor/autoload.php';
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
/*if (!$result = mysqli_query($link,"DROP TABLE IF EXISTS records")) {
    echo "Table deletion failed: (" . $mysqli->errno . ") " . $mysqli->error;
}*/
 $uploaddir = '/tmp/';
 $ext = 'database_backup.sql';
 $bkppath = $uploaddir . $ext;

	//Create client for s3 
	$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-west-2'
]);
$result = $s3->getObject([
    'Bucket' => 'snimbalkbucket', // REQUIRED
'Key' => 'database_backup_19_pm_11_18_16.sql',
'SaveAs' => $bkppath
]);	
$bk_path=$result['@metadata']['effectiveUri'];

	$command = "mysql --user=root --password=Goodluck16 --host=$endpoint school < $bkppath";
	exec($command);

/* Select queries return a resultset */
if ($result = mysqli_query($link, "SELECT * FROM records")) {
    printf("Select returned %d rows.\n", mysqli_num_rows($result));

    /* free result set */
    mysqli_free_result($result);
}	
	/*$uploaddir = '/tmp/';
	//$bkpname = uniqid("itmo544-mrp-customerrecords", false);
	$ext = 'database_backup.sql';
	$bkppath = $uploaddir . $ext;

	// Print the backup path

	echo  "Backup Path is :  $bkppath";
	$command = "mysqldump --user=root --password=Goodluck16 --host=$endpoint school > $bkppath";
	exec($command);
	mysqli_close($link);
	
	//Create client for s3 
	$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-west-2'
]);

$result = $s3->putObject([
    'ACL' => 'public-read',
    'Bucket' => 'snimbalkbucket',
    'Key' => basename($bkppath),
    'SourceFile' => $bkppath]);
$url = $result['ObjectURL'];
$_SESSION['s3_url'] = $url;
echo $url;*/
?>
