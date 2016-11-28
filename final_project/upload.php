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
    $query = "SELECT * FROM CONFIG";
    if ($result = mysqli_query($link, $query))
    {
         $row = $result->fetch_assoc();
 	 $mode=$row['mode'];
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
                <link rel="stylesheet" type="text/css" href="main.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	</head>
<body style="top:0;bottom:0;margin:0;padding:0;height:100%;">
        <div class="right" style="top:0;bottom:0;right:0;margin-left:300px;height:100%;padding-left:16px;border: 2px solid #A9A9A9;background-color: pink;">

                <h1 style="color:purple;font-size:300%;font-family:verdana;text-align:center;">ImageApp</h1>
		<form enctype="multipart/form-data" id="upload" action="uploader.php" method="POST">
		<!-- MAX_FILE_SIZE must precede the file input field -->
		<input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
		<!-- Name of the file -->
		Select file: <input name="file" type="file" /> </br></br>
		<?php if ($mode == 'N') {?>
		<input type="submit" id="buttonFile" name="buttonFile" value="Upload"/>
		<?php } else{
		echo "Upload functinality is disabled...";} ?>
		</form>
        </div>
        <div class="left" style="position: fixed;top: 0;bottom: 0;left:0;height: 100%;width: 300px;padding-top:50px;border: 2px solid #A9A9A9;background: url('htc-sense-htc-background-textures.jpg') pink;">
 		<span style="color:white;font-size:20px;"><?php echo "Welcome $user";?></span>
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
                <?php
                }
                ?>
                </br></br>
                <a href="upload.php">Logout</a>
                </br></br>   		
	</div>
</body>
</html>
