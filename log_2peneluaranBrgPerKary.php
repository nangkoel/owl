<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
 
echo open_body();
?>

<script language=javascript src='js/log_2peneluaranBrgPerKary.js'></script>
<script language=javascript src='js/log_transaksi_pengeluaran.js'></script>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src='js/zReport.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>



<?php
$optorg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql="SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where length(kodeorganisasi)='4' ORDER BY kodeorganisasi";
//echo $sql;
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
{
	$optorg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
}
			
$optbulan="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql="SELECT distinct(substr(tanggal,1,7)) as tanggal FROM ".$dbname.".log_poht group by tanggal";
//echo $sql;
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
{
	$optbulan.="<option value=".$data['tanggal'].">".$data['tanggal']."</option>";
}			
$optKar="<option value=''>".$_SESSION['lang']['all']."</option>";			
$optKlmpkBrg.="<option value=''>".$_SESSION['lang']['all']."</option>";
$sKlbrg="select kode,kelompok from ".$dbname.".log_5klbarang order by kode asc";
$qKlbrg=mysql_query($sKlbrg) or die(mysql_error($conn));
while($rKlbrg=mysql_fetch_assoc($qKlbrg)){
	$optKlmpkBrg.="<option value='".$rKlbrg['kode']."'>".$rKlbrg['kelompok']."</option>";
}
$arrKeg=array('114010001'=>'114010001','114020001'=>'114020001','127030101'=>'127030101','127040201'=>'127040201','127050101'=>'127050101','711010401'=>'711010401','712070001'=>'712070001','712040001'=>'712040001');
$optKodeKeg.="<option value=''>".$_SESSION['lang']['all']."</option>";
foreach($arrKeg as $lstKeg =>$kdKeg){
	$whr="kodekegiatan='".$kdKeg."'";
	$optNmkeg=makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan',$whr);
	$optKodeKeg.="<option value='".$kdKeg."'>".$kdKeg."-".$optNmkeg[$kdKeg]."</option>";
}

//departemen
$optDept="<option value=''>".$_SESSION['lang']['all']."</option>";
$str="select * from ".$dbname.".sdm_5departemen";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
      $optDept.="<option value='".$bar->kode."'>".$bar->nama."</option>";	
}	
 
include('master_mainMenu.php');
OPEN_BOX();
$arr="##kdorg##kddept##karyawanid##klmpkBrg##kdKeg##tgl1##tgl2";	

echo "<fieldset style='float:left;'><legend><b>".$_SESSION['lang']['pengeluaranbarang']." ".$_SESSION['lang']['karyawan']."</b></legend>
<table>
	<tr>
		<td>".$_SESSION['lang']['kodeorg']."</td>
		<td>:</td>
		<td><select id=kdorg onchange=getKar() style='width:200px;'>".$optorg."</select></td>
	</tr>
	
	<tr>
		<td>".$_SESSION['lang']['departemen']."</td>
		<td>:</td>
		<td><select id=kddept onchange=getKar() style='width:200px;'>".$optDept."</select></td>
	</tr>
	
	<tr>
		<td>".$_SESSION['lang']['namakaryawan']."</td>
		<td>:</td>
		<td><select id=karyawanid style='width:200px;'>".$optKar."</select></td>
	</tr>
	<tr>
		<td>".$_SESSION['lang']['kelompokbarang']."</td>
		<td>:</td>
		<td><select id=klmpkBrg style='width:200px;'>".$optKlmpkBrg."</select></td>
	</tr>
	<tr>
		<td>".$_SESSION['lang']['kodekegiatan']."</td>
		<td>:</td>
		<td><select id=kdKeg style='width:200px;'>".$optKodeKeg."</select></td>
	</tr>

	<tr>
		<td>".$_SESSION['lang']['tanggal']."</td>
		<td>:</td>
		<td><input type='text' class='myinputtext' id='tgl1' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='7' maxlength='10' >
		s/d
		<input type='text' class='myinputtext' id='tgl2' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='7' maxlength='10' ></td>
	</tr>	
	<tr>
		<td colspan=100>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=100>
		<button onclick=zPreview('log_slave_2peneluaranBrgPerKary','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
		<button onclick=zExcel(event,'log_slave_2peneluaranBrgPerKary.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
		
		<button onclick=batal() class=mybutton name=btnBatal id=btnBatal>".$_SESSION['lang']['cancel']."</button>
		</td>
	</tr>
</table>
</fieldset>";//<button onclick=zPdf('pabrik_slave_2qc','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['pdf']."</button>

echo "
<fieldset style='clear:both'><legend><b>".$_SESSION['lang']['printArea']."</b></legend>
<div id='printContainer'  >
</div></fieldset>";//style='overflow:auto;height:350px;max-width:1500px';

CLOSE_BOX();
echo close_body();




?>