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
if(isset($_POST['key'])){
  $key = $_POST['key'];
  
  
  $query = "select * from events where ekey='$key'";
  $result = mysql_query($query,$sqlserver);
  $row = mysql_fetch_row($result, MYSQL_ASSOC);
  
  $x = $row["ekey"];
  echo $x;
  
  $user = $row['user'];
  echo $user;
  
  
if($username==$user){
	$query1 = "update events set title='', description='' where ekey='$key'";
	mysql_query($query1,$sqlserver);
	echo "ok";
	
}else{
	echo 'restricted access';
}
}
?>