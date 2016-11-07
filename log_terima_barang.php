<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/log_terima_barang.js'></script>
<?php
include('master_mainMenu.php');
if(isTransactionPeriod())//check if transaction period is normal
{

	

OPEN_BOX('',"");

$frm[0]='';
$frm[1]='';
echo "<fieldset><legend>";
echo" <b>".$_SESSION['lang']['penerimaanbarang'].":</b> ";
echo"</legend>";

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
$optKary="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sKary="select distinct karyawanid,namakaryawan from ".$dbname.".datakaryawan 
        where lokasitugas='".$_SESSION['empl']['lokasitugas']."' and bagian IN ('PUR','AGR') and
        (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].") order by namakaryawan asc";
$qKary=mysql_query($sKary) or die(mysql_error($sKary));
while($rKary=mysql_fetch_assoc($qKary))
{
    $optKary.="<option value='".$rKary['karyawanid']."'>".$rKary['namakaryawan']."</option>";
}

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
	 <td>".$_SESSION['lang']['mengetahui']."</td><td><select id=mengetahuiId style=width:145px >".$optKary."</select><td></td>
	 </tr>
	 <tr>
	 <td>".$_SESSION['lang']['namapenerima']."</td><td><select id=penerimaId style=width:145px>".$optKary."</select></td>
	 <td>".$_SESSION['lang']['nopo']."</td><td><input type=text id=nopo class=myinputtext size=25 maxength=25 onkeypress=\"return tanpa_kutip(event);\">
	    <img src=images/zoom.png title='".$_SESSION['lang']['find']."' class=resicon onclick=cariPO('".$_SESSION['lang']['find']."',event)>
	    <button class=mybutton onclick=getPOSupplier() id=btnheader>".$_SESSION['lang']['tampilkan']."</button>
	 </td>
	 <td></td>
	 </tr>
	 </table><input type=hidden id=statInput value=0 />";
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
	  <input type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=12>
	  <button class=mybutton onclick=cariBapb()>".$_SESSION['lang']['find']."</button>
	  </fieldset>
	  <table class=sortable cellspacing=1 border=0>
      <thead>
	  <tr class=rowheader>
	  <td>No.</td>
	  <td>".$_SESSION['lang']['tipe']."</td>
	  <td>".$_SESSION['lang']['momordok']."</td>
	  <td>".$_SESSION['lang']['tanggal']."</td>
	  <td>".$_SESSION['lang']['nopo']."</td>	
	  <td>".$_SESSION['lang']['supplier']."</td> 
	  <td>".$_SESSION['lang']['dbuat_oleh']."</td>
	  <td>".$_SESSION['lang']['posted']."</td>
	  <td>Action</td>
	  </tr>
	  </head>
	   <tbody id=containerlist>
           <script>getBapbList()</script>
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
	echo " Error: Transaction period is missing";
}
CLOSE_BOX();
close_body();
?>