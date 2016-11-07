<?php //@Copy nangkoelframework
require_once('master_validation.php');
require_once('config/connection.php');
include('lib/nangkoelib.php');

//+++++++++++++++++++++++++++++++++++++++++++++
if($_GET['proses']){
    $tahun=$_GET['tahun'];
}
else{
  $tahun=$_POST['tahun'];
}

if($_POST['proses']!='getDetail'){
#BKM
$str="select tipetransaksi,kodeorg,left(tanggal,7) as periode,avg(selisih) as posting from ".$dbname.".sdm_kpidata_vw
          where left(tanggal,4)='".$tahun."'  group by tipetransaksi,kodeorg,left(tanggal,7)";

$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $data[$bar->tipetransaksi][$bar->kodeorg][$bar->periode]=$bar->posting;
    $tipetransaksi[$bar->tipetransaksi]=$bar->tipetransaksi;
}

$str="select kodeorganisasi from ".$dbname.".organisasi where length(kodeorganisasi)=4 and kodeorganisasi not like '%HO' order by kodeorganisasi";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $kebun[]=$bar->kodeorganisasi;
}
if($_GET['proses']=='excel')
{
    $border=1;
}
else{
    $border=0;
}
$bulan=Array('01','02','03','04','05','06','07','08','09','10','11','12');
$stream="<fieldset style='width:700px;'><legend>Note:</legend>";
if($_SESSION['language']=='ID'){
 $stream.="<i>-Rata-rata setiap bulan per jenis data adalah  rata-rata selisih hari dari tanggal transaksi sampai tanggal posting.</i><br>
                   <i>-Total  per jenis data (total per baris) adalah  rata-rata dari rata-rata bulanan.</i><br>
                   <i>-Total  per bulan (total per kolom) adalah  rata-rata dari rata-rata per jenis data.</i><br>
                   Cara Membaca:
                   <br>* angka 1.5 (satu koma lima) berarti rata rata posting adalah 1.5 hari setelah transaksi/kegiatan terjadi,
                   <br>*angka 21.6 (dua puluh satu koma enam) berarti rata rata posting adalah 21.6 hari setelah transaksi/kegiatan terjadi
                   <br>*'No Data' :kemungkinan adalah tidak ada jenis data tersebut pada database atau belum ada yang di posting";
}else{
  $stream.="<i>-On average each month per type of data is the average difference in days from the date of the transaction until the date of confirmation.</i><br>
                   <i>-Total per type of data (total per line) is the average of the monthly average.</i><br>
                   <i>-Total per month (total per column) is the average of the average for each type of data.</i><br>
                   How to read:
                   <br>* figure 1.5 (one point five) means the average was 1.5 days after transactions date,
                   <br>* figure 21.6 (twenty-one point six) means the average was 21.6 days after transaction date,
                   <br>*'No Data' :chances are there is no such kind of data in the database or not posted yet";   
}
$stream.="</fieldset>
                  <br>KPI Data and Posting ".$_SESSION['lang']['periode']." ".$tahun."
                  <table class=sortable cellspacing=1 border=".$border.">
                  <thead>
                      <tr class=rowcontent>
                         <td>".$_SESSION['lang']['unit']."</td>
                         <td>".$_SESSION['lang']['tipetransaksi']."</td>
                         <td>AVG ".$bulan[0]."</td>
                         <td>AVG ".$bulan[1]."</td>  
                         <td>AVG ".$bulan[2]."</td>  
                         <td>AVG ".$bulan[3]."</td>  
                         <td>AVG ".$bulan[4]."</td>  
                         <td>AVG ".$bulan[5]."</td>  
                         <td>AVG ".$bulan[6]."</td>  
                         <td>AVG ".$bulan[7]."</td>  
                         <td>AVG ".$bulan[8]."</td>  
                         <td>AVG ".$bulan[9]."</td>  
                         <td>AVG ".$bulan[10]."</td>  
                         <td>AVG ".$bulan[11]."</td>
                         <td>AVG ".$_SESSION['lang']['setahun']."</td>      
                      </tr>
                  </thead>
                  <tbody>";
#looping per kebun
foreach($kebun as $key=>$val){
    foreach($tipetransaksi as $keey=>$vaal) {
            $stream.="<tr class=rowcontent><td>".$val."</td><td>".$vaal."</td>";
            $tbaris=0;
          foreach($bulan as $keeey=>$vaaal){
              $tampil=$data[$vaal][$val][$tahun."-".$vaaal]!=0?number_format($data[$vaal][$val][$tahun."-".$vaaal],2):'--';
              $stream.="<td align=right onclick=lihatDetail('".$val."','".$vaal."','".$tahun."-".$vaaal."',event) title='Click for details' style='cursor:pointer;'>".$tampil."</td>";
              $tbaris+=$data[$vaal][$val][$tahun."-".$vaaal];
              $tcolom[$val][$vaaal]+=$data[$vaal][$val][$tahun."-".$vaaal];
              if(isset($data[$vaal][$val][$tahun."-".$vaaal]))
              {
                  $availcolum[$val][$vaaal]+=1;
              }
        }
      #total baris
       $stream.="<td align=right>".@number_format($tbaris/count($data[$vaal][$val]),2)."</td></tr>";
    }
  $stream.="<tr class=rowcontent><td bgcolor=#dedede colspan=2 align=right>".$_SESSION['lang']['total']." ".$val."</td>";
  $countable=0;   
  foreach($tcolom[$val] as $keykol=>$viil)
     {
         $stream.="<td bgcolor=#dedede align=right>".@number_format(($viil/$availcolum[$val][$keykol]),2)."</td>";
         if($viil>0)
             $countable+=1;
     }
        if($countable==0){
            $display='No Data';
        }
        else{
            $display=number_format((array_sum($tcolom[$val])/$countable),2);
        }
      $stream.="<td bgcolor=#dedede align=right>".$display."</td></tr>";    
}
$stream.="</tbody><tfoot></tfoot></table>
     ";
}

if($_GET['proses']=='excel')#this is GET
{
        $nop_="KPI Data Dan Posting tahun ".$tahun;
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
else if($_POST['proses']=='getDetail'){#this is POST
    $str="select *,b.namakaryawan from ".$dbname.".sdm_kpidata_vw a 
     left join ".$dbname.".datakaryawan b on a.oleh=b.karyawanid
     where kodeorg='".$_POST['unit']."' and tipetransaksi='".$_POST['jenisdata']."'
     and tanggal like '".$_POST['periode']."%' order by tanggal";
 $res=  mysql_query($str);
 $stream="<table class=sortable border=0 cellspacing=1 width='600px;'>
                  <thead><tr class=rowheader>
                  <td>".$_SESSION['lang']['kodeorg']."</td>
                  <td>".$_SESSION['lang']['tipetransaksi']."</td>
                  <td>".$_SESSION['lang']['tanggal']."</td>
                  <td>".$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['posting']."</td>
                  <td>".$_SESSION['lang']['selisih']."</td>
                  <td>".$_SESSION['lang']['notransaksi']."</td>
                  <td>".$_SESSION['lang']['posting']."</td>
                  </tr></thead>
                  <tbody>";   
 $x=0;
 $tt=0;
 while($bar=mysql_fetch_object($res))
 {
     $stream.="<tr class=rowcontent>
                  <td>".$bar->kodeorg."</td>
                  <td>".$bar->tipetransaksi."</td>
                  <td>".$bar->tanggal."</td>
                  <td>".$bar->posting."</td>
                  <td align=center>".$bar->selisih."</td>
                  <td>".$bar->notransaksi."</td>
                  <td>".$bar->namakaryawan."</td>
                  </tr>";
     $x++;
     $tt+=$bar->selisih;
 }
 $stream.="<tr class=rowcontent><td colspan=4>Total</td><td align=right>".@number_format($tt/$x,2)."</td><td colspan=2></td></tr>";
 $stream.="</tbody><tfoot></tfoot></table>";
 echo $stream;
}
else#get Detail #this is  POST
{
     echo $stream;
}
?>