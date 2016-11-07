<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php'); 

$proses=$_GET['proses'];
$_POST['kodeorg1']==''?$kodeorg1=$_GET['kodeorg1']:$kodeorg1=$_POST['kodeorg1'];
$_POST['kodebarang1']==''?$kodebarang1=$_GET['kodebarang1']:$kodebarang1=$_POST['kodebarang1'];
$_POST['tgl1_1']==''?$tgl1_1=$_GET['tgl1_1']:$tgl1_1=$_POST['tgl1_1'];
$_POST['tgl2_1']==''?$tgl2_1=$_GET['tgl2_1']:$tgl2_1=$_POST['tgl2_1'];
$tgl=explode('-',$tgl1_1);
$tgl1_1=$tgl[2].'-'.$tgl[1].'-'.$tgl[0];
$tgl=explode('-',$tgl2_1);
$tgl2_1=$tgl[2].'-'.$tgl[1].'-'.$tgl[0];

if($kodeorg1==''){
    echo"error: Please choose Mill.";
    exit;
}
if($kodebarang1==''){
    echo"error: Please choose commodity.";
    exit;
}
if($tgl1_1=='--'||$tgl2_1=='--'){
    echo"error: Please choose dates.";
    exit;
}

$str="select nokontrak, koderekanan from ".$dbname.".pmn_kontrakjual";   
$res=mysql_query($str);
while($bar= mysql_fetch_object($res))
{
    $kontrak[$bar->nokontrak]=$bar->koderekanan;
}
$str="select kodecustomer,namacustomer from ".$dbname.".pmn_4customer";   
$res=mysql_query($str);
while($bar= mysql_fetch_object($res))
{
    $kustom[$bar->kodecustomer]=$bar->namacustomer;
}
$sBrg="select namabarang,kodebarang from ".$dbname.".log_5masterbarang where kodebarang in ('40000001', '40000002')";
$qBrg=mysql_query($sBrg) or die(mysql_error());
while($rBrg=mysql_fetch_assoc($qBrg))
{
	$barang[$rBrg['kodebarang']]=$rBrg['namabarang'];
}

$stream='';
$border=0;
if($proses=='excel'){
    $border=1;
    $stream.=$_SESSION['lang']['pabrik']." : ".$kodeorg1."<br>";
    $stream.=$_SESSION['lang']['komoditi']." : ".$barang[$kodebarang1]."<br>";
    $stream.=$_SESSION['lang']['tanggal']." : ".$tgl1_1." - ".$tgl2_1."<br>";
}
$stream.=" <table class=sortable cellspacing=1 border=".$border.">
    <thead>
        <tr class=rowheader>
            <td>No.</td>
            <td>".$_SESSION['lang']['tanggal']."</td>
            <td>".$_SESSION['lang']['noTiket']."</td>
            <td>".$_SESSION['lang']['NoKontrak']."</td>
            <td>".$_SESSION['lang']['Pembeli']."</td>
			
			<td>".$_SESSION['lang']['hargasatuan']."</td>
			
            <td>".$_SESSION['lang']['jumlah']." (kg)</td>
			
			<td>".$_SESSION['lang']['total']."</td>
			
            <td>".$_SESSION['lang']['kendaraan']."</td>
            <td>".$_SESSION['lang']['supir']."</td>
	</tr>
    </thead><tbody>";
$no=1;
$total=0;

$hargaSatuan=makeOption($dbname,'pmn_kontrakjual','nokontrak,hargasatuan');

$sql="select tanggal,notransaksi,nokontrak,beratbersih,nokendaraan,supir from ".$dbname.".pabrik_timbangan where millcode = '".$kodeorg1."' and kodebarang = '".$kodebarang1."' and tanggal between '".$tgl1_1." 00:00:00' and '".$tgl2_1." 23:59:59' order by tanggal asc";
$query=mysql_query($sql) or die(mysql_error());
$row=mysql_num_rows($query);
/*if($row>0){*/
    while($res=mysql_fetch_assoc($query)){
        $stream.="<tr class=rowcontent>";
            $stream.="<td align=right>".$no."</td>";
            if($proses=='preview')$stream.="<td>".tanggalnormal(substr($res['tanggal'],0,10))."</td>";
            if($proses=='excel')$stream.="<td>".substr($res['tanggal'],0,10)."</td>";
            $stream.="<td>".$res['notransaksi']."</td>";
            $stream.="<td>".$res['nokontrak']."</td>";
            $stream.="<td>".$kustom[$kontrak[$res['nokontrak']]]."</td>";
			
			$stream.="<td>".$hargaSatuan[$res['nokontrak']]."</td>";
			
            $stream.="<td align=right>".number_format($res['beratbersih'],0)."</td>";
			
			$stream.="<td>".number_format($hargaSatuan[$res['nokontrak']]*$res['beratbersih'])."</td>";
			
            $stream.="<td>".$res['nokendaraan']."</td>";
            $stream.="<td>".$res['supir']."</td>";
        $stream.="</tr>";
        $no+=1;
        $total+=$res['beratbersih'];
		$totalx+=$hargaSatuan[$res['nokontrak']]*$res['beratbersih'];
    }
    $stream.="<tr class=rowcontent>";
        $stream.="<td align=center colspan=6>Total</td>";
        $stream.="<td align=right>".number_format($total,0)."</td>";
		 $stream.="<td align=right>".number_format($totalx,0)."</td>";
        $stream.="<td align=center colspan=2></td>";
    $stream.="</tr>";
    $no+=1;
/*}
else
{
    $stream.="<tr class=rowcontent align=center><td colspan=10>Not Found</td></tr>";
}*/
$stream.="</tbody></table>";

switch($proses)
{
    case'preview':
        echo $stream;
    break;
    case'excel':

        $stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
			
        $nop_="Penjualan Harian ".$kodeorg1." ".$kodebarang1." ".$tgl1_1."-".$tgl2_1;
        if(strlen($stream)>0)
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
            if(!fwrite($handle,$stream))
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
    break;
    default:
    break;
}

?>