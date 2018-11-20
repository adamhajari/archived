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
		echo "<div id='view_recipe'>";
                echo "{$row['title']}";
                echo "<input type='button' value='delete' onclick=delete_recipe({$row['id']})>";
                echo "<input type='button' value='Close' onclick=close_recipe({$row['id']})><br />";
		echo "<h5>Instructions</h5>";
		echo "{$row['instructions']}";
		echo "<h5>Ingredients</h5>";
		while ($ingred_result=mysql_fetch_array($ingredients, MYSQL_ASSOC)){
			echo "{$ingred_result['food_item']} - {$ingred_result['quantity']}&nbsp;{$ingred_result['units']}";
			echo "<br />";
		}
		echo "<hr />";	
		echo "</div>";
        }



        mysql_close($sqlserver);

?>

