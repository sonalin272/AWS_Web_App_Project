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
    if (!$link) {die('Could not connect: ' . mysqli_connect_error());}
    else
    {
		/*Select result*/
		$query = "SELECT * FROM RECORDS where email = '$user'";
		if ($result = mysqli_query($link, $query))
		{
			if ($result->num_rows > 0){
			while ($row = $result->fetch_assoc())
			{
			echo '<a href="' . $row['s3_raw_url'] . '" data-gallery ><img src="' . $row['s3_raw_url'] . '" width="100" height="100"></a>';
			echo '<a href="' . $row['s3_finished_url'] . '" data-gallery ><img src="' . $row['s3_finished_url'] . '" width="100" height="100"></a>';
			}
			}
			else {
			printf("No images to display!!!");
			}
			mysqli_free_result($result);
		}
		else {
			printf("Error in execution : %s.\n", mysqli_error($link));
		}
	$link->close();
    }
?>
