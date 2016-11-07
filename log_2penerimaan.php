<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX(); //1 O

?>
<script language="javascript" src="js/zMaster.js"></script>
<script type="text/javascript" src="js/log_2penerimaan.js"></script>
<div id="action_list">
<?php
$optGudang="<option value=''>".$_SESSION['lang']['pilihgudang']."</option>";
$optNma=makeOption($dbname,'organisasi', 'kodeorganisasi,namaorganisasi');
$sGudang="SELECT DISTINCT kodegudang FROM ".$dbname.".log_transaksiht";
$qGudang=mysql_query($sGudang) or die(mysql_error($conn));
while($rGudang=  mysql_fetch_assoc($qGudang))
{
    $optGudang.="<option value=".$rGudang['kodegudang'].">".$optNma[$rGudang['kodegudang']]."</option>";
}
echo"<table>
     <tr valign=moiddle>
	 <td align=center style='width:100px;cursor:pointer;' onclick=loadData()>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
	 <td><fieldset><legend>".$_SESSION['lang']['carinopo']."</legend>"; 
                        echo "<table cellpadding=1 cellspacing=1 border=0><tr><td>".$_SESSION['lang']['pilihgudang']."</td><td><select id=kdGdng>".$optGudang."</select></td>";
			echo "<td>".$_SESSION['lang']['nopp']."</td><td><input type=text id=txtsearch2 size=25 maxlength=30 class=myinputtext onkeypress='return tanpa_kutip(event)'></td>";
                        echo "<td>".$_SESSION['lang']['nopo']."</td><td><input type=text id=txtsearch size=25 maxlength=30 class=myinputtext onkeypress='return tanpa_kutip(event)'></td></tr><tr>";
			echo "<td>".$_SESSION['lang']['namabarang']."</td><td><input type=text class=myinputtext id=nmBrg onkeypress='return tanpa_kutip(event)' /></td>";
                        echo "<td>".$_SESSION['lang']['tanggal']."</td><td><input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td><td><td></tr></table>";
			echo"<button class=mybutton onclick=cariData()>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>
     </tr>
	 </table> "; 
?>
</div>
<?php
CLOSE_BOX(); //1 C //2 O
?>
<div id=list_pp_verication>
<?php OPEN_BOX();?>
<fieldset>
<legend><?php echo $_SESSION['lang']['list'];?></legend>
<div style="overflow:scroll; height:420px;">
	 <table class="sortable" cellspacing="1" border="0">
	 <thead>
        <tr class=rowheader>
        <td>No.</td>
        <td><?php echo $_SESSION['lang']['notransaksi'];?></td>
        <td><?php echo $_SESSION['lang']['tanggal'];?></td> 
        <td><?php echo $_SESSION['lang']['nopo']?></td>
        <td><?php echo $_SESSION['lang']['namaorganisasi'];?></td>
        <td>Action</td>
        </tr>
	 </thead>
	 <tbody id="contain">
	<script>loadData()</script>
	  </tbody>
	 <tfoot>
	 </tfoot>
	 </table></div>
</fieldset
><?php
CLOSE_BOX();
?>
</div>
<input type="hidden" name="method" id="method"  /> 
<input type="hidden" id="no_po" name="no_po" />
<input type="hidden" name="user_login" id="user_login" value="<?php echo $_SESSION['standard']['userid']?>" />

<?php
echo close_body();
?>