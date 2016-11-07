<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$karyawanid=$_POST['karid'];
$notransaksi=$_POST['notransaksi'];

$str="select * from ".$dbname.".sdm_riwayatjabatan where karyawanid=".$karyawanid ."
 and nomorsk='".$notransaksi."'";
 
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	echo"<?xml version='1.0' ?>
	     <karyawan>
			 <karyawanid>".($bar->karyawanid!=""?$bar->karyawanid:"*")."</karyawanid>
			 <nomorsk>".($bar->nomorsk!=""?$bar->nomorsk:"*")."</nomorsk>
			 <tanggalsk>".($bar->tanggalsk!=""?tanggalnormal($bar->tanggalsk):"*")."</tanggalsk>
			 <mulaiberlaku>".($bar->mulaiberlaku!=""?tanggalnormal($bar->mulaiberlaku):"*")."</mulaiberlaku>
                         <statussk>".($bar->statussk!=""?$bar->statussk:"*")."</statussk>
			 <darikodeorg>".($bar->darikodeorg!=""?$bar->darikodeorg:"*")."</darikodeorg>
                         <darilokasitugassub>".($bar->darilokasitugassub!=""?$bar->darilokasitugassub:"*")."</darilokasitugassub>                             
			 <darikodejabatan>".($bar->darikodejabatan!=""?$bar->darikodejabatan:"*")."</darikodejabatan>
			 <daritipe>".($bar->daritipe!=""?$bar->daritipe:"*")."</daritipe>
			 <tipesk>".($bar->tipesk!=""?$bar->tipesk:"*")."</tipesk>
			 <darikodegolongan>".($bar->darikodegolongan!=""?$bar->darikodegolongan:"*")."</darikodegolongan>
			 <bagian>".($bar->bagian!=""?$bar->bagian:"*")."</bagian>     
                         <kebagian>".($bar->kebagian!=""?$bar->kebagian:"*")."</kebagian>                             
			 <kekodeorg>".($bar->kekodeorg!=""?$bar->kekodeorg:"*")."</kekodeorg>
                         <kelokasitugassub>".($bar->kelokasitugassub!=""?$bar->kelokasitugassub:"*")."</kelokasitugassub>
			 <kekodejabatan>".($bar->kekodejabatan!=""?$bar->kekodejabatan:"*")."</kekodejabatan>
			 <ketipekaryawan>".($bar->ketipekaryawan!=""?$bar->ketipekaryawan:"*")."</ketipekaryawan>
			 <kekodegolongan>".($bar->kekodegolongan!=""?$bar->kekodegolongan:"*")."</kekodegolongan>
			 <ttd1>".($bar->ttd1!=""?$bar->ttd1:"*")."</ttd1>
			 <ttd2>".($bar->ttd2!=""?$bar->ttd2:"*")."</ttd2>
			 <ttd3>".($bar->ttd3!=""?$bar->ttd3:"*")."</ttd3>
			 <ttd4>".($bar->ttd4!=""?$bar->ttd4:"*")."</ttd4>
			 <ttd5>".($bar->ttd5!=""?$bar->ttd5:"*")."</ttd5>
			 <darigaji>".($bar->darigaji!=""?number_format($bar->darigaji,2,'.',','):"*")."</darigaji>
			 <kegaji>".($bar->kegaji!=""?number_format($bar->kegaji,2,'.',','):"*")."</kegaji>
			 <namadireksi>".($bar->namadireksi!=""?$bar->namadireksi:"*")."</namadireksi>			
			 <tembusan1>".($bar->tembusan1!=""?$bar->tembusan1:"*")."</tembusan1>
			 <tembusan2>".($bar->tembusan2!=""?$bar->tembusan2:"*")."</tembusan2>
			 <tembusan3>".($bar->tembusan3!=""?$bar->tembusan3:"*")."</tembusan3>
			 <tembusan4>".($bar->tembusan4!=""?$bar->tembusan4:"*")."</tembusan4>
			 <tembusan5>".($bar->tembusan5!=""?$bar->tembusan5:"*")."</tembusan5>
			 <updatetime>".($bar->updatetime!=""?$bar->updatetime:"*")."</updatetime>
			 <updateby>".($bar->updateby!=""?$bar->updateby:"*")."</updateby>
			 <tjjabatan>".($bar->tjjabatan!=""?number_format($bar->tjjabatan,2,'.',','):"*")."</tjjabatan>
			 <ketjjabatan>".($bar->ketjjabatan!=""?number_format($bar->ketjjabatan,2,'.',','):"*")."</ketjjabatan>
                             

                         <tjsdaerah>".($bar->tjsdaerah!=""?number_format($bar->tjsdaerah,2,'.',','):"*")."</tjsdaerah>
			 <ketjsdaerah>".($bar->ketjsdaerah!=""?number_format($bar->ketjsdaerah,2,'.',','):"*")."</ketjsdaerah>
			 <tjmahal>".($bar->tjmahal!=""?number_format($bar->tjmahal,2,'.',','):"*")."</tjmahal>
			 <ketjmahal>".($bar->ketjmahal!=""?number_format($bar->ketjmahal,2,'.',','):"*")."</ketjmahal>
                         <tjpembantu>".($bar->tjpembantu!=""?number_format($bar->tjpembantu,2,'.',','):"*")."</tjpembantu>
			 <ketjpembantu>".($bar->ketjpembantu!=""?number_format($bar->ketjpembantu,2,'.',','):"*")."</ketjpembantu>
                             
                         <tjkota>".($bar->tjkota!=""?number_format($bar->tjkota,2,'.',','):"*")."</tjkota>
			 <ketjkota>".($bar->ketjkota!=""?number_format($bar->ketjkota,2,'.',','):"*")."</ketjkota>
			 <tjtransport>".($bar->tjtransport!=""?number_format($bar->tjtransport,2,'.',','):"*")."</tjtransport>
			 <ketjtransport>".($bar->ketjtransport!=""?number_format($bar->ketjtransport,2,'.',','):"*")."</ketjtransport>
                         <tjmakan>".($bar->tjmakan!=""?number_format($bar->tjmakan,2,'.',','):"*")."</tjmakan>
                         <ketjmakan>".($bar->ketjmakan!=""?number_format($bar->ketjmakan,2,'.',','):"*")."</ketjmakan>
                             
                         <atasanbaru>".($bar->atasanbaru!=""?number_format($bar->atasanbaru,2,'.',','):"*")."</atasanbaru>		 
		         <paragraf1>".($bar->pg1!=""?$bar->pg1:"*")."</paragraf1>
                         <paragraf2>".($bar->pg2!=""?$bar->pg2:"*")."</paragraf2>    
                         <namajabatan>".($bar->namajabatan!=""?$bar->namajabatan:"*")."</namajabatan>
		 </karyawan>";	
}
?>