<?php
include('login.php'); // Includes Login Script
?>
<html>
	<head>
		<link href='http://fonts.googleapis.com/css?family=Salsa' rel='stylesheet' type='text/css'>
		<meta charset="utf-8">
		<title>Image Processing Application</title>
		<style>	
			body{
				background: url("./img/htc-sense-htc-background-textures.jpg") pink;
			}
			input[type=text], input[type=password] {
				width: 100%;
				font-size: 14px;
				padding: 12px 20px;
				margin: 8px 0;
				display: inline-block;
				border: 2px solid #555555;
				box-sizing: border-box;
				box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
			}
			button {
				background-color: #1a1a1a;
				color: white;
				border-radius: 8px;
				font-size: 18px;
				padding: 14px 20px;
				margin: 8px 0;
				border: 2px solid #555555;
				box-sizing: border-box;
				box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
				cursor: pointer;
				width: 100%;
			}
			.imgcontainer {
				text-align: center;
				margin:0 auto;
			}
			img.avatar {
				width: 10%;
				border-radius: 20%;
			}
			.container {
				width:400px;
				margin:0 auto;
			}
		</style>
	</head>
	<body>
		<h2 style="font-family:'Salsa';font-style:cursive;font-weight:bold;color:black;text-shadow: 2px 2px 4px #000000;font-size:230%;text-align:center;">ImageApp Login</h2>
		<form name="login" action="" method="post">
			<div class="imgcontainer">
				<img src="./img/avatar.png" alt="Avatar" class="avatar">
			</div>
			<div class="container">
				<label style="color:black;font-size:15px;font-family:Helvetica, sans-serif;"><b>Username</b></label>
				<input type="text" placeholder="Enter Username" name="username" required>
				<label style="color:black;font-size:15px;font-family:Helvetica, sans-serif;"><b>Password</b></label>
				<input type="password" placeholder="Enter Password" name="password" required>
				<button type="submit"  id= "buttonLogin" name="buttonLogin">Login </button>
				<span style="color:white;font-size:20px;font-weight:bold"><?php echo $error; ?></span>
			</div>
		</form>
	</body>
</html>
