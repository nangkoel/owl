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

$optTipe="<option value=''>".$_SESSION['lang']['all']."</option>";
$sTipe="select kode,nama from ".$dbname.".sdm_5departemen order by nama asc";
$qTipe=mysql_query($sTipe) or die(mysql_error());
while($rTipe=mysql_fetch_assoc($qTipe))
{
        $optTipe.="<option value=".$rTipe['kode'].">".$rTipe['nama']."</option>";
}

$optPeriode="<option value=''>".$_SESSION['lang']['all']."</option>";
$sPeriode="select distinct substring(tglpertanggungjawaban,1,7) as periode  from ".$dbname.".sdm_pjdinasht order by tglpertanggungjawaban desc";
$qPeriode=mysql_query($sPeriode) or die(mysql_error());
while($rPeriode=mysql_fetch_assoc($qPeriode))
{
    if($rPeriode['periode']!="0000-00")
    {
        $optPeriode.="<option value=".$rPeriode['periode'].">".$rPeriode['periode']."</option>";
    }
}

$optOrg="<select id=kdOrg name=kdOrg style=\"width:150px;\" onchange=getKaryawan()><option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='PT' order by namaorganisasi asc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
        $optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";

}
$optOrg.="</select>";


$arrSat=array("0"=>$_SESSION['lang']['belumlunas'],"1"=>$_SESSION['lang']['lunas']);
foreach($arrSat as $brsAr => $rStat)
{
    $optStat.="<option value=".$brsAr.">".$rStat."</option>";
}
$arr="##kdOrg##bagId##periode##karyawanId##stat";


?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<script>
function getKaryawan()
{
    kdPt=document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;
    param='kdOrg='+kdPt;
    tujuan='sdm_slave_2perjalananDinas.php';
    post_response_text(tujuan+'?proses=getKaryawan', param, respog);
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
                                                        document.getElementById('karyawanId').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  
}
function getKaryawan2()
{
    kdPt=document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;
    bagian=document.getElementById('bagId').options[document.getElementById('bagId').selectedIndex].value;
    param='kdOrg='+kdPt+'&bagId='+bagian;
    tujuan='sdm_slave_2perjalananDinas.php';
    post_response_text(tujuan+'?proses=getKaryawan', param, respog);
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
                                                        document.getElementById('karyawanId').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  
}
var opt="<option value=''><?php echo $_SESSION['lang']['all']; ?></option>";
function Clear1()
{
    document.getElementById('kdOrg').value='';
    document.getElementById('bagId').value='';
    document.getElementById('karyawanId').innerHTML='';
    document.getElementById('karyawanId').innerHTML=opt;
    document.getElementById('printContainer').innerHTML='';
    document.getElementById('stat').value='';
}
</script>

<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['lapPjd']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['perusahaan']?></label></td><td><?php echo $optOrg?></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['bagian']?></label></td><td><select id="bagId" name="bagId" style="width:150px" onchange="getKaryawan2()"><?php echo $optTipe?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['namakaryawan']?></label></td><td><select id="karyawanId" name="karyawanId"  style="width:150px"><option value=''><?php echo $_SESSION['lang']['all']?></option></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['periode']?></label></td><td><select id="periode" name="periode" style="width:150px"><?php echo $optPeriode?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['status']?></label></td><td><select id="stat" name="stat" style="width:150px"><option value=''><?php echo $_SESSION['lang']['all'] ?></option><?php echo $optStat?></select></td></tr>
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('sdm_slave_2perjalananDinas','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
        <button onclick="zPdf('sdm_slave_2perjalananDinas','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button>
        <button onclick="zExcel(event,'sdm_slave_2perjalananDinas.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button><button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal"><?php echo $_SESSION['lang']['cancel']?></button></td></tr>

</table>
</fieldset>
</div>

<div style="margin-bottom: 30px;">
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>
</div></fieldset>

<?php

CLOSE_BOX();
echo close_body();
?>