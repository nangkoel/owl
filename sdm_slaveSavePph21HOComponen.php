<?php
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
require_once('config/connection.php');

$to=$_POST['to'];
$idx=$_POST['idx'];


$iCek="select count(*) as ada from ".$dbname.".sdm_5komponenpph  where id='".$idx."' and regional='".$_SESSION['empl']['regional']."' ";
$nCek=mysql_query($iCek) or die (mysql_error($conn));
$dCek=mysql_fetch_assoc($nCek);

if($dCek['ada']>0)
{
    $str1="update ".$dbname.".sdm_5komponenpph 
                       set `status`=".$to.",updateby='".$_SESSION['standard']['userid']."' 
                       where id='".$idx."' and regional='".$_SESSION['empl']['regional']."' ";	   

                if(mysql_query($str1,$conn))
        {}
        else
        {echo " Error: ".addslashes(mysql_error($conn));} 
}
else
{
    
    
    $str1="INSERT INTO ".$dbname.".`sdm_5komponenpph` (`regional`, `id`, `status`, `updateby`) VALUES "
            . " ('".$_SESSION['empl']['regional']."','".$idx."','".$to."','".$_SESSION['standard']['userid']."')";	   

            if(mysql_query($str1,$conn))
    {}
    else
    {echo " Error: ".addslashes(mysql_error($conn));} 
}

						
?>
