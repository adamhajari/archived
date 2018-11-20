<?php
/* sample inputs for convert()
$quant1 = 3;
$unit1 = 'c';
$unit2 = 'oz';
$quant2 = convert($quant1,$unit1,$unit2);
echo $quant2;
*/

function convert($quant1,$unit1,$unit2)
{	
$unit_list = array('tspn', 'tbsp', 'oz', 'c', 'pt', 'qt', 'gal', 'L', 'mL');
$unit_conv = array(6, 2, 1, 1/8, 1/16, 3/100, 1/128, 0.02957, 29.57);
$list_size = 9;

	for($i=0; $i<$list_size; $i++){
		if($unit1==$unit_list[$i]){
			$conv1 = $unit_conv[$i];
		}
		if($unit2==$unit_list[$i]){
			$conv2 = $unit_conv[$i];
		}
	}
	$quant2 = $quant1*$conv2/$conv1;

	return $quant2;
}
?>