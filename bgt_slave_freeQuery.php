<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

$thnbudget=$_POST['thnbudget'];
if($thnbudget=='')
    $thnbudget=$_GET['thnbudget'];

$kegiatan=$_POST['kegiatan'];
if($kegiatan=='')
    $kegiatan=$_GET['kegiatan'];

$kodeorg=$_POST['kodeorg'];
if($kodeorg=='')
    $kodeorg=$_GET['kodeorg'];

if(isset($_POST['proses']))
   $proses=$_POST['proses'];
else{
    $proses=$_GET['proses'];
}  

$str="select a.noakun,a.kegiatan, a.kodebudget, sum(a.jumlah) as jumlah, a.satuanj, sum(a.rupiah) as rupiah,
      sum(a.volume) as volume,a.satuanv,b.namaakun,c.nama from ".$dbname.".bgt_budget a
      left join  ".$dbname.".keu_5akun b on a.noakun=b.noakun
      left join  ".$dbname.".bgt_kode c on a.kodebudget=c.kodebudget
      where a.tahunbudget=".$thnbudget." and a.kegiatan='".$kegiatan."' 
      and a.kodeorg like '".$kodeorg."%' and a.tipebudget='ESTATE'
      group by a.noakun,a.kegiatan,a.kodebudget";
$res=mysql_query($str);
$stream="<table class=sortable border=0 cellspacing=1>
     <thead>
       <tr class=rowheader>
          <td align=center>".$_SESSION['lang']['no']."</td>
          <td align=center>".$_SESSION['lang']['noakun']."</td>
          <td align=center>".$_SESSION['lang']['namaakun']."</td>
          <td align=center>".$_SESSION['lang']['kegiatan']."</td> 
          <td align=center>".$_SESSION['lang']['kodebudget']."</td>
          <td align=center>".$_SESSION['lang']['volume']."</td> 
          <td align=center>".$_SESSION['lang']['satuan']."</td>    
          <td align=center>".$_SESSION['lang']['jumlah']."</td>
          <td align=center>".$_SESSION['lang']['satuan']."</td>
          <td align=center>".$_SESSION['lang']['rp']."</td>    
       </tr>
     </thead><tbody>";
while($bar=mysql_fetch_object($res))
{
    $no+=1;
      $stream.="<tr class=rowcontent>
          <td>".$no."</td>
          <td>".$bar->noakun."</td>
          <td>".$bar->namaakun."</td>
          <td>".$bar->nama."</td> 
          <td>".$bar->kodebudget."</td>
          <td align=right>".number_format($bar->volume,2,'.',',')."</td>
          <td>".$bar->satuanv."</td>              
          <td align=right>".number_format($bar->jumlah,2,'.',',')."</td>
          <td>".$bar->satuanj."</td>
          <td align=right>".number_format($bar->rupiah,2,'.',',')."</td>    
       </tr>";
      $ttlv=$bar->volume;
      $satv=$bar->satuanv;
       $ttl+=$bar->rupiah;
       $satj=$bar->satuanj;
}

     $stream.="<tr class=rowcontent>
          <td colspan=9>".$_SESSION['lang']['total']."</td>                   
          <td align=right>".number_format($ttl,2,'.',',')."</td>  
       </tr>";
$stream.="<tbody><tfoot></tfoot></table>
          Kolom Volume hanya akan terpakai pada pengangkutan External dan internal
           ";

switch ($proses)
{
    case'preview':
        echo"<img onclick=\"printExcel('excel','bgt_slave_freeQuery.php','PRINT',event)\" src=\"images/excel.jpg\" class=resicon title='MS.Excel'> ";
        echo $stream;
        break;
    case'excel':
        $stream.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];
        $qwe=date("YmdHms");
        $nop_="Budget_".$kodeorg."_Pertelaan_".$thnbudget."_".$qwe;
        if(strlen($stream)>0)
        {
             $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
             gzwrite($gztralala, $stream);
             gzclose($gztralala);
             echo "<script language=javascript1.2>
                window.location='tempExcel/".$nop_.".xls.gz';
                </script>";
        } 
        break;
    default:
        break;
}
?>