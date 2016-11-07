<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('',"<b>".$_SESSION['lang']['penggantianKomponen']."</b>");
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script>
 jdl_ats_0='<?php echo $_SESSION['lang']['find']?>';
// alert(jdl_ats_0);
 jdl_ats_1='<?php echo $_SESSION['lang']['findBrg']?>';
 content_0='<fieldset><legend><?php echo $_SESSION['lang']['findnoBrg']?></legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div>';

tmblNew='<?php echo $_SESSION['lang']['new']?>';

tmblSave='<?php echo $_SESSION['lang']['save']?>';
tmblCancel='<?php echo $_SESSION['lang']['cancel']?>';
tmblDone='<?php echo $_SESSION['lang']['done']?>';
tmblCancelDetail='<?php echo $_SESSION['lang']['cancel']?>';
</script>
<script type="application/javascript" src="js/vhc_penggantianKomponen.js"></script>
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
<script>load_new_data();</script>
</div>



<div id="headher" style="display:none">
<?php
OPEN_BOX();
$svhc="select kodevhc,jenisvhc,tahunperolehan from ".$dbname.".vhc_5master  order by kodevhc"; //echo $svhc;
$qvhc=mysql_query($svhc) or die(mysql_error());
while($rvhc=mysql_fetch_assoc($qvhc))
{
        $optVhc.="<option value='".$rvhc['kodevhc']."'>".$rvhc['kodevhc']."[".$rvhc['tahunperolehan']."]</option>";
}


$svhc2="select kodeorg from ".$dbname.".vhc_5master group by kodeorg"; //echo $svhc;
$qvhc2=mysql_query($svhc2) or die(mysql_error());
while($rvhc2=mysql_fetch_assoc($qvhc2))
{
        $optOrg.="<option value='".$rvhc2['kodeorg']."'>".$rvhc2['kodeorg']."</option>";
}

##untuk jam dan menit option			
for($t=0;$t<24;)
{
	if(strlen($t)<2)
	{
		$t="0".$t;
	}
	$jm.="<option value=".$t." ".($t==00?'selected':'').">".$t."</option>";
	$t++;
}
for($y=0;$y<60;)
{
	if(strlen($y)<2)
	{
		$y="0".$y;
	}
	$mnt.="<option value=".$y." ".($y==00?'selected':'').">".$y."</option>";
	$y++;
}


$optKar="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$i="select karyawanid,namakaryawan,nik,kodejabatan from ".$dbname.".datakaryawan where 
	lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."' and kodeunit like '%RO%')
	and tipekaryawan!='0' and kodejabatan in (select kodejabatan from ".$dbname.".sdm_5jabatan where namajabatan like '%mekanik%' or namajabatan like '%welder%') ";
//echo $i;	
$n=mysql_query($i) or die (mysql_error($conn));
while($d=mysql_fetch_assoc($n))
{
	$optKar.="<option value='".$d['karyawanid']."'>".$d['nik']." - ".$d['namakaryawan']."</option>";
	
}




?>

<?php
echo"<fieldset>
<legend>".$_SESSION['lang']['header']."</legend>
<table cellspacing=1 border=0>
<tr>
	<td>".$_SESSION['lang']['unit']."</td>
	<td>:</td>
	<td><select id=codeOrg name=codeOrg style=width:150px; onchange=getNotrans(0)><option value=''></option>".$optOrg."</select></td>
</tr>
<tr>
	<td>".$_SESSION['lang']['notransaksi']."</td>
	<td>:</td>
	<td><input type=text  id=trans_no name=trans_no class=myinputtext style=width:150px; /></td>
</tr>
<tr>
	<td>".$_SESSION['lang']['kodevhc']."</td>
	<td>:</td>
	<td><select id=vhc_code name=vhc_code style=width:150px;>".$optVhc."</select></td>
</tr>
<tr>
	<td>".$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['perbaikan']."</td>
	<td>:</td>
	<td><input type=text class=myinputtext id=tgl_ganti name=tgl_ganti onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=width:150px; /></td>
</tr>


<tr>
	<td>".$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['masuk']."</td>
	<td>:</td>
	<td><input type=text class=myinputtext id=tglMasuk name=tglMasuk onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=width:150px; /></td>
</tr>
<tr>
	<td>".$_SESSION['lang']['jam']." ".$_SESSION['lang']['masuk']."</td> 
	<td>:</td>
	<td>
		<select id=jm1 name=jmId >".$jm."</select>:<select id=mn1>".$mnt."</select>
	</td>
</tr>
<tr>
	<td>".$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['selesai']."</td>
	<td>:</td>
	<td><input type=text class=myinputtext id=tglSelesai name=tglSelesai onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=width:150px; /></td>
</tr>
<tr>
	<td>".$_SESSION['lang']['jamselesai']."</td> 
	<td>:</td>
	
	<td>		
		<select id=jm2 name=jmId2 >".$jm."</select>:<select id=mn2>".$mnt."</select>
	</td>
</tr>
<tr>
	<td>".$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['diambil']."</td>
	<td>:</td>
	<td><input type=text class=myinputtext id=tglAmbil name=tglAmbil onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=width:150px; /></td>
</tr>


<tr>
	<td>".$_SESSION['lang']['downtime']."</td>
	<td>:</td>
	<td><input type=text class=myinputtextnumber id=dwnTime name=dwnTime onkeypress=return angka_doang(event);  value=0  maxlength=10 style=width:150px; />".$_SESSION['lang']['jmlhJam']."</td>
</tr>
<tr>
	<td>".$_SESSION['lang']['descDamage']."</td>
	<td>:</td>
	
	<td><textarea id=descDmg cols=30 rows=5   onkeypress=\"return tanpa_kutip(event);\" ></textarea></td>
</tr>







<tr>
	<td>".$_SESSION['lang']['kmhmmasuk']."</td>
	<td>:</td>
	<td><input type=text class=myinputtextnumber id=kmhmMasuk name=kmhmMasuk onkeypress=return angka_doang(event);  value=0  maxlength=10 style=width:150px; /></td>
</tr>
<tr>
	<td>".$_SESSION['lang']['namamekanik']."</td>
	<td>:</td>
	<td><img src=images/zoom.png title='".$_SESSION['lang']['find']."' id=tambahMekanik class=resicon onclick=mekanik('".$_SESSION['lang']['find']."',event)><= Input mekanik click here</td> 
</tr>
<tr>
	<td>".$_SESSION['lang']['notransaksi']." ".$_SESSION['lang']['gudang']."</td>
	<td>:</td>
	<td><input type=text id=noTranGudang disabled class=myinputtext size=25 maxength=25 onkeypress=\"return tanpa_kutip(event);\">
	    <img src=images/zoom.png title='".$_SESSION['lang']['find']."' id=tmblCariNoGudang class=resicon onclick=cariNoGudang('".$_SESSION['lang']['find']."',event)>
	 </td>
</tr>






<tr>
<td colspan=3 id=tmblHeader>
</td>
</tr>
</table>
</fieldset>";
?>

<?php
CLOSE_BOX();
?>
</div>
<div id="detail_ganti" style="display:none">
<?php 
OPEN_BOX();
?>
<div id="addRow_table">
<table cellspacing="1" border="0">
<tbody id="detail_isi">
<?php echo "<b>".$_SESSION['lang']['notransaksi']."</b> : <input type=\"text\" id='detail_kode' name='detail_kode' disabled=\"disabled\" style=\"width:150px\" />";?>
<table id="ppDetailTable" >
</table>
</tbody>
<tr><td>
<div  id="tmblDetail">
</div>
</td></tr>
</table>
</div>
<?php
CLOSE_BOX();
?>
</div>
<?php 
echo close_body();
?>