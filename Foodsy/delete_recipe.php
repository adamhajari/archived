<?php
session_start();
include("foodsy_connect.php");
if(isset($_SESSION["username"])){
	$username = $_SESSION["username"];
	$verified = 1;
}else{
	$verified = 0;
}
if($verified){
	echo "deleteing";
        $query="select * from Recipes where id={$_GET['id']}";
        $result=mysql_query($query, $sqlserver);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
//	$ingredients=mysql_query("select * from Ingredients where recipe='{$row[title]}'", $sqlserver);
	echo "deleting ingredients";
	$delete_ingred=mysql_query("delete from Ingredients where recipe='{$row[title]}';",$sqlserver);
	echo "deleteing recipe";
	$query="delete from Recipes where id={$_GET[id]};";
	echo $query;
	$result=mysql_query($query,$sqlserver);
}
else{
	echo 'Sorry, your submission was not successful. </br>';
}



mysql_close($sqlserver);

?>

