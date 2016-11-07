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
  
<p align="left"><u><b><font face="Arial" size="5" color="#000080">Penalti</font></b></u></p>
<?php
#======Select Prep======
$optKode = getEnum($dbname,'kebun_5penalty','kodedenda');
$optUom = getEnum($dbname,'kebun_5penalty','satuan');
$optBudidaya = makeOption($dbname,'kebun_5budidaya','kode,budidaya');
#======End Select Prep======
#=======Form============
echo "<div style='margin-bottom:30px'>";
$els = array();
# Fields
$els[] = array(
  makeElement('budidaya','label',$_SESSION['lang']['budidaya']),
  makeElement('budidaya','select','',array('style'=>'width:150px'),$optBudidaya)
);
$els[] = array(
  makeElement('kodedenda','label',$_SESSION['lang']['kodedenda']),
  makeElement('kodedenda','select','',array('style'=>'width:150px'),$optKode)
);
$els[] = array(
  makeElement('keterangan','label',$_SESSION['lang']['keterangan']),
  makeElement('keterangan','text','',array('style'=>'width:150px','maxlength'=>'50',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('satuan','label',$_SESSION['lang']['satuan']),
  makeElement('satuan','select','',array('style'=>'width:150px'),$optUom)
);
$els[] = array(
  makeElement('dendapemanen','label',$_SESSION['lang']['dendapemanen']),
  makeElement('dendapemanen','text','',array('style'=>'width:150px','maxlength'=>'10',
    'onkeypress'=>'return angka_doang(event)'))
);
$els[] = array(
  makeElement('dendasupervisi','label',$_SESSION['lang']['dendasupervisi']),
  makeElement('dendasupervisi','check','0',array('style'=>'width:150px'))
);
$els[] = array(
    makeElement('tglberlaku','label',$_SESSION['lang']['tglberlaku']),
    makeElement('tglberlaku','text','',array('style'=>'width:150px',
    'readonly'=>'readonly','onmousemove'=>'setCalendar(this.id)'))
);

# Fields
$fieldStr = '##budidaya##kodedenda##keterangan##satuan##dendapemanen##dendasupervisi##tglberlaku';
$fieldArr = explode("##",substr($fieldStr,2,strlen($fieldStr)-2));

# Button
$els['btn'] = array(
  genFormBtn($fieldStr,
    'kebun_5penalty',"##budidaya##kodedenda")
);

# Generate Field
echo genElement($els);
echo "</div>";
#=======End Form============

#=======Table===============
# Display Table
echo "<div style='height:200px;overflow:auto'>";
echo masterTable($dbname,'kebun_5penalty',"*",array(),array(),null,array(),null,'budidaya##kodedenda');
echo "</div>";
#=======End Table============

CLOSE_BOX();
echo close_body();
?>