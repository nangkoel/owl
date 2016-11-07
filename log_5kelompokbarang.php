<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/kelompok_barang.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX();

//pengambilan kelompok biaya sari table keu_5komponenbiaya
//kelompok biaya tsb di ENUNM pada table(Tidak memiliki table sendiri)
$str="select distinct kelompokbiaya from ".$dbname.".keu_5komponenbiaya order by kelompokbiaya";
$res=mysql_query($str);
$opt="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
	if($bar->kelompokbiaya==''){}
	else
	$opt.="<option value='".$bar->kelompokbiaya."'>".$bar->kelompokbiaya."</option>";
}
echo"<fieldset>
     <legend>".$_SESSION['lang']['kelompokbarang']."</legend>
	 <table>
	 <tr>
	   <td>".$_SESSION['lang']['materialgroupcode']."</td>
	   <td><input type=text class=myinputtext id=kelnumber size=3 maxlength=3 onkeypress=\"return angka_doang(event);\"></td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['namakelompok']."</td>
	   <td><input type=text class=myinputtext id=kelname size=60 maxlength=60 onkeypress=\"return tanpa_kutip(event);\"></td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['namakelompok']."(EN)</td>
	   <td><input type=text class=myinputtext id=kelname1 size=60 maxlength=60 onkeypress=\"return tanpa_kutip(event);\"></td>
	 </tr>
                        <tr>
	   <td>".$_SESSION['lang']['kelompokbiaya']."</td>
	   <td><select id=kelompokbiaya>".$opt."</select></td>
	 </tr>	
	 <tr>
	   <td>".$_SESSION['lang']['noakun']."</td>
	   <td><input type=text class=myinputtextnumber id=noakun size=15 maxlength=15 onkeypress=\"return angka_doang(event);\"></td>
	 </tr>	  
	 </table>
	 <input type=hidden value=insert id=method>
	 <button class=mybutton onclick=saveKelompokBarang()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelKelompokBarang()>".$_SESSION['lang']['cancel']."</button>
     </fieldset>";
CLOSE_BOX();
OPEN_BOX();
$str="select * from ".$dbname.".log_5klbarang order by kelompok";
$res=mysql_query($str);
echo"<table class=sortable cellspacing=1 border=0>
     <thead>
	  <tr class=rowheader>
	   <td>No</td>
	   <td>".$_SESSION['lang']['materialgroupcode']."</td>
	   <td>".$_SESSION['lang']['namakelompok']."</td>
                       <td>".$_SESSION['lang']['namakelompok']."(EN)</td>
	   <td>".$_SESSION['lang']['kelompokbiaya']."</td>
	   <td>".$_SESSION['lang']['noakun']."</td>
	   <td></td>
	  </tr>
	 </thead>
	 <tbody id=container>";
$no=0;	 
while($bar=mysql_fetch_object($res))
{
  $no+=1;	
  echo"<tr class=rowcontent>
	   <td>".$no."</td>
	   <td>".$bar->kode."</td>
	   <td>".$bar->kelompok."</td>
                       <td>".$bar->kelompok1."</td>
	   <td>".$bar->kelompokbiaya."</td>
	   <td>".$bar->noakun."</td>
		  <td>
		      <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->kode."','".$bar->kelompok."','".$bar->kelompok1."','".$bar->kelompokbiaya."','".$bar->noakun."');\"> 
			  <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delKelompok('".$bar->kode."','".$bar->kelompok."');\">
		  </td>
	   
	  </tr>";	
}     
echo"</tbody>
     <tfoot>
	 </tfoot>
	 </table>";
CLOSE_BOX();
echo close_body();
?>