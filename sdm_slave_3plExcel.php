<?php
//ind
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');


$_POST['method']==''?$method=$_GET['method']:$method=$_POST['method'];
$_POST['kom']==''?$kom=$_GET['kom']:$kom=$_POST['kom'];
$_POST['per']==''?$per=$_GET['per']:$per=$_POST['per'];
$_POST['org']==''?$org=$_GET['org']:$org=$_POST['org'];

$nmKom=makeOption($dbname,'sdm_ho_component','id,name');
$nmKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');
$nmOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
$optLok=makeOption($dbname,'datakaryawan','karyawanid,lokasitugas',"karyawanid='".$kar."'");

switch($method)
{
	case'excel':
	
		$stream.="Periode : ".$per."<br>";
		$stream.="Komponen : ".$nmKom[$kom]."<br>";
	
		$stream.="<br /><table class=sortable border=1 cellspacing=1>
			 <thead>
				<tr>
					<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['nourut']."</td> 
					<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['namakaryawan']."</td>
					<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['nik']."</td> 
					<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['lokasitugas']."</td> 
					<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['jumlah']."</td> 
				</tr>";
		
                if($_SESSION['empl']['regional']=='SULAWESI')
                {
                   if($_SESSION['empl']['tipelokasitugas']=='KANWIL')
                   {
                        $orgSort="and kodeorg in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";
                   }
                   else 
                   {
                        $orgSort="and kodeorg='".$org."' ";
                   } 
                }
                else
                {
                   $orgSort="and kodeorg='".$org."' ";
                }
                
                
		$iDet="select * from ".$dbname.".sdm_pendapatanlaindt where idkomponen='".$kom."' and periodegaji='".$per."' ".$orgSort." ";
		$nDet=mysql_query($iDet) or die (mysql_error($conn));
		while($dDet=mysql_fetch_assoc($nDet))
		{
			
			$optLokD=makeOption($dbname,'datakaryawan','karyawanid,lokasitugas',"karyawanid='".$dDet['karyawanid']."'");
			$nik=makeOption($dbname,'datakaryawan','karyawanid,nik',"karyawanid='".$dDet['karyawanid']."'");
			
			$no+=1;
			
			$stream.="<tr>
						<td>".$no."</td>
						<td>".$nmKar[$dDet['karyawanid']]."</td>
						<td>'".$nik[$dDet['karyawanid']]."</td>
						<td>".$nmOrg[$optLokD[$dDet['karyawanid']]]."</td>
						<td>".number_format($dDet['jumlah'])."</td>
					</tr>";	
					$tot+=$dDet['jumlah'];
		}
		$stream.="<tr>
						<td colspan=4>Total</td>
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
$nop_="Laporan_Pendapatan_Lain_".$nmKom[$kom].__.$per;
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