<?php
/*Connect to database*/
$link = mysqli_connect('snimbalk-db.c6ulbi9cmpdg.us-west-2.rds.amazonaws.com:3306', 'root', 'Goodluck16','school');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
echo 'Connected successfully....';

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


/* Prepare an insert statement */
$query = "INSERT INTO students (ID,Name,Age) VALUES (NULL,?,?)";
$stmt = mysqli_prepare($link, $query);

mysqli_stmt_bind_param($stmt, "si", $val1, $val2);

$val1 = 'Sonali Nimbalkar';
$val2 = 26;

/* Execute the statement */
mysqli_stmt_execute($stmt);
printf("%d Row inserted.\n", $stmt->affected_rows);

$val1 = 'Mandar Kakade';
$val2 = 28;

/* Execute the statement */
mysqli_stmt_execute($stmt);
printf("%d Row inserted.\n", $stmt->affected_rows);
$val1 = 'Archana Joshi';
$val2 = 22;

/* Execute the statement */
mysqli_stmt_execute($stmt);
printf("%d Row inserted.\n", $stmt->affected_rows);

$val1 = 'Yogini Korde';
$val2 = 21;

/* Execute the statement */
mysqli_stmt_execute($stmt);
printf("%d Row inserted.\n", $stmt->affected_rows);

$val1 = 'Gauri Joshi';
$val2 = 25;

/* Execute the statement */
mysqli_stmt_execute($stmt);
printf("%d Row inserted.\n", $stmt->affected_rows);

/* close statement */
mysqli_stmt_close($stmt);

mysqli_close($link);
?>
