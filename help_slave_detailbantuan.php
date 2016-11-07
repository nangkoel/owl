<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
?>
<script language=javascript1.2 src="js/generic.js"></script>
<script language=javascript1.2 src="js/help_bantuan.js"></script>
<link rel=stylesheet type=text/css href=style/generic.css>

<?php      
$proses = $_GET['proses'];
$param = $_GET;

$where = "kode='".$param['index']."' and modul='".$param['modul']."'";
$query = selectQuery($dbname,'owl_help','*',$where);
$res=mysql_query($query);

while($bar=mysql_fetch_object($res))
{
    $isi = $bar->isi;  
    $html= $bar->tujuan;
}
$isi=str_replace("<##","<image src='help/",$isi);
$isi=str_replace("##>","'>",$isi);
$isi=str_replace("<HH","<a href=# onclick='loadIndex(",$isi);
$isi=str_replace("H#H","'><",$isi);
$isi=str_replace("HH>","</a>",$isi);
$stream="$isi"; 
echo "<fieldset><legend>".$param['modul']."</legend>";
echo "<div width='350' height='350'>";
echo $stream;
echo "<hr>";
$dd=str_replace("help/","",$html);
if($dd=='null'){}
else{
    include($html);
}
echo "</div>";
echo "</fieldset>";    
?>
