<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/zPosting.php');

$param = $_POST;
$proses = $_GET['proses'];

switch($proses) {
    case 'masukBarang':
        /** Posting Penerimaan Barang **/
        #=== Extract Data ====
        # Main Data
        $query = selectQuery($dbname,'log_transaksi_vw','*',
            "notransaksi='".$param['notransaksi']."' and statussaldo=1");
        $data = fetchData($query);
        
        # Get Akun
        $whereBarang = "kode in (";
        foreach($data as $key=>$row) {
            
        }
        $whereBarang .= ")";
        
        
        #==================== Journal Counter ==================
        $kodejurnal = 'INVM1';
        $queryC = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
            "kodeorg='".$_SESSION['org']['kodeorganisasi'].
            "' and kodekelompok='".$kodejurnal."'");
        $tmpC = fetchData($queryC);
        if(empty($tmpC)) {
            echo "Warning : Kode Jurnal belum disetting untuk PT anda";
            exit;
        }
        $konter = addZero($tmpC[0]['nokounter']+1,3);
        $tanggalJ = tanggalsystem(tanggalnormal($data[0]['tanggal']));
        $nojurnal = $tanggalJ."/".substr($data[0]['notransaksi'],14,4).
            "/".$kodejurnal."/".$konter;
        #==================== Journal Counter ==================
        
        # Prep Header
        $dataRes['header'] = array(
            'nojurnal'=>$nojurnal,
            'kodejurnal'=>$kodejurnal,
            'tanggal'=>$data[0]['tanggal'],
            'tanggalentry'=>date('Ymd'),
            'posting'=>'0',
            'totaldebet'=>'0',
            'totalkredit'=>'0',
            'amountkoreksi'=>'0',
            'noreferensi'=>$data[0]['notransaksi'],
            'autojurnal'=>'1',
            'matauang'=>'IDR',
            'kurs'=>'1'
        );
        print_r($dataRes['header']);
        exit;
        #2. Data Detail
        # Detail (Many)
        $noUrut = 1;
        $totalJumlah = 0;
        foreach($data as $row) {
            if(substr($row['kode'],1,1)=='M') {
                $jumlah = $row['jumlah']*(-1);
            } else {
                $jumlah = $row['jumlah'];
            }
            $dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$dataH[0]['tanggal'],
                'nourut'=>$noUrut,
                'noakun'=>$row['noakun'],
                'keterangan'=>$row['keterangan2'],
                'jumlah'=>$jumlah,
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$row['kodeorg'],
                'kodekegiatan'=>$row['kodekegiatan'],
                'kodeasset'=>$row['kodeasset'],
                'kodebarang'=>$row['kodebarang'],
                'nik'=>$row['nik'],
                'kodecustomer'=>$row['kodecustomer'],
                'kodesupplier'=>$row['kodesupplier'],
                'noreferensi'=>'',
                'noaruskas'=>$row['noaruskas'],
                'kodevhc'=>$row['kodevhc'],
                'nodok'=>$row['nodok']
            );
            $totalJumlah += $jumlah;
            $noUrut++;
        }
        
        # Detail (One)
        $dataRes['detail'][] = array(
            'nojurnal'=>$nojurnal,
            'tanggal'=>$dataH[0]['tanggal'],
            'nourut'=>$noUrut,
            'noakun'=>$dataH[0]['noakun'],
            'keterangan'=>$dataH[0]['keterangan'],
            'jumlah'=>$totalJumlah*(-1),
            'matauang'=>'IDR',
            'kurs'=>'1',
            'kodeorg'=>$dataH[0]['kodeorg'],
            'kodekegiatan'=>'',
            'kodeasset'=>'',
            'kodebarang'=>'',
            'nik'=>'',
            'kodecustomer'=>'',
            'kodesupplier'=>'',
            'noreferensi'=>$dataH[0]['notransaksi'],
            'noaruskas'=>'',
            'kodevhc'=>'',
            'nodok'=>''
        );
        print_r($dataRes);
        break;
    case 'keluarBarang':
        /** Posting Pengeluaran Barang **/
        #=== Extract Data ====
        # Main Data
        $query = selectQuery($dbname,'log_transaksi_vw','*',
            "notransaksi='".$param['notransaksi']."' and statussaldo=1");
        $data = fetchData($query);
        
        #==================== Journal Counter ==================
        $kodejurnal = 'INVK1';
        $queryC = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
            "kodeorg='".$_SESSION['org']['kodeorganisasi'].
            "' and kodekelompok='".$kodejurnal."'");
        $tmpC = fetchData($queryC);
        $konter = addZero($tmpC[0]['nokounter']+1,3);
        $tanggalJ = tanggalsystem(tanggalnormal($data[0]['tanggal']));
        $nojurnal = $tanggalJ."/".substr($data[0]['notransaksi'],14,4).
            "/".$kodejurnal."/".$konter;
        echo $nojurnal;
        exit;
        #==================== Journal Counter ==================
        
        # Prep Header
        $dataRes['header'] = array(
            'nojurnal'=>$nojurnal,
            'kodejurnal'=>$data[0]['kode'],
            'tanggal'=>$data[0]['tanggal'],
            'tanggalentry'=>date('Ymd'),
            'posting'=>'0',
            'totaldebet'=>'0',
            'totalkredit'=>'0',
            'amountkoreksi'=>'0',
            'noreferensi'=>$dataH[0]['notransaksi'],
            'autojurnal'=>'1',
            'matauang'=>'IDR',
            'kurs'=>'1'
        );
        
        #2. Data Detail
        # Detail (Many)
        $noUrut = 1;
        $totalJumlah = 0;
        foreach($dataD as $row) {
            if(substr($row['kode'],1,1)=='M') {
                $jumlah = $row['jumlah']*(-1);
            } else {
                $jumlah = $row['jumlah'];
            }
            $dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$dataH[0]['tanggal'],
                'nourut'=>$noUrut,
                'noakun'=>$row['noakun'],
                'keterangan'=>$row['keterangan2'],
                'jumlah'=>$jumlah,
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$row['kodeorg'],
                'kodekegiatan'=>$row['kodekegiatan'],
                'kodeasset'=>$row['kodeasset'],
                'kodebarang'=>$row['kodebarang'],
                'nik'=>$row['nik'],
                'kodecustomer'=>$row['kodecustomer'],
                'kodesupplier'=>$row['kodesupplier'],
                'noreferensi'=>'',
                'noaruskas'=>$row['noaruskas'],
                'kodevhc'=>$row['kodevhc'],
                'nodok'=>$row['nodok']
            );
            $totalJumlah += $jumlah;
            $noUrut++;
        }
        
        # Detail (One)
        $dataRes['detail'][] = array(
            'nojurnal'=>$nojurnal,
            'tanggal'=>$dataH[0]['tanggal'],
            'nourut'=>$noUrut,
            'noakun'=>$dataH[0]['noakun'],
            'keterangan'=>$dataH[0]['keterangan'],
            'jumlah'=>$totalJumlah*(-1),
            'matauang'=>'IDR',
            'kurs'=>'1',
            'kodeorg'=>$dataH[0]['kodeorg'],
            'kodekegiatan'=>'',
            'kodeasset'=>'',
            'kodebarang'=>'',
            'nik'=>'',
            'kodecustomer'=>'',
            'kodesupplier'=>'',
            'noreferensi'=>$dataH[0]['notransaksi'],
            'noaruskas'=>'',
            'kodevhc'=>'',
            'nodok'=>''
        );
        print_r($data);
        break;
    default:
}
?>