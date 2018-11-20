<?php
session_start();
unset ($_SESSION['username']);
//session_unregister ($username);
echo "login to view/add events";
?>