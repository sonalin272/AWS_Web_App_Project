<?php
require '/home/ubuntu/vendor/autoload.php';
//Create client to connect to RDS
$rds_client = new Aws\Rds\RdsClient([
'version' => 'latest',
'region'  => 'us-west-2']);
$endpoint = "";
$rds_result = $rds_client->describeDBInstances( array('DBInstanceIdentifier' => 'snimbalk-db',));
$endpoint=$rds_result['DBInstances'][0]['Endpoint']['Address'];

//Connect to the database
$link = mysqli_connect($endpoint, 'root', 'goodluck16','dev') or die("Error " . mysqli_error($link));
//Check database connection
if (mysqli_connect_errno()) {
	printf("Connection failed: %s\n", mysqli_connect_error());
	exit();
}
printf("Connected successfully.... \n");

//Create table 'records'
$sql = "CREATE TABLE IF NOT EXISTS RECORDS
(
ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
email VARCHAR(100) NOT NULL,
phone VARCHAR(100) NOT NULL,
filename VARCHAR(100) NOT NULL,
s3_raw_url VARCHAR(100),
s3_finished_url VARCHAR(100),
status INT(3) NOT NULL,
receipt VARCHAR(256))";

$create_table = mysqli_query($link,$sql);
if ($create_table) {
       printf("Table is created.... \n");
}
else {
        printf("Error in table creation... \n");
	exit();
}

/* Prepare an insert statement */
$query = "INSERT INTO RECORDS (ID,email,phone,filename,s3_raw_url,s3_finished_url,status,receipt) VALUES (NULL,?,?,?,?,?,?,?)";
if(!($stmt = mysqli_prepare($link, $query))) {
  echo "Prepare failed: (" . $stmt->errno . ") " . $stmt->error;
}

mysqli_stmt_bind_param($stmt, "sssssis", $val1,$val2,$val3,$val4,$val5,$val6,$val7);
$val1 = 'controller';
$val2 = '1-354-456-533';
$val3 = 'eartrumpet.png';
$val4 = 'https://s3-us-west-2.amazonaws.com/raw-smn/eartrumpet.png';
$val5 = 'https://s3-us-west-2.amazonaws.com/finished-smn/eartrumpet-bw.png';
$val6 = 1;
$val7 = 'ngjf446ff';
// Execute the statement
mysqli_stmt_execute($stmt);
printf("%d Row inserted.\n", $stmt->affected_rows);

$val1 = 'controller';
$val2 = '1-354-456-533';
$val3 = 'Knuth.jpg';
$val4 = 'https://s3-us-west-2.amazonaws.com/raw-smn/Knuth.jpg';
$val5 = 'https://s3-us-west-2.amazonaws.com/finished-smn/Knuth-bw.jpg';
$val6 = 1;
$val7 = 'ngjf4ff45f';
// Execute the statement
mysqli_stmt_execute($stmt);
printf("%d Row inserted.\n", $stmt->affected_rows);

//Create table 'users'
$sql = "CREATE TABLE IF NOT EXISTS USERS
(
ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(100) NOT NULL,
password VARCHAR(100) NOT NULL)";

$create_table = mysqli_query($link,$sql);
if ($create_table) {
       printf("Table is created.... \n");
}
else {
        printf("Error in table creation...\n");
	exit();
}

// Insert records into USERS table
if (!($stmt = $link->prepare("INSERT INTO USERS (ID,username,password) VALUES (NULL,?,?)"))) {
    echo "Prepare failed: (" . $stmt->errno . ") " . $stmt->error;
}
// Prepared statements will not accept literals (pass by reference) in bind_params, you need to declare variables
$stmt->bind_param("ss",$username,$password);

$username='controller';
$password='admin';
mysqli_stmt_execute($stmt);
printf("%d Row inserted.\n", $stmt->affected_rows);
$username='snimbalk@hawk.iit.edu';
$password='sona123';
mysqli_stmt_execute($stmt);
printf("%d Row inserted.\n", $stmt->affected_rows);
$username='hajek@iit.edu';
$password='ilovebunny';
mysqli_stmt_execute($stmt);
printf("%d Row inserted.\n", $stmt->affected_rows);

//Create table 'config'
$sql = "CREATE TABLE IF NOT EXISTS CONFIG
(
ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
val VARCHAR(100) NOT NULL)";

$create_table = mysqli_query($link,$sql);
if ($create_table) {
       printf("Table is created....  \n");
}
else {
        printf("Error in table creation... \n");
	exit();
}

// Insert records into config table
if (!($stmt = $link->prepare("INSERT INTO CONFIG (ID,val) VALUES (NULL,?)"))) {
    echo "Prepare failed: (" . $stmt->errno . ") " . $stmt->error;
}
// Prepared statements will not accept literals (pass by reference) in bind_params, you need to declare variables
$stmt->bind_param("s",$val);
$val='N';
mysqli_stmt_execute($stmt);
printf("%d Row inserted.\n", $stmt->affected_rows);

$val=$argv[1];
mysqli_stmt_execute($stmt);
printf("%d Row inserted.\n", $stmt->affected_rows);

//Close statements
mysqli_stmt_close($stmt);
mysqli_close($link);
?>

