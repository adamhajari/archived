<html>
<head><title>Delete Comment</title></head>
<body>

<?php
session_start();
//newsy_connect.php includes the following line with appropriate values for
//the newsy mysql database:
// $sqlserver=mysql_connect(HOST,USER,PASSWORD);
// mysql_select_db(DATABASE,$sqlserver);
include("newsy_connect.php");

if(isset($_SESSION["username"])){
	$username = mysql_real_escape_string($_SESSION["username"]);
	$admin = $_SESSION['admin'];
}else{
	Header("Location:./login.php");
}
$ckey = mysql_real_escape_string($_GET['ckey']);


$query = "select * from comments where comment_key={$ckey}";
$result = mysql_query($query,$sqlserver);
$row = mysql_fetch_row($result, MYSQL_ASSOC);
$user = $row['username'];
$story_key = $row['story_key'];

if($username==$user || $admin==1){
	$query1 = "update comments set comment_subject='', comment_text='' where comment_key='{$ckey}'";
	mysql_query($query1,$sqlserver);
	
	$query3 = "update stories set num_comments=num_comments-1 where story_key='{$story_key}'";
	mysql_query($query3,$sqlserver);
	
	echo "<a href='./comments.php?story_key={$story_key}'>see comments</a></br>";
	Header("Location:./comments.php?story_key={$story_key}");

}else{
	echo 'fuck you';
}


?>

</body>
</html>