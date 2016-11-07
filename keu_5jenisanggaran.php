<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<script language=javascript src=js/zMaster.js></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<?php
#======Select Prep======
# Get Data
$where = "`tipe`='HOLDING' or `tipe`='PT'";
$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$where,'0');
#======End Select Prep======
#=======Form============
echo "<div style='margin-bottom:30px'>";
$els = array();
# Fields
$els[] = array(
  makeElement('kodeanggaran','label',$_SESSION['lang']['kodeanggaran']),
  makeElement('kodeanggaran','text','',array('style'=>'width:100px','maxlength'=>'10',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('namaanggaran','label',$_SESSION['lang']['namaanggaran']),
  makeElement('namaanggaran','text','',array('style'=>'width:250px','maxlength'=>'45',
    'onkeypress'=>'return tanpa_kutip(event)'))
);

# Fields
$fieldStr = '##kodeanggaran##namaanggaran';
$fieldArr = explode("##",substr($fieldStr,2,strlen($fieldStr)-2));

# Button
$els['btn'] = array(
  genFormBtn($fieldStr,
    'keu_5jenisanggaran',"##kodeanggaran")
);

# Generate Field
echo genElTitle('Jenis Anggaran',$els);
echo "</div>";
#=======End Form============

#=======Table===============
# Display Table
echo "<div style='clear:both;float:left'>";
echo masterTable($dbname,'keu_5jenisanggaran');
echo "</div>";
#=======End Table============

CLOSE_BOX();
echo close_body();
?>