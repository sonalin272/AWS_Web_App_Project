<?php
session_start();
$user=$_SESSION['username'];
?>
<!DOCTYPE html>
<html style="height:100%;bottom:0;">
	<head>
		<title>Image Processing Application</title>
		<link rel="stylesheet" type="text/css" href="main.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script>
    $(document).ready(function () {
                var imageFile = ["./img/img1.jpg", "./img/img3.jpg", "./img/img2.jpg", "./img/img4.jpg"];
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
	<body style="top:0;bottom:0;margin:0;padding:0;height:100%;">
		 <div class="right" style="top:0;bottom:0;right:0;margin-left:300px;height:100%;border: 2px solid #A9A9A9;background-size: cover;">

         		<h1 style="color:pink;font-size:300%;font-family:verdana;">ImageApp</h1>
 		</div>
		<div class="left" style="position: fixed;top: 0;bottom: 0;left:0;height: 100%;width: 300px;padding-top:50px;border: 2px solid #A9A9A9;background: url('./img/htc-sense-htc-background-textures.jpg') pink;">

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
				<a href="logout.php">Logout</a>
				</br></br>
		</div>
	</body>
</html>
