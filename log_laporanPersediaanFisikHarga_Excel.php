<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

	$pt=$_GET['pt'];
	$gudang=$_GET['gudang'];
	$periode=$_GET['periode'];
	$stream='';

$str="select tanggalmulai, tanggalsampai from ".$dbname.".setup_periodeakuntansi
    where periode ='".$periode."' and kodeorg='".$gudang."'";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $tanggalmulai=$bar->tanggalmulai;
    $tanggalsampai=$bar->tanggalsampai;
}
//=======================================	
 if(isset($_GET['unitDt']))//ini dari tab laporan stok per unit (tab 3)
{
                $str="select 
                      a.kodeorg,
                      a.kodebarang,
                      sum(a.saldoakhirqty) as salakqty,
                      sum(a.nilaisaldoakhir) as salakrp,
                      sum(a.qtymasuk) as masukqty,
                      sum(a.qtykeluar) as keluarqty,
                      sum(qtymasukxharga) as masukrp,
                      sum(qtykeluarxharga) as keluarrp,                      
                      sum(a.saldoawalqty) as sawalqty,
                      sum(a.nilaisaldoawal) as sawalrp,
                        b.namabarang,b.satuan    
                        from ".$dbname.".log_5saldobulanan a
                        left join ".$dbname.".log_5masterbarang b
                        on a.kodebarang=b.kodebarang
                      where kodegudang like '".$_GET['unitDt']."%' 
                      and periode='".$periode."'
                      group by a.kodebarang order by a.kodebarang";
            #masuk
            $smasuk="select sum(jumlah) as masukqty,sum(jumlah*hargarata) as masukrp,kodebarang from ".$dbname.".log_transaksi_vw
                         where tanggal between '".$tanggalmulai."' and '".$tanggalsampai."' and kodegudang like '".$_GET['unitDt']."%'
                         and tipetransaksi<5 and statussaldo in (1,2) group by kodebarang";
            $res=mysql_query($smasuk);
            while($bar=mysql_fetch_object($res)){
                $rmasukqty[$bar->kodebarang]=$bar->masukqty;
                $rmasukrp[$bar->kodebarang]=$bar->masukrp;
            }
            #keluar
            $skeluar="select sum(jumlah) as keluarqty,sum(jumlah*hargarata) as keluarrp,kodebarang from ".$dbname.".log_transaksi_vw
                         where tanggal between '".$tanggalmulai."' and '".$tanggalsampai."' and kodegudang like '".$_GET['unitDt']."%'
                         and tipetransaksi>4 and statussaldo in (1,2) group by kodebarang";
            $res=mysql_query($skeluar);
            while($bar=mysql_fetch_object($res)){
                $rkeluarqty[$bar->kodebarang]=$bar->keluarqty;
                $rkeluarrp[$bar->kodebarang]=$bar->keluarrp;
            }
}
    else if($gudang=='')
    {
            $str="select 
                      a.kodeorg,
                      a.kodebarang,
                      sum(a.saldoakhirqty) as salakqty,
                      sum(a.nilaisaldoakhir) as salakrp,
                      sum(a.qtymasuk) as masukqty,
                      sum(a.qtykeluar) as keluarqty,
                      sum(qtymasukxharga) as masukrp,
                      sum(qtykeluarxharga) as keluarrp,                      
                      sum(a.saldoawalqty) as sawalqty,
                      sum(a.nilaisaldoawal) as sawalrp,
                        b.namabarang,b.satuan    
                        from ".$dbname.".log_5saldobulanan a
                        left join ".$dbname.".log_5masterbarang b
                        on a.kodebarang=b.kodebarang
                      where kodeorg='".$pt."' 
                      and periode='".$periode."'
                      group by a.kodebarang order by a.kodebarang";
            #masuk
            $smasuk="select sum(jumlah) as masukqty,sum(jumlah*hargarata) as masukrp,kodebarang from ".$dbname.".log_transaksi_vw
                         where tanggal between '".$tanggalmulai."' and '".$tanggalsampai."' and kodept='".$pt."'
                         and tipetransaksi<5 and statussaldo in (1,2) group by kodebarang";
            $res=mysql_query($smasuk);
            while($bar=mysql_fetch_object($res)){
                $rmasukqty[$bar->kodebarang]=$bar->masukqty;
                $rmasukrp[$bar->kodebarang]=$bar->masukrp;
            }
            #keluar
            $skeluar="select sum(jumlah) as keluarqty,sum(jumlah*hargarata) as keluarrp,kodebarang from ".$dbname.".log_transaksi_vw
                         where tanggal between '".$tanggalmulai."' and '".$tanggalsampai."' and kodept='".$pt."'
                         and tipetransaksi>4 and statussaldo in (1,2) group by kodebarang";
            $res=mysql_query($skeluar);
            while($bar=mysql_fetch_object($res)){
                $rkeluarqty[$bar->kodebarang]=$bar->keluarqty;
                $rkeluarrp[$bar->kodebarang]=$bar->keluarrp;
            }
    }
    else
    {
            $str="select
                      a.kodeorg,
                      a.kodebarang,
                      a.saldoakhirqty as salakqty,
                      a.hargarata as harat,
                      a.nilaisaldoakhir as salakrp,
                      a.qtymasuk as masukqty,
                      a.qtykeluar as keluarqty,
                      a.qtymasukxharga as masukrp,
                      a.qtykeluarxharga as keluarrp,
                      a.saldoawalqty as sawalqty,
                      a.hargaratasaldoawal as sawalharat,
                      a.nilaisaldoawal as sawalrp,
                  b.namabarang,b.satuan 		 		      
                      from ".$dbname.".log_5saldobulanan a
                  left join ".$dbname.".log_5masterbarang b
                      on a.kodebarang=b.kodebarang
                      where kodeorg='".$pt."' 
                      and periode='".$periode."'
                      and kodegudang='".$gudang."'
                     order by a.kodebarang";
            #masuk
            $smasuk="select sum(jumlah) as masukqty,sum(jumlah*hargarata) as masukrp,kodebarang from ".$dbname.".log_transaksi_vw
                         where tanggal between '".$tanggalmulai."' and '".$tanggalsampai."' and kodegudang='".$gudang."'
                         and tipetransaksi<5 and statussaldo in (1,2) group by kodebarang";
            $res=mysql_query($smasuk);
            while($bar=mysql_fetch_object($res)){
                $rmasukqty[$bar->kodebarang]=$bar->masukqty;
                $rmasukrp[$bar->kodebarang]=$bar->masukrp;
            }
            #keluar
            $skeluar="select sum(jumlah) as keluarqty,sum(jumlah*hargarata) as keluarrp,kodebarang from ".$dbname.".log_transaksi_vw
                         where tanggal between '".$tanggalmulai."' and '".$tanggalsampai."' and kodegudang='".$gudang."'
                         and tipetransaksi>4 and statussaldo in (1,2) group by kodebarang";
            $res=mysql_query($skeluar);
            while($bar=mysql_fetch_object($res)){
                $rkeluarqty[$bar->kodebarang]=$bar->keluarqty;
                $rkeluarrp[$bar->kodebarang]=$bar->keluarrp;
            }
    }
        $stream.=$_SESSION['lang']['laporanstok'].": ".$pt."-".$gudang.":".$periode."<br>    
        <table border=1>
                <tr>
                  <td rowspan=2 align=center bgcolor=#DEDEDE >No.</td>
                  <td rowspan=2 align=center bgcolor=#DEDEDE >".$_SESSION['lang']['periode']."</td>
                  <td rowspan=2 align=center bgcolor=#DEDEDE >".$_SESSION['lang']['kodebarang']."</td>
                  <td rowspan=2 align=center bgcolor=#DEDEDE >".$_SESSION['lang']['namabarang']."</td>
                  <td rowspan=2 align=center bgcolor=#DEDEDE >".$_SESSION['lang']['satuan']."</td>
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
    $res=mysql_query($str);
    $no=0;        

        while($bar=mysql_fetch_object($res))
        {
                $no+=1;
                $kodebarang=$bar->kodebarang;
                $namabarang=$bar->namabarang; 


                    $kodebarang=$bar->kodebarang;
                    $namabarang=$bar->namabarang; 
                    $salakqty	=$bar->sawalqty+$rmasukqty[$kodebarang]-$rkeluarqty[$kodebarang];
                    $salakrp	=$bar->sawalrp+$rmasukrp[$kodebarang]-$rkeluarrp[$kodebarang];
                    $masukqty	=$bar->masukqty;
                    $keluarqty	=$bar->keluarqty;
                    $masukrp	=$bar->masukrp;
                    $keluarrp	=$bar->keluarrp;
                    $sawalQTY	=$bar->sawalqty;
                    $sawalrp	=$bar->sawalrp;
                    // Mengunci saldo akhir per Des 2014
                    if (substr($periode,0,4)==2014 and substr($periode,5)==12){
                        $salakqty=$bar->salakqty;
                        $salakrp=$bar->salakrp;
                    }

                    @$sawalharat=$bar->sawalrp/$bar->sawalqty;
                    @$haratmasuk=$rmasukrp[$kodebarang]/$rmasukqty[$kodebarang];
                    @$haratkeluar=$rkeluarrp[$kodebarang]/$rkeluarqty[$kodebarang];
                    @$harat	=$salakrp/$salakqty;

                $stream.="<tr>
                          <td>".$no."</td>
                          <td>".$periode."</td>
                          <td>'".$kodebarang."</td>
                          <td>".$namabarang."</td>
                          <td>".$bar->satuan."</td>
                           <td align=right class=firsttd>".number_format($sawalQTY,2,'.','')."</td>
                           <td align=right>".number_format($sawalharat,2,'.','')."</td>
                           <td align=right>".number_format($sawalrp,2,'.','')."</td>
                           <td align=right class=firsttd>".number_format($rmasukqty[$kodebarang],2,'.','')."</td>
                           <td align=right>".number_format($haratmasuk,2,'.','')."</td>
                           <td align=right>".number_format($rmasukrp[$kodebarang],2,'.','')."</td>
                           <td align=right class=firsttd>".number_format($rkeluarqty[$kodebarang],2,'.','')."</td>
                           <td align=right>".number_format($haratkeluar,2,'.','')."</td>
                           <td align=right>".number_format($rkeluarrp[$kodebarang],2,'.','')."</td>
                           <td align=right class=firsttd>".number_format($salakqty,2,'.','')."</td>
                           <td align=right>".number_format($harat,2,'.','')."</td>
                           <td align=right>".number_format($salakrp,2,'.','')."</td>			   
                        </tr>"; 		
        }
        $stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];			
	
$nop_="MaterialBalanceWPrice";
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