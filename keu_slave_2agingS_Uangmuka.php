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
$tgl=tanggalsystem($_POST['tgl']);

if($proses=='excel') {
        $kdorg=$_GET['kdorg'];
        $noakun=$_GET['noakun'];
        $tgl=tanggalsystem($_GET['tgl']);
        $dibuat=$_GET['dibuat'];
        $diperiksa=$_GET['diperiksa'];
        $pilId=$_GET['pilId'];
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
                    <td><b>PER ".tanggalnormal($tgl)." </b></td>
            </tr>
            <tr>
                    <td></td>
            </tr>
		</table>";

$stream.="<table cellspacing='1' ".$border."  class='sortable'>
			<thead>
	          	<tr class=rowheader>
	                <td rowspan=2 ".$bgcol." align=center><b>Nik Karyawan</b></td>
	                <td rowspan=2 align=center  ".$bgcol." align=center><b>Nama</b></td>
	                <td rowspan=2 ".$bgcol." align=center><b>".$_SESSION['lang']['keterangan']."</b></td>
	                <td colspan=4 align=center  ".$bgcol." align=center><b>Pengambilan</b></td>
	                <td colspan=3 align=center  ".$bgcol." align=center><b>Pertanggungjawaban</b></td>
	                <td rowspan=2 ".$bgcol." align=center><b>Adv. Belum Selesai s/d Hari Ini</b></td>
	                <td colspan=5 ".$bgcol." align=center><b>Umur Piutang Thn Berjalan</b></td>
	          	</tr>
	          	<tr>
	          		<td  ".$bgcol." align=center><b>".$_SESSION['lang']['nobayar']."</b></td>
	                <td  ".$bgcol." align=center>Tanggal</td>
	                <td  ".$bgcol." align=center><b>".$_SESSION['lang']['jatuhtempo']."</b></td>
	           		<td  ".$bgcol." align=center><b>".$_SESSION['lang']['jumlahhari']."</b></td>
	           		<td  ".$bgcol." align=center><b>".$_SESSION['lang']['jumlah']."</b></td>
	                <td  ".$bgcol." align=center>Ref</td>
	                <td  ".$bgcol." align=center><b>".$_SESSION['lang']['tanggal']."</b></td>
	                <td ".$bgcol."><b>0-30 Hari</b></td>
	                <td align=center ".$bgcol."><b>31-60 Hari</b></td>
	                <td align=center ".$bgcol."><b>61-90 Hari</b></td>
	                <td align=center ".$bgcol."><b>90-120 Hari</b></td>
	                <td align=center ".$bgcol."><b>120+ Hari</b></td>
	          	</tr>
			</thead>
			<tbody>";

		$sOne = "select a.jumlah as jumlah,b.tanggal,b.nobayar,b.keterangan,a.nik,c.namakaryawan,a.notransaksi,c.nik as nikdt
				from ".$dbname.".keu_kasbankdt a
				left join ".$dbname.".keu_kasbankht b on a.notransaksi=b.notransaksi
				left join ".$dbname.".datakaryawan c on a.nik=c.karyawanid
				where a.noakun='".$noakun."'
				and b.tanggal<='".$tgl."'
				and a.kodeorg='".$kdorg."'
				and b.posting=1
				and (a.notransaksi_adv='' or a.notransaksi_adv is null) 
				order by a.notransaksi asc";
		#kasus - muncul nobayar yang kosong
				// tambah posting=1
				// nobayar!=''
		//echo $sOne;
		//exit("Error: ".$sOne);
		$qOne = mysql_query($sOne) or die(mysql_error());
		while ($rOne = mysql_fetch_assoc($qOne)) {
				$diff =(strtotime($tgl)-strtotime($rOne['tanggal']));
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

		    if ($rOne['jumlah']<0) {
					$rOne['jumlah']=$rOne['jumlah']*-1;
		    }

			if($rOne['notransaksi']!=$tempTrans){
				$tempTrans=$rOne['notransaksi'];
				$dafNik[$rOne['nik']]=$rOne['nik'];
				$dafTrans[$rOne['notransaksi']]=$rOne['notransaksi'];
				$dataTrans[$rOne['nik'].$rOne['notransaksi']]=$rOne['notransaksi'];
				$dataKet[$rOne['nik'].$rOne['notransaksi']]=$rOne['keterangan'];
				$dataNik[$rOne['nik']]=$rOne['nikdt'];
				$dataNmKar[$rOne['nik']]=$rOne['namakaryawan'];
				$dataTgl[$rOne['nik'].$rOne['notransaksi']]=$rOne['tanggal'];
				$dataNb[$rOne['nik'].$rOne['notransaksi']]=$rOne['nobayar'];
				
				$dtRow[$rOne['nik']]+=1;
				
				$dataRp[$rOne['nik'].$rOne['notransaksi']][$kel]+=$rOne['jumlah'];
				$dataJum[$rOne['nik'].$rOne['notransaksi']]+=$rOne['jumlah'];
			}else{
				$dataRp[$rOne['nik'].$rOne['notransaksi']][$kel]+=$rOne['jumlah'];
				$dataJum[$rOne['nik'].$rOne['notransaksi']]+=$rOne['jumlah'];
			}
		}

		// ..
		$tempTrans="";
		$sTwo = "select a.jumlah as jumlah,b.tanggal,b.nobayar,a.nik,c.namakaryawan,a.notransaksi,c.nik as nikdt,notransaksi_adv
				from ".$dbname.".keu_kasbankdt a
				left join ".$dbname.".keu_kasbankht b on a.notransaksi=b.notransaksi
				left join ".$dbname.".datakaryawan c on a.nik=c.karyawanid
				where a.noakun='".$noakun."' 
				and b.tanggal<='".$tgl."'
				and (a.notransaksi_adv!='' or a.notransaksi_adv is not null) 
				and b.kodeorg='".$kdorg."'
				order by a.notransaksi asc";

		// ..tambahin tanggal awal & tanggal akhir di aging uang muka
				
		$qTwo = mysql_query($sTwo) or die(mysql_error());
		while ($rTwo = mysql_fetch_assoc($qTwo)) {
				$diff =(strtotime($tgl)-strtotime($rTwo['tanggal']));
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
			if ($rTwo['jumlah']<0) {
					$rTwo['jumlah']=$rTwo['jumlah']*-1;
		    }
		    //$notran[$rTwo['notransaksi_adv']]=$rTwo['notransaksi_adv'];

			if($rTwo['notransaksi_adv']!=$tempTrans){
				$tempTrans=$rTwo['notransaksi_adv'];
				$dataBTgl[$rTwo['nik'].$rTwo['notransaksi_adv']]=$rTwo['tanggal'];
				$dataRef[$rTwo['nik'].$rTwo['notransaksi_adv']]=$rTwo['nobayar'];
				$dataBayar[$rTwo['nik'].$rTwo['notransaksi_adv']]+=$rTwo['jumlah'];
				$dataJumByr[$rTwo['nik'].$rTwo['notransaksi_adv']][$kel]+=$rTwo['jumlah'];
			}else{
				$dataBayar[$rTwo['nik'].$rTwo['notransaksi_adv']]+=$rTwo['jumlah'];
				$dataJumByr[$rTwo['nik'].$rTwo['notransaksi_adv']][$kel]+=$rTwo['jumlah'];
			}
		}

		array_multisort($dafNik,sort_asc);
		if ($pilId!=0) {
			# code...
			foreach ($dafNik as $KaryiD) {
				foreach ($dafTrans as $noTr) {
					if ($dataTrans[$KaryiD.$noTr]!='') {
						if(($dataJum[$KaryiD.$noTr]-$dataBayar[$KaryiD.$noTr])!=0){
							$dtRow[$KaryiD]-=1;
							unset($dataTrans[$KaryiD.$noTr]);
						}
					}
				}
			}
		} else {
			foreach ($dafNik as $KaryiD) {
				foreach ($dafTrans as $noTr) {
					if ($dataTrans[$KaryiD.$noTr]!='') {
						if(($dataJum[$KaryiD.$noTr]-$dataBayar[$KaryiD.$noTr])==0){
							$dtRow[$KaryiD]-=1;
							unset($dataTrans[$KaryiD.$noTr]);
						}
					}
				}
			}
		}

		foreach ($dafNik as $KaryiD) {
			# code...
			foreach ($dafTrans as $noTr) {
				if ($dataTrans[$KaryiD.$noTr]!='') {
					$stream.="<tr class=rowcontent>";
								if($KaryiD!=$tempId){
						$stream.="<td align=left>".$dataNik[$KaryiD]."</td>
			                       <td align=left>".$dataNmKar[$KaryiD]."</td>";
			                    	$tempId=$KaryiD;
			                    	$tmpl=false;
			                    	$aret=1;
								}else{
									if($tmpl==false){
										$tmpl=true;
							   $stream.="<td align=left rowspan=".($dtRow[$KaryiD]-1).">&nbsp;</td>
			                             <td align=left rowspan=".($dtRow[$KaryiD]-1).">&nbsp;</td>";
									}
									$aret+=1;
								}
								$ar1=1;$ar2=2;$ar3=3;$ar4=4;$ar5=5;
			                    $stream.="
					                    <td align=left>".$dataKet[$KaryiD.$noTr]."</td>
					                    <td align=left>".$dataNb[$KaryiD.$noTr]."</td>
					                    <td align=left>".$dataTgl[$KaryiD.$noTr]."</td>";
					            
					            $jatuhtempo[$KaryiD.$noTr] = nambahHari($dataTgl[$KaryiD.$noTr],7,1);
					            $jumlah[$KaryiD.$noTr]=$dataJum[$KaryiD.$noTr];
					            /*echo "<pre>";
					            	print_r($jumlah);
					            echo "</pre>";*/
								if ($dataBayar[$KaryiD.$noTr]!=0) {
									$jumlah[$KaryiD.$noTr]=$dataBayar[$KaryiD.$noTr];
								}
					            
					            if($dataBTgl[$KaryiD.$noTr]!=''){
					            	$diff =(strtotime($dataBTgl[$KaryiD.$noTr])-strtotime($dataTgl[$KaryiD.$noTr]));
					            }else{
					            	$diff =(strtotime($tgl)-strtotime($dataTgl[$KaryiD.$noTr]));
					            }
					            $telat[$KaryiD.$noTr] =floor(($diff)/(60*60*24));
					            $stream.="<td align=left>".$jatuhtempo[$KaryiD.$noTr]."</td>
					            		<td align=right>".$telat[$KaryiD.$noTr]."</td>
					            		<td align=right>".number_format($jumlah[$KaryiD.$noTr],2)."</td>
					            		<td align=left>".$dataRef[$KaryiD.$noTr]."</td>
					            		<td align=left>".$dataBTgl[$KaryiD.$noTr]."</td>";
					            $stream.="<td align=right>".number_format(($dataJum[$KaryiD.$noTr]-$dataBayar[$KaryiD.$noTr]),2)."</td>";
					                    ($dataRp[$KaryiD.$noTr][$ar1]-$dataBayar[$KaryiD.$noTr])==0?$dataRp[$KaryiD.$noTr][$ar1]=$dataJumByr[$KaryiD.$noTr][$ar1]:$dataRp[$KaryiD.$noTr][$ar1]-$dataJumByr[$KaryiD.$noTr][$ar1];
					                    ($dataRp[$KaryiD.$noTr][$ar2]-$dataBayar[$KaryiD.$noTr])==0?$dataRp[$KaryiD.$noTr][$ar2]=$dataJumByr[$KaryiD.$noTr][$ar2]:$dataRp[$KaryiD.$noTr][$ar2]-$dataJumByr[$KaryiD.$noTr][$ar2];
					                    ($dataRp[$KaryiD.$noTr][$ar3]-$dataBayar[$KaryiD.$noTr])==0?$dataRp[$KaryiD.$noTr][$ar3]=$dataJumByr[$KaryiD.$noTr][$ar3]:$dataRp[$KaryiD.$noTr][$ar3]-$dataJumByr[$KaryiD.$noTr][$ar3];
					                    ($dataRp[$KaryiD.$noTr][$ar4]-$dataBayar[$KaryiD.$noTr])==0?$dataRp[$KaryiD.$noTr][$ar4]=$dataJumByr[$KaryiD.$noTr][$ar4]:$dataRp[$KaryiD.$noTr][$ar4]-$dataJumByr[$KaryiD.$noTr][$ar4];
					                    ($dataRp[$KaryiD.$noTr][$ar5]-$dataBayar[$KaryiD.$noTr])==0?$dataRp[$KaryiD.$noTr][$ar5]=$dataJumByr[$KaryiD.$noTr][$ar5]:$dataRp[$KaryiD.$noTr][$ar5]-$dataJumByr[$KaryiD.$noTr][$ar5];
					                    /*$dataRp[$KaryiD.$noTr][$ar2]!=''?$dataRp[$KaryiD.$noTr][$ar2]=$dataRp[$KaryiD.$noTr][$ar2]-$dataBayar[$KaryiD.$noTr]:$dataRp[$KaryiD.$noTr][$ar2]=0;
					                    $dataRp[$KaryiD.$noTr][$ar3]!=''?$dataRp[$KaryiD.$noTr][$ar3]=$dataRp[$KaryiD.$noTr][$ar3]-$dataBayar[$KaryiD.$noTr]:$dataRp[$KaryiD.$noTr][$ar3]=0;
					                    $dataRp[$KaryiD.$noTr][$ar4]!=''?$dataRp[$KaryiD.$noTr][$ar4]=$dataRp[$KaryiD.$noTr][$ar4]-$dataBayar[$KaryiD.$noTr]:$dataRp[$KaryiD.$noTr][$ar4]=0;
					                    $dataRp[$KaryiD.$noTr][$ar5]!=''?$dataRp[$KaryiD.$noTr][$ar5]=$dataRp[$KaryiD.$noTr][$ar5]-$dataBayar[$KaryiD.$noTr]:$dataRp[$KaryiD.$noTr][$ar5]=0;*/
					            
					            // ..update letak jumlah Rp pertanggungjawaban berdasarkan kolom banyaknya tanggal telat pertanggungjawaban 
					            if ($telat[$KaryiD.$noTr]==1 || $telat[$KaryiD.$noTr]<=30 ) {
					            		# code...
					            	$stream.= "<td align=right>".number_format(($dataRp[$KaryiD.$noTr][$ar5]),2)."</td>
					            				<td align=right>".number_format(($dataRp[$KaryiD.$noTr][$ar1]),2)."</td>
							                    <td align=right>".number_format(($dataRp[$KaryiD.$noTr][$ar2]),2)."</td>
							                    <td align=right>".number_format(($dataRp[$KaryiD.$noTr][$ar3]),2)."</td>
							                    <td align=right>".number_format(($dataRp[$KaryiD.$noTr][$ar4]),2)."</td></tr>";
					            } else
					            if ($telat[$KaryiD.$noTr]==31 || $telat[$KaryiD.$noTr]<=60 ) {
					            		# code...
					            	$stream.= "<td align=right>".number_format(($dataRp[$KaryiD.$noTr][$ar1]),2)."</td>
					            				<td align=right>".number_format(($dataRp[$KaryiD.$noTr][$ar5]),2)."</td>
					            				<td align=right>".number_format(($dataRp[$KaryiD.$noTr][$ar2]),2)."</td>
							                    <td align=right>".number_format(($dataRp[$KaryiD.$noTr][$ar3]),2)."</td>
							                    <td align=right>".number_format(($dataRp[$KaryiD.$noTr][$ar4]),2)."</td></tr>";
					            } else
					            if ($telat[$KaryiD.$noTr]==61 || $telat[$KaryiD.$noTr]<=90 ) {
					            		# code...
					            	$stream.= "<td align=right>".number_format(($dataRp[$KaryiD.$noTr][$ar1]),2)."</td>
					            				<td align=right>".number_format(($dataRp[$KaryiD.$noTr][$ar2]),2)."</td>
					            				<td align=right>".number_format(($dataRp[$KaryiD.$noTr][$ar5]),2)."</td>
					            				<td align=right>".number_format(($dataRp[$KaryiD.$noTr][$ar3]),2)."</td>
					            				<td align=right>".number_format(($dataRp[$KaryiD.$noTr][$ar4]),2)."</td></tr>";
					            } else
					            if ($telat[$KaryiD.$noTr]==91 || $telat[$KaryiD.$noTr]<=120 ) {
					            		# code...
					            	$stream.= "<td align=right>".number_format(($dataRp[$KaryiD.$noTr][$ar1]),2)."</td>
					            				<td align=right>".number_format(($dataRp[$KaryiD.$noTr][$ar2]),2)."</td>
					            				<td align=right>".number_format(($dataRp[$KaryiD.$noTr][$ar3]),2)."</td>
					            				<td align=right>".number_format(($dataRp[$KaryiD.$noTr][$ar5]),2)."</td>
					            				<td align=right>".number_format(($dataRp[$KaryiD.$noTr][$ar4]),2)."</td>";
					            } else
					            if ($telat[$KaryiD.$noTr]==120 || $telat[$KaryiD.$noTr]>=120 ) {
					            		# code...
					            	$stream.= "<td align=right>".number_format(($dataRp[$KaryiD.$noTr][$ar1]),2)."</td>
					            				<td align=right>".number_format(($dataRp[$KaryiD.$noTr][$ar2]),2)."</td>
					            				<td align=right>".number_format(($dataRp[$KaryiD.$noTr][$ar3]),2)."</td>
					            				<td align=right>".number_format(($dataRp[$KaryiD.$noTr][$ar4]),2)."</td>
					            				<td align=right>".number_format(($dataRp[$KaryiD.$noTr][$ar5]),2)."</td>";
					            }

					            //.. end here

			            	$perKary[$KaryiD]+= $jumlah[$KaryiD.$noTr];
			            	$advPerKary[$KaryiD]+= ($dataJum[$KaryiD.$noTr]-$dataBayar[$KaryiD.$noTr]);
			            	$collPer[$KaryiD][$ar1]+= $dataRp[$KaryiD.$noTr][$ar1];
			            	$collPer[$KaryiD][$ar2]+= $dataRp[$KaryiD.$noTr][$ar2];
			            	$collPer[$KaryiD][$ar3]+= $dataRp[$KaryiD.$noTr][$ar3];
			            	$collPer[$KaryiD][$ar4]+= $dataRp[$KaryiD.$noTr][$ar4];
			            	$collPer[$KaryiD][$ar5]+= $dataRp[$KaryiD.$noTr][$ar5];


				}
			}
			
			$stream.="<tr>
						<td colspan=7 align=center><b>Sub Total</b></td>
						<td align=right>".number_format($perKary[$KaryiD],2)."</td>
						<td colspan=2></td>
						<td  align=right>".number_format($advPerKary[$KaryiD],2)."</td>
						<td  align=right>".number_format($collPer[$KaryiD][$ar1],2)."</td>
						<td  align=right>".number_format($collPer[$KaryiD][$ar2],2)."</td>
						<td  align=right>".number_format($collPer[$KaryiD][$ar3],2)."</td>
						<td  align=right>".number_format($collPer[$KaryiD][$ar4],2)."</td>
						<td  align=right>".number_format($collPer[$KaryiD][$ar5],2)."</td>
					</tr>
					<tr>
						<td></td>
					</tr>";
					$grPerKary+= $perKary[$KaryiD];
					$grPerAdv+= $advPerKary[$KaryiD];
					$grCollA1+= $collPer[$KaryiD][$ar1];
					$grCollA2+= $collPer[$KaryiD][$ar2];
					$grCollA3+= $collPer[$KaryiD][$ar3];
					$grCollA4+= $collPer[$KaryiD][$ar4];
					$grCollA5+= $collPer[$KaryiD][$ar5];
		}
		$stream.="<tr>
						<td colspan=7 align=center><b>GRAND TOTAL</b></td>
						<td align=right>".number_format($grPerKary,2)."</td>
						<td colspan=2></td>
						<td align=right>".number_format($grPerAdv,2)."</td>
						<td align=right>".number_format($grCollA1,2)."</td>
						<td align=right>".number_format($grCollA2,2)."</td>
						<td align=right>".number_format($grCollA3,2)."</td>
						<td align=right>".number_format($grCollA4,2)."</td>
						<td align=right>".number_format($grCollA5,2)."</td>
					</tr>
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

                        for($i=1;$i<=5;$i++);
                        {
                                $stream.="<tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td colspan=3></td>
                                        <td colspan=2></td>
                                        </tr>";
                        }
                        $nmKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');
                        $stream.="<tr>
                                <td colspan=2 align=center>".$nmKar[$dibuat]."</td>
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