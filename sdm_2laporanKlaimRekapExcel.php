<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
 
$periode=$_GET['periode'];
$optJabatan=makeOption($dbname,'sdm_5jabatan','kodejabatan,namajabatan');

//ambil data keluarga
$stry="select nomor,nama,hubungankeluarga,tanggungan from ".$dbname.".sdm_karyawankeluarga";
$res=mysql_query($stry);
while($bar=mysql_fetch_object($res)){
    $nama[$bar->nomor]=$bar->nama;
    $hubungan[$bar->nomor]=$bar->hubungankeluarga;
    $tanggungan[$bar->nomor]=$bar->tanggungan;
}


	
$stream="Laporan Rekap Pengobatan Periode ".$periode."
    <table border=1>
    <thead>
    <tr>
        <td bgcolor=#dedede rowspan=2>No</td>
        <td bgcolor=#dedede rowspan=2>".$_SESSION['lang']['namakaryawan']."</td>
        <td bgcolor=#dedede rowspan=2>".$_SESSION['lang']['lokasitugas']."</td>            
        <td bgcolor=#dedede rowspan=2>".$_SESSION['lang']['tanggal']."</td>
        <td bgcolor=#dedede rowspan=2>".$_SESSION['lang']['jabatan']."</td>
        <td bgcolor=#dedede rowspan=2>".$_SESSION['lang']['nama']." ".$_SESSION['lang']['pasien']."</td>
        <td bgcolor=#dedede rowspan=2>".$_SESSION['lang']['rumahsakit']."</td>
        <td bgcolor=#dedede rowspan=2>".$_SESSION['lang']['nilaiklaim']."</td>
        <td bgcolor=#dedede colspan=3 align=center>".$_SESSION['lang']['dibayar']."</td>  
        <td bgcolor=#dedede rowspan=2>".$_SESSION['lang']['total']."</td>     
        <td bgcolor=#dedede rowspan=2>".$_SESSION['lang']['keterangan']."</td>
    </tr>
    <tr>
            <td bgcolor=#dedede>".$_SESSION['lang']['internal']."</td>
            <td bgcolor=#dedede>Providers</td>
            <td bgcolor=#dedede>".$_SESSION['lang']['klaim']."</td>
    </tr>
    </thead>
    <tbody>";  
$str="select a.*,b.namars,c.namakaryawan,c.lokasitugas,c.kodejabatan from ".$dbname.".sdm_pengobatanht a left join ".$dbname.".sdm_5rs b on a.rs=b.id
    left join ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid where periode='".$periode."'";
$res=mysql_query($str);
$no=0;
while($bar=mysql_fetch_object($res))
{
    $no+=1;
    $pasien='';
    if($bar->ygsakit!='0'){
        $pasien=$nama[$bar->ygsakit];
    }else{
         $pasien=$bar->namakaryawan;
    }
    if($bar->klaimoleh==0){
        $claim=$bar->jlhbayar;
        $tclaim+=$claim;
    }    
    if($bar->klaimoleh==1){
        $prov=$bar->jlhbayar;
        $tprov+=$prov;
    }
    if($bar->klaimoleh==2){
        $int=$bar->jlhbayar;
        $tint+=$int;
    }
    
    $stream.="<tr>
        <td>".$no."</td>
        <td>".$bar->namakaryawan."</td>
        <td>".$bar->lokasitugas."</td>
        <td>".tanggalnormal($bar->tanggal)."</td>
        <td>".$optJabatan[$bar->kodejabatan]."</td>
        <td>".$pasien."[".$hubungan[$bar->ygsakit]."]</td>
        <td>".$bar->namars."</td>
        <td align=right>".number_format($bar->totalklaim,0)."</td>
            
        <td align=right>".number_format($int,0)."</td>    
         <td align=right>".number_format($prov,0)."</td>   
        <td align=right>".number_format($claim,0)."</td>
        <td align=right>".number_format($bar->jlhbayar,0)."</td>    
        
       <td>".$bar->keterangan."</td>
    </tr>";	  
    $tklaim+=$bar->totalklaim;
    $tbayar+=$bar->jlhbayar;
}
    $stream.="<tr>
        <td colspan=7>TOTAL</td>
        <td align=right>".number_format($tklaim,0)."</td>
            
        <td align=right>".number_format($tint,0)."</td>    
         <td align=right>".number_format($tprov,0)."</td>   
        <td align=right>".number_format($tclaim,0)."</td>
        <td align=right>".number_format($tbayar,0)."</td>    
        
       <td></td>
    </tr>";	  
    
$stream.="</tbody>
    <tfoot>
    </tfoot>
    </table>";	 
//write exel   
$nop_="LaporanRekapPengobatan-".$periode;
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
            parent.window.alert('Cant convert to excel format');
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
?>
