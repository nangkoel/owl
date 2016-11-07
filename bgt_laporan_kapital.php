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
$optOrg="<option value='%'>".$_SESSION['lang']['all']."</option>";
$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where  CHAR_LENGTH(kodeorganisasi)='4' order by namaorganisasi asc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}
$optpt="<option value='%'>".$_SESSION['lang']['all']."</option>";
$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where  tipe='PT'";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optpt.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}
$optThn="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sThn="select distinct  tahunbudget from ".$dbname.".bgt_kapital order by tahunbudget desc";
$qThn=mysql_query($sThn) or die(mysql_error($conn));
while($rThn=mysql_fetch_assoc($qThn))
{
    $optThn.="<option value='".$rThn['tahunbudget']."'>".$rThn['tahunbudget']."</option>";
}
$arr="##thnBudget##kdUnit##pt";
?>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/zReport.js'></script>
<script>
function summForm()
{
	//closeDialog();
	width='350';
	height='200';
	content="<div id=container style='overflow:auto;width:100%;height:190px;'></div>";
	ev='event';
	title="Detail Alokasi";
	showDialog1(title,content,width,height,ev);
}
function getAlokasi(kdTraksi,kdkend,thnbdget)
{
    summForm();
    kodeTraksi=kdTraksi;
    kdVhc=kdkend;
    thnBudget=thnbdget;
    param='kdTraksi='+kodeTraksi+'&kdVhc='+kdVhc+'&thnBudget='+thnBudget;
    tujuan='bgt_slave_laporan_rp_jam_kendaraan.php';
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
                           // alert(con.responseText);
                            document.getElementById('container').innerHTML=con.responseText;
                            //return con.responseText;
                    }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
          }	
     } 	    
      post_response_text(tujuan+'?'+'proses=getAlokasi', param, respog);
    
}
function summForm2()
{
	//closeDialog();
	width='650';
	height='350';
	content="<div id=container2 style='overflow:auto;width:100%;height:330px;'></div>";
	ev='event';
	title="Detail Alokasi";
	showDialog2(title,content,width,height,ev);
}
function getBiaya(kdTraksi,kdkend,thnbdget)
{
    summForm2();
    kodeTraksi=kdTraksi;
    kdVhc=kdkend;
    thnBudget=thnbdget;
    param='kdTraksi='+kodeTraksi+'&kdVhc='+kdVhc+'&thnBudget='+thnBudget;
    tujuan='bgt_slave_laporan_rp_jam_kendaraan.php';
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
                           // alert(con.responseText);
                            document.getElementById('container2').innerHTML=con.responseText;
                            //return con.responseText;
                    }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
          }	
     } 	    
      post_response_text(tujuan+'?'+'proses=getBiaya', param, respog);
    
}
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='200';
   height='150';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}
function dataKeExcelAlokasi(ev,kdTraksi,kdkend,thnbdget)
{
        kodeTraksi=kdTraksi;
        kdVhc=kdkend;
        thnBudget=thnbdget;
        param='kdTraksi='+kodeTraksi+'&kdVhc='+kdVhc+'&thnBudget='+thnBudget+'&getExcelAlokasi'+'&proses=excelAlokasi';
        tujuan='bgt_slave_laporan_rp_jam_kendaraan.php';
	judul='Report Ms.Excel';	
	printFile(param,tujuan,judul,ev)	
}
function dataKeExcel(ev,kdTraksi,kdkend,thnbdget)
{
        kodeTraksi=kdTraksi;
        kdVhc=kdkend;
        thnBudget=thnbdget;
        param='kdTraksi='+kodeTraksi+'&kdVhc='+kdVhc+'&thnBudget='+thnBudget+'&getExcelAlokasi'+'&proses=excelBiaya';
        tujuan='bgt_slave_laporan_rp_jam_kendaraan.php';
	judul='Report Ms.Excel';	
	printFile(param,tujuan,judul,ev)	
}
function Clear1()
{
    document.getElementById('thnBudget').value='';
    document.getElementById('kdUnit').value='';
    document.getElementById('printContainer').innerHTML='';
}
</script>

<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['lapKapital']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['budgetyear']?></label></td><td><select id='thnBudget' style="width:150px;"><?php echo $optThn?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['pt']?></label></td><td><select id='pt'  style="width:150px;"><?php echo $optpt?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['namaorganisasi']?></label></td><td><select id='kdUnit'  style="width:150px;"><?php echo $optOrg?></select></td></tr>
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('bgt_slave_laporan_kapital','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview"><?php echo $_SESSION['lang']['preview']?></button>
<button onclick="zPdf('bgt_slave_laporan_kapital','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview"><?php echo $_SESSION['lang']['pdf']?></button>
        <button onclick="zExcel(event,'bgt_slave_laporan_kapital.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview"><?php echo $_SESSION['lang']['excel']?></button>
        <button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal"><?php echo $_SESSION['lang']['cancel']?></button></td></tr>

</table>
</fieldset>
</div>

<div style="margin-bottom: 30px;">
</div>
<fieldset style='clear:both'><legend><b><?php echo $_SESSION['lang']['printArea']?></b></legend>
<div id='printContainer' style='overflow:auto;height:350px;width:1100px;'>

</div></fieldset>

<?php

CLOSE_BOX();
echo close_body();
?>