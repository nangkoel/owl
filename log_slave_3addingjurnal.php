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


$optNmOrg=makeOption($dbname, 'organisasi','kodeorganisasi,namaorganisasi');

$_POST['kdOrg']==''?$kdOrg=$_GET['kdOrg']:$kdOrg=$_POST['kdOrg'];
$_POST['periodeGdng']==''?$periodeGdng=$_GET['periodeGdng']:$periodeGdng=$_POST['periodeGdng'];
$_POST['tpTransaksi']==''?$tpTransaksi=$_GET['tpTransaksi']:$tpTransaksi=$_POST['tpTransaksi'];
$lstTrns=explode(",",$_POST['listTransaksi']);
$namaTrans=array("0"=>"Koreksi","1"=>"Penerimaan Barang dari Supplier","2"=>"Retur","3"=>"Penerimaan Mutasi","5"=>"Pengeluaran/Pemakaian",
        "6"=>"Pengembalian penerimaan supplier","7"=>"Pengeluaran Mutasi");


if($proses=='preview'||$proses=='excel'){
$brdr=0;
$bgcoloraja='';
if($proses=='excel'){
    $brdr=1;
    $bgcoloraja='green';
}
	#bentuk array notransaksi yang belum terjurnal
	//foreach($lstTrns as $notrans){
		$sTipe="select distinct * from ".$dbname.".log_transaksi_vw 
				where  kodegudang='".$kdOrg."' and left(tanggal,7)='".$periodeGdng."'  and tipetransaksi='".$tpTransaksi."' and post=1   order by notransaksi asc"; 
				/*post=1  and a.tanggal like '".$periodeGdng."%' and tipetransaksi='".$tpTransaksi."'"
				. " and kodegudang='".$kdOrg."'  order by notransaksi asc*/
		$qTipe=mysql_query($sTipe) or die (mysql_error($conn));
		while($rData=mysql_fetch_assoc($qTipe)){		
					if($rData['notransaksi']!=$tmpTransaksi){
						$scek="select * from ".$dbname.".keu_jurnalht where noreferensi='".$rData['notransaksi']."'";
						$qcek=mysql_query($scek) or die(mysql_error($conn));
						$rcek=mysql_num_rows($qcek);
						if($rcek!=0){
							continue;
						}
						$tmpTransaksi=$rData['notransaksi'];
						$arr=1;
					}else{
						$arr+=1;
					}
					$jmRow[$rData['notransaksi']]=$arr;
					$dtNotransaksi[$rData['notransaksi']]=$rData['notransaksi'];
					$dtNotrans[$rData['notransaksi'].$arr]=$rData['notransaksi'];
					$dtTptrans[$rData['notransaksi'].$arr]=$rData['tipetransaksi'];
					$dtKdbrgtrans[$rData['notransaksi'].$arr]=$rData['kodebarang'];
					$dtSatbrgtrans[$rData['notransaksi'].$arr]=$rData['satuan'];
					$dtJumbrgtrans[$rData['notransaksi'].$arr]=$rData['jumlah'];
					$dtTgltrans[$rData['notransaksi'].$arr]=$rData['tanggal'];
					$dtPttrans[$rData['notransaksi'].$arr]=$rData['kodept'];
					$dtUPttrans[$rData['notransaksi'].$arr]=$rData['untukpt'];
					$dtUUnittrans[$rData['notransaksi'].$arr]=$rData['untukunit'];
					$dtKegttrans[$rData['notransaksi'].$arr]=$rData['kodekegiatan'];
					$dtVhcttrans[$rData['notransaksi'].$arr]=$rData['kodemesin'];
					$dtSupttrans[$rData['notransaksi'].$arr]=$rData['supplier'];
					$dtNoptrans[$rData['notransaksi'].$arr]=$rData['nopo'];
					$dtBlktrans[$rData['notransaksi'].$arr]=$rData['kodeblok'];
					$dtGdngxtrans[$rData['notransaksi'].$arr]=$rData['gudangx'];
					$dtHrggxtrans[$rData['notransaksi'].$arr]=$rData['hargasatuan'];
		}
       
    //}
	$tab.="<table cellspacing=1 border=".$brdr." class=sortable>
	<thead class=rowheader>
	<tr ".$bgcoloraja.">
        <td align=center>No.</td>
        <td align=center>".$_SESSION['lang']['notransaksi']."</td>
		<td align=center>".$_SESSION['lang']['pt']."</td>
		<td align=center>Untuk PT</td>
		<td align=center>Untuk unit</td>
        <td align=center>".$_SESSION['lang']['kodeblok']."</td>
		<td align=center>".$_SESSION['lang']['kodekegiatan']."</td>
		<td align=center>".$_SESSION['lang']['kodemesin']."</td>
		<td align=center>".$_SESSION['lang']['kodesupplier']."</td>
		<td align=center>".$_SESSION['lang']['nopo']."</td>
		<td align=center>".$_SESSION['lang']['tipetransaksi']."</td>
        <td align=center>".$_SESSION['lang']['tanggal']."</td>
		<td align=center>Gudang Tujuan</td>
		<td align=center>".$_SESSION['lang']['hargasatuan']."</td>
        <td align=center>".$_SESSION['lang']['kodebarang']."</td>
        <td align=center>".$_SESSION['lang']['jumlah']."</td>
		<td align=center>".$_SESSION['lang']['satuan']."</td>
        </tr>";
        $tab.="</thead><tbody>";
        foreach($dtNotransaksi as $lsTrans){
			for($awl=1;$awl<=$jmRow[$lsTrans];$awl++){
				$no+=1;
				$tab.="<tr class=rowcontent id=row_".$no.">";
				$tab.="<td>".$no."</td>";
				$tab.="<td id=notrans_".$no.">".$dtNotrans[$lsTrans.$awl]."</td>";
				$tab.="<td id=pt_".$no.">".$dtPttrans[$lsTrans.$awl]."</td>";
				$tab.="<td id=Upt_".$no.">".$dtUPttrans[$lsTrans.$awl]."</td>";
				$tab.="<td id=UUnit_".$no.">".$dtUUnittrans[$lsTrans.$awl]."</td>";
				$tab.="<td id=blok_".$no.">".$dtBlktrans[$lsTrans.$awl]."</td>";
				$tab.="<td id=kegId_".$no.">".$dtKegttrans[$lsTrans.$awl]."</td>";
				$tab.="<td id=vhcId_".$no.">".$dtVhcttrans[$lsTrans.$awl]."</td>";
				$tab.="<td id=suppId_".$no.">".$dtSupttrans[$lsTrans.$awl]."</td>";
				$tab.="<td id=nopo_".$no.">".$dtNoptrans[$lsTrans.$awl]."</td>";
				$tab.="<td align=right id=tpTrans_".$no.">".$dtTptrans[$lsTrans.$awl]."</td>";
				$tab.="<td  id=tglTrans_".$no.">".tanggalnormal($dtTgltrans[$lsTrans.$awl])."</td>";
				$tab.="<td  id=gdngx_".$no.">".$dtGdngxtrans[$lsTrans.$awl]."</td>";
				$tab.="<td  align=right id=hrgsat_".$no.">".$dtHrggxtrans[$lsTrans.$awl]."</td>";
				$tab.="<td  id=kdBrg_".$no.">".$dtKdbrgtrans[$lsTrans.$awl]."</td>";
				$tab.="<td  id=sat_".$no.">".$dtSatbrgtrans[$lsTrans.$awl]."</td>";
				$tab.="<td  align=right id=jmlh_".$no." >".$dtJumbrgtrans[$lsTrans.$awl]."</td>";
				$tab.="</tr>";
			}
		}
		$sPeriodeAkut="select distinct tipetransaksi from ".$dbname.".log_transaksiht  
				 where kodegudang='".$_POST['kdOrg']."' and left(tanggal,7)='".$periodeGdng."'";
		$qPeriodeCari=mysql_query($sPeriodeAkut) or die(mysql_error());
		$rPeriodeCari=mysql_fetch_assoc($qPeriodeCari);
        $tab.="</tbody></table><input type=hidden id=dt_start value='".$rPeriodeCari['tanggalmulai']."' /><input type=hidden id=dt_end value='".$rPeriodeCari['tanggalsampai']."' />";
         if(($_SESSION['empl']['bagian']=='IT')||($_SESSION['empl']['bagian']=='FIN')){
            $tab.="<button class=mybutton onclick=prosesPosting('".$no."','".$tpTransaksi."')  id=revTmbl3>Update Data</button>&nbsp;<button class=mybutton onclick=zExcel(event,'kebun_slave_3updategajibjr3.php','".$arr3."')>Excel</button>";
        }else{
            $tab.="<button class=mybutton onclick=zExcel(event,'kebun_slave_3updategajibjr3.php','".$arr3."')>Excel</button>";
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
					 where kodeorg='".$_POST['kdOrg']."' ";
					 //exit("error:".$sPeriodeAkut);
		$qPeriodeCari=mysql_query($sPeriodeAkut) or die(mysql_error());
		while($rPeriodeCari=mysql_fetch_assoc($qPeriodeCari))
		{
		   $optPeriode.="<option value='".$rPeriodeCari['periode']."'>".$rPeriodeCari['periode']."</option>";
		}
		echo $optPeriode;
	break;
	case'getTp':
		$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$sPeriodeAkut="select distinct tipetransaksi from ".$dbname.".log_transaksiht  
					 where kodegudang='".$_POST['kdOrg']."' and left(tanggal,7)='".$periodeGdng."'";
					 //exit("error:".$sPeriodeAkut);
		$qPeriodeCari=mysql_query($sPeriodeAkut) or die(mysql_error());
		while($rPeriodeCari=mysql_fetch_assoc($qPeriodeCari))
		{
		   $optPeriode.="<option value='".$rPeriodeCari['tipetransaksi']."'>".$namaTrans[$rPeriodeCari['tipetransaksi']]."</option>";
		}
		echo $optPeriode;
	break;
        case'updateData':
            foreach($_POST['notrans'] as $rowdt=>$isiRow){
                $scek="select distinct * from ".$dbname.".setup_periodeakuntansi where "
                    . "kodeorg='".substr($isiRow,0,4)."' and periode='".substr($_POST['tgl'][$rowdt],0,7)."'";
                $qcek=  mysql_query($scek) or die(mysql_error($conn));
                $rcek= mysql_fetch_assoc($qcek);
                if($rcek['tutupbuku']==1){
                    continue;
                }else{
                    if(($_POST['tgl'][$rowdt]=='')&&($_POST['nik'][$rowdt]=='')){
                        continue;
                    }
                    
                    $suphadir="update ".$dbname.".vhc_runhk set premi='".$_POST['updUpah'][$rowdt]."'"
                            . " where notransaksi='".$isiRow."' and idkaryawan='".$_POST['nik'][$rowdt]."'";
                    //exit("error".$suphadir);
                    if(!mysql_query($suphadir)){
                         exit("error: db bermasalah ".mysql_error($conn)."___".$suphadir);   
                    }
                }
            }
        break;
        case'excel':
        $thisDate=date("YmdHms");
                   //$nop_="Laporan_Pembelian";
                   $nop_="laporanUpdatePerawatan_".$thisDate;
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