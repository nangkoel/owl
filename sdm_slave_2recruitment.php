<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');
if(isset($_POST)){
    $param=$_POST;
}
if((isset($_GET['proses'])=='cvData')||(isset($_GET['proses'])=='pdf')){
    $param=$_GET;
}
 
$brd=0;
$bgclr="";
if($_GET['proses']=='excel'){
    $brd=1;
    $bgclr=" bgcolor=#DEDEDE";
}
if($param['deptId']!=''){
   $whr.="and departemen='".$param['deptId']."' ";
} 
if($param['periode']!=''||$param['periodesmp']!=''){
    $whr.=" and left(tanggal,7) between '".$param['periode']."' and  '".$param['periodesmp']."'";
    $whr2=" and left(tanggal,7) between '".$param['periode']."' and  '".$param['periodesmp']."'";
}
$sdata="select * from ".$dbname.".sdm_permintaansdm where notransaksi!='' ".$whr." order by tanggal asc";
$qData=mysql_query($sdata) or die(mysql_error());
while($rdata=  mysql_fetch_assoc($qData)){
    $nmLowongan[$rdata['notransaksi']]=$rdata['namalowongan'];
    $dtNotrans[$rdata['notransaksi']]=$rdata['notransaksi'];
    $dtTglPermintaan[$rdata['notransaksi']]=$rdata['tanggal'];
    $dtTglDbthkan[$rdata['notransaksi']]=$rdata['tgldibutuhkan'];
}
 
$qdata=mysql_query($sdata,$conn) or die(mysql_error($conn));
$rowdata=mysql_num_rows($qdata);
$saks="select distinct * from ".$dbname.".setup_remotetimbangan 
where lokasi='HRDJKRT'";		

$qaks=mysql_query($saks) or die(mysql_error($conn));
$jaks=mysql_fetch_assoc($qaks);
$uname2=$jaks['username'];
$passwd2=$jaks['password'];
$dbserver2=$jaks['ip'];
$dbport2=$jaks['port'];
$dbdt=$jaks['dbname'];
$conn2=mysql_connect($dbserver2,$uname2,$passwd2);
if (!$conn2)
{
    die('Could not connect: ' . mysql_error());
}
 $sdt="select a.email,nopermintaan,(year(curdate())-year(tanggallahir)) as umur,namacalon from ".$dbdt.".datacalon a left join 
       ".$dbdt.".sdm_apply_dt b on a.email=b.email left join 
       ".$dbdt.".sdm_lowongan c on b.notransaksi=c.notransaksi
       where nopermintaan!='' ".$whr2."";
 //echo $sdt;
$qdt=mysql_query($sdt,$conn2) or die(mysql_error());
while($rdt=mysql_fetch_assoc($qdt)){
$dtUmur[$rdt['nopermintaan'].$rdt['email']]=$rdt['umur'];
$dtNama[$rdt['nopermintaan'].$rdt['email']]=$rdt['namacalon'];
}
$tab.="<table cellpadding=1 cellspacing=1 border=".$brd." class=sortable>";
$tab.="<thead><tr ".$bgclr.">";
$tab.="<td>No.</td>";
$tab.="<td>".$_SESSION['lang']['namalowongan']."</td>";
$tab.="<td>".$_SESSION['lang']['tanggal']."</td>";
$tab.="<td>".$_SESSION['lang']['tgldibutuhkan']."</td>";
$tab.="<td>".$_SESSION['lang']['nama']."</td>";
$tab.="<td>".$_SESSION['lang']['umur']."</td>";
$tab.="<td>".$_SESSION['lang']['keputusan']."</td>";
if($_GET['proses']!='excel'){
    $tab.="<td colspan=2>".$_SESSION['lang']['action']."</td>";
}
$tab.="</tr></thead><tbody>";
foreach($dtNotrans as $dtNtran =>$lstTrans){
    $afdC=false;$blankC=false;
    $sdata="select distinct email,idpermintaan,hasilakhir from ".$dbname.".sdm_testcalon a left join 
            ".$dbname.".sdm_permintaansdm b on a.idpermintaan=b.notransaksi 
            where  notransaksi='".$lstTrans."' order by tanggal asc";
    $qdata=mysql_query($sdata,$conn) or die(mysql_error($conn));
    while($rdata=  mysql_fetch_assoc($qdata)){
        $nopermntaan[]=$rdata['idpermintaan'];
        $emaildt[$rdata['idpermintaan'].$rdata['email']]=$rdata['email'];
        $jmlhRow[$rdata['idpermintaan']]+=1;
        $lstEmail[$rdata['email']]=$rdata['email'];
        $hslAkhr[$rdata['email']]=$rdata['hasilakhir'];
        $sdata2="select distinct email,idpermintaan,hasilakhir from ".$dbname.".sdm_testcalon a left join 
            ".$dbname.".sdm_permintaansdm b on a.idpermintaan=b.notransaksi 
            where  notransaksi='".$lstTrans."' order by tanggal asc";
        $qdata2=mysql_query($sdata2,$conn) or die(mysql_error($conn));
        $tmpRow=mysql_num_rows($qdata2);
        
        $tab.="<tr class='rowcontent'>";
        if($afdC==false) {
                $no+=1;
                $tab .= "<td>".$no."</td>";
                $tab.="<td>".$nmLowongan[$lstTrans]."</td>";
                $tab.="<td>".$dtTglPermintaan[$lstTrans]."</td>";
                $tab.="<td>".$dtTglDbthkan[$lstTrans]."</td>";
                $afdC = true;
        } else {
                if($blankC==false) {
                        $tmpRow=$tmpRow-1;
                        $tab .= "<td  rowspan='".$tmpRow."'>&nbsp;</td>";
                        $tab.="<td  rowspan='".$tmpRow."'>&nbsp;</td>";
                        $tab.="<td rowspan='".$tmpRow."'>&nbsp;</td>";
                        $tab.="<td rowspan='".$tmpRow."'>&nbsp;</td>";
                        $blankC = true;
                }
        }	
        $tab.="<td>".$dtNama[$lstTrans.$rdata['email']]."</td>";
        $tab.="<td align=right>".$dtUmur[$lstTrans.$rdata['email']]."</td>";
        $tab.="<td>".$rdata['hasilakhir']."</td>";
        if($_GET['proses']!='excel'){
            $tab.="<td><img src=images/pdf.jpg class=resicon  title='PDF CV ' onclick=\"masterPDF('datacalon','".$rdata['email']."','','sdm_slave_pemanggilantest',event)\"></td>";
            $tab.="<td><img src=images/pdf.jpg class=resicon  title='PDF CV ' onclick=\"masterPDF2('sdm_permintaansdm','".$lstTrans."','','sdm_slave_daftartenagakerja',event)\"></td>";
        }
        $tab.="</tr>";
    }
}
$tab.="</tbody></table>";
switch($_GET['proses'])
{
        case'preview':
            echo $tab;
        break;
        case'pdf':
         
        class PDF extends FPDF
        {
            function Header() {
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
                global $param;
               
                               
                            # Alamat & No Telp
                $query = selectQuery($dbname,'organisasi','alamat,telepon',
                    "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                $orgData = mysql_query($query,$conn) or die(mysql_error($conn));
                $rOrgData=mysql_fetch_assoc($orgData);

                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 15;
                if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
                $this->Image($path,$this->lMargin,$this->tMargin,70);	
                $this->SetFont('Arial','B',9);
                $this->SetFillColor(255,255,255);	
                $this->SetX(100);   
                $this->Cell($width-100,$height,$_SESSION['org']['namaorganisasi'],0,1,'L');	 
                $this->SetX(100); 		
                $this->Cell($width-100,$height,$rOrgData['alamat'],0,1,'L');	
                $this->SetX(100); 			
                $this->Cell($width-100,$height,"Tel: ".$rOrgData['telepon'],0,1,'L');	
                $this->Line($this->lMargin,$this->tMargin+($height*4),
                $this->lMargin+$width,$this->tMargin+($height*4));
                $this->Ln();

                $this->SetFont('Arial','B',10);
                                
                                $this->Cell($width,$height,strtoupper("Rekapitulasi Rekruitment"),'',0,'C');
                                $this->Ln();
                                $dert=$param['periode']." s.d. ".$param['periodesmp'];
                                if($param['periodesmp']==''){
                                    $dert=$_SESSION['lang']['all'];
                                }else if($param['periode']==''){
                                    $dert=$_SESSION['lang']['all'];
                                }
                                    
                                $this->Cell($width,$height,strtoupper($_SESSION['lang']['periode'])." : ".$dert,'',1,'C');
                                $this->Ln(18);
                $this->SetFont('Arial','B',7);
                $this->SetFillColor(220,220,220);
                                $this->Cell(5/100*$width,$height,'No',1,0,'C',1);
                                $this->Cell(25/100*$width,$height,$_SESSION['lang']['namalowongan'],1,0,'C',1);		
                                $this->Cell(8/100*$width,$height,$_SESSION['lang']['tanggal'],1,0,'C',1);	
                                $this->Cell(12/100*$width,$height,$_SESSION['lang']['tgldibutuhkan'],1,0,'C',1);	
                                $this->Cell(25/100*$width,$height,$_SESSION['lang']['nama'],1,0,'C',1);	
                                $this->Cell(8/100*$width,$height,$_SESSION['lang']['umur'],1,0,'C',1);	
                                $this->Cell(15/100*$width,$height,$_SESSION['lang']['keputusan'],1,1,'C',1);
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
        $height = 12;
                $pdf->AddPage();
                $pdf->SetFillColor(255,255,255);
                $no=0;
            foreach($dtNotrans as $dtNtran =>$lstTrans){
            $afdC=false;$blankC=false;
            $sdata="select distinct email,idpermintaan,hasilakhir from ".$dbname.".sdm_testcalon a left join 
            ".$dbname.".sdm_permintaansdm b on a.idpermintaan=b.notransaksi 
            where  notransaksi='".$lstTrans."' order by tanggal asc";
            $qdata=mysql_query($sdata,$conn) or die(mysql_error($conn));
            while($rdata=  mysql_fetch_assoc($qdata)){
                    $sdata2="select distinct email,idpermintaan,hasilakhir from ".$dbname.".sdm_testcalon a left join 
                    ".$dbname.".sdm_permintaansdm b on a.idpermintaan=b.notransaksi 
                    where  notransaksi='".$lstTrans."' order by tanggal asc";
                    $qdata2=mysql_query($sdata2,$conn) or die(mysql_error($conn));
                    $tmpRow=mysql_num_rows($qdata2);

                    if($afdC==false) {
                        $no+=1;
                        $pdf->Cell(5/100*$width,$height,$no,1,0,'C',1);
                        $pdf->Cell(25/100*$width,$height,$nmLowongan[$lstTrans],1,0,'L',1);		
                        $pdf->Cell(8/100*$width,$height,$dtTglPermintaan[$lstTrans],1,0,'C',1);	
                        $pdf->Cell(12/100*$width,$height,$dtTglDbthkan[$lstTrans],1,0,'C',1);	
                        $afdC = true;
                    } else {
                      
                                //$tmpRow=($tmpRow)*$height;
                                $pdf->Cell(50/100*$width,$height,'','RL',0,'C',1);
                        
                    }	
                    $pdf->GetX($derttt);
                    $pdf->Cell(25/100*$width,$height,$dtNama[$lstTrans.$rdata['email']],1,0,'L',1);	
                    $pdf->Cell(8/100*$width,$height,$dtUmur[$lstTrans.$rdata['email']],1,0,'R',1);	
                    $pdf->Cell(15/100*$width,$height,$rdata['hasilakhir'],1,1,'C',1);   
                    if($blankC==true){
                     $derttt=$pdf->GetX();   
                    }
                }
            }

        $pdf->Output();
        break;

        case'excel':
        
            $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
            
            $nop_="rekapRekruitment";
            if(strlen($tab)>0)
            {
            if ($handle = opendir('tempExcel')) {
            while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
            @unlink('tempExcel/'.$file);
            }
            }	
            closedir($handle);
            }
            $handle=fopen("tempExcel/".$nop_.".xls",'w');
            if(!fwrite($handle,$tab))
            {
            echo "<script language=javascript1.2>
            parent.window.alert('Can't convert to excel format');
            </script>";
            exit;
            }
            else
            {
            echo "<script language=javascript1.2>
            window.location='tempExcel/".$nop_.".xls';
            </script>";
            }
            closedir($handle);
            }
        break;
       
}

?>