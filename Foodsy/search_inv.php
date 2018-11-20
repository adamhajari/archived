<?php

error_reporting(E_ALL);

session_start();
//foodsy_connect.php includes the following line with appropriate values for
//the foodsy mysql database:
// $sqlserver=mysql_connect(HOST,USER,PASSWORD);
// mysql_select_db(DATABASE,$sqlserver);
include("foodsy_connect.php");

//units.php includes a function to convert units:
//convert($quant1,$unit1,$unit2) converts $quant1 in $unit1 to a quantity in $unit2
include("units.php");

if(isset($_SESSION["username"])){
	$username = $_SESSION["username"];

$query = "select * from Inventory where user='$username'";
$result = mysql_query($query, $sqlserver);
$inv_indx = 0;
while ($row = mysql_fetch_row($result, MYSQL_ASSOC)) {
	$item[$inv_indx] = $row['food_item'];
	$inv_quant[$inv_indx] = $row['quantity'];
	$inv_units[$inv_indx] = $row['units'];
	$inv_indx++;
}

$query2 = "select * from Recipes where user='$username'";
$result2 = mysql_query($query2, $sqlserver);
$rec_indx = 0;
while ($row = mysql_fetch_row($result2, MYSQL_ASSOC)) {
	$recipes[$rec_indx] = $row['title'];
	//$rec_ing_count[$rec_indx] will keep track of how many ingredients
	//a user has for each $recipes[$rec_indx]
	$rec_ing_count[$rec_indx] = 0;
	$rec_indx++;
}

//check for each recipe, if you have all the ingredients
for ($i=0; $i<$inv_indx; $i++){
	$query3 = "select * from Ingredients where food_item='$item[$i]'";
	$result3 = mysql_query($query3, $sqlserver);
	while ($row = mysql_fetch_row($result3, MYSQL_ASSOC)) {
		$rec = $row['recipe'];
		for ($j=0; $j<$rec_indx; $j++){
			$rec = $row['recipe'];
			$quant = $row['quantity'];
			$units = $row['units'];
			
			if ($row['recipe']==$recipes[$j]){
				//$quant2 is the amt the recipe calls for in units specified in inventory
				$quant2 = convert($quant,$units,$inv_units[$i]);
				if($inv_quant[$i]>=$quant2){
					$rec_ing_count[$j]++;
				}
			}
		}
		
	}
}
echo "<b>You can make:</b><br>";
for ($j=0; $j<$rec_indx; $j++){
	$query4 = "select * from Ingredients where recipe='$recipes[$j]'";
	$result4 = mysql_query($query4, $sqlserver);
	$num_ing = mysql_num_rows($result4);
	if($rec_ing_count[$j]>=$num_ing){
		echo "$recipes[$j] <br>";
	}
}

}else{
	echo "you must be logged in to search";
}


?>