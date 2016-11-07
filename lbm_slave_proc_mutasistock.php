<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
 
//	$pt=$_POST['pt']; source: log_laporanHutangSupplier.php
$unit=$_POST['unit'];
if($unit=='')$unit=$_GET['unit'];
$tahun=$_POST['tahun'];
if($tahun=='')$tahun=$_GET['tahun'];
$excel=$_POST['proses'];
if($excel=='')$excel=$_GET['proses'];
$kelompok=$_POST['kelompok'];
if($kelompok=='')$kelompok=$_GET['kelompok'];
$pilih=$_POST['pilih'];
if($pilih=='')$pilih=$_GET['pilih'];
$mayor=$_POST['mayor'];
if($mayor=='')$mayor=$_GET['mayor'];
$urut=$_POST['urut'];
if($urut=='')$urut=$_GET['urut'];
$asc=$_POST['asc'];
if($asc=='')$asc=$_GET['asc'];

if($pilih=='volume')if($mayor=='mayor'){
    echo "warning: \nsilakan pilih Display : Nilai \nuntuk pilihan Per Mayor";
    exit;
}

$tahunlalu=$tahun-1;

if(($unit=='')||($tahun=='')){
    echo "Warning: silakan memilih gudang."; exit;
}

//namabarang
$sData="select kodebarang, namabarang, satuan from ".$dbname.".log_5masterbarang";
$qData=mysql_query($sData);// or die(mysql_error());
while($rData=mysql_fetch_assoc($qData))
{
    $nabar[$rData['kodebarang']]=$rData['namabarang'];  
    $satbar[$rData['kodebarang']]=$rData['satuan'];  
}

if($mayor=='mayor'){
    //kelompokbarang
    $sData="select kode, kelompok, kelompokbiaya from ".$dbname.".log_5klbarang";
    $qData=mysql_query($sData);// or die(mysql_error());
    while($rData=mysql_fetch_assoc($qData))
    {
        $nabar[$rData['kode']]=$rData['kelompok'];  
        $satbar[$rData['kode']]=$rData['kelompokbiaya'];  
    }
}

//last period
$sData="select substr(tanggal,6,2) as bulan from ".$dbname.".log_transaksiht where kodegudang = '".$unit."' order by tanggal";
if($unit=='sumatera')
    $sData="select substr(tanggal,6,2) as bulan from ".$dbname.".log_transaksiht where (kodegudang LIKE 'MRKE%' OR kodegudang LIKE 'SKSE%' OR kodegudang LIKE 'SOGM%' OR kodegudang LIKE 'SSRO%' OR kodegudang LIKE 'WKNE%' OR kodegudang LIKE 'SOGE%' OR kodegudang LIKE 'SENE%') order by tanggal";                                                                                
if($unit=='kalimantan')
    $sData="select substr(tanggal,6,2) as bulan from ".$dbname.".log_transaksiht where (kodegudang LIKE 'SBME%' OR kodegudang LIKE 'SBNE%' OR kodegudang LIKE 'SMLE%' OR kodegudang LIKE 'SMTE%' OR kodegudang LIKE 'SSGE%' OR kodegudang LIKE 'STLE%') order by tanggal";
$qData=mysql_query($sData);// or die(mysql_error());
while($rData=mysql_fetch_assoc($qData))
{
    $bulan=0+$rData['bulan'];
}

$buattes="";
//$buattes="and kodebarang = '31100042'";

$resData=array();

// pilihan default
$kodebarang_="kodebarang,";
$saldoawalqty_="saldoawalqty,";
$qtymasuk_="qtymasuk,";
$qtykeluar_="qtykeluar,";
$saldoakhirqty_="saldoakhirqty,";
$hargarata_="hargarata";

$groupbykodebarang_="group by kodebarang";

// pilih regional
if($unit==('sumatera')or('kalimantan')){
    $saldoawalqty_="sum(saldoawalqty) as saldoawalqty,";
    $qtymasuk_="sum(qtymasuk) as qtymasuk,";
    $qtykeluar_="sum(qtykeluar) as qtykeluar,";
    $saldoakhirqty_="sum(saldoakhirqty) as saldoakhirqty,";
    $hargarata_="avg(hargarata) as hargarata";        
}

// pilih per mayor
if($mayor=='mayor'){
    $kodebarang_="substr(kodebarang,1,3) as kodebarang,";
    $saldoawalqty_="sum(saldoawalqty*hargarata) as saldoawalqty,";
    $qtymasuk_="sum(qtymasuk*hargarata) as qtymasuk,";
    $qtykeluar_="sum(qtykeluar*hargarata) as qtykeluar,";
    $saldoakhirqty_="sum(saldoakhirqty*hargarata) as saldoakhirqty";
    $hargarata_="";
    
    $groupbykodebarang_="group by substr(kodebarang,1,3)";   
}

//saldo + transaksi per periode
for ($i=1; $i<=12; $i++)
{
    if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
    $per=$tahun."-".$ii;
    $sData="SELECT ".$kodebarang_." ".$saldoawalqty_." ".$qtymasuk_." ".$qtykeluar_." ".$saldoakhirqty_." ".$hargarata_." FROM ".$dbname.".log_5saldobulanan
        WHERE kodegudang = '".$unit."' ".$buattes." and periode = '".$per."' and kodebarang like '".$kelompok."%' ".$groupbykodebarang_." order by periode";
    if($unit=='sumatera')
    $sData="SELECT ".$kodebarang_." ".$saldoawalqty_." ".$qtymasuk_." ".$qtykeluar_." ".$saldoakhirqty_." ".$hargarata_." FROM ".$dbname.".log_5saldobulanan
        WHERE (kodegudang LIKE 'MRKE%' OR kodegudang LIKE 'SKSE%' OR kodegudang LIKE 'SOGM%' OR kodegudang LIKE 'SSRO%' OR kodegudang LIKE 'WKNE%' OR kodegudang LIKE 'SOGE%' OR kodegudang LIKE 'SENE%') ".$buattes." and periode = '".$per."' and kodebarang like '".$kelompok."%' ".$groupbykodebarang_." order by periode";    
    if($unit=='kalimantan')
    $sData="SELECT ".$kodebarang_." ".$saldoawalqty_." ".$qtymasuk_." ".$qtykeluar_." ".$saldoakhirqty_." ".$hargarata_." FROM ".$dbname.".log_5saldobulanan
        WHERE (kodegudang LIKE 'SBME%' OR kodegudang LIKE 'SBNE%' OR kodegudang LIKE 'SMLE%' OR kodegudang LIKE 'SMTE%' OR kodegudang LIKE 'SSGE%' OR kodegudang LIKE 'STLE%') ".$buattes." and periode = '".$per."' and kodebarang like '".$kelompok."%' ".$groupbykodebarang_." order by periode";    
    $qData=mysql_query($sData);// or die(mysql_error());

    while($rData=mysql_fetch_assoc($qData))
    {
        if($i==1){
            $resData[$rData['kodebarang']][sallu]=$rData['saldoawalqty'];       // saldo awal tahun
            if($pilih=='nilai')if($mayor=='')$resData[$rData['kodebarang']][sallu]=$rData['saldoawalqty']*$rData['hargarata'];  
        }
        $awal=$per."A";  
        $terima=$per."R";  
        $kasih=$per."I";  
        $saldo=$per."S";  
        $harga=$per."H";
        $resData[$rData['kodebarang']][kobar]=$rData['kodebarang'];             // kode barang
        $resData[$rData['kodebarang']][$awal]=$rData['saldoawalqty'];           // saldo awal bulan
            if($pilih=='nilai')if($mayor=='')$resData[$rData['kodebarang']][$awal]=$rData['saldoawalqty']*$rData['hargarata'];  
        $resData[$rData['kodebarang']][$terima]=$rData['qtymasuk'];             // saldo masuk
            if($pilih=='nilai')if($mayor=='')$resData[$rData['kodebarang']][$terima]=$rData['qtymasuk']*$rData['hargarata']; 
        $resData[$rData['kodebarang']][$kasih]=$rData['qtykeluar'];             // saldo keluar
            if($pilih=='nilai')if($mayor=='')$resData[$rData['kodebarang']][$kasih]=$rData['qtykeluar']*$rData['hargarata'];
        $resData[$rData['kodebarang']][$saldo]=$rData['saldoakhirqty'];         // saldo akhir
            if($pilih=='nilai')if($mayor=='')$resData[$rData['kodebarang']][$saldo]=$rData['saldoakhirqty']*$rData['hargarata'];

        $resData[$rData['kodebarang']][totem]+=$rData['qtymasuk'];              // total terima
            if($pilih=='nilai')if($mayor=='')$resData[$rData['kodebarang']][totem]+=$rData['qtymasuk']*$rData['hargarata'];
        $resData[$rData['kodebarang']][tokel]+=$rData['qtykeluar'];             // total keluar
            if($pilih=='nilai')if($mayor=='')$resData[$rData['kodebarang']][tokel]+=$rData['qtykeluar']*$rData['hargarata'];
        $resData[$rData['kodebarang']][salak]=$rData['saldoakhirqty'];          // saldo akhir tahun
            if($pilih=='nilai')if($mayor=='')$resData[$rData['kodebarang']][salak]=$rData['saldoakhirqty']*$rData['hargarata'];
        $resData[$rData['kodebarang']][$harga]=$rData['hargarata'];             // harga bulan ini
//            if($pilih=='nilai')if($mayor=='')$resData[$rData['kodebarang']][$harga]='';
        if($rData['hargarata']!=0)$resData[$rData['kodebarang']][hargaterakhir]=$rData['hargarata'];
    }
}

//if(!empty($resData))
//    ksort($resData);

   // foreach($resData as $c=>$key) {
   // $sort_kobar[] = $key['kobar'];
   // array_multisort($resData[$rData['kodebarang']], SORT_ASC);    
//}
 
if(!empty($resData)) foreach($resData as $c=>$key) {
    if($urut=='kodebarang')$sort_masuk[] = $key[kobar];
    if($urut=='awal')$sort_masuk[] = $key[sallu];
    if($urut=='masuk')$sort_masuk[] = $key[totem];
    if($urut=='keluar')$sort_masuk[] = $key[tokel];
    if($urut=='akhir')$sort_masuk[] = $key[salak];
    if($urut=='harga'){
        $sort_masuk[] = $key[hargaterakhir];
        if($mayor=='mayor')$sort_masuk[] = $key[kobar];
    }
}

if($asc=='asc'){
    if(!empty($resData))array_multisort($sort_masuk, SORT_ASC, $resData);    
}else{
    if(!empty($resData))array_multisort($sort_masuk, SORT_DESC, $resData);        
}

// sort

//echo "<pre>";	
//print_r($resData);
//echo "</pre>";	

//echo"<br>str :".$str; exit;
//=================================================
	 
$no=0; $tab='';

if($excel=='excel')
{
    if($urut=='kodebarang')$tampilurut='Kode Barang';
    if($urut=='awal')$tampilurut='Saldo Awal';
    if($urut=='masuk')$tampilurut='Penerimaan';
    if($urut=='keluar')$tampilurut='Pengeluaran';
    if($urut=='akhir')$tampilurut='Saldo Akhir';
    if($urut=='harga')$tampilurut='Harga';
    $bg=" bgcolor=#DEDEDE";
    $brdr=1;
    $tab.= 'Mutasi Stock '.$unit.' '.$tahun.'<br>';
    $tab.= 'Opsi: '.$kelompok.' '.$mayor.' '.$pilih.'<br>';
    $tab.= 'Urut: '.$tampilurut.' '.$asc;
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
for ($i=1; $i<=12; $i++)
{
    if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
    $per=$tahun."-".$ii;
    $tab.="<td align=center ".$bg.">".$per."</td>";                             // periode
}
$tab.="<td align=center ".$bg.">".$tahun."</td>";                               // total setahun
$tab.="</tr>";
$tab.="</thead><tbody>"; 

//echo "<pre>";
//print_r($resData);
//echo "</pre>";

//echo "warning: pilih: ".$pilih." mayor: ".$mayor;
//exit;

if(!empty($resData))foreach($resData as $ar)
{
    $no+=1;
    $tab.="<tr class=rowcontent>
        <td rowspan=5 align=center>".$no."</td>";                               // nomor
    
    $tab.="<td rowspan=5 align=left valign=center>".$ar[kobar]."<br>".$nabar[$ar[kobar]]."<br>".$satbar[$ar[kobar]]."</td>"; // kode barang
    
    // saldo awal
    $tab.="<td nowrap bgcolor='' align=left valign=bottom>Saldo Awal</td>";
    for ($i=1; $i<=12; $i++)
    {
        if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
        $per=$tahun."-".$ii;
        $terima=$per."R"; $kasih=$per."I"; $sama=$per."S"; $awal=$per."A"; $harga=$per."H";   
        $tab.="<td bgcolor='' align=right>".number_format($ar[$awal])."</td>";   // tampilkan saldo awal bulan ini
    }    
    $tab.="<td bgcolor='' align=right><b>".number_format($ar[sallu])."</b></td>";// tampilkan saldo awal tahun ini
    $tab.="</tr><tr class=rowcontent>";
    $tab.="<td nowrap bgcolor='AAFFAA' align=left valign=bottom>Masuk</td>";
    
    // penerimaan
    for ($i=1; $i<=12; $i++)
    {
        if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
        $per=$tahun."-".$ii;
        $terima=$per."R"; $kasih=$per."I"; $sama=$per."S"; $awal=$per."A"; $harga=$per."H";
        $tab.="<td bgcolor='AAFFAA' align=right style='cursor:pointer;' onclick=getDetailGudang('GR','".$ar[kobar]."','".$per."',event) title='Klik untuk melihat detail.'>".
                number_format($ar[$terima])."</td>";                               // tampilkan penerimaan bulan ini
    }
    $tab.="<td bgcolor='AAFFAA' align=right style='cursor:pointer;' onclick=getDetailGudang('GR','".$ar[kobar]."','".$tahun."',event) title='Klik untuk melihat detail.'><b>".
            number_format($ar[totem])."</b></td>";                               // tampilan total penerimaan
    $tab.="</tr><tr class=rowcontent>";
    $tab.="<td nowrap bgcolor='FFAAAA' align=left valign=bottom>Keluar</td>";
    
    // pengeluaran
    for ($i=1; $i<=12; $i++)
    {
        if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
        $per=$tahun."-".$ii;
        $terima=$per."R"; $kasih=$per."I"; $sama=$per."S"; $awal=$per."A"; $harga=$per."H";
        $tab.="<td bgcolor='FFAAAA' align=right style='cursor:pointer;' onclick=getDetailGudang('GI','".$ar[kobar]."','".$per."',event) title='Klik untuk melihat detail.'>".
                number_format($ar[$kasih])."</td>";                               // tampilkan pengeluaran bulan ini
    }
    $tab.="<td bgcolor='FFAAAA' align=right style='cursor:pointer;' onclick=getDetailGudang('GI','".$ar[kobar]."','".$tahun."',event) title='Klik untuk melihat detail.'><b>".
            number_format($ar[tokel])."</b></td>";                               // tam[pilkan total pengeluaran
    $tab.="</tr><tr class=rowcontent>";
    $tab.="<td nowrap bgcolor='' align=left valign=bottom>Saldo Akhir</td>";
    
    // saldo akhir
    for ($i=1; $i<=12; $i++)
    {
        if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
        $per=$tahun."-".$ii;
        $terima=$per."R"; $kasih=$per."I"; $sama=$per."S"; $awal=$per."A"; $harga=$per."H";
        $tab.="<td bgcolor='' align=right>".number_format($ar[$sama])."</td>";   // tampilkan saldo akhir bulan ini
    }
    $tab.="<td bgcolor='' align=right><b>".number_format($ar[salak])."</b></td>";// tampilkan saldo akhir tahun ini
    $tab.="</tr><tr class=rowcontent>";  
    
    // harga rata
    $tab.="<td nowrap bgcolor='AAAAFF' align=left valign=bottom>Harga</td>";
    for ($i=1; $i<=12; $i++)
    {
        if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
        $per=$tahun."-".$ii;
        $terima=$per."R"; $kasih=$per."I"; $sama=$per."S"; $awal=$per."A"; $harga=$per."H";
        $tab.="<td bgcolor='AAAAFF' align=right>".number_format($ar[$harga])."</td>";   
//        $totalharga+=$ar[$harga];
    }
    
//    $k=$bulan-1;
//    if(strlen($k)==1)$kk='0'.$k; else $kk=$k;
//    // jaga2 kalo tidak ada transaksi sama sekali, periode ini belum ditutup (belum ada harga bulan ini) ambil dari harga bulan lalu
//    $perk=$tahun."-".$kk;
//    $hargak=$perk."H";
//    @$ar[hargarata]=$ar[totalharga]/$ar[totalbarang]; // total/barang
//    if($ar[totalbarang]==0){
//        $ar[hargarata]=$ar[$hargak]; // harga bulan lalu
//    }
    if($mayor=='true')
    $tab.="<td bgcolor='AAAAFF' align=right><b></b></td>";  // harga rata tahunan
    else
    $tab.="<td bgcolor='AAAAFF' align=right><b>".number_format($ar[hargaterakhir])."</b></td>";  // harga rata tahunan
    $tab.="</tr>";  
}else{
    $qwe=4+$bulan;
    $tab.="<tr class=rowcontent><td colspan=".$qwe.">Data Empty.</td></tr>";
}    
    
$tab.="</tbody><tfoot></tfoot></table>";
if($excel!='excel'){
	echo $tab;
}else{
    if($unit=='sumatera')$unit='Sumatera(MRKE, SKSE, SOGM, SSRO, WKNE, SOGE, SENE)';
    if($unit=='kalimantan')$unit='Kalimantan(SBME, SBNE, SMLE, SMTE, SSGE, STLE)';
$nop_="MutasiStock_".$unit.$tahun.$kelompok.$pilih.$mayor.$urut.$asc;
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