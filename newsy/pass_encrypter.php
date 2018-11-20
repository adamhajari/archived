<?php


	//$password = crypt($row['password'],'xo');
	//$query2 = "update users set password='{$password}'";
	//mysql_query($query2,$sqlserver);
	
	$password_in = "password";
	$password_out = crypt($password_in);
	$password_check = crypt($password_in,$password_out);
	echo "password_in = $password_in<br>";
	echo "password_out = $password_out<br>";
	echo "password_check = $password_check<br>";

?>