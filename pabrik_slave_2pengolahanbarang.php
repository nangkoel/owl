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
   
$nopengolahan=$_GET['nopengolahan'];
$tanggal=$_GET['tanggal'];
$kodeorg=$_GET['kodeorg'];
$periode_tahun=$_GET['periode_tahun'];
$periode_bulan=$_GET['peruide_bulan'];
$periode = $periode_tahun.'-'.addZero($periode_bulan,2);

//=================================================
echo"<fieldset><legend>Print Excel</legend>
     <img onclick=\"detailExcel(event,'pabrik_slave_2pengolahanbarang.php?type=excel&nopengolahan=".$nopengolahan."&kodeorg=".$kodeorg."&periode_tahun=".$periode_tahun."&periode_bulan=".$periode_bulan."')\" src=images/excel.jpg class=resicon title='MS.Excel'>
     </fieldset>"; 
if($_GET['type']!='excel')$stream="<table class=sortable border=0 cellspacing=1>"; else
$stream="<table class=sortable border=1 cellspacing=1>";
$stream.="
      <thead>
        <tr class=rowcontent>
          <td>No</td>
          <td>Station</td>
          <td>".$_SESSION['lang']['mesin']."</td>
          <td>".$_SESSION['lang']['namabarang']."</td>
          <td>".$_SESSION['lang']['jumlah']."</td>
          <td>".$_SESSION['lang']['satuan']."</td>
          <td>".$_SESSION['lang']['hargasatuan']."</td>
          <td>".$_SESSION['lang']['total']."</td>
        </tr>
      </thead>
      <tbody>";
    $str="select * from ".$dbname.".pabrik_pengolahan_barang
              where nopengolahan = '".$nopengolahan."%'";   
	 $strJ="select * from ".$dbname.".organisasi";
	$resJ=mysql_query($strJ,$conn);
	while($barJ=mysql_fetch_object($resJ))
	{
		$org[$barJ->kodeorganisasi]=$barJ->namaorganisasi;
	}
	 $strJ="select * from ".$dbname.".log_5saldobulanan";
	$resJ=mysql_query($strJ,$conn);
	while($barJ=mysql_fetch_object($resJ))
	{
		$harga[$barJ->kodebarang]=$barJ->hargarata;
	}
	 $strJ="select * from ".$dbname.".log_5masterbarang";
	$resJ=mysql_query($strJ,$conn);
	while($barJ=mysql_fetch_object($resJ))
	{
		$namabar[$barJ->kodebarang]=$barJ->namabarang;
		$satuan[$barJ->kodebarang]=$barJ->satuan;
	}

    $res=mysql_query($str);
    $no=0;
    $total=0;
    $totalall=0;
    while($bar= mysql_fetch_object($res))
    {
        $no+=1;
		$total=($bar->jumlah)*($harga[$bar->kodebarang]);
    	$totalall+=$total;
    $stream.="<tr class=rowcontent>
           <td align=right>".$no."</td>
           <td>".$org[$bar->kodeorg]."</td>               
           <td>".$org[$bar->tahuntanam]."</td>               
           <td>".$namabar[$bar->kodebarang]."</td>               
           <td align=right>".$bar->jumlah."</td>               
           <td>".$satuan[$bar->kodebarang]."</td>               
           <td align=right>".number_format($harga[$bar->kodebarang],0)."</td>               
           <td align=right>".number_format($total,0)."</td>               
         </tr>";
    }
    $stream.="<tr class=rowheader>
           <td colspan=7>TOTAL</td>
           <td align=right>".number_format($totalall,0)."</td>               
         </tr>";
   $stream.="</tbody></table>";
   if($_GET['type']=='excel')
   {
$nop_="Detail_pengolahan_(Barang)_".$kodeorg."_".$nopengolahan;
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