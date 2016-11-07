<?php
require_once('master_validation.php');
require_once('config/connection.php');

$periode=$_POST['periode'];
$karyawanid=$_POST['karyawanid'];
$komponen=$_POST['komponen'];
$tipekaryawan=$_POST['tipekaryawan'];

#periksa periode pembukuan
$str="select periode from ".$dbname.".setup_periodeakuntansi 
      where kodeorg='".$_SESSION['empl']['lokasitugas']."' and periode='".$periode."'
      and tutupbuku=1";
$res=mysql_query($str);
if(mysql_numrows($res)>0)
{
    echo "Error:Periode Akuntansi sudah tutup buku";
}
else
{
    $addwhere='';
    if($komponen!='all')
        $addwhere.=" and idkomponen=".$komponen;
    if ($karyawanid!='all')
        $addwhere.=" and karyawanid='".$karyawanid."'";
    else
    {
        if($tipekaryawan!='all')
        {
           $addwhere.=" and karyawanid in(select karyawanid from ".$dbname.".datakaryawan 
               where sistemgaji='".$tipekaryawan."')"; 
        }
    }
        
    $str="delete from ".$dbname.".sdm_gaji where  periodegaji='".$periode."' and
        kodeorg='".$_SESSION['empl']['lokasitugas']."' ".$addwhere;
    if(mysql_query($str))
    {
        echo "Done";
    }
    else
    {
        echo mysql_error($conn);
    }
}    


?>
