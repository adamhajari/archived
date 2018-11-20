<?php
session_start();
unset ($_SESSION['username']);
session_unregister ($username);
Header("Location:./login.php");
?>