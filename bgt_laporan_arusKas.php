<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zFunction.php');
echo open_body();
include('master_mainMenu.php');
$frm[0]='';
$frm[1]='';
$frm[2]='';
$frm[3]='';

?>
<script>
pilh=" <?php echo $_SESSION['lang']['pilihdata'] ?>";
</script>
<script language="javascript" src="js/zMaster.js"></script>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/zReport.js'></script>

<script>
dataKdvhc="<?php echo $_SESSION['lang']['pilihdata']?>";
function Clear1()
{
    document.getElementById('thnBudget1').value='';
    document.getElementById('kdUnit1').value='';
    document.getElementById('printContainer1').innerHTML='';
}
function Clear2()
{
    document.getElementById('thnBudget2').value='';
    document.getElementById('kdUnit2').value='';
    document.getElementById('printContainer2').innerHTML='';
}
function Clear3()
{
    document.getElementById('thnBudget3').value='';
    document.getElementById('printContainer3').innerHTML='';
}
function Clear4()
{
    document.getElementById('thnBudget4').value='';
    document.getElementById('kdUnit4').value='';
    document.getElementById('printContainer4').innerHTML='';
}
function Clear5()
{
    document.getElementById('thnBudget5').value='';
    document.getElementById('printContainer5').innerHTML='';
}
function getUnit(n)
{
	kdPt=document.getElementById('kdPt'+n).options[document.getElementById('kdPt'+n).selectedIndex].value;
	param="kodePt="+kdPt+"&proses=getUnit";
	tujuan="bgt_slave_laporan_arusKas_neraca.php";
	//alert(param);	
    
	 function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
				//	alert(con.responseText);
                  	document.getElementById('kdUnit'+n).innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
  post_response_text(tujuan, param, respon);
}
</script>
<?php
$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where CHAR_LENGTH(kodeorganisasi)='4' order by namaorganisasi asc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}
$optPt="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe = 'PT' order by namaorganisasi asc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optPt.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}
$optThn="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sThn="select distinct  tahunbudget from ".$dbname.".bgt_summary_biaya_vw order by tahunbudget desc";
$qThn=mysql_query($sThn) or die(mysql_error($conn));
while($rThn=mysql_fetch_assoc($qThn))
{
    $optThn.="<option value='".$rThn['tahunbudget']."'>".$rThn['tahunbudget']."</option>";
}
$arr1="##thnBudget1##kdPt1##kdUnit1";
$arr2="##thnBudget2##kdPt2##kdUnit2";
$arr3="##thnBudget3##kdPt3##kdUnit3";
$arr4="##thnBudget4##kdUnit4";
$arr5="##thnBudget5";


OPEN_BOX('',"<b>".$_SESSION['lang']['aruskas']." ".$_SESSION['lang']['anggaran']."</b>");

$frm[0].="<fieldset style=\"overflow: left;\"><legend>".$_SESSION['lang']['neraca']."</legend>";
$frm[0].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['budgetyear']."</td><td>:</td><td>
<select id='thnBudget1' style='width:150px;'>".$optThn."</select></td></tr>
<tr><td>".$_SESSION['lang']['pt']."</td><td>:</td><td><select id='kdPt1' onchange='getUnit(1)'  style=\"width:150px;\">".$optPt."</select></td></tr>
<tr><td>".$_SESSION['lang']['unit']."</td><td>:</td><td><select id='kdUnit1'  style=\"width:150px;\"><option value=''>".$_SESSION['lang']['pilihdata']."</option></select></td></tr>
<tr><td colspan=3>
<button onclick=\"zPreview('bgt_slave_laporan_arusKas_neraca','".$arr1."','printContainer1')\" class=\"mybutton\" >Preview</button>
    <button onclick=\"zExcel(event,'bgt_slave_laporan_arusKas_neraca.php','".$arr1."')\" class=\"mybutton\" >Excel</button>
    <button onclick=\"Clear1()\" class=\"mybutton\" >".$_SESSION['lang']['cancel']."</button></td></tr></table>
";
$frm[0].="</fieldset><fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer1' style='overflow:auto;height:350px;max-width:1000px'>

</div></fieldset>";

$frm[1].="<fieldset style=\"float: left;\"><legend>".$_SESSION['lang']['labarugi']."</legend>";
$frm[1].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['budgetyear']."</td><td>:</td><td>
<select id='thnBudget2' style='width:150px;'>".$optThn."</select></td></tr>
<tr><td>".$_SESSION['lang']['pt']."</td><td>:</td><td><select id='kdPt2' onchange='getUnit(2)'  style=\"width:150px;\">".$optPt."</select></td></tr>
<tr><td>".$_SESSION['lang']['unit']."</td><td>:</td><td><select id='kdUnit2'  style=\"width:150px;\"><option value=''>".$_SESSION['lang']['pilihdata']."</option></select></td></tr>
<tr><td colspan=3>
<button onclick=\"zPreview('bgt_slave_laporan_arusKas_labarugi','".$arr2."','printContainer2')\" class=\"mybutton\" >Preview</button>
    <button onclick=\"zExcel(event,'bgt_slave_laporan_arusKas_labarugi.php','".$arr2."')\" class=\"mybutton\" >Excel</button>
    <button onclick=\"Clear2()\" class=\"mybutton\" >".$_SESSION['lang']['cancel']."</button></td></tr></table>
";
$frm[1].="</fieldset><fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer2' style='overflow:auto;height:350px;width:800px'>

</div></fieldset>";

$frm[2].="<fieldset style=\"float: left;\"><legend>".$_SESSION['lang']['proyeksiaruskas']." ".$_SESSION['lang']['sebaran']."</legend>";
$frm[2].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['budgetyear']."</td><td>:</td><td>
<select id='thnBudget3' style='width:150px;'>".$optThn."</select></td></tr>
    <tr><td>".$_SESSION['lang']['pt']."</td><td>:</td><td><select id='kdPt3' onchange='getUnit(3)'  style=\"width:150px;\">".$optPt."</select></td></tr>
<tr><td>".$_SESSION['lang']['unit']."</td><td>:</td><td><select id='kdUnit3'  style=\"width:150px;\"><option value=''>".$_SESSION['lang']['pilihdata']."</option></select></td></tr>
<tr><td colspan=3>
<button onclick=\"zPreview('bgt_slave_laporan_arusKas_sebaran','".$arr3."','printContainer3')\" class=\"mybutton\" >Preview</button>
    <button onclick=\"zExcel(event,'bgt_slave_laporan_arusKas_sebaran.php','".$arr3."')\" class=\"mybutton\" >Excel</button>
    <button onclick=\"Clear3()\" class=\"mybutton\" >".$_SESSION['lang']['cancel']."</button></td></tr></table>
";
$frm[2].="</fieldset><fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer3' style='overflow:auto;height:350px;width:800px'>

</div></fieldset>";

$optPt="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sPt="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='PT' order by namaorganisasi asc";
$qPt=mysql_query($sPt) or die(mysql_error($conn));
while($rPt=mysql_fetch_assoc($qPt))
{
	$optPt.="<option value=".$rPt['kodeorganisasi'].">".$rPt['namaorganisasi']."</option>";
}
$frm[3].="<fieldset style=\"float: left;\"><legend>".$_SESSION['lang']['proyeksiaruskas']." ".$_SESSION['lang']['perpt']."</legend>";
$frm[3].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['budgetyear']."</td><td>:</td><td>
<select id='thnBudget4' style='width:150px;'>".$optThn."</select></td></tr>
<tr><td>".$_SESSION['lang']['unit']."</td><td>:</td><td><select id='kdUnit4'  style=\"width:150px;\">".$optPt."</select></td></tr>
<tr><td colspan=3>
<button onclick=\"zPreview('bgt_slave_laporan_arusKas_perpt','".$arr4."','printContainer4')\" class=\"mybutton\" >Preview</button>
    <button onclick=\"zExcel(event,'bgt_slave_laporan_arusKas_perpt.php','".$arr4."')\" class=\"mybutton\" >Excel</button>
    <button onclick=\"Clear4()\" class=\"mybutton\" >".$_SESSION['lang']['cancel']."</button></td></tr></table>
";
$frm[3].="</fieldset><fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer4' style='overflow:auto;height:350px;max-width:1220px'>

</div></fieldset>";

////echo $sOrgs;
//$optKd="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
//$_SESSION['empl']['tipelokasitugas']=='HOLDING'?$sKd="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='KEBUN'":$sKd="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
//$qList=mysql_query($sKd) or die(mysql_error($sKd));
//while($rKd=mysql_fetch_assoc($qList))
//{
//    $optKd.="<option value='".$rKd['kodeorganisasi']."'>".$rKd['namaorganisasi']."</option>";
//}
$frm[4].="<fieldset style=\"float: left;\"><legend>".$_SESSION['lang']['proyeksiaruskas']." ".$_SESSION['lang']['konsolpt']."</legend>";
$frm[4].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['budgetyear']."</td><td>:</td><td>
<select id='thnBudget5' style='width:150px;'>".$optThn."</select></td></tr>
<tr><td colspan=3>
<button onclick=\"zPreview('bgt_slave_laporan_arusKas_konsolpt','".$arr5."','printContainer5')\" class=\"mybutton\" >Preview</button>
    <button onclick=\"zExcel(event,'bgt_slave_laporan_arusKas_konsolpt.php','".$arr5."')\" class=\"mybutton\" >Excel</button>
    <button onclick=\"Clear5()\" class=\"mybutton\" >".$_SESSION['lang']['cancel']."</button></td></tr></table>
";
$frm[4].="</fieldset><fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer5' style='overflow:auto;height:350px;max-width:1220px'>

</div></fieldset>";

//========================
$hfrm[0]=$_SESSION['lang']['neraca'];
$hfrm[1]=$_SESSION['lang']['labarugi'];
$hfrm[2]=$_SESSION['lang']['proyeksiaruskas']." ".$_SESSION['lang']['sebaran'];
$hfrm[3]=$_SESSION['lang']['proyeksiaruskas']." ".$_SESSION['lang']['perpt'];;
$hfrm[4]=$_SESSION['lang']['proyeksiaruskas']." ".$_SESSION['lang']['konsolpt'];;

//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,150,900);
//===============================================	
?>


<?php
CLOSE_BOX();
echo"</div>";
echo close_body();
?>