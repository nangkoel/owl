<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

$where = "a.namabarang like '%".$_POST['mat']."%'";
$query = "select * from ".$dbname.".log_5masterbarang a where ".$where;
$data = fetchData($query);
?>
<button class=mybutton onclick="add2detail('m')" style='margin-top:15px'>Add to Detail</button>
<div style="max-height:340px;overflow:auto">
<table cellpadding=1 cellspacing=1 border=0 class='sortable'>
	<thead><tr class=rowheader>
		<td>*</td>
		<td>Kode</td>
		<td>Nama</td>
		<td>Satuan</td>
	</tr></thead>
	<tbody id=bodySearch>
		<?php foreach($data as $key=>$row):?>
		<tr class=rowcontent>
			<td><?php echo makeElement('m_'.$key,'checkbox',0)?></td>
			<td id="kodebarang_<?php echo $key?>"><?php echo $row['kodebarang']?></td>
			<td id="namabarang_<?php echo $key?>"><?php echo $row['namabarang']?></td>
			<td id="satuan_<?php echo $key?>"><?php echo $row['satuan']?></td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>
</div>