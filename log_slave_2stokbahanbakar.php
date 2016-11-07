<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');
 
$param=$_POST;
if($_GET['ptId']!=''){
$param=$_GET;
}
//$arr="##ptId##kdBrg##tglDr##tanggalSampai";
$ingPt="select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."'";
$whrBrg2="left(kodegudang,4) in (".$ingPt.")  and (left(kodebarang,3)='010' or left(kodebarang,3)='012')";
if(($_GET['proses']=='preview')||($_GET['proses']=='excel')){
		if($param['ptId']==''){
			exit("warning: ".$_SESSION['lang']['pt']." tidak boleh kosong");
		}
		if($param['periode']==''){
			exit("warning: ".$_SESSION['lang']['periode']." tidak boleh kosong");
		}
		$whrBrg="left(kodegudang,4) in (".$ingPt.")";
		if($param['kdGudang']!=''){
			$whrBrg="kodegudang='".$param['kdGudang']."'";
		}
		$whrBrg.=" and (left(kodebarang,3)='010' or left(kodebarang,3)='012')";
		if($param['kdBrg']!=''){
			$whrBrg.=" and kodebarang='".$param['kdBrg']."'";
		}
		
		#pembentukan array bersumber dari table log_transaksi_vw
		$sLstGdng="select distinct kodegudang,kodebarang,sum(jumlah) as jumlah,tipetransaksi,tanggal from ".$dbname.".log_transaksi_vw where ".$whrBrg." and left(tanggal,7)='".$param['periode']."' group by tanggal,kodegudang,kodebarang,tipetransaksi order by left(kodegudang,4) asc";
		$qLstGdng=mysql_query($sLstGdng) or die(mysql_error($conn));
		while($rLstGdng=mysql_fetch_assoc($qLstGdng)){
			if($rLstGdng['tipetransaksi']>4){
				//barang keluar
				$lstOut[$rLstGdng['tanggal'].$rLstGdng['kodegudang'].$rLstGdng['kodebarang']]+=$rLstGdng['jumlah'];
			}elseif($rLstGdng['tipetransaksi']<5){
				//barang masuk
				$lstIn[$rLstGdng['tanggal'].$rLstGdng['kodegudang'].$rLstGdng['kodebarang']]+=$rLstGdng['jumlah'];
			}
			if(($rLstGdng['kodegudang']!='')&&($rLstGdng['kodebarang']!='')){
				$lstBrg[$rLstGdng['kodegudang']][$rLstGdng['kodebarang']]=$rLstGdng['kodebarang'];
				$dtGdng[$rLstGdng['kodegudang']]=$rLstGdng['kodegudang'];
			}
			$dtKdBrg[$rLstGdng['kodebarang']]=$rLstGdng['kodebarang'];
			$dtTgl[$rLstGdng['tanggal']]=$rLstGdng['tanggal'];
		}
		#pembentukan array untuk saldo awal
		$sAwal="select saldoawalqty,kodebarang,kodegudang from ".$dbname.".log_5saldobulanan where ".$whrBrg." 
		        and periode='".$param[	'periode']."' and saldoawalqty!=0  order by kodegudang asc";
		$qAwal=mysql_query($sAwal) or die(mysql_error($conn));
		while($rAwal=mysql_fetch_assoc($qAwal)){
			if(($rAwal['kodegudang']!='')&&($rAwal['kodebarang']!='')){
				$dtAwal[$rAwal['kodegudang'].$rAwal['kodebarang']]=$rAwal['saldoawalqty'];
			}
			
		}
		
		if(count($dtTgl)==0){
			exit("error: data kosong");
		}
		array_multisort($dtTgl,SORT_ASC);
		$brd=0;
		$bgwarna="class=rowheader  align=center";
		if($_GET['proses']=='excel'){
			$brd=1;
			$bgwarna="bgcolor=#DEDEDE align=center";
		}
		$tab.="<table cellpading=1 cellspacing=1 border=".$brd." class=sortable>";
		$tab.="<thead>";
		$tab.="<tr>";
		$tab.="<td rowspan=3 ".$bgwarna.">".$_SESSION['lang']['tanggal']."</td>";
		foreach($dtGdng as $lstGdang){
			if($lstGdang!=''){
				$whrtgdng="kodeorganisasi='".$lstGdang."'";
				$optNmOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whrtgdng);
				$tab.="<td colspan=".(count($lstBrg[$lstGdang])*3)."  ".$bgwarna.">".$lstGdang."-".$optNmOrg[$lstGdang]."</td>";
			}		
		}
		$tab.="</tr>";
		$tab.="<tr>";
		foreach($dtGdng as $lstGdang){
			if($lstGdang!=''){
				foreach($dtKdBrg as $dafBrg){				
					if($lstBrg[$lstGdang][$dafBrg]!=''){
						$whrtbrg="kodebarang='".$lstBrg[$lstGdang][$dafBrg]."'";
						$optNmBrg=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang',$whrtbrg);
						$tab.="<td colspan=3  ".$bgwarna.">".$optNmBrg[$lstBrg[$lstGdang][$dafBrg]]."</td>";
					}
				}
			}
		}
		$tab.="</tr>";
		$tab.="<tr>";
		foreach($dtGdng as $lstGdang){
			foreach($dtKdBrg as $dafBrg){
				if($lstBrg[$lstGdang][$dafBrg]!=''){
					$tab.="<td  ".$bgwarna.">IN</td>";
					$tab.="<td  ".$bgwarna.">OUT</td>";
					$tab.="<td  ".$bgwarna.">SALDO</td>";
				}			
			}
		}
		$tab.="</tr>";
		$tab.="</thead><tbody>";
		$tab.="<tr>";
		$tab.="<td>".$_SESSION['lang']['saldoawal']."</td>";
		foreach($dtGdng as $lstGdang){
			foreach($dtKdBrg as $dafBrg){
				if($lstBrg[$lstGdang][$dafBrg]!=''){
					$tab.="<td colspan=2>&nbsp;</td>";
					$tab.="<td align=right>".number_format($dtAwal[$lstGdang.$lstBrg[$lstGdang][$dafBrg]],2)."</td>";
					
				}
			}
		}
		$tab.="</tr>";
		$addd=1;
		foreach($dtTgl as $lstTanggal){
			$tab.="<tr class=rowcontent>";
			$tab.="<td>".$lstTanggal."</td>";
			foreach($dtGdng as $lstGdang){
			foreach($dtKdBrg as $dafBrg){
				if($lstBrg[$lstGdang][$dafBrg]!=''){
					$tab.="<td align=right>".number_format($lstIn[$lstTanggal.$lstGdang.$lstBrg[$lstGdang][$dafBrg]],2)."</td>";
					$tab.="<td align=right>".number_format($lstOut[$lstTanggal.$lstGdang.$lstBrg[$lstGdang][$dafBrg]],2)."</td>";
					
					if($addd==1){
						$saldo[$lstTanggal.$lstGdang.$lstBrg[$lstGdang][$dafBrg]]=($lstIn[$lstTanggal.$lstGdang.$lstBrg[$lstGdang][$dafBrg]]+$dtAwal[$lstGdang.$lstBrg[$lstGdang][$dafBrg]])-$lstOut[$lstTanggal.$lstGdang.$lstBrg[$lstGdang][$dafBrg]];
						
					}else{
						$tglKmrn=nambahHari($lstTanggal,1,0);
						$saldo[$lstTanggal.$lstGdang.$lstBrg[$lstGdang][$dafBrg]]=($lstIn[$lstTanggal.$lstGdang.$lstBrg[$lstGdang][$dafBrg]]+$saldo[$tglKmrn.$lstGdang.$lstBrg[$lstGdang][$dafBrg]])-$lstOut[$lstTanggal.$lstGdang.$lstBrg[$lstGdang][$dafBrg]];
					}
					$tab.="<td align=right>".number_format($saldo[$lstTanggal.$lstGdang.$lstBrg[$lstGdang][$dafBrg]],2)."</td>";
					$totIn[$lstGdang.$lstBrg[$lstGdang][$dafBrg]]+=$lstIn[$lstTanggal.$lstGdang.$lstBrg[$lstGdang][$dafBrg]];
					$totOut[$lstGdang.$lstBrg[$lstGdang][$dafBrg]]+=$lstOut[$lstTanggal.$lstGdang.$lstBrg[$lstGdang][$dafBrg]];
					$salAkhir[$lstGdang.$lstBrg[$lstGdang][$dafBrg]]=$saldo[$lstTanggal.$lstGdang.$lstBrg[$lstGdang][$dafBrg]];
				}
			}
		}
			$tab.="</tr>";
			$addd=0;
		}
		$tab.="<tr>";
		$tab.="<td>".$_SESSION['lang']['grnd_total']."</td>";
			foreach($dtGdng as $lstGdang){
				foreach($dtKdBrg as $dafBrg){
					if($lstBrg[$lstGdang][$dafBrg]!=''){
						$tab.="<td align=right>".number_format($totIn[$lstGdang.$lstBrg[$lstGdang][$dafBrg]],2)."</td>";
						$tab.="<td align=right>".number_format($totOut[$lstGdang.$lstBrg[$lstGdang][$dafBrg]],2)."</td>";
						$tab.="<td align=right>".number_format($salAkhir[$lstGdang.$lstBrg[$lstGdang][$dafBrg]],2)."</td>";
					}
				}
			}
		$tab.="</tr>";
		$tab.="</tbody></table>";
}



switch($_GET['proses']){
        case'getPrd':
        $optorg2="<option value=''>".$_SESSION['lang']['all']."</option>";
        $sOrg="select distinct kodegudang from ".$dbname.".log_transaksi_vw where ".$whrBrg2."";
		//exit("error:".$sOrg);
        $qOrg=mysql_query($sOrg) or die(mysql_error());
        while($rOrg=mysql_fetch_assoc($qOrg)){
			$whrtgdng="kodeorganisasi='".$rOrg['kodegudang']."'";
			$optNmOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whrtgdng);
			$optorg2.="<option value=".$rOrg['kodegudang'].">".$optNmOrg[$rOrg['kodegudang']]."</option>";
        }
		
        $optorg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $sOrg="select distinct periode from ".$dbname.".setup_periodeakuntansi where left(kodeorg,4) in (".$ingPt.") and char_length(kodeorg)=6 order by periode desc";
		//exit("error:".$sOrg);
        $qOrg=mysql_query($sOrg) or die(mysql_error());
        while($rOrg=mysql_fetch_assoc($qOrg))
        {
                $optorg.="<option value=".$rOrg['periode'].">".$rOrg['periode']."</option>";
        }
        echo $optorg."####".$optorg2;
        break;
        case'preview':
		 
        echo $tab;
        break;
        case'excel':
			$thisDate=date("YmdHms");
			//$nop_="Laporan_Pembelian";
			$nop_="lap_harian_bhnbakar_solar_".$thisDate;
			 $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
			 gzwrite($gztralala, $tab);
			 gzclose($gztralala);
			 echo "<script language=javascript1.2>
				window.location='tempExcel/".$nop_.".xls.gz';
				</script>";
        break;
}
?>