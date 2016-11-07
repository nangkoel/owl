<?php
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/zLib.php');

$method			=$_POST['method'];

switch($method)
{
case'getKodeAkhir':
    //exit("Error:Masuk");
    $sPt="select distinct induk from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
    $qPt=mysql_query($sPt) or die(mysql_error($conn));
    $rPt=mysql_fetch_assoc($qPt);
    $kpl=$rPt['induk']."-".$_POST['kdAset'];
    $tppenyusutan=makeOption($dbname, 'sdm_5tipeasset', 'kodetipe,metodepenyusutan');
//    $scek="select distinct kodeasset from ".$dbname.".sdm_daftarasset 
//          where tipeasset='".$_POST['kdAset']."' and kodeorg='".$_SESSION['empl']['lokasitugas']."' order by kodeasset desc limit 0,1";
       $scek="select distinct kodeasset from ".$dbname.".sdm_daftarasset 
          where kodeasset like '".$kpl."%' order by kodeasset desc limit 0,1";
    //exit("Error:".$scek);
    $urut=0;
    $qcek=mysql_query($scek) or die(mysql_error($conn));
    $rcek=mysql_fetch_assoc($qcek);
    if($rcek['kodeasset']!='')
    {
        if(strlen($_POST['kdAset'])==3)
        {
            $urut=substr($rcek['kodeasset'],-7);
        }
        else
        {
            $urut=substr($rcek['kodeasset'],-8);
        }
    }
   // exit("Error:".);
$rer=intval($urut);
$kdcrt=$rer+1;
$kdcrt=addZero($kdcrt, 7);
if(strlen($_POST['kdAset'])<3)
{
    $kdcrt=addZero($kdcrt, 8);    
}

$kdasst=$kpl.$kdcrt;
echo $kdasst."#####".$tppenyusutan[$_POST['kdAset']];
break;
default:
break;					
}
?>
