<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
include('lib/jAddition.php');
OPEN_BOX();
?>
<script type="text/javascript" src="js/setup_keycode.js" /></script>

<fieldset>
	<legend><?php echo $_SESSION['lang']['setupKeycode']?></legend>
	<table cellspacing="1" border="0">
		<tr>
			<td><?php echo $_SESSION['lang']['keycode']?></td>
			<td>:</td>
			<td><input type="text" id="keyCode" name="keyCode" onKeyPress="return tanpa_kutip(event);" class="myinputtext"  style="width:150px;"/>
            <input type="hidden" id="oldCode" name="oldCode"  />
				</td>
		</tr>
		<tr>
			<td><?php echo $_SESSION['lang']['keterangan']?></td>
			<td>:</td>
			<td><input type="text" id="ket"  name="ket" onKeyPress="return tanpa_kutip(event);" class="myinputtext"  style="width:150px;"/></td>
		</tr>
		<input type="hidden" id="method" value="insert" />
		<tr>
			<td colspan="3">
			<button class="mybutton" onclick="smpnKeycode()"><?php echo $_SESSION['lang']['save']?></button>
			<button class="mybutton" onclick="cancelKeycode()"><?php echo $_SESSION['lang']['cancel']?></button></td>
		</tr>
	</table>
</fieldset>
<?php CLOSE_BOX();
 OPEN_BOX();
?>
<fieldset>
	 <table class="sortable" cellspacing="1" border="0">
	 <thead>
	 <tr class=rowheader>
	 <td>No.</td>
	 <td><?php echo $_SESSION['lang']['keycode']?></td>
	 <td><?php echo $_SESSION['lang']['keterangan'];?></td> 
	 <td>Action</td>
	 </tr>
	 </thead>
	 <tbody id="container">
	 <?php 
	$limit=10;
	$page=0;
	if(isset($_POST['page']))
	{
	$page=$_POST['page'];
	if($page<0)
	$page=0;
	}
	$offset=$page*$limit;
	
	$ql2="select count(*) as jmlhrow from ".$dbname.".setup_keycode  order by code desc";// echo $ql2;
	$query2=mysql_query($ql2) or die(mysql_error());
	while($jsl=mysql_fetch_object($query2)){
	$jlhbrs= $jsl->jmlhrow;
	}
	
	$str="select * from ".$dbname.".setup_keycode order by code desc limit ".$offset.",".$limit."";
	if($res=mysql_query($str))
	{
	while($bar=mysql_fetch_object($res))
	{
	
	$no+=1;
	echo"<tr class=rowcontent id='tr_".$no."'>
	<td>".$no."</td>
	<td id='nmorg_".$no."'>".$bar->code."</td>
	<td id='kpsits_".$no."'>".$bar->keterangan."</td>
	<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->code."','".$bar->keterangan."');\"></td>
	</tr>";
	}	 
	echo" 
	</tr><tr class=rowheader><td colspan=3 align=center>
	".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
	<br />
	<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
	<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
	</td></tr>"; 	   	
	}	
	else
	{
	echo " Gagal,".(mysql_error($conn));
	}	
	
	 ?>
	  </tbody>
	 <tfoot>
	 </tfoot>
	 </table>
</fieldset>

<?php
CLOSE_BOX();
echo close_body();
?>