<?php
// file creator: dhyaz aug 10, 2011
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$tahunbudget=$_POST['tahunbudget'];
$kodeorg=substr($_SESSION['empl']['lokasitugas'],0,4);

//check, one-two
if($tahunbudget==''){
    echo "WARNING: silakan memilih tahun budget."; exit;
}

//bila sudah ada data, masukkan ke dalam array
//$str2="select a.golongan, a.jumlah, b.* from ".$dbname.".bgt_upah a
//    left join ".$dbname.".sdm_5golongan b on a.golongan=concat('SDM-',b.kodegolongan)
//    where a.tahunbudget = '".$tahunbudget."' and a.kodeorg = '".$kodeorg."'
//    order by a.golongan";
$str2="select a.golongan, a.jumlah, b.nama as namagolongan from ".$dbname.".bgt_upah a
    left join ".$dbname.".bgt_kode b on a.golongan=b.kodebudget
    where a.tahunbudget = '".$tahunbudget."' and a.kodeorg = '".$kodeorg."'
    order by a.golongan";
$res2=mysql_query($str2);
while($bar2= mysql_fetch_object($res2))
{
   $isidata[$bar2->golongan][kodegolongan]=$bar2->golongan;
   $isidata[$bar2->golongan][upah]=$bar2->jumlah;
   $isidata[$bar2->golongan][namagolongan]=$bar2->namagolongan;
}

//echo $str2;
//echo "<pre>";
//print_r($isidata);
//echo "</pre>";

echo"<button class=mybutton id=tutup onclick=tutupHarga(1)>".$_SESSION['lang']['close']."</button>";

echo"<table cellspacing=1 border=0 class=sortable>
    <thead>
    <tr class=\"rowheader\">
    <td>".substr($_SESSION['lang']['nomor'],0,2)."</td>
    <td>".$_SESSION['lang']['kodeorg']."</td>
    <td>".$_SESSION['lang']['kodegolongan']."</td>
    <td>".$_SESSION['lang']['levelname']."</td>
    <td>".$_SESSION['lang']['upahkerja']."</td>
    </tr></thead><tbody>";

//tampilkan data dalam array
foreach($isidata as $baris)
{
    $no+=1;
    echo"<tr id=baris2_".$no." class=rowcontent>";
        echo"<td>".$no."</td>";
        echo"<td><label id=kodeorg2_".$no.">".$kodeorg."</td>";
        echo"<td><label id=kodegolongan2_".$no.">".$baris[kodegolongan]."</td>";
        echo"<td>".$baris[namagolongan]."</td>";
        echo"<td align=right><label id=upah2_".$no.">".number_format($baris[upah])."</td>";
    echo"</tr>";
}    

echo"</tbody></table>";