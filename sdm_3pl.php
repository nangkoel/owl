<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
include_once('lib/zLib.php');


?>

<script language=javascript1.2 src='js/sdm_3pl.js'></script>
<script language=javascript src='js/zReport.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<script language="javascript" src="js/zMaster.js"></script>




<?php
$nmOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');


#Per SCH
$optPerSch="<option value=''>".$_SESSION['lang']['all']."</option>";
$aPt="select distinct periodegaji from ".$dbname.".sdm_pendapatanlainht order by periodegaji desc limit 12  ";
$bPt=mysql_query($aPt) or die (mysql_error($conn));
while($cPt=mysql_fetch_assoc($bPt))
{
	$optPerSch.="<option value='".$cPt['periodegaji']."'>".$cPt['periodegaji']."</option>";
}


##jenis komponen
$optJns="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$aPt="select id,name from ".$dbname.".sdm_ho_component where id in ('16','43','58','14','59','75') ";
$bPt=mysql_query($aPt) or die (mysql_error($conn));
while($cPt=mysql_fetch_assoc($bPt))
{
	$optJns.="<option value='".$cPt['id']."'>".$cPt['name']."</option>";
}



#kodeorg
$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$i="select kodeorganisasi,namaorganisasi  from ".$dbname.". organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
$j=mysql_query($i) or die (mysql_error($conn));
while($k=mysql_fetch_assoc($j))
{
	$optOrg.="<option value='".$k['kodeorganisasi']."'>".$k['namaorganisasi']."</option>";
}


##periode
$optPer="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$iO="select distinct periode from ".$dbname.". sdm_5periodegaji order by periode desc";
$jO=mysql_query($iO) or die (mysql_error($conn));
while($kO=mysql_fetch_assoc($jO))
{
	$optPer.="<option value='".$kO['periode']."'>".$kO['periode']."</option>";
}

##karyawan
/*if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{
  $str1="select * from ".$dbname.".datakaryawan
      where (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].") 
          and alokasi=1
          order by namakaryawan";
  // $str2="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where lenght(kodeorganisasi)='4'";	  	  
}*/

if($_SESSION['empl']['regional']=='SULAWESI')
{    
    if($_SESSION['empl']['tipelokasitugas']=='KANWIL')
    {
      $str1="select * from ".$dbname.".datakaryawan
             where (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].") 
              and tipekaryawan!=0 
              and lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
              order by namakaryawan";   	  
    }
    else
    {
       $str1="select * from ".$dbname.".datakaryawan
          where (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].") 
              and tipekaryawan!=0 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
              order by namakaryawan";	
            // $str2="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".substr($_SESSION['empl']['lokasitugas'],0,4)."'";	  
    }
}
else
{
    $str1="select * from ".$dbname.".datakaryawan
          where (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].") 
              and tipekaryawan!=0 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
              order by namakaryawan";	
}

//echo $str1;

$res1=mysql_query($str1,$conn);
$optKar="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
while($bar1=mysql_fetch_object($res1))
{
	$optKar.="<option value=".$bar1->karyawanid.">".$bar1->nik." -- ".$bar1->namakaryawan." -- ".$bar1->lokasitugas."[".$nmOrg[$bar1->lokasitugas]."]</option>";
}



?>


<?php

$frm[0]='';
$frm[1]='';


OPEN_BOX('',"<b>PENDAPATAN LAIN KARYAWAN<br /><br /></b>");



$frm[0].="<fieldset id=header>";
$frm[0].="<legend><b>".$_SESSION['lang']['header']."</b></legend>";

		$frm[0].="<table border=0 cellpadding=1 cellspacing=1>";
			$frm[0].="
			
			<tr>
				<td>".$_SESSION['lang']['kodeorg']."</td> 
				<td>:</td>
				<td><select id=org style=\"width:150px;\">".$optOrg."</select></td>
			</tr> 
			
			<tr>
				<td>".$_SESSION['lang']['periodegaji']."</td> 
				<td>:</td>
				<td><select id=per style=\"width:150px;\">".$optPer."</select></td>
			</tr> 
			<tr>
				<td>".$_SESSION['lang']['jenis']."</td> 
				<td>:</td>
				<td><select id=kom style=\"width:150px;\">".$optJns."</select></td>
			</tr> 
			<tr>
				<td>
				<button class=mybutton id=saveHeader onclick=saveHeader()>".$_SESSION['lang']['save']."</button>
				<button class=mybutton id=cancelHeader  onclick=cancelHeader()>".$_SESSION['lang']['baru']."</button>	
				</td>
			</tr>			
</table>
</fieldset>";	//<input type=hidden id=method value='insert'>

	
//$frm[0].="<input type=text id=notranDet disabled value='".$notran."' onkeypress=\"return tanpa_kutip(event);\" class=myinputtext disabled style=\"width:150px;\">";

	


$frm[0].="<div id=displayall  style='display:none'>";
$frm[0].="<fieldset><legend><b>".$_SESSION['lang']['detail']."</b></legend>";
$frm[0].="<table border=0 cellpadding=1 cellspacing=1 class=sortable>
			<thead><tr class=rowheader>
				<td align=center>Nama Karyawan</td>
				<td align=center>Jumlah</td>
				<td align=center>".$_SESSION['lang']['save']."</td>
			</tr></thead>
			<tr class=rowcontent>
				<td><select id=kar style=\"width:400px;\">".$optKar."</select></td>
				<td><input type=text maxlength=20 id=jum onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:100px;\"></td>
				<td align=center><img src=images/icons/Grey/PNG/save.png class=resicon  title='Save Detail' onclick=\"saveDetail();\" ></td>
			</tr></table></fieldset>";



$frm[0].="<fieldset><legend><b>".$_SESSION['lang']['list']."</b></legend><div id=containList style=\"height:300px;width:550px;overflow:scroll;display:none;\">
			<script>loadDataDetail()</script>
			</div></fieldset>";	// style='display:none;' 


$frm[1].="<fieldset>
		<legend>".$_SESSION['lang']['list']."</legend>
		".$_SESSION['lang']['periode']." : <select id=perSch style=\"width:100px;\" onchange=loadData()>".$optPerSch."</select>	
		<div id=container> 
			<script>loadData()</script>
		</div>
	</fieldset>";
	
$frm[1].="</div>";		
$hfrm[0]=$_SESSION['lang']['form'];
$hfrm[1]=$_SESSION['lang']['list'];

//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,250,600);		
		
CLOSE_BOX();
echo close_body();			
?>