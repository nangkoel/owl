<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/master_barang.js></script>
<?php
include('master_mainMenu.php');
OPEN_BOX();
if($_SESSION['language']=='EN'){
    $zz='kelompok1 as kelompok';
}
else{
    $zz='kelompok';
}
//pengambilan kelompok barang dari table kelompok barang
$str="select kode,".$zz." from ".$dbname.".log_5klbarang order by kode asc";
$res=mysql_query($str);
$optkelompok="<option value=''></option>";
//create option search
$optsearch="<option value=All>".$_SESSION['lang']['all']."</option>";
while($bar=mysql_fetch_object($res))
{
	$optkelompok.="<option value='".$bar->kode."'>".$bar->kelompok." [ ".$bar->kode." ] </option>";
    $optsearch.="<option value='".$bar->kode."'>".$bar->kelompok." [ ".$bar->kode." ] </option>";
}

//pengambilan satuan dari table setup_satuan
$str="select distinct satuan from ".$dbname.".setup_satuan order by satuan";
$res=mysql_query($str);
$optsatuan='';
while($bar=mysql_fetch_object($res))
{
	$optsatuan.="<option value='".$bar->satuan."'>".$bar->satuan."</option>";
}
?>
<fieldset>
	<legend><?echo $_SESSION['lang']['materialmaster'];?></legend>
<table border="0" cellspacing="0">
  <tr>
    <td><?echo $_SESSION['lang']['materialgroupcode'];?>
    </td>
    <td><select id="kelompokbarang" onchange=getMaterialNumber(this.options[this.selectedIndex].value)><?echo $optkelompok;?></select></td>
  </tr>
  <tr>
    <td><?echo $_SESSION['lang']['materialcode'];?></td>
    <td><input type=text  class=myinputtext  id="kodebarang" disabled size=10></td>
  </tr>
  <tr>
    <td><?echo $_SESSION['lang']['materialname'];?></td>
	<td><input type="text"  class=myinputtext id="namabarang" size=75 maxlength=150 onkeypress="return tanpa_kutip(event)"></td>
  </tr>
  <tr>
    <td><?echo $_SESSION['lang']['satuan'];?></td>
    <td><select id="satuan"><?echo $optsatuan;?></select></td>
  </tr>
  <tr>
    <td><?echo $_SESSION['lang']['minstok'];?></td>
	<td><input type="text"  class=myinputtextnumber id="minstok" value=0 size=4 maxlength=4 onkeypress="return angka_doang(event)"></td>
  </tr>
  <tr>
    <td><?echo $_SESSION['lang']['nokartubin'];?></td>
	<td><input type="text"  class=myinputtext id="nokartu" size=10 maxlength=10 onkeypress="return tanpa_kutip(event)"></td>
  </tr>  
  <tr>
    <td><?echo $_SESSION['lang']['konversi'];?></td>
    <td><select id="konversi"><option value=1>Yes</option><option value=0>No</option></select></td>
  </tr>  
  <input type=hidden value='insert' id=method>
</table>
<button class=mybutton onclick=simpanBarangBaru()><?echo $_SESSION['lang']['save'];?></button>
<button class=mybutton onclick=cancelBarang()><?echo $_SESSION['lang']['cancel'];?></button>
</fieldset>
<?php
CLOSE_BOX();
OPEN_BOX();

echo "
<img src='images/pdf.jpg' title='PDF Format' style='width:20px;height:20px;cursor:pointer' onclick=\"masterbarangPDF(event)\">&nbsp;
<img src='images/printer.png' title='Print Page' style='width:20px;height:20px;cursor:pointer' onclick='javascript:print()'>
<fieldset style='width:800px;background-color:#A9D4F4'>
<legend><b>".$_SESSION['lang']['find']."</b></legend>
Text <input type=text id=txtcari class=myinputtext size=40 onkeypress=\"return tanpa_kutip(event);\" maxlength=30>
".$_SESSION['lang']['on']."' <select id=optcari  onchange=getMaterialNumber(this.options[this.selectedIndex].value)>".$optsearch."</select>
<button class=mybutton onclick=cariBarang()>".$_SESSION['lang']['find']."</button>
</fieldset>
<div style='width:98%;overflow:scroll;height:230px;'>
<b id=caption></b>
      <table cellspacing=1 border=0 class=sortable>
      <thead>
	  <tr class=rowheader>
	  <td>No</td>
	  <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['materialgroupcode'])."</td>
	  <td>".$_SESSION['lang']['materialcode']."</td>
	  <td>".$_SESSION['lang']['materialname']."</td>
	  <td>".$_SESSION['lang']['satuan']."</td>
	  <td>".$_SESSION['lang']['keterangan']."</td>
	  <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['minstok'])."</td>
	  <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['nokartubin'])."</td>
	  <td>".$_SESSION['lang']['konversi']."</td>
	  <td>".$_SESSION['lang']['tidakaktif']."</td>	  
	  <td align=center>Detail<br>".$_SESSION['lang']['photo']."</td>
	  <td></td>
	  </tr>
	  </thead>
	  <tbody id=container>
	  </tbody>
	  <tfoot>
	  </tfoot>
	  </table>
</div>";
CLOSE_BOX();
echo close_body();
?>