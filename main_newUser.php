<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/usersetting.js></script>
<?php
include('master_mainMenu.php');

$str="select karyawanid,namakaryawan, lokasitugas from ".$dbname.".datakaryawan 
           where (tanggalkeluar>'".date('Y-m-d')."' or tanggalkeluar='0000-00-00') order by namakaryawan";
$res=mysql_query($str);
   $opt='';
while($bar=mysql_fetch_object($res))
{
	$opt.="<option value='".$bar->karyawanid."'>".$bar->namakaryawan."-".$bar->lokasitugas."</option>";
}
OPEN_BOX();
echo OPEN_THEME($_SESSION['lang']['newuser'].':');
echo"<fieldset>
     <legend><img src='images/user.png' height=60px style='vertical-align:middle;'><b>".$_SESSION['lang']['addnewuser'].":</b></legend> 
      <table cellspacing=1 border=0'>
	  <tbody>
        <tr><td>".$_SESSION['lang']['employeename']."</td><td>
		        <select id=userid onchange=enablecheck(this.options[this.selectedIndex].value)>
			      <option value=0>*Additional User ... </option>".$opt."
			     </select>
			 </td>
		 </tr>
	     <tr><td>".$_SESSION['lang']['username']."</td><td><input  class=myinputtext type=text size=20 maxlength=40 id=uname onkeypress=\"return tanpa_kutip_dan_sepasi(event);\"><img src='images/obligatory.gif' style='height:15px;vertical-align:middle;' title='Required Element'></td></tr>
		 <tr><td>".$_SESSION['lang']['password']."</td><td><input  class=myinputtext type=password id=pwd1 size=20 maxlength=20 onkeypress=\"return tanpa_kutip(event);\"><img src='images/obligatory.gif' style='height:15px;vertical-align:middle;' title='Required Element'></td></tr>
		 <tr><td>Re-Type ".$_SESSION['lang']['password']."</td><td><input  class=myinputtext type=password id=pwd2 size=20 maxlength=20 onkeypress=\"return tanpa_kutip(event);\"><img src='images/obligatory.gif' style='height:15px;vertical-align:middle;' title='Required Element'></td></tr>
          <tr><td>Status</td><td><input type=radio name=radio id=radio value=1 class=myradio checked>Active <input type=radio name=radio id=radio1 value=0 class=myradio>Not Active<br>
		  <input type=checkbox id=sendmail style='vertical-align:middle;' disabled>".$_SESSION['lang']['sendmailtouser']."
		  </td></tr>
		  <tr><td colspan=2 align=right>
			  <input type=button class=mybutton value='".$_SESSION['lang']['cancel']."' onclick=resetf()>
			  <input type=button class=mybutton value='".$_SESSION['lang']['save']."' onclick=savef()> &nbsp 
		  </td></tr>		 
	  </tbody>
	  </table>  
	 </fieldset><br><hr>
	 <div id=temp></div>
	 "; 
echo CLOSE_THEME();
CLOSE_BOX();
echo close_body();
?>
