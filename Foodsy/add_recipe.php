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
	$title=$_GET['title'];
	$instructions=$_GET['instructions'];
	$recipe_query="insert into Recipes (user, instructions, public, title) values('$username','$instructions','n','$title')";
	mysql_query($recipe_query,$sqlserver);
	
	$i=$_GET['i'];
	for($j=0;$j<=$i;$j++){
		$str1='item';
		$str2=(string)$j;
		$total=$str1.$str2;
		$name=$total;	
		$item=$_GET[$name];

		$str1='quant';
                $total=$str1.$str2;
                $name=$total;
		$quant=$_GET["$name"];
		
		$str1='unit';
                $total=$str1.$str2;
                $name=$total;	
		$units=$_GET["$name"];
			
		mysql_query("insert into Ingredients (recipe, food_item,quantity, units) values('$title','$item',$quant,'$units')",$sqlserver);

	}
	}else{
	echo 'Sorry, your submission was not successful. </br>';
}



mysql_close($sqlserver);

?>
                                                                                                             
