<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
?>
<script language=javascript1.2 src="js/generic.js"></script>
<script language=javascript1.2 src="js/sdm_2rekapabsen.js"></script>
<link rel=stylesheet type='text/css' href='style/generic.css'>
<?php
   
$karyawanid=$_GET['karyawanid'];
$tanggal=$_GET['tanggal'];

$str="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$karyawanid."'"; 
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $namakaryawan[$bar->karyawanid]=$bar->namakaryawan;
}

$strz="select notransaksi, tanggal, karyawanid,(upahpremi-rupiahpenalty) as upahpremi from ".$dbname.".kebun_prestasi_vw
     where tanggal like '".$tanggal."%' and karyawanid = '".$karyawanid."'
     order by notransaksi";   
$resz=mysql_query($strz);
while($barz=mysql_fetch_object($resz))
{
	$dibuat=makeOption($dbname,'kebun_aktifitas','notransaksi,updateby',"notransaksi='".$barz->notransaksi."'");
    $notran['BKM:'.$barz->notransaksi].='BKM:'.$barz->notransaksi;
    $premi['BKM:'.$barz->notransaksi]=$barz->upahpremi;
}
//echo $strz.'<br>';

//ambil data di perawatan
$strx="select notransaksi,karyawanid,tanggal,(insentif) as upahpremi from ".$dbname.".kebun_kehadiran_vw
     where tanggal like '".$tanggal."%' and karyawanid = '".$karyawanid."'
     order by notransaksi";   
$resx=mysql_query($strx);
while($barx=mysql_fetch_object($resx))
{
	$dibuat=makeOption($dbname,'kebun_aktifitas','notransaksi,updateby',"notransaksi='".$barx->notransaksi."'");
    $notran['BKM:'.$barx->notransaksi]='BKM:'.$barx->notransaksi;
    $premi['BKM:'.$barx->notransaksi]=$barx->upahpremi;
}
//echo $strx.'<br>';

//ambil data kemandoran
$stry="select karyawanid,tanggal,(premiinput) as upahpremi from ".$dbname.".kebun_premikemandoran 
     where tanggal like '".$tanggal."%' and karyawanid = '".$karyawanid."'
     order by tanggal";   
$resy=mysql_query($stry);
while($bary=mysql_fetch_object($resy))
{
	
    $notran['PREMI KEMANDORAN:'.$bary->tanggal]='PREMI KEMANDORAN:'.$bary->tanggal;
    $premi['PREMI KEMANDORAN:'.$bary->tanggal]=$bary->upahpremi;
}

//premi traksi
$strv="select notransaksi,idkaryawan as karyawanid,tanggal,(premi-penalty) as upahpremi from ".$dbname.".vhc_runhk 
     where tanggal like '".$tanggal."%' and idkaryawan = '".$karyawanid."'
     order by notransaksi";  
$resv=mysql_query($strv);
while($barv=mysql_fetch_object($resv))
{
	
	$dibuat=makeOption($dbname,'vhc_runht','notransaksi,updateby',"notransaksi='".$barv->notransaksi."'");
	
	//$not[$barv->notransaksi]=$barv->notransaksi;

	 $notran['TRAKSI:'.$barv->notransaksi]='TRAKSI:'.$barv->notransaksi;
    $notran['TRAKSI:'.$barv->notransaksi]='TRAKSI:'.$barv->notransaksi;
    $premi['TRAKSI:'.$barv->notransaksi]=$barv->upahpremi;
}




//echo $strv;
//
//echo "<pre>";
//print_r($notran);
//print_r($premi);
//echo "</pre>";

//=================================================
//echo"<fieldset><legend>Print Excel</legend>
//     <img onclick=\"detailExcel(event,'pabrik_slave_2pengolahandetail.php?type=excel&tanggal=".$tanggal."&kodeorg=".$kodeorg."&periode_tahun=".$periode_tahun."&periode_bulan=".$periode_bulan."')\" src=images/excel.jpg class=resicon title='MS.Excel'>
//     </fieldset>"; 
if($_GET['type']!='excel')$stream="<table class=sortable border=0 cellspacing=1>"; //else
//$stream="<table class=sortable border=1 cellspacing=1>";
$stream.="
      <thead>
        <tr class=rowcontent>
          <td>Karyawan</td>
          <td>No. Transaksi</td>
          <td>Tanggal</td>
          <td>Premi</td>
		  <td>Dibuat Oleh</td>";
//		  if($_GET['type']!='excel')$stream.="<td>Browse</td>";
        $stream.="</tr>
      </thead>
      <tbody>";
        if(empty($notran)){
            $stream.="<tr class=rowcontent>";
            $stream.="<td colspan=4>Abensce</td>";
            $stream.="</tr>";
        }else{
            foreach($notran as $kyu){
				$not=explode(":",$kyu);
				$keNm=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',"karyawanid='".$dibuat[$not[1]]."'");
                $stream.="<tr class=rowcontent>";
                $stream.="<td align=left>".$namakaryawan[$karyawanid]."</td>";
                $stream.="<td align=left>".$kyu."</td>";
                $stream.="<td align=center>".$tanggal."</td>";
                $stream.="<td align=right>".number_format($premi[$kyu])."</td>";
				 $stream.="<td align=center>".$keNm[$dibuat[$not[1]]]."</td>";
                $stream.="</tr>";
            }
			$stream.="</tbody></table><br />";
			
			
			$stream.="<table class=sortable border=0 cellspacing=1>";
			$stream.=" <thead><tr class=rowcontent>
							<td>Kode Jenis pekerjaan</td>
							<td>Nama Jenis pekerjaan</td>
							<td>Alokasi Biaya</td>
							<td>Berat Muatan</td>	
							<td>Jumlah Rit</td>
							<td>Satuan</td>
							</tr></thead>";
			$a="select * from ".$dbname.".vhc_rundt where notransaksi='".$not[1]."'";
				$b=mysql_query($a) or die (mysql_error($conn));
				while($d=mysql_fetch_assoc($b))
				{
					$kenamakeg=makeOption($dbname,'vhc_kegiatan','kodekegiatan,namakegiatan',"kodekegiatan='".$d['jenispekerjaan']."'");
					$stream.="<tr class=rowcontent>
							<td>".$d['jenispekerjaan']."</td>
							<td>".$kenamakeg[$d['jenispekerjaan']]."</td>
							<td>".$d['alokasibiaya']."</td>
							<td>".$d['beratmuatan']."</td>
							<td>".$d['jumlahrit']."</td>
							<td>".$d['satuan']."</td>
							</tr>";
				}        
        }
//    $str="select * from ".$dbname.".pabrik_pengolahan
//              where tanggal = '".$tanggal."'";   
//    $res=mysql_query($str);
//    $no=0;
//    $tdebet=0;
//    $tkredit=0;
//    while($bar= mysql_fetch_object($res))
//    {
//        $no+=1;
//        $debet=0;
//        $kredit=0;
//        if($bar->jumlah>0)
//             $debet= $bar->jumlah;
//        else
//             $kredit= $bar->jumlah*-1;
//    
//    $stream.="<tr class=rowcontent>
//           <td align=right>".$no."</td>
//           <td>".tanggalnormal($bar->tanggal)."</td>    
//           <td align=right>".$bar->nopengolahan."</td>               
//           <td align=right>".$bar->shift."</td>               
//           <td align=right>".substr($bar->jammulai,0,5)."</td>               
//           <td align=right>".substr($bar->jamselesai,0,5)."</td>               
//           <td align=right>".$bar->jamdinasbruto."</td>               
//           <td align=right>".$bar->jamstagnasi."</td>               
//           <td align=right>".number_format($bar->jumlahlori)."</td>               
//           <td align=right>".number_format($bar->tbsdiolah)."</td>";
//   		  if($_GET['type']!='excel')$stream.="
//           <td><img onclick=\"parent.browsemesin(".$bar->nopengolahan.",'".$tanggal."','".$kodeorg."','".$periode_tahun."','".$periode_bulan."',event);\" title=\"Mesin\" class=\"resicon\" src=\"images/icons/joystick.png\">
//		       <img onclick=\"parent.browsebarang(".$bar->nopengolahan.",'".$tanggal."','".$kodeorg."','".$periode_tahun."','".$periode_bulan."',event);\" title=\"Barang\" class=\"resicon\" src=\"images/icons/box.png\"></td>";               
//         $stream.="</tr>";
//    } 
   $stream.="</table>";
//   if($_GET['type']=='excel')
//   {
//$nop_="Detail_pengolahan_".$kodeorg."_".$tanggal;
//        if(strlen($stream)>0)
//        {
//        if ($handle = opendir('tempExcel')) {
//            while (false !== ($file = readdir($handle))) {
//                if ($file != "." && $file != "..") {
//                    @unlink('tempExcel/'.$file);
//                }
//            }	
//           closedir($handle);
//        }
//         $handle=fopen("tempExcel/".$nop_.".xls",'w');
//         if(!fwrite($handle,$stream))
//         {
//          echo "<script language=javascript1.2>
//                parent.window.alert('Can't convert to excel format');
//                </script>";
//           exit;
//         }
//         else
//         {
//          echo "<script language=javascript1.2>
//                window.location='tempExcel/".$nop_.".xls';
//                </script>";
//         }
//        closedir($handle);
//        }       
//   }
//   else
   {
       echo $stream;
   }    
       
?>