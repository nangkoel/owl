<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
//include('validator.php');
$tgl=$_GET['tanggal'];
$code=$_GET['code'];
$nama=$_GET['name'];
$tgl2=substr($tgl,6,4).'-'.substr($tgl,3,2).'-'.substr($tgl,0,2);
//$product=$_GET['produk'];
//$sipb=$_GET['sipb'];

//$periode_tampil=substr($periode,5,2)."-".substr($periode,0,4);
$stream='';	
$stream.="
		<table cellspacing=0px border=1px style='border-color:#000000;' width=700px>
			<tr style='font-family:tahoma,Arial Narrow;font-size:14px;'>
			<td colspan=14 align=center>
			LAPORAN PENERIMAAN ".$nama." PER TANGGAL
			</td>
			</tr>
			<tr  style='font-family:tahoma,Arial Narrow;font-size:12px;'>
			<td colspan=14  align=center>
			Tanggal	&nbsp &nbsp: ".$tgl."<br >
			</td>
			</tr>
			<tr style='font-family:tahoma,Arial Narrow;font-size:11px;background-color:#ededed'>
			<td align=center><b>No.SPB</b></td>
			<td align=center><b>No.Tiket</b></td>
			<td align=center><b>Suppllier</b></td>
			<td align=center><b>Afd</b></td>
			<td align=center><b>Supir</b></td>
			<td align=center><b>NOPOL</b></td>
			<td align=center><b>Jam Masuk</b></td>
			<td align=center><b>Jam Keluar</b></td>
			<td align=center><b>Tahun Tanam</b></td>
			<td align=center><b>Jml.JJG</b></td>
			<td align=center><b>1st Weigh</b></td>
			<td align=center><b>2nd Weigh</b></td>
			<td align=center><b>Netto</b></td>
                                                            <td align=center><b>Potongan</b></td>
                                                            <td align=center><b>Normal</b></td>
			<td align=center><b>BJR</b></td>
			</tr>";

$str="select SPBNO,TICKETNO2,UNITCODE,DIVCODE,DRIVER,VEHNOCODE,DATEIN,DATEOUT,TAHUNTANAM,JMLHJJG,WEI1ST,WEI2ND,NETTO,TRPCODE,KGPOTSORTASI
        from ".$dbname.".mstrxtbs where DATEOUT like '".$tgl2."%' and productcode='".$code."'
        and OUTIN=0 order by DATEOUT";
//echo $str;
$no=0;
//$biaya=0;
//$tbiaya=0;
$netto=0;$tarra=0;$bruto=0;$jjg=0;
$tnetto=0;$ttarra=0;$tbruto=0;$tjjg=0;
$res=mysql_query($str);
$xtjjg=0;
$twig=0;
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
                    $tpot+=$bar->KGPOTSORTASI;
	$trpcode=$bar->TRPCODE;	
	//$st="select TRPCODE,TRPNAME from ".$dbname.".msvendortrp where TRPCODE='".$trpcode."'";
	$st="select BUYERCODE,BUYERNAME from ".$dbname.".msvendorbuyer where BUYERCODE='".$trpcode."'";
	$re=mysql_query($st);
	while ($ba=mysql_fetch_array($re)){
		$trpcode2=$ba[1];
	}
	if ($bar->UNITCODE=='')
		$unitcode2=$trpcode2;
	else
		$unitcode2=$bar->UNITCODE;
		
$stream.="
	    <tr style='font-family:tahoma,Arial Narrow;font-size:11px;background-color:#ffffff'>
		<td align=right>".$bar->SPBNO."</td>
		<td>".$bar->TICKETNO2."</td>
		<td>".$unitcode2."</td>
		<td align=center>".$bar->DIVCODE."</td>
		<td>".$bar->DRIVER."</td>
		<td>".$bar->VEHNOCODE."</td>
		<td align=center>".$masuk."</td>
		<td align=center>".$keluar."</td	>
		<td align=center>".$bar->TAHUNTANAM."</td>
		<td>".$bar->JMLHJJG."</td>
		<td>".$bar->WEI1ST."</td>
		<td>".$bar->WEI2ND."</td>
		<td>".$bar->NETTO."</td>
                                        <td>".$bar->KGPOTSORTASI."</td>
                                         <td>".($bar->NETTO-$bar->KGPOTSORTASI)."</td>";   
		if($bar->JMLHJJG==0)
		{
		  
		  $stream.="<td bgcolor=red>0</td>";
		}
		else
		{
			$bjr=($bar->NETTO-$bar->KGPOTSORTASI)/$bar->JMLHJJG;
			$stream.="<td>".number_format($bjr,2,',','.')."</td>";
		}
$stream.="		
		</tr>
		";
		$xtjjg+=$bar->JMLHJJG;
		$twig+=$bar->NETTO-$bar->KGPOTSORTASI;
}	  			

		if($xtjjg>0)
		{
		$rbjr=$twig/$xtjjg;
		}
		else
		{
			$rbjr=0;
		}
$stream.="<tr style='font-family:tahoma,Arial Narrow;font-size:11px;background-color:#efefef'>
			<td align=center colspan=9 align=right><b>TOTAL:</b></td>
			<td>".$tjjg."</td>
			<td>".$ttarra."</td>
			<td>".$tbruto."</td>
			<td>".$tnetto."</td>
                                                            <td>".$tpot."</td>
                                                            <td>".($tnetto-$tpot)."</td>    
			<td>".number_format($rbjr,2)."</td>
			</tr>";
$stream.="			
		</table>

<font size=2>Printed:".date('d-m-Y H:i:s')."</font>";
$nop_="Laporan_harian"."_".$tgl;
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
