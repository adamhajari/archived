<?php
// Fill up array with names
$a[]="sugar";
$a[]="flour";
$a[]="salt";
$a[]="apples";
$a[]="chicken broth";
$a[]="vegatable broth";
$a[]="ground beef";
$a[]="pepper";
$a[]="baking soda";
$a[]="cinamon";
$a[]="eggs";
$a[]="tomato";
$a[]="chili powder";
$a[]="brown rice";
$a[]="white rice";
$a[]="black beans";
$a[]="chickpeas";
$a[]="black olives";
$a[]="green olives";
$a[]="olive oil";
$a[]="butter";
$a[]="vinegar";
$a[]="mozzarella";
$a[]="cheddar cheese";
$a[]="baking powder";
$a[]="chocolate chips";
$a[]="brown sugar";
$a[]="bell pepper";
$a[]="peaches";
$a[]="onions";
$a[]="garlic";
$a[]="jalepenos";
$a[]="lettuce";
$a[]="oranges";
$a[]="celery";
$a[]="asparagus";
$a[]="jam";
$a[]="jelly";
$a[]="bread";

//get the q parameter from URL
$q=$_GET["q"];

//lookup all hints from array if length of q>0
if (strlen($q) > 0)
  {
  $hint="";
  for($i=0; $i<count($a); $i++)
    {
    if (strtolower($q)==strtolower(substr($a[$i],0,strlen($q))))
      {
      if ($hint=="")
        {
        $hint=$a[$i];
        }
      else
        {
        $hint=$hint." , ".$a[$i];
        }
      }
    }
  }

// Set output to "no suggestion" if no hint were found
// or to the correct values
if ($hint == "")
  {
  $response="no suggestion";
  }
else
  {
  $response=$hint;
  }

//output the response
echo $response;
?>