<?php
session_start();
if(isset($_SESSION["username"])){
	$username = mysql_real_escape_string($_SESSION["username"]);
	$admin = $_SESSION['admin'];
}else{
	Header("Location:./login.php");
}
$story_key = mysql_real_escape_string($_GET['story_key']);

//newsy_connect.php includes the following line with appropriate values for
//the newsy mysql database:
// $sqlserver=mysql_connect(HOST,USER,PASSWORD);
// mysql_select_db(DATABASE,$sqlserver);
include("newsy_connect.php");

$query = "select * from stories where story_key='{$story_key}'";
$result = mysql_query($query,$sqlserver);
$row = mysql_fetch_row($result, MYSQL_ASSOC);
$user = $row['username'];

if($username=$user || $admin==1){
	$query1 = "update comments set comment_subject='', comment_text='' where story_key='{$story_key}'";
	mysql_query($query1,$sqlserver);

	$query2 = "update stories set story_subject='', story_link='', story_text='' where story_key='{$story_key}'";
	mysql_query($query2,$sqlserver);
	Header("Location:./index.php");
}else{
	echo 'fuck you';
}

?>