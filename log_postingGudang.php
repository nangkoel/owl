<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/log_postingGudang.js'></script>
<?php
include('master_mainMenu.php');

if(isTransactionPeriod())//check if transaction period is normal
{
OPEN_BOX('',"<b>".$_SESSION['lang']['konfirmasitransaksi'].":</b>");

$frm[0]='';
$frm[1]='';
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
  if($_SESSION['empl']['tipelokasitugas']=='KANWIL' and substr($_SESSION['empl']['subbagian'],-2)!='PK'){
   $str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='GUDANG'
       and left(induk,4) in(select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
       order by namaorganisasi";// and kodeorganisasi not in ('SENE10', 'SKNE10', 'SOGE30') order by namaorganisasi";
}else{
   $str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where (left(induk,4)='".$_SESSION['empl']['lokasitugas']."' 
       or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."') and tipe in ('GUDANGTEMP','GUDANG') order by namaorganisasi";
    
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
	 
$frm[0].="<fieldset>
	   <legend>".$_SESSION['lang']['list']."</legend>
	  <fieldset><legend></legend>
	  ".$_SESSION['lang']['cari_transaksi']."
	  <input title='Cukup mengetikkan nomor depannya saja, contoh:20140200001' type=text id=txtunpost size=25 class=myinputtext onkeypress=\"return validat(event);\" maxlength=12>
	  <button class=mybutton onclick=cariUnconfirmed(0)>".$_SESSION['lang']['find']."</button><br><i><b>Tipe:</b>
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
	  <td>".$_SESSION['lang']['asaltujuan']."</td>
	  <td>".$_SESSION['lang']['noreferensi']."</td>			  
	  <td>".$_SESSION['lang']['dbuat_oleh']."</td>
	  <td></td>
	  </tr>
	  </head>
	   <tbody id=unconfirmaedlist>
	   </tbody>
	   <tfoot>
	   </tfoot>
	   </table>
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
$frm[1].="<fieldset>
	   <legend>".$_SESSION['lang']['list']."</legend>
	  ".$_SESSION['lang']['cari_transaksi']."
	  <input title='Cukup mengetikkan nomor depannya saja, contoh:20140200001' type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return validat2(event);\" maxlength=12>
	  <button class=mybutton onclick=cariDokumen(0)>".$_SESSION['lang']['find']."</button><br><i><b>Tipe:</b>
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
	  <td>".$_SESSION['lang']['asaltujuan']."</td>
	  <td>".$_SESSION['lang']['noreferensi']."</td>		  
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
$hfrm[0]=$_SESSION['lang']['belumposting'];
$hfrm[1]=$_SESSION['lang']['daftartransaksi'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,150,1000);
//===============================================	 
}
else
{
        echo " Error: Transaction Period missing";
}
CLOSE_BOX();
close_body();
?>