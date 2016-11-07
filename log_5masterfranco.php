<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript1.2 src=js/log_5masterfranco.js></script>
<?php
$arr="##idFranco##nmFranco##almtFranco##cntcPerson##hdnPhn##method";
include('master_mainMenu.php');
OPEN_BOX();

echo"<fieldset>
     <legend>Master Franco</legend>
	 <table>
	 <tr>
	   <td>Franco Name</td>
	   <td><input type=text class=myinputtext id=nmFranco name=nmFranco onkeypress=\"return tanpa_kutip(event);\" style=\"width:150px;\" maxlength=100 /></td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['alamat']."</td>
	   <td><textarea id=almtFranco name=almtFranco></textarea></td>
	 </tr>
	 <tr>
	   <td>Contac Person</td>
	   <td><input type=text class=myinputtext id=cntcPerson name=cntcPerson onkeypress=\"return tanpa_kutip(event);\" style=\"width:150px;\" /> </td>
	 </tr>	
	 <tr>
	   <td>".$_SESSION['lang']['telp']."</td>
	   <td><input type=text class=myinputtext id=hdnPhn name=hdnPhn  onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=20></td>
	 </tr>	 
	  <tr>
	   <td>".$_SESSION['lang']['status']."</td>
	   <td><input type='checkbox' id=statFr name=statFr />".$_SESSION['lang']['tidakaktif']."</td>
	 </tr> 
	 </table>
	 <input type=hidden value=insert id=method>
	 <button class=mybutton onclick=saveFranco('log_slave_5masterfranco','".$arr."')>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>
     </fieldset><input type='hidden' id=idFranco name=idFranco />";
CLOSE_BOX();
OPEN_BOX();
$str="select * from ".$dbname.".setup_franco order by id_franco desc";
$res=mysql_query($str);
echo"<fieldset><legend>".$_SESSION['lang']['list']."</legend><table class=sortable cellspacing=1 border=0>
     <thead>
	  <tr class=rowheader>
	   <td>No</td>
	   <td>Nama Franco</td>
	   <td>".$_SESSION['lang']['alamat']."</td>
	   <td>Kontak Person</td>
	   <td>".$_SESSION['lang']['telp']."</td>
	   <td>".$_SESSION['lang']['status']."</td>
	   <td>Action</td>
	  </tr>
	 </thead>
	 <tbody id=container>";
	 echo"<script>loadData()</script>";
//$no=0;	 
//while($bar=mysql_fetch_object($res))
//{
//  $no+=1;	
//  echo"<tr class=rowcontent>
//	  <td>No</td>
//	   <td>Nama Franco</td>
//	   <td>".$_SESSION['lang']['alamat']."</td>
//	   <td>Kontak Person</td>
//	   <td>".$_SESSION['lang']['telp']."</td>
//	   <td>".$_SESSION['lang']['status']."</td>
//	   <td>
//		      <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->kode."','".$bar->kelompok."','".$bar->kelompokbiaya."','".$bar->noakun."');\"> 
//			  <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delKelompok('".$bar->kode."','".$bar->kelompok."');\">
//		  </td>
//	   
//	  </tr>";	
//}     
echo"</tbody>
     <tfoot>
	 </tfoot>
	 </table></fieldset>";
CLOSE_BOX();
echo close_body();
?>