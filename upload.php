<?php
session_start();
?>
<html>
<body>
<h3 style="padding:2px;width:100%;background-color:OldLace ;color:Purple ;text-align:center;">
   <center> Upload your image to Imagica Application </center>
</h3>
<?php
echo $_SESSION['username'];

?>
<div><br/></div>
<div style="min-height:300px;padding-left:20%;padding-right:20%;padding-top:5%">
   <form enctype="multipart/form-data" id="upload" action="uploader.php" method="POST">
      <!-- MAX_FILE_SIZE must precede the file input field -->
      <input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
      <!-- Name of the file -->
      Select file: <input name="file" type="file" /> </br></br>		
      <input type="submit" id="buttonFile" name="buttonFile" value="Upload"/>
   </form>
</div>
</body>
</html>
