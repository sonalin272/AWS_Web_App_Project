<?php
session_start();
require 'vendor/autoload.php';
include('profile.php');
$user=$_SESSION['username'];
//Create client to connect to RDS
$rds_client = new Aws\Rds\RdsClient([
        'version' => 'latest',
        'region'  => 'us-west-2']);
$endpoint = "";
$rds_result = $rds_client->describeDBInstances( array('DBInstanceIdentifier' => $db_identifier,));
$endpoint=$rds_result['DBInstances'][0]['Endpoint']['Address'];
//Connect to database
$link = mysqli_connect($endpoint,$db_user,$db_password,$db_name) or die("Error " . mysqli_connect_error());
$query = "SELECT * FROM CONFIG where ID = 1";
if ($result = mysqli_query($link, $query))
{
	$row = $result->fetch_assoc();
 	$mode=$row['val'];
        mysqli_free_result($result);
}
else {
        printf("Error in execution : %s.\n", mysqli_error($link));
}
?>
<!DOCTYPE html>
<html style="height:100%;bottom:0;">
        <head>
                <title>Image Processing Application</title>
                <link rel="stylesheet" type="text/css" href="css/demo.css">
		<link href='http://fonts.googleapis.com/css?family=Salsa' rel='stylesheet' type='text/css'>
		<meta charset="utf-8">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	</head>
	<body id="b01">
        	<div id="d01" class="right">
			<form enctype="multipart/form-data" id="upload" action="uploader.php" method="POST">
				<!-- MAX_FILE_SIZE must precede the file input field -->
				<input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
				<!-- Name of the file -->
				<span style="font-family:Salsa;font-style:cursive;color:black;font-size:15px;">Select file:</span><input name="file" type="file" /> </br></br>
				<?php if ($mode == 'N') {?>
				<input type="submit" id="buttonFile" name="buttonFile" value="Upload"/>
				<?php } else{
				echo '<span style="font-family:Salsa;font-style:italic;color:red;font-size:15px;">Upload functinality is unavailable...</span>';
				}?>
			</form>
        	</div>
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
	</body>
</html>
