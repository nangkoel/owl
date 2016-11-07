<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/keu_laporan.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['neraca']).'</b>');

//get existing period
$str="select distinct periode from ".$dbname.".setup_periodeakuntansi
    order by periode desc";  
	  
$res=mysql_query($str);
#$optper="<option value=''>".$_SESSION['lang']['sekarang']."</option>";
$optper='';
while($bar=mysql_fetch_object($res))
{
    $optper.="<option value='".$bar->periode."'>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</option>";
}	

//get revisi available
//$str="select distinct revisi from ".$dbname.".keu_jurnalht
//      order by revisi";	  
//$res=mysql_query($str);
//#$optper="<option value=''>".$_SESSION['lang']['sekarang']."</option>";
//$optrev="";
//while($bar=mysql_fetch_object($res))
//{
    $optrev.="<option value='0'>0</option>";
    $optrev.="<option value='1'>1</option>";
    $optrev.="<option value='2'>2</option>";
    $optrev.="<option value='3'>3</option>";
    $optrev.="<option value='4'>4</option>";    
    $optrev.="<option value='5'>5</option>";     
//}
	

if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{   
    //=================ambil PT;  
    $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
        where tipe='PT'
        order by namaorganisasi";
    $res=mysql_query($str);
    $optpt="";
    while($bar=mysql_fetch_object($res))
    {
        $optpt.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
    }

    //=================ambil gudang;  
    $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
        where length(kodeorganisasi)=4";
    $res=mysql_query($str);
    $optgudang="<option value=''>".$_SESSION['lang']['all']."</option>";
    while($bar=mysql_fetch_object($res))
    {
        $optgudang.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
    }
}
else
{ 
    $optpt="";
    $optpt.="<option value='".$_SESSION['empl']['kodeorganisasi']."'>". $_SESSION['empl']['kodeorganisasi']."</option>";
    $optgudang.="<option value='".$_SESSION['empl']['lokasitugas']."'>".$_SESSION['empl']['lokasitugas']."</option>";   
}

$optper1="<option value='akhir'>".$_SESSION['lang']['akhirtahun']."</option>";
$optper1.="<option value='lalu'>".$_SESSION['lang']['tahunlalu']."</option>";

#print_r($optgudang);
#exit;

echo"<fieldset>
    <legend>".$_SESSION['lang']['neraca']."</legend>
        ".$_SESSION['lang']['pt']." : "."<select id=pt style='width:200px;' onchange=ambilAnak(this.options[this.selectedIndex].value)>".$optpt."</select>
        ".$_SESSION['lang']['']."<select id=gudang style='width:150px;'>".$optgudang."</select>
        ".$_SESSION['lang']['periode']." : "."<select id=periode onchange=hideById('printPanel')>".$optper."</select>
         : "."<select id=periode1 onchange=hideById('printPanel')>".$optper1."</select>
        ".$_SESSION['lang']['revisi']." : "."<select id=revisi onchange=hideById('printPanel')>".$optrev."</select>     
        <button class=mybutton onclick=getLaporanNeraca()>".$_SESSION['lang']['proses']."</button>
    </fieldset>";
CLOSE_BOX();
OPEN_BOX('','Result:');
echo"<span id=printPanel style='display:none;'>
        <img onclick=fisikKeExcel(event,'keu_laporanNeraca_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
        <img onclick=fisikKePDF(event,'keu_laporanNeraca_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>
    </span>    
    <div id=container style='width:100%;height:359px;overflow:scroll;'>
    </div>";
CLOSE_BOX();
close_body();
?>