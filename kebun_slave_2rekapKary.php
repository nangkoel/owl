<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$kdAfd=$_POST['kdAfd'];
$kdKeg=$_POST['kdKeg'];
$tgl1=tanggalsystem($_POST['tgl1']);
$tgl2=tanggalsystem($_POST['tgl2']);
$kar=$_POST['kar'];

if($proses=='excel')
{
    $kdAfd=$_GET['kdAfd'];
    $kdKeg=$_GET['kdKeg'];
    $tgl1=tanggalsystem($_GET['tgl1']);
    $tgl2=tanggalsystem($_GET['tgl2']);
    $kar=$_GET['kar'];
}

$arrPost=array("0"=>"Belum Posting","1"=>"Posting");


$namaKeg=makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan');
$namaKar=  makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$nikKar=  makeOption($dbname, 'datakaryawan', 'karyawanid,nik');
$subKar=  makeOption($dbname, 'datakaryawan', 'karyawanid,subbagian');
//no	tanggal	Kegiatan	notransaksi	Nama	Nik	subbagian		KodeBlok	Jjg	Hasil Kerja	jhk	umr	premi

$stream = "<table class=sortable cellspacing=1>";
    $stream.="<thead class=rowheader>
        <tr class=rowheader>
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['nourut']."</td>
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['tanggal']."</td>
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['kegiatan']."</td>
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['namakegiatan']."</td>
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['namakaryawan']."</td>
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['nik']."</td>    
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['subbagian']."</td>
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['notransaksi']."</td>    
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['kodeblok']."</td>
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['jjg']."</td>
            <td bgcolor=#CCCCCC align=center>Hasil Kerja</td>    
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['jhk']."</td>    
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['umr']."</td>
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['premi']."</td>  
                <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['posting']."</td> 
        </tr>
        <tr>";
    $stream.="</thead>";
    
    /*$iBlok="select a.kodeorganisasi,b.statusblok from ".$dbname.".organisasi a left join ".$dbname.".setup_blok b  on a.kodeorganisasi=b.kodeorg"
         . " where a.kodeorganisasi like '".$kdAfd."%' and a.tipe='blok' and b.statusblok='TM' ";
    $nBlok=mysql_query($iBlok) or die (mysql_error($conn));
    while($dBlok=mysql_fetch_assoc($nBlok))
    {
        $blok[$dBlok['kodeorganisasi']]=$dBlok['kodeorganisasi'];
        $statusBlok[$dBlok['kodeorganisasi']]=$dBlok['statusblok'];
    }
    
    $iBjr="select * from ".$dbname.".kebun_5bjr where kodeorg like '".$kdAfd."%' and tahunproduksi='".$tahun."' ";
    $nBjr=mysql_query($iBjr) or die (mysql_error($conn));
    while($dBjr=mysql_fetch_assoc($nBjr))
    {
        $bjr[$dBjr['kodeorg']]=$dBjr['bjr'];
        $thnProd[$dBjr['kodeorg']]=$dBjr['tahunproduksi'];
    }*/
    
    
    if($kar!='')
    {
        $karIsi="and karyawanid='".$kar."'";
    }
    if($kdKeg!='')
    {
        $kegIsi="and kodekegiatan='".$kdKeg."'";
    }
    
    $iKar="select * from ".$dbname.".datakaryawan where tipekaryawan='4' and "
            . " lokasitugas='".substr($kdAfd,0,6)."'  ".$karIsi." order by namakaryawan asc";
    
    $nKar=mysql_query($iKar) or die (mysql_error($conn));
    while($dKar=mysql_fetch_assoc($nKar))
    {
        $kar[$dKar['karyawanid']]=$dKar['karyawanid'];
        $nama[$dKar['karyawanid']]=$dKar['namakaryawan'];
        $nik[$dKar['karyawanid']]=$dKar['nik'];
        $sub[$dKar['karyawanid']]=$dKar['subbagian'];
        
    }
    
    

   
    
    $iKeg="select * from ".$dbname.".kebun_kehadiran_vw where tanggal between '".$tgl1."' and '".$tgl2."' "
            . " ".$karIsi." ".$kegIsi." and kodeafd='".$kdAfd."' order by tanggal asc ";
  // echo $iKeg;
    $nKeg=mysql_query($iKeg) or die (mysql_error($conn));
    while($dKeg=mysql_fetch_assoc($nKeg))
    {
        
        $no+=1;
        $stream.="<tr class=rowcontent>";
        $stream.="<td>".$no."</td>";
        $stream.="<td>".tanggalnormal($dKeg['tanggal'])."</td>";
        $stream.="<td>".$dKeg['kodekegiatan']."</td>";
        $stream.="<td>".$namaKeg[$dKeg['kodekegiatan']]."</td>";
        $stream.="<td>".$namaKar[$dKeg['karyawanid']]."</td>";
        $stream.="<td>'".$nikKar[$dKeg['karyawanid']]."</td>";
        $stream.="<td>".$subKar[$dKeg['karyawanid']]."</td>";
        $stream.="<td>".$dKeg['notransaksi']."</td>";
        $stream.="<td>".$dKeg['kodeorg']."</td>";
        $stream.="<td align=right>".number_format($dKeg['jjg'],2)."</td>";
        $stream.="<td align=right>".number_format($dKeg['hasilkerja'],2)."</td>";
        $stream.="<td align=right>".number_format($dKeg['jhk'],2)."</td>";
        $stream.="<td align=right>".number_format($dKeg['umr'],2)."</td>";
        $stream.="<td align=right>".number_format($dKeg['insentif'],2)."</td>";
        $stream.="<td>".$arrPost[$dKeg['jurnal']]."</td>";
        $stream.="</tr>";  
        
        
    }
    

    
    #bentuk kegiatan

    
    
    //print_r($keg);
    
        
  
    
    
           

$stream.="</table>";	
	   
   


$stream.="<tbody></table>";
switch($proses)
{
######PREVIEW
	case 'preview':
		echo $stream;
    break;

######EXCEL	
	case 'excel':
		//$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="laporan_kerja_karyawan".$pt."_".$per;
		if(strlen($stream)>0)
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
			if(!fwrite($handle,$stream))
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
}
?>