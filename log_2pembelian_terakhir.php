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
$optPeriodePo="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sPeriodePo="select distinct substring(tanggal,1,7) as periode from ".$dbname.".log_poht order by tanggal asc";
$qPeriodePo=mysql_query($sPeriodePo) or die(mysql_error());
while($rPeriodePo=mysql_fetch_assoc($qPeriodePo))
{
    if($rPeriodePo['periode']!='0000-00')
    {
        if(substr($rPeriodePo['periode'],5,2)=='12')
        {
            $optPeriodePo.="<option value=".substr($rPeriodePo['periode'],0,4).">".substr($rPeriodePo['periode'],0,4)."</option>";
        }
        else
        {
            $optPeriodePo.="<option value=".$rPeriodePo['periode'].">".substr(tanggalnormal($rPeriodePo['periode']),1,7)."</option>";
        }
    }
    //echo substr($rPeriodePo['periode'],5,5);
}
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
semua="<?php echo $_SESSION['lang']['all'] ?>";
function batal()
{
    document.getElementById('klmpkBrg').value='';
    document.getElementById('kdBrg').innerHTML='';
    document.getElementById('kdBrg').innerHTML="<option value=''>"+semua+"</option>";
    document.getElementById('lokBeli').value='';
    document.getElementById('tglDr').value='';
    document.getElementById('tanggalSampai').value='';
    document.getElementById('printContainer').innerHTML='';
}
function searchBrg(title,content,ev)
{
        klmpk=document.getElementById('klmpkBrg').options[document.getElementById('klmpkBrg').selectedIndex].value;
        if(klmpk=='')
            {
                alert("Metrial group required!!");
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
<legend><b><?php echo $_SESSION['lang']['detPembTakhir']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['kelompokbarang']?></label></td><td><select id="klmpkBrg" name="klmpkBrg" style="width:150px" onchange="getBrg()"><?php echo $optKlmpk?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['namabarang']?></label></td><td><select id="kdBrg" name="kdBrg" style="width:150px"><option value=''><?php echo $_SESSION['lang']['all']?></option></select>&nbsp;<img src="images/search.png" class="resicon" title='<?php echo $_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang'] ?>' onclick="searchBrg('<?php echo $_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang'] ?>','<fieldset><legend><?php echo $_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang'] ?></legend><?php echo $_SESSION['lang']['find'];?>&nbsp;<input type=text class=myinputtext id=nmBrg><button class=mybutton onclick=findBrg()><?php echo $_SESSION['lang']['find'] ?></button></fieldset><div id=containerBarang style=overflow=auto;height=380;width=485></div>',event);"></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['lokasiBeli']?></label></td><td><select id="lokBeli" name="lokBeli" style="width:150px"><?php echo $optLokal?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['dari']." ".$_SESSION['lang']['periode']?></label></td><td><select id="tglDr" style="width:150px;"><?php echo $optPeriodePo ?></select><!--<input type="text" class="myinputtext" id="tglDr" name="tglDr" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10"  />--></td></tr>
<tr><td><?php echo $_SESSION['lang']['tglcutisampai']." ".$_SESSION['lang']['periode']?></td><td><select id="tanggalSampai" style="width:150px;"><?php echo $optPeriodePo ?></select><!--<input type="text" class="myinputtext" id="tanggalSampai" name="tanggalSampai" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" />--></td></tr>

<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('log_slave_2pembelian_terakhir','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
        <!--<button onclick="zPdf('log_slave_2detail_pembelian_brg','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button>-->
        <button onclick="zExcel(event,'log_slave_2pembelian_terakhir.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button>
        <button onclick="batal()" class="mybutton" name="btl" id="btl"><?php echo $_SESSION['lang']['cancel']?></button>
</td></tr>

</table>
</fieldset>
</div>


<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>
<?php
//$arrBln=array("1"=>"Jan","2"=>"Feb","3"=>"Mar","4"=>"Apr","5"=>"Mei","6"=>"Jun","7"=>"Jul","8"=>"Aug","9"=>"Sept","10"=>"Okt","11"=>"Nov","12"=>"Des");
//echo"<pre>";
//print_r($arrBln);
//echo"</pre>";
//echo"<table class=sortable border=0 cellspacing=1 cellpadding=1><thead><tr class=rowheader>";
//foreach($arrBln as $brs=>$dtBln)
//{
//echo"<td>".$dtBln."</td>";
//}
//echo"<td>action</td></tr></thead>";
//echo"<tbody><tr class=rowcontent>";
//foreach($arrBln as $brs2 =>$dtBln2)
//{
//echo"<td><input type='text' id=jam_".$brs2." /></td>";
//}
//echo"<td>action</td></tr></tbody></table>";
?>
</div></fieldset>

<?php
CLOSE_BOX();
echo close_body();
?>