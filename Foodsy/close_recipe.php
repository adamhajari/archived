<?php
        session_start();
        include("foodsy_connect.php");
        if(isset($_SESSION["username"])){
                $username = $_SESSION["username"];
        }else{
                echo " ";
        }

        $query="select * from Recipes where id='{$_GET['id']}'";
        $result=mysql_query($query, $sqlserver);
        while($row = mysql_fetch_array($result, MYSQL_ASSOC))
        {
                $ingredients=mysql_query("select * from Ingredients where recipe='{$row['title']}'", $sqlserver);
 
                echo "{$row['title']}";
                echo "<input type='button' value='delete' onclick=delete_recipe({$row['id']})>";
                echo "<input type='button' value='View' onclick=view_recipe({$row['id']})><br />";
       		echo "<hr />";
	 }



        mysql_close($sqlserver);

?>
