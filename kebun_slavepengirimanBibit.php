<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses=$_POST['proses'];
$codeOrg=$_POST['codeOrg'];
$tgl=tanggalsystem($_POST['tgl']);
$orgTujuan=$_POST['orgTujuan'];
$jmlh=$_POST['jmlh'];
$jnsBibit=$_POST['jnsBibit'];
$custId=$_POST['custId'];
$notrans=$_POST['notrans'];
$kdKeg=$_POST['kdKeg'];

	switch($proses)
	{
		//load data
		case'loadData':
		$thnBln=date("Y-m");
		OPEN_BOX();
		 echo"<fieldset>
<legend>".$_SESSION['lang']['list']."</legend>";
			echo"<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_pengirimanbbt','".$thnBln."','','kebun_slavepengirimanBibitPdf',event);\">&nbsp;<img onclick=dataKeExcel(event,'kebun_slave_pengirimanBibitExcel.php') src=images/excel.jpg class=resicon title='MS.Excel'>
			<table cellspacing=1 border=0 class=sortable>
		<thead>
<tr class=rowheader>
<td>".$_SESSION['lang']['notransaksi']."</td>
<td>".$_SESSION['lang']['kodeorg']."</td>
<td>".$_SESSION['lang']['tanggal']."</td>
<td>".$_SESSION['lang']['jenisbibit']."</td>
<td>".$_SESSION['lang']['jumlah']."</td>
<td>Action</td>
</tr>
</thead>
<tbody>
";
		$limit=10;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		
		$sql2="select count(*) as jmlhrow from ".$dbname.".kebun_pengirimanbbt order by `tanggal` desc";
		$query2=mysql_query($sql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		$slvhc="select * from ".$dbname.".kebun_pengirimanbbt order by `tanggal` desc limit ".$offset.",".$limit."";
		$qlvhc=mysql_query($slvhc) or die(mysql_error());
		while($res=mysql_fetch_assoc($qlvhc))
		{
			
		echo"
					<tr class=rowcontent>
					<td>". $res['notransaksi']."</td>
					<td>". $res['kodeorg']."</td>
					<td>". tanggalnormal($res['tanggal'])."</td>
					<td>". $res['jenisbibit']."</td>
					<td align='right'>". $res['jumlah']."</td>";
					echo"
					<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res['notransaksi']."');\">
					<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('". $res['notransaksi']."');\" >
					</td>
					</tr>";
					}
					echo"
					<tr><td colspan=5 align=center>
					".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
					<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
					<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
					</td>
					</tr>";
					echo"</table></fieldset>";
					CLOSE_BOX();
		break;
		
		// get no transaksi //
		case'generateNo':
		$tgl=  date('Ymd');
			$bln = substr($tgl,4,2);
			$thn = substr($tgl,0,4);
			
			$notransaksi=$codeOrg."/".date('Y')."/".date('m')."/";
			$ql="select `notransaksi` from ".$dbname.".`kebun_pengirimanbbt` where notransaksi like '%".$notransaksi."%' order by `notransaksi` desc limit 0,1";
			$qr=mysql_query($ql) or die(mysql_error());
			$rp=mysql_fetch_object($qr);
			$awal=substr($rp->notransaksi,-4,4);
			$awal=intval($awal);
			$cekbln=substr($rp->notransaksi,-7,2);
			$cekthn=substr($rp->notransaksi,-12,4);
			//echo "warning:".$awal;exit();
			if(($bln!=$cekbln)&&($thn!=$cekthn))
			{
			//echo $awal; exit();
					$awal=1;
			}
			else
			{
				  
					$awal++;
					// echo"warning:masuk".$awal;exit();
			}
			$counter=addZero($awal,4);
			$notransaksi=$codeOrg."/".$thn."/".$bln."/".$counter;
			
		
			echo $notransaksi;
		break;
		///insert data
		case'insert':
		//echo"warning:masuk";
		if(($notrans=='')||($tgl=='')||($jnsBibit=='')||($jmlh==''))
		{
			echo"warning:Please Complete The Form";
			exit();
		}
		$sCek="select notransaksi from ".$dbname.".kebun_pengirimanbbt where notransaksi='".$notrans."'";
		$qCek=mysql_query($sCek) or die(mysql_error());
		$rCek=mysql_num_rows($qCek);
		if($rCek<1)
		{
			$sIns="insert into ".$dbname.".kebun_pengirimanbbt (notransaksi, kodeorg, tanggal, jenisbibit, jumlah, orgtujuan, pembeliluar, kodekegiatan) values 
			('".$notrans."','".$codeOrg."','".$tgl."','".$jnsBibit."','".$jmlh."','".$orgTujuan."','".$custId."','".$kdKeg."')";
			//echo"warning:".$sIns;exit();
			if(mysql_query($sIns))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
		}
		else
		{
			echo"warning:This Notransaction already input";
			exit();
		}
		break;
		
		//getData
		case'getData':
		$sGet="select * from ".$dbname.".kebun_pengirimanbbt where notransaksi='".$notrans."'";
		$qGet=mysql_query($sGet) or die(mysql_error());
		$rGet=mysql_fetch_assoc($qGet);
		if($rGet['orgtujuan']=='')
		{
			$rGet['orgtujuan']=1;
		}
		if($rGet['pembeliluar']=='')
		{
			$rGet['pembeliluar']=1;
		}
		echo $rGet['kodeorg']."###".tanggalnormal($rGet['tanggal'])."###".$rGet['jenisbibit']."###".$rGet['jumlah']."###".$rGet['orgtujuan']."###".$rGet['pembeliluar']."###".$rGet['kodekegiatan'];
		break;
		
		case'update':
		if(($notrans=='')||($tgl=='')||($jnsBibit=='')||($jmlh==''))
		{
			echo"warning:Please Complete The Form";
			exit();
		}
		$sUp="update ".$dbname.".kebun_pengirimanbbt set tanggal='".$tgl."', jenisbibit='".$jnsBibit."', jumlah='".$jmlh."', orgtujuan='".$orgTujuan."', pembeliluar='".$custId."', kodekegiatan='".$kdKeg."' where notransaksi='".$notrans."'";
			//echo"warning:".$sIns;exit();
			if(mysql_query($sUp))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
		break;
		//hapus data
		case'delData':
		$sDel="delete from ".$dbname.".kebun_pengirimanbbt where notransaksi='".$notrans."'";
		if(mysql_query($sDel))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
		break;
		
		//cari transaksi
			case 'cari_transaksi':
		 OPEN_BOX();
		 echo"<fieldset>
<legend>".$_SESSION['lang']['result']."</legend>";
			echo"<div style=\"width:600px; height:450px; overflow:auto;\">
			<table cellspacing=1 border=0 class='sortable'>
		<thead>
<tr class=rowheader>
<td>".$_SESSION['lang']['notransaksi']."</td>
<td>".$_SESSION['lang']['kodeorg']."</td>
<td>".$_SESSION['lang']['tanggal']."</td>
<td>".$_SESSION['lang']['jenisbibit']."</td>
<td>".$_SESSION['lang']['jumlah']."</td>
<td>Action</td>
</tr>
</thead>
<tbody>
";
		if(isset($_POST['txtSearch']))
		{
			$txt_search=$_POST['txtSearch'];
			$txt_tgl=tanggalsystem($_POST['txtTgl']);
			$txt_tgl_a=substr($txt_tgl,0,4);
			$txt_tgl_b=substr($txt_tgl,4,2);
			$txt_tgl_c=substr($txt_tgl,6,2);
			$txt_tgl=$txt_tgl_a."-".$txt_tgl_b."-".$txt_tgl_c;
		}
		else
		{
			$txt_search='';
			$txt_tgl='';			
		}
			if($txt_search!='')
			{
				$where=" notransaksi LIKE  '%".$txt_search."%'";
			}
			elseif($txt_tgl!='')
			{
				$where.=" tanggal LIKE '".$txt_tgl."'";
			}
			elseif(($txt_tgl!='')&&($txt_search!=''))
			{
				$where.=" notransaksi LIKE '%".$txt_search."%' and tanggal LIKE '%".$txt_tgl."%'";
			}
		//echo $strx; exit();
		
				$strx="select * from ".$dbname.".kebun_pengirimanbbt where   ".$where." order by tanggal desc";
				
		//echo "warning:".$strx; exit();
		
		
			if($qry=mysql_query($strx))
			{
				$numrows=mysql_num_rows($qry);
				if($numrows<1)
				{
					echo"<tr class=rowcontent><td colspan=6>Not Found</td></tr>";
				}
				else
				{
					while($res=mysql_fetch_assoc($qry))
		{
			
		echo"
					<tr class=rowcontent>
					<td>". $res['notransaksi']."</td>
					<td>". $res['kodeorg']."</td>
					<td>". tanggalnormal($res['tanggal'])."</td>
					<td>". $res['jenisbibit']."</td>
					<td>". $res['jumlah']."</td>";
					echo"
					<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res['notransaksi']."');\">
					<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('". $res['notransaksi']."');\" >
				</td>
					</tr>";
					}

					echo"</tbody></table></div></fieldset>";
					
				}
			 }	
			else
			{
				echo "Gagal,".(mysql_error($conn));
			}	
			CLOSE_BOX();
		break;
		default:
		break;
	}
?>