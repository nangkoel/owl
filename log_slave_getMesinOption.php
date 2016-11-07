<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zFunction.php');
//=============================================
if(isTransactionPeriod())//check if transaction period is normal
{
      //  echo " Error:".$_POST['induk'];
		$induk=$_POST['induk'];
        $blehh="<option value=''></option>";
		$blehh.=getVhcCode('option',$induk);
		echo $blehh;
}
else
{
	echo " Error: Transaction Period missing";
}
?>