<html>
<head><title>Edit Story</title></head>
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
	$admin = $_SESSION['admin'];
}else{
	$admin = 0;
	Header("Location:./login.php");
}
$story_key = mysql_real_escape_string($_GET['story_key']);


$query = "select * from stories where story_key='{$story_key}'";
$result = mysql_query($query,$sqlserver);
$row = mysql_fetch_row($result, MYSQL_ASSOC);
$user = mysql_real_escape_string($row['username']);
$subject = mysql_real_escape_string($row['story_subject']);
$link = mysql_real_escape_string($row['story_link']);
$text = $row['story_text'];

if($username=$user || $admin==1){
	
	if(isset($_POST['subject']) && isset($_POST['text'])){
		$subject = mysql_real_escape_string($_POST['subject']);
		$link = mysql_real_escape_string($_POST['link']);
		$text = mysql_real_escape_string($_POST['text']);
		$query2 = "update stories set story_subject='{$subject}', story_link='{$link}', story_text='{$text}' where story_key='{$story_key}'";
		mysql_query($query2,$sqlserver);
		Header("Location:./index.php");
		echo 'hi';
	}
	
	echo '
	Edit your story:
	<form action= "./edit_story.php?story_key='.$story_key.'" method="post">
		subject:
		<input type="text" size="50" maxlength="200" name="subject" value="'.$subject.'"></br>
		original url:
		<input type="text" size="50" name="link" value="'.$link.'"></br>
		Story text:</br>
		<textarea rows="10" cols = "50" wrap="physical" name="text">'.$text.'</textarea>
		<input type="submit" value="repost">
	</form>
	<a href="./delete_story.php?story_key='.$story_key.'">delete story</a></br>
	<a href="./logout.php">log out</a>
	</body>
	</html> 
	';
	
}else{
	echo 'fuck you';
}

?>

</body>
</html>