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
//for($x=0;$x<=3;$x++)
//{
//	$dt=mktime(0,0,0,date('m')-$x,15,date('Y'));
//	$optPeriode.="<option value=".date("Y-m",$dt).">".date("m-Y",$dt)."</option>";
//}

//$optPabrik="<option value=''>".$_SESSION['lang']['all']."</option>";
$optReg=  makeOption($dbname, 'bgt_regional_assignment', 'kodeunit,regional');
$optPabrik="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optPabrik2="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOpt="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PABRIK'";
$qOpt=mysql_query($sOpt) or die(mysql_error());
while($rOpt=mysql_fetch_assoc($qOpt))
{
	$optPabrik.="<option value=".$rOpt['kodeorganisasi'].">".$rOpt['namaorganisasi']."</option>";
        if($optReg[$rOpt['kodeorganisasi']]=='SULAWESI'){
            $optPabrik2.="<option value=".$rOpt['kodeorganisasi'].">".$rOpt['namaorganisasi']."</option>";
        }
}
//$optProduk="<option value=''>".$_SESSION['lang']['all']."</option>";
$optProduk="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sPrd="select kodebarang,namabarang from ".$dbname.".log_5masterbarang where kodebarang like '4%'";
$qPrd=mysql_query($sPrd) or die(mysql_error());
while($rPrd=mysql_fetch_assoc($qPrd))
{
	$optProduk.="<option value=".$rPrd['kodebarang'].">".$rPrd['namabarang']."</option>";
}
$sGp="select DISTINCT substr(tanggal,1,7) as periode  from ".$dbname.".pabrik_produksi order by tanggal desc";
$qGp=mysql_query($sGp) or die(mysql_error());
while($rGp=mysql_fetch_assoc($qGp))
{
   $thn=explode("-", $rGp['periode']);
   if($thn[1]=='12')
   {
     $optPeriode.="<option value='".substr($rGp['periode'],0,4)."'>".substr($rGp['periode'],0,4)."</option>";
   }
    $optPeriode.="<option value='".$rGp['periode']."'>".substr($rGp['periode'],5,2)."-".substr($rGp['periode'],0,4)."</option>";
}

$arr="##kdPbrik##kdTangki";
$arr1="##kodeorg1##tanggal1";
$arr2="##kodeorg2##tanggal2";


?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<script language="javascript" src="js/pabrik_4persediaan.js"></script>

<link rel=stylesheet type=text/css href=style/zTable.css>
<?php
$frm[0].="<div style=margin-bottom: 30px;>";
$frm[0].="<fieldset>
<legend><b>".$_SESSION['lang']['laporanstok']."</b></legend>";
$frm[0].="<table cellspacing=1 border=0>
<tr><td><label>".$_SESSION['lang']['unit']."</label></td><td><select id=kdPbrik name=kdPbrik style=width:150px onchange=getTangki()>".$optPabrik."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['kodetangki']."</label></td><td><select id=kdTangki name=kdTangki style=width:150px><option value=\"\">".$_SESSION['lang']['all']."</option></select></td></tr>
<!--<tr><td><label>".$_SESSION['lang']['periode']."</label></td><td><select id=periode name=periode style=width:150px><option value=\"\">".$_SESSION['lang']['all']."</option>".$optPeriode."</select></td></tr>-->
<tr><td colspan=2><button onclick=zPreview('pabrik_slave_4persediaan','".$arr."','printContainer') class=mybutton name=preview id=preview>Preview</button>
    <button onclick=zPdf('pabrik_slave_4persediaan','".$arr."','printContainer') class=mybutton name=preview id=preview>PDF</button>
    <button onclick=zExcel(event,'pabrik_slave_4persediaan.php','".$arr."') class=mybutton name=preview id=preview>Excel</button></td></tr>
</table>
</fieldset>";
$frm[0].="</div>

<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>
</div></fieldset>";


$frm[1].="<div style=margin-bottom: 30px;>";
$frm[1].="<fieldset>
<legend><b>".$_SESSION['lang']['laporanstok']." vs ".$_SESSION['lang']['pengiriman']."</b></legend>";
$frm[1].="<table cellspacing=1 border=0>
<tr><td><label>".$_SESSION['lang']['unit']."</label></td><td><select id=kodeorg1 name=kodeorg1 style=width:150px>".$optPabrik."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['tanggal']."</label></td><td><input type=text class=myinputtext id=tanggal1 name=tanggal1 onmousemove=setCalendar(this.id) onkeypress=\"return false;\"  maxlength=10 style=\"width:150px;\" /></td></tr>
<tr><td colspan=2><button onclick=zPreview('pabrik_slave_4persediaan_kirim','".$arr1."','printContainer1') class=mybutton name=preview id=preview>Preview</button>
</table>
</fieldset>";
$frm[1].="</div>

<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer1' style='overflow:auto;height:350px;max-width:1220px'>
</div></fieldset>";

$frm[2].="<div style=margin-bottom: 30px;>";
$frm[2].="<fieldset>
<legend><b>".$_SESSION['lang']['laporanstok']."  HIP</b></legend>";
$frm[2].="<table cellspacing=1 border=0>
<tr><td><label>".$_SESSION['lang']['unit']."</label></td><td><select id=kodeorg2 name=kodeorg2 style=width:150px>".$optPabrik2."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['tanggal']."</label></td><td><input type=text class=myinputtext id=tanggal2 name=tanggal2 onmousemove=setCalendar(this.id) onkeypress=\"return false;\"  maxlength=10 style=\"width:150px;\" /></td></tr>
<tr><td colspan=2><button onclick=zPreview('pabrik_slave_4persediaanhip','".$arr2."','printContainer2') class=mybutton name=preview id=preview>Preview</button>
</table>
</fieldset>";
$frm[2].="</div>

<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer2' style='overflow:auto;height:350px;max-width:1220px'>
</div></fieldset>";

//<tr><td><label>".$_SESSION['lang']['produk']."</label></td><td><select id=produk1 name=produk1 style=width:150px>".$optProduk."</select></td></tr>
//    <button onclick=zPdf('pabrik_slave_4persediaan_kirim','".$arr1."','printContainer1') class=mybutton name=preview id=preview>PDF</button>
//    <button onclick=zExcel(event,'pabrik_slave_4persediaan_kirim.php','".$arr1."') class=mybutton name=preview id=preview>Excel</button></td></tr>

//========================
$hfrm[0]=$_SESSION['lang']['laporanstok'];
$hfrm[1]=$_SESSION['lang']['laporanstok']." vs ".$_SESSION['lang']['pengiriman'];
$hfrm[2]=$_SESSION['lang']['laporanstok']." HIP";
//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,200,900);
//===============================================

/*#======Select Prep======
# Get Data
//$where = " length(kodeorganisasi)='4'";
$optOrg = makeOption($dbname,'vhc_5master','kodevhc,kodevhc','','0');
#======End Select Prep======
#=======Form============
$els = array();
# Fields
$els[] = array(
  makeElement('tahun','label',$_SESSION['lang']['tahun']),
  makeElement('tahun','textnum',date(Y),array('style'=>'width:150px','maxlength'=>'16',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
  makeElement('kodeorg','select','',array('style'=>'width:150px'),$optOrg)
);
$els[] = array(
  makeElement('revisi','label',$_SESSION['lang']['revisi']),
  makeElement('revisi','textnum','0',array('style'=>'width:150px','maxlength'=>'80',
    'onkeypress'=>'return tanpa_kutip(event)'))
);

# Button
$param = '##tahun##kodeorg';
$container = 'printContainer';
$els['btn'] = array(
  makeElement('preview','btn','Preview',array('onclick'=>
    "zPreview('keu_slave_2laporanAnggaranKebun_print','".$param."','".$container."')")).
  makeElement('printPdf','btn','PDF',array('onclick'=>
    "zPdf('keu_slave_2laporanAnggaranKebun_print','".$param."','".$container."')")).
  makeElement('printExcel','btn','Excel',array('onclick'=>"excelBudKebun()"))
);

# Generate Field
echo "<div style='margin-bottom:30px'>";
echo genElTitle('Laporan Anggaran Traksi',$els);
echo "</div>";
echo "<fieldset style='clear:both'><legend><b>Print Area</b></legend>";
echo "<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'></div></fieldset>";
#=======End Form============*/

CLOSE_BOX();
echo close_body();
?>