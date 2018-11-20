<html>
<head></head>
<body>

<?php
session_start();
//calendar_connect.php includes the following line with appropriate values for
//the calendar mysql database:
// $sqlserver=mysql_connect(HOST,USER,PASSWORD);
// mysql_select_db(DATABASE,$sqlserver);
include("calendar_connect.php");

if(isset($_SESSION["username"])){
	$username = $_SESSION["username"];
}else{
	//Header("Location:./login.php");
}

if(isset($_POST['title'])){
  $title = mysql_real_escape_string($_POST['title']);
  $day = mysql_real_escape_string($_POST['day']);
  $month = mysql_real_escape_string($_POST['month']);
  $year = mysql_real_escape_string($_POST['year']);
  $hour = mysql_real_escape_string($_POST['hour']);
  $minute = mysql_real_escape_string($_POST['minute']);
  $description = mysql_real_escape_string($_POST['desc']);
  $public = mysql_real_escape_string($_POST['pub']);
  
  $time = "$hour:$minute";

  if($public=='true'){$public = 1;}
  else{$public = 0;}
  
  
  $query1 = 'select * from events';
  $result = mysql_query($query1, $sqlserver);
  $new_key = mysql_num_rows($result)+1;
  
  $query2 = "insert into events values ( 
  	'$new_key', '$username', '$day', 
	'$month', '$year', '$title',
	'$time', '$description', '$public'
	)";

  mysql_query($query2, $sqlserver);

  echo $query2;
}else{
  echo 'Event was not added </br>';
}


?>

</body>
</html>