<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('',"<b>".$_SESSION['lang']['pengirimanBibit']."</b>");
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script>
jdlExcel='<?php echo $_SESSION['lang']['pengirimanBibit']?>';

tmblDone='<?php echo $_SESSION['lang']['done']?>';
tmblCancelDetail='<?php echo $_SESSION['lang']['cancel']?>';
</script>
<script type="application/javascript" src="js/kebun_pengirimanBibit.js"></script>
<input type="hidden" id="proses" name="proses" value="insert"  />
<div id="action_list">
<?php
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
<script>loadData();</script>
</div>



<div id="headher" style="display:none">
<?php
OPEN_BOX();
$svhc="select kodevhc,jenisvhc,tahunperolehan from ".$dbname.".vhc_5master  order by kodevhc"; //echo $svhc;
$qvhc=mysql_query($svhc) or die(mysql_error());
while($rvhc=mysql_fetch_assoc($qvhc))
{
	$optVhc.="<option value=".$rvhc['kodevhc'].">".$rvhc['kodevhc']."[".$rvhc['tahunperolehan']."]</option>";
}
$sOrg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='BIBITAN' order by namaorganisasi asc"; //echo $sOrg;
$qOrg=mysql_query($sOrg) or die(mysql_error());
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}
$sOrg2="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe IN ('KEBUN','AFDELING') order by namaorganisasi asc"; //echo $sOrg2;
$qOrg2=mysql_query($sOrg2) or die(mysql_error());
while($rOrg2=mysql_fetch_assoc($qOrg2))
{
	$optOrg2.="<option value=".$rOrg2['kodeorganisasi'].">".$rOrg2['namaorganisasi']."</option>";
}
$sCust="select kodecustomer,namacustomer  from ".$dbname.".pmn_4customer order by namacustomer";
$qCust=mysql_query($sCust) or die(mysql_error($sCust));
while($rCust=mysql_fetch_assoc($qCust))
{
	$optCust.="<option value=".$rCust['kodecustomer']." >".$rCust['namacustomer']."</option>";
}	
$sKeg="select kodekegiatan,namakegiatan,kelompok,noakun from ".$dbname.".setup_kegiatan order by noakun asc" ;
$qKeg=mysql_query($sKeg) or die(mysql_error());
while($rKeg=mysql_fetch_assoc($qKeg))
{
	$optKeg.="<option value=".$rKeg['kodekegiatan']." >".$rKeg['noakun']." [".$rKeg['kelompok']."] [".$rKeg['namakegiatan']."]</option>";
}
$sBibit="select jenisbibit  from ".$dbname.".setup_jenisbibit order by jenisbibit  asc" ;
$qBibit=mysql_query($sBibit) or die(mysql_error());
while($rBibit=mysql_fetch_assoc($qBibit))
{
	$optBibit.="<option value=".$rBibit['jenisbibit']." >".$rBibit['jenisbibit']."</option>";
}

?>
<fieldset>
<legend><?php echo $_SESSION['lang']['entryForm']?></legend>
<table cellspacing="1" border="0">
<tr>
<td><?php echo $_SESSION['lang']['kodeorg']?></td>
<td>:</td>
<td><select id="codeOrg" name="codeOrg" style="width:150px;" onchange="getNotrans()"><option value=""></option><?php echo $optOrg;?></select></td>
</tr>

<tr>
<td><?php echo $_SESSION['lang']['notransaksi']?></td>
<td>:</td>
<td><input type="text"  id="trans_no" name="trans_no" class="myinputtext" style="width:150px;" /></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['tanggal']?></td>
<td>:</td>
<td><input type="text" class="myinputtext" id="tgl" name="tgl" onmousemove="setCalendar(this.id)" onkeypress="return false;"  size="10" maxlength="10" style="width:150px;" /></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['jenisbibit']?></td>
<td>:</td>
<td><select id="jnsBibit" name="jnsBibit" style="width:150px;" ><option value=""></option><?php echo $optBibit;?></select></td>
</tr>

<tr>
<td><?php echo $_SESSION['lang']['jumlah']?></td>
<td>:</td>
<td><input type="text" class="myinputtextnumber" id="jmlh" name="jmlh" onkeypress="return angka_doang(event);"  value="0"  maxlength="10" style="width:150px;" /></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['OrgTujuan']?></td>
<td>:</td>
<td><select id="OrgTujuan" name="OrgTujuan" style="width:150px;" onChange="knciForm()"  ><option value=""></option><?php echo $optOrg2;?></select></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['nmcust']?></td>
<td>:</td>
<td><select id="custId" name="custId" style="width:150px;" onChange="knciForm()"  ><option value=""></option><?php echo $optCust;?></select></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['namakegiatan']?></td>
<td>:</td>
<td><select id="kegCode" name="kegCode" style="width:150px;" ><option value=""></option><?php echo $optKeg;?></select></td>
</tr>


<tr>
<td colspan="3" id="tmblHeader">
<button class=mybutton id='dtl_pem' onclick='saveData()'><?php echo $_SESSION['lang']['save']?></button><button class=mybutton id='cancel_gti' onclick='cancelSave()'><?php echo $_SESSION['lang']['cancel']?></button>
</td>
</tr>
</table>
</fieldset>

<?php
CLOSE_BOX();
?>
</div>

<?php 
echo close_body();
?>