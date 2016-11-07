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
if($_SESSION['language']=='EN'){
   $zz='kelompok1 as kelompok'; 
}else{
   $zz='kelompok';    
}
$optKlmpk="<option value=''>".$_SESSION['lang']['all']."</option>";
$sOrg="select ".$zz.",kode from ".$dbname.".log_5klbarang order by kode asc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optKlmpk.="<option value=".$rOrg['kode'].">".$rOrg['kode']." - ".$rOrg['kelompok']."</option>";
}

$optSup="<option value=''>".$_SESSION['lang']['all']."</option>";
$sSup="select supplierid,namasupplier from ".$dbname.".log_5supplier where substring(kodekelompok,1,1)='S' order by namasupplier asc";
//echo $sSup;
$qSup=mysql_query($sSup) or die(mysql_error($conn));
while($rSup=mysql_fetch_assoc($qSup))
{
	$optSup.="<option value=".$rSup['supplierid'].">".$rSup['namasupplier']."</option>";
}
$optLokal="<option value=''>".$_SESSION['lang']['all']."</option>";
$arrPo=array("0"=>"Head Office","1"=>"Local");
foreach($arrPo as $brsLokal =>$isiLokal)
{
    $optLokal.="<option value=".$brsLokal.">".$isiLokal."</option>";
}
$arr="##klmpkBrg##kdBrg##tglDr##tanggalSampai##lokBeli";

?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>

<script>
function getBrg()
{
	klmpkBrg=document.getElementById('klmpkBrg').options[document.getElementById('klmpkBrg').selectedIndex].value;
	param='klmpkBrg='+klmpkBrg+'&proses=getBrg';
	tujuan="log_slave_2detail_pembelian_brg.php";
	//alert(param);	
    
	 function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                  	document.getElementById('kdBrg').innerHTML=con.responseText;
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
function searchBrg(title,content,ev)
{
        klmpk=document.getElementById('klmpkBrg').options[document.getElementById('klmpkBrg').selectedIndex].value;
        if(klmpk=='')
            {
                alert("Kelompok Barang Tidak Boleh Kosong!!");
                return;
            }
            
	width='500';
	height='400';
	showDialog1(title,content,width,height,ev);
	//alert('asdasd');
}
function findBrg()
{
    klmpkBrg=document.getElementById('klmpkBrg').value;
    nmBrg=document.getElementById('nmBrg').value;
    param='klmpkBrg='+klmpkBrg+'&nmBrg='+nmBrg+'&proses=getBarang';
    tujuan='log_slave_2detail_pembelian_brg.php';
        post_response_text(tujuan, param, respog);
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
                                          //	alert(con.responseText);
                                           document.getElementById('containerBarang').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
	 }  
		
}
function setData(kdbrg)
{
    document.getElementById('kdBrg').value=kdbrg;
    //document.getElementById('namaBrg').value=namaBarang;
    //document.getElementById('satuan').innerHTML=sat;
    closeDialog();
}
</script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['detPembBrg']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['kelompokbarang']?></label></td><td><select id="klmpkBrg" name="klmpkBrg" style="width:150px" onchange="getBrg()"><?php echo $optKlmpk?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['namabarang']?></label></td><td><select id="kdBrg" name="kdBrg" style="width:150px"><option value=''><?php echo $_SESSION['lang']['all']?></option></select>&nbsp;<img src="images/search.png" class="resicon" title='<?php echo $_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang'] ?>' onclick="searchBrg('<?php echo $_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang'] ?>','<fieldset><legend><?php echo $_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang'] ?></legend><?php echo $_SESSION['lang']['find'];?>&nbsp;<input type=text class=myinputtext id=nmBrg><button class=mybutton onclick=findBrg()><?php echo $_SESSION['lang']['find'] ?></button></fieldset><div id=containerBarang style=overflow=auto;height=380;width=485></div>',event);"></td>
</tr>
<tr><td><label><?php echo $_SESSION['lang']['lokasiBeli']?></label></td><td><select id="lokBeli" name="lokBeli" style="width:150px"><?php echo $optLokal?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['tanggal']?></label></td><td><input type="text" class="myinputtext" id="tglDr" name="tglDr" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" /></td></tr>
<tr><td><?php echo $_SESSION['lang']['tanggalsampai']?></td><td><input type="text" class="myinputtext" id="tanggalSampai" name="tanggalSampai" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" /></td></tr>

<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('log_slave_2detail_pembelian_brg','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zPdf('log_slave_2detail_pembelian_brg','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,'log_slave_2detail_pembelian_brg.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>

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