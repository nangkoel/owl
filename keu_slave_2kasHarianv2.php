<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/biReport.php');
include_once('lib/zPdfMaster.php');
include_once('lib/terbilang.php');

$otNmAkun=makeOption($dbname, 'keu_5akun', 'noakun,namaakun');

$param=$_POST;
$level = $_GET['level'];
if(isset($_GET['mode'])) {
    $mode = $_GET['mode'];
} else {
    $mode = 'preview';
}
if($mode=='pdf') {
    $param = $_GET;
    unset($param['mode']);
    unset($param['level']);
} else {
    $param = $_POST;
}
# Validasi Periode
$periode1 = $param['periode_from'];
$periode2 = $param['periode_until'];
#1. Empty
if($param['proses']!='getNoakun'){
    if($periode1=='' or $periode2=='') {
        echo 'Warning : Transaction period required';
        exit;
    }
}
#2. Range Terbalik
if(tanggalsystem($periode1)>tanggalsystem($periode2)) {
    $tmp = $periode1;
    $periode1 = $periode2;
    $periode2 = $tmp;
}
#3. akun terbalik--tambahan jamhari
if($param['noakun']>$param['noakunsmp']){
    $tmp=$param['noakunsmp'];
    $param['noakunsmp']=$param['noakun'];
    $param['noakun']=$tmp;
}
if($param['noakunsmp']==''){
    $param['noakunsmp']=$param['noakun'];
}

switch($level){
    case'0':
        if(isset($param['kodeorg']))
            $kodeorg=$param['kodeorg'];
        else
            $kodeorg=$_SESSION['empl']['lokasitugas'];
        #ambil saldo akhir bl lalu
        //ambil data periode sebelumnya per cut off date
        $qper="select * from ".$dbname.".setup_periodeakuntansi where tanggalmulai<='".tanggalsystem($periode1)."' and tanggalsampai>='".tanggalsystem($periode1)."' and kodeorg='".$kodeorg."' limit 1";
        $per=fetchData($qper);
        $prd=explode("-",$per[0]['periode']);
        if($prd[1]-1==0){
            $prdlalu=($prd[0]-1)."-12";
        }else{
            $prdlalu=$prd[0]."-".addZero($prd[1]-1,2);
        }
        $qsbl="select * from ".$dbname.".setup_periodeakuntansi where periode='".$prdlalu."' and kodeorg='".$kodeorg."'";
        $persbl=fetchData($qsbl);

        $blnawl=substr($per[0]['periode'],5,2);
	$queryx="select sum(awal".$blnawl.") as jumlah,noakun from ".$dbname.".keu_saldobulanankas where noakun>='".$param['noakun'].
            "' and noakun<='".$param['noakunsmp']."' and ".
            "kodeorg='".$kodeorg."' and periode='".str_replace("-","",$per[0]['periode'])."' group by noakun order by noakun asc";
        //echo $queryx;
        $res=mysql_query($queryx);
        while($bar=mysql_fetch_object($res))
        {
            $saldoAwal[$bar->noakun]+=$bar->jumlah;
            $grndTotalSawal+=$bar->jumlah;
        }

	$cols2 = "sum(jumlah) as jumlah,tipetransaksi,noakun";
        $where2 = "tanggalposting<'".tanggalsystem($periode1)."' and tanggalposting>'".$persbl[0]['tanggalsampai']."'
            and noakun>='".$param['noakun'].
            "' and noakun<='".$param['noakunsmp']."' and ".
            "kodeorg='".$kodeorg."' and posting=1 group by tipetransaksi,noakun";
        $query3 = selectQuery($dbname,'keu_kasbankht',$cols2,$where2,"tanggal,notransaksi");
        //echo $query3;
        $res=mysql_query($query3);
        while($bar=mysql_fetch_object($res)){
            if($bar->tipetransaksi=='M'){
                 $saldoAwal[$bar->noakun]+=$bar->jumlah;
                 $grndTotalSawal+=$bar->jumlah;
            }
            else{
                 $saldoAwal[$bar->noakun]-=$bar->jumlah;
                 $grndTotalSawal-=$bar->jumlah;
            }
        }
        
        $wherd="kasbank=1 and (pemilik='HOLDING' or pemilik='".$param['kodeorg']."' or pemilik='GLOBAL')";
        $cols = "noakun,notransaksi,tipetransaksi,tanggalposting as tanggal,jumlah,keterangan,matauang,nobayar";
        $where = "tanggalposting>='".tanggalsystem($periode1)."' and tanggalposting<='".
            tanggalsystem($periode2)."' and noakun>='".$param['noakun'].
            "' and noakun<='".$param['noakunsmp']."' and noakun in (select distinct noakun from  ".$dbname.".keu_5akun where ".$wherd.") and ".
            "kodeorg='".$kodeorg."' and posting=1";
        $sQue="select ".$cols." from ".$dbname.".keu_kasbankht where ".$where." order by noakun,tanggal asc";
        //exit("error:".$sQue);
        $query = mysql_query($sQue) or die(mysql_error($conn));
        while($res=  mysql_fetch_assoc($query)){
            $resH[]=$res;
        }
        #cek ada matauang selain IDR,jamhari
        $sMtUang="select matauang,noakun,notransaksi 
                  from ".$dbname.".keu_kasbankht where ".$where."";
        //exit("error:".$sMtUang);
        $qMtUang=mysql_query($sMtUang) or die(mysql_error($conn));
        while($rMtUang=mysql_fetch_assoc($qMtUang)){
            $lstMtuang[$rMtUang['matauang']]=$rMtUang['matauang'];
            $lstNoakun[$rMtUang['noakun']]=$rMtUang['noakun'];
            if($rMtUang['matauang']=='IDR'){
                $notExclude[$rMtUang['notransaksi']]=$rMtUang['notransaksi'];
            }
        }
        
	if(empty($resH)) {
	    echo 'Warning : No data found';
	    exit;
	}
        if(count($lstMtuang)>1){
            echo"List notransaction IDR Currency<pre>";
            print_r($notExclude);
            echo"</pre>";
            exit("error: Sorry can't display, the selected account number has multiple currency");
        }
        
        #ambil bulan lalu dan cek kurs bulanan bulan lalu dan bulan ini
        $tglGnti="01-".$blnawl."-".$thnawl;
        $currBln=$thnawl."-".$blnawl;
        $jmlhhari=1;
        $stat=0;
        $tglLalu=nambahHari($tglGnti, $jmlhhari, $stat);
//        $itungkosong=0;
//        foreach($lstMtuang as $dtMtuang){
//           if($dtMtuang!='IDR'){
//                $whr="periode='".substr($tglLalu,0,7)."' and matauang='".$dtMtuang."'";
//                $optKurs=makeOption($dbname, 'keu_5kursbulanan', 'matauang,kurs',$whr);            
//                $dtKurs=$optKurs[$dtMtuang];
//                $dtMatauang=$dtMtuang;
//                if($dtKurs==''){ 
//                    $whr="periode='".$currBln."' and matauang='".$dtMtuang."'";
//                    $optKurs=makeOption($dbname, 'keu_5kursbulanan', 'matauang,kurs',$whr);
//                    $dtKurs=$optKurs[$dtMtuang];
//                    $dtMatauang=$dtMtuang;
//                    if($dtKurs==''){
//                        $itungkosong+=1;
//                    }
//                }
//            }
//            else
//            {$dtKurs=1;}
//        }
//        if($itungkosong!=0){
//            exit("error: Please insert monthly rate for this month :".$currBln);
//        }
        
        $saldoKK=array();$saldoKM=array();
	$data = array();
        
        $varTemp="";
	foreach($resH as $key=>$row) {
            if($row['noakun']!=$varTemp){
                $nodt=1;
                $varTemp=$row['noakun'];
                $lstZkey[$row['noakun']]=$nodt;
            }else{
                $nodt+=1;
                $lstZkey[$row['noakun']]=$nodt;
            }
	    $data[$row['noakun']][$nodt] = array(
		'no'=>$nodt,
                'tanggal'=>tanggalnormal($row['tanggal']),
		'keterangan'=>$row['keterangan'],
		'km'=>'',
		'saldokm'=>'',
		'kk'=>'',
		'nobayar'=>'',
		'saldokk'=>'',
		'matauang'=>$row['matauang']
	    );
            if(($row['jumlah']!='')||($row['jumlah']!=0)){
                $sort_noakun[$row['noakun']]=$row['noakun'];
                
                if($row['tipetransaksi']=='K') {
                    $data[$row['noakun']][$nodt]['kk'] = $row['notransaksi'];
                    $data[$row['noakun']][$nodt]['nobayar'] = $row['nobayar'];
                    $data[$row['noakun']][$nodt]['saldokk'] = $row['jumlah'];
                    $saldoKK[$row['noakun']]+=$row['jumlah'];
                    $grndTotalKK+=$row['jumlah'];
                } else {
                    $data[$row['noakun']][$nodt]['km'] = $row['notransaksi'];
                    $data[$row['noakun']][$nodt]['nobayar'] = $row['nobayar'];
                    $data[$row['noakun']][$nodt]['saldokm'] = $row['jumlah'];
                    $saldoKM[$row['noakun']]+=$row['jumlah'];
                    $grndTotalKM+=$row['jumlah'];
                }
            }
            
	}
        
        // paling terakhir                
   $query1 = "select b.jumlah, b.tipetransaksi,b.keterangan2,b.notransaksi,a.tanggalposting as tanggal ,b.noakun,a.matauang,a.nobayar from ".$dbname.".keu_kasbankdt b 
              left join ".$dbname.".keu_kasbankht a on b.notransaksi=a.notransaksi
              where   b.noakun>='".$param['noakun']."' and b.noakun<='".$param['noakunsmp']."' 
              and b.noakun in (select noakun from ".$dbname.".keu_5akun where ".$wherd.") and b.kodeorg='".$kodeorg."' and
              a.tanggalposting>='".tanggalsystem($periode1)."' and a.tanggalposting<='".tanggalsystem($periode2)."'";
    $resH1 = fetchData($query1);
            foreach($resH1 as $key=>$row) {
                
            if($row['noakun']!=$varTemp){
                $nodt=$lstZkey[$row['noakun']];
                if($nodt==''){
                    $nodt=1;
                }else{
                    $nodt+=1;
                }
                $varTemp=$row['noakun'];
            }else{
                $nodt+=1;
                $lstZkey[$row['noakun']]=$nodt;
            }
            $data[$row['noakun']][$nodt] = array(
                'no'=>$nodt,
                'tanggal'=>tanggalnormal($row['tanggal']),
                'keterangan'=>$row['keterangan2'],
                'km'=>'',
                'saldokm'=>'',
                'kk'=>'',
                'saldokk'=>'',
		'matauang'=>$row['matauang']
            );
            if(($row['jumlah']!='')||($row['jumlah']!='0')){
                $sort_noakun[$row['noakun']]=$row['noakun'];
                
                if($row['tipetransaksi']=='M') {
                    $data[$row['noakun']][$nodt]['kk'] = $row['notransaksi'];
                    $data[$row['noakun']][$nodt]['saldokk'] = $row['jumlah'];
                    $saldoKK[$row['noakun']]+=$row['jumlah'];
                    $grndTotalKK+=$row['jumlah'];
                } else {
                    $data[$row['noakun']][$nodt]['km'] = $row['notransaksi'];
                    $data[$row['noakun']][$nodt]['saldokm'] = $row['jumlah'];
                    $saldoKM[$row['noakun']]+=$row['jumlah'];
                    $grndTotalKM+=$row['jumlah'];
                }
            }
        }
    
        if(!empty($data)) foreach($data as $c=>$key) {
            $sort_tangg[] = $key['tanggal'];
            $sort_debet[] = $key['saldokm'];
        }
        array_multisort($sort_noakun, SORT_ASC);
        // sort
        //if(!empty($data))array_multisort($sort_tangg, SORT_ASC, $sort_noakun, SORT_ASC,$sort_debet, SORT_DESC, $data);        
//	$dataShow = $data;
//	$dataExcel = $data;
//	foreach($dataShow as $key=>$row) {
//	    $row['saldokk']!='' ? $dataShow[$key][$row['noakun']]['saldokk'] = number_format($row['saldokk'],0) : null;
//	    $row['saldokm']!='' ? $dataShow[$key][$row['noakun']]['saldokm'] = number_format($row['saldokm'],0) : null;
//	}
        # Report Gen
        $theCols = array(
            $_SESSION['lang']['nomor'],
            $_SESSION['lang']['tanggal'],
            $_SESSION['lang']['keterangan'],
            $_SESSION['lang']['kasmasuk'],
            $_SESSION['lang']['penerimaan'],
            $_SESSION['lang']['kaskeluar'],
            $_SESSION['lang']['nobayar'],
            $_SESSION['lang']['pengeluaran'],
        );
	$align = explode(",","L,R,L,R,R,R,L,R");
	
	
    break;
}
if($param['pildt']==1)
{
    $brd=0;
    $bgclr="";
    $mode='totalAkun';
    if($_GET['mode']=='excel')
	{
        $mode="";
        $mode='totalAkun';
        $isiDari="cetakexcel";
        $brd=1;
        $bgclr="bgcolor:#DEDEDE";
    }
	elseif($_GET['mode']=='pdf')
	{  $mode="";
        $mode="cetakpdf";   
    }
}

$digit=2;   
switch($mode){
	
	
	
	
    case 'pdf':
        /** Report Prep **/
	# Options
	$optJab = makeOption($dbname,'sdm_5jabatan','kodejabatan,namajabatan',
	    "kodejabatan='".$_SESSION['empl']['kodejabatan']."'");
	
        $colPdf = array('nourut','tanggal','keterangan','kasmasuk','penerimaan',
            'kaskeluar','pengeluaran');
        $title = $_SESSION['lang']['kasharian'];
        $length = explode(",","5,12,35,10,14,10,14");
        
        $pdf = new zPdfMaster('P','pt','A4');
        $pdf->setAttr1($title,$align,$length,$colPdf);
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
	$pdf->AddPage();
        
        $pdf->SetFillColor(255,255,255);
	foreach($sort_noakun as $lstNoakun){
            if($erts!=$lstNoakun){
                # Saldo Awal
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(17/100*$width,$height,$lstNoakun,'TLR',0,'R',1);
                $pdf->Cell(59/100*$width,$height,$otNmAkun[$lstNoakun],'TLR',0,'L',1);
                $pdf->Cell(24/100*$width,$height,'','TLR',0,'L',1);
                $pdf->Ln();
                $pdf->Cell(17/100*$width,$height,'','TLR',0,'R',1);
                $pdf->Cell($length[2]/100*$width,$height,'Saldo Awal '.$periode1,'TLR',0,'C',1);
                $pdf->Cell($length[3]/100*$width,$height,'','TLR',0,'R',1);
                
//                @$saldoAwal[$lstNoakun]=$saldoAwal[$lstNoakun]/$dtKurs;
                @$saldoAwal[$lstNoakun]=$saldoAwal[$lstNoakun];
                //exit("error:".$saldoAwal[$lstNoakun]);
                $pdf->Cell($length[4]/100*$width,$height,number_format($saldoAwal[$lstNoakun],$digit),'TLR',0,'R',1);
                $pdf->Cell($length[5]/100*$width,$height,'','TLR',0,'R',1);
                $pdf->Cell($length[6]/100*$width,$height,'','TLR',0,'R',1);
                $pdf->Ln();
                $erts=$lstNoakun;
            }
	
	
	# Content
	$pdf->SetFont('Arial','',8);
        for($key=1;$key<=$lstZkey[$lstNoakun];$key++) {
                $pdf->Cell($length[0]/100*$width,$height,$data[$lstNoakun][$key][no],'TLR',0,'L',1);
                $pdf->Cell($length[1]/100*$width,$height,$data[$lstNoakun][$key][tanggal],'TLR',0,'C',1);
                $pdf->Cell($length[2]/100*$width,$height,$data[$lstNoakun][$key][keterangan],'TLR',0,'L',1);
                $pdf->Cell($length[3]/100*$width,$height,$data[$lstNoakun][$key][km],'TLR',0,'R',1);
                $pdf->Cell($length[4]/100*$width,$height,number_format($data[$lstNoakun][$key][saldokm],$digit),'TLR',0,'R',1);
                $pdf->Cell($length[5]/100*$width,$height,$data[$lstNoakun][$key][kk],'TLR',0,'R',1);
                $pdf->Cell($length[6]/100*$width,$height,number_format($data[$lstNoakun][$key][saldokk],$digit),'TLR',0,'R',1);
                $pdf->Ln();
            }
        
            $lenJudul = $length[0]+$length[1]+$length[2]+$length[3];
            # Total
            $pdf->Cell($lenJudul/100*$width,$height,'','TLR',0,'L',1);
            $pdf->Cell($length[4]/100*$width,$height,number_format($saldoKM[$lstNoakun],$digit),'TLR',0,$align[3],1);
            $pdf->Cell($length[5]/100*$width,$height,'','TLR',0,$align[4],1);
            $pdf->Cell($length[6]/100*$width,$height,number_format($saldoKK[$lstNoakun],$digit),'TLR',0,$align[5],1);
            $pdf->Ln();
            # Saldo
            $saldoSelisih[$erts]=$saldoKM[$erts] +$saldoAwal[$erts] - $saldoKK[$erts];
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell($lenJudul/100*$width,$height,$_SESSION['lang']['saldo'],'LR',0,'C',1);
            $pdf->Cell($length[4]/100*$width,$height,'','LR',0,$align[3],1);
            $pdf->Cell($length[5]/100*$width,$height,'','LR',0,$align[4],1);
            $pdf->Cell($length[6]/100*$width,$height,number_format($saldoSelisih[$lstNoakun],$digit),'LR',0,$align[5],1);
            $pdf->Ln();
            # Jumlah
            $pdf->Cell($lenJudul/100*$width,$height,$_SESSION['lang']['jumlah'],'LR',0,'C',1);
            $pdf->SetFont('Arial','',9);
            $pdf->Cell($length[4]/100*$width,$height,number_format($saldoKM[$lstNoakun],$digit),'BLR',0,$align[3],1);
            $pdf->Cell($length[5]/100*$width,$height,'','BLR',0,$align[4],1);
            $pdf->Cell($length[6]/100*$width,$height,number_format($saldoKM[$lstNoakun],$digit),'BLR',0,$align[5],1);
            $pdf->Ln();
            $pdf->Cell($lenJudul/100*$width,$height,'','L',0,$align[4],1);
            $pdf->Cell((100-$lenJudul)/100*$width,$height,'','TR',0,$align[4],1);
            $pdf->Ln();
            $saldoTerbilang+=$saldoKM[$lstNoakun];
            }
        
	# Terbilang
	$pdf->SetFont('Arial','I',9);
	$pdf->MultiCell($width,$height,
	    'Terbilang : [ '.terbilang($saldoTerbilang,0)." ".strtolower($dtMatauang).". ]",'LR','L');
	$pdf->Cell($width,$height,'','LR',0,$align[4],0);
	$pdf->Ln();
	
	# Tempat, Tanggal
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(2/3*$width,$height,'','L',0,$align[4],0);
	$pdf->Cell(1/3*$width,$height,$periode1,'R',0,'C',0);
	$pdf->Ln();
	
	# Mengetahui dll
	$pdf->Cell(1/3*$width,$height,$_SESSION['lang']['mengetahui'],'L',0,'C',0);
	$pdf->Cell(1/3*$width,$height,$_SESSION['lang']['diperiksa'],0,'C',0);
	$pdf->Cell(1/3*$width,$height,$_SESSION['lang']['disetujui'],'R',0,'C',0);
	$pdf->Ln();
	
	# Add few line
	$pdf->Cell($width,$height,'','LR',1,$align[4],0);
	$pdf->Cell($width,$height,'','LR',1,$align[4],0);
	$pdf->Cell($width,$height,'','LR',1,$align[4],0);
	
	# Nama
	$pdf->SetFont('Arial','U',9);
	$pdf->Cell(1/3*$width,$height,'                  ','L',0,'C',0);
	$pdf->Cell(1/3*$width,$height,'                  ','',0,'C',0);
	$pdf->Cell(1/3*$width,$height,$_SESSION['empl']['name'],'R',0,'C',0);
	$pdf->Ln();
	
	# Jabatan
	$pdf->SetFont('Arial','I',9);
	$pdf->Cell(1/3*$width,$height,'','LB',0,'C',0);
	$pdf->Cell(1/3*$width,$height,'','B',0,'C',0);
	$pdf->Cell(1/3*$width,$height,$optJab[$_SESSION['empl']['kodejabatan']],'RB',0,'C',0);
        
	$pdf->Output();
        break;
		
		
	case'cetakpdf':
	  class PDF extends FPDF
                    {
                            function Header() {
                                    //declarasi header variabel
                                    global $conn;
                                    global $dbname;
                                    global $align;
                                    global $length;
                                    global $colArr;
                                    global $title;

                                    global $nmOrg;
                                    global $kdOrg;
                                    global $kdAst;
                                    global $nmAst;
                                    global $thnPer;
                                    global $nmAsst;
                                    global $namakar;
                                    global $selisih;
                                    global $where;
                                    global $dtKurs;

                                    //alamat PT minanga dan logo
                                    $query = selectQuery($dbname,'organisasi','alamat,telepon',
                                            "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                                    $orgData = fetchData($query);

                                    $width = $this->w - $this->lMargin - $this->rMargin;
                                    $height = 20;
                                    $path='images/'.strtolower($_SESSION['org']['kodeorganisasi']).'_logo.jpg';
                                    $this->Image($path,$this->lMargin,$this->tMargin,70);	
                                    $this->SetFont('Arial','B',9);
                                    $this->SetFillColor(255,255,255);	
                                    $this->SetX(100);   
                                    $this->Cell($width-100,$height,$_SESSION['org']['namaorganisasi'],0,1,'L');	 
                                    $this->SetX(100); 		
                                    $this->Cell($width-100,$height,$orgData[0]['alamat'],0,1,'L');	
                                    $this->SetX(100); 			
                                    $this->Cell($width-100,$height,"Tel: ".$orgData[0]['telepon'],0,1,'L');	
                                    $this->Line($this->lMargin,$this->tMargin+($height*4),
                                    $this->lMargin+$width,$this->tMargin+($height*4));
                                    $this->Ln(25);
                                    //tutup logo dan alamat

                                    //untuk sub judul
                                    $this->SetFont('Arial','B',10);
                                    //$this->Cell((20/100*$width)-5,$height,"Daftar Asset",'',0,'L');
                                   // $this->Ln();
                                    $this->SetFont('Arial','',8);
                                    $this->Cell((100/100*$width)-5,10,"Printed By : ".$_SESSION['standard']['username'],'',1,'L');
                                    $this->Cell((100/100*$width)-5,10,"Date : ".date('d-m-Y'),'',1,'L');
                                    $this->Cell((100/100*$width)-5,10,"Time : ".date('h:i:s'),'',1,'L');
                                   
                                    $this->Ln();
                                    //tutup sub judul

                                    //judul tengah
                                    $this->SetFont('Arial','B',12);
                                    $this->Cell($width,$height,'KAS BANK','',0,'C');
                                    $this->Ln();
                                  //  $this->Cell($width,$height,strtoupper("$nmOrg"),'',0,'C');
                                    $this->Ln();
                                  
                                     $this->SetFont('Arial','B',8);
                                    $this->SetFillColor(220,220,220);
                                    
                                    $this->Cell(15/100*$width,$height,$_SESSION['lang']['noakun'],1,0,'C',1);
                                    $this->Cell(25/100*$width,$height,$_SESSION['lang']['namaakun'],1,0,'C',1);
									$this->Cell(15/100*$width,$height,$_SESSION['lang']['saldoawal'],1,0,'C',1);
									$this->Cell(15/100*$width,$height,$_SESSION['lang']['kasmasuk'],1,0,'C',1);
									$this->Cell(15/100*$width,$height,$_SESSION['lang']['kaskeluar'],1,0,'C',1);
									$this->Cell(15/100*$width,$height,$_SESSION['lang']['saldoakhir'],1,1,'C',1);
                                  
									
									
									//Nomor Akun	Nama Akun	Saldo Awal	KM	KK	Saldo Akhir



                                   
                            }


                            function Footer()
                            {
                                    $this->SetY(-15);
                                    $this->SetFont('Arial','I',8);
                                    $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
                            }
                    }
                    //untuk tampilan setting pdf
                    $pdf=new PDF('p','pt','Legal');//untuk kertas L=len p=pot
                    $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
                    $height = 20;
                    $pdf->AddPage();
                    $pdf->SetFillColor(255,255,255);
                    $pdf->SetFont('Arial','',8);

                    $no=0;
					
                 	
					foreach($sort_noakun as $lstNoakun){
//                                                @$saldoAwal[$lstNoakun]=$saldoAwal[$lstNoakun]/$dtKurs;
                                                @$saldoAwal[$lstNoakun]=$saldoAwal[$lstNoakun];
						$saldoAkhir[$lstNoakun]=$saldoKM[$lstNoakun] +$saldoAwal[$lstNoakun] - $saldoKK[$lstNoakun];
                                                
						//$tab .="<tr class=rowcontent>";
						//$tab .= "<td>".$lstNoakun."</td><td>".$otNmAkun[$lstNoakun]."</td>";
						$pdf->Cell(15/100*$width,$height,$lstNoakun,1,0,'L',1);
						$pdf->Cell(25/100*$width,$height,$otNmAkun[$lstNoakun],1,0,'L',1);
						$pdf->Cell(15/100*$width,$height,number_format($saldoAwal[$lstNoakun],$digit),1,0,'R',1);
						$pdf->Cell(15/100*$width,$height,number_format($saldoKM[$lstNoakun],$digit),1,0,'R',1);
						$pdf->Cell(15/100*$width,$height,number_format($saldoKK[$lstNoakun],$digit),1,0,'R',1);
						$pdf->Cell(15/100*$width,$height,number_format($saldoAkhir[$lstNoakun],$digit),1,1,'R',1);
						
						
						
						$grSalKm+=$saldoKM[$lstNoakun];
						$grSalKk+=$saldoKK[$lstNoakun];
						$grSalAw+=$saldoAwal[$lstNoakun];
					}
					$selisihTot=$grSalAw+$grSalKm-$grSalKk;
					$pdf->Cell(40/100*$width,$height,$_SESSION['lang']['total'],1,0,'R',1);
					$pdf->Cell(15/100*$width,$height,number_format($grSalAw,$digit),1,0,'R',1);
					$pdf->Cell(15/100*$width,$height,number_format($grSalKm,$digit),1,0,'R',1);
					$pdf->Cell(15/100*$width,$height,number_format($grSalKk,$digit),1,0,'R',1);
					$pdf->Cell(15/100*$width,$height,number_format($selisihTot,$digit),1,0,'R',1);
					
					
					$tab .="<tr class=rowcontent>";
					$tab .= "<td colspan=2>".$_SESSION['lang']['total']."</td>";
					$tab .= "<td>".number_format($grSalAw,$digit)."</td>";
					$tab .= "<td>".number_format($grSalKm,$digit)."</td>";
					$tab .= "<td>".number_format($grSalKk,$digit)."</td>";
					
					$tab .= "<td>".number_format($selisihTot,$digit)."</td>";
					$tab .="</tr>";
					$tab .="</tbody></table>";
					
					
					
                                      
                                        


                    
            $pdf->Output();
	break;	
		
		
        case'totalAkun':
            $tab.="<table cellpadding=1 cellspacing=1 border=".$brd.">";
            $tab.="<thead>";
            $tab.="<tr class=rowheader ".$bgclr."><td>".$_SESSION['lang']['noakun']."</td>";
            $tab.="<td align=center>".$_SESSION['lang']['namaakun']."</td>";
            $tab.="<td align=center>".$_SESSION['lang']['saldoawal']."</td>";
            $tab.="<td align=center>".$_SESSION['lang']['kasmasuk']."</td>";
            $tab.="<td align=center>".$_SESSION['lang']['kaskeluar']."</td>";
            $tab.="<td align=center>".$_SESSION['lang']['saldoakhir']."</td></tr></thead><tbody>";
            foreach($sort_noakun as $lstNoakun){
//                 @$saldoAwal[$lstNoakun]=$saldoAwal[$lstNoakun]/$dtKurs;
                 @$saldoAwal[$lstNoakun]=$saldoAwal[$lstNoakun];
                 $tab .="<tr class=rowcontent>";
                 $tab .= "<td>".$lstNoakun."</td><td>".$otNmAkun[$lstNoakun]."</td>";
                 $tab .= "<td align=right>".number_format($saldoAwal[$lstNoakun],$digit)."</td>";
                 $tab .= "<td align=right>".number_format($saldoKM[$lstNoakun],$digit)."</td>";
                 $tab .= "<td align=right>".number_format($saldoKK[$lstNoakun],$digit)."</td>";
                 $saldoAkhir[$lstNoakun]=$saldoKM[$lstNoakun] +$saldoAwal[$lstNoakun] - $saldoKK[$lstNoakun];
                 $tab .= "<td align=right>".number_format($saldoAkhir[$lstNoakun],$digit)."</td>";
                 $tab .="</tr>";
                 $grSalKm+=$saldoKM[$lstNoakun];
                 $grSalKk+=$saldoKK[$lstNoakun];
                 $grSalAw+=$saldoAwal[$lstNoakun];
            }
            $tab .="<tr class=rowcontent>";
            $tab .= "<td colspan=2>".$_SESSION['lang']['total']."</td>";
            $tab .= "<td align=right>".number_format($grSalAw,$digit)."</td>";
            $tab .= "<td align=right>".number_format($grSalKm,$digit)."</td>";
            $tab .= "<td align=right>".number_format($grSalKk,$digit)."</td>";
            $selisihTot=$grSalAw+$grSalKm-$grSalKk;
            $tab .= "<td  align=right>".number_format($selisihTot,$digit)."</td>";
            $tab .="</tr>";
            $tab .="</tbody></table>";
            if($_GET['mode']=='excel'){
                $stream = $tab;
                $nop_="KasHarian_total".$kodeorg;
                if(strlen($stream)>0) {
                    # Delete if exist
                    if ($handle = opendir('tempExcel')) {
                        while (false !== ($file = readdir($handle))) {
                            if ($file != "." && $file != "..") {
                                @unlink('tempExcel/'.$file);
                            }
                        }	
                        closedir($handle);
                    }

                    # Write to File
                    $handle=fopen("tempExcel/".$nop_.".xls",'w');
                    if(!fwrite($handle,$stream)) {
                        echo "Error : Tidak bisa menulis ke format excel";
                        exit;
                    } else {
                        echo $nop_;
                    }
                    fclose($handle);
                }
            }else{
                echo $tab;
            }
        break;
    default:
	# Redefine Align
	$alignPrev = array();
	foreach($align as $key=>$row) {
	    switch($row) {
		case 'L':
		    $alignPrev[$key] = 'left';
		    break;
		case 'R':
		    $alignPrev[$key] = 'right';
		    break;
		case 'C':
		    $alignPrev[$key] = 'center';
		    break;
		default:
	    }
	}
	
        $bgclr="";
	/** Mode Header **/
        if($mode=='excel') {
            $tab = strtoupper($_SESSION['lang']['kasharian'])." : ".$namagudang."<br>".
            strtoupper($_SESSION['lang']['noakun'])." : ".$param['noakun']."<br>".
            strtoupper($_SESSION['lang']['periode'])." : ".$periode1." s/d ".$periode2.
                $brd="border=1";
                $bgclr="bgcolor:#DEDEDE";
        } 
        $tab .= "<table id='kasharian' class='sortable' ".$brd.">";
        $tab .= "<thead><tr class='rowheader' ".$bgclr.">";
	/** Generate Table **/
        foreach($theCols as $head) {
           $tab .= "<td>".$head."</td>";
        }
        $tab .= "</tr></thead>";
        $tab .= "<tbody>";
        foreach($sort_noakun as $lstNoakun){
            if($erts!=$lstNoakun){
                # Saldo Awal
                $tab .= "<tr class='rowcontent'>";
                $tab .= "<td><b>".$lstNoakun."</b></td><td colspan=2><b>".$otNmAkun[$lstNoakun]."</b></td>";
                $tab .= "<td></td><td></td>";
                $tab .= "<td></td><td></td><td></td>";
                $tab .="</tr>";
                $tab .= "<tr class='rowcontent'>";
                $tab .= "<td></td><td align='center' colspan=2>Saldo Awal ".$periode1."</td>";
//                @$saldoAwal[$lstNoakun]=$saldoAwal[$lstNoakun]/$dtKurs;
                @$saldoAwal[$lstNoakun]=$saldoAwal[$lstNoakun];
                $tab .= "<td></td><td align='right'>".number_format($saldoAwal[$lstNoakun],$digit)."</td>";
                $tab .= "<td></td><td></td><td></td>";
                $tab .= "</tr>";
                $erts=$lstNoakun;
            }
            for($key=1;$key<=$lstZkey[$lstNoakun];$key++) {
                $tab .= "<tr class='rowcontent'>";
                $tab .= "<td ".$alignPrev[$key].">".$data[$lstNoakun][$key][no]."</td>";
                $tab .= "<td ".$alignPrev[$key]." nowrap>".$data[$lstNoakun][$key][tanggal]."</td>";
                $tab .= "<td ".$alignPrev[$key].">".$data[$lstNoakun][$key][keterangan]."</td>";
                $tab .= "<td ".$alignPrev[$key].">".$data[$lstNoakun][$key][km]."</td>";
                $tab .= "<td ".$alignPrev[$key]." align=right>".number_format($data[$lstNoakun][$key][saldokm],$digit)."</td>";
                $tab .= "<td ".$alignPrev[$key].">".$data[$lstNoakun][$key][kk]."</td>";
                $tab .= "<td ".$alignPrev[$key].">".$data[$lstNoakun][$key]['nobayar']."</td>";
                $tab .= "<td  align=right>".number_format($data[$lstNoakun][$key][saldokk],$digit)."</td>";
                $tab .="</tr>";
            }
            $tab .= "<tr class='rowcontent'>";
            $tab .= "<td colspan='4' align='right'></td>";
            $tab .= "<td align='right'>".number_format(($saldoKM[$erts]+$saldoAwal[$erts]),$digit)."</td><td></td><td></td>";
            $tab .= "<td align='right'>".number_format($saldoKK[$erts],$digit)."</td>";
            $tab .= "</tr>";
            # Saldo
            $saldoSelisih[$erts]=$saldoKM[$erts] +$saldoAwal[$erts] - $saldoKK[$erts];
            $tab .= "<tr class='rowcontent'>";
            $tab .= "<td colspan='4' align='right'>".$_SESSION['lang']['saldo']." ".$erts."</td>";
            $tab .= "<td align='right'></td><td></td><td></td>";
            $tab .= "<td align='right'><b>".number_format($saldoSelisih[$erts],$digit)."</b></td>";
            $tab .= "</tr>";
            # Jumlah
            $tab .= "<tr class='rowcontent'>";
            $tab .= "<td colspan='4' align='right'>".$_SESSION['lang']['jumlah']." ".$erts."</td>";
            $tab .= "<td align='right'>".number_format(($saldoKM[$erts]+$saldoAwal[$erts]),$digit)."</td><td></td><td></td>";
            $tab .= "<td align='right'>".number_format(($saldoKM[$erts]+$saldoAwal[$erts]),$digit)."</td>";
            $tab .= "</tr>";
        }
//            $grndTotalSawal=$grndTotalSawal/$dtKurs;
            $grndTotalSawal=$grndTotalSawal;
            $tab .= "<tr class='rowheader' ".$bgclr.">";
            $tab .= "<td colspan='4' align='right'></td>";
            $tab .= "<td align='right'>".number_format(($grndTotalKM+$grndTotalSawal),$digit)."</td><td></td><td></td>";
            $tab .= "<td align='right'>".number_format($grndTotalKK,$digit)."</td>";
            $tab .= "</tr>";
            # Saldo
            
            $selisihTot=$grndTotalSawal+$grndTotalKM-$grndTotalKK;
            $tab .= "<tr class='rowheader' ".$bgclr.">";
            $tab .= "<td colspan='4' align='right'><b>".$_SESSION['lang']['saldo']." ".$_SESSION['lang']['total']."</b></td>";
            $tab .= "<td align='right'></td><td></td><td></td>";
            $tab .= "<td align='right'><b>".number_format($selisihTot,$digit)."</b></td>";
            $tab .= "</tr>";
            # Jumlah
            $tab .= "<tr class='rowheader' ".$bgclr.">";
            $tab .= "<td colspan='4' align='right'><b>".$_SESSION['lang']['jumlah']." ".$_SESSION['lang']['total']."</b></td>";
            $tab .= "<td align='right'>".number_format(($grndTotalKM+$grndTotalSawal),$digit)."</td><td></td><td></td>";
            $tab .= "<td align='right'>".number_format(($grndTotalKM+$grndTotalSawal),$digit)."</td>";
            $tab .= "</tr>";
        $tab .= "</tbody>";
        $tab .= "</table>";
        
        /** Output Type **/
        if($mode=='excel') {
            $stream = $tab;
            $nop_="KasHarian_".$kodeorg;
            if(strlen($stream)>0) {
                # Delete if exist
                if ($handle = opendir('tempExcel')) {
                    while (false !== ($file = readdir($handle))) {
                        if ($file != "." && $file != "..") {
                            @unlink('tempExcel/'.$file);
                        }
                    }	
                    closedir($handle);
                }
                
                # Write to File
                $handle=fopen("tempExcel/".$nop_.".xls",'w');
                if(!fwrite($handle,$stream)) {
                    echo "Error : Tidak bisa menulis ke format excel";
                    exit;
                } else {
                    echo $nop_;
                }
                fclose($handle);
            }
        } else {
            echo $tab;
        }
        break;
}



switch($param['proses']){
    case'getNoakun':
        $optNoakun.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $optTipe=makeOption($dbname, 'organisasi', 'kodeorganisasi,tipe');
        $wherd="kasbank=1 and (pemilik='".$param['kodeorg']."' or pemilik='GLOBAL')";
        if($optTipe[$param['kodeorg']]=='HOLDING'){
            $wherd="kasbank=1 and (pemilik='HOLDING' or pemilik='".$param['kodeorg']."' or pemilik='GLOBAL')";
        }
        $optAkun = makeOption($dbname,'keu_5akun','noakun,namaakun',$wherd,'noakun');
        foreach($optAkun as $row=>$lstAkun){
            $optNoakun.="<option value='".$row."'>".$lstAkun."</option>";
        }
        echo $optNoakun;
    break;
	
}
?>