<?php
include('login.php'); // Includes Login Script
?>
<html>
<head>
<title>Image Processing Application</title>
</head>
<body>
<div style="min-height:300px;padding-left:20%;padding-right:20%;padding-top:5%">
   <form name="login" action="" method="post">
      <div style="padding-left:20%;padding-right:20%;padding-top:10%;padding-bottom:10%">
                  Username : <input name="username" type="text" /><br /><br />
                  Password : <input name="password" type="password" /><br /><br />
         <input type="submit"  id= "buttonLogin" name="buttonLogin" value="Login" />
<?php echo $error; ?>
<!--input type="submit"  id= "buttonSignUp" name="buttonSignUp" value="Sign Up" /-->
      </div>
   </form>
</div>
</body>
<html>
