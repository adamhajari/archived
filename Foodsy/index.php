<html>
<head><title>Foodsy</title>
<style>
h5 {margin-top:7px;margin-bottom:1px; margin-left:0px;margin-right:0px; padding:0; font-family: Arial, Helvetica, sans-serif;}
input {margin:3px;}
h4 {margin:0; padding:0;font-family:Arial,Helvetica,sans-serif; }
th {font-style:normal; font-weight:normal; font-family: Arial, Helvetica, sans-serif; }
#add_ingred{text-align:center;margin-top: 20px;margin-bottom: 0px; margin-left:auto; margin-right:auto;}
#view_recipe {margin-top:5px; margin-bottom: 25px; }
#recipes {display:none; border-width:2px; width:300px; border-style:solid; padding:10px;text-align:center;}
#inventory {border-width:2px; width:300px; border-style:solid; padding:10px;text-align:left;}
#add_recipe_dialog{display:none; }
#add_recipe_table {padding:2px; margin-top:2px; margin-bottom:10px; }
#add_item_table {padding:2px; margin-top:2px; margin-bottom:10px; }
#pos_recipes { display:none }
#login_dialog { display:none }
#register_dialog { display:none }
#search_dialog { display:none }
#use_dialog { }
#inv_add {display:none; }
#login_button { }
#register_button{ }
#logout_button {margin:0; padding:0;display:none;}
#make_button { margin-top:15px; margin-bottom:5px;display:none;text-align:center; }
</style>

<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/start/jquery-ui.css" type="text/css" rel="Stylesheet" /> 
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.5/jquery-ui.min.js"></script>
<script type="text/javascript">
//to convert quantity x in units[i] to quantity y in units[j] do: y = x*unit_conv[j]/unit_conv[i]
var units = ['tspn', 'tbsp', 'oz', 'c', 'pt', 'qt', 'gal', 'L', 'mL'];
var unit_conv = [6, 2, 1, 1/8, 1/16, 3/100, 1/128, 0.02957, 29.57];

function display_inventory(){
	username = document.getElementById('username').value;

	var xmlHttp=new XMLHttpRequest();
	xmlHttp.open("GET","inventory.php",true);
	xmlHttp.onreadystatechange=function(){
		if(xmlHttp.readyState==4){

			document.getElementById('inventory').innerHTML = xmlHttp.responseText;
		}
	}  
	xmlHttp.send();


}

function inv_add(){
	item = document.getElementById('item').value;
	quant = document.getElementById('quantity').value;  
	units = document.getElementById('units').value;

	var req = "item="+item+"&quant="+quant+"&units="+units;
	var xmlHttp=new XMLHttpRequest();
	xmlHttp.open("POST","inv_add.php",true);
	xmlHttp.onreadystatechange=function(){
		if(xmlHttp.readyState==4){
			display_inventory()
			document.getElementById("txtHint").innerHTML="";
		}
	}  
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", req.length);
	xmlHttp.setRequestHeader("Connection", "close");
	xmlHttp.send(req);

}

function display_recipes(){
	$('#recipes').css('display','block');

	var xmlHttp=new XMLHttpRequest();
	xmlHttp.open("GET","recipes.php",true);
	xmlHttp.onreadystatechange=function(){
		if(xmlHttp.readyState==4){
			document.getElementById('recipes').innerHTML = xmlHttp.responseText;
		}
	}  
	xmlHttp.send();

}

function view_recipe(id){
	var xmlHttp=new XMLHttpRequest();
	xmlHttp.open("GET","view_recipe.php?id="+id,true);
	xmlHttp.onreadystatechange=function(){
		if(xmlHttp.readyState==4){
			document.getElementById(['r'+id]).innerHTML = xmlHttp.responseText;
		}
	}  
	xmlHttp.send();
}

function close_recipe(id){
	var xmlHttp=new XMLHttpRequest();
	xmlHttp.open("GET","close_recipe.php?id="+id,true);
	xmlHttp.onreadystatechange=function(){
		if(xmlHttp.readyState==4){
			document.getElementById(['r'+id]).innerHTML = xmlHttp.responseText;
		}        }  
	xmlHttp.send();
}

function delete_recipe(id){
	var xmlHttp=new XMLHttpRequest();
	xmlHttp.open("GET","delete_recipe.php?id="+id,true);
	xmlHttp.onreadystatechange=function(){
		if(xmlHttp.readyState==4){
			display_recipes();
		}
	}  
	xmlHttp.send();
}

function add_recipe_dialog(){   
	$("#add_recipe_dialog").dialog({width:500, height:500});
}


function login_dialog(){
	$("#login_dialog").dialog();
}

function register_dialog(){
	$("#register_dialog").dialog();
}

function search_inv(){

	var req = "username="+1+"&password="+2;

	var xmlHttp=new XMLHttpRequest();
	xmlHttp.open("POST","search_inv.php",true);
	xmlHttp.onreadystatechange=function(){
		if(xmlHttp.readyState==4){
			$("#pos_recipes").dialog();
			document.getElementById('xrecipes').innerHTML = xmlHttp.responseText;
		}
	}  
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", req.length);
	xmlHttp.setRequestHeader("Connection", "close");
	xmlHttp.send(req);
}

function logout(){
	var xmlHttp2=new XMLHttpRequest();
	xmlHttp2.open("GET","logout.php",true);
	xmlHttp2.onreadystatechange=function(){
		if(xmlHttp2.readyState==4){
			document.getElementById('test1').innerHTML = xmlHttp2.responseText;
			display_inventory();
			display_recipes();	
			hide_recipes();
		}
	}  
	xmlHttp2.send();

	document.getElementById('events').innerHTML = "login to view/add events"; 
	document.getElementById('new_event').innerHTML = ""; 
}

function login(){
	username = document.getElementById('username').value;
	password = document.getElementById('password').value;

	var req = "username="+username+"&password="+password;

	var xmlHttp=new XMLHttpRequest();
	xmlHttp.open("POST","login.php",true);
	xmlHttp.onreadystatechange=function(){
		if(xmlHttp.readyState==4){
			document.getElementById('test1').innerHTML = xmlHttp.responseText;
			if((xmlHttp.responseText=="user does not exist")||(xmlHttp.responseText=="incorrect password")){
			//	document.getElementById('recipes').innerHTML="no recipes here";
			}else{
			$('#inv_add').css('display','inline');
			$('#logout_button').css('display','inline');
			$('#login_button').css('display','none');
			$('#register_button').css('display','none');
		        $("#make_button").css("display","block");
	                display_inventory();
                        display_recipes();}
		}
	}  
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", req.length);
	xmlHttp.setRequestHeader("Connection", "close");
	xmlHttp.send(req);

	$("#login_dialog").dialog("close");
}

function useadd_dialog(key){

	var xmlHttp=new XMLHttpRequest();
	var req = "key="+key;  
	//document.getElementById('use_key').value = key;

	xmlHttp.open("POST","useadd_dialog.php",true);
	xmlHttp.onreadystatechange=function(){
		if(xmlHttp.readyState==4){
			document.getElementById('use_dialog_head').innerHTML = xmlHttp.responseText;
			$("#use_dialog").dialog({ width: 450 });
		}
	}  
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", req.length);
	xmlHttp.setRequestHeader("Connection", "close");
	xmlHttp.send(req);
}

function useadd(key,useadd){

	var xmlHttp=new XMLHttpRequest();
	quant = document.getElementById('use_quant').value;
	unit = document.getElementById('use_unit').value;
	var req = "key="+key+"&quant="+quant+"&unit="+unit+"&useadd="+useadd;  

	xmlHttp.open("POST","useadd.php",true);
	xmlHttp.onreadystatechange=function(){
		if(xmlHttp.readyState==4){
			display_inventory();
			$("#use_dialog").dialog("close");
		}
	}  
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", req.length);
	xmlHttp.setRequestHeader("Connection", "close");
	xmlHttp.send(req);
}

function register(){
	var xmlHttp=new XMLHttpRequest();
	username = document.getElementById('reg_username').value;
	password = document.getElementById('reg_password').value;

	var req = "username="+username+"&password="+password;  

	xmlHttp.open("POST","register.php",true);
	xmlHttp.onreadystatechange=function(){
		if(xmlHttp.readyState==4){
                        display_inventory();
                        display_recipes();
                        $('#inv_add').css('display','inline');
                        $('#logout_button').css('display','inline');
                        $('#login_button').css('display','none');
                        $('#register_button').css('display','none');
	        	$("#make_button").css("display","block");
		}
	}  
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", req.length);
	xmlHttp.setRequestHeader("Connection", "close");
	xmlHttp.send(req);

	$("#register_dialog").dialog("close");
}

i=0;
function add(){
        i++;
        var table=document.getElementById('add_recipe_table');
        var rowCount=table.rows.length;
        var row=table.insertRow(rowCount);
        var itemCell=row.insertCell(0);
        var itemInput=document.createElement('input');
        itemInput.setAttribute('type','text');
        itemInput.setAttribute('id',['recipe_item'+i]);
        itemInput.setAttribute('name',['item'+i]);
		itemInput.setAttribute('onkeyup',"showHint(this.value)");
        itemCell.appendChild(itemInput);

        var quantityCell=row.insertCell(1);
        var quantityInput=document.createElement('input');
        quantityInput.setAttribute('type','text');
        quantityInput.setAttribute('id',['recipe_quantity'+i]);
        quantityInput.setAttribute('name',['quant'+i]);
        quantityInput.setAttribute('size',5);
        quantityCell.appendChild(quantityInput);

        var unitCell=row.insertCell(2);
        var unitInput=document.createElement('select');
        add_option(unitInput, 'cups','c');      
        add_option(unitInput,"oz","oz");       
        add_option(unitInput,"tspn","tspn");       
        add_option(unitInput,"tbsp","tbsp");
        add_option(unitInput,"pt","pt");
        add_option(unitInput,"qt","qt");
        add_option(unitInput,"gal","gal");
        add_option(unitInput,"mL","mL");
        add_option(unitInput,"L","L");
        

        unitInput.setAttribute('id',['recipe_unit'+i]);
        unitInput.setAttribute('name',['unit'+i]);
        unitCell.appendChild(unitInput);

}

function add_option(selectbox, text, value){
        var optn=document.createElement("OPTION");
        optn.text=text;
        optn.value=value;
        selectbox.options.add(optn);
}

function add_recipe(){
	var xmlHttp=new XMLHttpRequest();
	var ingredients=$("form").serialize();
	xmlHttp.open("GET","add_recipe.php?i="+i+"&"+ingredients,true);
	xmlHttp.onreadystatechange=function(){
		if(xmlHttp.readyState==4){      
			display_recipes();
		}
	}  
	xmlHttp.send();
	$("#add_recipe_dialog").dialog("close");
	document.getElementById("txtHint").innerHTML="";
}

function showHint(str){
	if (str.length==0)
	{
		document.getElementById("txtHint").innerHTML="";
		return;
	}
	xmlhttp=new XMLHttpRequest();
	xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("GET","list_of_foods.php?q="+str,true);
	xmlhttp.send();
}

function hide_recipes(){
	$("#inv_add").css("display","none");
	$("#recipes").css("display","none");
	$("#logout_button").css("display","none");
	$('#login_button').css('display','inline');
	$('#register_button').css('display','inline');
        $("#make_button").css("display","none");
}


</script>
</head>
<body>
<p id='test1'> </p>
<p id='inventory'>login to view your inventory and recipes</p>
<div id='recipes'></div>

<input id="make_button" type="button" value="what can i make?" onclick=search_inv() /><br>

<div id="inv_add">
<b>Add an item to your inventory:</b><br>
<form>
<table id="add_item_table" cellspacing='0'><tr><th>Item</th><th>Quantity</th><th>Units</th><th> </th></tr>
<tr><td><input id="item" type="text" onkeyup="showHint(this.value)" size="20"></td>
<td><input id="quantity" type="text" size=5></td>
<td>
<select id="units" name=units>
<option value="c">cups</option>
<option value="oz">oz</option> 
<option value="tspn">tspn</option>
<option value="tbpn">tbsp</option>
<option value="pt">pt</option>
<option value="qt">qt</option>
<option value="gal">gal</option>
<option value="mL">mL</option>
<option value="L">L</option>
</td>
<td><input type="button" value="add" onclick=inv_add() /> </td></tr>
</table>
</form>
<p>Suggestions: <span id="txtHint"></span></p>
</div>
<br><br>


<input id="login_button" type="button" value="login" onclick=login_dialog() />
<input id="register_button" type="button" value="register" onclick=register_dialog() />
<input id="logout_button" type="button" value="logout" onclick=logout() /><br>

<div id="pos_recipes" title="possible recipes">
<div id="xrecipes">Loading recipes</div><br>
</div>

<div id="login_dialog" title="login">
username:<input id="username" type="text" /><br>
password:<input id="password" type="password" /> <br>
<input type="button" value="login" onclick=login() /> 
</div>

<div id="register_dialog" title="register">
username:<input id="reg_username" type="text" /><br>
password:<input id="reg_password" type="password" /> <br>
<input type="button" value="register" onclick=register() /> 
</div>

<div id="add_recipe_dialog" title="Add Recipe">
<form>
<h4>Title</h4><input name="title" id="recipe_title" type="text"><br />
<br /><textarea name="instructions" cols="45" rows="5" id="recipe_instructions" wrap="hard">Enter instructions here.</textarea><br />
<h4 id="add_ingred">Ingredients</h4><br />
<table id="add_recipe_table" cellspacing='0'><tr><th>Item</th><th>Quantity</th><th>Units</th></tr>
<tr><td><input id="recipe_item" name="item0"  onkeyup="showHint(this.value)" type="text"></td>
<td><input name="quant0" id="recipe_quantity" type="text" size=5></td>
<td><select id="recipe_units" name="unit0">
<option value="c">cups</option>
<option value="oz">oz</option>
<option value="tspn">tspn</option>
<option value="tbpn">tbsp</option>
<option value="pt">pt</option>
<option value="qt">qt</option>
<option value="gal">gal</option>
<option value="mL">mL</option>
<option value="L">L</option>
</td>
</tr></table>

<input type="button" value="Add Another Ingredient" onclick=add()><br />
<input type="button" value="Add Recipe" onclick=add_recipe()><br />
</form></div>

<div id="use_dialog" title="Use/Add Item">
	<div id="use_dialog_head"> </div>
</div>

</body>
</html>
