<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

#=== Start ===
echo open_body();
?>
<!-- Includes -->
<script language=javascript1.2 src='js/zTools.js'></script>
<script language=javascript1.2 src='js/keu_3tutupbulanAudit.js'></script>
<link rel=stylesheet type=text/css href='style/zTable.css'>
<?php
#====== Controller ======
# Options
$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
    "kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
$bulantahun = ($_SESSION['org']['period']['tahun']-1)."-12";
$bulantahun1= ($_SESSION['org']['period']['tahun'])."-12";
$optPeriod = array($bulantahun1=>$bulantahun1,$bulantahun=>$bulantahun);

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

# Button
$els['btn'] = array(
  makeElement('btnList','button',$_SESSION['lang']['tutupbuku'],
    array('onclick'=>'tutupBuku()'))
);

#====== View ======
# Menu
include('master_mainMenu.php');

# Form
OPEN_BOX();
echo genElTitle('Recognition Ending Audit(base on the last revision):',$els);
if($_SESSION['language']=='EN'){
    echo"<fieldset style='width:500px';><legend>".$_SESSION['lang']['info']."</legend>
              After doing this process, you will be in the period of January next year, if you've been in a period greater than January 
              of the following year, then you must conduct the process of closing the books until your transaction at this time period 
              (after doing the process), through Finance menu-> process-> Close Book month (No need to process the 'Monthly Process') .
              </feldset>";    
}else{
    echo"<fieldset style='width:500px';><legend>".$_SESSION['lang']['info']."</legend>
              Setelah melakukan proses ini, anda akan berada pada periode bulan Januari tahun selanjutnya, 
              jika anda sudah berada pada periode lebih besar dari bulan Januari tahun berikut, makan anda wajib melakukan proses Tutup buku hingga periode
              transaksi anda saat ini(setelah melakukan proses ini), melalui menu Keuangan->Proses->Tutup Buku Bulanan(Tidak perlu proses akhir bulan) .
              </feldset>";
}
CLOSE_BOX();
close_body();
?>