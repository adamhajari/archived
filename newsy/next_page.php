<html>
<head>
<title>Newsy</title>
<link rel="stylesheet" type="text/css" href="css/screen.css" />
</head>
<body>
<div id="wrap">
<div id="content-outer" class="clear"><div id="content-wrap" >	
		<div class="content">
			<div class="post">


<?php
session_start();
//newsy_connect.php includes the following line with appropriate values for
//the newsy mysql database:
// $sqlserver=mysql_connect(HOST,USER,PASSWORD);
// mysql_select_db(DATABASE,$sqlserver);
include("newsy_connect.php");

echo "<h2>Welcome to Newsy</h2>";
if(isset($_SESSION["username"])){
	$username = $_SESSION["username"];
	$admin = $_SESSION['admin'];
	echo "<h4>Hello $username</h4>";
}else{
	//Header("Location:./login.php");
	$username = '';
	$admin = 0;
echo "<h4><a href='./login.php' title='login'>login</a> to post a story or leave a comment</h4>";
}



$num_stories = mysql_real_escape_string($_GET['num_stories']);
$result = mysql_query('select * from stories',$sqlserver);
$page_count = 0;
while($num_stories > 0 && $page_count<5){

	$query = "select * from stories where story_key={$num_stories}";
	$result = mysql_query($query,$sqlserver);
	$row = mysql_fetch_row($result, MYSQL_ASSOC);
	
	$key = $row['story_key'];
	$user = $row['username'];
	$subject = $row['story_subject'];
	$link = $row['story_link'];
	$text = $row['story_text'];
	$addDate = $row['addDate'];
	$num_comments = $row['num_comments'];
	
	if($subject!='' && $link!='' && $text!=''){

		echo "<hr /><h3><b><a href='./comments.php?story_key={$key}'>{$subject}</a></b></h3>";
		echo "
		<h5> story posted on {$addDate}</br></h5>";
		echo '<h4><a href="' . $link . '" text="link">link to article</a></h4>';
		$story_summary = substr($text,0,600);
		echo "<p>".nl2br($story_summary).
		"<br><font size='2'>posted by {$user }</font><p>";
		echo "<font size='2'><a href='./comments.php?story_key={$key}'>{$num_comments} comments</a> </br>";
		if($username!=''){
		echo "<a href='./add_comment.php?story_key={$key}'>leave a new comment</a></br>";
		}
		if($user==$username || $admin==1){
		echo "<a href='./edit_story.php?story_key={$key}'>edit story</a></br>";
		}
		echo '</font></br>';
		$page_count = $page_count+1;
		}
		
		
		$num_stories = $num_stories - 1;
		
		}
		echo "<hr/>";
		if ($page_count==5 && $num_stories > 0){
		echo "</br><h4><a href='./next_page.php?num_stories={$num_stories}'>next 5 stories</a></h4>";
		}
	
		echo '
		</br>
		<form action= "search.php" method="post">
		<label for="query">search: </label>
		<input type="text" size="50" maxlength="200" name="query" id="query">
		<input type="submit" value="search">
		</form>
		';
		
		if(isset($_SESSION["username"])){
		echo '
		</br></br>
		<h4>Post a new story:</h4>
		<form action= "add_story.php" method="post">
		<label for="subject">subject:<br> </label>
		<input type="text" size="100" maxlength="200" name="subject" id="subject"></br>
		<label for="link">original url:<br> </label>
		<input type="text" size="100" name="link" id="link"></br>
		Story text:</br>
		<textarea rows="10" cols = "100" wrap="physical" name="text"></textarea>
		<input type="submit" value="post">
		</form>
		<h4><a href="./logout.php">log out</a><h4>
		';
	}

?>
</div>
</div>
</div>
</div>
<br><br><br>
</body>
</html>