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
$optGdn=$optKlmpk="<option value=''>".$_SESSION['lang']['all']."</option>";
$sOrg="select kodebarang,namabarang from ".$dbname.".log_5masterbarang where kodebarang like '010%' or kodebarang like '012%' order by kodebarang asc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optKlmpk.="<option value=".$rOrg['kodebarang'].">".$rOrg['namabarang']."</option>";
}

$optPrd=$optSup="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
if($_SESSION['empl']['lokasitugas']=='HOLDING'){
	$sSup="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PT' order by namaorganisasi asc";
}else{
	$sSup="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."' order by namaorganisasi asc";
}

//echo $sSup;
$qSup=mysql_query($sSup) or die(mysql_error($conn));
while($rSup=mysql_fetch_assoc($qSup))
{
	$optSup.="<option value=".$rSup['kodeorganisasi'].">".$rSup['namaorganisasi']."</option>";
}

$arr="##ptId##kdBrg##kdGudang##periode";

?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<script>
function getPrd()
{
	klmpkBrg=document.getElementById('ptId').options[document.getElementById('ptId').selectedIndex].value;
	param='ptId='+klmpkBrg;
	tujuan="log_slave_2stokbahanbakar.php";
	//alert(param);	
    
	 function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
					dtdua=con.responseText.split("####");
                  	document.getElementById('periode').innerHTML=dtdua[0];
					document.getElementById('kdGudang').innerHTML=dtdua[1];
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
  post_response_text(tujuan+'?proses=getPrd', param, respon);

}
</script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b>Stock Bahan Bakar</b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['pt']?></label></td><td><select id="ptId" name="ptId" style="width:150px"  onchange="getPrd()"><?php echo $optSup?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['periode']?></label></td><td><select id="periode" name="periode" style="width:150px" ><?php echo $optPrd?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['sloc']?></label></td><td><select id="kdGudang" name="kdGudang" style="width:150px" ><?php echo $optGdn?></select></td></tr>
 <tr><td><label><?php echo $_SESSION['lang']['namabarang']?></label></td><td><select id="kdBrg" name="kdBrg" style="width:150px"><?php echo $optKlmpk?></select></td></tr>

<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('log_slave_2stokbahanbakar','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zExcel(event,'log_slave_2stokbahanbakar.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>

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