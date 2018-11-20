<?php


$isuser = 0; //isuser will be 1 if username exists in user array
if(isset($_POST['username']) && isset($_POST['password'])){

//calendar_connect.php includes the following line with appropriate values for
//the calendar mysql database:
// $sqlserver=mysql_connect(HOST,USER,PASSWORD);
// mysql_select_db(DATABASE,$sqlserver);
include("foodsy_connect.php");

//iterate through all elements of user array to check whether the 
//entererd username in the array.
	
	//get username and password from form
	$username = mysql_real_escape_string($_POST['username']);
	$password = $_POST['password'];
	
	$result = mysql_query('select * from users',$sqlserver);

	while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
	
		$user = $row['username'];
		$pass = $row['password'];

		if($username == $user){
			if(crypt($password,'xo') == $pass){
				echo "Hello $username, welcome to Foodsy!";
				session_start();
				$admin = $row['admin'];
				$_SESSION['username']=$username;
				$_SESSION['admin']=$admin;

			}else{
				echo "incorrect password";
			}
			$isuser = 1;
		}
	}
	if($isuser == 0){
		echo "user does not exist";
	
	}
}

//echo "</br></br><a href='./register.php' title='register'>register a new account</a></br>";
?>
