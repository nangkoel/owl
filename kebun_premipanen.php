<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/kebun_premipanen.js'></script>
<?php
include('master_mainMenu.php');
//ambil periode penggajian
$str="select distinct periode from ".$dbname.".sdm_5periodegaji 
      where kodeorg='".$_SESSION['empl']['lokasitugas']."' and sudahproses=0 order by periode desc";
$res=mysql_query($str);
while($bar=  mysql_fetch_object($res)){
    $optPeriode.="<option value='".$bar->periode."'>".$bar->periode."</option>";
}
if($_SESSION['empl']['regional']=='SULAWESI'){
    if($_SESSION['empl']['lokasitugas']=='H12E'){
        $skdprem="select distinct kodeorg from ".$dbname.".kebun_5premipanen where kodeorg not in ('KALIMANTAN','SULAWESI') order by kodeorg";
    }else{
        $skdprem="select distinct kodeorg from ".$dbname.".kebun_5premipanen where (kodeorg!='KALIMANTAN' and left(kodeorg,4)!='H12E')  order by kodeorg";
    }
}else{
    $skdprem="select distinct kodeorg from ".$dbname.".kebun_5premipanen where kodeorg='KALIMANTAN' order by kodeorg";
}
$qkdprem=mysql_query($skdprem) or die(mysql_error($conn));
while($rkdprem=mysql_fetch_assoc($qkdprem)){
    $optPremi.="<option value='".$rkdprem['kodeorg']."'>".$rkdprem['kodeorg']."</option>";
}
OPEN_BOX('',$_SESSION['lang']['premipemanen']);

$frm[0].="<fieldset><legend>Form</legend>
              <table>
              <tr><td>".$_SESSION['lang']['periode']."<td><td><select id=periode style=width:150px>".$optPeriode."</select></td></tr> 
              <tr><td>".$_SESSION['lang']['kodeorg']."<td><td><input type=text id=kodeorg disabled class=myinputtext value='".$_SESSION['empl']['lokasitugas']."'></td></tr>
              <tr><td>".$_SESSION['lang']['kodepremi']."<td><td><select id=kdpremi style=width:150px>".$optPremi."</select></td></tr>     
             </table>
             <button class=mybutton onclick=getData()>".$_SESSION['lang']['preview']."</button>
             </fieldset>
             <div id=container style='width:900px;height:600px;overflow:scroll;'>
             </div>";

if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{
    $str="select sum(jumlahrupiah) as jumlah, hargacatu,kodeorg,periodegaji from ".$dbname.".sdm_catu  
             group by kodeorg,periodegaji order by periodegaji desc  limit 40";
}
else{
    $str="select sum(jumlahrupiah) as jumlah,hargacatu,sum(posting) as posting, kodeorg,periodegaji from ".$dbname.".sdm_catu 
            where kodeorg='".$_SESSION['empl']['lokasitugas']."' group by kodeorg,periodegaji 
            order by periodegaji desc  limit 40";
}

$frm[1].="<fieldset><legend>".$_SESSION['lang']['premi']." ".$_SESSION['lang']['panen']."</legend>
              <table class=sortable cellspacing=1 border=0>
              <thead>
              <tr class=rowheader>
              <td>No.</td>
              <td>".$_SESSION['lang']['kodeorg']."</td>
              <td>".$_SESSION['lang']['periode']."</td>
              <td>".$_SESSION['lang']['action']."</td>
               </tr>
               <tbody id=containerlist><script>loadData()</script>";
$frm[1].="</tbody>
              <tfoot>
              </tfoot>
              </table>";



//========================
$hfrm[0]=$_SESSION['lang']['form'];
$hfrm[1]=$_SESSION['lang']['daftar'];

//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,300,900);
//===============================================
CLOSE_BOX();
echo close_body();
?>