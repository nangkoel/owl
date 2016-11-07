<?php
//@Copy nangkoelframework

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
$_POST['pt']==''?$namapt=$_GET['pt']:$namapt=$_POST['pt'];
if($proses!='getDiv'){
// ============ berhasil execute dan peringatan 
    if ($periode=='') {
        exit("Error:".$_SESSION['lang']['periode']." Tidak boleh kosong");
    }
}
    $thn=explode("-", $periode);
    if($divisi=='') {
        $wrd = "and a.kodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$namapt."')";
        $wrdG = "and b.lokasitugas in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$namapt."')";
    } else {
        $wrd = "and a.kodeorg='".$divisi."'";
        $wrdG = "and b.lokasitugas='".$divisi."'";
    }


$wrt="periode='".$periode."' and kodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$namapt."')";
#tgl cut off
if(substr($periode,0,4)=='2014'){
    if($_SESSION['empl']['regional']=='SULAWESI'){
        if($tpKary==3){
            #bentuk tanggal cut off lalu plus satu dan tanggal cut off bulan berjalan
            $optTglMulai=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tanggalmulai', $wrt);
            $tglKmrn=nambahHari($optTglMulai[$periode],1,0);
            $wrt2="periode='".substr($tglKmrn,0,7)."' and kodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$ptId."')";
            $tglCutoffLalu=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tglcutoff', $wrt2);
            $tglKmrn=nambahHari($tglCutoffLalu[substr($tglKmrn,0,7)],1,1);  
            $optTglCutoff=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tglcutoff', $wrt);
        }else{
            $optTglCutoff=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tanggalsampai', $wrt);
            $optTglMulai=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tanggalmulai', $wrt);
            $tglKmrn=$optTglMulai[$periode];
        }   
    }else{
        $optTglCutoff=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tanggalsampai', $wrt);
        $optTglMulai=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tanggalmulai', $wrt);
        $tglKmrn=$optTglMulai[$periode];
    }
}else{
    #bentuk tanggal cut off lalu plus satu dan tanggal cut off bulan berjalan,setelah 2015 menggunakan cut off
    $optTglMulai=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tanggalmulai', $wrt);
    $tglKmrn=nambahHari($optTglMulai[$periode],1,0);
    $wrt2="periode='".substr($tglKmrn,0,7)."' and kodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$namapt."')";
    $tglCutoffLalu=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tglcutoff', $wrt2);
    $tglKmrn=nambahHari($tglCutoffLalu[substr($tglKmrn,0,7)],1,1);  
    $optTglCutoff=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tglcutoff', $wrt);
}

// ======== select data join periode & divisi == 
$tgl = $periode."-01";
$tglsebelumnya = nambahHari($tgl, 1,0);
$sPerdiv = "select distinct a.*,b.darikodeorg,b.kekodeorg
            from ".$dbname.".datakaryawan a
            left join ".$dbname.".sdm_riwayatjabatan b on a.karyawanid=b.karyawanid
            where lokasitugas in ( select kodeorganisasi from ".$dbname.".organisasi where induk = '".$namapt."')
            and LEFT(tanggalmasuk, 7)='".substr($tgl, 0,7)."' or LEFT(b.mulaiberlaku, 7)='".substr($tgl, 0,7)."'
            and b.tipesk='Mutasi'";
$qPerdiv = mysql_query($sPerdiv) or die(mysql_error($conn));

    
    // ====
    $border = 0;
    $border1 = 1;
    $border2 = 2;
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
                    $tab.="<td colspan=2>Place LOGO BPJS Here</td>";
                    $tab.="<td colspan=8></td>";
                    $tab.="<td align=center><b>Formulir<br>Jamsostek<br>2a</b></td>";
                $tab.="</tr>";
                $tab.="<tr>";
                    $tab.="<td colspan=12 align=center><b style='font-size:20px;'>RINCIAN IURAN TENAGA KERJA BARU & PERHITUNGAN JKK & JKM</b></td>";
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
            $filUpah    = "idkomponen=1 and karyawanid='".$rPerdiv['karyawanid']."'";
            $upah   = makeOption($dbname, 'sdm_5gajipokok','karyawanid,jumlah',$filUpah);
            if((!is_null($rPerdiv['darikodeorg']))&&(!is_null($rPerdiv['kekodeorg']))){
            //(()&&)){
                $whrInd="kodeorganisasi='".$rPerdiv['darikodeorg']."'";
                $whrInd2="kodeorganisasi='".$rPerdiv['kekodeorg']."'";
                $induk   = makeOption($dbname, 'organisasi','kodeorganisasi,induk',$whrInd);
                $induk2   = makeOption($dbname, 'organisasi','kodeorganisasi,induk',$whrInd2);

                /*if($induk[$rPerdiv['darikodeorg']]==$induk2[$rPerdiv['kekodeorg']]){
                    continue;
                }elseif($rPerdiv2['lokasitugas']!=$induk[$rPerdiv['darikodeorg']]){
                    continue;
                }*/
                if ($induk[$rPerdiv['darikodeorg']]==$namapt) {
                      # code...
                    continue;
                  }  
                if ($namapt!=$induk2[$rPerdiv['kekodeorg']]) {
                    # code...
                    continue;
                }
                /*elseif($induk[$rPerdiv['darikodeorg']]==$induk2[$rPerdiv['kekodeorg']]){
                    continue;
                }elseif($rPerdiv2['lokasitugas']!=$induk[$rPerdiv['darikodeorg']]){
                    continue;
                }*/
            }
            
            //..
            $no+=1;

            $gaji = $upah[$rPerdiv['karyawanid']];
            $totalGaji += $gaji;

            // ========== persen iuran ===================
            $iuranJHTTK     = ($gaji*2)/100;
            $iuranJHTPRSH   = ($gaji*3.7) / 100;
            $iuranJKK       = ($gaji*0.54) /100;
            $iuranJKM       = ($gaji*0.30) / 100;
            $totalIuran     = $iuranJHTTK + $iuranJHTPRSH + $iuranJKK + $iuranJKM;
            $totalJumlah += $totalIuran;
            $totalJHTTK  += $iuranJHTTK;
            $totalJHTPRSH+= $iuranJHTPRSH;
            $totalJKK += $iuranJKK;
            $totalJKM += $iuranJKM;
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
                $tab.="<td align=right>".$rPerdiv['nik']."</td>";
                $tab.="<td align=left>".$rPerdiv['namakaryawan']."</td>";
                $tab.="<td align=right>".$tanggallahir."</td>";
                $tab.="<td align=center>".$status."</td>";
                $tab.="<td align=right>".number_format($gaji,0)."</td>";
                $tab.="<td align=right>".number_format($iuranJKK,0)."</td>";
                $tab.="<td align=right>".number_format($iuranJKM,0)."</td>";
                $tab.="<td align=right>".number_format($iuranJHTTK,0)."</td>";
                $tab.="<td align=right>".number_format($iuranJHTPRSH,0)."</td>";
                $tab.="<td align=right>".number_format($totalIuran,0)."</td>";
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
                $tab.="<td align=right>".number_format($totalGaji,0)."</td>";
                $tab.="<td align=right>".number_format($totalJKK,0)."</td>";
                $tab.="<td align=right>".number_format($totalJKM,0)."</td>";
                $tab.="<td align=right>".number_format($totalJHTTK,0)."</td>";
                $tab.="<td align=right>".number_format($totalJHTPRSH,0)."</td>";
                $tab.="<td align=right>".number_format($totalJumlah,0)."</td>";
                $tab.="<td></td>";
            $tab.="</tr>";
        $tab.="</tbody>";
    $tab.="</table>";
    $tab.="<table>";
        $tab.="<tr>";
            $tab.="<td colspan=12></td>";
        $tab.="</tr>";
        $tab.="<tr>";
            $tab.="<td colspan=8><i style='font-size:10px'>Isian formulir ini dapat disampaikan kepada Jamsostek dalam bentuk media elektronik (softcopy) ataupun hasil cetakan dari</></td>";
            $tab.="<td colspan=2></td>";
            $tab.="<td colspan=2><h6 style='font-size:12px;'>Nunukan, ".date("d F Y")."</h6></td>";
        $tab.="</tr>";
        $tab.="<tr>";
            $tab.="<td colspan=8><i style='font-size:10px'>sistem pengganjian perusahaan peserta yang bersangkutan, dengan aturan/format yang sesuai degnan ketentuan Jamsostek</i></td>";
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
            $tab.="<td colspan=3>_____________________</td>";
        $tab.="</tr>";
        $tab.="<tr>";
            $tab.="<td colspan=10></td>";
            $tab.="<td colspan=2><h6 style='font-size:12px;'>Manager HRD</h6></td>";
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