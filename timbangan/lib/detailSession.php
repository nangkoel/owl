<?php
/*function setEmplSession($conn,$userid,$dbname)//load all data from user_empl to session
{
	$strses1="select * from ".$dbname.".user_empl where userid=".$userid;
	$resses1=mysql_query($strses1);
	if(mysql_num_rows($resses1)>0)
	{
		while($barses1=mysql_fetch_object($resses1))
		{
		  $_SESSION['empl']['name']=$barses1->name;
		  $_SESSION['empl']['sex']=$barses1->sex;
		  $_SESSION['empl']['birthday']=$barses1->birthday;
		  $_SESSION['empl']['birthplace']=$barses1->birthplace;
		  $_SESSION['empl']['address']=$barses1->address;
		  $_SESSION['empl']['idnum']=$barses1->idnum; 			//'identity num/no ktp',
		  $_SESSION['empl']['idtype']=$barses1->idtype; 		//'jenis identitas ktp sim pasport',
		  $_SESSION['empl']['nationality']=$barses1->nationality;
		  $_SESSION['empl']['religion']=$barses1->religion;
		  $_SESSION['empl']['mstatus']=$barses1->mstatus;		//'status pajak/k1=kawin 1 anak',
		  $_SESSION['empl']['ethnic']=$barses1->ethnic;
		  $_SESSION['empl']['title']=$barses1->title;			//'id jabatan',
		  $_SESSION['empl']['level']=$barses1->level;			//'golongan',
		  $_SESSION['empl']['emplcode']=$barses1->emplcode;		//'unit pemberi kerja',
		  $_SESSION['empl']['emplloc']=$barses1->emplloc;		//'lokasi kerja',
		  $_SESSION['empl']['poh']=$barses1->poh;				//'point of hire',
		  $_SESSION['empl']['signdate']=$barses1->signdate;		//'tgl masuk',
		  $_SESSION['empl']['resigndate']=$barses1->resigndate;	//'tgl keluar',
		  $_SESSION['empl']['status']=$barses1->status;			//'employment is payroll active or not/employee scorrs',
		  $_SESSION['empl']['email']=$barses1->email;
		  $_SESSION['empl']['phone']=$barses1->phone;
		  $_SESSION['empl']['emptype']=$barses1->emptype;		//'bentuk ikatan',
		  $_SESSION['empl']['departement']=$barses1->departement;//'Id departement',
		  $_SESSION['empl']['lastupdate']=$barses1->lastupdate;	//date CURRENT_TIMESTAMP,
		  $_SESSION['empl']['lastuser']=$barses1->lastuser;		//'userid last update',
		  $_SESSION['empl']['usercode']=$barses1->usercode;		//'userid last update',

		}
	}
}*/
function getPrivillageType($conn,$dbname)//privillage type
{
	$strses2="select access_name from ".$dbname.".access_type
	          where status=1";
	$resses2=mysql_query($strses2);
	if(mysql_num_rows($resses2)>0)
	{
		while($barses2=mysql_fetch_object($resses2)){
		    $_SESSION['access_type']=$barses2->access_name;
		}

		if(isset($_SESSION['access_type']))
		 	return true;
		else
		    return false;
	}
	else
	{
		return false;
	}
}

function getPrivillages($conn,$username,$dbname)//get user privillages
{
	$strses3="select * from ".$dbname.".auth
	          where uname='".$username."'
			  and status=1";
	$resses3=mysql_query($strses3);
	if(mysql_num_rows($resses3)>0)
	{
	  	$x=0;
		while($barses3=mysql_fetch_object($resses3))
		{
			if($x==0)
			   $c_o=$barses3->menuid;
			else
			   $c_o.=",".$barses3->menuid;
		  $_SESSION['priv'][$x]=$barses3->menuid;
		  $x+=1;
		}
		$_SESSION['allpriv']=$c_o;
     if(count($_SESSION['priv'])>0)
	 	return true;
	 else
	    return false;
	}
	else
		return false;
}
/*
function setEmployer($conn,$dbname)
{
	$strses4="select * from ".$dbname.".org
	          where code='".$_SESSION['empl']['emplcode']."'";
	$resses4=mysql_query($strses4);
	if(mysql_num_rows($resses4)>0)
	{
		while($barses4=mysql_fetch_object($resses4))
		{
			$_SESSION['org']['code']=$barses4->code;
			$_SESSION['org']['emplname']=$barses4->emplname;
			$_SESSION['org']['address']=$barses4->address;
			$_SESSION['org']['telp']=$barses4->telp;
			$_SESSION['org']['city']=$barses4->city;
			$_SESSION['org']['zipcode']=$barses4->zipcode;
			$_SESSION['org']['active_period']=$barses4->active_period;
			$_SESSION['org']['parent']=$barses4->parent;
		}
	}
	else
	$_SESSION['org']=NULL;

	if(isset($_SESSION['org']['active_period']))
	{
		$strses5="select * from ".$dbname.".user_period
		          where period='".$_SESSION['org']['active_period']."'";
		$resses5=mysql_query($strses5);
		if(mysql_num_rows($resses5)>0)
		{
			while($barses5=mysql_fetch_object($resses5))
			{
				$_SESSION['org']['period']['start']=str_replace("-","",$barses5->start);
				$_SESSION['org']['period']['end']=str_replace("-","",$barses5->end);
			}
		}
		else
		$_SESSION['org']['period']='';
	}
}*/
?>

