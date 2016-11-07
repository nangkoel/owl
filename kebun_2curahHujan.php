<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zFunction.php');
echo open_body();
include('master_mainMenu.php');
$frm[0]='';
$frm[1]='';

?>
<script>
pilh=" <?php echo $_SESSION['lang']['pilihdata'] ?>";
</script>
<script language="javascript" src="js/zMaster.js"></script>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/zReport.js'></script>
<script type="text/javascript" src="js/kebun_2urahHujan.js" /></script>

<script>
dataKdvhc="<?php echo $_SESSION['lang']['pilihdata']?>";
function Clear1()
{
    document.getElementById('thnBudget').value='';
    document.getElementById('kdUnit').value='';
    document.getElementById('printContainer').innerHTML='';
}
function Clear2()
{
    document.getElementById('thnBudget_afd').value='';
    document.getElementById('kdUnit_afd').value='';
    document.getElementById('printContainer2').innerHTML='';
}
function Clear3()
{
    document.getElementById('thnBudget_sebaran').value='';
    document.getElementById('kdUnit_sebaran').value='';
    document.getElementById('printContainer3').innerHTML='';
}
function Clear5()
{
    document.getElementById('thnBudgetCst').value='';
    document.getElementById('kdUnitCst').value='';
    document.getElementById('printContainer5').innerHTML='';
}
</script>
<?php
$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optOrg2=$optOrg;
if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{
    $sOrg="select namaalias,kodeorganisasi from ".$dbname.".organisasi where tipe='AFDELING' order by namaalias asc";
    $sOrg2="select namaalias,kodeorganisasi from ".$dbname.".organisasi where tipe='KEBUN' order by namaalias asc";
}
else
{
    $sOrg="select namaalias,kodeorganisasi from ".$dbname.".organisasi where tipe='AFDELING' and induk='".$_SESSION['empl']['lokasitugas']."' order by namaalias asc";
    $sOrg2="select namaalias,kodeorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by namaalias asc";
}
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaalias']."</option>";
}
$qOrg=mysql_query($sOrg2) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
        $optOrg2.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaalias']."</option>";
}
 $optper="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";


$arr="##periodeUnit##kdUnit##kdUnitOrg2";
$arr2="##kdUnitOrg##periodeDt";



OPEN_BOX('',"<b>".$_SESSION['lang']['laporanCurahHujan']."</b>");

$frm[0].="<fieldset style=\"overflow: left;\"><legend>".$_SESSION['lang']['curahharian']."</legend>";
$frm[0].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['kebun']."</td><td>:</td><td><select id='kdUnitOrg2'  style=\"width:150px;\" onchange=getPeriode()>".$optOrg2."</select></td></tr>
<tr><td>".$_SESSION['lang']['afdeling']."</td><td>:</td><td><select id='kdUnit'  style=\"width:150px;\" onchange=getPeriode()>".$optOrg."</select></td></tr>
<tr><td>".$_SESSION['lang']['periode']."</td><td>:</td><td>
<select id='periodeUnit' style='width:150px;'>".$optper."</select></td></tr>

<tr><td colspan=3>
<button onclick=\"zPreview('kebun_2slaveCurahHujan','".$arr."','printContainer')\" class=\"mybutton\" >Preview</button>
    <button onclick=\"zPdf('kebun_2slaveCurahHujan','".$arr."','printContainer')\" class=\"mybutton\">PDF</button>
    <button onclick=\"zExcel(event,'kebun_2slaveCurahHujan.php','".$arr."')\" class=\"mybutton\" >Excel</button></td></tr></table>
";
$frm[0].="</fieldset><fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1000px'>

</div></fieldset>";

$frm[1].="<fieldset style=\"float: left;\"><legend>".$_SESSION['lang']['curahbulanan']."</legend>";
$frm[1].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['kebun']."</td><td>:</td><td><select id='kdUnitOrg'  style=\"width:150px;\" onchange=getPeriodeOrg()>".$optOrg2."</select></td></tr>
<tr><td>".$_SESSION['lang']['periode']."</td><td>:</td><td>
<select id='periodeDt' style='width:150px;'>".$optper."</select></td></tr>

<tr><td colspan=3>
<button onclick=\"zPreview('kebun_2slaveCurahHujanOrg','".$arr2."','printContainer2')\" class=\"mybutton\" >Preview</button>
    <button onclick=\"zPdf('kebun_2slaveCurahHujanOrg','".$arr2."','printContainer2')\" class=\"mybutton\">PDF</button>
    <button onclick=\"zExcel(event,'kebun_2slaveCurahHujanOrg.php','".$arr2."')\" class=\"mybutton\" >Excel</button></td></tr></table>
";
$frm[1].="</fieldset><fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer2' style='overflow:scroll;height:350px;max-width:1000px'>

</div></fieldset>";


//========================
$hfrm[0]=$_SESSION['lang']['curahharian'];//
$hfrm[1]=$_SESSION['lang']['curahbulanan'];//"";


//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,200,900);
//===============================================	
?>


<?php
CLOSE_BOX();
echo"</div>";
echo close_body();
?>