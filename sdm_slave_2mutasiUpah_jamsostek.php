<?php
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
    #list fiter data
    if($divisi=='') {
        $wrd = "and a.kodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$namapt."')";
        $wrdG = "and a.kodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$namapt."')";
        $whrdt="kodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$namapt."')";
        $whrMas="and kodeorganisasi='".$namapt."'";
        $whrTgs="kodeorganisasi='".$namapt."'";
        $whrdt2=" and a.darikodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$namapt."')";
    } else {
        $wrd = "and a.kodeorg='".$divisi."'";
        $wrdG = "and a.kodeorg='".$divisi."'";
        $whrdt="kodeorg='".$divisi."'";
        $whrMas="and lokasitugas='".$divisi."'";
        $whrTgs="lokasitugas='".$divisi."'";
        $whrdt2=" and a.darikodeorg='".$divisi."'";
    }

#ambil master gaji yang berlaku dan di bagi 25 agar menjadi UMR/Hari
$sMasGaji="select a.karyawanid,jumlah from ".$dbname.".sdm_5gajipokok a
            left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
           where tahun='".substr($periode,0,4)."' and idkomponen=1 ".$whrMas."";
$qMasGaji=mysql_query($sMasGaji) or die(mysql_error($conn));
while($rMasGaji=mysql_fetch_assoc($qMasGaji)){
    $gajiHarian[$rMasGaji['karyawanid']]=$rMasGaji['jumlah'];
}


// ======== select data join periode & divisi == 
    $tgl = $periode."-01";
    $tglsebelumnya = nambahHari($tgl, 1,0);
   
    $lstDt=array();
    $tgl = $periode."-01";
    $tglsebelumnya = nambahHari($tgl, 1,0);
    #gaji periode yang terpilih
    $sGaji = "select jumlah,a.karyawanid,b.namakaryawan,b.tipekaryawan,b.jms,b.nik 
                from ".$dbname.".sdm_gaji a 
                left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                where idkomponen=1 
                and a.periodegaji='".$periode."'
                ".$wrdG."
                group by a.karyawanid";
    $qGaji = mysql_query($sGaji) or die(mysql_error());
    while ($rGaji = mysql_fetch_assoc($qGaji)) {
        $dtGapok[$rGaji['karyawanid']]=$rGaji['jumlah'];
        $lstDt[$rGaji['karyawanid']]=$rGaji['karyawanid'];
        $lstNmKar[$rGaji['karyawanid']]=$rGaji['namakaryawan'];
        $lstJmsKar[$rGaji['karyawanid']]=$rGaji['jms'];
        $lstNikKar[$rGaji['karyawanid']]=$rGaji['nik'];
        $lstTpKar[$rGaji['karyawanid']]=$rGaji['tipekaryawan'];
    }
    // ========= query gaji bulan lalu
    $tgl = $periode."-01";
    $tglsebelumnya = nambahHari($tgl, 1,0);
    
        
    #ambil hk bulan lalu
    $sHKlalu="select * from ".$dbname.".sdm_hkbulanan  where ".$whrdt." and periode='".substr($tglsebelumnya, 0,7)."'";
    //echo $sHKlalu;
    $qHklalu=mysql_query($sHKlalu) or die(mysql_error($conn));
    while($rHklalu=mysql_fetch_assoc($qHklalu)){
        $hkKerja[$rHklalu['karyawanid']]=$rHklalu['hk'];
    }
    #mengambil gaji bulan lalu
    $gajilalu = "select jumlah,a.karyawanid,b.namakaryawan,b.tipekaryawan,b.jms,b.nik  
                from ".$dbname.".sdm_gaji a 
                left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
                where idkomponen=1
                ".$wrdG."
                and a.periodegaji='".substr($tglsebelumnya,0,7)."' group by a.karyawanid";
    //echo $gajilalu;
    $qGajilalu = mysql_query($gajilalu) or die(mysql_error());
    while($rGajilalu = mysql_fetch_assoc($qGajilalu)){
        $gjLalu[$rGajilalu['karyawanid']]=$rGajilalu['jumlah'];
        if(empty($lstDt[$gjLalu['karyawanid']])){
            $lstDt[$gjLalu['karyawanid']]=$gjLalu['karyawanid'];
            $lstNmKar[$gjLalu['karyawanid']]=$gjLalu['namakaryawan'];
            $lstJmsKar[$gjLalu['karyawanid']]=$gjLalu['jms'];
            $lstNikKar[$gjLalu['karyawanid']]=$gjLalu['nik'];
            $lstTpKar[$gjLalu['karyawanid']]=$gjLalu['tipekaryawan'];    
        }
    }
    #jika ada karyawan mutasi di irisan bulan
    $sDtAdd="select a.karyawanid,a.darikodeorg,a.kekodeorg,b.namakaryawan,b.tipekaryawan,b.jms,b.nik from ".$dbname.".sdm_riwayatjabatan a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
             where LEFT(a.mulaiberlaku, 7)>='".$periode."' and LEFT(a.mulaiberlaku, 7)<='".$periode."' and left(a.mulaiberlaku,4)='".substr($periode,0,4)."' and a.tipesk='Mutasi' ".$whrdt2."";    
    $qDtAdd=  mysql_query($sDtAdd) or die(mysql_error($conn));
    while($rDtAdd=  mysql_fetch_assoc($qDtAdd)){
        if(empty($lstDt[$rDtAdd['karyawanid']])){
            $whrGaji="karyawanid='".$rDtAdd['karyawanid']."' and idkomponen=1 and tahun='".substr($periode,0,4)."'";
            $whrGajiPrd="karyawanid='".$rDtAdd['karyawanid']."' and idkomponen=1 and periodegaji='".$periode."'";
            $whrGajiPrdLalu="karyawanid='".$rDtAdd['karyawanid']."' and idkomponen=1 and periodegaji='".substr($tglsebelumnya, 0,7)."'";
            $optGaji=  makeOption($dbname, 'sdm_gaji', 'karyawanid,jumlah', $whrGajiPrd);
            $optGajiLalu=  makeOption($dbname, 'sdm_gaji', 'karyawanid,jumlah', $whrGajiPrdLalu);
            $optGajiMs=  makeOption($dbname, 'sdm_5gajipokok', 'karyawanid,jumlah', $whrGaji);
            $lstDt[$rDtAdd['karyawanid']]=$rDtAdd['karyawanid'];
            $lstNmKar[$rDtAdd['karyawanid']]=$rDtAdd['namakaryawan'];
            $lstJmsKar[$rDtAdd['karyawanid']]=$rDtAdd['jms'];
            $lstNikKar[$rDtAdd['karyawanid']]=$rDtAdd['nik'];
            $lstTpKar[$rDtAdd['karyawanid']]=$rDtAdd['tipekaryawan'];
            $lstGjKar[$rDtAdd['karyawanid']]=$optGaji[$rDtAdd['karyawanid']];
            $lstGjLaluKar[$rDtAdd['karyawanid']]=$optGajiLalu[$rDtAdd['karyawanid']];
            $lstMsGjKar[$rDtAdd['karyawanid']]=$optGajiMs[$rDtAdd['karyawanid']];
        }
    }
    // ====
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
                    $tab.="<td colspan=11 align=center><b style='font-size:20px;'>DAFTAR MUTASI UPAH</b></td>";
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
    
    // ========== persen iuran ===================
                

    //=============== menampilkan header table =================
    $tab.="<table cellspacing=1 cellpadding=1 border=".$border1." class=sortable>";
        $tab.="<thead class=rowheader>";
            $tab.="<tr>";
                $tab.="<td align=center>No</td>";
                $tab.="<td align=center>N I K</td>";
                $tab.="<td align=center>Nomor KPJ</td>";
                $tab.="<td align=center>Nama Tenaga Kerja</td>";
                $tab.="<td align=center>Upah Bulan Lalu<br>(Rp.)</td>";
                $tab.="<td align=center>Upah Bulan Ini<br>(Rp.)</td>";
                $tab.="<td align=center> SELISIH (+/-) </td>";
            $tab.="</tr>";
        $tab.="</thead>";
        $tab.="<tbody>";
    // =============== isi table ===============
    //$brsPerdiv=mysql_num_rows($qPerdiv);
       if(!empty($lstDt)){
                foreach($lstDt as $karyId){
                    if($dtGapok[$karyId]==''){
                        $dtGapok[$karyId]=$lstGjKar[$karyId];    
                    }
                    if($gjLalu[$karyId]==''){
                        $gjLalu[$karyId]=$lstGjLaluKar[$karyId];
                    }
                    if($lstTpKar[$karyId]==4){
                        if($dtGapok[$karyId]>$lstMsGjKar[$karyId]){
                           $dtGapok[$karyId]= $lstMsGjKar[$karyId];
                        }
                        if($hkKerja[$karyId]<25){
                            continue;
                        }
                    }
                    $gaji = $dtGapok[$karyId];
                    $selisihgaji= $dtGapok[$karyId] - $gjLalu[$karyId];

                    $totalgajilalu += $gjLalu[$karyId];
                    $totalgaji += $dtGapok[$karyId];
                    $totalselisih += $selisihgaji;
                    if ($selisihgaji > 0) {
                        $no+=1;
                        $tab.="<tr class=rowcontent>";
                        $tab.="<td align=right>".$no."</td>";
                        $tab.="<td align=left>'".$lstNikKar[$karyId]."</td>";
                        $tab.="<td align=left>".$lstJmsKar[$karyId]."</td>";
                        $tab.="<td align=left>".$lstNmKar[$karyId]."</td>";
                        $tab.="<td align=right>".number_format($gjLalu[$karyId],0)."</td>";
                        $tab.="<td align=right>".number_format($dtGapok[$karyId],0)."</td>";
                        $tab.="<td align=right>".number_format($selisihgaji,0)."</td>";
                        $tab.="</tr>";
                    } 
                }
                $tab.="<tr>";
                $tab.="<td align=left colspan=3>JUMLAH SELURUHNYA</td>";
                $tab.="<td></td>";
                $tab.="<td align=right>".number_format($totalgajilalu,2)."</td>";
                $tab.="<td align=right>".number_format($totalgaji,2)."</td>";
                $tab.="<td align=right>".number_format($totalselisih,2)."</td>";
                $tab.="</tr>";
        }else{
            $tab.="<tr>";
                $tab.="<td align=left colspan=5>Data Kosong</td>";
            $tab.="</tr>";
        }                    

    // ..        
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