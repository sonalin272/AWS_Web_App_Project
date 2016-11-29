<?php
session_start();
include('profile.php');
require 'vendor/autoload.php';
   //Create client to connect to RDS
    $rds_client = new Aws\Rds\RdsClient([
	'version' => 'latest',
	'region'  => 'us-west-2']);
    $endpoint = "";
    $rds_result = $rds_client->describeDBInstances( array('DBInstanceIdentifier' => $db_identifier,));
    $endpoint=$rds_result['DBInstances'][0]['Endpoint']['Address'];
    //Connect to database
    $link = mysqli_connect($endpoint,$db_user,$db_password,$db_name) or die("Error " . mysqli_connect_error());
 
    $uploaddir = '/tmp/';
    $ext = 'database_backup.sql';
    $bkppath = $uploaddir . $ext;

    //Create client for s3 
    $s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-west-2'
    ]);

    //Restore database from s3 bucket
    if (isset($_POST["restoreData"]))
    {
	$result = $s3->getObject([
    	'Bucket' => $my_bucket, // REQUIRED
	'Key' => 'database_backup.sql',
	'SaveAs' => $bkppath
	]);	
	$bk_path=$result['@metadata']['effectiveUri'];
	$command = "mysql --user=$db_user --password=$db_password --host=$endpoint $db_name < $bkppath";
	exec($command);

	// Validate database restore
	if (!$result = mysqli_query($link, "SELECT count(*) FROM USERS")) {
		printf("Error in execution : %s.\n", mysqli_error($link));
    		/* free result set */
    		mysqli_free_result($result);
	}
	else {
		printf("Data is restored successfully!!!");	
	}
    }
    //Store backup of database in s3 bucket
    else if (isset($_POST["storeBkp"]))
    {
	$command = "mysqldump --user=$db_user --password=$db_password --host=$endpoint $db_name > $bkppath";
	exec($command);

	//Pull data from s3 bucket
	$result = $s3->putObject([
    	'ACL' => 'public-read',
    	'Bucket' => $my_bucket,
    	'Key' => basename($bkppath),
    	'SourceFile' => $bkppath]);
	$url = $result['ObjectURL'];
	$_SESSION['s3_url'] = $url;
	echo $url;
    }
    if (isset($_POST["save"])){
    	$mode = $_POST["gender"];
    	if ($result = mysqli_query($link, "UPDATE CONFIG SET val='$mode'")) {
        	 printf("Value updated..");
		mysqli_free_result($result);
	}
	else {
		printf("Error in execution : %s.\n", mysqli_error($link));
	}
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
 	Enable/disable read-only mode:
   	</br>
	  <input type="radio" name="gender" value="N" checked> No<br>
          <input type="radio" name="gender" value="Y"> Yes
	</br>
	<input type="submit"  id= "save" name="save" value="Save" />
	</br></br>
         <a href="welcome.php" style="color:Teal;font-family:'Salsa';font-style:cursive;font-size:120%;font-weight:bold;">View our gallery</a>
         </br></br>
      </div>
   </form>
</div>
</body>
</html>
