<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
?>
<script language=javascript1.2 src='js/keu_5piutangbrg.js'></script>
<?php
		$optkegiatan="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$i="select * from ".$dbname.".sdm_ho_component where name like '%Angsuran%' or name like '%ANGSURAN%' or name like '%Pot%' and id not in (46,47,52,37,74,50,59,49,53,36,51,75,70,68,16,63,18) order by name";// where pinjamanid=1
		$n=mysql_query($i) or die (mysql_error($conn));
		while($d=mysql_fetch_assoc($n))
		{
			$optkegiatan.="<option value='".$d['id']."'>".$d['id']."-".$d['name']."</option>";
		}
		$optBrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$i="select * from ".$dbname.".log_5masterbarang where kodebarang like '040%'";
		$n=mysql_query($i) or die (mysql_error($conn));
		while($d=mysql_fetch_assoc($n))
		{
			$optBrg.="<option value='".$d['kodebarang']."'>".$d['kodebarang']."-".$d['namabarang']."</option>";
		}
 
        
        
?>


<?php
OPEN_BOX();
//print_r($_SESSION['empl']['regional']);
echo"<fieldset style='float:left;'>";
		 
		echo"<legend>Form Daftar Material Piutang Karyawan</legend>";
		 
			echo"<table border=0 cellpadding=1 cellspacing=1>
				<tr>
					<td>".$_SESSION['lang']['nama']."</td> 
					<td>:</td>
					<td><select id=idcom style=\"width:150px;\">".$optkegiatan."</select></td>
				</tr>

				<tr>
					<td>".$_SESSION['lang']['namabarang']."</td> 
					<td>:</td>
					<td><select id=kdbrg style=\"width:200px;\">".$optBrg."</select></td>
				</tr>
				 
				<tr><td colspan=2></td>
					<td colspan=3>
						<button class=mybutton onclick=simpan()>Simpan</button>
						<button class=mybutton onclick=cancel()>Hapus</button>
					</td>
				</tr>
			
			</table></fieldset>
					<input type=hidden id=method value='insert'>
					<input type=hidden id=oldId value=''>
					<input type=hidden id=oldBrgId value=''>";
 


CLOSE_BOX();
?>

<?php
OPEN_BOX();
//$optTahunBudgetHeader="<option value=''>".$_SESSION['lang']['all']."</option>";
//ISI UNTUK DAFTAR 
 
echo "<fieldset style=float:left;clear:both;>
		<legend>".$_SESSION['lang']['list']."</legend>
		<div id=container> 
			<script>loadData(0)</script>
		</div>
	</fieldset>";
CLOSE_BOX();
echo close_body();					
?>