<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

$proses=$_POST['proses'];
$noTrans=$_POST['noTrans'];
$thisDate=date("Y-m-d");
$txtTgl=tanggalsystem($_POST['txtTgl']);
$statPost=$_POST['statPost'];

switch($proses)
{
	case'loadData':
		$limit=20;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		
		$ql2="select count(*) as jmlhrow from ".$dbname.".pabrik_rawatmesinht   order by `notransaksi` desc";// echo $ql2;
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}

		$slvhc="select * from ".$dbname.".pabrik_rawatmesinht  order by `notransaksi` desc limit ".$offset.",".$limit."";
		$qlvhc=mysql_query($slvhc) or die(mysql_error());
		$user_online=$_SESSION['standard']['userid'];
		while($rlvhc=mysql_fetch_assoc($qlvhc))
		{
		$no+=1;
		echo"
		<tr class=rowcontent>
		<td>".$no."</td>
		<td>".$rlvhc['notransaksi']."</td>
		<td>".tanggalnormal($rlvhc['tanggal'])."</td>
		<td>".$rlvhc['shift']."</td>
		<td>".$rlvhc['statasiun']."</td>
		<td>".$rlvhc['mesin']."</td>
		<td>".tanggalnormald($rlvhc['jammulai'])."</td>
		<td>".tanggalnormald($rlvhc['jamselesai'])."</td>";
			if($rlvhc['statPost']=='0')
			{
				if($rlvhc['updateby']!=$userOnline)
				{
				echo"<td><img src=images/skyblue/posting.png class=resicon  title='Edit' onclick=\"postThis('".$rlvhc['notransaksi']."');\">
				<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('pabrik_rawatmesinht','".$rlvhc['notransaksi']."','','pabrik_slavePemeliharaanPdf',event)\"></td>";
				 } else {
					 echo"
				<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('pabrik_rawatmesinht','".$rlvhc['notransaksi']."','','pabrik_slavePemeliharaanPdf',event)\"></td>";
				}
			}
			else
			{
			 echo"<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('pabrik_rawatmesinht','".$rlvhc['notransaksi']."','','pabrik_slavePemeliharaanPdf',event)\"></td>";
			}
		}	
		echo"
				 <tr><td colspan=9 align=center>
				".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
				<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
				<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
				</td>
				</tr>";
	break;
	case'postThis':
	$sCek="select statPost from ".$dbname.".pabrik_rawatmesinht where notransaksi='".$noTrans."'";
	$qCek=mysql_query($sCek) or die(mysql_error());
	$rCek=mysql_fetch_assoc($qCek);
	if($rCek['statPost']<1)
	{
		$sUpdate="update ".$dbname.".pabrik_rawatmesinht set statPost='1',postingby='".$_SESSION['standard']['userid']."',postingdate='".$thisDate."' where notransaksi='".$noTrans."' ";
		//echo"warning".$sUpdate;exit();
		//$qUp=mysql_query($sUpdate) or die(mysql_error());
		if(!mysql_query($sUpdate)) {
	    echo "DB Error : ".mysql_error();
	    exit;
		}
	}
	else
	{
		echo"warning:No Transaksi ini telah terposting";
		exit();
	}
	break;
	case'cariTransaksi':
	if(($noTrans=='')&&($txtTgl=='')&&($statPost==''))
	{
		$where="order by 'notransaksi' desc";
	}
	elseif(($noTrans!='')&&($txtTgl!='')&&($statPost!=''))
	{
		$where="where notransaksi='".$noTrans."' and tanggal='".$txtTgl."' and statPost='".$statPost."' ";
	}
	elseif(($noTrans!='')&&($txtTgl=='')&&($statPost==''))
	{
		$where="where notransaksi='".$noTrans."'";
	}
	elseif(($noTrans=='')&&($txtTgl!='')&&($statPost==''))
	{
		$where="where tanggal='".$txtTgl."'";
	}
	elseif(($noTrans=='')&&($txtTgl=='')&&($statPost!=''))
	{
		$hwere="where statPost='".$statPost."'";
	}
	elseif(($noTrans!='')&&($txtTgl=='')&&($statPost!=''))
	{
		$where="where notransaksi='".$noTrans."' and statPost='".$statPost."' ";
	}
	elseif(($noTrans=='')&&($txtTgl!='')&&($statPost!=''))
	{
		$where="where tanggal='".$txtTgl."' and statPost='".$statPost."' ";
	}
	elseif(($noTrans!='')&&($txtTgl!='')&&($statPost==''))
	{
		$where="where notransaksi='".$noTrans."' and tanggal='".$txtTgl."' ";
	}
	$sql="select * from ".$dbname.".pabrik_rawatmesinht  ".$where."";
	//echo "warning".$sql;exit();
	$query=mysql_query($sql) or die(mysql_error());
	while($rlvhc=mysql_fetch_assoc($query))
		{
		$no+=1;
		echo"
		<tr class=rowcontent>
		<td>".$no."</td>
		<td>".$rlvhc['notransaksi']."</td>
		<td>".tanggalnormal($rlvhc['tanggal'])."</td>
		<td>".$rlvhc['shift']."</td>
		<td>".$rlvhc['statasiun']."</td>
		<td>".$rlvhc['mesin']."</td>
		<td>".tanggalnormald($rlvhc['jammulai'])."</td>
		<td>".tanggalnormald($rlvhc['jamselesai'])."</td>";
			if($rlvhc['statPost']=='0')
			{
				if($rlvhc['updateby']!=$userOnline)
				{
				echo"<td><img src=images/skyblue/posting.png class=resicon  title='Edit' onclick=\"postThis('".$rlvhc['notransaksi']."');\">
				<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('pabrik_rawatmesinht','".$rlvhc['notransaksi']."','','pabrik_slavePemeliharaanPdf',event)\"></td>";
				 } else {
					 echo"
				<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=onclick=\"masterPDF('pabrik_rawatmesinht','".$rlvhc['notransaksi']."','','pabrik_slavePemeliharaanPdf',event);\"></td>";
				}
			}
			else
			{
			 echo"<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=onclick=\"masterPDF('pabrik_rawatmesinht','".$rlvhc['notransaksi']."','','pabrik_slavePemeliharaanPdf',event);\"></td>";
			}
		}	


	break;
	default:
	break;
}
?>