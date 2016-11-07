<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zMysql.php');
echo open_body();

//INSERT INTO `owlv2`.`bahasa` (`legend`, `ID`, `location`, `idx`, `MY`, `EN`, `TH`) VALUES ('kriteria', 'Kriteria', 'SDM', NULL, 'Kriteria', 'Criteria', 'Criteria'), ('psikologi', 'Psikologi', 'SDM', NULL, 'Psikologi', 'Psychology', 'Psychology');
?>

<script language=javascript1.2 src='js/sdm_5kriteriaPsy.js'></script>

<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['kriteria'].' '.$_SESSION['lang']['psikologi']);

$optJabat="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sJabat="select distinct * from ".$dbname.".sdm_5jabatan order by kodejabatan asc";
$qJabat=mysql_query($sJabat) or die(mysql_error());
while($rJabat=mysql_fetch_assoc($qJabat))
{
    $kamusJabat[$rJabat['kodejabatan']]=$rJabat['namajabatan'];
    $optJabat.="<option value='".$rJabat['kodejabatan']."'>".$rJabat['namajabatan']."</option>";
}

$arrKrite=getEnum($dbname,'sdm_5kriteriapsy','kriteria');
$optKrite="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
foreach($arrKrite as $kei=>$fal)
{
    $optKrite.="<option value='".$kei."'>".$fal."</option>";
} 	 


echo"<fieldset style='width:500px;'>
    <table>
    <tr>
        <td>".$_SESSION['lang']['kodejabatan']."</td>
        <td><select id=jabatan>".$optJabat."</select></td>
    </tr>
    <tr>
        <td>".$_SESSION['lang']['kriteria']."</td>
        <td><select id=kriteria>".$optKrite."</select></td>
    </tr>
    <tr>
        <td>".$_SESSION['lang']['deskripsi']."</td>
        <td><textarea rows=2 cols=22 id=deskripsi onkeypress=\"return tanpa_kutip();\"></textarea></td>
    </tr>
    </table>
    <input type=hidden id=method value='insert'>
    <button class=mybutton onclick=simpan()>".$_SESSION['lang']['save']."</button>
    <button class=mybutton onclick=cancel()>".$_SESSION['lang']['cancel']."</button>
    </fieldset>";

echo open_theme($_SESSION['lang']['list']);
echo "<div id=container>";
echo "<table><tr>
        <td>".$_SESSION['lang']['kodejabatan']."</td>
        <td><select id=jabatan2 onchange=pilihjabatan()>".$optJabat."</select> <img class=\"resicon\" src=\"images/pdf.jpg\" title=\"PDF\" onclick=\"lihatpdf(event,'sdm_slave_5kriteriaPsy')\"></td>
    </tr></table>";

$str1="select * from ".$dbname.". sdm_5kriteriapsy order by kodejabatan, kriteria";
$res1=mysql_query($str1);
echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
     <thead>
     <tr class=rowheader>
        <td>".$_SESSION['lang']['nourut']."</td>
        <td>".$_SESSION['lang']['jabatan']."</td>
        <td>".$_SESSION['lang']['kriteria']."</td>
        <td>".$_SESSION['lang']['deskripsi']."</td>
        <td width=100>".$_SESSION['lang']['action']."</td>
     </tr></thead>
     <tbody>";
$no=0;
while($bar1=mysql_fetch_object($res1))
{
    $no+=1;
    echo"<tr class=rowcontent>
        <td align=right>".$no."</td>
        <td>".$kamusJabat[$bar1->kodejabatan]."</td>
        <td>".$bar1->kriteria."</td>
        <td>".substr(str_replace("\n", "</br>",$bar1->penjelasan),0,75)."</td>
        <td align=center>
            <img src=images/application/application_view_list.png class=resicon  caption='Preview' onclick=\"lihat('".$bar1->kodejabatan."','".$bar1->kriteria."',event);\">
            <img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodejabatan."','".$bar1->kriteria."','".str_replace("\n", "\\n",$bar1->penjelasan)."');\">
            <img src=images/application/application_delete.png class=resicon  caption='Edit' onclick=\"hapus('".$bar1->kodejabatan."','".$bar1->kriteria."');\">
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