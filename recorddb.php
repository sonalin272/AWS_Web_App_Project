<?php
/*Connect to database*/
$link = mysqli_connect('snimbalk-db.c6ulbi9cmpdg.us-west-2.rds.amazonaws.com:3306', 'root', 'Goodluck16','school');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
echo 'Connected successfully....';
/*
$sql = "CREATE TABLE IF NOT EXISTS students
(
ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
Name VARCHAR(255) NOT NULL,
Age INT(3) NOT NULL)";

$create_table = mysqli_query($link,$sql);
echo $create_table;
if ($create_table) {
       echo "Table is created.... ";
}
else {
        echo "Error in table creation... ";
}


/* Prepare an insert statement
/*$query = "INSERT INTO students (ID,Name,Age) VALUES (NULL,?,?)";
$stmt = mysqli_prepare($link, $query);

mysqli_stmt_bind_param($stmt, "si", $val1, $val2);

$val1 = 'Sonali Nimbalkar';
$val2 = 26;

/* Execute the statement 
mysqli_stmt_execute($stmt);
printf("%d Row inserted.\n", $stmt->affected_rows);

$val1 = 'Mandar Kakade';
$val2 = 28;

/* Execute the statement 
mysqli_stmt_execute($stmt);
printf("%d Row inserted.\n", $stmt->affected_rows);
$val1 = 'Archana Joshi';
$val2 = 22;

/* Execute the statement 
mysqli_stmt_execute($stmt);
printf("%d Row inserted.\n", $stmt->affected_rows);

$val1 = 'Yogini Korde';
$val2 = 21;

/* Execute the statement
mysqli_stmt_execute($stmt);
printf("%d Row inserted.\n", $stmt->affected_rows);

$val1 = 'Gauri Joshi';
$val2 = 25;

/* Execute the statement 
mysqli_stmt_execute($stmt);
printf("%d Row inserted.\n", $stmt->affected_rows);
*/
$sql = "CREATE TABLE IF NOT EXISTS records
(
ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
email VARCHAR(100) NOT NULL,
phone varchar(100) not null,
s3_raw_url varchar(100),
s3_finished_url varchar(100),
status INT(3) NOT NULL,
receipt varchar(256))";

$create_table = mysqli_query($link,$sql);
echo $create_table;
if ($create_table) {
       echo "Table is created.... ";
}
else {
        echo "Error in table creation... ";
}


/* Prepare an insert statement */
$query = "INSERT INTO records (ID,email,phone,s3_raw_url,s3_finished_url,status,receipt) VALUES (NULL,?,?,?,?,?,?)";
if(!($stmt = mysqli_prepare($link, $query)))
{echo "Prepare failed: ";
}

mysqli_stmt_bind_param($stmt, "ssssis", $val1,$val2,$val3,$val4,$val5,$val6);
$val1 = 'snimbalk';
$val2 = '1-354-456-533';
$val3 = 'https://s3-us-west-2.amazonaws.com/raw-smn/IMG_20161016_180244.jpg';
$val4 = 'https://s3-us-west-2.amazonaws.com/finished-smn/20161030_195034.jpg';
$val5 = 1;
$val6 = 'ngjf446ff';
// Execute the statement
mysqli_stmt_execute($stmt);
printf("%d Row inserted.\n", $stmt->affected_rows);
mysqli_stmt_bind_param($stmt, "ssssis", $val1,$val2,$val3,$val4,$val5,$val6);
$val1 = 'snimbalk';
$val2 = '1-354-456-533';
$val3 = 'https://s3-us-west-2.amazonaws.com/raw-smn/switchonarex.png';
$val4 = 'https://s3-us-west-2.amazonaws.com/finished-smn/20161031_110229.jpg';
$val5 = 1;
$val6 = 'ngjf4ff5ff';
// Execute the statement
mysqli_stmt_execute($stmt);
printf("%d Row inserted.\n", $stmt->affected_rows);

// close statement 
mysqli_stmt_close($stmt);

mysqli_close($link);
?>
