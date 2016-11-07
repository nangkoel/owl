<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

// ..POST - GET
$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
$_POST['divisi']==''?$unitDt=$_GET['divisi']:$unitDt=$_POST['divisi'];
$_POST['gudang']==''?$gudang=$_GET['gudang']:$gudang=$_POST['gudang'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['klmpkBrg']==''?$klmpkBrg=$_GET['klmpkBrg']:$klmpkBrg=$_POST['klmpkBrg'];
$_POST['kdBrg']==''?$kdBrg=$_GET['kdBrg']:$kdBrg=$_POST['kdBrg'];

if(($proses=='preview')||($proses=='excel')){
	$bgwarna=" ";
	$brd=0;
	if($proses=='excel'){
		$bgwarna=" bgcolor=#DEDEDE";
		$brd=1;
	}
	$tab.="<table class=sortable cellspacing=1 border=".$brd." width=100%>
				<thead>
					<tr>
						<td align=center ".$bgwarna." rowspan=2>No.</td>
						<td align=center ".$bgwarna." rowspan=2>".$_SESSION['lang']['gudang']."</td>
						<td align=center ".$bgwarna." rowspan=2>".$_SESSION['lang']['notransaksi']."</td>
						<td align=center  ".$bgwarna." rowspan=2>".$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['transaksi']."</td>
						<td align=center  ".$bgwarna." rowspan=2>".$_SESSION['lang']['tipetransaksi']."</td>
						<td align=center  ".$bgwarna." rowspan=2>".$_SESSION['lang']['untukunit']." Unit</td>
						<td align=center  ".$bgwarna." rowspan=2>".$_SESSION['lang']['gudang']." x</td>
						<td align=center  ".$bgwarna." rowspan=2>".$_SESSION['lang']['notransaksi']." Referensi</td>
						<td align=center  ".$bgwarna." rowspan=2>".$_SESSION['lang']['kodebarang']."</td>
						<td align=center  ".$bgwarna." rowspan=2>".$_SESSION['lang']['namabarang']."</td>
						<td align=center  ".$bgwarna." colspan=2>Qty Issue</td>
						<td align=center  ".$bgwarna." rowspan=2>".$_SESSION['lang']['nik']."</td>
						<td align=center  ".$bgwarna." rowspan=2>".$_SESSION['lang']['nama']."</td>
						<td align=center  ".$bgwarna." rowspan=2>".$_SESSION['lang']['kodevhc']."</td>
						<td align=center  ".$bgwarna." rowspan=2>".$_SESSION['lang']['keterangan']." Pemakai (Alat, dll)</td>
					</tr>
					<tr>
						<td align=center  ".$bgwarna.">".$_SESSION['lang']['masuk']."</td>
						<td align=center  ".$bgwarna.">".$_SESSION['lang']['keluar']."</td>
					</tr>
				</thead>
				<tbody>";
				if ($klmpkBrg!='') {
       				 $kondisi = " and left (kodebarang,3)='".$klmpkBrg."' ";
      			}

			      if ($kdBrg!='') {
			        $kondisi = " and kodebarang='".$kdBrg."' ";
			      }

			      $sTanggal = "select tanggalmulai,tanggalsampai 
			                  from ".$dbname.".setup_periodeakuntansi
			                  where kodeorg in (select kodeunit 
						from ".$dbname.".bgt_regional_assignment 
						where regional='".$unitDt."')
			                  and periode='".$periode."'";
			      $qTgl = mysql_query($sTanggal) or die(mysql_error());
			      $twanggal=mysql_fetch_assoc($qTgl);
			      $tglmulai = $twanggal['tanggalmulai'];
			      $tglsampai = $twanggal['tanggalsampai']; 

			      $sPrew = "select * from ".$dbname.".log_transaksi_vw
			                where left(kodegudang,4) 
			                in (select kodeunit from ".$dbname.".bgt_regional_assignment 
								where regional='".$unitDt."')
			                and tanggal between '".$tglmulai."'
			                and '".$tglsampai."' ".$kondisi." 
			                order by kodegudang,tanggal"; 
			      $qPrew = mysql_query($sPrew) or die(mysql_error());
			      while ($rPrew = mysql_fetch_assoc($qPrew)) {
                                  switch ($rPrew['tipetransaksi']):
                                      case 1:$tipetrx="Masuk";break;
                                      case 2:$tipetrx="Retur";break;
                                      case 3:$tipetrx="Terima Mutasi";break;
                                      case 5:$tipetrx="Keluar";break;
                                      case 7:$tipetrx="Mutasi";break;
                                  endswitch;
			      	$no+=1;
			      	$tab.="<tr class=rowcontent>";
					$tab.="<td align=center>".$no."</td>";
					// ..panggil nama gudang
					$wGdg = "kodeorganisasi='".$rPrew['kodegudang']."'";
					$optGDG = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi', $wGdg);
					if ($optGDG[$rPrew['kodegudang']]!='') {
						$rPrew['kodegudang']=$optGDG[$rPrew['kodegudang']];
					}
					$tab.="<td>".$rPrew['kodegudang']."</td>";
					# ..end of panggil nama gudang
					$tab.="<td>".$rPrew['notransaksi']."</td>";
					$tab.="<td>".$rPrew['tanggal']."</td>";
					$tab.="<td>".$tipetrx."</td>";
					$tab.="<td>".$rPrew['untukunit']."</td>";
					// ..panggil nama gudang x
					$wGdgx = "kodeorganisasi='".$rPrew['gudangx']."'";
					$optGDGx = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi', $wGdgx);
					if ($optGDGx[$rPrew['gudangx']]!='') {
						$rPrew['gudangx']=$optGDGx[$rPrew['gudangx']];
					}
					$tab.="<td>".$rPrew['gudangx']."</td>";
					# ..end of panggil nama gudang
					$tab.="<td>".$rPrew['notransaksireferensi']."</td>";
					$tab.="<td>'".$rPrew['kodebarang']."</td>";
					$whrBrg="kodebarang='".$rPrew['kodebarang']."'";
					$optNmBrg=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang',$whrBrg);
					
					$tab.="<td>".$optNmBrg[$rPrew['kodebarang']]."</td>";
					if($rPrew['tipetransaksi']<4){
						$tab.="<td align=right>".number_format($rPrew['jumlah'],2)."</td>";
						$tab.="<td align=right>0</td>";	
					}else{
						$tab.="<td align=right>0</td>";	
						$tab.="<td align=right>".number_format($rPrew['jumlah'],2)."</td>";
					}
					
					// ..menentukan namakaryawan nya namapenerima
					$whrKar="karyawanid='".$rPrew['namapenerima']."'";
					$optNm=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',$whrKar);
					if($optNm[$rPrew['namapenerima']]!=''){
						$rPrew['namapenerima']= $optNm[$rPrew['namapenerima']];
					}
					// ..menentukan nik namapenerima
					$niknip="";
					$whrKar1="namakaryawan='".$rPrew['namapenerima']."'";
					$optNm1=makeOption($dbname,'datakaryawan','namakaryawan,nik',$whrKar1);
					if($optNm1[$rPrew['namapenerima']]!=''){
						$niknip="'".$optNm1[$rPrew['namapenerima']];
					}

					$tab.="<td>".$niknip."</td>";
					$tab.="<td>".$rPrew['namapenerima']."</td>";
					$tab.="<td>".$rPrew['kodemesin']."</td>";
					$tab.="<td>".$rPrew['keterangan']."</td>";
					$tab.="</tr>";
			      }
		$tab.="</tbody></table>";
}
switch ($proses) {
	case 'getAll':
		if (isset($_POST['unitDt'])) {
			$optGdng = "<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		    $sGdng    = "select kodeorganisasi,namaorganisasi 
		                from ".$dbname.".organisasi 
		                where induk='".$unitDt."' 
		                and tipe like 'GUDANG%' 
		                order by namaorganisasi";
		    $qGdng   = mysql_query($sGdng) or die(mysql_error());
		    while ($rGdng = mysql_fetch_assoc($qGdng)) {
		      $optGdng.= "<option value='".$rGdng['kodeorganisasi']."'>".$rGdng['namaorganisasi']."</option>";
		    }
		    echo $optGdng;
		}

		if (isset($_POST['gudang'])) {
			$optPriod 	= "<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	    	$sPriod    	= "select periode 
	                  		from ".$dbname.".setup_periodeakuntansi
	                  		where kodeorg='".$gudang."' ";
		    $qPriod   = mysql_query($sPriod) or die(mysql_error());
		    while ($rPriod = mysql_fetch_assoc($qPriod)) {
		      $optPriod.= "<option value='".$rPriod['periode']."'>".$rPriod['periode']."</option>";
		    }
		    echo $optPriod;
		}

		if (isset($_POST['klmpkBrg'])) {
			$optKdBrg = "<option value=''>".$_SESSION['lang']['all']."</option>";
		    $sKdBrg   = "select distinct kodebarang,namabarang
		                  from ".$dbname.".log_5masterbarang 
		                  where left (kodebarang,3)='".$klmpkBrg."'";
		    $qKdBrg   = mysql_query($sKdBrg) or die(mysql_error());
		    while ($rKdBrg = mysql_fetch_assoc($qKdBrg)) {
		      $optKdBrg.= "<option value='".$rKdBrg['kodebarang']."'>".$rKdBrg['namabarang']."</option>";
		    }
		    echo $optKdBrg;
		}
    	break;
	
	case 'preview':
		 echo $tab;
		break;
	case'excel':
		$thisDate=date("YmdHms");
                        //$nop_="Laporan_Pembelian";
        $nop_="Laporan_Gabungan_Brg_".$thisDate;
         $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
         gzwrite($gztralala, $tab);
         gzclose($gztralala);
         echo "<script language=javascript1.2>
            window.location='tempExcel/".$nop_.".xls.gz';
            </script>";
      
	break;
}
?>