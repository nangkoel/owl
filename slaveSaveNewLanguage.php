<?php
require_once('master_validation.php');
require_once('config/connection.php');

	$newlang	=$_POST['newlang'];
	$name 		=$_POST['langname'];
	$def		=$_POST['def'];
//add column to lang table
    $str="alter table ".$dbname.".bahasa add column ".$newlang." text";
   if(mysql_query($str)){
   	   //set value= def to new language
	   $str1="update ".$dbname.".bahasa set ".$newlang."=".$def;
	   mysql_query($str1);
	   $str2="insert into ".$dbname.".namabahasa
	          (code,name) values('".$newlang."','".$name."')";
	   mysql_query($str2);		  
	   
	   //get language column list to display 
	   $sta="select * from ".$dbname.".namabahasa";
		$res=mysql_query($sta);
		$langlist="";
		while($bar=mysql_fetch_object($res))
		{
			$langlist.=" &nbsp &nbsp<a href=# onclick=loadLang('".$bar->code."')>".$bar->name."</a>";	   
		}
echo" <fieldset style='width:850px;'>
	  <legend>".$_SESSION['lang']['availlang']."</legend>
	  ".$langlist."
	  </fieldset>";
   }
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}
?>
