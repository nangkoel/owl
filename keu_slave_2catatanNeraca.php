<?php
// file creator: dhyaz aug 3, 2011
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$pt=$_POST['pt'];
$periode=$_POST['periode'];
$akundari=$_POST['akundari'];
$akunsampai=$_POST['akunsampai'];

//check, one-two
if($akundari==''){
    echo "WARNING: Account No. is obligatory."; exit;
}
if($akunsampai==''){
    echo "WARNING: Account No. is obligatory."; exit;
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
//          echo $pt." ".$gudang." ".$tanggal1." ".$tanggal2." ".$akundari." ".$akunsampai."<br>";

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
    echo"<tr class=rowcontent>";
        echo"<td style='width:50px'>".$no."</td>";
        echo"<td style='width:80px'>".$bar->noakun."</td>";
        echo"<td style='width:330px'>".$namaakun[$bar->noakun]."</td>";
        echo"<td style='width:100px'>".$bar->kodeorg."</td>";
        echo"<td align=right style='width:150px'>".number_format($saldoawal)."</td>";
        echo"<td align=right style='width:150px'>".number_format($saldodebet)."</td>";
        echo"<td align=right style='width:150px'>".number_format($saldokredit)."</td>";
        echo"<td align=right style='width:150px'>".number_format($saldoakhir)."</td>";
     echo"</tr>";
} 
    echo"<tr>";
        echo"<td align=center colspan=4>Total</td>";
        echo"<td align=right style='width:150px'>".number_format($totalawal)."</td>";
        echo"<td align=right style='width:150px'>".number_format($totaldebet)."</td>";
        echo"<td align=right style='width:150px'>".number_format($totalkredit)."</td>";
        echo"<td align=right style='width:150px'>".number_format($totalakhir)."</td>";
     echo"</tr>";
echo"</tbody>
		 <tfoot>
		 </tfoot>		 
	   </table>";

