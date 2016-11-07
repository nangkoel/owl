<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

#=== Start ===
echo open_body();
?>
<!-- Includes -->
<script language=javascript1.2 src=js/zTools.js></script>
<script language=javascript1.2 src=js/log_rekalharga.js></script>
<link rel=stylesheet type=text/css href='style/zTable.css'>
<?php
#====== Controller ======
# Options
$whr="tipe like 'GUDANG%' and alokasi='".$_SESSION['empl']['kodeorganisasi']."'";
$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whr);
//$bulantahun = $_SESSION['org']['period']['tahun']."-".$_SESSION['org']['period']['bulan'];
//$optPeriod = array($bulantahun=>$bulantahun);
$optPeriod = makeOption($dbname,'setup_periodeakuntansi','periode,periode',"kodeorg like '".substr($_SESSION['empl']['lokasitugas'],0,1)."%' and length(kodeorg)=6");
$query = selectQuery($dbname,'log_5masterbarang','kodebarang,namabarang','inactive=0');
$data = fetchData($query);
$optBarang[''] = $_SESSION['lang']['all'];
foreach($data as $row) {
       $optBarang[$row['kodebarang']] = $row['kodebarang']." - ".$row['namabarang'];
}

# Fields
$els = array();
$els[] = array(
  makeElement('kodeorg','label',$_SESSION['lang']['kodegudang']),
  makeElement('kodeorg','select','',array('style'=>'width:200px'),$optOrg)
);
$els[] = array(
  makeElement('periode','label',$_SESSION['lang']['periode']),
  makeElement('periode','select','',array('style'=>'width:200px'),$optPeriod)
);
$els[] = array(
  makeElement('kodebarang','label',$_SESSION['lang']['namabarang']),
  makeElement('kodebarang','selectsearch','',array('style'=>'width:200px'),$optBarang)
);

# Button
$els['btn'] = array(
  makeElement('btnList','button',$_SESSION['lang']['list'],
    array('onclick'=>'listBarang()'))
);

#====== View ======
# Menu
include('master_mainMenu.php');

# Form
OPEN_BOX();
$catatan="<fieldset style='float:left; width:400px;'><legend id='catatan'><b>".$_SESSION['lang']['catatan']."</b></legend>
    Rekalkulasi hanya berlaku untuk periode gudang yang sudah ditutup
    </fieldset>";

echo genElTitle("Rekalkulasi Harga Barang",$els);
//echo $catatan;
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