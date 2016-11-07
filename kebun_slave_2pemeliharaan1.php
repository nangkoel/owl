<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$proses=$_GET['proses'];
//$lksiTgs=$_SESSION['empl']['lokasitugas'];
$kdOrg1=$_POST['kdOrg1'];
$kdAfd1=$_POST['kdAfd1'];
$tahun1=$_POST['tahun1'];
$kegiatan1=$_POST['kegiatan1'];
$ispo=$_POST['ispo1'];
if(($proses=='excel')or($proses=='pdf')){
	$kdOrg1=$_GET['kdOrg1'];
	$kdAfd1=$_GET['kdAfd1'];
	$tahun1=$_GET['tahun1'];
        $kegiatan1=$_GET['kegiatan1'];
        $ispo=$_GET['ispo1'];
}
if($kdAfd1=='')
    $kdAfd1=$kdOrg1;

if(($proses=='preview')or($proses=='excel')or($proses=='pdf')){
    if($kdOrg1==''){
            echo"Error: Estate code and afdeling code required."; exit;
    }
    if($tahun1==''){
            echo"Error: year is reqired."; exit;
    }
	
}


if ($ispo!=''){
    $blk=" and ispo=".$ispo;
}
if ($proses=='excel' or $proses=='preview') 
{
    // kamus kegiatan
if($_SESSION['language']=='EN'){
    $zz='namakegiatan1 as namakegiatan';
}else{
    $zz='namakegiatan';
}    
    $str="select kodekegiatan, ".$zz.", satuan
        from ".$dbname.".setup_kegiatan
        ";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $kamusKeg[$bar->kodekegiatan]['nama']=$bar->namakegiatan;
        $kamusKeg[$bar->kodekegiatan]['satu']=$bar->satuan;
    }
    
    // kamus blok
    $str="select kodeorg, luasareaproduktif, tahuntanam, bloklama
        from ".$dbname.".setup_blok";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $kamusOrg[$bar->kodeorg]['luas']=$bar->luasareaproduktif;
        $kamusOrg[$bar->kodeorg]['tata']=$bar->tahuntanam;
        $kamusOrg[$bar->kodeorg]['bloklama']=$bar->bloklama;
    }
    
    
    // ambil data kegiatan/blok
    $str="select kodekegiatan, a.kodeorg, hasilkerja, jumlahhk, tanggal from ".$dbname.".kebun_perawatan_vw a
          left join ".$dbname.".setup_blok b on a.kodeorg=b.kodeorg 
          where a.kodeorg like '".$kdAfd1."%' and tanggal like '".$tahun1."%'".$blk." and kodekegiatan like '%".$kegiatan1."%'";
    //echo $str;
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $dzKeg[$bar->kodekegiatan]=$bar->kodekegiatan;
        $dzOrg[$bar->kodeorg]=$bar->kodeorg;

        $bulan=substr($bar->tanggal,5,2);
        $dzArr[$bar->kodekegiatan][$bar->kodeorg][$bulan]['hasilkerja']+=$bar->hasilkerja;
        $dzArr[$bar->kodekegiatan][$bar->kodeorg][$bulan]['jumlahhk']+=$bar->jumlahhk;
        
        // cari jumlah baris untuk tiap kegiatan
        if(!isset($barisKeg[$bar->kodekegiatan][$bar->kodeorg])){
            $barisKeg[$bar->kodekegiatan][$bar->kodeorg]=$bar->kodekegiatan.$bar->kodeorg;            
            $barizKeg[$bar->kodekegiatan]+=1;
        }
    }
    
    if(!empty($dzKeg))asort($dzKeg);
    if(!empty($dzOrg))asort($dzOrg);
    $jumlahKeg = count($dzKeg);
    $jumlahOrg = count($dzOrg);

//    echo $str;
//    echo "<pre>";
//    print_r($dzArr);
//    echo "</pre>";

    $border=0;
    if($proses=='excel')$border=1;

    $stream.="<table cellspacing='1' border='".$border."' class='sortable'>
    <thead>
    <tr class=rowheader>
        <td rowspan=2 align=center>".$_SESSION['lang']['namakegiatan']."</td>
        <td colspan=4 align=center>".$_SESSION['lang']['blok']."</td>";   
    for ($i = 1; $i <= 12; $i++) {
        $stream.="<td colspan=3 align=center>".numToMonth($i)."</td>";   
    }    
        $stream.="<td colspan=3 align=center>".$_SESSION['lang']['semester']." I</td>
        <td colspan=3 align=center>".$_SESSION['lang']['semester']." II</td>
        <td colspan=3 align=center>".$_SESSION['lang']['total']."</td>
    </tr>
    <tr class=rowheader>
        <td align=center>".$_SESSION['lang']['kode']."</td>    
        <td align=center>".$_SESSION['lang']['bloklama']."</td>    
        <td align=center>".$_SESSION['lang']['luas']."</td>
        <td align=center>".$_SESSION['lang']['tahuntanam']."</td>";
    // tiap bulan
    for ($i = 1; $i <= 12; $i++) {
        $stream.="<td align=center>".$_SESSION['lang']['jhk']."</td>
        <td align=center>".$_SESSION['lang']['hasilkerjad']."</td>
        <td align=center>Output (Hasil/JHK)</td>";   
    }    
        $stream.="<td align=center>".$_SESSION['lang']['jhk']."</td>
        <td align=center>".$_SESSION['lang']['hasilkerjad']."</td>
        <td align=center>Output (Hasil/JHK)</td>
        <td align=center>".$_SESSION['lang']['jhk']."</td>
        <td align=center>".$_SESSION['lang']['hasilkerjad']."</td>
        <td align=center>Output (Hasil/JHK)</td>
        <td align=center>".$_SESSION['lang']['jhk']."</td>
        <td align=center>".$_SESSION['lang']['hasilkerjad']."</td>
        <td align=center>Output (Hasil/JHK)</td>
    </tr></thead>
    <tbody>";
    // tiap kegiatan    
    if(!empty($dzKeg))foreach($dzKeg as $rKeg){
        $bariskegiatan=true;
        $stream.="<tr class=rowcontent>
            <td rowspan=".$barizKeg[$rKeg].">".$kamusKeg[$rKeg]['nama']." (".$kamusKeg[$rKeg]['satu'].")</td>";
    // tiap blok    
    if(!empty($dzOrg))foreach($dzOrg as $rOrg){
        
        $adadata=false;
        for ($i = 1; $i <= 12; $i++) {
            if(strlen($i)==1)$ii='0'.$i; else $ii=$i;    
            
            if($dzArr[$rKeg][$rOrg][$ii]['hasilkerja']!='')$adadata=true;
            if($dzArr[$rKeg][$rOrg][$ii]['jumlahhk']!='')$adadata=true;
        }
        
        if($adadata){
            if(!$bariskegiatan)$stream.="<tr class=rowcontent>";
                $stream.="<td>".$rOrg."</td>";        
                $stream.="<td align=right>".$kamusOrg[$rOrg]['bloklama']."</td>";        
                $stream.="<td align=right>".$kamusOrg[$rOrg]['luas']."</td>";        
                $stream.="<td align=right>".$kamusOrg[$rOrg]['tata']."</td>";     

                $jumlahhk1=0;
                $jumlahhk2=0;
                $hasilkerja1=0;
                $hasilkerja2=0;
            for ($i = 1; $i <= 12; $i++) {
                if(strlen($i)==1)$ii='0'.$i; else $ii=$i;

                $haka=$dzArr[$rKeg][$rOrg][$ii]['jumlahhk'];
                $hasi=$dzArr[$rKeg][$rOrg][$ii]['hasilkerja'];
                $oput=0;
                @$oput=$hasi/$haka;
//                if($oput==0)$oput=''; else $oput=number_format($oput,2);
                
                if(($haka==0)&&($hasi==0)){
                    $haka='';
                    $hasi='';
                    $oput='';
                    
                    $bisadiklik='';                    
                }else{
                    $haka=number_format($haka,2);
                    $hasi=number_format($hasi,2);
                    $oput=number_format($oput,2);
                    
                    $bisadiklik=" style='cursor:pointer;' onclick=\"viewDetail1('".$rKeg."','".$rOrg."','".$tahun1."-".$ii."',event);\" title=\"Click untuk melihat detail\" ";
                }

                $stream.="<td align=right ".$bisadiklik.">".$haka."</td>
                <td align=right ".$bisadiklik.">".$hasi."</td>
                <td align=right ".$bisadiklik.">".$oput."</td>";

                if($i<8){ // semester 1
                     $jumlahhk1+=$dzArr[$rKeg][$rOrg][$ii]['jumlahhk'];
                     $hasilkerja1+=$dzArr[$rKeg][$rOrg][$ii]['hasilkerja'];
                }else{ // semester 2
                     $jumlahhk2+=$dzArr[$rKeg][$rOrg][$ii]['jumlahhk'];
                     $hasilkerja2+=$dzArr[$rKeg][$rOrg][$ii]['hasilkerja'];
                }           
            }

            // semester 1
            $oput=0;
            $haka=0;
            $hasi=0;

            $haka=$jumlahhk1;
            $hasi=$hasilkerja1;
            @$oput=$hasi/$haka;
            if(($haka==0)&&($hasi==0)){
                $haka='';
                $hasi='';
                $oput='';
            }else{
                $haka=number_format($haka,2);
                $hasi=number_format($hasi,2);                
                $oput=number_format($oput,2);
            }

            $stream.="<td align=right>".$haka."</td>
            <td align=right>".$hasi."</td>
            <td align=right>".$oput."</td>";

            // semester 2
            $oput=0;
            $haka=0;
            $hasi=0;

            $haka=$jumlahhk2;
            $hasi=$hasilkerja2;
            @$oput=$hasi/$haka;
            if(($haka==0)&&($hasi==0)){
                $haka='';
                $hasi='';
                $oput='';
            }else{
                $haka=number_format($haka,2);
                $hasi=number_format($hasi,2);                
                $oput=number_format($oput,2);
            }

            $stream.="<td align=right>".$haka."</td>
            <td align=right>".$hasi."</td>
            <td align=right>".$oput."</td>";

            // total
            $oput=0;
            $haka=0;
            $hasi=0;

            $haka=$jumlahhk1+$jumlahhk2;
            $hasi=$hasilkerja1+$hasilkerja2;
            @$oput=$hasi/$haka;
            
            if(($haka==0)&&($hasi==0)){
                $haka='';
                $hasi='';
                $oput='';
            }else{
                $haka=number_format($haka,2);
                $hasi=number_format($hasi,2);                
                $oput=number_format($oput,2);
            }            

            $stream.="<td align=right>".$haka."</td>
            <td align=right>".$hasi."</td>
            <td align=right>".$oput."</td>";

            $stream.="</tr>";
            $bariskegiatan=false;            
        } // end of adadata

    } //  end of tiap blok
    } //  end of tiap kegiatan    
    $stream.="</tbody></table>";
     
}  
switch($proses)
{
    case'preview':
        echo $stream;    
    break;
    case 'excel':
        $stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
        $dte=date("YmdHms");
        $nop_="Pusingan_Perawatan_".$kdAfd1."_".$tahun1."_".$kegiatan1."_".date('YmdHis');
        $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
        gzwrite($gztralala, $stream);
        gzclose($gztralala);
        echo "<script language=javascript1.2>
            window.location='tempExcel/".$nop_.".xls.gz';
            </script>";            
    break;    
}

?>