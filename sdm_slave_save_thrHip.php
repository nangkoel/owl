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
$kdKbn=$_POST['kdKbn'];

	

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
        $whr=" and agama='".$agama."'";
        $iTgl="select distinct periodebayar,tanggalcutoff from ".$dbname.".sdm_5periodethr
        where regional='".$_SESSION['empl']['regional']."' ".$whr." and tahun='".$tahun."' ";
        $nTgl=mysql_query($iTgl) or die(mysql_error($conn));
        $dTgl=mysql_fetch_assoc($nTgl);
        
       
        echo $dTgl['periodebayar']."###".tanggalnormal($dTgl['tanggalcutoff']);
        
        
        //echo $optPer;
       // exit("Error:MASUK");
        
        break;
    case 'getafd':
        $sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where induk='"
            . $kdKbn."' and kodeorganisasi in (SELECT DISTINCT b.subbagian FROM ".$dbname.".sdm_gaji a 
            LEFT JOIN ".$dbname.".datakaryawan b ON a.karyawanid=b.karyawanid WHERE a.idkomponen IN (28,71) AND 
            a.kodeorg='".$kdKbn."' AND b.subbagian!='') order by namaorganisasi asc";	
        $optkdOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $qOrg=mysql_query($sOrg) or die(mysql_error());
        while($rOrg=mysql_fetch_assoc($qOrg))
        {
                $optkdOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
        }
        $optkdOrg.="<option value='all'>".$_SESSION['lang']['all']."</option>";

        echo $optkdOrg;
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