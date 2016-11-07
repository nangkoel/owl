<?php
//ind
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');


$proses=$_POST['proses'];
$kdBlok=$_POST['kdBlok'];
$tahun=$_POST['tahun'];
$bjr=$_POST['bjr'];	

switch($proses)
{	
    case'savedata':
            $str="insert into ".$dbname.".kebun_5bjr (`kodeorg`,`bjr`,`tahunproduksi`)
            values ('".$kdBlok."','".$bjr."','".$tahun."')";//exit("Error:$str");
            if(mysql_query($str))
            {}
            else
            {
                $str="update ".$dbname.".kebun_5bjr set bjr='".$bjr."' where kodeorg='".$kdBlok."' and tahunproduksi='".$tahun."'";
                if(mysql_query($str))
                {}
                else
                {echo " Gagal,".addslashes(mysql_error($conn));}
            }
      
    break;
    default:
}

?>