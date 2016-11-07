<?php
// file creator: dhyaz aug 10, 2011
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$tahunbudget=$_POST['tahunbudget'];
$kodeorg=$_POST['kodeorg'];
$what=$_POST['what'];

//check, one-two
if($tahunbudget==''){
    echo "WARNING: silakan mengisi tahun budget."; exit;
}
if(strlen($tahunbudget)!=4){
    echo "WARNING: silakan mengisi tahun budget dengan benar."; exit;
}
if($kodeorg==''){
    echo "WARNING: silakan mengisi kode organisasi."; exit;
}

//bila sudah ada data, masukkan ke dalam array
$str2="select golongan, jumlah from ".$dbname.".bgt_upah
    where tahunbudget = '".$tahunbudget."' and kodeorg = '".$kodeorg."'
    order by golongan";
$res2=mysql_query($str2);
while($bar2= mysql_fetch_object($res2))
{
   $isidata[$bar2->golongan][kodegolongan]=$bar2->golongan;
   $isidata[$bar2->golongan][upah]=$bar2->jumlah;
}

//ambil data dari golongan, masukkan ke dalam array
$str="select * from ".$dbname.".bgt_kode where kodebudget like 'SDM%'
    order by nama";
//$kobar='';
$res=mysql_query($str);
while($bar= mysql_fetch_object($res))
{
   $isidata[$bar->kodebudget][kodegolongan]=$bar->kodebudget;
   $isidata[$bar->kodebudget][namagolongan]=$bar->nama;
   if($isidata[$bar->kodebudget][upah]!=0){}else $isidata[$bar->kodebudget][upah]=0;
}

//echo $kodeorg.">>>".substr($_SESSION['empl']['lokasitugas'],0,4);

if(($what=='closed')or(substr($_SESSION['empl']['lokasitugas'],0,4)!=$kodeorg)){
    
}else
echo "<button class=mybutton id=simpan onclick=simpanHarga(1)>".$_SESSION['lang']['save']."</button>";

echo"<table cellspacing=1 border=0 class=sortable>
    <thead>
    <tr class=\"rowheader\">
    <td>".substr($_SESSION['lang']['nomor'],0,2)."</td>
    <td>".$_SESSION['lang']['kodeorg']."</td>
    <td>".$_SESSION['lang']['kodegolongan']."</td>
    <td>".$_SESSION['lang']['levelname']."</td>
    <td>".$_SESSION['lang']['upahkerja']."</td>
    <td>".$_SESSION['lang']['save']."</td>
    </tr></thead><tbody>";

//tampilkan data dalam array
foreach($isidata as $baris)
{
    $no+=1;
    echo"<tr id=baris_".$no." class=rowcontent>";
        echo"<td>".$no."</td>";
        echo"<td><label id=kodeorg_".$no.">".$kodeorg."</td>";
        echo"<td><label id=kodegolongan_".$no.">".$baris[kodegolongan]."</td>";
        echo"<td>".$baris[namagolongan]."</td>";
        if(($what=='closed')or(substr($_SESSION['empl']['lokasitugas'],0,4)!=$kodeorg)){
            echo"<td align=right>".number_format($baris[upah])."</td>";
            echo"<td></td>";
        }else{
            echo"<td><input type=text id=upah_".$no." size=10 value='".$baris[upah]."' maxlength=10 class=myinputtext onkeypress=\"return angka_doang(event);\"></td>";
            echo"<td><button class=mybutton onclick=simpanHargasatusatu(".$no.")>".$_SESSION['lang']['save']."</button></td>";     
        }
    echo"</tr>";
}    

echo "</tbody></table>";