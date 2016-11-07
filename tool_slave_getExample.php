<?php
require_once('master_validation.php');
$param=$_GET['form'];
if($param=='ACCBAL'){ 
        header("Cache-Control: must-revalidate");
        header("Pragma: must-revalidate");
        header("Content-type: application/vnd.ms-excel");
        header("Content-disposition: attachment; filename=expampleaccbal.csv");
        echo "kodeorg,periode,noakun,saldo\n";
        echo "SOGE,201304,1110001,190000\n";
        echo "SOGE,201304,2110004,40000000\n";
        echo "SOGE,201304,1150001,2550500\n";
        echo "SOGE,201304,3110002,3000000\n";
        echo "SOGE,201304,1260001,10500\n";
        exit();
}
if($param=='JOURNAL'){ 
        header("Cache-Control: must-revalidate");
        header("Pragma: must-revalidate");
        header("Content-type: application/vnd.ms-excel");
        header("Content-disposition: attachment; filename=expamplejournalhistory.csv");
        echo "nojurnal,tanggal,nourut,noakun,keterangan,jumlah,matauang,kurs,kodeorg,kodekegiatan,kodeasset,kodebarang,nik,kodecustomer,kodesupplier,noreferensi,kodevhc,kodeblok\n";
        echo "20130631/SOGE/HIS/001,2013-06-31,1,0,Histori hutang spl,1000000,IDR,1,SOGE,,,,,,,,,\n";
        echo "20130631/SOGE/HIS/001,2013-06-31,2,2111101,Histori hutang spl,-300000,IDR,1,SOGE,,,,,,S001000001,,,\n";
        echo "20130631/SOGE/HIS/001,2013-06-31,3,2111101,Histori hutang spl,-200000,IDR,1,SOGE,,,,,,S001000079,,,\n";
        echo "20130631/SOGE/HIS/001,2013-06-31,4,2111101,Histori hutang spl,-250000,IDR,1,SOGE,,,,,,S001000602,,,\n";
        echo "20130631/SOGE/HIS/001,2013-06-31,5,2111101,Histori hutang spl,-250000,IDR,1,SOGE,,,,,,S001000101,,,\n";
        exit();        
}
if($param=='INV'){ 
        header("Cache-Control: must-revalidate");
        header("Pragma: must-revalidate");
        header("Content-type: application/vnd.ms-excel");
        header("Content-disposition: attachment; filename=saldomaterial.csv");
            echo "kodeorg,kodebarang,saldoakhirqty,hargarata,periode,kodegudang\n";
            echo "NFS,31200026,1,275000,2013-07,LGRM22\n";
            echo "NFS,32100001,6,1856500.667,2013-07,LGRM22\n";
            echo "NFS,32100003,7.5,170375.0667,2013-07,LGRM22\n";
            echo "NFS,32100005,2,37000,2013-07,LGRM22\n";
            echo "NFS,32100008,4,32500,2013-07,LGRM22\n";
            echo "NFS,32100009,5,53000,2013-07,LGRM22\n";
            echo "NFS,32100013,1,132500,2013-07,LGRM22\n";
            echo "NFS,32100014,3,65556,2013-07,LGRM22\n";
            echo "NFS,32100018,6,20500,2013-07,LGRM22\n";
            exit();        
}       
if($param=='PO'){ 
        header("Cache-Control: must-revalidate");
        header("Pragma: must-revalidate");
        header("Content-type: application/vnd.ms-excel");
        header("Content-disposition: attachment; filename=listpomanual.csv");
            echo "nopo,kodeorg,tanggal,kodesupplier,matauang,kurs,diskonpersen,nilaidiskon,ppn,subtotal,nilaipo,kodebarang,satuan,jumlahpesan,hargasatuan\n";
            echo "612/08/2013/PO/MA/NFS,NFS,2013-08-02,S001110341,IDR,1,0,0,50650,506500,557150,32102901,ROLL,1,270000\n";
            echo "612/08/2013/PO/MA/NFS,,,,,,,,,,,32102902,PCS,2,20000\n";
            echo "612/08/2013/PO/MA/NFS,,,,,,,,,,,32103182,PCS,2,5500\n";
            echo "612/08/2013/PO/MA/NFS,,,,,,,,,,,32201055,PCS,7,26500\n";
            echo "987/12/2012/PO/MA/NFS,NFS,2012-12-17,S001110070,IDR,1,0,0,0,25720000,25720000,37701061,BUKU,120,29000\n";
            echo "987/12/2012/PO/MA/NFS,,,,,,,,,,,37701269,BUKU,120,10000\n";
            echo "987/12/2012/PO/MA/NFS,,,,,,,,,,,37701270,BUKU,120,10000\n";
            echo "987/12/2012/PO/MA/NFS,,,,,,,,,,,37701271,LEMBAR,500,2600\n";
            echo "987/12/2012/PO/MA/NFS,,,,,,,,,,,37701272,BUKU,1200,5000\n";
            echo "987/12/2012/PO/MA/NFS,,,,,,,,,,,37701273,BUKU,240,11000\n";
            echo "987/12/2012/PO/MA/NFS,,,,,,,,,,,37701274,BUKU,120,11000\n";
            echo "987/12/2012/PO/MA/NFS,,,,,,,,,,,37701275,BUKU,120,14000\n";
            echo "987/12/2012/PO/MA/NFS,,,,,,,,,,,37701276,BUKU,300,23000\n";
            exit();        
}  
if($param=='ABSENSI'){ 
        header("Cache-Control: must-revalidate");
        header("Pragma: must-revalidate");
        header("Content-type: application/vnd.ms-excel");
        header("Content-disposition: attachment; filename=absensi.csv");
            echo "tanggal,nik,shift,absensi,jam,jamPlg,keterangan\n";
            echo "2013-08-01,xxxx,1,H,07:00:00,15:00:00,datang terlambat\n";
            echo ",xxxx,1,H,07:00:00,15:00:00,datang terlambat\n";
            echo ",xxxx,1,H,07:00:00,15:00:00,datang terlambat\n";
            echo ",xxxx,1,H,07:00:00,15:00:00,datang terlambat\n";
            echo ",xxxx,1,H,07:00:00,15:00:00,datang terlambat\n";
            echo ",xxxx,1,H,07:00:00,15:00:00,datang terlambat\n";
            exit();        
} 
?>