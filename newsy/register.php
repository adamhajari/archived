<html>
<head><title>Register a new account</title></head>
<body>

<form action= <?php echo $_SERVER['PHP_SELF'] ?> method="post">
    username: <input type="text" name="username"></br>
    password: <input type="password" name="password"></br>
	email address: <input type="text" name="email"></br>
    <input type="submit" value="Create Account">
</form>

<?php
session_start();

if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email'])){
//get username and password from form

//newsy_connect.php includes the following line with appropriate values for
//the newsy mysql database:
// $sqlserver=mysql_connect(HOST,USER,PASSWORD);
// mysql_select_db(DATABASE,$sqlserver);
include("newsy_connect.php");

$username = mysql_real_escape_string($_POST['username']);
$password = crypt($_POST['password'],'xo');
$email = mysql_real_escape_string($_POST['email']);

$query = 'insert into users values ("'. $username . '", "' . $password
	.'", "'. $email . '", 0)';
//echo $query;
mysql_query($query, $sqlserver);

Header("Location:./login.php");


}else{
echo "enter a username, password, and email";
}
?>

</body>
</html>