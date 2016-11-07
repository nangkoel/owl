<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$mayor=$_POST['mayor'];
$str="select a.*,b.namabarang,b.satuan as satuanori from ".$dbname.".log_5stkonversi a
      left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang
      where a.kodebarang like '".$mayor."%'";
$res=mysql_query($str);
$no=0;
while($bar=mysql_fetch_object($res))
{  $no+=1;
    echo "<tr class=rowcontent>
         <td class=firsttd>".$no."</td>
         <td>".$bar->kodebarang."</td>
         <td>".$bar->namabarang."</td>
         <td>".$bar->satuanori."</td>
         <td>".$bar->satuankonversi."</td>
         <td align=right>".$bar->jumlah."</td>
         </tr>";
}
?>