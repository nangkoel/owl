<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zFunction.php');
echo open_body();
include('master_mainMenu.php');
$lksiTugas=substr($_SESSION['empl']['lokasitugas'],0,4);

$frm[0]='';
$frm[1]='';

for($x=0;$x<=3;$x++)
{
	$dt=mktime(0,0,0,date('m')-$x,15,date('Y'));
	$optper.="<option value=".date("Y-m",$dt).">".date("m-Y",$dt)."</option>";
}
$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='PT' order by namaorganisasi asc";
$qOrg=mysql_query($sOrg) or die(mysql_error());
while($bar=mysql_fetch_object($qOrg))
{
	$optOrg.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}	


?>

<script type="text/javascript" src="js/pmn_rencanajual.js"></script>
<script type="text/javascript" src="js/zMaster.js"></script>
<script>
tmblSave='<?php echo $_SESSION['lang']['save'];?>';
tmblCancel='<?php echo $_SESSION['lang']['cancel'];?>';
tmblDone='<?php  echo $_SESSION['lang']['done']?>';
</script>
<?php

OPEN_BOX('',"<b>".$_SESSION['lang']['rencanaJual']."</b><br>");
$frm[0].="<fieldset><legend>".$_SESSION['lang']['header']."</legend>";
$frm[0].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['periode']."</td><td>:</td><td>
<select id=periode name=periode style='width:150px'><option value=''></option>".$optper."</select></td></tr>
<tr><td>".$_SESSION['lang']['kodeorg']."</td><td>:</td><td>
<select id=kdOrg name=kdOrg style='width:150px'>".$optOrg."</select></td></tr>

<tr><td colspan=3 id=tmbLhead><script>shwTmbl()</script></td></tr>
</table><input type=hidden id=proses name=proses value='insert' />";
$frm[0].="</fieldset>";
$frm[0].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>
         <table class=sortable cellspacing=1 border=0>
		<thead>
		<tr class=\"rowheader\">
		<td>No.</td>
		<td>".$_SESSION['lang']['periode']."</td>
		<td>".$_SESSION['lang']['kodeorg']."</td>
		<td>Action</td>
		</tr></thead><tbody id=contain>
		<script>loadData()</script>
		";
$frm[0].="</tbody></table></fieldset>";

//assseettt
$optBrg='';
$sBrg="select kodebarang,namabarang from ".$dbname.".log_5masterbarang where kelompokbarang='400' order by namabarang asc";
$qBrg=mysql_query($sBrg) or die(mysql_error());
while($rBrg=mysql_fetch_assoc($qBrg))
{
	$optBrg.="<option value=".$rBrg['kodebarang'].">".$rBrg['namabarang']."</option>";
}
$sCust="select kodecustomer,namacustomer from ".$dbname.".pmn_4customer";
$qCust=mysql_query($sCust) or die(mysql_error());
while($rCust=mysql_fetch_assoc($qCust))
{
	$optCust.="<option value=".$rCust['kodecustomer'].">".$rCust['namacustomer']."</option>";
}

$frm[1].="<fieldset><legend>".$_SESSION['lang']['entryForm']."</legend>";
$frm[1].="<table cellspacing=1 border=0>
		
		<tr>
		<td>".$_SESSION['lang']['periode']."</td><td><select id=periodeDetail name=periodeDetail style='width:150px' disabled>".$optper."</select></td></tr>
		<tr><td>".$_SESSION['lang']['kodeorg']."</td><td>
		<select id=kdOrgDetail name=kdOrgDetail style='width:150px' disabled>".$optOrg."</select></td></tr>
		<tr><td>".$_SESSION['lang']['tanggal']."</td><td><input type=text class=myinputtext id=tglDetail name=tglDetail onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style='width:150px' /></td></tr>
		<tr><td>".$_SESSION['lang']['namabarang']."</td>	<td><select id=kdBrg name=kdBrg style='width:150px'>".$optBrg."</select><input type=hidden id=oldKdbrg name=oldKdbrg /></td></tr>
		<tr><td>".$_SESSION['lang']['kodecustomer']."</td><td><select id=kdCust name=kdCust style='width:150px'>".$optCust."</select><input type=hidden id=oldCust name=oldCust /></td></tr>
		<tr><td>".$_SESSION['lang']['almt_kirim']."</td><td><input type=text class=myinputtext id=lokasi name=lokasi maxlength=45 onkeypress=\"return tanpa_kutip(event);\" style='width:150px'/></td></tr>
		<tr><td>".$_SESSION['lang']['volume']."</td><td><input type=text class=myinputtextnumber id=jmlh name=jmlh maxlength=4 value='0' onkeypress=\"return angka_doang(event);\" style='width:150px'/> KG</td></tr>
		<tr><td colspan=2><button class=mybutton onclick=saveDetail() >".$_SESSION['lang']['save']."</button><button class=mybutton onclick=clearDetail() >".$_SESSION['lang']['cancel']."</button></td>
		</tr>
</table>";

$frm[1].="</fieldset>";
$frm[1].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>
        <table class=sortable cellspacing=1 border=0>
		<thead>
		<tr class=\"rowheader\">
		<td>No.</td>
		<td>".$_SESSION['lang']['periode']."</td>
		<td>".$_SESSION['lang']['tanggal']."</td>
		<td>".$_SESSION['lang']['namabarang']."</td>
		<td>".$_SESSION['lang']['kodecustomer']."</td>
		<td>".$_SESSION['lang']['almt_kirim']."</td>
		<td>".$_SESSION['lang']['volume']." (KG)</td>
		<td>Action</td>
		</tr></thead><tbody id=containDetail>
		<script>loadDetail();</script>		";
$frm[1].="</tbody></table></fieldset><input type=hidden id=pros name=pros value=insertDetail />";



//========================
$hfrm[0]=$_SESSION['lang']['header'];
$hfrm[1]=$_SESSION['lang']['rencanaJualdetail'];

//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,250,800);
//===============================================	
?>

<?php
CLOSE_BOX();
echo close_body();
?>