<?php
session_start();
$user=$_SESSION['username'];
?>
<html>
	<head>
		<title>Image Processing Application</title>
		<link rel="stylesheet" type="text/css" href="main.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script>
    $(document).ready(function () {
                var imageFile = ["img1.jpg", "img3.jpg", "img2.jpg", "img4.jpg"];
                var currentIndex = 0;
                setInterval(function () {
                    if (currentIndex == imageFile.length) {
                        currentIndex = 0;
                    }
                    $("body").css('background-image', 'url("' + imageFile[currentIndex++] + '")');
                }, 5000);
            });
</script>
</head>
	<body>
		<div class="left">
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
