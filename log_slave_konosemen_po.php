<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

$where = "a.kodeorg like '%".$_POST['kodept']."%' and a.statuspo=3";
// $where .= " and b.nosj IS NULL and c.nokonosemen IS NULL";
if(!empty($_POST['po'])) {
	$where .= " and a.nopo like '%".$_POST['po']."%'";
}
$query = "select distinct 
		a.nopo,a.kodebarang,a.namabarang,a.nopp,
		a.jumlahpesan,a.satuan
	from ".$dbname.".log_po_vw a
	where ".$where;
$data = fetchData($query);

//$q2 = "SELECT nopo,kodebarang,sum(jumlah) as jumlah FROM ".$dbname.".`log_rinciankono` where nopo like '%".$_POST['po']."%'";
//$data2 = fetchData($q2);
//$optData = array();
//foreach($data2 as $row) {
//	$optData[$row['nopo']][$row['kodebarang']] = $row['jumlah'];
//}
$q2 = "SELECT nopo,kodebarang,sum(jumlah) as jumlah,nopp FROM ".$dbname.".`log_packing_vw` where nopo like '%".$_POST['po']."%' group by nopp";
$data2 = fetchData($q2);
$optData = array();
foreach($data2 as $row) {
	$optData[$row['nopo'].$row['nopp']][$row['kodebarang']] = $row['jumlah'];
}
?>
<button class=mybutton onclick="add2detail('po')" style='margin-top:15px'>Add to Detail</button>
<div style="max-height:340px;overflow:auto">
<table cellpadding=1 cellspacing=1 border=0 class='sortable'>
	<thead><tr class=rowheader>
		<td>*</td>
		<td><?php echo $_SESSION['lang']['nopo']?></td>
		<td><?php echo $_SESSION['lang']['kodebarang']?></td>
		<td><?php echo $_SESSION['lang']['namabarang']?></td>
		<td><?php echo $_SESSION['lang']['nopp']?></td>
		<td><?php echo $_SESSION['lang']['jumlah']?></td>
		<td><?php echo $_SESSION['lang']['jumlah'].' terkirim'?></td>
		<td><?php echo $_SESSION['lang']['satuan']?></td>
	</tr></thead>
	<tbody id=bodySearch>
		<?php 
		$i=0;
		foreach($data as $key=>$row):?>
		<?php if(
			(isset($optData[$row['nopo'].$row['nopp']][$row['kodebarang']]) and $row['jumlahpesan']>$optData[$row['nopo'].$row['nopp']][$row['kodebarang']])
			or !isset($optData[$row['nopo'].$row['nopp']][$row['kodebarang']])):?>
		<tr class=rowcontent>
			<td><?php echo makeElement('po_'.$key,'checkbox',0)?></td>
			<td id="nopo_<?php echo $key?>"><?php echo $row['nopo']?></td>
			<td id="kodebarang_<?php echo $key?>"><?php echo $row['kodebarang']?></td>
			<td id="namabarang_<?php echo $key?>"><?php echo $row['namabarang']?></td>
			<td id="nopp_<?php echo $key?>"><?php echo $row['nopp']?></td>
			<td id="jumlah_<?php echo $key?>" align=right><?php echo $row['jumlahpesan']?></td>
			<td id="jumlahkirim_<?php echo $i?>" align=right>
				<?php echo (isset($optData[$row['nopo'].$row['nopp']][$row['kodebarang']]))? $optData[$row['nopo'].$row['nopp']][$row['kodebarang']]: 0?>
			</td>
			<td id="satuan_<?php echo $key?>"><?php echo $row['satuan']?></td>
		</tr>
		<?php 
		$i++;
		endif;?>
		<?php endforeach;?>
	</tbody>
</table>
</div>