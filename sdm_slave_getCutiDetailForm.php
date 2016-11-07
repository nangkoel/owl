<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
require_once('config/connection.php');
?>
<?php

$periode=$_POST['periode'];
$karyawanid=$_POST['karyawanid'];
$kodeorg=$_POST['kodeorg'];
$namakaryawan=$_POST['namakaryawan'];

for($i=0;$i<24;)
{
	if(strlen($i)<2)
	{
		$i="0".$i;
	}
   $jm.="<option value=".$i.">".$i."</option>";
   $i++;
}
for($i=0;$i<60;)
{
	if(strlen($i)<2)
	{
		$i="0".$i;
	}
   $mnt.="<option value=".$i.">".$i."</option>";
   $i++;
}

echo"<fieldset><legend>".$_SESSION['lang']['form']."</legend>
    <table>
	 <tr>
	 
     <input type=hidden class=myinputtext id=kodeorgJ  value='".$kodeorg."'>
	 <input type=hidden class=myinputtext id=karyawanidJ value='".$karyawanid."'>
	 <input type=hidden class=myinputtext id=periodeJ value='".$periode."'>
	 
	 <td>".$_SESSION['lang']['namakaryawan']."</td><td><input type=text class=myinputtext id=namakaryawan disabled value='".$namakaryawan."' size=25></td>
	 <td>".$_SESSION['lang']['tangalcuti']."</td><td><input type=text class=myinputtext id=dariJ onmouseover=setCalendar(this) onchange=cekTanggal() size=10>&nbsp;".$_SESSION['lang']['jam']." :
         &nbsp;<select id=jamMulai>".$jm."></select>:<select id=mntMulai>".$mnt."></select></td>
	 <td>".$_SESSION['lang']['tglcutisampai']."</td><td><input type=text class=myinputtext id=sampaiJ onmouseover=setCalendar(this) onchange=cekTanggal() size=10>&nbsp;".$_SESSION['lang']['jam']." :
         &nbsp;<select id=jamPlg>".$jm."></select>:<select id=mntPlg>".$mnt."></select>
	 </tr>
	 
	 <tr>
	 <td>".$_SESSION['lang']['diambil']."</td><td><input type=text class=myinputtextnumber id=diambilJ  size=25 onkeypress=\"return angka_doang(event);\"  size=3 maxlength=2></td>
	 <td>".$_SESSION['lang']['keterangan']."</td><td colspan=3><input type=text class=myinputtext id=keteranganJ onkeypress=\"return tanpa_kutip(event);\" size=35 maxlength=45>
	 <button class=mybutton onclick=simpanJ()>".$_SESSION['lang']['save']."</button>
	 </td>
	 </tr>
         <tr>
         <td colspan=5><i><b>* Input jam hanya berlaku untuk perhitungan pada karyawan Harian (KHT)</b></i></td>
         </tr>
	 </table>
	 </fieldset>
	<fieldset>
	<legend>".$_SESSION['lang']['cuti']."->[".$namakaryawan."] ".$_SESSION['lang']['periode'].":".$periode."</legend>
	<div style='width:750px;height:210px;overflow:scroll;' id=containerlist3>
	<table class=sortable cellspacing=1 border=0>
	<thead>
	<tr class=rowheader>
	   <td>
	      No
	   </td>
	   <td>".$_SESSION['lang']['tangalcuti']."</td>
	   <td>".$_SESSION['lang']['tglcutisampai']."</td>
	   <td>".$_SESSION['lang']['diambil']."</td>
	   <td>".$_SESSION['lang']['keterangan']."</td>
	   <td>".$_SESSION['lang']['aksi']."</td>
	</tr>
	</thead>
	<tbody>
	";
	$str="select * from ".$dbname.".sdm_cutidt where karyawanid=".$karyawanid."
	      and periodecuti='".$periode."'";
	$res=mysql_query($str);
	$no=0;
	$ttl=0;
	while($bar=mysql_fetch_object($res))	  
	{
		$no+=1;
		echo"<tr class=rowcontent id=barisJ".$no.">
	   <td>".$no."</td>
	   <td>".tanggalnormal($bar->daritanggal)."</td>
	   <td>".tanggalnormal($bar->sampaitanggal)."</td>
	   <td align=right>".$bar->jumlahcuti."</td>
	   <td>".$bar->keterangan."</td>
	   <td>
	   <img src='images/application/application_delete.png'  title='".$_SESSION['lang']['delete']."' class=resicon onclick=\"hapusData('".$periode."','".$karyawanid."','".$kodeorg."','".$bar->daritanggal."','barisJ".$no."',".$bar->jumlahcuti.");\">
	   </td>
	   </tr>";
	   $ttl+=$bar->jumlahcuti;
	}
		echo"<tr class=rowcontent>
	   <td></td>
	   <td>TOTAL</td>
	   <td></td>
	   <td align=right id=cellttl>".$ttl."</td>
	   <td></td>
	   <td></td>
	   </tr>";	
echo"</tbody>
     <tfoot>
	 </tfoot>
     </div>
	</fieldset> 
	"; 
?>