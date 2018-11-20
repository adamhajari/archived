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

	echo "<h4> Recipes</h4><br /> ";
	
	$query="select * from Recipes where user='$username'";	
        $result=mysql_query($query, $sqlserver);
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
 	{
		$str1='r';
		$str2=$row['id'];
                $total=$str1.$str2;
                $name=$total;	
		echo "<div id='{$name}'>";
		echo "{$row['title']}";	
		echo "<input type='button' value='delete' onclick=delete_recipe({$row['id']})>";
		echo "<input type='button' value='View' onclick=view_recipe({$row['id']})><br />";
		echo "<hr />";
		echo "</div>";
	
	}

        echo "<br /><input type='button' value='Add Recipe' onclick=add_recipe_dialog() /><br /><br />";    

        mysql_close($sqlserver);
}
?>
                <script type='text/javascript'>hide_recipes();</script>

