<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PT'";
$res=mysql_query($str);
$opt.="";
while($bar=mysql_fetch_object($res))
{
	$opt.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}
?>
<script language=javascript src=js/org_npwp.js></script>
<fieldset style='width:450px'>
	<legend><b><?echo $_SESSION['lang'][setupnpwporg];?></b></legend>
	<table>
	<tr>	
	<td><?echo $_SESSION['lang']['namaorganisasi'];?></td><td><select id=org><?php echo".$opt.";?></select></td>
	</tr>
	<tr>	
	<td><?echo $_SESSION['lang']['npwp'];?></td><td><input type=text class=myinputtext id=npwp onkeypress="return tanpa_kutip(event)" size=25 maxlength=30></td>
	</tr>
	<tr>		
	<td><?echo $_SESSION['lang']['alamatnpwp'];?></td><td><input type=text class=myinputtext id=alamatnpwp onkeypress="return tanpa_kutip(event)" size=45 maxlength=100></td>
	</tr>	
	<tr>		
	<td><?echo $_SESSION['lang']['domisili'];?></td><td><input type=text class=myinputtext id=alamatdomisili onkeypress="return tanpa_kutip(event)" size=45 maxlength=100></td>
	</tr>
	</table>
	<button class=mybutton onclick=savenpwp()><?echo $_SESSION['lang']['save'];?></button>
	<button class=mybutton onclick=cancelnpwp()><?echo $_SESSION['lang']['cancel'];?></button>
</fieldset>	
<?php
echo"<table class=sortable cellspacing=1 border=0>
     <thead>
	  <tr class=rowheader>
	  <td>".$_SESSION['lang']['kodeorg']."</td>
	  <td>".$_SESSION['lang']['namaorganisasi']."</td>
	  <td>".$_SESSION['lang']['npwp']."</td>
	  <td>".$_SESSION['lang']['alamatnpwp']."</td>
	  <td>".$_SESSION['lang']['domisili']."</td>
	  <td></td>
	  </tr>
	 </thead>
	 <tbody id=container>";
  $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PT' order by namaorganisasi";
  $res=mysql_query($str);
  while($bar=mysql_fetch_object($res))
  {	 
	   $alamatnpwp='';
	   $npwp	  ='';
	   $alamatdom =''; 
	$str1="select * from ".$dbname.".setup_org_npwp where kodeorg='".$bar->kodeorganisasi."' order by kodeorg";
	$res1=mysql_query($str1);
	while($bar1=mysql_fetch_object($res1))
	{
	   $alamatnpwp=$bar1->alamatnpwp;
	   $npwp	  =$bar1->npwp;
	   $alamatdom =$bar1->alamatdomisili; 
	}
	echo"<tr class=rowcontent>
	  <td>".$bar->kodeorganisasi."</td>
	  <td>".$bar->namaorganisasi."</td>
	  <td>".$npwp."</td>
	  <td>".$alamatnpwp."</td>
	  <td>".$alamatdom."</td>
	  <td>
		  <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delnpwp('".$bar->kodeorganisasi."');\">
	  </td>
	  </tr>";	 
  }
	  
echo"</tbody>
	 <tfoot>
	 </tfoot>
     </table>";
CLOSE_BOX();
?>