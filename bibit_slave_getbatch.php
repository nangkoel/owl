<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/fpdf.php');

$kodeorg=$_POST['kodeorg'];
$tipe=$_POST['tipe'];

$hasil='';
 
if($tipe=='batch')
{
    $str="select distinct a.batch from ".$dbname.".bibitan_batch a
        left join ".$dbname.".bibitan_mutasi b on a.batch=b.batch
        where b.kodeorg like '".$kodeorg."%'
        order by a.batch";
    $res=mysql_query($str);
    $hasil="<option value=''>".$_SESSION['lang']['all']."</option>";
    while($bar=mysql_fetch_object($res))
    {
        $hasil.="<option value='".$bar->batch."'>".$bar->batch."</option>";
    }    
}else{
    
}

echo $hasil;
?>