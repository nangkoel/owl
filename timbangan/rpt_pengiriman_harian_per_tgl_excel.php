<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
//include('validator.php');
$tgl=$_GET['tanggal'];
$tgl2=substr($tgl,6,4).'-'.substr($tgl,3,2).'-'.substr($tgl,0,2);
$product=$_GET['product'];
$stz="select PRODUCTNAME from ".$dbname.".msproduct where PRODUCTCODE='".$product."'";
$rez=mysql_query($stz);
while ($ba=mysql_fetch_object($rez)){
        $productname=$ba->PRODUCTNAME;
}
$namauser=$_SESSION['standard']['username'];
$stream='';	
$stream.="
                <table width=700px>
			<tr style='font-family:tahoma,Arial Narrow;font-size:14px;'>
                            <td colspan=10 align=center>Laporan Pengiriman Barang Selain CPO/PK</td>
			</tr>
			<tr  style='font-family:tahoma,Arial Narrow;font-size:12px;'>
                            <td colspan=10  align=center>Tanggal &nbsp: ".$tgl."<br ></td>
			</tr>
			<tr  style='font-family:tahoma,Arial Narrow;font-size:12px;'>
                            <td><br></td>
			</tr>
			<tr  style='font-family:tahoma,Arial Narrow;font-size:12px;'>
                            <td colspan=7 align=right>Dicetak Oleh :<br ></td>
                            <td colspan=3 align=right> ".$namauser."<br ></td>
			</tr>
			<tr  style='font-family:tahoma,Arial Narrow;font-size:12px;'>
                            <td colspan=7 align=right>Tanggal Cetak :<br ></td>
                            <td colspan=3 align=right> ".date('d-m-Y')."<br ></td>
			</tr>
			<tr  style='font-family:tahoma,Arial Narrow;font-size:12px;'>
                            <td colspan=7 align=right>Jam Cetak :<br ></td>
                            <td colspan=3 align=right> ".date('H:i:s')."<br ></td>
			</tr>
			<tr  style='font-family:tahoma,Arial Narrow;font-size:12px;'>
                            <td colspan=10>Product : ".$productname."<br ></td>
			</tr>
                </table>
		<table cellspacing=0px border=1px style='border-color:#000000;' width=700px>
			<tr style='font-family:tahoma,Arial Narrow;font-size:11px;background-color:#ededed'>
			<td align=center><b>No.</b></td>
			<td align=center><b>No.Tiket</b></td>
			<td align=center><b>Jam Masuk</b></td>
			<td align=center><b>Jam Keluar</b></td>
			<td align=center><b>No.Kendaraan</b></td>
			<td align=center><b>Driver</b></td>
			<td align=center><b>Penerima</b></td>
			<td align=center><b>Brutto</b></td>
			<td align=center><b>Tarra</b></td>
			<td align=center><b>Netto</b></td>
			</tr>";

$str="select TICKETNO2,DRIVER,VEHNOCODE,DATEIN,DATEOUT,PENERIMA,WEI1ST,WEI2ND,NETTO
        from ".$dbname.".mstrxtbs where DATEOUT like '".$tgl2."%' and productcode='".$product."'
        and OUTIN=0 order by DATEOUT";
		
//echo $str;
$no=0;
//$biaya=0;
//$tbiaya=0;
$netto=0;$tarra=0;$bruto=0;$jjg=0;
$tnetto=0;$ttarra=0;$tbruto=0;$tjjg=0;
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$no+=1;

	//$tbiaya+=$bar->biaya;
	$tnetto+=$bar->NETTO;$ttarra+=$bar->WEI1ST;$tbruto+=$bar->WEI2ND;$tjjg+=$bar->JMLHJJG;
	$dateinn=$bar->DATEIN;$dateout=$bar->DATEOUT;
	//$tgl=substr($_q,3,2)."-".substr($_q,0,2)."-".substr($_q,5,4);
	$tgl=substr($dateinn,8,2)."-".substr($dateinn,5,2)."-".substr($dateinn,0,4);
	$masuk=substr($dateinn,11,2).":".substr($dateinn,14,2).":".substr($dateinn,17,2);
	$keluar=substr($dateout,11,2).":".substr($dateout,14,2).":".substr($dateout,17,2);
	//echo $tgl;

$stream.="
	  <tr style='font-family:tahoma,Arial Narrow;font-size:11px;background-color:#ffffff'>
		<td>".$no."</td>          
		<td>".$bar->TICKETNO2."</td>
		<td align=center>".$masuk."</td>
		<td align=center>".$keluar."</td>
		<td>".$bar->VEHNOCODE."</td>
		<td>".$bar->DRIVER."</td>
		<td>".$bar->PENERIMA."</td>
		<td>".$bar->WEI1ST."</td>
		<td>".$bar->WEI2ND."</td>
		<td>".$bar->NETTO."</td>
		</tr>
		";
}	  			
$stream.="<tr style='font-family:tahoma,Arial Narrow;font-size:11px;background-color:#efefef'>
			<td align=center colspan=7 align=right><b>TOTAL :</b></td>
			<!--<td>".$tjjg."</td>-->
			<td>".$ttarra."</td>
			<td>".$tbruto."</td>
			<td>".$tnetto."</td>
			</tr>";
$stream.="			
		</table>";

//<font size=2>Printed:".date('d-m-Y H:i:s')."</font>";
$nop_="Pengiriman Barang"."_".$tgl;
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
