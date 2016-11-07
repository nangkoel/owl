<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');


$proses=$_POST['proses'];

$blok=$_POST['blok'];
$noSpb=$_POST['noSpb'];
$tanggal=tanggalsystem($_POST['tgl']);
$bjrHsl=$_POST['bjr'];
$jjngHsl=intval($_POST['jjng']);
$brondolanHsl=intval($_POST['brondolan']);
$user_online=$_SESSION['standard']['userid'];
$kdOrg=$_POST['kdOrg'];
$idDiv=$_POST['idDiv'];
$matang=$_POST['matang'];
$mentah=$_POST['mentah'];
$busuk=$_POST['busuk'];
$lwtmatang=$_POST['lwtmatang'];
$kdOrg=$_POST['kdOrg'];
$oldBlok=$_POST['oldBlok'];
$kgwb=$_POST['kgwb'];
if($kgwb=='')
{
    $kgwb=0;
}
$sReg="select distinct regional from ".$dbname.".bgt_regional_assignment where kodeunit='".$_SESSION['empl']['lokasitugas']."'";
$qReg=mysql_query($sReg) or die(mysql_error($conn));
$rReg=mysql_fetch_assoc($qReg);

	switch($proses)
	{
		case'generateNo':
		$tgl=  date('Ymd');
		$bln = substr($tgl,4,2);
		$thn = substr($tgl,0,4);
		$lokasi=$_SESSION['empl']['lokasitugas'];
		$lokasi=substr($lokasi,0,4);
		$scOrg="select disticnt tipe from ".$dbname.".organisasi where kodeorganisasi='".$lokasi."'";
		$qcOrg=mysql_query($scOrg) or die(mysql_error());
		$rcOrg=mysql_fetch_assoc($qcOrg);
	
		if(($rcOrg['tipe']=="KEBUN")||($rcOrg['tipe']=="KANWIL"))
		{
			$nospb=$lokasi."/".date('Y')."/".date('m')."/";
			$ql="select `nospb` from ".$dbname.".`kebun_spbht` where nospb like '%".$nospb."%' order by `nospb` desc limit 0,1";
			$qr=mysql_query($ql) or die(mysql_error());
			$rp=mysql_fetch_object($qr);
			$awal=substr($rp->nospb,-4,4);
			$awal=intval($awal);
			$cekbln=substr($rp->nospb,-7,2);
			$cekthn=substr($rp->nospb,-12,4);
			//echo "warning:".$awal;exit();
			//if(($bln!=$cekbln)&&($thn!=$cekthn))
			if($thn!=$cekthn)
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
			$nospb=$lokasi."/".$thn."/".$bln."/".$counter;
			
			
			echo $nospb;
		}
		else
		{
			echo"warning:Anda Bukan di Kebun atau Traksi";
			exit();
		}
		break;
		
		case'amblBjr':
		$sStpBlok="select bjr from ".$dbname.".kebun_5bjr where kodeorg='".$blok."' and tahunproduksi=".substr($_POST['periode'],0,4);
		$qStpBlok=mysql_query($sStpBlok) or die(mysql_error());
		$rStpBlok=mysql_fetch_assoc($qStpBlok);
		echo $rStpBlok['bjr'];
		break;
		
		case'cekData':
		//echo"warning:masuk";
/*		$_SESSION['temp']['nSpb']=$noSpb;
		$lokasi=$_SESSION['empl']['lokasitugas'];
		$lokasi=substr($lokasi,0,4);
*/
                if($kgwb!='')
                {
                    if($rReg['regional']!='KALTIM')
                    {
                        $kgwb=0;
                    }
                }
		$sCek="select nospb from ".$dbname.".kebun_spbht where nospb='".$noSpb."'"; //echo "warning".$sCek;nospb
		$qCek=mysql_query($sCek) or die(mysql_error());
		$rCek=mysql_fetch_row($qCek);
		if($rCek<1)
		{
			$sIns="insert into ".$dbname.".kebun_spbht (`nospb`, `kodeorg`, `tanggal`,`updateby`) values ('".$noSpb."','".$kdOrg."','".$tanggal."','".$user_online."')"; //echo"warning:".$sIns;exit();
			if(mysql_query($sIns))
			{
				$kgBjr=($jjngHsl*$bjrHsl);
				$sDetIns="insert into ".$dbname.".kebun_spbdt (nospb, blok, jjg, bjr, brondolan, mentah, busuk, matang, lewatmatang,kgbjr) values ('".$noSpb."','".$blok."','".$jjngHsl."','".$bjrHsl."','".$brondolanHsl."','".$mentah."','".$busuk."','".$matang."','".$lwtmatang."','".$kgBjr."')";
				if(mysql_query($sDetIns))
				echo"";
				else
				echo "DB Error : ".mysql_error($conn);
			}
			else
			{
				echo "DB Error : ".mysql_error($conn);
			}
		}
		else
			{
                        $cekPost="select distinct posting from ".$dbname.".kebun_spbht where nospb='".$noSpb."'";
                        
                        $qcekPost=mysql_query($cekPost) or die(mysql_error($conn));
                        $rCek=mysql_fetch_assoc($qcekPost);
                        if($rCek['posting']!=0)
                        {
                            exit("Error:Nospb Sudah Posting");
                        }
			$kgBjr=($jjngHsl*$bjrHsl);
			$sDetIns="insert into ".$dbname.".kebun_spbdt (nospb, blok, jjg, bjr, brondolan, mentah, busuk, matang, lewatmatang,kgbjr,kgwb)
                                values ('".$noSpb."','".$blok."','".$jjngHsl."','".$bjrHsl."','".$brondolanHsl."','".$mentah."','".$busuk."','".$matang."','".$lwtmatang."','".$kgBjr."','".$kgwb."')";
                        //exit("Error:".$sDetIns);
				if(mysql_query($sDetIns))
				echo"";
				else
				echo "DB Error : ".mysql_error($conn);
			}
		break;
		
		case'loadNewData':
		$lokasi=$_SESSION['empl']['lokasitugas'];
		$limit=20;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		
		$ql2="select count(*) as jmlhrow from ".$dbname.".kebun_spbht where `kodeorg`='".$_SESSION['empl']['lokasitugas']."' order by tanggal desc";// echo $ql2;
		//ql2="select count(*) as jmlhrow from ".$dbname.".kebun_spbht where `updateby`='".$_SESSION['standard']['userid']."' order by `tanggal` desc";// echo $ql2;
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		
		
		$slvhc="select * from ".$dbname.".kebun_spbht where `kodeorg`='".$_SESSION['empl']['lokasitugas']."' order by tanggal desc limit ".$offset.",".$limit."";
		//$slvhc="select * from ".$dbname.".kebun_spbht where `updateby`='".$_SESSION['standard']['userid']."' order by `tanggal` desc limit ".$offset.",".$limit."";
	 	//echo $slvhc;
		$qlvhc=mysql_query($slvhc) or die(mysql_error());
		
		while($rlvhc=mysql_fetch_assoc($qlvhc))
		{
		$no+=1;
		$tgl=explode('-',tanggalnormal($rlvhc['tanggal']));
		$tglThn=$tgl[2];
		$tglBln=$tgl[1];
		$periode=$tglThn."-".$tglBln;
                    $scek="select distinct * from ".$dbname.".kebun_spbdt where nospb='".$rlvhc['nospb']."' and substr(nospb,9,6)<>left(blok,6)";
                    $qcek=mysql_query($scek) or die(mysql_error($conn));
                    $rcek=mysql_num_rows($qcek);
                    
			echo"
			<tr class=rowcontent>
			<td>".$no."</td>
			<td>".$rlvhc['nospb']."</td>
			<td>".tanggalnormal($rlvhc['tanggal'])."</td>
			<td>".$rlvhc['kodeorg']."</td>";
                        
			 if($rlvhc['updateby']==$user_online){
			echo"
			<td>
			<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['nospb']."',
			'".tanggalnormal($rlvhc['tanggal'])."','1','".$periode."','".$rcek."');\">
			<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['nospb']."');\" >	
			<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_spbht','".$rlvhc['nospb']."','','kebun_spbPdf',event)\"></td>";
			 } else if($_SESSION['empl']['kodejabatan']=='98'){
                             echo"<td>
			<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['nospb']."',
			'".tanggalnormal($rlvhc['tanggal'])."','1','".$periode."','".$rcek."');\">
			<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['nospb']."');\" >	
			<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_spbht','".$rlvhc['nospb']."','','kebun_spbPdf',event)\"></td>";
                         }else{
			echo"
			<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_spbht','".$rlvhc['nospb']."','','kebun_spbPdf',event)\"></td>";
			}
	
		
		}
		echo"</tr>
		<tr class=rowheader><td colspan=5 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
		break;
		
		case'delData':
		$sCek="select posting from ".$dbname.".kebun_spbht where nospb='".$noSpb."'";
		$qCek=mysql_query($sCek) or die(mysql_error());
		$rCek=mysql_fetch_assoc($qCek);
		if($rCek['posting']=='1')
		{
			echo"warning:Already Post This No. SPB";
			exit();
		}
		
		$sql="delete from ".$dbname.".kebun_spbht where nospb='".$noSpb."' ";
		//echo"warning:".$sql;
		if(mysql_query($sql))
		{
			$sqlDet="delete from ".$dbname.".kebun_spbdt where nospb='".$noSpb."'";
			if(mysql_query($sqlDet))
			echo"";
			else
			 echo "DB Error : ".mysql_error($conn);
		}		
		else
		{
			echo "DB Error : ".mysql_error($conn);
		}
		break;
		
		case'cariNospb':
		$lokasi=$_SESSION['empl']['lokasitugas'];
		$limit=20;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
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
				$where="and nospb LIKE  '%".$txt_search."%'";
			}
			elseif($txt_tgl!='')
			{
				$where.="and tanggal LIKE '".$txt_tgl."'";
			}
			elseif(($txt_tgl!='')&&($txt_search!=''))
			{
				$where.="and nospb LIKE '%".$txt_search."%' and tanggal LIKE '%".$txt_tgl."%'";
			}
		//echo $strx; exit();
		if($txt_search==''&&$txt_tgl=='')
		{
                        $slvhc="select * from ".$dbname.".kebun_spbht where kodeorg='".$lokasi."' ".$where." order by tanggal desc limit ".$offset.",".$limit."";
                        $ql2="select count(*) jmlhrow from ".$dbname.".kebun_spbht 	where  kodeorg='".$lokasi."' ".$where." order by tanggal,nospb desc";			
//			$slvhc="select * from ".$dbname.".kebun_spbht where `updateby`='".$_SESSION['standard']['userid']."' ".$where." order by nospb desc limit ".$offset.",".$limit."";
//			$ql2="select count(*) jmlhrow from ".$dbname.".kebun_spbht 	where  `updateby`='".$_SESSION['standard']['userid']."' ".$where." order by nospb desc";			 
		}
		else
		{
                    $slvhc="select * from ".$dbname.".kebun_spbht where  kodeorg='".$lokasi."' ".$where." order by tanggal desc limit ".$offset.",".$limit."";
			$ql2="select count(*) jmlhrow from ".$dbname.".kebun_spbht 	where   kodeorg='".$lokasi."' ".$where." order by tanggal,nospb desc";	
//                        $slvhc="select * from ".$dbname.".kebun_spbht where `updateby`='".$_SESSION['standard']['userid']."' ".$where." order by nospb desc limit ".$offset.",".$limit."";
//			$ql2="select count(*) jmlhrow from ".$dbname.".kebun_spbht 	where  `updateby`='".$_SESSION['standard']['userid']."' ".$where." order by nospb desc";	
		}
		
		
		//$ql2="select count(*) as jmlhrow from ".$dbname.".kebun_spbht where `kodeorg`='".$lokasi."' order by `nospb` desc";// echo $ql2;
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		
		
		//$slvhc="select * from ".$dbname.".kebun_spbht where `kodeorg`='".$lokasi."' order by `nospb` desc limit ".$offset.",".$limit."";
		$qlvhc=mysql_query($slvhc) or die(mysql_error());
		
		while($rlvhc=mysql_fetch_assoc($qlvhc))
		{
		$no+=1;
                $tgl=explode('-',tanggalnormal($rlvhc['tanggal']));
		$tglThn=$tgl[2];
		$tglBln=$tgl[1];
		$periode=$tglThn."-".$tglBln;
                $scek="select distinct * from ".$dbname.".kebun_spbdt where nospb='".$rlvhc['nospb']."' and substr(nospb,9,6)<>left(blok,6)";
                $qcek=mysql_query($scek) or die(mysql_error($conn));
                $rcek=mysql_num_rows($qcek);
			echo"
			<tr class=rowcontent>
			<td>".$no."</td>
			<td>".$rlvhc['nospb']."</td>
			<td>".tanggalnormal($rlvhc['tanggal'])."</td>
			<td>".$rlvhc['kodeorg']."</td>";
			 if($rlvhc['updateby']==$user_online){
			echo"
			<td>
			<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['nospb']."',
			'".tanggalnormal($rlvhc['tanggal'])."','1','".$periode."','".$rcek."');\">
			<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['nospb']."');\" >	
			<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_spbht','".$rlvhc['nospb']."','','kebun_spbPdf',event)\"></td>";
			 } else if($_SESSION['empl']['kodejabatan']=='98'){
                             echo"<td>
			<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['nospb']."',
			'".tanggalnormal($rlvhc['tanggal'])."','1','".$periode."','".$rcek."');\">
			<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['nospb']."');\" >	
			<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_spbht','".$rlvhc['nospb']."','','kebun_spbPdf',event)\"></td>";
                         }else{
			echo"
			<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_spbht','".$rlvhc['nospb']."','','kebun_spbPdf',event)\"></td>";
			}
		
		}
		echo"</tr>
		<tr class=rowheader><td colspan=5 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariData(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariData(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
		break;
		
		case'updateData':
                    if($kgwb!='')
                {
                    if($rReg['regional']!='KALTIM')
                    {
                        $kgwb=0;
                    }
                }
		$data=$_POST;
		$sCek="select distinct nospb from ".$dbname.".kebun_spbht where nospb='".$data['noSpb']."'";
		//echo "warning".$sCek;exit();
		$qCek=mysql_query($sCek) or die(mysql_error());
		$rCek=mysql_num_rows($qCek);
		if($rCek>0)
		{
			if(($data['jjng']=='') or ($data['brondolan']=='') or ($data['bjr']=='') ) {
					echo "Error : Tolong lengkap data detail, data tidak boleh kosong";
					exit();
				}
                                $cekPost="select distinct posting from ".$dbname.".kebun_spbht where nospb='".$data['noSpb']."' and posting=1";
                                $qcekPost=mysql_query($cekPost) or die(mysql_error($conn));
                                $rCek=mysql_fetch_assoc($qcekPost);
                                if($rCek['posting']!=0)
                                {
                                    exit("Error:Nospb Sudah Posting");
                                }
				$kgBjr=($jjngHsl*$bjrHsl);
				$sUpHead="update ".$dbname.".kebun_spbht set tanggal='".$tanggal."' where nospb='".$data['noSpb']."'";
				//echo "warning".$sUpHead;//exit();
				if(mysql_query($sUpHead))
				{
							$sUpDetail="update ".$dbname.".kebun_spbdt set
                                                                   blok='".$blok."',jjg='".$jjngHsl."',bjr='".$bjrHsl."',brondolan='".$brondolanHsl."',mentah='".$mentah."',
                                                                   busuk='".$busuk."',matang='".$matang."',lewatmatang='".$lwtmatang."',kgbjr='".$kgBjr."',kgwb='".$kgwb."'
                                                                   where nospb='".$noSpb."' and blok='".$oldBlok."'";
					//echo "warning:".$sUpDetail;exit();
					if(mysql_query($sUpDetail))
					{
						echo"";
					}
					else
					{ 
						echo "DB Error : ".mysql_error($conn);
					}

				}
				else
				{ 
					echo "DB Error : ".mysql_error($conn);
				}
		}
		else
		{
			
				$kgBjr=($jjngHsl*$bjrHsl);
				$sDetIns="insert into ".$dbname.".kebun_spbdt (nospb, blok, jjg, bjr, brondolan, mentah, busuk, matang, lewatmatang,kgbjr) values ('".$noSpb."','".$blok."','".$jjngHsl."','".$bjrHsl."','".$brondolanHsl."','".$mentah."','".$busuk."','".$matang."','".$lwtmatang."','".$kgBjr."')";
				//echo "warning:".$sDetIns;exit();
				if(mysql_query($sDetIns))
				echo"";
				else
				echo "DB Error : ".mysql_error($conn);
			
		}
		break;
		case'getDivData':
		//echo"warning:masuk";
		if($idDiv=='')
		{
			$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";	
			$sORg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='AFDELING' and kodeorganisasi LIKE '%".$kdOrg."%'";
			$qOrg=mysql_query($sORg) or die(mysql_error());
			while($rOrg=mysql_fetch_assoc($qOrg))
			{
				$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";	
			}
			echo $optOrg;
		}
		else
		{
			
			$sORg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='AFDELING' and kodeorganisasi LIKE '%".$kdOrg."%'"; //echo"warning:".$sORg;
			$qOrg=mysql_query($sORg) or die(mysql_error());
			while($rOrg=mysql_fetch_assoc($qOrg))
			{
				$optOrg.="<option value=".$rOrg['kodeorganisasi']." ".($rOrg['kodeorganisasi']==$idDiv?'selected':'').">".$rOrg['namaorganisasi']."</option>";	
			}
			echo $optOrg;
		}
		break;
		
		case'addSession':
		$_SESSION['temp']['nSpb']=$noSpb;
		echo "warning:".$_SESSION['temp']['nSpb'];
		exit();
		break;
		case'delDetail':
			$sqlDet="delete from ".$dbname.".kebun_spbdt where nospb='".$noSpb."' and blok='".$blok."'";
			if(mysql_query($sqlDet))
			echo"";
			else
			 echo "DB Error : ".mysql_error($conn);
		break;
		
		default:
		break;
	}


?>