<html>
<head><title>Post Story</title></head>
<body>

<?php
session_start();
//newsy_connect.php includes the following line with appropriate values for
//the newsy mysql database:
// $sqlserver=mysql_connect(HOST,USER,PASSWORD);
// mysql_select_db(DATABASE,$sqlserver);
include("newsy_connect.php");

if(isset($_SESSION["username"])){
	$username = $_SESSION["username"];
}else{
	Header("Location:./login.php");
}

if(isset($_POST['subject']) && isset($_POST['text'])){
$subject = mysql_real_escape_string($_POST['subject']);
$link = mysql_real_escape_string($_POST['link']);
$text = mysql_real_escape_string($_POST['text']);


$query1 = 'select * from stories';
//echo $query;
$result = mysql_query($query1, $sqlserver);
$new_id = mysql_num_rows($result)+1;

$query2 = "insert into stories values ( 
	{$new_id},  
	'{$username}',  
	'{$subject}', 
	'{$link}', 
	'{$text}', 
	'',
	now(),
	0
	)";

mysql_query($query2, $sqlserver);

echo 'Story successfully posted </br>
		<a href="./index.php" title="main">return to the main page</a></br>';
		Header("Location:./index.php");
}else{
echo 'Story was not posted </br>
		<a href="./index.php" title="main">return to the main page</a></br>';
}


?>

</body>
</html>