<?php
session_start();
$user=$_SESSION['username'];
?>
<html>
<head>
<title>Image Processing Application</title>
</head>
<body>
<?php 
echo "Welcome $user";
?>
<div style="min-height:300px;padding-left:20%;padding-right:20%;padding-top:5%">
         </br></br>
         <a href="gallery.php" style="color:Teal;font-family:'Salsa';font-style:cursive;font-size:120%;font-weight:bold;">View Gallery</a>
         </br></br>
         <a href="upload.php" style="color:Teal;font-family:'Salsa';font-style:cursive;font-size:120%;font-weight:bold;">Upload Images</a>
         </br></br>
         <?php
if($user == "controller")
{
?>
<a href="admin.php" style="color:Teal;font-family:'Salsa';font-style:cursive;font-size:120%;font-weight:bold;"
>Administration</a>
<?php
}
?>
      </div>
   </body>
</html>
