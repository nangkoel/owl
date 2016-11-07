<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('',"<b>".$_SESSION['lang']['timbanganpembeli']."</b>");
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script>
 jdl_ats_0='<?php echo $_SESSION['lang']['find']?>';
// alert(jdl_ats_0);
 jdl_ats_1='<?php echo $_SESSION['lang']['findBrg']?>';
 content_0='<fieldset><legend><?php echo $_SESSION['lang']['findnoBrg']?></legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div>';

nmSaveHeader='';
nmCancelHeader='';
nmDetialDone='<?php echo $_SESSION['lang']['done']?>';
nmDetailCancel='<?php echo $_SESSION['lang']['cancel']?>';

</script>
<script type="application/javascript" src="js/pabrik_timbangan_pembeli.js"></script>
<input type="hidden" id="proses" name="proses" value="insert"  />
<div id="headher">
<?php

for($i=0;$i<24;)
{
	if(strlen($i)<2)
	{
		$i="0".$i;
	}
   $jm.="<option value=".$i.">".$i."</option>";
   $i++;
}
for($i=0;$i<60;)
{
	if(strlen($i)<2)
	{
		$i="0".$i;
	}
   $mnt.="<option value=".$i.">".$i."</option>";
   $i++;
}
$optKary="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg="select karyawanid, namakaryawan from ".$dbname.".datakaryawan where tipekaryawan='0' and karyawanid!='".$_SESSION['standard']['userid']."' order by namakaryawan asc";
$qOrg=mysql_query($sOrg) or die(mysql_error());
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optKary.="<option value=".$rOrg['karyawanid'].">".$rOrg['namakaryawan']."</option>";
}
$optBrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sBrg="select kodebarang,namabarang from ".$dbname.".log_5masterbarang where kodebarang like '4%'";
$qBrg=mysql_query($sBrg) or die(mysql_error($conn));
while($rBrg=mysql_fetch_assoc($qBrg))
{
    $optBrg.="<option value='".$rBrg['kodebarang']."'>".$rBrg['namabarang']."</option>";
}  
$optJenis="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
?>
<fieldset style='width: 280px;'>
<legend><?php echo $_SESSION['lang']['form']?></legend>
<table cellspacing="1" border="0">

<tr>
<td><?php echo $_SESSION['lang']['namabarang']?></td>
<td>:</td>
<td><select id="kdBrg" name="kdBrg" style="width:150px" onchange="getCustomer(0,0,0)"><?php echo $optBrg;?></select></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['nmcust']?></td>
<td>:</td>
<td><select id="custId" name="custId" style="width:150px" onchange="getKontrak(0,0)"><?php echo $optJenis;?></select></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['NoKontrak']?></td>
<td>:</td>
<td><select id="noKontrak" style="width:150px"><?php echo $optJenis;?></select></td>
</tr>


<tr>
<td colspan="3" id="tmblHeader">
    <button class=mybutton id=dtlFormAtas onclick=getForm()>Preview</button>
   
</td>
</tr>
</table>
</fieldset>
    <div id="formInputan" style="display: none;">
        <fieldset>
            <legend><?php echo $_SESSION['lang']['list'] ?></legend>
            <div id="formTampil">
                
            </div>
        </fieldset>
    </div>
  
    </div>
<?php
CLOSE_BOX();
?>

<div id="list_ganti">
<?php OPEN_BOX()?>
    <div id="action_list">

</div>
    <?php
echo"<table>
     <tr valign=moiddle>
	 
	 <td><img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."' style='width:55px;cursor:pointer;' onclick=displayList()></td><td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
			echo $_SESSION['lang']['notransaksi'].":<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>";
                        echo $_SESSION['lang']['NoKontrak'].":<input type=text id=txtsearchKntrk size=25 maxlength=30 class=myinputtext>";
			echo $_SESSION['lang']['tanggal'].":<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />";
			echo"<button class=mybutton onclick=cariTransaksi()>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>
     </tr>
	 </table> "; 
?>
<fieldset style='float:left;'>
<legend><?php echo $_SESSION['lang']['list']?></legend>

<div id="contain">
<script>loadNData()</script>

</div>
</fieldset>
<?php CLOSE_BOX()?>
</div>

<?php 
echo close_body();
?>