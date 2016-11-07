<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/fpdf.php');
include_once('lib/zLib.php');
require_once('lib/nangkoelib.php');

$tahun=$_GET['tahun'];
$pabrik=$_GET['pabrik'];
        
	
//check, one-two
if($tahun==''){
    echo "WARNING: silakan mengisi tahun."; exit;
}
if($pabrik==''){
    echo "WARNING: silakan mengisi pabrik."; exit;
}

//echo $tahun.$kebun;

// ambil data
    $isidata=array();
$str="select * from ".$dbname.".bgt_produksi_pks_vw where tahunbudget = '".$tahun."' and millcode = '".$pabrik."' order by kodeunit";
//            echo $str;
$res=mysql_query($str);
while($bar= mysql_fetch_object($res))
{
//    $qwe=$bar->nojurnal.$bar->noakun.$bar->nourut;
    $isidata[$bar->kodeunit][tbstotal]=$bar->kgolah;
    $isidata[$bar->kodeunit][tbs01]=$bar->olah01;
    $isidata[$bar->kodeunit][tbs02]=$bar->olah02;
    $isidata[$bar->kodeunit][tbs03]=$bar->olah03;
    $isidata[$bar->kodeunit][tbs04]=$bar->olah04;
    $isidata[$bar->kodeunit][tbs05]=$bar->olah05;
    $isidata[$bar->kodeunit][tbs06]=$bar->olah06;
    $isidata[$bar->kodeunit][tbs07]=$bar->olah07;
    $isidata[$bar->kodeunit][tbs08]=$bar->olah08;
    $isidata[$bar->kodeunit][tbs09]=$bar->olah09;
    $isidata[$bar->kodeunit][tbs10]=$bar->olah10;
    $isidata[$bar->kodeunit][tbs11]=$bar->olah11;
    $isidata[$bar->kodeunit][tbs12]=$bar->olah12;
    $isidata[$bar->kodeunit][cpototal]=$bar->kgcpo;
    $isidata[$bar->kodeunit][cpo01]=$bar->kgcpo01;
    $isidata[$bar->kodeunit][cpo02]=$bar->kgcpo02;
    $isidata[$bar->kodeunit][cpo03]=$bar->kgcpo03;
    $isidata[$bar->kodeunit][cpo04]=$bar->kgcpo04;
    $isidata[$bar->kodeunit][cpo05]=$bar->kgcpo05;
    $isidata[$bar->kodeunit][cpo06]=$bar->kgcpo06;
    $isidata[$bar->kodeunit][cpo07]=$bar->kgcpo07;
    $isidata[$bar->kodeunit][cpo08]=$bar->kgcpo08;
    $isidata[$bar->kodeunit][cpo09]=$bar->kgcpo09;
    $isidata[$bar->kodeunit][cpo10]=$bar->kgcpo10;
    $isidata[$bar->kodeunit][cpo11]=$bar->kgcpo11;
    $isidata[$bar->kodeunit][cpo12]=$bar->kgcpo12;
    $isidata[$bar->kodeunit][kertotal]=$bar->kgkernel;
    $isidata[$bar->kodeunit][ker01]=$bar->kgker01;
    $isidata[$bar->kodeunit][ker02]=$bar->kgker02;
    $isidata[$bar->kodeunit][ker03]=$bar->kgker03;
    $isidata[$bar->kodeunit][ker04]=$bar->kgker04;
    $isidata[$bar->kodeunit][ker05]=$bar->kgker05;
    $isidata[$bar->kodeunit][ker06]=$bar->kgker06;
    $isidata[$bar->kodeunit][ker07]=$bar->kgker07;
    $isidata[$bar->kodeunit][ker08]=$bar->kgker08;
    $isidata[$bar->kodeunit][ker09]=$bar->kgker09;
    $isidata[$bar->kodeunit][ker10]=$bar->kgker10;
    $isidata[$bar->kodeunit][ker11]=$bar->kgker11;
    $isidata[$bar->kodeunit][ker12]=$bar->kgker12;
    

}
//        echo "<pre>";
////        print_r($rowdata);
//        print_r($isidata);
//        echo "</pre>";

//$jumlahafdeling=0;
//if(!empty($headerdata))foreach($headerdata as $baris1)
//{
//    $jumlahafdeling+=1;
//} 
//$jumlahrow=0;
//if(!empty($rowdata))foreach($rowdata as $baris2)
//{
//    $jumlahrow+=1;
//}else{
//    echo"Data tidak tersedia."; exit;
//} 
                $lebarno=3;
                $lebarasal=7;
                $lebaruraian=5;
                $lebarsatuan=5;
                $lebarbulan=6;
                $lebartotal=7;

        class PDF extends FPDF
        {
            function Header() {
                global $tahun;
                global $pabrik;
                global $dbname;
                global $isidata;
                global $lebarno;
                global $lebarasal;
                global $lebaruraian;
                global $lebarsatuan;
                global $lebarbulan;
                global $lebartotal;
                
                # Bulan
               // $optBulan = 
                
                # Alamat & No Telp
                $query = selectQuery($dbname,'organisasi','alamat,telepon',
                    "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                $orgData = fetchData($query);
                
                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 12;
                if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
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
                $this->Ln();
                $this->SetFont('Arial','',8);
              	$this->Cell((10/100*$width)-5,$height,$_SESSION['lang']['budgetyear'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
               	$this->Cell(70/100*$width,$height,$tahun,'',0,'L');		
              	$this->Cell((7/100*$width)-5,$height,'Printed By','',0,'L');
                $this->Cell(5,$height,':','',0,'L');
               	$this->Cell(15/100*$width,$height,$_SESSION['empl']['name'],'',1,'L');		
              	$this->Cell((10/100*$width)-5,$height,$_SESSION['lang']['kodeorg'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
               	$this->Cell(70/100*$width,$height,$pabrik,'',0,'L');		
              	$this->Cell((7/100*$width)-5,$height,$_SESSION['lang']['tanggal'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
               	$this->Cell(15/100*$width,$height,date('d-m-Y H:i:s'),'',1,'L');		
		$title=$_SESSION['lang']['distribusiproduksi'];		
                $this->Ln();
                $this->SetFont('Arial','U',12);
                $this->Cell($width,$height,$title,0,1,'C');	
                $this->Ln();	
                $this->SetFont('Arial','',10);
                $this->SetFillColor(220,220,220);
                $this->Cell($lebarno/100*$width,$height,'No',1,0,'C',1);
                $this->Cell($lebarasal/100*$width,$height,$_SESSION['lang']['asaltbs'],1,0,'C',1);
                $this->Cell($lebaruraian/100*$width,$height,$_SESSION['lang']['uraian'],1,0,'C',1);
                $this->Cell($lebarsatuan/100*$width,$height,$_SESSION['lang']['satuan'],1,0,'C',1);
                $this->Cell($lebarbulan/100*$width,$height,'Jan',1,0,'C',1);
                $this->Cell($lebarbulan/100*$width,$height,'Feb',1,0,'C',1);
                $this->Cell($lebarbulan/100*$width,$height,'Mar',1,0,'C',1);
                $this->Cell($lebarbulan/100*$width,$height,'Apr',1,0,'C',1);
                $this->Cell($lebarbulan/100*$width,$height,'May',1,0,'C',1);
                $this->Cell($lebarbulan/100*$width,$height,'Jun',1,0,'C',1);
                $this->Cell($lebarbulan/100*$width,$height,'Jul',1,0,'C',1);
                $this->Cell($lebarbulan/100*$width,$height,'Aug',1,0,'C',1);
                $this->Cell($lebarbulan/100*$width,$height,'Sep',1,0,'C',1);
                $this->Cell($lebarbulan/100*$width,$height,'Oct',1,0,'C',1);
                $this->Cell($lebarbulan/100*$width,$height,'Nov',1,0,'C',1);
                $this->Cell($lebarbulan/100*$width,$height,'Dec',1,0,'C',1);
                $this->Cell($lebartotal/100*$width,$height,$_SESSION['lang']['total'],1,1,'C',1);
//                $this->Cell(15/100*$width,$height,'',LRB,0,'C',1); // uraian
//                $this->Cell(10/100*$width,$height,'',LRB,0,'C',1); // tahuntanam
//                if(!empty($headerdata))foreach($headerdata as $baris)
//                {
//                    $this->Cell(10/100*$width,$height,$baris,1,0,'C',1); // afdeling
//                } 
//                $this->Cell(10/100*$width,$height,'',LRB,1,'C',1); // total
                
          
            }
                
            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
        }
        $pdf=new PDF('L','pt','A4');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
		$pdf->AddPage();
		
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',9);

//        $str="select distinct kodeorg from ".$dbname.".pabrik_timbangan
//                  where intex = 2 order by kodeorg"; // 2 : afiliasi
        $str="select distinct kodeorganisasi from ".$dbname.".organisasi where induk<>'".$_SESSION['org']['kodeorganisasi']."'";        
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
                $afiliasi[$bar->kodeorganisasi]=$bar->kodeorganisasi;
        }
//        $str="select distinct kodeorg from ".$dbname.".pabrik_timbangan
//                  where intex = 1 order by kodeorg"; // 1 : internal
        $str="select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$_SESSION['org']['kodeorganisasi']."'";        
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
                $internal[$bar->kodeorganisasi]=$bar->kodeorganisasi;
        }
//        $optNm=makeOption($dbname, 'log_5supplier', 'kodetimbangan,supplierid');
//        $str="select distinct kodecustomer from ".$dbname.".pabrik_timbangan
//                  where intex = 0 order by kodecustomer"; // 0 : eksternal
        $str="select distinct supplierid from ".$dbname.".log_5supplier
                  order by supplierid"; // 0 : eksternal
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
//                $eksternal[$optNm[$bar->kodecustomer]]=$optNm[$bar->kodecustomer];
                $eksternal[$bar->supplierid]=$bar->supplierid;
        }
//        echo "<pre>";
////        print_r($rowdata);
//        print_r($eksternal);
//        echo "</pre>";

        $no=1;
//body2
if(!empty($internal))foreach($internal as $int) // kodeorg / row
{
    $olahdata[internal][tbstotal]+=$isidata[$int][tbstotal];  
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="tbs0".$i; else $ii="tbs".$i;
        $olahdata[internal][$ii]+=$isidata[$int][$ii];    
    }    
    $olahdata[internal][cpototal]+=$isidata[$int][cpototal];    
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="cpo0".$i; else $ii="cpo".$i;
        $olahdata[internal][$ii]+=$isidata[$int][$ii];    
    }    
    $olahdata[internal][kertotal]+=$isidata[$int][kertotal];    
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="ker0".$i; else $ii="ker".$i;
        $olahdata[internal][$ii]+=$isidata[$int][$ii];    
    }    
    $olahdata[internal][paltotal]+=$isidata[$int][cpototal]+$isidata[$int][kertotal];    
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1){
            $ii="pal0".$i; $jj="cpo0".$i; $kk="ker0".$i; 
        }else{
            $ii="pal".$i; $jj="cpo".$i; $kk="ker".$i;
        }
        $olahdata[internal][$ii]+=$isidata[$int][$jj]+$isidata[$int][$kk];    
    }    
}
if(!empty($afiliasi))foreach($afiliasi as $afi) // kodeorg / row
{
    $olahdata[afiliasi][tbstotal]+=$isidata[$afi][tbstotal];    
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="tbs0".$i; else $ii="tbs".$i;
        $olahdata[afiliasi][$ii]+=$isidata[$afi][$ii];    
    }    
    $olahdata[afiliasi][cpototal]+=$isidata[$afi][cpototal];    
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="cpo0".$i; else $ii="cpo".$i;
        $olahdata[afiliasi][$ii]+=$isidata[$afi][$ii];    
    }    
    $olahdata[afiliasi][kertotal]+=$isidata[$afi][kertotal];    
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="ker0".$i; else $ii="ker".$i;
        $olahdata[afiliasi][$ii]+=$isidata[$afi][$ii];    
    }    
    $olahdata[afiliasi][paltotal]+=$isidata[$afi][cpototal]+$isidata[$afi][kertotal];    
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1){
            $ii="pal0".$i; $jj="cpo0".$i; $kk="ker0".$i; 
        }else{
            $ii="pal".$i; $jj="cpo".$i; $kk="ker".$i;
        }
        $olahdata[afiliasi][$ii]+=$isidata[$afi][$jj]+$isidata[$afi][$kk];    
    }    
} 
if(!empty($eksternal))foreach($eksternal as $eks) // kodeorg / row
{
    $olahdata[eksternal][tbstotal]+=$isidata[$eks][tbstotal];    
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="tbs0".$i; else $ii="tbs".$i;
        $olahdata[eksternal][$ii]+=$isidata[$eks][$ii];    
    }    
    $olahdata[eksternal][cpototal]+=$isidata[$eks][cpototal];    
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="cpo0".$i; else $ii="cpo".$i;
        $olahdata[eksternal][$ii]+=$isidata[$eks][$ii];    
    }    
    $olahdata[eksternal][kertotal]+=$isidata[$eks][kertotal];    
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="ker0".$i; else $ii="ker".$i;
        $olahdata[eksternal][$ii]+=$isidata[$eks][$ii];    
    }    
    $olahdata[eksternal][paltotal]+=$isidata[$eks][cpototal]+$isidata[$eks][kertotal];    
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1){
            $ii="pal0".$i; $jj="cpo0".$i; $kk="ker0".$i; 
        }else{
            $ii="pal".$i; $jj="cpo".$i; $kk="ker".$i;
        }
        $olahdata[eksternal][$ii]+=$isidata[$eks][$jj]+$isidata[$eks][$kk];    
    }    
}

$olahdata[all][tbstotal]=$olahdata[internal][tbstotal]+$olahdata[afiliasi][tbstotal]+$olahdata[eksternal][tbstotal];
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="tbs0".$i; else $ii="tbs".$i;
        $olahdata[all][$ii]+=$olahdata[internal][$ii]+$olahdata[afiliasi][$ii]+$olahdata[eksternal][$ii];    
    }    
$olahdata[all][cpototal]=$olahdata[internal][cpototal]+$olahdata[afiliasi][cpototal]+$olahdata[eksternal][cpototal];
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="cpo0".$i; else $ii="cpo".$i;
        $olahdata[all][$ii]+=$olahdata[internal][$ii]+$olahdata[afiliasi][$ii]+$olahdata[eksternal][$ii];    
    }    
$olahdata[all][kertotal]=$olahdata[internal][kertotal]+$olahdata[afiliasi][kertotal]+$olahdata[eksternal][kertotal];
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="ker0".$i; else $ii="ker".$i;
        $olahdata[all][$ii]+=$olahdata[internal][$ii]+$olahdata[afiliasi][$ii]+$olahdata[eksternal][$ii];    
    }    
$olahdata[all][paltotal]=$olahdata[internal][paltotal]+$olahdata[afiliasi][paltotal]+$olahdata[eksternal][paltotal];
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="pal0".$i; else $ii="pal".$i;
        $olahdata[all][$ii]+=$olahdata[internal][$ii]+$olahdata[afiliasi][$ii]+$olahdata[eksternal][$ii];    
    }    

if(!empty($olahdata)){
    $pdf->Cell($lebarno/100*$width,$height,'1',RLT,0,'C',1);
    $pdf->Cell($lebarasal/100*$width,$height,'Internal',RLT,0,'C',1);
    $pdf->Cell($lebaruraian/100*$width,$height,'TBS',1,0,'C',1);
    $pdf->Cell($lebarsatuan/100*$width,$height,'Ton',1,0,'C',1);
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="tbs0".$i; else $ii="tbs".$i;
        @$toNolahdata[internal][$ii]=$olahdata[internal][$ii]/1000;
        $pdf->Cell($lebarbulan/100*$width,$height,number_format($toNolahdata[internal][$ii],2),1,0,'R',1);
        $jmlhSma[internal][tbstotal]+=$olahdata[internal][$ii];
    }   
    @$toNjmlhSma[internal][tbstotal]=$jmlhSma[internal][tbstotal]/1000;
    $pdf->Cell($lebartotal/100*$width,$height,number_format($toNjmlhSma[internal][tbstotal],2),1,1,'R',1);

    $pdf->Cell($lebarno/100*$width,$height,'',RL,0,'C',1);
    $pdf->Cell($lebarasal/100*$width,$height,'',RL,0,'C',1);
    $pdf->Cell($lebaruraian/100*$width,$height,'CPO',1,0,'C',1);
    $pdf->Cell($lebarsatuan/100*$width,$height,'Ton',1,0,'C',1);
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="cpo0".$i; else $ii="cpo".$i;
        @$toNolahdata[internal][$ii]=$olahdata[internal][$ii]/1000;
        $pdf->Cell($lebarbulan/100*$width,$height,number_format($toNolahdata[internal][$ii],2),1,0,'R',1);
        $jmlhSma[internal][cpototal]+=$olahdata[internal][$ii];
    }    
    @$toNjmlhSma[internal][cpototal]=$jmlhSma[internal][cpototal]/1000;
    $pdf->Cell($lebartotal/100*$width,$height,number_format($toNjmlhSma[internal][cpototal],2),1,1,'R',1);
    
    $pdf->Cell($lebarno/100*$width,$height,'',RL,0,'C',1);
    $pdf->Cell($lebarasal/100*$width,$height,'',RL,0,'C',1);
    $pdf->Cell($lebaruraian/100*$width,$height,'Kernel',1,0,'C',1);
    $pdf->Cell($lebarsatuan/100*$width,$height,'Ton',1,0,'C',1);
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="ker0".$i; else $ii="ker".$i;
        @$toNolahdata[internal][$ii]=$olahdata[internal][$ii]/1000;
        $pdf->Cell($lebarbulan/100*$width,$height,number_format($toNolahdata[internal][$ii],2),1,0,'R',1);
        $jmlhSma[internal][kertotal]+=$olahdata[internal][$ii];
    }    
    @$toNjmlhSma[internal][kertotal]=$jmlhSma[internal][kertotal]/1000;
    $pdf->Cell($lebartotal/100*$width,$height,number_format($toNjmlhSma[internal][kertotal],2),1,1,'R',1);

    $pdf->Cell($lebarno/100*$width,$height,'',RLB,0,'C',1);
    $pdf->Cell($lebarasal/100*$width,$height,'',RLB,0,'C',1);
    $pdf->Cell($lebaruraian/100*$width,$height,'Palm',1,0,'C',1);
    $pdf->Cell($lebarsatuan/100*$width,$height,'Ton',1,0,'C',1);
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="pal0".$i; else $ii="pal".$i;
         @$toNolahdata[internal][$ii]=$olahdata[internal][$ii]/1000;
        $pdf->Cell($lebarbulan/100*$width,$height,number_format($toNolahdata[internal][$ii],2),1,0,'R',1);
        $jmlhSma[internal][paltotal]+=$olahdata[internal][$ii];
    }    
     @$toNjmlhSma[internal][paltotal]=$olahdata[internal][$ii]= $jmlhSma[internal][paltotal]/1000;
    $pdf->Cell($lebartotal/100*$width,$height,number_format($toNjmlhSma[internal][paltotal],2),1,1,'R',1);

    $pdf->Cell($lebarno/100*$width,$height,'2',RLT,0,'C',1);
    $pdf->Cell($lebarasal/100*$width,$height,'Afiliasi',RLT,0,'C',1);
    $pdf->Cell($lebaruraian/100*$width,$height,'TBS',1,0,'C',1);
    $pdf->Cell($lebarsatuan/100*$width,$height,'Ton',1,0,'C',1);
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="tbs0".$i; else $ii="tbs".$i;
         @$toNolahdata[afiliasi][$ii]=$olahdata[afiliasi][$ii]/1000;
        $pdf->Cell($lebarbulan/100*$width,$height,number_format($toNolahdata[afiliasi][$ii],2),1,0,'R',1);
        $jmlhSma[afiliasi][tbstotal]+=$olahdata[afiliasi][$ii];
    }    
    @$toNjmlhSma[afiliasi][tbstotal]=$jmlhSma[afiliasi][tbstotal]/1000;
    $pdf->Cell($lebartotal/100*$width,$height,number_format($toNjmlhSma[afiliasi][tbstotal],2),1,1,'R',1);

    $pdf->Cell($lebarno/100*$width,$height,'',RL,0,'C',1);
    $pdf->Cell($lebarasal/100*$width,$height,'',RL,0,'C',1);
    $pdf->Cell($lebaruraian/100*$width,$height,'CPO',1,0,'C',1);
    $pdf->Cell($lebarsatuan/100*$width,$height,'Ton',1,0,'C',1);
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="cpo0".$i; else $ii="cpo".$i;
        @$toNolahdata[afiliasi][$ii]=$olahdata[afiliasi][$ii]/1000;
        $pdf->Cell($lebarbulan/100*$width,$height,number_format($toNolahdata[afiliasi][$ii],2),1,0,'R',1);
         $jmlhSma[afiliasi][cpototal]+=$olahdata[afiliasi][$ii];
    }    
    @$toNjmlhSma[afiliasi][cpototal]=$jmlhSma[afiliasi][cpototal]/1000;
    $pdf->Cell($lebartotal/100*$width,$height,number_format($toNjmlhSma[afiliasi][cpototal],2),1,1,'R',1);
    
    $pdf->Cell($lebarno/100*$width,$height,'',RL,0,'C',1);
    $pdf->Cell($lebarasal/100*$width,$height,'',RL,0,'C',1);
    $pdf->Cell($lebaruraian/100*$width,$height,'Kernel',1,0,'C',1);
    $pdf->Cell($lebarsatuan/100*$width,$height,'Ton',1,0,'C',1);
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="ker0".$i; else $ii="ker".$i;
        @$toNolahdata[afiliasi][$ii]=$olahdata[afiliasi][$ii]/1000;
        $pdf->Cell($lebarbulan/100*$width,$height,number_format($toNolahdata[afiliasi][$ii],2),1,0,'R',1);
        $jmlhSma[afiliasi][kertotal]+=$olahdata[afiliasi][$ii];
    }    
    @$toNjmlhSma[afiliasi][kertotal]=$jmlhSma[afiliasi][kertotal]/1000;
    $pdf->Cell($lebartotal/100*$width,$height,number_format($toNjmlhSma[afiliasi][kertotal],2),1,1,'R',1);

    $pdf->Cell($lebarno/100*$width,$height,'',RLB,0,'C',1);
    $pdf->Cell($lebarasal/100*$width,$height,'',RLB,0,'C',1);
    $pdf->Cell($lebaruraian/100*$width,$height,'Palm',1,0,'C',1);
    $pdf->Cell($lebarsatuan/100*$width,$height,'Ton',1,0,'C',1);
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="pal0".$i; else $ii="pal".$i;
        @$toNolahdata[afiliasi][$ii]=$olahdata[afiliasi][$ii]/1000;
        $pdf->Cell($lebarbulan/100*$width,$height,number_format($toNolahdata[afiliasi][$ii],2),1,0,'R',1);
        $jmlhSma[afiliasi][paltotal]+=$olahdata[afiliasi][$ii];
    }    
    @$toNjmlhSma[afiliasi][paltotal]=$jmlhSma[afiliasi][paltotal]/1000;
    $pdf->Cell($lebartotal/100*$width,$height,number_format($toNjmlhSma[afiliasi][paltotal],2),1,1,'R',1);
    
    $pdf->Cell($lebarno/100*$width,$height,'3',RLT,0,'C',1);
    $pdf->Cell($lebarasal/100*$width,$height,'Eksternal',RLT,0,'C',1);
    $pdf->Cell($lebaruraian/100*$width,$height,'TBS',1,0,'C',1);
    $pdf->Cell($lebarsatuan/100*$width,$height,'Ton',1,0,'C',1);
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="tbs0".$i; else $ii="tbs".$i;
        @$toNolahdata[eksternal][$ii]=$olahdata[eksternal][$ii]/1000;
        $pdf->Cell($lebarbulan/100*$width,$height,number_format($toNolahdata[eksternal][$ii],2),1,0,'R',1);
        $jmlhSma[eksternal][tbstotal]+=$olahdata[eksternal][$ii];
    }    
    @$toNjmlhSma[eksternal][tbstotal]=$jmlhSma[eksternal][tbstotal]/1000;
    $pdf->Cell($lebartotal/100*$width,$height,number_format($toNjmlhSma[eksternal][tbstotal],2),1,1,'R',1);

    $pdf->Cell($lebarno/100*$width,$height,'',RL,0,'C',1);
    $pdf->Cell($lebarasal/100*$width,$height,'',RL,0,'C',1);
    $pdf->Cell($lebaruraian/100*$width,$height,'CPO',1,0,'C',1);
    $pdf->Cell($lebarsatuan/100*$width,$height,'Ton',1,0,'C',1);
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="cpo0".$i; else $ii="cpo".$i;
        @$toNolahdata[eksternal][$ii]=$olahdata[eksternal][$ii]/1000;
        $pdf->Cell($lebarbulan/100*$width,$height,number_format($toNolahdata[eksternal][$ii],2),1,0,'R',1);
        $jmlhSma[eksternal][cpototal]+=$olahdata[eksternal][$ii];
    }    
     @$toNjmlhSma[eksternal][cpototal]= $jmlhSma[eksternal][cpototal]/1000;
    $pdf->Cell($lebartotal/100*$width,$height,number_format($toNjmlhSma[eksternal][cpototal],2),1,1,'R',1);
    
    $pdf->Cell($lebarno/100*$width,$height,'',RL,0,'C',1);
    $pdf->Cell($lebarasal/100*$width,$height,'',RL,0,'C',1);
    $pdf->Cell($lebaruraian/100*$width,$height,'Kernel',1,0,'C',1);
    $pdf->Cell($lebarsatuan/100*$width,$height,'Ton',1,0,'C',1);
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="ker0".$i; else $ii="ker".$i;
        @$toNolahdata[eksternal][$ii]=$olahdata[eksternal][$ii]/1000;
        $pdf->Cell($lebarbulan/100*$width,$height,number_format($toNolahdata[eksternal][$ii],2),1,0,'R',1);
         $jmlhSma[eksternal][kertotal]+=$olahdata[eksternal][$ii];
    }    
    @$toNjmlhSma[eksternal][kertotal]=$jmlhSma[eksternal][kertotal]/1000;
    $pdf->Cell($lebartotal/100*$width,$height,number_format($toNjmlhSma[eksternal][kertotal],2),1,1,'R',1);

    $pdf->Cell($lebarno/100*$width,$height,'',RLB,0,'C',1);
    $pdf->Cell($lebarasal/100*$width,$height,'',RLB,0,'C',1);
    $pdf->Cell($lebaruraian/100*$width,$height,'Palm',1,0,'C',1);
    $pdf->Cell($lebarsatuan/100*$width,$height,'Ton',1,0,'C',1);
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="pal0".$i; else $ii="pal".$i;
        @$toNolahdata[eksternal][$ii]=$olahdata[eksternal][$ii]/1000;
        $pdf->Cell($lebarbulan/100*$width,$height,number_format($toNolahdata[eksternal][$ii],2),1,0,'R',1);
        $jmlhSma[eksternal][paltotal]+=$olahdata[eksternal][$ii];
    }    
    @$toNjmlhSma[eksternal][paltotal]=$jmlhSma[eksternal][paltotal]/1000;
    $pdf->Cell($lebartotal/100*$width,$height,number_format($toNjmlhSma[eksternal][paltotal],2),1,1,'R',1);    
    
    $pdf->Cell($lebarno/100*$width,$height,'',RLT,0,'C',1);
    $pdf->Cell($lebarasal/100*$width,$height,'Grand Total',RLT,0,'C',1);
    $pdf->Cell($lebaruraian/100*$width,$height,'TBS',1,0,'C',1);
    $pdf->Cell($lebarsatuan/100*$width,$height,'Ton',1,0,'C',1);
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="tbs0".$i; else $ii="tbs".$i;
        @$toNolahdata[all][$ii]=$olahdata[all][$ii]/1000;
        $pdf->Cell($lebarbulan/100*$width,$height,number_format($toNolahdata[all][$ii],2),1,0,'R',1);
        $jmlhSma[all][tbstotal]+=$olahdata[all][$ii];
    }    
     @$toNjmlhSma[all][tbstotal]=$jmlhSma[all][tbstotal]/1000;
    $pdf->Cell($lebartotal/100*$width,$height,number_format($toNjmlhSma[all][tbstotal],2),1,1,'R',1);

    $pdf->Cell($lebarno/100*$width,$height,'',RL,0,'C',1);
    $pdf->Cell($lebarasal/100*$width,$height,'',RL,0,'C',1);
    $pdf->Cell($lebaruraian/100*$width,$height,'CPO',1,0,'C',1);
    $pdf->Cell($lebarsatuan/100*$width,$height,'Ton',1,0,'C',1);
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="cpo0".$i; else $ii="cpo".$i;
         @$toNolahdata[all][$ii]=$olahdata[all][$ii]/1000;
        $pdf->Cell($lebarbulan/100*$width,$height,number_format($toNolahdata[all][$ii],2),1,0,'R',1);
        $jmlhSma[all][cpototal]+=$olahdata[all][$ii];
    }    
    @$toNjmlhSma[all][cpototal]=$jmlhSma[all][cpototal]/1000;
    $pdf->Cell($lebartotal/100*$width,$height,number_format($toNjmlhSma[all][cpototal],2),1,1,'R',1);
    
    $pdf->Cell($lebarno/100*$width,$height,'',RL,0,'C',1);
    $pdf->Cell($lebarasal/100*$width,$height,'',RL,0,'C',1);
    $pdf->Cell($lebaruraian/100*$width,$height,'Kernel',1,0,'C',1);
    $pdf->Cell($lebarsatuan/100*$width,$height,'Ton',1,0,'C',1);
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="ker0".$i; else $ii="ker".$i;
         @$toNolahdata[all][$ii]=$olahdata[all][$ii]/1000;
        $pdf->Cell($lebarbulan/100*$width,$height,number_format($toNolahdata[all][$ii],2),1,0,'R',1);
        $jmlhSma[all][kertotal]+=$olahdata[all][$ii];
    }    
    @$toNjmlhSma[all][kertotal]=$jmlhSma[all][kertotal]/1000;
    $pdf->Cell($lebartotal/100*$width,$height,number_format($toNjmlhSma[all][kertotal],2),1,1,'R',1);

    $pdf->Cell($lebarno/100*$width,$height,'',RLB,0,'C',1);
    $pdf->Cell($lebarasal/100*$width,$height,'',RLB,0,'C',1);
    $pdf->Cell($lebaruraian/100*$width,$height,'Palm',1,0,'C',1);
    $pdf->Cell($lebarsatuan/100*$width,$height,'Ton',1,0,'C',1);
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="pal0".$i; else $ii="pal".$i;
          @$toNolahdata[all][$ii]=$olahdata[all][$ii]/1000;
        $pdf->Cell($lebarbulan/100*$width,$height,number_format($toNolahdata[all][$ii],2),1,0,'R',1);
        $jmlhSma[all][paltotal]+=$olahdata[all][$ii];
    }    
    @$toNjmlhSma[all][paltotal]=$jmlhSma[all][paltotal]/1000;
    $pdf->Cell($lebartotal/100*$width,$height,number_format($toNjmlhSma[all][paltotal],2),1,1,'R',1); 

    
    $stream.="<tr class=rowcontent>";
    $stream.="<td rowspan=4 valign=middle align=right>&nbsp;</td>";
    $stream.="<td rowspan=4 valign=middle align=left>Grand Total</td>";
    $stream.="<td align=left>TBS</td>";
    $stream.="<td align=left>Ton</td>";
    $stream.="<td align=right>".number_format($olahdata[all][tbstotal],2)."</td>";
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="tbs0".$i; else $ii="tbs".$i;
        $stream.="<td align=right>".number_format($olahdata[all][$ii],2)."</td>";
    }    
    $stream.="<td align=right>".number_format($olahdata[all][tbstotal],2)."</td>";
    $stream.="</tr>";
    $stream.="<tr class=rowcontent>";
//    $stream.="<td align=right>1</td>";
//    $stream.="<td align=right>all</td>";
    $stream.="<td align=left>CPO</td>";
    $stream.="<td align=left>Ton</td>";
    $stream.="<td align=right>".number_format($olahdata[all][cpototal],2)."</td>";
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="cpo0".$i; else $ii="cpo".$i;
        $stream.="<td align=right>".number_format($olahdata[all][$ii],2)."</td>";
    }    
    $stream.="<td align=right>".number_format($olahdata[all][cpototal],2)."</td>";
    $stream.="</tr>";
    $stream.="<tr class=rowcontent>";
//    $stream.="<td align=right>1</td>";
//    $stream.="<td align=right>all</td>";
    $stream.="<td align=left>Kernel</td>";
    $stream.="<td align=left>Ton</td>";
    $stream.="<td align=right>".number_format($olahdata[all][kertotal],2)."</td>";
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="ker0".$i; else $ii="ker".$i;
        $stream.="<td align=right>".number_format($olahdata[all][$ii],2)."</td>";
    }    
    $stream.="<td align=right>".number_format($olahdata[all][kertotal],2)."</td>";
    $stream.="</tr>";
    $stream.="<tr class=rowcontent>";
//    $stream.="<td align=right>1</td>";
//    $stream.="<td align=right>all</td>";
    $stream.="<td align=left>Palm</td>";
    $stream.="<td align=left>Ton</td>";
    $stream.="<td align=right>".number_format($olahdata[all][paltotal],2)."</td>";
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="pal0".$i; else $ii="pal".$i;
        $stream.="<td align=right>".number_format($olahdata[all][$ii],2)."</td>";
    }    
    $stream.="<td align=right>".number_format($olahdata[all][paltotal],2)."</td>";
    $stream.="</tr>";       
}
                
                
////body1
//$countdown=$jumlahrow;
//if(!empty($rowdata))foreach($rowdata as $tt) // tahun tanam / row
//{
//    if($tt!=0)
//    {
//    if($countdown==$jumlahrow)$pdf->Cell(15/100*$width,$height,'A. Luas Areal (ha)',LRT,0,'L',1); // uraian
//        else $pdf->Cell(15/100*$width,$height,'',LR,0,'C',1);                               // uraian
//    $pdf->Cell(10/100*$width,$height,$tt,1,0,'C',1);                                        // tahun tanam
//    if(!empty($headerdata))foreach($headerdata as $af) // afdeling / column
//    {
//        $pdf->Cell(10/100*$width,$height,number_format($isidata[$tt][$af],2),1,0,'R',1);    // data tiap afdeling
//    }
//    $pdf->Cell(10/100*$width,$height,number_format($totalrowdata[$tt][total],2),1,1,'R',1); // total
//    $countdown-=1;
//    }
//} 
//    $pdf->Cell(15/100*$width,$height,'',LRB,0,'C',1);                                       // uraian
//    $pdf->SetFillColor(220,220,220);
//    $pdf->Cell(10/100*$width,$height,'Total Areal',1,0,'C',1);                              // tahun tanam
//    if(!empty($headerdata))foreach($headerdata as $af)
//    {
//        $pdf->Cell(10/100*$width,$height,number_format($totalcolumndata[$af][total],2),1,0,'R',1); // data tiap afdeling
//    } 
//    $pdf->Cell(10/100*$width,$height,number_format($total,2),1,1,'R',1); // total
//    $pdf->SetFillColor(255,255,255);	
//
////body2
//$countdown=$jumlahrow;
//if(!empty($rowdata))foreach($rowdata as $tt) // tahun tanam / row
//{
//    if($tt!=0)
//    {
//    if($countdown==$jumlahrow)$pdf->Cell(15/100*$width,$height,'B. Populasi (pkk)',LRT,0,'L',1); // uraian
//        else $pdf->Cell(15/100*$width,$height,'',LR,0,'C',1);                               // uraian
//    $pdf->Cell(10/100*$width,$height,$tt,1,0,'C',1);                                        // tahun tanam
//    if(!empty($headerdata))foreach($headerdata as $af) // afdeling / column
//    {
//        $pdf->Cell(10/100*$width,$height,number_format($isidata2[$tt][$af],2),1,0,'R',1);    // data tiap afdeling
//    }
//    $pdf->Cell(10/100*$width,$height,number_format($totalrowdata2[$tt][total],2),1,1,'R',1); // total
//    $countdown-=1;
//    }
//} 
//    $pdf->Cell(15/100*$width,$height,'',LRB,0,'C',1);                                       // uraian
//    $pdf->SetFillColor(220,220,220);
//    $pdf->Cell(10/100*$width,$height,'Total Areal',1,0,'C',1);                              // tahun tanam
//    if(!empty($headerdata))foreach($headerdata as $af)
//    {
//        $pdf->Cell(10/100*$width,$height,number_format($totalcolumndata2[$af][total],2),1,0,'R',1); // data tiap afdeling
//    } 
//    $pdf->Cell(10/100*$width,$height,number_format($total2,2),1,1,'R',1); // total
//    $pdf->SetFillColor(255,255,255);	
    
    $pdf->Output();



?>