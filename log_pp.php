<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX(); //1 O
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script type="text/javascript" src="js/log_pp.js" /></script>


<?php

	$user_id=$_SESSION['standard']['userid'];
	if($user_id=='' or $user_id==0000000000)
	{
		echo "Error : You do not have organization code and license to create PR";
		CLOSE_BOX();
		echo close_body();
		exit;
	}
?>

<script>
 jdl_ats_0='<?php echo $_SESSION['lang']['find']?>';
// alert(jdl_ats_0);
 jdl_ats_1='<?php echo $_SESSION['lang']['findBrg']?>';
 content_0='<fieldset><legend><?php echo $_SESSION['lang']['findnoBrg']?></legend>Find&nbsp;<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div>';

 jdl_bwh_0='<?php echo $_SESSION['lang']['find']?>';
 jdl_bwh_1='<?php echo $_SESSION['lang']['findAngrn']?>';
 content_1='<fieldset><legend><?php echo $_SESSION['lang']['findnoAngrn']?></legend>Find<input type=text class=myinputtext id=no_angrn><button class=mybutton onclick=findAngrn()>Find</button></fieldset><div id=container></div>';

	title_d='PR Submission';
	content_d='<fieldset><legend><?php echo $_SESSION['lang']['findnoAngrn']?></legend><div id=container></div>';
	ev_d='event';
	
	baTal='<?php echo $_SESSION['lang']['cancel']?>';
	Done='<?php echo $_SESSION['lang']['done']?>'
</script><br />
<div id="action_list">
<?php
//echo"<pre>";
//print_r($_SESSION);
//echo"</pre>";exit();
echo"<table>
     <tr valign=moiddle>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayFormInput()>
	   <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
	 <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
			echo $_SESSION['lang']['carinopp'].":<input type=text id=txtsearch size=25 maxlength=30 onkeypress=\"return validat(event);\" class=myinputtext>";
			echo $_SESSION['lang']['tanggal'].":<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />";
			echo"<button class=mybutton onclick=cariNopp()>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>
     </tr>
	 </table> "; 
?>
</div>
<?php
CLOSE_BOX(); //1 C
echo "<div id=\"list_pp\">";
OPEN_BOX(); //2 O
?>
<fieldset>
<legend><?php echo $_SESSION['lang']['list_pp'];?></legend>
<img src="images/pdf.jpg" onclick="masterPDF('log_prapoht','','','log_print_pdf_pp',event)" width="20" height="20" />
<img onclick="javascript:print()" style="width: 20px; height: 20px; cursor: pointer;" title="Print Page" src="images/printer.png">
<div style="overflow:scroll; height:420px;">
	 <table class="sortable" cellspacing="1" border="0">
	 <thead>
	 <tr class=rowheader>
	 <td>No.</td>
	 <td><?php echo $_SESSION['lang']['nopp']?></td>
	 <td><?php echo $_SESSION['lang']['tanggal'];?></td> 
	 <td><?php echo $_SESSION['lang']['namaorganisasi'];?></td>
	 <td><?php echo $_SESSION['lang']['dbuat_oleh']?>
	  <td><?php echo "Progress";?></td>
	 <td align="center">Action</td>
	 </tr>
	 </thead>
	 <tbody id="contain">
	<script>loadData()</script>

	  </tbody>
	 <tfoot>
	 </tfoot>
	 </table></div>
</fieldset>

<?php
CLOSE_BOX(); //2 C
echo"</div>";
//div form
?>
<div id="form_pp" style="display:none;">
<?php
OPEN_BOX();
?>

 <?php

		$optBagian='';
		if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
		{
			$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where CHAR_LENGTH(kodeorganisasi)='4'";
		}
		else
		{
			$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where `kodeorganisasi`='".substr($_SESSION['empl']['lokasitugas'],0,4)."'"; //echo $str;
		}
		$res=mysql_query($str) or die(mysql_error($conn));
	?>
<fieldset>
	<legend><?php echo $_SESSION['lang']['prmntaanPembelian']?></legend>
	<table cellspacing="1" border="0" id="opl">
	 <tr>
			<td><?php echo $_SESSION['lang']['namaorganisasi']?></td>
			<td>:</td>
		  <td><?php
			while($bar=mysql_fetch_object($res))
			{
				$optBagian.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";	
			}
			?>
			<select id="kd_bag" style='width:150px;' onchange="get_isi(this.options[this.selectedIndex].value,this.options[this.selectedIndex].text)">
			<option value="" selected="selected"></option>
			<?php echo $optBagian; ?>
			</select>	</td>
		</tr>
		<tr>
			<td><?php echo $_SESSION['lang']['nopp']?></td>
			<td>:</td>
			<td><input type="text" id="nopp" class="myinputtext" disabled="disabled" style='width:150px;' /></td>
		</tr>		
		<tr>
			<td><?php echo $_SESSION['lang']['tanggal']?></td>
			<td>:</td>
                        <?php
                            $as=date("Y-m-d");
                            echo"<td><input type=text class=myinputtext value=".tanggalnormal($as)." id=tgl_pp name=tgl_pp onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false\";   maxlength=10  style=width:150px; /></td>
                        ";//<td><input type="text" class="myinputtext" id="tgl_pp" name="tgl_pp" value="<?php echo tanggalnormal($as); " readonly="readonly" style='width:150px;' /></td>
                            ?>
			
		</tr>
        <tr>
			<td><?php echo $_SESSION['lang']['catatan']?></td>
			<td>:</td>
                        
			<td><textarea id=catatan name="catatan" cols="50" rows="5"></textarea></td>
		</tr>
    <!--ALTER TABLE  `log_prapoht` ADD  `catatanpp` TEXT NOT NULL-->    
		<tr>
			<td colspan="3">
			<input type="hidden" id="method" value="insert" />
            <input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION['standard']['userid']?>" />
			<button class=mybutton id="dtl_pem" onclick=detailPembelian()><?php echo $_SESSION['lang']['save']?></button>			</td>
		</tr>
	</table>
</fieldset><br />
<br />
<fieldset>
<legend><?php echo $_SESSION['lang']['detailprmntaanPembelian']?></legend><br />
<div id="detailTable" style="display:none;">
<!-- content detail pp-->
    
</div>
<div id="tmbl_all"> 	

</div>
</fieldset>

<?php
CLOSE_BOX();
echo "</div>";
?>
<!--div persetujuan-->
<div id="persetujuan" style="display:none;">
<?php
OPEN_BOX();
?>
    <div id="persetujuandata"></div>
<?php 
CLOSE_BOX();
echo "</div>";
?>
<?php echo close_body(); ?>