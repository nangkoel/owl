<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript1.2 src='js/kebun_5stproduksi.js'></script>
<?php
$arr="##bibit##tanah##umur##produksi##method##oldjb##oldkt##oldum";
include('master_mainMenu.php');
OPEN_BOX();

$optbibit="<option value=''></option>";
$str="select * from ".$dbname.".setup_jenisbibit order by jenisbibit";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $optbibit.="<option value='".$bar->jenisbibit."'>".$bar->jenisbibit."</option>";
}
$opttanah="<option value=''></option>";
$x=readCountry('config/jenistanah.lst');
foreach($x as $bar=>$val)
{                    
    $opttanah.="<option value='".$val[0]."'>".$val[1]."</option>";
}
                

echo"<fieldset>
     <legend>".$_SESSION['lang']['standardprodkebun']."</legend>
	 <table>
	 <tr>
	   <td>".$_SESSION['lang']['jenisbibit']."<input type='hidden' id=oldjb name=oldjb /></td>
	   <td><select id=bibit style='width:150px;'>".$optbibit."</select></td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['klasifikasitanah']."<input type='hidden' id=oldkt name=oldkt /></td>
	   <td><select id=tanah style='width:150px;'>".$opttanah."</select></td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['umur']."<input type='hidden' id=oldum name=oldum /></td>
	   <td><input type=text class=myinputtext id=umur name=umur onkeypress=\"return angkadowang(event);\" style=\"width:150px;\" maxlength=2/></td>
	 </tr>	
	 <tr>
	   <td>".$_SESSION['lang']['kgproduksi']."/Ha</td>
	   <td><input type=text class=myinputtext id=produksi name=produksi onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=10></td>
	 </tr>	 
	 </table>
	 <input type=hidden value=insert id=method>
	 <button class=mybutton onclick=saveFranco('kebun_slave_5stproduksi','".$arr."')>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>
     </fieldset><input type='hidden' id=hiddenz name=hiddenz />";
CLOSE_BOX();
OPEN_BOX();
echo"<fieldset><legend>".$_SESSION['lang']['list']."</legend><table class=sortable cellspacing=1 border=0>
     <thead>
	  <tr class=rowheader>
	   <td>No</td>
	   <td>".$_SESSION['lang']['jenisbibit']."</td>
	   <td>".$_SESSION['lang']['klasifikasitanah']."</td>
	   <td>".$_SESSION['lang']['umur']."</td>
	   <td>".$_SESSION['lang']['kgproduksi']."/Ha</td>
	   <td>".$_SESSION['lang']['action']."</td>
	  </tr>
     </thead>
     <tbody id=container>";
echo"<script>loadData()</script>";
echo"</tbody>
     <tfoot>
     </tfoot>
     </table></fieldset>";
CLOSE_BOX();
echo close_body();
?>