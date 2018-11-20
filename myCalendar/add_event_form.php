<html>
<head></head>
<body>

<?php
session_start();
//calendar_connect.php includes the following line with appropriate values for
//the calendar mysql database:
// $sqlserver=mysql_connect(HOST,USER,PASSWORD);
// mysql_select_db(DATABASE,$sqlserver);
include("calendar_connect.php");

if(isset($_SESSION["username"])){
	$username = $_SESSION["username"];
	echo '
	 <b>add a new event</b><br>
     title:<input id="title" type="text" /><br>
	 description:<br><textarea id="desc" rows="4" cols = "50" wrap="physical" name="text"></textarea><br>
	 hour: <input id="hour" type="text" /> <br>
	 minute: <input id="minute" type="text" /><br>
	 <input type="checkbox" id="pub" /> make public<br />
	 <input type="button" value="add" onclick=addEvent() /> 
	 ';
}else{
	echo "please login to view or add events";
}

?>

</body>
</html>