<?php
session_start();
//calendar_connect.php includes the following line with appropriate values for
//the calendar mysql database:
// $sqlserver=mysql_connect(HOST,USER,PASSWORD);
// mysql_select_db(DATABASE,$sqlserver);
include("foodsy_connect.php");

if(isset($_SESSION["username"])){
	$username = $_SESSION["username"];
}else{
	echo " ";
}
$key = mysql_real_escape_string($_POST['key']);
$query = "select * from Inventory where id='$key'";
$result = mysql_query($query,$sqlserver);
$row = mysql_fetch_row($result, MYSQL_ASSOC);
	$food_item = $row['food_item'];
	$quant = $row['quantity'];
	$unit = $row['units'];

	echo "You have $quant $unit of $food_item <br>";
//*	
	echo "would you like to use some or add more? <br>";
	echo "
	<input id='use_quant' type='text' size=5 />
	<select id='use_unit' name=units>
	<option value='c'>cups</option>
	<option value='oz'>oz</option> 
	<option value='tspn'>tspn</option>
	<option value='tbpn'>tbsp</option>
	<option value='pt'>pt</option>
	<option value='qt'>qt</option>
	<option value='gal'>gal</option>
	<option value='mL'>mL</option>
	<option value='L'>L</option>
	<input type='button' value='use' onclick=useadd($key,-1) /> 
	<input type='button' value='add' onclick=useadd($key,1) />
	<input type='button' value='delete' onclick=useadd($key,0) /> ";
//*/
?>