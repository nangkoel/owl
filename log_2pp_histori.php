<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<?php
if($_SESSION['language']=='EN'){
    $zz='kelompok1';
}else{
    $zz='kelompok';
}

$optKelompok=makeOption($dbname, 'log_5klbarang', "kode,".$zz);
$optNmOrg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$opKlmpkBrg="<option value=''>".$_SESSION['lang']['all']."</option>";
$sKelompokBrg="select distinct substr(kodebarang,1,3) as kelompokBrg from ".$dbname.".log_po_vw order by kodebarang asc";
$qKlmpkBrg=mysql_query($sKelompokBrg) or die(mysql_error());
while($rKlmplkBrg=  mysql_fetch_assoc($qKlmpkBrg))
{
    $opKlmpkBrg.="<option value='".$rKlmplkBrg['kelompokBrg']."'>".$rKlmplkBrg['kelompokBrg']." - ".$optKelompok[$rKlmplkBrg['kelompokBrg']]."</option>";
}
$optListUnit="<option value=''>".$_SESSION['lang']['all']."</option>";
$sListUnit="select distinct kodeorg from ".$dbname.".log_prapoht where close='2'";
$qListUnit=mysql_query($sListUnit) or die(mysql_error($sListUnit));
while($rListUnit=mysql_fetch_assoc($qListUnit))
{
   $optListUnit.="<option value='".$rListUnit['kodeorg']."'>".$optNmOrg[$rListUnit['kodeorg']]."</option>";
}
$optPeriodeCari="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sPeriodeCari="select distinct substr(tanggal,1,7) as periode from ".$dbname.".log_prapoht order by substr(tanggal,1,7) desc";
$qPeriodeCari=mysql_query($sPeriodeCari) or die(mysql_error());
while($rPeriodeCari=mysql_fetch_assoc($qPeriodeCari))
{
   $optPeriodeCari.="<option value='".$rPeriodeCari['periode']."'>".$rPeriodeCari['periode']."</option>";
}
$optLokal="<option value=''>".$_SESSION['lang']['all']."</option>";
if($_SESSION['empl']['tipelokasitugas']!='KANWIL')
{
    $arrPo=array("0"=>"Head Offcice","1"=>"Local");
}
else
{
    $arrPo=array("1"=>"Local");
}
foreach($arrPo as $brsLokal =>$isiLokal)
{
    $optLokal.="<option value=".$brsLokal.">".$isiLokal."</option>";
}
$optStatusPP="<option value='2'>".$_SESSION['lang']['pilihdata']."</option>";
$stataPP=array("0"=>"On Process","1"=>$_SESSION['lang']['sdhPO']);
foreach($stataPP as $dataIni=>$listNama)
{
   $optStatusPP.="<option value='".$dataIni."'>".$listNama."</option>";
}
$optPur="<option value=''>".$_SESSION['lang']['all']."</option>";
if($_SESSION['empl']['tipelokasitugas']!='KANWIL')
{
$sPur="select karyawanid,namakaryawan from ".$dbname.".datakaryawan 
       where (bagian='PUR'or kodejabatan='17') and kodejabatan!='5' 
       and (tanggalkeluar>'".date('Y-m-d')."' or tanggalkeluar='0000-00-00')  order by namakaryawan asc";
}
else
{
$sPur="select karyawanid,namakaryawan from ".$dbname.".datakaryawan 
       where (bagian='PUR'or kodejabatan='17') and kodejabatan!='5' and lokasitugas='".$_SESSION['empl']['lokasitugas']."'
       and (tanggalkeluar>'".date('Y-m-d')."' or tanggalkeluar='0000-00-00')  order by namakaryawan asc";
}
//exit("Error".$sPur);
$qPur=fetchData($sPur);
foreach($qPur as $brsKary)
{
$optPur.="<option value=".$brsKary['karyawanid'].">".$brsKary['namakaryawan']."</option>";
}
$arr="##klmpkBrg##kdUnit##periode##lokasi##statId##purId";

?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>

<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['ppLap']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['periode']?></label></td><td><select id="periode" name="periode" style="width:150px"><?php echo $optPeriodeCari?></select></td></tr>
        <tr><td><label><?php echo $_SESSION['lang']['status']?></label></td><td><select id="statId" name="statId" style="width:150px"><?php echo $optStatusPP?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['kelompokbarang']?></label></td><td><select id="klmpkBrg" name="klmpkBrg" style="width:150px"><?php echo $opKlmpkBrg?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['pt']?></label></td><td><select id="kdUnit" name="kdUnit" style="width:150px"><?php echo $optListUnit;?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['lokasiBeli']?></label></td><td><select id="lokasi" name="lokasi" style="width:150px"><?php echo $optLokal?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['purchaser']?></label></td><td><select id="purId" name="purId" style="width:150px"><?php echo $optPur?></select></td></tr>


<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('log_2slave_pp_histori','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zExcel(event,'log_2slave_pp_histori.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>

</table>
</fieldset>
</div>


<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>

</div></fieldset>

<?php
CLOSE_BOX();
echo close_body();
?>