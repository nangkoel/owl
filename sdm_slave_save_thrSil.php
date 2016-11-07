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
$kdorg=$_POST['kdorg'];
$per=$_POST['per'];
$jumlah=$_POST['jumlah'];
$tk=$_POST['tk'];


$tahun=$_POST['tahun'];
$agama=$_POST['agama'];

	

switch($proses)
{
    case'getPer':
        
        /*$iPer="select periodebayar,tanggalcutoff from ".$dbname.".sdm_5periodethr
        where regional='".$_SESSION['empl']['regional']."' and agama='".$agama."' and tahun='".$tahun."' ";
       // exit("Error:$iPer");
        $nPer=mysql_query($iPer) or die(mysql_error($conn));
        while($dPer=mysql_fetch_assoc($nPer))
        {
                $optPer.="<option value=".$dPer['periodebayar'].">".$dPer['periodebayar']."</option>";
        }*/
        
        $iTgl="select periodebayar,tanggalcutoff from ".$dbname.".sdm_5periodethr
        where regional='".$_SESSION['empl']['regional']."' and agama='".$agama."' and tahun='".$tahun."' ";
        $nTgl=mysql_query($iTgl) or die(mysql_error($conn));
        $dTgl=mysql_fetch_assoc($nTgl);
        
       
        echo $dTgl['periodebayar']."###".tanggalnormal($dTgl['tanggalcutoff']);
        
        
        //echo $optPer;
       // exit("Error:MASUK");
        
        break;
    
    
    case'savedata':
        if($jumlah=='0' or $jumlah=='')
        {}
        else
        {
            #jika KHT insert ke komponen 71
            if($tk=='4')
            {
                $str="insert into ".$dbname.".sdm_gaji (`kodeorg`,`periodegaji`,`karyawanid`,`idkomponen`,`jumlah`,`pengali`)
                values ('".$kdorg."','".$per."','".$karyawanid."','71','".$jumlah."','1')";//exit("Error:$str"); 
                if(mysql_query($str))
                {}
                else
                {
                    $str="update ".$dbname.".sdm_gaji set jumlah='".$jumlah."' where idkomponen='71' and kodeorg='".$kdorg."' and periodegaji='".$per."' and karyawanid='".$karyawanid."'";
                    if(mysql_query($str))
                    {}
                    else
                    {echo " Gagal,".addslashes(mysql_error($conn));}
                }
            }
            else
            {
                $str="insert into ".$dbname.".sdm_gaji (`kodeorg`,`periodegaji`,`karyawanid`,`idkomponen`,`jumlah`,`pengali`)
                values ('".$kdorg."','".$per."','".$karyawanid."','28','".$jumlah."','1')";//exit("Error:$str");
                if(mysql_query($str))
                {}
                else
                {
                    $str="update ".$dbname.".sdm_gaji set jumlah='".$jumlah."' where idkomponen='28' and kodeorg='".$kdorg."' and periodegaji='".$per."' and karyawanid='".$karyawanid."'";
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