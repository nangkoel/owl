<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
require_once('lib/zLib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/log_laporan.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['laporanstok']).'</b>');
$whr="namaorganisasi!=''";
$optNmOrg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi',$whr);
//get existing period
$str="select distinct periode from ".$dbname.".log_5saldobulanan
      order by periode desc";
$res=mysql_query($str);
$optper="<option value=''>".$_SESSION['lang']['sekarang']."</option>";
while($bar=mysql_fetch_object($res))
{
	$optper.="<option value='".$bar->periode."'>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</option>";
}	
//=================ambil PT;  
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
      where tipe='PT'
	  order by namaorganisasi";
$res=mysql_query($str);
$optpt="";
while($bar=mysql_fetch_object($res))
{
	$optpt.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";

}

//=================ambil gudang;  
$str="select distinct a.kodeorg,b.namaorganisasi from ".$dbname.".setup_periodeakuntansi a
      left join ".$dbname.".organisasi b
	  on a.kodeorg=b.kodeorganisasi
      where b.tipe='GUDANG'
	  order by namaorganisasi";
$res=mysql_query($str);
$optgudang="<option value=''>".$_SESSION['lang']['all']."</option>";
while($bar=mysql_fetch_object($res))
{
	$optgudang.="<option value='".$bar->kodeorg."'>".$bar->namaorganisasi."</option>";

}
$str="select distinct a.kodeorg,b.namaorganisasi from ".$dbname.".setup_periodeakuntansi a
      left join ".$dbname.".organisasi b
	  on a.kodeorg=b.kodeorganisasi
      where b.tipe='GUDANG'
	  order by namaorganisasi";
$res=mysql_query($str);
$optgudang2="<option value=''>".$_SESSION['lang']['all']."</option>";
while($bar=mysql_fetch_object($res))
{
	$optgudang2.="<option value='".$bar->kodeorg."'>".$bar->namaorganisasi."</option>";

}
$optUnit="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optUnit2=$optGdng=$optUnit;
$sUnit="select distinct substr(kodeorganisasi,1,4) as kodeorganisasi from ".$dbname.".organisasi where tipe like 'GUDANG%' order by namaorganisasi asc";
$qUnit=mysql_query($sUnit) or die(mysql_error($conn));
while($rUnit=mysql_fetch_assoc($qUnit))
{
    $optUnit.="<option value='".$rUnit['kodeorganisasi']."'>".$optNmOrg[$rUnit['kodeorganisasi']]."</option>";

}
$sUnit2="select distinct substr(kodeorganisasi,1,4) as kodeorganisasi from ".$dbname.".organisasi
        where tipe like 'GUDANG%' and namaorganisasi!='' order by namaorganisasi asc";
$qUnit2=mysql_query($sUnit2) or die(mysql_error($conn));
while($rUnit2=mysql_fetch_assoc($qUnit2))
{
    $optUnit2.="<option value='".$rUnit2['kodeorganisasi']."'>".$optNmOrg[$rUnit2['kodeorganisasi']]."</option>";
}
$optPeriode="<option value=''>".$_SESSION['lang']['all']."</option>";

for($x=0;$x<13;$x++)
{
	$dt=mktime(0,0,0,date('m')-$x,15,date('Y'));
	$optPeriode.="<option value=".date("Y-m",$dt).">".date("m-Y",$dt)."</option>";
        $optPeriode2.="<option value=".date("Y-m",$dt).">".date("m-Y",$dt)."</option>";
}

echo"<br /><fieldset style=width:250px;float:left;>
     <legend>".$_SESSION['lang']['laporanstok']." Per ".$_SESSION['lang']['pt']."</legend>
         <table border=0 cellpadding=1 cellspacing=1>
         <tr><td>
	 ".$_SESSION['lang']['pt']."</td><td><select id=pt style='width:150px;' onchange=hideById('printPanel')>".$optpt."</select></td></tr><!--<tr><td>
	 ".$_SESSION['lang']['sloc']."</td><td><select id=gudang style='width:150px;' onchange=hideById('printPanel')>".$optgudang."</select></td></tr>--><tr><td>
	 ".$_SESSION['lang']['periode']."</td><td><select id=periode onchange=hideById('printPanel')>".$optper."</select></td></tr><tr><td colspan=2 align=center>
	 <button class=mybutton onclick=getLaporanFisik()>".$_SESSION['lang']['proses']."</button></td></tr></table>
	 </fieldset>
    <fieldset style=width:250px;float:left;>
     <legend>".$_SESSION['lang']['persediaanfisik'].' Per '.$_SESSION['lang']['sloc']."</legend>
         <table cellpadding=1 cellspacing=1 border=0>
         <tr><td>
	 ".$_SESSION['lang']['unit']."</td><td><select id=unitDt style='width:150px;' onchange=getGudangDt()>".$optUnit."</select></td></tr>
	 <tr><td>".$_SESSION['lang']['sloc']."</td><td><select id=gudang2 style='width:150px;' onchange=hideById('printPanel2')>".$optGdng."</select></td></tr>
	 <tr><td>".$_SESSION['lang']['periode']."</td><td><select id=periode2 onchange=hideById('printPanel2')>".$optPeriode."</select></td></tr>
	 <tr><td colspan=2><button class=mybutton onclick=getLaporanFisik2()>".$_SESSION['lang']['proses']."</button></td></tr></table>
	 </fieldset>
      <fieldset style=width:250px;>
     <legend>".$_SESSION['lang']['persediaanfisik'].' Per '.$_SESSION['lang']['unit']."</legend>
         <table cellpadding=1 cellspacing=1 border=0>
         <tr><td>
	 ".$_SESSION['lang']['unit']."</td><td><select id=unitDt2 style='width:150px;' onchange=hideById('printPanel3')>".$optUnit2."</select></td></tr>
	 <tr><td>".$_SESSION['lang']['periode']."</td><td><select id=periode3 onchange=hideById('printPanel3')>".$optPeriode2."</select></td></tr>
	 <tr><td colspan=2><button class=mybutton onclick=getLaporanFisik3()>".$_SESSION['lang']['proses']."</button></td></tr></table>
	 </fieldset>";

CLOSE_BOX();
OPEN_BOX('','Result:');
echo"<span id=printPanel style='display:none;'>
     <img onclick=fisikKeExcel(event,'log_laporanPersediaanFisik_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
	 <img onclick=fisikKePDF(event,'log_laporanPersediaanFisik_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>
	 </span>   
         <span id=printPanel2 style='display:none;'>
     <img onclick=fisikKeExcel2(event,'log_slaveLaporanPersediaanFisikUnit.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
	 <img onclick=fisikKePDF2(event,'log_slaveLaporanPersediaanFisikUnit.php') title='PDF' class=resicon src=images/pdf.jpg>
	 </span>
         <span id=printPanel3 style='display:none;'>
     <img onclick=fisikKeExcel3(event,'log_slaveLaporanPersediaanFisikUnit2.php') src=images/excel.jpg class=resicon title='MS.Excel'>
	 <img onclick=fisikKePDF3(event,'log_slaveLaporanPersediaanFisikUnit2.php') title='PDF' class=resicon src=images/pdf.jpg>
	 </span>
	 <div style='width:100%;height:359px;overflow:scroll;'>
       <table class=sortable cellspacing=1 border=0 width=100%>
	     <thead>
		    <tr>
			  <td align=center>No.</td>
			  <td align=center>".$_SESSION['lang']['pt']."</td>
			  <td align=center>".$_SESSION['lang']['sloc']."</td>
			  <td align=center>".$_SESSION['lang']['periode']."</td>
			  <td align=center>".$_SESSION['lang']['kodebarang']."</td>
			  <td align=center>".$_SESSION['lang']['namabarang']."</td>
			  <td align=center>".$_SESSION['lang']['satuan']."</td>
			  <td align=center>".$_SESSION['lang']['saldoawal']."</td>
			  <td align=center>".$_SESSION['lang']['masuk']."</td>
			  <td align=center>".$_SESSION['lang']['keluar']."</td>
			  <td align=center>".$_SESSION['lang']['saldo']."</td>
			</tr>  
		 </thead>
		 <tbody id=container>
		 </tbody>
		 <tfoot>
		 </tfoot>		 
	   </table>
     </div>";
CLOSE_BOX();
close_body();
?>