<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

$method=$_POST['method'];
$periode=$_POST['periode'];
$hariminggu=$_POST['hariminggu'];
$harilibur=$_POST['harilibur'];
$hkefektif=$_POST['hkefektif'];
$catatan=$_POST['catatan'];

switch($method)
{
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

    case'loadData':
    $str="select * from ".$dbname.".sdm_hk_efektif  order by periode desc";
    $res=mysql_query($str) or die(mysql_error($conn));
    while($bar=mysql_fetch_assoc($res))
    {
        $no+=1;	
        echo"<tr class=rowcontent>
        <td>".$no."</td>
        <td align=right>".substr($bar['periode'],0,4)."-".substr($bar['periode'],4,2)."</td>
        <td align=right>".$bar['minggu']."</td>
        <td align=right>".$bar['libur']."</td>
        <td align=right>".$bar['hkefektif']."</td>
        <td align=right>".$bar['catatan']."</td>
        <td align=center><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"deletehk('".$bar['periode']."');\"></td>
        </tr>";	
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