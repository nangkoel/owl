<?php
//ind
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');


$proses=$_POST['proses'];
$karyawanid=$_POST['karyawanid'];
$tk=$_POST['tk'];
$unit=$_POST['unit'];

	

switch($proses)
{
   
    case'savedata':
       
        
        if($tk=='4')
        {
            $upah=76040;
        }
        else
        {
            $upah=0;
        }
        
        for($i=1;$i<=30;$i++)
        {
            if(strlen($i)<2)
            {
                $i='0'.$i;
            }
            
            $tgl="2014-06-".$i;
            
            $iCek=" select count(*) as ada from ".$dbname.".vhc_runhk where tanggal='".$tgl."' and idkaryawan='".$karyawanid."' ";
            $nCek=mysql_query($iCek) or die (mysql_error($conn));
            $dCek=mysql_fetch_assoc($nCek);
                $adaVhc=$dCek['ada'];
            
            
            if($adaVhc>0)
            {
            }
            else 
            {
                $iAbs=" select count(*) as ada from ".$dbname.".sdm_absensidt where tanggal='".$tgl."' and karyawanid='".$karyawanid."' ";
               
                $nAbs=mysql_query($iAbs) or die (mysql_error($conn));
                $dAbs=mysql_fetch_assoc($nAbs);
                    $adaAbs=$dAbs['ada'];
                    
                if($adaAbs>0)
                {
                }
                else
                {
                    #insert ke absensi dt
                    
                    $str="INSERT INTO ".$dbname.".`sdm_absensidt` (`kodeorg`, `tanggal`, `karyawanid`, `shift`, `absensi`, `jam`, `jamPlg`, `penjelasan`, `catu`, `penaltykehadiran`, `premi`, `insentif`) 
                         values ('".$unit."','".$tgl."','".$karyawanid."','','H','07:30:00','16:00:00','','1','0','0','".$upah."')";   
                    if(mysql_query($str))
                    {}
                    else
                    {echo " Gagal,".addslashes(mysql_error($conn));}
                }
                    
            }  
        }
        
        
    break;
    default:
}

?>