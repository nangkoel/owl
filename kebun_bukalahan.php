<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');

$_SESSION['tmp']['actStat'] = 'lc';
include('kebun_operasional.php');
#unset($_SESSION['tmp']['actStat']);

echo close_body();
?>