<?php
#ind
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$div=$_POST['div'];
$per=$_POST['per'];
$ulatKir=$_POST['ulat'];


if($proses=='excel')
{	
	$div=$_GET['div'];
	$per=$_GET['per'];
	$ulatKir=$_GET['ulat'];
}


$optNmOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');

if(($proses=='preview')or($proses=='excel')or($proses=='pdf'))
{

    if(($div=='' or $per==''))
	{
		echo"Error: Field Was Empty"; 
		exit;
    }
}	


$arrNm=array("jlhdarnatrima"=>"Darna Trima","jlhsetothosea"=>"Setothosea Asigna","jlhsetothosea"=>"Setora Nitens","jlhulatkantong"=>"Ulat Kantong");


	
	
$blnthn=explode("-",$per);
$jumHari = cal_days_in_month(CAL_GREGORIAN, $blnthn[1], $blnthn[0]);

$tgl1=$per.'-01';
$tgl2=$per.'-'.$jumHari;

$arrTgl=rangeTanggal($tgl1,$tgl2);

$nmBulan=strtoupper(numToMonth($blnthn[1],'I','long'));



if($proses=='excel')
{
	$border="border=1";
	$bgCol="bgcolor=#CCCCCC";
	
	
}
else
{
	$border="border=0";
	$bgCol="";
}


//print_r($_SESSION['org']);

$stream.=" ".$_SESSION['org']['namaorganisasi']."<br />Quality Control<br /><br /><b>MONITORING SENSUS ULAT API</b><br /><br />".$optNmOrg[$div]."
";


	$stream.="
			<table class=sortable ".$border." cellspacing=1 cellpadding=0>
<thead>
					 <tr>
						<td rowspan=2 align=center ".$bgCol." >".$_SESSION['lang']['blok']."</td>
						<td colspan=".$jumHari." align=center ".$bgCol." >".$nmBulan." ".$blnthn[0]."</td>
					 </tr>
					 <tr>";
						foreach($arrTgl as $lstTgl=>$tgl)
						{
							$stream.="<td align=center ".$bgCol.">".substr($tgl,8,2)."</td>";
						}
								 
					 $stream.="</tr></thead></tbody>";
					 
	
	if($ulatKir=='jlhdarnatrima')
	{
		
		$sql="select a.jenissensus,a.kodeblok,a.tanggal as tanggal,sum(pokokdiamati) as pokokdiamati,sum(luasdiamati) as luasdiamati,
		sum(jlhdarnatrima) as jlhdarnatrima
		from ".$dbname.".kebun_qc_ulatapiht a left join ".$dbname.".kebun_qc_ulatapidt b on a.tanggal=b.tanggal and a.kodeblok=b.kodeblok
		where a.tanggal like '%".$per."%' and a.kodeblok like '%".$div."%'
		 group by a.kodeblok,a.tanggal order by a.kodeblok asc";
	}
	else if($ulatKir=='jlhsetothosea')
	{
		$sql="select a.jenissensus,a.kodeblok,a.tanggal as tanggal,sum(pokokdiamati) as pokokdiamati,sum(luasdiamati) as luasdiamati,
		sum(jlhsetothosea) as jlhsetothosea
		from ".$dbname.".kebun_qc_ulatapiht a left join ".$dbname.".kebun_qc_ulatapidt b on a.tanggal=b.tanggal and a.kodeblok=b.kodeblok
		where a.tanggal like '%".$per."%' and a.kodeblok like '%".$div."%'
		 group by a.kodeblok,a.tanggal order by a.kodeblok asc";
	}
	elseif($ulatKir=='jlhsetoranitens')
	{
		$sql="select a.jenissensus,a.kodeblok,a.tanggal as tanggal,sum(pokokdiamati) as pokokdiamati,sum(luasdiamati) as luasdiamati,
		sum(jlhsetoranitens) as jlhsetoranitens
		from ".$dbname.".kebun_qc_ulatapiht a left join ".$dbname.".kebun_qc_ulatapidt b on a.tanggal=b.tanggal and a.kodeblok=b.kodeblok
		where a.tanggal like '%".$per."%' and a.kodeblok like '%".$div."%'
		 group by a.kodeblok,a.tanggal order by a.kodeblok asc";
	}
	else
	{
		$sql="select a.jenissensus,a.kodeblok,a.tanggal as tanggal,sum(pokokdiamati) as pokokdiamati,sum(luasdiamati) as luasdiamati,sum(jlhdarnatrima) as jlhdarnatrima,
		sum(jlhsetothosea) as jlhsetothosea,sum(jlhsetoranitens) as jlhsetoranitens 
		from ".$dbname.".kebun_qc_ulatapiht a left join ".$dbname.".kebun_qc_ulatapidt b on a.tanggal=b.tanggal and a.kodeblok=b.kodeblok
		where a.tanggal like '%".$per."%' and a.kodeblok like '%".$div."%'
		 group by a.kodeblok,a.tanggal order by a.kodeblok asc";
	}
		
		//echo $sql;			 
	/*$sql="select a.jenissensus,a.kodeblok,a.tanggal as tanggal,sum(pokokdiamati) as pokokdiamati,sum(luasdiamati) as luasdiamati,sum(jlhdarnatrima) as jlhdarnatrima,
		sum(jlhsetothosea) as jlhsetothosea,sum(jlhsetoranitens) as jlhsetoranitens 
		from ".$dbname.".kebun_qc_ulatapiht a left join ".$dbname.".kebun_qc_ulatapidt b on a.tanggal=b.tanggal and a.kodeblok=b.kodeblok
		where a.tanggal like '%".$per."%' and a.kodeblok like '%".$div."%' and 
		 group by a.kodeblok,a.tanggal order by a.kodeblok asc";*/
	//echo $sql;	 
	$res=mysql_query($sql) or die (mysql_error($conn));
	while($bar=mysql_fetch_assoc($res))
	{
		
		$blok[$bar['kodeblok']]=$bar['kodeblok'];
		$jenis[$bar['kodeblok']]=$bar['jenissensus'];
		$pokokdiamati[$bar['kodeblok'].$bar['jenissensus'].$bar['tanggal']].=$bar['pokokdiamati'];
		$jlhdarnatrima[$bar['kodeblok'].$bar['jenissensus'].$bar['tanggal']]=$bar['jlhdarnatrima'];
		$jlhsetothosea[$bar['kodeblok'].$bar['jenissensus'].$bar['tanggal']]=$bar['jlhsetothosea'];
		$jlhsetoranitens[$bar['kodeblok'].$bar['jenissensus'].$bar['tanggal']]=$bar['jlhsetoranitens'];
		
	}
	
	$bgcolor=array("sebelum"=>"bgcolor=#FF0000","pengendalian"=>"bgcolor=#FFCC00","sesudah"=>"bgcolor=00FF00");
	
	foreach($blok as $lstblok){
		
		
		
		$stream.="<tr class=rowcontent>
				<td>".$lstblok."</td>";
					foreach($arrTgl as $lstTgl=>$i)
					{
						//<td ".$bgcolor[$jenis[$lstblok]].">".$pokokdiamati[$lstblok.$jenis[$lstblok].$i]."</td>
						/*<td ".$bgcolor[$jenis[$lstblok]].">".$jlhdarnatrima[$lstblok.$jenis[$lstblok].$i]."</td>
						<td ".$bgcolor[$jenis[$lstblok]].">".$jlhsetothosea[$lstblok.$jenis[$lstblok].$i]."</td>
						<td ".$bgcolor[$jenis[$lstblok]].">".$jlhsetoranitens[$lstblok.$jenis[$lstblok].$i]."</td>
						*/
						
						
						//echo $ulatKir;
						
						
						$jumPkk=$pokokdiamati[$lstblok.$jenis[$lstblok].$i];
						$ulat=$jlhdarnatrima[$lstblok.$jenis[$lstblok].$i]+$jlhsetothosea[$lstblok.$jenis[$lstblok].$i]+$jlhsetoranitens[$lstblok.$jenis[$lstblok].$i];
						
						$jenisPembagi=$jenis[$lstblok];
						//echo $jenis[$bar['kodeblok']];
						
						if($jenisPembagi=="sebelum")
						{
							//$hasil=$ulat/$jumPkk;
							$hasil=$ulat;
						}
						else if($jenisPembagi=="pengendalian")
						{
							//$hasil=$jumPkk;
							$hasil=$ulat;
						}
						else if($jenisPembagi=="sesudah")
						{
							//$hasil=$ulat/$jumPkk;
							$hasil=$ulat;
						}
						
						
						if($hasil==0)
						{
							$hasil='';
							$stream.="<td>".number_format($hasil,2)."</td>";
						}
						else
						{
							$stream.="<td align=right ".$bgcolor[$jenis[$lstblok]].">".number_format($hasil,2)."</td>";
						}	
						
					}
						
				
			$stream.="</tr>";
	}
$stream.="</tbody></table><br /><br />Keterangan :<br />";	



$stream.="<table class=sortable ".$border." cellspacing=1 cellpadding=0>
<thead>";
					
$stream.="<tr ".$bgCol." class=rowcontent>
			<td align=center>Ulat</td>
			<td >Kreteria</td>
			<td align=center>Max</td>
			<td align=center>Min</td>
			
</tr></thead>";

			$x="select * from ".$dbname.".kebun_qc_5ulatapi where ulat='".$ulatKir."'";
			$y=mysql_query($x) or die (mysql_error($conn));
			while($z=mysql_fetch_assoc($y))
			{
				$stream.="<tr class=rowcontent>";
					$stream.="<td>".$arrNm[$z['ulat']]."</td>";
					$stream.="<td>".ucfirst($z['kret'])."</td>";
					$stream.="<td>".$z['minu']."</td>";
					$stream.="<td>".$z['maxu']."</td>";
				$stream.="</tr>";
			}
			
		
			
$stream.="</table>";

	
/*echo"<pre>";
print_r($arrTgl);	
echo"</pre>";	*/

#######################################################################
############PANGGGGGGGGGGGGGGGGGGILLLLLLLLLLLLLLLLLLLLLLLLLL###########   
#######################################################################

switch($proses)
{
######HTML
	case 'preview':
		echo $stream;
    break;

######EXCEL	
	case 'excel':
		$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="Laporan_QC_Ulat_Api".$per;
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
		}           
		break;

	
	
	default:
	break;
}

?>