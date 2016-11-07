<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zFunction.php');
include_once('lib/zLib.php');
echo open_body();
?>
<link rel=stylesheet type="text/css" href='style/zTable.css'>
<script language=javascript1.2 src='js/log_transaksi_pengeluaran.js'></script>

<?php
include('master_mainMenu.php');

if(isTransactionPeriod())//check if transaction period is normal
{
OPEN_BOX('',"<b>".$_SESSION['lang']['pengeluaranbarang'].":</b>");

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

#kodeorganisasi untuk klinik harus berakhiran PK
if($_SESSION['empl']['tipelokasitugas']=='KANWIL' and substr($_SESSION['empl']['subbagian'],-2)!='PK'){
   $str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='GUDANG'
       and left(induk,4) in(select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
       order by namaorganisasi";// and kodeorganisasi not in ('SENE10', 'SKNE10', 'SOGE30') order by namaorganisasi";
}
   else{
   $str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi 
       where (left(induk,4)='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."') 
      and tipe='GUDANGTEMP'";// and kodeorganisasi not in ('SENE10', 'SKNE10', 'SOGE30') order by namaorganisasi";
}
$res=mysql_query($str);
$optsloc="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
	$optsloc.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}
$optSupp="<option value=''></option>";
$sSupp="select supplierid,namasupplier from ".$dbname.".log_5supplier where status=1 and kodekelompok!='S005' order by kodekelompok, namasupplier";
$qSupp=mysql_query($sSupp) or die(mysql_error($conn));
while($rSupp=mysql_fetch_assoc($qSupp)){
    $optSupp.="<option value='".$rSupp['supplierid']."'>".$rSupp['supplierid']." - ".$rSupp['namasupplier']."</option>";
}

echo"<fieldset>
     <legend>
	 ".$_SESSION['lang']['daftargudang']."
     </legend>
	  ".$_SESSION['lang']['pilihgudang'].": <select id=sloc onchange=getPT(this.options[this.selectedIndex].value)>".$optsloc."</select>
	   ".$_SESSION['lang']['ptpemilikbarang']."<select id=pemilikbarang style='width:200px;'>
	   <option value=''></option>
	   </select>
	   <button onclick=setSloc('simpan') class=mybutton id=btnsloc>".$_SESSION['lang']['save']."</button>
	   <button onclick=setSloc('ganti') class=mybutton>".$_SESSION['lang']['ganti']."</button>	  
	 </fieldset>";
//==================masukkan variable periode gudang
//$sess=$_SESSION['gudang'];
foreach($_SESSION['gudang'] as $key=>$val)
{
 //  echo	$sess[$key]['start'];

	echo"<input type=hidden id='".$key."_start' value='".$_SESSION['gudang'][$key]['start']."'>
	     <input type=hidden id='".$key."_end' value='".$_SESSION['gudang'][$key]['end']."'>
		";
}	 
//Unit dan Sub Unit
$str="select distinct namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where length(kodeorganisasi)=4
       and left(kodeorganisasi,4) in(select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
       order by namaorganisasi";
$res=mysql_query($str);
$optlokasitujuan="<option value=''></option>";
while($bar=mysql_fetch_object($res)){
    $optlokasitujuan.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}
$optsubunit="<option value=''></option>";

//get Kegiatan
$optKegiatan="<option value=''></option>";
$strf="select kodekegiatan,kelompok,namakegiatan from ".$dbname.".setup_kegiatan order by kelompok,namakegiatan";
$resf=mysql_query($strf);
while($barf=mysql_fetch_object($resf))
{
	 $optKegiatan.="<option value='".$barf->kodekegiatan."'>[".$barf->kelompok."]-".$barf->namakegiatan."</option>";
}

//=================Get kendaraan
   $optionm="<option value=''></option>"; 
	$str="select * from ".$dbname.".vhc_5master 	 
                where left(kodeorg,4) in(select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
                order by kodetraksi,kodevhc";
	$res=mysql_query($str);
	while($bar1=mysql_fetch_object($res))
	{
		$str="select namajenisvhc from ".$dbname.".vhc_5jenisvhc where jenisvhc='".$bar1->jenisvhc."'";
                //echo $str;
		$res1=mysql_query($str);
		$namabarang='';
		while($bar=mysql_fetch_object($res1))
		{
			$namabarang=$bar->namajenisvhc;
		}
		$optionm.="<option value='".$bar1->kodevhc."'>".$bar1->kodetraksi." : ".$bar1->kodevhc." - ".$namabarang."</option>";
	}
//========================================

//===================================
$frm[0].="<fieldset><legend>".$_SESSION['lang']['header']."</legend>";

$frm[0].="<table cellspacing=1 border=0>
     <tr>
		<td>".$_SESSION['lang']['momordok']."</td>
		<td><input type=text id=nodok size=25 disabled class=myinputtext></td>	 
	    <td>".$_SESSION['lang']['tanggal']."</td><td>
		     <input type=text class=myinputtext id=tanggal size=12 onmousemove=setCalendar(this.id) maxlength=10 onkeypress=\"return false;\" value='".date('d-m-Y')."'>
		</td>
	 </tr>
	 <tr>
	 <td>".$_SESSION['lang']['untukunit']."</td><td width=250><select id=untukunit onchange=loadSubunit(this.options[this.selectedIndex].value,'','') style='width:200px;'>".$optlokasitujuan."</select></td>
	 <td>".$_SESSION['lang']['subunit']."</td><td><select id=subunit onchange=loadBlock(this.options[this.selectedIndex].value,'')>".$optsubUnit."</select>
 	    <input type=hidden value='insert' id=method>
	 </td>
	 </tr>                                                                                                                                                                                                 
	 <tr>
	 <td nowrap>".$_SESSION['lang']['penerima']."</td><td><select id=penerima style=width:200px>".$optsubUnit."</select><img class='zImgBtn' style='position:relative;top:5px' src='images/onebit_02.png' onclick=\"getKary('".$_SESSION['lang']['find']." ".$_SESSION['lang']['namakaryawan']."','1',event);\"  /></td>
	 <td>".$_SESSION['lang']['supplier']."</td><td><select id=supplier>".$optSupp."</select></select><img class='zImgBtn' style='position:relative;top:5px' src='images/onebit_02.png' onclick=\"getSupp('".$_SESSION['lang']['find']." ".$_SESSION['lang']['namasupplier']."',event);\"  /></td>
	 </tr>                                                                                                                                                                                                 
	 <tr>
	 <td>".$_SESSION['lang']['note']."</td><td colspan=3><input type=text id=catatan class=myinputtext onkeypress=\"return tanpa_kutip(event);\" size=100></td>
	 </tr>

	 </table>
    </fieldset>
    <fieldset>
	   <legend>".$_SESSION['lang']['detail']."</legend>
	   <div id=container>
	   <table class=sortable cellspacing=1 border=0>
		   <thead>
		   <tr class=rowheader>
		    <td>Kode.Barang</td>
			<td>".$_SESSION['lang']['namabarang']."</td>
			<td>".$_SESSION['lang']['satuan']."</td>
			<td>".$_SESSION['lang']['jumlah']."</td>
			<td>".$_SESSION['lang']['blok']."</td>
			<td>".$_SESSION['lang']['mesin']."</td>
			<td>".$_SESSION['lang']['kegiatan']."</td>
			</tr>
		   </thead>
			   <tbody>
				   <tr class=rowcontent>
				    <td><input type=text size=10 maxlength=10 id=kodebarang class=myinputtext onkeypress=\"return false;\" onclick=\"showWindowBarang('".$_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang']."',event);\"></td>
					<td><input type=text size=45 maxlength=100 id=namabarang class=myinputtext readonly onclick=\"showWindowBarang('".$_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang']."',event);\"></td>
					<td><input type=text size=5 maxlength=5 id=satuan class=myinputtext  onkeypress=\"return false;\" onclick=\"showWindowBarang('".$_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang']."',event);\"></td>
					<td><input type=text size=8 maxlength=10 id=qty value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\"></td>
					<td><select id=blok style='width:100px;' onchange=getKegiatan(this.options[this.selectedIndex].value,'BLOK')></select><img class='zImgBtn' style='position:relative;top:5px' src='images/onebit_02.png' onclick=\"getKary('".$_SESSION['lang']['find']." ".$_SESSION['lang']['blok']."','2',event);\"  /></td>
					<td><select id=mesin style='width:100px;' onchange=getKegiatan(this.options[this.selectedIndex].value,'TRAKSI')>".$optionm."</select><img class='zImgBtn' style='position:relative;top:5px' src='images/onebit_02.png' onclick=\"getKary('".$_SESSION['lang']['find']." ".$_SESSION['lang']['mesin']."','3',event);\"  /></td>
					<td><select id=kegiatan style='width:100px;'>".$optKegiatan."</select><img class='zImgBtn' style='position:relative;top:5px' src='images/onebit_02.png' onclick=\"getKary('".$_SESSION['lang']['find']." ".$_SESSION['lang']['kegiatan']."','4',event);\"  /></td>
		 		   </tr>			   
			   </tbody>
		   <tfoot>
		   </tfoot>
	   </table>
	   </div>
	   <button onclick=saveItemBast() class=mybutton>".$_SESSION['lang']['save']."</button>
	   <button onclick=nextItem() class=mybutton>".$_SESSION['lang']['cancel']."</button>	
	   <button onclick=bastBaru() class=mybutton>".$_SESSION['lang']['done']."</button>	 
	 </fieldset>

    <fieldset>
	   <legend>".$_SESSION['lang']['datatersimpan']."</legend>
	   <table class=sortable cellspacing=1 border=0 width=100%>
		   <thead>
		   <tr class=rowheader>
		   <td>No</td>
		    <td>".$_SESSION['lang']['kodebarang']."</td>
			<td>".$_SESSION['lang']['namabarang']."</td>
			<td>".$_SESSION['lang']['satuan']."</td>
			<td>".$_SESSION['lang']['jumlah']."</td>
			<td>".$_SESSION['lang']['pt']."</td>
			<td>".$_SESSION['lang']['untukunit']."</td>
			<td>".$_SESSION['lang']['kodeblok']."</td>
			<td>".$_SESSION['lang']['kegiatan']."</td>
			<td>".$_SESSION['lang']['kodenopol']."</td>
			<td></td>
 		   </tr>
		   </thead>
			   <tbody id=bastcontainer>			   
			   </tbody>
		   <tfoot>
		   </tfoot>
	   </table>
	 </fieldset>
	 	 
	 ";
	 
$frm[1].="<fieldset>
	   <legend>".$_SESSION['lang']['list']."</legend>
	  <fieldset><legend></legend>
	  ".$_SESSION['lang']['cari_transaksi']."
	  <input type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return validat(event);\" maxlength=30>
	  <button class=mybutton onclick=cariBast()>".$_SESSION['lang']['find']."</button>
	  </fieldset>
	  <table class=sortable cellspacing=1 border=0>
      <thead>
	  <tr class=rowheader>
	  <td>No.</td>
	  <td>".$_SESSION['lang']['sloc']."</td>
	  <td>".$_SESSION['lang']['tipe']."</td>
	  <td>".$_SESSION['lang']['momordok']."</td>
	  <td>".$_SESSION['lang']['tanggal']."</td>
	  <td>".$_SESSION['lang']['ptpemilikbarang']."</td>
	  <td>".$_SESSION['lang']['untukunit']."</td>	  	 
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
$hfrm[0]=$_SESSION['lang']['pengeluaranbarang'];
$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,200,950);
//===============================================	 
}
else
{
	echo " Error: Transaction Period missing";
}
CLOSE_BOX();
close_body();
?>