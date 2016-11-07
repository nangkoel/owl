<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');
?>
<link rel="stylesheet" type="text/css" href="style/generic.css">
<script language=javascript src='js/generic.js'></script>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/keu_vp.js'></script>
<?php
$tipe = $_GET['tipe'];
$param = $_GET;

$optTipe = array(
	'po' => $_SESSION['lang']['po'],
	'k' =>  $_SESSION['lang']['kontrak'],
	'sj' => $_SESSION['lang']['suratjalan'],
	'ns' => $_SESSION['lang']['konosemen'],
	'ot' => $_SESSION['lang']['lain']
);
?>

<div style='margin:10px 0 15px 5px'>
    <label for='po'><?php echo $_SESSION['lang']['find']?></label>
	<?php 
	echo makeElement('tipe','select','po',array(),$optTipe);
	echo makeElement('po','text','',array('onkeypress'=>'key=getKey(event);if(key==13){findPO()}'));
	?>
    <button class=mybutton onclick='findPO()'><?php echo $_SESSION['lang']['find']?></button>
</div>
<fieldset><legend><?php echo $_SESSION['lang']['hasil']?></legend>
    <div id='hasilPO'></div>
    <div id='hasilInvoice' style='display:none'></div>
</fieldset>
<div id='progress'></div>