<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

$proses=$_GET['proses'];
$lksiTgs=$_SESSION['empl']['lokasitugas'];
if(isset($_POST['periode'])){
		$kdeOrg=$_POST['kdeOrg'];
		$kdOrg=$_POST['kdOrg'];
		$tgl1=tanggalsystem($_POST['tgl1']);
		$tgl2=tanggalsystem($_POST['tgl2']);
		$tgl_1=tanggalsystem($_POST['tgl_1']);
		$tgl_2=tanggalsystem($_POST['tgl_2']);
		$periodeGaji=$_POST['periode'];
		$periode=explode('-',$_POST['periode']);
		$sistemGaji=$_POST['sistemGaji'];
		$idKry=$_POST['idKry'];
		$tp=$_POST['tp'];
}
else{

    $kdeOrg=$_GET['kdeOrg'];
    $kdOrg=$_GET['kdOrg'];
    $tgl1=tanggalsystem($_GET['tgl1']);
    $tgl2=tanggalsystem($_GET['tgl2']);
    $tgl_1=tanggalsystem($_GET['tgl_1']);
    $tgl_2=tanggalsystem($_GET['tgl_2']);
    $period=explode('-',$_GET['period']);
    $periode=explode('-',$_GET['periode']);
    $periodeGaji=$_GET['periode'];
    $idKry=$_GET['idKry'];
    $sistemGaji=$_GET['sistemGaji'];
    $tp=$_GET['tp'];
}

function dates_inbetween($date1, $date2){

    $day = 60*60*24;

    $date1 = strtotime($date1);
    $date2 = strtotime($date2);

    $days_diff = round(($date2 - $date1)/$day); // Unix time difference devided by 1 day to get total days in between

    $dates_array = array();

    $dates_array[] = date('Y-m-d',$date1);

    for($x = 1; $x < $days_diff; $x++){
        $dates_array[] = date('Y-m-d',($date1+($day*$x)));
    }

    $dates_array[] = date('Y-m-d',$date2);

    return $dates_array;
}
if(($proses=='excel')||($proses=='preview')){
     $brd=0;
    $bgr="";
    if($proses=='excel'){
        $brd=1;
        $bgr=" bgcolor=#DEDEDE align=center";
    }
     if(($tgl_1!='')&&($tgl_2!=''))
        {
                $tgl1=$tgl_1;
                $tgl2=$tgl_2;
        }
        $test = dates_inbetween($tgl1, $tgl2);
//        if(($tgl2=="")&&($tgl1==""))
//        {
//                echo"warning: Tanggal Mulai dan Tanggal Sampai tidak boleh kosong";
//                exit();
//        }

        $jmlHari=count($test);
        //cek max hari inputan
        if($jmlHari>41)
        {
                echo"warning: invalid period";
                exit();
        }
        if ($tp!=''){
            $whrtp=" and tipekaryawan=".$tp;
        }
        if($sistemGaji=='All')$wherez="";        
        if($sistemGaji=='Bulanan')$wherez=" and sistemgaji = 'Bulanan'";        
        if($sistemGaji=='Harian')$wherez=" and sistemgaji = 'Harian'";        
        if($kdOrg!='')
        {
                $kodeOrg=$kdOrg;
                        if(strlen($kdOrg)>4)
                        {
                                $where=" and subbagian='".$kdOrg."'";
                        }
                        else
                        {
                                $where=" and lokasitugas='".$kdOrg."' and (subbagian='0' or subbagian is null)";
                        }
                        $where2=" and kodeorg='".substr($kodeOrg,0,4)."'";
        if($_SESSION['empl']['tipelokasitugas']=='HOLDING')$where=" and lokasitugas='".$kdOrg."'";
        }

        elseif($kdOrg=='')
        {
			$where=" and lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
			$where2=" and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
        }


        $resData=array();
        $qwe=array();
        
        $sData="select distinct a.nik from ".$dbname.".sdm_potongandt a left join ".$dbname.".datakaryawan b on a.nik=b.karyawanid where periodegaji='".$periodeGaji."' ".$where2.$whrtp." ";
	$qData=mysql_query($sData);// or die(mysql_error());
        while($rData=mysql_fetch_assoc($qData)){
            $qwe[]=$rData['nik'];
        }
        $sData="select distinct jumlahpotongan,a.nik,tipepotongan as keterangan from ".$dbname.".sdm_potongandt a left join ".$dbname.".datakaryawan b on a.nik=b.karyawanid where periodegaji='".$periodeGaji."' ".$where2.$whrtp." ";
	$qData=mysql_query($sData);// or die(mysql_error());
        while($rData=mysql_fetch_assoc($qData)){
            $sCek="select distinct * from ".$dbname.".sdm_gaji where idkomponen='".$rData['keterangan']."' and karyawanid='".$rData['nik']."'";
            $qCek=  mysql_query($sCek) or die(mysql_error($conn));
            $rCek=  mysql_num_rows($qCek);
            if($rCek>0){
                $resData[$rData['nik']][flag]=1; 
                $resData[$rData['nik']][$rData['keterangan']]=$rData['jumlahpotongan'];  
                $resData[$rData['nik']][jm]+=$rData['jumlahpotongan']; 
                $aPotongan[$rData['keterangan']]=$rData['keterangan'];
            }
        }
        $optPot=  makeOption($dbname, 'sdm_ho_component', 'id,name');
        

// PREVIEW HEADER
        $tab.="<table cellspacing='1' border='".$brd."' class='sortable'>
        <thead class=rowheader>
        <tr ".$bgr.">
        <td>No</td>
        <td>".$_SESSION['lang']['nik']."</td>
        <td>".$_SESSION['lang']['nama']."</td>";
        foreach($aPotongan as $ar => $isi)
        {
                $tab.="<td>";
                $tab.=$optPot[$isi]; 
                $tab.="</td>";
        }

        $tab.="<td>".$_SESSION['lang']['jumlah']."</td></tr><tbody>";
// PREVIEW CONTENT	
        $i=0;	
        foreach($qwe as $idid){
                if($resData[$idid][flag]==1){
                        $no+=1;
                        $wrk="karyawanid='".$idid."'";
                        $optNm=  makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', $wrk);
                        $optNik=  makeOption($dbname, 'datakaryawan', 'karyawanid,nik', $wrk);
                        $tab.="<tr class='rowcontent'>";
                                $tab .= "<td>".$no."</td>";
                                $tab .= "<td>'".$optNik[$idid]."</td>";
                                $tab .= "<td>".$optNm[$idid]."</td>";
                                foreach($aPotongan as $ar => $isi)
                                {
                                        $tab.="<td align='right'>";
                                        $tab.=number_format($resData[$idid][$isi]); 
                                        $tab.="</td>";
                                }
                                $tab .= "<td align='right'>".number_format($resData[$idid][jm])."</td>";
                        $tab.="</tr>";
                    $tot+=$resData[$idid][jm];    
                }

        }
        $tab.="<tr><td colspan=".(count($aPotongan)+3)." align=center>Total</td><td>".number_format($tot)."</td></tr>";
        $tab.="</tbody></table>";
}
switch($proses)
{
        case'preview':
        echo $tab;
        break;
        case'pdf':
        $kdeOrg=$_GET['kdeOrg'];
        $kdOrg=$_GET['kdOrg'];
        $tgl1=tanggalsystem($_GET['tgl1']);
        $tgl2=tanggalsystem($_GET['tgl2']);
        $tgl_1=tanggalsystem($_GET['tgl_1']);
        $tgl_2=tanggalsystem($_GET['tgl_2']);
        $period=explode('-',$_GET['period']);
        $periode=explode('-',$_GET['periode']);
        $periodeGaji=$_GET['periode'];
        $idKry=$_GET['idKry'];
		$sistemGaji=$_GET['sistemGaji'];
        if(($tgl_1!='')&&($tgl_2!=''))
        {
                $tgl1=$tgl_1;
                $tgl2=$tgl_2;
        }

        $test = dates_inbetween($tgl1, $tgl2);
        if(($tgl2=="")&&($tgl1==""))
        {
                echo"warning: date required";
                exit();
        }

        $jmlHari=count($test);
        //cek max hari inputan
           if($jmlHari>41)
        {
                echo"warning:invalid period";
                exit();
        }
        //ambil query untuk tanggal kehadiran


        //+++++++++++++++++++++++++++++++++++++++++++++++++++++
//create Header

                $subtot=array();
                if($kdOrg!='')
        {
                $kodeOrg=$kdOrg;
                        if(strlen($kdOrg)>4)
                        {
                                $where=" and subbagian='".$kdOrg."'";
                        }
                        else
                        {
                                $where=" and lokasitugas='".$kdOrg."' and (subbagian='0' or subbagian is null)";
                        }
                        $where2=" and kodeorg='".substr($kodeOrg,0,4)."'";
if($_SESSION['empl']['tipelokasitugas']=='HOLDING')$where=" and lokasitugas='".$kdOrg."'";

        }
        elseif($kdOrg=='')
        {
//		echo"warning:Pilih Unit yang di Inginkan";
//                exit();
$where=" and lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
$where2=" and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
        }
        if($sistemGaji=='All')$wherez="";        
        if($sistemGaji=='Bulanan')$wherez=" and sistemgaji = 'Bulanan'";        
        if($sistemGaji=='Harian')$wherez=" and sistemgaji = 'Harian'";        
        $resData=array();
        $qwe=array();
        $sGetKary="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where tipekaryawan in (1,2,3,4)  ".$where." ".$wherez.$whrtp."  order by namakaryawan asc";
        $rGetkary=fetchData($sGetKary);
        foreach($rGetkary as $row => $kar)
    {
      $qwe[]=$kar['karyawanid'];	// buat batas user saat nampilkan nanti;
          $resData[$kar['karyawanid']][id]=$kar['karyawanid'];		
      $resData[$kar['karyawanid']][nm]=$kar['namakaryawan'];
    }
        $sData="select jumlahpotongan,a.nik,tanggal,keterangan from ".$dbname.".sdm_potongandt a left join ".$dbname.".datakaryawan on a.nik=b.karyawanid where periodegaji='".$periodeGaji.$whrtp."' ".$where2.$whrtp." ";
        $qData=mysql_query($sData);// or die(mysql_error());
        while($rData=mysql_fetch_assoc($qData))
        {
                $resData[$rData['nik']][flag]=1;  
                $resData[$rData['nik']][$rData['keterangan']]=$rData['jumlahpotongan'];  
                $resData[$rData['nik']][jm]+=$rData['jumlahpotongan'];  
        }
        $aPotongan=array();
        $sPotongan="select keterangan from ".$dbname.".sdm_potongandt a left join ".$dbname.".datakaryawan on a.nik=b.karyawanid where periodegaji='".$periodeGaji."' ".$where2.$whrtp." group by keterangan";	
        $rPotongan=fetchData($sPotongan);
        $kolomPotongan=0;
        foreach($rPotongan as $row => $pot)
    {
          $aPotongan[$pot['keterangan']]=$pot['keterangan'];
          $kolomPotongan+=1;		
        }

        if($kolomPotongan>10)
        {
                echo"warning: Data too large, please use preview button.";
                exit();
        }


class PDF extends FPDF
        {
            function Header() {
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
                                global $period;
                                global $periode;
                                global $kdOrg;
                                global $kdeOrg;
                                global $tgl1;
                                global $tgl2;
                                global $where;
                                global $jmlHari;
                                global $test;
                                global $klmpkAbsn;
                                global $baris;
                                global $i;
                                global $row;
                                global $nomor;
                                global $sistemGaji;
                                global $periodeGaji;
                                global $aPotongan;
                                global $kolomPotongan;


                                $jmlHari=$jmlHari*1.5;
                                $cols=247.5;
                            # Alamat & No Telp
                $query = selectQuery($dbname,'organisasi','alamat,telepon',
                    "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                $orgData = fetchData($query);

                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 20;
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

                $this->SetFont('Arial','B',10);
                                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['lapPotongan']." ".$sistemGaji,'',0,'L');
                                $this->Ln();

                                $this->Cell($width,$height,strtoupper($_SESSION['lang']['lapPotongan']),'',0,'C');
                                $this->Ln();
                                $this->Cell($width,$height,strtoupper($_SESSION['lang']['periode'])." :". tanggalnormal($tgl1)." s.d. ". tanggalnormal($tgl2),'',0,'C');
                                $this->Ln();
                $this->SetFont('Arial','B',7);
                $this->SetFillColor(220,220,220);
                                $this->Cell(3/100*$width,$height,'No',1,0,'C',1);
                                $this->Cell(10/100*$width,$height,$_SESSION['lang']['nama'],1,0,'C',1);
                                $lebarPotongan=82/$kolomPotongan;
                                $legend=0;		
                                foreach($aPotongan as $ar => $isi)
                                {
                                        $legend+=1;
                                        if(strlen($isi)>15)
                                        {
                                                $isinya=substr($isi,0,10);
                                                $isinya.=substr($isi,-5,5);
                                        } else $isinya=$isi;
                                        if($lebarPotongan>10)$this->Cell($lebarPotongan/100*$width,$height,$isinya,1,0,'C',1); else
                                                $this->Cell($lebarPotongan/100*$width,$height,$legend,1,0,'C',1);
                                }
                                $this->Cell(6/100*$width,$height,$_SESSION['lang']['jumlah'],1,1,'C',1);
            }

            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
        }
        $pdf=new PDF('P','pt','Legal');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
                $pdf->AddPage();
                $pdf->SetFillColor(255,255,255);
                $pdf->SetFont('Arial','',7);

// PDF CONTENT	

        $i=0;	
    $pdf->SetFont('Arial','',6);
        foreach($qwe as $idid)
        {
                if($resData[$idid][flag]==1){
                        $no+=1;
                        $pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);
                        $pdf->Cell(10/100*$width,$height,$resData[$idid][nm],1,0,'L',1);
                        $lebarPotongan=82/$kolomPotongan;		
                        foreach($aPotongan as $ar => $isi)
                        {
                        $pdf->Cell($lebarPotongan/100*$width,$height,number_format($resData[$idid][$isi]),1,0,'R',1);	
                        }
                        $pdf->Cell(6/100*$width,$height,number_format($resData[$idid][jm]),1,1,'R',1);
                }

        }
        if($lebarPotongan<=10){
                $legend=0;		
                foreach($aPotongan as $ar => $isi)
                {
                        $legend+=1;
                $pdf->Cell($width,$height,$legend.". ".$isi,'',1,'L');
                }


        }


        $pdf->Output();

        break;
        case'excel':
       
                        //echo "warning:".$strx;
                        //=================================================


                        $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
                        if($period!='')
                        {
                                $art=$period;
                                $art=$art[1].$art[0];
                        }
                        if($periode!='')
                        {
                                $art=$periode;
                                $art=$art[1].$art[0];
                        }
                        if($kdeOrg!='')
                        {
                                $kodeOrg=$kdeOrg;
                        }
                        if($kdOrg!='')
                        {
                                $kodeOrg=$kdOrg;
                        }
                        $nop_="RekapPotongan".$art."__".$kodeOrg;
if($kodeOrg=='') $nop_="RekapPotongan".$art."__".$_SESSION['empl']['lokasitugas'];
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
        case'getTgl':
        if($periode!='')
        {
                $tgl=$periode;
                $tanggal=$tgl[0]."-".$tgl[1];
        }
        elseif($period!='')
        {
                $tgl=$period;
                $tanggal=$tgl[0]."-".$tgl[1];
        }
$kdUnit=$_GET['kdUnit'];
$periode=$_GET['periode'];
if($_SESSION['empl']['tipelokasitugas']=='HOLDING')	$sTgl="select distinct tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji where kodeorg = '".$kdUnit."' and  periode='".$periode."' ";
else
        $sTgl="select distinct tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and periode='".$tanggal."' ";
        //echo"warning".$sTgl;
        $qTgl=mysql_query($sTgl) or die(mysql_error());
        $rTgl=mysql_fetch_assoc($qTgl);
        echo tanggalnormal($rTgl['tanggalmulai'])."###".tanggalnormal($rTgl['tanggalsampai']);
        break;
        case'getKry':
        if(strlen($kdeOrg)>4)
        {
                $where=" subbagian='".$kdeOrg."'";
        }
        else
        {
                $where=" lokasitugas='".$kdeOrg."'";
        }
        $sKry="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where ".$where." order by namakaryawan asc";
        $qKry=mysql_query($sKry) or die(mysql_error());
        while($rKry=mysql_fetch_assoc($qKry))
        {
                $optKry.="<option value=".$rKry['karyawanid'].">".$rKry['namakaryawan']."</option>";
        }
        echo $optKry;
        break;
case'getPeriode':
        $optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $sPeriode="select distinct periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$kdeOrg."'";
        //exit("Error".$sPeriode);
        $qPeriode=mysql_query($sPeriode) or die(mysql_error());
        while($rPeriode=mysql_fetch_assoc($qPeriode))
        {
                $optPeriode.="<option value=".$rPeriode['periode'].">".substr(tanggalnormal($rPeriode['periode']),1,7)."</option>";
        }
        echo $optPeriode;
        break;

        default:
        break;
}

?>