<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
$bhs=$_SESSION['lang'];
?>
<?php
$optUnit="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where CHAR_LENGTH(kodeorganisasi)='4' and kodeorganisasi in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') order by namaorganisasi asc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optUnit.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}

$optSup="<option value=''>".$_SESSION['lang']['all']."</option>";
$sSup="select supplierid,namasupplier from ".$dbname.".log_5supplier where substring(kodekelompok,1,1)='S' order by namasupplier asc";
//echo $sSup;
$qSup=mysql_query($sSup) or die(mysql_error($conn));
while($rSup=mysql_fetch_assoc($qSup))
{
	$optSup.="<option value=".$rSup['supplierid'].">".$rSup['namasupplier']."</option>";
}
$optLokal="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

$optKdtraksi="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sTraksi="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where kodeorganisasi like '%".$_SESSION['empl']['lokasitugas']."%' and tipe='TRAKSI' order by namaorganisasi asc";
$qTraksi=mysql_query($sTraksi) or die(mysql_error());
while($rTraksi=mysql_fetch_assoc($qTraksi))
{
    $optKdtraksi.="<option value=".$rTraksi['kodeorganisasi'].">".$rTraksi['namaorganisasi']."</option>";
}
?>
<script>save="<?php echo $_SESSION['lang']['save']; ?>";btl="<?php echo $_SESSION['lang']['cancel']; ?>";pilih="<?php echo $_SESSION['lang']['pilihdata']; ?>";</script>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<script language=javascript src='js/budget_total_jam_vhc.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['totJamKendBudget']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['budgetyear']?></label></td><td><input type="text" id="thnBudget" class="myinputtextnumber" onkeypress="return angka_doang(event);" style="width:150px" maxlength="4" /></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['kodetraksi']?></label></td><td><select id="kdTraksi" name="kdTraksi" style="width:150px" onchange="getKdvhc(0,0)"><?php echo $optKdtraksi;?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['kodevhc']?></label></td><td><select id="kdVhc" name="kdVhc" style="width:150px"><?php echo $optLokal; ?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['unit']?></label></td><td><select id="kdUnit" style="width:150px;"><?php echo $optUnit ?></select></td></tr>
<tr><td><?php echo $_SESSION['lang']['totJamThn']?></td><td><input type="text" id="totJamThn" class="myinputtextnumber" onkeypress="return angka_doang(event);" style="width:150px"  /></td></tr>

<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><div id="tmblSave"><button onclick="saveHead()" class="mybutton" name="saveDt" id="saveDt"><?php echo $_SESSION['lang']['save'] ?></button>
        <button onclick="batal()" class="mybutton" name="btl" id="btl"><?php echo $_SESSION['lang']['cancel']?></button></div>
</td></tr>

</table><input type="hidden" id="proses" value="saveData"/>
</fieldset>
</div>
      <br />
      <br />
<div id='printContainer' style="display:none;">
      <fieldset style='clear:both;float: left;'><legend>Sebaran Bulanan</legend>

<?php
$arrBln=array("1"=>"Jan","2"=>"Feb","3"=>"Mar","4"=>"Apr","5"=>"Mei","6"=>"Jun","7"=>"Jul","8"=>"Aug","9"=>"Sept","10"=>"Okt","11"=>"Nov","12"=>"Des");
//echo"<pre>";
//print_r($arrBln);
//echo"</pre>";
$tot=count($arrBln);
echo"<table class=sortable border=0 cellspacing=1 cellpadding=1><thead><tr class=rowheader>";
foreach($arrBln as $brs=>$dtBln)
{
echo"<td>".$dtBln."</td>";
}
echo"<td>action</td></tr></thead>";
echo"<tbody><tr class=rowcontent>";
foreach($arrBln as $brs2 =>$dtBln2)
{
echo"<td><input type='text' id=jam_x".$brs2." class=\"myinputtextnumber\" style=\"width:50px;\" /></td>";
}
echo"<td align=center style='cursor:pointer;'><img id='detail_add' title='Simpan' class=zImgBtn onclick=\"saveJam(".$tot.")\" src='images/save.png'/></td></tr></tbody></table>";
?>
</fieldset></div>

<?php
CLOSE_BOX();
OPEN_BOX();
$optThnBudget="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optKdvhc="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optUnit="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sThnBudget="select distinct tahunbudget from ".$dbname.".bgt_vhc_jam where  kodetraksi like '%".$_SESSION['empl']['lokasitugas']."%' ";
$qThnBudget=mysql_query($sThnBudget) or die(mysql_error());
while($rThnBudget=mysql_fetch_assoc($qThnBudget))
{
    $optThnBudget.="<option value='".$rThnBudget['tahunbudget']."'>".$rThnBudget['tahunbudget']."</option>";
}
$sThnBudget2="select distinct unitalokasi from ".$dbname.".bgt_vhc_jam where  kodetraksi like '%".$_SESSION['empl']['lokasitugas']."%' ";
$qThnBudget2=mysql_query($sThnBudget2) or die(mysql_error());
while($rThnBudget2=mysql_fetch_assoc($qThnBudget2))
{
    $optUnit.="<option value='".$rThnBudget2['unitalokasi']."'>".$rThnBudget2['unitalokasi']."</option>";
}
$sThnBudget3="select distinct kodevhc from ".$dbname.".bgt_vhc_jam where  kodetraksi like '%".$_SESSION['empl']['lokasitugas']."%' ";
$qThnBudget3=mysql_query($sThnBudget3) or die(mysql_error());
while($rThnBudget3=mysql_fetch_assoc($qThnBudget3))
{
    $optKdvhc.="<option value='".$rThnBudget3['kodevhc']."'>".$rThnBudget3['kodevhc']."</option>";
}

echo"<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>";
echo"<table><tr><td>".$_SESSION['lang']['budgetyear']." <select id=thndBudgetHead onchange=loadData()>".$optThnBudget."</select></td>";
echo"<td>".$_SESSION['lang']['kodevhc']." <select id=kdVhcHead onchange=loadData()>".$optKdvhc."</select></td>";
echo"<td>".$_SESSION['lang']['unit']." <select id=kdUnit onchange=loadData()>".$optUnit."</select></td>";
echo"</tr></table>";
echo"<div id=contain><script>loadData()</script></div>";
echo"</fieldset>";
CLOSE_BOX();
echo close_body();
?>