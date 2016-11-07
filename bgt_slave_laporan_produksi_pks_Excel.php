<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$tahun=$_GET['tahun'];
$pabrik=$_GET['pabrik'];
        
	
//check, one-two
if($tahun==''){
    echo "WARNING: silakan mengisi tahun."; exit;
}
if($pabrik==''){
    echo "WARNING: silakan mengisi pabrik."; exit;
}

//echo $tahun.$kebun;

// ambil data
    $isidata=array();
$str="select * from ".$dbname.".bgt_produksi_pks_vw where tahunbudget = '".$tahun."' and millcode = '".$pabrik."' order by kodeunit";
//            echo $str;
$res=mysql_query($str);
while($bar= mysql_fetch_object($res))
{
//    $qwe=$bar->nojurnal.$bar->noakun.$bar->nourut;
    $isidata[$bar->kodeunit][tbstotal]=$bar->kgolah;
    $isidata[$bar->kodeunit][tbs01]=$bar->olah01;
    $isidata[$bar->kodeunit][tbs02]=$bar->olah02;
    $isidata[$bar->kodeunit][tbs03]=$bar->olah03;
    $isidata[$bar->kodeunit][tbs04]=$bar->olah04;
    $isidata[$bar->kodeunit][tbs05]=$bar->olah05;
    $isidata[$bar->kodeunit][tbs06]=$bar->olah06;
    $isidata[$bar->kodeunit][tbs07]=$bar->olah07;
    $isidata[$bar->kodeunit][tbs08]=$bar->olah08;
    $isidata[$bar->kodeunit][tbs09]=$bar->olah09;
    $isidata[$bar->kodeunit][tbs10]=$bar->olah10;
    $isidata[$bar->kodeunit][tbs11]=$bar->olah11;
    $isidata[$bar->kodeunit][tbs12]=$bar->olah12;
    $isidata[$bar->kodeunit][cpototal]=$bar->kgcpo;
    $isidata[$bar->kodeunit][cpo01]=$bar->kgcpo01;
    $isidata[$bar->kodeunit][cpo02]=$bar->kgcpo02;
    $isidata[$bar->kodeunit][cpo03]=$bar->kgcpo03;
    $isidata[$bar->kodeunit][cpo04]=$bar->kgcpo04;
    $isidata[$bar->kodeunit][cpo05]=$bar->kgcpo05;
    $isidata[$bar->kodeunit][cpo06]=$bar->kgcpo06;
    $isidata[$bar->kodeunit][cpo07]=$bar->kgcpo07;
    $isidata[$bar->kodeunit][cpo08]=$bar->kgcpo08;
    $isidata[$bar->kodeunit][cpo09]=$bar->kgcpo09;
    $isidata[$bar->kodeunit][cpo10]=$bar->kgcpo10;
    $isidata[$bar->kodeunit][cpo11]=$bar->kgcpo11;
    $isidata[$bar->kodeunit][cpo12]=$bar->kgcpo12;
    $isidata[$bar->kodeunit][kertotal]=$bar->kgkernel;
    $isidata[$bar->kodeunit][ker01]=$bar->kgker01;
    $isidata[$bar->kodeunit][ker02]=$bar->kgker02;
    $isidata[$bar->kodeunit][ker03]=$bar->kgker03;
    $isidata[$bar->kodeunit][ker04]=$bar->kgker04;
    $isidata[$bar->kodeunit][ker05]=$bar->kgker05;
    $isidata[$bar->kodeunit][ker06]=$bar->kgker06;
    $isidata[$bar->kodeunit][ker07]=$bar->kgker07;
    $isidata[$bar->kodeunit][ker08]=$bar->kgker08;
    $isidata[$bar->kodeunit][ker09]=$bar->kgker09;
    $isidata[$bar->kodeunit][ker10]=$bar->kgker10;
    $isidata[$bar->kodeunit][ker11]=$bar->kgker11;
    $isidata[$bar->kodeunit][ker12]=$bar->kgker12;
    

}
//        echo "<pre>";
////        print_r($rowdata);
//        print_r($isidata);
//        echo "</pre>";
        
$stream="";

$warnalatar="#77ff77";

//header
$stream.="<table class=sortable cellspacing=1 border=1 width=100%>
     <thead>
        <tr class=rowtitle>
            <td bgcolor=\"".$warnalatar."\" rowspan=2 align=center>No.</td>
            <td bgcolor=\"".$warnalatar."\" rowspan=2 align=center>".$_SESSION['lang']['asaltbs']."</td>
             <td bgcolor=\"".$warnalatar."\" colspan=2 align=center>ORE(%)</td>   
            <td bgcolor=\"".$warnalatar."\" rowspan=2 align=center>".$_SESSION['lang']['uraian']."</td>
            <td bgcolor=\"".$warnalatar."\" rowspan=2 align=center>".$_SESSION['lang']['satuan']."</td>
            <td bgcolor=\"".$warnalatar."\" rowspan=2 align=center>".$_SESSION['lang']['total']."</td>
            <td bgcolor=\"".$warnalatar."\" colspan=12 align=center>Distribusi Produksi</td>";
       $stream.="<td bgcolor=\"".$warnalatar."\" rowspan=2 align=center>".$_SESSION['lang']['total']."</td>
        </tr>";
       $stream.="<tr>
           <td bgcolor=\"".$warnalatar."\" align=center>CPO</td>
            <td bgcolor=\"".$warnalatar."\" align=center>KER</td>
           <td bgcolor=\"".$warnalatar."\" align=center>Jan</td>
           <td bgcolor=\"".$warnalatar."\" align=center>Feb</td>
           <td bgcolor=\"".$warnalatar."\" align=center>Mar</td>
           <td bgcolor=\"".$warnalatar."\" align=center>Apr</td>
           <td bgcolor=\"".$warnalatar."\" align=center>May</td>
           <td bgcolor=\"".$warnalatar."\" align=center>Jun</td>
           <td bgcolor=\"".$warnalatar."\" align=center>Jul</td>
           <td bgcolor=\"".$warnalatar."\" align=center>Aug</td>
           <td bgcolor=\"".$warnalatar."\" align=center>Sep</td>
           <td bgcolor=\"".$warnalatar."\" align=center>Oct</td>
           <td bgcolor=\"".$warnalatar."\" align=center>Nov</td>
           <td bgcolor=\"".$warnalatar."\" align=center>Dec</td>
       </tr>";
     
$stream.="</thead>
    <tbody>";


//        $str="select distinct kodeorg from ".$dbname.".pabrik_timbangan
//                  where intex = 2 order by kodeorg"; // 2 : afiliasi
        $str="select distinct kodeorganisasi from ".$dbname.".organisasi where induk<>'".$_SESSION['org']['kodeorganisasi']."'"; 
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
                $afiliasi[$bar->kodeorganisasi]=$bar->kodeorganisasi;
        }
 //        $str="select distinct kodeorg from ".$dbname.".pabrik_timbangan
//                  where intex = 1 order by kodeorg"; // 1 : internal
        $str="select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$_SESSION['org']['kodeorganisasi']."'";  
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
                $internal[$bar->kodeorganisasi]=$bar->kodeorganisasi;
        }
//        $optNm=makeOption($dbname, 'log_5supplier', 'kodetimbangan,supplierid');
//        $str="select distinct kodecustomer from ".$dbname.".pabrik_timbangan
//                  where intex = 0 order by kodecustomer"; // 0 : eksternal
        $str="select distinct supplierid from ".$dbname.".log_5supplier
                  order by supplierid"; // 0 : eksternal
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
//                $eksternal[$optNm[$bar->kodecustomer]]=$optNm[$bar->kodecustomer];
                $eksternal[$bar->supplierid]=$bar->supplierid;
        }
//        echo "<pre>";
////        print_r($rowdata);
//        print_r($eksternal);
//        echo "</pre>";

        $no=1;
//body2
if(!empty($internal))foreach($internal as $int) // kodeorg / row
{
    $olahdata[internal][tbstotal]+=$isidata[$int][tbstotal];  
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="tbs0".$i; else $ii="tbs".$i;
        $olahdata[internal][$ii]+=$isidata[$int][$ii];    
    }    
    $olahdata[internal][cpototal]+=$isidata[$int][cpototal];    
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="cpo0".$i; else $ii="cpo".$i;
        $olahdata[internal][$ii]+=$isidata[$int][$ii];    
    }    
    $olahdata[internal][kertotal]+=$isidata[$int][kertotal];    
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="ker0".$i; else $ii="ker".$i;
        $olahdata[internal][$ii]+=$isidata[$int][$ii];    
    }    
    $olahdata[internal][paltotal]+=$isidata[$int][cpototal]+$isidata[$int][kertotal];    
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1){
            $ii="pal0".$i; $jj="cpo0".$i; $kk="ker0".$i; 
        }else{
            $ii="pal".$i; $jj="cpo".$i; $kk="ker".$i;
        }
        $olahdata[internal][$ii]+=$isidata[$int][$jj]+$isidata[$int][$kk];    
    }    
}
if(!empty($afiliasi))foreach($afiliasi as $afi) // kodeorg / row
{
    $olahdata[afiliasi][tbstotal]+=$isidata[$afi][tbstotal];    
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="tbs0".$i; else $ii="tbs".$i;
        $olahdata[afiliasi][$ii]+=$isidata[$afi][$ii];    
    }    
    $olahdata[afiliasi][cpototal]+=$isidata[$afi][cpototal];    
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="cpo0".$i; else $ii="cpo".$i;
        $olahdata[afiliasi][$ii]+=$isidata[$afi][$ii];    
    }    
    $olahdata[afiliasi][kertotal]+=$isidata[$afi][kertotal];    
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="ker0".$i; else $ii="ker".$i;
        $olahdata[afiliasi][$ii]+=$isidata[$afi][$ii];    
    }    
    $olahdata[afiliasi][paltotal]+=$isidata[$afi][cpototal]+$isidata[$afi][kertotal];    
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1){
            $ii="pal0".$i; $jj="cpo0".$i; $kk="ker0".$i; 
        }else{
            $ii="pal".$i; $jj="cpo".$i; $kk="ker".$i;
        }
        $olahdata[afiliasi][$ii]+=$isidata[$afi][$jj]+$isidata[$afi][$kk];    
    }    
} 
if(!empty($eksternal))foreach($eksternal as $eks) // kodeorg / row
{
    $olahdata[eksternal][tbstotal]+=$isidata[$eks][tbstotal];    
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="tbs0".$i; else $ii="tbs".$i;
        $olahdata[eksternal][$ii]+=$isidata[$eks][$ii];    
    }    
    $olahdata[eksternal][cpototal]+=$isidata[$eks][cpototal];    
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="cpo0".$i; else $ii="cpo".$i;
        $olahdata[eksternal][$ii]+=$isidata[$eks][$ii];    
    }    
    $olahdata[eksternal][kertotal]+=$isidata[$eks][kertotal];    
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="ker0".$i; else $ii="ker".$i;
        $olahdata[eksternal][$ii]+=$isidata[$eks][$ii];    
    }    
    $olahdata[eksternal][paltotal]+=$isidata[$eks][cpototal]+$isidata[$eks][kertotal];    
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1){
            $ii="pal0".$i; $jj="cpo0".$i; $kk="ker0".$i; 
        }else{
            $ii="pal".$i; $jj="cpo".$i; $kk="ker".$i;
        }
        $olahdata[eksternal][$ii]+=$isidata[$eks][$jj]+$isidata[$eks][$kk];    
    }    
}

$olahdata[all][tbstotal]=$olahdata[internal][tbstotal]+$olahdata[afiliasi][tbstotal]+$olahdata[eksternal][tbstotal];
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="tbs0".$i; else $ii="tbs".$i;
        $olahdata[all][$ii]+=$olahdata[internal][$ii]+$olahdata[afiliasi][$ii]+$olahdata[eksternal][$ii];    
    }    
$olahdata[all][cpototal]=$olahdata[internal][cpototal]+$olahdata[afiliasi][cpototal]+$olahdata[eksternal][cpototal];
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="cpo0".$i; else $ii="cpo".$i;
        $olahdata[all][$ii]+=$olahdata[internal][$ii]+$olahdata[afiliasi][$ii]+$olahdata[eksternal][$ii];    
    }    
$olahdata[all][kertotal]=$olahdata[internal][kertotal]+$olahdata[afiliasi][kertotal]+$olahdata[eksternal][kertotal];
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="ker0".$i; else $ii="ker".$i;
        $olahdata[all][$ii]+=$olahdata[internal][$ii]+$olahdata[afiliasi][$ii]+$olahdata[eksternal][$ii];    
    }    
$olahdata[all][paltotal]=$olahdata[internal][paltotal]+$olahdata[afiliasi][paltotal]+$olahdata[eksternal][paltotal];
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="pal0".$i; else $ii="pal".$i;
        $olahdata[all][$ii]+=$olahdata[internal][$ii]+$olahdata[afiliasi][$ii]+$olahdata[eksternal][$ii];    
    }    

if(!empty($olahdata)){
    $stream.="<tr class=rowcontent>";
    $stream.="<td rowspan=4 valign=middle align=right>1</td>";
    $stream.="<td rowspan=4 valign=middle align=left>Internal</td>";
    $RCPO=number_format($olahdata[internal][cpototal]/$olahdata[internal][tbstotal]*100,2);
    $RKER=number_format($olahdata[internal][kertotal]/$olahdata[internal][tbstotal]*100,2);
    $stream.="<td align=left rowspan=4>".$RCPO."</td>";
    $stream.="<td align=left rowspan=4>".$RKER."</td>";    
    $stream.="<td align=left>TBS</td>";
    $stream.="<td align=left>Kg</td>";
    $stream.="<td align=right>".number_format($olahdata[internal][tbstotal])."</td>";
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="tbs0".$i; else $ii="tbs".$i;
        $stream.="<td align=right>".number_format($olahdata[internal][$ii])."</td>";
    }    
    $stream.="<td align=right>".number_format($olahdata[internal][tbstotal])."</td>";
    $stream.="</tr>";
    $stream.="<tr class=rowcontent>";
//    $stream.="<td align=right>1</td>";
//    $stream.="<td align=left>Internal</td>";
    $stream.="<td align=left>CPO</td>";
    $stream.="<td align=left>Kg</td>";
    $stream.="<td align=right>".number_format($olahdata[internal][cpototal])."</td>";
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="cpo0".$i; else $ii="cpo".$i;
        $stream.="<td align=right>".number_format($olahdata[internal][$ii])."</td>";
    }    
    $stream.="<td align=right>".number_format($olahdata[internal][cpototal])."</td>";
    $stream.="</tr>";
    $stream.="<tr class=rowcontent>";
//    $stream.="<td align=right>1</td>";
//    $stream.="<td align=left>Internal</td>";
    $stream.="<td align=left>Kernel</td>";
    $stream.="<td align=left>Kg</td>";
    $stream.="<td align=right>".number_format($olahdata[internal][kertotal])."</td>";
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="ker0".$i; else $ii="ker".$i;
        $stream.="<td align=right>".number_format($olahdata[internal][$ii])."</td>";
    }    
    $stream.="<td align=right>".number_format($olahdata[internal][kertotal])."</td>";
    $stream.="</tr>";
    $stream.="<tr class=rowcontent>";
//    $stream.="<td align=right>1</td>";
//    $stream.="<td align=left>Internal</td>";
    $stream.="<td align=left>Produk</td>";
    $stream.="<td align=left>Kg</td>";
    $stream.="<td align=right>".number_format($olahdata[internal][paltotal])."</td>";
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="pal0".$i; else $ii="pal".$i;
        $stream.="<td align=right>".number_format($olahdata[internal][$ii])."</td>";
    }    
    $stream.="<td align=right>".number_format($olahdata[internal][paltotal])."</td>";
    $stream.="</tr>";

    $stream.="<tr class=rowcontent>";
    $stream.="<td rowspan=4 valign=middle align=right>2</td>";
    $stream.="<td rowspan=4 valign=middle align=left>Afiliasi</td>";
    $RCPO=number_format($olahdata[afiliasi][cpototal]/$olahdata[afiliasi][tbstotal]*100,2);
    $RKER=number_format($olahdata[afiliasi][kertotal]/$olahdata[afiliasi][tbstotal]*100,2);
    $stream.="<td align=left rowspan=4>".$RCPO."</td>";
    $stream.="<td align=left rowspan=4>".$RKER."</td>";       
    $stream.="<td align=left>TBS</td>";
    $stream.="<td align=left>Kg</td>";
    $stream.="<td align=right>".number_format($olahdata[afiliasi][tbstotal])."</td>";
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="tbs0".$i; else $ii="tbs".$i;
        $stream.="<td align=right>".number_format($olahdata[afiliasi][$ii])."</td>";
    }    
    $stream.="<td align=right>".number_format($olahdata[afiliasi][tbstotal])."</td>";
    $stream.="</tr>";
    $stream.="<tr class=rowcontent>";
//    $stream.="<td align=right>1</td>";
//    $stream.="<td align=left>Afiliasi</td>";
    $stream.="<td align=left>CPO</td>";
    $stream.="<td align=left>Kg</td>";
    $stream.="<td align=right>".number_format($olahdata[afiliasi][cpototal])."</td>";
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="cpo0".$i; else $ii="cpo".$i;
        $stream.="<td align=right>".number_format($olahdata[afiliasi][$ii])."</td>";
    }    
    $stream.="<td align=right>".number_format($olahdata[afiliasi][cpototal])."</td>";
    $stream.="</tr>";
    $stream.="<tr class=rowcontent>";
//    $stream.="<td align=right>1</td>";
//    $stream.="<td align=left>Afiliasi</td>";
    $stream.="<td align=left>Kernel</td>";
    $stream.="<td align=left>Kg</td>";
    $stream.="<td align=right>".number_format($olahdata[afiliasi][kertotal])."</td>";
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="ker0".$i; else $ii="ker".$i;
        $stream.="<td align=right>".number_format($olahdata[afiliasi][$ii])."</td>";
    }    
    $stream.="<td align=right>".number_format($olahdata[afiliasi][kertotal])."</td>";
    $stream.="</tr>";
    $stream.="<tr class=rowcontent>";
//    $stream.="<td align=right>1</td>";
//    $stream.="<td align=left>Afiliasi</td>";
    $stream.="<td align=left>Produk</td>";
    $stream.="<td align=left>Kg</td>";
    $stream.="<td align=right>".number_format($olahdata[afiliasi][paltotal])."</td>";
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="pal0".$i; else $ii="pal".$i;
        $stream.="<td align=right>".number_format($olahdata[afiliasi][$ii])."</td>";
    }    
    $stream.="<td align=right>".number_format($olahdata[afiliasi][paltotal])."</td>";
    $stream.="</tr>";

    $stream.="<tr class=rowcontent>";
    $stream.="<td rowspan=4 valign=middle align=right>3</td>";
    $stream.="<td rowspan=4 valign=middle align=left>Eksternal</td>";
    $RCPO=number_format($olahdata[eksternal][cpototal]/$olahdata[eksternal][tbstotal]*100,2);
    $RKER=number_format($olahdata[eksternal][kertotal]/$olahdata[eksternal][tbstotal]*100,2);
    $stream.="<td align=left rowspan=4>".$RCPO."</td>";
    $stream.="<td align=left rowspan=4>".$RKER."</td>";         
    $stream.="<td align=left>TBS</td>";
    $stream.="<td align=left>Kg</td>";
    $stream.="<td align=right>".number_format($olahdata[eksternal][tbstotal])."</td>";
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="tbs0".$i; else $ii="tbs".$i;
        $stream.="<td align=right>".number_format($olahdata[eksternal][$ii])."</td>";
    }    
    $stream.="<td align=right>".number_format($olahdata[eksternal][tbstotal])."</td>";
    $stream.="</tr>";
    $stream.="<tr class=rowcontent>";
//    $stream.="<td align=right>1</td>";
//    $stream.="<td align=left>Eksternal</td>";
    $stream.="<td align=left>CPO</td>";
    $stream.="<td align=left>Kg</td>";
    $stream.="<td align=right>".number_format($olahdata[eksternal][cpototal])."</td>";
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="cpo0".$i; else $ii="cpo".$i;
        $stream.="<td align=right>".number_format($olahdata[eksternal][$ii])."</td>";
    }    
    $stream.="<td align=right>".number_format($olahdata[eksternal][cpototal])."</td>";
    $stream.="</tr>";
    $stream.="<tr class=rowcontent>";
//    $stream.="<td align=right>1</td>";
//    $stream.="<td align=left>Eksternal</td>";
    $stream.="<td align=left>Kernel</td>";
    $stream.="<td align=left>Kg</td>";
    $stream.="<td align=right>".number_format($olahdata[eksternal][kertotal])."</td>";
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="ker0".$i; else $ii="ker".$i;
        $stream.="<td align=right>".number_format($olahdata[eksternal][$ii])."</td>";
    }    
    $stream.="<td align=right>".number_format($olahdata[eksternal][kertotal])."</td>";
    $stream.="</tr>";
    $stream.="<tr class=rowcontent>";
//    $stream.="<td align=right>1</td>";
//    $stream.="<td align=left>Eksternal</td>";
    $stream.="<td align=left>Produk</td>";
    $stream.="<td align=left>Kg</td>";
    $stream.="<td align=right>".number_format($olahdata[eksternal][paltotal])."</td>";
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="pal0".$i; else $ii="pal".$i;
        $stream.="<td align=right>".number_format($olahdata[eksternal][$ii])."</td>";
    }    
    $stream.="<td align=right>".number_format($olahdata[eksternal][paltotal])."</td>";
    $stream.="</tr>"; 
    
    $stream.="<tr class=rowcontent>";
    $stream.="<td rowspan=4 valign=middle align=right>&nbsp;</td>";
    $stream.="<td rowspan=4 valign=middle align=left>Grand Total</td>";
    $RCPO=number_format($olahdata[all][cpototal]/$olahdata[all][tbstotal]*100,2);
    $RKER=number_format($olahdata[all][kertotal]/$olahdata[all][tbstotal]*100,2);
    $stream.="<td align=left rowspan=4>".$RCPO."</td>";
    $stream.="<td align=left rowspan=4>".$RKER."</td>";          
    $stream.="<td align=left>TBS</td>";
    $stream.="<td align=left>Kg</td>";
    $stream.="<td align=right>".number_format($olahdata[all][tbstotal])."</td>";
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="tbs0".$i; else $ii="tbs".$i;
        $stream.="<td align=right>".number_format($olahdata[all][$ii])."</td>";
    }    
    $stream.="<td align=right>".number_format($olahdata[all][tbstotal])."</td>";
    $stream.="</tr>";
    $stream.="<tr class=rowcontent>";
//    $stream.="<td align=right>1</td>";
//    $stream.="<td align=right>all</td>";
    $stream.="<td align=left>CPO</td>";
    $stream.="<td align=left>Kg</td>";
    $stream.="<td align=right>".number_format($olahdata[all][cpototal])."</td>";
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="cpo0".$i; else $ii="cpo".$i;
        $stream.="<td align=right>".number_format($olahdata[all][$ii])."</td>";
    }    
    $stream.="<td align=right>".number_format($olahdata[all][cpototal])."</td>";
    $stream.="</tr>";
    $stream.="<tr class=rowcontent>";
//    $stream.="<td align=right>1</td>";
//    $stream.="<td align=right>all</td>";
    $stream.="<td align=left>Kernel</td>";
    $stream.="<td align=left>Kg</td>";
    $stream.="<td align=right>".number_format($olahdata[all][kertotal])."</td>";
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="ker0".$i; else $ii="ker".$i;
        $stream.="<td align=right>".number_format($olahdata[all][$ii])."</td>";
    }    
    $stream.="<td align=right>".number_format($olahdata[all][kertotal])."</td>";
    $stream.="</tr>";
    $stream.="<tr class=rowcontent>";
//    $stream.="<td align=right>1</td>";
//    $stream.="<td align=right>all</td>";
    $stream.="<td align=left>Produk</td>";
    $stream.="<td align=left>Kg</td>";
    $stream.="<td align=right>".number_format($olahdata[all][paltotal])."</td>";
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii="pal0".$i; else $ii="pal".$i;
        $stream.="<td align=right>".number_format($olahdata[all][$ii])."</td>";
    }    
    $stream.="<td align=right>".number_format($olahdata[all][paltotal])."</td>";
    $stream.="</tr>";       
}

if(empty($olahdata)){
    $stream.="<tr class=rowcontent><td colspan=18>Data tidak tersedia.</td></tr>";
}
$stream.="    </tbody>
         <tfoot>
         </tfoot>		 
   </table>";    
$stream.="Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];

$qwe=date("YmdHms");

$nop_="bgt_produksi_".$tahun." ".$pabrik;
//if(strlen($stream)>0)
//{
//     $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
//     gzwrite($gztralala, $stream);
//     gzclose($gztralala);
//     echo "<script language=javascript1.2>
//        window.location='tempExcel/".$nop_.".xls.gz';
//        </script>";
//}    
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