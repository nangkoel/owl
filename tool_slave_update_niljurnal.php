<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

if(isset($_POST['proses']))
{
	$proses=$_POST['proses'];
}
else
{
	$proses=$_GET['proses'];
}
$arr="##kdOrg##periode##tpTrk";
 
$optNmOrg=makeOption($dbname, 'organisasi','kodeorganisasi,namaorganisasi');

$_POST['kdOrg']==''?$kdOrg=$_GET['kdOrg']:$kdOrg=$_POST['kdOrg'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['tpTrk']==''?$tpTrk=$_GET['tpTrk']:$tpTrk=$_POST['tpTrk'];
$tipe='PNN';

$unitId=$_SESSION['lang']['all'];
$dktlmpk=$_SESSION['lang']['all'];

  
$arr="##kdOrg##tanggal1##tanggal2";
if($proses=='preview'||$proses=='excel'){
$brdr=0;
$bgcoloraja='';
if($proses=='excel'){
    $brdr=1;
    $bgcoloraja='green';
}
 
		$sJurnal="select substr(nojurnal,10,4) as unit,noreferensi,nojurnal,totaldebet from ".$dbname.".keu_jurnalht where 
		          noreferensi in (select distinct notransaksi from ".$dbname.".kebun_aktifitas where tipetransaksi='".$tpTrk."' and tanggal like '".$periode."%' and jurnal=1) and tanggal like '".$periode."%' and nojurnal not like '%INVK%' and substr(nojurnal,10,4)='".$kdOrg."'";
	    if($tpTrk!='PNN'){
			$sTrans="SELECT unit,notransaksi,jurnal,sum(umr+insentif) as jumlah
					 FROM ".$dbname.".`kebun_kehadiran_vw` where  notransaksi like '%".$tpTrk."%' and  jurnal=1 and tanggal like '".$periode."%' 
					 and unit='".$kdOrg."' group by unit,notransaksi";
		}else{
			$sTrans="SELECT unit,notransaksi,jurnal,sum(upahkerja+upahpremi) as jumlah
                 FROM ".$dbname.".`kebun_prestasi_vw` where tanggal like '".$periode."%' and jurnal=1 
				 and unit='".$kdOrg."'
				 group by unit,notransaksi";
		}
	     echo $sJurnal."___".$sTrans;
		$qJurnal=mysql_query($sJurnal) or die(mysql_error($conn));
		while($rJurnal=mysql_fetch_assoc($qJurnal)){	
			$lstNoreferensi[$rJurnal['noreferensi']]=$rJurnal['noreferensi'];
			$lstUnit[$rJurnal['unit']]=$rJurnal['unit'];
			$dtNotrans[$rJurnal['unit'].$rJurnal['noreferensi']]=$rJurnal['noreferensi'];
			$dtJurnal[$rJurnal['noreferensi']]=$rJurnal['nojurnal'];
			if($rJurnal['totaldebet']<0){
				$rJurnal['totaldebet']=$rJurnal['totaldebet']*-1;
			}
			$dtJumlah[$rJurnal['noreferensi']]+=$rJurnal['totaldebet'];
	    }

		$qTrans=mysql_query($sTrans) or die(mysql_error($conn));
		while($rTrans=mysql_fetch_assoc($qTrans)){
			$lstNoreferensi[$rTrans['notransaksi']]=$rTrans['notransaksi'];
			$lstUnit[$rTrans['unit']]=$rTrans['unit'];
			$dtNotrans[$rTrans['unit'].$rTrans['notransaksi']]=$rTrans['notransaksi'];
			$dtTrans[$rTrans['unit'].$rTrans['notransaksi']]=$rTrans['notransaksi'];
			$dtRupiah[$rTrans['notransaksi']]=$rTrans['jumlah'];
			$dtStat[$rTrans['notransaksi']]=$rTrans['jurnal'];
		}
		$rowdt=count($lstNoreferensi);
		
        $dtUnit=$kdOrg;
		$whrt="kodeorganisasi='".$kdOrg."'";
		$optOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whrt);
		$tab.="<table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable>
			   <thead>";
		$tab.="<tr ".$bgcoloraja." class=sortable>";
		$tab.="<td colspan=7>".$kdOrg."-".$optOrg[$kdOrg]."</td></tr>";
		$tab.="<tr ".$bgcoloraja."><td>".$_SESSION['lang']['nojurnal']."</td>";
		$tab.="<td>".$_SESSION['lang']['noreferensi']."</td>";
		$tab.="<td>".$_SESSION['lang']['jumlah']."</td>";
		$tab.="<td>".$_SESSION['lang']['notransaksi']."</td>";
		$tab.="<td>".$_SESSION['lang']['status']."</td>
			   <td>".$_SESSION['lang']['jumlah']."</td>
			   <td>".$_SESSION['lang']['selisih']."</td>
			   </tr></thead><tbody id=dataIsi>";
				foreach($lstNoreferensi as $notransaksi){
					$notrans=$dtNotrans[$dtUnit.$notransaksi];
					if($notrans!=''){
						if(intval($dtJumlah[$notrans]-$dtRupiah[$notrans])!=0){
						$selisih[$notrans]=$dtJumlah[$notrans]-$dtRupiah[$notrans];
							$no+=1;
							$tab.="<tr class=rowcontent>";
							$tab.="<td id=nojurnal_".$no.">".$dtJurnal[$notrans]."</td>";
							$tab.="<td id=noreferensi_".$no.">".$notrans."</td>";
							$tab.="<td align=right>".number_format($dtJumlah[$notrans],2)."</td>";
							$tab.="<td>".$notrans."</td>";
							$tab.="<td>".$dtStat[$notrans]."</td>";
							$tab.="<td align=right ><input type=hidden id=jmlh_".$no." value='".$dtRupiah[$notrans]."' />".number_format($dtRupiah[$notrans],2)."</td>";
							$tab.="<td align=right>".number_format($selisih[$notrans],2)."</td>";
							$tab.="</tr>";
							if($dtJurnal[$notrans]!=''){
								$itungJurnal[$dtUnit]+=1;
								$rpJurnal[$dtUnit]+=$dtJumlah[$notrans];
							}
							if($dtTrans[$dtUnit.$notransaksi]!=''){
								$itungTransaksi[$dtUnit]+=1;
								$rpTransaksi[$dtUnit]+=$dtRupiah[$notrans];
							}
						}
					}
					
				}
		//$tab.="<tr>";
		//$tab.="<td>Jumlah Data Jurnal</td><td align=right>".number_format($itungJurnal[$dtUnit],0)."</td><td align=right>".number_format($rpJurnal[$dtUnit],2)."</td>";
		//$tab.="<td>Jumlah Data Transaksi</td><td align=right>".number_format($itungTransaksi[$dtUnit],0)."</td><td align=right>".number_format($rpTransaksi[$dtUnit],2)."</td></tr>";
		$tab.="</tbody></table>";
		if(($_SESSION['empl']['bagian']=='IT')||($_SESSION['empl']['kodejabatan']=='FIN')){
            $tab.="<button class=mybutton onclick=postingDat3(".$itungTransaksi[$dtUnit].")  id=revTmbl>Update Data</button>&nbsp;
			<!--<button class=mybutton onclick=zExcel(event,'tool_slave_update_niljurnal.php','".$arr."')>Excel</button>-->";
        }else{
           // $tab.="<button class=mybutton onclick=zExcel(event,'tool_slave_update_niljurnal.php','".$arr."')>Excel</button>";
        }
}
        
switch($proses)
{ 
	case'preview':
	echo $tab;
	break;
        case'getPeriode': 
            $optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
            $sPeriodeAkut="select distinct periode from ".$dbname.".setup_periodeakuntansi 
                         where kodeorg='".$_POST['kdOrg']."' and tutupbuku=0";
            $qPeriodeCari=mysql_query($sPeriodeAkut) or die(mysql_error());
            while($rPeriodeCari=mysql_fetch_assoc($qPeriodeCari))
            {
               $optPeriode.="<option value='".$rPeriodeCari['periode']."'>".$rPeriodeCari['periode']."</option>";
            }
            echo $optPeriode;
        break;
        case'updateData':
            foreach($_POST['nojuranl'] as $rwJrn=>$nojurnal){
				$sCek="select count(nojurnal) as itung from ".$dbname.".keu_jurnaldt where nojurnal='".$nojurnal."'";
				$qCek=mysql_query($sCek) or die(mysql_error($conn));
				$rCek=mysql_fetch_assoc($qCek);
				if($rCek['itung']>2){
					if($tpTrk=='PNN'){
						$sFilter="select noakun from ".$dbname.".keu_jurnaldt where noakun!='6110101' and jumlah<0";
						$qFilter=mysql_query($sFilter) or die(mysql_error($conn));
						$rFilter=mysql_fetch_assoc($qFilter);
						if($rFilter['noakun']!='2130100'){
							$supdt="update ".$dbname.".keu_jurnaldt set jumlah='".($_POST['jumlah'][$rwJrn]*-1)."',noakun='2130100' where nojurnal='".$nojurnal."' and nourut=2";
						}else{
							$supdt="update ".$dbname.".keu_jurnaldt set jumlah='".($_POST['jumlah'][$rwJrn]*-1)."',noakun='2130100' where nojurnal='".$nojurnal."' and nourut=2";
						}
						mysql_query($supdt) or die(mysql_error($conn));
					}else{
						continue;
					}
					
				}else{
					$supdHt="update ".$dbname.".keu_jurnalht set totaldebet='".$_POST['jumlah'][$rwJrn]."',totalkredit='".$_POST['jumlah'][$rwJrn]."'
					         where nojurnal='".$nojurnal."'";
				    if(mysql_query($supdHt)){
						$supdt="update ".$dbname.".keu_jurnaldt set jumlah='".$_POST['jumlah'][$rwJrn]."' where nojurnal='".$nojurnal."' and nourut=1";
						mysql_query($supdt) or die(mysql_error($conn));
						$whr="nojurnal='".$nojurnal."' and nourut=2";
						$optAkun=makeOption($dbname,'keu_jurnaldt','nojurnal,noakun',$whr);
						if($optAkun[$nojurnal]!='2130100'){
							$supdt="update ".$dbname.".keu_jurnaldt set jumlah='".($_POST['jumlah'][$rwJrn]*-1)."',noakun='2130100' where nojurnal='".$nojurnal."' and nourut=2";
						}else{
							$supdt="update ".$dbname.".keu_jurnaldt set jumlah='".($_POST['jumlah'][$rwJrn]*-1)."',noakun='2130100' where nojurnal='".$nojurnal."' and nourut=2";
						}
						mysql_query($supdt) or die(mysql_error($conn));
					}
				}
			}
        break;
        case'excel':
        $thisDate=date("YmdHms");
                   //$nop_="Laporan_Pembelian";
                   $nop_="laporanUpdateBjr_".$thisDate;
                   $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
                    gzwrite($gztralala, $tab);
                    gzclose($gztralala);
                    echo "<script language=javascript1.2>
                       window.location='tempExcel/".$nop_.".xls.gz';
                       </script>";
        break;
	default:
	break;
}
?>