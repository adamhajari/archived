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
//news_parser_connect.php includes the following line with appropriate values for
//the news parser mysql database:
// $sqlserver=mysql_connect(HOST,USER,PASSWORD);
// mysql_select_db('newsy',$sqlserver);
include("news_parser_connect.php");

echo "<h2>Welcome to News Parser</h2>";


$total_count = mysql_real_escape_string($_GET['total_count']);
$page_count = 0;
$stories_per_page = 10;
$result = mysql_query('select * from stories order by id desc',$sqlserver);
$num_stories = mysql_num_rows($result);

$i = 0;

while($total_count < $num_stories && $page_count<$stories_per_page && $row = mysql_fetch_row($result, MYSQL_ASSOC )){
	
	if($i >= $total_count){
		$id = $row['id'];
		$subject = $row['title'];
		$link = $row['link'];
		$story_text = $row['story'];
		$summary = $row['description'];
		$addDate = $row['date'];
		

		echo "<hr /><h3><b><a href='./full_story.php?id={$id}'>{$subject}</a></b></h3>";
		echo "
		<h5> story posted on {$addDate}</br></h5>";
		echo '<h4><a href="' . $link . '" text="link">link to article</a></h4>';

		echo "<p>".nl2br($summary)."</p>";
		echo '</font></br>';
		
		$page_count++;
		$total_count++;
	}
		
	$i++;
		
}
		
echo "<hr/>";
if ($page_count==$stories_per_page && $num_stories > 0){
	echo "</br><h4><a href='./next_page.php?num_stories={$num_stories}'>next $stories_per_page stories</a></h4>";
}
echo '
</br>
<form action= "search.php" method="post">
<label for="query">search: </label>
<input type="text" size="50" maxlength="200" name="query" id="query">
<input type="submit" value="search">
</form>
';

?>
</div>
</div>
</div>
</div>
<br><br><br>
</body>
</html>