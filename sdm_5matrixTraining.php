<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zMysql.php');
echo open_body();

//INSERT INTO `bahasa` (`legend`, `ID`, `location`, `idx`, `MY`, `EN`, `TH`) VALUES ('topik', 'Topik', 'SDM', NULL, 'Topik', 'Topic', 'Topic'), ('remark', 'Remark', 'SDM', NULL, 'Remark', 'Remark', 'Remark'), ('kategori', 'Kategori', 'SDM', NULL, 'Kategori', 'Category', 'Category');
//INSERT INTO `bahasa` (`legend`, `ID`, `location`, `idx`, `MY`, `EN`, `TH`) VALUES ('matrikstraining', 'Matriks Training', 'SDM', NULL, 'Matriks Training', 'Training Matrix', 'Training Matrix');
?>

<script language=javascript1.2 src='js/sdm_5matrixTraining.js'></script>

<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['matrikstraining']);

$optJabat="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sJabat="select distinct * from ".$dbname.".sdm_5jabatan order by kodejabatan asc";
$qJabat=mysql_query($sJabat) or die(mysql_error());
while($rJabat=mysql_fetch_assoc($qJabat))
{
    $kamusJabat[$rJabat['kodejabatan']]=$rJabat['namajabatan'];
    $optJabat.="<option value='".$rJabat['kodejabatan']."'>".$rJabat['namajabatan']."</option>";
}

$arrKateg=getEnum($dbname,'sdm_5matriktraining','kategori');
$optKateg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
foreach($arrKateg as $kei=>$fal)
{
    $optKateg.="<option value='".$kei."'>".$fal."</option>";
} 	 


echo"<fieldset style='width:500px;'>
    <table>
    <tr>
        <td>".$_SESSION['lang']['kodejabatan']."</td>
        <td><select id=jabatan>".$optJabat."</select></td>
    </tr>
    <tr>
        <td>".$_SESSION['lang']['kategori']."</td>
        <td><select id=kategori>".$optKateg."</select></td>
    </tr>
    <tr>
        <td>".$_SESSION['lang']['topik']."</td>
        <td><input type=text class=myinputtext id=topik onkeypress=\"return tanpa_kutip(event);\" size=30 maxlength=30></td>
    </tr>
    <tr>
        <td>".$_SESSION['lang']['remark']."</td>
        <td><input type=text class=myinputtext id=remark onkeypress=\"return tanpa_kutip(event);\" size=30 maxlength=30></td>
    </tr>
    </table>
    <input type=hidden id=method value='insert'>
    <input type=hidden id=matrixid value=''>    
    <button class=mybutton onclick=simpan()>".$_SESSION['lang']['save']."</button>
    <button class=mybutton onclick=cancel()>".$_SESSION['lang']['cancel']."</button>
    </fieldset>";

echo open_theme($_SESSION['lang']['list']);
echo "<div id=container>";
echo "<table><tr>
        <td>".$_SESSION['lang']['kodejabatan']."</td>
        <td><select id=jabatan2 onchange=pilihjabatan()>".$optJabat."</select></td>
    </tr></table>";

$str1="select * from ".$dbname.".sdm_5matriktraining order by kodejabatan, kategori, topik";
$res1=mysql_query($str1);
echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
     <thead>
     <tr class=rowheader>
        <td>".$_SESSION['lang']['jabatan']."</td>
        <td>".$_SESSION['lang']['kategori']."</td>
        <td>".$_SESSION['lang']['topik']."</td>
        <td>".$_SESSION['lang']['catatan']."</td>
        <td width=100>".$_SESSION['lang']['action']."</td>
     </tr></thead>
     <tbody>";
$no=0;
while($bar1=mysql_fetch_object($res1))
{ 
    $no+=1;
    echo"<tr class=rowcontent>
        <td>".$kamusJabat[$bar1->kodejabatan]."</td>
        <td>".$bar1->kategori."</td>
        <td>".$bar1->topik."</td>
        <td>".$bar1->catatan."</td>
        <td align=center>
            <img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodejabatan."','".$bar1->kategori."','".$bar1->topik."','".$bar1->catatan."','".$bar1->matrixid."');\">
            <img src=images/application/application_delete.png class=resicon  caption='Edit' onclick=\"hapus('".$bar1->matrixid."');\">
        </td>
    </tr>";
}	 
echo"</tbody>
    <tfoot>
    </tfoot>
    </table>";
echo "</div>";

echo close_theme();
CLOSE_BOX();
echo close_body();
?>