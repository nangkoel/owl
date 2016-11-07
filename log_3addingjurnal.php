<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
$frm[0]='';
//$frm[1]='';
//$frm[2]='';
?>
<script language=javascript src=js/zMaster.js></script>
<script language=javascript src=js/zSearch.js></script>
<<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<script language=javascript src=js/log_3addingjurnal.js></script>
<?php
$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optOrg=$optPeriode;
$sOrg="select  kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where
      tipe like '%GUDANG%' and kodeorganisasi like '%".$_SESSION['empl']['lokasitugas']."%' order by namaorganisasi asc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg)){
    $optOrg.="<option value='".$rOrg['kodeorganisasi']."'>".$rOrg['namaorganisasi']."</option>";
}
$arr="##kdOrg##periodeGdng##tpTransaksi##listTransaksi";


$frm[0].="<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style=\"float: left;\">
<legend><b>Update Data Inputan Panen</b></legend>
<table cellspacing=\"1\" border=\"0\" >
<tr><td><label>".$_SESSION['lang']['kodeorg']."</label></td><td>
    <select id=\"kdOrg\" name=\"kdOrg\" style=\"width:150px\" onchange='getPrd()'>".$optOrg."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['periode']."</label></td>
    <td><select id=\"periodeGdng\" name=\"periodeGdng\"  style=\"width:150px;\" onchange='getTptrk()'>".$optPeriode."</select></td>
</tr>
<tr><td><label>".$_SESSION['lang']['tipetransaksi']."</label></td>
    <td><select id=\"tpTransaksi\" name=\"tpTransaksi\"  style=\"width:150px;\">".$optPeriode."</select></td>
</tr>
<tr>
	   <td>".$_SESSION['lang']['notransaksi']."</td>
	   <td><textarea id=listTransaksi name=listTransaksi></textarea></td>
	 </tr>

<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>
<tr><td colspan=\"2\"><button onclick=\"zPreview('log_slave_3addingjurnal','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button></td></tr>
</table>
</fieldset>
</div>


<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id=awal>
    <div id='printContainer' style='overflow:auto;height:350px;max-width:1220px;'>

    </div>
</div>
<div id=detailData style=display:none>
<div id=isiData>
</div>
</div>
</fieldset>";
$optKegiatanNm=  makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
$optKegiatan.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
if($_SESSION['empl']['tipelokasitugas']!='HOLDING'){
    $sKegiatan="select distinct kodekegiatan,regional from ".$dbname.".kebun_5psatuan where konversi=1";
}else{
    $sKegiatan="select distinct kodekegiatan,regional from ".$dbname.".kebun_5psatuan where konversi=1 and regional='".$_SESSION['empl']['regional']."'";
}
$qKegiatan=  mysql_query($sKegiatan) or die(mysql_error($conn));
while($rKegiatan=  mysql_fetch_assoc($qKegiatan)){
    if($_SESSION['empl']['tipelokasitugas']!='HOLDING'){
        $optKegiatan.="<option value='".$rKegiatan['kodekegiatan']."'>".$rKegiatan['kodekegiatan']."-".$optKegiatanNm[$rKegiatan['kodekegiatan']]."</option>";
    }else{
        $optKegiatan.="<option value='".$rKegiatan['kodekegiatan']."'>".$rKegiatan['kodekegiatan']."-".$optKegiatanNm[$rKegiatan['kodekegiatan']]."-".$rKegiatan['regional']."</option>";
    }
}
/* 
$frm[1].="<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style=\"float: left;\">
<legend><b>Update Data Inputan Perawatan Menggunakan JJG</b></legend>
<table cellspacing=\"1\" border=\"0\" >
<tr><td><label>".$_SESSION['lang']['kodeorg']."</label></td><td><select id=\"kdOrgb\" name=\"kdOrgb\" style=\"width:150px\">".$optOrg."</select></td></tr>
    <td><label>".$_SESSION['lang']['tanggal']."</label></td>
    <td><input type=\"text\" class=\"myinputtext\" id=\"tanggal1b\" name=\"tanggal1b\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td>
</tr>
<tr>
    <td><label>".$_SESSION['lang']['tanggal']." 2</label></td>
    <td><input type=\"text\" class=\"myinputtext\" id=\"tanggal2b\" name=\"tanggal2b\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td>
</tr>
<tr>
    <td><label>".$_SESSION['lang']['kegiatan']."</label></td>
    <td><select id=kdKegiatan style=width:150px>".$optKegiatan."</select></td>
</tr>
<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>
<tr><td colspan=\"2\"><button onclick=\"zPreview('kebun_slave_3updategajibjr2','".$arr2."','printContainer2')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button></td></tr>
</table>
</fieldset>
</div>


<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id=awal>
    <div id='printContainer2' style='overflow:auto;height:350px;max-width:1220px;'>

    </div>
</div>
<div id=detailData style=display:none>
<div id=isiData>
</div>
</div>
</fieldset>";

$optKegiatan2.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
if($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
    $sKegiatan="select distinct kodekegiatan,regional,namakegiatan from ".$dbname.".vhc_kegiatan ";
}else{
        $sKegiatan="select distinct kodekegiatan,regional,namakegiatan from ".$dbname.".vhc_kegiatan "
             . "where regional='".$_SESSION['empl']['regional']."'";
}


$qKegiatan=  mysql_query($sKegiatan) or die(mysql_error($conn));
while($rKegiatan=  mysql_fetch_assoc($qKegiatan)){
        $optKegiatan2.="<option value='".$rKegiatan['kodekegiatan']."'>".$rKegiatan['kodekegiatan']."-".$rKegiatan['namakegiatan']."-".$rKegiatan['regional']."</option>";
}
$frm[2].="<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style=\"float: left;\">
<legend><b>Update Traksi JJG</b></legend>
<table cellspacing=\"1\" border=\"0\" >
<tr><td><label>".$_SESSION['lang']['kodeorg']."</label></td><td><select id=\"kdOrgT\" name=\"kdOrgT\" style=\"width:150px\">".$optOrg2."</select></td></tr>
    <td><label>".$_SESSION['lang']['tanggal']."</label></td>
    <td><input type=\"text\" class=\"myinputtext\" id=\"tanggal1T\" name=\"tanggal1T\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td>
</tr>
<tr>
    <td><label>".$_SESSION['lang']['tanggal']." 2</label></td>
    <td><input type=\"text\" class=\"myinputtext\" id=\"tanggal2T\" name=\"tanggal2T\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /><input type=hidden id=kdKegiatanT value='' /></td>
</tr>
<!--<tr>
    <td><label>".$_SESSION['lang']['kegiatan']."</label></td>
    <td><select id=kdKegiatanT style=width:150px>".$optKegiatan2."</select></td>
</tr>-->
<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>
<tr><td colspan=\"2\"><button onclick=\"zPreview('kebun_slave_3updategajibjr3','".$arr3."','printContainer3')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button></td></tr>
</table>
</fieldset>
</div>


<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id=awal>
    <div id='printContainer3' style='overflow:auto;height:350px;max-width:1220px;'>

    </div>
</div>
<div id=detailData style=display:none>
<div id=isiData>
</div>
</div>
</fieldset>"; */
//========================
$hfrm[0]="Adding Jurnal";
//$hfrm[1]="Update Perawatan";
//$hfrm[2]="Update Traksi JJG";
//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,170,1050);
//===============================================

CLOSE_BOX();
echo close_body();
?>