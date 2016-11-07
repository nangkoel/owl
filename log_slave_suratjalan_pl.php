<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

$where = "a.notransaksi like '%".$_POST['pl']."%'";
$where .= " and b.nosj IS NULL";
$query = "select distinct notransaksi from ".$dbname.".log_packinght a
	left join ".$dbname.".log_suratjalandt b
	on a.notransaksi=b.kodebarang
	where ".$where;
$data = fetchData($query);
?>
<button class=mybutton onclick="add2detail('pl')" style='margin-top:15px'>Add to Detail</button>
<div style="max-height:340px;overflow:auto">
<table cellpadding=1 cellspacing=1 border=0 class='sortable'>
	<thead><tr class=rowheader>
		<td>*</td>
		<td>No. Packing List</td>
	</tr></thead>
	<tbody id=bodySearch>
		<?php foreach($data as $key=>$row):?>
		<tr class=rowcontent>
			<td><?php echo makeElement('pl_'.$key,'checkbox',0)?></td>
			<td id="notransaksi_<?php echo $key?>"><?php echo $row['notransaksi']?></td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>
</div>