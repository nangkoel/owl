<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');
include_once('lib/zPdfMaster.php');

$proses = $_GET['proses'];
$tipe=$_GET['tipe'];
$param = $_GET;

if($_SESSION['language']=='EN'){
    $optKegiatan=makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan1');
}else{
        $optKegiatan=makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
}
$optSatKegiatan=makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,satuan');
$optNamaKary=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optNamaBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optGudang=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');

/** Report Prep **/
$cols = array();

# Prestasi
//$col1 = 'nik,kodekegiatan,kodeorg,hasilkerja,jumlahhk,upahkerja,upahpremi,umr';
$col1 = 'tanggal,kodekegiatan,a.kodeorg,hasilkerja,jumlahhk,upahkerja,upahpremi,umr';
$cols[] = explode(',',$col1);
//$query = selectQuery($dbname,'kebun_prestasi',$col1,
//    "notransaksi='".$param['notransaksi']."'");
$query="select ".$col1." from ".$dbname.".kebun_prestasi a left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi where a.notransaksi='".$param['notransaksi']."'";
//exit("Error".$query);
$data[] = fetchData($query);
$align[] = explode(",","L,L,L,R,R,R,R,R");
$length[] = explode(",","10,10,15,10,10,15,15,15");

# Kehadiran
$col2 = 'nik,absensi,jhk,umr,insentif';
$cols[] = explode(',',$col2);
$query = selectQuery($dbname,'kebun_kehadiran',$col2,
    "notransaksi='".$param['notransaksi']."'");
$data[] = fetchData($query);
$align[] = explode(",","L,L,R,R,R");
$length[] = explode(",","20,20,20,20,20");

# Pakai Material
$col3 = 'kodeorg,kodebarang,kwantitas,kwantitasha,hargasatuan';
$cols[] = explode(',',$col3);
$query = selectQuery($dbname,'kebun_pakaimaterial',$col3,
    "notransaksi='".$param['notransaksi']."'");
$data[] = fetchData($query);
$align[] = explode(",","L,L,R,R,R");
$length[] = explode(",","20,20,20,20,20");

//getNamakaryawan
$sDtKaryawn="select karyawanid,namakaryawan from ".$dbname.".datakaryawan order by namakaryawan asc";
$rData=fetchData($sDtKaryawn);
foreach($rData as $brKary =>$rNamakaryawan)
{
    $RnamaKary[$rNamakaryawan['karyawanid']]=$rNamakaryawan['namakaryawan'];
}
switch($tipe) {
    case "LC":
        $title = "Land Clearing";
        break;
    case "BBT":
	$title = $_SESSION['lang']['pembibitan'];
	break;
    case "TBM":
	$title = "UPKEEP-".$_SESSION['lang']['tbm'];
	break;
    case "TM":
		if(isset($_SESSION['lang']['tm'])) {
			$title = "UPKEEP-".$_SESSION['lang']['tm'];
		} else {
			$title = "UPKEEP";
		}
	break;
	case "PNN":
	$title = $_SESSION['lang']['panen'];
	break;
    case "TB":
	$title = "UPKEEP-".$_SESSION['lang']['tbm'];
	break;
    default:
	echo "Error : Atribut not Defined";
	exit;
	break;
}
$titleDetail = array($_SESSION['lang']['prestasi'],$_SESSION['lang']['absensi'],$_SESSION['lang']['material']);

/** Output Format **/
switch($proses) {
    case 'pdf':
        
        $pdf=new zPdfMaster('P','pt','A4');
        $pdf->_noThead=true;
        $pdf->setAttr1($title,$align,$length,array());
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
	$pdf->AddPage();
        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','B',9);
        $pdf->Ln();
        $pdf->Cell($width,$height,$_SESSION['lang']['notransaksi']." : ".$param['notransaksi'],0,1,'L',1);
        //'tanggal,kodekegiatan,a.kodeorg,hasilkerja,jumlahhk,upahkerja,upahpremi,umr';
       
        $sPres="select distinct sum(a.insentif) as upahpremi, sum(a.umr) as umr,sum(a.jhk) as jumlahhk,kodekegiatan,
                tanggal,b.kodeorg,b.hasilkerja,b.jjg from ".$dbname.".kebun_kehadiran a left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
                left join ".$dbname.".kebun_aktifitas c on a.notransaksi=c.notransaksi where a.notransaksi='".$param['notransaksi']."' group by a.notransaksi";
       
       // echo $sPres;
        //length[] = explode(",","10,10,15,10,10,15,15,15");
        $pdf->Ln();
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell($width,$height,$titleDetail[0],0,1,'L',1);
        $pdf->SetFillColor(220,220,220);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(10/100*$width,$height,$_SESSION['lang']['tanggal'],1,0,'C',1);
        $pdf->Cell(22/100*$width,$height,$_SESSION['lang']['namakegiatan'],1,0,'C',1);
        $pdf->Cell(10/100*$width,$height,$_SESSION['lang']['blok'],1,0,'C',1);
		$pdf->Cell(8/100*$width,$height,$_SESSION['lang']['bloklama'],1,0,'C',1);
		$pdf->Cell(7/100*$width,$height,$_SESSION['lang']['jjg'],1,0,'C',1);
        $pdf->Cell(10/100*$width,$height,$_SESSION['lang']['hasilkerjad'],1,0,'C',1);
        $pdf->Cell(6/100*$width,$height,$_SESSION['lang']['satuan'],1,0,'C',1);
        $pdf->Cell(14/100*$width,$height,$_SESSION['lang']['upahpremi'],1,0,'C',1);
        $pdf->Cell(14/100*$width,$height,$_SESSION['lang']['umr'],1,1,'C',1);
        $qPres=mysql_query($sPres) or die(mysql_error($conn));
        $rPres=mysql_fetch_assoc($qPres);
		$optBlokLama = makeOption($dbname,'setup_blok','kodeorg,bloklama',"kodeorg='".$rPres['kodeorg']."'");
        $pdf->SetFont('Arial','',7);
		
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(10/100*$width,$height,tanggalnormal($rPres['tanggal']),1,0,'C',1);
        $pdf->Cell(22/100*$width,$height,$optKegiatan[$rPres['kodekegiatan']],1,0,'L',1);
        $pdf->Cell(10/100*$width,$height,$rPres['kodeorg'],1,0,'L',1);
		$pdf->Cell(8/100*$width,$height,$optBlokLama[$rPres['kodeorg']],1,0,'L',1);
		$pdf->Cell(7/100*$width,$height,$rPres['jjg'],1,0,'R',1);
        $pdf->Cell(10/100*$width,$height,$rPres['hasilkerja'],1,0,'R',1);
        $pdf->Cell(6/100*$width,$height,$optSatKegiatan[$rPres['kodekegiatan']],1,0,'C',1);
        $pdf->Cell(14/100*$width,$height,number_format($rPres['upahpremi'],0),1,0,'R',1);
        $pdf->Cell(14/100*$width,$height,number_format($rPres['umr'],0),1,1,'R',1);
        
        //$col2 = 'nik,absensi,jhk,umr,insentif';
        
        
        $sKhdrn="select distinct * from ".$dbname.".kebun_kehadiran where notransaksi='".$param['notransaksi']."'";
        $qKhdrn=mysql_query($sKhdrn) or die(mysql_error($conn));
        $pdf->Ln(30);
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell($width,$height,$titleDetail[1],0,1,'L',1);
        $pdf->SetFillColor(220,220,220);
        $pdf->SetFont('Arial','B',8);
        
        $pdf->Cell(5/100*$width,$height,"No.",1,0,'C',1);
        $pdf->Cell(20/100*$width,$height,$_SESSION['lang']['nik'],1,0,'C',1);
        $pdf->Cell(10/100*$width,$height,$_SESSION['lang']['absensi'],1,0,'C',1);
		
		$pdf->Cell(10/100*$width,$height,$_SESSION['lang']['jjg'],1,0,'C',1);
		$pdf->Cell(10/100*$width,$height,$_SESSION['lang']['hasilkerjad'],1,0,'C',1);
		
        $pdf->Cell(10/100*$width,$height,$_SESSION['lang']['jhk'],1,0,'C',1);
        $pdf->Cell(10/100*$width,$height,$_SESSION['lang']['umr'],1,0,'C',1);
        $pdf->Cell(10/100*$width,$height,$_SESSION['lang']['insentif'],1,1,'C',1);
        $pdf->SetFont('Arial','',7);
        $pdf->SetFillColor(255,255,255);
		$no=0;
		$totHk=0;
		$totUmr=0;
		$totIns=0;
		$totJjg=0;
		$totHasilKerja=0;
        while($rKhdrn=mysql_fetch_assoc($qKhdrn))
        {
            $no++;
            $pdf->Cell(5/100*$width,$height,$no,1,0,'C',1);
            $pdf->Cell(20/100*$width,$height,$optNamaKary[$rKhdrn['nik']],1,0,'L',1);
            $pdf->Cell(10/100*$width,$height,$rKhdrn['absensi'],1,0,'C',1);
			
			$pdf->Cell(10/100*$width,$height,$rKhdrn['jjg'],1,0,'C',1);
			$pdf->Cell(10/100*$width,$height,$rKhdrn['hasilkerja'],1,0,'C',1);
			
            $pdf->Cell(10/100*$width,$height,$rKhdrn['jhk'],1,0,'C',1);
            $pdf->Cell(10/100*$width,$height,number_format($rKhdrn['umr'],0),1,0,'R',1);
            $pdf->Cell(10/100*$width,$height,number_format($rKhdrn['insentif'],0),1,1,'R',1);
            $totHk+=$rKhdrn['jhk'];
            $totUmr+=$rKhdrn['umr'];
            $totIns+=$rKhdrn['insentif'];
			$totJjg+=$rKhdrn['jjg'];
			$totHasilKerja+=$rKhdrn['hasilkerja'];
        }
            $pdf->SetFillColor(220,220,220);
            $pdf->Cell(35/100*$width,$height,$_SESSION['lang']['total'],1,0,'C',1);
			$pdf->Cell(10/100*$width,$height,$totJjg,1,0,'C',1);
			$pdf->Cell(10/100*$width,$height,$totHasilKerja,1,0,'C',1);
            $pdf->Cell(10/100*$width,$height,$totHk,1,0,'C',1);
            $pdf->Cell(10/100*$width,$height,number_format($totUmr,0),1,0,'R',1);
            $pdf->Cell(10/100*$width,$height,number_format($totIns,0),1,1,'R',1);
        $pdf->SetFillColor(255,255,255);    
        $pdf->Ln(30);
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell($width,$height,$titleDetail[2],0,1,'L',1);
        $pdf->SetFillColor(220,220,220);
        $pdf->SetFont('Arial','B',8);
        $sMat="select distinct * from ".$dbname.".kebun_pakaimaterial where notransaksi='".$param['notransaksi']."'";
        $qMat=mysql_query($sMat) or die(mysql_error($conn));
        
        $pdf->Cell(5/100*$width,$height,"No.",1,0,'C',1);
        $pdf->Cell(13/100*$width,$height,$_SESSION['lang']['kodeorg'],1,0,'C',1);
        $pdf->Cell(10/100*$width,$height,$_SESSION['lang']['kodebarang'],1,0,'C',1);
        $pdf->Cell(10/100*$width,$height,$_SESSION['lang']['kwantitas'],1,0,'C',1);
        $pdf->Cell(15/100*$width,$height,$_SESSION['lang']['kwantitasha'],1,0,'C',1);
        $pdf->Cell(10/100*$width,$height,$_SESSION['lang']['hargasatuan'],1,0,'C',1);
        $pdf->Cell(30/100*$width,$height,$_SESSION['lang']['sloc'],1,1,'C',1);
        $pdf->SetFont('Arial','',7);
        $pdf->SetFillColor(255,255,255);
		$no3=0;
        while($rMat=mysql_fetch_assoc($qMat))
        {
            $no3++;
            $pdf->Cell(5/100*$width,$height,$no3,1,0,'C',1);
            $pdf->Cell(13/100*$width,$height,$rMat['kodeorg'],1,0,'C',1);
            $pdf->Cell(10/100*$width,$height,$optNamaBrg[$rMat['kodebarang']],1,0,'L',1);
            $pdf->Cell(10/100*$width,$height,$rMat['kwantitas'],1,0,'C',1);
            $pdf->Cell(15/100*$width,$height,$rMat['kwantitasha'],1,0,'R',1);
            $pdf->Cell(10/100*$width,$height,$rMat['hargasatuan'],1,0,'R',1);
            $pdf->Cell(30/100*$width,$height,$optGudang[$rMat['kodegudang']],1,1,'L',1);
        }
        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','B',8);
        $sAsis="select distinct nikmandor,nikmandor1,nikasisten,keranimuat,tanggal,kodeorg from ".$dbname.".kebun_aktifitas where notransaksi='".$param['notransaksi']."'";
        $qAsis=mysql_query($sAsis) or die(mysql_error($conn));
        $rAsis=mysql_fetch_assoc($qAsis);
        $pdf->ln(35);
        $pdf->Cell(85/100*$width,$height,$rAsis['kodeorg'].",".tanggalnormal($rAsis['tanggal']),0,1,'R',0);
        $pdf->ln(35);
        $pdf->Cell(28/100*$width,$height,$_SESSION['lang']['dstujui_oleh'],0,0,'C',0);
        $pdf->Cell(28/100*$width,$height,$_SESSION['lang']['diperiksa'],0,0,'C',0);
        $pdf->Cell(29/100*$width,$height,$_SESSION['lang']['dibuatoleh'],0,1,'C',0);
        $pdf->ln(65);
        $pdf->SetFont('Arial','U',8);
        $pdf->Cell(28/100*$width,$height,$RnamaKary[$rAsis['nikasisten']],0,0,'C',0);
        $pdf->Cell(28/100*$width,$height,$RnamaKary[$rAsis['nikmandor1']],0,0,'C',0);
        $pdf->Cell(29/100*$width,$height,$RnamaKary[$rAsis['nikmandor']],0,1,'C',0);
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(28/100*$width,$height,$_SESSION['lang']['asisten'],0,0,'C',0);
        $pdf->Cell(28/100*$width,$height,$_SESSION['lang']['nikmandor1'],0,0,'C',0);
        $pdf->Cell(29/100*$width,$height,$_SESSION['lang']['nikmandor'],0,1,'C',0);
            //$col3 = 'kodeorg,kodebarang,kwantitas,kwantitasha,hargasatuan';
//        foreach($data as $c=>$dataD) {
//            # Header
//            $pdf->SetFont('Arial','B',9);
//            $pdf->Cell($width,$height,$titleDetail[$c],0,1,'L',1);
//            $pdf->SetFillColor(220,220,220);
//            $i=0;
//            foreach($cols[$c] as $column) {
//                if(substr($column,1,1)==".")
//                {
//                    $column=substr($column,2,7);
//                    //exit("error".$column);
//                }
//                
//                $pdf->Cell($length[$c][$i]/100*$width,$height,$_SESSION['lang'][$column],1,0,'C',1);
//                $i++;
//            }
//            $pdf->Ln();
//            
//            # Content
//            $pdf->SetFillColor(255,255,255);
//            $pdf->SetFont('Arial','',9);
//            foreach($dataD as $key=>$row) {    
//                $i=0;
//                foreach($row as $cont) {
//                    if(strlen($cont)==10)
//                    {
//                        if(substr($cont,4,1)=='-')
//                        {
//                            $cont=tanggalnormal($cont);
//                        }
//                        else
//                        {
//                            $cont=$RnamaKary[$cont];
//                        }
//                    }
//                   
//                        $pdf->Cell($length[$c][$i]/100*$width,$height,$cont,1,0,$align[$c][$i],1);
//                    
//                    $i++;
//                }
//                $pdf->Ln();
//            }
//            $pdf->Ln();
//        }
	
        $pdf->Output();
        break;
    case 'excel':
        break;
		
    case'html':
        
        $tab.="<link rel=stylesheet type=text/css href=style/generic.css>";
        $tab.="<fieldset><legend>".$title."</legend>";
        $tab.="<table cellpadding=1 cellspacing=1 border=0 width=65% class=sortable><tbody class=rowcontent>";
        $tab.="<tr><td>".$_SESSION['lang']['kodeorganisasi']."</td><td> :</td><td> ".$_SESSION['empl']['lokasitugas']."</td></tr>";
        $tab.="<tr><td>".$_SESSION['lang']['notransaksi']."</td><td> :</td><td> ".$param['notransaksi']."</td></tr>";
        
        $tab.="</tbody></table>";
            
        
        $tab.="<br />".$titleDetail[0]."<br />";
        $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>";
        $tab.="<tr class=rowheader>";
        $tab.="<td>".$_SESSION['lang']['tanggal']."</td>";
        $tab.="<td>".$_SESSION['lang']['kodeorg']."</td>";
        $tab.="<td>".$_SESSION['lang']['namakegiatan']."</td>";
		$tab.="<td>".$_SESSION['lang']['jjg']."</td>";
        $tab.="<td>".$_SESSION['lang']['hasilkerjad']."</td>";
        $tab.="<td>".$_SESSION['lang']['satuan']."</td>";
        $tab.="<td>".$_SESSION['lang']['upahpremi']."</td>";
        $tab.="<td>".$_SESSION['lang']['umr']."</td>";
        $tab.="</tr></thead><tbody>";
         $sPres="select distinct sum(a.insentif) as upahpremi, sum(a.umr) as umr,sum(a.jhk) as jumlahhk,kodekegiatan,
                tanggal,b.kodeorg,b.hasilkerja,b.jjg from ".$dbname.".kebun_kehadiran a left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
                left join ".$dbname.".kebun_aktifitas c on a.notransaksi=c.notransaksi where a.notransaksi='".$param['notransaksi']."' group by a.notransaksi";
         $qPres=mysql_query($sPres) or die(mysql_error($conn));
        $rPres=mysql_fetch_assoc($qPres);   
  
              //'tanggal,kodekegiatan,a.kodeorg,hasilkerja,jumlahhk,upahkerja,upahpremi,umr';
             $tab.="<tr class=rowcontent>";
             
             $tab.="<td>".tanggalnormal($rPres['tanggal'])."</td>";
             $tab.="<td>".$rPres['kodeorg']."</td>";
             $tab.="<td>".$optKegiatan[$rPres['kodekegiatan']]."</td>";
			 $tab.="<td>".number_format($rPres['jjg'])."</td>";
             $tab.="<td>".$rPres['hasilkerja']."</td>";
             $tab.="<td>".$optSatKegiatan[$rPres['kodekegiatan']]."</td>";
             $tab.="<td align=right>".number_format($rPres['upahpremi'],0)."</td>";
             $tab.="<td align=right>".number_format($rPres['umr'],0)."</td>";
             $tab.="</tr>";
         $tab.="</table>";
         $tab.="<br />".$titleDetail[1]."<br />";
      
        $sKhdrn="select distinct * from ".$dbname.".kebun_kehadiran where notransaksi='".$param['notransaksi']."'";
        $qKhdrn=mysql_query($sKhdrn) or die(mysql_error($conn));  
            $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>";
            $tab.="<tr class=rowheader>";
            $tab.="<td>".$_SESSION['lang']['nik']."</td>";
            $tab.="<td>".$_SESSION['lang']['absensi']."</td>";
			$tab.="<td>".$_SESSION['lang']['jjg']."</td>";
			$tab.="<td>".$_SESSION['lang']['hasilkerjad']."</td>";
            $tab.="<td>".$_SESSION['lang']['jhk']."</td>";
            $tab.="<td>".$_SESSION['lang']['umr']."</td>";
            $tab.="<td>".$_SESSION['lang']['insentif']."</td>";
            $tab.="</tr></thead><tbody>";
           
            while($rKhdrn=mysql_fetch_assoc($qKhdrn))
            {
 
             $tab.="<tr class=rowcontent>";
             $tab.="<td>".$optNamaKary[$rKhdrn['nik']]."</td>";
             $tab.="<td>".$rKhdrn['absensi']."</td>";
			 $tab.="<td>".number_format($rKhdrn['jjg'])."</td>";
			 $tab.="<td>".number_format($rKhdrn['hasilkerja'],2)."</td>";
             $tab.="<td>".$rKhdrn['jhk']."</td>";
             $tab.="<td  align=right>".number_format($rKhdrn['umr'],0)."</td>";
             $tab.="<td  align=right>".number_format($rKhdrn['insentif'],0)."</td>";
             $tab.="</tr>";
             $totJhk+=$rKhdrn['jhk'];
             $totUmr+=$rKhdrn['umr'];
             $totInsentif+=$rKhdrn['insentif'];
			 $totJjg+=$rKhdrn['jjg'];
			 $totHasilKerja+=$rKhdrn['hasilkerja'];
            }
             $tab.="<thead><tr class=rowcontent>";
             $tab.="<td colspan=2 align=center>".$_SESSION['lang']['total']."</td>";
			 $tab.="<td  align=right>".number_format($totJjg)."</td>";
			 $tab.="<td  align=right>".number_format($totHasilKerja,2)."</td>";
             $tab.="<td  align=right>".number_format($totJhk,2)."</td>";
             $tab.="<td  align=right>".number_format($totUmr,0)."</td>";
             $tab.="<td  align=right>".number_format($totInsentif,0)."</td>";
             $tab.="</tr>";
         $tab.="</table><br />";
        $sMat="select distinct * from ".$dbname.".kebun_pakaimaterial where notransaksi='".$param['notransaksi']."'";
        $qMat=mysql_query($sMat) or die(mysql_error($conn));
        $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>";
        $tab.="<tr class=rowheader>";
        $tab.="<td>".$_SESSION['lang']['kodeorg']."</td>";
        $tab.="<td>".$_SESSION['lang']['kodebarang']."</td>";
        $tab.="<td>".$_SESSION['lang']['kwantitas']."</td>";
        $tab.="<td>".$_SESSION['lang']['kwantitasha']."</td>";
        $tab.="<td>".$_SESSION['lang']['hargasatuan']."</td>";
        $tab.="<td>".$_SESSION['lang']['sloc']."</td>";
        $tab.="</tr></thead><tbody>";
        while($rMat=mysql_fetch_assoc($qMat))
        {
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$rMat['kodeorg']."</td>";
            $tab.="<td>".$optNamaBrg[$rMat['kodebarang']]."</td>";
            $tab.="<td>".$rMat['kwantitas']."</td>";
            $tab.="<td>".$rMat['kwantitasha']."</td>";
            $tab.="<td>".$rMat['hargasatuan']."</td>";
            $tab.="<td>".$optGudang[$rMat['kodegudang']]."</td>";
            $tab.="</tr>";
        }
        $tab.="</table><br />";
         //$col3 = 'kodeorg,kodebarang,kwantitas,kwantitasha,hargasatuan';
//        foreach($data as $ac =>$dtData)
//        {
//            
//            $tab.=$titleDetail[$ac]."<br />";
//            $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead><tr class=rowheader>";
//            if($ac==1)
//            {
//                $tab.="<td>No.</td>";
//            }
//            foreach($cols[$ac] as $column) {
//                if(substr($column,1,1)==".")
//                {
//                    $column=substr($column,2,7);
//                    //exit("error".$column);
//                }
//                $tab.="<td>".$_SESSION['lang'][$column]."</td>";
//            }
//            $tab.="</tr></thead><tbody class=rowcontent>";
//            foreach($dtData as $key=>$row) { 
//                if($key==1)
//                {
//                    $no++;
//                }
//                
//                $tab.="<tr>";
//                if($key==1)
//                { $tab.="<td>".$no."</td>";}
//                foreach($row as $cont) {
//                    if(strlen($cont)==10)
//                    {
//                        if(substr($cont,4,1)=='-')
//                        {
//                            $cont=tanggalnormal($cont);
//                        }
//                        else
//                        {
//                            $cont=$RnamaKary[$cont];
//                        }
//                    }
//                   $tab.="<td>".$cont."</td>";
//                }
//                $tab.="</tr>";
//            }
//             $tab.="</tbody></table> <br />";
//        }
        echo $tab;
        
    break;
    default:
    break;
}
?>