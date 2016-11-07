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
//ambil periode gaji 
$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
//ambil organisasi
$optDept="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optTipe=$optDept;
$sDept="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe!='HOLDING'
       and char_length(kodeorganisasi)='4'  order by namaorganisasi asc";
$qDept=mysql_query($sDept) or die(mysql_error());
while($rDept=mysql_fetch_assoc($qDept))
{
	$optDept.="<option value=".$rDept['kodeorganisasi'].">".$rDept['namaorganisasi']."</option>";
}

$arrKry="##periode##kdUnit";

?>

<script language=javascript src=js/zTools.js></script>
<script language=javascript src='js/zReport.js'></script>
<script>
function  getPeriode()
{
    kdOrg=document.getElementById('kdUnit').options[document.getElementById('kdUnit').selectedIndex].value;
    tujuan='sdm_slave_2realisasiGaji';
    param='kdUnit='+kdOrg;
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
                                                        document.getElementById('periode').innerHTML=con.responseText;
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
<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['gaji']." ".$_SESSION['lang']['realisasi'];?></b><?php //echo $_SESSION['lang']['']?></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['unit']?></label></td><td><select id="kdUnit" name="kdUnit" style="width:150px" onchange="getPeriode()"><?php echo $optDept?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['periode']?></label></td><td><select id="periode" name="periode" style="width:150px"><?php echo $optPeriode?></select></td></tr>
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('sdm_slave_2realisasiGaji','<?php echo $arrKry?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
        <!--<button onclick="zPdf('sdm_slave_2realisasiGaji','<?php echo $arrKry?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button>--><button onclick="zExcel(event,'sdm_slave_2realisasiGaji.php','<?php echo $arrKry?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>

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