<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript1.2 src='js/sdm_5fasilitasMpp.js'></script>
<?php
$arr="##thnBudget##kdJabatan##kdBarang##hrgSat##sat##jmlhBrng##method##totBrg##oldKdBrg";
include('master_mainMenu.php');
OPEN_BOX();
$optGol="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sGol="select * from ".$dbname.".sdm_5jabatan order by namajabatan asc";
$qGol=mysql_query($sGol) or die(mysql_error($sGol));
while($rGol=  mysql_fetch_assoc($qGol))
{
    $optGol.="<option value='".$rGol['kodejabatan']."'>".$rGol['namajabatan']."</option>";
}
$optReg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sReg="select distinct namabarang,kodebarang from ".$dbname.".log_5masterbarang order by namabarang asc";
$qReg=mysql_query($sReg) or die(mysql_error($sReg));
while($rReg=  mysql_fetch_assoc($qReg))
{
    $optReg.="<option value='".$rReg['kodebarang']."'>".$rReg['kodebarang']." [".$rReg['namabarang']."]</option>";
}
echo"<input type='hidden' id='method' name='method' value='insert' />";

echo"<fieldset style=width:290px;>
     <legend>".$_SESSION['lang']['form']." ".$_SESSION['lang']['fasiltasmpp']."</legend>
	 <table>
	 <tr>
	   <td>".$_SESSION['lang']['budgetyear']."</td>
	   <td><input type=text class=myinputtextnumber id=thnBudget name=thnBudget onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=4 /></td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['kodejabatan']."</td>
	   <td><select id=kdJabatan name=kdJabatan style=\"width:150px;\" >".$optGol."</select></td>
	 </tr>
	 <tr>
       <tr><td>".$_SESSION['lang']['namabarang']."</td>
	   <td><select id=kdBarang name=kdBarang style=\"width:150px;\" onchange='getSatuan()' >".$optReg."</select>&nbsp;<img src=\"images/search.png\" class=\"resicon\" title='".$_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang']."' onclick=\"searchBrg('".$_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang']."','<fieldset><legend>".$_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang']."</legend>".$_SESSION['lang']['find']."&nbsp;<input type=text class=myinputtext id=nmBrg><button class=mybutton onclick=findBrg()>".$_SESSION['lang']['find']."</button></fieldset><div id=containerBarang style=overflow=auto;height=380;width=485></div>',event);\"></td>
	 </tr>	
	 <tr>
	   <td>".$_SESSION['lang']['hargasatuan']."</td>
	   <td><input type=text class=myinputtextnumber id=hrgSat name=hrgSat  onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=20 onblur=\"kalikan()\"></td>
	 </tr>	 
	  <tr>
	   <td>".$_SESSION['lang']['satuan']."</td>
	   <td><input type=text class=myinputtextnumber id=sat name=sat disabled onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=20></td>
	 </tr>	
           <tr>
	   <td>".$_SESSION['lang']['jumlah']."</td>
	   <td><input type=text class=myinputtextnumber id=jmlhBrng name=jmlhBrng  onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=20 onblur=\"kalikan()\"></td>
	 </tr>	
           <tr>
	   <td>".$_SESSION['lang']['total']."</td>
	   <td><input type=text class=myinputtextnumber disabled id=totBrg name=totBrg  onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=20></td>
	 </tr>	

	 </table>
	 <button class=mybutton onclick=saveFranco('sdm_slave_5fasilitasMpp','".$arr."')>".$_SESSION['lang']['save']."</button>
        <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>
<input type=hidden id=oldKdBrg value='' />
     </fieldset>";
echo"</div>";
CLOSE_BOX();
OPEN_BOX();
$optData="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

$str="select distinct tahunbudget from ".$dbname.".sdm_5transportpjd order by tahunbudget desc";
$res=mysql_query($str) or die(mysql_error($str));
while($rData=mysql_fetch_assoc($res))
{
    $optData.="<option value='".$rData['tahunbudget']."'>".$rData['tahunbudget']."</option>";
}
echo"<table><tr>
    <td>".$_SESSION['lang']['budgetyear']." <select id=thnBudgetHead style='width:100px' onchange='loadData()'>".$optData."</select></td>
    <td>".$_SESSION['lang']['kodejabatan']." <select id=kdJabtanHead style='width:100px' onchange='loadData()'>".$optGol."</select></td>
   
    </tr></table>";
echo"<fieldset><legend>".$_SESSION['lang']['list']."</legend><table class=sortable cellspacing=1 border=0>
     <thead>
	  <tr class=rowheader>
	   <td>No</td>
	   <td>".$_SESSION['lang']['budgetyear']."</td>
	   <td>".$_SESSION['lang']['kodejabatan']."</td>
	   <td>".$_SESSION['lang']['namabarang']."</td>
	   <td>".$_SESSION['lang']['hargasatuan']."</td>
	   <td>".$_SESSION['lang']['satuan']."</td>
            <td>".$_SESSION['lang']['jumlah']."</td>
            <td>".$_SESSION['lang']['total']."</td>

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