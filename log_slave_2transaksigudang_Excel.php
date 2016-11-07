<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

//	$pt=$_POST['pt'];
	$unit=$_GET['unit'];
	$periode=$_GET['periode'];
	$jenis=$_GET['jenis'];
	$kodebarang=$_GET['kodebarang'];

    $kamusjenis['0']='Mutasi dalam perjalanan';
    $kamusjenis['1']='Penerimaan';
    $kamusjenis['2']='Pengembalian pengeluaran';
    $kamusjenis['3']='Penerimaan mutasi';
    $kamusjenis['5']='Pengeluaran';
    $kamusjenis['6']='Pengembalian penerimaan';
    $kamusjenis['7']='Pengeluaran mutasi';
        
if($unit==''){
	echo "Warning: silakan mengisi gudang"; exit;
}
if($periode==''){
	echo "Warning: silakan mengisi periode"; exit;
}
if($jenis==''){
	echo "Warning: silakan mengisi tipe transaksi"; exit;
}

if($jenis=='9')$jenis='';
$tipetransaksi = "a.tipetransaksi like '%".$jenis."%'";

$str="select tanggalmulai, tanggalsampai from ".$dbname.".setup_periodeakuntansi
      where periode ='".$periode."' and kodeorg='".$unit."'";
    if($unit=='sumatera')
        $str="select tanggalmulai, tanggalsampai from ".$dbname.".setup_periodeakuntansi
              where periode ='".$periode."' and kodeorg in ('MRKE10','SKSE10','SOGM20','SSRO21','WKNE10')";
    if($unit=='kalimantan')
        $str="select tanggalmulai, tanggalsampai from ".$dbname.".setup_periodeakuntansi
              where periode ='".$periode."' and kodeorg in ('SBME10','SBNE10','SMLE10','SMTE10','SSGE10','STLE10')";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$tanggalmulai=$bar->tanggalmulai;
	$tanggalsampai=$bar->tanggalsampai;
}

/*
$str="select distinct kodebarang, namabarang from ".$dbname.".log_5masterbarang";
$res=mysql_query($str);
$optper="";
while($bar=mysql_fetch_object($res))
{
	$barang[$bar->kodebarang]=$bar->namabarang;
}	

*/	
//	if($kodebarang=='')
//	$str="select a.tanggal, a.kodebarang, b.namabarang, a.jumlah, a.satuan, a.hargasatuan, a.hargarata, a.nopo, c.namasupplier, a.kodeblok, a.kodemesin, a.notransaksi, d.gudangx from ".$dbname.".log_transaksi_vw a
//	      left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang  
//	      left join ".$dbname.".log_5supplier c on a.idsupplier=c.supplierid  
//	      left join ".$dbname.".log_transaksiht d on a.notransaksi=d.notransaksi  
//	      where a.tanggal>='".$tanggalmulai."' and a.tanggal<='".$tanggalsampai."' and a.tipetransaksi = '".$jenis."' and a.kodegudang = '".$unit."'
//		  order by a.tanggal";
//	else
//	$str="select a.tanggal, a.kodebarang, b.namabarang, a.jumlah, a.satuan, a.hargasatuan, a.hargarata, a.nopo, c.namasupplier, a.kodeblok, a.kodemesin, a.notransaksi, d.gudangx from ".$dbname.".log_transaksi_vw a
//	      left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang  
//	      left join ".$dbname.".log_5supplier c on a.idsupplier=c.supplierid  
//	      left join ".$dbname.".log_transaksiht d on a.notransaksi=d.notransaksi  
//	      where a.tanggal>='".$tanggalmulai."' and a.tanggal<='".$tanggalsampai."' and a.tipetransaksi = '".$jenis."' and a.kodegudang = '".$unit."' and a.kodebarang = '".$kodebarang."'
//		  order by a.tanggal";
if($kodebarang==''){
    if($unit=='sumatera'){
    $str="select a.tanggal, a.kodebarang, b.namabarang, a.jumlah, a.satuan, a.hargasatuan, a.hargarata, 
        a.nopo, c.namasupplier, a.kodeblok, a.kodemesin, a.notransaksi, d.gudangx, a.tipetransaksi, a.keterangan, a.notransaksireferensi 
        from ".$dbname.".log_transaksi_vw a
        left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang  
        left join ".$dbname.".log_5supplier c on a.idsupplier=c.supplierid  
        left join ".$dbname.".log_transaksiht d on a.notransaksi=d.notransaksi  
        where a.tanggal>='".$tanggalmulai."' and a.tanggal<='".$tanggalsampai."' 
        and ".$tipetransaksi." and a.kodegudang in ('MRKE10','SKSE10','SOGM20','SSRO21','WKNE10')
        order by a.tanggal";
    }
    else if($unit=='kalimantan'){
    $str="select a.tanggal, a.kodebarang, b.namabarang, a.jumlah, a.satuan, a.hargasatuan, a.hargarata, 
        a.nopo, c.namasupplier, a.kodeblok, a.kodemesin, a.notransaksi, d.gudangx, a.tipetransaksi, a.keterangan, a.notransaksireferensi 
        from ".$dbname.".log_transaksi_vw a
        left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang  
        left join ".$dbname.".log_5supplier c on a.idsupplier=c.supplierid  
        left join ".$dbname.".log_transaksiht d on a.notransaksi=d.notransaksi  
        where a.tanggal>='".$tanggalmulai."' and a.tanggal<='".$tanggalsampai."' 
        and ".$tipetransaksi." and a.kodegudang in ('SBME10','SBNE10','SMLE10','SMTE10','SSGE10','STLE10')
        order by a.tanggal";
    }
    else{
    $str="select a.tanggal, a.kodebarang, b.namabarang, a.jumlah, a.satuan, a.hargasatuan, a.hargarata, a.nopo, 
        c.namasupplier, a.kodeblok, a.kodemesin, a.notransaksi, d.gudangx, a.tipetransaksi, a.keterangan, a.notransaksireferensi 
        from ".$dbname.".log_transaksi_vw a
        left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang  
        left join ".$dbname.".log_5supplier c on a.idsupplier=c.supplierid  
        left join ".$dbname.".log_transaksiht d on a.notransaksi=d.notransaksi  
        where a.tanggal>='".$tanggalmulai."' and a.tanggal<='".$tanggalsampai."' 
        and ".$tipetransaksi." and a.kodegudang = '".$unit."'
        order by a.tanggal";
    }
}
else{
    if($unit=='sumatera'){
    $str="select a.tanggal, a.kodebarang, b.namabarang, a.jumlah, a.satuan, a.hargasatuan, a.hargarata, a.nopo, 
        c.namasupplier, a.kodeblok, a.kodemesin, a.notransaksi, d.gudangx, a.tipetransaksi, a.keterangan, a.notransaksireferensi 
        from ".$dbname.".log_transaksi_vw a
        left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang  
        left join ".$dbname.".log_5supplier c on a.idsupplier=c.supplierid  
        left join ".$dbname.".log_transaksiht d on a.notransaksi=d.notransaksi  
        where a.tanggal>='".$tanggalmulai."' and a.tanggal<='".$tanggalsampai."' and ".$tipetransaksi." 
        and a.kodegudang in ('MRKE10','SKSE10','SOGM20','SSRO21','WKNE10') and a.kodebarang = '".$kodebarang."'
        order by a.tanggal";
    $str22="select sum(saldoawalqty) as saldoawalqty, avg(hargaratasaldoawal) as hargaratasaldoawal, sum(nilaisaldoawal) as nilaisaldoawal from ".$dbname.".log_5saldobulanan where kodegudang in ('MRKE10','SKSE10','SOGM20','SSRO21','WKNE10')
        and kodebarang = '".$kodebarang."' and periode = '".$periode."'";
    }
    else if($unit=='kalimantan'){
    $str="select a.tanggal, a.kodebarang, b.namabarang, a.jumlah, a.satuan, a.hargasatuan, a.hargarata, a.nopo, 
        c.namasupplier, a.kodeblok, a.kodemesin, a.notransaksi, d.gudangx, a.tipetransaksi, a.keterangan, a.notransaksireferensi 
        from ".$dbname.".log_transaksi_vw a
        left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang  
        left join ".$dbname.".log_5supplier c on a.idsupplier=c.supplierid  
        left join ".$dbname.".log_transaksiht d on a.notransaksi=d.notransaksi  
        where a.tanggal>='".$tanggalmulai."' and a.tanggal<='".$tanggalsampai."' and ".$tipetransaksi."
        and a.kodegudang in ('SBME10','SBNE10','SMLE10','SMTE10','SSGE10','STLE10') and a.kodebarang = '".$kodebarang."'
        order by a.tanggal";
    $str22="select sum(saldoawalqty) as saldoawalqty, avg(hargaratasaldoawal) as hargaratasaldoawal, sum(nilaisaldoawal) as nilaisaldoawal from ".$dbname.".log_5saldobulanan where kodegudang in ('SBME10','SBNE10','SMLE10','SMTE10','SSGE10','STLE10')
        and kodebarang = '".$kodebarang."' and periode = '".$periode."'";
    }
    else{
    $str="select a.tanggal, a.kodebarang, b.namabarang, a.jumlah, a.satuan, a.hargasatuan, a.hargarata, a.nopo, 
        c.namasupplier, a.kodeblok, a.kodemesin, a.notransaksi, d.gudangx, a.tipetransaksi, a.keterangan, a.notransaksireferensi 
        from ".$dbname.".log_transaksi_vw a
        left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang  
        left join ".$dbname.".log_5supplier c on a.idsupplier=c.supplierid  
        left join ".$dbname.".log_transaksiht d on a.notransaksi=d.notransaksi  
        where a.tanggal>='".$tanggalmulai."' and a.tanggal<='".$tanggalsampai."' and ".$tipetransaksi."
        and a.kodegudang = '".$unit."' and a.kodebarang = '".$kodebarang."' 
        order by a.tanggal";
    $str22="select sum(saldoawalqty) as saldoawalqty, avg(hargaratasaldoawal) as hargaratasaldoawal, sum(nilaisaldoawal) as nilaisaldoawal from ".$dbname.".log_5saldobulanan where kodegudang = '".$unit."'
        and kodebarang = '".$kodebarang."' and periode = '".$periode."'";                
    }

$res22=mysql_query($str22);
if(mysql_num_rows($res22)>0)
while($bar22=mysql_fetch_object($res22))
{    
    $saldoawalqty=$bar22->saldoawalqty;
    $hargaratasaldoawal=$bar22->hargaratasaldoawal;
    $nilaisaldoawal=$bar22->nilaisaldoawal;
}
}
     
$str44="select kodebarang, namabarang, satuan from ".$dbname.".log_5masterbarang where kodebarang = '".$kodebarang."'";                
$res44=mysql_query($str44);
if(mysql_num_rows($res44)>0)
while($bar44=mysql_fetch_object($res44))
{    
    $namabarang=$bar44->namabarang;
    $satuan=$bar44->satuan;
}

        

//echo"str :".$str;
//=================================================
	$res=mysql_query($str);
	$no=0;
	if(mysql_num_rows($res)<1)
	{
		echo"<tr class=rowcontent><td colspan=14>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
	}
	else
	{
		$stream.=$_SESSION['lang']['transaksigudang'].": ".$jenis." : ".$unit." : ".$periode." (".tanggalnormal($tanggalmulai)." - ".tanggalnormal($tanggalsampai).")<br>
		<table border=1>
				    <tr>
			  <td bgcolor=#DEDEDE align=center><b>No.</td>";
			  $stream.="<td bgcolor=#DEDEDE align=center><b>".$_SESSION['lang']['tipetransaksi']."</b></td>";
			  $stream.="<td bgcolor=#DEDEDE align=center><b>".$_SESSION['lang']['tanggal']."</b></td>";
			  $stream.="<td bgcolor=#DEDEDE align=center><b>".$_SESSION['lang']['kodebarang']."</b></td>";
			  $stream.="<td bgcolor=#DEDEDE align=center><b>".$_SESSION['lang']['namabarang']."</b></td>";
        if($jenis==''){
			  $stream.="<td bgcolor=#DEDEDE align=center><b>".$_SESSION['lang']['masuk']."</b></td>";
			  $stream.="<td bgcolor=#DEDEDE align=center><b>".$_SESSION['lang']['keluar']."</b></td>";
			  $stream.="<td bgcolor=#DEDEDE align=center><b>".$_SESSION['lang']['saldo']."</b></td>";
        }else{
			  $stream.="<td bgcolor=#DEDEDE align=center><b>".$_SESSION['lang']['jumlah']."</b></td>";
        }
			  $stream.="<td bgcolor=#DEDEDE align=center><b>".$_SESSION['lang']['satuan']."</b></td>";
			  $stream.="<td bgcolor=#DEDEDE align=center><b>".$_SESSION['lang']['hargasatuan']."</b></td>";
			  $stream.="<td bgcolor=#DEDEDE align=center><b>".$_SESSION['lang']['totalharga']."</b></td>";
			  if(($jenis=='0')or($jenis=='1')or($jenis=='2')or($jenis=='3'))$stream.="<td bgcolor=#DEDEDE align=center><b>".$_SESSION['lang']['nopo']."</b></td>";
			  if(($jenis=='0')or($jenis=='1')or($jenis=='2')or($jenis=='3'))$stream.="<td bgcolor=#DEDEDE align=center><b>".$_SESSION['lang']['supplier']."</b></td>";
        if($jenis=='')$stream.="<td bgcolor=#DEDEDE align=center><b>".$_SESSION['lang']['sumber']."/".$_SESSION['lang']['tujuan']."</b></td>";
			  if($jenis=='7')$stream.="<td bgcolor=#DEDEDE align=center><b>".$_SESSION['lang']['tujuan']."</b></td>";
			  if(($jenis=='5')or($jenis=='6'))$stream.="<td bgcolor=#DEDEDE align=center><b>".$_SESSION['lang']['kodeblok']."</b></td>";
			  if(($jenis=='5')or($jenis=='6'))$stream.="<td bgcolor=#DEDEDE align=center><b>".$_SESSION['lang']['kodevhc']."</b></td>";
			  $stream.="<td bgcolor=#DEDEDE align=center><b>".$_SESSION['lang']['notransaksi']."</b></td>";
			  $stream.="<td bgcolor=#DEDEDE align=center><b>".$_SESSION['lang']['keterangan']."</b></td>";
			  $stream.="<td bgcolor=#DEDEDE align=center><b>".$_SESSION['lang']['noreferensi']."</b></td>";
					$stream.="</tr>";
        if($jenis==''){
        $no=1;
        $saldo=$saldoawalqty;
        $masuk=0;
        $keluar=0;
        $stream.="<tr class=rowcontent>
            <td align=right>".$no."</td>";
            $stream.="<td align=right><b>Saldo Awal</b></td>";
            $stream.="<td>".tanggalnormal($periode."-01")."</td>";
            $stream.="<td>".$kodebarang."</td>";
            $stream.="<td nowrap>".$namabarang."</td>";
            if($saldoawalqty>=0){
                $masuk=$saldoawalqty;
                $totmas+=$masuk;
            }
            else{
                $keluar=$saldoawalqty*(-1);
                $totkel+=$keluar;
            }
            $stream.="<td align=right>".number_format($masuk,2)."</td>";
            $stream.="<td align=right>".number_format($keluar,2)."</td>";
            $stream.="<td align=right>".number_format($saldoawalqty,2)."</td>";
            $stream.="<td>".$satuan."</td>";
            $stream.="<td align=right>".number_format($hargaratasaldoawal)."</td>";
            $stream.="<td align=right>".number_format($nilaisaldoawal)."</td>";
            $stream.="<td></td>";
            $stream.="<td></td>";
            $stream.="<td></td>";
            $stream.="<td></td>";
        $stream.="</tr>";
        }    
        while($bar=mysql_fetch_object($res))
	{
		$no+=1; $total=0;
			if(($jenis=='0')or($jenis=='1')or($jenis=='2')or($jenis=='3')) $total=$bar->jumlah*$bar->hargasatuan; else
			$total=$bar->jumlah*$bar->hargarata;
		$stream.="<tr>";
				  $stream.="<td align=right>".$no."</td>";
				  $stream.="<td>".$kamusjenis[$bar->tipetransaksi]."</td>";
				  $stream.="<td>".$bar->tanggal."</td>";
				  $stream.="<td>".$bar->kodebarang."</td>";
				  $stream.="<td nowrap>".$bar->namabarang."</td>";
                                  
            if($jenis==''){
                $masuk=0;
                $keluar=0;
                if($bar->tipetransaksi<4)$masuk=$bar->jumlah;
                if($bar->tipetransaksi>4)$keluar=$bar->jumlah;
                $totmas+=$masuk;
                $totkel+=$keluar;
                $stream.="<td align=right>".number_format($masuk,2)."</td>";
                $stream.="<td align=right>".number_format($keluar,2)."</td>";
                $saldo+=$masuk-$keluar;
                $stream.="<td align=right>".number_format($saldo,2)."</td>";
            }else{
				  $stream.="<td align=right>".number_format($bar->jumlah,2)."</td>";
            }
                                                                    
				  $stream.="<td>".$bar->satuan."</td>";
				  if(($jenis=='0')or($jenis=='1')or($jenis=='2')or($jenis=='3'))$stream.="<td align=right>".number_format($bar->hargasatuan)."</td>"; else
				  	$stream.="<td align=right>".number_format($bar->hargarata)."</td>";
				  $stream.="<td align=right>".number_format($total)."</td>";
				  if(($jenis=='0')or($jenis=='1')or($jenis=='2')or($jenis=='3'))$stream.="<td nowrap>".$bar->nopo."</td>";
				  if(($jenis=='0')or($jenis=='1')or($jenis=='2')or($jenis=='3'))$stream.="<td nowrap>".$bar->namasupplier."</td>";
				  if($jenis=='7')$stream.="<td>".$bar->gudangx."</td>";
				  if(($jenis=='5')or($jenis=='6'))$stream.="<td>".$bar->kodeblok."</td>";
				  if(($jenis=='5')or($jenis=='6'))$stream.="<td>".$bar->kodemesin."</td>";
            if($jenis==''){
                if($bar->tipetransaksi<4)$keluarmasuk=$bar->nopo." ".$bar->namasupplier;
                if($bar->tipetransaksi>4)$keluarmasuk=$bar->kodeblok." ".$bar->kodemesin." ".$bar->gudangx;
                $stream.="<td nowrap>".$keluarmasuk."</td>";
            }
				  $stream.="<td nowrap>".$bar->notransaksi."</td>";
				  $stream.="<td nowrap>".$bar->keterangan."</td>";
				  $stream.="<td nowrap>".$bar->notransaksireferensi."</td>";
			$stream.="</tr>"; 	
	}
        if($jenis==''){
        $stream.="<tr class=rowcontent>
            <td align=center colspan=5>Total</td>";
            $stream.="<td align=right>".number_format($totmas,2)."</td>";
            $stream.="<td align=right>".number_format($totkel,2)."</td>";
            $stream.="<td align=right>".number_format($saldo,2)."</td>";
            $stream.="<td colspan=7>".$satuan."</td>";
        $stream.="</tr>";
        }    

	$stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
  }
	
$nop_="TransaksiGudang_".$jenis."".$unit."_".$periode;
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