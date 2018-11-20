<?php
session_start();
//newsy_connect.php includes the following line with appropriate values for
//the newsy mysql database:
// $sqlserver=mysql_connect(HOST,USER,PASSWORD);
// mysql_select_db(DATABASE,$sqlserver);
include("newsy_connect.php");

if (isset($_SESSION["username"])) {
$username = $_SESSION["username"];
$admin = $_SESSION['admin'];
echo "Hello {$username}, Welcome to Newsy</br></br>";
} else {
$username = '';
$admin = 0;
echo "Welcome to Newsy,
<a href='./login.php' title='login'>login</a> to post a story or leave a comment</br></br>";
}

$query_text = mysql_real_escape_string($_POST['query']);

$result = mysql_query('SELECT * FROM stories', $sqlserver);
$row = mysql_fetch_row($result, MYSQL_ASSOC);
$num_rows = mysql_num_rows($result);
$counter = 0;
for ($i = 1; $i <= $num_rows; $i++) {


$query = "select * from stories where story_key={$i}";
$result = mysql_query($query,$sqlserver);
$row = mysql_fetch_row($result, MYSQL_ASSOC);
$username = $row['username'];
$subject = $row['story_subject'];
$text = $row['story_text'];
$all_text = "{$username} {$subject} {$text}";


if (preg_match("/{$query_text}/i", $all_text)) {
$key = $row['story_key'];
$user = $row['username'];
$link = $row['story_link'];
$addDate = $row['addDate'];
$num_comments = $row['num_comments'];
echo "<hr /><font size='4'><b><a href='./comments.php?story_key={$key}'>{$subject}</a></b></font></br>";
echo "
<font size='2'> story posted on {$addDate}</font></br>";
echo '<a href="' . $link . '" text="link">link to article</a>';
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
$counter++;
}
}
echo("Found $counter results.");

?>