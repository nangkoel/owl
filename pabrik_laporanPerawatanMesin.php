<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['pemeliharaanMesinReport']).'</b>'); //1 O
?>
<!--<script type="text/javascript" src="js/log_2keluarmasukbrg.js" /></script>
-->
<script type="text/javascript" src="js/pabrik_laporanPerawatanMesin.js"></script>
<div id="action_list">
<?php
$sOrg="select kodeorganisasi, namaorganisasi from ".$dbname.".organisasi where tipe='PABRIK'";
$qOrg=mysql_query($sOrg) or die(mysql_error());
while($rOrg=mysql_fetch_assoc($qOrg))
{
        $optPabrik.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}
$str="select distinct periode from ".$dbname.".log_5saldobulanan order by periode desc";
$res=mysql_query($str);
$optper="<option value='0'>All</option>";
while($bar=mysql_fetch_object($res))
{
        $optper.="<option value='".$bar->periode."'>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</option>";
}	


echo"<table>
     <tr valign=moiddle>
                 <td><fieldset><legend>".$_SESSION['lang']['pilihdata']."</legend>"; 
                        echo $_SESSION['lang']['pabrik'].":<select id=pbrkId name=pbrkId style=width:150px onchange=getStation(0,0)><option value=''>".$_SESSION['lang']['pilihdata']."</option>".$optPabrik."</select>&nbsp;"; 
                        echo $_SESSION['lang']['statasiun'].":<select id=statId name=statId style=width:150px ><option value=''>".$_SESSION['lang']['pilihdata']."</option></select>&nbsp;";
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
<!--<fieldset>
<legend><?php echo "Other&nbsp;".$_SESSION['lang']['data']?></legend>
<table cellspacing="1" border="0">
<tr><td><?php echo $_SESSION['lang']['mesin']?> : <select id="msnId" name="msnId" style="width:150px" onChange="getDataMsn()"></select>&nbsp;<?php echo $_SESSION['lang']['nm_brg']?> : <input type="text" id="nm_goods" name="nm_goods" maxlength="35" onKeyPress="return tanpa_kutip(event)" onClick="cari_brng('<?php echo $_SESSION['lang']['findBrg']?>','<fieldset><legend><?php echo $_SESSION['lang']['findnoBrg']?></legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div>','',event)" /><input type="hidden" id="kd_br" name="kd_br" />  </td></tr>
</table>
</fieldset>
-->
<img onclick=dataKeExcel(event,'pabrikPemeliharaanMesinExcel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
<img onclick=dataKePDF(event) title='PDF' class=resicon src=images/pdf.jpg>
<div id="hasil_cari" name="hasil_cari" style="display:none">
    <fieldset>
    <legend><?php echo $_SESSION['lang']['result']?></legend>
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