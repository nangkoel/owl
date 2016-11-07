<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/log_prosesAkhirBulan.js'></script>
<?php
include('master_mainMenu.php');

if(isTransactionPeriod())//check if transaction period is normal
{
OPEN_BOX('',"<b>".$_SESSION['lang']['kalkulasihargarata'].":</b>");

$frm[0]='';
$frm[1]='';
echo "<fieldset><legend>".$_SESSION['lang']['infoakhirbulangudang']."</legend>";

if($_SESSION['empl']['tipelokasitugas']=='KANWIL')
    $str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='GUDANG' order by namaorganisasi";
else    
    $str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where (induk='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."') and tipe='GUDANG' order by namaorganisasi";

$res=mysql_query($str);
$optsloc="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
	$optsloc.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}
$optper='';
for($x=0;$x<13;$x++)
{
  $y=mktime(0,0,0,date('m')-$x,15,date('Y'));
  $optper.="<option value='".date('Y-m',$y)."'>".date('m-Y',$y)."</option>";
}

echo"<fieldset>
     <legend>
	 ".$_SESSION['lang']['pt']."
     </legend>
	  ".$_SESSION['lang']['ptpemilikbarang'].": <select id=sloc>".$optsloc."</select>
           <select id=periode>".$optper."</select>
	   <button onclick=setSloc('simpan') class=mybutton id=btnsloc>".$_SESSION['lang']['save']."</button>
	   <button onclick=setSloc('ganti') class=mybutton>".$_SESSION['lang']['ganti']."</button>
	  
	 </fieldset>";

$frm[0].="<fieldset><legend>".$_SESSION['lang']['info']."</legend>
          <div id=infoDisplay>
		  </div>
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
$frm[0].="</fieldset>";
//========================
$hfrm[0]=$_SESSION['lang']['daftarproses'];
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