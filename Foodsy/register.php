<html>
<head><title>Register a new account</title></head>
<body>
<!--
<form action= <?php echo $_SERVER['PHP_SELF'] ?> method="post">
username: <input type="text" name="username"></br>
password: <input type="password" name="password"></br>
<input type="submit" value="Create Account">
</form>
-->
<?php
session_start();

if(isset($_POST['username']) && isset($_POST['password']) ){
	//calendar_connect.php includes the following line with appropriate values for
	//the calendar mysql database:
	// $sqlserver=mysql_connect(HOST,USER,PASSWORD);
	// mysql_select_db(DATABASE,$sqlserver);
	include("foodsy_connect.php"); 

	$username = mysql_real_escape_string($_POST['username']);
	$username = $_POST['username'];
	$password = crypt($_POST['password'],'xo');
	echo "successful registration";
	$query = "insert into users values ('$username', '$password', 0)";
	mysql_query($query, $sqlserver);
	$_SESSION['username']=$username;

}else{
	echo "enter a username, password, and email";
}
?>

</body>
</html>
