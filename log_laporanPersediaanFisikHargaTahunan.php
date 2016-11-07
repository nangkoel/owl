<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$pt=$_POST['pt'];
$gudang=$_POST['gudang'];
$periode=$_POST['periode'];



//exit("Error:$periode");

if($periode==''){
    echo"Error: Please choose Periode.";
    exit;
}        

$arrBarang=array();
$arrAwal=array();
$kamussatuan=array();
$kamusnamabarang=array();


#cari periode terkecil untuk sawal awal tahun
if($gudang=='')
{
    $iPer="select distinct periode from ".$dbname.".log_5saldobulanan where kodeorg='".$pt."' order by periode asc limit 1";
}
else
{
    $iPer="select distinct periode from ".$dbname.".log_5saldobulanan where kodeorg='".$pt."' and kodegudang = '".$gudang."' order by periode asc limit 1";
}
$nPer=mysql_query($iPer) or die (mysql_error($conn));
$dPer=mysql_fetch_assoc($nPer);
//echo $iPer;
$perAwal=$dPer['periode'];

//nyari barang
if($gudang==''){
    $str="select a.kodebarang, b.satuan, b.namabarang from ".$dbname.".log_5saldobulanan a
    left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang
    where a.kodeorg='".$pt."' 
    and a.periode like '".$periode."%'
    order by a.kodebarang";
}
else {
    $str="select a.kodebarang, b.satuan, b.namabarang from ".$dbname.".log_5saldobulanan a
    left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang
    where a.kodeorg='".$pt."' and kodegudang = '".$gudang."'
    and a.periode like '".$periode."%'
    order by a.kodebarang";
}
//$qData=mysql_query($sData) or die(mysql_error());
$res=mysql_query($str) or die(mysql_error());
while($bar=mysql_fetch_object($res))
{
    $arrBarang[$bar->kodebarang]=$bar->kodebarang;
    $kamussatuan[$bar->kodebarang]=$bar->satuan;
    $kamusnamabarang[$bar->kodebarang]=$bar->namabarang;
}



//nyari saldoawal
if($gudang=='')
{
    $str="select kodebarang, sum(saldoawalqty) as saldoawalqty , sum(nilaisaldoawal) as nilaisaldoawal from ".$dbname.".log_5saldobulanan
    where kodeorg='".$pt."' 
    and periode ='".$perAwal."'
    group by kodebarang order by kodebarang";
}
else {
    $str="select kodebarang, saldoawalqty, hargaratasaldoawal, nilaisaldoawal from ".$dbname.".log_5saldobulanan
    where kodeorg='".$pt."' and kodegudang = '".$gudang."'
    and periode ='".$perAwal."'
    order by kodebarang";
}
$res=mysql_query($str) or die(mysql_error());
while($bar=mysql_fetch_object($res))
{
    $arrAwal[$bar->kodebarang]['saldoawalqty']=$bar->saldoawalqty;
    @$arrAwal[$bar->kodebarang]['hargaratasaldoawal']=$bar->nilaisaldoawal/$bar->saldoawalqty;
    $arrAwal[$bar->kodebarang]['nilaisaldoawal']=$bar->nilaisaldoawal;
}

//nyari tahun berjalan
if($gudang==''){
    $str="select kodebarang, sum(qtymasuk) as qtymasuk, sum(qtykeluar) as qtykeluar, sum(qtymasukxharga) as qtymasukxharga, sum(qtykeluarxharga) as qtykeluarxharga 
    from ".$dbname.".log_5saldobulanan
    where kodeorg='".$pt."' 
    and periode like '".$periode."%'
    group by kodebarang
    order by kodebarang";
}
else {
    $str="select kodebarang, sum(qtymasuk) as qtymasuk, sum(qtykeluar) as qtykeluar, sum(qtymasukxharga) as qtymasukxharga, sum(qtykeluarxharga) as qtykeluarxharga 
    from ".$dbname.".log_5saldobulanan 
    where kodeorg='".$pt."' and kodegudang = '".$gudang."'
    and periode like '".$periode."%'
    group by kodebarang
    order by kodebarang";
}
$res=mysql_query($str) or die(mysql_error());
while($bar=mysql_fetch_object($res))
{
    $arrAwal[$bar->kodebarang]['qtymasuk']=$bar->qtymasuk;
    $arrAwal[$bar->kodebarang]['qtykeluar']=$bar->qtykeluar;
    $arrAwal[$bar->kodebarang]['qtymasukxharga']=$bar->qtymasukxharga;
    $arrAwal[$bar->kodebarang]['qtykeluarxharga']=$bar->qtykeluarxharga;
}

//echo"<pre>";
//print_r($arrAwal);
//echo"</pre>";

echo "<table>";
$no=0;
if(!empty($arrBarang)){
foreach($arrBarang as $barang)
{
    $no+=1;
    $hargamasuk=0;
    $hargakeluar=0;
    @$hargamasuk=$arrAwal[$barang]['qtymasukxharga']/$arrAwal[$barang]['qtymasuk'];
    @$hargakeluar=$arrAwal[$barang]['qtykeluarxharga']/$arrAwal[$barang]['qtykeluar'];
    
    @$salakqty=$arrAwal[$barang]['saldoawalqty']+$arrAwal[$barang]['qtymasuk']-$arrAwal[$barang]['qtykeluar'];
    @$salakrp =$arrAwal[$barang]['nilaisaldoawal']+$arrAwal[$barang]['qtymasukxharga']-$arrAwal[$barang]['qtykeluarxharga'];
    @$salakhar=$salakrp/$salakqty;
    echo"<tr class=rowcontent>
        <td>".$no."</td>
        <td>".$periode."</td>
        <td>".$barang."</td>
        <td>".$kamusnamabarang[$barang]."</td>
        <td>".$kamussatuan[$barang]."</td>
        <td align=right class=firsttd>".number_format($arrAwal[$barang]['saldoawalqty'],2)."</td>
        <td align=right>".number_format($arrAwal[$barang]['hargaratasaldoawal'],2)."</td>
        <td align=right>".number_format($arrAwal[$barang]['nilaisaldoawal'],2)."</td>
        <td align=right class=firsttd>".number_format($arrAwal[$barang]['qtymasuk'],2)."</td>
        <td align=right>".number_format($hargamasuk,2)."</td>
        <td align=right>".number_format($arrAwal[$barang]['qtymasukxharga'],2)."</td>
        <td align=right class=firsttd>".number_format($arrAwal[$barang]['qtykeluar'],2)."</td>
        <td align=right>".number_format($hargakeluar,2)."</td>
        <td align=right>".number_format($arrAwal[$barang]['qtykeluarxharga'],2)."</td>
        <td align=right class=firsttd>".number_format($salakqty,2)."</td>
        <td align=right>".number_format($salakhar,2)."</td>
        <td align=right>".number_format($salakrp,2)."</td>
    </tr>";    
}
}
if(empty($arrBarang)){
    echo"<tr class=rowcontent>
        <td colspan=17>no data.</td>
    </tr>";    
    
}
echo "</table>";

?>