<html>
<head><title>Calendar</title>
<style>
	table,td,th {border:1px solid black;}
	table{width:100%;}
	th{width:100px;}
	td{
	  height:100px;
	  vertical-align:top;
	}
    #event_dialog { display:none }
	#login_dialog { display:none }
	#register_dialog { display:none }
	#search_dialog { display:none }
</style>

  <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/start/jquery-ui.css" type="text/css" rel="Stylesheet" /> 
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script>
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.5/jquery-ui.min.js"></script>
<script type="text/javascript">

//THIS CALENDAR CURRENTLY DOES NOT ACCOMODATE LEAP YEARS
var DIM=[31,28,31,30,31,30,31,31,30,31,30,31]; // number of Days In each Month
var MONTHS=['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

//get current date info
var d = new Date();
cur_month = d.getMonth(); 		//curent month (from 0 to 11)
cur_year = d.getFullYear(); 	//current year
cur_day = d.getDate(); 			//current day of the month (1 to 31)
cur_DOW = d.getDay(); 			//current day of the week (from 0 to 6)
first_DOM = (7-((cur_day-1)%7)+cur_DOW)%7;


$(document).ready(function(){
  populate_calendar(first_DOM);
});

function populate_calendar(first_DOM){
  
  document.getElementById('header').innerHTML=MONTHS[cur_month]+', '+cur_year;
  sq_index =0;
  day_index = 1;
  while(day_index <= 40){
	  for(j=0; j<7; j++){
		  element = document.getElementById(sq_index);
		  if (day_index<=DIM[cur_month] && sq_index >=first_DOM){
			  element.innerHTML = day_index;
			  event_summary(day_index, element);
			  day_index++;
			  last_DOM = j;
		  }else{ 
			  element.innerHTML='';
		  }
		  sq_index++;
	  }
  }
}

function event_summary(day_index, element){
  var req = "day="+day_index+"&month="+cur_month+"&year="+cur_year;

  var xmlHttp=new XMLHttpRequest();
  xmlHttp.open("POST","event_summary.php",true);
  xmlHttp.onreadystatechange=function(){
    if(xmlHttp.readyState==4){
	  element.innerHTML = xmlHttp.responseText;
	}
  }  
  xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xmlHttp.setRequestHeader("Content-length", req.length);
  xmlHttp.setRequestHeader("Connection", "close");
  xmlHttp.send(req);
}  

function openDay(x){
  day = x.id-first_DOM+1;  //day of the month
  event_dialog(day);
}	

function event_dialog(day){
  var req = "day="+day+"&month="+cur_month+"&year="+cur_year;

  var xmlHttp=new XMLHttpRequest();
  xmlHttp.open("POST","events.php",true);
  xmlHttp.onreadystatechange=function(){
    if(xmlHttp.readyState==4){
	  document.getElementById('events').innerHTML = xmlHttp.responseText;
	}
  }  
  xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xmlHttp.setRequestHeader("Content-length", req.length);
  xmlHttp.setRequestHeader("Connection", "close");
  xmlHttp.send(req);
 
  var xmlHttp2=new XMLHttpRequest();
  xmlHttp2.open("GET","add_event_form.php",true);
  xmlHttp2.onreadystatechange=function(){
    if(xmlHttp2.readyState==4){
	  document.getElementById('new_event').innerHTML = xmlHttp2.responseText;
	  $("#event_dialog").dialog( { width: 700 });
	}
  }  
  xmlHttp2.send();

  
}

function addEvent(){
  username = document.getElementById('username').value;
  //day = day;
  month = cur_month;
  year = cur_year;
  title = document.getElementById('title').value;
  hour = document.getElementById('hour').value;
  minute = document.getElementById('minute').value;
  desc = document.getElementById('desc').value;
  pub = document.getElementById("pub").checked;
  
  var req = "user="+username+"&day="+day+"&month="+cur_month+"&year="+cur_year+
    "&title="+title+"&hour="+hour+"&minute="+minute+"&desc="+desc+"&pub="+pub;
  
  var xmlHttp=new XMLHttpRequest();
  xmlHttp.open("POST","add_event.php",true);
  xmlHttp.onreadystatechange=function(){
    if(xmlHttp.readyState==4){
	  
      $("#event_dialog").dialog("close")
	  populate_calendar(first_DOM);
	}
  }  
  xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xmlHttp.setRequestHeader("Content-length", req.length);
  xmlHttp.setRequestHeader("Connection", "close");
  xmlHttp.send(req);
  
  
}

function deletex(key){
  var xmlHttp=new XMLHttpRequest();
  xmlHttp.open("POST","remove_event.php",true);
  xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xmlHttp.setRequestHeader("Connection", "close");
  xmlHttp.send("key="+key);
  
  $("#event_dialog").dialog("close")
  populate_calendar(first_DOM);
}

function fwd(){
  update_month(+1);
  first_DOM=(last_DOM+1)%7;
  populate_calendar(first_DOM);
}
function back(){
  update_month(-1);
  first_DOM = ((first_DOM-(DIM[cur_month]%7))%7+7)%7;
  populate_calendar(first_DOM);
  
}

function login_dialog(){
  $("#login_dialog").dialog();
}

function register_dialog(){
  $("#register_dialog").dialog();
}

function logout(){

  var xmlHttp2=new XMLHttpRequest();
  xmlHttp2.open("GET","logout.php",true);
  xmlHttp2.onreadystatechange=function(){
    if(xmlHttp2.readyState==4){
	  document.getElementById('test1').innerHTML = xmlHttp2.responseText;
	  populate_calendar(first_DOM);
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
	  populate_calendar(first_DOM);
	}
  }  
  xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xmlHttp.setRequestHeader("Content-length", req.length);
  xmlHttp.setRequestHeader("Connection", "close");
  xmlHttp.send(req);
  
  $("#login_dialog").dialog("close");
}

function register(){

  var xmlHttp=new XMLHttpRequest();

  username = document.getElementById('reg_username').value;
  password = document.getElementById('reg_password').value;
  document.getElementById('test1').innerHTML = username;
  
  var req = "username="+username+"&password="+password;  
  
  xmlHttp.open("POST","register.php",true);
  xmlHttp.onreadystatechange=function(){
    if(xmlHttp.readyState==4){
	}
  }  
  xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xmlHttp.setRequestHeader("Content-length", req.length);
  xmlHttp.setRequestHeader("Connection", "close");
  xmlHttp.send(req);
  
  $("#register_dialog").dialog("close");
}

function update_month(dir){
//dir=1 to increment month and dir=-1 to decrement month
  if(dir==1){
	if(cur_month<11){
	  cur_month++;
	}else{
	  cur_month=0;
	  cur_year++;
	}
  }else{
    if(cur_month>0){
	  cur_month--;
	}else{
	  cur_month=11;
	  cur_year--;
	}
  }
}

function search_dialog(){
  $("#search_dialog").dialog({ width: 500 });
}

function search(){
  document.getElementById('results').innerHTML = 1
  query = document.getElementById('query').value;
  var xmlHttp=new XMLHttpRequest();
  var req = "query="+query;  
  
  xmlHttp.open("POST","search.php",true);
  xmlHttp.onreadystatechange=function(){
    if(xmlHttp.readyState==4){
      document.getElementById('results').innerHTML = xmlHttp.responseText;
	}
  }  
  xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xmlHttp.setRequestHeader("Content-length", req.length);
  xmlHttp.setRequestHeader("Connection", "close");
  xmlHttp.send(req);
}

</script>
</head>
<body>
<?php

$DOW = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
$DIM = array(31,28,31,30,31,30,31,31,30,31,30,31); //number of Days In each Month

$cur_date=getdate();
$cur_month = $cur_date['month']; 	//curent month string
$cur_month_num = date("n"); 		//current month int
$cur_year = $cur_date['year']; 		//current year
$cur_day = $cur_date['mday']; 		//current day of the month
$cur_DOW = $cur_date['weekday']; 	//current day of the week string
$cur_DOW_num = $cur_date['wday']; 	//current day of the week int

echo "<h3 id='header'>$cur_month, $cur_year </h3>
 <button id='back' onclick='back()'>Back</button><button type='button' onclick='fwd()'>Forward</button>";
echo "<p id='test1'>login to view/add events</p>";
for($i=0; $i<7; $i++){
	if (preg_match("/{$cur_DOW}/i", $DOW[$i])) {
		$first_DOW = (7-(($cur_day-1)%7)+$i)%7;
	}
}

echo "<table id='calendar' border='1'>
<tr>";
for($i=0; $i<7; $i++){
	echo "<th> $DOW[$i] </th>";
}
echo "</tr>";


$sq_index =0;
$day_index = 1;
while($day_index <= $DIM[$cur_month_num-1]){
	echo "<tr>";
	for($j=0; $j<7; $j++){
		
		if ($sq_index >=$first_DOW && $day_index<=$DIM[$cur_month_num-1]){
			echo "<td id='$sq_index' onclick='openDay(this)'>$day_index</td>";
			$day_index++;
		}else echo "<td id='$sq_index' onclick='openDay(this)'> </td>";
		$sq_index++;
	}
	echo "</tr>";
}
if($sq_index<36){
echo "<tr>";
	for($j=0; $j<7; $j++){
		echo "<td id='$sq_index' onclick='openDay(this)'></td>";
		$sq_index++;
	}
	echo "</tr>";
}
echo "</table> ";
?>
<br><br>

<input type="button" value="login" onclick=login_dialog() />
<input type="button" value="register" onclick=register_dialog() />
<input type="button" value="logout" onclick=logout() /><br>
<input type="button" value="search" onclick=search_dialog() />


<div id="event_dialog" title="events">

  <div id="events">no events for this day</div><br>
  
  <div id="new_event">
    <b>add a new event</b><br>
    title:<input id="title" type="text" /><br>
	description:<br><textarea id="desc" rows="4" cols = "50" wrap="physical" name="text"></textarea><br>
	hour: <input id="hour" type="text" /> <br>
	minute: <input id="minute" type="text" /><br>
	<input type="checkbox" id="pub" /> make public<br />
	<input type="button" value="add" onclick=addEvent() /> 
  </div>

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

<div id="search_dialog" title="search">
  <div id="results">
    
  </div>
  <div id="query_div">
    search for:<input id="query" type="text" /><br>
    <input type="button" value="search" onclick=search() /> 
  </div>
</div>

</body>
</html>