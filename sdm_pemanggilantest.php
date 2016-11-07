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
<script type="text/javascript" src="js/sdm_pemanggilantest.js"></script>
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
$arrKond=array("0"=>">","1"=>"<");
$optKon="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
foreach($arrKond as $lstKond=>$gmbKond){
    $optKon.="<option value='".$lstKond."'>".$gmbKond."</option>";
}
echo"<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['utkdept']."</td><td>:</td><td>
<select id=deptId name=deptId style=width:150px; onchange=\"getData()\">".$optPeriode."</select></td></tr>
<tr><td>".$_SESSION['lang']['namalowongan']."</td><td>:</td><td>
<select id=nmLowongan name=nmLowongan style=width:150px; >".$optAfd."</select></td></tr>
<tr><td>".$_SESSION['lang']['periodetest']."</td><td>:</td><td><select style=width:150px id=periodeTest>".$optPrd."</select></td></tr>
 <tr><td>".$_SESSION['lang']['umur']."</td><td>:</td><td><select style=width:75px id=kondId>".$optKon."</select><input type='text' onkeypress='return angka_doang(event)' class='myinputtextnumber' id=umrNa style=width:75px /> </td></tr>
<tr><td colspan=3 align=center>
<button class=mybutton onclick=prevData()  >".$_SESSION['lang']['preview']."</button>

<input type=hidden id=proses name=proses value=insert_header >
</td></tr></table>";
echo"</fieldset>";
echo"<div id=sddataList style='display:none;'>
    <fieldset style=float:left;>
    <legend>".$_SESSION['lang']['list']."</legend>
<div id=dataList>
</div>
    </fieldset></div>";
echo"<button style='display:none;float:left;' id='tombolData' onclick=clsPdf()>".$_SESSION['lang']['tutup']."</button><div id=printpdf  style='display:none;'> </div>";


CLOSE_BOX();
echo close_body();
?>