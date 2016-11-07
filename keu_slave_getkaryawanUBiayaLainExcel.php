<?php //@Copy nangkoelframework
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

if($_GET['proses']=='excel')
{
    $optTipe=makeOption($dbname, 'sdm_5tipekaryawan', 'id,tipe');
$str="select a.namakaryawan,a.karyawanid,b.tipe,a.subbagian from ".$dbname.".datakaryawan a
     left join ".$dbname.".sdm_5tipekaryawan b on a.tipekaryawan=b.id
     where lokasitugas='".$_SESSION['empl']['lokasitugas']."'
     and tipekaryawan=".$_GET['tipekaryawan']." and 
     (tanggalkeluar>'".$_GET['periode']."-01' or tanggalkeluar='0000-00-00') 
     order by namakaryawan";
$res=mysql_query($str);

//$stream="<img onclick=dataKeExcel(event) src=images/excel.jpg class=resicon title='MS.Excel'>";
$stream="<table class=sortable cellspacing=1 border=1>
        <thead>
        <tr class=rowheader>
        <td  bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nomor']."</td>
        <td  bgcolor=#DEDEDE align=center>".$_SESSION['lang']['karyawanid']."</td>    
        <td   bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namakaryawan']."</td>
        <td   bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tipekaryawan']."</td>
        <td   bgcolor=#DEDEDE align=center>".$_SESSION['lang']['subbagian']."</td>    
        <td   bgcolor=#DEDEDE align=center>".$_SESSION['lang']['biayalistrik']."</td> 
        <td   bgcolor=#DEDEDE align=center>".$_SESSION['lang']['biayaair']."</td>
        <td   bgcolor=#DEDEDE align=center>".$_SESSION['lang']['biayaklinik']."</td>    
        <td   bgcolor=#DEDEDE align=center>".$_SESSION['lang']['biayasosial']."</td>
        <td   bgcolor=#DEDEDE align=center>".$_SESSION['lang']['manajemenperumahan']."</td> 
        <td   bgcolor=#DEDEDE align=center>".$_SESSION['lang']['natura']."</td>     
        <td   bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jms']."</td>    
         
        </tr>
        </thead>
        <tbody>";
#ambil data pada table keu_unalocated
$str1="select * from ".$dbname.".keu_byunalocated where periode='".$_GET['periode']."' 
       and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
$res1=mysql_query($str1);
$listrik=Array();
$air=Array();
$klinik=Array();
$sosial=Array();
while($barx=mysql_fetch_object($res1))
{
    $listrik[$barx->karyawanid]=$barx->listrik;
    $air[$barx->karyawanid]=$barx->air;
    $klinik[$barx->karyawanid]=$barx->klinik;
    $sosial[$barx->karyawanid]=$barx->sosial;
    $perumahan[$barx->karyawanid]=$barx->perumahan;
    $natura[$barx->karyawanid]=$barx->natura;
    $jms[$barx->karyawanid]=$barx->jms;
    $post[$barx->karyawanid]=$barx->posting;
}
$no=0;
 while($bar=mysql_fetch_object($res))
  {
     $no+=1; 
    
     
     $stream.="<tr class=rowcontent>
                <td>".$no."</td>
                <td id=karid".$no.">".$bar->karyawanid."</td>
                <td id=namakaryawan".$no.">".$bar->namakaryawan."</td>    
                <td>".$bar->tipe."</td>
                <td id=subbagian".$no.">".$bar->subbagian."</td>    
                <td>".number_format($listrik[$bar->karyawanid],2)."</td> 
                <td>".number_format($air[$bar->karyawanid],2)."</td>
                <td>".number_format($klinik[$bar->karyawanid])."</td>   
                <td>".number_format($sosial[$bar->karyawanid],2)."</td>
                <td>".number_format($perumahan[$bar->karyawanid],2)."</td>
                <td>".number_format($natura[$bar->karyawanid],2)."</td>
                <td>".number_format($jms[$bar->karyawanid],2)."</td>    
            </tr>";
     $totListrik+=$listrik[$bar->karyawanid];
     $totAir+=$air[$bar->karyawanid];
     $totKlinik+=$klinik[$bar->karyawanid];
     $totSosial+=$sosial[$bar->karyawanid];
     $totPerumahan+=$perumahan[$bar->karyawanid];
     $totNatura+=$natura[$bar->karyawanid];
     $totJms+=$jms[$bar->karyawanid];
  }
    $stream.="<tr class=rowcontent>
                <td colspan=5 bgcolor=#DEDEDE  align=right>".$_SESSION['lang']['total']."</td>
                
                <td bgcolor=#DEDEDE  align=right>".number_format($totListrik,2)."</td> 
                <td bgcolor=#DEDEDE  align=right>".number_format($totAir,2)."</td>
                <td bgcolor=#DEDEDE  align=right>".number_format($totKlinik)."</td>   
                <td bgcolor=#DEDEDE  align=right>".number_format($totSosial,2)."</td>
                <td bgcolor=#DEDEDE  align=right>".number_format($totPerumahan,2)."</td>
                <td bgcolor=#DEDEDE  align=right>".number_format($totNatura,2)."</td>
                <td bgcolor=#DEDEDE  align=right>".number_format($totJms,2)."</td>    
            </tr>";    
$stream.="</tbody>
          <tfoot></tfoot> 
          </table> 
        ";
 $stream.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
            $dte=date("YmdHis");
            $nop_="alokasiBiayaExcel".$optTipe[$_GET['tipekaryawan']];
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
?>