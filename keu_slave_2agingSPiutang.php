<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');


// ..proses
$proses=$_GET['proses'];
$kdorg=$_POST['kdorg'];
$noakun=$_POST['noakun'];
$dibuat=$_POST['dibuat'];
$pilId=$_POST['pilId'];
$diperiksa=$_POST['diperiksa'];
//$tgl=tanggaldgnbar($_POST['tgl']);
$tgl=$_POST['tgl'];
$tgl2=$_POST['tgl2'];

if($proses=='excel') {
        $kdorg=$_GET['kdorg'];
        $noakun=$_GET['noakun'];
        //$tgl=tanggaldgnbar($_GET['tgl']);
        $tgl=$_GET['tgl'];
        $tgl2=$_GET['tgl2'];
        $dibuat=$_GET['dibuat'];
        $diperiksa=$_GET['diperiksa'];
        $pilId=$_GET['pilId'];
}
if($proses=='getPeriode'){
        $optPeriode.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $sPeriode="select periode from ".$dbname.".setup_periodeakuntansi where kodeorg='".$_POST['kodeorg']."' order by periode desc";
        $qPeriode=mysql_query($sPeriode) or die(mysql_error($conn));
        while($rPeriode=mysql_fetch_assoc($qPeriode)){
            $optPeriode.="<option value='".$rPeriode['periode']."'>".$rPeriode['periode']."</option>";
        }
        echo $optPeriode;
}
if($tgl>$tgl2){
	exit("warning: Periode yang dimasukan Belum benar");
}
// ..proses (preview)
$pt=makeOption($dbname,'organisasi','kodeorganisasi,induk');
$nmPt=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
$optAkun=makeOption($dbname,'keu_5akun','noakun,namaakun');

if($proses=='excel') {
        $border="border=1";
        $bgcol="bgcolor=#CCCCCC";
} else {
        $border="border=0";
}

if (($proses == 'preview') or ($proses=='excel')) {
	if ($kdorg=='') {
		echo "Error : Organisasi tidak boleh kosong";
		exit;
	}
}


$thn=substr($tgl,0,4);//echo $thn;
$thnKm=$thn-1;
$tglAkhir="".$thnKm."1231";
#mengambil tanggalmulai dan tanggalsampai dari periode akutansi
$stgl="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where kodeorg='".$kdorg."' and periode='".$tgl."'";
$qtgl=mysql_query($stgl) or die(mysql_error($conn));
$rtgl=mysql_fetch_assoc($qtgl);
$stgl2="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where kodeorg='".$kdorg."' and periode='".$tgl2."'";
//echo $stgl2;
$qtgl2=mysql_query($stgl2) or die(mysql_error($conn));
$rtgl2=mysql_fetch_assoc($qtgl2);
$periode1=$tgl;
// $tgl=" and tanggal between '".$rtgl['tanggalmulai']."' and '".$rtgl2['tanggalsampai']."'";
$tgl=" and tanggal<='".$rtgl2['tanggalsampai']."'";
// ..start pilih data tipe piutang (query)
if ($noakun=='01') {
	
	// ..Q1 bensin
	$sAllOne = "select sum(a.debet) as jumlah,a.nik,a.kodeorg,b.namakaryawan,b.nik as nikdt,tanggal
				from ".$dbname.".keu_jurnaldt_vw a
				left join ".$dbname.".datakaryawan b on a.nik=b.karyawanid
				where kodeorg='".$kdorg."'
				and kodebarang='01000001'
				and noakun='1140200'
				".$tgl."
				and a.nik!='' group by a.nik";

	// ..Q2 bensin
	$tempNik = "";
	$sAllTwo = "select sum(a.kredit) as jumlah,a.nik,a.kodeorg,a.noreferensi,b.namakaryawan,b.nik as nikdt,tanggal
				from ".$dbname.".keu_jurnaldt_vw a
				left join ".$dbname.".datakaryawan b on a.nik=b.karyawanid
				where kodeorg='".$kdorg."'
				and noreferensi like '%ALK_POT%' and right(noreferensi,2)='46'
				and noakun='1140200' ".$tgl."
				and a.nik!='' group by a.nik order by tanggal desc";
	$sAllTwo2 = "select sum(a.kredit) as jumlah,a.nik,a.kodeorg,a.noreferensi,b.namakaryawan,b.nik as nikdt,tanggal,kodebarang
				from ".$dbname.".keu_jurnaldt_vw a
				left join ".$dbname.".datakaryawan b on a.nik=b.karyawanid
				where kodeorg!='' and noakun='1140200' and kodebarang='01000001' and kodeorg='".$kdorg."' ".$tgl."
				and a.nik!='' group by a.nik,a.kodebarang  having sum(a.kredit)!=0 order by tanggal desc";
} else if ((substr($noakun,0,2)=='02')||($noakun=='03')) {
	# code...
	// ..Q1 alat kerja
	if(substr($noakun,0,2)=='02'){
		$varArr = explode(".", $noakun);
		$whrid="";
		$ddlm="";
		if($varArr[1]!=''){
			$whrid= " and id='".$varArr[1]."'";
		}
		$sDet = "select name,id from ".$dbname.".sdm_ho_component where pinjamanid=1 ".$whrid."";
			$qDet=mysql_query($sDet) or die(mysql_error($conn));
			while($rDet=mysql_fetch_assoc($qDet)){
				//$nmdto=explode(" ",$rDet['name']);
				$lstDt[$rDet['id']]=$rDet['id'];
				//$lstNm[$rDet['id']]=$ddlm;
				$lstKet[$rDet['id']]=explode(" ",$rDet['name']);
			}
			foreach($lstKet as $rwId=>$isiRow){//mengganti kata pertama menjadi piutang
				foreach($isiRow as $lstKetid=>$lstname){
					if($lstKetid==0){
						$varnm="Piutang ";
					}else{
						$varnm.=" ".$lstname;	
					}
					
				}
				$lstKet[$rwId]=$varnm;
			}
			$Mule=1;
			$inKdBrg.="(";
			foreach($lstDt as $key){
				$sBrg="select distinct kodebarang from ".$dbname.".keu_5piutangbrgkary where id='".$key."' order by kodebarang";				
				$qBrg=mysql_query($sBrg) or die(mysql_error($conn));
				while($rBrg=mysql_fetch_assoc($qBrg)){
					$dtBrg[$rBrg['kodebarang']]+=1;
					if($Mule==1){
						$jnsId[$rBrg['kodebarang']]=$key;
						$inKdBrg.="'".$rBrg['kodebarang']."'";
						$Mule+=1;
					}else{
							$inKdBrg.=",'".$rBrg['kodebarang']."'";
							$jnsId[$rBrg['kodebarang']]=$key;
					}
				}
			}
			$inKdBrg.=")";
		//exit("error:".$nmdto[1]."___".$noakun);
        $whrin=" and kodebarang in ".$inKdBrg."";
		$whrjrn="and noreferensi like '%ALK_POT%' and right(noreferensi,2) in (select id from ".$dbname.".sdm_ho_component where pinjamanid=1 ".$whrid.") ";
		$whrAdd="and kodebarang in ".$inKdBrg."";
	}else{
        $sDet = "select replace(name,'Angsuran','Piutang') as name,id from ".$dbname.".sdm_ho_component where pinjamanid=1 ".$whrid."";
        $qDet=mysql_query($sDet) or die(mysql_error($conn));
        $rDet=mysql_fetch_assoc($qDet);
        $nmdto=explode(" ",$rDet['name']);
        $whrin=" and kodebarang=''";
		$whrjrn="and noreferensi like '%ALK_POT%' and right(noreferensi,2) in (select id from ".$dbname.".sdm_ho_component where pinjamanid=0 and name like '%angsuran%')";
		$whrAdd="and kodebarang in ".$inKdBrg."";
	}
	
	if($noakun=='03') {
		$sObat = "select sum(jumlah) as jumlah,nik from ".$dbname.".keu_jurnaldt  where kodeorg='".$kdorg."'
				and kodebarang like '030%'
				and nik!=''
				and noakun='1140200'
				 ".$tgl."
				group by nik";
		//echo $sObat;
		$qObat = mysql_query($sObat) or die(mysql_error());
		while($rObat = mysql_fetch_assoc($qObat)) {
			if($rObat['jumlah']<0){
				$rObat['jumlah']=$rObat['jumlah']*-1;
			}
			$dtRupOb[$rObat['nik']] = $rObat['jumlah'];
		}
	}
    $sAllOne = "select sum(a.debet) as jumlah,a.nik,a.kodeorg,b.namakaryawan,b.nik as nikdt,tanggal,kodebarang
				from ".$dbname.".keu_jurnaldt_vw a
				left join ".$dbname.".datakaryawan b on a.nik=b.karyawanid
				where kodeorg='".$kdorg."'
				and noakun='1140200'
				".$tgl." ".$whrin."
				and a.nik!='' group by a.nik,kodebarang";

	
	// ..Q2 alat kerja
	$tempNik = "";
	$sAllTwo = "select sum(a.kredit) as jumlah,a.nik,a.kodeorg,a.noreferensi,b.namakaryawan,b.nik as nikdt,tanggal,right(noreferensi,2) as jenis,kodebarang
				from ".$dbname.".keu_jurnaldt_vw a
				left join ".$dbname.".datakaryawan b on a.nik=b.karyawanid
				where kodeorg!='' ".$whrjrn." and kodeorg='".$kdorg."'
				".$tgl." and a.nik!='' group by a.nik,right(noreferensi,2)  order by tanggal desc";
	$sAllTwo2 = "select sum(a.kredit) as jumlah,a.nik,a.kodeorg,a.noreferensi,b.namakaryawan,b.nik as nikdt,tanggal,kodebarang
				from ".$dbname.".keu_jurnaldt_vw a
				left join ".$dbname.".datakaryawan b on a.nik=b.karyawanid
				where kodeorg!='' ".$whrAdd." and kodeorg='".$kdorg."' and nojurnal not like '%INVK%'
				".$tgl." and a.nik!='' group by a.nik,a.kodebarang   order by tanggal desc";
}
// ..end of pilih data tipe piutang(query)
	 
 
// ..q1
$qBensinOne = mysql_query($sAllOne) or die(mysql_error());
	while ($rBensinOne = mysql_fetch_assoc($qBensinOne)) {
		if($rBensinOne['kodeorg']!=$kdorg){
				continue;
			}
		$tdTgl = $rBensinOne['tanggal'];
		# mengambil data sumber hutang
		if (substr($noakun,0,2)=='02' || $noakun=='03') {
			$rBensinOne['jenis']=$jnsId[$rBensinOne['kodebarang']];
			$dafJenis[$rBensinOne['jenis']] = $rBensinOne['jenis'];
		}
                $tglDt=$rtgl2['tanggalsampai'];
		$diff =(strtotime($tglDt)-strtotime($tdTgl));
		        $outstd =floor(($diff)/(60*60*24));
		        $kel=0;
		        if(($outstd>=0)and($outstd<=30))
		            $kel=1;
		        if(($outstd>=31)and($outstd<=60))
		            $kel=2;
		        if(($outstd>=61)and($outstd<=90))
		            $kel=3;
		        if(($outstd>=91)and($outstd<=120))
		            $kel=4;
		        if($outstd>120)
		            $kel=5;

		if ($rBensinOne['jumlah']<0) {
			# jika minus di buat plus dulu
			$rBensinOne['jumlah']=$rBensinOne['jumlah']*1;
		}
                
		
		if ($rBensinOne['nik']!=$tempNik) {
			# code...
			$tempNik = $rBensinOne['nik'];
			$dafNik[$rBensinOne['nik']] = $rBensinOne['nik'];
			$dataNik[$rBensinOne['nik']] = $rBensinOne['nikdt'];
			$dataNmKary[$rBensinOne['nik']] = $rBensinOne['namakaryawan'];
			if ((substr($noakun,0,2)=='02')||($noakun=='03')) {
            	# code...
            		if($tempJenis!=$rBensinOne['jenis']){
            			$tempJenis=$rBensinOne['jenis'];
            		}
					$dtRow[$rBensinOne['nik']][$rBensinOne['jenis']]=$rBensinOne['jenis'];
					$dafName[$rBensinOne['nik'].$rBensinOne['jenis']] = $lstKet[$rBensinOne['jenis']];
					$dataRp[$rBensinOne['nik'].$rBensinOne['jenis']]+=$rBensinOne['jumlah'];
					$dataJum[$rBensinOne['nik'].$rBensinOne['jenis']][$kel]+=$rBensinOne['jumlah'];
					$dtPos[$rBensinOne['nik']]=$kel;
            }else{
            	$dataRp[$rBensinOne['nik']]+=$rBensinOne['jumlah'];
				$dataJum[$rBensinOne['nik']][$kel]+=$rBensinOne['jumlah'];
                $dtPos[$rBensinOne['nik']]=$kel;
				$dtRow[$rBensinOne['nik']][$rBensinOne['jenis']]=$rBensinOne['jenis'];
            }
		} else {
			if ((substr($noakun,0,2)=='02')||($noakun=='03')) {
					if($tempJenis!=$rBensinOne['jenis']){
						$tempJenis=$rBensinOne['jenis'];
						$dtRow[$rBensinOne['nik']][$rBensinOne['jenis']]=$rBensinTwo['jenis'];
					}
					$dafName[$rBensinOne['nik'].$rBensinOne['jenis']] = $lstKet[$rBensinOne['jenis']];
					$dataRp[$rBensinOne['nik'].$rBensinOne['jenis']]+=$rBensinOne['jumlah'];
					$dataJum[$rBensinOne['nik'].$rBensinOne['jenis']][$kel]+=$rBensinOne['jumlah'];
					$dtPos[$rBensinOne['nik']]=$kel;
					$dtPosPerJns[$rBensinOne['nik'].$rBensinOne['jenis']]=$kel;
					$dtRow[$rBensinOne['nik']][$rBensinOne['jenis']]=$rBensinOne['jenis'];
            }else{
					$dataRp[$rBensinOne['nik']]+=$rBensinOne['jumlah'];
					$dataJum[$rBensinOne['nik']][$kel]+=$rBensinOne['jumlah'];
					$dtPos[$rBensinOne['nik']]=$kel;
					$dtPosPerJns[$rBensinOne['nik'].$rBensinOne['jenis']]=$kel;
					$dtRow[$rBensinOne['nik']][$rBensinOne['jenis']]=$rBensinOne['jenis'];
            }
		}
	}
// ..q2
    $tempNik="";
	$qBensinTwo = mysql_query($sAllTwo) or die(mysql_error());
		while ($rBensinTwo = mysql_fetch_assoc($qBensinTwo)) {
			if($rBensinTwo['kodeorg']!=$kdorg){
				continue;
			}
		if ($rBensinTwo['jumlah']<0) {
			$rBensinTwo['jumlah'] = $rBensinTwo['jumlah']*-1;
		}
		
		
			if($rBensinTwo['kodebarang']!=''){
					$rBensinTwo['jenis']=$jnsId[$rBensinTwo['kodebarang']];
			}
			$dafJenis[$rBensinTwo['jenis']] = $rBensinTwo['jenis'];
		if ($tempNik!=$rBensinTwo['nik']) {
			# code...
			$tempNik = $rBensinTwo['nik'];

			if ((substr($noakun,0,2)=='02')||($noakun=='03')) {
				$kel=$dtPosPerJns[$rBensinTwo['nik'].$rBensinTwo['jenis']];
				$dataBayar[$rBensinTwo['nik'].$rBensinTwo['jenis']]+=$rBensinTwo['jumlah'];
				$dataJumByr[$rBensinTwo['nik'].$rBensinTwo['jenis']][$kel]+=$rBensinTwo['jumlah'];
				$dtPos[$rBensinTwo['nik'].$rBensinTwo['jenis']]=$kel;
                $dafNik[$rBensinTwo['nik']] = $rBensinTwo['nik'];
                $dataNik[$rBensinTwo['nik']] = $rBensinTwo['nikdt'];
                $dataNmKary[$rBensinTwo['nik']] = $rBensinTwo['namakaryawan'];
                $dtRow[$rBensinTwo['nik']][$rBensinTwo['jenis']]=$rBensinTwo['jenis'];
                $dafName[$rBensinTwo['nik'].$rBensinTwo['jenis']] = $lstKet[$rBensinTwo['jenis']];
			}else{
				$kel=$dtPosPerJns[$rBensinTwo['nik'].$rBensinTwo['jenis']];
				$dafNik[$rBensinTwo2['nik']] = $rBensinTwo2['nik'];
				$dataBayar[$rBensinTwo['nik']]+=$rBensinTwo['jumlah'];
				$dataJumByr[$rBensinTwo['nik']][$kel]+=$rBensinTwo['jumlah'];
				$dtPos[$rBensinTwo['nik']]=$kel;
			}

		} else {
			if ((substr($noakun,0,2)=='02')||($noakun=='03')) {
				# code...
				$kel=$dtPosPerJns[$rBensinTwo['nik'].$rBensinTwo['jenis']];
				$dataBayar[$rBensinTwo['nik'].$rBensinTwo['jenis']]+=$rBensinTwo['jumlah'];
				$dataJumByr[$rBensinTwo['nik'].$rBensinTwo['jenis']][$kel]+=$rBensinTwo['jumlah'];
				$dtPos[$rBensinTwo['nik'].$rBensinTwo['jenis']]=$kel;
			}else{
				$kel=$dtPosPerJns[$rBensinTwo['nik'].$rBensinTwo['jenis']];
				$dataBayar[$rBensinTwo['nik']]+=$rBensinTwo['jumlah'];
				$dtPos[$rBensinTwo['nik']]=$kel;
				$dataJumByr[$rBensinTwo['nik']][$kel]+=$rBensinTwo['jumlah'];	
			}
			
		}
}
	//echo $sAllTwo2;
	$tempNik="";
	$qBensinTwo2 = mysql_query($sAllTwo2) or die(mysql_error());
	while ($rBensinTwo2 = mysql_fetch_assoc($qBensinTwo2)) {
		
		if ($rBensinTwo2['jumlah']<0) {
			$rBensinTwo2['jumlah'] = $rBensinTwo2['jumlah']*-1;
		}
			
			if($rBensinTwo2['kodebarang']!=''){
					$rBensinTwo2['jenis']=$jnsId[$rBensinTwo2['kodebarang']];
			}
			$dafJenis[$rBensinTwo2['jenis']] = $rBensinTwo2['jenis'];
		if ($tempNik!=$rBensinTwo2['nik']) {
			# code...
			$tempNik = $rBensinTwo2['nik'];

			if ((substr($noakun,0,2)=='02')||($noakun=='03')) {
				if($rBensinTwo2['jenis']==''){
					continue;
				}
				$kel=$dtPosPerJns[$rBensinTwo2['nik'].$rBensinTwo2['jenis']];
				$dataBayar[$rBensinTwo2['nik'].$rBensinTwo2['jenis']]+=$rBensinTwo2['jumlah'];
				$dataJumByr[$rBensinTwo2['nik'].$rBensinTwo2['jenis']][$kel]+=$rBensinTwo2['jumlah'];
				$dtPos[$rBensinTwo2['nik'].$rBensinTwo2['jenis']]=$kel;
                                $dafNik[$rBensinTwo2['nik']] = $rBensinTwo2['nik'];
                                $dataNik[$rBensinTwo2['nik']] = $rBensinTwo2['nikdt'];
                                $dataNmKary[$rBensinTwo2['nik']] = $rBensinTwo2['namakaryawan'];
                                $dtRow[$rBensinTwo2['nik']][$rBensinTwo2['jenis']]=$rBensinTwo2['jenis'];
                                $dafName[$rBensinTwo2['nik'].$rBensinTwo2['jenis']]= $lstKet[$rBensinTwo2['jenis']];
			} else {
				$kel=$dtPosPerJns[$rBensinTwo2['nik'].$rBensinTwo2['jenis']];
				$dataBayar[$rBensinTwo2['nik']]+=$rBensinTwo2['jumlah'];
				$dataJumByr[$rBensinTwo2['nik']][$kel]+=$rBensinTwo2['jumlah'];
				$dtPos[$rBensinTwo2['nik']]=$kel;
				$dafNik[$rBensinTwo2['nik']] = $rBensinTwo2['nik'];
			}

		} else {
			if ((substr($noakun,0,2)=='02')||($noakun=='03')) {
				if($rBensinTwo2['jenis']==''){
					continue;
				}
				# code...
				$kel=$dtPosPerJns[$rBensinTwo2['nik'].$rBensinTwo2['jenis']];
				$dataBayar[$rBensinTwo2['nik'].$rBensinTwo2['jenis']]+=$rBensinTwo2['jumlah'];
				$dataJumByr[$rBensinTwo2['nik'].$rBensinTwo2['jenis']][$kel]+=$rBensinTwo2['jumlah'];
				$dtPos[$rBensinTwo2['nik'].$rBensinTwo2['jenis']]=$kel;
			}else{
				$kel=$dtPosPerJns[$rBensinTwo2['nik'].$rBensinTwo2['jenis']];
				$dataBayar[$rBensinTwo2['nik']]+=$rBensinTwo2['jumlah'];
				$dtPos[$rBensinTwo2['nik']]=$kel;
				$dataJumByr[$rBensinTwo2['nik']][$kel]+=$rBensinTwo2['jumlah'];	
			}
			
		}
	}



$stream="<table>
            <tr>
                    <td colspan=5><b>".$nmPt[$pt[$kdorg]]."</b></td>
            </tr>
            <tr>
                    <td></td>
            </tr>
            <tr>
                    <td><b>AGING SCHEDULE</b></td>
            </tr>
            <tr>
                    <td><b>".strtoupper($optAkun[$noakun])." </b></td>
            </tr>
            <tr>
                    <td><b>".$rtgl['tanggalmulai']." s.d ".$rtgl2['tanggalsampai']."</b></td>
            </tr>
            <tr>
                    <td></td>
            </tr>
		</table>";

	// ..get "judul - Saldo Per tgl/bln/thn" (test array)
		//$ArTgl = list($tgls, $bln, $thn) = explode("-", $_POST['tgl']);
		$SaldoPer = "s.d ".$rtgl2['tanggalsampai']; //$rtgl['tanggalmulai']." s.d ".$rtgl2['tanggalsampai'];
		

$stream.="<table cellspacing='1' ".$border."  class='sortable'>
			<thead>
	          	<tr class=rowheader>
	                <td rowspan=2 ".$bgcol." align=center><b>".$_SESSION['lang']['nik']." ".$_SESSION['lang']['karyawan']."</b></td>
	                <td rowspan=2 ".$bgcol." align=center><b>".$_SESSION['lang']['namakaryawan']."</b></td>
	                <td rowspan=2 ".$bgcol." align=center><b>".$_SESSION['lang']['saldo']." ".$_SESSION['lang']['per']." ".$SaldoPer."</b></td>
	                <td rowspan=2 ".$bgcol." align=center><b>".$_SESSION['lang']['dibayar']."</b></td>
	                <td rowspan=2 ".$bgcol." align=center><b>".$_SESSION['lang']['sisa']."</b></td>
	                <td colspan=5 ".$bgcol." align=center><b>".$_SESSION['lang']['umur']."</b></td>
	                <td rowspan=2 align=center ".$bgcol."><b>".$_SESSION['lang']['keterangan']."</b></td>";
	
	  $stream.="</tr>
	          	<tr>
	                <td ".$bgcol."><b>0-30 Hari</b></td>
	                <td align=center ".$bgcol."><b>31-60 Hari</b></td>
	                <td align=center ".$bgcol."><b>61-90 Hari</b></td>
	                <td align=center ".$bgcol."><b>90-120 Hari</b></td>
	                <td align=center ".$bgcol."><b>120+ Hari</b></td>
	  			</tr>
			</thead>
			<tbody>";

// ..arr

        // ..
	if(empty($dafNik)){
		exit("warning: Data kosong");
	}
	array_multisort($dafNik,SORT_ASC);
	if($noakun=='01') {
	
		foreach ($dafNik as $KaryId) {
	# code...
			if ($dataNik[$KaryId]!='') {
				# code...
				$ar1=1;$ar2=2;$ar3=3;$ar4=4;$ar5=5;
				
				$stream.= "<tr class=rowcontent>";
					if ($KaryId!=$tempId) {
						$tempId=$KaryId;
						# code...
						$stream.= "<td align=left>'".$dataNik[$KaryId]."</td>
									<td align=left>".$dataNmKary[$KaryId]."</td>";
									$tempId=$KaryId;
			                    	$tmpl=false;
			                    	$aret=1;
					} else {
						if ($tmpl == false) {
							# code...
							$tmpl = true;
							$stream.= "<td align=left rowspan=".(count($dtRow[$KaryId]-1)).">&nbsp;</td>
			                            <td align=left rowspan=".(count($dtRow[$KaryId]-1)).">&nbsp;</td>";
						}
						$aret+=1;
					}
				   		$jumlah[$KaryId]=$dataJum[$KaryId];
						if ($dataBayar[$KaryId]!=0) {
							$jumlah[$KaryId]=$dataBayar[$KaryId];
						}




				   	// ..jumlah saldo arr
						$stream.="<td align=right>".number_format($dataRp[$KaryId],0)."</td>";

						$sisaDt[$KaryId] = $dataRp[$KaryId]-$dataBayar[$KaryId];
						// ..update 
						$stream.= "<td align=right>".number_format($dataBayar[$KaryId],0)."</td>
									<td align=right>".number_format($sisaDt[$KaryId],0)."</td>";	
						// ..
						$totalBayarDt+= $dataBayar[$KaryId]; 
                        $totalSisaDt+= $sisaDt[$KaryId];

	                    for($af=1;$af<6;$af++){
	                    	unset($dataJum);
                                if($af==$dtPos[$KaryId]){
                                    $dataJum[$af] = $sisaDt[$KaryId];
                                    if($dataJum[$af]==0){
                                        $dataJum[$af]=$dataRp[$KaryId];
                                    }
                                }
	                    	$stream.= "<td align=right>".number_format(($dataJum[$af]),0)."</td>";
							$colAr[$af]+= $dataJum[$af];
	                    }   
						$jumDt+= round($dataRp[$KaryId],0);
						$stream.= "<td align=left>Piutang Bensin</td>";
						$stream.="</tr>";
					}
			}
        }
        if((substr($noakun,0,2)=='02')||($noakun=='03')){
            foreach ($dafNik as $KaryId) {
                    foreach ($dafJenis as $kJenis) {
                            if (($dataRp[$KaryId.$kJenis]!='')||($dataBayar[$KaryId.$kJenis]!='')){
                                    $ar1=1;$ar2=2;$ar3=3;$ar4=4;$ar5=5;
                                    $no+=1;
                                    if($KaryId!=$tempId2) {
                                    	 $tempId2=$KaryId;
                                    	 $htngsbb=1;
                                    }else{
                                    	$htngsbb+=1;
                                    }
                                    
                                 
                                    $stream.= "<tr class=rowcontent>";
                                            if ($KaryId!=$tempId) {
                                                    $tempId=$KaryId;
                                                    # code...
                                                    $stream.= "<td align=left>'".$dataNik[$KaryId]."</td>
                                                                <td align=left>".$dataNmKary[$KaryId]."</td>";
                                                                $tempId=$KaryId;
                                                    $tmpl=false;
                                                    $aret=1;
                                                   
                                            } else {
                                                    if ($tmpl == false) {
                                                            $tmpl = true;
                                                            $stream.= "<td align=left rowspan=".(count($dtRow[$KaryId])-1).">&nbsp;</td>
                                                        				<td align=left rowspan=".(count($dtRow[$KaryId])-1).">&nbsp;</td>";
                                                    }
                                                    $aret+=1;
                                            }
                                                    $jumlah[$KaryId]=$dataJum[$KaryId];
                                                    if ($dataBayar[$KaryId]!=0) {
                                                            $jumlah[$KaryId]=$dataBayar[$KaryId];
                                                    }
                                            // ..jumlah saldo arr
													
													// ..
													if($kJenis==25){
														if($dtRupOb[$KaryId]!=0){
															$dataRp[$KaryId.$kJenis]=$dataRp[$KaryId.$kJenis]+$dtRupOb[$KaryId]; #update 27/01/2015
															$dafName[$KaryId.$kJenis]=$dafName[$KaryId.$kJenis]."+ Pengeluaran Gudang ";	
														}
													}
													// ..
													
                                                    $stream.="<td align=right>".number_format($dataRp[$KaryId.$kJenis],0)."</td>";

                                                    $sisaDt = $dataRp[$KaryId.$kJenis]-$dataBayar[$KaryId.$kJenis];
                                                    // .. update
                                                    $stream.= "<td align=right>-".number_format($dataBayar[$KaryId.$kJenis],0)."</td>
                                                                <td align=right>".number_format($sisaDt,0)."</td>";
                                                    // ..
                                                    $totalBayarDt+= $dataBayar[$KaryId.$kJenis]; 
                                                    $totalSisaDt+= $sisaDt;

                                                    for($af=1;$af<6;$af++){
                                                    	unset($dataJum);
                                                    	$dataJum[$dtPos[$KaryId.$kJenis]] = $dataRp[$KaryId.$kJenis]-$dataBayar[$KaryId.$kJenis];
                                                    	if($dataJum[$dtPos[$KaryId.$kJenis]]==0){
                                                    		$dataJum[$dtPos[$KaryId.$kJenis]]=$dataBayar[$KaryId.$kJenis];
                                                    	}
                                                        $stream.= "<td align=right>".number_format(($dataJum[$af]),0)."</td>";
                                                                                $colAr[$af]+= $dataJum[$af];
                                                    }
                                                    $jumDt+= $dataRp[$KaryId.$kJenis];   
						    							$stream.= "<td align=left>".$dataNik[$KaryId]."__".$dataNmKary[$KaryId]."__".$dafName[$KaryId.$kJenis]."</td>";
                                                    $stream.="</tr>";
                                                    $subNil[$KaryId]+=$dataRp[$KaryId.$kJenis];
                                                    $subNilByr[$KaryId]+=$dataBayar[$KaryId.$kJenis];
                                                    $subNilSisa[$KaryId]+=$sisaDt;
                                                    if(count($dtRow[$KaryId])==$htngsbb){
				                                    	$stream.= "<tr>";
				                                    	$stream.= "<td align=left><b>'".$dataNik[$KaryId]."</b></td>
                                                                <td align=left><b>".$_SESSION['lang']['subtotal']." ".$dataNmKary[$KaryId]."</b></td>";
                                                        $stream.="<td align=right><b>".number_format($subNil[$KaryId],0)."</b></td>";
                                                        $stream.="<td align=right><b>-".number_format($subNilByr[$KaryId],0)."</b></td>";
                                                        $stream.="<td align=right><b>".number_format($subNilSisa[$KaryId],0)."</b></td>";
                                                        $stream.= "<td align=left colspan=6>&nbsp;</td>";
				                                    	$stream.= "</tr>";
				                                    }
                                                    
                            //}
					}
			}
                }
        }    
$stream.="		<tr>
					<td bgcolor=#CCCCCC></td>
					<td align=left bgcolor=#CCCCCC><b>Jumlah</b></td>
					<td align=right bgcolor=#CCCCCC><b>".number_format($jumDt,0)."</b></td>
					<td align=right bgcolor=#CCCCCC><b>-".number_format($totalBayarDt,0)."</b></td>
					<td align=right bgcolor=#CCCCCC><b>".number_format($totalSisaDt,0)."</b></td>";
					for($af=1;$af<6;$af++){
                    	$stream.= "<td align=right bgcolor=#CCCCCC>".number_format(($colAr[$af]),0)."</td>";
                    } 
$stream.= "<td align=left bgcolor=#CCCCCC>&nbsp;</td>";
	$stream.="</tr>
			</tbody>
		</table>";

$stream.="<table>
                <tr>
                        <td colspan=2 align=center>Dibuat:</td>
                        <td colspan=5></td>
                        <td colspan=2 align=center>Diperiksa:</td>
                        <td colspan=5></td>
                        <td colspan=2 align=center>Mengetahui:</td>
                </tr>";

                for($i=1;$i<=5;$i++); {
                        $stream.="<tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan=3></td>
                                <td colspan=2></td>
                                </tr>";
                }

                // ..option untuk panggil nama pembuat dan pemeriksa
                $nmKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');

                $stream.="<tr>
                        <td colspan=2>".$nmKar[$dibuat]."</td>
                        <td colspan=5></td>
                        <td colspan=2>".$nmKar[$diperiksa]."</td>
                        <td colspan=5></td>
                        <td colspan=2>Accounting Manager</td>
                </tr>

</table>";	

switch ($proses) {
	case 'preview':
		echo $stream;
		break;

	case 'excel':
                //$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
                $tglSkrg=date("Ymd");
                $nop_="Laporan_Aging_Pinjaman".$tglSkrg;
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
}

?>