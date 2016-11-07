<?php
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
echo OPEN_THEME('Login History:');
//echo"<pre>";
//print_r($_SESSION);
//echo"</pre>";
$status_logout=$_SESSION['standard']['logged']==1?"Not LogOut":"Normal";
$x=str_replace("-","",$_SESSION['standard']['lastupdate']);
$mark=mktime(0,0,0,substr($x,4,2),substr($x,6,2),substr($x,0,4));
echo"<table>
	     <tr>
		 <tr><td><u>Last Login</u></td><td>: ".$status_logout."</td></tr>
		 <tr><td><u>Last Login Date</u></td><td>: ".date('l',$mark).",".tanggalnormal(substr($_SESSION['standard']['lastupdate'],0,10))."</td></tr>
		 <tr><td><u>Last Login Time</u></td><td>: ".substr($_SESSION['standard']['lastupdate'],10,9)."</td></tr>
		 <tr><td><u>Last Login IP</u></td><td>: ".$_SESSION['standard']['lastip']."</td></tr>
		 <tr><td><u>Computer Name</u></td><td>: ".$_SESSION['standard']['lastcomp']."</td></tr>
     </table>";

echo CLOSE_THEME();
?>
