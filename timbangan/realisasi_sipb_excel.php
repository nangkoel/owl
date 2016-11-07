<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
//$mill=$_GET['mill'];
$product=$_GET['product'];
$sipb=$_GET['sipb'];
$mill='SOGM';
//$periode_tampil=substr($periode,5,2)."-".substr($periode,0,4);
$stream='';	
$stream.="
		<table cellspacing=0px border=1px style='border-color:#000000;' width=700px>
			<tr style='font-family:tahoma,Arial Narrow;font-size:14px;'>
			<td colspan=11 align=center>
			REALISASI PENGIRIMAN BARANG
			</td>
			</tr>
			<tr  style='font-family:tahoma,Arial Narrow;font-size:12px;'>
			<td colspan=11  align=left>
			Mill	&nbsp &nbsp: ".$mill."<br >
			No. SIPB: ".$sipb."
			</td>
			</tr>
			<tr style='font-family:tahoma,Arial Narrow;font-size:11px;background-color:#ededed'>
			<td align=center><b>No.Urut</b></td>
			<td align=center><b>Tanggal</b></td>
			<td align=center><b>Angkutan</b></td>
			<td align=center><b>No.Tiket</b></td>
			<td align=center><b>Jam Masuk</b></td>
			<td align=center><b>Jam Keluar</b></td>
			<td align=center><b>Nama Supir</b></td>
			<td align=center><b>No. Polisi</b></td>
			<td align=center><b>Timbang Kosong</b></td>
			<td align=center><b>Timbang Isi</b></td>
			<td align=center><b>Netto</b></td>
			</tr>";

$str="select datein,trpcode,ticketno2,datein,dateout,driver,vehnocode,wei1st,wei2nd,netto
        from ".$dbname.".mstrxtbs where productcode='".$product."'
        and sipbno='".$sipb."' and OUTIN=0 order by dateout";

//echo $str;  
$no=0;
//$biaya=0;
//$tbiaya=0;
$netto=0;$tarra=0;$bruto=0;
$tnetto=0;$ttarra=0;$tbruto=0;
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$no+=1;

	//$tbiaya+=$bar->biaya;
	$tnetto+=$bar->netto;$ttarra+=$bar->wei1st;$tbruto+=$bar->wei2nd;
	$dateinn=$bar->datein;$dateout=$bar->dateout;
	//$tgl=substr($_q,3,2)."-".substr($_q,0,2)."-".substr($_q,5,4);
	$tgl=substr($dateinn,8,2)."-".substr($dateinn,5,2)."-".substr($dateinn,0,4);
	$masuk=substr($dateinn,11,2).":".substr($dateinn,14,2).":".substr($dateinn,17,2);
	$keluar=substr($dateout,11,2).":".substr($dateout,14,2).":".substr($dateout,17,2);
	//echo $tgl;
	/*
if($bar->deskripsi=='')
	    $bar->deskripsi='-';
	if($bar->bengkel=='')
	    $bar->bengkel='-';
*/
	$trpcode=$bar->trpcode;	
	$st="select TRPCODE,TRPNAME from ".$dbname.".msvendortrp where TRPCODE='".$trpcode."'";
	$re=mysql_query($st);
	while ($ba=mysql_fetch_array($re)){
		$trpcode2=$ba[1];
	}
	
	//echo $st;
$stream.="
	    <tr style='font-family:tahoma,Arial Narrow;font-size:11px;background-color:#ffffff'>
		<td align=right>".$no."</td>
		<td>".$tgl."</td>
		<td>".$trpcode2."</td>
		<td>".$bar->ticketno2."</td>
		<td>".$masuk."</td>
		<td>".$keluar."</td	>
		<td>".$bar->driver."</td>
		<td>".$bar->vehnocode."</td>
		<td>".$bar->wei1st."</td>
		<td>".$bar->wei2nd."</td>
		<td>".$bar->netto."</td>
		</tr>
		";
}	  			
$stream.="<tr style='font-family:tahoma,Arial Narrow;font-size:11px;background-color:#efefef'>
			<td align=center colspan=8 align=right><b>TOTAL:</b></td>
			<td>".$ttarra."</td>
			<td>".$tbruto."</td>
			<td>".$tnetto."</td>
			</tr>";
$stream.="			
		</table>

<font size=2>Printed:".date('d-m-Y H:i:s')."</font>";
#$nop_="Realisasi_Pengiriman_Barang_".$mill."-".$sipb;
$nop_="Realisasi_Pengiriman_Barang_";
if(strlen($stream)>0)
{
 @unlink("excel/".$nop_.".xls");
 $handle=fopen("excel/".$nop_.".xls",'w');
 if(!fwrite($handle,$stream))
 {
  echo "<script language=javascript1.2>
        parent.window.alert('Can't convert to excel format');
        self.close();
        </script>";
  exit(0);
 }
 else
 {
  echo "<script language=javascript1.2>
        window.location='excel/".$nop_.".xls';
        </script>";
 }
}
?>
