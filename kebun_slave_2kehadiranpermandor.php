<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$proses=$_GET['proses'];
$lokasi=$_SESSION['empl']['lokasitugas'];
$kebun=$_POST['kebun'];
$mandor=$_POST['mandor'];
$tanggal=$_POST['tanggal'];
if(($proses=='excel')or($proses=='pdf')){
    $kebun=$_GET['kebun'];
    $mandor=$_GET['mandor'];
    $tanggal=$_GET['tanggal'];
}

if($proses=='getmandor'){
$optMandor="<option value=\"all\">".$_SESSION['lang']['all']."</option>";
$sMan="select a.nikmandor, b.namakaryawan, b.lokasitugas from ".$dbname.".kebun_aktifitas a
    left join ".$dbname.".datakaryawan b on a.nikmandor=b.karyawanid
    where a.kodeorg like '%".$lokasi."%'
    group by a.nikmandor
    order by b.namakaryawan";
$qMan=mysql_query($sMan) or die(mysql_error($conn));
while($rMan=mysql_fetch_assoc($qMan))
{
    $optMandor.="<option value=".$rMan['nikmandor'].">".$rMan['namakaryawan']." [".$rMan['lokasitugas']."]</option>";
}
    
    
}

$lokasi=substr($_SESSION['empl']['lokasitugas'],0,4);

$tanggal=tanggalsystem($tanggal); $tanggal=substr($tanggal,0,4).'-'.substr($tanggal,4,2).'-'.substr($tanggal,6,2);

if(($proses=='preview')or($proses=='excel')or($proses=='pdf')){
    if($kebun==''){
        echo"Error: Kebun tidak boleh kosong."; exit;
    }
    if(($tanggal=='')){
        echo"Error: Tanggal tidak boleh kosong."; exit;
    }
}

$sMan="select a.nikmandor, b.namakaryawan from ".$dbname.".kebun_aktifitas a
    left join ".$dbname.".datakaryawan b on a.nikmandor=b.karyawanid
    where a.kodeorg = '".$lokasi."'
    group by a.nikmandor
    order by b.namakaryawan";
$qMan=mysql_query($sMan) or die(mysql_error($conn));
while($rMan=mysql_fetch_assoc($qMan))
{
    $namamandor[$rMan['nikmandor']]=$rMan['namakaryawan'];
}

if ($proses=='excel' or $proses=='preview')
{
    $border=0;
    if($proses=='excel')$border=1;

    $selectmandor="b.nikmandor = '".$mandor."'";
    if($mandor=='all')$selectmandor="b.nikmandor like '%%'";
    
    $str="select a.*, b.*, c.namakaryawan from ".$dbname.".kebun_kehadiran a
        left join ".$dbname.".kebun_aktifitas b on a.notransaksi = b.notransaksi
        left join ".$dbname.".datakaryawan c on a.nik = c.karyawanid
        where  b.kodeorg like '".$kebun."%' and ".$selectmandor." and b.tanggal = '".$tanggal."'        
        ";
    
        $res=mysql_query($str);
	$stream.="<table cellspacing='1' border='".$border."' class='sortable'>
	<thead>
	<tr class=rowheader>
        <td>".$_SESSION['lang']['nomor']."</td>
        <td>".$_SESSION['lang']['notransaksi']."</td>    
	<td>".$_SESSION['lang']['tanggal']."</td>
	<td>".$_SESSION['lang']['namakaryawan']."</td>
	<td>".$_SESSION['lang']['jhk']."</td>
	<td>".$_SESSION['lang']['umr']."</td>
	<td>".$_SESSION['lang']['insentif']."</td>            
        </tr></thead>
	<tbody>";
        $no=0;
        while($bar=mysql_fetch_object($res))
        {
            $dzda[$bar->nikmandor]=$bar->nikmandor;
            
            $dzdata[$bar->nikmandor]['mandor']=$bar->nikmandor;
            $dzdata[$bar->nikmandor]['jhk']+=$bar->jhk;
            $dzdata[$bar->nikmandor]['umr']+=$bar->umr;
            $dzdata[$bar->nikmandor]['insentif']+=$bar->insentif;
            
            $niknotransaksi=$bar->nik.$bar->notransaksi;
            
            $dzdatadetail[$bar->nikmandor][$niknotransaksi]['notransaksi']=$bar->notransaksi;
            $dzdatadetail[$bar->nikmandor][$niknotransaksi]['tanggal']=$bar->tanggal;
            $dzdatadetail[$bar->nikmandor][$niknotransaksi]['namakaryawan']=$bar->namakaryawan;
            $dzdatadetail[$bar->nikmandor][$niknotransaksi]['jhk']=$bar->jhk;
            $dzdatadetail[$bar->nikmandor][$niknotransaksi]['umr']=$bar->umr;
            $dzdatadetail[$bar->nikmandor][$niknotransaksi]['insentif']=$bar->insentif;
            
//            $no+=1;
//            $stream.="<tr class=rowcontent>
//            <td>".$no."</td>
//            <td>".$bar->notransaksi."</td>    
//            <td>".tanggalnormal($bar->tanggal)."</td>
//            <td>".$bar->namakaryawan."</td>
//            <td align=right>".number_format($bar->jhk,2)."</td>
//            <td align=right>".number_format($bar->umr)."</td>
//            <td align=right>".number_format($bar->insentif)."</td>         
//            </tr>";
            $jhk+=$bar->jhk;
            $umr+=$bar->umr;
            $insentif+=$bar->insentif;
        }   
        
        if(!empty($dzda))foreach($dzda as $datanya){
            $stream.="
            <tr class=rowcontent style=cursor:pointer; onclick=tampilhilang('".$dzdata[$datanya]['mandor']."')>
            <td colspan=4>".$namamandor[$dzdata[$datanya]['mandor']]."</td>
            <td align=right>".number_format($dzdata[$datanya]['jhk'],2)."</td>
            <td align=right>".number_format($dzdata[$datanya]['umr'])."</td>
            <td align=right>".number_format($dzdata[$datanya]['insentif'])."</td>
            </tr>";
            
//            $stream.="<tr id=".$dzdata[$datanya]['mandor']." style=display:none;><td colspan=7>
//            <table cellspacing='1' border='".$border."' class='sortable' width=100%><tbody>";
            $no=0;
            if(!empty($dzdatadetail[$datanya]))foreach($dzdatadetail[$datanya] as $datadetailnya){
                $no+=1;
                $stream.="<tr class=rowcontent>
                <td>".$no."</td>
                <td>".$datadetailnya['notransaksi']."</td>    
                <td>".tanggalnormal($datadetailnya['tanggal'])."</td>
                <td>".$datadetailnya['namakaryawan']."</td>
                <td align=right>".number_format($datadetailnya['jhk'],2)."</td>
                <td align=right>".number_format($datadetailnya['umr'])."</td>
                <td align=right>".number_format($datadetailnya['insentif'])."</td>     
                </tr>";                
            }
//        $stream.="</tbody></table></td></tr>";
        }        
            
	$stream.="<tr class=rowcontent>
	<td colspan=4>Total</td>
	<td align=right>".number_format($jhk,2)."</td>
	<td align=right>".number_format($umr)."</td>
	<td align=right>".number_format($insentif)."</td>
        </tbody></table>";
}  
switch($proses)
{
    case'preview':
        echo $stream;    
    break;
    case 'excel':
        $stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
        $dte=date("YmdHms");
        $nop_="KehadiranperMandor".$kebun.$mandor."-".$tanggal."_".date('YmdHis');
         $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
         gzwrite($gztralala, $stream);
         gzclose($gztralala);
         echo "<script language=javascript1.2>
            window.location='tempExcel/".$nop_.".xls.gz';
            </script>";            
    break;    
}

?>