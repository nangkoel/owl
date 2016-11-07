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
        $wrd = "and kodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$namapt."')";
        $wrdG = "and b.lokasitugas in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$namapt."')";
    } else {
        $wrd = "and kodeorg='".$divisi."'";
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

$tgl = $periode."-01";
$tglsebelumnya = nambahHari($tgl, 1,0);
$sPerdiv = "select distinct a.*,b.darikodeorg,b.kekodeorg
            from ".$dbname.".datakaryawan a
            left join ".$dbname.".sdm_riwayatjabatan b on a.karyawanid=b.karyawanid
            where lokasitugas in ( select kodeorganisasi from ".$dbname.".organisasi where induk = '".$namapt."') and
            left(tanggalkeluar,4)='".substr($tgl,0,4)."' and tanggalkeluar<'".$tgl."' or LEFT(b.mulaiberlaku, 7)='".substr($tglsebelumnya, 0,7)."'
            and b.tipesk='Mutasi' order by namakaryawan asc";
$qPerdiv = mysql_query($sPerdiv) or die(mysql_error($conn));
//echo $sPerdiv;
    // ==== tampilan
    $border = 0;
    $border1 = 1;
    $border2 = 2;
// ========================= preview ==========
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
                    $tab.="<td colspan=3>Place LOGO BPJS here</td>";
                    $tab.="<td colspan=7></td>";
                    $tab.="<td align=center><b>Formulir<br>Jamsostek<br>2a</b></td>";
                $tab.="</tr>";
                $tab.="<tr>";
                    $tab.="<td colspan=11 align=center><b style='font-size:20px;'>DAFTAR TENAGA KERJA KELUAR</b></td>";
                $tab.="</tr>";
                $tab.="<tr>";
                	$tab.="<td></td>";
                    $tab.="<td colspan=2>NPP:</td>";
                    $tab.="<td colspan=3>Nama Perusahaan</td>"; 
                    $tab.="<td></td>";
                    $tab.="<td colspan=2>Sejak</td>";
                $tab.="</tr>";
                $tab.="<tr>";
                    $tab.="<td></td>";
                    $tab.="<td colspan=2><!-- NPP: --></td>";
                    $tab.="<td colspan=3>".$_SESSION['org']['namaorganisasi']."</td>";
                    $tab.="<td></td>";
                    $tab.="<td align=left>".$Bulanpelaporan."</td>";
                    $tab.="<td align=left>".$formatTahun."</td>";
                $tab.="</tr>";
                $tab.="<tr>";
                    $tab.="<td colspan=7></td>";
                    $tab.="<td><b style='font-size:10px;'>Bulan</b></td>";
                    $tab.="<td><b style='font-size:10px;'>Tahun</b></td>";
                $tab.="</tr>";
                $tab.="<tr>";
                    $tab.="<td colspan=3></td>";
                    $tab.="<td colspan=2>Nama Unit Kerja</td>";
                $tab.="</tr>";
                $tab.="<tr>";
                    $tab.="<td colspan=3></td>";
                    $tab.="<td colspan=2><!-- Nama Unit Kerja --></td>";
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
                $tab.="<td align=center colspan=2>N I K</td>";
                $tab.="<td align=center colspan=2>Nomor KPJ</td>";
                $tab.="<td align=center colspan=2>Nama Tenaga Kerja Keluar</td>";
                $tab.="<td align=center>Upah Bulan Lalu<br>(Rp.)</td>";
                $tab.="<td align=center colspan=3> Keterangan </td>";
            $tab.="</tr>";
        $tab.="</thead>";
        $tab.="<tbody>";
    // =============== isi table ===============
    $brsPerdiv=mysql_num_rows($qPerdiv);
    if ($brsPerdiv!=0) {
        while ($rPerdiv = mysql_fetch_assoc($qPerdiv)) {
            # code...
            $filUpah    = "idkomponen=1 and karyawanid='".$rPerdiv['karyawanid']."' and tahun='".substr($tglsebelumnya, 0,4)."'";
            $upahLalu   = makeOption($dbname, 'sdm_5gajipokok','karyawanid,jumlah',$filUpah);
            if((!is_null($rPerdiv['darikodeorg']))&&(!is_null($rPerdiv['kekodeorg']))){
                $whrInd="kodeorganisasi='".$rPerdiv['darikodeorg']."'";
                $whrInd2="kodeorganisasi='".$rPerdiv['kekodeorg']."'";
                $induk   = makeOption($dbname, 'organisasi','kodeorganisasi,induk',$whrInd);
                $induk2   = makeOption($dbname, 'organisasi','kodeorganisasi,induk',$whrInd2);

                if ($induk[$rPerdiv['darikodeorg']]!=$namapt) {
                      # code...
                    continue;
                  }  
                if ($namapt==$induk2[$rPerdiv['kekodeorg']]) {
                    # code...
                    continue;
                }
            }
            //..
            $no+=1;

            $gajiLalu = $upahLalu[$rPerdiv['karyawanid']];
            $totalGaji += $gajiLalu;

            //-- 

            // ======= menampilkan isi table ======
            $tab.="<tr class=rowcontent>";
                $tab.="<td align=right>".$no."</td>";

                $tab.="<td align=left colspan=2>'".$rPerdiv['nik']."</td>";
                $tab.="<td align=left colspan=2>".$rPerdiv['jms']."</td>";
                $tab.="<td align=left colspan=2>".$rPerdiv['namakaryawan']."</td>";
                $tab.="<td align=right>".number_format($gajiLalu,0)."</td>";
                $tab.="<td colspan=3><!-- Keterangan --></td>";
            $tab.="</tr>";
        }
    } else {
            $tab.="<tr class=rowcontent>";
                $tab.="<td>".$_SESSION['lang']['dataempty']."</td>";
            $tab.="</tr>";
    } 
            $tab.="<tr>";
                $tab.="<td align=left colspan=5>JUMLAH SELURUHNYA</td>";
                $tab.="<td colspan=2></td>";
                $tab.="<td align=right>".number_format($totalGaji,0)."</td>";
            $tab.="</tr>";
        $tab.="</tbody>";
    $tab.="</table>";
    $tab.="<table>";
        $tab.="<tr>";
            $tab.="<td colspan=11></td>";
        $tab.="</tr>";
        $tab.="<tr>";
            $tab.="<td colspan=11></td>";
        $tab.="</tr>";
        $tab.="<tr>";
            $tab.="<td colspan=7></td>";
            $tab.="<td><u>Nunukan</u></td>";
            $tab.="<td><u>".date("d F Y")."</u></td>";
        $tab.="</tr>";
        $tab.="<tr>";
            $tab.="<td colspan=7></td>";
            $tab.="<td>Kota</td>";
            $tab.="<td>Tanggal & Bulan</td>";
            $tab.="<td>Tahun</td>";
        $tab.="</tr>";
        $tab.="<tr>";
            $tab.="<td colspan=11></td>";
        $tab.="</tr>";
        $tab.="<tr>";
            $tab.="<td colspan=11></td>";
        $tab.="</tr>";
        $tab.="<tr>";
            $tab.="<td></td>";
            $tab.="<td colspan=3>________________________________</td>";
            $tab.="<td colspan=3></td>";
            $tab.="<td><u>Manager HRD      </u></td>";
        $tab.="</tr>";
        $tab.="<tr>";
            $tab.="<td></td>";
            $tab.="<td colspan=3>Nama dan Tanda Tangan Pimpinan Perusahaan</td>";
            $tab.="<td colspan=3></td>";
            $tab.="<td>Jabatan</td>";
        $tab.="</tr>";
        $tab.="<tr>";
            $tab.="<td colspan=11></td>";
        $tab.="</tr>";
        $tab.="<tr>";
        $tab.="<td></td>";
            $tab.="<td>Keterangan :</td>";
        $tab.="</tr>";
        $tab.="<tr>";
        	$tab.="<td></td>";
            $tab.="<td colspan=11>Isian formulir ini dapat disampaikan kepada Jamsostek dalam bentuk media elektronik (softcopy)</td>";
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
                        parent.window.alert('Can\'t convert to excel format');
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