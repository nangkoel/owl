<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

$where = "a.kodept like '%".$_POST['kodept']."%'";
$where .= " and b.nosj IS NULL";
if(!empty($_POST['sj'])) {
	$where .= " and a.nosj like '%".$_POST['s']."%'";
}
$query = "select distinct 
		a.nosj,a.nopo,a.kodebarang,a.nopp,
		a.jumlah,a.satuanpo,a.jenis
	from ".$dbname.".log_suratjalandt a
	left join ".$dbname.".log_konosemendt b
	on a.kodebarang=b.kodebarang and a.nopo=b.nopo
	where ".$where;
$data = fetchData($query);

$optBarang = makeOption($dbname,'log_5masterbarang','kodebarang,namabarang');
?>
<button class=mybutton onclick="add2detail('sj')" style='margin-top:15px'>Add to Detail</button>
<div style="max-height:340px;overflow:auto">
<table cellpadding=1 cellspacing=1 border=0 class='sortable'>
	<thead><tr class=rowheader>
		<td>*</td>
		<td><?php echo $_SESSION['lang']['nosj']?></td>
		<td><?php echo $_SESSION['lang']['jenis']?></td>
		<td><?php echo $_SESSION['lang']['kodebarang']?></td>
		<td><?php echo $_SESSION['lang']['namabarang']?></td>
		<td><?php echo $_SESSION['lang']['nopo']?></td>
		<td><?php echo $_SESSION['lang']['nopp']?></td>
		<td><?php echo $_SESSION['lang']['jumlah']?></td>
		<td><?php echo $_SESSION['lang']['satuan']?></td>
	</tr></thead>
	<tbody id=bodySearch>
		<?php foreach($data as $key=>$row):?>
		<tr class=rowcontent>
			<td><?php echo makeElement('sj_'.$key,'checkbox',0)?></td>
			<td id="nosj_<?php echo $key?>"><?php echo $row['nosj']?></td>
			<td id="jenis_<?php echo $key?>"><?php echo $row['jenis']?></td>
			<td id="kodebarang_<?php echo $key?>"><?php echo $row['kodebarang']?></td>
			<td id="namabarang_<?php echo $key?>">
				<?php echo ($row['jenis']=='PO')? $optBarang[$row['kodebarang']]: '';?>
			</td>
			<td id="nopo_<?php echo $key?>"><?php echo $row['nopo']?></td>
			<td id="nopp_<?php echo $key?>"><?php echo $row['nopp']?></td>
			<td id="jumlah_<?php echo $key?>" align=right><?php echo $row['jumlah']?></td>
			<td id="satuan_<?php echo $key?>"><?php echo ($row['jenis']=='PO')? $row['satuanpo']: 'PETI'?></td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>
</div>