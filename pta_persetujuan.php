<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/pta_persetujuan.js'></script>
<script>
    tolak="<?php echo $_SESSION['lang']['ditolak'];?>";
    ajukan="<?php echo $_SESSION['lang']['diajukan'];?>";
    setujuak="<?php echo $_SESSION['lang']['setujuakhir'];?>";
    </script>
<?php
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['persetujuan']." PTA").'</b>');
echo"<table cellpadding=1 cellspacing=1 border=0>";
echo"<tr><td>".$_SESSION['lang']['daftar']." PTA</td></tr>";
echo"<tr><td>".$_SESSION['lang']['find']." <input type=text class=myinputtext onkeypress='return tanpa_kutip(event)' id='txtCari' />";
echo"&nbsp;<button onclick='loadData()' class=mybutton>".$_SESSION['lang']['find']."</button>";
echo"</td></tr>";
echo"</table>";
echo"
      <div style='width:100%;height:359px;overflow:scroll;'>
       <table class=sortable cellspacing=1 border=0 width=80%>
	     <thead>
		    <tr>
			  <td align=center>No.</td>
			  <td align=center>".$_SESSION['lang']['nopta']."</td>
			  <td align=center>".$_SESSION['lang']['penjelasan']."</td>
			  <td align=center>".$_SESSION['lang']['jumlah']." (Rp.)</td>
                         
                          <td align=center colspan=3>".$_SESSION['lang']['action']."</td>
			</tr>  
		 </thead>
		 <tbody id=container><script>loadData()</script>
		 </tbody>
		 <tfoot>
		 </tfoot>		 
	   </table>
     </div>";
CLOSE_BOX();
close_body();
?>