<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body(); 
?>
<script language=javascript1.2 src="js/keu_laporan.js"></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['neracasaldo']).'</b>');

//get existing period
$str="select distinct periode as periode from ".$dbname.".setup_periodeakuntansi
      order by periode desc";	  
$res=mysql_query($str);
#$optper="<option value=''>".$_SESSION['lang']['sekarang']."</option>";
$optper="";
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
    $optpt.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
    while($bar=mysql_fetch_object($res))
    {
        $optpt.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
    }

    //=================ambil gudang;  
    $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
        where (tipe='KEBUN' or tipe='PABRIK' or tipe='KANWIL'
        or tipe='HOLDING')  and induk!=''
        ";
    $res=mysql_query($str);
    $optgudang="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
    while($bar=mysql_fetch_object($res))
    {
//        $optgudang.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
    }
}
else
if($_SESSION['empl']['tipelokasitugas']=='KANWIL')
{   
    //=================ambil PT;  
    $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
        where kodeorganisasi='".$_SESSION['empl']['kodeorganisasi']."'
        order by namaorganisasi";
    $res=mysql_query($str);
    $optpt="";
    $optpt.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
    while($bar=mysql_fetch_object($res))
    {
        $optpt.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
    }

    //=================ambil gudang;  
    $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
        where (tipe='KEBUN' or tipe='PABRIK' or tipe='KANWIL')  and induk!=''
        ";
    $res=mysql_query($str);
    $optgudang="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
    while($bar=mysql_fetch_object($res))
    {
//        $optgudang.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
    }
}
else    
{
    $optpt="";
    $optpt.="<option value='".$_SESSION['empl']['kodeorganisasi']."'>". $_SESSION['empl']['kodeorganisasi']."</option>";
    $optgudang.="<option value='".$_SESSION['empl']['lokasitugas']."'>".$_SESSION['empl']['lokasitugas']."</option>";   
}

echo"<fieldset>
    <legend>".$_SESSION['lang']['neracasaldo']."</legend>
    ".$_SESSION['lang']['pt']." : "."<select id=pt style='width:200px;'  onchange=ambilAnakBB(this.options[this.selectedIndex].value)>".$optpt."</select>
    ".$_SESSION['lang']['']."<select id=gudang style='width:150px;' onchange=hideById('printPanel')>".$optgudang."</select>
    ".$_SESSION['lang']['periode']." : "."<select id=periode onchange=hideById('printPanel')>".$optper."</select>
    ".$_SESSION['lang']['tglcutisampai']."
    ".$_SESSION['lang']['periode']." : "."<select id=periode1 onchange=hideById('printPanel')>".$optper."</select>
    ".$_SESSION['lang']['revisi']." : "."<select id=revisi onchange=hideById('printPanel')>".$optrev."</select>
    <button class=mybutton onclick=getLaporanBukuBesar()>".$_SESSION['lang']['proses']."</button>
</fieldset>";
CLOSE_BOX();
OPEN_BOX('','Result:');
echo"<span id=printPanel style='display:none;'>
        <img onclick=fisikKeExcel(event,'keu_laporanBukuBesar_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
        <img onclick=fisikKePDF(event,'keu_laporanBukuBesar_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>
    </span>  
    <div style='width:1100px;display:fixed''>
    <table class=sortable cellspacing=1 border=0 width=1080px>
    <thead>
    <tr>
        <td align=center style='width:50px;'>".$_SESSION['lang']['nomor']."</td>
        <td align=center style='width:80px;'>".$_SESSION['lang']['noakun']."</td>
        <td align=center style='width:430px;'>".$_SESSION['lang']['namaakun']."</td>
        <td align=center style='width:130px;'>".$_SESSION['lang']['saldoawal']."</td>
        <td align=center style='width:130px;'>".$_SESSION['lang']['debet']."</td>
        <td align=center style='width:130px;'>".$_SESSION['lang']['kredit']."</td>
        <td align=center style='width:130px;'>".$_SESSION['lang']['saldoakhir']."</td>
    </tr>  
    </thead>
    <tbody>
    </tbody>
    <tfoot>
    </tfoot>		 
    </table>
    </div>         
    <div style='width:1100px;height:359px;overflow:scroll;'>
    <table class=sortable cellspacing=1 border=0 width=1080px style='display:fixed'>
    <thead>
    </thead>
    <tbody id=container>
    </tbody>
    <tfoot>
    </tfoot>		 
    </table>
    </div>";
CLOSE_BOX();
close_body();
?>