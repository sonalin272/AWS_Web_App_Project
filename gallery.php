<?php
session_start();
require 'vendor/autoload.php';
	$user = $_SESSION['username'];
	//echo $user;
    //Create client to connect to RDS
    $rds_client = new Aws\Rds\RdsClient([
	'version' => 'latest',
	'region'  => 'us-west-2']);
    $endpoint = "";
    $rds_result = $rds_client->describeDBInstances( array('DBInstanceIdentifier' => 'snimbalk-db',));
    $endpoint=$rds_result['DBInstances'][0]['Endpoint']['Address'];
    //echo $endpoint;
    /*Connect to database*/
    $link = mysqli_connect($endpoint, 'root', 'Goodluck16','school');
    if (!$link) {die('Could not connect: ' . mysqli_error());}
    else
    {
		//printf ("Connection succeeeded \n");
		/*Select result*/
		$query = "SELECT * FROM records where email='$user'";
		if ($result = mysqli_query($link, $query))
		{
			//$row = mysqli_fetch_row($result);
			//      printf ("%s %s \n", $row[0], $row[1]);
			while ($row = $result->fetch_assoc())
			{
			echo '<a href="' . $row['s3_raw_url'] . '" data-gallery ><img src="' . $row['s3_raw_url'] . '" width="100" height="100"></a>';
			echo '<a href="' . $row['s3_finished_url'] . '" data-gallery ><img src="' . $row['s3_finished_url'] . '" width="100" height="100"></a>';
			}
		mysqli_free_result($result);
		}
	$link->close();
    }
?>
