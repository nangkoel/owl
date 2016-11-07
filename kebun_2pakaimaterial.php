<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formReport.php');

/** Controller **/
# Options
if($_SESSION['empl']['tipelokasitugas']=='HOLDING') {
    $optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
        "tipe='KEBUN'");
} else {
    $optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
        "kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
}

$optTahunTanam = makeOption($dbname,'setup_blok','tahuntanam,tahuntanam',
    "left(kodeorg,4)='".$_SESSION['empl']['lokasitugas']."'",'0',true);
$optTahunTanam[''] = $_SESSION['lang']['all'];

// cek bahasa
if($_SESSION['language']=='EN'){
$optKegiatan = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan1,kelompok',
    "kelompok in ('BBT', 'TB', 'TBM', 'TM')",'7',true);
}else{
$optKegiatan = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan,kelompok',
    "kelompok in ('BBT', 'TB', 'TBM', 'TM')",'7',true);
}        
$optKegiatan[''] = $_SESSION['lang']['all'];

$optBarang[''] = $_SESSION['lang']['all'];
$str="select distinct a.kodebarang, b.namabarang from ".$dbname.".kebun_pakaimaterial a 
    left join ".$dbname.".log_5masterbarang b on a.kodebarang = b.kodebarang 
    where a.kodebarang like '3%'
    order by a.kodebarang asc";
$que=mysql_query($str) or die(mysql_error());
while($row=mysql_fetch_assoc($que))
{
    $optBarang[$row['kodebarang']]=$row['kodebarang'].' '.$row['namabarang'];
}

$optISPO['']=$_SESSION['lang']['all'];
$optISPO['1']="ISPO";
$optISPO['0']="Non ISPO";

if(!isset($_SESSION['lang']['lapmaterial'])) {
    $_SESSION['lang']['lapmaterial'] = ucfirst('pakaimaterial');
}
$fReport = new formReport('pakaimaterial','kebun_slave_2pakaimaterial',$_SESSION['lang']['lapmaterial']);
$fReport->addPrime('kodeorg',$_SESSION['lang']['kodeorg'],'','select','L',20,$optOrg);
$fReport->addPrime('periode',$_SESSION['lang']['periode'],'','bulantahun','L',25);
#$fReport->addPrime('tahuntanam',$_SESSION['lang']['tahuntanam'],'','select','L',20,$optTahunTanam);
$fReport->addPrime('kegiatan',$_SESSION['lang']['kegiatan'],'','select','L',20,$optKegiatan);
$fReport->addPrime('barang',$_SESSION['lang']['kodebarang'],'','select','L',20,$optBarang);
$fReport->addPrime('ispo',$_SESSION['lang']['statusISPO'],'','select','L',20,$optISPO);

/** View **/
echo open_body();
?>
<script language="JavaScript1.2" src="js/formReport.js"></script>
<script language="JavaScript1.2" src="js/biReport.js"></script>
<link rel="stylesheet" type="text/css" href="style/zTable.css">
<?php
include('master_mainMenu.php');

OPEN_BOX();
$fReport->render();
CLOSE_BOX();

echo close_body();
?>