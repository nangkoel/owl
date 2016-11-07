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

$optKelompok=makeOption($dbname, 'log_5klbarang', 'kode,kelompok');
$optOrg="<option value=''>".$_SESSION['lang']['all']."</option>";
$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='PT'";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}

$optSup="<option value=''>".$_SESSION['lang']['all']."</option>";
$sSup="select distinct substr(kodebudget,3,3) as kelompokbarang from ".$dbname.".bgt_budget_detail where kodebudget like 'M%' order by substr(kodebudget,3,3) asc";
//echo $sSup;
$qSup=mysql_query($sSup) or die(mysql_error($conn));
while($rSup=mysql_fetch_assoc($qSup))
{ 
	$optSup.="<option value=".$rSup['kelompokbarang'].">".$rSup['kelompokbarang']."-".$optKelompok[$rSup['kelompokbarang']]."</option>";
}
$optLokal="<option value=''>".$_SESSION['lang']['all']."</option>";
$arrPo=array("0"=>"Pusat","1"=>"Lokal");
foreach($arrPo as $brsLokal =>$isiLokal)
{
    $optLokal.="<option value=".$brsLokal.">".$isiLokal."</option>";
}
$optThn="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sThnBUdget="select distinct tahunbudget from ".$dbname.".bgt_budget order by tahunbudget desc";
$qThnBudget=mysql_query($sThnBUdget) or die(mysql_error());
while($rThnBudget=mysql_fetch_assoc($qThnBudget))
{
    $optThn.="<option value=".$rThnBudget['tahunbudget'].">".$rThnBudget['tahunbudget']."</option>";
}
$arrPilMode=array("0"=>$_SESSION['lang']['fisik'],"1"=>$_SESSION['lang']['rp']);
foreach($arrPilMode as $pilihan=>$lstData)
{
    $optPilMode.="<option value=".$pilihan.">".$lstData."</option>";
}
$arr="##kdPt##kdUnit##thnBudget##kdBudget##pilMode";

?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<script language=javascript src=js/keu_2laporanAnggaranKebun.js></script>
<script>
function getKdorg()
{
	kdPt=document.getElementById('kdPt').options[document.getElementById('kdPt').selectedIndex].value;
	param='kdPt='+kdPt+'&proses=getKdorg';
	tujuan="log_slave_2detail_pembelian.php";
	//alert(param);	
    
	 function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                  	document.getElementById('kdUnit').innerHTML=con.responseText;
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
function searchSupplier(title,content,ev)
{
	width='500';
	height='400';
	showDialog1(title,content,width,height,ev);
	//alert('asdasd');
}
function findSupplier()
{
    nmSupplier=document.getElementById('nmSupplier').value;
    param='proses=getSupplierNm'+'&nmSupplier='+nmSupplier;
    tujuan='log_slave_save_po.php';
    post_response_text(tujuan, param, respog);			

    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                  document.getElementById('containerSupplier').innerHTML=con.responseText;
                        }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }	
}
function setData(kdSupp)
{
    l=document.getElementById('kdSup');
    
    for(a=0;a<l.length;a++)
        {
            if(l.options[a].value==kdSupp)
                {
                    l.options[a].selected=true;
                }
        }
		
       closeDialog();
	   get_supplier();
}
</script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['bgtMaterial']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['budgetyear']?></label></td><td><select id="thnBudget" name="thnBudget" style="width:150px"><?php echo $optThn?></select></td></tr>
    <tr><td><label><?php echo $_SESSION['lang']['pt']?></label></td><td><select id="kdPt" name="kdPt" style="width:150px" onchange="getKdorg()"><?php echo $optOrg?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['unit']?></label></td><td><select id="kdUnit" name="kdUnit" style="width:150px"><option value=''><?php echo $_SESSION['lang']['all']?></option></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['kodebudget']?></label></td><td><select id="kdBudget" name="kdBudget" style="width:150px"><?php echo $optSup?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['pilih']?></label></td><td><select id="pilMode" name="pilMode" style="width:150px"><?php echo $optPilMode?></select></td></tr>

<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('bgt_slave_laporan_budget_material','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zExcel(event,'bgt_slave_laporan_budget_material.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>

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