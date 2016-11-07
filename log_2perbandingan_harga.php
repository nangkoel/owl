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
$optOrg="<option value=''>".$_SESSION['lang']['all']."</option>";
$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='PT'";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}

$optSup="<option value=''>".$_SESSION['lang']['all']."</option>";
$sSup="select supplierid,namasupplier from ".$dbname.".log_5supplier where substring(kodekelompok,1,1)='S' order by namasupplier asc";
//echo $sSup;
$qSup=mysql_query($sSup) or die(mysql_error($conn));
while($rSup=mysql_fetch_assoc($qSup))
{
	$optSup.="<option value=".$rSup['supplierid'].">".$rSup['namasupplier']."</option>";
}
$optLokal="<option value=''>".$_SESSION['lang']['pilih']."</option>";
$arrPo=array("0"=>"Laporan","1"=>"Form Update");
foreach($arrPo as $brsLokal =>$isiLokal)
{
    $optLokal.="<option value=".$brsLokal.">".$isiLokal."</option>";
}
$arr="##nopp##formPil";
$optListNopp="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sLnopp="select distinct nopp from ".$dbname.".log_permintaanhargadt order by nopp desc";
$qLnopp=mysql_query($sLnopp) or die(mysql_error($sLnopp));
while($rLnopp=mysql_fetch_assoc($qLnopp))
{
    $optListNopp.="<option value='".$rLnopp['nopp']."'>".$rLnopp['nopp']."</option>";
}
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<script language=javascript src='js/log_2perbandingnan_harga.js'></script>
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
function searchNopp(title,content,ev)
{
    width='500';
    height='400';
    showDialog2(title,content,width,height,ev);
}
function findNopp()
{
    kdNopp=document.getElementById('kdNopp').value;
    param='proses=getNopp'+'&kdNopp='+kdNopp;
    tujuan='log_slave_2perbandingan_harga.php';
    post_response_text(tujuan, param, respog);			

    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                  document.getElementById('containerNopp').innerHTML=con.responseText;
                        }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }	
}
function setDataNopp(brNopp)
{
    listdt=document.getElementById('nopp');//.value=brNopp;
    for(awal=0;awal<listdt.length;awal++)
    {
        if(listdt.options[awal].value==brNopp)
        {
            listdt.options[awal].selected=true;
        }
    }
    closeDialog2();
}
</script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['bandingHarga']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['nopp']?></label></td><td><select id="nopp" name="nopp"  style="width:200px;" ><?php echo $optListNopp; ?></select><img  src='images/search.png' class=dellicon title='<?php echo $_SESSION['lang']['find']." ".$_SESSION['lang']['nopp'];?>' onclick="searchNopp('<?php echo $_SESSION['lang']['find']." ".$_SESSION['lang']['nopp']; ?>','<fieldset><legend><?php echo $_SESSION['lang']['find']." ".$_SESSION['lang']['nopp'];?></legend><?php echo $_SESSION['lang']['find']; ?>&nbsp;<input type=text class=myinputtext id=kdNopp><button class=mybutton onclick=findNopp()><?php echo $_SESSION['lang']['find']; ?></button></fieldset><div id=containerNopp style=overflow=auto;height=380;width=485></div>',event);"></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['form']?></label></td><td><select id="formPil" name=""formPil style="width:200px;"><?php echo $optLokal; ?></select></td></tr>

<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('log_slave_2perbandingan_harga','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><!--<button onclick="zPdf('log_slave_2perbandingan_harga','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button>--><button onclick="zExcel(event,'log_slave_2perbandingan_harga.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>

</table>
</fieldset>
</div>


<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:550px;width:1200px'>

</div></fieldset>

<?php
CLOSE_BOX();
echo close_body();
?>