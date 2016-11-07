<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['laporanPabrikTimbangan']).'</b>'); //1 O
?>
<!--<script type="text/javascript" src="js/log_2keluarmasukbrg.js" /></script>
-->
<script type="text/javascript" src="js/pabrik_2timbangan.js" /></script>
<div id="action_list">
<?php
$sBrg="select namabarang,kodebarang from ".$dbname.".log_5masterbarang where `kelompokbarang`='400'";
$qBrg=mysql_query($sBrg) or die(mysql_error());
while($rBrg=mysql_fetch_assoc($qBrg))
{
	$optBrg.="<option value=".$rBrg['kodebarang'].">".$rBrg['namabarang']."</option>";
}
$sPbrik="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PABRIK'";
$qPabrik=mysql_query($sPbrik) or die(mysql_error());
while($rPabrik=mysql_fetch_assoc($qPabrik))
{
	$optPabrik.="<option value=".$rPabrik['kodeorganisasi']." ".($rPabrik['kodeorganisasi']==$kdPbrk?'selected':'').">".$rPabrik['namaorganisasi']."</option>";
}


echo"<table>
     <tr valign=moiddle>
		 <td><fieldset><legend>".$_SESSION['lang']['pilihdata']."</legend>"; 
			echo $_SESSION['lang']['namabarang'].":<select id=kdBrg name=kdBrg style=width:200px><option value=0>All</option>".$optBrg."</select>&nbsp;"; 
			echo $_SESSION['lang']['pabrik'].":<select id=kdPbrk name=kdPbrk style=width:100px>".$optPabrik."</select>&nbsp;";
			echo $_SESSION['lang']['tanggal'].":<input type=text class=myinputtext id=tglTrans name=tglTrans onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />";
			echo "s/d". $_SESSION['lang']['tanggal'].":<input type=text class=myinputtext id=tglTrans1 name=tglTrans1 onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />";
			echo"<button class=mybutton onclick=savePil()>".$_SESSION['lang']['save']."</button>
			     <button class=mybutton onclick=gantiPil()>".$_SESSION['lang']['ganti']."</button>";
echo"</fieldset></td>
     </tr>
	 </table> "; 
?>
</div>
<?php 
CLOSE_BOX();
OPEN_BOX();

?>
<div id="cari_barang" name="cari_barang">
   <div id="hasil_cari" name="hasil_cari">
    <fieldset>
    <legend><?php echo $_SESSION['lang']['result']?></legend>
     <img onclick=dataKeExcel(event,'pabrik_slaveLaporanTimbanganExcel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
	 <img onclick=dataKePDF(event) title='PDF' class=resicon src=images/pdf.jpg>
        <div id="contain">
        </div>
    </fieldset>
    </div>
</div>
<?php
CLOSE_BOX();
?>
<?php
echo close_body();
?>