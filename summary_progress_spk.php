<?php //@Copy nangkoelframework 
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript src=js/zMaster.js></script> 
<script language=javascript src=js/zSearch.js></script>
<script languange=javascript1.2 src='js/formTable.js'></script>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/zReport.js'></script>
<script>
function getUnit()
{
    reg=document.getElementById('regional').options[document.getElementById('regional').selectedIndex].value;
    param='proses=getUnit'+'&regional='+reg;
	tujuan='summary_slave_progress_spk';
    post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    //var res = document.getElementById(idCont);
//                    res.innerHTML = con.responseText;
					  document.getElementById('unit').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

}
function getUnit2()
{
    reg=document.getElementById('tahun').options[document.getElementById('tahun').selectedIndex].value;
    param='proses=getUnit'+'&tahun='+reg;
	tujuan='summary_slave_progress_spk2';
    post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    //var res = document.getElementById(idCont);
//                    res.innerHTML = con.responseText;
					  document.getElementById('kontaktor').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

}
    </script>
<?php
include('master_mainMenu.php');
$arr = "##periode##regional##unit";
OPEN_BOX();

//get existing period
$str="select distinct substr(tanggal,1,7) as periode from ".$dbname.".log_spkht
      order by tanggal desc";
$res=mysql_query($str); 
$optperiode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optthn="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optregional=$optperiode;
$optUnit="<option value=''>".$_SESSION['lang']['all']."</option>";
while($bar=mysql_fetch_object($res))
{
	$optperiode.="<option value='".$bar->periode."'>".$bar->periode."</option>";      
        
}
$str="select distinct substr(tanggal,1,4) as tahun from ".$dbname.".log_spkht
      order by tanggal desc";
$res=mysql_query($str); 
while($rdt=mysql_fetch_object($res))
{
$optthn.="<option value='".$rdt->tahun."'>".$rdt->tahun."</option>";
}
//get existing regional
$str="select distinct * from ".$dbname.".bgt_regional
      order by regional desc";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$optregional.="<option value='".$bar->regional."'>".$bar->nama."</option>";
}
$arr2 = "##tahun##kontaktor";
?>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['summaryprogress']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['periode']?></label></td>
    <td><select id=periode style='width:200px;'><?php echo $optperiode; ?></select></td>
</tr>
<tr><td><label><?php echo $_SESSION['lang']['regional']?></label></td>
    <td><select id=regional style='width:200px;' onchange="getUnit()"><?php echo $optregional; ?></select></td>
</tr>
<tr><td><label><?php echo $_SESSION['lang']['unit']?></label></td>
    <td><select id=unit style='width:200px;'><?php echo $optUnit; ?></select></td>
</tr>
<tr>
    <td colspan="3">
      <?php echo " <button onclick=\"zPreview('summary_slave_progress_spk','".$arr."','reportcontainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['preview']."</button>
    <button onclick=\"zExcel(event,'summary_slave_progress_spk.php','".$arr."','reportcontainer')\" class=\"mybutton\" name=\"excel\" id=\"excel\">".$_SESSION['lang']['excel']."</button>    
    <button onclick=\"zPdf('summary_slave_progress_spk','".$arr."','reportcontainer')\" class=\"mybutton\" name=\"pdf\" id=\"pdf\">". $_SESSION['lang']['pdf']."</button>"; ?></td>
</tr>
</table>
</fieldset>
    
    <fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['summaryprogress']?> per <?php echo $_SESSION['lang']['tahun']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['tahun']?></label></td>
    <td><select id=tahun style='width:200px;' onchange="getUnit2()"><?php echo $optthn; ?></select></td>
</tr>
<tr><td><label><?php echo $_SESSION['lang']['kontraktor']?></label></td>
    <td><select id=kontaktor style='width:200px;' ><?php echo $optUnit; ?></select></td>
</tr>

<tr>
    <td colspan="3">
      <?php echo " <button onclick=\"zPreview('summary_slave_progress_spk2','".$arr2."','reportcontainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['preview']."</button>
    <button onclick=\"zExcel(event,'summary_slave_progress_spk2.php','".$arr2."','reportcontainer')\" class=\"mybutton\" name=\"excel\" id=\"excel\">".$_SESSION['lang']['excel']."</button>    
    <!--<button onclick=\"zPdf('summary_slave_progress_spk2','".$arr2."','reportcontainer')\" class=\"mybutton\" name=\"pdf\" id=\"pdf\">". $_SESSION['lang']['pdf']."</button>-->"; ?></td>
</tr>
</table>
</fieldset>
<?php

CLOSE_BOX();
OPEN_BOX('','Result:');
echo"<fieldset><legend>".$_SESSION['lang']['list']."</legend>
                 <div id='reportcontainer' style='width:100%;height:550px;overflow:scroll;background-color:#FFFFFF;'></div> 
                 </fieldset>"; 
CLOSE_BOX();
close_body();
exit;
?>
