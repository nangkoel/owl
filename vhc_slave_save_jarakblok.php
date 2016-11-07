<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
?>

<?php
    
    $kodeorg=$_POST['kodeorg'];
    $regional=$_POST['regional'];
    $kodeblok=$_POST['kodeblok'];
    $jarak=$_POST['jarak'];
    $method=$_POST['method'];
    //exit("ERROR:$namaitem");
switch($method)
{
    case 'save':
		$i="insert into ".$dbname.".vhc_5jarakblok(regional,kodeorg,kodeblok,jarak,updateby)
		values ('".$regional."','".$kodeorg."','".$kodeblok."','".$jarak."','".$_SESSION['standard']['userid']."')";
		//exit("Error.$sDel2");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
        
        
        
        
        
        
            case 'getBlok':
            
                $optblok="<option value=''>Pilih data</option>";
                $x="select * from ".$dbname.".setup_blok where kodeorg like '%".$kodeorg."%'";
                $y=  mysql_query($x) or die (mysql_error($conn));
                while($z=mysql_fetch_assoc($y))
                {
                    $optblok.="<option value='".$z['kodeorg']."'>".$z['kodeorg']."</option>";
                }
                echo $optblok;

                
                
                
            break;
            case 'delete':
                
                //DELETE FROM `dev`.`pabrik_5kelengkapanloses` WHERE `pabrik_5kelengkapanloses`.`id` = 85 LIMIT 
		$i="DELETE FROM ".$dbname.".pabrik_5kelengkapanloses WHERE id='".$id."'";
		//exit("Error.$i");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
        
        default;
}   
    
    
    
?>