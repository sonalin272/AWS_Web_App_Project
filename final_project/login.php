<?php
session_start();
require 'vendor/autoload.php';
include('profile.php');
$error=''; // Variable To Store Error Message
if (isset($_POST['buttonLogin'])) 
{
    if (empty($_POST['username']) || empty($_POST['password'])) {
		$error = "Please enter username and password!!!";
	}
    else
    {
		$user = $_POST['username'];
		$pwd = $_POST['password'];
		//Create client to connect to RDS
		$rds_client = new Aws\Rds\RdsClient([
		'version' => 'latest',
		'region'  => 'us-west-2']);
		$endpoint = "";
		$rds_result = $rds_client->describeDBInstances( array('DBInstanceIdentifier' => $db_identifier,));    
		$endpoint=$rds_result['DBInstances'][0]['Endpoint']['Address'];
		//Connect to database
		$link = mysqli_connect($endpoint,$db_user,$db_password,$db_name);
		if (!$link) {die('Could not connect: ' . mysqli_error());}
		else
		{
			//Select result
			$query = "SELECT * FROM USERS where username='$user' and password='$pwd'";
			if ($result = mysqli_query($link, $query))
			{
				$row = mysqli_fetch_row($result);
				$count = mysqli_num_rows($result);
				if ($count == 1)
				{
					$_SESSION["username"] = $user;
					header("Location: welcome.php");
				}
				else
				{
					$error =  "Invalid username or password!!!";

				}
				mysqli_free_result($result);
			}
			$link->close();
		}
    }
}
?>

