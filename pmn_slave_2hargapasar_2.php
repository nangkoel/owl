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
$periodePsr= isset($_POST['periodePsr2'])? $_POST['periodePsr2']: $_GET['periodePsr2'];
$barang= isset($_POST['komodoti'])? $_POST['komodoti']: $_GET['komodoti'];
$tglHarga=isset($_POST['tglHarga'])? tanggalsystem($_POST['tglHarga']): '';
$where = '';
$whr="kelompokbarang='400'";
$optNmBarang=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang',$whr);

if($periodePsr=='') {
    exit("Error:Periode Tidak Boleh Kosong");
}
if($barang=='') {
    exit("Error:Komoditi harus dipilih");
}

// Check Days
$dayInMonth = count(rangeTanggal(tanggalsystem($periodePsr), date('Ymd')));
$tmpTgl = explode('-',$periodePsr);
$tglPeriode = $tmpTgl[2].'-'.$tmpTgl[1].'-'.$tmpTgl[0];
$datetime1 = date_create($tglPeriode);
$datetime2 = date_create(date('Y-m-d'));
$interval = date_diff($datetime1, $datetime2);
$dayInMonth = $interval->format('%a');
if($dayInMonth>91) {
    $date2 = date('Y-m-d',mktime(0,0,0,$tmpTgl[1],$tmpTgl[0]+90,$tmpTgl[2]));
    $dayInMonth = 91;
} else {
    $date2 = date('Y-m-d');
}

$where.=" and tanggal >= '".tanggalsystem($periodePsr)."'";
$where.=" and tanggal <= '".$date2."'";
$where.=" and kodeproduk = '".$barang."'";
$str = "select * from ".$dbname.".pmn_hargapasar where tanggal!='' ".$where." order by `tanggal` asc";
$resHarga = fetchData($str);

$optPasar = makeOption($dbname,'pmn_5pasar','id,namapasar');

// Rearrange Data
foreach($resHarga as $row) {
	$datetime2 = date_create($row['tanggal']);
    $interval = date_diff($datetime1, $datetime2);
    $day = $interval->format('%a');
	$dataHarga[$day+1][$row['pasar']] = $row['harga'];
}

foreach($optPasar as $id=>$nama) {
	$pasarHarga[$id] = 0;
}

for($i=1;$i<=$dayInMonth;$i++) {
	foreach($optPasar as $id=>$nama) {
		if(isset($dataHarga[$i][$id])) {
			$pasarHarga[$id] = $dataHarga[$i][$id];
		}
		$tmpData1[$id][$i] = $pasarHarga[$id];
	}
}

$data1 = array();
foreach($tmpData1 as $pasar=>$row1) {
	$i=1;
	$minggu=1;
	$harga = 0;
	foreach($row1 as $row2) {
		if($i>7) {
			$i=1;
			$data1[$minggu][$pasar] = $harga/7;
			$harga = 0;
			$minggu++;
		}
		$harga+=$row2;
		$i++;
	}
    if($i<=8) {
        $data1[$minggu][$pasar] = $harga/($i-1);
    }
}

$arr="##periodePsr##barang";
if($proses=='preview') {
	echo makeElement('excel1','btn','Export to Excel',array('onclick'=>"zExcel(event,'pmn_slave_2hargapasar_2.php','".$arr."')"));
}

ob_start();
?>
<table class=sortable cellspacing=1 border=0>
	<thead>
		<tr class=rowheader>
			<td><?php echo $_SESSION['lang']['tanggal']?></td>
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
	src='pmn_slave_2hargapasar_2_graph.php?periodePsr2=<?php echo $periodePsr?>&komodoti=<?php echo $barang?>'></iframe>

<?php }?>