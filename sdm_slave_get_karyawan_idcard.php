<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/fpdf.php');

// ..get karyawanid
    $karyawanid = $_GET['karyawanid'];

// ..get data organisasi karyawan
    $str = "select b.kodeorganisasi,a.namaorganisasi,c.alamatdomisili,b.lokasitugas 
            from ".$dbname.".datakaryawan b 
            left join ".$dbname.".organisasi a on b.kodeorganisasi=a.kodeorganisasi 
            left join ".$dbname.".setup_org_npwp c on b.kodeorganisasi=c.kodeorg 
            where b.karyawanid=".$karyawanid;
    $res = mysql_query($str) or die(mysql_error());
    while ($bar = mysql_fetch_object($res)) {
        $kodept=$bar->kodeorganisasi;
        $namapt=$bar->namaorganisasi;
        $alamatpt=$bar->alamatdomisili;
        $lokasitugas=$bar->lokasitugas;
    }

// ..create header
    class PDF extends FPDF {
        var $col = 0;
        // ..set default coloum
            function SetCol($col) {
                // ..move position to a coloum <- default command(df)
                    $this->col=$col;
                    $x = 10+$col*100;
                    $this->SetLeftMargin($x);
                    $this->SetX($x);
            }

        // ..set header
            function Header() {
                global $kodept;
                global $namapt;
                global $alamatpt;
                global $lokasitugas;
                    if (substr($lokasitugas,-2)=='HO') 
                        $alamatpt="Jakarta";
            }

        // ..set footer

    }

// ..get datakaryawan
    $str = "select * from ".$dbname.".datakaryawan 
            where karyawanid=".$karyawanid ." 
            limit 1";
    $res =  mysql_query($str) or die(mysql_error());
    $defaulsrc = 'images/user.jpg';
    while ($bar = mysql_fetch_object($res)) {
        $photo=($bar->photo==''?$defaulsrc:$bar->photo);
        $karyawanid=$bar->karyawanid;
        $nik=$bar->nik;
        $nama=$bar->namakaryawan;
        $tmasuk=date("d F Y",strtotime($bar->tanggalmasuk));

    // ..create and customize PDF
        $pdf = new PDF('P','mm','A4');
            // ..mensejajarkan id card depan dan belakang
                // ..halaman 1
                    $pdf->SetLeftMargin(74);
                    $pdf->AddPage();
                    $awalX=$pdf->GetX();
                    $awalY=$pdf->GetY();

                    $pdf->Rect($awalX+1,$awalY-5, 60, 85);
                        // ..logo perusahaan
                            if($kodept=='HIP'){  
                                $path='images/hip_logo.jpg'; 
                            } else if($kodept=='SIL'){  
                                $path='images/sil_logo.jpg'; 
                            } else if($kodept=='SIP'){  
                                $path='images/sip_logo.jpg'; 
                            }
                            $pdf->Image($path,76,6,37);
                        
                        // ..header detail pt
                            $pdf->SetFont('Arial','B',7);
                            $pdf->Cell(15,5,'',0,0,'C');
                            $pdf->Cell(47,5,$namapt,0,1,'C');
                            $pdf->Cell(15,5,'',0,0,'C');
                            $pdf->Cell(50,5,strtoupper($alamatpt),0,1,'C');
                            $pdf->Line(134,28,74,28);
                            $pdf->SetFont('Arial','B',12);
                            $pdf->setY(30);
                            $pdf->setX(12);
                            $pdf->SetLeftMargin(75);
                            // ..nama
                            $pdf->Cell(13,6,$_SESSION['lang']['nama'],0,0,'LT');
                            $pdf->Cell(3,6,":",0,0,'L');
                            if (strlen($nama)>15){
                                $pdf->MultiCell(45,4,$nama,0,'L');
                            } else {
                                $pdf->Cell(45,6,$nama,0,1,'L');
                            }

                            //$pdf->setX(12);
                            //$pdf->SetLeftMargin(75);
                            $pdf->Cell(13,6,$_SESSION['lang']['nik'],0,0,'L');
                            $pdf->Cell(3,6,":",0,0,'L');
                            $pdf->Cell(50,6,$nik,0,1,'L');
                            //$pdf->setX(12);
                            //$pdf->SetLeftMargin(75);
                            $pdf->Cell(13,6,$_SESSION['lang']['tmk'],0,0,'L');
                            $pdf->Cell(3,6,":",0,0,'L');
                            $pdf->Cell(50,6,$tmasuk,0,1,'L');
                            $pdf->SetFont('Arial','',8);
                            //$pdf->setY(100);
                            $pdf->Image($photo,92,52,25);

                // ..halaman 2
                    //$pdf->SetLeftMargin(75);
                    $pdf->AddPage();
                    $awalX=$pdf->GetX();
                    $awalY=$pdf->GetY();
                    
//                    $pdf->Rect($awalX+1,$awalY-5, 60, 85);

                    $pdf->setY(10);
                    $pdf->SetFont('Arial','BU',12);
                    $pdf->Cell(60,5,'KETENTUAN',0,1,'C');
                    $pdf->setY(22);
                    $pdf->SetFont('Arial','',7);
                    $pdf->Cell(5,3,'1.',0,0,'R');
                    $pdf->MultiCell(55,3,'Kartu ini adalah milik '.$namapt.' yang harus dikembalikan apabila ditemukan atau diminta oleh pihak perusahaan.',0,'L');
                    $pdf->setY(33);
                    $pdf->Cell(5,3,'2.',0,0,'R');
                    $pdf->MultiCell(55,3,'Dilarang memakai ID Card orang lain dan atau penyalahgunaan diluar ketentuan yang berlaku.',0,'L');
                    $pdf->setY(41);
                    $pdf->Cell(5,3,'3.',0,0,'R');
                    $pdf->MultiCell(55,3,'Tanda pengenal harus selalu dikenakan selama jam kerja atau dinas.',0,'L');
                    $pdf->setY(49);
                    $pdf->Cell(5,3,'4.',0,0,'R');
                    $pdf->MultiCell(55,3,'Kartu yang hilang atau rusak harus segera dilaporkan ke personalia. Penggantian karena kelalaian pekerja dikenakan biaya Rp 25.000 / Kartu.',0,'L');

//HRD Manager
if ($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
    $str="select namakaryawan,karyawanid,pangkat from ".$dbname.".datakaryawan
      where tipekaryawan=0 and bagian='HRD' 
      and kodejabatan in (21,33) 
      and tanggalkeluar='0000-00-00' 
      and karyawanid <>".$_SESSION['standard']['userid']. " 
      and lokasitugas like '%HO' 
      limit 1";
} else {
    $str=   "select namakaryawan,karyawanid,pangkat from ".$dbname.".datakaryawan
            where tipekaryawan=0 and bagian='HRD' 
            and kodejabatan in (21,33) 
            and tanggalkeluar='0000-00-00' 
            and karyawanid <>".$_SESSION['standard']['userid']. " 
            and lokasitugas in 
                            (select kodeunit from ".$dbname.".bgt_regional_assignment 
                            where regional='".$_SESSION['empl']['regional']."') 
            and lokasitugas not like '%HO' 
            limit 1";
}
$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
        $pdf->setY(64);
        $pdf->setX(85);
        $pdf->Cell(70,3,'Disahkan,',0,1,'C');
        $pdf->setY(80);
        $pdf->setX(85);
        $pdf->SetFont('Arial','BU',7);
        $pdf->Cell(70,3,$bar->namakaryawan,0,1,'C');
        $pdf->setX(85);
        $pdf->SetFont('Arial','',7);
        $pdf->Cell(70,3,$bar->pangkat,0,1,'C');
}
        $pdf->output();
}

?>