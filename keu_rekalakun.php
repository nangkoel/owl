<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

#=== Start ===
echo open_body();
?>
<!-- Includes -->
<script language=javascript1.2 src=js/zTools.js></script>
<script language=javascript1.2 src=js/keu_3tutupbulan.js></script>
<link rel=stylesheet type=text/css href='style/zTable.css'>
<?php
#====== Controller ======
# Options
$whr="kodeorganisasi in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";
if ($_SESSION['empl']['tipelokasitugas']=='HOLDING')
    $whr="LENGTH(kodeorganisasi)=4";
$query = selectQuery($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whr,'alokasi,tipe',true);
$data = fetchData($query);
foreach($data as $row) {
       $optOrg[$row['kodeorganisasi']] = $row['namaorganisasi'];
}
//$bulantahun = $_SESSION['org']['period']['tahun']."-".$_SESSION['org']['period']['bulan'];
//$optPeriod = array($bulantahun=>$bulantahun);
$optPeriod = makeOption($dbname,'setup_periodeakuntansi','periode,periode',"kodeorg='".$_SESSION['empl']['lokasitugas']."' and tutupbuku=1");

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
  makeElement('btnList','button',$_SESSION['lang']['list'],
    array('onclick'=>'listAkun()'))
);

#====== View ======
# Menu
include('master_mainMenu.php');

# Form
OPEN_BOX();
$catatan="<fieldset style='float:left; width:400px;'><legend id='catatan'><b>".$_SESSION['lang']['catatan']."</b></legend>
    Rekalkulasi hanya berlaku untuk periode akunting yang sudah ditutup
    </fieldset>";

echo genElTitle("Rekalkulasi Saldo Akun",$els);
echo $catatan;
CLOSE_BOX();
//echo"<pre>";
//print_r($_SESSION);
//echo"</pre>";
# List
OPEN_BOX();
echo makeFieldset($_SESSION['lang']['list'],'listPosting',null,true);
CLOSE_BOX();

#=== End ===
close_body();
?>