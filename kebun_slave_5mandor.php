<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

$method=$_POST['method'];
$mandor=$_POST['mandor'];
$karyawan=$_POST['karyawan'];
$urut=$_POST['urut'];
$aktif=$_POST['aktif'];

switch($method)
{
    case'tampilmandor': // nampilin data mandor yang punya karyawan
    $str="select distinct(a.mandorid), b.namakaryawan from ".$dbname.".kebun_5mandor a
        left join ".$dbname.".datakaryawan b on a.mandorid = b.karyawanid
        ";
    $res=mysql_query($str) or die(mysql_error($conn));
    while($bar=mysql_fetch_assoc($res))
    {
        $no+=1;	
        echo"<tr class=rowcontent>
        <td>".$no."</td>
        <td align=left onclick=pilihmandor('".$bar['mandorid']."') style=\"cursor:pointer;\">".$bar['namakaryawan']."</td>
        <td align=center><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"hapusmandor('".$bar['mandorid']."');\"></td>
        </tr>";	
    }     
    break;
    
    case'tampilkaryawan': // nampilin pilihan karyawan setelah pilih mandor
        $optkaryawan='<option value=\'\'>'.$_SESSION['lang']['pilihdata'].'</option>';
        $str="select karyawanid, namakaryawan from ".$dbname.".datakaryawan
            where lokasitugas like '".$_SESSION['empl']['lokasitugas']."%' and (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].") and alokasi = 0
                and karyawanid != '".$mandor."'
            order by namakaryawan";
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
            $optkaryawan.="<option value='".$bar->karyawanid."'>".$bar->namakaryawan." [".$bar->karyawanid."]</option>";
        }
        echo $optkaryawan;
    break;
    
    case'pilihmandor': // nampilin data karyawan yang dimandori
    $no=0;
    $str="select a.karyawanid, b.namakaryawan, a.statusaktif, a.mandorid from ".$dbname.".kebun_5mandor a
        left join ".$dbname.".datakaryawan b on a.karyawanid = b.karyawanid
        where a.mandorid='".$mandor."'
        order by a.nourut";
    $res=mysql_query($str) or die(mysql_error($conn));
    echo"<table>";
    $statusaktif['0']='';
    $statusaktif['1']='Aktif';
    while($bar=mysql_fetch_assoc($res))
    {
        $no+=1;	
        echo"<tr class=rowcontent>
        <td>".$no."</td>
        <td align=left>".$bar['namakaryawan']."</td>
        <td align=center title='Set Aktif' onclick=\"aktifkaryawan('".$bar['karyawanid']."','".$bar['mandorid']."','".$bar['statusaktif']."');\" style=\"cursor:pointer;\">[ ".$statusaktif[$bar['statusaktif']]." ]</td>
        <td align=center><img src=images/application/application_delete.png class=resicon title='Delete' onclick=\"hapuskaryawan('".$bar['karyawanid']."');\"></td>
        </tr>";	
    }     
    echo"</table>";
    break;
    
    case'tambahkaryawan': // tambah karyawan mandor
    $sIns="insert into ".$dbname.".kebun_5mandor (`mandorid`,`karyawanid`,`statusaktif`,`nourut`,`updateby`) 
        values ('".$mandor."','".$karyawan."','1','".$urut."','".$_SESSION['standard']['userid']."')";
    if(!mysql_query($sIns))
    {
        echo"Gagal : ".mysql_error($conn);
    }
    break;

    case'hapuskaryawan': // hapus karyawan mandor        
    $sIns="delete from ".$dbname.".kebun_5mandor where mandorid='".$mandor."' and karyawanid='".$karyawan."'";
    if(!mysql_query($sIns))
    {
        echo"Gagal : ".mysql_error($conn);
    }
    break;

    case'hapusmandor': // hapus mandor beserta karyawannya
    $sIns="delete from ".$dbname.".kebun_5mandor where mandorid='".$mandor."'";
    if(!mysql_query($sIns))
    {
        echo"Gagal : ".mysql_error($conn);
    }
    break;
    
    case'aktifkaryawan': // update status aktif karyawan 
    if($aktif=='1')$aktif='0'; else $aktif='1';
    // UPDATE `owlv2`.`kebun_5mandor` SET `statusaktif` = '0' WHERE `kebun_5mandor`.`mandorid` =0000012456 AND `kebun_5mandor`.`karyawanid` =0000013591;    
    $sIns="update ".$dbname.".kebun_5mandor set statusaktif ='".$aktif."' where mandorid='".$mandor."' and karyawanid = '".$karyawan."'";
    if(!mysql_query($sIns))
    {
        echo"Gagal : ".mysql_error($conn);
    }
    break;
    
    
    
    
    
    
    
    
    
    case'insert':
    $qwe=explode('-',$periode);
    $periode=$qwe[0].$qwe[1];
    if($hkefektif=='')
    {
            echo "warning : Silakan memilih periode.";
            exit();
    }
    if($hkefektif<=0)
    {
            echo "warning : HK Efektif <= 0.";
            exit();
    }

    $sIns="insert into ".$dbname.".sdm_hk_efektif (`periode`,`minggu`,`libur`,`hkefektif`,`catatan`) 
        values ('".$periode."','".$hariminggu."','".$harilibur."','".$hkefektif."','".$catatan."')";
    if(!mysql_query($sIns))
    {
        echo"Gagal".mysql_error($conn);
    }
    break;

    case'delete':
        $sIns="delete from ".$dbname.".sdm_hk_efektif where periode = '".$periode."'";
        //exit("Error".$sIns);
        if(!mysql_query($sIns))
        {
                echo"Gagal".mysql_error($conn);
        }
    break;
        default:
        break;
}
?>