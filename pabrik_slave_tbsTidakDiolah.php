<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');

$proses=$_POST['proses'];
$kdORg=$_POST['kdOrg'];
$jmLh=$_POST['jmLh'];
$daTtgl=tanggalsystem($_POST['daTtgl']);
$lokasi=$_SESSION['empl']['lokasitugas'];

	switch($proses)
	{
		case'LoadData':
		echo"<table cellspacing=1 border=0>
		<thead>
		<tr class=rowheader>
		<td>No.</td>
		<td>".$_SESSION['lang']['kodeorg']."</td>
		<td>".$_SESSION['lang']['namaorganisasi']."</td> 
		<td>".$_SESSION['lang']['tanggal']."</td>
		<td>".$_SESSION['lang']['jumlah']."</td>	 
		<td>Action</td>
		</tr>
		</thead>
		<tbody>";
		//echo"warning:masuk";exit();
		$limit=10;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		
		$ql2="select count(*) as jmlhrow from ".$dbname.".pabrik_sisatbsolah where `kodeorg` = '".$lokasi."'  order by `tanggal` desc";// echo $ql2;
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		
		
		$str="select * from ".$dbname.".pabrik_sisatbsolah where `kodeorg` = '".$lokasi."'  order by `tanggal` desc  limit ".$offset.",".$limit."";
		//echo "warning:".$str;exit();
		if($res=mysql_query($str))
		{
		while($bar=mysql_fetch_object($res))
		{
		$spr="select namaorganisasi from  ".$dbname.".organisasi where  kodeorganisasi='".$bar->kodeorg."'";
		$rep=mysql_query($spr) or die(mysql_error($conn));
		$bas=mysql_fetch_object($rep);
		$no+=1;
		
		//echo $minute_selesai; exit();
		echo"<tr class=rowcontent id='tr_".$no."'>
		<td>".$no."</td>
		<td>".$bar->kodeorg."</td>
		<td>".$bas->namaorganisasi."</td>
		<td>".tanggalnormal($bar->tanggal)."</td>
		<td align=right>".number_format($bar->jumlah,2)."</td>
		<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."','".$bar->jumlah."');\"><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"deldata('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."');\"></td>
		</tr>";
		}	 	
		echo"
		<tr><td colspan=7 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";   	
		}	
		else
		{
		echo " Gagal,".(mysql_error($conn));
		}	
		echo"</tbody></table>";
		break;
		case'insert':
		if(($kdORg=='')||($jmLh=='')||($daTtgl==''))
		{
			echo"warning:Please Complete The Form";
			exit();
		}
		$tglCek=explode("-",$_POST['daTtgl']);
		$thnSkrng=date("Y");
		$blnSkrng=date("m");
		if($tglCek[2]!=$thnSkrng)
		{
			echo"warning: Please use this year, ".$thnSkrng."";
			exit();
		}
		$sCek="select kodeorg,tanggal from ".$dbname.".pabrik_sisatbsolah where kodeorg='".$kdORg."' and tanggal='".$daTtgl."'";
		$qCek=mysql_query($sCek) or die(mysql_error());
		$rCek=mysql_num_rows($qCek);
		if($rCek<1)
		{
			$sIns="insert into ".$dbname.".pabrik_sisatbsolah (kodeorg, tanggal, jumlah) values ('".$kdORg."','".$daTtgl."','".$jmLh."')";
			if(mysql_query($sIns))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
		}
		else
		{
			echo"warning:Data Already Entry";
			exit();
		}
		break;
		case'showData':
		$sql="select catatan,pagi,sore from ".$dbname.".kebun_curahhujan where kodeorg='".$kdORg."' and tanggal='".$daTtgl."'";
		//echo"warning".$sql;
		$query=mysql_query($sql) or die(mysql_error());
		$res=mysql_fetch_assoc($query);
		echo $res['catatan']."###".$res['pagi']."###".$res['sore'];
		break;
		case'update':
		if(($kdORg=='')||($daTtgl=='')||($jmLh==''))
		{
			echo"warning:Please Complete The Form";
			exit();
		}
			$sUpd="update ".$dbname.".pabrik_sisatbsolah set  jumlah='".$jmLh."' where  kodeorg='".$kdORg."' and tanggal='".$daTtgl."'";
			//echo "warning:".$sUpd;exit();
			if(mysql_query($sUpd))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
		
		break;
		case'delData':
		$sDel="delete from ".$dbname.".pabrik_sisatbsolah where  kodeorg='".$kdORg."' and tanggal='".$daTtgl."'";
		if(mysql_query($sDel))
		echo"";
		else
		echo "DB Error : ".mysql_error($conn);
		break;
		
		case'cariData':
		echo"<div style='overflow:auto; width:450px; height:450px;'><table cellspacing=1 border=0>
		<thead>
		<tr class=rowheader>
		<td>No.</td>
		<td>".$_SESSION['lang']['kodeorg']."</td>
		<td>".$_SESSION['lang']['namaorganisasi']."</td> 
		<td>".$_SESSION['lang']['tanggal']."</td>
		<td>".$_SESSION['lang']['jumlah']."</td>	 
		<td>Action</td>
		</tr>
		</thead>
		<tbody>";
			if(($kdORg!='')&&($daTtgl!=''))
			{
				$where=" kodeorg='".$kdORg."' and tanggal='".$daTtgl."'";
			}
			elseif($kdORg!='')
			{
				$where=" kodeorg='".$kdORg."'";
			}
			elseif($daTtgl!='')
			{
				$where=" tanggal='".$daTtgl."' and kodeorg = '".$lokasi."'";
			}
			
			elseif(($kdORg=='')&&($daTtgl==''))
			{
				echo"warning:Please Insert Data";	
				exit();
			}
			$sCek="select * from ".$dbname.".pabrik_sisatbsolah where ".$where."";
			//echo"warning:".$sCek; 
			$qCek=mysql_query($sCek) or die(mysql_error());
			$rCek=mysql_num_rows($qCek);
			if($rCek>0)
			{	
				
				$str="select * from ".$dbname.".pabrik_sisatbsolah where ".$where." order by tanggal desc";
				//echo"warning:".$str; exit();
				if($res=mysql_query($str))
				{
				while($bar=mysql_fetch_object($res))
				{
				$spr="select * from  ".$dbname.".organisasi where  kodeorganisasi='".$bar->kodeorg."'";
				$rep=mysql_query($spr) or die(mysql_error($conn));
				$bas=mysql_fetch_object($rep);
				$no+=1;
				
				//echo $minute_selesai; exit();
				echo"<tr class=rowcontent id='tr_".$no."'>
					<td>".$no."</td>
					<td>".$bar->kodeorg."</td>
					<td>".$bas->namaorganisasi."</td>
					<td>".tanggalnormal($bar->tanggal)."</td>
					<td align=right>".number_format($bar->jumlah,2)."</td>
					<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."','".$bar->jumlah."');\"><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"deldata('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."');\"></td>
					</tr>";
				}	 	 
				    	
				}	
				else
				{
				echo " Gagal,".(mysql_error($conn));
				}	
			}
			else
			{
				echo"<tr class=rowcontent><td colspan=6 align=center>Not Found</td></tr>";
			}
			echo"</tbody></table></div>";
		
		break;
		default:
		break;
	}

?>