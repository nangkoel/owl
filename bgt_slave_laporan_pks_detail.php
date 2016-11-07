<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
?>
<link rel=stylesheet type='text/css' href='style/generic.css'>
<?php
$station =$_GET['station'];
$kdbudget=$_GET['kdbudget'];
$tahun   =$_GET['tahun'];

$str="select a.*,b.namabarang,c.nama from ".$dbname.".bgt_budget_detail a left join 
      ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang left join 
      ".$dbname.".bgt_kode c on a.kodebudget=c.kodebudget
      where a.kodeorg like '".$station."%' and a.kodebudget like '".$kdbudget."%' 
      and a.tahunbudget=".$tahun;

echo"Unit:".$station." Tahun Budget:".$tahun."
     <table class=sortable cellspacing=1 border=0 width=100%>
     <thead>
         <tr class=rowheader>
           <td align=center>".$_SESSION['lang']['nourut']."</td>
           <td align=center>".$_SESSION['lang']['mesin']."</td>
           <td align=center>".$_SESSION['lang']['kodeabs']."</td>
           <td align=center>".$_SESSION['lang']['namabarang']."</td>    
           <td align=center>".$_SESSION['lang']['jumlah']."</td> 
           <td align=center>".$_SESSION['lang']['satuan']."</td>                
           <td align=center>".$_SESSION['lang']['jumlahrp']."</td>     
         </tr>
         </thead>
         <tbody>";
$no=0;
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $no+=1;
    echo"<tr class=rowcontent>
           <td>".$no."</td>
           <td>".$bar->kodeorg."</td>
           <td>".$bar->nama."</td>
           <td>".$bar->namabarang."</td>
           <td align=right>".number_format($bar->jumlah,0,'.',',')."</td>
           <td>".$bar->satuanj."</td>     
           <td align=right>".number_format($bar->rupiah,0,'.',',')."</td>   
         </tr>";    
}
echo"</tbody>
		 <tfoot>
		 </tfoot>
		 </table>";