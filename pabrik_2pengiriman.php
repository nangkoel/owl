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
$optBrg="<option value=''>".$_SESSION['lang']['all']."</option>";
$sOrg="select kodebarang,namabarang from ".$dbname.".log_5masterbarang where kelompokbarang='400' and kodebarang!='40000003'";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optBrg.="<option value=".$rOrg['kodebarang'].">".$rOrg['namabarang']."</option>";
}
$optTimb=makeOption($dbname, 'pmn_4customer', 'kodetimbangan,namacustomer');
$optCust="<option value=''>".$_SESSION['lang']['all']."</option>";

//    $sCust="select distinct a.kodecustomer,namacustomer from ".$dbname.".pabrik_timbangan a left join
//            ".$dbname.".pmn_4customer b on a.kodecustomer=b.kodetimbangan where namacustomer!=''
//            order by b.namacustomer asc";
//    $qCust=mysql_query($sCust) or die(mysql_error($conn));
//    while($rCust=mysql_fetch_assoc($qCust))
//    {
//        $optCust.="<option value=".$rCust['kodecustomer'].">".$rCust['namacustomer']."</option>";
//    }



$optPabrik="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg2="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='PABRIK'";
$qOrg2=mysql_query($sOrg2) or die(mysql_error($conn));
while($rOrg2=mysql_fetch_assoc($qOrg2))
{
	$optPabrik.="<option value=".$rOrg2['kodeorganisasi'].">".$rOrg2['namaorganisasi']."</option>";
}
$arr="##kdPabrik##tgl_1##tgl_2##kdCust##nkntrak##kdBrg";
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<script>

function cleart(){
    document.getElementById('kdPabrik').value='';
    document.getElementById('kdCust').value='';
    document.getElementById('kdBrg').value='';
    document.getElementById('nkntrak').value='';
    
}
function getCust(){
    tgl1=document.getElementById('tgl_1').value;
    tgl2=document.getElementById('tgl_2').value;
    kdpbr=document.getElementById('kdPabrik').options[document.getElementById('kdPabrik').selectedIndex].value;
    if((tgl1=='')&&(tgl2==''))
    {
        cleart();
        alert("Date Can't Empty!!");
        return;
    }
    if(kdpbr==''){
        cleart();
        alert("Mill Code Can't Empty");
    }
    kdCustom=document.getElementById('kdBrg').options[document.getElementById('kdBrg').selectedIndex].value;
    
    param='proses=getCust'+'&kdBrg='+kdCustom+'&tgl1='+tgl1+'&tgl2='+tgl2+'&kdPabrik='+kdpbr;
    tujuan='pabrik_slave_2pengiriman.php';
    //alert(param);
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
                                    //alert(con.responseText);
                                    drt=con.responseText.split("####");
                                    document.getElementById('kdCust').innerHTML=drt[0];
                                    document.getElementById('nkntrak').innerHTML=drt[1];
                                    //load_data();
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
<legend><b><?php echo $_SESSION['lang']['rPengiriman'];?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['tanggal'];?></label></td><td><input type="text" class="myinputtext" id="tgl_1" onmousemove="setCalendar(this.id);" onkeypress="return false;"  size="10" maxlength="10" onblur="cleart()" /> s.d. <input type="text" class="myinputtext" id="tgl_2" onmousemove="setCalendar(this.id);" onkeypress="return false;"  size="10" maxlength="10"  onblur="cleart()" />
</td></tr>
<tr><td><label><?php echo $_SESSION['lang']['unit'];?></label></td><td><select id="kdPabrik" name="kdPabrik"  style="width:169px" ><?php echo $optPabrik;?></select></td></tr>
<tr><td><?php echo $_SESSION['lang']['materialname'];?></td><td><select id="kdBrg" style="width:169px" onchange="getCust()"><?php echo $optBrg; ?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['transporter'];?></label></td><td><select id="kdCust" name="kdCust" style="width:169px;" onchange="getKontrak()"><?php echo $optCust;?></select></td></tr>
<tr><td><?php echo $_SESSION['lang']['NoKontrak'];?></td><td><select id="nkntrak" style="width:169px"><option value=''><?php echo $_SESSION['lang']['all'] ;?></option></select></td></tr>

<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('pabrik_slave_2pengiriman','<?php echo $arr;?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zPdf('pabrik_slave_2pengiriman','<?php echo $arr;?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,'pabrik_slave_2pengiriman.php','<?php echo $arr;?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>

</table>
</fieldset>
</div>


<fieldset style='clear:both;'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px;'>

</div></fieldset>

<?php
CLOSE_BOX();
echo close_body();
?>