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
if (isset($_POST["restoreData"]))
	{
$result = $s3->getObject([
    'Bucket' => 'snimbalkbucket', // REQUIRED
'Key' => 'database_backup.sql',
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
}
else if (isset($_POST["storeBkp"]))
	{
	// Print the backup path

	$command = "mysqldump --user=root --password=Goodluck16 --host=$endpoint school > $bkppath";
	exec($command);

$result = $s3->putObject([
    'ACL' => 'public-read',
    'Bucket' => 'snimbalkbucket',
    'Key' => basename($bkppath),
    'SourceFile' => $bkppath]);
$url = $result['ObjectURL'];
$_SESSION['s3_url'] = $url;
echo $url;
}
mysqli_close($link);
?>
<html>
<body>
<div style="min-height:300px;padding-left:20%;padding-right:20%;padding-top:5%">
   <form name="admin"  method="post" action="">
      <div style="padding-left:20%;padding-right:20%;padding-top:10%;padding-bottom:10%">
         <input type="submit"  id= "restoreData" name="restoreData" value="Restore Data" />
         <input type="submit"  id= "storeBkp" name="storeBkp" value="Store Backup" />
         </br></br>
         <a href="welcome.php" style="color:Teal;font-family:'Salsa';font-style:cursive;font-size:120%;font-weight:bold;">View our gallery</a>
         </br></br>
      </div>
   </form>
</div>
</body>
</html>
