<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{
    $str="select sum(jumlahrupiah) as jumlah,hargacatu, kodeorg,periodegaji from ".$dbname.".sdm_catu  
             group by kodeorg,periodegaji order by periodegaji desc limit 40";
}
else{
    $str="select sum(jumlahrupiah) as jumlah,hargacatu,sum(posting) as posting, kodeorg,periodegaji from ".$dbname.".sdm_catu 
            where kodeorg='".$_SESSION['empl']['lokasitugas']."' group by kodeorg,periodegaji 
            order by periodegaji desc  limit 40";
}
$res=  mysql_query($str);
$no=0;
while($bar=mysql_fetch_object($res))
{
    $no+=1;
    $frm[1].="<tr class=rowcontent>
                  <td>".$no."</td>
                    <td>".$bar->kodeorg."</td> 
                    <td>".$bar->periodegaji."</td>
                    <td>".number_format($bar->hargacatu,0,'.',',')."</td>      
                    <td>".number_format($bar->jumlah,0,'.',',')."</td>    
                    <td><img src='images/excel.jpg' class='resicon' title='Excel' onclick=getExcel(event,'sdm_slave_pembagianCatuExcel.php','".$bar->kodeorg."','".$bar->periodegaji."') > &nbsp &nbsp";
         if($bar->posting>0)    
               $frm[1].="<img src='images/skyblue/posted.png'>";
         else
               $frm[1].="<img src='images/skyblue/posting.png'  class='resicon' title='Posting' onclick=postingCatu('".$bar->kodeorg."','".$bar->periodegaji."',".$bar->jumlah.")>";
     $frm[1].="</td>    
                  </tr>";
}
echo $frm[1];
?>
