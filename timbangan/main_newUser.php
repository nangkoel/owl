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
echo OPEN_THEME('Pemakai Baru:');
echo"<fieldset>
     <legend><img src='images/user.png' height=60px style='vertical-align:middle;'><b>Tambah Pemakai Baru:</b></legend>
      <table cellspacing=1 border=0'>
	  <tbody>
        <!--<tr><td>Employee Name</td><td>
		        <select id=userid onchange=enablecheck(this.options[this.selectedIndex].value)>
			      <option value=0>*Additional User ... </option>".$opt."
			     </select>
			 </td>
		 </tr>-->
	     <tr><td>UserName</td><td><input  class=myinputtext type=text size=20 maxlength=40 id=uname onkeypress=\"return tanpa_kutip_dan_sepasi(event);\"><img src='images/obligatory.gif' style='height:15px;vertical-align:middle;' title='Harus Diisi'></td></tr>
		 <tr><td>Password</td><td><input  class=myinputtext type=password id=pwd1 size=20 maxlength=20 onkeypress=\"return tanpa_kutip(event);\"><img src='images/obligatory.gif' style='height:15px;vertical-align:middle;' title='Harus Diisi'></td></tr>
		 <tr><td>Re-Type Password</td><td><input  class=myinputtext type=password id=pwd2 size=20 maxlength=20 onkeypress=\"return tanpa_kutip(event);\"><img src='images/obligatory.gif' style='height:15px;vertical-align:middle;' title='Harus Diisi'></td></tr>
          <tr><td>Status</td><td><input type=radio name=radio id=radio value=1 class=myradio checked>Aktif<input type=radio name=radio id=radio1 value=0 class=myradio>Tidak Aktif<br>
		  
		  </td></tr>
		  <tr><td colspan=2 align=right>
			  <input type=button class=mybutton value=Batal onclick=resetf()>
			  <input type=button class=mybutton value=Simpan onclick=savef()> &nbsp
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
