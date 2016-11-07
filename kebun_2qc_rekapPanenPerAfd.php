<?php //@Copy nangkoelframework
//-----------------ind
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();


?>

<script language=javascript1.2 src='js/kebun_2qc_rekapPanenPerAfd.js'></script>



<?php
#divisi (kebun)
$optDiv="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
//$g="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi  where tipe='KEBUN' and induk='".$_SESSION['empl']['kodeorganisasi']."'";
$g="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi  where tipe='KEBUN'";
$h=mysql_query($g) or die (mysql_error($conn));
while($i=mysql_fetch_assoc($h))
{
	$optDiv.="<option value='".$i['kodeorganisasi']."'>".$i['namaorganisasi']."</option>";
}

#periode for searching 
$optPer="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$i="select distinct substr(tanggalcek,1,7) as periode from ".$dbname.".kebun_qc_panenht order by periode desc limit 10";
$j=mysql_query($i) or die (mysql_error($conn));
while($k=mysql_fetch_assoc($j))
{
	$optPer.="<option value='".$k['periode']."'>".$k['periode']."</option>";
}	
			
?>



<?php
include('master_mainMenu.php');
OPEN_BOX();

echo "<fieldset style='float:left;'><legend><b>".$_SESSION['lang']['cek']." ".$_SESSION['lang']['panen']." ".$_SESSION['lang']['afdeling']."</b></legend>
<table>
	<tr>
		<td>".$_SESSION['lang']['divisi']."</td>
		<td>:</td>
		<td><select id=div style='width:200px;'>".$optDiv."</select></td>
	</tr>
	
	
	<tr>
		<td>".$_SESSION['lang']['periode']."</td>
		<td>:</td>
		<td><select id=per style='width:200px;'>".$optPer."</select></td>
	</tr>
	
	<tr>
		<td colspan=100>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=5 align=center>

		<img src='images/icons/Basic_set_Png/statistics_16.png' style='width:50px;' title='Graphics'  onclick=graph(event)>
		  
		</td>
	</tr>
</table>
</fieldset>";


CLOSE_BOX();
echo close_body();




?>