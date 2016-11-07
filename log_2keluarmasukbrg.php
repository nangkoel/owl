<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['mutasi']).'</b>'); //1 O
?>
<script type="text/javascript" src="js/log_2keluarmasukbrg.js" /></script>
<div id="action_list">
<?php
$optPt="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$spt="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='PT'";
$qpt=mysql_query($spt) or die(mysql_error());
while($rpt=mysql_fetch_assoc($qpt))
{
	$optPt.="<option value=".$rpt['kodeorganisasi'].">".$rpt['namaorganisasi']."</option>";
}
$str="select distinct substr(tanggal,1,7) as tanggal from ".$dbname.".log_transaksiht
      order by tanggal desc";
//echo $str;
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$periode=substr($bar->tanggal,0,7);
	$optper.="<option value='".$periode."'>".substr($periode,5,2)."-".substr($periode,0,4)."</option>";
}	

$optGudang="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sgdng="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe like 'GUDANG%'";
$qgdng=mysql_query($sgdng) or die(mysql_error());
while($rgdng=mysql_fetch_assoc($qgdng))
{
	$optGudang.="<option value=".$rgdng['kodeorganisasi'].">".$rgdng['namaorganisasi']."</option>";
}
echo"<table>
     <tr valign=moiddle>
		 <td><fieldset><legend>".$_SESSION['lang']['pilihdata']."</legend>"; 
			echo $_SESSION['lang']['pt'].":<select id=company_id name=company_id onchange=getGudangPt()>".$optPt."</select>&nbsp;"; 
			echo $_SESSION['lang']['pilihgudang'].":<select id=gudang_id name=gudang_id>".$optGudang."</select>";
			echo $_SESSION['lang']['periode'].":<select id=period name=period>".$optper."</select>";
			echo"<button class=mybutton onclick=save_pil()>".$_SESSION['lang']['save']."</button>
			     <button class=mybutton onclick=ganti_pil()>".$_SESSION['lang']['ganti']."</button>";
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
<fieldset>
<legend><?php echo $_SESSION['lang']['findBrg']?></legend>
<table cellspacing="1" border="0">
<tr><td><?php echo $_SESSION['lang']['nm_brg']?></td><td>:</td><td><input type="text" id="nm_goods" name="nm_goods" maxlength="35" onKeyPress="return tanpa_kutip(event)" /> <button class="mybutton" onClick="cari_brng('<?php echo $_SESSION['lang']['findBrg']?>','<fieldset><legend><?php echo $_SESSION['lang']['findnoBrg']?></legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div>','',event)"><?php echo $_SESSION['lang']['find']?> </button></td></tr>
</table>
</fieldset>
    <div id="hasil_cari" name="hasil_cari" style="display:none;">
    <fieldset>
    <legend><?php echo $_SESSION['lang']['result']?></legend>
     <img onclick=dataKeExcel(event,'log_laporanKeluarMasukPerBarang_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
	 <img onclick=dataKePDF(event) title='PDF' class=resicon src=images/pdf.jpg>

     <table cellspacing="1" border="0" id="table_data_barang">
   <tbody id="isi_conten">
    <tr id="isi_data_barang">
    <td><?php echo $_SESSION['lang']['kodebarang']?></td>
    <td>:</td>
    <td id="kd_brg"></td>
    </tr>
    <tr id="isi_data_barang">
    <td><?php echo $_SESSION['lang']['namabarang']?></td>
    <td>:</td>
    <td id="nm_brg"></td>
    </tr>
    <tr id="isi_data_barang">
    <td><?php echo $_SESSION['lang']['satuan']?></td>
    <td>:</td>
    <td id="satuan_brg"></td>
    </tr>
    </tbody>
    </table>
    <table cellspacing="1" border="0">
		<thead>
        	<tr class="rowheader">
            <td>No.</td>
            <td align="center"><?php echo $_SESSION['lang']['notransaksi']?></td>
           <td align="center"><?php echo $_SESSION['lang']['tanggal']?></td>
            <td align="center"><?php echo $_SESSION['lang']['saldoawal']?> </td>
            <td align="center"><?php echo $_SESSION['lang']['masuk']?> </td>
            <td align="center"><?php echo $_SESSION['lang']['keluar']?> </td>
            <td align="center"><?php echo $_SESSION['lang']['saldo']?> </td>
            </tr>
        </thead>
        <tbody id="contain">
        </tbody>
    </table>
    </fieldset>
    </div>


</div>



<?php
CLOSE_BOX();
?>







































<?php
echo close_body();
?>