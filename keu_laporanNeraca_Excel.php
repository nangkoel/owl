<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

	$pt=$_GET['pt'];
	$unit=$_GET['gudang'];
	$periode=$_GET['periode'];
	$periode1=$_GET['periode1'];
	$revisi=$_GET['revisi'];

$qwe=explode('-',$periode);
$tahun=$qwe[0];
$tahunlalu=$tahun-1;
$bulan=$qwe[1];

//ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt='COMPANY NAME';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $namapt=strtoupper($bar->namaorganisasi);
}
#++++++++++++++++++++++++++++++++++++++++++
$kodelaporan='BALANCE SHEET';

$periodesaldo=str_replace("-", "", $periode);

#lalu
if($periode1=='akhir')$periodPRF=substr($periodesaldo,0,4)."01"; else $periodPRF=$tahunlalu.$bulan;
if($periode1=='akhir')$periodPRF2=substr($periodesaldo,0,4)."-01"; else $periodPRF2=$tahunlalu."-".$bulan;
if($periode1=='akhir')$kolomPRF="awal01"; else $kolomPRF="awal".date('m',$t); #"awal".substr($periodesaldo,4,2);

#sekarang
$t=mktime(0,0,0,substr($periodesaldo,4,2)+1,15,substr($periodesaldo,0,4));
$periodCUR=date('Ym',$t);
$periodCUR2=substr($periodesaldo,0,4).'-'.substr($periodesaldo,4,2);
$kolomCUR="awal".date('m',$t);

#captionsekarang============================
$t=mktime(0,0,0,substr($periodesaldo,4,2),15,substr($periodesaldo,0,4));
$captionCUR=date('M-Y',$t);

#captionlalu
$t=mktime(0,0,0,12,15,substr($periodesaldo,0,4)-1);
$t1=mktime(0,0,0,$bulan,15,substr($periodesaldo,0,4)-1);
if($periode1=='akhir')$captionPRF=date('M-Y',$t); else $captionPRF=$captionPRF=date('M-Y',$t1);

//echo "--".$periodPRF."==".$kolomPRF.">>".$captionPRF;

#query+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
if($unit=='')
    $where=" kodeorg in(select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."')";
else 
    $where=" kodeorg='".$unit."'";

$str="select * from ".$dbname.".keu_5mesinlaporandt where namalaporan='".$kodelaporan."' order by nourut";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $dzArr[$bar->nourut]['nourut']=$bar->nourut;
    $dzArr[$bar->nourut]['tampil']=$bar->variableoutput;    
    $dzArr[$bar->nourut]['tipe']=$bar->tipe;
    if($_SESSION['language']=='ID'){
        $dzArr[$bar->nourut]['keterangan']=$bar->keterangandisplay;
    }
    else{
        $dzArr[$bar->nourut]['keterangan']=$bar->keterangandisplay1;
    }
    $dzArr[$bar->nourut]['noakundari']=$bar->noakundari;
    $dzArr[$bar->nourut]['noakunsampai']=$bar->noakunsampai;
}

$stream="<table class=sortable border=0 cellspacing=1>
    <thead>
        <tr class=rowheader>
        <td colspan=5></td>
        <td align=center>".$captionCUR."</td>
        <td align=center>".$captionPRF."</td>    
        </tr>
    </thead><tbody>";
$jlhkolom=7;

if(!empty($dzArr))foreach($dzArr as $data){
    $st12="select sum(".$kolomPRF.") as kemarin
        from ".$dbname.".keu_saldobulanan where noakun between '".$data['noakundari']."' 
        and '".$data['noakunsampai']."' and (periode='".$periodPRF."') and ".$where;  
    
    $res12=mysql_query($st12);
    $jlhlalu=0;
    while($ba12=mysql_fetch_object($res12))
    {
        $jlhlalu=$ba12->kemarin;
    }     
    $dzArr[$data['nourut']]['jumlahlalu']=$jlhlalu;
    
    if($revisi==0){ // kalo revisi 0, ambil data dari saldo bulanan
        $st12="select sum(".$kolomCUR.") as sekarang
            from ".$dbname.".keu_saldobulanan where noakun between '".$data['noakundari']."' 
            and '".$data['noakunsampai']."' and (periode='".$periodCUR."') and ".$where;
        $res12=mysql_query($st12);
        $jlhsekarang=0;
        while($ba12=mysql_fetch_object($res12))
        {
            $jlhsekarang=$ba12->sekarang;
        }      
        $dzArr[$data['nourut']]['jumlahsekarang']=$jlhsekarang;      
    }
}

if($revisi>0){ // kalo revisi > 0, ambil data dari jurnal
    $st12="select noakun, sum(jumlah) as jumlah
        from ".$dbname.".keu_jurnaldt_vw where periode between '".$periodPRF2."' 
        and '".$periodCUR2."' and ".$where." and revisi <= '".$revisi."' group by noakun";  
    $res12=mysql_query($st12);
    $jlhsekarang=0;
    while($ba12=mysql_fetch_object($res12))
    {
        if(!empty($dzArr))foreach($dzArr as $data){
            if(($ba12->noakun>=$data['noakundari'])&&($ba12->noakun<=$data['noakunsampai'])){
                $dzArr[$data['nourut']]['jumlahtemp']+=$ba12->jumlah; 
                $dzArr[$data['nourut']]['jumlahsekarang']=$dzArr[$data['nourut']]['jumlahlalu']+$dzArr[$data['nourut']]['jumlahtemp'];
            }
        }        
    }                 
}

//echo "<pre>";
//print_r($dzArr);
//echo "</pre>";
//exit;

#ambil format mesinlaporan==========
if(!empty($dzArr))foreach($dzArr as $data){
    if($data['tipe']=='Header')
    {
        if($data['tampil']==0)
            $stream.="<tr class=rowcontent><td colspan=7><b>".$data['keterangan']."</b></td></tr>";  
        else{
            $stream.="<tr class=rowcontent>
                <td colspan=".$data['tampil']."></td>
                <td colspan=".($jlhkolom-$data['tampil'])."><b>".$data['keterangan']."</b></td>
            </tr>"; 
        }
    }
    else
    if($data['tipe']=='Total'){
        if($data['tampil']==0){
            $stream.="<tr class=rowcontent>
                <td colspan=5></td>
                <td colspan=2>------------------------------------------------------------</td>
                </tr>
            <tr class=rowcontent>
                <td colspan=5><b>".$data['keterangan']."</b></td>
                <td align=right><b>".number_format($data['jumlahsekarang'])."</b></td>
                <td align=right><b>".number_format($data['jumlahlalu'])."</b></td>    
            </tr>
            <tr class=rowcontent>
                <td style='width:30px'></td>
                <td style='width:30px'></td>
                <td style='width:30px'></td>
                <td colspan=4></td>
            </tr>
            "; 
        }
        else
        {
            $stream.="<tr class=rowcontent>
                <td colspan=5></td>
                <td colspan=".($jlhkolom-5).">------------------------------------------------------------</td>
            </tr>
            <tr class=rowcontent>
                <td colspan=".$data['tampil']."></td>
                <td colspan=".(5-$data['tampil'])."><b>".$data['keterangan']."</b></td>
                <td align=right><b>".number_format($data['jumlahsekarang'])."</b></td>
                <td align=right><b>".number_format($data['jumlahlalu'])."</b></td>    
            </tr>
            <tr class=rowcontent><td colspan=7>.</td></tr>
            ";                
        }   
    }
    else
    $stream.="
    <tr class=rowcontent title='Click untuk melihat detail' onclick=\"lihatDetailNeraca('".$data['noakundari']."','".$data['noakunsampai']."','".$periode."','".$periode1."','".$pt."','".$unit."',event);\">
        <td colspan=".($data['tampil'])."></td>
        <td colspan=".(5-$data['tampil']).">".$data['keterangan']."</td>
        <td align=right>".number_format($data['jumlahsekarang'])."</td>
        <td align=right>".number_format($data['jumlahlalu'])."</td>    
    </tr>";          
}

$stream.= "</tbody></tfoot></tfoot></table>";        
        
//old script before revisi        
//$qwe=explode('-',$periode);
//$tahun=$qwe[0];
//$tahunlalu=$tahun-1;
//$bulan=$qwe[1];
//
//#===========================================
////ambil namapt
//$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
//$namapt='COMPANY NAME';
//$res=mysql_query($str);
//while($bar=mysql_fetch_object($res))
//{
//	$namapt=strtoupper($bar->namaorganisasi);
//}
////ambil namaunit
//$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$unit."'";
//$namagudang='';
//$res=mysql_query($str);
//while($bar=mysql_fetch_object($res))
//{
//	$namagudang=strtoupper($bar->namaorganisasi);
//}
//#++++++++++++++++++++++++++++++++++++++++++
//$kodelaporan='BALANCE SHEET';
//$periodesaldo=str_replace("-", "", $periode);
//
//#lalu
////$periodPRF=substr($periodesaldo,0,4)."01";
////$kolomPRF="awal01";#"awal".substr($periodesaldo,4,2);
//if($periode1=='akhir')$periodPRF=substr($periodesaldo,0,4)."01"; else $periodPRF=$tahunlalu.$bulan;
//if($periode1=='akhir')$periodPRF2=substr($periodesaldo,0,4)."-01"; else $periodPRF2=$tahunlalu."-".$bulan;
//if($periode1=='akhir')$kolomPRF="awal01"; else $kolomPRF="awal".date('m',$t); #"awal".substr($periodesaldo,4,2);
//
//#sekarang
//$t=mktime(0,0,0,substr($periodesaldo,4,2)+1,15,substr($periodesaldo,0,4));
//$periodCUR=date('Ym',$t);
//$kolomCUR="awal".date('m',$t);
//
//#captionsekarang============================
//$t=mktime(0,0,0,substr($periodesaldo,4,2),15,substr($periodesaldo,0,4));
//$captionCUR=date('M-Y',$t);
//#captionlalu
//$t=mktime(0,0,0,12,15,substr($periodesaldo,0,4)-1);
//$t1=mktime(0,0,0,$bulan,15,substr($periodesaldo,0,4)-1);
////$captionPRF=date('M-Y',$t);
//if($periode1=='akhir')$captionPRF=date('M-Y',$t); else $captionPRF=$captionPRF=date('M-Y',$t1);
//
//#ambil format mesinlaporan==========
//$str="select * from ".$dbname.".keu_5mesinlaporandt where namalaporan='".$kodelaporan."' order by nourut";
//$res=mysql_query($str);
//
//#query+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//if($unit=='')
//    $where=" kodeorg in(select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."')";
//else 
//    $where=" kodeorg='".$unit."'";
//
//if($periode1=='akhir')$tampilperiode1='Akhir Tahun'; else $tampilperiode1='Tahun Lalu';
//$stream=strtoupper($_SESSION['lang']['neraca'])." : ".$namapt." ".$namagudang."<br>".
//        strtoupper($_SESSION['lang']['periode'])." : ".$periode." : ".$tampilperiode1.
//        "<table border=1>
//          <thead>
//           <tr>
//            <td colspan=5></td>
//            <td align=center>".$captionCUR."</td>
//            <td align=center>".$captionPRF."</td>    
//            </tr>
//         </thead><tbody>";
//$jlhkolom=7;
//while($bar=mysql_fetch_object($res))
//{
//    $tampildari=$bar->variableoutput;
//    
//    if($bar->tipe=='Header')
//      {
//        if($tampildari==0)
//          $stream.="<tr><td colspan=7><b>".$bar->keterangandisplay."</b></td></tr>";  
//        else{
//          $stream.="<tr>
//                      <td colspan=".$tampildari."></td>
//                      <td colspan=".($jlhkolom-$tampildari)."><b>".$bar->keterangandisplay."</b></td>
//                    </tr>"; 
//        }
//      }
//    else
//    {
//            $st12="select sum(".$kolomPRF.") as kemarin
//                   from ".$dbname.".keu_saldobulanan where noakun between '".$bar->noakundari."' 
//                   and '".$bar->noakunsampai."' and (periode='".$periodPRF."') and ".$where;
//            $res12=mysql_query($st12);
//            $jlhlalu=0;
//            while($ba12=mysql_fetch_object($res12))
//            {
//                $jlhlalu=$ba12->kemarin;
//            }    
//         /*   
//            $st12="select sum(".$kolomCUR.") as sekarang
//                   from ".$dbname.".keu_saldobulanan where noakun between '".$bar->noakundari."' 
//                   and '".$bar->noakunsampai."' and (periode='".$periodCUR."') and ".$where;
//            $res12=mysql_query($st12);
//            $jlhsekarang=0;
//            while($ba12=mysql_fetch_object($res12))
//            {
//                $jlhsekarang=$ba12->sekarang;
//            }  
//            */
//            $st12="select sum(awal".substr($periodesaldo,4,2)."+debet".substr($periodesaldo,4,2)."-kredit".substr($periodesaldo,4,2).") as sekarang
//                   from ".$dbname.".keu_saldobulanan where noakun between '".$bar->noakundari."' 
//                   and '".$bar->noakunsampai."' and (periode='".$periodesaldo."') and ".$where;
//            $res12=mysql_query($st12);
//            $jlhsekarang=0;
//            while($ba12=mysql_fetch_object($res12))
//            {
//                $jlhsekarang=$ba12->sekarang;
//            }  
//            
//        if($bar->tipe=='Total'){
//            if($tampildari==0){
//            $stream.="<tr>
//                        <td colspan=5></td>
//                        <td colspan=2>----------------------</td>
//                        </tr>
//                    <tr>
//                        <td  colspan=5><b>".$bar->keterangandisplay."</b></td>
//                        <td align=right><b>".number_format($jlhsekarang)."</b></td>
//                        <td align=right><b>".number_format($jlhlalu)."</b></td>    
//                     </tr>
//                     <tr>
//                     <td style='width:30px'></td>
//                     <td style='width:30px'></td>
//                     <td style='width:30px'></td>
//                     <td colspan=4></td>
//                     </tr>
//                     "; 
//            }
//            else
//            {
//            $stream.="<tr>
//                        <td colspan=5></td>
//                        <td colspan=".($jlhkolom-5).">----------------------</td>
//                        </tr>
//                    <tr>
//                       <td colspan=".$tampildari."></td>
//                        <td  colspan=".(5-$tampildari)."><b>".$bar->keterangandisplay."</b></td>
//                        <td align=right><b>".number_format($jlhsekarang)."</b></td>
//                        <td align=right><b>".number_format($jlhlalu)."</b></td>    
//                     </tr>
//                     <tr><td colspan=7>.</td></tr>
//                     ";                
//            }   
//        }
//        else
//        {
//            $stream.="
//                    <tr>
//                    <td colspan=".($tampildari)."></td>
//                    <td colspan=".(5-$tampildari).">".$bar->keterangandisplay."</td>
//                    <td align=right>".number_format($jlhsekarang)."</td>
//                    <td align=right>".number_format($jlhlalu)."</td>    
//                     </tr>";             
//        }   
//    }   
//}
//$stream.= "</tbody></tfoot></tfoot></table>";
#===========================================================================

$nop_="Neraca-".$pt."-".$unit."-".$periodesaldo;
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