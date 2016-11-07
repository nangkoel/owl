<?php
//@Copy nangkoelframework
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');

$periode=$_GET['periode'];
$tampil=$_GET['tampil'];
$pabrik=$_GET['pabrik'];

if(strlen($periode)==4)
{
	//tahunan
	$str="select sum(tbsmasuk) as tbsmasuk,
		  sum(tbsdiolah) as tbsdiolah,
		  sum(oer)  as oer,
		  avg(ffa) as ffa,
		  avg(kadarair) as kadarair,
		  avg(kadarkotoran) as kadarkotoran,
		  sum(oerpk) as oerpk,
		  avg(ffapk) as ffapk,
		  avg(kadarairpk) as kadarairpk,
		  avg(kadarkotoranpk) as kadarkotoranpk,
		  sum(jumlahpk) as jumlahpk,
		  sum(jumlahck) as jumlahck,
		  sum(jumlahjakos) as jumlahjakos,
		  left(tanggal,7) as perio from ".$dbname.".pabrik_produksi
		  where kodeorg='".$pabrik."' and tanggal like '".$periode."%'
		  group by perio order by perio";  
	//ambil sisa tbs hari ini
	$stsisa="select sisahariini from ".$dbname.".pabrik_produksi 
	          where tanggal like '".$periode."%' order by tanggal desc limit 1";
	$ressisa=mysql_query($stsisa);
	$sisa=0;
	while($barsisa=mysql_fetch_object($ressisa))
	{
		$sisa=$barsisa->sisahariini;
	}				  
			  
	//ambil tbs sisa sebelumnya
	$stsedia="select sisahariini from ".$dbname.".pabrik_produksi 
	          where tanggal like '".($periode-1)."%' order by tanggal desc limit 1";
	$ressedia=mysql_query($stsedia);
	$tbskemarin=0;
	while($barsedia=mysql_fetch_object($ressedia))
	{
		$tbskemarin=$barsedia->sisahariini;
	}		  			  
    
	$res=mysql_query($str);
	$stream='';
        $stream.="
	  <table border=1>
	    <thead>
		  <tr>
		   <td rowspan=2 align=center>".$_SESSION['lang']['kodeorganisasi']."</td>
		   <td rowspan=2 align=center>".$_SESSION['lang']['bulan']."</td>
		   <td rowspan=2 align=center>".$_SESSION['lang']['tersedia']." (Kg.)</td>
		   <td rowspan=2 align=center>".$_SESSION['lang']['tbsdiolah']." (Kg.)</td>
		   <td rowspan=2 align=center>".$_SESSION['lang']['sisa']." (Kg.)</td>
		   <td colspan=5 align=center>".$_SESSION['lang']['cpo']."
		   </td>
		   <td colspan=5 align=center>".$_SESSION['lang']['kernel']."
		   </td>	  
		  </tr>  
		  <tr class=rowheader> 
		   <td align=center>".$_SESSION['lang']['cpo']." (Kg)</td>
		   <td align=center>".$_SESSION['lang']['oer']." (%)</td>
		   <td align=center>(FFa)(%)</td>
		   <td align=center>".$_SESSION['lang']['kotoran']." (%)</td>
		   <td align=center>".$_SESSION['lang']['kadarair']." (%)</td>
		   
		   <td align=center>".$_SESSION['lang']['kernel']." (Kg)</td>
		   <td align=center>".$_SESSION['lang']['oer']." (%)</td>
		   <td align=center>(FFa) (%)</td>
		   <td align=center>".$_SESSION['lang']['kotoran']." (%)</td>
		   <td align=center>".$_SESSION['lang']['kadarair']." (%)</td>
		   
		  </tr>
		</thead>
		<tbody>";
       while($bar=mysql_fetch_object($res))
        {
		 $stream.="<tr>
		   <td>".$pabrik."</td>
		   <td>".$bar->perio."</td>
		   <td align=right>".number_format($bar->tbsmasuk+$tbskemarin,0)."</td>
		   <td align=right>".number_format($bar->tbsdiolah,0)."</td>
		   <td align=right>".number_format($bar->tbsmasuk+$tbskemarin-$bar->tbsdiolah,0)."</td>		   
		   <td align=right>".number_format($bar->oer,2,'.',',')."</td>
		   <td align=right>".(@number_format($bar->oer/$bar->tbsdiolah*100,2,'.',','))."</td>
		   <td align=right>".number_format($bar->ffa,2,'.',',')."</td>
		   <td align=right>".number_format($bar->kadarkotoran,2,'.',',')."</td>
		   <td align=right>".number_format($bar->kadarair,2,'.',',')."</td>
		   
		   <td align=right>".number_format($bar->oerpk,2,'.',',')."</td>
		   <td align=right>".(@number_format($bar->oerpk/$bar->tbsdiolah*100,2,'.',','))."</td>
		   <td align=right>".number_format($bar->ffapk,2,'.',',')."</td>
		   <td align=right>".number_format($bar->kadarkotoranpk,2,'.',',')."</td>
		   <td align=right>".number_format($bar->kadarairpk,2,'.',',')."</td>
		  </tr>";
		  $tbskemarin=$bar->tbsmasuk+$tbskemarin-$bar->tbsdiolah;
         }	  
		
      $stream.="
		</tbody>
		<tfoot>
		</tfoot>
	  </table>
	  </fieldset>";
}
else
{
	//bulanan
	$str="select * from ".$dbname.".pabrik_produksi where tanggal like '".$periode."%'
	      and kodeorg='".$pabrik."'
		  order by tanggal desc";
    $res=mysql_query($str);
	$stream='';
        $stream.="
      <table border=1>
	    <thead>
		  <tr>
		   <td rowspan=2 align=center>".$_SESSION['lang']['kodeorganisasi']."</td>
		   <td rowspan=2 align=center>".$_SESSION['lang']['tanggal']."</td>
		   <td rowspan=2 align=center>".$_SESSION['lang']['tersedia']." (Kg.)</td>
		   <td rowspan=2 align=center>".$_SESSION['lang']['tbsdiolah']." (Kg.)</td>
		   <td rowspan=2 align=center>".$_SESSION['lang']['sisa']." (Kg.)</td>
		   <td colspan=5 align=center>".$_SESSION['lang']['cpo']."
		   </td>
		   <td colspan=5 align=center>".$_SESSION['lang']['kernel']."
		   </td>	  
		  </tr>  
		  <tr class=rowheader> 
		   <td align=center>".$_SESSION['lang']['cpo']." (Kg)</td>
		   <td align=center>".$_SESSION['lang']['oer']." (%)</td>
		   <td align=center>(FFa)(%)</td>
		   <td align=center>".$_SESSION['lang']['kotoran']." (%)</td>
		   <td align=center>".$_SESSION['lang']['kadarair']." (%)</td>
		   
		   <td align=center>".$_SESSION['lang']['kernel']." (Kg)</td>
		   <td align=center>".$_SESSION['lang']['oer']." (%)</td>
		   <td align=center>(FFa) (%)</td>
		   <td align=center>".$_SESSION['lang']['kotoran']." (%)</td>
		   <td align=center>".$_SESSION['lang']['kadarair']." (%)</td>
		  </tr>
		</thead>
		<tbody>";
       while($bar=mysql_fetch_object($res))
        {
		$stream.="<tr>
		   <td>".$bar->kodeorg."</td>
		   <td>".tanggalnormal($bar->tanggal)."</td>
		   <td align=right>".number_format($bar->tbsmasuk+$bar->sisatbskemarin,0)."</td>
		   <td align=right>".number_format($bar->tbsdiolah,0)."</td>
		   <td align=right>".number_format($bar->sisahariini,0)."</td>
		   
		   <td align=right>".number_format($bar->oer,2,'.',',')."</td>
		   <td align=right>".(@number_format($bar->oer/$bar->tbsdiolah*100,2,'.',','))."</td>
		   <td align=right>".number_format($bar->ffa,2,'.',',')."</td>
		   <td align=right>".number_format($bar->kadarkotoran,2,'.',',')."</td>
		   <td align=right>".number_format($bar->kadarair,2,'.',',')."</td>
		   
		   <td align=right>".number_format($bar->oerpk,2,'.',',')."</td>
		   <td align=right>".(@number_format($bar->oerpk/$bar->tbsdiolah*100,2,'.',','))."</td>
		   <td align=right>".number_format($bar->ffapk,2,'.',',')."</td>
		   <td align=right>".number_format($bar->kadarkotoranpk,2,'.',',')."</td>
		   <td align=right>".number_format($bar->kadarairpk,2,'.',',')."</td>
		  </tr>";	
         }	  
		
       $stream.="
		</tbody>
		<tfoot>
		</tfoot>
	  </table>
	  </fieldset>";
}
$nop_="Produksi_".$pabrik."_".$periode;
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
?>