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

<script language=javascript1.2 src='js/sdm_matrixKompetensi.js'></script>

<?php
include('master_mainMenu.php');
OPEN_BOX('','Matrix '.$_SESSION['lang']['kompetensi']);

$optJabat="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sJabat="select distinct * from ".$dbname.".sdm_5jabatan order by kodejabatan asc";
$qJabat=mysql_query($sJabat) or die(mysql_error());
while($rJabat=mysql_fetch_assoc($qJabat))
{
    $kamusJabat[$rJabat['kodejabatan']]=$rJabat['namajabatan'];
    $optJabat.="<option value='".$rJabat['kodejabatan']."'>".$rJabat['namajabatan']."</option>";
}
//
//$arrKateg=getEnum($dbname,'sdm_5matriktraining','kategori');
//$optKateg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
//foreach($arrKateg as $kei=>$fal)
//{
//    $optKateg.="<option value='".$kei."'>".$fal."</option>";
//} 	 

//$optJenis="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
//$sJenis="select * from ".$dbname.". sdm_5matriktraining order by kategori, topik asc";
//$qJenis=mysql_query($sJenis) or die(mysql_error());
//while($rJenis=mysql_fetch_assoc($qJenis))
//{
////    $jenis=$rJenis['kategori'].'##'.$rJenis['topik'];
//    $optJenis.="<option value='".$rJenis['matrixid']."'>".$rJenis['kategori'].' - '.$rJenis['topik']."</option>";
//}

echo"<fieldset style='width:500px;'>
    <table>
    <tr>
        <td>".$_SESSION['lang']['jabatan']."</td>
        <td><select id=jabatan>".$optJabat."</select></td>
        <td><button class=mybutton onclick=\"lihatpdf(event,'sdm_slave_matrixKompetensi.php');\">".$_SESSION['lang']['pdf']."</button></td>
    </tr>
    </table>
    </fieldset>";


echo close_theme();
CLOSE_BOX();
echo close_body();
?>