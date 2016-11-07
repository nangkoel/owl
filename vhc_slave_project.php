<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include('lib/zMysql.php');
include_once('lib/zLib.php');

$_POST['method']==''?$method=$_GET['method']:$method=$_POST['method'];
$_POST['kode']==''?$kode=$_GET['kode']:$kode=$_POST['kode'];
//$method =$_POST['method'];
//$kode =$_POST['kode'];
$unit =$_POST['unit'];
$aset =$_POST['aset'];
$jenis =$_POST['jenis'];
$nama =$_POST['nama'];
$tanggalmulai=tanggalsystem($_POST['tanggalmulai']);
$tanggalselesai=tanggalsystem($_POST['tanggalselesai']);


$notran=$_POST['notran'];


$kelompok =$_POST['kelompok'];
$nilai =$_POST['nilai'];

$optLokasi=makeOption($dbname, 'datakaryawan', 'karyawanid,lokasitugas');

$namaBarangCari=$_POST['namaBarangCari'];

$kodeproject=$_POST['kodeproject'];
$nmBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$satBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');
$optNmKegBrg=makeOption($dbname, 'project_dt', 'kegiatan,namakegiatan');
$kegiatan =$_POST['kegiatan'];

$kodeproject=$_POST['kodeproject'];
$kodekegiatan=$_POST['kodekegiatan'];
$kodeBarangForm=$_POST['kodeBarangForm'];//buat insert
$kodebarang=$_POST['kodebarang'];//buat delete
$jumlahBarangForm=$_POST['jumlahBarangForm'];

$nmKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');

$optNmOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');


$persetujuan1=$_POST['persetujuan1'];
$persetujuan2=$_POST['persetujuan2'];
$persetujuan3=$_POST['persetujuan3'];
$persetujuan4=$_POST['persetujuan4'];
$persetujuan5=$_POST['persetujuan5'];
$persetujuan6=$_POST['persetujuan6'];
$persetujuan7=$_POST['persetujuan7'];


$optKar="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$g="select karyawanid,namakaryawan,nik,tipekaryawan from ".$dbname.".datakaryawan  where kodegolongan>='4'";
//exit("Error:$i");
$h=mysql_query($g) or die (mysql_error($conn));
while($i=mysql_fetch_assoc($h))
{
	$optKar.="<option value='".$i['karyawanid']."'>".$i['nik']." - ".$i['namakaryawan']."</option>";
}
switch($method)
{
	
	
	case'timeFrame':
	
		$iHead="select * from ".$dbname.".project where kode='".$kode."'";
		$nHead=mysql_query($iHead) or die (mysql_error($conn));
		$dHead=mysql_fetch_assoc($nHead);
			
			$tgl1=
			
		$stream="<table border=0>
					<tr>
						<td colspan=2>".$_SESSION['lang']['unit']."</td>
						<td><u>".$optNmOrg[$dHead['kodeorg']]."</u></td>
					</tr>
					<tr>
						<td colspan=2>".$_SESSION['lang']['nama']." ".$_SESSION['lang']['project']."</td>
						<td><u>".$dHead['nama']."</u></td>
					</tr>
					<tr>
						<td colspan=2>".$_SESSION['lang']['namakelompok']." ".$_SESSION['lang']['project']."</td>
						<td><u>".$dHead['tipe']."</u></td>
					</tr>
					<tr>
						<td colspan=2>".$_SESSION['lang']['nilai']."</td>
						<td><u>".number_format($dHead['nilai'])."</u></td>
					</tr>
					<tr>
						<td colspan=2>".$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['mulai']."</td>
						<td><u>".tanggalnormal($dHead['tanggalmulai'])."</u></td>
						<td>".$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['selesai']."</td>
						<td><u>".tanggalnormal($dHead['tanggalselesai'])."</u></td>
					</tr>
				</table>";//NO	Kodebarang	Namabarang	Satuan	JLH RAB	DIPAKAI	SELISIH	
	$arrTgl=rangeTanggal($dHead['tanggalmulai'],$dHead['tanggalselesai']);
	//print_r($arrTgl);
	$stream.="<br /><table class=sortable border=1 cellspacing=1>
				 <thead>
					<tr>
						<td align=center bgcolor=#CCCCCC>Tahapan</td>";
						foreach($arrTgl as $lstTgl=>$tgl)
							{
								$stream.="<td align=center bgcolor=#CCCCCC>".tanggalnormal($tgl)."</td>";
							}
	$stream.="</tr>";
	$iTahap="select * from ".$dbname.".project_dt where kodeproject='".$kode."' ";
	//echo $iTahap;
	$nTahap=mysql_query($iTahap) or die (mysql_error($conn));
	while($dTahap=mysql_fetch_assoc($nTahap))
	{
		//$i+=1;
		//$listKdProject[$dTahap['kodeproject']]=$dTahap['kodeproject'];
		$tahapan[$dTahap['namakegiatan']]=$dTahap['namakegiatan'];
		$tglMulai[$dTahap['namakegiatan']]=$dTahap['tanggalmulai'];
		$tglSelesai[$dTahap['namakegiatan']]=$dTahap['tanggalselesai'];
	}	
	
	echo $i;
	
	

	//$tglMulai[$dTahap['namakegiatan'].$dTahap['tanggalmulai']]
		
	
	
	//$arrTgl=rangeTanggal($dHead['tanggalmulai'],$dHead['tanggalselesai']);


	foreach($tahapan as $listTahapan)
	{
		$arrTglData=rangeTanggal($tglMulai[$listTahapan],$tglSelesai[$listTahapan]);
		$listTersimpan=false;
		$dert=false;
		$stream.="<tr>
				<td>".$tahapan[$listTahapan]."</td>";
				
				
				$isi="";
				foreach($arrTgl as $listTgl)
				{
						if($dert==false)
						{
							if($tglSelesai[$listTahapan]==$listTgl)
							{	
								$isi="bgcolor=blue";//$isi="bgcolor=red";
								$listTersimpan=false;
								//$tglSelesai[$listTahapan]="";
								$dert=true;
							}
							else
							{
								if($listTersimpan==false)
								{
									if($tglMulai[$listTahapan]==$listTgl)
									{
										$isi="bgcolor=blue";
										$listTersimpan=true;
									}
									
								}
							
							}
						}
						else
						{
							$isi="";
							$dert=false;
						}
						//$isi="";//exit("Error:HAHA");
						$stream.="<td ".$isi."></td>";	//".$tglSelesai[$listTahapan]."			
				}	
	}
	//$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="Laporan_Progres_Project".$dHead['kode'];
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
		case'excelMaterial':
		
		$iHead="select * from ".$dbname.".project where kode='".$kode."'";
		$nHead=mysql_query($iHead) or die (mysql_error($conn));
		$dHead=mysql_fetch_assoc($nHead);
			
		$stream="<table border=0>
					<tr>
						<td></td>
						<td>".$_SESSION['lang']['unit']."</td>
						<td><u>".$optNmOrg[$dHead['kodeorg']]."</u></td>
					</tr>
					<tr>
						<td ></td >
						<td>".$_SESSION['lang']['nama']." ".$_SESSION['lang']['project']."</td>
						<td><u>".$dHead['nama']."</u></td>
					</tr>
					<tr>
						<td></td>
						<td>".$_SESSION['lang']['namakelompok']." ".$_SESSION['lang']['project']."</td>
						<td><u>".$dHead['tipe']."</u></td>
					</tr>
					<tr>
						<td></td>
						<td>".$_SESSION['lang']['nilai']."</td>
						<td><u>".$dHead['nilai']."</u></td>
					</tr>
					<tr>
						<td></td>
						<td>".$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['mulai']."</td>
						<td><u>".tanggalnormal($dHead['tanggalmulai'])."</u></td>
						<td>".$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['selesai']."</td>
						<td><u>".tanggalnormal($dHead['tanggalselesai'])."</u></td>
					</tr>
				</table>";//NO	Kodebarang	Namabarang	Satuan	JLH RAB	DIPAKAI	SELISIH
	
		$stream.="<br /><table class=sortable border=1 cellspacing=1>
					 <thead>
						<tr>
							<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['nourut']."</td> 
							<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['kodebarang']."</td> 
							<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['namabarang']."</td> 
							<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['satuan']."</td> 
							<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['penggunaan']." ".$_SESSION['lang']['project']."</td> 
							<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['jumlahkeluargudang']."</td>
							<td align=center bgcolor=#CCCCCC>".$_SESSION['lang']['selisih']."</td> 
						</tr>";
						
		$iPro="select * from ".$dbname.".project_material where kodeproject='".$kode."' ";
		$nPro=mysql_query($iPro) or die (mysql_error($conn));
		while($dPro=mysql_fetch_assoc($nPro))
		{
			$listKdBrg[$dPro['kodebarang']]=$dPro['kodebarang'];
			$listJumlahRab[$dPro['kodebarang']]=$dPro['jumlah'];
		}
		$iGud="select * from ".$dbname.".log_transaksi_vw where kodeblok='".$kode."' and post='1' ";
		$nGud=mysql_query($iGud) or die (mysql_error($conn));
		while($dGud=mysql_fetch_assoc($nGud))
		{
			$listKdBrg[$dGud['kodebarang']]=$dGud['kodebarang'];
			$listJumlahPakai[$dGud['kodebarang']]=$dGud['jumlah'];
		}	
		foreach($listKdBrg as $kdBarang)
		{
			$no+=1;
			$selisih[$kdBarang]=$listJumlahRab[$kdBarang]-$listJumlahPakai[$kdBarang];
			$stream.="<tr>
						<td>".$no."</td>
						<td>".$kdBarang."</td>
						<td>".$nmBrg[$kdBarang]."</td>
						<td>".$satBrg[$kdBarang]."</td>
						<td>".$listJumlahRab[$kdBarang]."</td>
						<td>".$listJumlahPakai[$kdBarang]."</td>
						<td>".$selisih[$kdBarang]."</td>
					</tr>";	
		}
		$nop_="Laporan_Material_".$dHead['kode'];
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
	
	
	
	case'postIni':
		$i="select * from ".$dbname.".project where kode='".$kode."' ";
		$n=mysql_query($i) or die (mysql_error($conn));
		$d=mysql_fetch_assoc($n);

                $qwe=substr($kode,3,3);
                $asd=substr($qwe,-1);
                if($asd=='0')$aset=substr($qwe,0,2);
                else $aset=$qwe;
		
                $tanggal=date("Y-m-d");
                $tutup=false;
                if ($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
                    if ($_SESSION['empl']['kodejabatan']==114 and $_SESSION['empl']['bagian']=='FAT')
                        $tutup=true;
                } else {
                    if ($_SESSION['empl']['kodejabatan']==33 and ($_SESSION['empl']['bagian']=='FIN' or $_SESSION['empl']['bagian']=='ACC'))
                        $tutup=true;
                }
                
		if($tutup)
		{
			$w="update ".$dbname.".project set `posting`='1' where kode='".$kode."' ";
			if(mysql_query($w))
			{
                            //Proses Jurnal
                            $kodeJurnal = 'PRJ01';
                            $queryParam = selectQuery($dbname,'keu_5parameterjurnal','noakundebet',
                                "kodeaplikasi='PRJ' and jurnalid='".$kodeJurnal."'");
                            $resDebet = fetchData($queryParam);

                            $queryParam = selectQuery($dbname,'sdm_5tipeasset','akunak',
                                "kodetipe='".$aset."'");
                            $resKredit = fetchData($queryParam);

                            #======================== Nomor Jurnal =============================
                            # Get Journal Counter
                            $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
                                "kodeorg='".$_SESSION['empl']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."' ");
                            $tmpKonter = fetchData($queryJ);
                            $konter = addZero($tmpKonter[0]['nokounter']+1,3);

                            # Transform No Jurnal dari No Transaksi
                            $nojurnal = str_replace("-","",$tanggal)."/".$d['kodeorg']."/".$kodeJurnal."/".$konter;
                            #======================== /Nomor Jurnal ============================
                            $strceknilai = "select sum(jumlah) as jumlah from ".$dbname.".keu_jurnaldt where kodeasset='".$kode."'";
                            $resnilai = fetchData($strceknilai);
                            $nilaiproject=$resnilai[0]['jumlah'];
                            if ($nilaiproject==0){
                                $w="update ".$dbname.".project set `posting`='0' where kode='".$kode."' ";
                                mysql_query($w);
                                echo "Error: Project belum ada realisasi";
                                exit;
                            }
                            # Prep Header
                                $dataRes['header'] = array(
                                    'nojurnal'=>$nojurnal,
                                    'kodejurnal'=>$kodeJurnal,
                                    'tanggal'=>$tanggal,
                                    'tanggalentry'=>date('Ymd'),
                                    'posting'=>1,
                                    'totaldebet'=>$nilaiproject,
                                    'totalkredit'=>-1*$nilaiproject,
                                    'amountkoreksi'=>'0',
                                    'noreferensi'=>$kode,
                                    'autojurnal'=>'1',
                                    'matauang'=>'IDR',
                                    'kurs'=>'1',
                                    'revisi'=>'0'
                                );

                                # Data Detail
                                $noUrut = 1;

                                # Debet
                                $dataRes['detail'][] = array(
                                    'nojurnal'=>$nojurnal,
                                    'tanggal'=>$tanggal,
                                    'nourut'=>$noUrut,
                                    'noakun'=>$resDebet[0]['noakundebet'],
                                    'keterangan'=>'Penutupan Project '.$kode,
                                    'jumlah'=>$nilaiproject,
                                    'matauang'=>'IDR',
                                    'kurs'=>'1',
                                    'kodeorg'=>$d['kodeorg'],
                                    'kodekegiatan'=>'',
                                    'kodeasset'=>'',
                                    'kodebarang'=>'',
                                    'nik'=>'',
                                    'kodecustomer'=>'',
                                    'kodesupplier'=>'',
                                    'noreferensi'=>$kode,
                                    'noaruskas'=>'',
                                    'kodevhc'=>'',
                                    'nodok'=>'',
                                    'kodeblok'=>'',
                                    'revisi'=>'0'                
                                );
                                $noUrut++;

                                # Kredit
                                $dataRes['detail'][] = array(
                                    'nojurnal'=>$nojurnal,
                                    'tanggal'=>$tanggal,
                                    'nourut'=>$noUrut,
                                    'noakun'=>$resKredit[0]['akunak'],
                                    'keterangan'=>'Penutupan Project '.$kode,
                                    'jumlah'=>-1*$nilaiproject,
                                    'matauang'=>'IDR',
                                    'kurs'=>'1',
                                    'kodeorg'=>$d['kodeorg'],
                                    'kodekegiatan'=>'',
                                    'kodeasset'=>'',
                                    'kodebarang'=>'',
                                    'nik'=>'',
                                    'kodecustomer'=>'',
                                    'kodesupplier'=>'',
                                    'noreferensi'=>$kode,
                                    'noaruskas'=>'',
                                    'kodevhc'=>'',
                                    'nodok'=>'',
                                    'kodeblok'=>'',
                                    'revisi'=>'0'                
                                );
                                $noUrut++;      
                                #=========================================
                                
                                $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
                                if(!mysql_query($insHead)) {
                                    $headErr .= "Insert Header Error : ".addslashes(mysql_error($conn))."\n";
                                }
                                if($headErr=='') {
                                    $detailErr = '';
                                    foreach($dataRes['detail'] as $row) {
                                        $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                                        if(!mysql_query($insDet)) {
                                            $w="update ".$dbname.".project set `posting`='0' where kode='".$kode."' ";
                                            mysql_query($w);
                                            $detailErr .= "Insert Detail Error : ".addslashes(mysql_error($conn))."\n";
                                            break;
                                        }
                                    }
                                    if($detailErr=='') {
                                        # Header and Detail inserted
                                        #>>> Update Kode Jurnal
                                        $updJurnal = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$konter),
                                            "kodeorg='".$_SESSION['empl']['kodeorganisasi'].
                                            "' and kodekelompok='".$kodeJurnal."'");
                                        if(!mysql_query($updJurnal)) {
                                            echo "Update Kode Jurnal Error : ".addslashes(mysql_error($conn))."\n";
                                            # Rollback if Update Failed
                                            $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                                            if(!mysql_query($RBDet)) {
                                                echo "Rollback Delete Header Error : ".addslashes(mysql_error($conn))."\n";
                                                exit;
                                            }
                                            $w="update ".$dbname.".project set `posting`='0' where kode='".$kode."' ";
                                            mysql_query($w);
                                            exit;
                                        }

                                    } else {
                                        echo $detailErr;
                                        # Rollback, Delete Header
                                        $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                                        if(!mysql_query($RBDet)) {
                                            echo "Rollback Delete Header Error : ".addslashes(mysql_error($conn))."\n";
                                            exit;
                                        }
                                        $w="update ".$dbname.".project set `posting`='0' where kode='".$kode."' ";
                                        mysql_query($w);
                                    }
                                } else {
                                    $w="update ".$dbname.".project set `posting`='0' where kode='".$kode."' ";
                                    mysql_query($w);
                                    echo $headErr;
                                    exit;  
                                }
                                
			}
			else
			{
				echo " Gagal,".addslashes(mysql_error($conn));
			}
		}
		else
		{
			exit("Error:Sory You Can't Posting this Project!");
			//echo "Anda tidak bisa mengunci data ini";
		}
	break;
	
	
	case'getFormApv':
		echo"
			<table cellpadding=1 cellspacing=1 border=0 class=sortable>
				<thead><tr class=rowheader>
					<td colspan=3 align=center>".$_SESSION['lang']['persetujuan']."</td>
				</tr></thead>";
				for($i=1;$i<=7;$i++)
				{
					echo"<tr class=rowcontent>
							<td>".$_SESSION['lang']['persetujuan']." ".$i."</td>
							<td>:</td>
							<td><select id=persetujuan".$i." style='width:195px;'>".$optKar."</select></td>
						</tr>";
				}
				echo"<tr class=rowcontent>
					<td colspan=3 align=center>
						<button class=mybutton onclick=saveFormApv('".$kode."')>".$_SESSION['lang']['save']."</button>
						<button class=mybutton onclick=closeDialog()>".$_SESSION['lang']['selesai']."</button>
					</td>
				</tr>
				
			</table>";//<button class=mybutton onclick=cancelFormApv()>".$_SESSION['lang']['cancel']."</button>
	break;
	
	case'saveFormApv':
		$iCek="select * from ".$dbname.".project WHERE  `kode` = '".$kode."' ";
		$nCek=mysql_query($iCek) or die (mysql_error($conn));
		$dCek=mysql_fetch_assoc($nCek);
			
			if($dCek['stpersetujuan1']=='1' || $dCek['stpersetujuan1']=='2')
			{
				exit("Error:Sorry, this project has been approved");
			}
			else
			{
			}
		$i="UPDATE  ".$dbname.".`project` SET  
			`persetujuan1` =  '".$persetujuan1."',`persetujuan2` =  '".$persetujuan2."',`persetujuan3` =  '".$persetujuan3."',
			`persetujuan4` =  '".$persetujuan4."',`persetujuan5` =  '".$persetujuan5."',`persetujuan6` =  '".$persetujuan6."',
			`persetujuan7` =  '".$persetujuan7."',stpersetujuan1='3'
			 WHERE  `kode` = '".$kode."'";
		//exit("Error:$i");	
		if(mysql_query($i))
        {
			$to=getUserEmail($persetujuan1);
			$namakaryawan=getNamaKaryawan($_SESSION['standard']['userid']);
			$nmpnlk=getNamaKaryawan($persetujuan1);
			$subject="[Notifikasi] ".$_SESSION['lang']['persetujuan']." ".$_SESSION['lang']['project']." : ".$kode." ";
			//exit("Error:$to");
			$body="<html>
					 <head>
					 <body>
					   <dd>Dengan Hormat, Mr./Mrs. ".$nmpnlk."</dd><br>
					   Pada hari ini, tanggal ".date('d-m-Y')." karyawan a/n  ".$namakaryawan." mengajukan Persertujuan atas ".$_SESSION['lang']['project']." : ".$kode."
					   kepada bapak/ibu. Untuk menindak-lanjuti, silahkan ikuti link dibawah.
					   <br>
					   Regards,<br>
					   Owl-Plantation System.
					 </body>
					 </head>
				   </html>
				   ";//exit("Error:$body");
		   $x=kirimEmail($to,$subject,$body);#this has return but disobeying;	
        }
        else
        {
            echo " Gagal,".addslashes(mysql_error($conn));
        }
	break;
	
	
	case'saveFormBarang':
		$i="INSERT INTO ".$dbname.".`project_material` (`kodeproject`, `kodekegiatan`, `kodebarang`, `jumlah`, `updateby`) 
		 	values('".$kodeproject."','".$kodekegiatan."','".$kodeBarangForm."','".$jumlahBarangForm."','".$_SESSION['standard']['userid']."')";
		if(mysql_query($i))
        {
        }
        else
        {
            echo " Gagal,".addslashes(mysql_error($conn));
        }
	break;	
		
		
	case 'deleteMaterial':
	//exit("Error:hahaha");
		$i="DELETE FROM ".$dbname.".`project_material` WHERE `kodeproject` = '".$kodeproject."' AND `kodekegiatan` = '".$kegiatan."' AND `kodebarang`= '".$kodebarang."'";
		//exit("Error.$i");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;	

	case'getListBarang':
	//exit("Error:MASUK");
		echo"
			<fieldset>
			<legend>".$_SESSION['lang']['form']." Utama</legend>
				<fieldset  style='float:left;' >
					<legend>".$_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang']."</legend>
						<table cellspacing=1 border=0 class=data>
						
							<tr>
								<td colspan=2>".$_SESSION['lang']['namabarang']."</td>
								
								<td colspan=5>: 
									<input type=text id=namaBarangCari  class=myinputtext maxlength=100 onkeypress=\"return tanpa_kutip(event);\" style='width:100px;'>
									<button class=mybutton onclick=cariListBarang('".$kegiatan."','".$kodeproject."')>cari</button>
								<td>
							<tr>
							</table>
							
							<table id=listCariBarang >
							<thead>
							<tr class=rowheader>
								<td>No</td>
								<td>".$_SESSION['lang']['kodebarang']."</td>
								<td>".$_SESSION['lang']['namabarang']."</td>
								<td>".$_SESSION['lang']['satuan']."</td>
							</tr></thead>";
							
						if($namaBarangCari=='')
						{
						}
						else
						{
						$i="select kodebarang,namabarang from ".$dbname.".log_5masterbarang where namabarang like '%".$namaBarangCari."%'";
						//echo $i;
						$n=mysql_query($i) or die (mysql_error($conn));
						while ($d=mysql_fetch_assoc($n))
						{
						$no+=1;
						echo"
							<tr class=rowcontent  style='cursor:pointer;' title='Click It' onclick=\"moveDataBarang('".$d['kodebarang']."','".$nmBrg[$d['kodebarang']]."','".$satBrg[$d['kodebarang']]."');\">
								<td>".$no."</td>
								<td>".$d['kodebarang']."</td>
								<td>".$nmBrg[$d['kodebarang']]."</td>
								<td>".$satBrg[$d['kodebarang']]."</td>
							</tr>";
						}
						}
						echo"</table>
					</fieldset>
					
					
					<fieldset>
					<legend>".$_SESSION['lang']['form']."</legend>
						<table cellspacing=1 border=0>
							<tr>
								<td>".$_SESSION['lang']['project']."</td>
								<td>:</td>
								<td><input type=text id=kodeproject disabled value='".$kodeproject."' class=myinputtext maxlength=100 onkeypress=\"return tanpa_kutip(event);\" style='width:100px;'></td>
							</tr>
							<tr>
								<td>".$_SESSION['lang']['kodekegiatan']."</td>
								<td>:</td>
								<td><input type=text id=kodekegiatan disabled value='".$kegiatan."' class=myinputtext maxlength=100 onkeypress=\"return tanpa_kutip(event);\" style='width:100px;'></td>
							</tr>
							<tr>
								<td>".$_SESSION['lang']['kodebarang']."</td>
								<td>:</td>
								<td>
									<input type=text id=kodeBarangForm disabled class=myinputtext maxlength=100 onkeypress=\"return tanpa_kutip(event);\" style='width:100px;'>
								</td>
							</tr>
							<tr>
								<td>".$_SESSION['lang']['namabarang']."</td>
								<td>:</td>
								<td><input type=text id=namaBarangForm disabled class=myinputtext maxlength=100 onkeypress=\"return tanpa_kutip(event);\" style='width:100px;'></td>
							</tr>
							<tr>
								<td>".$_SESSION['lang']['satuan']."</td>
								<td>:</td>
								<td><input type=text id=satuanBarangForm disabled class=myinputtext maxlength=100 onkeypress=\"return tanpa_kutip(event);\" style='width:100px;'></td>
							</tr>
							<tr>
								<td>".$_SESSION['lang']['jumlah']."</td>
								<td>:</td>
								<td><input type=text id=jumlahBarangForm class=myinputtext maxlength=100 onkeypress=\"return tanpa_kutip(event);\" style='width:100px;'></td>
							</tr>
							
							<tr>
								<td>
									<button class=mybutton onclick=saveFormBarang('".$kegiatan."','".$kodeproject."','".$_SESSION['lang']['find']."',event)>Simpan</button>
									<button class=mybutton onclick=cancelFormBarang('".$kegiatan."','".$kodeproject."','".$_SESSION['lang']['find']."',event)>Hapus</button>
									<button class=mybutton onclick=closeDialog()>".$_SESSION['lang']['selesai']."</button>
								</td>
							</tr>
						</table>
					</fieldset>	
				</fieldset>
				
		<fieldset>
		<legend>".$_SESSION['lang']['datatersimpan']."</legend>
		<table cellspacing=1 border=0 class=data>
		<thead>
			<tr class=rowheader>
				<td>No</td>
				<td>".$_SESSION['lang']['project']."</td>
				<td>".$_SESSION['lang']['namakegiatan']."</td>
				<td>".$_SESSION['lang']['kodebarang']."</td>
				<td>".$_SESSION['lang']['namabarang']."</td>
				<td>".$_SESSION['lang']['jumlah']."</td>
				<td>".$_SESSION['lang']['satuan']."</td>
				<td>".$_SESSION['lang']['dibuat']."</td>
				<td>".$_SESSION['lang']['action']."</td>
			</tr>
		</thead>
		</tbody>";
		
		$i="select * from ".$dbname.".project_material where kodekegiatan='".$kegiatan."'";
		//echo $i;
		$n=mysql_query($i) or die (mysql_error($conn));
		while ($d=mysql_fetch_assoc($n))
		{
			$noData+=1;
		echo"
			<tr class=rowcontent>
				<td>".$noData."</td>
				<td>".$d['kodeproject']."</td>
				<td>".$optNmKegBrg[$d['kodekegiatan']]."</td>
				<td>".$d['kodebarang']."</td>
				<td>".$nmBrg[$d['kodebarang']]."</td>
				<td align=right>".$d['jumlah']."</td>
				<td>".$satBrg[$d['kodebarang']]."</td>
				<td>".$nmKar[$d['updateby']]."</td>
				
				<td>
					<img src=images/application/application_delete.png class=resicon  caption='Delete' 
					onclick=\"delMaterial('".$d['kodeproject']."','".$d['kodekegiatan']."','".$d['kodebarang']."');\">
				</td>
			</tr>";
		}
		echo "</table></fieldset>";
	
	break;
	
	
    case 'update':
	
		$iCek="select * from ".$dbname.".project WHERE  `kode` = '".$kode."' ";
		$nCek=mysql_query($iCek) or die (mysql_error($conn));
		$dCek=mysql_fetch_assoc($nCek);
			
			if($dCek['stpersetujuan1']=='1' || $dCek['stpersetujuan1']=='2')
			{
				exit("Error:Sorry, You can't edit, because this project has been approval");
			}
			else
			{
			}
		
    $str="update ".$dbname.".project set nama='".$nama."',
        tanggalmulai='".$tanggalmulai."',tanggalselesai='".$tanggalselesai."',kelompok='".$kelompok."',nilai='".$nilai."',
        updateby='".$_SESSION['standard']['userid']."',notransaksi='".$notran."'
        where kode='".$kode."'";
    if(mysql_query($str))
    {
        
    }
    else
    {
        echo " Gagal,".addslashes(mysql_error($conn));
    }
    break;
	
	
    case 'insert':
        // cari nomor terakhir
        $str="select kode from ".$dbname.".project
            order by substring(kode, -7) desc
            limit 1";
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
            $belakangnya=intval(substr($bar->kode,-7));
        }
        $belakangnya+=1;
        
        $belakangnya=addZero($belakangnya,10-strlen($aset));
        $kode=$jenis."-".$aset.$belakangnya;
        $str="insert into ".$dbname.".project (kode, nama, tipe, kodeorg,
            tanggalmulai,tanggalselesai,updateby,kelompok,nilai,notransaksi)
            values('".$kode."','".$nama."','".$jenis."',
            '".$unit."','".$tanggalmulai."','".$tanggalselesai."',".$_SESSION['standard']['userid'].",'".$kelompok."','".$nilai."','".$notran."')";
        if(mysql_query($str))
        {
            
        }
        else
        {
            echo " Gagal,".addslashes(mysql_error($conn));
        }	
    break;
	
    case 'delete':
	
		$iCek="select * from ".$dbname.".project WHERE  `kode` = '".$kode."' ";
		$nCek=mysql_query($iCek) or die (mysql_error($conn));
		$dCek=mysql_fetch_assoc($nCek);
		if($dCek['stpersetujuan1']=='1' || $dCek['stpersetujuan1']=='2')
		{
			exit("Error:Sorry, You can't delete, because this project has been approval");
		}
		else
		{
		}
		
		#header detail material
		$str="delete from ".$dbname.".project_material where kodeproject='".$kode."'";
		if(mysql_query($str))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}
		
		#header detail kegiatan
		$str="delete from ".$dbname.".project_dt where kodeproject='".$kode."'";
		if(mysql_query($str))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}
		
		#hapus header
        $str="delete from ".$dbname.".project
        where kode='".$kode."'";
        if(mysql_query($str))
        {
        }
        else
        {
            echo " Gagal,".addslashes(mysql_error($conn));
        }
		
    break;
    case'loadData':
        //$str1="select * from ".$dbname.".project where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by substring(kode, -7) desc";
     $str1="select a.*,b.namakaryawan from ".$dbname.".project a 
                 left join ".$dbname.".datakaryawan b on a.updateby=b.karyawanid order by substring(kode, -7) desc";   
    if($res1=mysql_query($str1))
    {
        $rowd=mysql_num_rows($res1);
        if($rowd==0)
        {
            echo"<tr class=rowcontent><td colspan=7>".$_SESSION['lang']['dataempty']."</td></tr>";
        }
        else
        {
            $no=0;
            while($bar1=mysql_fetch_object($res1))
            {
                $qwe=substr($bar1->kode,3,3);
                $asd=substr($qwe,-1);
                if($asd=='0')$aset=substr($qwe,0,2);
                else $aset=$qwe;

                $no+=1;
                echo"<tr class=rowcontent>
					<td>".$bar1->notransaksi."</td>
                    <td nowrap>".$bar1->kode."</td>
                    <td>".$bar1->kodeorg."</td>
                    <td>".$bar1->tipe."</td>
					<td>".$bar1->kelompok."</td>
                    <td>".$bar1->nama."</td>
                    <td nowrap>".tanggalnormal($bar1->tanggalmulai)."</td>
                    <td nowrap>".tanggalnormal($bar1->tanggalselesai)."</td>
					<td align=right>".number_format($bar1->nilai,2)."</td>
                    <td>".$bar1->namakaryawan."</td>
                    <td nowrap>";
                    $tutup=false;
                    if ($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
                        if ($_SESSION['empl']['kodejabatan']==114 and $_SESSION['empl']['bagian']=='FAT')
                            $tutup=true;
                    } else {
                        if ($_SESSION['empl']['kodejabatan']==33 and ($_SESSION['empl']['bagian']=='FIN' or $_SESSION['empl']['bagian']=='ACC'))
                            $tutup=true;
                    }
                    
                    if($bar1->posting==0){
                        if ($tutup){
                            echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar1->kodeorg."','".$aset."','".$bar1->tipe."','".$bar1->nama."','".tanggalnormal($bar1->tanggalmulai)."','".tanggalnormal($bar1->tanggalselesai)."','update','".$bar1->kode."','".$bar1->kelompok."','".$bar1->nilai."','".$bar1->notransaksi."');\">
                            <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"hapus('".$bar1->kode."');\">
                            <img src=images/nxbtn.png class=resicon  title='Detail' onclick=\"detailForm('".$bar1->kodeorg."','".$aset."','".$bar1->tipe."','".$bar1->nama."','".tanggalnormal($bar1->tanggalmulai)."','".tanggalnormal($bar1->tanggalselesai)."','detail','".$bar1->kode."','".$bar1->kelompok."','".$bar1->nilai."','".$bar1->notransaksi."');\">
                            <img src=images/application/application_go.png class=resicon  title='Approval' onclick=\"apv('".$bar1->kode."','".$_SESSION['lang']['persetujuan']."',event);\">
						<img src=images/skyblue/posting.png class=resicon  title='Close Project' onclick=\"postIni('".$bar1->kode."');\">
                           ";
                        } else {
                            if ($bar->updateby==$_SESSION['standard']['iduser'])
                            echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar1->kodeorg."','".$aset."','".$bar1->tipe."','".$bar1->nama."','".tanggalnormal($bar1->tanggalmulai)."','".tanggalnormal($bar1->tanggalselesai)."','update','".$bar1->kode."','".$bar1->kelompok."','".$bar1->nilai."','".$bar1->notransaksi."');\">
                            <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"hapus('".$bar1->kode."');\">
                            <img src=images/nxbtn.png class=resicon  title='Detail' onclick=\"detailForm('".$bar1->kodeorg."','".$aset."','".$bar1->tipe."','".$bar1->nama."','".tanggalnormal($bar1->tanggalmulai)."','".tanggalnormal($bar1->tanggalselesai)."','detail','".$bar1->kode."','".$bar1->kelompok."','".$bar1->nilai."','".$bar1->notransaksi."');\">
                           ";
                        }
				    }else{
                        if($bar1->posting==1){
                            echo"<img src=images/skyblue/posted.png class=resicon>";
                        }
                        else {    
                            echo"<img src=images/skyblue/posting.png>";
                            }                       
                       // echo"<img onclick=\"masterPDF('project','".$bar1->kode.",".$bar1->updateby."','','vhc_slave_project_pdf',event);\" title=\"Print\" class=\"resicon\" src=\"images/pdf.jpg\">";
                    }
                    echo"</td>
					<td nowrap>
					 <img onclick=\"masterPDF('project','".$bar1->kode.",".$bar1->updateby."','','vhc_slave_project_pdf',event);\" title=\"Print\" class=\"resicon\" src=\"images/pdf.jpg\">
                   	 <img onclick=excelMaterial(event,'".$bar1->kode."') src=images/excel.jpg class=resicon title='MS.Excel Material'>
					 <img onclick=timeFrame(event,'".$bar1->kode."') src=images/excel.jpg class=resicon title='MS.Excel Time Frame Project'>
				   
					
					</td>
					
					</tr>";
            }
        }
    }
    break;
    case'detail':
   
    $sDet="select distinct * from ".$dbname.".project_dt  where kodeproject='".$kode."'";
    $qDet=mysql_query($sDet) or die(mysql_error($conn));
    $row=mysql_num_rows($qDet);
    if($row==0)
    {
        $frmdt.="<tr class=rowcontent><td colspan=5>".$_SESSION['lang']['dataempty']."</td></tr>";
    }
    else
    {
        while($rDet=  mysql_fetch_assoc($qDet))
        {
        $frmdt.="<tr class=rowcontent><td>".$rDet['kodeproject']."</td>";
        $frmdt.="<td>".$rDet['namakegiatan']."</td>";
        $frmdt.="<td>".tanggalnormal($rDet['tanggalmulai'])."</td>";
        $frmdt.="<td>".tanggalnormal($rDet['tanggalselesai'])."</td>";
        $frmdt.="<td>
                <img src=images/zoom.png title='".$_SESSION['lang']['find']."' id=tmblCariNoGudang class=resicon onclick=tambahBarang('".$rDet['kegiatan']."','".$rDet['kodeproject']."','".$_SESSION['lang']['find']."',event)>
				<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editDet('".tanggalnormal($rDet['tanggalmulai'])."','".tanggalnormal($rDet['tanggalselesai'])."','updatedet','".$rDet['kodeproject']."','".$rDet['kegiatan']."','".$rDet['namakegiatan']."');\">
                <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"hapusData('".$rDet['kegiatan']."');\">
                </td></tr>";
        }
    }
    echo $frmdt;
    break;
    case'insertDetail':
        $tglMul=tanggalsystem($_POST['tglMul']);
        $tglakh=tanggalsystem($_POST['tglSmp']);


        $sCek="SELECT datediff('".$tglakh."', '".$tglMul."') as selisih";
        $hasil = mysql_query($sCek);
        $data = mysql_fetch_array($hasil);
        if($data['selisih']<0)
        {
            exit("Error:Tanggal Selesai Lebih Besar dari Tanggal Mulai");
        }
    $sInser="insert into ".$dbname.".project_dt (kodeproject, namakegiatan, tanggalmulai, tanggalselesai) 
             values ('".$kode."','".$_POST['nmKeg']."','".tanggalsystem($_POST['tglMul'])."','".tanggalsystem($_POST['tglSmp'])."')";
    if(!mysql_query($sInser))
    {
        die(mysql_error($conn));
    }
    break;
    case'updatedet':
         $tglMul=tanggalsystem($_POST['tglMul']);
        $tglakh=tanggalsystem($_POST['tglSmp']);


        $sCek="SELECT datediff('".$tglakh."', '".$tglMul."') as selisih";
        $hasil = mysql_query($sCek);
        $data = mysql_fetch_array($hasil);
        if($data['selisih']<0)
        {
            exit("Error:Tanggal Selesai Lebih Kecil dari Tanggal Mulai");
        }
    $sUpdate="update ".$dbname.".project_dt set namakegiatan='".$_POST['nmKeg']."',
              tanggalmulai='".tanggalsystem($_POST['tglMul'])."', tanggalselesai='".tanggalsystem($_POST['tglSmp'])."'
              where kegiatan='".$_POST['index']."'";
    if(!mysql_query($sUpdate))
    {
        die(mysql_error($conn));
    }
    break;
	
    
	
	case'hpsDetail':
	
		$sdel="delete from ".$dbname.".project_dt where kegiatan='".$_POST['index']."'";
		if(mysql_query($sdel))
		{
			$delMat="delete from ".$dbname.".project_material where kodekegiatan='".$_POST['index']."'";
			if(mysql_query($delMat))
			{
			}
			else
			{
				echo " Gagal,".addslashes(mysql_error($conn));
			}

		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}
	
	
/*    $sdel="delete from ".$dbname.".project_dt where kegiatan='".$_POST['index']."'";
	exit("Error:$sdel");
    if(!mysql_query($sdel))
    {
        die(mysql_error($conn));
    }*/
    break;
    
	
	
	
	case'postingData':
        $sCari="select distinct updateby from ".$dbname.".project where kode='".$_POST['kode']."'";
        $qCari=mysql_query($sCari) or die(mysql_error($conn));
        $rCari=mysql_fetch_assoc($qCari);
        if($optLokasi[$rCari['updateby']]!=$_SESSION['empl']['lokasitugas'])
        {
            exit("Error:Anda Tidak Memiliki Autorisasi");
        }
        $sPost="update ".$dbname.".project set updateby='".$_SESSION['standard']['userid']."',posting='1' where kode='".$_POST['kode']."'";
        //exit("Error:".$sPost);
        if(!mysql_query($sPost))
        {
            die(mysql_error($conn));
        }
        
    break;
    default:
    break;					
}


?>
