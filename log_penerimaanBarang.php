<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zLib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/log_transaksi.js'></script>
<?php
include('master_mainMenu.php');
if(isTransactionPeriod())//check if transaction period is normal
{

$optSupp = makeOption($dbname,'log_5supplier','supplierid,namasupplier');

OPEN_BOX('',"<b>".$_SESSION['lang']['penerimaanbarang'].":</b>");

$frm[0]='';
$frm[1]='';
echo "<div id='optSupp' style='display:none'>";
foreach($optSupp as $key=>$value) {
	echo "<option value='".$key."'>".$value."</option>";
}
echo "</div>";
echo "<fieldset><legend>";
foreach($_SESSION['gudang'] as $key=>$val){
    if (substr($key,0,4)==$_SESSION['empl']['lokasitugas'] and (substr($key,4)=='WH' or substr($key,4)=='01')){
        $kodegudang=$key;
        $gudangStart=tanggalnormal($_SESSION['gudang'][$key]['start']);
        $gudangEnd=tanggalnormal($_SESSION['gudang'][$key]['end']);
    }
}
echo" <b>".$_SESSION['lang']['periode']." ".$_SESSION['lang']['gudang']." (".$kodegudang."): <span id=displayperiod>".$gudangStart." - ".$gudangEnd."</span></b>";
echo"</legend>";
#kodeorganisasi untuk klinik harus berakhiran PK
if($_SESSION['empl']['tipelokasitugas']=='KANWIL' and substr($_SESSION['empl']['subbagian'],-2)!='PK'){
   $str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='GUDANG'
       and left(induk,4) in(select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
       order by namaorganisasi";// and kodeorganisasi not in ('SENE10', 'SKNE10', 'SOGE30') order by namaorganisasi";
}
elseif($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
      $str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where induk in (select kodeorganisasi 
       from ".$dbname.".organisasi where tipe='".$_SESSION['empl']['tipelokasitugas']."') and tipe= 'GUDANGTEMP'
       order by namaorganisasi";
}
else{
      $str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where (left(induk,4)='".$_SESSION['empl']['lokasitugas']."' 
       or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."') and tipe= 'GUDANGTEMP' order by namaorganisasi";
}
//exit($str);
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

$frm[0].=$_SESSION['lang']['peringatanretur']."
     <table cellspacing=1 border=0>
     <tr>
		<td>".$_SESSION['lang']['momordok']."</td>
		<td><input type=text id=nodok size=25 disabled class=myinputtext></td>	 
	    <td>".$_SESSION['lang']['tanggal']."</td><td>
		     <input type=text class=myinputtext id=tanggal size=25 onmousemove=setCalendar(this.id) maxlength=10 onkeypress=\"return false;\" value='".date('d-m-Y')."'>
		</td>
	 </tr>
	 <tr>
	 <td>".$_SESSION['lang']['supplier']."</td><td><input type=hidden value='' id=idsupplier><input type=text id=supplier class=myinputtext size=25 maxength=25 onkeypress=\"return tanpa_kutip(event);\" disabled></td>
	 <td>".$_SESSION['lang']['suratjalan']."</td><td><input type=text id=nosj class=myinputtext size=25 maxength=25 onkeypress=\"return tanpa_kutip(event);\"></td>
	 </tr>
	 <tr>
	 <td>".$_SESSION['lang']['faktur']."</td><td><input type=text id=nofaktur class=myinputtext size=25 maxength=25 onkeypress=\"return tanpa_kutip(event);\"></td>
	 <td>".$_SESSION['lang']['nopo']."</td><td><input type=text id=nopo class=myinputtext size=25 maxength=25 onkeypress=\"return tanpa_kutip(event);\">
	    <img src=images/zoom.png title='".$_SESSION['lang']['find']."' class=resicon onclick=cariPO('".$_SESSION['lang']['find']."',event)>
	    <button class=mybutton onclick=getPOSupplier() id=btnheader>".$_SESSION['lang']['tampilkan']."</button>
	 </td>
	 <td></td>
	 </tr>
	 </table>";
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
    <fieldset>
	   <legend>".$_SESSION['lang']['detail']."</legend>
	   <div id=container>
	   </div>
	 </fieldset>
	 ";
	 
$frm[1].="<fieldset>
	   <legend>".$_SESSION['lang']['list']."</legend>
	  <fieldset><legend></legend>
	  ".$_SESSION['lang']['cari_transaksi']."
	  <input title='Cukup mengetikkan nomor depannya saja, contoh:20140200001' type=text id=txtbabp size=15 class=myinputtext onkeypress=\"return validat(event);\" maxlength=12>
	  <button class=mybutton onclick=cariBapb()>".$_SESSION['lang']['find']."</button><br><i><b>Tipe:</b>
          1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi, 5=Pengeluaran, 6=Pengembalian penerimaan, 7=pengeluaran mutasi</i>
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
	  <td>".$_SESSION['lang']['supplier']."</td> 
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
$hfrm[0]=$_SESSION['lang']['penerimaanbarang'];
$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,200,900);
//===============================================	 
}
else
{
	echo " Error: Transaction Period missing";
}
CLOSE_BOX();
close_body();
?>