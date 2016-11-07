<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
include_once('lib/zLib.php');
?>

<script language=javascript1.2 src='js/kebun_qc_semprot.js'></script>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src='js/iReport.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<script language="javascript" src="js/zMaster.js"></script>




<?php
	
#keg	
$optKeg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$a="select * from ".$dbname.".setup_kegiatan order by namakegiatan asc";
$b=mysql_query($a) or die (mysql_error($conn));
while($c=mysql_fetch_assoc($b))
{
	$optKeg.="<option value='".$c['kodekegiatan']."'>".$c['namakegiatan']." - ".$c['kelompok']." - ".$c['satuan']."</option>";
}

#barang
$optBarang="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$d="select * from ".$dbname.".log_5masterbarang  where kelompokbarang='055' order by namabarang asc";
$e=mysql_query($d) or die (mysql_error($conn));
while($f=mysql_fetch_assoc($e))
{
	$optBarang.="<option value='".$f['kodebarang']."'>".$f['namabarang']." - ".$f['satuan']."</option>";
}

#divisi (kebun)
$optDiv="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$g="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi  where tipe='KEBUN' and induk='".$_SESSION['empl']['kodeorganisasi']."'";
$h=mysql_query($g) or die (mysql_error($conn));
while($i=mysql_fetch_assoc($h))
{
	$optDiv.="<option value='".$i['kodeorganisasi']."'>".$i['namaorganisasi']."</option>";
}

#periode for searching 
$optPer="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$i="select distinct substr(tanggal,1,7) as periode from ".$dbname.".kebun_qc_semprot order by periode desc limit 10";
$j=mysql_query($i) or die (mysql_error($conn));
while($k=mysql_fetch_assoc($j))
{
	$optPer.="<option value='".$k['periode']."'>".$k['periode']."</option>";
}



$optKar="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optMandor="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optAstn="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optKadiv="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";




?>


<?php
OPEN_BOX();
//print_r($_SESSION['empl']);
echo"<fieldset style='float:left;'>";
echo"<legend><b>Semprot Kimia</b></legend>";
	
	##form 1
	echo"<fieldset style='float:left;'>";
	echo"<legend>".$_SESSION['lang']['header']."</legend>";
		echo"<table border=0 cellpadding=1 cellspacing=1>";
			echo"
			<tr>
				<td>".$_SESSION['lang']['tanggal']."</td> 
				<td>:</td>
				<td><input type=text class=myinputtext  id=tgl onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=\"width:100px;\"/></td>
			</tr>
			<tr>
				<td>".$_SESSION['lang']['divisi']."</td> 
				<td>:</td>
				<td><select id=kdDiv onchange=getAfd() style=\"width:100px;\">".$optDiv."</select></td>
			</tr> 
			<tr>
				<td>".$_SESSION['lang']['afdeling']."</td> 
				<td>:</td>
				<td><select id=kdAfd  onchange=getBlok() style=\"width:100px;\">".$optAfd."</select></td>
			</tr> 
			<tr>
				<td>".$_SESSION['lang']['blok']."</td> 
				<td>:</td>
				<td><select id=kdBlok onchange=getData() style=\"width:100px;\">".$optBlok."</select></td>
			</tr> 
			<tr>
				<td>".$_SESSION['lang']['luasareal']."</td> 
				<td>:</td>
				<td><input type=text maxlength=10 disabled id=luasAreal onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtextnumber style=\"width:100px;\"></td>
			</tr>
			<tr>
				<td>".$_SESSION['lang']['jumlahpokok']."</td> 
				<td>:</td>
				<td><input type=text maxlength=10 disabled id=jmlPkk onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtextnumber style=\"width:100px;\"></td>
			</tr>
			<tr>
				<td>".$_SESSION['lang']['kegiatan']."</td> 
				<td>:</td>
				<td><select id=kdKeg style=\"width:100px;\">".$optKeg."</select></td>
			</tr> 
		</table>
	</fieldset>";
		##tutup form 1
		
		
		
		

		
	##dosis gulma
	echo"<fieldset  style='float:left;'>";
	echo"<legend>".$_SESSION['lang']['material']." & Gulma</legend>";		
	
	echo"<fieldset>";
	echo"<legend></legend>";
		echo"<table border=0 cellpadding=1 cellspacing=1>";	
		echo"
		
			<tr>
				<td>".$_SESSION['lang']['takaranpakai']."</td> 
				<td>:</td>
				<td><input type=text maxlength=20 id=dosis onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:100px;\"></td>
			</tr>
			<tr>
				<td>".$_SESSION['lang']['jenisgulmadominan']."</td> 
				<td>:</td>
				<td><input type=text maxlength=20 id=jenisgulma onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:100px;\"></td>
			</tr>		
			<tr>
				<td>".$_SESSION['lang']['kondisigulma']."</td> 
				<td>:</td>
				<td><input type=text maxlength=20 id=kondisigulma onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:100px;\"></td>
			</tr>		
		</table>
	</fieldset>";		
		

	##dosis material	
	echo"<fieldset style='float:left;'>";
	echo"<legend>".$_SESSION['lang']['material']."</legend>";
		echo"<table table class=sortable border=0 cellpadding=1 cellspacing=1 >";
			echo"
			<tr class=rowheader>
				<td align=center>".$_SESSION['lang']['dosis']."</td>
				<td align=center>".$_SESSION['lang']['material']."</td>
				<td align=center>".$_SESSION['lang']['dosis']."</td>
				<td align=center>".$_SESSION['lang']['pengeluaranbarang']."</td>
				<td align=center>".$_SESSION['lang']['pakaimaterial']."</td>
			</tr>";
			for($i=1;$i<=3;$i++)
			{
				echo"<tr class=rowcontent>
					<td align=center>".$i."</td> 
					<td><select id=dosismaterial$i style=\"width:100px;\">".$optBarang."</select></td>
					<td><input type=text maxlength=10 id=dosisjumlah$i onkeypress=\"return angka_doang(event);\"  class=myinputtext style=\"width:50px;\"></td>
					
					<td><input type=text maxlength=10 id=jumlahdiambil$i onkeypress=\"return angka_doang(event);\"  class=myinputtext style=\"width:110px;\"></td>
					<td><input type=text maxlength=10 id=jumlahdipakai$i onkeypress=\"return angka_doang(event);\"  class=myinputtext style=\"width:70px;\"></td>
				</tr>"; 
			}
			
			
			
			
		echo"
			</table>
		</fieldset>
		</fieldset>";
	
	
	/*##diambil gudang. ID : materialdiambil1 jumlahdiambil1
	echo"<fieldset>";
	echo"<legend>".$_SESSION['lang']['diambil']." ".$_SESSION['lang']['gudang']." / ".$_SESSION['lang']['pengeluaranbarang']."</legend>";
		echo"<table table class=sortable border=0 cellpadding=1 cellspacing=1 >";
			echo"
			<tr class=rowheader>
				<td>".$_SESSION['lang']['dosis']."</td>
				<td>".$_SESSION['lang']['material']."</td>
				<td>".$_SESSION['lang']['jumlah']."</td>
			</tr>";
			for($i=1;$i<=3;$i++)
			{
				echo"<tr class=rowcontent>
					<td>".$i."</td> 
					<td><select id=materialdiambil$i style=\"width:100px;\">".$optBarang."</select></td>
					<td><input type=text maxlength=10 id=jumlahdiambil$i onkeypress=\"return angka_doang(event);\"  class=myinputtext style=\"width:50px;\"></td>
				</tr>"; 
			}
		echo"
			</table>
		</fieldset>";
	
	##pemakaian barang id: materialdipakai1,jumlahdipakai1
	echo"<fieldset style='float:left;'>";
	echo"<legend>".$_SESSION['lang']['pemakaianBarang']."</legend>";
		echo"<table table class=sortable border=0 cellpadding=1 cellspacing=1 >";
			echo"
			<tr class=rowheader>
				<td>".$_SESSION['lang']['dosis']."</td>
				<td>".$_SESSION['lang']['material']."</td>
				<td>".$_SESSION['lang']['jumlah']."</td>
			</tr>";
			for($i=1;$i<=3;$i++)
			{
				echo"<tr class=rowcontent>
					<td>".$i."</td> 
					<td><select id=materialdipakai$i style=\"width:100px;\">".$optBarang."</select></td>
					<td><input type=text maxlength=10 id=jumlahdipakai$i onkeypress=\"return angka_doang(event);\"  class=myinputtext style=\"width:50px;\"></td>
				</tr>"; 
			}
		echo"
			</table>
		</fieldset>";	
echo"</fieldset>";	*/	

	##karyawan
	echo"<fieldset style='float:left;'>";
	echo"<legend>".$_SESSION['lang']['karyawan']."</legend>";
		echo"<table table class=sortable border=0 cellpadding=1 cellspacing=1 >";
			echo"
			<tr class=rowheader>
				<td>".$_SESSION['lang']['nourut']."</td>
				<td>".$_SESSION['lang']['namakaryawan']."</td>
				<td>".$_SESSION['lang']['hasil']."</td>
			</tr>";
			
			for($i=1;$i<=15;$i++)
			{
		
				echo"
				<tr class=rowcontent>
					<td>$i</td> 
					<td><select id=karyawan$i style=\"width:100px;\">".$optKar."</select></td>
					<td><input type=text maxlength=10 id=hasilkaryawan$i onkeypress=\"return angka_doang(event);\"  class=myinputtext style=\"width:50px;\"></td>
				</tr>";	
			}
	echo"</table></fieldset>";		
	
	
	echo"<fieldset style='float:left;'>";
	echo"<legend>".$_SESSION['lang']['keterangan']."</legend>";
		echo"<table border=0 cellpadding=1 cellspacing=1>";	
		echo"
			<tr>
				<td valign=top>".$_SESSION['lang']['keterangan']."</td> 
				<td valign=top>:</td>
				<td><textarea cols=25 rows=6 id=keterangan onkeypress=\"return_tanpa_kutip(event);\"></textarea></td>
			</tr>
				<tr>
					<td>".$_SESSION['lang']['pengawasan']."</td> 
					<td>:</td> 
					<td><select id=pengawas style=\"width:100px;\">".$optMandor."</select></td>
				</tr>	
				<tr>
					<td>".$_SESSION['lang']['pendamping']."</td> 
					<td>:</td> 
					<td><select id=asisten style=\"width:100px;\">".$optAstn."</select></td>
				</tr>	
				<tr>
					<td>".$_SESSION['lang']['mengetahui']."</td> 
					<td>:</td> 
					<td><select id=mengetahui style=\"width:100px;\">".$optKadiv."</select></td>
				</tr>		
			
		</table>
	</fieldset>";	
		
	echo"<fieldset style='float:left;'>	
		<button class=mybutton onclick=simpan()>".$_SESSION['lang']['save']."</button>
		<button class=mybutton onclick=cancel()>".$_SESSION['lang']['baru']."</button>	
		</fieldset>";
	
	
		

		
echo"</fieldset>";			
CLOSE_BOX();

OPEN_BOX();
echo"<fieldset style='float:left;'>
		<legend>".$_SESSION['lang']['list']."</legend>
		".$_SESSION['lang']['kodeorg']." : <select id=kdDivSch style=\"width:100px;\" onchange=loadData()>".$optDiv."</select>
		".$_SESSION['lang']['periode']." : <select id=perSch style=\"width:100px;\" onchange=loadData()>".$optPer."</select>
		
		
		
		
		<div id=container> 
			<script>loadData()</script>
		</div>
	</fieldset>";

CLOSE_BOX();
	
echo close_body();			
?>