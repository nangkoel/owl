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
<script type="text/javascript" src="js/bgt_departemen.js"></script>
<?php


    $optdepartemen="";
    $optdepartemen.="<option value='".$_SESSION['empl']['bagian']."'>".$_SESSION['empl']['bagian']."</option>";

//pilihan alokasi
    $str="select kodeorganisasi, namaorganisasi from ".$dbname.".organisasi
        where length(kodeorganisasi) = 4 order by kodeorganisasi
        ";
    $optalokasi="";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $optalokasi.="<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi."</option>";
    }

//pilihan tahunbudget
    $str="select distinct tahunbudget from ".$dbname.".bgt_dept
        where departemen = '".$_SESSION['empl']['bagian']."'
            order by tahunbudget desc
        ";
    $opttahunbudget="";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $opttahunbudget.="<option value='".$bar->tahunbudget."'>".$bar->tahunbudget."</option>";
    }
    
//pilihan kodeakun    
     if($_SESSION['language']=='EN'){
        $dd='namaakun1 as namaakun';
    }else{
        $dd='namaakun as namaakun';
    }   
    $str="select noakun,".$dd." from ".$dbname.".keu_5akun
                    where detail=1 order by noakun
                    ";
    $optnoakun="";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
            $optnoakun.="<option value='".$bar->noakun."'>".$bar->noakun." - ".$bar->namaakun."</option>";
            $noakun[$bar->noakun]=$bar->namaakun;
    }
    
//atas
OPEN_BOX('',"<b>".$_SESSION['lang']['budget']." ".$_SESSION['lang']['departemen']."</b>");
echo"<table cellspacing=1 border=0>
    <tr><td>".$_SESSION['lang']['budgetyear']."</td><td>:</td><td>
        <input type=text class=myinputtext id=tahunbudget name=tahunbudget onkeypress=\"return angka_doang(event);\" maxlength=4 style=width:150px; /></td></tr>
    <tr><td>".$_SESSION['lang']['departemen']."</td><td>:</td><td>
        <select id=departemen name=departemen style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata']."</option>".$optdepartemen."</select></td></tr>
    <tr><td>".$_SESSION['lang']['noakun']."</td><td>:</td><td>
        <select id=noakun name=noakun style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata']."</option>".$optnoakun."</select></td></tr>
    <tr><td>".$_SESSION['lang']['keterangan']." </td><td>:</td><td>
        <input type=text class=myinputtext id=keterangan name=keterangan maxlength=100 style=width:150px; value=\"".$keterangan."\"/></td></tr>

   <tr><td>".$_SESSION['lang']['fisik']." </td><td>:</td><td>
        <input type=text class=myinputtextnumber id=fisik name=fisik  style=width:150px; value='0' okeypress=\"return angka_doang(event);\"></td></tr>
            
   <tr><td>".$_SESSION['lang']['satuan']." </td><td>:</td><td>
        <input type=text class=myinputtext id=satuanf name=satuanf maxlength=10 style=width:150px; onkeypress=\"return tanpa_kutip(event);\"></td></tr>

    <tr><td>".$_SESSION['lang']['alokasibiaya']."</td><td>:</td><td>
        <select name=alokasi id=alokasi style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata']."</option>".$optalokasi."</select></td></tr>
    <tr><td>".$_SESSION['lang']['jumlahpertahun']." </td><td>:</td><td>
        <input type=text class=myinputtext id=jumlahbiaya name=jumlahbiaya onkeypress=\"return angka_doang(event);\" maxlength=20 style=width:150px; />(Rp)</td></tr>


    <tr><td colspan=3>
        <button class=mybutton id=simpan name=simpan onclick=simpan()>".$_SESSION['lang']['save']."</button>
        <input type=hidden id=tersembunyi name=tersembunyi value=tersembunyi >
    </td></tr></table><br>";

$frm[0].="<fieldset id=tab0><legend>".$_SESSION['lang']['list']."</legend>
    <div style='overflow:scroll;width:900px;height:300px'; id=container0>";
    $frm[0].=" ".$_SESSION['lang']['budget']." ".$_SESSION['lang']['departemen']." : ".$_SESSION['empl']['bagian']." --- ";
    $frm[0].=$_SESSION['lang']['budgetyear']." : <select name=pilihtahun0 id=pilihtahun0 onchange=\"updateTab();\"><option value=''>".$_SESSION['lang']['all']."</option>".$opttahunbudget."</select>";


    $frm[0].="<table id=container6 class=sortable cellspacing=1 border=0 style='width:3000px;'>
     <thead>
        <tr>
            <td align=center>".substr($_SESSION['lang']['nomor'],0,2)."</td>
            <td align=center>".$_SESSION['lang']['budgetyear']."</td>
            <td align=center>".$_SESSION['lang']['namaakun']."</td>
            <td align=center>".$_SESSION['lang']['keterangan']."</td>
            <td align=center>".$_SESSION['lang']['alokasibiaya']."</td>
            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>
            <td align=center>".substr($_SESSION['lang']['jan'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['peb'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['mar'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['apr'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['mei'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['jun'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['jul'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['agt'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['sep'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['okt'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['nov'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['dec'],0,3)."</td>
            <td align=center>".$_SESSION['lang']['action']."</td>
       </tr>  
     </thead>
     <tbody><script>updateTab()</script>";
 

    $frm[0].= "</tbody>
     <tfoot>
     </tfoot>		 
     </table>
    </div>";
$frm[0].="</fieldset>";
//$optThnTtp="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

$frm[1].="<fieldset><legend>".$_SESSION['lang']['tutup']."</legend>
    <div><table><tr><td>".$_SESSION['lang']['budgetyear']."</td><td><select id='thnBudgetTutup' style='width:150px'>".$optThnTtp."</select></td></tr>";
$frm[1].="<tr><td colspan=2 align=center><button class=\"mybutton\"  id=\"saveData\" onclick='closeBudget()'>".$_SESSION['lang']['tutup']."</button></td></tr></table>";
$frm[1].="</div></fieldset>";
//========================
//tab title

$hfrm[0]=$_SESSION['lang']['list'];
$hfrm[1]=$_SESSION['lang']['tutup'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,100,900);
//===============================================	
?>

<?php
CLOSE_BOX();

echo close_body();
?>