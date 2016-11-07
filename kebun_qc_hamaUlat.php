<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
include_once('lib/zLib.php');


?>

<script language=javascript1.2 src='js/kebun_qc_hamaUlat.js'></script>
<script language=javascript src='js/zReport.js'></script>
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
$d="select * from ".$dbname.".log_5masterbarang  where kodebarang like '056%' order by namabarang asc";
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
$i="select distinct substr(tanggal,1,7) as periode from ".$dbname.".kebun_qc_hama order by periode desc limit 10";
$j=mysql_query($i) or die (mysql_error($conn));
while($k=mysql_fetch_assoc($j))
{
	$optPer.="<option value='".$k['periode']."'>".$k['periode']."</option>";
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
$optMandor="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optAstn="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optKadiv="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";


$optAlat="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optAlat.="<option value='MISSBLOWER'>MISSBLOWER</option>";
$optAlat.="<option value='BOR'>BOR</option>";
?>


<?php
OPEN_BOX();
echo"<fieldset style='float:left;'>";
echo"<legend><b>Hama</b></legend>";

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
				<td><select id=kdBlok style=\"width:100px;\">".$optBlok."</select></td>
			</tr> 
			
			<tr>
				<td>".$_SESSION['lang']['lapPersonel']."</td> 
				<td>:</td>
				<td><input type=text maxlength=10 id=tenagakerja onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=\"width:100px;\"></td>
			</tr>
			
			<tr>
				<td>".$_SESSION['lang']['jammulai']."</td> 
				<td>:</td>
				
				<td>
					<select id=jm1 name=jmId >".$jm."</select>:<select id=mn1>".$mnt."</select>
					".$_SESSION['lang']['jamselesai']." :
					<select id=jm2 name=jmId2 >".$jm."</select>:<select id=mn2>".$mnt."</select>
				
				</td>
				
				
			</tr>
			
			<tr>
				<td>".$_SESSION['lang']['peralatan']."</td> 
				<td>:</td>
				<td><select id=alat style=\"width:100px;\"()>".$optAlat."</select></td>
			</tr>";
			
			for($i=1;$i<=1;$i++)
			{
				echo"<tr>
					<td>".$_SESSION['lang']['namabarang']." ".$i."</td> 
					<td>:</td>
					<td><select id=bahan$i style=\"width:100px;\"()>".$optBarang."</select></td>
				</tr>
				<tr>
					<td>".$_SESSION['lang']['dosis']." ".$i."</td> 
					<td>:</td>
					<td><input type=text maxlength=20 id=dosis$i onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:100px;\"></td>
				</tr>";
			}
			
			
			for($i=2;$i<=3;$i++)
			{
			/*	echo"<tr>
					<td>".$_SESSION['lang']['namabarang']." ".$i."</td> 
					<td>:</td>";*/
				echo"	<td><input type=hidden maxlength=20 id=bahan$i onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:100px;\"></td>
				";
				/*echo"</tr>
				<tr>
					<td>".$_SESSION['lang']['dosis']." ".$i."</td> 
					<td>:</td>";*/
				echo"	<td><input type=hidden maxlength=20 id=dosis$i onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:100px;\"></td>
				";
				//</tr>";
			}
			
			
			
		/*	echo"
				<tr>
					<td>".$_SESSION['lang']['dosis']."</td> 
					<td>:</td>";*/
				echo"		<td><input type=hidden maxlength=20 id=dosis onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:100px;\"></td>
				";
				//</tr>
			echo"	<tr>
					<td>".$_SESSION['lang']['jumlahpokok']."</td> 
					<td>:</td>
					<td><input type=text maxlength=20 id=pokok onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:100px;\"></td>
				</tr>
				<tr>
					<td>".$_SESSION['lang']['bensin']."</td> 
					<td>:</td>
					<td><input type=text maxlength=20 id=bensin onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:100px;\"></td>
				</tr>
				<tr>
					<td>".$_SESSION['lang']['oli']."</td> 
					<td>:</td>
					<td><input type=text maxlength=20 id=oli onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:100px;\"></td>
				</tr>
				<tr>
					<td valign=top>".$_SESSION['lang']['catatan']."</td> 
					<td valign=top>:</td>
					<td><textarea rows=3 colspan=5 id=catatan onkeypress=\"return_tanpa_kutip(event);\"></textarea></td>
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
			
			
			
			
			";
			
			
			
			
		
		

	
	echo"<tr><td><button class=mybutton onclick=simpan()>".$_SESSION['lang']['save']."</button>
		<button class=mybutton onclick=cancel()>".$_SESSION['lang']['baru']."</button></td></tr>";
		
	
		
echo"</table></fieldset>";			
CLOSE_BOX();




OPEN_BOX();
echo"<fieldset style='float:left;'>
		<legend>".$_SESSION['lang']['list']."</legend>
		".$_SESSION['lang']['divisi']." : <select id=kdDivSch style=\"width:100px;\" onchange=loadData()>".$optDiv."</select>
		".$_SESSION['lang']['periode']." : <select id=perSch style=\"width:100px;\" onchange=loadData()>".$optPer."</select>
		
		
		
		
		<div id=container> 
			<script>loadData()</script>
		</div>
	</fieldset>";

CLOSE_BOX();
	
echo close_body();			
?>