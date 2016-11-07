<?php
require_once('master_validation.php');
require_once('config/connection.php');

$tgl=$_POST['tanggal'];
$tgl2=substr($tgl,6,4).'-'.substr($tgl,3,2).'-'.substr($tgl,0,2);
$product=$_POST['product'];

	$stz="select PRODUCTNAME from ".$dbname.".msproduct where PRODUCTCODE='".$product."'";
	$rez=mysql_query($stz);
	while ($ba=mysql_fetch_object($rez)){
		$productname=$ba->PRODUCTNAME;
	}

		
   echo"<br>TANGGAL:".$tgl."<br>
        COMMODITY:".$productname."
        <table boder=0 cellspacing=1px class=sortable>
          <tr class=rowheader style='background-color:#DEDEDE;'>
            <td align=center>No.</td>
			<td align=center>NO.KENDARAAN</td>
			<td align=center>NOMOR KONTRAK</td>
			<td align=center>NO.DO</td>
			<td align=center>NETTO (Kg)</td>
			<td align=center>LOCIS</td>
			<td align=center>CATATAN</td>
			<td align=center>K.Air<br>(%)</td>
			<td align=center>Dirt(%)</td>
			<td align=center>FFa(%)</td>
			<td align=center>NO.SP</td>
			<td align=center>PENGIRIM</td>
			<td align=center>NO.SIM</td>
			<td align=center></td>
		   </tr>";	
           //ambil nama manager
		 $str="select MNGRNAME from ".$dbname.".mssystem";
		 $resh=mysql_query($str);
		 while($barh=mysql_fetch_object($resh)){
		  $mgr=$barh->MNGRNAME;
 		 } 
		   
    $str="select TICKETNO2,DRIVER,VEHNOCODE,SIPBNO,DATEIN,DATEOUT,WEI1ST,WEI2ND,NETTO,MILLCODE,CTRNO,NODOTRP,
	      b.*
        from ".$dbname.".mstrxtbs 
		left join ".$dbname.".wb_bukti b on TICKETNO2=nowb
		where DATEOUT like '".$tgl2."%' and productcode='".$product."'
        and OUTIN=0 order by DATEOUT";
	
	$no=0;	
	$res=mysql_query($str);
	while($bar=mysql_fetch_object($res))
	{
		$no+=1;
		echo"<tr class=rowcontent id=row".$bar->TICKETNO2.">
            <td>".$no."</td>
			<td>".$bar->VEHNOCODE."</td>
			<td>".$bar->CTRNO."</td>
			<td>".$bar->SIPBNO."</td>
			<td align=right>".number_format($bar->NETTO,2,',','.')."</td>
			<td><input type=text  size=8 id=nosegel".$no." value='".$bar->nosegel."'></td>
			<td><input type=text  size=12 id=manager".$no." value='".$bar->manager."'></td>
			<td><input type=text  size=3 id=air".$no." value='".$bar->air."' maxlength=4 onkeypress=\"return angka_doang(event);\"></td>
			<td><input type=text  size=3 id=kotoran".$no." value='".$bar->kotoran."' maxlength=4 onkeypress=\"return angka_doang(event);\"></td>
			<td><input type=text  size=3 id=ffa".$no." value='".$bar->ffa."' maxlength=4 onkeypress=\"return angka_doang(event);\"></td>
			<td><input type=text  size=12 id=nobuku".$no." value='".$bar->nobuku."'></td>
			<td><input type=text  size=12 id=kapabrik".$no." value='".$mgr."'></td>
			<td><input type=text  size=12 id=bongkar".$no." value='".$bar->pbongkar."'></td>
			<td><input type=button value='Save' onclick=simpanBukti('".$bar->TICKETNO2."','".$no."')>
			<input type=button value='Print' onclick=printBuktiP('".$bar->TICKETNO2."','".$no."')>
			</td>
		   </tr>";	
		
	}	
	echo"</table>";
?>
