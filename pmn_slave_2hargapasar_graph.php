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
$periodePsr= isset($_GET['periodePsr'])? $_GET['periodePsr']: '';
$barang= isset($_GET['barang'])? $_GET['barang']: '';
$tglHarga=isset($_GET['tglHarga'])? tanggalsystem($_GET['tglHarga']): '';
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

$arrTgl = array();
for($i=1;$i<=$dayInMonth;$i++) {
	$arrTgl[] = $i;
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
		$data1[$id][$i-1] = $pasarHarga[$id];
	}
}



// Graph
$ydata = $data1;
$test = array(1,2,3,4,5);
$width=600;
$height=300;

$graph = new Graph($width,$height);
$graph->SetScale('intlin');
$graph->img->SetMargin(60,30,40,40);
$graph->xaxis->SetTickLabels($arrTgl);

foreach($ydata as $pasar=>$data) {
	$lineplot=new LinePlot($ydata[$pasar]);
	$lineplot->SetLegend($optPasar[$pasar]);
	$graph->Add($lineplot);
}

// Display the graph
$graph->Stroke();
?>