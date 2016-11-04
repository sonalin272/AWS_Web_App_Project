<html>
    <head>
    <style>
                table {
                        width:50%;
                }
                table, th, td {
                                border: 1px solid black;
                                border-collapse: collapse;
                }
                th, td {
                padding: 5px;
                text-align: left;
                }
                table#t01 tr:nth-child(even) {
                        background-color: #eee;
                }
                table#t01 tr:nth-child(odd) {
                   background-color:#fff;
                }
                table#t01 th {
                        background-color:rgb(90,90,90);
                        color: white;
                }
        </style>
    </head>
    <body style="background-color:powderblue;">
        <h2 style="color:black;font-size:100%;font-family:verdana;text-align:left;">STUDENT INFORMATION</h2>
            <?php
            /*Connect to database*/
            $link = mysqli_connect('snimbalk-db.c6ulbi9cmpdg.us-west-2.rds.amazonaws.com:3306', 'root', 'Goodluck16'
,'school');
            if (!$link) {
                    die('Could not connect: ' . mysql_error());
            }

            /*Select result*/
            $s_query = "SELECT ID,Name,Age FROM students";

			if ($stmt = mysqli_prepare($link, $s_query)) {

			/* execute statement */
			mysqli_stmt_execute($stmt);

			/* bind result variables */
			mysqli_stmt_bind_result($stmt, $id, $name, $age);
			echo '<table id="t01">'; 
			echo "<tr><th>ID</th><th>Name</th><th>Age</th></tr>"; 
			/* fetch values */
			while (mysqli_stmt_fetch($stmt)) {
			  echo "<tr><td>"; 
			  echo $id;
			  echo "</td><td>";   
			  echo $name;
			  echo "</td><td>";    
			  echo $age;
			  echo "</td></tr>";  
			}
			echo "</table>";   

			/* close statement */
			mysqli_stmt_close($stmt);
		}

		mysqli_close($link);
		?>
	</body>
</html>
