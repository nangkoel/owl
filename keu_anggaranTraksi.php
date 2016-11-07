<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zFunction.php');
echo open_body();
include('master_mainMenu.php');
$lksiTugas=substr($_SESSION['empl']['lokasitugas'],0,4);
$sOrg="select kodeorganisasi,induk from ".$dbname.".organisasi where tipe='TRAKSI'";
$qOrg=mysql_query($sOrg) or die(mysql_error());
while($rOrg=mysql_fetch_assoc($qOrg))
{
	if($rOrg['induk']!=$lksiTugas)
	{
		echo"warning:You Are Not In Traksi";
		exit();
	}
}

$frm[0]='';
$frm[1]='';
$frm[2]='';


?>

<script type="text/javascript" src="js/keu_anggaranTraksi.js"></script>
<script type="text/javascript" src="js/zMaster.js"></script>
<script>
tmblSave='<?php echo $_SESSION['lang']['save'];?>';
tmblCancel='<?php echo $_SESSION['lang']['cancel'];?>';
tmblDone='<?php  echo $_SESSION['lang']['done']?>';
</script>
<?php
$sVhc="select kodevhc from ".$dbname.".vhc_5master order by tahunperolehan desc";
$qVHc=mysql_query($sVhc) or die(mysql_error());
while($rVhc=mysql_fetch_assoc($qVHc))
{
	$optVhc.="<option value=".$rVhc['kodevhc'].">".$rVhc['kodevhc']."</option>";
}


//----tab pertama...kondisi rmh 0, sedang dalam perbaikan, 1 kondisi baik, 2 kondisi rusak
$isiOpt=array($_SESSION['lang']['dlm_perbaikan_rmh'],$_SESSION['lang']['dlm_baik_rmh'],$_SESSION['lang']['dlm_rusak_rmh']);
foreach($isiOpt as $num => $teks)
{
	$optKondisi.="<option value=".$num.">".$teks."</option>";
}
$optThn='';
$thn_skrng=intval(date("Y"));
for($i=$thn_skrng;$i>=($thn_skrng-30);$i--)
{
	//echo "warning".$i;exit();
	$optThn.="<option value='".$i."'>".$i."</option>";
}
OPEN_BOX('',"<b>".$_SESSION['lang']['anggaranTraksi']."</b><br>");
$frm[0].="<fieldset><legend>".$_SESSION['lang']['header']."</legend>";
$frm[0].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['tahunanggaran']."</td><td>:</td><td>
<input type=text id=thnAnggaran name=thnAnggaran class=myinputtextnumber style=width:150px; onkeypress=\"return angka_doang(event);\" maxlength=4 /></td></tr>
<tr><td>".$_SESSION['lang']['kodevhc']."</td><td>:</td><td><select id=kdvhc name=kdvhc style=width:150px;>".$optVhc."</select></td></tr>
<tr><td>".$_SESSION['lang']['jmlhHariOperasi']."</td><td>:</td><td>
<input type=text id=jmlhHari name=jmlhHari class=myinputtextnumber style=width:150px; onkeypress=\"return angka_doang(event);\" maxlength=4 /></td></tr>
<tr><td>".$_SESSION['lang']['pemakaianHmKm']."</td><td>:</td><td>
<input type=text class=myinputtextnumber id=pemakaianHm name=pemakaianHm maxlength=4 onkeypress=\"return angka_doang(event);\" style=width:150px; /></td></tr>
<tr><td>".$_SESSION['lang']['jmlhHariTdkOpr']."</td><td>:</td><td>
<input type=text class=myinputtextnumber id=jmlhHariTdk name=tipe_rmh maxlength=8 onkeypress=\"return angka_doang(event);\" style=width:150px; /></td></tr>

<tr><td colspan=3 id=tmbLhead><script>shwTmbl()</script></td></tr>
</table><input type=hidden id=proses name=proses value='insert' />";
$frm[0].="</fieldset>";
$frm[0].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>
         <table class=sortable cellspacing=1 border=0>
		<thead>
		<tr class=\"rowheader\">
		<td>No.</td>
		<td>".$_SESSION['lang']['tahunanggaran']."</td>
		<td>".$_SESSION['lang']['kodevhc']."</td>
		<td>".$_SESSION['lang']['jmlhHariOperasi']."</td>
		<td>".$_SESSION['lang']['pemakaianHmKm']."</td>
		<td>".$_SESSION['lang']['jmlhHariTdkOpr']."</td>
		<td>Action</td>
		</tr></thead><tbody id=contain>
		<script>loadData()</script>
		";
$frm[0].="</tbody></table></fieldset>";

//assseettt
$optBrg='';
$sBrg="select kodebarang,namabarang from ".$dbname.".log_5masterbarang order by namabarang asc";
$qBrg=mysql_query($sBrg) or die(mysql_error());
while($rBrg=mysql_fetch_assoc($qBrg))
{
	$optBrg.="<option value=".$rBrg['kodebarang'].">".$rBrg['namabarang']."</option>";
}

$frm[1].="<fieldset><legend>".$_SESSION['lang']['entryForm']."</legend>";
$frm[1].="<table class=sortable cellspacing=1 border=0>
		<thead>
		<tr class=\"rowheader\">
		<td>".$_SESSION['lang']['namabarang']."</td>
		<td>".$_SESSION['lang']['jumlah']."</td>
		<td></td>
		</tr></thead>
		<tbody><tr class=rowcontent>
		<td><select id=kdBrg name=kdBrg style='width:150px'>".$optBrg."</select><input type=hidden id=oldKdbrg name=oldKdbrg /></td>
		<td><input type=text class=myinputtextnumber id=jmlh name=jmlh maxlength=4 value='0' onkeypress=\"return angka_doang(event);\" style='width:150px'/></td>
		<td><button class=mybutton onclick=saveDetail() >".$_SESSION['lang']['save']."</button><button class=mybutton onclick=clearDetail() >".$_SESSION['lang']['cancel']."</button></td>
</tr></tbody></table>";

$frm[1].="</fieldset>";
$frm[1].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>
        <table class=sortable cellspacing=1 border=0>
		<thead>
		<tr class=\"rowheader\">
		<td>No.</td>
		<td>".$_SESSION['lang']['namabarang']."</td>
		<td>".$_SESSION['lang']['jumlah']."</td>
		<td>Action</td>
		</tr></thead><tbody id=containDetailTraksi>
		<script>loadDetail();</script>		";
$frm[1].="</tbody></table></fieldset><input type=hidden id=pros name=pros value=insertDetail />";

//karyawan
$optOrg='';
$sOrg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where length(kodeorganisasi)=4";//echo $skary;
$qOrg=mysql_query($sOrg) or die(mysql_error());
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}
$frm[2].="<fieldset><legend>".$_SESSION['lang']['entryForm']."</legend>";
$frm[2].="<table class=sortable cellspacing=1 border=0>
		<thead>
		<tr class=\"rowheader\">
		<td>".$_SESSION['lang']['kodeorg']."</td>
		<td>".$_SESSION['lang']['jmlhMeter']."</td>
		<td>Jan</td>
		<td>Feb</td>
		<td>Mar</td>
		<td>Apr</td>
		<td>Mei</td>
		<td>Jun</td>
		<td>Jul</td>
		<td>Aug</td>
		<td>Sep</td>
		<td>Okt</td>
		<td>Nov</td>
		<td>Des</td>
		<td></td>
		</tr></thead>
		<tbody>
		<td><select id=kdOrg name=kdOrg>".$optOrg."</select></td>
		<td><input type=text class=myinputtextnumber id=jmlhMeter name=jmlhMeter  onkeypress=\"return angka_doang(event);\" value='0' /> </td>
		<td><input type=text class=myinputtextnumber id=jmlhJan name=jmlhJan  onkeypress=\"return angka_doang(event);\" value='0' style='width:30px' maxlength=4 /> </td>
		<td><input type=text class=myinputtextnumber id=jmlhFeb name=jmlhFeb  onkeypress=\"return angka_doang(event);\" value='0' style='width:30px' maxlength=4 /></td>
		<td><input type=text class=myinputtextnumber id=jmlhMar name=jmlhMar  onkeypress=\"return angka_doang(event);\" value='0' style='width:30px' maxlength=4 /></td>
		<td><input type=text class=myinputtextnumber id=jmlhApr name=jmlhApr  onkeypress=\"return angka_doang(event);\" value='0' style='width:30px' maxlength=4 /></td>
		<td><input type=text class=myinputtextnumber id=jmlhMei name=jmlhMei  onkeypress=\"return angka_doang(event);\" value='0' style='width:30px' maxlength=4 /></td>
		<td><input type=text class=myinputtextnumber id=jmlhJun name=jmlhJun  onkeypress=\"return angka_doang(event);\" value='0' style='width:30px' maxlength=4 /></td>
		<td><input type=text class=myinputtextnumber id=jmlhJul name=jmlhJul  onkeypress=\"return angka_doang(event);\" value='0' style='width:30px' maxlength=4 /></td>
		<td><input type=text class=myinputtextnumber id=jmlhAug name=jmlhAug  onkeypress=\"return angka_doang(event);\" value='0' style='width:30px' maxlength=4 /></td>
		<td><input type=text class=myinputtextnumber id=jmlhSep name=jmlhSep  onkeypress=\"return angka_doang(event);\" value='0' style='width:30px' maxlength=4 /></td>
		<td><input type=text class=myinputtextnumber id=jmlhOkt name=jmlhOkt  onkeypress=\"return angka_doang(event);\" value='0' style='width:30px' maxlength=4 /></td>
		<td><input type=text class=myinputtextnumber id=jmlhNov name=jmlhNov  onkeypress=\"return angka_doang(event);\" value='0' style='width:30px' maxlength=4 /></td>
		<td><input type=text class=myinputtextnumber id=jmlhDes name=jmlhDes  onkeypress=\"return angka_doang(event);\" value='0' style='width:30px' maxlength=4 /></td>
		<td><button class=mybutton onclick=saveAlokasi() >".$_SESSION['lang']['save']."</button><button class=mybutton onclick=clearAlokasi() >".$_SESSION['lang']['cancel']."</button></td>
		</tbody></table>";

$frm[2].="</fieldset>";
$frm[2].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>
      <table class=sortable cellspacing=1 border=0>
		<thead>
		<tr class=\"rowheader\">
		<td>No.</td>
		<td>".$_SESSION['lang']['kodeorg']."</td>
		<td>".$_SESSION['lang']['jmlhMeter']."</td>
		<td>Jan</td>
		<td>Feb</td>
		<td>Mar</td>
		<td>Apr</td>
		<td>Mei</td>
		<td>Jun</td>
		<td>Jul</td>
		<td>Aug</td>
		<td>Sep</td>
		<td>Okt</td>
		<td>Nov</td>
		<td>Des</td>
		<td>Action</td>
		</tr></thead><tbody id=containAlokasi>
		<script>loadaLokasi();</script>
		";
$frm[2].="</tbody></table></fieldset><input type=hidden id=prosAlokasi name=prosAlokasi value=insertAlokasi />";

//========================
$hfrm[0]=$_SESSION['lang']['header'];
$hfrm[1]=$_SESSION['lang']['anggaranTraksiDetail'];
$hfrm[2]=$_SESSION['lang']['anggaranTraksiAlokasi'];
//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,220,930);
//===============================================	
?>

<?php
CLOSE_BOX();
echo close_body();
?>