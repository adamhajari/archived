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
	echo " ";
}
if(isset($_POST['day']) && isset($_POST['month']) && isset($_POST['year'])){
  $day = mysql_real_escape_string($_POST['day']);
  $month = mysql_real_escape_string($_POST['month']);
  $year = mysql_real_escape_string($_POST['year']);
  echo "$day<br>";

	
//select events from selected day
$query = "select * from events where day='$day' AND 
	month='$month' AND year='$year' order by time";
$result = mysql_query($query,$sqlserver);
if(isset($_SESSION["username"])){
while($row = mysql_fetch_row($result, MYSQL_ASSOC)){
	$key = $row['ekey'];
	$user = $row['user'];
	$title = $row['title'];
	$time = $row['time'];
	$description = $row['description'];
	$public = $row['public'];
	$short_title = substr($title,0,13);
	
	if(($public || $user == $username) && $title != '' ){
	  echo "
	  <div id='$key'>
		<font size='2'>$time - $short_title</font> 
	  </div>
	  ";
	}
}
}
}
?>