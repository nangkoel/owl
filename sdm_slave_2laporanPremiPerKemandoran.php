<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

// get post =========================================================================
$proses=$_GET['proses'];
$periode=$_POST['periode'];
$lksiTgs=$_SESSION['empl']['lokasitugas'];
$kdOrg=$_POST['kdOrg'];
if($periode=='')$periode=$_GET['periode'];
if($kdOrg=='')$kdOrg=$_GET['kdOrg'];
if($kdOrg=='')$kdOrg=$_SESSION['empl']['lokasitugas'];

#ambil tanggal periode gaji
    $lok=substr($kdOrg,0,4); 
    $sDatez = "select tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji where periode = '".$periode."' and kodeorg= '".$lok."'";
    $qDatez=mysql_query($sDatez) or die(mysql_error($conn));
    while($rDatez=mysql_fetch_assoc($qDatez))
    {
            $tanggalMulai=$rDatez['tanggalmulai'];
            $tanggalSampai=$rDatez['tanggalsampai'];
    }
#ambil semua nama karyawan unit bersangkuran
 $str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$lok."'";
 $res=mysql_query($str);
 $nama=Array();
 while($bar=mysql_fetch_object($res))
 {
     $nama[$bar->karyawanid]=$bar->namakaryawan;
 }
if(($_SESSION['empl']['tipelokasitugas']=='HOLDING')||($_SESSION['empl']['tipelokasitugas']=='KANWIL'))
{
    $str="select a.notransaksi,b.tanggal,sum(a.upahpremi) as premi, sum(a.hasilkerja) as jjg, sum(a.rupiahpenalty)as penalty,
    sum(hasilkerjakg) as kg, b.nikmandor as mandor,b.nikmandor1 as mandor1, b.nikasisten as kraniproduksi, b.keranimuat
    FROM ".$dbname.".kebun_prestasi a
    left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi
    where a.notransaksi like '%PNN%' and b.tanggal between '".$tanggalMulai."' and '".$tanggalSampai."' 
    and a.notransaksi like '%".$lok."%'
    group by a.notransaksi";    
}
else
    {   
    $str="select a.notransaksi,b.tanggal,sum(a.upahpremi) as premi, sum(a.hasilkerja) as jjg, sum(a.rupiahpenalty)as penalty,
    sum(hasilkerjakg) as kg,b.nikmandor as mandor,b.nikmandor1 as mandor1, b.nikasisten as kraniproduksi, b.keranimuat
    FROM ".$dbname.".kebun_prestasi a
    left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi
    where a.notransaksi like '%PNN%' and b.tanggal between '".$tanggalMulai."' and '".$tanggalSampai."' 
    and a.notransaksi like '%".$lok."%' and a.nik in(
        select karyawanid from ".$dbname.".datakaryawan where subbagian='".$kdOrg."' and lokasitugas='".$lok."'
        )
    group by a.notransaksi";
}    
$res=mysql_query($str);
$brd=0;
$bg="";
if($proses=='exce')
{
  $brd=1;  
   $bg=" bgcolor=#DEDEDE";
}
#generate header
$stream="Premi per No.Transaksi:";
$stream.="<table class=sortable cellspacing=1 border=".$brd.">
          <thead>
          <tr class=rowheader>
            <td ".$bg.">".$_SESSION['lang']['nomor']."</td>
            <td ".$bg.">".$_SESSION['lang']['notransaksi']."</td>    
            <td ".$bg.">".$_SESSION['lang']['tanggal']."</td>
            <td ".$bg.">".$_SESSION['lang']['mandor']."</td>  
            <td ".$bg.">".$_SESSION['lang']['nikmandor1']."</td> 
            <td ".$bg.">".$_SESSION['lang']['keraniafdeling']."</td>
            <td ".$bg.">".$_SESSION['lang']['keranimuat']."</td>   
            <td ".$bg.">".$_SESSION['lang']['jmlhTandan']."</td>
            <td ".$bg.">".$_SESSION['lang']['upahpremi']."</td>
            <td ".$bg.">".$_SESSION['lang']['rupiahpenalty']."</td>   
            <td ".$bg.">".$_SESSION['lang']['hasilkerjakg']."</td>    
          </tr>
          </thead>
          <tbody>
          ";
$no=0;
$ttandan=0;
$tpremi=0;
$tpenalty=0;
$tkg=0;
while($bar=mysql_fetch_object($res))
{
    $no+=1;
    $stream.="  <tr class=rowcontent>
                <td>".$no."</td>
                <td>".$bar->notransaksi."</td>    
                <td>".tanggalnormal($bar->tanggal)."</td>
                <td>".$nama[$bar->mandor]."</td>  
                <td>".$nama[$bar->mandor1]."</td> 
                <td>".$nama[$bar->kraniproduksi]."</td>
                <td>".$nama[$bar->keranimuat]."</td>   
                <td align=right>".$bar->jjg."</td> 
                <td align=right>".number_format($bar->premi)."</td>
                <td align=right>".number_format($bar->penalty)."</td>   
                <td align=right>".number_format($bar->kg)."</td>    
              </tr>"; 
    $ttandan+=$bar->jjg;
    $tpremi +=$bar->premi;
    $tpenalty+=$bar->penalty;
    $tkg+=$bar->kg;
}  
$stream.="</tbody>
          <tfoot>
          <tr class=rowcontent>
             <td colspan=7>Total</td>
             <td align=right>".$ttandan."</td>
             <td align=right>".number_format($tpremi)."</td>
             <td align=right>".number_format($tpenalty)."</td>   
             <td align=right>".number_format($tkg)."</td>     
          </tr>
          </tfoot>
          </table>";

switch($proses)
{
	case'preview':
          echo $stream;
	break;
	case 'excel':
            $nop_="Laporan_premi_per_kemandoran_".$kdOrg."_".$periode;
            if(strlen($stream)>0)
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
                if(!fwrite($handle,$stream))
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
	case'pdf':
        echo"belum tersedia"    ;
        
        break;
}    
?>