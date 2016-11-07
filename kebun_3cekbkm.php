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
<script language=javascript src='js/kebun_3cekbkm.js'></script>


<?php
$optPer="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql="SELECT distinct periode FROM ".$dbname.".sdm_5periodegaji limit 12";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
				$optPer.="<option value=".$data['periode'].">".$data['periode']."</option>";
			}	
			
	//print_r($_SESSION['empl']);		
$optKdOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
	$sql="SELECT * FROM ".$dbname.".organisasi where tipe='KEBUN'";
else
	$sql="SELECT * FROM ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' ";	
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
{
	$optKdOrg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
}				
			
					
?>



<?php
include('master_mainMenu.php');
OPEN_BOX();
$arr="##kdorg##per";	
$arr2="##kdorg2##per2";

echo "<fieldset style='float:left;'><legend><b>Hapus BKM/PNN Kosong</b></legend>
<table>
	
	
	<tr>
		<td>".$_SESSION['lang']['kodeorg']."</td>
		<td>:</td>
		<td><select id=kdorg2 style='width:155px;'>".$optKdOrg."</select></td>
	</tr>
        
	
	<tr>
		<td>".$_SESSION['lang']['periode']."</td>
		<td>:</td>
		<td><select id=per2 style='width:155px;'>".$optPer."</select></td>
	</tr>
	
	<tr>
		<td colspan=100>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=100>
		<button id=tPreviewx onclick=zPreview('kebun_slave_3cekbkm2','".$arr2."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
		<button id=tExcelx onclick=zExcel(event,'kebun_slave_3cekbkm2.php','".$arr2."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>

		
		<button onclick=batalx() id=tBatalx class=mybutton name=btnBatal id=btnBatal>".$_SESSION['lang']['cancel']."</button>
		</td>
	</tr>
</table>
</fieldset>";


echo "<fieldset style='float:left;'><legend><b>Update BKM Salah</b></legend>
<table>
	
	
	<tr>
		<td>".$_SESSION['lang']['kodeorg']."</td>
		<td>:</td>
		<td><select id=kdorg style='width:155px;'>".$optKdOrg."</select></td>
	</tr>
	
	<tr>
		<td>".$_SESSION['lang']['periode']."</td>
		<td>:</td>
		<td><select id=per style='width:155px;'>".$optPer."</select></td>
	</tr>
	
	<tr>
		<td colspan=100>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=100>
		<button id=tPreview onclick=zPreview('kebun_slave_3cekbkm','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
		<button id=tExcel onclick=zExcel(event,'kebun_slave_3cekbkm.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>

		
		<button onclick=batal() id=tBatal class=mybutton name=btnBatal id=btnBatal>".$_SESSION['lang']['cancel']."</button>
		</td>
	</tr>
</table>
</fieldset>";


echo "<fieldset style='float:left;'><legend><b>Update BKM Salah</b></legend>
        <b>Harap melakukan proses hapus transaksi yang kosong, baru kemudian melakukan update bkm yang salah</b>
       
        
        
        ";







CLOSE_BOX();
?>





<?php
OPEN_BOX();

echo "
<fieldset style='clear:both'><legend><b>".$_SESSION['lang']['printArea']."</b></legend>
<div id='printContainer' style='overflow:auto;height:400px;max-width:1220px'; >
</div></fieldset>";//<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'; >
//<div id='printContainer'>

CLOSE_BOX();
echo close_body();					
?>