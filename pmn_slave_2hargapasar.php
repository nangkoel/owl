<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include('lib/zMysql.php');
include('lib/zFunction.php');
include_once('lib/zLib.php');
require_once('jpgraph/jpgraph.php');
require_once ('jpgraph/jpgraph_line.php');

//$arr="##tglHarga##kdBarang##satuan##idPasar##idMatauang##hrgPasar##proses";
$proses= isset($_POST['proses'])? $_POST['proses']: $_GET['proses'];
$periodePsr= isset($_POST['periodePsr'])? $_POST['periodePsr']: $_GET['periodePsr'];
$barang= isset($_POST['barang'])? $_POST['barang']: $_GET['barang'];
$tglHarga=isset($_POST['tglHarga'])? tanggalsystem($_POST['tglHarga']): '';
$where = '';
$whr="kelompokbarang='400'";
$optNmBarang=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang',$whr);

if($periodePsr!='') {
    $where.=" and tanggal like '".$periodePsr."%'";
} else {
    exit("Error:Periode Tidak Boleh Kosong");
}
if($barang!='') {
    $where.=" and kodeproduk = '".$barang."'";
} else {
    exit("Error:Komoditi harus dipilih");
}
$str = "select * from ".$dbname.".pmn_hargapasar where tanggal!='' ".$where." order by `tanggal` asc";

$resHarga = fetchData($str);

$optPasar = makeOption($dbname,'pmn_5pasar','id,namapasar');

// Rearrange Data
$dataHarga = $data1 = $data2 = $pasarHarga = array();
$tmpPeriod = explode('-',$periodePsr);
$dayInMonth = cal_days_in_month(CAL_GREGORIAN, $tmpPeriod[1], $tmpPeriod[0]);

foreach($resHarga as $row) {
	$tmpTgl = date('d',strtotime($row['tanggal']));
	$dataHarga[$tmpTgl][$row['pasar']] = $row['harga'];
	if(!empty($row['catatan'])) {
		$data2[] = array(
			'tanggal' => tanggalnormal($row['tanggal']),
			'pasar' => $row['pasar'],
			'catatan' => $row['catatan']
		);
	}
}

foreach($optPasar as $id=>$nama) {
	$pasarHarga[$id] = 0;
}

for($i=1;$i<=$dayInMonth;$i++) {
	foreach($optPasar as $id=>$nama) {
		
		if(strlen($i)<2)
		{
			$i="0".$i;
		}
		
		if(isset($dataHarga[$i][$id])) {
			$pasarHarga[$id] = $dataHarga[$i][$id];
		}
		else
		{
			//unset($pasarHarga[$id]);
			$pasarHarga[$id]=0;
		}
		
		$data1[$i][$id] = $pasarHarga[$id];
	}
}
/*echo "<pre>";
print_r($data1);
echo "</pre>";*/
$arr="##periodePsr##barang";
if($proses=='preview') {
	$border="border=0";
	echo makeElement('excel1','btn','Export to Excel',array('onclick'=>"zExcel(event,'pmn_slave_2hargapasar.php','".$arr."')"));
}
else
{
	$border="border=1";
}
ob_start();
?>
<table class=sortable cellspacing=1 <?php echo $border ?>>
	<thead>
		<tr class=rowheader>
			<td ><?php echo $_SESSION['lang']['tanggal']?></td>
			<?php foreach($optPasar as $pasar):?>
			<td><?php echo $pasar;?></td>
			<?php endforeach;?>
		</tr>
	</thead>
	<tbody>
		<?php foreach($data1 as $day=>$row):?>
		<tr class=rowcontent>
			<td><?php echo $day?></td>
			<?php foreach($row as $price):?>
			<td align=right><?php echo number_format($price,2);?></td>
			<?php endforeach;?>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>
<br>
<div><?php echo $_SESSION['lang']['catatan']?></div>
<table class=sortable cellspacing=1 border=0>
	<thead>
		<tr class=rowheader>
			<td><?php echo $_SESSION['lang']['tanggal']?></td>
			<td><?php echo $_SESSION['lang']['pasar']?></td>
			<td><?php echo $_SESSION['lang']['catatan']?></td>
		</tr>
	</thead>
	<tbody>
		<?php foreach($data2 as $row):?>
		<tr class=rowcontent>
			<td><?php echo $row['tanggal']?></td>
			<td><?php echo $optPasar[$row['pasar']]?></td>
			<td><?php echo $row['catatan']?></td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>
<br>
<?php
$tab = ob_get_contents();
ob_end_clean();

if($proses=='excel') {
	$tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
	$dte=date("Hms");
	$nop_="hargaPasar_".$dte;
	$gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
	gzwrite($gztralala, $tab);
	gzclose($gztralala);
	echo "<script language=javascript1.2>
	window.location='tempExcel/".$nop_.".xls.gz';
	</script>";	
} else {
	echo $tab;
?>
<div style='margin-top:10px'>Graph</div>
<iframe style='border:0;width:600px;height:300px'
	src='pmn_slave_2hargapasar_graph.php?periodePsr=<?php echo $periodePsr?>&barang=<?php echo $barang?>'></iframe>

<?php }?>