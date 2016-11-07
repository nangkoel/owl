<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('',"<b>".$_SESSION['lang']['mutasiLahan']."</b>");
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script language="javascript" src="js/kebun_mutasiLahan.js"></script>

<?php
for($x=0;$x<=24;$x++)
{
	$dt=mktime(0,0,0,date('m')-$x,15,date('Y'));
	$optPeriode.="<option value=".date("Y-m",$dt).">".date("Y-m",$dt)."</option>";
}
	$lokasi=$_SESSION['empl']['lokasitugas'];
	$sql="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='KEBUN' and kodeorganisasi='".$lokasi."'";
	$query=mysql_query($sql) or die(mysql_error());
	while($res=mysql_fetch_assoc($query))
	{
		$optOrg.="<option value=".$res['kodeorganisasi'].">".$res['namaorganisasi']."</option>"; 
	}
/*echo"<table cellspacing=1 border=0>
     <tr valign=moiddle>
	 <td align=center style='width:100px;cursor:pointer;' onclick=add_new_data()>
	   <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
	 <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
			echo $_SESSION['lang']['kodeorg'].":<select id=unitOrg name=unitOrg><option value=''></option>".$optOrg."</select>&nbsp;";
			echo $_SESSION['lang']['tanggal'].":<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />";
			echo"<button class=mybutton onclick=cariCurah()>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>
	
	 </tr>
	 </table> "*/; 
?>
<div id="headher">

<fieldset>
<legend><?php echo $_SESSION['lang']['entryForm']?></legend>
<table cellspacing="1" border="0">
<tr>
<td><?php echo $_SESSION['lang']['kodeorg']?></td>
<td>:</td>
<td>
<select id="kodeOrg" name="kodeOrg" style="width:170px;" onchange="getAfdeling(0,0)" ><option value=""></option><?php echo $optOrg;?></select>
</td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['afdeling']?></td>
<td>:</td>
<td>
<select id="kodeAfdeling" name="kodeAfdeling" style="width:170px;" onchange="getBlok(0,0)" ><option value=""></option></select>
</td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['blok']?></td>
<td>:</td>
<td>
<select id="kodeBlok" name="kodeBlok" style="width:170px;"><option value=""></option></select>
</td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['periodeTm']?></td>
<td>:</td>
<td>
<input type="text" class="myinputtext" id="periodetm"  onkeypress="return tanpa_kutip(event)" size="10" maxlength="7" value="00-0000" style="width:170px;" /> e.g:12-2010 (bulan dan tahun)</td>
</tr>

<tr>
<td colspan="3" id="tmbLheader">
<button class="mybutton" id="dtlAbn" onclick="saveData()"><?php echo $_SESSION['lang']['save']?></button><button class="mybutton" id="cancelAbn" onclick="cancelSave()"><?php echo $_SESSION['lang']['cancel']?></button>
</td>
</tr>
</table><input type="hidden" id="proses" name="proses" value="insert"  />
</fieldset>

</div>
<?php
CLOSE_BOX();
?>
<div id="listData">
<?php OPEN_BOX()?>
<fieldset>
<legend><?php echo $_SESSION['lang']['list']?></legend>

<table cellspacing="1" border="0" class="sortable">
<thead>
<tr class="rowheader">
<td>No.</td>
<td><?php echo $_SESSION['lang']['kebun']?></td>
<td><?php echo $_SESSION['lang']['afdeling'];?></td> 
<td><?php echo $_SESSION['lang']['blok'];?></td>
<td><?php echo $_SESSION['lang']['periodeTm'];?></td>	 
<td>Action</td>
</tr>
</thead>
<tbody id="contain">
<script>loadData()</script>

</tbody>
</table>
</fieldset>

<?php CLOSE_BOX()?>
</div>




<?php 
echo close_body();

?>