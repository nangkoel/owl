<?php //@Copy nangkoelframework
//-----------------ind
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();


?>

<script language=javascript src=js/zTools.js></script>
<script language=javascript src='js/zReport.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>

<script language=javascript>
	function batal()
	{
		location.reload();	
	}
</script>

<?php
//$optorg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{
	$sql="SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where length(kodeorganisasi)=4 ORDER BY kodeorganisasi";
}
else if($_SESSION['empl']['tipelokasitugas']=='KANWIL')
{
	$sql="SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where kodeorganisasi in (select kodeunit from ".$dbname.".bgt_regional_assignment
		 where regional='".$_SESSION['empl']['regional']."') and tipe!='HOLDING' ";
}
else
{
	$sql="SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
}
//echo $sql;
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
				$optorg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
			}
			
$sql="SELECT karyawanid,namakaryawan FROM ".$dbname.".datakaryawan where bagian in ('FIN','ACC') and lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
//echo $sql;
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
				$optKar.="<option value=".$data['karyawanid'].">".$data['namakaryawan']."</option>";
			}			
			
/*$optTipe="<option value='1180400'>Operasional Karyawan</option>";
$optTipe.="<option value='1180300'>Perjalanan Dinas</option>";
$optTipe.="<option value='1180100'>Pembelian Barang</option>";
$optTipe.="<option value='2130500'>Pengobatan Karyawan</option>";

$optTipe.="<option value='1180100'>Pembelian Barang</option>";
$optTipe.="<option value='2130500'>Pengobatan Karyawan</option>";*/



$a="SELECT noakun, namaakun
FROM ".$dbname.".keu_5akun
WHERE LEFT( noakun, 4 ) 
IN (
'1180',  '1140'
)
AND detail =1";
$b=mysql_query($a) ;
while($c=mysql_fetch_assoc($b))
{
	$optTipe.="<option value=".$c['noakun'].">".$c['namaakun']."</option>";

}





			
?>






<?php




include('master_mainMenu.php');
OPEN_BOX();
$arr="##kdorg##noakun##tgl##dibuat##diperiksa";	

echo "<fieldset style='float:left;'><legend><b>Laporan Aging Schedule Pinjaman</b></legend>
<table>
	<tr>
		<td>".$_SESSION['lang']['kodeorg']."</td>
		<td>:</td>
		<td><select id=kdorg style='width:200px;'>".$optorg."</select></td>
	</tr>
	<tr>
		<td>".$_SESSION['lang']['tipe']." Uang Muka</td>
		<td>:</td>
		<td><select id=noakun style='width:200px;'>".$optTipe."</select></td>
	</tr>
	<tr>
		<td>Sampai ".$_SESSION['lang']['tanggal']."</td>
		<td>:</td>
		<td><input type='text' class='myinputtext' id='tgl' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='7' maxlength='10' >
		</td>
	</tr>
	<tr>
		<td>".$_SESSION['lang']['dibuat']."</td>
		<td>:</td>
		<td><select id=dibuat style='width:200px;'>".$optKar."</select></td>
	</tr>
	<tr>
		<td>".$_SESSION['lang']['diperiksa']."</td>
		<td>:</td>
		<td><select id=diperiksa style='width:200px;'>".$optKar."</select></td>
	</tr>	
	
	<tr>
		<td colspan=100>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=100>
		<button onclick=zPreview('keu_slave_2agingPinjaman','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
		<button onclick=zExcel(event,'keu_slave_2agingPinjaman.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
		
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