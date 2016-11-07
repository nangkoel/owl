<?php
require_once('master_validation.php');
require_once('config/connection.php');

$kodeorg=$_POST['kodeorg'];
$thnbudget=$_POST['thnbudget'];
#ambil produksi pks
$tbs=0;
$cpo=0;
$pk=0;
$str="select sum(kgolah) as tbs,sum(kgcpo) as cpo,sum(kgkernel) as kernel from ".$dbname.".bgt_produksi_pks_vw 
      where tahunbudget=".$thnbudget." and millcode = '".$kodeorg."'";
$res=mysql_query($str);
//echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{
    $tbs=$bar->tbs;
    $cpo=$bar->cpo;
    $pk=$bar->kernel;
}
$stream="<fieldset><legend>".$_SESSION['lang']['produksipabrik']." (".$_SESSION['lang']['ton'].")</legend>
<table class=sortable cellspacing=1 border=0 width=300px>
     <thead>
         <tr class=rowheader>
           <td align=center>".$_SESSION['lang']['tbsdiolah']."</td>
           <td align=center>Palm Product</td>    
           <td align=center>".$_SESSION['lang']['cpo']."</td>
           <td align=center>".$_SESSION['lang']['kernel']."</td>
         </tr>
         </thead>
         <tbody>
         <tr class=rowcontent>
           <td align=right>".number_format($tbs/1000,0,".",",")."</td>
           <td align=right>".number_format(($cpo+$pk)/1000,0,".",",")."</td>    
           <td align=right>".number_format($cpo/1000,0,".",",")."</td>
           <td align=right>".number_format($pk/1000,0,".",",")."</td>
         </tr>     
     </tbody>
     <tfoot>
     </tfoot>
     </table>
     </fieldset>"; 

$str="select a.*,b.namaorganisasi,c.nama from ".$dbname.".bgt_pks_station_vw a left join
      ".$dbname.".organisasi b on a.station=b.kodeorganisasi left join ".$dbname.".bgt_kode c on a.kdbudget=c.kodebudget
      where tahunbudget=".$thnbudget." and a.station like '".$kodeorg."%'
      ";
$res=mysql_query($str);
$no=0;
$rpperha=0;
$stream.="<fieldset><legend>".$_SESSION['lang']['list']."
            <img onclick=\"fisikKeExcel(event,'bgt_slave_laporan_biaya_pks_excel.php')\" src=\"images/excel.jpg\" class=\"resicon\" title=\"MS.Excel\"> 
	     <img onclick=\"fisikKePDF(event,'bgt_slave_laporan_biaya_pks_pdf.php')\" title=\"PDF\" class=\"resicon\" src=\"images/pdf.jpg\">
            </legend>
             ".$_SESSION['lang']['unit'].":".$kodeorg." ".$_SESSION['lang']['budgetyear'].":".$thnbudget."
             <table class=sortable cellspacing=1 border=0' width=100%>
	     <thead>
		 <tr class=rowheader>
                   <td align=center>".$_SESSION['lang']['nourut']."</td>
                   <td align=center>".$_SESSION['lang']['station']."</td>
                   <td align=center>".$_SESSION['lang']['kodeabs']."</td>
                   <td align=center>".$_SESSION['lang']['jumlahrp']."</td>
                   <td align=center>".$_SESSION['lang']['rpperkg']."-TBS</td> 
                   <td align=center>".$_SESSION['lang']['rpperkg']."-CPO</td>
                   <td align=center>".$_SESSION['lang']['rpperkg']."-PP</td>
                 </tr>
		 </thead>
		 <tbody>"; 
$old='';
$jumlah=0;
$grandtt=0;
while($bar=mysql_fetch_object($res))
{
    $no+=1;
    $new=$bar->station;
    //$jumlah+=$bar->rupiah;
    $grandtt+=$bar->rupiah;
    if($bar->kdbudget=='M')
        $nama_komponen="Material";
    else
        $nama_komponen=$bar->nama;
    
    if($old!='' and $old!=$new)
    {
        #subtotal
        @$jumlahpercpo=$jumlah/($cpo+$pk);
        @$jumlahpertbs=$jumlah/$tbs;
        @$jmlhCpo=$jumlah/$cpo;
    $stream.="<tr class=rowheader>
           <td colspan=3>".$_SESSION['lang']['total']."</td>
           <td align=right>".number_format($jumlah,0,'.',',')."</td>
           <td align=right>".number_format($jmlhCpo,3,'.',',')."</td> 
           <td align=right>".number_format($jumlahpertbs,3,'.',',')."</td> 
           <td align=right>".number_format($jumlahpercpo,3,'.',',')."</td>
         </tr>";        
        $jumlah=0;
        $jumlah+=$bar->rupiah;
    }
    else
    {
        $jumlah+=$bar->rupiah;
    }
    
    @$rupiahpercpo=$bar->rupiah/($cpo+$pk);
    @$rupiahpertbs=$bar->rupiah/$tbs;
    @$rupiahpercpo2=$bar->rupiah/$cpo;
    $stream.="<tr class=rowcontent style='cursor:pointer;' onclick=\"showDt('".$bar->station."','".$bar->kdbudget."','".$thnbudget."',event)\">
           <td>".$no."</td>
           <td>".$bar->namaorganisasi."</td>
           <td>".$nama_komponen."</td>
           <td align=right>".number_format($bar->rupiah,0,'.',',')."</td>
           <td align=right>".number_format($rupiahpercpo2,3,'.',',')."</td> 
           <td align=right>".number_format($rupiahpertbs,3,'.',',')."</td>     
           <td align=right>".number_format($rupiahpercpo,3,'.',',')."</td>
           
            
         </tr>";
    $old=$bar->station;
}
#subtotal terakhir
        @$jumlahpercpo=$jumlah/($cpo+$pk);
        @$jumlahpertbs=$jumlah/$tbs;
        @$jumlahpercpo2=$jumlah/$cpo;
    $stream.="<tr class=rowheader>
           <td colspan=3 align=center>".$_SESSION['lang']['total']."</td>
           <td align=right>".number_format($jumlah,0,'.',',')."</td> 
           <td align=right>".number_format($jumlahpercpo2,3,'.',',')."</td> 
           <td align=right>".number_format($jumlahpertbs,0,'.',',')."</td>
           <td align=right>".number_format($jumlahpercpo,3,'.',',')."</td>
              
         </tr>"; 
    @$grandttpercpo=$grandtt/($cpo+$pk);
    @$grandttpertbs=$grandtt/$tbs;
    @$grandttpercpo2=$grandtt/$cpo;
        $stream.="<tr class=rowcontent>
           <td colspan=3 align=center>".$_SESSION['lang']['grnd_total']."</td>
           <td align=right>".number_format($grandtt,0,'.',',')."</td>
           <td align=right>".number_format($grandttpercpo2,3,'.',',')."</td>    
           <td align=right>".number_format($grandttpertbs,3,'.',',')."</td> 
           <td align=right>".number_format($grandttpercpo,3,'.',',')."</td>
         </tr>";     
$stream.="</tbody>
		 <tfoot>
		 </tfoot>
		 </table>";
echo $stream; 
?>