<html>
<head><title>Story</title><link rel="stylesheet" type="text/css" href="css/screen.css" />
</head>
<body>
<div id="wrap">
<div id="content-outer" class="clear"><div id="content-wrap" >	
		<div class="content">
			<div class="post">

<?php
//news_parser_connect.php includes the following line with appropriate values for
//the news parser mysql database:
// $sqlserver=mysql_connect(HOST,USER,PASSWORD);
// mysql_select_db('newsy',$sqlserver);
include("news_parser_connect.php");


$id = mysql_real_escape_string($_GET['id']);

//display full story
$query = "select * from stories where id=$id";
$result = mysql_query($query,$sqlserver);
$row = mysql_fetch_row($result, MYSQL_ASSOC);
$subject = $row['title'];
$link = $row['link'];
$addDate = $row['date'];
$story_text = $row['story'];

echo "<h3>$subject</h4>";
echo "<h5> story posted on $addDate</br></h5>";
echo "<h4><a href='$link' text='link'>link to article</a></h4>";
//echo "<p>".nl2br($story_text)."<br>";
echo $story_text;

echo '</br>';

echo '<br><h4><a href="./index.php" title="main">return to the main page</a></br>';
?>

</div>
</div>
</div>
</div>
<br><br><br>
</body>
</html>