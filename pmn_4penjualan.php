<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
//tentukan table yang akan dikontrol
$TABLENAME='pmn_sodt';
?>

<script language=javascript1.2 src=js/tablebrowser.js></script>
<?php
include('master_mainMenu.php');
OPEN_BOX("","<b>Table:".$TABLENAME."</b>");
printTableController($TABLENAME);
CLOSE_BOX();
OPEN_BOX("","<b>Table:".$TABLENAME."</b>");
echo"<div id=container style='width:100%;height:400px;overflow:scroll;'></div>";
CLOSE_BOX();
echo close_body();
?>