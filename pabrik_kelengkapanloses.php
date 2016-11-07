<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
include_once('lib/zLib.php');
?>

<script language=javascript1.2 src='js/pabrik_kelengkapanloses.js'></script>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src='js/iReport.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>


<?php
	$optProduk="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	$i="select distinct produk from ".$dbname.".pabrik_5kelengkapanloses";
	$n=mysql_query($i) or die (mysql_error($conn));
	while($d=mysql_fetch_assoc($n))
	{
		$optProduk.="<option value='".$d['produk']."'>".$d['produk']."</option>";
	}	
	
$frm[0]='';
$frm[1]='';

$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql="SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where kodeorganisasi like '%M' and length(kodeorganisasi)=4 ORDER BY kodeorganisasi";

//echo $sql;
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
				$optOrg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
			}
			


$arrLaporan="##kodeorgLap##tglLap##produkLap";	
							
?>




<?php
OPEN_BOX();
//print_r($_SESSION['empl']['regional']);
$frm[0].="<fieldset style='float:left;'>";
		$frm[0].="<legend><b>".$_SESSION['lang']['kelengkapanloses']."</b></legend>";
		
			$frm[0].="<fieldset  style='float:left;'>";
			$frm[0].="<legend>".$_SESSION['lang']['form']."</legend>";
			$frm[0].="<table border=0 cellpadding=1 cellspacing=1>";
				$frm[0].="<tr>
						<td>".$_SESSION['lang']['kodeorg']."</td> 
						<td>:</td>
						<td><input type=text maxlength=4 disabled value='".$_SESSION['empl']['lokasitugas']."' id=kodeorg onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:100px;\"></td>
					</tr> 
					<tr>
						<td>".$_SESSION['lang']['tanggal']."</td> 
						<td>:</td>
						<td><input type=text class=myinputtext  id=tgl onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td>
					</tr> 
					<tr>
						<td>".$_SESSION['lang']['produk']."</td> 
						<td>:</td>
						<td><select id=produk onchange=getForm() style=\"width:150px;\">".$optProduk."</select></td>
					</tr>";
			$frm[0].="</table></fieldset>";
			
				
			$frm[0].="<div id=form style=display:none>";
			$frm[0].="<fieldset style='float:left;'>";
			$frm[0].="<legend>".$_SESSION['lang']['form']."</legend>";
			$frm[0].="<table id=isi border=0 cellpadding=1 cellspacing=1>";
			$frm[0].="</table>";
			$frm[0].="</fieldset></div>";
			
			$frm[0].="<div id=editForm style=display:none>";
			$frm[0].="<fieldset style='float:left;'>";
			$frm[0].="<legend>".$_SESSION['lang']['edit']." ".$_SESSION['lang']['form']."</legend>";
			$frm[0].="	<table border=0 cellpadding=1 cellspacing=1>
							<tr>
								<td>".$_SESSION['lang']['kodeorg']."</td> 
								<td>:</td>
								<td><input type=text maxlength=4 disabled value='".$_SESSION['empl']['lokasitugas']."' id=kodeorgEdit onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:100px;\"></td>
							</tr> 
				
							
							<tr>
								<td>".$_SESSION['lang']['tanggal']."</td> 
								<td>:</td>
								<td><input type=text class=myinputtext disabled  id=tglEdit onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td>
							</tr> 
							<tr>
								<td>".$_SESSION['lang']['produk']."</td> 
								<td>:</td>
								<td><select id=produkEdit disabled style=\"width:150px;\">".$optProduk."</select></td>
							</tr>
							<tr>
								<td>".$_SESSION['lang']['namabarang']."</td> 
								<td>:</td>
								<td><input type=text id=barangEdit disabled maxlength=50 disabled onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:100px;\"></td>
							</tr>
							<tr>
								<td>".$_SESSION['lang']['nilai']."</td> 
								<td>:</td>
								<td><input type=text id=inpEdit onkeypress=\"return angka_doang(event);\"  value=0 class=myinputtextnumber style=\"width:50px;\"></td>
							</tr> 
								<input type=hidden id=idEdit disabled onkeypress=\"return angka_doang(event);\"   class=myinputtextnumber style=\"width:50px;\">
							
							<tr>
							<td>
								<button class=mybutton onclick=saveEdit()>Simpan</button>
								<button class=mybutton onclick=cancel()>Hapus</button>
							<td>
							</tr>
							
							
							
							
						</table>";
			$frm[0].="</fieldset></div>";			
			

$frm[0].="<fieldset style='float:left;'>
		<legend>".$_SESSION['lang']['list']."</legend>
		<tr>
			<td>".$_SESSION['lang']['tanggal']."</td> 
			<td>:</td>
			<td><input type=text class=myinputtext onchange=loadData() id=tglsch onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td>
		</tr>
		
		<div id=container> 
			<script>loadData()</script>
		</div>
	</fieldset>";
$frm[0].="</fieldset>";



$frm[1]="<fieldset style='float:left;'><legend><b>".$_SESSION['lang']['kelengkapanloses']."</b></legend>
<table>
	<tr>
		<td>".$_SESSION['lang']['kodeorg']."</td> 
		<td>:</td>
		<td><select id=kodeorgLap style='width:150px;'>".$optOrg."</select></td>
	</tr> 
	<tr>
		<td>".$_SESSION['lang']['tanggal']."</td> 
		<td>:</td>
		<td><input type=text class=myinputtext  id=tglLap onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td>
	</tr> 
	<tr>
		<td>".$_SESSION['lang']['produk']."</td> 
		<td>:</td>
		<td><select id=produkLap style=\"width:150px;\">".$optProduk."</select></td>
	</tr>
	<tr>
		<td colspan=100>
		<button onclick=iPreview('pabrik_slave_kelengkapanloses','".$arrLaporan."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
		<button onclick=iExcel(event,'pabrik_slave_kelengkapanloses.php','".$arrLaporan."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
		
		<button onclick=batal() class=mybutton name=btnBatal id=btnBatal>".$_SESSION['lang']['cancel']."</button>
		</td>
	</tr>
</table>
</fieldset>

<fieldset style='clear:both'><legend><b>".$_SESSION['lang']['printArea']."</b></legend>
<div id='printContainer'  >
</div></fieldset>";

$hfrm[0]=$_SESSION['lang']['form'];
$hfrm[1]=$_SESSION['lang']['printArea'];

//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,250,800);

CLOSE_BOX();
echo close_body();	
?>				
