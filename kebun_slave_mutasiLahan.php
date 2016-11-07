<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');

$proses=$_POST['proses'];
$kdKbn=$_POST['kdKbn'];
$kdAfdeling=$_POST['kdAfdeling'];
$kdBlok=$_POST['kdBlok'];
$periodetm=$_POST['periodetm'];
$lokasi=substr($_SESSION['empl']['lokasitugas'],0,4);

	switch($proses)
	{
		case'getAfdeling':
		$optAfdling='';
		if($kdKbn=='')
		{
			//$optAfdling.="<option value=''></option>";
			echo"";
		}
		elseif($kdKbn!='')
		{
			$sAfdlng="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where induk='".$kdKbn."'";
		  //  echo"warning".$sAfdlng."__".$kdAfdeling;
			$qAfdeling=mysql_query($sAfdlng) or die(mysql_error());
			while($rAfdeling=mysql_fetch_assoc($qAfdeling))
			{
				if($kdAfdeling!='')
				{
					$sBlok="select count(kodeorg) as dor from ".$dbname.".setup_blok where kodeorg like '%".$rAfdeling['kodeorganisasi']."%' and statusblok='TBM'";
					//echo"warning".$sBlok;
					$qBlok=mysql_query($sBlok) or die(mysql_error());
					$rBlok=mysql_fetch_assoc($qBlok);
					$optAfdling.="<option value=".$rAfdeling['kodeorganisasi']." ".($rAfdeling['kodeorganisasi']==$kdAfdeling?'selected':'').">".$rAfdeling['namaorganisasi']." (".$rBlok['dor'].")</option>";
				}
				else
				{
					$sBlok="select count(kodeorg) as dor from ".$dbname.".setup_blok where kodeorg like '%".$rAfdeling['kodeorganisasi']."%' and statusblok='TBM'";
					//echo"warning".$sBlok;
					$qBlok=mysql_query($sBlok) or die(mysql_error());
					$rBlok=mysql_fetch_assoc($qBlok);
					$optAfdling.="<option value=".$rAfdeling['kodeorganisasi'].">".$rAfdeling['namaorganisasi']." (".$rBlok['dor'].")</option>";
				}
			}
		}
		echo $optAfdling;
		case'getBlok':
		if($kdAfdeling=='')
		{
			$optBlok="<option value=''></option>";
		}
		else
		{
			$sBlok="select kodeorg from ".$dbname.".setup_blok where kodeorg like '%".$kdAfdeling."%'";
			//echo"warning".$sBlok;
			$qBlok=mysql_query($sBlok) or die(mysql_error());
			while($rBlok=mysql_fetch_assoc($qBlok))
			{
				if($kdAfdeling!='')
				{
					$optBlok.="<option value=".$rBlok['kodeorg']." ".($rBlok['kodeorg']==$kdBlok?'selected':'').">".$rBlok['kodeorg']."</option>";
				}
				else
				{
					$optBlok.="<option value=".$rBlok['kodeorg'].">".$rBlok['kodeorg']."</option>";
				}
			}
		}
		echo $optBlok;
		break;		
		case'LoadData':
		$limit=20;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		
		$ql2="select count(*) as jmlhrow from ".$dbname.".setup_blok where `kodeorg` like '%".$lokasi."%' and statusblok='TM' order by `periodetm` desc";// echo $ql2;
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		
		
		$str="select periodetm,kodeorg from ".$dbname.".setup_blok where `kodeorg` like '%".$lokasi."%' and statusblok='TM' order by `periodetm` desc limit ".$offset.",".$limit."";
		if($res=mysql_query($str))
		{
			while($bar=mysql_fetch_object($res))
			{
				$no+=1;
				$spr="select induk from  ".$dbname.".organisasi where  kodeorganisasi='".$bar->kodeorg."'";
				$rep=mysql_query($spr) or die(mysql_error($conn));
				$bas=mysql_fetch_object($rep);
			
			$kdkebun=substr($bas->induk,0,4);
			if($bar->periodetm=='')
			{
				$period='';
			}
			else
			{
				$periode=explode("-",$bar->periodetm);
				$period=$periode[1]."-".$periode[0];
			}
			//echo $minute_selesai; exit();
			echo"<tr class=rowcontent id='tr_".$no."'>
			<td>".$no."</td>
			<td>".substr($bas->induk,0,4)."</td>
			<td>".$bas->induk."</td>
			<td>".$bar->kodeorg."</td>
			<td>".$period."</td>
			<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$kdkebun."','".$bas->induk."','".$bar->kodeorg."','".$period."');\"><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"deldata('".$bar->kodeorg."');\"></td>
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
		break;
		case'insert':
		//$regDate='(19|20)[0-9]{2}[- /.](0[1-9]|1[012])';
		if(($kdBlok=='')||($kdAfdeling=='')||($kdKbn=='')||($periodetm==''))
		{
			echo"warning:Please Complete The Form";
			exit();
		}
		if(strlen($periodetm)<7)
		{
			echo"warning:Periode is less then 7 Caracther";
			exit();
		}
		$periode=explode("-",$periodetm);
		$period=$periode[1]."-".$periode[0];
		//if(preg_match($regDate,$period))
		if(!preg_match ("((19|20)[0-9]{2}[- /.](0[1-9]|1[012]))", $period)) 
		{
			echo"warning:Please check the periode";
			exit();
		}
		$sCek="select periodetm from ".$dbname.".setup_blok where kodeorg='".$kdBlok."'";
		$qCek=mysql_query($sCek) or die(mysql_error());
		$rCek=mysql_fetch_assoc($qCek);
		if($rCek['periodetm']=='')
		{
			$sIns="update ".$dbname.".setup_blok  set statusblok='TM', periodetm='".$period."' where kodeorg='".$kdBlok."'";
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
		if(($kdBlok=='')||($kdAfdeling=='')||($kdKbn=='')||($periodetm==''))
		{
			echo"warning:Please Complete The Form";
			exit();
		}
		if(strlen($periodetm)<7)
		{
			echo"warning:Periode is less then 7 Caracther";
			exit();
		}
		$periode=explode("-",$periodetm);
		$period=$periode[1]."-".$periode[0];
		if(preg_match ("((19|20)[0-9]{2}[- /.](0[1-9]|1[012]))", $period)) 
		{
			
			$sUpd="update ".$dbname.".setup_blok set  periodetm='".$period."'  where  kodeorg='".$kdBlok."'";
		//	echo "warning:".$sUpd;exit();
			if(mysql_query($sUpd))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
		}
		else
		{
			echo"warning:Please check the periode".$period;
			exit();

		}
		break;
		case'delData':
		$sUpd="update ".$dbname.".setup_blok set  periodetm='',statusblok='TBM'  where  kodeorg='".$kdBlok."'";
		if(mysql_query($sUpd))
		echo"";
		else
		echo "DB Error : ".mysql_error($conn);
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
				
				//echo $minute_selesai; exit();
				echo"<tr class=rowcontent id='tr_".$no."'>
				<td>".$no."</td>
				<td id='nmorg_".$no."'>".$bas->namaorganisasi."</td>
				<td id='kpsits_".$no."'>".tanggalnormal($bar->tanggal)."</td>
				<td id='strt_".$no."'>".$bar->pagi."</td>
				<td id='end_".$no."'>".$bar->sore."</td>
				<td id='tglex_".$no."'>".$bar->catatan."</td>
				<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."');\"><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"deldata('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."');\"><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"printPDF('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."',event);\"></td>
				</tr>";
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