<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

if(isset($_POST['proses']))
{
	$proses=$_POST['proses'];
}
else
{
	$proses=$_GET['proses'];
}

//$arr2="##kdPabrik__2##tgl__2##kdUnit__2##kdAfdeling__2";
$kdPabrik=$_POST['kdPabrik__2'];
$tgl=$_POST['tgl__2'];
$kdUnit=$_POST['kdUnit__2'];
$kdAfdeling=$_POST['kdAfdeling__2'];

$tanggal=substr($tgl,6,4)."-".substr($tgl,3,2)."-".substr($tgl,0,2);

switch($proses)
{
    case'preview':
    if($tgl=='')
    {
        echo"Warning: Date required";
        exit();
    }

    echo $_SESSION['lang']['rPenerimaanTbs']."/".$_SESSION['lang']['afdeling']."/".$_SESSION['lang']['tanggal'];
    
    echo"<table cellspacing=1 border=0 class=sortable>
    <thead class=rowheader>
    <tr>
        <td>No.</td>
        <td>".$_SESSION['lang']['noTiket']."</td>
        <td>".$_SESSION['lang']['nospb']."</td>
        <td>".$_SESSION['lang']['afdeling']."</td>
        <td>".$_SESSION['lang']['nopol']."</td>
        <td>".$_SESSION['lang']['sopir']."</td>
        <td>".$_SESSION['lang']['jammasuk']."</td>
        <td>".$_SESSION['lang']['jamkeluar']."</td>
        <td>".$_SESSION['lang']['jumlah']." ".$_SESSION['lang']['jjg']."</td>
        <td>".$_SESSION['lang']['berat']." Dikirim</td>
        <td>".$_SESSION['lang']['beratMasuk']."</td>
        <td>".$_SESSION['lang']['beratKeluar']."</td>
        <td>".$_SESSION['lang']['beratBersih']."</td>
        <td>".$_SESSION['lang']['potongankg']."</td>
        <td>".$_SESSION['lang']['beratnormal']."</td>
    </tr>
    </thead>
    <tbody>";
    
    $where="and millcode like '%".$kdPabrik."%' and kodeorg != '' and kodeorg like '%".$kdUnit."%' and tanggal like '".$tanggal."%' and nospb like '%".$kdAfdeling."%'";
    //notransaksi, tanggal, kodeorg, kodecustomer, bjr, jumlahtandan1, kodebarang, jammasuk, beratmasuk, jamkeluar, beratkeluar, nokendaraan, supir, nospb, petugassortasi, timbangonoff, statussortasi, nokontrak, nodo, intex, nosipb, thntm1, thntm2, thntm3, jumlahtandan2, jumlahtandan3, brondolan, username, millcode, beratbersih
    $sData="select millcode, notransaksi, nospb, nokendaraan, supir, jammasuk, jamkeluar, sum(jumlahtandan1+jumlahtandan2+jumlahtandan3) as jumlahtandan, kgpembeli, beratmasuk, beratkeluar, beratbersih,kgpotsortasi
           from ".$dbname.".pabrik_timbangan where kodebarang='40000003' ".$where." group by notransaksi order by notransaksi ";
    //echo $sData;
    //echo "warning".$sData;exit();
    $qData=mysql_query($sData) or die(mysql_error());

    $brs=mysql_num_rows($qData);
    if($brs>0)
    {
        while($rData=mysql_fetch_assoc($qData))
        {	
            $no+=1;
             $brtNormal=$rData['beratbersih']-$rData['kgpotsortasi'];
             $bgwarna="";
             if($rData['nospb']!=''){
                $scek="select distinct * from ".$dbname.".kebun_spbdt where nospb='".$rData['nospb']."' and substr(nospb,9,6)<>left(blok,6)";
                $qcek=mysql_query($scek) or die(mysql_error($conn));
                $rcek=mysql_num_rows($qcek);
                if($rcek==1){
                    $bgwarna="bgcolor=yellow title='Ada Buah Dari Afdeling lain'";
                }
             }
            echo"<tr class=rowcontent>
                <td>".$no."</td>
                <td>".$rData['notransaksi']."</td>
                <td ".$bgwarna.">".$rData['nospb']."</td>
                <td>".substr($rData['nospb'],8,6)."</td>
                <td>".$rData['nokendaraan']."</td>
                <td>".$rData['supir']."</td>
                <td>".$rData['jammasuk']."</td>
                <td>".$rData['jamkeluar']."</td>
                <td align=right>".number_format($rData['jumlahtandan'])."</td>
                <td align=right>".number_format($rData['kgpembeli'])."</td>
                <td align=right>".number_format($rData['beratmasuk'])."</td>
                <td align=right>".number_format($rData['beratkeluar'])."</td>
                <td align=right>".number_format($rData['beratbersih'])."</td>
                <td  align=right>".number_format($rData['kgpotsortasi'])."</td>
                <td  align=right>".number_format($brtNormal,0)."</td>
            </tr>";
            $totaljanjang+=$rData['jumlahtandan'];
            $totalberat+=$rData['kgpembeli'];
            $totalmasuk+=$rData['beratmasuk'];
            $totalkeluar+=$rData['beratkeluar'];
            $totalbersih+=$rData['beratbersih'];
            $subBrtNor+=$brtNormal;
            $subBrtPot+=$rData['kgpotsortasi'];
            $brtNormal=0;
        }
        echo"<tr class=rowcontent >
        <td colspan=8 align=right>Total (KG)</td>
        <td align=right>".number_format($totaljanjang)."</td>
        <td align=right>".number_format($totalberat)."</td>
        <td align=right>".number_format($totalmasuk)."</td>
        <td align=right>".number_format($totalkeluar)."</td>
        <td align=right>".number_format($totalbersih)."</td>
        <td align=right>".number_format($subBrtPot)."</td>
        <td align=right>".number_format($subBrtNor)."</td>

        </tr>";
    }
    else
    {
            echo"<tr class=rowcontent><td colspan=12 align=center>Data empty</td></tr>";
    }
    break;
    case'pdf':
    $kdPabrik=$_GET['kdPabrik__2'];
    $tgl=$_GET['tgl__2'];
    $kdUnit=$_GET['kdUnit__2'];
    $kdAfdeling=$_GET['kdAfdeling__2'];
    
    $tanggal=substr($tgl,6,4)."-".substr($tgl,3,2)."-".substr($tgl,0,2);

    class PDF extends FPDF
    {
        function Header() {
            global $conn;
            global $dbname;
            global $align;
            global $length;
            global $colArr;
            global $title;
        global $kdPabrik;
        global $tgl;
        global $kdUnit;
        global $kdAfdeling;

            $sAlmat="select namaorganisasi,alamat,telepon from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'";
            $qAlamat=mysql_query($sAlmat) or die(mysql_error());
            $rAlamat=mysql_fetch_assoc($qAlamat);

            $width = $this->w - $this->lMargin - $this->rMargin;
            $height = 11;
            if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
            $this->Image($path,$this->lMargin,$this->tMargin,70);	
            $this->SetFont('Arial','B',9);
            $this->SetFillColor(255,255,255);	
            $this->SetX(100);   
            $this->Cell($width-100,$height,$rAlamat['namaorganisasi'],0,1,'L');	 
            $this->SetX(100); 		
            $this->Cell($width-100,$height,$rAlamat['alamat'],0,1,'L');	
            $this->SetX(100); 			
            $this->Cell($width-100,$height,"Tel: ".$rAlamat['telepon'],0,1,'L');	
            $this->Line($this->lMargin,$this->tMargin+($height*4),
            $this->lMargin+$width,$this->tMargin+($height*4));
            $this->Ln();	
            $this->Ln();
            $this->SetFont('Arial','B',11);
            $this->Cell($width,$height,$_SESSION['lang']['rPenerimaanTbs']." / ".$_SESSION['lang']['afdeling']." / ".$_SESSION['lang']['tanggal'],0,1,'C');	
            $this->Cell($width,$height,$kdPabrik." ".$tgl,0,1,'C');	
            $this->SetFont('Arial','B',7);
            if($kdUnit=='')$kdUnitz=$_SESSION['lang']['all']; else $kdUnitz=$kdUnit;
            if($kdAfdeling=='')$kdAfdelingz=$_SESSION['lang']['all']; else $kdAfdelingz=$kdAfdeling;
              	$this->Cell(50,$height,$_SESSION['lang']['unit'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
               	$this->Cell(350,$height,$kdUnitz,'',0,'L');		
              	$this->Cell(50,$height,'Printed By','',0,'L');
                $this->Cell(5,$height,':','',0,'L');
               	$this->Cell(350,$height,$_SESSION['empl']['name'],'',1,'L');		
              	$this->Cell(50,$height,$_SESSION['lang']['afdeling'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
               	$this->Cell(350,$height,$kdAfdelingz,'',0,'L');		
              	$this->Cell(50,$height,$_SESSION['lang']['tanggal'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
               	$this->Cell(350,$height,date('d-m-Y H:i:s'),'',1,'L');		

            $this->SetFont('Arial','B',5);	
            $this->SetFillColor(220,220,220);

            $this->Cell(3/100*$width,$height,'No.',1,0,'C',1);
            $this->Cell(6/100*$width,$height,$_SESSION['lang']['noTiket'],1,0,'C',1);		
            $this->Cell(13/100*$width,$height,$_SESSION['lang']['nospb'],1,0,'C',1);		
            $this->Cell(8/100*$width,$height,$_SESSION['lang']['nopol'],1,0,'C',1);	
            $this->Cell(10/100*$width,$height,$_SESSION['lang']['sopir'],1,0,'C',1);	
            $this->Cell(7/100*$width,$height,$_SESSION['lang']['jammasuk'],1,0,'C',1);	
            $this->Cell(7/100*$width,$height,$_SESSION['lang']['jamkeluar'],1,0,'C',1);			
            $this->Cell(7/100*$width,$height,$_SESSION['lang']['jumlah']." ".$_SESSION['lang']['jjg'],1,0,'C',1);
            $this->Cell(7/100*$width,$height,$_SESSION['lang']['berat']." Dikirim",1,0,'C',1);
            $this->Cell(7/100*$width,$height,$_SESSION['lang']['beratMasuk'],1,0,'C',1);
            $this->Cell(7/100*$width,$height,$_SESSION['lang']['beratKeluar'],1,0,'C',1);
            $this->Cell(7/100*$width,$height,$_SESSION['lang']['beratBersih'],1,0,'C',1);	            
            $this->Cell(7/100*$width,$height,$_SESSION['lang']['potongankg'],1,0,'C',1);
            $this->Cell(7/100*$width,$height,$_SESSION['lang']['beratnormal'],1,1,'C',1);
        }

        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial','I',8);
            $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
        }
    }
    $pdf=new PDF('P','pt','A4');
    $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
    $height = 9;
            $pdf->AddPage();
            $pdf->SetFillColor(255,255,255);
            $pdf->SetFont('Arial','',5);
    if($tgl=='')
    {
        echo"Warning: Date required";
        exit();
    }

    $where="and millcode like '%".$kdPabrik."%' and kodeorg != '' and kodeorg like '%".$kdUnit."%' and tanggal like '".$tanggal."%' and nospb like '%".$kdAfdeling."%'";
    //notransaksi, tanggal, kodeorg, kodecustomer, bjr, jumlahtandan1, kodebarang, jammasuk, beratmasuk, jamkeluar, beratkeluar, nokendaraan, supir, nospb, petugassortasi, timbangonoff, statussortasi, nokontrak, nodo, intex, nosipb, thntm1, thntm2, thntm3, jumlahtandan2, jumlahtandan3, brondolan, username, millcode, beratbersih
    $sData="select millcode, notransaksi, nospb, nokendaraan, supir, jammasuk, jamkeluar, sum(jumlahtandan1+jumlahtandan2+jumlahtandan3) as jumlahtandan, kgpembeli, beratmasuk, beratkeluar, beratbersih,kgpotsortasi 
           from ".$dbname.".pabrik_timbangan where kodebarang='40000003' ".$where." group by notransaksi order by notransaksi ";
    //echo $sData;
    //echo "warning".$sData;exit();
    $qData=mysql_query($sData) or die(mysql_error());

    $brs=mysql_num_rows($qData);
    if($brs>0)
    {
        while($rData=mysql_fetch_assoc($qData))
        {
            $no+=1;
            $brtNormal=$rData['beratbersih']-$rData['kgpotsortasi'];
            $pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);
            $pdf->Cell(6/100*$width,$height,$rData['notransaksi'],1,0,'L',1);		
            $pdf->Cell(13/100*$width,$height,$rData['nospb'],1,0,'L',1);		
            $pdf->Cell(8/100*$width,$height,$rData['nokendaraan'],1,0,'L',1);	
            $pdf->Cell(10/100*$width,$height,$rData['supir'],1,0,'L',1);	
            $pdf->Cell(7/100*$width,$height,$rData['jammasuk'],1,0,'C',1);	
            $pdf->Cell(7/100*$width,$height,$rData['jamkeluar'],1,0,'C',1);			
            $pdf->Cell(7/100*$width,$height,number_format($rData['jumlahtandan']),1,0,'R',1);
            $pdf->Cell(7/100*$width,$height,number_format($rData['kgpembeli']),1,0,'R',1);
            $pdf->Cell(7/100*$width,$height,number_format($rData['beratmasuk']),1,0,'R',1);
            $pdf->Cell(7/100*$width,$height,number_format($rData['beratkeluar']),1,0,'R',1);
            $pdf->Cell(7/100*$width,$height,number_format($rData['beratbersih']),1,0,'R',1);	            
            $pdf->Cell(7/100*$width,$height,number_format($rData['kgpotsortasi']),1,0,'R',1);	
            $pdf->Cell(7/100*$width,$height,number_format($brtNormal),1,1,'R',1);	
            
            $totaljanjang+=$rData['jumlahtandan'];
            $totalberat+=$rData['kgpembeli'];
            $totalmasuk+=$rData['beratmasuk'];
            $totalkeluar+=$rData['beratkeluar'];
            $totalbersih+=$rData['beratbersih'];
            $subbrtnor+=$brtNormal;
            $subbrtpot+=$rData['kgpotsortasi'];
            
            
        }
            $pdf->Cell(54/100*$width,$height,'Total (KG)',1,0,'C',1);			
            $pdf->Cell(7/100*$width,$height,number_format($totaljanjang),1,0,'R',1);
            $pdf->Cell(7/100*$width,$height,number_format($totalberat),1,0,'R',1);
            $pdf->Cell(7/100*$width,$height,number_format($totalmasuk),1,0,'R',1);
            $pdf->Cell(7/100*$width,$height,number_format($totalkeluar),1,0,'R',1);
            $pdf->Cell(7/100*$width,$height,number_format($totalbersih),1,0,'R',1);	            
            $pdf->Cell(7/100*$width,$height,number_format($subbrtpot),1,0,'R',1);	            
            $pdf->Cell(7/100*$width,$height,number_format($subbrtnor),1,1,'R',1);	            
    }
    else
    {
            echo"<tr class=rowcontent><td colspan=12 align=center>Data empty</td></tr>";
    }

    $pdf->Output();
    break;
    
    case'excel':
    $kdPabrik=$_GET['kdPabrik__2'];
    $tgl=$_GET['tgl__2'];
    $kdUnit=$_GET['kdUnit__2'];
    $kdAfdeling=$_GET['kdAfdeling__2'];
    
    $tanggal=substr($tgl,6,4)."-".substr($tgl,3,2)."-".substr($tgl,0,2);
    
    if($tgl=='')
    {
        echo"Warning: Date required";
        exit();
    }

    $tab=$_SESSION['lang']['rPenerimaanTbs']."/".$_SESSION['lang']['afdeling']."/".$_SESSION['lang']['tanggal']."<br>
        Tanggal: ".$tanggal;
    if($kdPabrik)$tab.="<br>Pabrik: ".$kdPabrik;
    if($kdUnit)$tab.="<br>Unit: ".$kdUnit;
    if($kdAfdeling)$tab.="<br>Unit: ".$kdAfdeling;
    $tab.="<table cellspacing=1 border=1 class=sortable>
    <thead class=rowheader>
    <tr>
        <td  bgcolor=#DEDEDE align=center>No.</td>
        <td  bgcolor=#DEDEDE align=center>".$_SESSION['lang']['noTiket']."</td>
        <td  bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nospb']."</td>
        <td  bgcolor=#DEDEDE align=center>".$_SESSION['lang']['afdeling']."</td>
        <td  bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nopol']."</td>
        <td  bgcolor=#DEDEDE align=center>".$_SESSION['lang']['sopir']."</td>
        <td  bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jammasuk']."</td>
        <td  bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jamkeluar']."</td>
        <td  bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jumlah']." ".$_SESSION['lang']['jjg']."</td>
        <td  bgcolor=#DEDEDE align=center>".$_SESSION['lang']['berat']." Dikirim</td>
        <td  bgcolor=#DEDEDE align=center>".$_SESSION['lang']['beratMasuk']."</td>
        <td  bgcolor=#DEDEDE align=center>".$_SESSION['lang']['beratKeluar']."</td>
        <td  bgcolor=#DEDEDE align=center>".$_SESSION['lang']['beratBersih']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['potongankg']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['beratnormal']."</td>
    </tr>
    </thead>
    <tbody>";
    
    $where="and millcode like '%".$kdPabrik."%' and kodeorg != '' and kodeorg like '%".$kdUnit."%' and tanggal like '".$tanggal."%' and nospb like '%".$kdAfdeling."%'";
    //notransaksi, tanggal, kodeorg, kodecustomer, bjr, jumlahtandan1, kodebarang, jammasuk, beratmasuk, jamkeluar, beratkeluar, nokendaraan, supir, nospb, petugassortasi, timbangonoff, statussortasi, nokontrak, nodo, intex, nosipb, thntm1, thntm2, thntm3, jumlahtandan2, jumlahtandan3, brondolan, username, millcode, beratbersih
    $sData="select millcode, notransaksi, nospb, nokendaraan, supir, jammasuk, jamkeluar, sum(jumlahtandan1+jumlahtandan2+jumlahtandan3) as jumlahtandan, kgpembeli, beratmasuk, beratkeluar, beratbersih from ".$dbname.".pabrik_timbangan where kodebarang='40000003' ".$where." group by notransaksi order by notransaksi ";
    //echo $sData;
    //echo "warning".$sData;exit();
    $qData=mysql_query($sData) or die(mysql_error());

    $brs=mysql_num_rows($qData);
    if($brs>0)
    {
        while($rData=mysql_fetch_assoc($qData))
        {	
            $no+=1;
            $brtNormal=$rData['beratbersih']-$rData['kgpotsortasi'];
            $bgwarna="";
             if($rData['nospb']!=''){
                $scek="select distinct * from ".$dbname.".kebun_spbdt where nospb='".$rData['nospb']."' and substr(nospb,9,6)<>left(blok,6)";
                $qcek=mysql_query($scek) or die(mysql_error($conn));
                $rcek=mysql_num_rows($qcek);
                if($rcek==1){
                    $bgwarna="bgcolor=yellow";
                }
             }
            $tab.="<tr class=rowcontent>
                <td>".$no."</td>
                <td>".$rData['notransaksi']."</td>
                <td ".$bgwarna.">".$rData['nospb']."</td>
                <td>".substr($rData['nospb'],8,6)."</td>
                <td>".$rData['nokendaraan']."</td>
                <td>".$rData['supir']."</td>
                <td>".$rData['jammasuk']."</td>
                <td>".$rData['jamkeluar']."</td>
                <td align=right>".number_format($rData['jumlahtandan'])."</td>
                <td align=right>".number_format($rData['kgpembeli'])."</td>
                <td align=right>".number_format($rData['beratmasuk'])."</td>
                <td align=right>".number_format($rData['beratkeluar'])."</td>
                <td align=right>".number_format($rData['beratbersih'])."</td>
                <td align=right>".number_format($rData['kgpotsortasi'])."</td>
                <td  align=right>".number_format($brtNormal,0)."</td>
            </tr>";
            $totaljanjang+=$rData['jumlahtandan'];
            $totalberat+=$rData['kgpembeli'];
            $totalmasuk+=$rData['beratmasuk'];
            $totalkeluar+=$rData['beratkeluar'];
            $totalbersih+=$rData['beratbersih'];
             $subBrtNor+=$brtNormal;
              $subBrtPot+=$rData['kgpotsortasi'];
            $brtNormal=0;
        }
        $tab.="<tr class=rowcontent >
        <td colspan=8 align=right>Total (KG)</td>
        <td align=right>".number_format($totaljanjang)."</td>
        <td align=right>".number_format($totalberat)."</td>
        <td align=right>".number_format($totalmasuk)."</td>
        <td align=right>".number_format($totalkeluar)."</td>
        <td align=right>".number_format($totalbersih)."</td>
            <td align=right>".number_format($subBrtPot)."</td>
        <td align=right>".number_format($subBrtNor)."</td>
        </tr>";
    }


                    $tab.="</tbody></table>Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
                    $tglSkrg=date("Ymd");
$qwe=date("YmdHms");
                    $nop_="LaporanPenerimaanTbs2".$tglSkrg."__".$qwe;
if(strlen($tab)>0)
{
    $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
    gzwrite($gztralala, $tab);
    gzclose($gztralala);
    echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls.gz';
        </script>";
}                          
//                    if(strlen($tab)>0)
//                    {
//                    if ($handle = opendir('tempExcel')) {
//                    while (false !== ($file = readdir($handle))) {
//                    if ($file != "." && $file != "..") {
//                    @unlink('tempExcel/'.$file);
//                    }
//                    }	
//                    closedir($handle);
//                    }
//                    $handle=fopen("tempExcel/".$nop_.".xls",'w');
//                    if(!fwrite($handle,$tab))
//                    {
//                    echo "<script language=javascript1.2>
//                    parent.window.alert('Can't convert to excel format');
//                    </script>";
//                    exit;
//                    }
//                    else
//                    {
//                    echo "<script language=javascript1.2>
//                    window.location='tempExcel/".$nop_.".xls';
//                    </script>";
//                    }
//                    closedir($handle);
//                    }
    break;
    default:
    break;
}
?>