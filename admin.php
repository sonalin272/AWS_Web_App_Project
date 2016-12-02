<?php
session_start();
include('profile.php');
require 'vendor/autoload.php';
use Aws\Exception\AwsException;
use Aws\S3\Exception\S3Exception;
$user=$_SESSION['username'];
?>
<html>
	<head>
         <title>Image Processing Application</title>
         <link rel="stylesheet" type="text/css" href="css/demo.css">
         <link href='http://fonts.googleapis.com/css?family=Salsa' rel='stylesheet' type='text/css'>
         <meta charset="utf-8">
         <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	</head>
	<body id="b01">
		<div class="left">
			<div class="logo">
				<img src="img/logo1.jpg"/>
				<h2>ImageApp</h2>
			</div>
			</br>
			<span style="font-family:'Salsa';font-style:italic;color:white;font-size:15px;"><?php echo "Welcome $user";?></span>
			</br></br>
			<a href="welcome.php">Home</a>
			</br></br>
			<a href="gallery.php">View Gallery</a>
			</br></br>
			<a href="upload.php">Upload Images</a>
			</br></br>
			<?php
			if($user == "controller")
			{
			?>
			<a href="admin.php">Administration</a>
			</br></br>
			<?php
			}
			?>
			<a href="logout.php">Logout</a>
			</br></br>
			<div style="left:10px;position:fixed;bottom:0;color:white;font-family:'Salsa';font-style:cursive;font-size:12px;min-height:30px;">
			 Copyright 2016, ImageApp, Inc.
		   </div>
		</div>
		<div style="min-height:300px;padding-left:20%;padding-right:20%;padding-top:5%">
		<?php
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
		if ($user == 'controller') {
		if (isset($_POST["restoreData"]))
		{
			try{
				$result = $s3->getObject([
				'Bucket' => $my_bucket, // REQUIRED
				'Key' => 'database_backup.sql',
				'SaveAs' => $bkppath
				]);}
			catch (S3Exception $e) {
				// Catch an S3 specific exception.
						echo $e->getMessage();
				exit();
			}catch (AwsException $e) {
				// This catches the more generic AwsException. You can grab information
				// from the exception using methods of the exception object.
				echo $e->getAwsRequestId() . "\n";
				echo $e->getAwsErrorType() . "\n";
				echo $e->getAwsErrorCode() . "\n";
				exit();
			}
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
					echo '<span style="font-family:Salsa;font-style:italic;color:blue;font-size:15px;">Data is restored successfully!!!</span></br>';
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
			echo '<span style="font-family:Salsa;font-style:italic;color:blue;font-size:15px;">DB backup file: ' . $url . '</span></br>';
		}
		if (isset($_POST["save"])){
			$mode = $_POST["opt"];
			if ($result = mysqli_query($link, "UPDATE CONFIG SET val='$mode' where ID = 1")) {
                             if ($mode == 'N'){
                                        echo '<span style="font-family:Salsa;font-style:italic;color:blue;font-size:15px
;">Upload functionality is enabled...</span></br>';}
                                else{
                                        echo '<span style="font-family:Salsa;font-style:italic;color:blue;font-size:15px
;">Upload functionality is disabled...</span></br>';}					
				mysqli_free_result($result);
			}
			else {
					printf("Error in execution : %s.\n", mysqli_error($link));
			}
		}
		}
		else {
			echo '<span style="font-family:Salsa;font-style:italic;color:red;font-size:15px;">You do not have permission!!!</span></br>';
		}		
		mysqli_close($link);
		?>
		</div>
                <div id="d01">
                        <form name="admin"  method="post" action="">
                                        </br></br>
                                        <span style="font-family:'Salsa';font-style:cursive;color:black;font-size:15px;"
>Restore DB: </span></br>
                                        <input type="submit"  id= "restoreData" name="restoreData" value="Submit" />
                                        </br></br>
                                        <span style="font-family:'Salsa';font-style:cursive;color:black;font-size:15px;"
>Store DB backup:</span></br>
                                        <input type="submit"  id= "storeBkp" name="storeBkp" value="Save" />
                                        </br></br>
                                        <span style="font-family:'Salsa';font-style:cursive;color:black;font-size:15px;"
>Enable read-only mode:</span>
                                        </br>
                                        <input type="radio" name="opt" value="N" checked> <span style="font-family:Salsa
;color:black;font-size:12px;">No</span><br>
                                        <input type="radio" name="opt" value="Y"> <span style="font-family:Salsa;color:b
lack;font-size:12px;">Yes</span>
                                        </br>
                                        <input type="submit"  id= "save" name="save" value="Save" />
                                        </br></br>
                        </form>
                </div>
	</body>
</html>
