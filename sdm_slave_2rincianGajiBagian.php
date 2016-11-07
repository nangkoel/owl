<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');


$proses=$_GET['proses'];
//$arr="##kdOrg##periode##kdBag##tgl1##tgl2##sisGaji";
$lksiTgs=$_SESSION['empl']['lokasitugas'];

$_POST['kdOrg']==''?$kdOrg=$_GET['kdOrg']:$kdOrg=$_POST['kdOrg'];
$_POST['tgl1']==''?$tgl1=tanggalsystem($_GET['tgl1']):$tgl1=tanggalsystem($_POST['tgl1']);
$_POST['tgl2']==''?$tgl2=tanggalsystem($_GET['tgl2']):$tgl2=tanggalsystem($_POST['tgl2']);
$_POST['periode']==''?$periodeGaji=$_GET['periode']:$periodeGaji=$_POST['periode'];
$periode=explode('-',$_POST['periode']);
$_POST['kdBag']==''?$kdBag=$_GET['kdBag']:$kdBag=$_POST['kdBag'];
$_POST['sisGaji']==''?$sisGaji=$_GET['sisGaji']:$sisGaji=$_POST['sisGaji'];

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
        if($kdOrg!='')
        {
                        if(strlen($kdOrg)>4)
                        {
                                $where=" and subbagian='".$kdOrg."'";
                        }
                        else
                        {
                                $where=" and lokasitugas='".$kdOrg."' and (subbagian='0' or subbagian='')";
                        }

        }
        elseif($kdOrg=='')
        {
            $kdOrg=$_SESSION['empl']['lokasitugas'];
            $where=" and lokasitugas='".$kdOrg."'";
        }
        if($kdBag!='')
        {
            $where.=" and bagian='".$kdBag."'";
            $where2=" where kode='".$kdBag."'";
        }
        if($sisGaji!='')
        {
            $where.=" and sistemgaji='".$sisGaji."'";
        }
        $resData=array();
        $sGetKary="select karyawanid,namakaryawan,b.nama,c.namajabatan,b.kode from ".$dbname.".datakaryawan a 
            left join ".$dbname.".sdm_5departemen b on a.bagian=b.kode
            left join  ".$dbname.".sdm_5jabatan c on a.kodejabatan=c.kodejabatan
            where tipekaryawan in (1,2,3,4)  ".$where."   order by namakaryawan asc";
        //exit("Error".$sGetKary);
        $rGetkary=fetchData($sGetKary);
        //$resData[]=$rGetkary;
        foreach($rGetkary as $row => $kar)
        {
            $resData[$kar['karyawanid']][]=$kar['karyawanid'];		
            $namakar[$kar['karyawanid']]=$kar['namakaryawan'];
            $namaJabatan[$kar['karyawanid']]=$kar['namajabatan'];
            $namaBagian[$kar['karyawanid']]=$kar['nama'];
            $kdBagian[$kar['karyawanid']]=$kar['kode'];
        }  
        $qBagian="select * from ".$dbname.".sdm_5departemen ".$where2." order by nama asc";
       // exit("Error".$qBagian);
        $rBagian=fetchData($qBagian);
        foreach($rBagian as $brs =>$isi)
        {
            $namaKlmBag[$isi['kode']]=$isi['nama'];
            $kodeBagian[]=$isi;
        }
          $qDataGapok="select sum(jumlah) as gapok,karyawanid from ".$dbname.".sdm_gaji 
               where idkomponen in (1,2,31,30,27) and kodeorg='".substr($kdOrg,0,4)."' and periodegaji='".$periodeGaji."' group by karyawanid";
          //exit($qDataGapok);
        $rDataGapok=fetchData($qDataGapok);
        foreach($rDataGapok as $barisGapok => $rowGapok)
        {
            $dtGapok[$rowGapok['karyawanid']]=$rowGapok['gapok'];
        }
        $qLembur="select sum(uangkelebihanjam) as lembur,karyawanid from ".$dbname.".sdm_lemburdt where kodeorg='".substr($kdOrg,0,4)."' 
            and tanggal between '".$tgl1."' and '".$tgl2."' group by karyawanid ";
       //exit("Error".$qLembur);
        $rLembur=fetchData($qLembur);
        foreach($rLembur as $brsLembur => $rowLembur)
        {
            $dtLembur[$rowLembur['karyawanid']]=$rowLembur['lembur'];
        }
        $qDataJamSos="select sum(jumlah) as gapok,karyawanid from ".$dbname.".sdm_gaji 
            where idkomponen ='3' and kodeorg='".substr($kdOrg,0,4)."' and periodegaji='".$periodeGaji."' group by karyawanid";
       // exit("Error".$qDataJamSos);
        $rDataGapokJamSos=fetchData($qDataJamSos);
        foreach($rDataGapokJamSos as $barisGapokSos => $rowGapokSos)
        {
            $dtGapokSos[$rowGapokSos['karyawanid']]=$rowGapokSos['gapok'];
        }
switch($proses)
{
        case'preview':
        if(($tgl_1!='')&&($tgl_2!=''))
        {
                $tgl1=$tgl_1;
                $tgl2=$tgl_2;
        }

        $test = dates_inbetween($tgl1, $tgl2);
        if(($tgl2=="")&&($tgl1==""))
        {
                echo"warning: Preiod required";
                exit();
        }

        $jmlHari=count($test);
        //cek max hari inputan
        if($jmlHari>31)
        {
                echo"warning: invalid period";
                exit();
        }


        $tab.="<table cellspacing='1' border='0' class='sortable'>
        <thead class=rowheader>
        <tr>

        <td>".$_SESSION['lang']['bagian']."</td>
        <td>".$_SESSION['lang']['namakaryawan']."</td>
        <td>".$_SESSION['lang']['jabatan']."</td>
        <td>".$_SESSION['lang']['gajipokok']."</td>
        <td>".$_SESSION['lang']['lembur']."</td>
        <td>Jamsostek</td>
        </tr><tbody>";



        foreach($kodeBagian as $hslBrs => $hslAkhir)
        {
            if($hslAkhir['kode']!='')
            {
            $afdC=false;$blankC=false;
            if($kdBag=='')
            {
                $sGetKary="select karyawanid,namakaryawan,b.nama,c.namajabatan,b.kode from ".$dbname.".datakaryawan a 
                left join ".$dbname.".sdm_5departemen b on a.bagian=b.kode
                left join  ".$dbname.".sdm_5jabatan c on a.kodejabatan=c.kodejabatan
                where tipekaryawan in (1,2,3,4)  ".$where." and bagian='".$hslAkhir['kode']."'  order by namakaryawan asc";
            }
            else
            {
                $sGetKary="select karyawanid,namakaryawan,b.nama,c.namajabatan,b.kode from ".$dbname.".datakaryawan a 
                left join ".$dbname.".sdm_5departemen b on a.bagian=b.kode
                left join  ".$dbname.".sdm_5jabatan c on a.kodejabatan=c.kodejabatan
                where tipekaryawan in (1,2,3,4)  ".$where."   order by namakaryawan asc";
            }
            $qGetData=mysql_query($sGetKary) or die(mysql_error($conn));
            $rowData=mysql_num_rows($qGetData);
            $tmpRow=$rowData-1;
            if($tmpRow!=-1)
            {

            while($rData=  mysql_fetch_assoc($qGetData))
            {

                $tab.="<tr class='rowcontent'>";
                if($afdC==false) 
                {
                    //$tab .= "<td>".$no."</td>";
                    $tab .= "<td>".$hslAkhir['nama']."</td>";
                    $afdC = true;
                } else {
                    if($blankC==false) {
                            $tab .= "<td rowspan='".$tmpRow."'>&nbsp;</td>";
                            $blankC = true;
                    }
                }
                $tab .= "<td>".$rData['namakaryawan']."</td>";
                $tab .= "<td>".$rData['namajabatan']."</td>";
                $tab .= "<td align=right>".number_format($dtGapok[$rData['karyawanid']],0)."</td>";
                $tab .= "<td align=right>".number_format($dtLembur[$rData['karyawanid']],0)."</td>";
                $tab .= "<td  align=right>".number_format($dtGapokSos[$rData['karyawanid']],0)."</td>";
                $tab .="</tr>";
                $subtot[$hslAkhir['kode']]+=$dtLembur[$rData['karyawanid']];
                $subtotGapok[$hslAkhir['kode']]+=$dtGapok[$rData['karyawanid']];
                $subtotGapokSos[$hslAkhir['kode']]+=$dtGapokSos[$rData['karyawanid']];

            }
            $tab.="<tr class='rowcontent'><td colspan=3 align=right>Total ".$namaKlmBag[$hslAkhir['kode']]." </td>
                <td align=\"right\">".number_format($subtotGapok[$hslAkhir['kode']],0)."</td>
                <td align=\"right\">".number_format($subtot[$hslAkhir['kode']],0)."</td>
                <td align=\"right\">".number_format($subtotGapokSos[$hslAkhir['kode']],0)."</td></tr>";
            }
           }
        }

        $tab.="</tbody></table>";
        echo $tab;
        break;
        case'pdf':

        if(($tgl_1!='')&&($tgl_2!=''))
        {
                $tgl1=$tgl_1;
                $tgl2=$tgl_2;
        }

        $test = dates_inbetween($tgl1, $tgl2);
        if(($tgl2=="")&&($tgl1==""))
        {
                echo"warning: Invalid period";
                exit();
        }

        $jmlHari=count($test);
        //cek max hari inputan
        if($jmlHari>31)
        {
                echo"warning: Invalid period";
                exit();
        }


        //+++++++++++++++++++++++++++++++++++++++++++++++++++++
//create Header

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
                                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['rinciGajiBag'],'',0,'L');
                                $this->Ln();
                                $this->Ln();

                                $this->Cell($width,$height,strtoupper($_SESSION['lang']['rinciGajiBag']),'',0,'C');
                                $this->Ln();
                                $this->Cell($width,$height,strtoupper($_SESSION['lang']['periode'])." :". tanggalnormal($tgl1)." s.d. ". tanggalnormal($tgl2),'',0,'C');
                                $this->Ln();
                                $this->Ln();
                $this->SetFont('Arial','B',8);
                $this->SetFillColor(220,220,220);
                                $this->Cell(15/100*$width,$height,$_SESSION['lang']['bagian'],1,0,'C',1);
                                $this->Cell(20/100*$width,$height,$_SESSION['lang']['namakaryawan'],1,0,'C',1);		
                                $this->Cell(20/100*$width,$height,$_SESSION['lang']['jabatan'],1,0,'C',1);
                                $this->Cell(12/100*$width,$height,$_SESSION['lang']['gajipokok'],1,0,'C',1);
                                $this->Cell(12/100*$width,$height,$_SESSION['lang']['lembur'],1,0,'C',1);
                                $this->Cell(12/100*$width,$height,"Jamsostek",1,1,'C',1);
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
                $subtot=array();


        foreach($kodeBagian as $hslBrs => $hslAkhir)
        {

            if($hslAkhir['kode']!='')
            {
            $afdC=false;$blankC=false;
            if($kdBag=='')
            {
                $sGetKary="select karyawanid,namakaryawan,b.nama,c.namajabatan,b.kode from ".$dbname.".datakaryawan a 
                left join ".$dbname.".sdm_5departemen b on a.bagian=b.kode
                left join  ".$dbname.".sdm_5jabatan c on a.kodejabatan=c.kodejabatan
                where tipekaryawan in (1,2,3,4)  ".$where." and bagian='".$hslAkhir['kode']."'  order by namakaryawan asc";
            }
            else
            {
                $sGetKary="select karyawanid,namakaryawan,b.nama,c.namajabatan,b.kode from ".$dbname.".datakaryawan a 
                left join ".$dbname.".sdm_5departemen b on a.bagian=b.kode
                left join  ".$dbname.".sdm_5jabatan c on a.kodejabatan=c.kodejabatan
                where tipekaryawan in (1,2,3,4)  ".$where."   order by namakaryawan asc";
            }
            $qGetData=mysql_query($sGetKary) or die(mysql_error($conn));
            $rowData=mysql_num_rows($qGetData);
            $tmpRow=$rowData-1;
           if($tmpRow!=-1)
            {
            while($rData=  mysql_fetch_assoc($qGetData))
            {					
                        if($afdC==false) {
                                $pdf->Cell(15/100*$width,$height,$hslAkhir['nama'],1,0,'C',1);	
                                $afdC = true;
                        } else {

                                if($blankC==false) {
                                        $pdf->Cell(15/100*$width,$height,'',1,0,'L',1);	
                                }
                        }	
                        $pdf->Cell(20/100*$width,$height,$rData['namakaryawan'],1,0,'L',1);	
                        $pdf->Cell(20/100*$width,$height,$rData['namajabatan'],1,0,'L',1);
                        $pdf->Cell(12/100*$width,$height,number_format($dtGapok[$rData['karyawanid']],0),1,0,'R',1);
                        $pdf->Cell(12/100*$width,$height,number_format($dtLembur[$rData['karyawanid']],0),1,0,'R',1);
                        $pdf->Cell(12/100*$width,$height,number_format($dtGapokSos[$rData['karyawanid']],0),1,1,'R',1);
                        $subtot[$hslAkhir['kode']]+=$dtLembur[$rData['karyawanid']];
                        $subtotGapok[$hslAkhir['kode']]+=$dtGapok[$rData['karyawanid']];
                        $subtotGapokSos[$hslAkhir['kode']]+=$dtGapokSos[$rData['karyawanid']];

                }
                        $pdf->Cell(55/100*$width,$height,"Total ".$namaKlmBag[$hslAkhir['kode']],1,0,'R',1);
                        $pdf->Cell(12/100*$width,$height,number_format($subtotGapok[$hslAkhir['kode']],0),1,0,'R',1);
                        $pdf->Cell(12/100*$width,$height,number_format($subtot[$hslAkhir['kode']],0),1,0,'R',1);
                        $pdf->Cell(12/100*$width,$height,number_format($subtotGapokSos[$hslAkhir['kode']],0),1,1,'R',1);
            }
            }
        }
        $pdf->Output();

        break;
        case'excel':
        if(($tgl_1!='')&&($tgl_2!=''))
        {
                $tgl1=$tgl_1;
                $tgl2=$tgl_2;
        }

        $test = dates_inbetween($tgl1, $tgl2);
        if(($tgl2=="")&&($tgl1==""))
        {
                echo"warning: Invalid period";
                exit();
        }

        $jmlHari=count($test);
        //cek max hari inputan
        if($jmlHari>31)
        {
                echo"warning:Invalid period";
                exit();
        }

        $tab.="<table border=0 cellspacing=1>
            <tr><td align=center colspan=6>".$_SESSION['lang']['rinciGajiBag']."</td></tr>
            <tr><td align=left colspan=2>".$_SESSION['lang']['periode']." :</td><td align=left colspan=4>".$periodeGaji."</td></tr>
            <tr><td align=left colspan=2>".$_SESSION['lang']['unit']." :</td><td align=left colspan=4>".$kdOrg."</td></tr>
            </table>";
        $tab.="<table cellspacing='1' border='1' class='sortable'>
        <thead class=rowheader>
        <tr>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['bagian']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namakaryawan']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jabatan']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['gajipokok']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['lembur']."</td>
        <td bgcolor=#DEDEDE align=center>Jamsostek</td>
        </tr><tbody>";



        foreach($kodeBagian as $hslBrs => $hslAkhir)
        {
            if($hslAkhir['kode']!='')
            {
            $afdC=false;$blankC=false;
            if($kdBag=='')
            {
                $sGetKary="select karyawanid,namakaryawan,b.nama,c.namajabatan,b.kode from ".$dbname.".datakaryawan a 
                left join ".$dbname.".sdm_5departemen b on a.bagian=b.kode
                left join  ".$dbname.".sdm_5jabatan c on a.kodejabatan=c.kodejabatan
                where tipekaryawan in (1,2,3,4)  ".$where." and bagian='".$hslAkhir['kode']."'  order by namakaryawan asc";
            }
            else
            {
                $sGetKary="select karyawanid,namakaryawan,b.nama,c.namajabatan,b.kode from ".$dbname.".datakaryawan a 
                left join ".$dbname.".sdm_5departemen b on a.bagian=b.kode
                left join  ".$dbname.".sdm_5jabatan c on a.kodejabatan=c.kodejabatan
                where tipekaryawan in (1,2,3,4)  ".$where."   order by namakaryawan asc";
            }
            $qGetData=mysql_query($sGetKary) or die(mysql_error($conn));
            $rowData=mysql_num_rows($qGetData);
            $tmpRow=$rowData-1;
            if($tmpRow!=-1)
            {

            while($rData=  mysql_fetch_assoc($qGetData))
            {

                $tab.="<tr class='rowcontent'>";
                if($afdC==false) 
                {
                    //$tab .= "<td>".$no."</td>";
                    $tab .= "<td>".$hslAkhir['nama']."</td>";
                    $afdC = true;
                } else {
                    if($blankC==false) {
                            $tab .= "<td rowspan='".$tmpRow."'>&nbsp;</td>";
                            $blankC = true;
                    }
                }
                $tab .= "<td>".$rData['namakaryawan']."</td>";
                $tab .= "<td>".$rData['namajabatan']."</td>";
                $tab .= "<td align=right>".number_format($dtGapok[$rData['karyawanid']],0)."</td>";
                $tab .= "<td align=right>".number_format($dtLembur[$rData['karyawanid']],0)."</td>";
                $tab .= "<td  align=right>".number_format($dtGapokSos[$rData['karyawanid']],0)."</td>";
                $tab .="</tr>";
                $subtot[$hslAkhir['kode']]+=$dtLembur[$rData['karyawanid']];
                $subtotGapok[$hslAkhir['kode']]+=$dtGapok[$rData['karyawanid']];
                $subtotGapokSos[$hslAkhir['kode']]+=$dtGapokSos[$rData['karyawanid']];

            }
            $tab.="<tr class='rowcontent'><td colspan=3 align=right>Total ".$namaKlmBag[$hslAkhir['kode']]." </td>
                <td align=\"right\">".number_format($subtotGapok[$hslAkhir['kode']],0)."</td>
                <td align=\"right\">".number_format($subtot[$hslAkhir['kode']],0)."</td>
                <td align=\"right\">".number_format($subtotGapokSos[$hslAkhir['kode']],0)."</td></tr>";
            }
           }
        }

        $tab.="</tbody></table>";
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
                        $nop_=$_SESSION['lang']['rinciGajiBag']."__".$kodeOrg;
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
        default:
        break;
}

?>