<?php //@Copy nangkoelframework
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');

    $param=$_GET;
#header
$total=0;
$stream="
        Periode:".$param['dari']." S/d ".$param['sampai']." 
        <table class=sortable cellspacing=0 border=1>
        <thead>
        <tr class=rowheader>
        <td>".$_SESSION['lang']['notransaksi']."</td>
        <td>".$_SESSION['lang']['tanggal']."</td>
        <td>".$_SESSION['lang']['keterangan']."</td>
        <td>".$_SESSION['lang']['blok']."</td>
        <td>".$_SESSION['lang']['jumlah']."</td>
        </tr>
        </thead>
        <tbody>";

if($param['afd']=='')
{
    $else="kodeblok =''";
}
else
{
    $else="kodeblok like '".$param['afd']."%'";
}
if($param['tipe']!='PTM')
{   
    $str="select *  from ".$dbname.".keu_jurnaldt_vw
         where kodeorg='".$param['unit']."'
         and tanggal between ".$param['dari']." and ".$param['sampai']."
         and noakun >='".$param['akundari']."' and noakun<='".$param['akunsampai']."'
         and ".$else;

    $res=mysql_query($str);
    $no=0;
     while($bar=mysql_fetch_object($res))
      {
         $no+=1; 
         $stream.="<tr class=rowcontent>
                    <td>".$bar->noreferensi."</td>
                    <td>".tanggalnormal($bar->tanggal)."</td>
                    <td>".$bar->keterangan."</td>
                    <td>".$bar->kodeblok."</td>    
                    <td align=right>".number_format($bar->jumlah)."</td>
                   </tr>";
         $total+=$bar->jumlah;
      }
}
else
{    
    $str="select *  from ".$dbname.".keu_jurnaldt_vw
         where kodeorg='".$param['unit']."'
         and tanggal between ".$param['dari']." and ".$param['sampai']."
         and ((noakun >='6510100' and noakun<'6510301') or    
         (noakun >'6510311' and noakun<='6511003'))
         and ".$else;;
    $res=mysql_query($str);
     while($bar=mysql_fetch_object($res))
      {
         $no+=1; 
         $stream.="<tr class=rowcontent>
                    <td>".$bar->noreferensi."</td>
                    <td>".tanggalnormal($bar->tanggal)."</td>
                    <td>".$bar->keterangan."</td>
                    <td>".$bar->kodeblok."</td>    
                    <td align=right>".number_format($bar->jumlah)."</td>
                   </tr>";
         $total+=$bar->jumlah;         
      }    
}
$stream.="<tr class=rowcontent>
                    <td colspan=4>TOTAL</td>    
                    <td align=right>".number_format($total)."</td>
                   </tr>   
          </tbody>
          <tfoot></tfoot> 
          </table> 
        ";
       echo $stream;
   
?>