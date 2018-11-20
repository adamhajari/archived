<?php
session_start();
//calendar_connect.php includes the following line with appropriate values for
//the calendar mysql database:
// $sqlserver=mysql_connect(HOST,USER,PASSWORD);
// mysql_select_db(DATABASE,$sqlserver);
include("foodsy_connect.php");

if(isset($_SESSION["username"])){
	$username = $_SESSION["username"];
	$verified = 1;
}else{
	$verified = 0;
}

if($verified){
  $item = mysql_real_escape_string($_POST['item']);
  $quant = mysql_real_escape_string($_POST['quant']);
  $units = mysql_real_escape_string($_POST['units']);
  
  
  $query2 = "insert into Inventory (user,food_item, quantity, units) 
		values ('$username', '$item', $quant, '$units')";
		
/*
  $query1 = 'select * from events';
  $result = mysql_query($query1, $sqlserver);
  $new_key = mysql_num_rows($result)+1;
  
  $query2 = "insert into events values ( 
  	'$new_key', '$username', '$day', 
	'$month', '$year', '$title',
	'$time', '$description', '$public'
	)";
*/
  mysql_query($query2, $sqlserver);

  echo $query2;
}else{
  echo 'Event was not added </br>';
}


?>