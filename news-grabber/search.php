<html>
<head>
<title>News Parser</title>
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

echo "<h3>Search Results</h3>";

$query_text = mysql_real_escape_string($_POST['query']);

$result = mysql_query('SELECT * FROM stories', $sqlserver);
$row = mysql_fetch_row($result, MYSQL_ASSOC);
$num_rows = mysql_num_rows($result);
$counter = 0;
while($row = mysql_fetch_row($result, MYSQL_ASSOC )){

	$subject = $row['title'];
	$text = $row['story'];
	$summary = $row['description'];
	$all_text = "$summary $subject $text";

	if (preg_match("/{$query_text}/i", $all_text)) {
		$id = $row['id'];
		$link = $row['link'];
		$addDate = $row['date'];

		echo "<hr /><h3><b><a href='./full_story.php?id={$id}'>{$subject}</a></b></h3>";
		echo "
		<h5> story posted on {$addDate}</br></h5>";
		echo '<h4><a href="' . $link . '" text="link">link to article</a></h4>';

		echo "<p>".nl2br($summary)."</p>";
		echo '</font></br>';
		$counter++;
	}
}
echo("Found $counter results.");

?>

</div>
</div>
</div>
</div>
<br><br><br>
</body>
</html>