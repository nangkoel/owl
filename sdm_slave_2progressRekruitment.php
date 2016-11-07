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
if($_GET['proses']=='pdf'){
    $param=$_GET;
}
$optNmkar=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optKdJbtn=makeOption($dbname, 'datakaryawan', 'karyawanid,kodejabatan');
$optNmJabatan=makeOption($dbname, 'sdm_5jabatan', 'kodejabatan,namajabatan');
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
    $whr.="and periodetest between '".$param['periode']."' and  '".$param['periodesmp']."'";
    $whr2=" and left(tanggal,7) between '".$param['periode']."' and  '".$param['periodesmp']."'";
}else{
    
    exit("error:\n Periode tidak boleh kosong");
}
$sdata="select a.* from ".$dbname.".sdm_testcalon a left join 
        ".$dbname.".sdm_permintaansdm b on a.idpermintaan=b.notransaksi
        where idpermintaan!='' ".$whr." order by tanggal asc";
//exit("error:".$sdata);
$qData=mysql_query($sdata) or die(mysql_error());
while($rdata=  mysql_fetch_assoc($qData)){
    $nmLowongan[$rdata['email']]=$rdata['namalowongan'];
    $dtTglIntr[$rdata['email']]=$rdata['tglivew'];
    $dtEmail[$rdata['email']]=$rdata['email'];
}

#interviewer
$sint="select distinct a.* from ".$dbname.".sdm_interview  a left join
        ".$dbname.".sdm_testcalon b on a.email=b.email where 
        periodetest between '".$param['periode']."' and  '".$param['periodesmp']."'";
 //exit("error:".$sint);
$qint=mysql_query($sint) or die(mysql_error());
while($rint=  mysql_fetch_assoc($qint)){
    $dintr[$rint['email'].$rint['interviewer']]=$rint['interviewer'];
    $dtinterviwer[$rint['interviewer']]=$rint['interviewer'];
    $dthasil[$rint['email'].$rint['interviewer']]=$rint['hasil'];
    if($rint['stat']==1){
        $dtcatatan[$rint['email']]=$rint['catatan'];
    }
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
 $sdt="select a.email,nopermintaan,(year(curdate())-year(tanggallahir)) as umur,tanggallahir,namacalon,jeniskelamin
       from ".$dbdt.".datacalon a left join 
       ".$dbdt.".sdm_apply_dt b on a.email=b.email left join 
       ".$dbdt.".sdm_lowongan c on b.notransaksi=c.notransaksi
       where nopermintaan!='' and a.status=0";
 //exit("error:".$sdt);
 //echo $sdt;
$qdt=mysql_query($sdt,$conn2) or die(mysql_error());
while($rdt=mysql_fetch_assoc($qdt)){
$dtUmur[$rdt['email']]=$rdt['umur'];
$dtNama[$rdt['email']]=$rdt['namacalon'];
$dtTglLahir[$rdt['email']]=$rdt['tanggallahir'];
$dtJnsKelamin[$rdt['email']]=$rdt['jeniskelamin'];
}
$tab.="<table cellpadding=1 cellspacing=1 border=".$brd." class=sortable>";
$tab.="<thead><tr ".$bgclr.">";
$tab.="<td rowspan=2>No.</td>";
$tab.="<td rowspan=2>".$_SESSION['lang']['tanggal']." Interview</td>";
$tab.="<td rowspan=2>".$_SESSION['lang']['nama']." Kandidat</td>";
$tab.="<td rowspan=2>".$_SESSION['lang']['tanggallahir']."</td>";
$tab.="<td rowspan=2>".$_SESSION['lang']['umur']."</td>";
$tab.="<td rowspan=2>".$_SESSION['lang']['jeniskelamin']."</td>";
$tab.="<td rowspan=2>".$_SESSION['lang']['namalowongan']."</td>";
$colspandt=count($dtinterviwer);
$tab.="<td colspan=".$colspandt."  align=center>Rekomendasi Interviewer</td>
      <td rowspan=2 align=center>".$_SESSION['lang']['catatan']."</td>
      </tr><tr>";
foreach($dtinterviwer as $lstInterViewer){
    $tab.="<td>".$optNmkar[$lstInterViewer]." <br>[".$optNmJabatan[$optKdJbtn[$lstInterViewer]]."]</td>";
}
$tab.="</tr></thead><tbody>";
foreach($dtEmail as $lstEmail){
    $no+=1;
    $tab.="<tr class=rowcontent>";
    $tab.="<td>".$no."</td>";
    $tab.="<td>".tanggalnormal($dtTglIntr[$lstEmail])."</td>";
    $tab.="<td>".$dtNama[$lstEmail]."</td>";
    $tab.="<td>".tanggalnormal($dtTglLahir[$lstEmail])."</td>";
    $tab.="<td align=right>".$dtUmur[$lstEmail]."</td>";
    $tab.="<td>".$dtJnsKelamin[$lstEmail]."</td>";
    $tab.="<td>".$nmLowongan[$lstEmail]."</td>";
    foreach($dtinterviwer as $lstInterViewer){
        $tab.="<td>".$dthasil[$lstEmail.$lstInterViewer]."</td>";
    }
    $tab.="<td>".$dtcatatan[$lstEmail]."</td>";
    $tab.="</tr>";
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
                global $dtinterviwer;
                $jmlrow=count($dtinterviwer);
                               
                            # Alamat & No Telp
                $query = selectQuery($dbname,'organisasi','alamat,telepon',
                    "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                $orgData = mysql_query($query,$conn) or die(mysql_error($conn));
                $rOrgData=mysql_fetch_assoc($orgData);

                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 13;
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

                $this->SetFont('Arial','B',8);
                                
                                $this->Cell($width,$height,strtoupper("Laporan Progress Rekruitment"),'',0,'C');
                                $this->Ln();
                                $dert=$param['periode']." s.d. ".$param['periodesmp'];
                                if($param['periodesmp']==''){
                                    $dert=$_SESSION['lang']['all'];
                                }else if($param['periode']==''){
                                    $dert=$_SESSION['lang']['all'];
                                }
                                    
                                $this->Cell($width,$height,strtoupper($_SESSION['lang']['periode'])." : ".$dert,'',1,'C');
                                $this->Ln(18);
                $this->SetFont('Arial','B',6);
                $this->SetFillColor(220,220,220);
                                $this->Cell(3/100*$width,$height,'No','TRL',0,'C',1);
                                $this->Cell(8/100*$width,$height,$_SESSION['lang']['tanggal'],'TRL',0,'C',1);		
                                $this->Cell(15/100*$width,$height,$_SESSION['lang']['nama'],'TRL',0,'C',1);	
                                $this->Cell(9/100*$width,$height,$_SESSION['lang']['tanggallahir'],'TRL',0,'C',1);	
                                $this->Cell(5/100*$width,$height,$_SESSION['lang']['umur'],'TRL',0,'C',1);	
                                $this->Cell(7/100*$width,$height,$_SESSION['lang']['jeniskelamin'],'TRL',0,'C',1);	
                                $this->Cell(13/100*$width,$height,$_SESSION['lang']['nama'],'TRL',0,'C',1);
                                foreach($dtinterviwer as $lstInterViewer){
                                    $ert+=1;
                                    $det=0;
                                    if($jmlrow==$ert){
                                        $det=1;
                                    }
                                    $this->Cell(10/100*$width,$height,$optNmkar[$lstInterViewer],'TRL',$det,'C',1);
                                    //$tab.="<td>".$optNmkar[$lstInterViewer]." <br>[".$optNmJabatan[$optKdJbtn[$lstInterViewer]]."]</td>";
                                }
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
                $no=0;
            

        $pdf->Output();
        break;

        case'excel':
        
            $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
            
            $nop_="progressRekruitment";
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