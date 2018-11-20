<?php
session_start();
//calendar_connect.php includes the following line with appropriate values for
//the calendar mysql database:
// $sqlserver=mysql_connect(HOST,USER,PASSWORD);
// mysql_select_db(DATABASE,$sqlserver);
include("calendar_connect.php");

if(isset($_SESSION["username"])){
	$username = $_SESSION["username"];

$query_text = mysql_real_escape_string($_POST['query']);

$result = mysql_query('SELECT * FROM events', $sqlserver);
$row = mysql_fetch_row($result, MYSQL_ASSOC);
$num_rows = mysql_num_rows($result);
$counter = 0;

for ($i = 1; $i <= $num_rows; $i++) {


$query = "select * from events where ekey=$i";
$result = mysql_query($query,$sqlserver);
$row = mysql_fetch_row($result, MYSQL_ASSOC);

$user = $row['user'];
$public = $row['public'];
$title = $row['title'];
$description = $row['description'];
$all_text = "$title $description";

if (preg_match("/{$query_text}/i", $all_text) && ($username==$user || $public)) {
$day = $row['day'];
$month = $row['month'];
$year = $row['year'];
$time = $row['time'];
$title = $row['title'];
$description = $row['description'];
echo "
  $month-$day-$year <br>
  $time <br>
  $title <br>
  $description <br>
  <hr/>
  ";
$counter++;
}
}
echo("Found $counter results.");

}else{
	echo "you must be logged in to search";
}

?>