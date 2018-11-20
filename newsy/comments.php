<html>
<head><title>Story</title><link rel="stylesheet" type="text/css" href="css/screen.css" />
</head>
<body>
<div id="wrap">
<div id="content-outer" class="clear"><div id="content-wrap" >	
		<div class="content">
			<div class="post">

<?php
session_start();

if(isset($_SESSION["username"])){
	$username = $_SESSION["username"];
	$admin = $_SESSION['admin'];
}else{
	//Header("Location:./login.php");
	$username = '';
	$admin = 0;
	echo "<a href='./login.php' title='login'>login</a> to leave a comment</br></br>";
}
//newsy_connect.php includes the following line with appropriate values for
//the newsy mysql database:
// $sqlserver=mysql_connect(HOST,USER,PASSWORD);
// mysql_select_db(DATABASE,$sqlserver);
include("newsy_connect.php");

$story_key = mysql_real_escape_string($_GET['story_key']);

$result = mysql_query('select * from comments',$sqlserver);
$num_comments = mysql_num_rows($result);

//display full story
$query = "select * from stories where story_key={$story_key}";
$result = mysql_query($query,$sqlserver);
$row = mysql_fetch_row($result, MYSQL_ASSOC);
$user = $row['username'];
$subject = $row['story_subject'];
$link = $row['story_link'];
$text = $row['story_text'];
$user = $row['username'];
echo "<h3>$subject</h4>";
echo "<h4><a href='$link' text='link'>link to article</a></h4>";
echo "<p>".nl2br($text)."<br>";
echo "posted by $user </p>";
if($username!=''){
	echo "<h4><a href='./add_comment.php?story_key={$story_key}'>leave a new comment</a></h4>";
}
if($user==$username){
	echo "<h4><a href='./edit_story.php?story_key={$story_key}'>edit story</a></h4>";
}
echo '</br>';

//display comments
$query = "select * from comments where story_key='{$story_key}'";
$result = mysql_query($query,$sqlserver);
$are_comments = 0;
while($row = mysql_fetch_row($result, MYSQL_ASSOC)){
	
	//$query = "select * from stories where story_key={$num_stories}";
	//$result = mysql_query($query,$sqlserver);
	//$row = mysql_fetch_row($result, MYSQL_ASSOC);
	
	
	
	$ckey = $row['comment_key'];
	//$skey = $row['story_key'];
	$user = $row['username'];
	$subject = $row['comment_subject'];
	$text = $row['comment_text'];
	
	if($subject!='' || $text!=''){
		echo "<hr /><h4><b>$subject</b></h4>";
		echo "<p>".nl2br($text)."<br>";
		echo "posted by $user </p>";
		if($user==$username || $admin==1){
			echo "<a href='./delete_comment.php?ckey={$ckey}'>delete comment</a></br>";
		}
		echo '</br>';
		$are_comments = 1;
	}
}

if(!$are_comments){
	echo '<h4>No comments for this story</br>';
}

echo '<br><h4><a href="./index.php" title="main">return to the main page</a></br>';
?>

</div>
</div>
</div>
</div>
<br><br><br>
</body>
</html>