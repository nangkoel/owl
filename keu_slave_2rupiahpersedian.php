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

if(($_GET['proses']=='preview')||($_GET['proses']=='excel')){
		if($param['ptId']==''){
			exit("warning: ".$_SESSION['lang']['pt']." tidak boleh kosong");
		}
		if($param['periode']==''){
			exit("warning: ".$_SESSION['lang']['periode']." tidak boleh kosong");
		}
		$whrBrg="left(noakun,5)='11501' and kodeorg in (".$ingPt.")";
		$whrBrg2=" kodept='".$param['ptId']."'";
		$whrSaldo="and kodeorg in (".$ingPt.")";
		$whrtgl="kodeorg in (".$ingPt.")";
		if($param['divisiId']!=''){
			$whrBrg="left(noakun,5)='11501' and kodeorg='".$param['divisiId']."'";
			$whrBrg2=" left(kodegudang,4)='".$param['divisiId']."'";
			$whrSaldo=" and kodeorg='".$param['divisiId']."'";
			$whrtgl="kodeorg='".$param['divisiId']."'";
		}
		if($param['noakun']!=''){
			$whrBrg.=" and noakun='".$param['noakun']."'";
			$whrBrg2.=" and left(kodebarang,3) in (select kode from ".$dbname.".log_5klbarang where noakun='".$param['noakun']."')";
			$whrSaldo.=" and noakun='".$param['noakun']."'";
		}
		#ambil tanggal mulai dan tanggal sampai di setup_periodeakutansi
		$prd=substr($param['periode'],0,4)."-01";
		if(substr($param['periode'],0,4)=='2014'){
				$prd=substr($param['periode'],0,4)."-03";
		}
		$sTgl1="select distinct tanggalmulai from ".$dbname.".setup_periodeakuntansi
		       where ".$whrtgl." and periode='".$prd."'";
		$qTgl1=mysql_query($sTgl1) or die(mysql_error($conn));
		$rTgl1=mysql_fetch_assoc($qTgl1);
		
		$sTgl="select distinct tanggalsampai from ".$dbname.".setup_periodeakuntansi
		       where ".$whrtgl." and periode='".$param['periode']."'";
		$qTgl=mysql_query($sTgl) or die(mysql_error($conn));
		$rTgl=mysql_fetch_assoc($qTgl);
		$tglwrt=" and tanggal>='".$rTgl1['tanggalmulai']."' and  tanggal<='".$rTgl['tanggalsampai']."'";
		
		#pembentukan array bersumber dari table keu_jurnaldt_vw
		$sLstGdng="select  noakun,kodeorg,sum(debet) as debet,sum(kredit) as kredit,left(tanggal,7) as periode from ".$dbname.".keu_jurnaldt_vw 
		           where ".$whrBrg." ".$tglwrt." and kodebarang<>'' and nojurnal not like '%/M/%' group by noakun,kodeorg,left(tanggal,7) order by kodeorg,left(tanggal,7) asc";
		    //echo $sLstGdng."___";
		$qLstGdng=mysql_query($sLstGdng) or die(mysql_error($conn));
		while($rLstGdng=mysql_fetch_assoc($qLstGdng)){
			$dtDebetGl[$rLstGdng['noakun'].$rLstGdng['kodeorg'].$rLstGdng['periode']]=$rLstGdng['debet'];
			$dtKreditGl[$rLstGdng['noakun'].$rLstGdng['kodeorg'].$rLstGdng['periode']]=$rLstGdng['kredit'];
			$dtDebetGlPt[$rLstGdng['noakun'].$param['ptId'].$rLstGdng['periode']]+=$rLstGdng['debet'];
			$dtKreditGlPt[$rLstGdng['noakun'].$param['ptId'].$rLstGdng['periode']]+=$rLstGdng['kredit'];

		}
		#pembentukan array bersumber dari table log_transaksi_vw debet
		$sLstGdng="select distinct left(kodegudang,4) as kodeorg,kodegudang,gudangx,left(kodebarang,3) as klmpkBrg,sum(jumlah*hargasatuan) as rupiah,sum(jumlah*hargarata) as rupiah2,tipetransaksi,left(tanggal,7) as periode 
		           from ".$dbname. ".log_transaksi_vw  where ".$whrBrg2."  ".$tglwrt."  and tipetransaksi<5 and post!=0 and statusjurnal!=0 
				   group by kodegudang,gudangx,left(kodebarang,3),left(tanggal,7),tipetransaksi order by left(kodegudang,4) asc";
		//echo $sLstGdng;
		$qLstGdng=mysql_query($sLstGdng) or die(mysql_error($conn));
		while($rLstGdng=mysql_fetch_assoc($qLstGdng)){
			$whrAkun="kode='".$rLstGdng['klmpkBrg']."'";
			$optNoakun=makeOption($dbname,'log_5klbarang','kode,noakun',$whrAkun);
			$dtKdorg[$rLstGdng['kodeorg']]=$rLstGdng['kodeorg'];
			if($rLstGdng['rupiah']==0){
				$rLstGdng['rupiah']=$rLstGdng['rupiah2'];
			}
			if($rLstGdng['tipetransaksi']==3){
				if(substr($rLstGdng['kodegudang'],0,4)==substr($rLstGdng['gudangx'],0,4)){
					continue;
				}	
			}

			$dtDebet[$optNoakun[$rLstGdng['klmpkBrg']].$rLstGdng['kodeorg'].$rLstGdng['periode']]+=$rLstGdng['rupiah'];
			$dtDebetPt[$optNoakun[$rLstGdng['klmpkBrg']].$param['ptId'].$rLstGdng['periode']]+=$rLstGdng['rupiah'];
			$dtNoakun[$optNoakun[$rLstGdng['klmpkBrg']]]=$optNoakun[$rLstGdng['klmpkBrg']];			
			
		}
		#pembentukan array bersumber dari table log_transaksi_vw kredit
		$sLstGdng="select distinct left(kodegudang,4) as kodeorg,left(kodebarang,3) as klmpkBrg,kodegudang,gudangx,sum(jumlah*hargarata) as rupiah,tipetransaksi,left(tanggal,7) as periode 
		           from ".$dbname. ".log_transaksi_vw  where ".$whrBrg2."  ".$tglwrt." and tipetransaksi>4 and post!=0 and statusjurnal!=0
				   group by kodegudang,gudangx,left(kodebarang,3),left(tanggal,7),tipetransaksi order by left(kodegudang,4) asc";
		//echo $sLstGdng;
		$qLstGdng=mysql_query($sLstGdng) or die(mysql_error($conn));
		while($rLstGdng=mysql_fetch_assoc($qLstGdng)){
			$whrAkun="kode='".$rLstGdng['klmpkBrg']."'";
			$optNoakun=makeOption($dbname,'log_5klbarang','kode,noakun',$whrAkun);
			if($rLstGdng['tipetransaksi']==7){
				if(substr($rLstGdng['kodegudang'],0,4)==substr($rLstGdng['gudangx'],0,4)){
					continue;
				}	
			}
			$dtKredit[$optNoakun[$rLstGdng['klmpkBrg']].$rLstGdng['kodeorg'].$rLstGdng['periode']]+=$rLstGdng['rupiah'];
			$dtKreditPt[$optNoakun[$rLstGdng['klmpkBrg']].$param['ptId'].$rLstGdng['periode']]+=$rLstGdng['rupiah'];
			$dtPt[$param['ptId']]=$param['ptId'];
			$dtKdorg[$rLstGdng['kodeorg']]=$rLstGdng['kodeorg'];
			$dtNoakun[$optNoakun[$rLstGdng['klmpkBrg']]]=$optNoakun[$rLstGdng['klmpkBrg']];			
		}
		$prde=explode("-",$param['periode']);
		#pembentukan array untuk saldo awal
		$sAwal="select * from ".$dbname.".keu_saldobulanan where ".$whrBrg." 
		        and periode<='".$prde[0]."".$prde[1]."' and left(periode,4)='".$prde[0]."' 
				".$whrSaldo."  order by periode asc";
		$qAwal=mysql_query($sAwal) or die(mysql_error($conn));
		while($rAwal=mysql_fetch_assoc($qAwal)){
			$bln=substr($rAwal['periode'],4,2);
			$periodedt=$prde[0]."-".$bln;
			if($addd==1){	
				$prdAwal=$periodedt;
			}
			$dtAwal[$rAwal['noakun'].$rAwal['kodeorg'].$periodedt]=$rAwal['awal'.$bln];
			$dtAwalPt[$rAwal['noakun'].$param['ptId'].$periodedt]+=$rAwal['awal'.$bln];
			$dtPeriode[$periodedt]=$periodedt;
		}
		
		if(count($dtNoakun)==0){
			exit("error: data kosong");
		}
		array_multisort($dtNoakun,SORT_ASC);
		array_multisort($dtPeriode,SORT_ASC);
		array_multisort($dtKdorg,SORT_ASC);
		$brd=0;
		$bgwarna="class=rowheader  align=center";
		if($_GET['proses']=='excel'){
			$brd=1;
			$bgwarna="bgcolor=#DEDEDE align=center";
		}
		$tab.="<table cellpading=1 cellspacing=1 border=".$brd." class=sortable>";
		$tab.="<thead>";
		$tab.="<tr>";
		$tab.="<td rowspan=4 ".$bgwarna.">".$_SESSION['lang']['noakun']."</td>";
		$tab.="<td rowspan=4 ".$bgwarna.">".$_SESSION['lang']['namaakun']."</td>";
		$tab.="<td rowspan=4 ".$bgwarna.">".$_SESSION['lang']['divisi']."</td>";
		foreach($dtPt as $lstKdOrg){
			if($lstKdOrg!=''){
				$whrtgdng="kodeorganisasi='".$lstKdOrg."'";
				$optNmOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whrtgdng);
				$tab.="<td colspan=".((count($dtPeriode)*8)+8)."  ".$bgwarna.">".$lstKdOrg."-".$optNmOrg[$lstKdOrg]."</td>";
			}		
		}
		$tab.="</tr>";
		$tab.="<tr>";
		foreach($dtPt as $lstKdOrg){
			foreach($dtPeriode as $lstPeriode){
				if($lstKdOrg!=''){
					$tab.="<td ".$bgwarna." rowspan=3>".$_SESSION['lang']['saldoawal']."</td>";
					$tab.="<td colspan=6  ".$bgwarna.">".$lstPeriode."</td>";
					$tab.="<td ".$bgwarna." rowspan=3>".$_SESSION['lang']['selisih']."</td>";
				}
			}
		}
		$tab.="<td ".$bgwarna." rowspan=3>".$_SESSION['lang']['saldoawal']."</td>";
		$tab.="<td colspan=3  rowspan=2 ".$bgwarna.">Modul</td>";
		$tab.="<td colspan=3  rowspan=2 ".$bgwarna.">GL</td>";
		$tab.="<td ".$bgwarna." rowspan=3>".$_SESSION['lang']['selisih']."</td>";
		$tab.="</tr>";
		$tab.="<tr>";
		foreach($dtPt as $lstKdOrg){
			foreach($dtPeriode as $lstPeriode){
				if($lstKdOrg!=''){
					$tab.="<td colspan=3  ".$bgwarna.">Modul</td>";
					$tab.="<td colspan=3  ".$bgwarna.">GL</td>";
				}
			}
		}
		$tab.="</tr>";
		$tab.="<tr>";
		foreach($dtPt as $lstKdOrg){
			foreach($dtPeriode as $lstPeriode){
				if($lstKdOrg!=''){
					$tab.="<td ".$bgwarna.">".$_SESSION['lang']['debet']."</td>";
					$tab.="<td ".$bgwarna.">".$_SESSION['lang']['kredit']."</td>";
					$tab.="<td ".$bgwarna.">".$_SESSION['lang']['saldo']."</td>";
					$tab.="<td ".$bgwarna.">".$_SESSION['lang']['debet']."</td>";
					$tab.="<td ".$bgwarna.">".$_SESSION['lang']['kredit']."</td>";
					$tab.="<td ".$bgwarna.">".$_SESSION['lang']['saldo']."</td>";
				}
			}
		}
		$tab.="<td ".$bgwarna.">".$_SESSION['lang']['debet']."</td>";
		$tab.="<td ".$bgwarna.">".$_SESSION['lang']['kredit']."</td>";
		$tab.="<td ".$bgwarna.">".$_SESSION['lang']['saldo']."</td>";
		$tab.="<td ".$bgwarna.">".$_SESSION['lang']['debet']."</td>";
		$tab.="<td ".$bgwarna.">".$_SESSION['lang']['kredit']."</td>";
		$tab.="<td ".$bgwarna.">".$_SESSION['lang']['saldo']."</td>";
		$tab.="</tr>";
		$tab.="</thead><tbody>";
		$addd=1;
		foreach($dtNoakun as $lstNoakun){
			foreach($dtKdorg as $lstKdOrg){
			$tab.="<tr class=rowcontent>";
			if($temAkun!=$lstNoakun){
				$tab.="<td>".$lstNoakun."</td>";
				$whr="noakun='".$lstNoakun."'";
				$optNmAkun=makeOption($dbname,'keu_5akun','noakun,namaakun',$whr);
				$tab.="<td>".$optNmAkun[$lstNoakun]."</td>";
				$temAkun=$lstNoakun;
				$tempDer=true;
			}else{
				if($tempDer==true){
					$tab.="<td rowspan='".(count($dtKdorg)-1)."'>&nbsp;</td>";
					$tab.="<td rowspan='".(count($dtKdorg)-1)."'>&nbsp;</td>";
					$tempDer=false;
				}
				
			}
			$tab.="<td>".$lstKdOrg."</td>";
			
				foreach($dtPeriode as $lstPeriode){
					#zExcel(event,'keu_slave_2rupiahpersedian.php',' echo $arr')
					$arrLin="##periodeDt".$lstPeriode."##kdorgDt".$lstKdOrg."##noakunDt".$lstNoakun."";
					$addLink="style=cursor:pointer; onclick=zExcel(event,'keu_slave_2rupiahpersedian2.php','".$arrLin."')";
					$tab.="<td ".$addLink." align=right>".number_format($dtAwal[$lstNoakun.$lstKdOrg.$lstPeriode],2)."</td>";
					$tab.="<td ".$addLink." align=right>".number_format($dtDebet[$lstNoakun.$lstKdOrg.$lstPeriode],2)."</td>";
					$tab.="<td ".$addLink." align=right>".number_format($dtKredit[$lstNoakun.$lstKdOrg.$lstPeriode],2)."</td>";
					$salDo[$lstNoakun.$lstKdOrg.$lstPeriode]=($dtAwal[$lstNoakun.$lstKdOrg.$lstPeriode]+$dtDebet[$lstNoakun.$lstKdOrg.$lstPeriode])-$dtKredit[$lstNoakun.$lstKdOrg.$lstPeriode];
					$tab.="<td align=right>".number_format($salDo[$lstNoakun.$lstKdOrg.$lstPeriode],2)."";
					$tab.="<input type=hidden id=periodeDt".$lstPeriode." value='".$lstPeriode."' />
					       <input type=hidden id=kdorgDt".$lstKdOrg." value='".$lstKdOrg."' />
					       <input type=hidden id=noakunDt".$lstNoakun." value='".$lstNoakun."' />
					       </td>";
					
					$totSawal[$lstNoakun.$lstKdOrg]=$dtAwal[$lstNoakun.$lstKdOrg.$prdAwal];
					
					
					$totDebet[$lstNoakun.$lstKdOrg]+=$dtDebet[$lstNoakun.$lstKdOrg.$lstPeriode];
					$totKredit[$lstNoakun.$lstKdOrg]+=$dtKredit[$lstNoakun.$lstKdOrg.$lstPeriode];
					$tab.="<td align=right ".$addLink.">".number_format($dtDebetGl[$lstNoakun.$lstKdOrg.$lstPeriode],2)."</td>";
					$tab.="<td align=right ".$addLink.">".number_format($dtKreditGl[$lstNoakun.$lstKdOrg.$lstPeriode],2)."</td>";
					$salDoGl[$lstNoakun.$lstKdOrg.$lstPeriode]=($dtAwal[$lstNoakun.$lstKdOrg.$lstPeriode]+$dtDebetGl[$lstNoakun.$lstKdOrg.$lstPeriode])-$dtKreditGl[$lstNoakun.$lstKdOrg.$lstPeriode];
					$totDebetGl[$lstNoakun.$lstKdOrg]+=$dtDebetGl[$lstNoakun.$lstKdOrg.$lstPeriode];
					$totKreditGl[$lstNoakun.$lstKdOrg]+=$dtKreditGl[$lstNoakun.$lstKdOrg.$lstPeriode];
					$tab.="<td align=right  ".$addLink.">".number_format($salDoGl[$lstNoakun.$lstKdOrg.$lstPeriode],2)."</td>";
					$selisihGl[$lstNoakun.$lstKdOrg.$lstPeriode]=$salDo[$lstNoakun.$lstKdOrg.$lstPeriode]-$salDoGl[$lstNoakun.$lstKdOrg.$lstPeriode];
					$tab.="<td align=right  ".$addLink.">".number_format($selisihGl[$lstNoakun.$lstKdOrg.$lstPeriode],2)."</td>";
				}
				$tab.="<td align=right>".number_format($totSawal[$lstNoakun.$lstKdOrg],2)."</td>";
				$tab.="<td align=right>".number_format($totDebet[$lstNoakun.$lstKdOrg],2)."</td>";
				$tab.="<td align=right>".number_format($totKredit[$lstNoakun.$lstKdOrg],2)."</td>";
				$salDoGr[$lstNoakun.$lstKdOrg]=($totSawal[$lstNoakun.$lstKdOrg]+$totDebet[$lstNoakun.$lstKdOrg])-$totKredit[$lstNoakun.$lstKdOrg];
				$tab.="<td align=right>".number_format($salDoGr[$lstNoakun.$lstKdOrg],2)."</td>";
				$tab.="<td align=right>".number_format($totDebetGl[$lstNoakun.$lstKdOrg],2)."</td>";
				$tab.="<td align=right>".number_format($totKreditGl[$lstNoakun.$lstKdOrg],2)."</td>";
				$salDoGl[$lstNoakun.$lstKdOrg]=($totSawal[$lstNoakun.$lstKdOrg]+$totDebetGl[$lstNoakun.$lstKdOrg])-$totKreditGl[$lstNoakun.$lstKdOrg];
				$tab.="<td align=right>".number_format($salDoGl[$lstNoakun.$lstKdOrg],2)."</td>";
				$selisihTot[$lstNoakun.$lstKdOrg]=$salDoGr[$lstNoakun.$lstKdOrg]-$salDoGl[$lstNoakun.$lstKdOrg];
				$tab.="<td align=right>".number_format($selisihTot[$lstNoakun.$lstKdOrg],2)."</td>";
				$tab.="</tr>";
			}
			$tab.="<tr>";
			$tab.="<td align=right>".$_SESSION['lang']['grnd_total']."</td>";
			$tab.="<td align=right>".$lstNoakun."</td>";
			$whr="noakun='".$lstNoakun."'";
			$optNmAkun=makeOption($dbname,'keu_5akun','noakun,namaakun',$whr);
			$tab.="<td align=right>".$optNmAkun[$lstNoakun]."</td>";
			foreach($dtPeriode as $lstPeriode){
					$arrLin="##periodeDt".$lstPeriode."##kdorgDt".$param['ptId']."##noakunDt".$lstNoakun."";
					$addLink="style=cursor:pointer; onclick=zExcel(event,'keu_slave_2rupiahpersedian2.php','".$arrLin."')";
					$tab.="<td align=right ".$addLink.">".number_format($dtAwalPt[$lstNoakun.$param['ptId'].$lstPeriode],2)."</td>";
					$tab.="<td align=right ".$addLink.">".number_format($dtDebetPt[$lstNoakun.$param['ptId'].$lstPeriode],2)."</td>";
					$tab.="<td align=right ".$addLink.">".number_format($dtKreditPt[$lstNoakun.$param['ptId'].$lstPeriode],2)."</td>";
					$salDoPt[$lstNoakun.$param['ptId'].$lstPeriode]=($dtAwalPt[$lstNoakun.$param['ptId'].$lstPeriode]+$dtDebetPt[$lstNoakun.$param['ptId'].$lstPeriode])-$dtKreditPt[$lstNoakun.$param['ptId'].$lstPeriode];
					$tab.="<td align=right>".number_format($salDoPt[$lstNoakun.$param['ptId'].$lstPeriode],2)."";
					$tab.="<input type=hidden id=periodeDt".$lstPeriode." value='".$lstPeriode."' />
					       <input type=hidden id=kdorgDt".$param['ptId']." value='".$param['ptId']."' />
					       <input type=hidden id=noakunDt".$lstNoakun." value='".$lstNoakun."' />";
					$tab.="</td>";	   
					
					$totSawalPt[$lstNoakun]=$dtAwalPt[$param['ptId'].$param['ptId'].$prdAwal];
					
					
					$totDebetPt[$lstNoakun]=$dtDebetPt[$lstNoakun.$param['ptId'].$lstPeriode];
					$totKreditPt[$lstNoakun]=$dtKreditPt[$lstNoakun.$param['ptId'].$lstPeriode];
					$tab.="<td align=right ".$addLink.">".number_format($dtDebetGlPt[$lstNoakun.$param['ptId'].$lstPeriode],2)."</td>";
					$tab.="<td align=right ".$addLink.">".number_format($dtKreditGlPt[$lstNoakun.$param['ptId'].$lstPeriode],2)."</td>";
					$salDoGlPt[$lstNoakun.$param['ptId'].$lstPeriode]=($dtAwalPt[$lstNoakun.$param['ptId'].$lstPeriode]+$dtDebetGlPt[$lstNoakun.$param['ptId'].$lstPeriode])-$dtKreditGlPt[$lstNoakun.$param['ptId'].$lstPeriode];
					$totDebetGlPt[$lstNoakun]+=$dtDebetGlPt[$lstNoakun.$param['ptId'].$lstPeriode];
					$totKreditGlPt[$lstNoakun]+=$dtKreditGlPt[$lstNoakun.$param['ptId'].$lstPeriode];
					$tab.="<td align=right ".$addLink.">".number_format($salDoGlPt[$lstNoakun.$param['ptId'].$lstPeriode],2)."</td>";
					$selisihGlPt[$lstNoakun.$param['ptId'].$lstPeriode]=$salDoPt[$lstNoakun.$param['ptId'].$lstPeriode]-$salDoGlPt[$lstNoakun.$param['ptId'].$lstPeriode];
					$tab.="<td align=right ".$addLink.">".number_format($selisihGlPt[$lstNoakun.$param['ptId'].$lstPeriode],2)."</td>";
			}
			$tab.="<td align=right>".number_format($totSawalPt[$lstNoakun],2)."</td>";
			$tab.="<td align=right>".number_format($totDebetPt[$lstNoakun],2)."</td>";
			$tab.="<td align=right>".number_format($totKreditPt[$lstNoakun],2)."</td>";
			$salDoGrPt[$lstNoakun]=($totSawalPt[$lstNoakun]+$totDebetPt[$lstNoakun])-$totKreditPt[$lstNoakun];
			$tab.="<td align=right>".number_format($salDoGrPt[$lstNoakun],2)."</td>";
			$tab.="<td align=right>".number_format($totDebetGlPt[$lstNoakun],2)."</td>";
			$tab.="<td align=right>".number_format($totKreditGlPt[$lstNoakun],2)."</td>";
			$salDoGlPt[$lstNoakun]=($totSawalPt[$lstNoakun]+$totDebetGlPt[$lstNoakun])-$totKreditGlPt[$lstNoakun];
			$tab.="<td align=right>".number_format($salDoGlPt[$lstNoakun],2)."</td>";
			$selisihTotPt[$lstNoakun]=$salDoGrPt[$lstNoakun]-$salDoGlPt[$lstNoakun];
			$tab.="<td align=right>".number_format($selisihTotPt[$lstNoakun],2)."</td>";
			$tab.="</tr>";
		}
		$tab.="</tbody></table>";
}



switch($_GET['proses']){
        case'getPrd':
        $optorg2="<option value=''>".$_SESSION['lang']['all']."</option>";
        $sOrg="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where induk='".$param['ptId']."'";
		//exit("error:".$sOrg);
        $qOrg=mysql_query($sOrg) or die(mysql_error());
        while($rOrg=mysql_fetch_assoc($qOrg)){
			$optorg2.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
        }
		
        $optorg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $sOrg="select distinct periode from ".$dbname.".setup_periodeakuntansi where left(kodeorg,4) in (".$ingPt.") and char_length(kodeorg)=4 order by periode desc";
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
			$nop_="rupiah_persediaan_".$thisDate;
			 $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
			 gzwrite($gztralala, $tab);
			 gzclose($gztralala);
			 echo "<script language=javascript1.2>
				window.location='tempExcel/".$nop_.".xls.gz';
				</script>";
        break;
}
?>