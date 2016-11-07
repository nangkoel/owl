<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
?>
<script language=javascript1.2 src="js/generic.js"></script>
<script language=javascript1.2 src="js/pabrik_2pengolahan.js"></script>
<link rel=stylesheet type='text/css' href='style/generic.css'>
<?php
   
$notransaksi=$_GET['notransaksi'];
/*
$tanggal=$_GET['tanggal'];
$kodeorg=$_GET['kodeorg'];
$periode_tahun=$_GET['periode_tahun'];
$periode_bulan=$_GET['periode_bulan'];
$periode = $periode_tahun.'-'.addZero($periode_bulan,2);

*///=================================================
$str="select a.karyawanid as karyawanid, a.namakaryawan as namakaryawan, b.namajabatan as namajabatan from ".$dbname.".datakaryawan a
left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan
      where a.karyawanid in (select nik from ".$dbname.".kebun_kehadiran where notransaksi='".$notransaksi."')";
//echo $str;
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$kary[$bar->karyawanid][nm]=$bar->namakaryawan;
	$kary[$bar->karyawanid][jb]=$bar->namajabatan;
}

echo"<fieldset><legend>Print Excel</legend>
     <img onclick=\"detailExcel(event,'kebun_slave_2pemeliharaandetail.php?type=excel&notransaksi=".$notransaksi."')\" src=images/excel.jpg class=resicon title='MS.Excel'>
     </fieldset>"; 
if($_GET['type']!='excel')$stream="<table class=sortable border=0 cellspacing=1>"; else
$stream="<table class=sortable border=1 cellspacing=1>";
$stream.="
      <thead>
        <tr class=rowcontent>
          <td>No</td>
          <td>Nama Karyawan</td>
          <td>Jabatan</td>
          <td>Upah</td>
          <td>Premi</td>";
//		  if($_GET['type']!='excel')$stream.="<td>Browse</td>";
        $stream.="</tr>
      </thead>
      <tbody>";
    $str="select * from ".$dbname.".kebun_kehadiran
              where notransaksi = '".$notransaksi."'";   
    $res=mysql_query($str);
    $no=0;
    while($bar= mysql_fetch_object($res))
    {
        $no+=1;
    $stream.="<tr class=rowcontent>
           <td align=right>".$no."</td>
           <td>".$kary[$bar->nik][nm]."</td>    
           <td>".$kary[$bar->nik][jb]."</td>    
           <td align=right>".number_format($bar->umr)."</td>               
           <td align=right>".number_format($bar->insentif)."</td>";
         $stream.="</tr>";
    } 
   $stream.="</tbody></table>";
   if($_GET['type']=='excel')
   {
$nop_="Detail_pemeliharaan_".$notransaksi;
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
   }
   else
   {
       echo $stream;
   }    
       
?>