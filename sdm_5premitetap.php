<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zMysql.php');
include('lib/zFunction.php');
echo open_body();
?>
<script language=javascript src='js/zMaster.js'></script>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript1.2 src='js/sdm_5premitetap.js'></script>
<script>

</script>
<?php
 $optTipe="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
 $optTipe5=$optTipe;
$arrd=array("0"=>"Premi Tetap","1"=>"Insentif");
foreach($arrd as $rwdd=>$lstarr){
     $optTipe2.="<option value='".$rwdd."'>".$lstarr."</option>";
     $optTipe.="<option value='".$rwdd."'>".$lstarr."</option>";
}
$arr="##tpTransaksi##pilInp##premiIns##method";
include('master_mainMenu.php');
OPEN_BOX();

echo"<fieldset style='width:380px;float:left;'>
     <legend><b>".$_SESSION['lang']['premitetap']."</b></legend>
	 <table>
	 
           <tr>
	   <td>".$_SESSION['lang']['tipetransaksi']." </td>
	    <td><select id=tpTransaksi style='width:150px;' onchange='getDt(0,0)'>".$optTipe."</select></td>
	 </tr>	
         <tr>
	   <td>".$_SESSION['lang']['kodejabatan']."/".$_SESSION['lang']['tipekaryawan']." </td>
	    <td><select id=pilInp style=width:150px;>".$optTipe5."</select> <img src='images/search.png' style=cursor:pointer onclick=\"searchNopo('".$_SESSION['lang']['find']." ".$_SESSION['lang']['kodejabatan']."/".$_SESSION['lang']['tipekaryawan']." ','<div id=formPencariandata></div>',event)\"</td>
	 </tr>	
	 <tr>
	   <td>".$_SESSION['lang']['premi']."/Insentif</td>
	   <td><input type=text class=myinputtextnumber id=premiIns style=width:150px; onkeypress='return angka_doang(event)' /></td>
	 </tr>	 
	 </table>
	 <input type=hidden value=insert id=method>
	 <button class=mybutton onclick=saveFranco('sdm_slave_5premitetap','".$arr."')>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>
     </fieldset>";
 
CLOSE_BOX();
OPEN_BOX();
echo"<fieldset  style=width:750px;><legend>".$_SESSION['lang']['list']."</legend>
     <table><tr>
        <td>".$_SESSION['lang']['tipetransaksi']." </td>
        <td><select id=tpTransaksi2 style='width:150px;' onchange='loadData()'>".$optTipe2."</select></td>
	 </tr>	</table>
     <table class=sortable cellspacing=1 border=0>
     <thead>
	  <tr class=rowheader>
           <td>".$_SESSION['lang']['tipetransaksi']."</td>
           <td>".$_SESSION['lang']['kodejabatan']."/".$_SESSION['lang']['tipekaryawan']."</td>
	   <td>".$_SESSION['lang']['premi']."/insentif</td>
        
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