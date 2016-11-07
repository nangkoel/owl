<?php
	require_once('master_validation.php');
	include('lib/nangkoelib.php');
	include_once('lib/zLib.php');	
	echo open_body();
	include('master_mainMenu.php');
	OPEN_BOX();
?>
	<script language=javascript src=js/zTools.js></script>
	<script language=javascript src=js/zReport.js></script>
	<link rel=stylesheet type=text/css href=style/zTable.css>
	<?php		
		$optBarang = "<option value=''>".$_SESSION['lang']['all']."</option>";
		$qBarang = "select kodebarang,namabarang from ".$dbname.".log_5masterbarang where kelompokbarang = '045' order by kodebarang asc";
		$eBarang = mysql_query($qBarang) or die(mysql_error($conn));
		while($rBarang=mysql_fetch_assoc($eBarang))
		{
			$optBarang.="<option value=".$rBarang['kodebarang'].">".$rBarang['namabarang']."</option>";
		}
		
		
		$arr="##tglAwal##tglAkhir##kdBrg";
	?>
	<div>
		<fieldset style="float:left;">
			<legend><b>Laporan Pengeluaran Pupuk</b></legend>
			<table cellspacing="1" border="0">
				<tr>
					<td><label><?php echo $_SESSION['lang']['namabarang']?></label></td>
					<td><select id="kdBrg" name="kdBrg" style="width:250px"><?php echo $optBarang?></select></td>
				</tr>
				<tr>
					<td><label><?php echo $_SESSION['lang']['tgldari']?></label></td>
					<td>
						<input type="text" class="myinputtext" id="tglAwal" name="tglAwal" onmousemove="setCalendar(this.id)" 
						 onkeypress="return false;"  maxlength="10" style="width:250px;"></input>
					</td>
				</tr>
				<tr>
					<td><label><?php echo $_SESSION['lang']['tglsmp']?></label></td>
					<td>
						<input type="text" class="myinputtext" id="tglAkhir" name="tglAkhir" onmousemove="setCalendar(this.id)" 
						 onkeypress="return false;"  maxlength="10" style="width:250px;"></input>
					</td>
				</tr>
				<tr height="20"><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td colspan="2">
						<button onclick="zPreview('log_slave_2PemakaianPupuk','<?php echo $arr?>','printContainer')" 
						class="mybutton" name="preview" id="preview">Preview</button>
						<button onclick="zExcel(event,'log_slave_2PemakaianPupuk','<?php echo $arr?>')" 
						class="mybutton" name="preview" id="preview">Excel</button>
					</td>
				</tr>
			</table>
		</fieldset>
	</div>

		
	<fieldset style='clear:both;'>
		<legend><b>Print Area</b></legend>
		<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'></div>
	</fieldset>

	
<?php
	CLOSE_BOX();
	echo close_body();
?>