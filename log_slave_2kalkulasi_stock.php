<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
 
//	$pt=$_POST['pt']; source: log_laporanHutangSupplier.php
$unit=$_POST['unit'];
if($unit=='')$unit=$_GET['unit'];
$tahun=$_POST['tahun'];
if($tahun=='')$tahun=$_GET['tahun'];
$excel=$_POST['excel'];
if($excel=='')$excel=$_GET['excel'];
$kelompok=$_POST['kelompok'];
if($kelompok=='')$kelompok=$_GET['kelompok'];
$kodebarang=$_POST['kodebarang'];
if($kodebarang=='')$kodebarang=$_GET['kodebarang'];
$pilih=$_POST['pilih'];
if($pilih=='')$pilih=$_GET['pilih'];

$tahunlalu=$tahun-1;

if(($unit=='')||($tahun=='')){
    echo "Warning: Period is missing."; exit;
}
//namabarang
$sData="select kodebarang, namabarang, satuan from ".$dbname.".log_5masterbarang";
$qData=mysql_query($sData);// or die(mysql_error());
while($rData=mysql_fetch_assoc($qData))
{
    $nabar[$rData['kodebarang']]=$rData['namabarang'];  
    $satbar[$rData['kodebarang']]=$rData['satuan'];  
}

//last period
$sData="select substr(tanggal,6,2) as bulan from ".$dbname.".log_transaksiht where tanggal like '".$tahun."%' and kodegudang = '".$unit."' order by tanggal";
    if($unit=='sumatera')
    $sData="select substr(tanggal,6,2) as bulan from ".$dbname.".log_transaksiht where tanggal like '".$tahun."%' and kodegudang in ('MRKE10','SKSE10','SOGM20','SSRO21','WKNE10') order by tanggal";
    if($unit=='kalimantan')
    $sData="select substr(tanggal,6,2) as bulan from ".$dbname.".log_transaksiht where tanggal like '".$tahun."%' and kodegudang in ('SBME10','SBNE10','SMLE10','SMTE10','SSGE10','STLE10') order by tanggal";
$qData=mysql_query($sData);// or die(mysql_error());
while($rData=mysql_fetch_assoc($qData))
{
    $bulan=0+$rData['bulan'];
}

$buattes="";
//$buattes="and kodebarang = '31100042'";

$resData=array();

//saldo tahunlalu	
$sData="select kodebarang, sum(saldoakhirqty) as saldo from ".$dbname.".log_5saldobulanan where kodegudang = '".$unit."' ".$buattes."
and periode = '".$tahunlalu."-12' and kodebarang like '".$kelompok."%' and kodebarang like '".$kodebarang."%' group by kodebarang";
    if($unit=='sumatera')
    $sData="select kodebarang, sum(saldoakhirqty) as saldo from ".$dbname.".log_5saldobulanan where kodegudang in ('MRKE10','SKSE10','SOGM20','SSRO21','WKNE10') ".$buattes."
    and periode = '".$tahunlalu."-12' and kodebarang like '".$kelompok."%' and kodebarang like '".$kodebarang."%' group by kodebarang";
    if($unit=='kalimantan')
    $sData="select kodebarang, sum(saldoakhirqty) as saldo from ".$dbname.".log_5saldobulanan where kodegudang in ('SBME10','SBNE10','SMLE10','SMTE10','SSGE10','STLE10') ".$buattes."
    and periode = '".$tahunlalu."-12' and kodebarang like '".$kelompok."%' and kodebarang like '".$kodebarang."%' group by kodebarang";
$qData=mysql_query($sData);// or die(mysql_error());
$per=$tahun."-01";
$awal=$per."A"; 
while($rData=mysql_fetch_assoc($qData))
{
    $resData[$rData['kodebarang']][kobar]=$rData['kodebarang'];  
    $resData[$rData['kodebarang']][sallu]=$rData['saldo'];  
    $resData[$rData['kodebarang']][salak]=$rData['saldo'];  
    $resData[$rData['kodebarang']][$awal]=$rData['saldo'];  
}

//harga januari
$sData="select kodebarang, sum(saldoakhirqty) as saldo, (sum(saldoakhirqty*hargarata)/sum(saldoakhirqty)) as hargarata, periode from ".$dbname.".log_5saldobulanan where kodegudang = '".$unit."' ".$buattes."
and periode = '".$tahun."-01' and kodebarang like '".$kelompok."%' and kodebarang like '".$kodebarang."%' group by kodebarang order by periode";
    if($unit=='sumatera')
    $sData="select kodebarang, sum(saldoakhirqty) as saldo, (sum(saldoakhirqty*hargarata)/sum(saldoakhirqty)) as hargarata, periode from ".$dbname.".log_5saldobulanan where kodegudang in ('MRKE10','SKSE10','SOGM20','SSRO21','WKNE10') ".$buattes."
    and periode = '".$tahun."-01' and kodebarang like '".$kelompok."%' and kodebarang like '".$kodebarang."%' group by kodebarang order by periode";
    if($unit=='kalimantan')
    $sData="select kodebarang, sum(saldoakhirqty) as saldo, (sum(saldoakhirqty*hargarata)/sum(saldoakhirqty)) as hargarata, periode from ".$dbname.".log_5saldobulanan where kodegudang in ('SBME10','SBNE10','SMLE10','SMTE10','SSGE10','STLE10') ".$buattes."
    and periode = '".$tahun."-01' and kodebarang like '".$kelompok."%' and kodebarang like '".$kodebarang."%' group by kodebarang order by periode";
$qData=mysql_query($sData);// or die(mysql_error());
while($rData=mysql_fetch_assoc($qData))
{
    $harga=$tahun."-01H";  
    $resData[$rData['kodebarang']][$harga]=$rData['hargarata'];  
}

//saldo + transaksi per periode
for ($i=1; $i<=$bulan; $i++)
{
    //penerimaan
    if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
    $per=$tahun."-".$ii;
    //perj = saldo next month
    $j=$i+1;
    if(strlen($j)==1)$jj='0'.$j; else $jj=$j;
    $perj=$tahun."-".$jj;
    $sData="select kodebarang, sum(jumlah) as saldo from ".$dbname.".log_transaksi_vw where kodegudang = '".$unit."' ".$buattes."
    and tanggal like '".$per."-%' and notransaksi like '%GR%' and post = '1' and kodebarang like '".$kelompok."%' and kodebarang like '".$kodebarang."%' group by kodebarang";
    if($unit=='sumatera')
    $sData="select kodebarang, sum(jumlah) as saldo from ".$dbname.".log_transaksi_vw where kodegudang in ('MRKE10','SKSE10','SOGM20','SSRO21','WKNE10') ".$buattes."
    and tanggal like '".$per."-%' and notransaksi like '%GR%' and post = '1' and kodebarang like '".$kelompok."%' and kodebarang like '".$kodebarang."%' group by kodebarang";
    if($unit=='kalimantan')
    $sData="select kodebarang, sum(jumlah) as saldo from ".$dbname.".log_transaksi_vw where kodegudang in ('SBME10','SBNE10','SMLE10','SMTE10','SSGE10','STLE10') ".$buattes."
    and tanggal like '".$per."-%' and notransaksi like '%GR%' and post = '1' and kodebarang like '".$kelompok."%' and kodebarang like '".$kodebarang."%' group by kodebarang";
    $qData=mysql_query($sData);// or die(mysql_error());
    while($rData=mysql_fetch_assoc($qData))
    {
        $resData[$rData['kodebarang']][kobar]=$rData['kodebarang'];
        $terima=$per."R";  
        $resData[$rData['kodebarang']][$terima]=$rData['saldo'];  
        $resData[$rData['kodebarang']][totem]+=$rData['saldo']; // total harga penerimaan
        $resData[$rData['kodebarang']][salak]+=$rData['saldo']; // update saldo akhir
        $sama=$per."S";  
        $resData[$rData['kodebarang']][$sama]=$resData[$rData['kodebarang']][salak];  
    }
    //pengeluaran
    $sData="select kodebarang, sum(jumlah) as saldo from ".$dbname.".log_transaksi_vw where kodegudang = '".$unit."' ".$buattes."
    and tanggal like '".$per."-%' and notransaksi like '%GI%' and post = '1' and kodebarang like '".$kelompok."%' and kodebarang like '".$kodebarang."%' group by kodebarang";
    if($unit=='sumatera')
    $sData="select kodebarang, sum(jumlah) as saldo from ".$dbname.".log_transaksi_vw where kodegudang in ('MRKE10','SKSE10','SOGM20','SSRO21','WKNE10') ".$buattes."
    and tanggal like '".$per."-%' and notransaksi like '%GI%' and post = '1' and kodebarang like '".$kelompok."%' and kodebarang like '".$kodebarang."%' group by kodebarang";
    if($unit=='kalimantan')
    $sData="select kodebarang, sum(jumlah) as saldo from ".$dbname.".log_transaksi_vw where kodegudang in ('SBME10','SBNE10','SMLE10','SMTE10','SSGE10','STLE10') ".$buattes."
    and tanggal like '".$per."-%' and notransaksi like '%GI%' and post = '1' and kodebarang like '".$kelompok."%' and kodebarang like '".$kodebarang."%' group by kodebarang";
    $qData=mysql_query($sData);// or die(mysql_error());
    while($rData=mysql_fetch_assoc($qData))
    {
        $resData[$rData['kodebarang']][kobar]=$rData['kodebarang'];  
        $kasih=$per."I";  
        $resData[$rData['kodebarang']][$kasih]=$rData['saldo'];  
        $resData[$rData['kodebarang']][tokel]+=$rData['saldo']; // total harga pengeluaan
        $resData[$rData['kodebarang']][salak]-=$rData['saldo']; // update saldo akhir
        $sama=$per."S";  
        $resData[$rData['kodebarang']][$sama]=$resData[$rData['kodebarang']][salak];  
    }
    //saldoakhir sistem
    $sData="select kodebarang, sum(saldoakhirqty) as saldo, (sum(saldoakhirqty*hargarata)/sum(saldoakhirqty)) as hargarata, periode from ".$dbname.".log_5saldobulanan where kodegudang = '".$unit."' ".$buattes."
    and periode = '".$per."' and kodebarang like '".$kelompok."%' and kodebarang like '".$kodebarang."%' group by kodebarang order by periode";
    if($unit=='sumatera')
    $sData="select kodebarang, sum(saldoakhirqty) as saldo, (sum(saldoakhirqty*hargarata)/sum(saldoakhirqty)) as hargarata, periode from ".$dbname.".log_5saldobulanan where kodegudang in ('MRKE10','SKSE10','SOGM20','SSRO21','WKNE10') ".$buattes."
    and periode = '".$per."' and kodebarang like '".$kelompok."%' and kodebarang like '".$kodebarang."%' group by kodebarang order by periode";
    if($unit=='kalimantan')
    $sData="select kodebarang, sum(saldoakhirqty) as saldo, (sum(saldoakhirqty*hargarata)/sum(saldoakhirqty)) as hargarata, periode from ".$dbname.".log_5saldobulanan where kodegudang in ('SBME10','SBNE10','SMLE10','SMTE10','SSGE10','STLE10') ".$buattes."
    and periode = '".$per."' and kodebarang like '".$kelompok."%' and kodebarang like '".$kodebarang."%' group by kodebarang order by periode";
    $qData=mysql_query($sData);// or die(mysql_error());
    while($rData=mysql_fetch_assoc($qData))
    {
//        $bulanterakhir=substr($rData['periode'],5,2);
        $resData[$rData['kodebarang']]['kobar']=$rData['kodebarang'];  
        $resData[$rData['kodebarang']][$per]=$rData['saldo'];  
        $resData[$rData['kodebarang']]['salaw']=$rData['saldo'];  
        $sama=$per."S";  
        $resData[$rData['kodebarang']][$sama]=$resData[$rData['kodebarang']][salak]; 
        $awal=$perj."A";  
        $resData[$rData['kodebarang']][$awal]=$rData['saldo']; // saldo akhir = saldo awal bulan berikutnya
        $harga=$per."H";
        $resData[$rData['kodebarang']][$harga]=$rData['hargarata'];  
        $hargaj=$perj."H";
        $resData[$rData['kodebarang']][$hargaj]=$rData['hargarata'];  
    }
}

//saldo akhir	
$sData="select kodebarang, saldoqty as saldo from ".$dbname.".log_5masterbarangdt where kodegudang = '".$unit."' and kodebarang like '".$kelompok."%' and kodebarang like '".$kodebarang."%' ".$buattes."";
    if($unit=='sumatera')
    $sData="select kodebarang, sum(saldoqty) as saldo from ".$dbname.".log_5masterbarangdt where kodegudang in ('MRKE10','SKSE10','SOGM20','SSRO21','WKNE10') and kodebarang like '".$kelompok."%' and kodebarang like '".$kodebarang."%' ".$buattes." group by kodebarang";
    if($unit=='kalimantan')
    $sData="select kodebarang, sum(saldoqty) as saldo from ".$dbname.".log_5masterbarangdt where kodegudang in ('SBME10','SBNE10','SMLE10','SMTE10','SSGE10','STLE10') and kodebarang like '".$kelompok."%' and kodebarang like '".$kodebarang."%' ".$buattes." group by kodebarang";
//echo $sData;    
$qData=mysql_query($sData);// or die(mysql_error());
while($rData=mysql_fetch_assoc($qData))
{
    $resData[$rData['kodebarang']]['salakpondoh']=$rData['saldo'];  
}


if(!empty($resData))
    ksort($resData);
   
	 
$no=0; $tab='';

if($excel=='excel')
{
    $bg=" bgcolor=#DEDEDE";
    $brdr=1;
}
else
{ 
    $bg="";
    $brdr=0;
}
$tab.="<table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable style='width:100%;'><thead><tr>
<td rowspan=1 align=center ".$bg.">No.</td>";
$tab.="<td align=left ".$bg.">".STRTOUPPER($_SESSION['lang']['kodebarang'])."<br>".STRTOUPPER($_SESSION['lang']['namabarang'])."<br>".STRTOUPPER($_SESSION['lang']['satuan'])."</td>";
$tab.="<td align=left ".$bg."></td>";
//$tab.="<td align=center ".$bg.">".$tahunlalu."</td>";
for ($i=1; $i<=$bulan; $i++)
{
    if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
    $per=$tahun."-".$ii;
    $tab.="<td align=center ".$bg.">".$per."</td>"; // periode
}
$tab.="<td align=center ".$bg.">".$tahun."</td>"; // total setahun
$tab.="</tr>";
$tab.="</thead><tbody>"; 

if(!empty($resData))foreach($resData as $ar)
{
    $no+=1;
    $tab.="<tr class=rowcontent>
    <td rowspan=5 align=center>".$no."</td>";
    $tab.="<td rowspan=5 align=left valign=center>".$ar[kobar]."<br>".$nabar[$ar[kobar]]."<br>".$satbar[$ar[kobar]]."</td>"; // kode barang
    $tab.="<td nowrap bgcolor='' align=left valign=bottom>".$_SESSION['lang']['saldoawal']."</td>";
//    $tab.="<td rowspan=4 bgcolor='AAAAFF' align=right>".number_format($ar[sallu])."</td>";
    // saldo awal + saldoakhir
    for ($i=1; $i<=$bulan; $i++)
    {
        if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
        $per=$tahun."-".$ii;
        $terima=$per."R"; $kasih=$per."I"; $sama=$per."S"; $awal=$per."A"; $harga=$per."H";
        $tampilan=$ar[$awal]; //  saldo awal bulan ini
        if($pilih=='nilai')$tampilan=$ar[$awal]*$ar[$harga]; // nilai awal bulan
        $tab.="<td bgcolor='' align=right>".number_format($tampilan)."</td>"; 
    }
    
    $tampilan=$ar[sallu]; // saldo awal tahun ini
        if($pilih=='nilai')$tampilan=$ar[sallu]*$ar[$harga]; // nilai awal tahun ini
    $tab.="<td bgcolor='' align=right><b>".number_format($tampilan)."</b></td>";  
    $tab.="</tr><tr class=rowcontent>";
    $tab.="<td nowrap bgcolor='AAFFAA' align=left valign=bottom>".$_SESSION['lang']['masuk']."</td>";
    // penerimaan
    $ar[totalnilai]=0;
    for ($i=1; $i<=$bulan; $i++)
    {
        if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
        $per=$tahun."-".$ii;
        $terima=$per."R"; $kasih=$per."I"; $sama=$per."S"; $awal=$per."A"; $harga=$per."H";
        $tampilan=$ar[$terima]; // penerimaan bulan ini
        if($pilih=='nilai')$tampilan=$ar[$terima]*$ar[$harga]; // nilai penerimaan
//        $tab.="<td bgcolor='AAFFAA' align=right style='cursor:pointer;' onclick=getDetailGudang('GR','".$ar[kobar]."','".$per."',event) title='Klik untuk melihat detail.'>".number_format($tampilan)."</td>"; 
        $tab.="<td bgcolor='AAFFAA' align=right style='cursor:pointer;' onclick=getDetailGudang('GR','".$ar[kobar]."','".$per."',event) title=".$_SESSION['lang']['klikdetail'].">".number_format($tampilan)."</td>"; 
        $ar[totalharga]+=$ar[$terima]*$ar[$harga];
        $ar[totalbarang]+=$ar[$terima];
        $ar[totalnilai]+=$ar[$terima]*$ar[$harga];
    }
    $tampilan=$ar[totem]; // total penerimaan 
        if($pilih=='nilai')$tampilan=$ar[totalharga]; // nilai penerimaan
//    $tab.="<td bgcolor='AAFFAA' align=right style='cursor:pointer;' onclick=getDetailGudang('GR','".$ar[kobar]."','".$tahun."',event) title='Klik untuk melihat detail.'><b>".number_format($tampilan)."</b></td>"; 
    $tab.="<td bgcolor='AAFFAA' align=right style='cursor:pointer;' onclick=getDetailGudang('GR','".$ar[kobar]."','".$per."',event) title=".$_SESSION['lang']['klikdetail']."><b>".number_format($tampilan)."</b></td>"; 
    $tab.="</tr><tr class=rowcontent>";
    $tab.="<td nowrap bgcolor='FFAAAA' align=left valign=bottom>".$_SESSION['lang']['keluar']."</td>";
    // pengeluaran
    $ar[totalnilai]=0;
    for ($i=1; $i<=$bulan; $i++)
    {
        if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
        $per=$tahun."-".$ii;
        $terima=$per."R"; $kasih=$per."I"; $sama=$per."S"; $awal=$per."A"; $harga=$per."H";
        $tampilan=$ar[$kasih]; // pengeluaran bulan ini
        if($pilih=='nilai')$tampilan=$ar[$kasih]*$ar[$harga]; // nilai pengeluaran
        $tab.="<td bgcolor='FFAAAA' align=right style='cursor:pointer;' onclick=getDetailGudang('GI','".$ar[kobar]."','".$per."',event) title=".$_SESSION['lang']['klikdetail'].">".number_format($tampilan)."</td>"; 
        $ar[totalharga]+=$ar[$kasih]*$ar[$harga];
        $ar[totalbarang]+=$ar[$kasih];
        $ar[totalnilai]+=$ar[$kasih]*$ar[$harga];
    }
    $tampilan=$ar[tokel]; // total pengeluaran
        if($pilih=='nilai')$tampilan=$ar[totalnilai]; // nilai pengeluaran
    $tab.="<td bgcolor='FFAAAA' align=right style='cursor:pointer;' onclick=getDetailGudang('GI','".$ar[kobar]."','".$tahun."',event) title=".$_SESSION['lang']['klikdetail']."><b>".number_format($tampilan)."</b></td>"; 
    $tab.="</tr><tr class=rowcontent>";
    $tab.="<td nowrap bgcolor='' align=left valign=bottom>".$_SESSION['lang']['saldoakhir']."</td>";
    // saldo akhir
    for ($i=1; $i<=$bulan; $i++)
    {
        if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
        $per=$tahun."-".$ii;
        $terima=$per."R"; $kasih=$per."I"; $sama=$per."S"; $awal=$per."A"; $harga=$per."H";
        $tampilan=$ar[$sama]; //  saldo akhir HASIL PERHITUNGAN !!!
        if($pilih=='nilai')$tampilan=$ar[$sama]*$ar[$harga]; // nilai saldo akhir
        $tab.="<td bgcolor='' align=right>".number_format($tampilan)."</td>";    
//            $tab.="<td align=right>".number_format($ar[$per])."</td>";                    saldo akhir DARI DATABASE !!!
    }
    $tampilan=$ar[salakpondoh];  // MASTERBARANGDT
        if($pilih=='nilai')$tampilan=$ar[salakpondoh]*$ar[$harga]; // nilai saldo akhir
    $tab.="<td bgcolor='' align=right><b>".number_format($tampilan)."</b></td>";
//        $tab.="<td bgcolor='9999FF' align=right>".number_format($ar[salak])."</td>";
    $tab.="</tr><tr class=rowcontent>";  
    // harga rata
    $totalharga=0;
    $tab.="<td nowrap bgcolor='AAAAFF' align=left valign=bottom>".$_SESSION['lang']['harga']."</td>";
    for ($i=1; $i<=$bulan; $i++)
    {
        if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
        $per=$tahun."-".$ii;
        $terima=$per."R"; $kasih=$per."I"; $sama=$per."S"; $awal=$per."A"; $harga=$per."H";
        $tab.="<td bgcolor='AAAAFF' align=right>".number_format($ar[$harga])."</td>";   
        $totalharga+=$ar[$harga];
        $ar[hargaterakhir]=$ar[$harga];
    }
    $k=$bulan-1;
    if(strlen($k)==1)$kk='0'.$k; else $kk=$k;
    // jaga2 kalo tidak ada transaksi sama sekali, periode ini belum ditutup (belum ada harga bulan ini) ambil dari harga bulan lalu
    $perk=$tahun."-".$kk;
    $hargak=$perk."H";
    @$ar[hargarata]=$ar[totalharga]/$ar[totalbarang]; // total/barang
    if($ar[totalbarang]==0){
        $ar[hargarata]=$ar[$hargak]; // harga bulan lalu
    }
    $tab.="<td bgcolor='AAAAFF' align=right><b>".number_format($ar[hargarata])."</b></td>";  // harga rata tahunan
    $tab.="</tr>";  
}else{
    $qwe=4+$bulan;
    $tab.="<tr class=rowcontent><td colspan=".$qwe.">Data Empty.</td></tr>";
}    
    
$tab.="</tbody><tfoot></tfoot></table>";
if($excel!='excel'){
	echo $tab;
}else{
    if($unit=='sumatera')$unit='Sumatera(MRKE, SKSE, SOGM, SSRO, WKNE)';
    if($unit=='kalimantan')$unit='Kalimantan(SBME, SBNE, SMLE, SMTE, SSGE, STLE)';
$nop_="MutasiStock_".$unit.$tahun;
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
}			
?>