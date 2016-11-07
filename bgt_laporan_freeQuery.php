<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/bgt_freeQuery.js'></script>

<?php
include('master_mainMenu.php');
OPEN_BOX('',  strtoupper($_SESSION['lang']['budget']).' FREE QUERY');
$optOrg="";
$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi 
       where length(kodeorganisasi)=4 and tipe='KEBUN' order by namaorganisasi asc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}

//ambil tanub budget
$str="select distinct(tahunbudget) as tahunbudget  
      from ".$dbname.".bgt_budget  order by tahunbudget";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $optthn.="<option value='".$bar->tahunbudget."'>".$bar->tahunbudget."</option>";
}

//ambil kegiatan
$str="select kodekegiatan,namakegiatan,kelompok  
      from ".$dbname.".setup_kegiatan where
      kelompok in('TB','BBT','TBM','TM','PNN')
      order by kelompok asc,namakegiatan";

$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $optkeg.="<option value='".$bar->kodekegiatan."'>".$bar->kelompok." - ".$bar->namakegiatan."</option>";
}



echo"<fieldset style='width:500px;'><legend>".$_SESSION['lang']['form']."</legend>";
echo"<table>
     <tr>
          <td>".$_SESSION['lang']['budgetyear']."</td>
          <td><select id='thnbudget'>".$optthn."</select></td>    
     </tr>
     <tr>
          <td>".$_SESSION['lang']['kodeorg']."</td>
          <td><select id='kodeorg'>".$optOrg."</select></td>    
     </tr>
     <tr>
          <td>".$_SESSION['lang']['kegiatan']."</td>
          <td><select id='kegiatan'>".$optkeg."</select></td>    
     </tr>     
</table>
<button class=mybutton onclick=getFreeQuery()>".$_SESSION['lang']['lihat']."</button>";
echo"</fieldset><br>
<fieldset><legend>".$_SESSION['lang']['list']."</legend>
  <div id=container  style='width:1000px,overflow:scroll'></div>  
</fieldset>    
";


CLOSE_BOX();
echo close_body();
?>