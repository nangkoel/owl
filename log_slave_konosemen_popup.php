<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');
?>
<link rel="stylesheet" type="text/css" href="style/generic.css">
<script language=javascript src='js/generic.js'></script>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/log_konosemen.js'></script>
<?php
$tipe = $_GET['tipe'];
$param = $_GET;

switch($tipe) {
    case 'PO':
		echo "<label for=po>".$_SESSION['lang']['nopo']."</label>";
		echo "<input id='po' onkeypress='key=getKey(event);if(key==13){findPO()}'>";
		echo "<button class=mybutton onclick='findPO()'>".
			$_SESSION['lang']['find']."</button>";
		break;
    
	case 'SJ':
		echo "<label for=sj>No. Delivery Order</label>";
		echo "<input id='sj' onkeypress='key=getKey(event);if(key==13){findSJ()}'>";
		echo "<button class=mybutton onclick='findSJ()'>".
			$_SESSION['lang']['find']."</button>";
		break;
    
    case 'M':
		echo "<label for=mat>Find Material</label>";
		echo "<input id='mat' onkeypress='key=getKey(event);if(key==13){findMat()}'>";
		echo "<button class=mybutton onclick='findMat()'>".
			$_SESSION['lang']['find']."</button>";
		break;
    
	default:
	break;
}
?>
<input type=hidden id=kodept value='<?php echo $param['kodept']?>'>
<div id='hasilCari'></div>
<div id='progress'></div>