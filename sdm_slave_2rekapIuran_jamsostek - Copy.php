<?php
//@Copy nangkoelframework
//mainMenu tree == SDM > Laporan > Rekap Jamsostek *>sdm_slave_2rekapIuran_jamsostek
//Created on Wednesday, December 10, 2014
//for PT Hardaya Inti Plantation
//UPDATE Issue - penambahan menu baru rekap laporan Jamsostek
//OWL-Plantation System

require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

// ========= proses ===========================
if (isset($_POST['proses'])) {
    $proses = $_POST['proses'];
} else {
    $proses = $_GET['proses'];
}
// ========= option dan fungsi post========    // ======
$optDivisi=makeOption($dbname, 'organisasi', 'kodeorganisasi, namaorganisasi');
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['divisi']==''?$divisi=$_GET['divisi']:$divisi=$_POST['divisi'];
// ============ berhasil execute dan peringatan 
    if ($periode=='') {
        exit("Error:".$_SESSION['lang']['periode']." Tidak boleh kosong");
    }
    if ($divisi!='') {
        $divisiID=$optDivisi[$divisi];
    } else {
        exit("Error:".$_SESSION['lang']['divisi']." Tidak boleh kosong");
    }
    $thn=explode("-", $periode);
// ======== select data join periode & divisi == 
    $sPerdiv = "select nik,jms,namakaryawan,tanggallahir,statusperkawinan,a.karyawanid,a.jumlah from ".$dbname.".sdm_gaji a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where a.kodeorg='".$divisi."' and a.periodegaji='".$periode."' and idkomponen=1";
    $qPerdiv = mysql_query($sPerdiv) or die(mysql_error($conn));
    // ====
    $border = 0;
    $border1 = 0;
    $border2 = 2;

    
$whrHrd="kodejabatan=33 and kodegolongan>=7 and bagian='HRD' and tanggalkeluar='0000-00-00' and lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";
$qHrd = selectQuery($dbname,'datakaryawan','*',$whrHrd);
$rHrd=fetchData($qHrd);

    
// ========================= preview ========================================
    if ($proses=='excel') {
        $bgcolor="bgcolor=#DEDEDE";
        $border = 0;
        $border1 = 1;
        $border2 = 2;

        $periode=$_GET['periode'];
        $divisi=$_GET['divisi'];
            if ($periode=='') {
                exit("Error: Periode Harus Di Pilih");
            }
            if ($divisi=='') {
                exit("Error: Divisi Harus Di Pilih"); 
            }
    // =============== proses preview excel ==============
                 // ========== format bulan ===========================
                $formatTahun= substr($_GET['periode'], 0,4);
                $formatBulan= substr($_GET['periode'], 5,2);
                if ($formatBulan=='01') {
                    $Bulanpelaporan= "Januari";
                } if ($formatBulan=='02') {
                    $Bulanpelaporan= "Februari";
                } if ($formatBulan=='03') {
                    $Bulanpelaporan= "Maret";
                } if ($formatBulan== '04') {
                    $Bulanpelaporan= "April";
                } if ($formatBulan== '05') {
                    $Bulanpelaporan= "Mei";
                } if ($formatBulan== '06') {
                    $Bulanpelaporan= "Juni";
                } if ($formatBulan=='07') {
                    $Bulanpelaporan= "Juli";
                } if ($formatBulan== '08') {
                    $Bulanpelaporan= "Agustus";
                } if ($formatBulan=='09') {
                    $Bulanpelaporan= "September";
                } if ($formatBulan=='10') {
                    $Bulanpelaporan= "Oktober";
                } if ($formatBulan=='11') {
                    $Bulanpelaporan= "November";
                } if ($formatBulan== '12') {
                    $Bulanpelaporan= "Desember";
                }
                $periodepelaporan= "".$Bulanpelaporan."||".$formatTahun."";
    // ========================== excel ==============================
        $tab.="<table>";
                $tab.="<tr>";
                    $tab.="<td></td>";
                $tab.="</tr>";
                $tab.="<tr>";
                    $tab.="<td></td>";
                $tab.="</tr>";
                $tab.="<tr>";
                    $tab.="<td colspan=2><img src=images/logo_bpjs.png></td>";
                    $tab.="<td colspan=8></td>";
                    $tab.="<td align=center><b>Formulir<br>Jamsostek<br>2a</b></td>";
                $tab.="</tr>";
                $tab.="<tr>";
                    $tab.="<td colspan=12 align=center><b style='font-size:20px;'>RINCIAN IURAN TENAGA KERJA BARU</b></td>";
                $tab.="</tr>";
                $tab.="<tr>";
                    $tab.="<td colspan=2>NPP:</td>";
                    $tab.="<td></td>";
                    $tab.="<td colspan=4>Nama Perusahaan</td>"; 
                    $tab.="<td></td>";
                    $tab.="<td>Nama Unit Kerja</td>";
                    $tab.="<td></td>";
                    $tab.="<td colspan=2>Periode Pelaporan</td>";
                $tab.="</tr>";
                $tab.="<tr>";
                    $tab.="<td colspan=2></td>";
                    $tab.="<td></td>";
                    $tab.="<td colspan=4>".$_SESSION['org']['namaorganisasi']."</td>";
                    $tab.="<td></td>";
                    $tab.="<td>".$divisi."</td>";
                    $tab.="<td></td>";
                    $tab.="<td align=left>".$Bulanpelaporan."</td>";
                    $tab.="<td align=left>".$formatTahun."</td>";
                $tab.="</tr>";
                $tab.="<tr>";
                    $tab.="<td colspan=10></td>";
                    $tab.="<td><h5 style='font-size:10px;'>Bulan</h5></td>";
                    $tab.="<td><h5 style='font-size:10px;'>Tahun</h5></td>";
                $tab.="</tr>";
                $tab.="<tr>";
                    $tab.="<td></td>";
                $tab.="</tr>";
        $tab.="</table>";
    }
    //=============== menampilkan header table =================
    $tab.="<table cellspacing=1 cellpadding=1 border=".$border1." class=sortable>";
        $tab.="<thead class=rowheader>";
            $tab.="<tr>";
                $tab.="<td align=center>No</td>";
                $tab.="<td align=center>Nomor KPJ</td>";
                $tab.="<td align=center>N I K</td>";
                $tab.="<td align=center>Nama Tenaga Kerja</td>";
                $tab.="<td align=center>Tgl.Lahir<br>(tgl/bln/thn)</td>";
                $tab.="<td align=center>Status<br>(L/K)</td>";
                $tab.="<td align=center>Data Upah<br>(Rp.)</td>";
                $tab.="<td align=center>Iuran JKK<br>0.54%/0.89%</td>";
                $tab.="<td align=center>Iuran JKM<br>0.30%</td>";
                $tab.="<td align=center>Iuran JHT TK<br>2%</td>";
                $tab.="<td align=center>Iuran JHT PRSH<br>3.7%</td>";
                $tab.="<td align=center>Total Iuran</td>";
                $tab.="<td align=center>KET</td>";
            $tab.="</tr>";
        $tab.="</thead>";
        $tab.="<tbody>";
    // =============== isi table ===============
    $brsPerdiv=mysql_num_rows($qPerdiv);
    if ($brsPerdiv!=0) {
        while ($rPerdiv=mysql_fetch_assoc($qPerdiv)) {
            $no+=1;
            // ========== persen iuran ===================
            $gaji = $rPerdiv['jumlah'];
            $iuranJHTTK     = ($gaji*2)/100;
            $iuranJHTPRSH   = ($gaji*3.7) / 100;
            $totalIuran     = $iuranJHTTK + $iuranJHTPRSH;
            // ========== menampilkan if status perkawinan===
            if ($rPerdiv['statusperkawinan']== 'Menikah') {
                $status= "M";
            } else {
                $status= "L";
            }
            // ======= merapikan tanggal lahir======
            $tanggal= substr($rPerdiv['tanggallahir'], 8,2);
            $bulan= substr($rPerdiv['tanggallahir'], 5,2);
            $tahun= substr($rPerdiv['tanggallahir'], 0,4);
            $tanggallahir= "".$tanggal."/".$bulan."/".$tahun."";
            // ======= menampilkan isi table ======
            $tab.="<tr class=rowcontent>";
                $tab.="<td align=right>".$no."</td>";
                $tab.="<td align=right>".$rPerdiv['jms']."</td>";
                $tab.="<td align=right>'".$rPerdiv['nik']."</td>";
                $tab.="<td align=left>".$rPerdiv['namakaryawan']."</td>";
                $tab.="<td align=right>".$tanggallahir."</td>";
                $tab.="<td align=center>".$status."</td>";
                $tab.="<td align=right>".number_format($gaji,2)."</td>";
                $tab.="<td><!-- Iuran JKK --></td>";
                $tab.="<td><!-- Iuran JKM --></td>";
                $tab.="<td align=right>".number_format($iuranJHTTK,2)."</td>";
                $tab.="<td align=right>".number_format($iuranJHTPRSH,2)."</td>";
                $tab.="<td align=right>".number_format($totalIuran,2)."</td>";
                $tab.="<td><!-- Keterangan --></td>";
            $tab.="</tr>";
        }
    } else {
            $tab.="<tr class=rowcontent>";
                $tab.="<td>".$_SESSION['lang']['dataempty']."</td>";
            $tab.="</tr>";
    } 
            $tab.="<tr>";
                $tab.="<td align=center colspan=5>JUMLAH SELURUHNYA</td>";
                $tab.="<td></td>";
                $tab.="<td></td>";
                $tab.="<td></td>";
                $tab.="<td></td>";
                $tab.="<td></td>";
                $tab.="<td></td>";
                $tab.="<td></td>";
                $tab.="<td></td>";
            $tab.="</tr>";
        $tab.="</tbody>";
    $tab.="</table>";
    $tab.="<table>";
        $tab.="<tr>";
            $tab.="<td colspan=12></td>";
        $tab.="</tr>";
        $tab.="<tr>";
            $tab.="<td colspan=8><i style='font-size:10px'>Isian formulir ini dapat disampaikan kepada Jamsostek dalam bentuk media elektronik (softcopy) ataupun hasil cetakan dari<br>
                   sistem pengganjian perusahaan peserta yang bersangkutan, dengan aturan/format yang sesuai degnan ketentuan Jamsostek</></td>";
            $tab.="<td colspan=2></td>";
            if ($_SESSION['empl']['regional']=='KALIMANTAN')
                $tab.="<td colspan=2><h6 style='font-size:12px;'>Nunukan, ".date("d F Y")."</h6></td>";
            else
                $tab.="<td colspan=2><h6 style='font-size:12px;'>Modo, ".date("d F Y")."</h6></td>";
        $tab.="</tr>";
        $tab.="<tr>";
            $tab.="<td colspan=8></td>";
        $tab.="</tr>";
        $tab.="<tr>";
            $tab.="<td colspan=8></td>";
        $tab.="</tr>";
        $tab.="<tr>";
            $tab.="<td colspan=8></td>";
        $tab.="</tr>";
        $tab.="<tr>";
            $tab.="<td colspan=10></td>";
            $tab.="<td colspan=2 align=center><h6 style='font-size:12px;'><u>".$rHrd[0]['namakaryawan']."</u><br>".$rHrd[0]['pangkat']." ".$rHrd[0]['bagian']."</h6></td>";
        $tab.="</tr>";
    $tab.="</table>";
// ========================= end of preview =================
    switch ($proses) {
        case 'preview':
            echo $tab;
            break;

        case 'excel':
                $wktu=date("Hms");
                $nop_="RekapIuran_Jamsostek_".$periode."_".$divisi."_".$wktu;
                if(strlen( $tab)>0) {
                    if ($handle = opendir('tempExcel')) {
                        while (false !== ($file = readdir($handle))) {
                            if ($file != "." && $file != "..") {
                                @unlink('tempExcel/'.$file);
                            }
                        }   
                        closedir($handle);
                    }
                    $handle=fopen("tempExcel/".$nop_.".xls",'w');
                    if(!fwrite($handle, $tab)) {
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
        default:
            break;
    }
?>