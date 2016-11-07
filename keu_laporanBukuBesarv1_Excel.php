<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$pt=$_GET['pt'];
$gudang=$_GET['gudang'];
$tanggal1=$_GET['tanggal1'];
$tanggal2=$_GET['tanggal2'];
$akundari=$_GET['akundari'];
$akunsampai=$_GET['akunsampai'];
        
//$periode buat filter keu_saldobulanan, $bulan buat nentuin field-nya
$qwe=explode("-",$tanggal1);
$periode=$qwe[2].$qwe[1];
$bulan=$qwe[1];

//balik tanggal
$qwe=explode("-",$tanggal1);
$tanggal1=$qwe[2]."-".$qwe[1]."-".$qwe[0];
$qwe=explode("-",$tanggal2);
$tanggal2=$qwe[2]."-".$qwe[1]."-".$qwe[0];

//ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt='';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namapt=strtoupper($bar->namaorganisasi);
}

// kamus no pembayaran
$str="select notransaksi,nobayar from ".$dbname.".keu_kasbankht where tanggal >='".$tanggal1."' and tanggal <='".$tanggal2."'";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
    $arrNoBayar[$bar->notransaksi]=$bar->nobayar;
    $arrNoGiro[$bar->notransaksi]=$bar->nogiro;
}

//ambil namagudang
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$gudang."'";
$namagudang='';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namagudang=strtoupper($bar->namaorganisasi);
}

// kamus karyawan
$str="select karyawanid,nik,namakaryawan from ".$dbname.".datakaryawan where lokasitugas in
      (select kodeunit from ".$dbname.".bgt_regional_assignment where regional in
      (select regional from ".$dbname.".bgt_regional_assignment where kodeunit='".$gudang."'))";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $nikkary[$bar->karyawanid]=$bar->nik;
    $nmkary[$bar->karyawanid]=$bar->namakaryawan;
}
    $nmkary['masyarakat']="masyarakat";
    $nmkary['traksi']="traksi";

// exclude laba rugi tahun berjalan
$str="select noakundebet from ".$dbname.".keu_5parameterjurnal
    where kodeaplikasi = 'CLM'
    ";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $clm=$bar->noakundebet;
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
$str="select * from ".$dbname.".keu_saldobulanan where noakun != '".$clm."' and periode = '".$periode."' and noakun >= '".$akundari."' and noakun <= '".$akunsampai."' ".$wheregudang." order by noakun";

$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $qwe="awal".$bulan;
    $saldoawal[$bar->noakun]+=$bar->$qwe;
    $aqun[$bar->noakun]=$bar->noakun;
}
//        echo "<pre>";
//        print_r($saldoawal);
//        echo "</pre>";

//$cekData="select count(*) as jumlah from ".$dbname.".keu_jurnaldt_vw where noakun != '".$clm."' and tanggal >= '".
//        $tanggal1."' and tanggal <= '".$tanggal2."' and noakun >= '".$akundari."' and noakun <= '".$akunsampai."' ".$wheregudang;
//$rescek=fetchData($cekData);
//if ($rescek[0]['jumlah']>26000){
//    exit('Error:\n\rRange tanggal terlalu besar. Harap memilih range yang lebih kecil.');
//}

// ambil data
$isidata=array();
$str="select * from ".$dbname.".keu_jurnaldt_vw where noakun != '".$clm."' and tanggal >= '".$tanggal1."' and tanggal <= '".$tanggal2."' and noakun >= '".$akundari."' and noakun <= '".$akunsampai."' ".$wheregudang." order by noakun, tanggal";
$res=mysql_query($str);
if (mysql_num_rows($res)>26000){
    echo 'Range tanggal terlalu besar. Harap memilih range yang lebih kecil.<br>';
    exit('Error');
}
while($bar= mysql_fetch_object($res))
{
    $qwe=$bar->nojurnal.$bar->noakun.$bar->nourut;
    $isidata[$qwe][nojur]=$bar->nojurnal;
    $isidata[$qwe][tangg]=$bar->tanggal;
    $isidata[$qwe][noaku]=$bar->noakun;
    $isidata[$qwe][keter]=$bar->keterangan;
    $isidata[$qwe][debet]=$bar->debet;
    $isidata[$qwe][kredi]=$bar->kredit;
    $isidata[$qwe][kodeb]=$bar->kodeblok;
        $noref[$bar->nojurnal]=$bar->noreferensi;
    if($bar->kodeblok=='')$org=$bar->kodeorg; else $org=substr($bar->kodeblok,0,6);
    $isidata[$qwe][organ]=$org;
    $isidata[$qwe][noref]=$bar->noreferensi;
    $isidata[$qwe][kosup]=$bar->kodesupplier;
    $isidata[$qwe][nodok]=$bar->nodok;
    $aqun[$bar->noakun]=$bar->noakun;
    $isidata[$qwe][nik]=$bar->nik;
}
//        echo "<pre>";
//        print_r($isidata);
//        echo "</pre>";

// kamus nama akun
$str="select noakun,namaakun from ".$dbname.".keu_5akun
    where level = '5' and noakun!='".$clm."'";
 
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $namaakun[$bar->noakun]=$bar->namaakun;
}

// kamus nama supplier
$str="select supplierid, namasupplier from ".$dbname.".log_5supplier
    ";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $namasupplier[$bar->supplierid]=$bar->namasupplier;
}

// kamus tahun tanam
$aresta="SELECT kodeorg, tahuntanam FROM ".$dbname.".setup_blok
    ";
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $tahuntanam[$res['kodeorg']]=$res['tahuntanam'];
} 

if(!empty($isidata)) foreach($isidata as $c=>$key) {
    $sort_noaku[] = $key['noaku'];
    $sort_tangg[] = $key['tangg'];
    $sort_debet[] = $key['debet'];
    $sort_nojur[] = $key['nojur'];
}

// sort
if(!empty($isidata))array_multisort($sort_noaku, SORT_ASC, $sort_tangg, SORT_ASC, $sort_debet, SORT_DESC, $sort_nojur, SORT_ASC, $isidata);
if(!empty($aqun))asort($aqun);

$stream=strtoupper($_SESSION['lang']['laporanbukubesar'])." : ".$namapt." ".$namagudang."<br>".
        strtoupper($_SESSION['lang']['tanggal'])." : ".tanggalnormal($tanggal1)." s/d ".tanggalnormal($tanggal2)."<br>".
        strtoupper($_SESSION['lang']['noakun'])." : ".$akundari." s/d ".$akunsampai."<br>
    <table border=1>
    <thead>
    <tr bgcolor='#dedede'>
        <td align=center>".$_SESSION['lang']['nomor']."</td>
        <td align=center>".$_SESSION['lang']['nojurnal']."</td>
        <td align=center>".$_SESSION['lang']['nobayar']."</td>   
        <td align=center>".$_SESSION['lang']['nogiro']."</td>   
        <td align=center>".$_SESSION['lang']['tanggal']."</td>
        <td align=center>".$_SESSION['lang']['noakun']."</td>
        <td align=center>".$_SESSION['lang']['keterangan']."</td>
        <td align=center>".$_SESSION['lang']['debet']."</td>
        <td align=center>".$_SESSION['lang']['kredit']."</td>
        <td align=center>".$_SESSION['lang']['saldo']."</td>
        <td align=center>".$_SESSION['lang']['kodeorg']."</td>
        <td align=center>".$_SESSION['lang']['kodeblok']."</td>
        <td align=center>".$_SESSION['lang']['tahuntanam']."</td>
        <td align=center>".$_SESSION['lang']['noreferensi']."</td>
        <td align=center>".$_SESSION['lang']['namasupplier']."</td>
        <td align=center>".$_SESSION['lang']['nodok']."</td>
        <td align=center>".$_SESSION['lang']['nik']."</td>
        <td align=center>".$_SESSION['lang']['namakaryawan']."</td>
    </tr>  
    </thead>
    <tbody id=container>";
 //tampil data
$no=0;
// tampilin daftar akun
if(!empty($aqun))foreach($aqun as $akyun){
    $subsalwal=$saldoawal[$akyun];
    $totaldebet=0;
    $totalkredit=0;
    $subsalak=$subsalwal;
    $salwal=$subsalwal;
    $grandsalwal+=$subsalwal;
    $stream.="<tr bgcolor='#dedede'>";
        $stream.="<td align=right colspan=5></td>";
        $stream.="<td>".$akyun."</td>";
        $stream.="<td colspan=3>".$namaakun[$akyun]."</td>";
        $stream.="<td align=right>".number_format($salwal)."</td>";
        $stream.="<td colspan=8></td>";
    $stream.="</tr>";
// tampilin jurnal daftar akun    
    if(!empty($isidata))foreach($isidata as $baris)
    {
        if($baris[noaku]==$akyun){
            $no+=1;
            $stream.="<tr>";
            $stream.="<td>".$no."</td>";
            $stream.="<td>".substr($baris[nojur],14,8)."</td>";
            $stream.="<td>".$arrNoBayar[$noref[$baris[nojur]]]."</td>";                       
            $stream.="<td>".$arrNoGiro[$noref[$baris[nojur]]]."</td>";                       
            $stream.="<td>".$baris[tangg]."</td>";
            $stream.="<td>".$baris[noaku]."</td>";
            $stream.="<td>".$baris[keter]."</td>";

//            $stream.="<td align=right>".number_format($salwal)."</td>";
            $stream.="<td align=right>".number_format($baris[debet],2)."</td>";
            $totaldebet+=$baris[debet];
            $grandtotaldebet+=$baris[debet];
            $stream.="<td align=right>".number_format($baris[kredi],2)."</td>";
            $totalkredit+=$baris[kredi];
            $grandtotalkredit+=$baris[kredi];
            $salwal=$salwal+($baris[debet])-($baris[kredi]);
            $stream.="<td align=right>".number_format($salwal,2)."</td>";
            $stream.="<td>".$baris[organ]."</td>";
            $stream.="<td>".$baris[kodeb]."</td>";
            $stream.="<td>".$tahuntanam[$baris[kodeb]]."</td>";
            $stream.="<td>".$baris[noref]."</td>";
            $stream.="<td>".$namasupplier[$baris[kosup]]."</td>";
            $stream.="<td>".$baris[nodok]."</td>";
            $stream.="<td>'".$nikkary[$baris[nik]]."</td>";
            $stream.="<td>".$nmkary[$baris[nik]]."</td>";
            $stream.="</tr>";
            $subsalak=$salwal;
        }
    } 
// subtotal    
    $stream.="<tr bgcolor='#dedede'>";
        $stream.="<td align=right colspan=7>SubTotal</td>";
//        $stream.="<td align=right>".number_format($subsalwal)."</td>";
        $stream.="<td align=right>".number_format($totaldebet,2)."</td>";
        $stream.="<td align=right>".number_format($totalkredit,2)."</td>";
        $stream.="<td align=right>".number_format($subsalak,2)."</td>";
        $stream.="<td colspan=6></td>";
     $stream.="</tr>";
}

// total
    $grandsalak=$grandsalwal+$grandtotaldebet-$grandtotalkredit;
    $stream.="<tr bgcolor='#dedede'>";
        $stream.="<td align=right colspan=7>GrandTotal</td>";
//        $stream.="<td align=right>".number_format($grandsalwal)."</td>";
        $stream.="<td align=right>".number_format($grandtotaldebet,2)."</td>";
        $stream.="<td align=right>".number_format($grandtotalkredit,2)."</td>";
        $stream.="<td align=right>".number_format($grandsalak,2)."</td>";
        $stream.="<td colspan=6></td>";
     $stream.="</tr>";

$stream.="</tbody>
		 <tfoot>
		 </tfoot>		 
	   </table>";
$stream.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];
$qwe=date("YmdHms");
$nop_="Laporan_BukuBesar_".$pt."_".$gudang." ".$qwe;
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