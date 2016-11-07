<?php
require_once('master_validation.php');
require_once('config/connection.php');

	$parent		=strtoupper(trim($_POST['parent']));
	$orgcode	=strtoupper(trim($_POST['orgcode']));
	$orgname    =strtoupper(trim($_POST['orgname']));
	$orgtype	=strtoupper(trim($_POST['orgtype']));
	$orgadd		=trim($_POST['orgadd']);
	$orgcity	=strtoupper(trim($_POST['orgcity']));
	$orgcountry	=strtoupper(trim($_POST['orgcountry']));											
	$orgzip 	=strtoupper(trim($_POST['orgzip']));
	$orgtelp	=strtoupper(trim($_POST['orgtelp']));		
			
	//check if the same code and the same parent already exist
	$jum=0;//indicate not exist
	$exist=false;
	$s1="select count(*) from ".$dbname.".org where code='".$orgcode."' and parent='".$parent."'";
	$re1=mysql_query($s1);
	while($row=mysql_fetch_array($re1))
	{
		$jum=$row[0];
	}
	if($jum>0)
	  $exist=true;
	  
	if(!$exist){//then insert
		$st2="insert into ".$dbname.".org
		      (code,emplname,address,telp,city,zipcode,parent,country,type,lastuser)
		values('".$orgcode."','".$orgname."','".$orgadd."','".$orgtelp."','".$orgcity."','".
		          $orgzip."','".$parent."','".$orgcountry."','".$orgtype."','".
				  $_SESSION['standard']['username']."')";
	}
	else
	{//then update
	  $st2="update ".$dbname.".org
	        set	emplname='".$orgname."',
				address	='".$orgadd."',
				telp	='".$orgtelp."',
				city	='".$orgcity."',
				zipcode	='".$orgzip."',
				country	='".$orgcountry."',
				type	='".$orgtype."',
				lastuser='".$_SESSION['standard']['username']."'
			 where code	='".$orgcode."'
			 and parent ='".$parent."'";	
	}
   mysql_query($st2);
   if(mysql_affected_rows($conn)!=-1)
   {}
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}
?>
