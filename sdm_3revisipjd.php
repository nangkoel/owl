<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/sdm_3revisipjd.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['spdinas']." ".$_SESSION['lang']['koreksi']);
	
		
$frm[0].="<fieldset>
     <legend>".$_SESSION['lang']['form']."</legend>
	 <table>
		<tr>
		   <td>".$_SESSION['lang']['notransaksi']."</td>
		   <td><input type=text class=myinputtext id=notransaksi onkeypress='return validat(event);' value=''>&nbsp;<span id=isiNotrans style='display:none'><input type=text class=myinputtext id=notransaksi2  value=''></span>
                       <button class=mybutton onclick=cariDt()>".$_SESSION['lang']['find']."</button>
		   </td>		   
		</tr>	
	 </table>
	 <fieldset>
	    <legend>".$_SESSION['lang']['datatersimpan']."</legend>
		<table class=sortable cellspacing=1>
		<thead>
		 <tr>
                        <td>No.</td>
		        <td>".$_SESSION['lang']['jenisbiaya']."</td>
		        <td>".$_SESSION['lang']['tanggal']."</td>
		        <td>".$_SESSION['lang']['keterangan']."</td>
		        <td>".$_SESSION['lang']['jumlah']."</td>
			<td>".$_SESSION['lang']['disetujui']."</td>
		</tr>	
		 </thead>	
		 <tbody id=innercontainer>
		 </tbody>
		 <tfoot>
		 </tfoot>
		</table>
		<button class=mybutton onclick=selesai()>".$_SESSION['lang']['done']."</button>
		<button class=mybutton onclick=batalkan()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>
	 </fieldset>
	 "; 
//=====================================

//==================================================	 	 
$hfrm[0]=$_SESSION['lang']['form'];

	 
drawTab('FRM',$hfrm,$frm,100,900);	  
CLOSE_BOX();
echo close_body();
?>