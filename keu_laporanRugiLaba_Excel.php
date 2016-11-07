<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

	$pt=$_GET['pt'];
	$unit=$_GET['gudang'];
	$periode=$_GET['periode'];
 //ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt='COMPANY NAME';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namapt=strtoupper($bar->namaorganisasi);
}
//ambil namaunit
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$unit."'";
$namagudang='';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namagudang=strtoupper($bar->namaorganisasi);
}
#=========================================
$kodelaporan='INCOME STATEMENT';

$periodesaldo=str_replace("-", "", $periode);
$tahunini=substr($periodesaldo,0,4);

#sekarang
$t=mktime(0,0,0,substr($periodesaldo,4,2)+1,15,substr($periodesaldo,0,4));
$periodCUR=date('Ym',$t);#periode saldoakhir bulan berjalan
$kolomCUR="awal".date('m',$t);

#captionsekarang============================
$t=mktime(0,0,0,substr($periodesaldo,4,2),15,substr($periodesaldo,0,4));
$captionCUR=date('M-Y',$t);

#ambil format mesinlaporan==========
$str="select * from ".$dbname.".keu_5mesinlaporandt where namalaporan='".$kodelaporan."' order by nourut";
$res=mysql_query($str);

#query+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
if($unit=='')
    $where=" kodeorg in(select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."')";
else 
    $where=" kodeorg='".$unit."'";

//            <tr><td colspan=5 align=center style='font-size:20px'><b>".$namapt."</b></td></tr>
//            <tr><td colspan=5 align=center style='font-size:20px'><b>LAPORAN  LABA  RUGI</b></td></tr> 
$stream=strtoupper($_SESSION['lang']['rugilaba'])." : ".$namapt." ".$namagudang."<br>".
        strtoupper($_SESSION['lang']['periode'])." : ".$periode.
        "<table class=sortable border=1>
          <thead>
           <tr class=rowheader>
            <td colspan=3></td>
            <td align=center>".$captionCUR."</td>
            <td align=center>YTD</td>    
            </tr>
         </thead><tbody>";
$tnow2=0;
$ttill2=0;
$tnow3=0;
$ttill3=0;

while($bar=mysql_fetch_object($res))
{
    if($bar->tipe=='Header')
      {
        if($_SESSION['language']=='ID'){
            $stream.="<tr class=rowcontent><td colspan=5><b>".$bar->keterangandisplay."</b></td></tr>";  }
        else{
            $stream.="<tr class=rowcontent><td colspan=5><b>".$bar->keterangandisplay1."</b></td></tr>";
        }
      }
    else
    {
        #ambil saldo akhir periode barjalan sebagai akumulasi
      /*  $st12="select sum(".$kolomCUR.") as akumilasi
               from ".$dbname.".keu_saldobulanan where noakun between '".$bar->noakundari."' 
               and '".$bar->noakunsampai."' and  periode='".$periodCUR."' and ".$where;
        //echo $st12;
      */
        $st12="select sum(awal".substr($periodesaldo,4,2).")+sum(debet".substr($periodesaldo,4,2).") - sum(kredit".substr($periodesaldo,4,2).") as akumilasi
                from ".$dbname.".keu_saldobulanan where noakun between '".$bar->noakundari."' 
                and '".$bar->noakunsampai."' and  periode='".$periodesaldo."' and ".$where;         
        $res12=mysql_query($st12);
        
        $akumulasi=0;
        while($ba12=mysql_fetch_object($res12))
        {
            $akumulasi=$ba12->akumilasi;
        }
        #mutasi bulan berjalan
//        $st13="select sum(debet) - sum(kredit) as sekarang
//               from ".$dbname.".keu_jurnalsum_vw where noakun between '".$bar->noakundari."' 
//               and '".$bar->noakunsampai."' and  periode='".$periode."' and ".$where;
        $st13="select sum(debet".substr($periodesaldo,4,2).") - sum(kredit".substr($periodesaldo,4,2).") as sekarang
               from ".$dbname.".keu_saldobulanan where noakun between '".$bar->noakundari."' 
               and '".$bar->noakunsampai."' and  periode='".$periodesaldo."' and ".$where;
        $res13=mysql_query($st13);
        $jlhsekarang=0;
        while($ba13=mysql_fetch_object($res13))
        {
            $jlhsekarang=$ba13->sekarang;
        }
        
        $tnow2+=$jlhsekarang;
        $ttill2+=$akumulasi;
        $tnow3+=$jlhsekarang;
        $ttill3+=$akumulasi;  
        
        if($bar->tipe=='Total'){
                if($bar->noakundari=='' or $bar->noakunsampai=='')
                {
                    if($bar->variableoutput=='2')
                    {
                        $jlhsekarang=$tnow2;
                        $akumulasi=$ttill2; 
                        $tnow2=0;
                        $ttill2=0;
                    }
                    if($bar->variableoutput=='3')
                    {
                        $jlhsekarang=$tnow3;
                        $akumulasi=$ttill3; 
                        $tnow3=0;
                        $ttill3=0;
                    }         
                } 
            $stream.="<tr class=rowcontent>
                        <td></td>";
            if($_SESSION['language']=='ID'){
                $stream.="<td colspan=2><b>".$bar->keterangandisplay."</b></td>";
            }
            else{
                $stream.="<td colspan=2><b>".$bar->keterangandisplay1."</b></td>";
            }
            $stream.="<td align=right><b>".number_format($jlhsekarang)."</b></td>
            <td align=right><b>".number_format($akumulasi)."</b></td></tr>"; 
        }
        else
        {
            $stream.="<tr class=rowcontent>
                    <td style='width:30px'></td><td style='width:30px'></td>";
            if($_SESSION['language']=='ID'){
                $stream.="<td>".$bar->keterangandisplay."</td>";}
            else{
                $stream.="<td>".$bar->keterangandisplay1."</td>";
            }
            $stream.="<td align=right>".number_format($jlhsekarang)."</td>
            <td align=right>".number_format($akumulasi)."</td></tr>";             
        }   
    }   
}
$stream.= "</tbody></tfoot></tfoot></table>";

$nop_="LaporanLabaRugi-".$pt."_"."_".$unit."_".$periode;
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