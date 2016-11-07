<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$proses=$_GET['proses'];
if($proses=='excel'){ 
     $unit=$_GET['kdUnit'];
}
else
{
     $unit=$_POST['kdUnit'];
}

#ambil luas yang sudah terbuka:
#kegiatan adalah tubang dan chipping
$dibuka=0;  
$str="select sum(hasilkerja) as luas from ".$dbname.".kebun_perawatan_dan_spk_vw where kodekegiatan='126010201'
            and unit='".$unit."'";  
  $res=mysql_query($str);
  while($bar=mysql_fetch_object($res))
  {
      $dibuka=$bar->luas;
  }
  if($dibuka==0)
  {
      #jika nol maka lihat stacking
        $str="select sum(hasilkerja) as luas from ".$dbname.".kebun_perawatan_dan_spk_vw where kodekegiatan='126010301'
                    and unit='".$unit."'";  
          $res=mysql_query($str);
          while($bar=mysql_fetch_object($res))
          {
              $dibuka=$bar->luas;
          }            
  }
  if($dibuka=='')
      $dibuka=0;
  
$stream='';
//==============Free
        $str1="select a.*,b.nama,b.alamat,b.desa,c.namakaryawan from ".$dbname.".pad_lahan a
            left join ".$dbname.".pad_5masyarakat b on a.pemilik=b.padid 
            left join ".$dbname.".datakaryawan c on a.updateby=c.karyawanid    
            where posting=0 and unit='".$unit."' order by b.nama,b.desa limit 500";       
if($res1=mysql_query($str1))
{
    if($proses=='preview') {    
        $stream.="<table class=sortable cellspacing=1 border=0 width=2500px>";
        $add='';
     }
     else
     {
         $stream.="<table border=1>";
         $add=" bgcolor=#dedede";
     }
     $stream.="<thead>
                <tr class=rowheader>
               <td rowspan=2 ".$add.">*</td>  
               <td rowspan=2 ".$add.">No</td>
                <td rowspan=2 ".$add.">".$_SESSION['lang']['id']."</td>
                <td rowspan=2 ".$add.">".$_SESSION['lang']['unit']."</td>                     
                <td rowspan=2 ".$add.">".$_SESSION['lang']['pemilik']."</td>
                <td rowspan=2 ".$add.">".$_SESSION['lang']['lokasi']."/(No.Persil)</td>                       
                <td rowspan=2 ".$add.">".$_SESSION['lang']['desa']."</td>               
                <td rowspan=2 ".$add.">".$_SESSION['lang']['luas']."</td>    
                <td rowspan=2 ".$add.">".$_SESSION['lang']['bisaditanam']."</td> 
                <td rowspan=2 ".$add.">".$_SESSION['lang']['blok']."</td>    
                <td colspan=4 align=center ".$add.">".$_SESSION['lang']['batas']."</td> 
                <td colspan=7 align=center ".$add.">".$_SESSION['lang']['biaya']."-".$_SESSION['lang']['biaya']."</td>  
                <td colspan=4 align=center ".$add.">".$_SESSION['lang']['status']."</td>    
                <td rowspan=2 ".$add.">".$_SESSION['lang']['nomor']." ".$_SESSION['lang']['dokumen']."</td>
                <td rowspan=2 ".$add.">".$_SESSION['lang']['keterangan']."</td> 
                 <td rowspan=2 ".$add.">".$_SESSION['lang']['updateby']."</td>   
                 </tr><tr class=rowheader>   
                <td ".$add.">".$_SESSION['lang']['batastimur']."</td>                      
                <td ".$add.">".$_SESSION['lang']['batasbarat']."</td>  
                <td ".$add.">".$_SESSION['lang']['batasutara']."</td>
                <td ".$add.">".$_SESSION['lang']['batasselatan']."</td> 
                    
                <td ".$add.">".$_SESSION['lang']['tanamtumbuh']." (Rp)</td> 
                <td ".$add.">".$_SESSION['lang']['gantilahan']." (Rp)</td> 
                <td ".$add.">".$_SESSION['lang']['total']."<br>".$_SESSION['lang']['gantilahan']." (Rp)</td>    
                <td ".$add.">".$_SESSION['lang']['biaya']."<br>".$_SESSION['lang']['camat']." (Rp)</td> 
                <td ".$add.">".$_SESSION['lang']['biaya']."<br>".$_SESSION['lang']['kades']." (Rp)</td>
                <td ".$add.">".$_SESSION['lang']['biaya']."<br>Matrai (Rp)</td>
                <td ".$add.">".$_SESSION['lang']['total']."<br>".$_SESSION['lang']['biaya']." (Rp)</td>     
                    
                <td ".$add.">".$_SESSION['lang']['status']."<br>".$_SESSION['lang']['permintaandana']."</td>
                <td ".$add.">".$_SESSION['lang']['status']."<br>".$_SESSION['lang']['pembayaran']."</td>
                <td ".$add.">".$_SESSION['lang']['status']."<br>".$_SESSION['lang']['desa']."</td>
                <td ".$add.">".$_SESSION['lang']['status']."<br>".$_SESSION['lang']['camat']."</td>
                </tr></thead>
                <tbody>";
 $no=0;
 while($bar1=mysql_fetch_object($res1))
        {
        $no++;
         $stdana=$bar1->statuspermintaandana==1?tanggalnormal($bar1->tanggalpengajuan):"";
         
         if($bar1->statuspermbayaran==1){
                 $stbayar=tanggalnormal($bar1->tanggalbayar)." Belum Lunas";
         }else if($bar1->statuspermbayaran==0){
                 $stbayar='Belum Bayar';
         }else if($bar1->statuspermbayaran==2){
                  $stbayar=tanggalnormal($bar1->tanggalbayar)." Lunas";
         }
         $stkades=$bar1->statuskades==1?tanggalnormal($bar1->tanggalkades):"";
         $stcamat=$bar1->statuscamat==1?tanggalnormal($bar1->tanggalcamat):"";
                $stream.="<tr class=rowcontent>                 
                          <td>";                      
                if($proses=='preview')               
                {
                    $stream.="<img src='images/skyblue/pdf.jpg' class='resicon' onclick=\"ptintPDF('".$bar1->idlahan."','".$bar1->pemilik."',event);\" title='Print Data Detail'>";                               
                }
               $stream.="</td>
                           <td>".$no."</td>
                           <td>".$bar1->idlahan."</td>
                           <td>".$bar1->unit."</td>
                           <td>".$bar1->nama."</td>
                           <td>".$bar1->lokasi."</td>                                 
                           <td>".$bar1->desa."</td>
                           <td align=right>".$bar1->luas."</td>  
                           <td align=right>".$bar1->luasdapatditanam."</td>
                           <td>".$bar1->kodeblok."</td>    
                           <td>".$bar1->batastimur."</td>
                           <td>".$bar1->batasbarat."</td>
                           <td>".$bar1->batasutara."</td>
                           <td>".$bar1->batasselatan."</td>  
                           <td align=right>".number_format($bar1->rptanaman,0)."</td>    
                           <td align=right>".number_format($bar1->rptanah,0)."</td>
                           <td align=right>".number_format($bar1->totalgantirugi,0)."</td>    
                           <td align=right>".number_format($bar1->biayakades,0)."</td>
                           <td align=right>".number_format($bar1->biayacamat,0)."</td>
                           <td align=right>".number_format($bar1->biayamatrai,0)."</td>
                           <td align=right>".number_format(($bar1->totalgantirugi+$bar1->biayakades+$bar1->biayacamat+$bar1->biayamatrai),0)."</td>
                            <td>".$stdana."</td>
                           <td>".$stbayar."</td>
                           <td>".$stkades."</td>
                           <td>".$stcamat."</td>        
                           <td>".$bar1->nosurat."</td>  
                           <td>".$bar1->keterangan."</td>   
                           <td>".$bar1->namakaryawan."</td>                                
                            </td></tr>";
                $tluas+=$bar1->luas;
                $ditanam+=$bar1->luasdapatditanam;
                $ttanaman+=$bar1->rptanaman;
                $ttanah+=$bar1->rptanah;
                $tgrugi+=$bar1->totalgantirugi;
                $tkades+=$bar1->biayakades;
                $tcamat+=$bar1->biayacamat;
                $tmaterai+=$bar1->biayamatrai;   
                $ttl+=$bar1->totalgantirugi+$bar1->biayakades+$bar1->biayacamat+$bar1->biayamatrai;
                
        }	 
        #print Total
    $stream.="<tr class=rowcontent>                 
              <td colspan=7>TOTAL</td>
               <td align=right>".$tluas."</td>  
               <td align=right>".$ditanam."</td>
                <td>Sudah Dibuka:".$dibuka." Ha</td>   
               <td colspan=4></td>  
               <td align=right>".number_format($ttanaman,0)."</td>    
               <td align=right>".number_format($ttanah,0)."</td>
               <td align=right>".number_format($tgrugi,0)."</td>    
               <td align=right>".number_format($tkades,0)."</td>
               <td align=right>".number_format($tcamat,0)."</td>
               <td align=right>".number_format($tmaterai,0)."</td>
               <td align=right>".number_format($ttl,0)."</td>
                <td colspan=7></td>                                
                </td></tr>";        
        $stream.="	 
                 </tbody>
                 <tfoot>
                 </tfoot>
                 </table>
                 Note: Luas dibuka merupakan kegiatan Tumbang atau Stacking";
}
if($proses=='preview') 
    echo $stream;
else{
        $nop_="Data_Pembebasan_Lahan".$unit;
        if(strlen($stream)>0)
        {
             $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
             gzwrite($gztralala, $stream);
             gzclose($gztralala);
             echo "<script language=javascript1.2>
                window.location='tempExcel/".$nop_.".xls.gz';
                </script>";
        } 
}
?>
