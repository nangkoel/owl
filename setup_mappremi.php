<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
include('lib/jAddition.php');
OPEN_BOX();
?>
<script type="text/javascript" src="js/setup_mappremi.js" /></script>
<?php
$optKeycode='';
$sKey="select code from ".$dbname.".setup_keycode order by code asc";
$qKey=mysql_query($sKey) or die(mysql_error());
while($rKey=mysql_fetch_assoc($qKey))
{
	$optKeycode.="<option value=".$rKey['code']." title=".$rKey['keterangan'].">".$rKey['code']."</option>";
}
$soptOrg='';
/*$sorg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where
      tipe in('HOLDING','KEBUN','KANWIL','PABRIK') order by namaorganisasi";
*/
$sorg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
$qorg=mysql_query($sorg) or die(mysql_error());
global $kd_org;
while($rorg=mysql_fetch_assoc($qorg))
{
	$kd_org=$rorg['kodeorganisasi'];
	$optOrg.="<option '".($rorg['kodeorganisasi']==$rest['kodeorganisasi']?'selected=selected':'')."' value=".$rorg['kodeorganisasi']." >".$rorg['namaorganisasi']."</option>";
}
/*$optOrg='';
$sPt="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='PT'";
$qPt=mysql_query($sPt) or die(mysql_error());
while($rPt=mysql_fetch_assoc($qPt))
{
	$optOrg.="<option value=".$rPt['kodeorganisasi'].">".$rPt['namaorganisasi']."</option>";
}*/
$arrTipe=getEnum($dbname,'setup_mappremi','tipepremi');
$optTipe='';
foreach($arrTipe as $isi)
{
	$optTipe.="<option value=".$isi.">".$isi."</option>";
}
?>
<fieldset>
	<legend><?php echo $_SESSION['lang']['setupKeycode']?></legend>
	<table cellspacing="1" border="0">
		<tr>
			<td><?php echo $_SESSION['lang']['kodeorg']?></td>
			<td>:</td>
			<td><select id="optOrg" name="optOrg"  style="width:150px;"><?php echo $optOrg?></select></td>
		</tr>
		<tr>
			<td><?php echo $_SESSION['lang']['tipepremi']?></td>
			<td>:</td>
			<td><select id="tipePremi" style="width:150px;"><?php echo $optTipe;?></select><input type="hidden" id="oldtipePremi" name="oldtipePremi" /></td>
		</tr>
        <tr>
			<td><?php echo $_SESSION['lang']['keycode']?></td>
			<td>:</td>
			<td><select id="keyCode" name="keyCode"><?php echo $optKeycode;?></select><input type="hidden" id="oldKey" name="oldKey" /></td>
		</tr>
		<input type="hidden" id="method" name="method" value="insert" />
		<tr>
			<td colspan="3">
			<button class="mybutton" onclick="smpnKeycode()"><?php echo $_SESSION['lang']['save']?></button>
			<button class="mybutton" onclick="cancelKeycode()"><?php echo $_SESSION['lang']['cancel']?></button></td>
		</tr>
	</table>
</fieldset>
<?php CLOSE_BOX();
 OPEN_BOX();
?>
<fieldset>
	 <table class="sortable" cellspacing="1" border="0">
	 <thead>
	 <tr class=rowheader>
	 <td>No.</td>
	 <td><?php echo $_SESSION['lang']['kodeorg']?></td>
	 <td><?php echo $_SESSION['lang']['tipepremi'];?></td> 
     <td><?php echo $_SESSION['lang']['keycode'];?></td> 
	 <td>Action</td>
	 </tr>
	 </thead>
	 <tbody id="container">
	 <?php 
	 $limit=10;
	$page=0;
	if(isset($_POST['page']))
	{
	$page=$_POST['page'];
	if($page<0)
	$page=0;
	}
	$offset=$page*$limit;
	
	$ql2="select count(*) as jmlhrow from ".$dbname.".setup_mappremi  order by kodeorg,keycode desc limit ".$offset.",".$limit."";// echo $ql2;
	$query2=mysql_query($ql2) or die(mysql_error());
	while($jsl=mysql_fetch_object($query2)){
	$jlhbrs= $jsl->jmlhrow;
	}
	
	$str="select * from ".$dbname.".setup_mappremi order by kodeorg,keycode desc";
	if($res=mysql_query($str))
	{
	while($bar=mysql_fetch_object($res))
	{
		$sPt="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$bar->kodeorg."'";
		$qPt=mysql_query($sPt) or die(mysql_error());
		$rOrg=mysql_fetch_assoc($qPt);
	$no+=1;
	echo"<tr class=rowcontent id='tr_".$no."'>
	<td>".$no."</td>
	<td id='nmorg_".$no."'>".$rOrg['namaorganisasi']."</td>
	<td id='kpsits_".$no."'>".$bar->tipepremi."</td>
	<td id='kpsits_".$no."'>".$bar->keycode."</td>
	<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->kodeorg."','".$bar->tipepremi."','".$bar->keycode."');\"><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delCode('".$bar->kodeorg."','".$bar->tipepremi."','".$bar->keycode."');\"></td>
	</tr>";
	}	 
	echo" 
	</tr><tr class=rowheader><td colspan=5 align=center>
	".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
	<br />
	<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
	<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
	</td></tr>"; 	   	
	}	
	else
	{
	echo " Gagal,".(mysql_error($conn));
	}	
	
	 ?>
	  </tbody>
	 <tfoot>
	 </tfoot>
	 </table>
</fieldset>

<?php
CLOSE_BOX();
echo close_body();
?>