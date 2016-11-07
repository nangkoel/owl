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

<iframe src="keu_slave_5daftarperkiraan_pdf.php?table=keu_5akun" width="100%" height="450px"></iframe>

<?php
CLOSE_BOX();
echo close_body();
?>