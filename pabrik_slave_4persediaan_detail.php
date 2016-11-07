<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
?>
<script language=javascript1.2 src="js/generic.js"></script>
<script language=javascript1.2 src="js/pabrik_2pengolahan.js"></script>
<link rel=stylesheet type='text/css' href='style/generic.css'>
<?php
   
$kodeorg=$_GET['kodeorg'];
$tanggal=$_GET['tanggal'];
$barang=$_GET['barang'];
//=================================================

if($_GET['type']!='excel')$stream="<table class=sortable border=0 cellspacing=1>"; else
$stream="<table class=sortable border=1 cellspacing=1>";
$stream.="
      <thead>
        <tr class=rowcontent>
          <td>No</td>
          <td>".$_SESSION['lang']['tanggal']."</td>
          <td>".$_SESSION['lang']['NoKontrak']."</td>
          <td>".$_SESSION['lang']['material']."</td>
          <td>".$_SESSION['lang']['kuantitas']."</td>
          <td>".$_SESSION['lang']['kendaraan']."</td>
          <td>".$_SESSION['lang']['Pembeli']."</td>";
        $stream.="</tr>
      </thead>
      <tbody>";
    $str="select nokontrak, koderekanan from ".$dbname.".pmn_kontrakjual";   
    $res=mysql_query($str);
    while($bar= mysql_fetch_object($res))
    {
        $kontrak[$bar->nokontrak]=$bar->koderekanan;
    }
    $str="select kodecustomer,namacustomer from ".$dbname.".pmn_4customer";   
    $res=mysql_query($str);
    while($bar= mysql_fetch_object($res))
    {
        $kustom[$bar->kodecustomer]=$bar->namacustomer;
    }
    $str="select tanggal, nokontrak, kodebarang, beratbersih, nokendaraan from ".$dbname.".pabrik_timbangan
              where millcode = '".$kodeorg."' and tanggal like '".$tanggal."%' and kodebarang = '".$barang."'";   
    $res=mysql_query($str);
    $no=0;
    $total=0;
    while($bar= mysql_fetch_object($res))
    {
        $no+=1;    
        if(($bar->kodebarang)=='40000001')$barang='CPO';
        if(($bar->kodebarang)=='40000002')$barang='Kernel';
        $total+=$bar->beratbersih;
        $stream.="<tr class=rowcontent>
           <td align=right>".$no."</td>
           <td align=left>".$bar->tanggal."</td>    
           <td align=left>".$bar->nokontrak."</td>               
           <td align=left>".$barang."</td>               
           <td align=right>".number_format($bar->beratbersih,0)."</td>               
           <td align=left>".$bar->nokendaraan."</td>               
           <td align=left>".$kustom[$kontrak[$bar->nokontrak]]."</td>";               
         $stream.="</tr>";
    } 
        $stream.="<tr class=rowcontent>
           <td align=center colspan=4>Total</td>               
           <td align=right>".number_format($total,0)."</td>               
           <td align=center colspan=2></td>";               
         $stream.="</tr>";
   $stream.="</tbody></table>";

       echo $stream;
       
?>