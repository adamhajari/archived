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
if(isset($_POST['day']) && isset($_POST['month']) && isset($_POST['year']) && isset($_SESSION["username"])){
  $day = mysql_real_escape_string($_POST['day']);
  $month = mysql_real_escape_string($_POST['month']);
  $year = mysql_real_escape_string($_POST['year']);

	
//select events from selected day
$query = "select * from events where day='$day' AND 
	month='$month' AND year='$year' order by time";
$result = mysql_query($query,$sqlserver);
while($row = mysql_fetch_row($result, MYSQL_ASSOC)){
	$key = $row['ekey'];
	$user = $row['user'];
	$title = $row['title'];
	$time = $row['time'];
	$description = $row['description'];
	$public = $row['public'];
	
	if(($public || $user == $username) && $title != '' ){
	  echo "
	  <div id='$key'>
		<b>$title </b><br>
		$time <br>
		<p>".nl2br($description)."<br>
		<font size='2'>added by $user </font> </p>
		<input type='hidden' id='key' name='key' value='$key' /> 
		<input type='button' value='delete' onclick=deletex($key) /> <br>
		<hr/>
	  </div>
	  ";
	}
}
}
?>