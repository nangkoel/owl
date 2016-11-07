<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
include_once('lib/zLib.php');


?>

<script language=javascript1.2 src='js/log_packing.js'></script>
<script language=javascript src='js/zReport.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<script language="javascript" src="js/zMaster.js"></script>




<?php
#PT
$optPt="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$aPt="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi  where tipe='PT' ";
$bPt=mysql_query($aPt) or die (mysql_error($conn));
while($cPt=mysql_fetch_assoc($bPt))
{
	$optPt.="<option value='".$cPt['kodeorganisasi']."'>".$cPt['namaorganisasi']."</option>";
}


#PT SCH
$optPtSch="<option value=''>".$_SESSION['lang']['all']."</option>";
$aPt="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi  where tipe='PT' ";
$bPt=mysql_query($aPt) or die (mysql_error($conn));
while($cPt=mysql_fetch_assoc($bPt))
{
	$optPtSch.="<option value='".$cPt['kodeorganisasi']."'>".$cPt['namaorganisasi']."</option>";
}

##karyawan yg menyerahkan
$optKar="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
	$aKar="select karyawanid,namakaryawan from ".$dbname.".datakaryawan  where bagian='LOG' and lokasitugas like '%HO%' ";
else
	$aKar="select karyawanid,namakaryawan from ".$dbname.".datakaryawan  where bagian='LOG' and lokasitugas='".$_SESSION['empl']['lokasitugas']."' ";
	
$bKar=mysql_query($aKar) or die (mysql_error($conn));
while($cKar=mysql_fetch_assoc($bKar))
{
	$optKar.="<option value='".$cKar['karyawanid']."'>".$cKar['namakaryawan']."</option>";
}
	


#periode for searching 
$optPer="<option value=''>".$_SESSION['lang']['all']."</option>";
$i="select distinct substr(tanggal,1,7) as periode from ".$dbname.".log_packinght order by periode desc limit 10";
$j=mysql_query($i) or die (mysql_error($conn));
while($k=mysql_fetch_assoc($j))
{
	$optPer.="<option value='".$k['periode']."'>".$k['periode']."</option>";
}

$optMandor="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optAstn="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optKadiv="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";



$in='PL'.date('YmdHis');
//$in='PL'.$dNo;




?>


<?php

$frm[0]='';
$frm[1]='';


OPEN_BOX('',"<b>PACKING LIST</b>");



$frm[0].="<fieldset id=header>";
$frm[0].="<legend><b>".$_SESSION['lang']['header']."</b></legend>";

		$frm[0].="<table border=0 cellpadding=1 cellspacing=1>";
			$frm[0].="
			
			<tr>
				<td>".$_SESSION['lang']['notransaksi']."</td> 
				<td>:</td>
				<td><input type=text id=notran value=".$in." onkeypress=\"return tanpa_kutip(event);\" class=myinputtext disabled style=\"width:150px;\"></td>
			</tr>
			
			<tr>
				<td>".$_SESSION['lang']['pt']."</td> 
				<td>:</td>
				<td><select id=pt = style=\"width:150px;\">".$optPt."</select></td>
			</tr> 
			
			<tr>
				<td>".$_SESSION['lang']['kodeorg']."</td> 
				<td>:</td>
				<td><input type=text maxlength=20 value='".$_SESSION['empl']['lokasitugas']."' id=kdOrg  onkeypress=\"return tanpa_kutip(event);\" disabled class=myinputtext style=\"width:150px;\"></td>
			</tr>
			
			
			<tr>
				<td>".$_SESSION['lang']['tanggal']."</td> 
				<td>:</td>
				<td><input type=text class=myinputtext readonly  id=tgl onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=\"width:150px;\"/></td>
			</tr>
			
			<tr>
				<td>".$_SESSION['lang']['ukuranpeti']."</td> 
				<td>:</td>
				<td><input type=text maxlength=20 id=peti  onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=\"width:150px;\"></td>
			</tr>
			
			<tr>
				<td>No. Koli</td> 
				<td>:</td>
				<td><input type=text  id=ket onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=\"width:150px;\"></td>
			</tr>
			
			<tr>
				<td>".$_SESSION['lang']['menyerahkan']."</td> 
				<td>:</td>
				<td><select id=serah style=\"width:150px;\">".$optKar."</select></td>
			</tr>
			
			<tr>
				<td>".$_SESSION['lang']['menerima']."</td> 
				<td>:</td>
				<td><input type=text  id=terima onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=\"width:150px;\"></td>
			</tr>
			
			
			<tr>
				<td>
				<button class=mybutton onclick=saveHeader()>".$_SESSION['lang']['save']."</button>
				<button class=mybutton  onclick=cancel()>".$_SESSION['lang']['baru']."</button>	
				</td>
			</tr>			
</table>
</fieldset><input type=hidden id=method value='insert'>";	

	
//$frm[0].="<input type=text id=notranDet disabled value='".$notran."' onkeypress=\"return tanpa_kutip(event);\" class=myinputtext disabled style=\"width:150px;\">";

	$tmbl="<tr>
			<td>".$_SESSION['lang']['find']." ".$_SESSION['lang']['nopo']." : 
				 <img src=images/zoom.png title='".$_SESSION['lang']['find']."'  class=resicon onclick=cariNoPo('".$_SESSION['lang']['find']."',event)>
			</td>
		  </tr>";

	$tmbl.="<tr>
			<td>".$_SESSION['lang']['find']." ".$_SESSION['lang']['kodebarang']." : 
				 <img src=images/zoom.png title='".$_SESSION['lang']['find']."'  class=resicon onclick=inputBarang('".$_SESSION['lang']['find']."',event)>
			</td>
		  </tr>";




$frm[0].="<div id=detailForm  style='display:none'>";
$frm[0].="<fieldset style=float:left>";
$frm[0].="<legend><b>".$_SESSION['lang']['detail']."</b></legend>";
$frm[0].="<table border=0 cellpadding=1 cellspacing=1>";
$frm[0].=$tmbl;	
$frm[0].="</table>";
$frm[0].="<div id=containList  style='display:none;'>
			<script>loadDataDetail()</script>
			</div></fieldset>";	


$frm[1].="<fieldset>
		<legend>".$_SESSION['lang']['list']."</legend>
		".$_SESSION['lang']['kodept']." : <select id=kdPtSch style=\"width:100px;\" onchange=loadData()>".$optPtSch."</select>
		".$_SESSION['lang']['periode']." : <select id=perSch style=\"width:100px;\" onchange=loadData()>".$optPer."</select>		
                ".$_SESSION['lang']['notransaksi']." : <input type=text style=width:100px class=myinputtext id=notransCari onkeypress='return tanpa_kutip(event)' onblur=loadData() />
		<div id=container> 
			<script>loadData()</script>
		</div>
	</fieldset>";
		
$hfrm[0]=$_SESSION['lang']['form'];
$hfrm[1]=$_SESSION['lang']['list'];

//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,250,1150);		
		
CLOSE_BOX();
echo close_body();			
?>