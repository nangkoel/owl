<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

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
echo OPEN_THEME('Reset Password Pemakai:');
echo"<fieldset>
     <legend><img src='images/vista_icons_03.png' height=60px style='vertical-align:middle;'><b>Reset Password Pemakai:</b></legend>
	  Cari Pemakai:<input type=text id=uname class=myinputtext onkeypress=\"return validat1(event);\" size=20 maxlength=30 title='Masukkan Seluruh atau sebagian nama pemakai lalu tekan tombol Cari'>
	 <input type=button class=mybutton value=Cari title='Klik U/ Melanjutkan' onclick=getUserForResetP()>
	 <br>
	 </fieldset><br><hr>
	 <fieldset>
	 <legend>Hasil</legend>
	 <div id=result></div>
	 </fieldset>
	 <div id=temp></div>
	 ";
echo CLOSE_THEME();

//menu order editor
echo"<div id=resetter style='display:none;position:absolute;'>";
echo OPEN_THEME('Reset Password:');
  echo"<input type=hidden value='' id=uid>
       <center></center>
       <div id=resetwin>
	   <table>
	   <tr><td><b>Account</b></td><td>:<b><a id=un></a></b></td></tr>
	   <tr>
	    <td>New Password</td><td>:<input class=myinputtext type=password id=newpwd1 size=15 onkeypress=\"return tanpa_kutip(event);\"><img src='images/obligatory.gif' style='height:15px;vertical-align:middle;' title='Required Element'></td></tr>
        <tr><td>Re-Type Here</td><td>:<input class=myinputtext type=password id=newpwd2 size=15 onkeypress=\"return tanpa_kutip(event);\"><img src='images/obligatory.gif' style='height:15px;vertical-align:middle;' title='Required Element'></td></tr>
	    <tr><td colspan=2 align=right><input style='vertical-align:middle;' type=checkbox id=sendmail>Send an email to account owner.</td></tr>
		<tr><td colspan=2 align=right>
		<input type=button class=mybutton value=Close onclick=hideSetter()>
		<input type=button class=mybutton value=Done onclick=saveNewPwd()></td></tr>
	   </table>
	   </div>";
echo CLOSE_THEME();
echo"</div>";
CLOSE_BOX();
echo close_body();
?>
