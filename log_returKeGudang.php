<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/log_returkegudang.js'></script>
<?php
include('master_mainMenu.php');

if(isTransactionPeriod())//check if transaction period is normal
{

OPEN_BOX('',"<b>".$_SESSION['lang']['retur']." (Ke Gudang):</b>");

$frm[0]='';
$frm[1]='';
echo "<fieldset><legend>";
echo" <b>".$_SESSION['lang']['periode'].": <span id=displayperiod>".tanggalnormal($_SESSION['org']['period']['start'])." - ".tanggalnormal($_SESSION['org']['period']['end'])."</span></b>";
echo"</legend>";
#kodeorganisasi untuk klinik harus berakhiran PK
  if($_SESSION['empl']['tipelokasitugas']=='KANWIL' and substr($_SESSION['empl']['subbagian'],-2)!='PK'){
   $str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='GUDANG'
       and left(induk,4) in(select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
       order by namaorganisasi";// and kodeorganisasi not in ('SENE10', 'SKNE10', 'SOGE30') order by namaorganisasi";
}else{
   $str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where (left(induk,4)='".$_SESSION['empl']['lokasitugas']."' 
       or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."') and tipe= 'GUDANGTEMP' order by namaorganisasi";
    
}
$res=mysql_query($str);
$optsloc="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
	$optsloc.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}

echo"<fieldset>
     <legend>
	 ".$_SESSION['lang']['daftargudang']."
     </legend>
	  ".$_SESSION['lang']['pilihgudang'].": <select id=sloc>".$optsloc."</select>
	   <button onclick=setSloc('simpan') class=mybutton id=btnsloc>".$_SESSION['lang']['save']."</button>
	   <button onclick=setSloc('ganti') class=mybutton>".$_SESSION['lang']['ganti']."</button>
	  
	 </fieldset>";

$frm[0].="<fieldset><legend>".$_SESSION['lang']['header']."</legend>";

$frm[0].="<table cellspacing=1 border=0>
     <tr>
		<td>".$_SESSION['lang']['momordok']."</td>
		<td><input type=text id=nodok size=25 disabled class=myinputtext></td>	 
	    <td>".$_SESSION['lang']['tanggalretur']."</td><td>
		     <input type=text class=myinputtext id=tanggal size=25 onmousemove=setCalendar(this.id) maxlength=10 onkeypress=\"return false;\" value='".date('d-m-Y')."'>
		</td>
	 </tr>
	 </table>
	 <fieldset><legend>".$_SESSION['lang']['dokumenlama']."</legend>
	 <table>
	 <tr>
	 <td>".$_SESSION['lang']['nomorlama']."</td><td><input type=text id=nomorlama class=myinputtext size=25 maxength=25 onkeypress=\"return tanpa_kutip(event);\"></td>
	 <td>".$_SESSION['lang']['kodebarang']."</td><td><input type=text id=kodebarang class=myinputtext size=25 maxength=11 onkeypress=\"return angka_doang(event);\">
         <td>".$_SESSION['lang']['kodeblok']."</td><td><input type=text id=kodeblok class=myinputtext size=25 maxength=11 onkeypress=\"return tanpa_kutip_dan_sepasi(event);\">    
	       <button class=mybutton onclick=Fverify()>".$_SESSION['lang']['cek']."</button>
	 </td>
	 </tr>
	 <tr>
	 <td>".$_SESSION['lang']['namabarang']."</td><td><input type=text id=namabarang class=myinputtext size=25 maxength=25 onkeypress=\"return tanpa_kutip(event);\" disabled></td>
	 <td>".$_SESSION['lang']['jumlah']."</td><td><input type=text id=jlhlama class=myinputtextnumber size=25 maxength=25 onkeypress=\"return tanpa_kutip(event);\" disabled>
	 <input type=text id=satuan size=6 disabled class=myinputtext>
	 </td>
	 </tr>
	 </table>
	 </fieldset>
	 <fieldset><legend>".$_SESSION['lang']['jumlahkembali']."</legend>
	 ".$_SESSION['lang']['jumlahkembali'].": <input type=text id=jlhretur disabled value=0 class=myinputtextnumber size=10 maxlength=6 onkeypress=\"return tanpa_kutip(event);\">
	 <input type=hidden id=hargasatuan value='0'>
	 <input type=hidden id=kodept value=''>
	 <input type=hidden id=untukunit value=''>
	 <input type=hidden id=untukpt value=''>
	 ".$_SESSION['lang']['keterangan']."
	 <input type=text id=keterangan class=myinputtext onkeypress=\"return tanpa_kutip(event);\" size=25 maxlength=80>
	 <button id=savebutton class=mybutton onclick=simpanRetur() disabled>".$_SESSION['lang']['save']."</button>
	 <button id=savebutton class=mybutton onclick=window.location.reload()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>
	 ";
//==================masukkan variable periode gudang
//$sess=$_SESSION['gudang'];
foreach($_SESSION['gudang'] as $key=>$val)
{
 //  echo	$sess[$key]['start'];

	$frm[0].="<input type=hidden id='".$key."_start' value='".$_SESSION['gudang'][$key]['start']."'>
	     <input type=hidden id='".$key."_end' value='".$_SESSION['gudang'][$key]['end']."'>
		";
}	 
$frm[0].="</fieldset>
	 ";
	 
$frm[1].="<fieldset>
	   <legend>".$_SESSION['lang']['list']."</legend>
	  <fieldset><legend></legend>
	  ".$_SESSION['lang']['cari_transaksi']."
	  <input type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=9>
	  <button class=mybutton onclick=cariBapb()>".$_SESSION['lang']['find']."</button>
	  </fieldset>
	  <table class=sortable cellspacing=1 border=0>
      <thead>
	  <tr class=rowheader>
	  <td>No.</td>
	  <td>".$_SESSION['lang']['sloc']."</td>
	  <td>".$_SESSION['lang']['tipe']."</td>
	  <td>".$_SESSION['lang']['momordok']."</td>
	  <td>".$_SESSION['lang']['tanggal']."</td>
	  <td>".$_SESSION['lang']['pt']."</td>
	  <td>".$_SESSION['lang']['nopo']."</td>	
	  <td>".$_SESSION['lang']['dari']."</td> 
	  <td>".$_SESSION['lang']['dbuat_oleh']."</td>
	  <td>".$_SESSION['lang']['posted']."</td>
	  <td></td>
	  </tr>
	  </head>
	   <tbody id=containerlist>
	   </tbody>
	   <tfoot>
	   </tfoot>
	   </table>
	 </fieldset>	 
	 ";	 
//========================
$hfrm[0]=$_SESSION['lang']['retur'];
$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,100,900);
//===============================================	 
}
else
{
	echo " Error: Transaction Period missing";
}
CLOSE_BOX();
close_body();
?>