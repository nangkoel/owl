<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script  language=javascript1.2 src=js/sdm_rumahsakit.js></script>
<link rel=stylesheet type=text/css href=style/payroll.css>
<?php
include('master_mainMenu.php');
//+++++++++++++++++++++++++++++++++++++++++++++
 
	OPEN_BOX('');
		echo"<div id=EList>";
		echo OPEN_THEME($_SESSION['lang']['rumahsakit']." Form: <a id=label style='color:#FFFFFF;'>New</a>");
		  echo"<fieldset><legend>Input Form</legend>
		       <br>
			   <table>
			      <tr><td>".$_SESSION['lang']['namars']."</td><td><input type=hidden id=hosid value=''>
				  <input type=hidden id=update value=''><input type=text class=myinputtext id=hosname size=25 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"></td></tr>
				  <tr><td>".$_SESSION['lang']['alamat']."</td><td><input type=text class=myinputtext id=hosadd size=45 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"></td></tr>
				  <tr><td>".$_SESSION['lang']['kota']."</td><td><input type=text class=myinputtext id=hoscity size=25 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"></td></tr>
			      <tr><td>".$_SESSION['lang']['telp']."</td><td><input type=text class=myinputtext id=hosphone size=25 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"></td></tr>
				  <tr><td>".$_SESSION['lang']['email']."</td><td><input type=text class=myinputtext id=hosmail size=25 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"></td></tr>
			      <tr><td>".$_SESSION['lang']['status']."</td><td><select id=status><option value=1>Active</option><option value=0>Black List</option></select></td></tr>
			   </table>
			   <br>
			   <button class=mybutton onclick=saveHospital()>".$_SESSION['lang']['save']."</button>
			   <button class=mybutton onclick=cancelHospital()>".$_SESSION['lang']['new']."</button>
			   </fieldset>";
		echo CLOSE_THEME();

		echo"</div>";
	CLOSE_BOX();	
    OPEN_BOX('',$_SESSION['lang']['list']);
	echo"<table class=sortable cellspacing=1 border=0 width=100%>
	        <thead>
	          <tr class=rowheader><td>No.</td>
			      <td align=center>".$_SESSION['lang']['namars']."</td>
				  <td align=center>".$_SESSION['lang']['alamat']."</td>
				  <td align=center>".$_SESSION['lang']['kota']."</td>
				  <td align=center>".$_SESSION['lang']['telp']."</td> 
				  <td align=center>".$_SESSION['lang']['email']."</td>
				  <td align=center>".$_SESSION['lang']['status']."</td>
				  <td align=center>Edit/Del</td>
			  </tr>
			</thead><tbody id=tbody>";
$str="select *,case status when 1 then 'Active' when 0 then	 'Black List' end as xstatus from ".$dbname.".sdm_5rs order by namars";
$res=mysql_query($str);

$no=0;
while($bar=mysql_fetch_object($res))
{
	$no+=1;
  echo"<tr class=rowcontent><td>".$no."</td>
	      <td>".$bar->namars."</td>
		  <td>".$bar->alamat."</td>
		  <td>".$bar->kota."</td>
		  <td>".$bar->telp."</td> 
		  <td>".$bar->email."</td>
		  <td>".$bar->xstatus."</td>
		  <td align=center>
		   <img src=images/tool.png class=dellicon title=Edit height=11px onclick=\"editHospital('".$bar->id."','".$bar->namars."','".$bar->kota."','".$bar->alamat."','".$bar->telp."','".$bar->email."','".$bar->status."')\">
		  <img src=images/close.png class=dellicon title=delete height=11px onclick=\"deleteHospital('".$bar->id."');\">
         </td>
	  </tr>";	
}			  
	echo"</tbody><tfoot></tfoot></table>";
	CLOSE_BOX();
echo close_body();
?>