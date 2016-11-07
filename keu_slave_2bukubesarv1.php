<?php
// file creator: dhyaz aug 3, 2011
// updated: dz may 22, 2012

require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$pt=$_POST['pt'];
$gudang=$_POST['gudang'];
$tanggal1=$_POST['tanggal1'];
$tanggal2=$_POST['tanggal2'];
$akundari=$_POST['akundari'];
$akunsampai=$_POST['akunsampai'];

//check, one-two
if($tanggal1==''){
    echo "WARNING: silakan mengisi tanggal."; exit;
}
if($tanggal2==''){
    echo "WARNING: silakan mengisi tanggal."; exit;
}
if($akundari==''){
    echo "WARNING: silakan memilih akun."; exit;
}
if($akunsampai==''){
    echo "WARNING: silakan memilih akun."; exit;
}

//$periode buat filter keu_saldobulanan, $bulan buat nentuin field-nya
$qwe=explode("-",$tanggal1);
$periode=$qwe[2].$qwe[1];
$bulan=$qwe[1];

//balik tanggal
$qwe=explode("-",$tanggal1);
$tanggal1=$qwe[2]."-".$qwe[1]."-".$qwe[0];
$qwe=explode("-",$tanggal2);
$tanggal2=$qwe[2]."-".$qwe[1]."-".$qwe[0];

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

// exclude laba rugi tahun berjalan
$str="select noakundebet from ".$dbname.".keu_5parameterjurnal
    where kodeaplikasi = 'CLM'
    ";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $clm=$bar->noakundebet;
}

// kamus no pembayaran
$str="select notransaksi,nobayar,nogiro from ".$dbname.".keu_kasbankht where tanggalposting >='".$tanggal1."' and tanggalposting <='".$tanggal2."'";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
    $arrNoBayar[$bar->notransaksi]=$bar->nobayar;
    $arrNoGiro[$bar->notransaksi]=$bar->nogiro;
}
// ambil saldo awal
$str="select * from ".$dbname.".keu_saldobulanan where noakun != '".$clm."' and periode = '".$periode."' and noakun >= '".$akundari."' and noakun <= '".$akunsampai."' ".$wheregudang." order by noakun";
$res=mysql_query($str);
$saldoawal = array();
while($bar=mysql_fetch_object($res))
{
    $qwe="awal".$bulan;
	if(!isset($saldoawal[$bar->noakun])) $saldoawal[$bar->noakun]=0;
    $saldoawal[$bar->noakun]+=$bar->$qwe;
    $aqun[$bar->noakun]=$bar->noakun;
}
//        echo "<pre>";
//        print_r($saldoawal);
//        echo "</pre>";

// kamus karyawan
$str="select karyawanid,nik,namakaryawan from ".$dbname.".datakaryawan";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $nikkary[$bar->karyawanid]=$bar->nik;
    $nmkary[$bar->karyawanid]=$bar->namakaryawan;
}
    $nmkary['masyarakat']="masyarakat";
    $nmkary['traksi']="traksi";

// kamus nama akun
$str="select noakun,namaakun from ".$dbname.".keu_5akun
    where level = '5' and noakun!='".$clm."'";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $namaakun[$bar->noakun]=$bar->namaakun;
}

// kamus tahun tanam
$aresta="SELECT kodeorg, tahuntanam FROM ".$dbname.".setup_blok
    ";
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $tahuntanam[$res['kodeorg']]=$res['tahuntanam'];
}   

// ambil data
$isidata=array();
$str="select * from ".$dbname.".keu_jurnaldt_vw where noakun != '".$clm."' and tanggal >= '".$tanggal1."' and tanggal <= '".$tanggal2."' and noakun >= '".$akundari."' and noakun <= '".$akunsampai."' ".$wheregudang." order by noakun, tanggal limit 50";
//echo $str;
$res=mysql_query($str);
while($bar= mysql_fetch_object($res))
{
    $qwe=$bar->nojurnal.$bar->noakun.$bar->nourut;
    $isidata[$qwe]['nojur']=$bar->nojurnal;
    $isidata[$qwe]['tangg']=$bar->tanggal;
    $isidata[$qwe]['noaku']=$bar->noakun;
    $isidata[$qwe]['keter']=$bar->keterangan;
    $isidata[$qwe]['debet']=$bar->debet;
    $isidata[$qwe]['kredi']=$bar->kredit;
    $isidata[$qwe]['kodeb']=$bar->kodeblok;
    $noref[$bar->nojurnal]=$bar->noreferensi;
    if($bar->kodeblok=='')$org=$bar->kodeorg; else $org=substr($bar->kodeblok,0,6);
    $isidata[$qwe]['organ']=$org;
    $aqun[$bar->noakun]=$bar->noakun;
    $isidata[$qwe]['nik']=$bar->nik;
}

//        echo "<pre>";
//        print_r($isidata);
//        echo "</pre>";

if(!empty($isidata)) foreach($isidata as $c=>$key) {
    $sort_noaku[] = $key['noaku'];
    $sort_tangg[] = $key['tangg'];
    $sort_debet[] = $key['debet'];
    $sort_nojur[] = $key['nojur'];
}
// sort
if(!empty($isidata))array_multisort($sort_noaku, SORT_ASC, $sort_tangg, SORT_ASC, $sort_debet, SORT_DESC, $sort_nojur, SORT_ASC, $isidata);
if(!empty($aqun))asort($aqun);

$no=0;

// tampilin daftar akun
$grandsalwal = $grandtotaldebet = $grandtotalkredit = 0;
if(!empty($aqun))foreach($aqun as $akyun){
    $subsalwal=$saldoawal[$akyun];
    $totaldebet=0;
    $totalkredit=0;
    $subsalak=$subsalwal;
    $salwal=$subsalwal;
    $grandsalwal+=$subsalwal;
    echo"<tr class=rowcontent>";
        echo"<td align=right colspan=5></td>";
        echo"<td>".$akyun."</td>";
        echo"<td colspan=3>".$namaakun[$akyun]."</td>";
        echo"<td align=right>".number_format($salwal)."</td>";
        echo"<td colspan=5></td>";
    echo"</tr>";
// tampilin jurnal daftar akun    
    if(!empty($isidata))foreach($isidata as $baris)
    {
        if($baris['noaku']==$akyun){
            $no+=1;
            echo"<tr class=rowcontent>";
            echo"<td style='width:30px;'>".$no."</td>";
            echo"<td style='width:75px;'>".substr($baris['nojur'],14,10)."</td>";
            echo"<td style='width:70px;'>".$arrNoBayar[$noref[$baris['nojur']]]."</td>";
            echo"<td style='width:50px;'>".$arrNoGiro[$noref[$baris['nojur']]]."</td>";
            echo"<td style='width:80px;' nowrap>".tanggalnormal($baris['tangg'])."</td>";
            echo"<td style='width:70px;'>".$baris['noaku']."</td>";
            echo"<td style='width:250px;'>".$baris['keter']."</td>";

//            echo"<td align=right style='width:100px;'>".number_format($salwal)."</td>";
            echo"<td align=right style='width:90px;'>".number_format($baris['debet'],2)."</td>";
            $totaldebet+=$baris['debet'];
            $grandtotaldebet+=$baris['debet'];
            echo"<td align=right style='width:90px;'>".number_format($baris['kredi'],2)."</td>";
            $totalkredit+=$baris['kredi'];
            $grandtotalkredit+=$baris['kredi'];
            $salwal=$salwal+($baris['debet'])-($baris['kredi']);
            echo"<td align=right style='width:90px;'>".number_format($salwal,2)."</td>";
            echo"<td style='width:50px;'>".$baris['organ']."</td>";
            echo"<td style='width:50px;'>".$baris['kodeb']."</td>";
            echo"<td style='width:40px;'>".(isset($tahuntanam[$baris['kodeb']])? $tahuntanam[$baris['kodeb']]: '')."</td>";
            echo"<td style='width:50px;'>".$nikkary[$baris['nik']]."</td>";
            echo"<td style='width:100px;'>".$nmkary[$baris['nik']]."</td>";
            echo"</tr>";
            $subsalak=$salwal;
        }
    } 
// subtotal    
    echo"<tr class=rowtitle>";
        echo"<td align=right colspan=7>SubTotal</td>";
//        echo"<td align=right style='width:100px;'>".number_format($subsalwal)."</td>";
        echo"<td align=right style='width:90px;'>".number_format($totaldebet,2)."</td>";
        echo"<td align=right style='width:90px;'>".number_format($totalkredit,2)."</td>";
        echo"<td align=right style='width:90px;'>".number_format($subsalak,2)."</td>";
        echo"<td colspan=3></td>";
     echo"</tr>";
}

// total
    $grandsalak=$grandsalwal+$grandtotaldebet-$grandtotalkredit;
    echo"<tr class=rowtitle>";
        echo"<td align=right colspan=7>GrandTotal</td>";
//        echo"<td align=right style='width:100px;'>".number_format($grandsalwal)."</td>";
        echo"<td align=right style='width:90px;'>".number_format($grandtotaldebet,2)."</td>";
        echo"<td align=right style='width:90px;'>".number_format($grandtotalkredit,2)."</td>";
        echo"<td align=right style='width:90px;'>".number_format($grandsalak,2)."</td>";
        echo"<td colspan=3></td>";
     echo"</tr>";