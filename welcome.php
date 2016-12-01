<?php
session_start();
$user=$_SESSION['username'];
?>
<!DOCTYPE html>
<html style="height:100%;bottom:0;">
	<head>
		<title>Image Processing Application</title>
		<link href='http://fonts.googleapis.com/css?family=Salsa' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" type="text/css" href="css/demo.css">
		<meta charset="utf-8">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<!--script src="js/welcome.js"></script-->
                <script>
                        $(document).ready(function () {
                        var imageFile = ["img/img10.jpg", "img/img6.jpg", "img/img7.jpg","img/img8.jpg",
"img/img9.jpg"];
                        var currentIndex = 0;
                        setInterval(function () {
                                if (currentIndex == imageFile.length) {
                                        currentIndex = 0;
                                }
                                $(".right").css('background-image', 'url("' + imageFile[currentIndex++] + '")');
                                }, 5000);
                        });
                </script>
	</head>
	<body style="top:0;bottom:0;margin:0;line-height: 1.4em;padding:0;height:100%;width:100%;background-image:url(img/backgrnd1.jpg);background-size: cover;">
		<div class="right" style="top:0;bottom:0;right:0;left:300px;height:100%;border: 2px solid #A9A9A9;background-size: cover;">
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
