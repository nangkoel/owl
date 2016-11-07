<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$notransaksi=$_POST['notransaksi'];
$karid=$_POST['karid'];

$str="select * from ".$dbname.".sdm_pjdinasht
      where karyawanid=".$karid ." and notransaksi='".$notransaksi."'";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	echo"<?xml version='1.0' ?>
	     <pjd>
			 <karyawanid>".($bar->karyawanid!=""?$bar->karyawanid:"*")."</karyawanid>
			 <kodeorg>".($bar->kodeorg!=""?$bar->kodeorg:"*")."</kodeorg>
			 <persetujuan>".($bar->persetujuan!=""?$bar->persetujuan:"*")."</persetujuan>
			  <persetujuan2>".($bar->persetujuan2!=""?$bar->persetujuan2:"*")."</persetujuan2>
			 <hrd>".($bar->hrd!=""?$bar->hrd:"*")."</hrd>
			 <tujuan3>".($bar->tujuan3!=""?$bar->tujuan3:"*")."</tujuan3>
			 <tujuan2>".($bar->tujuan2!=""?$bar->tujuan2:"*")."</tujuan2>
		     <tujuan1>".($bar->tujuan1!=""?$bar->tujuan1:"*")."</tujuan1>
			 <tanggalperjalanan>".($bar->tanggalperjalanan!=""?tanggalnormal($bar->tanggalperjalanan):"*")."</tanggalperjalanan>
			 <tanggalkembali>".($bar->tanggalkembali!=""?tanggalnormal($bar->tanggalkembali):"*")."</tanggalkembali>
			 <uangmuka>".($bar->uangmuka!=""?$bar->uangmuka:"*")."</uangmuka>
			 <tugas1>".($bar->tugas1!=""?$bar->tugas1:"*")."</tugas1>
			 <tugas2>".($bar->tugas2!=""?$bar->tugas2:"*")."</tugas2>
			 <tugas3>".($bar->tugas3!=""?$bar->tugas3:"*")."</tugas3>
			 <tugaslain>".($bar->tugaslain!=""?$bar->tugaslain:"*")."</tugaslain>
			 <tujuanlain>".($bar->tujuanlain!=""?$bar->tujuanlain:"*")."</tujuanlain>
			 <pesawat>".($bar->pesawat!=""?$bar->pesawat:"*")."</pesawat>
			 <darat>".($bar->darat!=""?$bar->darat:"*")."</darat>
			 <laut>".($bar->laut!=""?$bar->laut:"*")."</laut>
			 <mess>".($bar->mess!=""?$bar->mess:"*")."</mess>
			 <notransaksi>".($bar->notransaksi!=""?$bar->notransaksi:"*")."</notransaksi>
			 <hotel>".($bar->hotel!=""?$bar->hotel:"*")."</hotel>
			 <mobilsewa>".($bar->mobilsewa!=""?$bar->mobilsewa:"*")."</mobilsewa>
			 <ket>".($bar->keterangan!=""?$bar->keterangan:"*")."</ket>
		 </pjd>";	   		 	
}
?>