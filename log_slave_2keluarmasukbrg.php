<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/fpdf.php');
require_once('lib/nangkoelib.php');

    $pt=$_POST['pt'];
    $gudang=$_POST['kd_gudang'];
    $periode=substr($_POST['periode'],0,7);
    $kodebarang=$_POST['kodebarang'];
    $namabarang=$_POST['namabarang'];
    $satuan=$_POST['satuan'];	
    $_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
    $_POST['unitDt']==''?$unitDt=$_GET['unitDt']:$unitDt=$_POST['unitDt'];
//======================================
  $x=str_replace("-","",$periode);
  $x=str_replace("/","",$x);
  $x=mktime(0,0,0,(intval(substr($x,4,2))-1),15,substr($x,0,4));
  $prefper=date('Y-m',$x); 

switch($proses)
{
    case'getGudang':
    $optUnit="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
    $sUnit="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where 
            alokasi='".$unitDt."' and tipe like 'GUDANG%' order by namaorganisasi asc";
//    $sUnit="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where 
//            (induk IN (SELECT kodeorganisasi FROM ".$dbname.".organisasi WHERE induk='".$unitDt."') 
//            or induk IN (SELECT kodeorganisasi FROM ".$dbname.".organisasi WHERE induk IN 
//            (SELECT kodeorganisasi FROM ".$dbname.".organisasi WHERE induk='".$unitDt."')))
//            and tipe like 'GUDANG%' order by namaorganisasi asc";
    $qUnit=mysql_query($sUnit) or die(mysql_error($conn));
    while($rUnit=mysql_fetch_assoc($qUnit))
    {
        $optUnit.="<option value='".$rUnit['kodeorganisasi']."'>".$rUnit['namaorganisasi']."</option>";
    }
     echo $optUnit;
    break;
}
//ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt='COMPANY NAME';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namapt=strtoupper($bar->namaorganisasi);
}
//==========================get periode
$str="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi
      where kodeorg='".$gudang."' and periode='".$periode."'";
	$awal='';
	$akhir='';	  
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $awal=$bar->tanggalmulai;
    $akhir=$bar->tanggalsampai;
}	  
//ambil saldo awal===============================
    if($gudang=='')
    {
            $str="select  sum(saldoakhirqty) as sawal,
                            sum(nilaisaldoakhir) as sawalrp from 
                            ".$dbname.".log_5saldobulanan
                            where kodebarang='".$kodebarang."'
                            and periode='".$prefper."'";			
            //=========================================
            //ambil transaksi detail
            $strx="select a.*,b.idsupplier,b.tanggal,b.kodegudang,
                  b.tipetransaksi 
                  from ".$dbname.".log_transaksidt a
                  left join ".$dbname.".log_transaksiht b
                      on a.notransaksi=b.notransaksi
                      where kodebarang='".$kodebarang."'
                      and kodept='".$pt."'
                      and b.tanggal>='".$awal."'
                      and b.tanggal<='".$akhir."'
                      and b.post=1
                      order by tanggal,waktutransaksi";
    }
    else
    {
            $str="select  sum(saldoakhirqty) as sawal,
                            sum(nilaisaldoakhir) as sawalrp from 
                            ".$dbname.".log_5saldobulanan
                            where kodebarang='".$kodebarang."'
                            and periode='".$prefper."'
                            and kodegudang='".$gudang."'";		
            //=========================================
            //ambil transaksi detail
            $strx="select a.*,b.idsupplier,b.tanggal,b.kodegudang,
                  b.tipetransaksi
                      from ".$dbname.".log_transaksidt a
                  left join ".$dbname.".log_transaksiht b
                      on a.notransaksi=b.notransaksi
                      where kodebarang='".$kodebarang."'
                      and kodept='".$pt."'
                      and kodegudang='".$gudang."'
                      and b.tanggal>='".$awal."'
                      and b.tanggal<='".$akhir."'
                      and b.post=1
                      order by tanggal,waktutransaksi";
    }
$sawal=0;
$sawalrp=0;	
$hargasawal=0;
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $sawal=$bar->sawal;
    $sawalrp=$bar->sawalrp;
}
if($sawal>0){
$hargasawal=$sawalrp/$sawal;
}
    $resx=mysql_query($strx);
    $no=0;
    $saldo=$sawal;
    $masuk=0;
    $keluar=0;
    while($barx=mysql_fetch_object($resx))
    {
            $no+=1;
            if($barx->tipetransaksi<5)
             {
                    $saldo=$saldo+$barx->jumlah;
                    $masuk=$barx->jumlah;
                    $keluar=0;
             }
             else
             {
                    $saldo=$saldo-$barx->jumlah;
                    $keluar=$barx->jumlah;
                    $masuk=0;
             }
		
 echo"	<tr class=rowcontent>
            <td>".$no."</td>
            <td align=center>".tanggalnormal($barx->tanggal)."</td>
            <td align=center>".number_format($sawal,2,'.',',')."</td>
            <td align=center>".number_format($sawalrp/$sawal,2,'.',',')."</td>
            <td align=center>".number_format($sawalrp,2,'.',',')."</td>                
            <td align=center>".number_format($masuk,2,'.',',')."</td>
            <td align=center>".number_format($barx->hargasatuan,2,'.',',')."</td>
            <td align=center>".number_format($masuk*$barx->hargasatuan,2,'.',',')."</td>                
            <td align=center>".number_format($keluar,2,'.',',')."</td>
             <td align=center>".number_format($barx->hargarata,2,'.',',')."</td>
            <td align=center>".number_format($keluar*$barx->hargarata,2,'.',',')."</td>                    
            <td align=center>".number_format($saldo,2,'.',',')."</td>
            <td align=center>".number_format($barx->hargarata,2,'.',',')."</td>
            <td align=center>".number_format($saldo*$barx->hargarata,2,'.',',')."</td>                
            </tr>";
                 $sawal=$saldo;
        $sawalrp=$saldo*$barx->hargarata;  
        }
?>