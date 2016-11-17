<?php
session_start();
if(isset($_FILES['file']) and isset($_POST['buttonFile'])){
$filename = $_FILES['file'];
$user = $_SESSION['username'];
}
?>
<html>
<head>
</head>
<body>
<?php
//print_r($filename);
echo "filename: " . $filename['name'];
echo "username: " . $user;
?>
</body>
</html>

