<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

$param=$_GET;
foreach($param as $rowPrd=>$lstVar){
	if(substr($rowPrd,0,7)=='periode'){
		$periode=$lstVar;
	}
	if(substr($rowPrd,0,5)=='kdorg'){
		$kdorgDt=$lstVar;
	}
	if(substr($rowPrd,0,6)=='noakun'){
		$noakunDt=$lstVar;
	}
}
if(strlen($kdorgDt)==3){
	$whrd=" and left(kodegudang,4) in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$kdorgDt."')";
	$whrd2=" kodeorg in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$kdorgDt."')";
}else{
	$whrd=" and left(kodegudang,4)='".$kdorgDt."'";
	$whrd2=" kodeorg='".$kdorgDt."'";
}
#ambil tanggal mulai dan tanggal sampai di setup_periodeakutansi
$prd=substr($periode,0,4)."-01";
if(substr($periode,0,4)=='2014'){
	$prd=substr($periode,0,4)."-03";
}
$sTgl1="select distinct tanggalmulai from ".$dbname.".setup_periodeakuntansi
       where ".$whrd2." and periode='".$prd."'";
$qTgl1=mysql_query($sTgl1) or die(mysql_error($conn));
$rTgl1=mysql_fetch_assoc($qTgl1);

$sTgl="select distinct tanggalsampai from ".$dbname.".setup_periodeakuntansi
       where ".$whrd2." and periode='".$periode."'";
$qTgl=mysql_query($sTgl) or die(mysql_error($conn));
$rTgl=mysql_fetch_assoc($qTgl);
$tglwrt=" and tanggal>='".$rTgl1['tanggalmulai']."' and  tanggal<='".$rTgl['tanggalsampai']."'";
$sTrnsaksi="select notransaksi,kodebarang,jumlah,hargasatuan,hargarata,kodegudang,tipetransaksi,idsupplier,gudangx, 
            notransaksireferensi from ".$dbname.".log_transaksi_vw 
            where left(kodebarang,3) in (select kode from ".$dbname.".log_5klbarang where noakun='".$noakunDt."')
			 ".$tglwrt." ".$whrd."
			order by tanggal,tipetransaksi,kodegudang asc";
$qTransaksi=mysql_query($sTrnsaksi) or die(mysql_error($conn));
while($rTransaksi=mysql_fetch_assoc($qTransaksi)){
	if($rTransaksi['tipetransaksi']>4){
		if($rTransaksi['tipetransaksi']==7){
			if(substr($rTransaksi['kodegudang'],0,4)==substr($rTransaksi['gudangx'],0,4)){
					continue;
				}	
		}
		$dtHrgKredit[$rTransaksi['notransaksi'].$rTransaksi['kodebarang']]=$rTransaksi['hargarata'];
	}else{
		if($rTransaksi['tipetransaksi']==2){
			$rTransaksi['hargasatuan']=$rTransaksi['hargarata'];
		}
		if($rTransaksi['tipetransaksi']==3){
			if(substr($rTransaksi['kodegudang'],0,4)==substr($rTransaksi['gudangx'],0,4)){
					continue;
				}	
		}
		$dtHrgDebet[$rTransaksi['notransaksi'].$rTransaksi['kodebarang']]=$rTransaksi['hargasatuan'];
	}
	$dtNtrns[$rTransaksi['notransaksi']]=$rTransaksi['notransaksi'];
	$dtBrg[$rTransaksi['kodebarang']]=$rTransaksi['kodebarang'];
	$dtNotrans[$rTransaksi['notransaksi'].$rTransaksi['kodebarang']]=$rTransaksi['notransaksi'];
	$dtNotransRef[$rTransaksi['notransaksi'].$rTransaksi['kodebarang']]=$rTransaksi['notransaksireferensi'];
	$dtTpTrns[$rTransaksi['notransaksi'].$rTransaksi['kodebarang']]=$rTransaksi['tipetransaksi'];
	$dtJumlah[$rTransaksi['notransaksi'].$rTransaksi['kodebarang']]=$rTransaksi['jumlah'];
	$dtGdng[$rTransaksi['notransaksi'].$rTransaksi['kodebarang']]=$rTransaksi['kodegudang'];
	$dtGdngx[$rTransaksi['notransaksi'].$rTransaksi['kodebarang']]=$rTransaksi['gudangx'];
	$dtSplier[$rTransaksi['notransaksi'].$rTransaksi['kodebarang']]=$rTransaksi['idsupplier'];
}
$sJurnal="select noreferensi,nojurnal,kodebarang,debet,kredit from ".$dbname.".keu_jurnaldt_vw 
          where ".$whrd2." and noakun='".$noakunDt."' 
		  ".$tglwrt." and kodebarang<>''";
//echo $sJurnal;
$qJurnal=mysql_query($sJurnal) or die(mysql_error($conn));
while($rJurnal=mysql_fetch_assoc($qJurnal)){
		$sCek="select distinct notransaksi from ".$dbname.".log_transaksi_vw 
		       where notransaksireferensi='".$rJurnal['noreferensi']."' and (notransaksireferensi like '%TM%' or notransaksireferensi like '%BBT%' or notransaksireferensi like '%TBM%')";
		$qCek=mysql_query($sCek) or die(mysql_error($conn));
		$rCek=mysql_fetch_assoc($qCek);
		$rRow=mysql_num_rows($qCek);
		if($rRow!=0){
			$refId=$rJurnal['noreferensi'];
			$rJurnal['noreferensi']=$rCek['notransaksi'];
		} 
		$dtNotransRef[$rJurnal['noreferensi'].$rJurnal['kodebarang']]=$refId;
		$dtNtrns[$rJurnal['noreferensi']]=$rJurnal['noreferensi'];
		$dtNotrans[$rJurnal['noreferensi'].$rJurnal['kodebarang']]=$rJurnal['noreferensi'];
		$dtBrg[$rJurnal['kodebarang']]=$rJurnal['kodebarang'];
		$dtNojurnal[$rJurnal['noreferensi'].$rJurnal['kodebarang']]=$rJurnal['nojurnal'];
		if($rJurnal['debet']!=0){
			$dtRupDebet[$rJurnal['noreferensi'].$rJurnal['kodebarang']]=$rJurnal['debet'];
		}else{
			$dtRupKredit[$rJurnal['noreferensi'].$rJurnal['kodebarang']]=$rJurnal['kredit'];
		}
		
}
switch($_GET['proses']){
        case'excel':
		$tab.="<table cellpadding=1 cellspacing=1 border=1 class=sortable>";
		$tab.="<thead>";
		$tab.="<tr>";
		$tab.="<td bgcolor=#DEDEDE align=center rowspan=2>No.</td>";
		$tab.="<td bgcolor=#DEDEDE align=center rowspan=2>".$_SESSION['lang']['notransaksi']."</td>";
		$tab.="<td bgcolor=#DEDEDE align=center rowspan=2>".$_SESSION['lang']['nojurnal']."</td>";
		$tab.="<td bgcolor=#DEDEDE align=center rowspan=2>".$_SESSION['lang']['sloc']."</td>";
		$tab.="<td bgcolor=#DEDEDE align=center rowspan=2>".$_SESSION['lang']['tipetransaksi']."</td>";
		$tab.="<td bgcolor=#DEDEDE align=center rowspan=2>".$_SESSION['lang']['sloc']." Tujuan/Asal</td>";
		$tab.="<td bgcolor=#DEDEDE align=center rowspan=2>".$_SESSION['lang']['noreferensi']."</td>";
		$tab.="<td bgcolor=#DEDEDE align=center rowspan=2>".$_SESSION['lang']['namasupplier']."</td>";
		$tab.="<td bgcolor=#DEDEDE align=center rowspan=2>".$_SESSION['lang']['kodebarang']."</td>";
		$tab.="<td bgcolor=#DEDEDE align=center rowspan=2>".$_SESSION['lang']['namabarang']."</td>";
		$tab.="<td bgcolor=#DEDEDE align=center rowspan=2>".$_SESSION['lang']['jumlah']."</td>";
		$tab.="<td bgcolor=#DEDEDE align=center rowspan=2>".$_SESSION['lang']['harga']."</td>";
		$tab.="<td bgcolor=#DEDEDE align=center colspan=2>".$_SESSION['lang']['rp']." Transaksi</td>";
		$tab.="<td bgcolor=#DEDEDE align=center colspan=2>".$_SESSION['lang']['rp']." Jurnal</td>";
		$tab.="</tr>";
		$tab.="<tr>";
		$tab.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['debet']."</td>";
		$tab.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kredit']."</td>";
		$tab.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['debet']."</td>";
		$tab.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kredit']."</td>";
		$tab.="</tr>";
		$tab.="</thead><tbody>";
		foreach($dtNtrns as $lstNtrans){
			foreach($dtBrg as $lstBrg){
				if($dtNotrans[$lstNtrans.$lstBrg]!=''){
					$no+=1;
					$tab.="<tr class=rowcontent>";
					$tab.="<td>".$no."</td>";
					$tab.="<td>".$lstNtrans."</td>";
					$tab.="<td>".$dtNojurnal[$lstNtrans.$lstBrg]."</td>";
					$tab.="<td>".$dtGdng[$lstNtrans.$lstBrg]."</td>";
					$tab.="<td>".$dtTpTrns[$lstNtrans.$lstBrg]."</td>";
					$tab.="<td>".$dtGdngx[$lstNtrans.$lstBrg]."</td>";
					$tab.="<td>".$dtNotransRef[$lstNtrans.$lstBrg]."</td>";
					$whr="supplierid='".$dtSplier[$lstNtrans.$lstBrg]."'";
					$nmOrg=makeOption($dbname,'log_5supplier','supplierid,namasupplier',$whr);
					$tab.="<td>".$nmOrg[$dtSplier[$lstNtrans.$lstBrg]]."</td>";
					$whr2="kodebarang='".$lstBrg."'";
					$nmBrg=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang',$whr2);
					$tab.="<td>'".$lstBrg."</td>";
					$tab.="<td>'".$nmBrg[$lstBrg]."</td>";
					$tab.="<td  align=right>".$dtJumlah[$lstNtrans.$lstBrg]."</td>";
					$hrgTrDbt[$lstNtrans.$lstBrg]=$dtJumlah[$lstNtrans.$lstBrg]*$dtHrgDebet[$lstNtrans.$lstBrg];
					$hrgTrKrdt[$lstNtrans.$lstBrg]=$dtJumlah[$lstNtrans.$lstBrg]*$dtHrgKredit[$lstNtrans.$lstBrg];
					if($dtTpTrns[$lstNtrans.$lstBrg]<5){
						$tab.="<td  align=right>".$dtHrgDebet[$lstNtrans.$lstBrg]."</td>";
					}else{
						$tab.="<td  align=right>".$dtHrgKredit[$lstNtrans.$lstBrg]."</td>";
					}
					$tab.="<td align=right>".$hrgTrDbt[$lstNtrans.$lstBrg]."</td>";
					$tab.="<td align=right>".$hrgTrKrdt[$lstNtrans.$lstBrg]."</td>";
					$tab.="<td align=right>".$dtRupDebet[$lstNtrans.$lstBrg]."</td>";
					$tab.="<td align=right>".$dtRupKredit[$lstNtrans.$lstBrg]."</td>";
					$totDbtJrn+=$dtRupDebet[$lstNtrans.$lstBrg];
					$totKrdtJrn+=$dtRupKredit[$lstNtrans.$lstBrg];
					$totDbtTrs+=$hrgTrDbt[$lstNtrans.$lstBrg];
					$totKrdtTrs+=$hrgTrKrdt[$lstNtrans.$lstBrg];
					
					$tab.="</tr>";
				}
			}
		}
		$tab.="<tr class=rowcontent>";
		$tab.="<td  align=right colspan=12>".$_SESSION['lang']['grnd_total']."</td>";
		$tab.="<td align=right>".$totDbtTrs."</td>";
		$tab.="<td align=right>".$totKrdtTrs."</td>";
		$tab.="<td align=right>".$totDbtJrn."</td>";
		$tab.="<td align=right>".$totKrdtJrn."</td>";
		$tab.="</tbody></table>";
		
		//exit("error:".$tab);
			$thisDate=date("YmdHms");
			//$nop_="Laporan_Pembelian";
			$nop_="detail_".$noakunDt."__".$periode."__".$kdorgDt."__".$thisDate;
			 $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
			 gzwrite($gztralala, $tab);
			 gzclose($gztralala);
			 echo "<script language=javascript1.2>
				window.location='tempExcel/".$nop_.".xls.gz';
				</script>";
        break;
}
?>