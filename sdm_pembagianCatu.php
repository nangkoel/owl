<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/sdm_pembagianCatu.js'></script>
<?php
include('master_mainMenu.php');
//ambil periode penggajian
$str="select distinct periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by periode desc";
$res=mysql_query($str);
while($bar=  mysql_fetch_object($res))
{
    $optPeriode.="<option value='".$bar->periode."'>".$bar->periode."</option>";
}
OPEN_BOX('',$_SESSION['lang']['pembagiancatu']);

$frm[0].="<fieldset><legend>Form</legend>
              <table>
              <tr><td>".$_SESSION['lang']['kodeorg']."<td><td><input type=text id=kodeorg disabled class=myinputtext value='".$_SESSION['empl']['lokasitugas']."'></td></tr>
             <tr><td>".$_SESSION['lang']['periodegaji']."<td><td><select id=periode>".$optPeriode."</select></td></tr> 
             <tr><td>".$_SESSION['lang']['hargasatuan']." Catu<td><td><input type=text class=myinputtextnumber onkeypress=\"return angka_doang(event);\" id=harga size=10>/Ltr</td></tr>     
             </table>
             <button class=mybutton onclick=tampilkanCatu()>".$_SESSION['lang']['tampilkan']."</button>
             </fieldset>
             

             <div id=container style='width:850px;height:400px;overflow:scroll;'>
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

$frm[1].="<fieldset><legend>".$_SESSION['lang']['laporanCatu']."</legend>
              <table class=sortable cellspacing=1 border=0>
              <thead>
              <tr class=rowheader>
              <td>".$_SESSION['lang']['nomor']."</td>
              <td>".$_SESSION['lang']['kodeorg']."</td>
              <td>".$_SESSION['lang']['periode']."</td>
              <td>".$_SESSION['lang']['harga']."/Ltr</td>    
              <td>".$_SESSION['lang']['jumlah']." (Rp)</td>    
              <td>".$_SESSION['lang']['action']."</td>
               </tr>
               <tbody id=containerlist>";
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