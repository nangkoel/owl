<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');


$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses']; 
$_POST['kebun0']==''?$kebun=$_GET['kebun0']:$kebun=$_POST['kebun0']; 
$_POST['afdeling0']==''?$afdeling=$_GET['afdeling0']:$afdeling=$_POST['afdeling0'];
//$_POST['mandor0']==''?$mandor=$_GET['mandor0']:$mandor=$_POST['mandor0'];
$_POST['periode0']==''?$periode=$_GET['periode0']:$periode=$_POST['periode0'];

if($proses=='preview'||$proses=='excel'){
    if($periode==''||$kebun==''){
        exit("Error: All field required");
    }
    
    $brd=0;
    if($proses=='excel'){
        $brd=1;
        $bgcoloraja="bgcolor=#DEDEDE align=center";
    }
    #ambil data timbangan
    $sPabrik="select nospb,kodeorg, (jumlahtandan1+jumlahtandan2+jumlahtandan3) as jjgpabrik,(beratbersih-kgpotsortasi) as beratbersih,
        notransaksi,left(tanggal,10) as tanggal, substr(nospb,9,6) as afdeling from ".$dbname.".pabrik_timbangan 
        where left(tanggal,10)!='' and kodeorg = '".$kebun."' and nospb!='' and substr(nospb,9,6) like '".$afdeling."%'
        and left(tanggal,10) like '".$periode."%' order by substr(nospb,9,6)";  
    $respabrik=mysql_query($sPabrik);
    while($bar0=mysql_fetch_object($respabrik)){
        $kunci2=$kebun.$bar0->tanggal;
        
        $dzArr[$kunci2]['p_kg']+=$bar0->beratbersih;
    }
    


    #ambil afdeling
    $sPabrik="select kodeorganisasi from ".$dbname.".organisasi
        where kodeorganisasi like '".$kebun."%' and kodeorganisasi like '".$afdeling."%' and tipe = 'AFDELING'";  
    $respabrik=mysql_query($sPabrik);
    while($bar0=mysql_fetch_object($respabrik)){
        $listafd[$bar0->kodeorganisasi]=$bar0->kodeorganisasi;
    }
    
    #ambil data taksasi
    $sTaksasi="select afdeling, tanggal, blok, seksi, hasisa, haesok, jmlhpokok, persenbuahmatang, jjgmasak, jjgoutput, hkdigunakan, bjr, (bjr*jjgmasak) as kg from ".$dbname.".kebun_taksasi 
        where afdeling like '".$kebun."%' and afdeling like '%".$afdeling."%' and tanggal like '".$periode."%' 
        ";    
    $restaksasi=mysql_query($sTaksasi);
    while($bar1=mysql_fetch_object($restaksasi)){        
        $kunci=$bar1->afdeling.$bar1->tanggal;
        
        $dzArr[$kunci]['counter']+=1;
        $dzArr[$kunci]['afdeling']=$bar1->afdeling;
        $dzArr[$kunci]['blok'].=$bar1->blok.'</br>';
        $dzArr[$kunci]['seksi'].=$bar1->seksi.'</br>';
        $dzArr[$kunci]['hasisa']+=$bar1->hasisa;
        $dzArr[$kunci]['haesok']+=$bar1->haesok;
        $dzArr[$kunci]['jmlhpokok']+=$bar1->jmlhpokok;
        $dzArr[$kunci]['pbm']+=$bar1->persenbuahmatang;
        @$dzArr[$kunci]['persenbuahmatang']=$dzArr[$kunci]['pbm']/$dzArr[$kunci]['counter'];
        $dzArr[$kunci]['jjgmasak']+=$bar1->jjgmasak;
        $dzArr[$kunci]['jjgoutput']+=$bar1->jjgoutput;
        $dzArr[$kunci]['hkdigunakan']+=$bar1->hkdigunakan;
        $dzArr[$kunci]['kg']+=$bar1->kg;
        @$dzArr[$kunci]['bjr']=$dzArr[$kunci]['kg']/$dzArr[$kunci]['jjgmasak'];
    }
    
    #ambil data taksasi total
    $sTaksasi="select afdeling, tanggal, blok, seksi, hasisa, haesok, jmlhpokok, persenbuahmatang, jjgmasak, jjgoutput, hkdigunakan, bjr, (bjr*jjgmasak) as kg from ".$dbname.".kebun_taksasi 
        where afdeling like '".$kebun."%' and tanggal like '".$periode."%'
        ";    
    $restaksasi=mysql_query($sTaksasi);
    while($bar1=mysql_fetch_object($restaksasi)){        
        $kunci2=$kebun.$bar1->tanggal;
        
        $dzArr[$kunci2]['hkdigunakan']+=$bar1->hkdigunakan;
        $dzArr[$kunci2]['hasisa']+=$bar1->hasisa;
        $dzArr[$kunci2]['haesok']+=$bar1->haesok;
        $dzArr[$kunci2]['jmlhpokok']+=$bar1->jmlhpokok;
        $dzArr[$kunci2]['jjgmasak']+=$bar1->jjgmasak;
        $dzArr[$kunci2]['kg']+=$bar1->kg;
    }    
    

    if($proses!='excel'){

    }else{
        $tab.= $_SESSION['lang']['laporan']." ".$_SESSION['lang']['rencanapanen']." ".$_SESSION['lang']['harian']."<br>Kebun: ".$kebun." ".$afdeling." ".$periode." ";
    }
    $tab.="
    <table width=100% cellspacing=1 border=".$brd." >
    <thead>
    <tr>
        <td ".$bgcoloraja." rowspan=3>".$_SESSION['lang']['tanggal']."</td>";
        if(!empty($listafd))foreach($listafd as $laf)$tab.="<td ".$bgcoloraja." colspan=12 align=center>".$laf."</td>";
        $tab.="
        <td ".$bgcoloraja." colspan=11 align=center>".$kebun."</td>
    </tr>
    <tr>";
        if(!empty($listafd))foreach($listafd as $laf)$tab.="<td ".$bgcoloraja." rowspan=2>".$_SESSION['lang']['section']."</td>
        <td ".$bgcoloraja." rowspan=2>".$_SESSION['lang']['blok']."</td>
        <td ".$bgcoloraja." rowspan=2>".$_SESSION['lang']['hasisa']."</td>
        <td ".$bgcoloraja." rowspan=2>".$_SESSION['lang']['haesok']."</td>
        <td ".$bgcoloraja." rowspan=2>".$_SESSION['lang']['jumlahha']."</td>
        <td ".$bgcoloraja." rowspan=2>".$_SESSION['lang']['jmlhpokok']."</td>
        <td ".$bgcoloraja." rowspan=2>".$_SESSION['lang']['persenbuahmatang']."</td>
        <td ".$bgcoloraja." rowspan=2>".$_SESSION['lang']['jjgmasak']."</td>
        <td ".$bgcoloraja." rowspan=2>".$_SESSION['lang']['jjgoutput']."</td>
        <td ".$bgcoloraja." rowspan=2>".$_SESSION['lang']['hkdigunakan']."</td>
        <td ".$bgcoloraja." rowspan=2>".$_SESSION['lang']['bjr']."</td>
        <td ".$bgcoloraja." rowspan=2>".$_SESSION['lang']['taksasi']." (kg)</td>";
        $tab.="
        <td ".$bgcoloraja." rowspan=2>".$_SESSION['lang']['jumlahhk']."</td>
        <td ".$bgcoloraja." rowspan=2>".$_SESSION['lang']['jumlahha']."</td>
        <td ".$bgcoloraja." rowspan=2>".$_SESSION['lang']['persenbuahmatang']."</td>
        <td ".$bgcoloraja." colspan=2>".$_SESSION['lang']['taksasi']." (kg)</td>
        <td ".$bgcoloraja." colspan=2>".$_SESSION['lang']['realisasi']." (kg)</td>
        <td ".$bgcoloraja." colspan=2>Varian (kg)</td>
        <td ".$bgcoloraja." colspan=2>".$_SESSION['lang']['varian']."</td>
    </tr>
    <tr>
        <td ".$bgcoloraja.">".$_SESSION['lang']['hi']."</td>
        <td ".$bgcoloraja.">".$_SESSION['lang']['sdhi']."</td>
        <td ".$bgcoloraja.">".$_SESSION['lang']['hi']."</td>
        <td ".$bgcoloraja.">".$_SESSION['lang']['sdhi']."</td>
        <td ".$bgcoloraja.">".$_SESSION['lang']['hi']."</td>
        <td ".$bgcoloraja.">".$_SESSION['lang']['sdhi']."</td>
        <td ".$bgcoloraja.">".$_SESSION['lang']['hi']."</td>
        <td ".$bgcoloraja.">".$_SESSION['lang']['sdhi']."</td>
    </tr>
    </thead>
    <tbody>";
    
    // With timestamp, this gets last day of April 2010 'Y-m-t'
    $tanggalterakhir = date('t', strtotime($periode.'-01'));
    
    $kgsdhi=0;
    $p_kgsdhi=0;
    $varian_kgsdhi=0;
    for ($i = 1; $i <= $tanggalterakhir; $i++) {
        if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
        $kunci2=$kebun.$periode.'-'.$ii;
        $jumlahha2=$dzArr[$kunci2]['hasisa']+$dzArr[$kunci2]['haesok'];
        
        $kgsdhi+=$dzArr[$kunci2]['kg'];
        $p_kgsdhi+=$dzArr[$kunci2]['p_kg'];
        $varian_kg=$dzArr[$kunci2]['p_kg']-$dzArr[$kunci2]['kg'];
        $varian_kgsdhi+=$varian_kg;
        @$varian_ps=100-($dzArr[$kunci2]['p_kg']-$dzArr[$kunci2]['kg'])/$dzArr[$kunci2]['p_kg']*100;
        @$varian_pssdhi=100-($p_kgsdhi-$kgsdhi)/$p_kgsdhi*100;
        if($dzArr[$kunci2]['kg']==0)$varian_ps=0;
        
        $tab.="<tr class=rowcontent>
        <td align=right>".$i."</td>";
        if(!empty($listafd))foreach($listafd as $laf){
            $kunci=$laf.$periode.'-'.$ii;
            $jumlahha=$dzArr[$kunci]['hasisa']+$dzArr[$kunci]['haesok'];
            @$pbm=$dzArr[$kunci]['jjgmasak']*100/$dzArr[$kunci]['jmlhpokok'];
            $tab.="<td>".$dzArr[$kunci]['seksi']."</td>
            <td>".$dzArr[$kunci]['blok']."</td>
            <td align=right>".number_format($dzArr[$kunci]['hasisa'],2)."</td>
            <td align=right>".number_format($dzArr[$kunci]['haesok'],2)."</td>
            <td align=right>".number_format($jumlahha,2)."</td>
            <td align=right>".number_format($dzArr[$kunci]['jmlhpokok'])."</td>
            <td align=right>".number_format($pbm,2)."</td>
            <td align=right>".number_format($dzArr[$kunci]['jjgmasak'])."</td>
            <td align=right>".number_format($dzArr[$kunci]['jjgoutput'])."</td>
            <td align=right>".number_format($dzArr[$kunci]['hkdigunakan'])."</td>
            <td align=right>".number_format($dzArr[$kunci]['bjr'],2)."</td>
            <td align=right>".number_format($dzArr[$kunci]['kg'])."</td>";            
        }
        @$pbm2=$dzArr[$kunci2]['jjgmasak']*100/$dzArr[$kunci2]['jmlhpokok'];
        $tab.="
        <td align=right>".number_format($dzArr[$kunci2]['hkdigunakan'])."</td>
        <td align=right>".number_format($jumlahha2,2)."</td>
        <td align=right>".number_format($pbm2,2)."</td>
        <td align=right>".number_format($dzArr[$kunci2]['kg'])."</td>
        <td align=right>".number_format($kgsdhi)."</td>
        <td align=right>".number_format($dzArr[$kunci2]['p_kg'])."</td>
        <td align=right>".number_format($p_kgsdhi)."</td>
        <td align=right>".number_format($varian_kg)."</td>
        <td align=right>".number_format($varian_kgsdhi)."</td>
        <td align=right>".number_format($varian_ps,2)."</td>
        <td align=right>".number_format($varian_pssdhi,2)."</td>
        </tr>";                        
        
    }
    
    $tab.="</tbody></table></td></tr></tbody><table>";

}	
switch($proses)
{
    case'preview':
        echo $tab;
    break;

    case'excel':
        $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
        $nop_="taksasi_pertgl_".$kebun."_".$afdeling."_".$periode;
        if(strlen($tab)>0)
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
            if(!fwrite($handle,$tab))
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
    break;
    
    default:
    break;
}
?>