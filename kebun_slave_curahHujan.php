<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');

$proses=$_POST['proses'];
$kdORg=$_POST['kdOrg'];
$daTpagi=$_POST['daTpagi'];
$daTsore=$_POST['daTsore'];
$note=$_POST['note'];
$daTtgl=tanggalsystem($_POST['daTtgl']);
$lokasi=$_SESSION['empl']['lokasitugas'];

$jam1=$_POST['jm1'].":".$_POST['mn1'].":00";
$jam2=$_POST['jm2'].":".$_POST['mn2'].":00";

$mulaipagi=$_POST['jmp'].":".$_POST['mmp'].":00";
$selesaipagi=$_POST['jsp'].":".$_POST['msp'].":00";
$mulaisore=$_POST['jms'].":".$_POST['mms'].":00";
$selesaisore=$_POST['jss'].":".$_POST['mss'].":00";
$periodeAkutansi=$_SESSION['org']['period']['tahun']."-".$_SESSION['org']['period']['bulan'];
/*jmp mmp
jsp msp

jms mms
jss mss*/


	switch($proses)
	{
		case'LoadData':
		$limit=10;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		
		$ql2="select count(*) as jmlhrow from ".$dbname.".kebun_curahhujan where `kodeorg` like  '".$lokasi."%' order by `tanggal` desc";// echo $ql2;
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		
		
		$str="select * from ".$dbname.".kebun_curahhujan where `kodeorg` like '".$lokasi."%' order by tanggal desc limit ".$offset.",".$limit."";
		if(mysql_query($str))
		{
                        $res=mysql_query($str);
			while($bar=mysql_fetch_object($res))
			{
			$spr="select namaorganisasi from  ".$dbname.".organisasi where  kodeorganisasi='".$bar->kodeorg."'";
			$rep=mysql_query($spr) or die(mysql_error($conn));
			$bas=mysql_fetch_object($rep);
			$no+=1;
                        $sGp="select DISTINCT sudahproses from ".$dbname.".sdm_5periodegaji where kodeorg='".$bar->kodeorg."' and `periode`='".substr($bar->tanggal,0,7)."'";
                        $qGp=mysql_query($sGp) or die(mysql_error());
                        $rGp=mysql_fetch_assoc($qGp);
			
			//echo $minute_selesai; exit();
			echo"<tr class=rowcontent id='tr_".$no."'>
			<td>".$no."</td>
			<td id='nmorg_".$no."'>".$bas->namaorganisasi."</td>
			<td id='kpsits_".$no."'>".tanggalnormal($bar->tanggal)."</td>
			<td id='strt_".$no."'>".$bar->pagi."</td>
			<td id='end_".$no."'>".$bar->sore."</td>
			<td id='tglex_".$no."'>".$bar->catatan."</td><td>";
                        if((substr($bar->tanggal,7)==$periodeAkutansi)||($rGp['sudahproses']==0)){
				echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."');\"><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"deldata('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."');\"><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"printPDF('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."',event);\">";
		    }else{
				
			}
			echo"</td></tr>";
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
		break;
		
		
		case'insert':
		
		//exit("Error:HAHAHA");
		if(($kdORg=='')||($daTtgl==''))
		{
			echo"warning:Please Complete The Form";
			exit();
		}
		$tglCek=explode("-",$_POST['daTtgl']);
		$thnSkrng=date("Y");
		$blnSkrng=date("m");
		
		$sCek="select kodeorg,tanggal from ".$dbname.".kebun_curahhujan where kodeorg='".$kdORg."' and tanggal='".$daTtgl."'";
		$qCek=mysql_query($sCek) or die(mysql_error());
		$rCek=mysql_num_rows($qCek);
		if($rCek<1)
		{
			$sIns="insert into ".$dbname.".kebun_curahhujan (kodeorg, tanggal, pagi, sore, catatan,mulaipagi,selesaipagi,mulaisore,selesaisore) 
			values ('".$kdORg."','".$daTtgl."','".$daTpagi."','".$daTsore."','".$note."','".$mulaipagi."','".$selesaipagi."','".$mulaisore."','".$selesaisore."')";
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
		$sql="select * from ".$dbname.".kebun_curahhujan where kodeorg='".$kdORg."' and tanggal='".$daTtgl."'";
		//echo"warning".$sql;
		$query=mysql_query($sql) or die(mysql_error());
		$res=mysql_fetch_assoc($query);
		echo $res['catatan']."###".$res['pagi']."###".$res['sore']
			 ."###".substr($res['mulaipagi'],0,2)."###".substr($res['mulaipagi'],3,2)."###".substr($res['selesaipagi'],0,2)."###".substr($res['selesaipagi'],3,2)
			 ."###".substr($res['mulaisore'],0,2)."###".substr($res['mulaisore'],3,2)."###".substr($res['selesaisore'],0,2)."###".substr($res['selesaisore'],3,2);	 
		break;
		
		
		
		
		
		case'update':
		if(($kdORg=='')||($daTtgl==''))
		{
			echo"warning:Please Complete The Form";
			exit();
		}
			$sUpd="update ".$dbname.".kebun_curahhujan set  pagi='".$daTpagi."', sore='".$daTsore."', catatan='".$note."',
			mulaipagi='".$mulaipagi."',selesaipagi='".$selesaipagi."',mulaisore='".$mulaisore."',selesaisore='".$selesaisore."'		
			where  kodeorg='".$kdORg."' and tanggal='".$daTtgl."'";
			//echo "warning:".$sUpd;exit();
			if(mysql_query($sUpd))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
		
		break;
		case'delData':
		$sDel="delete from ".$dbname.".kebun_curahhujan where  kodeorg='".$kdORg."' and tanggal='".$daTtgl."'";
		if(mysql_query($sDel))
		echo"";
		else
		echo "DB Error : ".mysql_error($conn);
		break;
		case'CekData':
		if(eregi("e$",$lokasi))
		{
			echo"";
		}
		else
		{
			echo"warning:You Not In Estate";
			exit();
		}
		break;
		case'cariData':
		if(eregi("e$",$lokasi))
		{

			$limit=10;
			$page=0;
			if(isset($_POST['page']))
			{
			$page=$_POST['page'];
			if($page<0)
			$page=0;
			}
			$offset=$page*$limit;
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
			$sCek="select * from ".$dbname.".kebun_curahhujan where ".$where."";
			//echo"warning:".$sCek; 
			$qCek=mysql_query($sCek) or die(mysql_error());
			$rCek=mysql_num_rows($qCek);
			if($rCek>0)
			{
				$ql2="select count(*) as jmlhrow from ".$dbname.".kebun_curahhujan where ".$where." order by `tanggal` desc";// echo $ql2;
				$query2=mysql_query($ql2) or die(mysql_error());
				while($jsl=mysql_fetch_object($query2)){
				$jlhbrs= $jsl->jmlhrow;
				}
				
				
				$str="select * from ".$dbname.".kebun_curahhujan where ".$where." order by tanggal desc limit ".$offset.",".$limit."";
				//echo"warning:".$str; exit();
				if($res=mysql_query($str))
				{
				while($bar=mysql_fetch_object($res))
				{
				$spr="select * from  ".$dbname.".organisasi where  kodeorganisasi='".$bar->kodeorg."'";
				$rep=mysql_query($spr) or die(mysql_error($conn));
				$bas=mysql_fetch_object($rep);
				$no+=1;
                                $sGp="select DISTINCT sudahproses from ".$dbname.".sdm_5periodegaji where kodeorg='".$bar->kodeorg."' and `periode`='".substr($bar->tanggal,0,7)."'";
                                $qGp=mysql_query($sGp) or die(mysql_error());
                                $rGp=mysql_fetch_assoc($qGp);
				
				//echo $minute_selesai; exit();
				echo"<tr class=rowcontent id='tr_".$no."'>
				<td>".$no."</td>
				<td id='nmorg_".$no."'>".$bas->namaorganisasi."</td>
				<td id='kpsits_".$no."'>".tanggalnormal($bar->tanggal)."</td>
				<td id='strt_".$no."'>".$bar->pagi."</td>
				<td id='end_".$no."'>".$bar->sore."</td>
				<td id='tglex_".$no."'>".$bar->catatan."</td>><td>";
				if((substr($bar->tanggal,0,7)==$periodeAkutansi)||($rGp['sudahproses']==0)){
				echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."');\"><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"deldata('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."');\"><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"printPDF('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."',event);\">";
		        }else{
			    }
				echo"</td></tr>";
				}	 	 
				echo"
				<tr class=rowheader><td colspan=7 align=center>
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
			}
			else
			{
				echo"<tr class=rowcontent><td colspan=7 align=center>Not Found</td></tr>";
			}
		}
		else
		{
			echo"warning:You Not In Estate";
			exit();
		}
	
		break;
		default:
		break;
	}

?>
