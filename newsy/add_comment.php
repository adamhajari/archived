<html>
<head><title>Login Page</title></head>
<body>



<?php
session_start();
//newsy_connect.php includes the following line with appropriate values for
//the newsy mysql database:
// $sqlserver=mysql_connect(HOST,USER,PASSWORD);
// mysql_select_db(DATABASE,$sqlserver);
include("newsy_connect.php");

$story_key = mysql_real_escape_string($_GET['story_key']);
echo '
<form action= "add_comment.php?story_key='.$story_key.'" method="post">
    <label for="subject">subject: </label>
    <input type="text" size="50" maxlength="200" name="subject" id="subject"></br>
	Comment:</br>
	<textarea rows="10" cols = "50" wrap="physical" name="text"></textarea>
    <input type="submit" value="post">
</form>
';


if(isset($_SESSION["username"])){
	$username = $_SESSION["username"];
}else{
	Header("Location:./login.php");
}
$story_key = mysql_real_escape_string($_GET['story_key']);
if(isset($_POST['subject']) && isset($_POST['text'])){
$subject = mysql_real_escape_string($_POST['subject']);
$text = mysql_real_escape_string($_POST['text']);




$query1 = 'select * from comments';
$result = mysql_query($query1, $sqlserver);
$new_key_num = mysql_num_rows($result)+1;


$query2 = "insert into comments values ( 
	{$new_key_num},  
	{$story_key}, 
	'{$username}',  
	'{$subject}',  
	'{$text}')";
	
$query3 = "update stories set num_comments=num_comments+1 where story_key='{$story_key}'";
mysql_query($query3,$sqlserver);

mysql_query($query2, $sqlserver);

echo '</br>comment successfully posted </br>
		<a href="./index.php" title="main">return to the main page</a></br>';
		Header("Location:./index.php");
}


?>

</body>
</html>