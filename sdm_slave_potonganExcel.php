<?php
//ind
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
 $optTipePot=makeOption($dbname,'sdm_ho_component','id,name');
$_POST['method']==''?$method=$_GET['method']:$method=$_POST['method'];
$_POST['kodeorg']==''?$kodeorg=$_GET['kodeorg']:$kodeorg=$_POST['kodeorg'];
$_POST['periodegaji']==''?$periodegaji=$_GET['periodegaji']:$periodegaji=$_POST['periodegaji'];
$_POST['tipepotongan']==''?$tipepotongan=$_GET['tipepotongan']:$tipepotongan=$_POST['tipepotongan'];
$arrNmtp=array("0","Staff","3"=>"KBL","4"=>"KHT");


switch($method)
{
	case'excel':
	
		$iHead="select * from ".$dbname.".sdm_potonganht 
		where kodeorg='".$kodeorg."' and periodegaji='".$periodegaji."' and tipepotongan='".$tipepotongan."'";
		$nHead=mysql_query($iHead) or die (mysql_error($conn));
		$dHead=mysql_fetch_assoc($nHead);
	
		$stream="Kode Organisasi : ".$kodeorg."<br>";
		$stream.="Periode : ".$periodegaji."<br>";
		$stream.="Tipe Potongan : ".$optTipePot[$tipepotongan]."<br>";
                $lokasitugassub="Sub ".$_SESSION['lang']['lokasitugas'];
	
		$stream.="<br /><table class=sortable border=1 cellspacing=1>
			 <thead>
				<tr>
					<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['nourut']."</td> 
					<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['nik']."</td> 
					<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['namakaryawan']."</td> 
					<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['tipekaryawan']."</td> 
					<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['lokasitugas']."</td> 
                                        <td align=center bgcolor=#CCCCCC>".$lokasitugassub."</td> 
					<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['potongan']."</td> 
					<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['keterangan']."</td>
				</tr>";
		
		
		if($_SESSION['empl']['tipelokasitugas']=='KANWIL'){
                $iDet="select * from ".$dbname.".sdm_potongandt where periodegaji='".$periodegaji."' "
                   . "and kodeorg in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
                      and tipepotongan='".$tipepotongan."'  order by nik asc";// echo $str;exit();
        }else{
                $iDet="select * from ".$dbname.".sdm_potongandt where periodegaji='".$periodegaji."' "
                   . "and kodeorg='".$_SESSION['empl']['lokasitugas']."'
                      and tipepotongan='".$tipepotongan."'  order by nik asc";
        }
		
				
		
		$nDet=mysql_query($iDet) or die (mysql_error($conn));
		while($dDet=mysql_fetch_assoc($nDet))
		{
			
                    
			$wh="karyawanid='".$dDet['nik']."'";
			$optNik=makeOption($dbname,'datakaryawan','karyawanid,nik',$wh);
			$optNm=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',$wh);
			$optTp=makeOption($dbname,'datakaryawan','karyawanid,tipekaryawan',$wh);
                        
                        $optNmLokasitugasub=makeOption($dbname,'datakaryawan','karyawanid,subbagian',$wh);
                        
                        //$sKry="select subbagian from ".$dbname.".datakaryawan where nik='".$optNik[$dDet['nik']]."'";
                        //$qKry=mysql_query($sKry) or die(mysql_error());
                        //$rKry=mysql_fetch_assoc($qKry);
                                                                        
                        
			
			$no+=1;
			
			$stream.="<tr>
						<td>".$no."</td>
						<td>'".$optNik[$dDet['nik']]."</td>
						<td>".$optNm[$dDet['nik']]."</td>
						<td>".$arrNmtp[$optTp[$dDet['nik']]]."</td>
						<td>".$dDet['kodeorg']."</td>
                                                <td>".$optNmLokasitugasub[$dDet['nik']]."</td>                                                                                                                                
						<td>".number_format($dDet['jumlahpotongan'])."</td>
						<td>".$dDet['keterangan']."</td>
					</tr>";	
					$tot+=$dDet['jumlahpotongan'];
		}
		$stream.="<tr>
						<td colspan=5>Total</td>
						<td colspan=1>".number_format($tot)."</td>
					</tr></table>";	

/*$nop_="Laporan_Potongan_".$tipepotongan;
if(strlen($stream)>0)
{
	if ($handle = opendir('tempExcel')) {
		while (false !== ($file = readdir($handle))) {
		if ($file != "." && $file != "..") {
			@unlink('tempExcel/'.$file);
		}
		}	
		closedir($handle);
	}
	$handle=fopen("tempExcel/".$nop_.".xls",'w');
	if(!fwrite($handle,$stream))
	{
		echo "<script language=javascript1.2>
		parent.window.alert('Can't convert to excel format');
		</script>";
		exit;
	}
	else
	{
		echo "<script language=javascript1.2>
		window.location='tempExcel/".$nop_.".xls';
		</script>";
	}
	closedir($handle);
			break;
		
		
}
}*/



$stream.="</tbody></table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
$dte=date("Hms");
$nop_="Laporan_Potongan_".$dHead['kode'];
 $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
 gzwrite($gztralala, $stream);
 gzclose($gztralala);
 echo "<script language=javascript1.2>
	window.location='tempExcel/".$nop_.".xls.gz';
	</script>";
		break;
}





/*

$stream.="</tbody></table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
$dte=date("Hms");
$nop_="Laporan_Potongan_".$dHead['kode'];
 $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
 gzwrite($gztralala, $stream);
 gzclose($gztralala);
 echo "<script language=javascript1.2>
	window.location='tempExcel/".$nop_.".xls.gz';
	</script>";
		break;
}*/


?>