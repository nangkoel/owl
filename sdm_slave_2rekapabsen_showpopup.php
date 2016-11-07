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
$namakaryawan=$_GET['namakaryawan'];
$tanggal=$_GET['tanggal'];
$notransaksi=$_GET['notransaksi'];

//ilangin __ paling belakang
$namakaryawan=substr($namakaryawan,0,-2);
$notransaksi=substr($notransaksi,0,-2);

//transform __ into spasi
$qwe=explode('__',$notransaksi);
$qwe2=explode('__',$namakaryawan);
foreach($qwe2 as $kyu2){
    $namakar.=$kyu2.' ';
}
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
          <td>Tanggal</td>";
//		  if($_GET['type']!='excel')$stream.="<td>Browse</td>";
        $stream.="</tr>
      </thead>
      <tbody>";
foreach($qwe as $kyu){
    $stream.="<tr class=rowcontent>";
    $stream.="<td align=left>".$namakar."</td>";
    $stream.="<td align=left>".$kyu."</td>";
    $stream.="<td align=center>".$tanggal."</td>";
    $stream.="</tr>";
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
   $stream.="</tbody></table>";
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