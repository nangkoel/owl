<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];

$kdorg=$_POST['kdorg'];
$kdkeg=$_POST['kdkeg'];
$per=$_POST['per'];
$kdbarang=$_POST['kdbarang'];
if($proses=='excel')
{
	$kdorg=$_GET['kdorg'];
	$kdkeg=$_GET['kdkeg'];
	$per=$_GET['per'];
	$kdbarang=$_GET['kdbarang'];
}



/*if($kdorg!='')
	$kdorg=$kdorg;
else
	$kdorg=$_SESSION['empl']['lokasitugas'];
	
if($kdorg!='')
	$kdorg=$kdorg;
else*/
	

$nmbarang=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang');
$satuanbarang=makeOption($dbname,'log_5masterbarang','kodebarang,satuan');

//echo $daftargudang.____.$tgl1;
if($proses=='excel')
{
	$border="border=1";
	$bgcol="bgcolor=#CCCCCC ";
}



if($kdorg=='')
$kdorg="and kodeorg like '%".$_SESSION['empl']['lokasitugas']."%'";
else
$kdorg="and kodeorg like '%".$kdorg."%'";

if($kdbarang=='')
$kdbarang="";
else
$kdbarang="and a.kodebarang='".$kdbarang."'";

if($kdkeg=='')
$kdkeg="";
else
$kdkeg=" and kodekegiatan='".$kdkeg."'";



$stream="<table class=sortable cellspacing=1 ".$border." cellpadding=0>";
$stream.="<thead class=rowheader>
                 <tr class=rowheader>
				 	<td align=center>No</td>
					<td align=center>Blok</td>
					<td align=center>Luas (HA)</td>
					<td align=center>Kode Barang</td>
					<td align=center>Nama Barang</td>
					<td align=center>Jumlah Barang</td>
  				</tr></thead>";
				
$sql="SELECT sum(kwantitas) as kwantitas,sum(kwantitasha) as kwantitasha,a.kodebarang,namabarang,kodeorg,kodekegiatan
FROM ".$dbname.".kebun_pakai_material_vw a left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang
where tanggal like '%".$per."%' ".$kdorg." ".$kdkeg." ".$kdbarang."   group by kodeorg,a.kodebarang";



$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
//echo $sql;
while($bar=mysql_fetch_assoc($qry))
{
		$no+=1;
		$stream.="<tr class=rowcontent>
		<td>".$no."</td>
		<td>".$bar['kodeorg']."</td>
		<td>".$bar['kwantitasha']."</td>
		<td>".$bar['kodebarang']."</td>
		<td>".$nmbarang[$bar['kodebarang']]."</td>
		<td>".$bar['kwantitas']."</td>
		</tr>";
                $totalKuan+=$bar['kwantitas'];
}
$stream.="<tr class=rowcontent>
		<td colspan=5>Total</td>
		
		<td>".$totalKuan."</td>
		</tr>";
				
				

$stream.="<tbody></table>";
switch($proses)
{
######PREVIEW
	case 'preview':
		echo $stream;
    break;

######EXCEL	
	case 'excel':
		//$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="laporan_material_perblok".$tglSkrg;
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

	
}


?>












