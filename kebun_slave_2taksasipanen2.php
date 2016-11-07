<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');
    

$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses']; 
$_POST['kebun2']==''?$kebun=$_GET['kebun2']:$kebun=$_POST['kebun2']; 
$_POST['periode2']==''?$periode=$_GET['periode2']:$periode=$_POST['periode2'];
$_POST['afdeling2']==''?$afdeling=$_GET['afdeling2']:$afdeling=$_POST['afdeling2'];

//function putertanggal($tgl){
//    $qwe=explode("-",$tgl);
//    return $qwe[2]."-".$qwe[1]."-".$qwe[0];
//}

if($proses=='preview'||$proses=='excel'){
    if($periode==''||$kebun==''){
        exit("Error: All field required");
    }
    
//    $tanggal=putertanggal($tanggal);
//
//    $esok=putertanggal(date('Y-m-d', strtotime('+1 day', strtotime($tanggal))));
//    $kemarin=putertanggal(date('Y-m-d', strtotime('-1 day', strtotime($tanggal))));
//
//    $tanggalkemarin=putertanggal($kemarin);

    $brd=0;
    if($proses=='excel'){
        $brd=1;
        $bgcoloraja="bgcolor=#DEDEDE align=center";
    }
    #ambil data timbangan
    $sPabrik="select nospb,kodeorg, (jumlahtandan1+jumlahtandan2+jumlahtandan3) as jjgpabrik,(beratbersih-kgpotsortasi) as beratbersih,
        notransaksi,left(tanggal,10) as tanggal, substr(nospb,9,6) as afdeling from ".$dbname.".pabrik_timbangan 
        where left(tanggal,10)!='' and kodeorg = '".$kebun."' and nospb!='' and substr(nospb,9,6) like '".$afdeling."%'
        and left(tanggal,7) like '".$periode."%' order by substr(nospb,9,6)";  
    $respabrik=mysql_query($sPabrik);
    while($bar0=mysql_fetch_object($respabrik)){
        $keyAfd[$bar0->afdeling]=$bar0->afdeling;
        $keyTgl[$bar0->tanggal]=$bar0->tanggal;
        $dzArr[$bar0->afdeling][$bar0->tanggal]['p_kg']+=$bar0->beratbersih;
        $dzArr[$kebun][$bar0->tanggal]['p_kg']+=$bar0->beratbersih;
    }
    #ambil data timbangan kemarin
    
    #ambil data taksasi
    $sTaksasi="select afdeling, tanggal, hasisa, haesok, jmlhpokok, persenbuahmatang, jjgmasak, jjgoutput, hkdigunakan, bjr, (bjr*jjgmasak) as kg from ".$dbname.".kebun_taksasi 
        where afdeling like '".$kebun."%' and afdeling like '%".$afdeling."%' and tanggal like '".$periode."%'
        ";    
    $restaksasi=mysql_query($sTaksasi);
    while($bar1=mysql_fetch_object($restaksasi)){
        $keyAfd[$bar1->afdeling]=$bar1->afdeling;
        $keyTgl[$bar1->tanggal]=$bar1->tanggal;
        $dzArr[$bar1->afdeling][$bar1->tanggal]['counter']+=1;
        $dzArr[$bar1->afdeling][$bar1->tanggal]['afdeling']=$bar1->afdeling;
        $dzArr[$bar1->afdeling][$bar1->tanggal]['hasisa']+=$bar1->hasisa;
        $dzArr[$bar1->afdeling][$bar1->tanggal]['haesok']+=$bar1->haesok;
        $dzArr[$bar1->afdeling][$bar1->tanggal]['jmlhpokok']+=$bar1->jmlhpokok;
        $dzArr[$bar1->afdeling][$bar1->tanggal]['pbm']+=$bar1->persenbuahmatang;
        @$dzArr[$bar1->afdeling][$bar1->tanggal]['persenbuahmatang']=$dzArr[$bar1->afdeling][$bar1->tanggal]['pbm']/$dzArr[$bar1->afdeling][$bar1->tanggal]['counter'];
        $dzArr[$bar1->afdeling][$bar1->tanggal]['jjgmasak']+=$bar1->jjgmasak;
        $dzArr[$bar1->afdeling][$bar1->tanggal]['jjgoutput']+=$bar1->jjgoutput;
        $dzArr[$bar1->afdeling][$bar1->tanggal]['hkdigunakan']+=$bar1->hkdigunakan;
        $dzArr[$bar1->afdeling][$bar1->tanggal]['kg']+=$bar1->kg;
        @$dzArr[$bar1->afdeling][$bar1->tanggal]['bjr']=$dzArr[$bar1->afdeling][$bar1->tanggal]['kg']/$dzArr[$bar1->afdeling][$bar1->tanggal]['jjgmasak'];
        
        $dzArr[$kebun][$bar1->tanggal]['counter']+=1;
        $dzArr[$kebun][$bar1->tanggal]['afdeling']=$kebun;
        $dzArr[$kebun][$bar1->tanggal]['hasisa']+=$bar1->hasisa;
        $dzArr[$kebun][$bar1->tanggal]['haesok']+=$bar1->haesok;
        $dzArr[$kebun][$bar1->tanggal]['jmlhpokok']+=$bar1->jmlhpokok;
        $dzArr[$kebun][$bar1->tanggal]['pbm']+=$bar1->persenbuahmatang;
        @$dzArr[$kebun][$bar1->tanggal]['persenbuahmatang']=$dzArr[$kebun][$bar1->tanggal]['pbm']/$dzArr[$kebun][$bar1->tanggal]['counter'];
        $dzArr[$kebun][$bar1->tanggal]['jjgmasak']+=$bar1->jjgmasak;
        $dzArr[$kebun][$bar1->tanggal]['jjgoutput']+=$bar1->jjgoutput;
        $dzArr[$kebun][$bar1->tanggal]['hkdigunakan']+=$bar1->hkdigunakan;
        $dzArr[$kebun][$bar1->tanggal]['kg']+=$bar1->kg;
        @$dzArr[$kebun][$bar1->tanggal]['bjr']=$dzArr[$kebun][$bar1->tanggal]['kg']/$dzArr[$kebun][$bar1->tanggal]['jjgmasak'];
    }
    #ambil data taksasi kemarin

    sort($keyTgl);
    sort($keyAfd);

    if($proses!='excel'){
//        $tab.="
//        <table width=100% cellspacing=1 border=".$brd." >
//        <tr>
//            <td align=left><button onclick=pindahtanggal('".$kebun."','".$afd."','".$esok."') class=mybutton name=preview id=preview><- Esok/Tomorrow (".$esok.")</button></td>
//            <td>&nbsp;</td>
//            <td align=right><button onclick=pindahtanggal('".$kebun."','".$afd."','".$kemarin."') class=mybutton name=preview id=preview>(".$kemarin.") Kemarin/Yesterday -></button></td>
//        </tr>
//        </table>    
//        ";
    }else{
        $tab.= "Laporan Taksasi<br>Kebun: ".$kebun." ".$afdeling." ".$periode." ";
    }
    $tab.="
    <table width=100% cellspacing=1 border=".$brd." >
    <thead>
    <tr>
        <td ".$bgcoloraja.">".$_SESSION['lang']['tanggal']."</td>
        <td ".$bgcoloraja.">".$_SESSION['lang']['kebun']."</td>
        <td ".$bgcoloraja.">".$_SESSION['lang']['afdeling']."</td>
        <td ".$bgcoloraja.">".$_SESSION['lang']['hasisa']."</td>
        <td ".$bgcoloraja.">".$_SESSION['lang']['haesok']."</td>
        <td ".$bgcoloraja.">".$_SESSION['lang']['jumlahha']."</td>
        <td ".$bgcoloraja.">".$_SESSION['lang']['jmlhpokok']."</td>
        <td ".$bgcoloraja.">".$_SESSION['lang']['persenbuahmatang']."</td>
        <td ".$bgcoloraja.">".$_SESSION['lang']['jjgmasak']."</td>
        <td ".$bgcoloraja.">".$_SESSION['lang']['jjgoutput']."</td>
        <td ".$bgcoloraja.">".$_SESSION['lang']['hkdigunakan']."</td>
        <td ".$bgcoloraja.">".$_SESSION['lang']['bjr']."</td>
        <td ".$bgcoloraja.">".$_SESSION['lang']['taksasi']." (kg)</td>
        <td ".$bgcoloraja.">".$_SESSION['lang']['realisasi']." (kg)</td>
        <td ".$bgcoloraja.">".$_SESSION['lang']['varian']."</td>
    </tr></thead><tbody>";
    
    if(!empty($keyTgl))foreach($keyTgl as $tgl){    
        $jumlahha=$dzArr[$kebun][$tgl]['hasisa']+$dzArr[$kebun][$tgl]['haesok'];

        @$pbm=$dzArr[$kebun][$tgl]['jjgmasak']*100/$dzArr[$kebun][$tgl]['jmlhpokok'];
        @$varian=100-($dzArr[$kebun][$tgl]['p_kg']-$dzArr[$kebun][$tgl]['kg'])/$dzArr[$kebun][$tgl]['p_kg']*100;
        @$varian_k=100-($dzArr_k[$kebun][$tgl]['p_kg']-$dzArr_k[$kebun][$tgl]['kg'])/$dzArr_k[$kebun][$tgl]['p_kg']*100;
        if($dzArr[$kebun][$tgl]['kg']==0)$varian=0;
        $tab.="<tr class=rowcontent>
        <td ".$bgcoloraja.">".$tgl."</td>
        <td ".$bgcoloraja.">".$kebun."</td>
        <td ".$bgcoloraja."></td>
        <td ".$bgcoloraja." align=right>".number_format($dzArr[$kebun][$tgl]['hasisa'],2)."</td>
        <td ".$bgcoloraja." align=right>".number_format($dzArr[$kebun][$tgl]['haesok'],2)."</td>
        <td ".$bgcoloraja." align=right>".number_format($jumlahha,2)."</td>
        <td ".$bgcoloraja." align=right>".number_format($dzArr[$kebun][$tgl]['jmlhpokok'])."</td>
        <td ".$bgcoloraja." align=right>".number_format($pbm,2)."</td>
        <td ".$bgcoloraja." align=right>".number_format($dzArr[$kebun][$tgl]['jjgmasak'])."</td>
        <td ".$bgcoloraja." align=right>".number_format($dzArr[$kebun][$tgl]['jjgoutput'])."</td>
        <td ".$bgcoloraja." align=right>".number_format($dzArr[$kebun][$tgl]['hkdigunakan'])."</td>
        <td ".$bgcoloraja." align=right>".number_format($dzArr[$kebun][$tgl]['bjr'],2)."</td>
        <td ".$bgcoloraja." align=right>".number_format($dzArr[$kebun][$tgl]['kg'])."</td>
        <td ".$bgcoloraja." align=right>".number_format($dzArr[$kebun][$tgl]['p_kg'])."</td>
        <td ".$bgcoloraja." align=right>".number_format($varian,2)."</td>
        </tr>";                        

        if(!empty($keyAfd))foreach($keyAfd as $afd){
        $jumlahha=$dzArr[$afd][$tgl]['hasisa']+$dzArr[$afd][$tgl]['haesok'];

        @$pbm=$dzArr[$afd][$tgl]['jjgmasak']*100/$dzArr[$afd][$tgl]['jmlhpokok'];        
        @$varian=100-($dzArr[$afd][$tgl]['p_kg']-$dzArr[$afd][$tgl]['kg'])/$dzArr[$afd][$tgl]['p_kg']*100;
        @$varian_k=100-($dzArr_k[$afd][$tgl]['p_kg']-$dzArr_k[$afd][$tgl]['kg'])/$dzArr_k[$afd][$tgl]['p_kg']*100;
        if($dzArr[$afd][$tgl]['kg']==0)$varian=0;
        $tab.="<tr class=rowcontent>
        <td></td>
        <td>".$kebun."</td>
        <td>".$afd."</td>
        <td align=right>".number_format($dzArr[$afd][$tgl]['hasisa'],2)."</td>
        <td align=right>".number_format($dzArr[$afd][$tgl]['haesok'],2)."</td>
        <td align=right>".number_format($jumlahha,2)."</td>
        <td align=right>".number_format($dzArr[$afd][$tgl]['jmlhpokok'])."</td>
        <td align=right>".number_format($pbm,2)."</td>
        <td align=right>".number_format($dzArr[$afd][$tgl]['jjgmasak'])."</td>
        <td align=right>".number_format($dzArr[$afd][$tgl]['jjgoutput'])."</td>
        <td align=right>".number_format($dzArr[$afd][$tgl]['hkdigunakan'])."</td>
        <td align=right>".number_format($dzArr[$afd][$tgl]['bjr'],2)."</td>
        <td align=right>".number_format($dzArr[$afd][$tgl]['kg'])."</td>
        <td align=right>".number_format($dzArr[$afd][$tgl]['p_kg'])."</td>
        <td align=right>".number_format($varian,2)."</td>
        </tr>";                        
        }
    }
        $tab.="</tbody></table>";


}	
switch($proses)
{
    case'preview':
        echo $tab;
    break;

    case'excel':
        $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
        $nop_="taksasi_".$kebun."_".$afdeling."_".$tanggal;
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
    case'getAfdeling0':
        $optAfd="<option value=''>".$_SESSION['lang']['all']."</option>";
        $sPrd="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
               where induk = '".$kebun."' and tipe='afdeling' order by namaorganisasi asc";
        $qPrd=mysql_query($sPrd) or die(mysql_error($conn));
        while($rPrd=  mysql_fetch_assoc($qPrd)){
            $optAfd.="<option value=".$rPrd['kodeorganisasi'].">".$rPrd['namaorganisasi']."</option>";
        }
        
        // taken from kebun_slave_taksasi... ambil karyawan selain bhl
        $sorg2="select distinct karyawanid,namakaryawan from ".$dbname.".datakaryawan 
                where lokasitugas='".$kebun."' and tipekaryawan!='4' order by namakaryawan asc";

        $qorg2=mysql_query($sorg2) or die(mysql_error($conn));
        while($rorg2=mysql_fetch_assoc($qorg2)){
            if($param['mandor']!=''){
                $optafd2.="<option value='".$rorg2['karyawanid']."' ".($param['mandor']==$rorg2['karyawanid']?"selected":"").">".$rorg2['namakaryawan']."</option>";
            }
            else{
                $optafd2.="<option value='".$rorg2['karyawanid']."'>".$rorg2['namakaryawan']."</option>";
            }
        }
        
        
        echo $optAfd."####".$optafd2;
    break;

    case'getAfdeling':
        $optAfd="<option value=''>".$_SESSION['lang']['all']."</option>";
        $sPrd="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
               where induk = '".$kebun."' and tipe='afdeling' order by namaorganisasi asc";
        $qPrd=mysql_query($sPrd) or die(mysql_error($conn));
        while($rPrd=  mysql_fetch_assoc($qPrd)){
            $optAfd.="<option value=".$rPrd['kodeorganisasi'].">".$rPrd['namaorganisasi']."</option>";
        }
        echo $optAfd;
    break;
    
    default:
    break;
}
?>