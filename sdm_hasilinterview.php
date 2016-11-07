<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zFunction.php');
require_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
?>
<script language="javascript" src="js/zMaster.js"></script>
<script language=javascript src='js/zTools.js'></script>
<script type="text/javascript" src="js/sdm_hasilinterview.js"></script>
<script>
dataKdvhc="<?php echo $_SESSION['lang']['pilihdata']?>";
</script>
<?php
OPEN_BOX('',"");
echo"<fieldset style=width:350px;><legend>".$_SESSION['lang']['form']."</legend>";
##ambil departemen##
$optDept=makeOption($dbname, 'sdm_5departemen', 'kode,nama');
$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optAfd=$optPeriode;
$sprd="select distinct departemen from ".$dbname.".sdm_permintaansdm 
       where stpersetujuanhrd=1 order by tanggal desc";
$qprd=mysql_query($sprd) or die(mysql_error($conn));
while($rprd=mysql_fetch_assoc($qprd)){
	$optPeriode.="<option value='".$rprd['departemen']."'>".$optDept[$rprd['departemen']]."</option>";
}
$optPrd="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
for($x=0;$x<=6;$x++)
{
	$dte=mktime(0,0,0,(date('m')+2)-$x,15,date('Y'));
	$optPrd.="<option value=".date("Y-m",$dte).">".date("m-Y",$dte)."</option>";
}
echo"<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['periodetest']."</td><td>:</td><td><select style=width:150px id=periodeTest onchange='getNmLowongan()'>".$optPrd."</select></td></tr>
<tr><td>".$_SESSION['lang']['namalowongan']."</td><td>:</td><td>
<select id=nmLowongan name=nmLowongan style=width:150px; >".$optAfd."</select></td></tr>

<tr><td colspan=3 align=center>
<button class=mybutton onclick=prevData()  >".$_SESSION['lang']['preview']."</button>

<input type=hidden id=proses name=proses value=insert_header >
</td></tr></table>";
echo"</fieldset>";
echo"<div id=sddataList style='display:none;'><fieldset style=float:left;><legend>".$_SESSION['lang']['list']."</legend>
<div id=dataList>
 
</div>
    </fieldset></div>";

echo"<div style=clear:both></div><div id=pdfDet style='display:none;'>
     <fieldset style=float:left;width:100%;height:780px; ><legend>".$_SESSION['lang']['pdf']."</legend>";
echo"<div id=contentData></div>";
echo"</div>";
CLOSE_BOX();
echo close_body();
?>