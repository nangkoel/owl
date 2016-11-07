<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/satuan.js></script>
<?php
include('master_mainMenu.php');
OPEN_BOX();
echo"<fieldset>
     <legend><b>".$_SESSION['lang']['satuan']."</b></legend>
	 Note:".$_SESSION['lang']['uomnote']."<br>
	 <br>".$_SESSION['lang']['satuan']."<b id=old></b><input type=text class=myinputtext id=satuan onkeypress=\"return tanpa_kutip(event);\" size=10 maxlength=10>
	  
	  <input type=hidden id=method value=insert>
	  <button class=mybutton onclick=saveSatuan()>".$_SESSION['lang']['save']."</button>
	  <button class=mybutton onclick=cancelSatuan()>".$_SESSION['lang']['cancel']."</button>
     </fieldset>";
CLOSE_BOX();

OPEN_BOX();
echo "<fieldset>
       <legend><b>".$_SESSION['lang']['satuan']." ".$_SESSION['lang']['list']."</b></legend>
       <div style='width:100%; height:400px;overflow:auto;'>";
$str="select * from ".$dbname.".setup_satuan order by satuan";
$res=mysql_query($str);
echo "<table class=sortable cellspacing=1 border=0>
      <thead>
	    <tr class=rowheader>
		 <td>
		 	No
		 </td>
		 <td>
		    ".$_SESSION['lang']['satuan']."
		 </td>
		 <td>
		 </td>
		</tr>
	  </thead>
	  <tbody id=container>
	  ";
$no=0;	  
while($bar=mysql_fetch_object($res))
{
	$no+=1;
	echo"<tr class=rowcontent>
		 <td>
		 	".$no."
		 </td>
		 <td>
		    ".$bar->satuan."
		 </td>
		  <td>
		      <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->satuan."');\"> 
			  <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delSatuan('".$bar->satuan."');\">
		  </td>		 
		</tr>";	
}
echo"</tbody><tfoot></tfoot></table></div></fieldset>";
CLOSE_BOX();
echo close_body();
?>