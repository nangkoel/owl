<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('',"<b>".$_SESSION['lang']['pemeliharaanMesin']."</b>");
//echo"<pre>";
//print_r($_SESSION);
//echo"</pre>";

$namaKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');

?>



<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script>
 jdl_ats_0='<?php echo $_SESSION['lang']['find']?>';
// alert(jdl_ats_0);
 jdl_ats_1='<?php echo $_SESSION['lang']['findBrg']?>';
 content_0='<fieldset><legend><?php echo $_SESSION['lang']['findnoBrg']?></legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div>';

nmSaveHeader='<?php echo $_SESSION['lang']['save']?>';
nmCancelHeader='<?php echo $_SESSION['lang']['cancel']?>';
nmDetialDone='<?php echo $_SESSION['lang']['done']?>';
nmDetailCancel='<?php echo $_SESSION['lang']['cancel']?>';

</script>
<script type="application/javascript" src="js/pabik_pemeliharaanmesin.js"></script>
<input type="hidden" id="proses" name="proses" value="insert"  />
<div id="action_list">
<?php

//print_r($_SESSION['empl']['kodejabatan']);

echo"<table>
     <tr valign=moiddle>
	 <td align=center style='width:100px;cursor:pointer;' onclick=add_new_data()>
	   <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
	 <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
			echo $_SESSION['lang']['notransaksi'].":<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>";
			echo $_SESSION['lang']['tanggal'].":<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />";
			echo"<button class=mybutton onclick=cariTransaksi()>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>
     </tr>
	 </table> "; 
?>
</div>
<?php
CLOSE_BOX();
?>
<div id="list_ganti">
<?php OPEN_BOX()?>
<fieldset>
<legend><?php echo $_SESSION['lang']['list']?></legend>
<table cellspacing="1" border="0" class="sortable">
<thead>
<tr class="rowheader">
<td>No.</td>
<td><?php echo $_SESSION['lang']['notransaksi']?></td>
<td><?php echo $_SESSION['lang']['tanggal']?></td>
<td><?php echo $_SESSION['lang']['shift']?></td>
<td><?php echo $_SESSION['lang']['statasiun']?></td>
<td><?php echo $_SESSION['lang']['mesin']?></td>
<td><?php echo $_SESSION['lang']['dari']?></td>
<td><?php echo $_SESSION['lang']['sampai']?></td>
<td>Update By</td>
<td>Action</td>
</tr>
</thead>
<tbody id="contain">
<?php
	//$lokasi=$_SESSION['empl']['lokasitugas'];
        $userOnline=$_SESSION['standard']['userid'];
        $userName=$_SESSION['standard']['username'];

	$limit=25;
	$page=0;
	if(isset($_POST['page']))
	{
	$page=$_POST['page'];
	if($page<0)
	$page=0;
	}
	$offset=$page*$limit;
	
	$ql2="select count(*) as jmlhrow from ".$dbname.".pabrik_rawatmesinht   order by `tanggal` desc";// echo $ql2;
	$query2=mysql_query($ql2) or die(mysql_error());
	while($jsl=mysql_fetch_object($query2)){
	$jlhbrs= $jsl->jmlhrow;
	}


$slvhc="select * from ".$dbname.".pabrik_rawatmesinht   order by `tanggal` desc limit ".$offset.",".$limit." ";
$qlvhc=mysql_query($slvhc) or die(mysql_error());
$user_online=$_SESSION['standard']['userid'];
while($rlvhc=mysql_fetch_assoc($qlvhc))
{
    $no+=1;
    $dtJamMulai=explode(" ",$rlvhc['jammulai']);
    $jamMulai=explode(":",$dtJamMulai[1]);

    $dtJamSlsi=explode(" ",$rlvhc['jamselesai']);
    $jamSlsi=explode(":",$dtJamSlsi[1]);
    ?>
    <tr class="rowcontent">
    <td><?php echo $no?></td>
    <td><?php echo $rlvhc['notransaksi']?></td>
    <td><?php echo tanggalnormal($rlvhc['tanggal'])?></td>
    <td><?php echo $rlvhc['shift']?></td>
    <td><?php echo $rlvhc['statasiun']?></td>
    <td><?php echo $rlvhc['mesin']?></td>
    <td><?php echo tanggalnormald($rlvhc['jammulai'])?></td>
    <td><?php echo tanggalnormald($rlvhc['jamselesai'])?></td>
    <td><?php echo $namaKar[$rlvhc['updateby']]?></td><td>

    <?php 
       if($rlvhc['updateby']==$userOnline)
    {
    echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['notransaksi']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['pabrik']."','".$rlvhc['shift']."','".$rlvhc['statasiun']."','".$rlvhc['mesin']."','".$rlvhc['kegiatan']."','".tanggalnormal($dtJamMulai[0])."','".tanggalnormal($dtJamSlsi[0])."','".$jamMulai[0]."','".$jamMulai[1]."','".$jamSlsi[0]."','".$jamSlsi[1]."','".$rlvhc['keterangan']."');\">
        <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['notransaksi']."');\" >
        <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('pabrik_rawatmesinht','".$rlvhc['notransaksi']."','','pabrik_slavePemeliharaanPdf',event);\">";
     } else {
             echo" <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('pabrik_rawatmesinht','".$rlvhc['notransaksi']."','','pabrik_slavePemeliharaanPdf',event)\">";}
 
 
		
 
 }
?>
</td></tr>

<?php 
echo"
	<tr class=rowheader><td colspan=9 align=center>
	".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
	<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
	<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
	</td>
	</tr>";
?>

</tbody>
</table>
</fieldset>
<?php CLOSE_BOX()?>
</div>



<div id="headher" style="display:none">
<?php
OPEN_BOX();
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
$sOrg="select kodeorganisasi, namaorganisasi from ".$dbname.".organisasi where tipe='PABRIK'";
$qOrg=mysql_query($sOrg) or die(mysql_error());
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optPabrik.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}
?>
<fieldset>
<legend><?php echo $_SESSION['lang']['header']?></legend>
<table cellspacing="1"  border="0">

<tr>
<td><?php echo $_SESSION['lang']['pabrik']?></td>
<td>:</td>
<td><input type="hidden" id="trans_no" name="trans_no" class="myinputtext" style="width:120px;" />
<select id="pbrkId" name="pbrkId" style="width:150px" onchange="getStation(0,0,0)"><option value=""></option><?php echo $optPabrik;?></select></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['shift']?></td>
<td>:</td>
<td><select id="shitId" name="shitId" style="width:150px"></select></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['statasiun']?></td>
<td>:</td>
<td><select id="statId" name="statId" style="width:150px" onchange="getMesin(0,0)"></select></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['mesin']?></td>
<td>:</td>
<td><select id="msnId" name="msnId" style="width:150px"></select></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['kegiatan']?></td>
<td>:</td>
<td>
<input type="text" class="myinputtext" id="kegtn" name="kegtn" onkeypress="return tanpa_kutip();" maxlength="50" style="width:150px;" /></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['tanggal']?></td>
<td>:</td>
<td>
<input type="text" class="myinputtext" id="tglCek" name="tglCek" onmousemove="setCalendar(this.id)" onkeypress="return false;"  size="10" maxlength="10" style="width:150px;" /></td>
</tr>

<tr>
<td><?php echo $_SESSION['lang']['jammulai']?></td>
<td>:</td>
<td><input type="text" class="myinputtext" id="jmAwal" name="jmAwal" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" />
    <select id="jamMulai"><?php echo $jm;?></select>:<select id="mntMulai"><?php echo $mnt;?></select>
</td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['jamselesai']?></td>
<td>:</td>
<td><input type="text" class="myinputtext" id="jmAkhir" name="jmAkhir" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" />
     <select id="jamSlsi"><?php echo $jm;?></select>:<select id="mntSlsi"><?php echo $mnt;?></select>
</td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['keterangan']?></td>
<td>:</td>
<td>
<textarea id="keterangan" id="keterangan" onkeypress="return tanpa_kutip();" rows="4"/></textarea></td>
</tr>
<tr>
<td colspan="3" id="tmblHeader">
</td>
</tr>
</table>
</fieldset>

<?php
CLOSE_BOX();
?>
</div>
<div id="detail_ganti" style="display:none">
<?php 
OPEN_BOX();
?>
<div id="addRow_table">
<div id="detail_isi">
</div>
<div id="tmblDetail">

</div>
</div>
<?php
CLOSE_BOX();
?>
</div>
<?php 
echo close_body();
?>