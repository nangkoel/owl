<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src=js/usersetting.js></script>
<?php
include('master_mainMenu.php');

$str="select karyawanid,namakaryawan from ".$dbname.".datakaryawan order by namakaryawan";
$res=mysql_query($str);
   $opt='';
while($bar=mysql_fetch_object($res))
{
	$opt.="<option value='".$bar->karyawanid."'>".$bar->namakaryawan."</option>";
}
OPEN_BOX();
echo OPEN_THEME($_SESSION['lang']['resetuserpassword'].':');
echo"<fieldset>
     <legend><img src='images/vista_icons_03.png' height=60px style='vertical-align:middle;'><b>".$_SESSION['lang']['resetuserpassword'].":</b></legend> 
	  ".$_SESSION['lang']['finduser'].":<input type=text id=uname class=myinputtext onkeypress=\"return validat1(event);\" size=20 maxlength=30 title='Enter part of username then click Find'>
	 <input type=button class=mybutton value='".$_SESSION['lang']['find']."' title='Click to process' onclick=getUserForResetP()>
	 <br>
	 </fieldset><br><hr>
	 <fieldset>
	 <legend>Result</legend>
	 <div id=result></div>
	 </fieldset>
	 <div id=temp></div>
	 "; 
echo CLOSE_THEME();

//menu order editor
echo"<div id=resetter style='display:none;position:absolute;'>";
echo OPEN_THEME($_SESSION['lang']['resetuserpassword'].':');
  echo"<input type=hidden value='' id=uid>
       <center></center>
       <div id=resetwin>
	   <table>
	   <tr><td><b>Account</b></td><td>:<b><a id=un></a></b></td></tr>
	   <tr>
	    <td>".$_SESSION['lang']['newpassword']."</td><td>:<input class=myinputtext type=password id=newpwd1 size=15 onkeypress=\"return tanpa_kutip(event);\"><img src='images/obligatory.gif' style='height:15px;vertical-align:middle;' title='Required Element'></td></tr>
        <tr><td>Re-Type ".$_SESSION['lang']['newpassword']."</td><td>:<input class=myinputtext type=password id=newpwd2 size=15 onkeypress=\"return tanpa_kutip(event);\"><img src='images/obligatory.gif' style='height:15px;vertical-align:middle;' title='Required Element'></td></tr>
	    <tr><td colspan=2 align=right><input style='vertical-align:middle;' type=checkbox id=sendmail>".$_SESSION['lang']['sendmailtouser'].".</td></tr>
		<tr><td colspan=2 align=right>
		<input type=button class=mybutton value='".$_SESSION['lang']['close']."' onclick=hideSetter()>
		<input type=button class=mybutton value='".$_SESSION['lang']['save']."' onclick=saveNewPwd()></td></tr>
	   </table>
	   </div>";  
echo CLOSE_THEME();
echo"</div>";
CLOSE_BOX();
echo close_body();
?>
