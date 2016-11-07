<?php //@Copy nangkoelframework 
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src="js/keu_laporan.js"></script>
<?php
include('master_mainMenu.php'); 
OPEN_BOX();

//$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
//    where tipe in('KEBUN','PABRIK','GUDANG','GUDANGTEMP','TRAKSI','KANWIL') or (tipe='HOLDING' and length(kodeorganisasi)=4)
//    order by kodeorganisasi";
//$res=mysql_query($str);
//$optkodeorg="<option value=''>".$_SESSION['lang']['all']."</option>";
//while($bar=mysql_fetch_object($res))
//{
//    $optkodeorg.="<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi." (".$bar->namaorganisasi.")</option>";
//}

//=================ambil PT;  
if($_SESSION['empl']['tipelokasitugas']=='HOLDING' or $_SESSION['empl']['tipelokasitugas']=='KANWIL'){
    $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
        where tipe='PT'
        order by namaorganisasi";
}else{
    $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
        where tipe='PT' and kodeorganisasi = '".$_SESSION['empl']['kodeorganisasi']."'
        order by namaorganisasi";
}
$res=mysql_query($str);
$optpt="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
while($bar=mysql_fetch_object($res))
{
    $optpt.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}

$optunit="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
//=================ambil unit;  
//if($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
//    $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
//        where (tipe='KEBUN' or tipe='PABRIK' or tipe='KANWIL' or tipe='TRAKSI'
//        or tipe='HOLDING')  and induk!=''
//        ";
//    $optunit.="<option value=''>".$_SESSION['lang']['all']."</option>";
//}
//else
//if($_SESSION['empl']['tipelokasitugas']=='KANWIL'){
//    $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
//        where induk='".$_SESSION['empl']['kodeorganisasi']."' and length(kodeorganisasi)=4 and kodeorganisasi not like '%HO'";
//}
//else
//$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
//    where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'  and induk!=''";
//$res=mysql_query($str);
//while($bar=mysql_fetch_object($res))
//{
//    $optunit.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
//
//}

?>
<fieldset style="float: left;"> 
<legend><b><?php echo $_SESSION['lang']['periode'].' '.$_SESSION['lang']['tutupbuku']?></b></legend>
<table cellspacing="1" border="0" >
<tr>
    <td><label><?php echo $_SESSION['lang']['pt']?></label></td>
    <td><select id=kodept style='width:200px;' onchange=ambilAnakPA(this.options[this.selectedIndex].value)><?php echo $optpt; ?></select></td>
</tr>
<tr>
    <td><label><?php echo $_SESSION['lang']['kodeorganisasi']?></label></td>
    <td><select id=kodeunit style='width:200px;' onchange=document.getElementById('container').innerHTML=''><?php echo $optunit; ?></select></td>
</tr>
<tr height="20"><td colspan="2"><button class=mybutton onclick=getPeriodeAkuntansi()><?php echo $_SESSION['lang']['preview'] ?></button></td></tr>
</table>
</fieldset>
<?php

CLOSE_BOX();
OPEN_BOX('','Result:');
//echo"<span id=printPanel style='display:none;'>
//     <img onclick=periksajurnalKeExcel(event,'keu_slave_2periksaJurnal_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
//	 <img onclick=periksajurnalKePDF(event,'keu_slave_2periksaJurnal_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>
//	 </span>";    
echo"<div id=container style='width:100%;height:359px;overflow:scroll;'>
</div>";
CLOSE_BOX();
close_body();

?>
