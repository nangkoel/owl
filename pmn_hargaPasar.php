<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zLib.php');
include('lib/zFunction.php');
echo open_body();
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript1.2 src='js/pmn_hargapasar.js'></script>
<?php
$arr="##tglHarga##kdBarang##satuan##idPasar##idMatauang##hrgPasar##proses##status##catatan";
include('master_mainMenu.php');
OPEN_BOX();
$optBrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optKodeSat=$optKode=$optGoldar=$optBrg;
$sBrng="select distinct kodebarang,namabarang from ".$dbname.".log_5masterbarang where kelompokbarang='400' order by namabarang asc";
$qBrng=mysql_query($sBrng) or die(mysql_error($conn));
while($rBarang=mysql_fetch_assoc($qBrng))
{
    $optBrg.="<option value='".$rBarang['kodebarang']."'>".$rBarang['namabarang']."</option>";
}
$sData="select distinct kode  from ".$dbname.".setup_matauang order by kode asc";
$qData=mysql_query($sData) or die(mysql_error($conn));
while($rData=mysql_fetch_assoc($qData))
{
    $optKode.="<option value='".$rData['kode']."'>".$rData['kode']."</option>";
}
$arrenum=makeOption($dbname,'pmn_5pasar','id,namapasar');
foreach($arrenum as $key=>$val)
{
	$optGoldar.="<option value='".$key."'>".$val."</option>";
} 	
$arrSatuan=array("KG","TON");
foreach($arrSatuan as $der)
{
    $optKodeSat.="<option value='".$der."'>".$der."</option>";
}
$optStatus = getEnum($dbname,'pmn_hargapasar','status');
foreach($optStatus as $key=>$val) {
	$optStatus[$key] = ucfirst($val);
}

echo"<fieldset style=width:250px>
     <legend>".$_SESSION['lang']['hargapasar']."</legend>
	 <table>
	 <tr>
	   <td>".$_SESSION['lang']['tanggal']."</td>
	   <td><input type=text class=myinputtext id=tglHarga onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=\"width:150px;\" /></td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['komoditi']."</td>
	   <td><select id=kdBarang style=\"width:150px;\">".$optBrg."</select></td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['satuan']."</td>
	   <td><select id=satuan style=\"width:150px;\">".$optKodeSat."</select></td>
	 </tr>	
	 <tr>
	   <td>".$_SESSION['lang']['pasar']."</td>
	   <td><select id=idPasar style=\"width:150px;\">".$optGoldar."</select></td>
	 </tr>	 
	  <tr>
	   <td>".$_SESSION['lang']['matauang']."</td>
	   <td><select id=idMatauang style=\"width:150px;\">".$optKode."</select></td>
	 </tr> 
          <tr>
	   <td>".$_SESSION['lang']['harga']."</td>
	   <td><input type=text class=myinputtextnumber id=hrgPasar onkeypress=\"return angka_doang(event);\" style=\"width:150px;\"  /> </td>
	 </tr>
	 <tr>
		<td>".$_SESSION['lang']['status']."</td>
		<td>".makeElement('status','select','',array('style'=>'width:150px'),$optStatus)."</td>
	 </tr>
	 <tr>
		<td>".$_SESSION['lang']['catatan']."</td>
		<td>".makeElement('catatan','text','',array('style'=>'width:150px'))."</td>
	 </tr>
	 </table>
	 <input type=hidden value=insert id=proses>
	 <button class=mybutton onclick=saveFranco('pmn_slave_hargapasar','".$arr."')>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>
     </fieldset><input type='hidden' id=idFranco name=idFranco />";
CLOSE_BOX();
OPEN_BOX();

echo"<fieldset  style=width:650px><legend>".$_SESSION['lang']['list']."</legend>";
echo"<table cellpadding=1 cellspacing=1 border=0><tr><td>".$_SESSION['lang']['tanggal']." : <input type=text class=myinputtext id=tglCri onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10  />";
echo"&nbsp;".$_SESSION['lang']['komoditi']." : <select id=kdBrgCari style=\"width:150px;\">".$optBrg."</select>";
echo"&nbsp;".$_SESSION['lang']['pasar']." : <select id=idPsrCari style=\"width:150px;\">".$optGoldar."</select><button class=mybutton onclick=cariTransaksi()>".$_SESSION['lang']['find']."</button></td></tr></table>";
echo"
    <table class=sortable cellspacing=1 border=0>
     <thead>
	  <tr class=rowheader>
	   <td>No</td>
	   <td>".$_SESSION['lang']['tanggal']."</td>
	   <td>".$_SESSION['lang']['komoditi']."</td>
	   <td>".$_SESSION['lang']['satuan']."</td>
	   <td>".$_SESSION['lang']['pasar']."</td>
	   <td>".$_SESSION['lang']['matauang']."</td>
       <td>".$_SESSION['lang']['harga']."</td>
	   <td>".$_SESSION['lang']['status']."</td>
	   <td>".$_SESSION['lang']['catatan']."</td>
	   <td>Action</td>
	  </tr>
	 </thead>
	 <tbody id=container>";
	 echo"<script>loadData()</script>";

echo"</tbody>
     <tfoot>
	 </tfoot>
	 </table></fieldset>";
CLOSE_BOX();
echo close_body();
?>