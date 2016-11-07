<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

	$pt=$_GET['pt'];
	$gudang=$_GET['gudang'];
	$periode=substr($_GET['periode'],0,7);
	$kodebarang=$_GET['kodebarang'];
	$namabarang=$_GET['namabarang'];
	$satuan=$_GET['satuan'];	
//======================================
  $x=str_replace("-","",$periode);
  $x=str_replace("/","",$x);
  $x=mktime(0,0,0,(intval(substr($x,4,2))-1),15,substr($x,0,4));
  $prefper=date('Y-m',$x); 
  	
//ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt='COMPANY NAME';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namapt=strtoupper($bar->namaorganisasi);
}

if($gudang=='')
    exit(" Error: Untuk melihat rincian, harus memilih gudang, data gabungan tidak dapat dilihat rinci");
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
                      and kodegudang='".$gudang."'
                      and b.tanggal>='".$awal."'
                      and b.tanggal<='".$akhir."'
                      and b.post=1
                      order by tanggal,waktutransaksi";
//                      and kodept='".$pt."'

$sawal=0;
$sawalrp=0;	
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$sawal=$bar->sawal;
	$sawalrp=$bar->sawalrp;
}
	$hargasawal=$sawalrp/$sawal;

//=================================================
$stream.=$_SESSION['lang']['detailtransaksibarang']."<br> ".$_SESSION['lang']['pt'].":".$pt."<br>
    ".$_SESSION['lang']['namabarang'].":[".$kodebarang."]".$namabarang."(".$satuan.")<br>".$_SESSION['lang']['periode'].":".$periode."<br>      
<table border=1>
        <tr>
          <td rowspan=2 align=center bgcolor=#DEDEDE >No.</td>
          <td rowspan=2 align=center bgcolor=#DEDEDE >".$_SESSION['lang']['sloc']."</td>
          <td rowspan=2 align=center bgcolor=#DEDEDE >".$_SESSION['lang']['tanggal']."</td>
          <td rowspan=2 align=center bgcolor=#DEDEDE >".$_SESSION['lang']['tipe']."</td>
          <td colspan=3 align=center bgcolor=#DEDEDE >".$_SESSION['lang']['saldoawal']."</td>
          <td colspan=3 align=center bgcolor=#DEDEDE >".$_SESSION['lang']['masuk']."</td>
          <td colspan=3 align=center bgcolor=#DEDEDE >".$_SESSION['lang']['keluar']."</td>
          <td colspan=3 align=center bgcolor=#DEDEDE >".$_SESSION['lang']['saldo']."</td>
        </tr>
        <tr>
           <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['kuantitas']."</td>
           <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['hargasatuan']."</td>
           <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['totalharga']."</td>	   
           <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['kuantitas']."</td>
           <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['hargasatuan']."</td>
           <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['totalharga']."</td>	   
           <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['kuantitas']."</td>
           <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['hargasatuan']."</td>
           <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['totalharga']."</td>	   
           <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['kuantitas']."</td>
           <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['hargasatuan']."</td>
           <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['totalharga']."</td>	   
        </tr>";   

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
                    $hargasatuanm=$barx->hargasatuan;
                    //$hargasatuank=0;
             }
             else
             {
                    $saldo=$saldo-$barx->jumlah;
                    $keluar=$barx->jumlah;
                    $hargasatuank=$barx->hargarata;
                    //$hargasatuanm=0;
                    $masuk=0;
             }

        $stream.="<tr>
                    <td>".$no."</td>
                    <td>".$barx->kodegudang."</td>
                    <td>".tanggalnormal($barx->tanggal)."</td>
                    <td>".$barx->tipetransaksi."</td>
                    <td align=right class=firsttd>".number_format($sawal,2,'.','')."</td>
                    <td align=right>".number_format($hargasawal,2,'.','')."</td>
                    <td align=right>".number_format($sawalrp,2,'.','')."</td>
                    <td align=right class=firsttd>".number_format($masuk,2,'.','')."</td>
                    <td align=right>".number_format($hargasatuanm,2,'.','')."</td>
                    <td align=right>".number_format($masuk*$hargasatuanm,2,'.','')."</td>
                    <td align=right class=firsttd>".number_format($keluar,2,'.','')."</td>
                    <td align=right>".number_format($hargasatuank,2,'.','')."</td>
                    <td align=right>".number_format($keluar*$hargasatuank,2,'.','')."</td>
                    <td align=right class=firsttd>".number_format($saldo,2,'.','')."</td>
                    <td align=right>".number_format($hargasatuank,2,'.','')."</td>
                    <td align=right>".number_format($saldo*$hargasatuank,2,'.','')."</td>			   
                </tr>"; 	
        $sawal=$saldo;
    }
$stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name']; 


$nop_="DetailMaterialBalanceWPrice";
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
?>