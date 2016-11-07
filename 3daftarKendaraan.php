<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
for($x=0;$x<=24;$x++)
{
	$dt=mktime(0,0,0,date('m')-$x,15,date('Y'));
	$optPeriode.="<option value=".date("Y-m",$dt).">".date("Y-m",$dt)."</option>";
}
$sKbn="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe in ('KEBUN','PABRIK','KANWIL','TRAKSI')";
$qKbn=mysql_query($sKbn) or die(mysql_error());
while($rKbn=mysql_fetch_assoc($qKbn))
{
	$optKbn.="<option value=".$rKbn['kodeorganisasi'].">".$rKbn['namaorganisasi']."</option>";
}
$sJnsvhc="select jenisvhc,namajenisvhc from ".$dbname.".vhc_5jenisvhc order by namajenisvhc asc";
$qJnsVhc=mysql_query($sJnsvhc) or die(mysql_error());
while($rJnsvhc=mysql_fetch_assoc($qJnsVhc))
{
	$optJns.="<option value=".$rJnsvhc['jenisvhc'].">".$rJnsvhc['namajenisvhc']."</option>";
}
	$arrklvhc=getEnum($dbname,'vhc_5master','kelompokvhc');
	foreach($arrklvhc as $kei=>$fal)
	{
		switch($kei)
		{
                                            case 'AB':
                                                     $_SESSION['language']!='EN'?$fal='Alat Berat':$fal='Heavy Equipment';
                                            break;
                                            case 'KD':                            
                                                    $_SESSION['language']!='EN'?$fal='Kendaraan':$fal='Vehicle';
                                            break;
                                            case 'MS':
                                                    $_SESSION['language']!='EN'? $fal='Mesin':$fal='Machinery';
                                            break;		
		}
		$optklvhc.="<option value='".$kei."'>".$fal."</option>";
	} 
$arr="##kdKbn##klpmkVhc";
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<script language=javascript src=js/keu_2laporanAnggaranKebun.js></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<div style="margin-bottom: 30px;">
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['laporanKendAb']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['unit']?></label></td><td><select id="kdKbn" name="kdKbn" style="width:150px">
<option value="0"><?php echo $_SESSION['lang']['all']?></option><?php echo $optKbn?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['kodekelompok']?></label></td><td><select id="klpmkVhc" name="klpmkVhc" style="width:150px">
<option value="0"><?php echo $_SESSION['lang']['all']?></option><?php echo $optklvhc?></select></td></tr>
<tr><td colspan="2"><button onclick="zPreview('3slave_daftarKendaran','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zPdf('3slave_daftarKendaran','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,'3slave_daftarKendaran.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>
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