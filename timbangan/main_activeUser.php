<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<link rel=stylesheet type='text/css' href=style/efs.css>
<script language=javascript1.2 src=js/usersetting.js></script>
<?php
include('master_mainMenu.php');

/*$str="select userid,name from ".$dbname.".user_empl order by name";
$res=mysql_query($str);
   $opt='';
while($bar=mysql_fetch_object($res))
{
	$opt.="<option value='".$bar->userid."'>".$bar->name."</option>";
}*/
OPEN_BOX();
echo OPEN_THEME('Aktif/Non Aktif/Hapus Pemakai:');
echo"<br><fieldset>
     <legend><img src='images/useraccounts.png' height=60px style='vertical-align:middle;'><b>Activate/Deactivate/Delete Users Account:</b></legend>
	  Find User:<input type=text id=uname class=myinputtext onkeypress=\"return validat(event);\" size=20 maxlength=30 title='Enter part of username then click Find'>
	 <input type=button class=mybutton value=Find title='Click to process' onclick=getUserForActivation()>
	 <br>
	 </fieldset><br><hr>
	 <fieldset>
	 <legend>Result</legend>
	 <div id=result></div>
	 </fieldset>
	 <div id=temp></div>
	 ";
echo CLOSE_THEME();
CLOSE_BOX();
echo close_body();
?>
