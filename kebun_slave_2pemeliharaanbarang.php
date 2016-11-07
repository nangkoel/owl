<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
?>
<script language=javascript1.2 src="js/generic.js"></script>
<script language=javascript1.2 src="js/kebun_2pemeliharaan.js"></script>
<link rel=stylesheet type='text/css' href='style/generic.css'>
<?php
   
$notransaksi=$_GET['notransaksi'];
$tanggal=$_GET['tanggal'];
$kdOrg=$_GET['kdOrg'];
$type=$_GET['type'];
$periode=substr($tanggal,0,7);

//echo("not:".$notransaksi." tgl:".$tanggal." org:".$kdOrg." typ:".$type);

/*
$tanggal=$_GET['tanggal'];
$kodeorg=$_GET['kodeorg'];
$periode_tahun=$_GET['periode_tahun'];
$periode_bulan=$_GET['periode_bulan'];
$periode = $periode_tahun.'-'.addZero($periode_bulan,2);

*///=================================================
$str="select kodebarang, namabarang, satuan from ".$dbname.".log_5masterbarang 
      where kodebarang in (select kodebarang from ".$dbname.".kebun_pakaimaterial where notransaksi='".$notransaksi."')";
//echo $str;
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$barang[$bar->kodebarang][kb]=$bar->kodebarang;
	$barang[$bar->kodebarang][nm]=$bar->namabarang;
	$barang[$bar->kodebarang][st]=$bar->satuan;
}
$temp=0; // cari harga rata2 di log_5saldobulanan sesuai periode tanggal
$str="select kodebarang, hargarata from ".$dbname.".log_5saldobulanan 
      where periode ='".$periode."' and kodebarang in (select kodebarang from ".$dbname.".kebun_pakaimaterial where notransaksi='".$notransaksi."')";
//echo $str;
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$barang[$bar->kodebarang][hr]=$bar->hargarata;
	$temp=$bar->hargarata;
}
if($temp==0){ // kalo ga ada di log_5saldobulanan, cari harga di log_5masterbarangdt sesuai kodegudang
	$str="select kodebarang, hargalastin from ".$dbname.".log_5masterbarangdt 
      where kodegudang like '".$kdOrg."%' and kodebarang in (select kodebarang from ".$dbname.".kebun_pakaimaterial where notransaksi='".$notransaksi."')";
	//echo $str;
	$res=mysql_query($str);
	while($bar=mysql_fetch_object($res))
	{
		$barang[$bar->kodebarang][hr]=$bar->hargalastin;
		$temp=$bar->hargalastin;
	}
}
if($temp==0){ // kalo ga ada di log_5saldobulanan juga, cari harga di log_5saldobulanan
	$str="select kodebarang, hargarata from ".$dbname.".log_5saldobulanan 
      where kodebarang in (select kodebarang from ".$dbname.".kebun_pakaimaterial where notransaksi='".$notransaksi."')";
	//echo $str;
	$res=mysql_query($str);
	while($bar=mysql_fetch_object($res))
	{
		$barang[$bar->kodebarang][hr]=$bar->hargarata;
		$temp=$bar->hargarata;
	}
}



echo"<fieldset><legend>Print Excel</legend>
     <img onclick=\"detailExcel(event,'kebun_slave_2pemeliharaanbarang.php?type=excel&notransaksi=".$notransaksi."&tanggal=".$tanggal."&kdOrg=".$kdOrg."')\" src=images/excel.jpg class=resicon title='MS.Excel'>
     </fieldset>"; 
if($_GET['type']!='excel')$stream="<table class=sortable border=0 cellspacing=1>"; else
$stream="<table class=sortable border=1 cellspacing=1>";
$stream.="
      <thead>
        <tr class=rowcontent>
          <td>No</td>
          <td>Nama Barang</td>
          <td>Jumlah</td>
          <td>Satuan</td>
          <td>Total</td>";
//		  if($_GET['type']!='excel')$stream.="<td>Browse</td>";
        $stream.="</tr>
      </thead>
      <tbody>";
    $str="select * from ".$dbname.".kebun_pakaimaterial
              where notransaksi = '".$notransaksi."'";   
    $res=mysql_query($str);
    $no=0;
    while($bar= mysql_fetch_object($res))
    {
        $no+=1;
		$total=$bar->kwantitas*$barang[$bar->kodebarang][hr];
    $stream.="<tr class=rowcontent>
           <td align=right>".$no."</td>
           <td>".$barang[$bar->kodebarang][nm]."</td>    
           <td align=right>".$bar->kwantitas."</td>    
           <td>".$barang[$bar->kodebarang][st]."</td>    
           <td align=right>".number_format($total)."</td>";
         $stream.="</tr>";
    } 
   $stream.="</tbody></table>";
   if($_GET['type']=='excel')
   {
$nop_="Detail_pemeliharaan_barang_".$tanggal;
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
//echo("not:".$notransaksi." tgl:".$tanggal." org:".$kdOrg." typ:".$type." nop:".$nop_);
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