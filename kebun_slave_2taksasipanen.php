<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');


$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses']; 
$_POST['kebun']==''?$kebun=$_GET['kebun']:$kebun=$_POST['kebun']; 
$_POST['tanggal']==''?$tanggal=$_GET['tanggal']:$tanggal=$_POST['tanggal'];
$_POST['afdeling']==''?$afdeling=$_GET['afdeling']:$afdeling=$_POST['afdeling'];

function putertanggal($tgl){
    $qwe=explode("-",$tgl);
    return $qwe[2]."-".$qwe[1]."-".$qwe[0];
}

if($proses=='preview'||$proses=='excel'){
    if($tanggal==''||$kebun==''){
        exit("Error: All field required");
    }
    
    $tanggal=putertanggal($tanggal);

    $esok=putertanggal(date('Y-m-d', strtotime('+1 day', strtotime($tanggal))));
    $kemarin=putertanggal(date('Y-m-d', strtotime('-1 day', strtotime($tanggal))));

    $tanggalkemarin=putertanggal($kemarin);

    $brd=0;
    if($proses=='excel'){
        $brd=1;
        $bgcoloraja="bgcolor=#DEDEDE align=center";
    }
    #ambil data timbangan
    $sPabrik="select nospb,kodeorg, (jumlahtandan1+jumlahtandan2+jumlahtandan3) as jjgpabrik,(beratbersih-kgpotsortasi) as beratbersih,
        notransaksi,left(tanggal,10) as tanggal, substr(nospb,9,6) as afdeling from ".$dbname.".pabrik_timbangan 
        where left(tanggal,10)!='' and kodeorg = '".$kebun."' and nospb!='' and substr(nospb,9,6) like '".$afdeling."%'
        and left(tanggal,10) like '".$tanggal."%' order by substr(nospb,9,6)";  
    $respabrik=mysql_query($sPabrik);
    while($bar0=mysql_fetch_object($respabrik)){
        $keyAfd[$bar0->afdeling]=$bar0->afdeling;
        $dzArr[$bar0->afdeling]['p_kg']+=$bar0->beratbersih;
        $dzArr[$kebun]['p_kg']+=$bar0->beratbersih;
    }
    #ambil data timbangan kemarin
    
    #ambil data taksasi
    $sTaksasi="select afdeling, tanggal, hasisa, haesok, jmlhpokok, persenbuahmatang, jjgmasak, jjgoutput, hkdigunakan, bjr, (bjr*jjgmasak) as kg from ".$dbname.".kebun_taksasi 
        where afdeling like '".$kebun."%' and afdeling like '%".$afdeling."%' and tanggal = '".$tanggal."'
        ";    
    $restaksasi=mysql_query($sTaksasi);
    while($bar1=mysql_fetch_object($restaksasi)){
        $keyAfd[$bar1->afdeling]=$bar1->afdeling;
        $dzArr[$bar1->afdeling]['counter']+=1;
        $dzArr[$bar1->afdeling]['afdeling']=$bar1->afdeling;
        $dzArr[$bar1->afdeling]['hasisa']+=$bar1->hasisa;
        $dzArr[$bar1->afdeling]['haesok']+=$bar1->haesok;
        $dzArr[$bar1->afdeling]['jmlhpokok']+=$bar1->jmlhpokok;
        $dzArr[$bar1->afdeling]['pbm']+=$bar1->persenbuahmatang;
        @$dzArr[$bar1->afdeling]['persenbuahmatang']=$dzArr[$bar1->afdeling]['pbm']/$dzArr[$bar1->afdeling]['counter'];
        $dzArr[$bar1->afdeling]['jjgmasak']+=$bar1->jjgmasak;
        $dzArr[$bar1->afdeling]['jjgoutput']+=$bar1->jjgoutput;
        $dzArr[$bar1->afdeling]['hkdigunakan']+=$bar1->hkdigunakan;
        $dzArr[$bar1->afdeling]['kg']+=$bar1->kg;
        @$dzArr[$bar1->afdeling]['bjr']=$dzArr[$bar1->afdeling]['kg']/$dzArr[$bar1->afdeling]['jjgmasak'];
        
        $dzArr[$kebun]['counter']+=1;
        $dzArr[$kebun]['afdeling']=$kebun;
        $dzArr[$kebun]['hasisa']+=$bar1->hasisa;
        $dzArr[$kebun]['haesok']+=$bar1->haesok;
        $dzArr[$kebun]['jmlhpokok']+=$bar1->jmlhpokok;
        $dzArr[$kebun]['pbm']+=$bar1->persenbuahmatang;
        @$dzArr[$kebun]['persenbuahmatang']=$dzArr[$kebun]['pbm']/$dzArr[$kebun]['counter'];
        $dzArr[$kebun]['jjgmasak']+=$bar1->jjgmasak;
        $dzArr[$kebun]['jjgoutput']+=$bar1->jjgoutput;
        $dzArr[$kebun]['hkdigunakan']+=$bar1->hkdigunakan;
        $dzArr[$kebun]['kg']+=$bar1->kg;
        @$dzArr[$kebun]['bjr']=$dzArr[$kebun]['kg']/$dzArr[$kebun]['jjgmasak'];
    }
    #ambil data taksasi kemarin


    if($proses!='excel'){
        $tab.="
        <table width=100% cellspacing=1 border=".$brd." >
        <tr>
            <td align=left><button onclick=pindahtanggal('".$kebun."','".$afd."','".$esok."') class=mybutton name=preview id=preview><- Esok/Tomorrow (".$esok.")</button></td>
            <td>&nbsp;</td>
            <td align=right><button onclick=pindahtanggal('".$kebun."','".$afd."','".$kemarin."') class=mybutton name=preview id=preview>(".$kemarin.") Kemarin/Yesterday -></button></td>
        </tr>
        </table>    
        ";
    }else{
        $tab.= "Laporan Taksasi<br>Kebun: ".$kebun." ".$afdeling." ".putertanggal($tanggal)." ";
    }
    $tab.="
    <table width=100% cellspacing=1 border=".$brd." >
    <thead>
    <tr class=rowheader>
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
    </tr>";
        $jumlahha=$dzArr[$kebun]['hasisa']+$dzArr[$kebun]['haesok'];
        
        @$pbm=$dzArr[$kebun]['jjgmasak']*100/$dzArr[$kebun]['jmlhpokok'];
        @$varian=100-($dzArr[$kebun]['p_kg']-$dzArr[$kebun]['kg'])/$dzArr[$kebun]['p_kg']*100;
        @$varian_k=100-($dzArr_k[$kebun]['p_kg']-$dzArr_k[$kebun]['kg'])/$dzArr_k[$kebun]['p_kg']*100;
        if($dzArr[$kebun]['kg']==0)$varian=0;
      $tab.="<tr class=rowcontent>
        <td ".$bgcoloraja.">".$kebun."</td>
        <td ".$bgcoloraja."></td>
        <td ".$bgcoloraja." align=right>".number_format($dzArr[$kebun]['hasisa'],2)."</td>
        <td ".$bgcoloraja." align=right>".number_format($dzArr[$kebun]['haesok'],2)."</td>
        <td ".$bgcoloraja." align=right>".number_format($jumlahha,2)."</td>
        <td ".$bgcoloraja." align=right>".number_format($dzArr[$kebun]['jmlhpokok'])."</td>
        <td ".$bgcoloraja." align=right>".number_format($pbm,2)."</td>
        <td ".$bgcoloraja." align=right>".number_format($dzArr[$kebun]['jjgmasak'])."</td>
        <td ".$bgcoloraja." align=right>".number_format($dzArr[$kebun]['jjgoutput'])."</td>
        <td ".$bgcoloraja." align=right>".number_format($dzArr[$kebun]['hkdigunakan'])."</td>
        <td ".$bgcoloraja." align=right>".number_format($dzArr[$kebun]['bjr'],2)."</td>
        <td ".$bgcoloraja." align=right>".number_format($dzArr[$kebun]['kg'])."</td>
        <td ".$bgcoloraja." align=right>".number_format($dzArr[$kebun]['p_kg'])."</td>
        <td ".$bgcoloraja." align=right>".number_format($varian,2)."</td>
      </tr>";                        
    $tab.="</thead>
    <tbody>";
//        <td ".$bgcoloraja.">&nbsp;</td>
//        <td ".$bgcoloraja.">".$_SESSION['lang']['taksasi']." (kg)</td>
//        <td ".$bgcoloraja.">".$_SESSION['lang']['realisasi']." (kg)</td>
//        <td ".$bgcoloraja.">".$_SESSION['lang']['varian']."</td>
    
    if(!empty($keyAfd))foreach($keyAfd as $afd){
        $jumlahha=$dzArr[$afd]['hasisa']+$dzArr[$afd]['haesok'];
        
        @$pbm=$dzArr[$afd]['jjgmasak']*100/$dzArr[$afd]['jmlhpokok'];        
        @$varian=100-($dzArr[$afd]['p_kg']-$dzArr[$afd]['kg'])/$dzArr[$afd]['p_kg']*100;
        @$varian_k=100-($dzArr_k[$afd]['p_kg']-$dzArr_k[$afd]['kg'])/$dzArr_k[$afd]['p_kg']*100;
        if($dzArr[$afd]['kg']==0)$varian=0;
      $tab.="<tr class=rowcontent>
        <td>".$kebun."</td>
        <td>".$afd."</td>
        <td align=right>".number_format($dzArr[$afd]['hasisa'],2)."</td>
        <td align=right>".number_format($dzArr[$afd]['haesok'],2)."</td>
        <td align=right>".number_format($jumlahha,2)."</td>
        <td align=right>".number_format($dzArr[$afd]['jmlhpokok'])."</td>
        <td align=right>".number_format($pbm,2)."</td>
        <td align=right>".number_format($dzArr[$afd]['jjgmasak'])."</td>
        <td align=right>".number_format($dzArr[$afd]['jjgoutput'])."</td>
        <td align=right>".number_format($dzArr[$afd]['hkdigunakan'])."</td>
        <td align=right>".number_format($dzArr[$afd]['bjr'],2)."</td>
        <td align=right>".number_format($dzArr[$afd]['kg'])."</td>
        <td align=right>".number_format($dzArr[$afd]['p_kg'])."</td>
        <td align=right>".number_format($varian,2)."</td>
      </tr>";                        
//        <td align=right>&nbsp;</td>
//        <td align=right>".number_format($dzArr_k[$afd]['kg'],2)."</td>
//        <td align=right>".number_format($dzArr_k[$afd]['p_kg'],2)."</td>
//        <td align=right>".number_format($varian_k,2)."</td>
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