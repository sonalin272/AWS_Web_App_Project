<?php
session_start();
require 'vendor/autoload.php';
include('profile.php');
    $user = $_SESSION['username'];
    //Create client to connect to RDS
    $rds_client = new Aws\Rds\RdsClient([
	'version' => 'latest',
	'region'  => 'us-west-2']);
    $endpoint = "";
    $rds_result = $rds_client->describeDBInstances( array('DBInstanceIdentifier' => $db_identifier,));
    $endpoint=$rds_result['DBInstances'][0]['Endpoint']['Address'];
    //Connect to database
    $link = mysqli_connect($endpoint, $db_user, $db_password,$db_name);
?>
<html>
   <head>
      <link href='http://fonts.googleapis.com/css?family=Salsa' rel='stylesheet' type='text/css'>
      <meta charset="utf-8">
      <meta name="description" content="IMAGES GALLERY">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="stylesheet" href="css/blueimp-gallery.css">
      <link rel="stylesheet" href="css/blueimp-gallery-indicator.css">
      <link rel="stylesheet" href="css/demo.css">
      <title>Image Application</title>
   </head>
<body>
    <div class="left">      
<div class="logo">
<img src="../img/logo.jpg"/>
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
<!-- The Gallery as lightbox dialog, should be a child element of the document body -->
<div id="blueimp-gallery" class="blueimp-gallery">
   <div class="slides"></div>
   <h3 class="title"></h3>
   <a class="prev"></a>
   <a class="next"></a>
   <a class="close"></a>
   <a class="play-pause"></a>
   <ol class="indicator"></ol>
</div>
<!-- The Gallery as inline carousel, can be positioned anywhere on the page -->
<div id="blueimp-gallery-carousel" class="blueimp-gallery blueimp-gallery-carousel">
   <div class="slides"></div>
   <h3 class="title"></h3>
   <a class="prev"></a>
   <a class="next"></a>
   <a class="play-pause"></a>
   <ol class="indicator"></ol>
</div>
</br>
<div class="links">
         <div id="links">    
<?php
	if (!$link) {die('Could not connect: ' . mysqli_connect_error());}
    else
    {
		/*Select result*/
		$query = "SELECT * FROM RECORDS where email = '$user'";
		if ($result = mysqli_query($link, $query))
		{
			if ($result->num_rows > 0){
				echo '<h3>Original Images</h3>';
				$result1 =  mysqli_query($link, $query);
				while ($row = $result->fetch_assoc())
				{
				echo '<a href="' . $row['s3_raw_url'] . '" data-gallery ><img style="width:100;height:100;margin:10px 10px 10px 10px;border-style:solid;border-color:black;" src="' . $row['s3_raw_url'] . '"></a>';
				}
				echo '<h3>Processed Images</h3>';
                               while ($row1 = $result1->fetch_assoc())
                                {
                                echo '<a href="' . $row1['s3_finished_url'] . '" data-gallery ><img style="width:100;height:100;margin:10px 10px 10px 10px;border-style:solid;border-color:black;" src="' . $row1['s3_finished_url'] . '"></a>';

                                }
			}
			else {
			      echo '<span style="font-family:Salsa;font-style:italic;color:black;font-size:15px;">No images to display!!!</span>';
			}
		}
		else {
			printf("Error in execution : %s.\n", mysqli_error($link));
		}
	$link->close();
    }
?>
         </div>
      </div></br></br>


<script src="js/blueimp-helper.js"></script>
<script src="js/blueimp-gallery.js"></script>
<script src="js/blueimp-gallery-fullscreen.js"></script>
<script src="js/blueimp-gallery-indicator.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script>
   if (!window.jQuery) {
       document.write(
           '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"><\/script>'
       );
   }
</script>
<script src="js/jquery.blueimp-gallery.js"></script>    
<script src="js/demo.js"></script>
<script>
   blueimp.Gallery(
   document.getElementById('links').getElementsByTagName('a'),
   {
     container: '#blueimp-gallery-carousel',
     carousel: true
   }
   );
</script>
</body>
</html>
