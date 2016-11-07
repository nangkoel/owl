<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/zPosting.php');

$param = $_POST;
$tahunbulan = implode("",explode('-',$param['periode']));
/** Controller **/
#======================== Sum dari Jurnal Detail ==============================
$qJurnal = selectQuery($dbname,'keu_jurnaldt_vw','sum(jumlah) as jumlah',
    "substr(nojurnal,10,4)='".$param['kodeorg'].
    "' and left(tanggal,7)='".$param['periode'].
    "' and left(noakun,1)='6'");
$resJurnal = fetchData($qJurnal);

if($resJurnal[0]['jumlah']!=0) {
    echo 'asssss';
} else {
    $header1 = array(
        $_SESSION['lang']['noakun'],
        $_SESSION['lang']['jumlah']
    );
    $cols1 = explode(',',"noakun,jumlah");
    
    # Data
    $data1 = array();
    
    # Set Table
    $tab1 = new zPosting('biayaUmum',null,$cols1,$header1,$data1);
    $tab1->_title = 'Alokasi Biaya Umum';
}
#======================== /Penerimaan Barang ==================================

/** View **/
$tab1->render();
?>