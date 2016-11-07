<?php
require_once('master_validation.php');
require_once('config/connection.php');

	$parent		=strtoupper(trim($_POST['parent']));
	$orgcode	=strtoupper(trim($_POST['orgcode']));
	$orgname        =strtoupper(trim($_POST['orgname']));
	$orgalias       =strtoupper(trim($_POST['orgalias']));
	$orgtype	=strtoupper(trim($_POST['orgtype']));
	$orgadd		=trim($_POST['orgadd']);
	$orgcity	=strtoupper(trim($_POST['orgcity']));
	$orgcountry	=strtoupper(trim($_POST['orgcountry']));											
	$orgzip 	=strtoupper(trim($_POST['orgzip']));
	$orgtelp	=strtoupper(trim($_POST['orgtelp']));
	$orgfax		=strtoupper(trim($_POST['orgfax']));
	$orgdetail  =$_POST['orgdetail'];
	$alokasi	=strtoupper(trim($_POST['alokasi']));
	$noakun		=strtoupper(trim($_POST['noakun']));		
			
	//check if the same code and the same parent already exist
	$jum=0;//indicate not exist
	$exist=false;
	$s1="select count(*) from ".$dbname.".organisasi where kodeorganisasi='".$orgcode."' and induk='".$parent."'";
	$re1=mysql_query($s1);
	while($row=mysql_fetch_array($re1))
	{
		$jum=$row[0];
	}
	if($jum>0)
	  $exist=true;
	  
	if(!$exist){//then insert
		$st2="insert into ".$dbname.".organisasi
		      (kodeorganisasi,namaorganisasi,alamat,telepon,fax,wilayahkota,kodepos,induk,negara,tipe,lastuser,detail,alokasi,noakun,namaalias)
		values('".$orgcode."','".$orgname."','".$orgadd."','".$orgtelp."','".$orgfax."','".$orgcity."','".
		          $orgzip."','".$parent."','".$orgcountry."','".$orgtype."','".
				  $_SESSION['standard']['username']."',".$orgdetail.",'".$alokasi."','".$noakun."','".$orgalias."')";
	}
	else
	{//then update
	  $st2="update ".$dbname.".organisasi
	        set	namaorganisasi='".$orgname."',
				alamat	='".$orgadd."',
				telepon	='".$orgtelp."',
				fax	='".$orgfax."',
				wilayahkota	='".$orgcity."',
				kodepos	='".$orgzip."',
				negara	='".$orgcountry."',
				tipe	='".$orgtype."',
				detail  =".$orgdetail.",
				alokasi ='".$alokasi."',
				noakun  ='".$noakun."',
				namaalias  ='".$orgalias."',
				lastuser='".$_SESSION['standard']['username']."'
			 where kodeorganisasi	='".$orgcode."'
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
