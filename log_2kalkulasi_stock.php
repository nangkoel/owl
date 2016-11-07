<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/log_2kalkulasi_stock.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['mutasi']).'</b>');

//=================ambil unit;  
$str="select distinct kodeorganisasi, namaorganisasi from ".$dbname.".organisasi
      where tipe like 'GUDANG%'
      order by kodeorganisasi"; 
$res=mysql_query($str);
$optunit="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optunit.="<option value='sumatera'>Sumatera (MRKE, SKSE, SOGM, SSRO, WKNE, SOGE, SENE)</option>";
$optunit.="<option value='kalimantan'>Kalimantan (SBME, SBNE, SMLE, SMTE, SSGE, STLE)</option>";
while($bar=mysql_fetch_object($res))
{
    $optunit.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}

//=================ambil periode;  
$str="select distinct SUBSTR(tanggal, 1, 4) as tahun from ".$dbname.".log_transaksiht
      order by tahun"; 
$res=mysql_query($str);
$opttahun="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
while($bar=mysql_fetch_object($res))
{
    $opttahun.="<option value='".$bar->tahun."'>".$bar->tahun."</option>";
}

//=================ambil kelompok; 
if($_SESSION['language']=='EN'){
    $str="select kode, kelompok1 as kelompok from ".$dbname.".log_5klbarang
      order by kode";
}else{
     $str="select kode, kelompok from ".$dbname.".log_5klbarang
      order by kode";
}
$res=mysql_query($str);
$optkelompok="<option value=''>".$_SESSION['lang']['all']."</option>";
while($bar=mysql_fetch_object($res))
{
    $optkelompok.="<option value='".$bar->kode."'>".$bar->kode." - ".$bar->kelompok."</option>";
}

    $optpilih.="<option value='volume'>".$_SESSION['lang']['volume']."</option>";
    $optpilih.="<option value='nilai'>".$_SESSION['lang']['harga']."</option>";


echo"<fieldset>
     <legend>".$_SESSION['lang']['mutasi']."</legend>
     <table cellspacing=1 border=0>
     <tr>
        <td>".$_SESSION['lang']['pilihgudang']."</td>
        <td><select id=unit style='width:150px;'>".$optunit."</select></td>
     </tr>
     <tr>
        <td>".$_SESSION['lang']['periode']."</td>
        <td><select id=tahun style='width:150px;'>".$opttahun."</select></td>
     </tr>
     <tr>
        <td>".$_SESSION['lang']['kelompokbarang']."</td>
        <td><select id=kelompok onchange=clearkobar() style='width:150px;'>".$optkelompok."</select></td>
     </tr>
     <tr>
        <td>".$_SESSION['lang']['kodebarang']."</td>
        <td>
            <input type=\"text\" class=\"myinputtextnumber\" id=\"kodebarang\" name=\"kodebarang\" onkeypress=\"return angka_doang(event);\" value=\"\" maxlength=\"10\" style=\"width:150px;\" disabled=true/>
            <img src=images/search.png class=dellicon title=".$_SESSION['lang']['find']." onclick=\"searchBrg('".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg']."</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=".$key.">',event)\";>
        </td>
     </tr>
     <tr>
        <td>Display</td>
        <td><select id=pilih style='width:150px;'>".$optpilih."</select></td>
     </tr>
     <tr>
        <td>Per Mayor</td>
        <td><input type=\"checkbox\" name=mayor id=mayor value=\"Major\" onclick=pilihMayor()></td>
     </tr>
     <tr>
        <td colspan=2><button class=mybutton onclick=getTransaksiGudang()>".$_SESSION['lang']['proses']."</button></td>
     </tr></table>
    </fieldset>";
CLOSE_BOX();
OPEN_BOX('','Result:');
echo"<span id=printPanel style='display:none;'>
        <img onclick=rekalkulasiStockKeExcel(event,'log_slave_2kalkulasi_stock.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
     </span>    
     <div style='width:100%;height:359px;overflow:scroll;'>
        <table class=sortable cellspacing=1 border=0 width=100% id=container2></table>
     </div>";
CLOSE_BOX();
close_body();
?>