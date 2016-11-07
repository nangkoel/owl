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
$lksiTugas=substr($_SESSION['empl']['lokasitugas'],0,4);
if($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
    $sPeriode="select distinct periode from ".$dbname.".sdm_5periodegaji order by periode desc";
}
else{
$optOrg= '<option value="">'.$_SESSION['lang']['all'].'</option>';	
$sPeriode="select distinct periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$lksiTugas."' order by periode desc";
}
$qPeriode=mysql_query($sPeriode) or die(mysql_error());
while($rPeriode=mysql_fetch_assoc($qPeriode))
{
	$optPeriode.="<option value=".$rPeriode['periode'].">".substr(tanggalnormal($rPeriode['periode']),1,7)."</option>";
}

if(($_SESSION['empl']['tipelokasitugas']=='HOLDING')||($_SESSION['empl']['tipelokasitugas']=='KANWIL'))
{
	$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where  induk='".$_SESSION['org']['kodeorganisasi']."' and tipe!='HOLDING' order by namaorganisasi asc ";	
}
else
{
	$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where induk='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by kodeorganisasi asc";
}

$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}
$optGaji="<option value='All'>".$_SESSION['lang']['all']."</option>";
                $arrsgaj=getEnum($dbname,'datakaryawan','sistemgaji');
                foreach($arrsgaj as $kei=>$fal)
                {
                        $optGaji.="<option value='".$kei."'>".$_SESSION['lang'][strtolower($fal)]."</option>";
                 
                }  

$optTp="<option value=''>".$_SESSION['lang']['all']."</option>";
$iTp="select * from ".$dbname.".sdm_5tipekaryawan where id<>'0'";
$nTp=mysql_query($iTp) or die(mysql_error($conn));
while($dTp=mysql_fetch_assoc($nTp))
{
	$optTp.="<option value=".$dTp['id'].">".$dTp['tipe']."</option>";
}

 
$arr="##kdOrg##periode##tgl1##tgl2##sistemGaji##tp";
$arrKry="##kdeOrg##period##idKry##tgl_1##tgl_2";

?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<script>
function getTgl()
{
	periode=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
	kdUnit=document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;
	param='periode='+periode+'&proses=getTgl'+'&kdUnit='+kdUnit;
	//alert(param);
	tujuan='sdm_slave_2potongan_pendapatan';
	post_response_text(tujuan+'.php?'+param, param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                        ar=con.responseText.split("###");
                        document.getElementById('tgl1').value=ar[0];
                        document.getElementById('tgl2').value=ar[1];
                        document.getElementById('tgl1').disabled=true;
                        document.getElementById('tgl2').disabled=true;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
//
//  alert(fileTarget+'.php?proses=preview', param, respon);
}
function getKry()
{
    kdeOrg=document.getElementById('kdeOrg').options[document.getElementById('kdeOrg').selectedIndex].value;
    param='kdeOrg='+kdeOrg;
    tujuan='sdm_slave_2potongan_pendapatan';
    post_response_text(tujuan+'.php?proses=getKry', param, respon);
    function respon() {
    if (con.readyState == 4) {
        if (con.status == 200) {
            busy_off();
            if (!isSaveResponse(con.responseText)) {
                alert('ERROR TRANSACTION,\n' + con.responseText);
            } else {
                // Success Response
                                            document.getElementById('idKry').innerHTML=con.responseText;
            }
        } else {
            busy_off();
            error_catch(con.status);
        }
    }
}
}
function getTgl2()
{
    periode=document.getElementById('period').options[document.getElementById('period').selectedIndex].value;
    kdUnit=document.getElementById('kdeOrg').options[document.getElementById('kdeOrg').selectedIndex].value;
    param='periode='+periode+'&proses=getTgl'+'&kdUnit='+kdUnit;
    tujuan='sdm_slave_2potongan_pendapatan';
    post_response_text(tujuan+'.php?'+param, param, respon);
    function respon() {
    if (con.readyState == 4) {
        if (con.status == 200) {
            busy_off();
            if (!isSaveResponse(con.responseText)) {
                alert('ERROR TRANSACTION,\n' + con.responseText);
            } else {
                // Success Response
                                            ar=con.responseText.split("###");
                                            document.getElementById('tgl_1').value=ar[0];
                                            document.getElementById('tgl_2').value=ar[1];
                                            document.getElementById('tgl_1').disabled=true;
                                            document.getElementById('tgl_2').disabled=true;
            }
        } else {
            busy_off();
            error_catch(con.status);
        }
    }
}
//
  //  alert(fileTarget+'.php?proses=preview', param, respon);
}

function getKry()
{
	kdeOrg=document.getElementById('kdeOrg').options[document.getElementById('kdeOrg').selectedIndex].value;
	param='kdeOrg='+kdeOrg;
	tujuan='sdm_slave_2rekapabsen';
	post_response_text(tujuan+'.php?proses=getKry', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                        document.getElementById('idKry').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function Clear1()
{
	document.getElementById('tgl1').value='';
	document.getElementById('tgl2').value='';
	document.getElementById('tgl1').disabled=false;
	document.getElementById('tgl2').disabled=false;
	document.getElementById('kdOrg').value='';
	document.getElementById('periode').value='';
	document.getElementById('printContainer').innerHTML='';
	document.getElementById('tp').value='';
}
function Clear2()
{
	document.getElementById('tgl_1').value='';
	document.getElementById('tgl_2').value='';
	document.getElementById('tgl_1').disabled=false;
	document.getElementById('tgl_2').disabled=false;
	document.getElementById('kdeOrg').value='';
	document.getElementById('period').value='';
	document.getElementById('idKry').innerHTML="<option value''></option>";
	document.getElementById('printContainer').innerHTML='';
}
</script>

<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['lapPotongan']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['unit']?></label></td><td><select id="kdOrg" name="kdOrg" style="width:150px"><?php echo $optOrg?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['periode']?></label></td><td><select id="periode" name="periode" style="width:150px" onchange="getTgl()"><option value=""></option><?php echo $optPeriode?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['tanggalmulai']?></label></td><td><input type="text" class="myinputtext" id="tgl1" name="tgl1" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" /></td></tr>

<tr><td><label><?php echo $_SESSION['lang']['tanggalsampai']?></label></td><td><input type="text" class="myinputtext" id="tgl2" name="tgl2" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" />
        <input type="hidden" id="sistemGaji" value="harian" />
    
    </td></tr>
<tr><td><label><?php echo $_SESSION['lang']['tipekaryawan']?></label></td><td><select id="tp" name="tp" style="width:150px"><?php echo $optTp?></select></td></tr>
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('sdm_slave_2potongan_pendapatan','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>	<button onclick="zExcel(event,'sdm_slave_2potongan_pendapatan.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button><button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal"><?php echo $_SESSION['lang']['cancel']?></button></td></tr>

</table>
</fieldset>
</div>
<!--<div >
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['lapPotongan']."/".$_SESSION['lang']['karyawan'];?> </b><?php //echo $_SESSION['lang']['']?></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['unit']?></label></td><td><select id="kdeOrg" name="kdeOrg" style="width:150px" onchange="getKry()"><?php echo $optOrg?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['namakaryawan']?></label></td><td><select id="idKry" name="idKry" style="width:150px"><option value=""></option></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['periode']?></label></td><td><select id="period" name="period" style="width:150px" onchange="getTgl2()"><option value=""></option><?php echo $optPeriode?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['tanggalmulai']?></label></td><td><input type="text" class="myinputtext" id="tgl_1" name="tgl_1" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" /></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['tanggalsampai']?></label></td><td><input type="text" class="myinputtext" id="tgl_2" name="tgl_2" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" /></td></tr>


<tr><td colspan="2"><button onclick="zPreview('sdm_slave_2potongan_pendapatan_kary','<?php echo $arrKry?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zExcel(event,'sdm_slave_2potongan_pendapatan_kary.php','<?php echo $arrKry?>')" class="mybutton" name="preview" id="preview">Excel</button><button onclick="Clear2()" class="mybutton" name="btnBatal" id="btnBatal"><?php echo $_SESSION['lang']['cancel']?></button></td></tr>
</table>
</fieldset>
</div>
<div style="margin-bottom: 30px;">
</div>-->
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>

</div></fieldset>


<?php

CLOSE_BOX();
echo close_body();
?>