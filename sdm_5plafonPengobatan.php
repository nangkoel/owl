<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('','MEDICAL PLAFOND');
?>
<?php
	$optGolongan='';
	$str="select * from ".$dbname.". sdm_5golongan order by `kodegolongan` asc"; //echo $str;
	$res=mysql_query($str) or die(mysql_error($conn));
?>
<script type="text/javascript" src="js/sdm_setup_plafond.js"></script>
<fieldset style="width:500px;">
<table>
     <tr><td><?php echo $_SESSION['lang']['levelcode']?></td><td>
	 <?php
		while($bar=mysql_fetch_object($res))
		{
			$optGolongan.="<option value='".$bar->kodegolongan."'>".$bar->namagolongan."</option>";
		}
   $optjenis='';
   $str="select * from ".$dbname.".sdm_5jenisbiayapengobatan order by kode";
   $res=mysql_query($str);
   while($bar=mysql_fetch_object($res))
   {
   	$optjenis.="<option value='".$bar->kode."'>".$bar->nama."</option>";
   }
		
	 ?>
	 <select id="kodegolongan" name="kodegolongan">
	 	<?php echo $optGolongan;?>
	 </select>
	 </td></tr>
	 <?php
	   echo"<tr><td>".$_SESSION['lang']['jenisbiayapengobatan']."</td>
	        <td><select id=jenisbiaya>".$optjenis."</select></td></tr>";
	 ?>		 
	 <tr>
	 	<td>
	 <?php echo $_SESSION['lang']['persen']?></td><td><input type="text" id="prsn" name="prsn" size="6" onkeypress="return angka_doang(event);" class="myinputtext" maxlength=3>%/<?echo $_SESSION['lang']['tahun'];?></td></tr>
     </table>
	 <input type='hidden' id='method' value='insert'>
	 <button class='mybutton' onclick='simpanPlafon()'><?php echo $_SESSION['lang']['save']?></button>
	 <button class='mybutton' onclick='cancelPlafon()'><?php echo $_SESSION['lang']['cancel']?></button>
	 </fieldset>
<?php 	 echo open_theme($_SESSION['lang']['availavel']); ?>
<div>
<?php
	$str1="select * from ".$dbname.".sdm_pengobatanplafond order by kodegolongan";
	$res1=mysql_query($str1);
	echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
	     <thead>
		 <tr class=rowheader><td style='width:150px;'>".$_SESSION['lang']['levelcode']."</td>
		 <td>".$_SESSION['lang']['jenisbiayapengobatan']."</td>
		 <td>".$_SESSION['lang']['persen']."</td>
		 <td style='width:30px;'>*</td></tr>
		 </thead>
		 <tbody id=container>";?>
	<?php
	while($bar1=mysql_fetch_object($res1))
	{
		echo"<tr class=rowcontent><td align=center>".$bar1->kodegolongan."</td>
		<td align=center>".$bar1->kodejenisbiaya."</td>
		<td align=right>".$bar1->persen."</td><td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodegolongan."','".$bar1->persen."','".$bar1->kodejenisbiaya."');\"></td></tr>";
	}	 
	?>
 
		 </tbody>
		 <tfoot>
		 </tfoot>
		 </table>
</div>

	<?php
echo close_theme();
CLOSE_BOX();
echo close_body();
?>