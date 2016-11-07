<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
//include('validator.php');
$tgl=$_POST['tanggal'];
$tgl2=substr($tgl,6,4).'-'.substr($tgl,3,2).'-'.substr($tgl,0,2);
//$product=$_GET['produk'];
//$sipb=$_GET['sipb'];

//$periode_tampil=substr($periode,5,2)."-".substr($periode,0,4);
$stream='';	
$stream.="
		<table cellspacing=0px border=1px style='border-color:#000000;' width=500px>
			<tr style='font-family:tahoma,Arial Narrow;font-size:14px;'>
			<td colspan=13 align=center>
			LAPORAN PENERIMAAN TBS INTERNAL PER UNIT PER TANGGAL
			</td>
			</tr>
			<tr  style='font-family:tahoma,Arial Narrow;font-size:12px;'>
			<td colspan=13  align=center>
			Tanggal	&nbsp &nbsp: ".$tgl."<br >
			</td>
			</tr>
			<tr style='font-family:tahoma,Arial Narrow;font-size:11px;background-color:#ededed'>
			<td align=center><b>UNIT</b></td>
			<td align=center><b>TOTAL(JJg)</b></td>
			<td align=center><b>TOTAL(Kg)</b></td>
			<td align=center><b>BJR(Kg)</b></td>
			</tr>";


$str="select UNITCODE,sum(JMLHJJG) as jlhjjg,sum(NETTO-KGPOTSORTASI) as netto
        from ".$dbname.".mstrxtbs where DATEOUT like '".$tgl2."%' and length(unitcode)=4 and productcode='40000003'
        and OUTIN=0 group by UNITCODE order by DATEOUT";
$no=0;

$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$no+=1;

$tjjg+=$bar->jlhjjg;
$tnetto+=$bar->netto;
$stream.="
	    <tr style='font-family:tahoma,Arial Narrow;font-size:11px;background-color:#ffffff'>
		<td>".$bar->UNITCODE."</td>
		<td align=right>".number_format($bar->jlhjjg,2,',','.')."</td>
		<td align=right>".number_format($bar->netto,2,',','.')."</td>
		<td align=right>".number_format($bar->netto/$bar->jlhjjg,2,',','.')."</td>
		</tr>
		";
}	  			
$stream.="<tr style='font-family:tahoma,Arial Narrow;font-size:11px;background-color:#efefef'>
			<td><b>TOTAL:</b></td>
			<td align=right>".number_format($tjjg,2,',','.')."</td>
			<td align=right>".number_format($tnetto,2,',','.')."</td>
			<td align=right>".@number_format($tnetto/$tjjg,2,',','.')."</td>
			</tr>";
$stream.="			
		</table>";
echo $stream;		
?>
