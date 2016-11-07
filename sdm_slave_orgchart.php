<?php
require_once('master_validation.php');
require_once('config/connection.php');

	$parent		=strtoupper(trim($_POST['parent']));
	$kdStruk	=strtoupper(trim($_POST['kdStruk']));
	$karyId         =trim($_POST['karyId']);
	$kdJbtn         =$_POST['kdJbtn'];
	$detail      =$_POST['detail'];
        $mailDt      =$_POST['mailDt'];
	$alokasi	=strtoupper(trim($_POST['alokasi']));
	 	
			
	//check if the same code and the same parent already exist
	$jum=0;//indicate not exist
	$exist=false;
	$s1="select count(*) from ".$dbname.".sdm_strukturjabatan where kodestruktur='".$kdStruk."' and induk='".$parent."'";
	$re1=mysql_query($s1);
	while($row=mysql_fetch_array($re1))
	{
		$jum=$row[0];
	}
	if($jum>0)
	  $exist=true;
	  
	if(!$exist){//then insert
		$st2="insert into ".$dbname.".sdm_strukturjabatan
		      (`induk`, `kodestruktur`, `karyawanid`, `kodejabatan`, `email`, `kodept`, `lastuser`)
		values('".$parent."','".$kdStruk."','".$karyId."','".$kdJbtn."','".$mailDt."','".
		          $alokasi."','".$_SESSION['standard']['username']."')";
	}
	else
	{//then update
	  $st2="update ".$dbname.".sdm_strukturjabatan
	        set	karyawanid='".$karyId."',
				kodejabatan	='".$kdJbtn."',
				email	='".$mailDt."',
				kodept	='".$alokasi."',
				lastuser	='".$_SESSION['standard']['username']."'
			 where kodestruktur	='".$kdStruk."'
			 and induk ='".$parent."'";	
	}
	//echo "error:".$st2;
   mysql_query($st2);
   if(mysql_affected_rows($conn)!=-1)
   {}
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}
?>
