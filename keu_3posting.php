<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

#=== Start ===
echo open_body();
?>
<!-- Includes -->
<script language=javascript1.2 src='js/zTools.js'></script>
<script language=javascript1.2 src='js/keu_3posting.js'></script>
<link rel=stylesheet type=text/css href='style/zTable.css'>
<?php
#====== Controller ======
# Options
$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
    "kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
$optPeriod = makeOption($dbname,'setup_periodeakuntansi','periode,periode',"kodeorg='".$_SESSION['empl']['lokasitugas']."'");
//$bulantahun = $_SESSION['org']['period']['tahun']."-".$_SESSION['org']['period']['bulan'];
//$optPeriod = array($bulantahun=>$bulantahun);
//$optJenisData=array('gudang'=>'gudang - (RO,Kebun,PKS)','gaji'=>'Gaji Karyawan Tidak Langsung - (RO,Traksi,Kebun,PKS)','gajiharilibur'=>'Gaji Karyawan Langsung - (Kebun)','alokasi'=>'Alokasi Traksi - (Traksi)','depresiasi'=>'Depresiasi');
if ($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
    if($_SESSION['language']=='EN'){
        $optJenisData=array('gaji'=>'Salaries From General Cost','gajiharilibur'=>'UnAllocated Salaries of Plant Labour','potongan'=>'Deduction Journal','alokasi'=>'Vehicle Running Allocation - (Traksi)','depresiasi'=>'Depreciation');
    }  else  {
        $optJenisData=array('gaji'=>'Gaji Karyawan Tidak Langsung - (RO,Traksi,Kebun,PKS)','gajiharilibur'=>'Gaji Karyawan Langsung - (Kebun)','potongan'=>'Penarikan Potongan Gaji Karyawan','alokasi'=>'Alokasi Traksi - (Traksi)','depresiasi'=>'Depresiasi');
    }
} else {
    if($_SESSION['language']=='EN'){
        $optJenisData=array('gaji'=>'Salaries From General Cost','gajiharilibur'=>'UnAllocated Salaries of Plant Labour','potongan'=>'Deduction Journal','alokasi'=>'Vehicle Running Allocation - (Traksi)');
    }  else  {
        $optJenisData=array('gaji'=>'Gaji Karyawan Tidak Langsung - (RO,Traksi,Kebun,PKS)','gajiharilibur'=>'Gaji Karyawan Langsung - (Kebun)','potongan'=>'Penarikan Potongan Gaji Karyawan','alokasi'=>'Alokasi Traksi - (Traksi)');
    }
}
# Fields
$els = array();
$els[] = array(
  makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
  makeElement('kodeorg','select','',array('style'=>'width:200px'),$optOrg)
);
$els[] = array(
  makeElement('periode','label',$_SESSION['lang']['periode']),
  makeElement('periode','select','',array('style'=>'width:200px'),$optPeriod)
);
$els[] = array(
  makeElement('jenisData','label',$_SESSION['lang']['jenisbiaya']),
  makeElement('jenisData','select','',array('style'=>'width:200px'),$optJenisData)
);

# Button
$els['btn'] = array(
  makeElement('btnList','button',$_SESSION['lang']['list'],
    array('onclick'=>'listPosting()'))
);

#====== View ======
# Menu
include('master_mainMenu.php');

# Form
OPEN_BOX();
echo genElTitle($_SESSION['lang']['peosesakhirbulan'],$els);
CLOSE_BOX();

# List
OPEN_BOX();
echo makeFieldset($_SESSION['lang']['list'],'listPosting',null,true);
CLOSE_BOX();

#=== End ===
close_body();
?>