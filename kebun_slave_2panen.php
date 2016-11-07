<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

$pt=$_POST['pt'];
if(isset($_GET['proses']))
{
    $proses=$_GET['proses'];
}
else
{   
    $proses=$_POST['proses'];
}

switch($proses)
{
    case'getKbn':
    $optKebun="<option value=''>".$_SESSION['lang']['all']."</option>";
    $sKbn="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where induk='".$pt."' and tipe='KEBUN'";
    $qKbn=mysql_query($sKbn) or die(mysql_error($conn));
    while($rKbn=  mysql_fetch_assoc($qKbn))
    {
    $optKebun.="<option value=".$rKbn['kodeorganisasi'].">".$rKbn['namaorganisasi']."</option>";
    }
    echo $optKebun;
    break;
    
    case'getDetail':
    $kodeorg=$_GET['kodeorg'];
    $tgl=$_GET['tanggal'];
    $sKary="select namakaryawan,karyawanid from ".$dbname.".datakaryawan where lokasitugas='".substr($kodeorg,0,4)."'";
    $qKary=mysql_query($sKary) or die(mysql_error());
    while($rKary=mysql_fetch_assoc($qKary))
    {
        $rArrKary[$rKary['karyawanid']]=$rKary['namakaryawan'];
    }
    echo"<link rel=stylesheet type=text/css href=style/generic.css>
        <script language=javascript1.2 src='js/generic.js'></script>
        <script language=javascript1.2 src='js/kebun_panen.js'></script>";
    echo"<fieldset><legend>".$_SESSION['lang']['detail']."</legend>";
    echo $_SESSION['lang']['unit'].":".$kodeorg."<br />";
    echo $_SESSION['lang']['tanggal'].":".tanggalnormal($tgl)."<br />";
    
    echo"<br /><img onclick=fisikKeExcel2(event,'kebun_slave_2panen.php') src=images/excel.jpg class=resicon title='MS.Excel'> ";
    echo"<input type='hidden' id='tanggal' value='".$tgl."' /><input type='hidden' id='kdOrg' value='".$kodeorg."' />
        <table class=sortable cellpadding=1 border=0>
        <thead>
        <tr class=rowheader>
        <td>No.</td>
        <td>".$_SESSION['lang']['notransaksi']."</td>
        <td>".$_SESSION['lang']['blok']."</td>
        <td>".$_SESSION['lang']['nikmandor']."</td>
        <td>".$_SESSION['lang']['namakaryawan']."</td>
        <td>".$_SESSION['lang']['hasilkerja']."</td>
        <td>".$_SESSION['lang']['hasilkerjakg']."</td>
        <td>".$_SESSION['lang']['upahkerja']."</td>
        //<td>".$_SESSION['lang']['upahpremi']."</td>
        //<td>".$_SESSION['lang']['rupiahpenalty']."</td>
        </tr></thead><tbody>
            ";
    
    $sPrestasi="select a.*,b.tanggal,b.nikmandor from ".$dbname.".kebun_prestasi a 
        left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi 
            where a.kodeorg='".$kodeorg."' and b.tanggal='".$tgl."' and b.tipetransaksi='PNN'";
    //echo $sPrestasi;

    $qPrestasi=mysql_query($sPrestasi) or die(mysql_erro($conn));
    while($rPrestasi=  mysql_fetch_assoc($qPrestasi))
    {
        $no+=1;
        
        echo"<tr class=rowcontent>
            <td>".$no."</td>
            <td>".$rPrestasi['notransaksi']."</td>
            <td>".$rPrestasi['kodeorg']."</td>";
            if($tempNik!=$rPrestasi['nikmandor'])
            {
                $brs=1;
            }
            if($brs==1)
            {
                  $tempNik=$rPrestasi['nikmandor'];
                  echo"<td>".$rArrKary[$rPrestasi['nikmandor']]."</td>";
                  $brs=0;
            }
            else
            {
                  echo"<td>&nbsp;</td>";
            }
            echo"<td>".$rArrKary[$rPrestasi['nik']]."</td>
            <td align=right>".number_format($rPrestasi['hasilkerja'],2)."</td>
            <td align=right>".number_format($rPrestasi['hasilkerjakg'],2)."</td>
            <td align=right>".number_format($rPrestasi['upahkerja'],2)."</td>
            <td align=right>".number_format($rPrestasi['upahpremi'],2)."</td>
            <td align=right>".number_format($rPrestasi['rupiahpenalty'],2)."</td>
            </tr>";
            $totKerja+=$rPrestasi['hasilkerja'];
            $totKerjakg+=$rPrestasi['hasilkerjakg'];
            $totUpahKerja+=$rPrestasi['upahkerja'];
            $totPenalty+=$rPrestasi['rupiahpenalty'];
            $totPremi+=$rPrestasi['upahpremi'];
    }
    echo"<tr class=rowcontent><td colspan=5>Total</td><td align=right>".number_format($totKerja,2)."</td>
        <td align=right>".number_format($totKerjakg,2)."</td><td align=right>".number_format($totUpahKerja,2)."</td>
        <td align=right>".number_format($totPremi,2)."</td><td align=right>".number_format($totPenalty,2)."</td></tr>";
    echo"</tbody></table></fieldset>";
    break;
    
    case'excelDetail':
        $kodeorg=$_GET['kdOrg'];
        $tgl=$_GET['tgl'];
        $sKary="select namakaryawan,karyawanid from ".$dbname.".datakaryawan where lokasitugas='".substr($kodeorg,0,4)."'";
        $qKary=mysql_query($sKary) or die(mysql_error());
        while($rKary=mysql_fetch_assoc($qKary))
        {
            $rArrKary[$rKary['karyawanid']]=$rKary['namakaryawan'];
        }
        $tab.="
        <table class=sortable cellpadding=1 border=1>
        <thead>
        <tr class=rowheader>
        <td bgcolor=#DEDEDE align=center>No.</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['notransaksi']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['blok']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nikmandor']."</td>    
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namakaryawan']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['hasilkerja']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['hasilkerjakg']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['upahkerja']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['upahpremi']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['rupiahpenalty']."</td>
        </tr></thead><tbody>
            ";
    
    $sPrestasi="select a.*,b.tanggal,b.nikmandor from ".$dbname.".kebun_prestasi a 
        left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi 
            where a.kodeorg='".$kodeorg."' and b.tanggal='".$tgl."' and b.tipetransaksi='PNN'";
    //echo $sPrestasi;
    $qPrestasi=mysql_query($sPrestasi) or die(mysql_erro($conn));
    while($rPrestasi=  mysql_fetch_assoc($qPrestasi))
    {
        $no+=1;
        
        $tab.="<tr class=rowcontent>
            <td>".$no."</td>
            <td>".$rPrestasi['notransaksi']."</td>
            <td>".$rPrestasi['kodeorg']."</td>";
            if($tempNik!=$rPrestasi['nikmandor'])
            {
                $brs=1;
            }
            if($brs==1)
            {
                  $tempNik=$rPrestasi['nikmandor'];
                  $tab.="<td>".$rArrKary[$rPrestasi['nikmandor']]."</td>";
                  $brs=0;
            }
            else
            {
                  $tab.="<td>&nbsp;</td>";
            }
            $tab.="
            <td>".$rArrKary[$rPrestasi['nik']]."</td>
            <td align=right>".number_format($rPrestasi['hasilkerja'],2)."</td>
            <td align=right>".number_format($rPrestasi['hasilkerjakg'],2)."</td>
            <td align=right>".number_format($rPrestasi['upahkerja'],2)."</td>
            <td align=right>".number_format($rPrestasi['upahpremi'],2)."</td>
            <td align=right>".number_format($rPrestasi['rupiahpenalty'],2)."</td>
            </tr>";
            $totKerja+=$rPrestasi['hasilkerja'];
            $totKerjakg+=$rPrestasi['hasilkerjakg'];
            $totUpahKerja+=$rPrestasi['upahkerja'];
            $totPenalty+=$rPrestasi['rupiahpenalty'];
            $totPremi+=$rPrestasi['upahpremi'];
    }
    $tab.="<tr class=rowcontent><td colspan=5>Total</td><td align=right>".number_format($totKerja,2)."</td>
        <td align=right>".number_format($totKerjakg,2)."</td><td align=right>".number_format($totUpahKerja,2)."</td>
        <td align=right>".number_format($totPremi,2)."</td><td align=right>".number_format($totPenalty,2)."</td></tr>";
    $tab.="</tbody>";
			
			//echo "warning:".$strx;
			//=================================================

			
			$tab.="</table>Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
			
			$nop_="laporanPanenDetail_".$kodeorg."_".$tgl;
			if(strlen($tab)>0)
			{
			if ($handle = opendir('tempExcel')) {
			while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
			@unlink('tempExcel/'.$file);
			}
			}	
			closedir($handle);
			}
			$handle=fopen("tempExcel/".$nop_.".xls",'w');
			if(!fwrite($handle,$tab))
			{
			echo "<script language=javascript1.2>
			parent.window.alert('Can't convert to excel format');
			</script>";
			exit;
			}
			else
			{
			echo "<script language=javascript1.2>
			window.location='tempExcel/".$nop_.".xls';
			</script>";
			}
			closedir($handle);
			}
    break;
    default:
        break;
}
//if(isset($_POST['proses']))//=="getKbn")
//{
//    
//}
//if(isset($_POST['proses'])=="getDetail")
//{
//    echo"warning:masuk";
//}
?>
