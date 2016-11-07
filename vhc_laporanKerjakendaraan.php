<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['laporanPekerjaan']).'</b>'); //1 O
?>
<!--<script type="text/javascript" src="js/log_2keluarmasukbrg.js" /></script>
-->
<script type="text/javascript" src="js/vhc_laporanKerjaKendaraan.js" /></script>
<div id="action_list">
<?php

$optPt="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$whr="where tipe='TRAKSI'";
if ($_SESSION['empl']['tipelokasitugas']=='KANWIL'){
    $whr.=" and LEFT(kodeorganisasi,4) in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";
}
$sOrg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi ".$whr;
$qOrg=fetchData($sOrg);
foreach($qOrg as $brsOrg)
{
    $optPt.="<option value=".$brsOrg['kodeorganisasi'].">".$brsOrg['namaorganisasi']."</option>";
}



echo"<table>
     <tr valign=moiddle>
                 <td><fieldset><legend>".$_SESSION['lang']['pilihdata']."</legend>"; 
                        echo $_SESSION['lang']['unit'].":<select id=company_id name=company_id onChange=get_jnsVhc() style=width:200px>".$optPt."</select>&nbsp;"; 
                        echo $_SESSION['lang']['jenisvch'].":<select id=jnsVhc name=jnsVhc onchange=\"getKdVhc()\" style=width:100px><option  value=''>".$_SESSION['lang']['all']."</option></select>&nbsp;";
                        echo $_SESSION['lang']['kodevhc'].":<select id=kdVhc name=kdVhc style=width:100px><option  value=''>".$_SESSION['lang']['all']."</option></select>&nbsp;";
                        echo $_SESSION['lang']['tgldari'].":<input type=\"text\" class=\"myinputtext\" id=\"tglAwal\" name=\"tglAwal\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:100px;\" />";
                        echo $_SESSION['lang']['tglsmp'].":<input type=\"text\" class=\"myinputtext\" id=\"tglAkhir\" name=\"tglAkhir\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:100px;\" />";
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
   <div id="hasil_cari" name="hasil_cari">
    <fieldset>
    <legend><?php echo $_SESSION['lang']['result']?></legend>
     <img onclick=dataKeExcel(event,'vhc_slave_laporanKerjaKendaraan.php') src=images/excel.jpg class=resicon title='MS.Excel'> 

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