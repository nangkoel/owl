<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zMysql.php');
echo open_body();

//INSERT INTO `bahasa` (`legend`, `ID`, `location`, `idx`, `MY`, `EN`, `TH`) VALUES ('topik', 'Topik', 'SDM', NULL, 'Topik', 'Topic', 'Topic'), ('remark', 'Remark', 'SDM', NULL, 'Remark', 'Remark', 'Remark'), ('kategori', 'Kategori', 'SDM', NULL, 'Kategori', 'Category', 'Category');
//INSERT INTO `bahasa` (`legend`, `ID`, `location`, `idx`, `MY`, `EN`, `TH`) VALUES ('matrikstraining', 'Matriks Training', 'SDM', NULL, 'Matriks Training', 'Training Matrix', 'Training Matrix');

//ALTER TABLE `sdm_matriktraining` ADD `catatan` INT( 45 ) NULL 
//ALTER TABLE `sdm_matriktraining` CHANGE `updateby` `updateby` INT( 10 ) UNSIGNED ZEROFILL NOT NULL COMMENT 'id karyawan yang update'
//
//ALTER TABLE `sdm_matriktraining` DROP PRIMARY KEY ,
//ADD PRIMARY KEY ( `karyawanid` , `matrikxid` ) 

?>

<script language=javascript1.2 src='js/sdm_matrixTraining.js'></script>

<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['matrikstraining']);

$sJabat="select distinct * from ".$dbname.".sdm_5jabatan where 1";
$qJabat=mysql_query($sJabat) or die(mysql_error());
while($rJabat=mysql_fetch_assoc($qJabat))
{
    $kamusJabat[$rJabat['kodejabatan']]=$rJabat['namajabatan'];
}

$sJabat="select distinct * from ".$dbname.".datakaryawan where tipekaryawan = 0";
$qJabat=mysql_query($sJabat) or die(mysql_error());
while($rJabat=mysql_fetch_assoc($qJabat))
{
    $kamusNama[$rJabat['karyawanid']]=$rJabat['namakaryawan'];
    $kamusJabatan[$rJabat['karyawanid']]=$rJabat['kodejabatan'];
    $kamusLokasi[$rJabat['karyawanid']]=$rJabat['lokasitugas'];
    $kamusDept[$rJabat['karyawanid']]=$rJabat['bagian'];
}

//
//$arrKateg=getEnum($dbname,'sdm_5matriktraining','kategori');
//$optKateg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
//foreach($arrKateg as $kei=>$fal)
//{
//    $optKateg.="<option value='".$kei."'>".$fal."</option>";
//} 	 

$optJenis="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sJenis="select * from ".$dbname.". sdm_5matriktraining order by kategori, topik asc";
$qJenis=mysql_query($sJenis) or die(mysql_error());
while($rJenis=mysql_fetch_assoc($qJenis))
{
//    $jenis=$rJenis['kategori'].'##'.$rJenis['topik'];
    $optJenis.="<option value='".$rJenis['matrixid']."'>".$rJenis['kategori'].' - '.$rJenis['topik']."</option>";
}
 
echo"<fieldset style='width:700px;'>
    <table>
    <tr>
        <td>Jenis Training</td>
        <td><select id=matrixid onchange=pilihkaryawan()>".$optJenis."</select></td>
    </tr>
    <tr>
        <td>".$_SESSION['lang']['tanggalmulai']."</td>
        <td><input id=\"tanggal1\" name=\"tanggal1\" class=\"myinputtext\" onkeypress=\"return tanpa_kutip(event)\"  style=\"width:100px\" readonly=\"readonly\" onmousemove=\"setCalendar(this.id)\" type=\"text\"></td>
    </tr>
    <tr>
        <td>".$_SESSION['lang']['tanggalsampai']."</td>
        <td><input id=\"tanggal2\" name=\"tanggal2\" class=\"myinputtext\" onkeypress=\"return tanpa_kutip(event)\"  style=\"width:100px\" readonly=\"readonly\" onmousemove=\"setCalendar(this.id)\" type=\"text\"></td>
    </tr>
    <tr>
        <td>".$_SESSION['lang']['karyawan']."</td>
        <td><div id=container></div></td>
    </tr>
    </table>
    </fieldset>";

echo open_theme($_SESSION['lang']['list']);
echo "<div id=icontainer>";

$str1="select * from ".$dbname.".sdm_matriktraining where 1 order by karyawanid";
$res1=mysql_query($str1);
echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
     <thead>
     <tr class=rowheader>
        <td>".$_SESSION['lang']['namakaryawan']."</td>
        <td>".$_SESSION['lang']['lokasitugas']."</td>
        <td>".$_SESSION['lang']['departemen']."</td>
        <td>".$_SESSION['lang']['jabatan']."</td>
        <td width=100>".$_SESSION['lang']['action']."</td>
     </tr></thead>
     <tbody>";
$no=0;
while($bar1=mysql_fetch_object($res1))
{ 
    $no+=1;
    echo"<tr class=rowcontent>
        <td>".$kamusNama[$bar1->karyawanid]."</td>
        <td>".$kamusLokasi[$bar1->karyawanid]."</td>
        <td>".$kamusDept[$bar1->karyawanid]."</td>
        <td>".$kamusJabat[$kamusJabatan[$bar1->karyawanid]]."</td>
        <td align=center>
            <button class=mybutton onclick=\"lihatpdf(event,'sdm_slave_matrixTraining.php','".$bar1->karyawanid."');\">".$_SESSION['lang']['pdf']."</button>
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