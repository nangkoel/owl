<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

//$arrDat="##kdeOrg##period##pilihan_2##pilihan_3##tgl_1##tgl_2";
$proses=$_GET['proses'];
//$periode=$_POST['periode'];
//$period=$_POST['period'];
$lksiTgs=$_SESSION['empl']['lokasitugas'];
$kdeOrg=isset($_POST['kdeOrg'])?$_POST['kdeOrg']:'';
        if($kdeOrg=='')$kdeOrg=$_GET['kdeOrg'];
$kdOrg=isset($_POST['kdOrg'])?$_POST['kdOrg']:'';
        if($kdOrg=='')$kdOrg=isset($_GET['kdOrg'])? $_GET['kdOrg']:'';
$tgl1=isset($_POST['tgl1'])?tanggalsystem($_POST['tgl1']):'';
$tgl2=isset($_POST['tgl2'])?tanggalsystem($_POST['tgl2']):'';
        if($tgl1=='')$tgl1=isset($_GET['tgl1'])? tanggalsystem($_GET['tgl1']):'';
        if($tgl2=='')$tgl2=isset($_GET['tgl2'])? tanggalsystem($_GET['tgl2']):'';
$tgl_1=isset($_POST['tgl_1'])?tanggalsystem($_POST['tgl_1']):'';
$tgl_2=isset($_POST['tgl_2'])?tanggalsystem($_POST['tgl_2']):'';
        if($tgl_1=='')$tgl_1=tanggalsystem($_GET['tgl_1']);
        if($tgl_2=='')$tgl_2=tanggalsystem($_GET['tgl_2']);
$periode=isset($_POST['period'])?$_POST['period']:'';
        if($periode=='')$periode=$_GET['period'];
$periodeGaji=$periode;

$periode=explode('-',$periode);
        //exit("Error:".$periode[0]);
$kdUnit=isset($_POST['kdUnit'])?$_POST['kdUnit']:'';
$pilihan=isset($_POST['pilihan'])?$_POST['pilihan']:'';
        if($pilihan=='')$pilihan=isset($_GET['pilihan'])?$_GET['pilihan']:'';
$pilihan2=isset($_POST['pilihan_2'])?$_POST['pilihan_2']:'';
        if($pilihan2=='')$pilihan2=$_GET['pilihan_2'];
$pilihan3=isset($_POST['pilihan_3'])?$_POST['pilihan_3']:'';
        if($pilihan3=='')$pilihan3=$_GET['pilihan_3'];

if(!$kdOrg)$kdOrg=$_SESSION['empl']['lokasitugas'];

$optBagian=makeOption($dbname,'sdm_5departemen','kode,nama');
$optTipe=makeOption($dbname,'sdm_5tipekaryawan','id,tipe');
$optJabatan=makeOption($dbname,'sdm_5jabatan','kodejabatan,namajabatan');

//get data karyawan nama,jabatan sm tipe
$optNmKar=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optNikKar=makeOption($dbname, 'datakaryawan', 'karyawanid,nik');
$optDtJbtnr=makeOption($dbname, 'datakaryawan', 'karyawanid,kodejabatan');
$optDtBag=makeOption($dbname, 'datakaryawan', 'karyawanid,bagian');
$optDtTipe=makeOption($dbname, 'datakaryawan', 'karyawanid,tipekaryawan');
$optDtSub=makeOption($dbname, 'datakaryawan', 'karyawanid,subbagian');
$arrTpLmbr=array("0"=>"Normal","1"=>"Minggu","2"=>"Hari libur bukan minggu","3"=>"Hari raya");

function dates_inbetween($date1, $date2)
{
    $day = 60*60*24;
    $date1 = strtotime($date1);
    $date2 = strtotime($date2);
    $days_diff = round(($date2 - $date1)/$day); // Unix time difference devided by 1 day to get total days in between
    $dates_array = array();
    $dates_array[] = date('Y-m-d',$date1);
    for($x = 1; $x < $days_diff; $x++)
        {
        $dates_array[] = date('Y-m-d',($date1+($day*$x)));
    }
    $dates_array[] = date('Y-m-d',$date2);
    return $dates_array;
}

        if(($tgl_1!='')&&($tgl_2!=''))
        {	
                $tgl1=$tgl_1;
                $tgl2=$tgl_2;
        }
        $test = dates_inbetween($tgl1, $tgl2);




// get namaorganisasi =========================================================================
        $sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where kodeorganisasi ='".$kdeOrg."' ";	
        $qOrg=mysql_query($sOrg) or die(mysql_error($conn));
        while($rOrg=mysql_fetch_assoc($qOrg))
        {
                $nmOrg=$rOrg['namaorganisasi'];
        }
        if(!$nmOrg)$nmOrg=$kdOrg;
        //ambil where untuk data karyawan
        if($kdeOrg!='')
        {

                if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
                {
                        $where=" and lokasitugas = '".$kdeOrg."'";
                        $where2=" and substr(kodeorg,1,4)='".$kdeOrg."'";
                }
                else
                {
                        if(strlen($kdeOrg)>4)
                        {
                                $where=" and subbagian='".$kdeOrg."'";
                                $where2=" and kodeorg='".$kdeOrg."'";
                        }
                        else
                        {
                                $where=" and lokasitugas='".$kdeOrg."'";
                                $where2=" and substr(kodeorg,1,4)='".$kdeOrg."'";
                        }
                }
        }
        else
        {
                $kodeOrg=$_SESSION['empl']['lokasitugas'];
                $where=" and lokasitugas='".$kodeOrg."'";
        }
// pilihan 2
if($pilihan2=='semua'){
        $where3 = '';
}else
if($pilihan2=='bulanan'){
        $where3 = ' and a.sistemgaji = \'Bulanan\' ';
}else
if($pilihan2=='harian'){
        $where3 = ' and a.sistemgaji = \'Harian\' ';
}

// pilihan 3
if($pilihan3=='semua')
        $where4 = '';
else
        $where4 = " and a.bagian = '".$pilihan3."' ";
$bgclr=" ";
$brdr=0;
if($proses=='excel')
{
    $bgclr=" bgcolor=#DEDEDE align=center";
    $brdr=1;
}
if(($proses=='excel')||($proses=='preview')||($proses=='pdf'))
{

        $sAbsensi="select distinct count(absensi) as jmlhhadir,absensi,karyawanid from ".$dbname.".sdm_absensidt_vw where nilaihk='H' and
                   tanggal between '".$tgl_1."' and '".$tgl_2."' and substring(kodeorg,1,4)='".substr($kdeOrg,0,4)."' group by karyawanid,absensi";
        //exit("Error:".$sAbsensi);
        $qAbsensi=mysql_query($sAbsensi) or die(mysql_error());
        while($rAbsensi=mysql_fetch_assoc($qAbsensi))
        {
            $jmlhHadir[$rAbsensi['karyawanid']]=$rAbsensi['jmlhhadir'];
        }
        //kehadiran di perawatan
        $sKehadiran="select count(absensi) as jmlhhadir,karyawanid from ".$dbname.".kebun_kehadiran_vw 
                     where tanggal between  '".$tgl_1."' and '".$tgl_2."' and substring(unit,1,4)='".substr($kdeOrg,0,4)."'";
        $qKehadiran=mysql_query($sKehadiran) or die(mysql_error($conn));
		$jmlhHadir = array();
        while($rKehadiran=mysql_fetch_assoc($qKehadiran))
        {
            if(isset($jmlhHadir[$rKehadiran['karyawanid']])) {
				$jmlhHadir[$rKehadiran['karyawanid']]+=$rKehadiran['jmlhhadir'];
			} else {
				$jmlhHadir[$rKehadiran['karyawanid']]=$rKehadiran['jmlhhadir'];
			}
        }
        //kehadiran di panen
        $sPrestasi="select count(a.nik) as jmlhhadir,a.nik from ".$dbname.".kebun_prestasi a left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi 
                    where b.notransaksi like '%PNN%' and substr(b.kodeorg,1,4)='".substr($kdeOrg,0,4)."' and b.tanggal between '".$tgl_1."' and '".$tgl_2."'
                    group by a.nik";
        //exit("Error:".$sPrestasi);
        $qPrestasi=mysql_query($sPrestasi) or die(mysql_error($conn));
        while($rPrestasi=mysql_fetch_assoc($qPrestasi))
        {
			if(isset($jmlhHadir[$rPrestasi['nik']])) {
				$jmlhHadir[$rPrestasi['nik']]+=$rPrestasi['jmlhhadir'];
			} else {
				$jmlhHadir[$rPrestasi['nik']]=$rPrestasi['jmlhhadir'];
			}
        }
        //get jam lembur
        $sGetLembur="select jamaktual, jamlembur,tipelembur from ".$dbname.".sdm_5lembur where kodeorg = '".substr($kdeOrg,0,4)."'";
        //exit("Error".$sGetLembur);
        $rGetLembur=fetchData($sGetLembur);
        foreach($rGetLembur as $row => $kar)
        {
            $GetLembur[$kar['tipelembur'].$kar['jamaktual']]=$kar['jamlembur'];
        }  
        //semua data lembur
        $sLembur="select  uangkelebihanjam,a.karyawanid,jamaktual,tipelembur from ".$dbname.".sdm_lemburdt b
                  LEFT JOIN ".$dbname.".datakaryawan a on a.karyawanid = b.karyawanid
                  WHERE b.tanggal between  '".$tgl_1."' and '".$tgl_2."' ".$where2." ".$where3." ".$where4." order by namakaryawan asc ";
        $qLembur=mysql_query($sLembur) or die(mysql_error($conn));
		$dtKaryawan = array();
        while($rLembur=mysql_fetch_assoc($qLembur)){
             
            
            if(isset($jlhJmLembur[$rLembur['karyawanid']])) {
				$jlhJmLembur[$rLembur['karyawanid']]+=$GetLembur[$rLembur['tipelembur'].$rLembur['jamaktual']];//jumlah jam sblm perkalian
			} else {
				$jlhJmLembur[$rLembur['karyawanid']]=$GetLembur[$rLembur['tipelembur'].$rLembur['jamaktual']];//jumlah jam sblm perkalian
			}
			if(isset($jlhJamLemburKali[$rLembur['karyawanid']])) {
				$jlhJamLemburKali[$rLembur['karyawanid']]+=$rLembur['jamaktual'];
			} else {
				$jlhJamLemburKali[$rLembur['karyawanid']]=$rLembur['jamaktual'];
			}
			if(isset($jlhUang[$rLembur['karyawanid']])) {
				$jlhUang[$rLembur['karyawanid']]+=$rLembur['uangkelebihanjam'];
			} else {
				$jlhUang[$rLembur['karyawanid']]=$rLembur['uangkelebihanjam'];
			}
            $dtKaryawan[$rLembur['karyawanid']]=$rLembur['karyawanid'];
        }
        $tab="<table cellspacing='1' border='".$brdr."' class='sortable'>
        <thead class=rowheader>
        <tr>
        <td ".$bgclr.">No.</td>
        <td ".$bgclr.">".$_SESSION['lang']['nik']."</td>
        <td ".$bgclr.">".$_SESSION['lang']['nama']."</td>
        <td ".$bgclr.">".$_SESSION['lang']['subbagian']."</td>
        <td ".$bgclr.">".$_SESSION['lang']['tipekaryawan']."</td>
        <td ".$bgclr.">".$_SESSION['lang']['bagian']."</td>
        <td ".$bgclr.">".$_SESSION['lang']['jabatan']."</td>
        <td ".$bgclr.">".$_SESSION['lang']['total']." ".$_SESSION['lang']['absensi']."</td>
		<td ".$bgclr.">".$_SESSION['lang']['totLembur']." Actual</td>
        <td ".$bgclr.">".$_SESSION['lang']['totLembur']."</td>";
		if(($_SESSION['empl']['tipelokasitugas']=='KANWIL'||$_SESSION['empl']['tipelokasitugas']=='HOLDING') and ($_SESSION['empl']['bagian']=='FIN'||$_SESSION['empl']['bagian']=='FAT'||$_SESSION['empl']['bagian']=='IT')) {
                $tab.="<td ".$bgclr.">".$_SESSION['lang']['jumlah']." (Rp)</td>";
		}
       $tab.="</tr><thead><tbody>";
		$no=0;
        foreach($dtKaryawan as $dtKary)
        {
            $no++;
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$no."</td>";
            $tab.="<td>".$optNikKar[$dtKary]."</td>";
            $tab.="<td>".$optNmKar[$dtKary]."</td>";
            $tab.="<td>".$optDtSub[$dtKary]."</td>";
            $tab.="<td>".$optTipe[$optDtTipe[$dtKary]]."</td>";
			if(isset($optBagian[$optDtBag[$dtKary]])) {
				$tab.="<td>".$optBagian[$optDtBag[$dtKary]]."</td>";
			} else {
				$tab.="<td></td>";
			}
            $tab.="<td>".$optJabatan[$optDtJbtnr[$dtKary]]."</td>";
			if(isset($jmlhHadir[$dtKary])) {
				$tab.="<td align=right>".$jmlhHadir[$dtKary]."</td>";
			} else {
				$tab.="<td></td>";
			}
            $tab.="<td align=right>".$jlhJamLemburKali[$dtKary]."</td>";
            $tab.="<td align=right>".$jlhJmLembur[$dtKary]."</td>";
            if(($_SESSION['empl']['tipelokasitugas']=='KANWIL'||$_SESSION['empl']['tipelokasitugas']=='HOLDING') and ($_SESSION['empl']['bagian']=='FIN'||$_SESSION['empl']['bagian']=='FAT'||$_SESSION['empl']['bagian']=='IT')) {
                $tab.="<td align=right>".number_format($jlhUang[$dtKary],0)."</td>";
                }
            $tab.="</tr>";
        }
        $tab.="</tbody></table>";
}
switch($proses)
{
        case'preview':   
        if($periodeGaji=='')
        {
                echo"warning: Periode tidak boleh kosong";
                exit();
        }

        echo $tab;
        break;

        case'pdf':

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
                                global $nmOrg;
                                global $pilihan;
                                global $pilihan2;

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
				$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['laporanLembur']."  Total/".$_SESSION['lang']['karyawan']." (option ".$pilihan.") ".$pilihan2,'',0,'L');
				$this->Ln();
				$this->Cell($width,$height,strtoupper('Overtime Recapitulation')." : ".$nmOrg,'',0,'C');
				$this->Ln();
				$this->Cell($width,$height,strtoupper($_SESSION['lang']['periode'])." :". tanggalnormal($tgl1)." s.d. ". tanggalnormal($tgl2),'',0,'C');
				$this->Ln();
				$this->SetFont('Arial','B',7);
				$this->SetFillColor(220,220,220);
				$this->Cell(3/100*$width,$height,'No','TLR',0,'C',1);
				$this->Cell(15/100*$width,$height,$_SESSION['lang']['nama'],'TLR',0,'C',1);
				$this->Cell(10/100*$width,$height,$_SESSION['lang']['tipekaryawan'],'TLR',0,'C',1);
				$this->Cell(10/100*$width,$height,$_SESSION['lang']['bagian'],'TLR',0,'C',1);
				$this->Cell(10/100*$width,$height,$_SESSION['lang']['jabatan'],'TLR',0,'C',1);	
				$this->Cell(10/100*$width,$height,$_SESSION['lang']['total'],'TLR',0,'C',1);	
				$this->Cell(10/100*$width,$height,$_SESSION['lang']['totLembur'],'TLR',0,'C',1);	
				$this->Cell(10/100*$width,$height,$_SESSION['lang']['totLembur'],'TLR',0,'C',1);
				if(($_SESSION['empl']['tipelokasitugas']=='KANWIL'||$_SESSION['empl']['tipelokasitugas']=='HOLDING') and ($_SESSION['empl']['bagian']=='FIN'||$_SESSION['empl']['bagian']=='FAT'||$_SESSION['empl']['bagian']=='IT')) {
					$this->Cell(10/100*$width,$height,'','TLR',1,'C',1);
				} else {
					$this->Ln();
				}

				$this->Cell(3/100*$width,$height," ",'BLR',0,'C',1);
				$this->Cell(15/100*$width,$height," ",'BLR',0,'C',1);
				$this->Cell(10/100*$width,$height," ",'BLR',0,'C',1);
				$this->Cell(10/100*$width,$height," ",'BLR',0,'C',1);
				$this->Cell(10/100*$width,$height," ",'BLR',0,'C',1);	
				$this->Cell(10/100*$width,$height,$_SESSION['lang']['absensi'],'BLR',0,'C',1);	
				$this->Cell(10/100*$width,$height,'Actual','BLR',0,'C',1);	
				$this->Cell(10/100*$width,$height,'','BLR',0,'C',1);
				if(($_SESSION['empl']['tipelokasitugas']=='KANWIL'||$_SESSION['empl']['tipelokasitugas']=='HOLDING') and ($_SESSION['empl']['bagian']=='FIN'||$_SESSION['empl']['bagian']=='FAT'||$_SESSION['empl']['bagian']=='IT')) {
					$this->Cell(10/100*$width,$height,"(Rupiah)",'BLR',1,'C',1);
				} else {
					$this->Ln();
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
                $pdf->SetFont('Arial','',6);
				$n3o=0;
                foreach($dtKaryawan as $dtKary)
                {
                    $n3o++;
                    $pdf->Cell(3/100*$width,$height,$n3o,'TBLR',0,'C',1);
                    $pdf->Cell(15/100*$width,$height,$optNmKar[$dtKary],'TBLR',0,'L',1);
                    $pdf->Cell(10/100*$width,$height,$optTipe[$optDtTipe[$dtKary]],'TBLR',0,'L',1);
                    $pdf->Cell(10/100*$width,$height,isset($optBagian[$optDtBag[$dtKary]])?$optBagian[$optDtBag[$dtKary]]:'','TBLR',0,'L',1);
                    $pdf->Cell(10/100*$width,$height,$optJabatan[$optDtJbtnr[$dtKary]],'TBLR',0,'L',1);	
                    $pdf->Cell(10/100*$width,$height,isset($jmlhHadir[$dtKary])?$jmlhHadir[$dtKary]:'','TBLR',0,'R',1);	
                    $pdf->Cell(10/100*$width,$height,$jlhJamLemburKali[$dtKary],'TBLR',0,'R',1);	
                    $pdf->Cell(10/100*$width,$height,$jlhJmLembur[$dtKary],'TBLR',0,'R',1);
					if(($_SESSION['empl']['tipelokasitugas']=='KANWIL'||$_SESSION['empl']['tipelokasitugas']=='HOLDING') and ($_SESSION['empl']['bagian']=='FIN'||$_SESSION['empl']['bagian']=='FAT'||$_SESSION['empl']['bagian']=='IT')) {
						$pdf->Cell(10/100*$width,$height,number_format($jlhUang[$dtKary],0),'TBLR',1,'R',1);
					} else {
						$pdf->Ln();
					}
                }

        $pdf->Output();
        break;

        case'excel':
       $wktu=date("Hms");
                $nop_="RekapLembur_total_per_orang_".$wktu."__".$kdeOrg;
                if(strlen( $tab)>0)
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
                if(!fwrite($handle, $tab))
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
            $add='';

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
        if($pilihan2=='bulanan')
        {
            $add=" and jenisgaji='B'";

        }
        if($pilihan2=='harian')
        {
            $add=" and jenisgaji='H'";

        }
        $sTgl="select distinct tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji where 
            kodeorg='".substr($kdUnit,0,4)."' and periode='".$tanggal."' ".$add."";
        //echo"warning".$sTgl;
        // exit("Error:".$sTgl);
        $qTgl=mysql_query($sTgl) or die(mysql_error());
        $rTgl=mysql_fetch_assoc($qTgl);
        echo tanggalnormal($rTgl['tanggalmulai'])."###".tanggalnormal($rTgl['tanggalsampai']);
        break;
        case'getPeriode':
            //echo"warning:masuk";
            $sPeriode="select distinct periode from ".$dbname.".sdm_5periodegaji  where kodeorg='".$kdOrg."'";
            $optPeriode="<option value''>".$_SESSION['lang']['pilihdata']."</option>";
            $qPeriode=mysql_query($sPeriode) or die(mysql_error());
            while($rPeriode=mysql_fetch_assoc($qPeriode))
            {
                $optPeriode.="<option value=".$rPeriode['periode'].">".$rPeriode['periode']."</option>";
            }
            echo $optPeriode;
        break;
        default:
        break;
}

?>