<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

$method=$_POST['method'];
$bibit=$_POST['bibit'];
$oldjb=$_POST['oldjb'];
$umur=$_POST['umur'];
$oldum=$_POST['oldum'];
$tanah=$_POST['tanah'];
$oldkt=$_POST['oldkt'];
$produksi=$_POST['produksi'];
$x=readCountry('config/jenistanah.lst');
foreach($x as $bar=>$val)
{                    
    $namatanah[$val[0]]=$val[1];
}
switch($method)
{
    case'insert':
    if($bibit=='')
    {
        echo"warning: Silakan pilih jenis bibit"; exit();
    }
    if($tanah=='')
    {
        echo"warning: Silakan pilih klasifikasi tanah"; exit();
    }
    if($umur=='')
    {
        echo"warning: Silakan isi umur tanaman"; exit();
    }
    if($produksi=='')
    {
        echo"warning: Silakan isi Kg Produksi/Ha"; exit();
    }

    $sIns="insert into ".$dbname.".kebun_5stproduksi (`jenisbibit`,`klasifikasitanah`,`umur`,`kgproduksi`) values ('".$bibit."','".$tanah."','".$umur."','".$produksi."')";
    if(!mysql_query($sIns))
    {
        echo"Gagal".mysql_error($conn);
    }
    break;
    case'loadData':
    $no=0;	 
    $str="select * from ".$dbname.".kebun_5stproduksi order by jenisbibit, umur, klasifikasitanah";
    $res=mysql_query($str);
    while($bar=mysql_fetch_assoc($res))
    {
    $no+=1;	
    echo"<tr class=rowcontent>
    <td>".$no."</td>
    <td>".$bar['jenisbibit']."</td>
    <td>".$namatanah[$bar['klasifikasitanah']]."</td>
    <td>".$bar['umur']."</td>
    <td>".$bar['kgproduksi']."</td>
    <td>
        <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar['jenisbibit']."','".$bar['klasifikasitanah']."','".$bar['umur']."','".$bar['kgproduksi']."');\"> 
    </td>
    </tr>";	
    }  
    if($no==0){
        echo"<tr class=rowcontent>
        <td colspan=6>Data Empty.</td>
        </tr>";	
    }
    break;
    case'update':
    if($bibit=='')
    {
        echo"warning: Silakan pilih jenis bibit"; exit();
    }
    if($tanah=='')
    {
        echo"warning: Silakan pilih klasifikasi tanah"; exit();
    }
    if($umur=='')
    {
        echo"warning: Silakan isi umur tanaman"; exit();
    }
    if($produksi=='')
    {
        echo"warning: Silakan isi Kg Produksi/Ha"; exit();
    }
    $sUpd="update ".$dbname.".kebun_5stproduksi set `jenisbibit`='".$bibit."',`klasifikasitanah`='".$tanah."',`umur`='".$umur."',`kgproduksi`='".$produksi."' where jenisbibit='".$oldjb."' and klasifikasitanah='".$oldkt."' and umur='".$oldum."'";
    if(!mysql_query($sUpd))
    {
        echo"Gagal".mysql_error($conn);
    }
    break;
    case'delData':
    $sDel="delete from ".$dbname.".setup_franco where id_franco='".$idFranco."'";
    if(!mysql_query($sDel))
    {
            echo"Gagal".mysql_error($conn);
    }
    break;
    default:
    break;
}
?>