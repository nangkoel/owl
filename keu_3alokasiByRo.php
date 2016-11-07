<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/alokasiByRo.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('','REGIONAL COST ALLOCATIONS (to working unit)');
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
$res=mysql_query($str);
$optOrg='';
while($bar=mysql_fetch_object($res))
{
    $optOrg.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}
echo"<fieldset><legend>".$_SESSION['lang']['sumber']."</legend><table>
       <tr><td>".$_SESSION['lang']['kodeorg']."</td>
              <td><select id=kodeorg>".$optOrg."</select></td></tr>
        <tr><td>".$_SESSION['lang']['periode']."</td><td><input type=text size=12 id=periode disabled  class=myinputtext value='".$_SESSION['org']['period']['tahun']."-".$_SESSION['org']['period']['bulan']."'></td></tr>
</table></fieldset>";
echo"<table><tr><td><fieldset><legend>".$_SESSION['lang']['tujuan']."</legend><table>";
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PT' order by namaorganisasi";
$res=mysql_query($str);
$no=0;
while($bar=mysql_fetch_object($res))
{
    $no+=1;
    echo "<tr><td><select id=pt".$no."><option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option></select></td>
                <td>".$_SESSION['lang']['logouang'].".<input type=text class=myinputtextnumber id=jumlah".$no." onkeypress=\"return angka_doang(event)\" maxlength=15 size=15 onblur=hitungTotal(".  mysql_num_rows($res).")><button onclick=alokasiKan(".$no.") class=mybutton id=button".$no.">".$_SESSION['lang']['proses']."</button></td></tr>";
}
echo"<tr><td>".$_SESSION['lang']['total']."</td><td>".$_SESSION['lang']['logouang'].".<input type=text class=myinputtextnumber size=15 maxlength=15 id=total></td></tr>";
if($_SESSION['language']=='EN'){
            echo"</table></fieldset>
         </td><td>
         <fieldset style='width:250px;'><legend>Info:</legend>
           The allocation process will only apply to estate units in the destination company, 
           divided by the area of the estate in the company, and in the one unit will be divided based on the extent of TBM and TM (if any).
        </fieldset>
         </td></tr></table>";
}else{
        echo"</table></fieldset>
         </td><td>
         <fieldset style='width:250px;'><legend>Info:</legend>
         Proses alokasi ini hanya akan berlaku untuk unit kebun dalam PT tujuan, dibagi berdasarkan luasan areal per unit kebun dalam satu PT, dan di dalam satu unit akan dibagi ber
         dasarkan luasan TBM dan TM (jika ada).
        </fieldset>
         </td></tr></table>";
}
CLOSE_BOX();
close_body();
?>