<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
?>

<?php
    $id=$_POST['id'];
    $kodeorg=$_POST['kodeorg'];
    $produk=$_POST['produk'];
    $namaitem=$_POST['namaitem'];
    $standard=$_POST['standard'];
    $satuan=$_POST['satuan'];
    $method=$_POST['method'];
    //exit("ERROR:$namaitem");
switch($method)
{
    case 'insert':
		$i="insert into ".$dbname.".pabrik_5kelengkapanloses (kodeorg,produk,namaitem,standard,satuan,updateby)
		values ('".$kodeorg."','".$produk."','".$namaitem."','".$standard."','".$satuan."','".$_SESSION['standard']['userid']."')";
		//exit("Error.$sDel2");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
        /*UPDATE `dev`.`pabrik_5kelengkapanloses` SET `produk` = 'KERNEL',
//`namaitem` = 'ABCDE',
`standard` = '1234',
`satuan` = '123' WHERE `pabrik_5kelengkapanloses`.`id` =90*/
            case 'update':
                $i="update ".$dbname.".pabrik_5kelengkapanloses set kodeorg='".$kodeorg."',produk='".$produk."',
                    namaitem='".$namaitem."',standard='".$standard."',satuan='".$satuan."',updateby='".$_SESSION['standard']['userid']."'
                        where id='".$id."'";
                //exit("Error.$i");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
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