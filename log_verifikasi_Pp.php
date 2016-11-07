<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX(); //1 O
?>
<div id="action_list">
<?php
echo"<table>
     <tr valign=moiddle>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayFormInput()>
	   <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
	 <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
			echo $_SESSION['lang']['carinopp'].":<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>";
			echo $_SESSION['lang']['tanggal'].":<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />";
			echo"<button class=mybutton onclick=cariNopp()>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>
     </tr>
	 </table> "; 
?>
</div>
<?php
CLOSE_BOX(); //1 C
echo "<div id=\"list_pp_verication\">";
OPEN_BOX(); //2 O
?>
<fieldset>
<legend><?php echo $_SESSION['lang']['list_pp'];?></legend>
<div style="overflow:scroll; height:420px;">
	 <table class="sortable" cellspacing="1" border="0">
	 <thead>
	 <tr class=rowheader>
	 <td>No.</td>
	 <td><?php echo $_SESSION['lang']['namaorganisasi']?></td>
	 <td><?php echo $_SESSION['lang']['nopp'];?></td> 
	 <td><?php echo $_SESSION['lang']['namabarang'];?></td>
 	 <td><?php echo $_SESSION['lang']['jmlhDiminta'];?></td>
 	 <td><?php echo $_SESSION['lang']['namaorganisasi'];?></td>
 	 <td><?php echo $_SESSION['lang']['namaorganisasi'];?></td>
	  <td><?php echo "Progress";?></td>
	 <td colspan="3" align="center">Action</td>
	 </tr>
	 </thead>
	 <tbody id="contain">
	
	 <?php 
	$str="select * from ".$dbname.".log_prapoht a inner join log_prapodt b on a.nopp=b.nopp order where a.close='1' by nopp desc";
  if($res=mysql_query($str))
  {
	while($bar=mysql_fetch_object($res))
	{
		$koderorg=$bar->kodeorg;
		$spr="select * from  ".$dbname.".organisasi where  kodeorganisasi='".$koderorg."' or induk='".$koderorg."'"; //echo $spr;
		$rep=mysql_query($spr) or die(mysql_error($conn));
		$bas=mysql_fetch_object($rep);
		$no+=1;
			
		if($bar->close=='0')
		{
		
			$b="<a href=# id=seeprog onclick=frm_aju('".$bar->nopp."','".$bar->close."') title=\"Click To Change The Status \">Need Approval</a>";
		}
		elseif($bar->close=='1')
		{
			$b="<a href=# id=seeprog onclick=frm_aju('".$bar->nopp."','".$bar->close."') title=\"Click To Change The Status\">Waiting Approval</a>";
		}
		elseif($bar->close=='2')
		{
			$b="<a href=# id=seeprog onclick=frm_aju('".$bar->nopp."','".$bar->close."') title=\"Can Make PO\">Approved</a>";
		}
		echo"<tr class=rowcontent id='tr_".$no."'>
		      <td>".$no."</td>
		      <td>".$bar->nopp."</td>
			  <td>".tanggalnormal($bar->tanggal)."</td>
			  <td>".$bas->namaorganisasi."</td>
			  <td>".$b."</td>
		 <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->nopp."','".tanggalnormal($bar->tanggal)."','".$bar->kodeorg."','".$bar->close."');\"></td>
			  <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPp('".$bar->nopp."','".$bar->close."');\"></td>
			  <td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_prapoht','".$bar->nopp."','','log_slave_print_log_pp',event);\"></td>
			 </tr>";
	}	 	   	
  }	
  else
	{
		echo " Gagal,".(mysql_error($conn));
	}	
	
	 ?>
	  </tbody>
	 <tfoot>
	 </tfoot>
	 </table></div>
</fieldset>
<?php
echo"</div>";
CLOSE_BOX();
?>