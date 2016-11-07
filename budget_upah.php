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
<script type="text/javascript" src="js/budget_upah.js"></script>
<?php
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

    //=================ambil unit;  
    $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
                    where (tipe='KEBUN' or tipe='PABRIK' or tipe='KANWIL'
                    or tipe='HOLDING')  and induk!=''
                    ";
    $res=mysql_query($str);
//        $optgudang="<option value=''>".$_SESSION['lang']['all']."</option>";
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

//atas
OPEN_BOX('',"<b>".$_SESSION['lang']['upahharian']."</b>");
//box dalam tab 0
$frm[0].="<fieldset><legend>".$_SESSION['lang']['upahkerja']."".$thn_skrg."</legend>";
$frm[0].="<table cellspacing=1 border=0>
    <tr><td>".$_SESSION['lang']['budgetyear']." </td><td>:</td><td>
    <input type=text class=myinputtext onkeyup=\"resetcontainer();\" id=tahunbudget name=tahunbudget onkeypress=\"return angka_doang(event);\" maxlength=4 style=width:150px; /></td></tr>
    <tr><td>".$_SESSION['lang']['kodeorg']."</td><td>:</td><td>
    <select id=kodeorg onchange=\"resetcontainer();\" name=kodeorg style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata']."</option>".$optgudang."</select></td></tr>
    <tr><td colspan=3>
    <button class=mybutton id=proses name=proses onclick=prosesUpah()>".$_SESSION['lang']['proses']."</button>
    <input type=hidden id=tersembunyi name=tersembunyi value=tersembunyi >
    </td></tr></table>";
$frm[0].="</fieldset>";

//box bawah di dalam tab 0
//box bawahnya di dalam tab 0
$frm[0].="<fieldset><legend>".$_SESSION['lang']['list']."</legend>    
<div id=container></div>
    ";
$frm[0].="</fieldset>";

//Ambil data yang sudah ada, buat list daftar tahun budget
$str="select tahunbudget from ".$dbname.".bgt_upah
    where kodeorg = '".substr($_SESSION['empl']['lokasitugas'],0,4)."'
          group by tahunbudget order by tahunbudget";
$res=mysql_query($str);
$opttahun="";
while($bar=mysql_fetch_object($res))
{
    $opttahun.="<option value='".$bar->tahunbudget."'>".$bar->tahunbudget."</option>";
}

//box atas, tab 1
$frm[1].="<fieldset><legend>".$_SESSION['lang']['close']."</legend>";
//isi box atas, tab 1
$frm[1].="<table cellspacing=1 border=0><thead>
    </thead>
    <tr><td>".$_SESSION['lang']['budgetyear']."</td><td>:</td><td>
    <select id=tahunbudget2 onchange=\"resetcontainer2();\" name=tahunbudget2 style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata']."</option>".$opttahun."</select></td></tr>
    <tr><td>".$_SESSION['lang']['kodeorg']."</td><td>:</td><td><label id=kodeorg2>
    ".substr($_SESSION['empl']['lokasitugas'],0,4)."</td></tr>
    <tr><td colspan=3>
    <button class=mybutton id=proses name=proses onclick=prosesTutupUpah()>".$_SESSION['lang']['proses']."</button>
    <input type=hidden id=proses_pekerjaan name=proses_pekerjaan value=insert_pekerjaan />
</table>";
$frm[1].="</fieldset>";
//box bawah, tab 1
//$frm[1].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend><table cellspacing=1 border=0 class=sortable>
//    <thead>
//    <tr class=\"rowheader\">
//    <td>No.</td>
//    <td>".$_SESSION['lang']['kodeorg']."</td>
//    <td>".$_SESSION['lang']['kodegolongan']."</td>
//    <td>".$_SESSION['lang']['levelname']."</td>
//    <td>".$_SESSION['lang']['upahkerja']."</td>
//    </tr></thead><tbody id=container2>
//    <script>load_data()</script>
//    ";
$frm[1].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>
<div id=container2></div>    
    ";
$frm[1].="</fieldset>";


//========================
//tab title
$hfrm[0]=$_SESSION['lang']['upahharian'];
$hfrm[1]=$_SESSION['lang']['tutup'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,100,900);
//===============================================	
?>

<?php
CLOSE_BOX();
echo close_body();
?>