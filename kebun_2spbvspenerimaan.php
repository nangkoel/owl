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
 
$optOrg.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optAfd.="<option value=''>".$_SESSION['lang']['all']."</option>";
$lksiTugas=substr($_SESSION['empl']['lokasitugas'],0,4);
if ($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
    $sOrg="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='KEBUN'";
} else {
    $sOrg="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='KEBUN' and kodeorganisasi in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";
}

$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}
 
$arr="##traksiId##afdId##periode";


$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

for($x=0;$x<=24;$x++){
    $t=mktime(0,0,0,date('m')-$x,15,date('Y'));
    $optPeriode.="<option value='".date('Y-m',$t)."'>".date('Y-m',$t)."</option>";
}
?>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/zReport.js'></script>
<script>
    function getPeriode(){
        trksi=document.getElementById('traksiId').options[document.getElementById('traksiId').selectedIndex].value;
 
	param='traksiId='+trksi+'&proses=getPrd';
	//alert(param);
	tujuan='kebun_slave_2spbvspenerimaan.php';
	post_response_text(tujuan, param, respon);
	function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
					//alert(con.responseText);
                                        cor=con.responseText.split("####");
		//document.getElementById('periode').innerHTML=cor[0];
                                        document.getElementById('afdId').innerHTML=cor[1];
				 
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    }
</script>

<link rel='stylesheet' type='text/css' href='style/zTable.css'>

<fieldset style="float: left;">
<legend><b><?php

    echo $_SESSION['lang']['suratPengantarBuah']." Vs Weighbridge";

?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['kebun']?></label></td><td><select id="traksiId" name="traksiId"  style="width:150px" onchange=getPeriode()><?php echo $optOrg?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['afdeling']?></label></td><td><select id="afdId" name="afdId"  style="width:150px"><?php echo $optAfd?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['periode']?></label></td><td>
            <select id="periode"  style=width:150px onchange=getPeriodeStr()><?php echo $optPeriode?></select></td></tr>
<tr><td></td><td><label id="periodestr"/></td></tr>

<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('kebun_slave_2spbvspenerimaan','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
                    <!--<button onclick="zPdf('sdm_slave_2rekapabsen','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button>
                        <button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal"><?php echo $_SESSION['lang']['cancel']?></button></td></tr>-->
                    <button onclick="zExcel(event,'kebun_slave_2spbvspenerimaan.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button>
                    

</table>
</fieldset>

<div style="margin-bottom: 30px;">
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>
<?php
//echo"<pre>";
//print_r($_SESSION);
//echo"</pre>";
?>
</div></fieldset>

<?php

CLOSE_BOX();
echo close_body();
?>