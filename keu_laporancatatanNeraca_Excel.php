<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$pt=$_GET['pt'];
$periode=$_GET['periode'];
$akundari=$_GET['akundari'];
$akunsampai=$_GET['akunsampai'];
        
//check, one-two
if($akundari==''){
    echo "WARNING: silakan memilih akun."; exit;
}
if($akunsampai==''){
    echo "WARNING: silakan memilih akun."; exit;
}
// exclude laba rugi tahun berjalan
$str="select noakundebet from ".$dbname.".keu_5parameterjurnal
    where kodeaplikasi = 'CLM'
    ";
$clm='';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $clm=$bar->noakundebet;
}
$qwe=explode("-",$periode);
$periode=$qwe[0].$qwe[1];
$bulan= $qwe[1];       
$periode2=$qwe[1].'-'.$qwe[0];
////          echo $pt." ".$gudang." ".$tanggal1." ".$tanggal2." ".$akundari." ".$akunsampai."<br>";

//ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt='COMPANY NAME';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namapt=strtoupper($bar->namaorganisasi);
}
// kamus akun
if($_SESSION['language']=='EN'){
    $zz='namaakun1 as namaakun';
}
else
{
    $zz='namaakun';
}
$str="select noakun,".$zz." from ".$dbname.".keu_5akun
                        where level = '5'
                        order by noakun
                        ";
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
                $namaakun[$bar->noakun]=$bar->namaakun;

        }

//ambil saldo awal
if($gudang==''){
    $str="select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."'";
    $wheregudang='';
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
	$wheregudang.="'".strtoupper($bar->kodeorganisasi)."',";
    }
    $wheregudang="and kodeorg in (".substr($wheregudang,0,-1).") ";
}else{
    $wheregudang="and kodeorg = '".$gudang."' ";
}
$str="select * from ".$dbname.".keu_saldobulanan where periode = '".$periode."' and noakun >= '".$akundari."' and noakun <= '".$akunsampai."'
     and noakun !='".$clm."' ".$wheregudang." order by noakun, kodeorg";
//$saldoawal=0;
//$saldoawal=0; 

$stream=$_SESSION['lang']['catatanneraca'].'<br>'.$_SESSION['lang']['periode'].':'.$periode2.'<br>'.$_SESSION['lang']['pt'].':'.$namapt;
       $stream.="<table class=sortable cellspacing=1 border=1 width=100%>
		    <tr>
			  <td align=center>".$_SESSION['lang']['nomor']."</td>
			  <td align=center>".$_SESSION['lang']['noakun']."</td>
			  <td align=center>".$_SESSION['lang']['namaakun']."</td>
			  <td align=center>".$_SESSION['lang']['kodeorg']."</td>
			  <td align=center>".$_SESSION['lang']['saldoawal']."</td>
			  <td align=center>".$_SESSION['lang']['debet']."</td>
			  <td align=center>".$_SESSION['lang']['kredit']."</td>
			  <td align=center>".$_SESSION['lang']['saldoakhir']."</td>
			</tr> ";

$no=0;
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $no+=1;
    $qweawal="awal".$bulan;
    $qwedebet="debet".$bulan;
    $qwekredit="kredit".$bulan;
    $saldoawal=$bar->$qweawal; $totalawal+=$saldoawal;
    $saldodebet=$bar->$qwedebet; $totaldebet+=$saldodebet; 
    $saldokredit=$bar->$qwekredit; $totalkredit+=$saldokredit;
    $saldoakhir=$saldoawal+$saldodebet-$saldokredit; $totalakhir+=$saldoakhir;
    $stream.="<tr class=rowcontent>";
        $stream.="<td>".$no."</td>";
        $stream.="<td>".$bar->noakun."</td>";
        $stream.="<td>".$namaakun[$bar->noakun]."</td>";
        $stream.="<td>".$bar->kodeorg."</td>";
        $stream.="<td align=right>".number_format($saldoawal)."</td>";
        $stream.="<td align=right>".number_format($saldodebet)."</td>";
        $stream.="<td align=right>".number_format($saldokredit)."</td>";
        $stream.="<td align=right>".number_format($saldoakhir)."</td>";
     $stream.="</tr>";
}
    $stream.="<tr class=rowcontent>";
        $stream.="<td align=center colspan=4>Total</td>";
        $stream.="<td align=right>".number_format($totalawal)."</td>";
        $stream.="<td align=right>".number_format($totaldebet)."</td>";
        $stream.="<td align=right>".number_format($totalkredit)."</td>";
        $stream.="<td align=right>".number_format($totalakhir)."</td>";
     $stream.="</tr>";
$stream.="</tbody>
		 <tfoot>
		 </tfoot>		 
	   </table>";
$stream.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];
$qwe=date("YmdHms");
$nop_="Laporan_Catatan_Neraca_".$pt.$periode." ".$qwe;
if(strlen($stream)>0)
{
     $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
     gzwrite($gztralala, $stream);
     gzclose($gztralala);
     echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls.gz';
        </script>";
}    
?>