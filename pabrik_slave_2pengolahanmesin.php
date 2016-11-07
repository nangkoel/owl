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
     <img onclick=\"detailExcel(event,'pabrik_slave_2pengolahanmesin.php?type=excel&nopengolahan=".$nopengolahan."&kodeorg=".$kodeorg."&periode_tahun=".$periode_tahun."&periode_bulan=".$periode_bulan."')\" src=images/excel.jpg class=resicon title='MS.Excel'>
     </fieldset>"; 
if($_GET['type']!='excel')$stream="<table class=sortable border=0 cellspacing=1>"; else
$stream="<table class=sortable border=1 cellspacing=1>";
$stream.="
      <thead>
        <tr class=rowcontent>
          <td>No</td>
          <td>Station</td>
          <td>".$_SESSION['lang']['mesin']."</td>
          <td>".$_SESSION['lang']['jammulai']."</td>
          <td>".$_SESSION['lang']['jamselesai']."</td>
          <td>".$_SESSION['lang']['jamstagnasi']."</td>
          <td>".$_SESSION['lang']['keterangan']."</td>
          <td>".$_SESSION['lang']['prestasi']."</td>
        </tr>
      </thead>
      <tbody>";
    $str="select * from ".$dbname.".pabrik_pengolahanmesin
              where nopengolahan = '".$nopengolahan."%'";   
	 $strJ="select * from ".$dbname.".organisasi";
	$resJ=mysql_query($strJ,$conn);
	while($barJ=mysql_fetch_object($resJ))
	{
		$org[$barJ->kodeorganisasi]=$barJ->namaorganisasi;
	}

    $res=mysql_query($str);
    $no=0;
    $tdebet=0;
    $tkredit=0;
    while($bar= mysql_fetch_object($res))
    {
        $no+=1;
        $debet=0;
        $kredit=0;
        if($bar->jumlah>0)
             $debet= $bar->jumlah;
        else
             $kredit= $bar->jumlah*-1;
    
    $stream.="<tr class=rowcontent>
           <td align=right>".$no."</td>
           <td>".$org[$bar->kodeorg]."</td>               
           <td>".$org[$bar->tahuntanam]."</td>               
           <td align=right>".substr($bar->jammulai,0,5)."</td>               
           <td align=right>".substr($bar->jamselesai,0,5)."</td>               
           <td align=right>".$bar->jamstagnasi."</td>               
           <td>".$bar->keterangan."</td>               
           <td>".$bar->prestasi."</td>               
         </tr>";
    } 
   $stream.="</tbody></table>";
   if($_GET['type']=='excel')
   {
$nop_="Detail_pengolahan_(Mesin)_".$kodeorg."_".$nopengolahan;
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