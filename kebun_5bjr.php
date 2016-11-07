<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
?>
<script language=javascript>isidata="<?php echo"<tr class=rowcontent><td colspan=10>".$_SESSION['lang']['dataempty']."</td></tr>"?>";</script>
<script language=javascript src=js/zTools.js></script>
<script language=javascript1.2 src='js/kebun_5bjr.js'></script>
<?php
$optBlok="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optNmOrg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$sBlok="select distinct kodeorg,bloklama from ".$dbname.".setup_blok where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' ";
//echo $sBlok;
$qBlok=mysql_query($sBlok) or die(mysql_error());
while($rBlok=mysql_fetch_assoc($qBlok))
{
    $optBlok.="<option value='".$rBlok['kodeorg']."'>".$rBlok['kodeorg']." - ".$rBlok['bloklama']."</option>";
}
$arr="##thnProd##kdBlok##jmBjr##proses";
include('master_mainMenu.php');
OPEN_BOX();

echo"<fieldset style=width:350px;float:left;>
     <legend>".$_SESSION['lang']['bjr']."</legend>
	 <table>
	 <tr>
	   <td>".$_SESSION['lang']['tahunproduksi']."</td>
	   <td><input type=text class=myinputtext id=thnProd name=thnProd onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=4 />
           <button class=mybutton onclick=loadData() title=clik untuk get data>".$_SESSION['lang']['ok']."</button></td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['kodeblok']."</td>
	   <td><select id='kdBlok'  style=\"width:150px;\" disabled>".$optBlok."</select></td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['bjr']."</td>
	   <td><input type=text class=myinputtextnumber id=jmBjr name=jmBjr onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=7  disabled /> </td>
	 </tr>	
	
	 </table>
	 <input type=hidden value=insert id=proses>
	 <button class=mybutton onclick=saveFranco('kebun_slave_5bjr','".$arr."')>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['done']."</button>
     </fieldset>";
CLOSE_BOX();
OPEN_BOX();
$str="select distinct substr(kodeorg,1,4) as kodeorg,tahunproduksi from ".$dbname.".kebun_5bjr where substr(kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."' order by tahunproduksi desc";
$res=mysql_query($str) or die(mysql_error());

echo"<div id=listThnProduksi><fieldset style=width:250px;float:left;><legend>".$_SESSION['lang']['list']." ".$_SESSION['lang']['tahunproduksi']."</legend>";
echo"<table border=0 cellpadding=1 cellspacing=1><thead>";
echo"<tr class=rowheader><td>".$_SESSION['lang']['kodeorg']."</td><td>".$_SESSION['lang']['tahunproduksi']."</td></tr><tbody>";
while($rowData=mysql_fetch_assoc($res))
{
    echo"<tr class=rowcontent><td>".$optNmOrg[$rowData['kodeorg']]."</td><td>".$rowData['tahunproduksi']."</td></tr>";
}
echo"</tbody></table></fieldset></div>";
echo"<div id=listDataBjr style=display:none>";
echo"<fieldset style=width:650px;float:left;><legend>".$_SESSION['lang']['list']."</legend><table class=sortable cellspacing=1 border=0>
     <thead>
	  <tr class=rowheader>
	   <td>No</td>
	   <td>".$_SESSION['lang']['kodeblok']."</td>
           <td>".$_SESSION['lang']['bloklama']."</td>
	   <td>".$_SESSION['lang']['tahunproduksi']."</td>
	   <td>".$_SESSION['lang']['tahuntanam']."</td>
	   <td>".$_SESSION['lang']['jenisbibit']."</td>
	   <td>".$_SESSION['lang']['bjr']."</td>
	   <td>Action</td>
	  </tr>
	 </thead>
	 <tbody id=container>";
	 echo"<tr class=rowcontent><td colspan=10>".$_SESSION['lang']['dataempty']."</td></tr>";

echo"</tbody>
     <tfoot>
	 </tfoot>
	 </table></fieldset></div>";
CLOSE_BOX();
echo close_body();
?>