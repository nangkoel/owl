<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('',"<b>".$_SESSION['lang']['lembur']."</b>");
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script language="javascript">
 
nmTmblDone='<?php echo $_SESSION['lang']['done']?>';
nmTmblCancel='<?php echo $_SESSION['lang']['cancel']?>';
nmTmblSave='<?php echo $_SESSION['lang']['save']?>';
nmTmblCancel='<?php echo $_SESSION['lang']['cancel']?>';
</script>
<script language="javascript" src="js/sdm_lembur.js"></script>
<input type="hidden" id="proses" name="proses" value="insert"  />


<div id="action_list">
<?php
for($x=0;$x<=24;$x++)
{
	$dt=mktime(0,0,0,date('m')-$x,15,date('Y'));
	$optPeriode.="<option value=".date("Y-m",$dt).">".date("Y-m",$dt)."</option>";
}
	$idOrg=substr($_SESSION['empl']['lokasitugas'],0,4);
	$sql="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$idOrg."' or induk='".$idOrg."' ORDER BY `namaorganisasi` ASC";
	$query=mysql_query($sql) or die(mysql_error());
	while($res=mysql_fetch_assoc($query))
	{
		$optOrg.="<option value=".$res['kodeorganisasi'].">".$res['namaorganisasi']."</option>"; 
	}
echo"<table cellspacing=1 border=0>
     <tr valign=moiddle>
	 <td align=center style='width:100px;cursor:pointer;' onclick=add_new_data()>
	   <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
	 <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
			echo $_SESSION['lang']['nm_perusahaan'].":<select id=kdOrgCr><option value=''></option>".$optOrg."</select>&nbsp;";
			echo $_SESSION['lang']['tanggal'].":<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />";
			echo"<button class=mybutton onclick=cariAsbn()>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>
	 </tr>
	 </table> "; 
?>
</div>
<?php
CLOSE_BOX();
?>
<div id="listData">
<?php OPEN_BOX()?>
<fieldset>
<legend><?php echo $_SESSION['lang']['list']?></legend>

<div id="contain">
<script>loadData();</script>
</div>
</fieldset>
<?php CLOSE_BOX()?>
</div>



<div id="headher" style="display:none">
<?php
OPEN_BOX();
	
?>
<fieldset>
<legend><?php echo $_SESSION['lang']['header']?></legend>
<table cellspacing="1" border="0">
<tr>
<td><?php echo $_SESSION['lang']['kodeorg']?></td>
<td>:</td>
<td>
<select id="kdOrg" name="kdOrg" style="width:150px;" ><option value=""><?php echo $_SESSION['lang']['pilihdata']; ?></option><?php echo $optOrg;?></select>
</td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['tanggal']?></td>
<td>:</td>
<td>
<input type="text" class="myinputtext" id="tglAbsen" name="tglAbsen" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" />
</td>
</tr>
<tr>
<td colspan="3" id="tmbLheader">
</td>
</tr>
</table>
</fieldset>

<?php
CLOSE_BOX();
?>
</div>
<div id="detailEntry" style="display:none">
<?php 
OPEN_BOX();
?>
<div id="addRow_table">
<fieldset>
<legend><?php echo $_SESSION['lang']['detail']?></legend>
<div id="detailIsi">
</div>
<table cellspacing="1" border="0">
<tr><td id="tombol">

</td></tr>
</table>
</fieldset>
</div><br />
<br />
<div style="overflow:auto; height:300px;">
<fieldset>
<legend><?php echo $_SESSION['lang']['datatersimpan']?></legend>
<table cellspacing='1' border='0' class='sortable'>
<thead>
 <tr class="rowheader">
 <td>No</td>
    <td><?php echo $_SESSION['lang']['namakaryawan'] ?></td>
 	<td><?php echo $_SESSION['lang']['tipelembur'] ?></td>
  	<td><?php echo $_SESSION['lang']['jamaktual'] ?></td>
  	<td style='display:none'><?php echo $_SESSION['lang']['uangmakan'] ?></td>
    <td style='display:none'><?php echo $_SESSION['lang']['penggantiantransport'] ?></td>
	 <td style='display:none'><?php echo $_SESSION['lang']['uangkelebihanjam'] ?></td>
    <td>Action</td>
    </tr>
</thead>
<tbody id="contentDetail">

</tbody>
</table>
</fieldset>
</div>
<?php
CLOSE_BOX();
?>
</div>
<?php 
echo close_body();
?>

