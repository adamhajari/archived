<?php
session_start();
//calendar_connect.php includes the following line with appropriate values for
//the calendar mysql database:
// $sqlserver=mysql_connect(HOST,USER,PASSWORD);
// mysql_select_db(DATABASE,$sqlserver);
include("foodsy_connect.php");

//units.php includes a function to convert units:
//convert($quant1,$unit1,$unit2) converts $quant1 in $unit1 to a quantity in $unit2
include("units.php");

if(isset($_SESSION["username"])){
	$username = $_SESSION["username"];
}else{
	echo " ";
}
$key = mysql_real_escape_string($_POST['key']);
$quant_in = mysql_real_escape_string($_POST['quant']);
$new_unit = mysql_real_escape_string($_POST['unit']);
$useadd = mysql_real_escape_string($_POST['useadd']);

$query = "select * from Inventory where id='$key'";
$result = mysql_query($query,$sqlserver);
$row = mysql_fetch_row($result, MYSQL_ASSOC);
$quant_old = $row['quantity'];
$unit = $row['units'];
$new_quant = convert($quant_in,$new_unit,$unit);
if($useadd==1){
	//add item
	echo "test1 <br>";
	$quant_new = $quant_old + $new_quant;
}else if ($useadd==-1){
	//use item
	$quant_new = $quant_old - $new_quant;
}else if ($useadd==0){
	//use item
	$quant_new = -1;
}

echo $useadd;

if($quant_new >=0 && $useadd!=0){
	$query2 = "update Inventory set quantity=$quant_new where id='$key'";
	mysql_query($query2,$sqlserver);
}if($useadd==0){
	$query2 = "delete from Inventory where id='$key'";
	mysql_query($query2,$sqlserver);
}else{
	echo "you can't use more than you have";
}
	
?>