<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
//OPEN_BOX();
OPEN_BOX('',"<b>SIL SIP</b>");
?>
<?php
//ambil periode gaji sesuai dengan lokasi tugas
$lksiTugas=substr($_SESSION['empl']['lokasitugas'],0,4);
$optPeriode="<option value''>".$_SESSION['lang']['pilihdata']."</option>";
$sPeriode="select distinct periode from ".$dbname.".sdm_5periodegaji where sudahproses='0' order by periode desc";
$qPeriode=mysql_query($sPeriode) or die(mysql_error());
while($rPeriode=mysql_fetch_assoc($qPeriode))
{
	$optPeriode.="<option value=".$rPeriode['periode'].">".$rPeriode['periode']."</option>";
}
//ambil karyawand


if($_SESSION['empl']['bagian']=='IT' || $_SESSION['empl']['bagian']=='FIN' || $_SESSION['empl']['bagian']=='ACC')
{
    $sKry="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where lokasitugas='".$lksiTugas."'  and sistemgaji='Harian' order by namakaryawan asc";
}
else
{
    $sKry="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where lokasitugas='".$lksiTugas."' and tipekaryawan=4 and sistemgaji='Harian' order by namakaryawan asc";    
}



//echo $sKry;exit();
$qKry=mysql_query($sKry) or die(mysql_error());
while($rKry=mysql_fetch_assoc($qKry))
{
	$optKry.="<option value=".$rKry['karyawanid'].">".$rKry['namakaryawan']."</option>";
}
//ambil kodeorgannisasi dan organisasi dibawahnya
$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
if($_SESSION['empl']['tipelokasitugas']=='HOLDING' or $_SESSION['empl']['tipelokasitugas']=='KANWIL')
{
	$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi 
               where tipe in ('PABRIK','KANWIL','KEBUN','TRAKSI') and CHAR_LENGTH(kodeorganisasi)=4 and kodeorganisasi in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='KALIMANTAN') order by namaorganisasi asc";	
}
else
{
	$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where induk='".$_SESSION['empl']['lokasitugas']."' or  kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
}
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}
//ambil dept
$optDept="<option value=''>".$_SESSION['lang']['all']."</option>";
//$optTipe=$optDept;
$sDept="select * from ".$dbname.".sdm_5departemen order by nama asc";
$qDept=mysql_query($sDept) or die(mysql_error());
while($rDept=mysql_fetch_assoc($qDept))
{
	$optDept.="<option value=".$rDept['kode'].">".$rDept['nama']."</option>";
}

//ambil tipekaryawan 



if($_SESSION['empl']['bagian']=='IT' || $_SESSION['empl']['bagian']=='FIN' || $_SESSION['empl']['bagian']=='ACC')
{
    $sTipeKary="select distinct * from ".$dbname.".sdm_5tipekaryawan order by tipe asc";
    $optTipe="<option value=''>".$_SESSION['lang']['all']."</option>";
}
else
{
    $sTipeKary="select distinct * from ".$dbname.".sdm_5tipekaryawan where id=4 order by tipe asc";
    
}


$qTipeKary=mysql_query($sTipeKary) or die(mysql_error($conn));
while($rTipeKary=mysql_fetch_assoc($qTipeKary))
{
    $optTipe.="<option value='".$rTipeKary['id']."'>".$rTipeKary['tipe']."</option>";
}

$arr="##periode##kdBag##tPkary";
$arrKry="##period##idKry";
$arrAfd="##perod##idAfd##kdBag2##tPkary2";
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>

<script language=javascript src=js/keu_2laporanAnggaranKebun.js></script>
<script>
function  getPeriode()
{
    kdOrg=document.getElementById('idAfd').options[document.getElementById('idAfd').selectedIndex].value;
    tujuan='sdm_slave_2slipGajiHarianSilsipAfd';
    param='idAfd='+kdOrg;
    post_response_text(tujuan+'.php?proses=getPeriode', param, respog);
    function respog()
	{
		      if(con.readyState==4)
		      {
			        if (con.status == 200) {
						busy_off();
						if (!isSaveResponse(con.responseText)) {
							alert('ERROR TRANSACTION,\n' + con.responseText);
						}
						else {
							//alert(con.responseText);
							document.getElementById('perod').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  	
}
</script>

<link rel=stylesheet type=text/css href=style/zTable.css>
      <?php 
           if($_SESSION['empl']['tipelokasitugas']!='HOLDING' or $_SESSION['empl']['tipelokasitugas']=='KANWIL')
           {
      ?>

<div><br>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['slipgajiharianper'];?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['periode']?></label></td><td><select id="periode" name="periode" style="width:150px"><?php echo $optPeriode?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['bagian']?></label></td><td><select id="kdBag" name="kdBag" style="width:150px"><?php echo $optDept?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['tipekaryawan']?></label></td><td><select id="tPkary" name="tPkary" style="width:150px"><?php echo $optTipe?></select></td></tr>
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2" align="center">
<button onclick="zPreview('sdm_slave_2slipGajiHarianSilsip','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zPdf('sdm_slave_2slipGajiHarianSilsip','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,'sdm_slave_2slipGajiHarianSilsip.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>

</table>
</fieldset>
</div>
      
<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['slipgajiharianper']."/".$_SESSION['lang']['karyawan'];?></b><?php //echo $_SESSION['lang']['']?></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['periode']?></label></td><td><select id="period" name="period" style="width:150px"><?php echo $optPeriode?></select></td></tr>

<tr><td><label><?php echo $_SESSION['lang']['namakaryawan']?></label></td><td><select id="idKry" name="idKry" style="width:150px"><?php echo $optKry?></select></td></tr>
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2" align="center"><button onclick="zPreview('sdm_slave_2slipGajiHarianSilsip','<?php echo $arrKry?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zPdf('sdm_slave_2slipGajiHarianSilsip','<?php echo $arrKry?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button></td></tr>
</table>
</fieldset>
</div>
<div style="margin-bottom: 30px;">
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['slipgajiharianper']."/";?>Afdeling</b><?php //echo $_SESSION['lang']['']?></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['periode']?></label></td><td><select id="perod" name="perod" style="width:150px"><?php echo $optPeriode?></select></td></tr>

<tr><td><label><?php echo $_SESSION['lang']['unit']?></label></td><td>
<select id="idAfd" name="idAfd" style="width:150px"><?php echo $optOrg?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['bagian']?></label></td><td><select id="kdBag2" name="kdBag2" style="width:150px"><?php echo $optDept?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['tipekaryawan']?></label></td><td><select id="tPkary2" name="tPkary" style="width:150px"><?php echo $optTipe?></select></td></tr>
<tr><td colspan="2" align="center"><button onclick="zPreview('sdm_slave_2slipGajiHarianSilsipAfd','<?php echo $arrAfd?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zPdf('sdm_slave_2slipGajiHarianSilsipAfd','<?php echo $arrAfd?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,'sdm_slave_2slipGajiHarianSilsipAfd.php','<?php echo $arrAfd?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>
</table>
</fieldset>
</div>
<?php } else { ?>
      <div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['slipgajiharianper']."/";?>Afdeling</b><?php //echo $_SESSION['lang']['']?></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['unit']?></label></td><td>
<select id="idAfd" name="idAfd" style="width:150px"><?php echo $optOrg?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['periode']?></label></td><td><select id="perod" name="perod" style="width:150px"><?php echo $optPeriode?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['bagian']?></label></td><td><select id="kdBag2" name="kdBag2" style="width:150px"><?php echo $optDept?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['tipekaryawan']?></label></td><td><select id="tPkary2" name="tPkary2" style="width:150px"><?php echo $optTipe?></select></td></tr>
<tr><td colspan="2"><button onclick="zPreview('sdm_slave_2slipGajiHarianSilsipAfd','<?php echo $arrAfd?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zPdf('sdm_slave_2slipGajiHarianSilsipAfd','<?php echo $arrAfd?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,'sdm_slave_2slipGajiHarianSilsipAfd.php','<?php echo $arrAfd?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>
</table>
</fieldset>
</div>
      <?php }?>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>

</div></fieldset>



<?php

CLOSE_BOX();
echo close_body();
?>