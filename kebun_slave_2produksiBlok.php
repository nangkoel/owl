<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$proses=$_GET['proses'];
switch ($proses){
        case 'preview':
                $param=$_POST; 
            break;
         case 'excel':
                $param=$_GET;    
             break;
}

    $bulanini=$param['periode'];
    $cekPeriod="select distinct tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where periode='".$bulanini."' and kodeorg like '".$param['idKebun']."%'";
    $rPeriod=fetchData($cekPeriod);
    $qwe=explode('-',$bulanini);
    $tahunlalu=$qwe[0];
    $bulanlalu=$qwe[1];
    if($bulanlalu=='01'){
        $tahunlalu-=1;
        $bulanlalu='12';
    }else{
        $bulanlalu-=1;
    }
    
    $bulanlalu=str_pad($bulanlalu, 2, "0", STR_PAD_LEFT);

    // bjr bulan kemarin =  taken from kebun_laporanPanen_orang.php
    $bulankemarin=$tahunlalu."-".$bulanlalu;
    
    $sbjrlalu="select blok, sum(jjg) as jjg, sum(kgwb) as kgwb from ".$dbname.".kebun_spb_vw
        where notiket IS NOT NULL and tanggal like '".$bulankemarin."%'
        group by blok";
    $qbjrlalu=mysql_query($sbjrlalu) or die(mysql_error($conn));
    while($rbjrlalu=  mysql_fetch_assoc($qbjrlalu))
    {
        @$beje=$rbjrlalu['kgwb']/$rbjrlalu['jjg'];
        $bjrlalu[$rbjrlalu['blok']]=$beje;
    }    
    

        //ambil  tahun tanam
        $str="select kodeorg,tahuntanam,kodeorg from ".$dbname.".setup_blok where kodeorg like '".$param['idKebun']."%'";
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
            $tt[$bar->kodeorg]=$bar->tahuntanam;
            $blok[]=$bar->kodeorg;
        }
        
        //ambil  jjg panen
        $str="select sum(hasilkerja) as jjgpanen,kodeorg,tanggal,notransaksi from ".$dbname.".kebun_prestasi_vw where tanggal between '".$rPeriod[0]['tanggalmulai']."' and '".$rPeriod[0]['tanggalsampai']."'
                  and kodeorg like '".$param['idKebun']."%' group by tanggal,kodeorg";
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res)){
            $jjgpanen[$bar->tanggal][$bar->kodeorg]=$bar->jjgpanen;
			$notranpanen[$bar->tanggal][$bar->kodeorg]=$bar->notransaksi;
        }
        //ambil janjang spb
        $str="select sum(jjg) as jjgangkut,blok,sum(totalkg) as kgwb, tanggal,sum(brondolan) as brd from ".$dbname.".kebun_spb_vw where tanggal between '".$rPeriod[0]['tanggalmulai']."' and '".$rPeriod[0]['tanggalsampai']."'
                  and kodeorg = '".$param['idKebun']."' group by tanggal,blok";
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
            $jjgangkut[$bar->tanggal][$bar->blok]=$bar->jjgangkut;
            $brdkbn[$bar->tanggal][$bar->blok]=$bar->brd;
            $berat[$bar->tanggal][$bar->blok]=$bar->kgwb;
        }        
        //======================================
        //ambil spb per tiket
        $str="select blok,jjg,tanggal,notiket,nospb from ".$dbname.".kebun_spb_vw where tanggal between '".$rPeriod[0]['tanggalmulai']."' and '".$rPeriod[0]['tanggalsampai']."'
                  and kodeorg = '".$param['idKebun']."'";
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res)){
            $spbk[$bar->notiket][$bar->tanggal][$bar->blok]=$bar->jjg;
			$nosbp[$bar->tanggal][$bar->blok]=$bar->nospb;
            $spbktg[$bar->notiket]=$bar->tanggal;
        }
        //ambil brondolan per no tiket dari timbangan
        $str="select notransaksi,brondolan as bb from ".$dbname.".pabrik_timbangan
                  where notransaksi in(select notiket from ".$dbname.".kebun_spb_vw where tanggal between '".$rPeriod[0]['tanggalmulai']."' and '".$rPeriod[0]['tanggalsampai']."'
                  and kodeorg = '".$param['idKebun']."')";
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
            $tiket[$bar->notransaksi]=$bar->bb;
        }        
        //kalkulasi brondolan per blok spb;
        foreach($tiket as $tik =>$nx)
        {
               foreach($spbk[$tik] as $tg){
                   $tjg=array_sum($tg);
                   foreach($tg as $bl=>$jg)
                   {
                      $brd[$spbktg[$tik]][$bl]+=$jg/$tjg*$tiket[$tik];
                   }
               }    
            
        }
 $stream.="Produksi_Per_Blok ".$param['idKebun']." Periode:".$param['periode']."
         <table class=sortable border=0 cellspacing=1>
          <thead>
          <tr class=rowheader>
             <td>No</td>
             <td>".$_SESSION['lang']['tanggal']."</td>
			 <td>".$_SESSION['lang']['notransaksi']."</td>
			 <td>".$_SESSION['lang']['nospb']."</td>
             <td>".$_SESSION['lang']['blok']."</td>
             <td>".$_SESSION['lang']['thntnm']."</td>
             <td>".$_SESSION['lang']['tbs']." ".$_SESSION['lang']['panen']."(JJG)</td>
             <td>".$_SESSION['lang']['pengiriman']."(JJG)</td>
             <td>Netto(Kg)</td>           
             <td>".$_SESSION['lang']['bjr']." Actual</td>           
             <td>".$_SESSION['lang']['bjr']." ".$_SESSION['lang']['blnlalu']."</td>           
          </tr></thead><tbody>
          ";
      //jumlah hari
      $mk=mktime(0,0,0,substr($param['periode'],5,2),15,substr($param['periode'],0,4));
      $jhari=date('t',$mk);
      $a=0;
      for($x=1;$x<=$jhari;$x++){
          foreach($blok as $ki=>$bl){
            $tttt=str_pad($x, 2, "0", STR_PAD_LEFT);
            
            if($jjgpanen[$param['periode']."-".$tttt][$bl]>0 or $jjgangkut[$param['periode']."-".$tttt][$bl]>0 or $brdkbn[$param['periode']."-".$tttt][$bl]>0 or $brd[$param['periode']."-".$tttt][$bl]>0)
            {
                $a++;
                @$bjraktual=$berat[$param['periode']."-".$tttt][$bl]/$jjgangkut[$param['periode']."-".$tttt][$bl];
                if(round($bjraktual)<round($bjrlalu[$bl])){
                    $merah=' bgcolor=red';
                }else{
                    $merah='';
                }
                $stream.="<tr class=rowcontent>
                           <td>".$a."</td>
                           <td>".$param['periode']."-".$tttt."</td>
						   <td>".$notranpanen[$param['periode']."-".$tttt][$bl]."</td>
						   <td>".$nosbp[$param['periode']."-".$tttt][$bl]."</td>
                           <td>".$bl."</td>
                           <td>".$tt[$bl]."</td>";
						   #tambahan jamhari, info jika jjg panen!=jjg angkut
						   $merah2="";
						   if($jjgpanen[$param['periode']."-".$tttt][$bl]!=$jjgangkut[$param['periode']."-".$tttt][$bl]){
							$merah2=' bgcolor=red';
						   }
                $stream.="<td align=right>".number_format($jjgpanen[$param['periode']."-".$tttt][$bl])."</td>
                            <td align=right ".$merah2.">".number_format($jjgangkut[$param['periode']."-".$tttt][$bl])."</td>    
                           <td align=right>".number_format($berat[$param['periode']."-".$tttt][$bl],2)."</td> 
                           <td align=right>".number_format($bjraktual,2)."</td> 
                           <td align=right ".$merah.">".number_format($bjrlalu[$bl],2)."</td> 
                     </tr>";
                $tjp+=$jjgpanen[$param['periode']."-".$tttt][$bl];
                $tja+=$jjgangkut[$param['periode']."-".$tttt][$bl];
                $tbk+=$brdkbn[$param['periode']."-".$tttt][$bl];
                $tb+=$brd[$param['periode']."-".$tttt][$bl];
                $tberat+=$berat[$param['periode']."-".$tttt][$bl];
            }
          }
      }
      $stream.="</tbody><tfoot>
                    <tr class=rowcontent>
                       <td colspan=6>TOTAL</td>
                       <td align=right>".number_format($tjp,2)."</td>
                       <td align=right>".number_format($tja,2)."</td>
                       <td align=right>".number_format($tberat,2)."</td>
                       <td></td><td></td>    
                       </tr align=right>
                 </tfoot></table>Pastikan SPB sudah diinput dengan Benar/Make sure all FFB Transport document has been confirmed";
        //========================================
switch ($proses){
        case 'preview':
                echo $stream;
            break;
         case 'excel':
            $nop_="produksiperblok_".$param['unit']."_".$param['periode'];
            if(strlen($stream)>0)
            {
                 $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
                 gzwrite($gztralala, $stream);
                 gzclose($gztralala);
                 echo "<script language=javascript1.2>
                    window.location='tempExcel/".$nop_.".xls.gz';
                    </script>";
            }
             break;
}

?>
