<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zFunction.php');
echo open_body();
?>
<script language=javascript1.2 src='js/log_mutasi.js'></script>
<?php
include('master_mainMenu.php');

if(isTransactionPeriod())//check if transaction period is normal
{
OPEN_BOX('',"<b>".$_SESSION['lang']['mutasi'].":</b>");

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

}



else{
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
	  ".$_SESSION['lang']['pilihgudang'].": <select id=sloc onchange=getPT(this.options[this.selectedIndex].value)>".$optsloc."</select>
	   ".$_SESSION['lang']['ptpemilikbarang']."<select id=pemilikbarang style='width:200px;'>
	   <option value=''></option>
	   </select>
	   <button onclick=setSloc('simpan') class=mybutton id=btnsloc>".$_SESSION['lang']['save']."</button>
	   <button onclick=setSloc('ganti') class=mybutton>".$_SESSION['lang']['ganti']."</button>
	  
	 </fieldset>";
$optlokasitujuan="<option value=''></option>";


if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{
	 $str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where 
	  kodeorganisasi not like '".$_SESSION['empl']['lokasitugas']."%' and tipe like '%GUDANG%' order by namaorganisasi";
}
else
{


//$optlokasitujuan.=ambilSeluruhGudang('','');
$str="  select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe like 'GUDANG%' 
           and left(induk,4) in(select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
           order by kodeorganisasi    
";
/*$tempreg=$_SESSION['empl']['regional'];
if(($tempreg=='SUMSEL')||($tempreg=='LAMPUNG'))
$str="  select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe like 'GUDANG%' 
           and induk in(select kodeunit from ".$dbname.".bgt_regional_assignment where regional in ('SUMSEL','LAMPUNG'))
           order by kodeorganisasi    
";*/
}


$res=mysql_query($str);
$optsloc="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
	$optlokasitujuan.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}

$optsubunit="<option value=''></option>";



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
	 <td>".$_SESSION['lang']['tujuan']."</td><td><select id=kegudang style='width:200px;' onchange=cekGudang(this)>".$optlokasitujuan."</select></td>
 	 <td>".$_SESSION['lang']['note']."</td><td><input type=text id=catatan class=myinputtext onkeypress=\"return tanpa_kutip(event);\" size=40 maxlength=80></td>
	 </td>
	 </tr>
         <tr>
	 <td>".$_SESSION['lang']['nokonosemen']."</td><td><input type=text id=konosemen onkeypress='return false' onclick=\"showWindowKonosemen('".$_SESSION['lang']['find']." ".$_SESSION['lang']['nokonosemen']."',event);\" class=myinputtext onkeypress=\"return tanpa_kutip(event);\" size=25  maxlength=35 /></td>
 	 <td>&nbsp;</td><td>&nbsp</td>
	 </td>
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
	   <table class=sortable cellspacing=1 border=0>
		   <thead>
		   <tr class=rowheader>
		    <td>".$_SESSION['lang']['kodebarang']."</td>
			<td>".$_SESSION['lang']['namabarang']."</td>
			<td>".$_SESSION['lang']['satuan']."</td>
			<td>".$_SESSION['lang']['jumlah']."</td>
 		   </tr>
		   </thead>
                        <tbody>
                                <tr class=rowcontent>
                                 <td><input type=text size=10 maxlength=10 id=kodebarang class=myinputtext onkeypress=\"return false;\" onclick=\"showWindowBarang('".$_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang']."',event);\"></td>
                                     <td><input type=text size=55 maxlength=100 id=namabarang class=myinputtext readonly onclick=\"showWindowBarang('".$_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang']."',event);\"></td>
                                     <td><input type=text size=5 maxlength=5 id=satuan class=myinputtext  onkeypress=\"return false;\" onclick=\"showWindowBarang('".$_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang']."',event);\"></td>
                                     <td><input type=text size=6 maxlength=10 id=qty value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\"></td>
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
	  <input type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return validat(event);\" maxlength=12>
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
	  <td>".$_SESSION['lang']['tujuan']."</td>	  	 
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
$hfrm[0]=$_SESSION['lang']['mutasi'];
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
