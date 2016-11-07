<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zFunction.php');
echo open_body();
include('master_mainMenu.php');
$frm[0]='';
$frm[1]='';

?>
<script language="javascript" src="js/zMaster.js"></script>
<script type="text/javascript" src="js/bgt_laporan_harga_barang.js"></script>
<?php

//pilihan workshop
    $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
        where (tipe='WORKSHOP') and kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%'
        ";
    $optws="";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $optws.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
    }

//pilihan kodebudget tab0
    $str="select kodebudget,nama from ".$dbname.".bgt_kode
        where kodebudget like 'SDM%'
        ";
    $optkodebudget0="";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $optkodebudget0.="<option value='".$bar->kodebudget."'>".$bar->nama."</option>";
    }

//pilihan kodebudget tab1
    $str="select kodebudget,nama from ".$dbname.".bgt_kode
        where kodebudget like 'M%'
        ";
    $optmaterial1="";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $optmaterial1.="<option value='".$bar->kodebudget."'>".$bar->nama."</option>";
    }
    
//pilihan kodebudget tab2    
    $str="select kodebudget,nama from ".$dbname.".bgt_kode
        where kodebudget like 'TOOL%'
        ";
    $opttool2="";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $opttool2.="<option value='".$bar->kodebudget."'>".$bar->nama."</option>";
    }
    
//pilihan kodebudget tab3    
    $str="select kodebudget,nama from ".$dbname.".bgt_kode
                    where kodebudget like 'TRANSIT%'
                    ";
    $opttransit3="";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
            $opttransit3.="<option value='".$bar->kodebudget."'>".$bar->nama."</option>";
    }

//pilihan kodeakun tab3    
    $str="select noakun,namaakun from ".$dbname.".keu_5akun
                    where detail=1 and tipeakun = 'Biaya' order by noakun
                    ";
    $optakun3="";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
            $optakun3.="<option value='".$bar->noakun."'>".$bar->noakun." - ".$bar->namaakun."</option>";
    }
    
//pilihan tahunbudget tab0    
    $str="select distinct tahunbudget from ".$dbname.".bgt_budget 
                    order by tahunbudget desc
                    ";
    $opttahunbudget0="";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
            $opttahunbudget0.="<option value='".$bar->tahunbudget."'>".$bar->tahunbudget."</option>";
    }
    
//pilihan regional tab0    
    $str="select regional, nama from ".$dbname.".bgt_regional
                    order by nama desc
                    ";
    $optregional0="";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
            $optregional0.="<option value='".$bar->regional."'>".$bar->nama."</option>";
    }
    
//pilihan kelompok tab0    
    $str="select kode, kelompok from ".$dbname.".log_5klbarang
                    order by kode 
                    ";
    $optkelompokbarang0="";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
            $optkelompokbarang0.="<option value='".$bar->kode."'>".$bar->kode." - ".$bar->kelompok."</option>";
    }
    
//atas
OPEN_BOX('',"<b>".$_SESSION['lang']['hargabarang']."</b>");

//tab0
$frm[0].="<fieldset id=tab0><legend>per ".$_SESSION['lang']['kelompokbarang']."</legend>";
$frm[0].="<table cellspacing=1 border=0>
    <tr><td>".$_SESSION['lang']['budgetyear']."</td><td>:</td><td>
        <select id=tahunbudget0 name=tahunbudget0 style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata']."</option>".$opttahunbudget0."</select></td></tr>
    <tr><td>".$_SESSION['lang']['regional']." </td><td>:</td><td>
        <select id=regional0 name=regional0 style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata']."</option>".$optregional0."</select></td></tr>
    <tr><td>".$_SESSION['lang']['kelompokbarang']." </td><td>:</td><td>
        <select id=kelompokbarang0 name=kelompokbarang0 style='width:150px;'><option value=''>".$_SESSION['lang']['all']."</option>".$optkelompokbarang0."</select></td></tr>
    <tr><td colspan=3>
        <button class=mybutton id=proses0 name=proses0 onclick=proses0()>".$_SESSION['lang']['proses']."</button>
        <input type=hidden id=tersembunyi0 name=tersembunyi0 value=tersembunyi >
    </td></tr></table>";
$frm[0].="</fieldset>";
//box dalam tab0, daftar table
$frm[0].="<fieldset><legend>".$_SESSION['lang']['list']."</legend>    
<div id=container0></div>
    ";
$frm[0].="</fieldset>";

//tab 1
$frm[1].="<fieldset id=tab1><legend>".$_SESSION['lang']['caribarang']."</legend>";
$frm[1].="<table cellspacing=1 border=0>
    <tr><td>".$_SESSION['lang']['budgetyear']."</td><td>:</td><td>
        <select id=tahunbudget1 name=tahunbudget1 style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata']."</option>".$opttahunbudget0."</select></td></tr>
    <tr><td>".$_SESSION['lang']['regional']." </td><td>:</td><td>
        <select id=regional1 name=regional1 style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata']."</option>".$optregional0."</select></td></tr>
    <tr><td>".$_SESSION['lang']['namabarang']." </td><td>:</td><td>
        <input type=text class=myinputtext id=namabarang1 name=namabarang1 maxlength=10 style=width:150px;/>
    <tr><td colspan=3>
        <button class=mybutton id=proses1 name=proses1 onclick=proses1()>".$_SESSION['lang']['proses']."</button>
        <input type=hidden id=tersembunyi1 name=tersembunyi1 value=tersembunyi >
    </td></tr></table>";
$frm[1].="</fieldset>";
//box dalam tab0, daftar table
$frm[1].="<fieldset><legend>".$_SESSION['lang']['list']."</legend>    
<div id=container1></div>
    ";
$frm[1].="</fieldset>";

//========================
//tab title
$hfrm[0]='per '.$_SESSION['lang']['kelompokbarang'];
$hfrm[1]=$_SESSION['lang']['caribarang'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,100,900);
//===============================================	
?>

<?php
CLOSE_BOX();

echo close_body();
?>