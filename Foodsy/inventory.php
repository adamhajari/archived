<?php
//calendar_connect.php includes the following line with appropriate values for
//the calendar mysql database:
// $sqlserver=mysql_connect(HOST,USER,PASSWORD);
// mysql_select_db(DATABASE,$sqlserver);
	

session_start();
include("foodsy_connect.php");
if(isset($_SESSION["username"])){
        $username = $_SESSION["username"];
        $verified = 1;
}else{
        $verified = 0;
}

if($verified){

echo "<b>Inventory</b><br>";
$query = "select * from Inventory where user='$username'";
$result = mysql_query($query,$sqlserver);
while($row = mysql_fetch_row($result, MYSQL_ASSOC)){
	$food_item = $row['food_item'];
	$quant = $row['quantity'];
	$unit = $row['units'];
	$key = $row['id'];

	  echo "
	  <div id='$key' onclick=useadd_dialog($key)>
		$food_item - $quant $unit";
//	  echo "<input type='button' value='use/add' onclick=useadd_dialog($key) /> <br>";
	  echo "<hr/> </div> ";
}}
else{
 echo "log in to view your inventory and recipes";
}
?>
