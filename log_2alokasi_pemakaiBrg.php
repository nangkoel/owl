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
$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='GUDANG'";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}
$optVhc="<option value=''>".$_SESSION['lang']['all']."</option>";
$sVhc="select distinct kodevhc from ".$dbname.".vhc_runht where kodevhc<>'' order by kodevhc";
$qVhc=mysql_query($sVhc) or die(mysql_error($conn));
while($rVhc=mysql_fetch_assoc($qVhc))
{
	$optVhc.="<option value=".$rVhc['kodevhc'].">".$rVhc['kodevhc']."</option>";
}

$arr="##kdGudang##periode##kdVhc";

?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<script language=javascript src=js/keu_2laporanAnggaranKebun.js></script>
<script>
function getPeriode()
{
	kdGudang=document.getElementById('kdGudang').options[document.getElementById('kdGudang').selectedIndex].value;
	param='kdGudang='+kdGudang+'&proses=getPeriode';
	tujuan="log_slave_2alokasi_pemakaiBrg.php";
	//alert(param);	
    
	 function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                  	document.getElementById('periode').innerHTML=con.responseText;
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
<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['lapAlokasiBrg']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['pilihgudang']?></label></td><td><select id="kdGudang" name="kdGudang" style="width:150px" onchange="getPeriode()"><?php echo $optOrg?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['periode']?></label></td><td><select id="periode" name="periode" style="width:150px"><option value=''><?php echo $_SESSION['lang']['all']?></option></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['kodevhc']?></label></td><td><select id="kdVhc" name="kdVhc" style="width:150px"><?php echo $optVhc?></select></td></tr>
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('log_slave_2alokasi_pemakaiBrg','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zPdf('log_slave_2alokasi_pemakaiBrg','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,'log_slave_2alokasi_pemakaiBrg.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>

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