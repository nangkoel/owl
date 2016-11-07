<?php
    require_once('master_validation.php');
    require_once('config/connection.php');
    require_once('lib/nangkoelib.php');
    require_once('lib/zLib.php');

    // ambil yang dilempar javascript
    $pt=$_GET['pt'];
    $unit=$_GET['unit'];
    $tgl1=$_GET['tgl1'];
    $tgl2=$_GET['tgl2'];
        
    // olah tanggal
    $tanggal1=explode('-',$tgl1);
    $tanggal2=explode('-',$tgl2);
    $date1=$tanggal1[2].'-'.$tanggal1[1].'-'.$tanggal1[0];
    $tanggalterakhir=date(t, strtotime($date1));
    
    // kamus blok
    $sdakar="select kodeorg,tahuntanam from ".$dbname.".setup_blok";
    $qdakar=mysql_query($sdakar) or die(mysql_error($conn));
    while($rdakar=  mysql_fetch_assoc($qdakar))
    {
        $belok[$rdakar['kodeorg']]=$rdakar['tahuntanam'];
    }
    
    if($unit=='') // script copy-an dari kebun_laporanPanen.php
    {
        $str="select a.blok,d.bloklama,a.tanggal,a.nospb,a.notiket,a.nokendaraan,a.jjg,a.kgwb,a.bjr,a.kgbjr
        from (".$dbname.".kebun_spb_vw a
        left join ".$dbname.".organisasi c
        on substr(a.kodeorg,1,4)=c.kodeorganisasi) inner join ".$dbname.".setup_blok d 
                on a.blok=d.kodeorg 
        where c.induk = '".$pt."'  and a.tanggal between ".tanggalsystem($tgl1)." and ".tanggalsystem($tgl2)." 
        order by a.blok, a.tanggal";
    }
    else
    {
        $str="select a.blok,d.bloklama,a.tanggal,a.nospb,a.notiket,a.nokendaraan,a.jjg,a.kgwb,a.bjr,a.kgbjr
        from ".$dbname.".kebun_spb_vw a inner join ".$dbname.".setup_blok d 
                on a.blok=d.kodeorg 
        where blok like '".$unit."%'  and a.tanggal between ".tanggalsystem($tgl1)." and ".tanggalsystem($tgl2)." 
        order by a.blok, a.tanggal";
    }	
    
    #ambil  spb timbangan
    $sPabrik="select nospb,notransaksi,nokendaraan from ".$dbname.".pabrik_timbangan where nospb!='' 
              and tanggal between '".tanggaldgnbar($tgl1)."' and '".tanggaldgnbar($tgl2)." 23:59:00'";
    $respabrik=mysql_query($sPabrik);
    while($bar1=mysql_fetch_object($respabrik)){
        $nospbx[$bar1->nospb]=$bar1->nospb;
        $notiketx[$bar1->nospb]=$bar1->notransaksi;
        $nokendaraanx[$bar1->nospb]=$bar1->nokendaraan;
    }
    
//    exit('error: '.$str);
    $stream=$_SESSION['lang']['laporanpanen']." ".$pt." ".$unit." SPB vs WB ".$tgl1." - ".$tgl2;
    $stream.='<table border=1 cellpading=1>';
    // header
    $stream.="<thead>
                <tr>
            <td align=center>No.</td>
            <td align=center>".$_SESSION['lang']['afdeling']."</td>
            <td align=center>".$_SESSION['lang']['blok']."</td>
            <td align=center>".$_SESSION['lang']['bloklama']."</td>    
            <td align=center>".$_SESSION['lang']['tahuntanam']."</td>
            <td align=center>".$_SESSION['lang']['tanggal']."</td> 
            <td align=center>".$_SESSION['lang']['nospb']."</td>
            <td align=center>".$_SESSION['lang']['noTiket']."</td>
            <td align=center>".$_SESSION['lang']['kendaraan']."</td>
            <td align=center>".$_SESSION['lang']['jjg']."</td>
            <td align=center>"."KG ".$_SESSION['lang']['kebun']."</td>    
            <td align=center>".$_SESSION['lang']['kgwb']."</td>
            <td align=center>".$_SESSION['lang']['bjr']." ".$_SESSION['lang']['aktual']."</td>
            <td align=center>".$_SESSION['lang']['bjr']." Sensus</td>
            <td align=center>%</td>
        </tr>
        </thead>
	<tbody>";

    // content
    $res=mysql_query($str);
    $no=0;
    if(mysql_num_rows($res)<1){
        $jukol=12;
        echo"<tr class=rowcontent><td colspan=".$jukol.">".$_SESSION['lang']['tidakditemukan']."</td></tr>";
        exit;
    }else{
        while($bar=mysql_fetch_object($res)){
        // content
        $arrnospb=explode("/",$bar->nospb);
        if ($arrnospb[2]-1==0) {
            $nospblain=$arrnospb[0]."/".$arrnospb[1]."/12/".($arrnospb[3]-1);
        } else {
            $nospblain=$arrnospb[0]."/".$arrnospb[1]."/".addZero(($arrnospb[2]-1),2)."/".$arrnospb[3];
        }
        if ($nospbx[$bar->nospb]==''){
            $notiketx[$bar->nospb]=$notiketx[$nospblain];
            $nokendaraanx[$bar->nospb]=$nokendaraanx[$nospblain];
        } 
        $no+=1;
        @$aktual=$bar->kgwb/$bar->jjg;
        $stream.="<tr class='rowcontent'>
            <td align=center>".$no."</td>
            <td align=left>".substr($bar->blok,0,6)."</td>
            <td align=center>".$bar->blok."</td>
            <td align=center>".$bar->bloklama."</td>    
            <td align=center>".$belok[$bar->blok]."</td>
            <td align=center>".$bar->tanggal."</td>
            <td align=center>".$bar->nospb."</td>";
            $notiket=$notiketx[$bar->nospb];
            if($notiket!='')
            $stream.="<td align=right>".$notiket."</td>";else{
                $stream.="<td bgcolor=red title='Belum Masuk PKS' align=right>".$notiket."</td>";
            }
            $stream.="<td align=center>".$nokendaraanx[$bar->nospb]."</td>
            <td align=right>".$bar->jjg."</td>";
            $stream.="<td align=right>".number_format($bar->kgbjr,2)."</td>";
            $kgwb=$bar->kgwb;
            if($kgwb!=0){
                $stream.="<td align=right>".number_format($kgwb,2)."</td>";
                $beda=$kgwb-$bar->kgbjr;
                @$persen=($beda/$bar->kgbjr)*100;
//                $beda=abs($kgwb-$bar->kgbjr);
//                @$persen=100-(($beda/$kgwb)*100);
            }
            else{
                $stream.="<td bgcolor=red title='SPB Belum Diinput' align=right>".number_format($kgwb,2)."</td>";
                $persen=0;
            }
            $stream.="<td align=right>".number_format($aktual,2)."</td>
            <td align=right>".$bar->bjr."</td>";
            $stream.="<td align=right>".number_format($persen,2)."</td></tr>";
            $totalbarjjg+=$bar->jjg;
            $totalbarkgbjr+=$bar->kgbjr;
            $totalbarkgwb+=$bar->kgwb;
        }
        $stream.="<tr class='rowcontent'>
            <td align=center></td>
            <td align=left></td>
            <td align=center></td>
            <td align=center></td>
            <td align=center></td>
            <td align=center></td>
            <td align=center>Total</td>";
//            $notiket=$bar->notiket;
//            if($notiket!='')
            $stream.="<td align=right></td>";
//            else{
//                echo"<td bgcolor=red title='Belum Masuk PKS' align=right>".$notiket."</td>";
//            }
            $stream.="<td align=center></td>
            <td align=right>".number_format($totalbarjjg)."</td>";
            $stream.= "<td align=right>".number_format($totalbarkgbjr,2)."</td>";
//            $kgwb=$bar->kgwb;
//            if($kgwb!=0){
                $stream.="<td align=right>".number_format($totalbarkgwb,2)."</td>";
                $beda=$totalbarkgwb-$totalbarkgbjr;
                @$persen=($beda/$totalbarkgbjr)*100;
//            }
//            else{
//                echo"<td bgcolor=red title='SPB Belum Diinput' align=right>".number_format($kgwb,2)."</td>";
//                $persen=0;
//            }
        @$aktual=$totalbarkgwb/$totalbarjjg;
            $stream.="<td align=right>".number_format($aktual,2)."</td>
            <td align=right></td>";
            $stream.="<td align=right>".number_format($persen,2)."</td>";
            $stream.= "</tr>";          
    } 
    $stream.="</tbody>
        <tfoot>
        </tfoot>";		 
                
    $stream.="</table>Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
	
$tglSkrg=date("Ymd");
$nop_="LaporanPanenSPBWB".$pt."_".$unit."_".$tgl1;
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