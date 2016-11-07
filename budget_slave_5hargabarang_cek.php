<?php
// file creator: dhyaz aug 10, 2011
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$tahunbudget=$_POST['tahunbudget'];
$regional=$_POST['regional'];
$sumberharga=$_POST['sumberharga'];
$kelompokbarang=$_POST['kelompokbarang'];
$what=$_POST['what'];

if($what=='adadata'){
    $str="select * from ".$dbname.".bgt_masterbarang 
    where tahunbudget='".$tahunbudget."' and regional = '".$regional."' 
        and kodebarang like '".$kelompokbarang."%'
            limit 0,1";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $adadata="1";	
    }
    if($adadata=="1"){
        echo "Sudah ada data, bila lanjut akan ditimpa.\nLanjut?/nThis kind of data already exist.\n Replace ?"; exit;
    }
}

if($what=='closing'){
    $str="select * from ".$dbname.".bgt_masterbarang 
    where tahunbudget='".$tahunbudget."' and regional = '".$regional."' 
        and closed = 1
            limit 0,1";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $sudahtutup="1";	
    }
    if($sudahtutup=="1"){
        echo "Data has been closed"; exit;
    }
    
//    echo $str;
}
if($what=='delete'){
    $str="DELETE FROM ".$dbname.".bgt_masterbarang WHERE tahunbudget = ".$tahunbudget." and regional = '".$regional."'";
    $res=mysql_query($str);
    
//    echo $str;
}

if($what=='editing'){
echo"<table width=100%><tr id=baris_0 class=rowheader>";
    echo"<td align=left>Set ".$_SESSION['lang']['varian']."";
    echo"<input type=text id=varianall size=5 value='0.00' maxlength=5 class=myinputtext onkeypress=\"return angka_doangsamaminus(event);\">
            <button class=mybutton id=proses onclick=updateHargaall()>".$_SESSION['lang']['proses']."</button></td>";
    echo"<td align=right><button class=mybutton id=simpan onclick=updateHarga(1)>".$_SESSION['lang']['save']."</button></td>";
echo"</tr></table>";
echo"<table id=container9 class=sortable cellspacing=1 border=0 width=100%>
     <thead>
        <tr>
            <td align=center>".$_SESSION['lang']['nomor']."</td>
            <td align=center>".$_SESSION['lang']['budgetyear']."</td>
            <td align=center>".$_SESSION['lang']['regional']."</td>
            <td align=center>".$_SESSION['lang']['kodebarang']."</td>
            <td align=center>".$_SESSION['lang']['namabarang']."</td>
            <td align=center>".$_SESSION['lang']['satuan']."</td>
            <td align=center>".$_SESSION['lang']['sumberHarga']."</td>
            <td align=center>".$_SESSION['lang']['hargatahunlalu']."</td>
            <td align=center>".$_SESSION['lang']['varian']."</td>
            <td align=center>".$_SESSION['lang']['hargabudget']."</td>
       </tr>  
     </thead>
     <tbody>";

//Ambil harga dari gudang jika harga budget tahun lalu belum ada
$thnlalu=$tahunbudget-1;
$str="select distinct kodebarang,hargarata from ".$dbname.".log_5saldobulanan where hargarata>0 and periode like '".$thnlalu."%' order by hargarata";
$resq=mysql_query($str);
while($barq=mysql_fetch_object($resq)){
    if(!isset($harga[$bar->kodebarang])){
       $harga[$barq->kodebarang]=$barq->hargarata;
    }
}

$str="select b.regional, b.tahunbudget, a.kodebarang, b.hargasatuan, b.sumberharga, b.variant, b.hargalalu,a.namabarang,a.satuan from ".$dbname.
      ".log_5masterbarang a left join ".$dbname.".bgt_masterbarang b ON a.kodebarang=b.kodebarang
      where a.kodebarang like '".$kelompokbarang."%' order by a.namabarang";
//$str="select regional, tahunbudget, kodebarang, hargasatuan, sumberharga, variant, hargalalu from ".$dbname.".bgt_masterbarang
//      where tahunbudget = '".$tahunbudget."' and kodebarang like '".$kelompokbarang."%' and regional = '".$regional."' order by regional";
//echo $str;
$res=mysql_query($str);
$kobar='';
while($bar= mysql_fetch_object($res))
{
   if ($bar->tahunbudget == $tahunbudget and $bar->regional==$regional){
    $isidata[$bar->kodebarang][regional]=$bar->regional;
    $isidata[$bar->kodebarang][tahunbudget]=$bar->tahunbudget;
    $isidata[$bar->kodebarang][kodebarang]=$bar->kodebarang;
    $isidata[$bar->kodebarang][namabarang]=$bar->namabarang;
    $isidata[$bar->kodebarang][satuan]=$bar->satuan;
    $isidata[$bar->kodebarang][hargasatuan]=$bar->hargasatuan;
    $isidata[$bar->kodebarang][sumberharga]=$bar->sumberharga;
    $isidata[$bar->kodebarang][variant]=$bar->variant;
    $isidata[$bar->kodebarang][hargalalu]=$bar->hargalalu;
    $kobar.="'".$bar->kodebarang."',";
   } elseif ($bar->hargasatuan==NULL){
    $isidata[$bar->kodebarang][regional]=$regional;
    $isidata[$bar->kodebarang][tahunbudget]=$tahunbudget;
    $isidata[$bar->kodebarang][kodebarang]=$bar->kodebarang;
    $isidata[$bar->kodebarang][namabarang]=$bar->namabarang;
    $isidata[$bar->kodebarang][satuan]=$bar->satuan;
    $isidata[$bar->kodebarang][hargasatuan]=0;
    $isidata[$bar->kodebarang][sumberharga]=$regional;
    $isidata[$bar->kodebarang][variant]=0;
    $isidata[$bar->kodebarang][hargalalu]=0;
    $kobar.="'".$bar->kodebarang."',";
   }
}
    
//echo "<pre>";
//print_r($isidata);
//echo "</pre>";

//tampilkan data dalam array
if(empty($isidata)){
    echo"<tr><td colspan=9>Empty, please click
        <button id= buttonbaru class=mybutton onclick=buatbaru(".$tahunbudget.",'".$regional."',".$kelompokbarang.")>".$_SESSION['lang']['new']."</button>.</td>
        </tr>";
}else
foreach($isidata as $baris)
{
         if($baris[hargalalu]==0){
        $baris[hargalalu]=$harga[$baris['kodebarang']];
     }
     
    $no+=1;
    echo"<tr id=baris_".$no." class=rowcontent>";
        echo"<td>".$no."</td>";
        echo"<td>".$tahunbudget."</td>";
        echo"<td>".$regional."</td>";
        echo"<td><label id=kode_".$no.">".$baris[kodebarang]."</label></td>";
        echo"<td>".$baris[namabarang]."</td>";
        echo"<td>".$baris[satuan]."</td>";
        echo"<td><label id=sumber_".$no.">".$baris[sumberharga]."</td>";
        echo"<td align=right><label id=rata_".$no.">".number_format($baris[hargalalu],2)."</label></td>";
        echo"<td><input type=text id=varian_".$no." size=5 value='".$baris[variant]."' maxlength=5 class=myinputtext onkeyup=\"hitungharga(".$baris[hargalalu].",this.value,".$no.")\" onkeypress=\"return angka_doangsamaminus(event);\"></td>";
//        $hargarata=$baris[hargarata]+0; $hargarata=round($hargarata*100)/100;
        echo"<td><input type=text id=harga_".$no." size=15 value='".$baris[hargasatuan]."' maxlength=15 class=myinputtext onkeyup=\"hitungpersen(".$baris[hargalalu].",this.value,".$no.")\" onkeypress=\"return angka_doang(event);\"></td>";
    echo"</tr>";
}    

echo "     </tbody>
     <tfoot>
     </tfoot>		 
     </table>";
//    echo $str;
}
