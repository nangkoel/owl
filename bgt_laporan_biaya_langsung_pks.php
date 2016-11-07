<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/zTools.js'></script>
<script language=javascript1.2 src='js/bgt_btl_kebun.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['biayalangsung']);
#ambil tahun budget
$str="select distinct(tahunbudget) as tahunbudget from  ".$dbname.".bgt_budget order by tahunbudget desc";
$res=mysql_query($str);
$opttahun="<option value=''>Pilih..</option>";
while($bar=mysql_fetch_object($res))
{
    $opttahun.="<option value='".$bar->tahunbudget."'>".$bar->tahunbudget."</option>";
}
#ambil kode kebun
$str="select kodeorganisasi as kodeorg from  ".$dbname.".organisasi where tipe='PABRIK' order by kodeorganisasi";
$res=mysql_query($str);
$optunit="<option value=''>Pilih..</option>";
while($bar=mysql_fetch_object($res))
{
    $optunit.="<option value='".$bar->kodeorg."'>".$bar->kodeorg."</option>";
}

echo"<fieldset style='width:500px;'><table>
     <tr><td>".$_SESSION['lang']['tahunanggaran']."</td><td><select id=thnbudget style='width:200px'>".$opttahun."</select></td></tr>
	 <tr><td>".$_SESSION['lang']['kodeorganisasi']."</td><td><select id=kodeunit style='width:200px'>".$optunit."</select></td></tr>
     </table>
	 <input type=hidden id=method value='insert'>
	 <button class=mybutton onclick=tampilkanBLPks()>".$_SESSION['lang']['save']."</button>
	 </fieldset>";
echo "<div id=container>
</div>";


CLOSE_BOX();
echo close_body();
?>