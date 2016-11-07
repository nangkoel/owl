<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/fpdf.php');
require_once('lib/zLib.php');
$notr=$_GET['notr'];

//$nmOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
//$indukOrg=makeOption($dbname,'organisasi','kodeorganisasi,induk');

    // ..select sdm_pengalamankerja
        $sSPK = "select * from ".$dbname.".sdm_pengalamankerja where notransaksi='".$notr."'";
        $qSPK = mysql_query($sSPK) or die(mysql_error());
        while ($rSPK = mysql_fetch_object($qSPK)) {
            $pt=$_SESSION['org']['namaorganisasi'];
            $tanggal = $rSPK->tanggal;

          // ..get detail perusahaan untuk kop surat
                $sKop = "select a.namaorganisasi,a.alamat,a.telepon,a.fax,b.alamatnpwp,c.kodeorganisasi
                        from ".$dbname.".organisasi a 
                        left join ".$dbname.".setup_org_npwp b on a.kodeorganisasi=b.kodeorg
                        left join ".$dbname.".datakaryawan c on a.kodeorganisasi=c.kodeorganisasi
                        where karyawanid='".$rSPK->karyawanid."'";
                $qKop = mysql_query($sKop) or die(mysql_error());
                while ($rKop = mysql_fetch_object($qKop)) {
                    $kdOrg = $rKop->kodeorganisasi;
                    $namaPT = $rKop->namaorganisasi;
                    $addPT = $rKop->alamat;
                    $tlpPT = $rKop->telepon;
                    $faxPT = $rKop->fax;
                    $addKbn = $rKop->alamatnpwp;
                }


            // ..get datakaryawan
                $namakaryawan='';
                $sdKarya = "select a.namakaryawan,a.jeniskelamin,a.tempatlahir,a.tanggallahir,a.nik,a.tanggalmasuk,a.tanggalkeluar,a.subbagian,b.namajabatan,c.alasan,d.namaorganisasi 
                            from ".$dbname.".datakaryawan a 
                            left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan 
                            left join ".$dbname.".sdm_exitinterview c on a.tanggalkeluar=c.tanggal 
                            left join ".$dbname.".organisasi d on a.lokasitugas=d.kodeorganisasi       
                            where a.karyawanid=".$rSPK->karyawanid;
                $qdKarya = mysql_query($sdKarya) or die(mysql_error());
                while ($rdKarya = mysql_fetch_object($qdKarya)) {
                    $namakaryawan   = $rdKarya->namakaryawan;
                    $jeniskelamin   = $rdKarya->jeniskelamin;

                    // ..penamaan kelamin
                    if ($jeniskelamin == 'L') {
                        $kelamin = "Laki-laki";
                    } else if ($jeniskelamin == 'P') {
                        $kelamin = "Perempuan";
                    }

                    // .. penamaan bulan
                    $aBln = array(  1=>"Januari",
                                    2=>"Februari",
                                    3=>"Maret",
                                    4=>"April",
                                    5=>"Mei",
                                    6=>"Juni",
                                    7=>"Juli",
                                    8=>"Agustus",
                                    9=>"September",
                                    10=>"Oktober",
                                    11=>"November",
                                    12=>"Desember");

                // .. detail tanggal lahir
                    $tempatlahir    = $rdKarya->tempatlahir;

                            $tgllahir       = $rdKarya->tanggallahir;
                        $blnlahir       = $aBln[intval(substr($tgllahir, 5,2))];
                    $tanggallahir   = substr($tgllahir, 8,2)." ".$blnlahir." ".substr($tgllahir, 0,4);

                // ..nik
                    $nik            = $rdKarya->nik; //nik == nip

                // ..detail tanggal masuk
                            $tglmasuk       = $rdKarya->tanggalmasuk;
                        $blnmasuk       = $aBln[intval(substr($tglmasuk, 5,2))];
                    $tanggalmasuk   = substr($tglmasuk, 8,2)." ".$blnmasuk." ".substr($tglmasuk, 0,4);

                // ..detail tanggal keluar
                            $tglkeluar      = $rdKarya->tanggalkeluar;
                        $blnkeluar      = $aBln[intval(substr($tglkeluar, 5,2))];
                    $tanggalkeluar  = substr($tglkeluar, 8,2)." ".$blnkeluar." ".substr($tglkeluar, 0,4);

                    $namajabatan    = $rdKarya->namajabatan;
                    $lokasitugas = $rdKarya->namaorganisasi;
                    $alasan = $rdKarya->alasan;
                }

                $sUnit = "select a.subbagian,b.namaorganisasi from ".$dbname.".datakaryawan a left join ".$dbname.".organisasi b on a.subbagian=b.kodeorganisasi where karyawanid=".$rSPK->karyawanid;
                $qUnit = mysql_query($sUnit) or die(mysql_error());
                while ($rUnit = mysql_fetch_object($qUnit)) {
                    $subbagian = $rUnit->namaorganisasi;
                }

                $sHRD = "select a.namakaryawan,a.pangkat,a.kodejabatan,b.penandatangan,c.namajabatan,d.nama from ".$dbname.".datakaryawan a 
                    left join ".$dbname.".sdm_pengalamankerja b on a.karyawanid=b.penandatangan 
                    left join ".$dbname.".sdm_5jabatan c on a.kodejabatan=c.kodejabatan
                    left join ".$dbname.".sdm_5departemen d on a.bagian=d.kode
                    where a.karyawanid=".$rSPK->penandatangan;
                $qHRD = mysql_query($sHRD) or die(mysql_error());
                while ($rHRD = mysql_fetch_object($qHRD)) {
                    $penandatangan = $rHRD->namakaryawan;
                    $penjabatan = $rHRD->pangkat;
                    $namadept = $rHRD->nama;
                }

                $addNunu = "Nunukan Office    : ".$addKbn.", Telp/Fax. : ".$tlpPT."/".$faxPT;
                $addJKT = "Jakarta Office       : ".$addPT.", Telp. : ".$tlpPT.", Fax. : ".$faxPT;
                $paragraf1 = "Pimpinan ".$namaPT.", dengan ini menerangkan bahwa yang namanya tersebut di bawah ini :";
                $paragraf2 = "Pernah bekerja di ".$namaPT.", dengan kondisi kerja terakhir sebagai berikut :";
        }
        // ..create header
    class PDF extends FPDF
    {
        
        function Header() {
            global $conn;
            global $dbname;
            //-----------
            /*$this->SetFillColor(255,255,255);
            $this->SetMargins(15,10,0);
            if ($_SESSION['org']['kodeorganisasi']=='HIP') {
                $path = 'images/hip_logo.jpg';
                //$namaPER = "P T.   H A R D A Y A  I N T I  P L A N T A T I O N";
                
            } else
            if ($_SESSION['org']['kodeorganisasi']=='SIL') {
                $path = 'images/sil_logo.jpg';
                //$namaPER = "P T.   S E B A K I S   I N T I   L E S T A R I";
            } else
            if ($_SESSION['org']['kodeorganisasi']=='SIP') {
                $path = 'images/sip_logo.jpg';
                //$namaPER = "P T.   S E B U K U   I N T I   P L A N T A T I O N";
            }
            $this->Image($path,12,5,40);
            $this->SetFont('Times','B',18);
            $this->SetFillColor(255,255,255);
            $this->SetX(70);
            $this->Cell(80,10,'',0,1,'C'); 
            $this->SetFont('Times','',9);
            //$this->SetY(27);
            $this->SetX(42);
            $this->Cell(0,0,'Nunukan Office    : Jl. Gajah Mada No. 61A, Nunukan - Kalimantan Timur, Telp/Fax. : 0556 - 23159',5,1,'L');
            $this->SetX(42);
            $this->Cell(0,7,'',5,1,'L');
            $this->SetX(42);
            $this->Cell(0,0,'Plantation              : Desa Pembelingan, Kec. Sebuku, Kab, Nunukan, Kalimantan Timur',5,1,'L');
            */
        }

        function Footer() {
            
        }
    }

        $pdf=new PDF('P','mm','A4');
        $pdf->AddPage();
        // .. BEGIN KOP SURAT
        $pdf->SetMargins(15,10,0);
        if ($kdOrg=='SIL') {
            $fontsizePT=21.5;
            $path = 'images/sil_logo.jpg';
            $namaPTPanjang="P T.   S E B A K I S   I N T I   L E S T A R I";
        } else if ($kdOrg=='SIP') {
            $fontsizePT=21.5;
            $path = 'images/sip_logo.jpg';
            $namaPTPanjang="P T.   S E B U K U   I N T I   P L A N T A T I O N";
        } else if ($kdOrg=='HIP') {
            $fontsizePT=19;
            $path = 'images/hip_logo.jpg';
            $namaPTPanjang="P T.  H A R D A Y A  I N T I  P L A N T A T I O N";
        }
        $pdf->Image($path,12,5,45);
        $pdf->SetFont('Times','B',$fontsizePT);
        $pdf->SetFillColor(255,255,255);
        $pdf->SetX(42);
        $pdf->Cell(80,10,$namaPTPanjang,0,1,'L'); 
        $pdf->SetFont('Times','',9);
        //$this->SetY(27);
        $pdf->SetX(42);
        if ($kdOrg=='HIP'){
            $pdf->Cell(0,0,"Head Office       : ".$addPT.", Telp. : ".$tlpPT.", Fax. : ".$faxPT,5,1,'L');
            $pdf->SetX(42);
            $pdf->Cell(0,7,"Branch Office    : Jl. Dewi Sartika II, Lorong Jembolan No.32, Palu - Sulawesi Tengah  Telp & Fax.: 0451-487659",5,1,'L');
            $pdf->SetX(42);
            $pdf->Cell(0,0,"Plantation        : Desa Winangun, Kab Buol - Sulawesi Tengah. Website : www.hardaya.co.id",5,1,'L');
            $pdf->SetY(40);
        } else if ($kdOrg=='SIL'){
            $pdf->Cell(0,0,$addNunu,5,1,'L');
            $pdf->SetX(42);
            $pdf->Cell(0,7,$addJKT,5,1,'L');
            $pdf->SetY(30);
        } else {
            $pdf->Cell(0,0,$addNunu,5,1,'L');
            $pdf->SetX(42);
            $pdf->Cell(0,7,$addJKT,5,1,'L');
            $pdf->SetY(30);
        }
//        $pdf->SetX(42);
//        $pdf->Cell(0,0,$addNunu,5,1,'L');
//        $pdf->SetX(42);
//        $pdf->Cell(0,7,$addJKT,5,1,'L');
        // ..END OF KOP SURAT
        
        $pdf->SetFillColor(255,255,255);
        $pdf->SetX(20);
        $pdf->Ln(); 
        $pdf->SetFont('Arial','BU',12);
        $pdf->Cell(30,5,'',0,0,'L');
        $pdf->Cell(40,5,'',0,0,'C');
        $pdf->Cell(40,5,'SURAT KETERANGAN KERJA',0,1,'C');
        $pdf->SetX(20);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(30,5,'',0,0,'L');
        $pdf->Cell(40,5,'',0,0,'C');
        $pdf->Cell(30,5,$notr,0,0,'C');
        $pdf->SetX(20);
        $pdf->SetFont('Arial','',10);
        $pdf->Ln();
        $pdf->Ln();
            $pdf->MultiCell(175,5,$paragraf1);
        $pdf->Ln();
        $pdf->Cell(30,5,'',0,0,'L');
        $pdf->Cell(30,5,'Nama                                        :     '.$namakaryawan,0,0,'L');
        $pdf->SetX(20);
        $pdf->Ln();
        $pdf->Cell(30,5,'',0,0,'L');
        $pdf->Cell(30,5,'Jenis Kelamin                           :     '.$kelamin,0,0,'L');
        $pdf->SetX(20);
        $pdf->Ln();
        $pdf->Cell(30,5,'',0,0,'L');
        $pdf->Cell(30,5,'Tempat/Tanggal Lahir              :     '.$tempatlahir.', '.$tanggallahir,0,0,'L');
        $pdf->SetX(20);
        $pdf->SetFont('Arial','B',10);
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Cell(10,5,'',0,0,'L');
        $pdf->Cell(15,5,'',0,0,'C');
        $pdf->Cell(10,15,'--------------------------------------------- B  E  N  A  R ---------------------------------------------',0,0,'L');
        $pdf->SetX(20);
        $pdf->SetFont('Arial','',10);
        $pdf->Ln();
        $pdf->Cell(20,5,$paragraf2,0,0,'L');
        $pdf->SetX(20);
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Cell(30,5,'',0,0,'L');
        $pdf->Cell(30,5,'NIP                                            :     '.$nik,0,0,'L');
        $pdf->SetX(20);
        $pdf->Ln();
        $pdf->Cell(30,5,'',0,0,'L');
        $pdf->Cell(30,5,'Dept./Divisi                                :     '.$lokasitugas,0,0,'L');
        $pdf->SetX(20);
        $pdf->Ln();
        $pdf->Cell(30,5,'',0,0,'L');
        $pdf->Cell(30,5,'Unit Kerja                                  :     '.$subbagian,0,0,'L');
        $pdf->SetX(20);
        $pdf->Ln();
        $pdf->Cell(30,5,'',0,0,'L');
        $pdf->Cell(30,5,'Jabatan                                     :     '.$namajabatan,0,0,'L');
        $pdf->SetX(20);
        $pdf->Ln();
        $pdf->Cell(30,5,'',0,0,'L');
        $pdf->Cell(30,5,'Tanggal Masuk Kerja                :     '.$tanggalmasuk,0,0,'L');
        $pdf->SetX(20);
        $pdf->Ln();
        $pdf->Cell(30,5,'',0,0,'L');
        $pdf->Cell(30,5,'Tanggal Berhenti Kerja             :     '.$tanggalkeluar,0,0,'L');
        $pdf->SetX(20);
        $pdf->Ln();
        $pdf->Cell(30,5,'',0,0,'L');
        $pdf->Cell(30,5,'Alasan Berhenti                        :     '.$alasan,0,0,'L');
        $pdf->SetX(20);
        $pdf->SetFont('Arial','',10);
        $pdf->Ln();
        $pdf->Ln();
        $pdf->MultiCell(175,5,'Pimpinan Perusahaan mengucapkan terima kasih atas dedikasi saudara selama bekerja / bergabung di '
                .$namaPT.', semoga segala pengalaman yang saudara dapatkan bermanfaat untuk Karir saudara di masa mendatang.');
        $pdf->Ln();
        $pdf->MultiCell(175,5,'Dengan diterbitkannya surat keterangan ini maka hubungan kerja serta segala hak dan '
                . 'kewajiban antara saudara dan perusahaan telah diselesaikan secara menyeluruh.');
        $pdf->Ln();
        $pdf->Cell(20,5,'Demikian Surat Keterangan ini diterbitkan untuk dapat dipergunakan sebagaimana mestinya.',0,0,'L');
        $pdf->SetX(20);
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Cell(40,5,'',0,0,'L');
        $pdf->Cell(70,5,'',0,0,'L');
        $pdf->Cell(20,5,'Dikeluarkan di : Nunukan,',0,0,'L');
        $pdf->SetX(20);
        $pdf->Ln();
        $pdf->Cell(40,5,'',0,0,'L');
        $pdf->Cell(70,5,'',0,0,'L');
        $pdf->Cell(20,5,'Pada Tanggal  : '.tanggalnormal($tanggal),0,0,'L');
        $pdf->SetX(20);
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Cell(40,5,'',0,0,'L');
        $pdf->Cell(70,5,'',0,0,'L');
        $pdf->SetX(20);
        $pdf->SetFont('Arial','',10);
        $pdf->Ln();
        $pdf->Cell(40,5,'',0,0,'L');
        $pdf->Cell(70,5,'',0,0,'L');
        $pdf->Cell(40,5,$penandatangan,'B',0,'L');
        $pdf->SetX(20);
        $pdf->SetFont('Arial','',10);
        $pdf->Ln();
        $pdf->Cell(40,5,'',0,0,'L');
        $pdf->Cell(70,5,'',0,0,'L');
        $pdf->Cell(20,5,$penjabatan.' '.$namadept,0,0,'L');



        $pdf->Output();

    



?>