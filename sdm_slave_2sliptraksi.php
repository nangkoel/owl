<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

$proses=$_GET['proses'];//
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['divisiId']==''?$divisiId=$_GET['divisiId']:$divisiId=$_POST['divisiId'];
$_POST['tpKary']==''?$tpKary=$_GET['tpKary']:$tpKary=$_POST['tpKary'];
$optTpkar=array("4"=>"HARIAN","3"=>"BULANAN");

$wrt="periode='".$periode."' and kodeorg='".$divisiId."'";
#tgl cut off
if(substr($periode,0,4)=='2014'){
	if($_SESSION['empl']['regional']=='SULAWESI'){
		if($tpKary==3){
			#bentuk tanggal cut off lalu plus satu dan tanggal cut off bulan berjalan
			$optTglMulai=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tanggalmulai', $wrt);
			$tglKmrn=nambahHari($optTglMulai[$periode],1,0);
			$wrt2="periode='".substr($tglKmrn,0,7)."' and kodeorg='".$divisiId."'";
			$tglCutoffLalu=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tglcutoff', $wrt2);
			$tglKmrn=nambahHari($tglCutoffLalu[substr($tglKmrn,0,7)],1,1);	
			$optTglCutoff=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tglcutoff', $wrt);
		}else{
			$optTglCutoff=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tglcutoff', $wrt);
			$optTglMulai=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tanggalmulai', $wrt);
			$tglKmrn=$optTglMulai[$periode];
		}	
	}else{
		$optTglCutoff=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tglcutoff', $wrt);
		$optTglMulai=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tanggalmulai', $wrt);
		$tglKmrn=$optTglMulai[$periode];
	}
}else{
	#bentuk tanggal cut off lalu plus satu dan tanggal cut off bulan berjalan,setelah 2015 menggunakan cut off
	$optTglMulai=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tanggalmulai', $wrt);
	$tglKmrn=nambahHari($optTglMulai[$periode],1,0);
	$wrt2="periode='".substr($tglKmrn,0,7)."' and kodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$ptId."')";
	$tglCutoffLalu=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tglcutoff', $wrt2);
	$tglKmrn=nambahHari($tglCutoffLalu[substr($tglKmrn,0,7)],1,1);	
	$optTglCutoff=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tglcutoff', $wrt);
}


$optRegional=  makeOption($dbname, 'bgt_regional_assignment', 'kodeunit,regional');
$optNmKeg=  makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
$optSatKeg=  makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,satuan');
$prd=explode("-",$periode);
$arrBln=array(1=>"Januari",2=>"Februari",3=>"Maret",4=>"April",5=>"Mei",6=>"Juni",7=>"Juli",8=>"Agustus",9=>"September",10=>"Oktober",11=>"November",12=>"Desember");


$garis=0;
if($proses=='excel'){
    $garis=1;
   $bgcolordt=" bgcolor=#DEDEDE";
}
if(($proses=='preview')||($proses=='pdf')){
     if($periode==''){
        exit("error:Period can't empty");
    } 
	$wheretang=" a.tanggal between '".$tglKmrn."' and '".$optTglCutoff[$periode]."' ";
	#sum premi yang di dapat saat ini
	/* $sSum="select sum(premi) as premi,idkaryawan,notransaksi from ".$dbname.".vhc_runhk a 
	       left join ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid 
		   where ".$wheretang."  and lokasitugas='".$divisiId."' group by notransaksi,idkaryawan
		   order by namakaryawan asc";
	$qSum=mysql_query($sSum) or die(mysql_error($conn));
	while($rSum=mysql_fetch_assoc($qSum)){
		$dtKary[$rSum['idkaryawan']]=$rSum['idkaryawan'];
        $dafNotrans[$rSum['notransaksi']]=$rSum['notransaksi'];
		$rupiahPremi[$rSum['idkaryawan'].$rSum['notransaksi']]=$rSum['premi'];
	} */
	#itung ulang premi yang seharusnya di dapet
    $svhc="select distinct a.notransaksi,jenispekerjaan,beratmuatan,idkaryawan,tanggal,a.notransaksi,b.alokasibiaya from ".$dbname.".vhc_runhk a left join "
        . "".$dbname.".vhc_rundt b on a.notransaksi=b.notransaksi "
        . "left join ".$dbname.".datakaryawan c on a.idkaryawan=c.karyawanid "
        . " where ".$wheretang." and lokasitugas='".$divisiId."'  order by a.notransaksi,jenispekerjaan asc";
     
    $qVhc=  mysql_query($svhc) or die(mysql_error());
    while($rvhc=  mysql_fetch_assoc($qVhc)){
		 $totPremi2=0;
         $sdr="select basis,hargasatuan,hargaslebihbasis,hargaminggu,auto,satuan,namakegiatan 
               from  ".$dbname.".vhc_kegiatan where kodekegiatan='".$rvhc['jenispekerjaan']."'";
           //exit("error:".$sdr);
           $qdr=mysql_query($sdr) or die(mysql_error($conn));
           while($rDr=  mysql_fetch_assoc($qdr)){
			$dhr="regional='".$_SESSION['empl']['regional']."' and tanggal='".$rvhc['tanggal']."'";
            $optHariLbr=makeOption($dbname, 'sdm_5harilibur', 'regional,tanggal',$dhr);
               switch ($rDr['auto']){
                   case'0':
                       $lbhbasis=0;
                       $qwe=date('D', strtotime($rvhc['tanggal']));
                       if(($qwe=='Sun')||($optHariLbr[$_SESSION['empl']['regional']]!='')){
                            $totPremi=$rvhc['beratmuatan']*$rDr['hargaminggu'];
                       }else{
						if($rDr['beratmuatan']>$rDr['basis']){
							   $lbhbasis=$rvhc['beratmuatan']-$rDr['basis'];
							   $totPremi=($rDr['basis']*floatval(str_replace(',','',number_format($rDr['hargasatuan'],2))))+($lbhbasis*floatval(str_replace(',','',number_format($rDr['hargaslebihbasis'],2))));
							}else{
							$totPremi=$rvhc['beratmuatan']*floatval(str_replace(',','',number_format($rDr['hargasatuan'],2)));
							}
                       }
                   break;
                   case'1':
				       $qwe=date('D', strtotime($rDr['tanggal']));
                       if(($qwe=='Sun')||($optHariLbr[$_SESSION['empl']['regional']]!='')){
                            $totPremi=$rvhc['beratmuatan']*$rDr['hargaminggu'];
                       }else{
                            $totPremi=$rvhc['beratmuatan']*floatval(str_replace(',','',number_format($rDr['hargasatuan'],2)));
                       }
                      /*  $whr="notransaksi='".$rvhc['notransaksi'];
                       $optRow=makeOption($dbname,'vhc_rundt','notransaksi,jenispekerjaan',$whr);
                       $jmlRow=count($optRow);
                       $summing="select sum(hargasatuan) as hrgsat,sum(basis) as basis,sum(hargaminggu) as minggu
                                 from ".$dbname.",vhc_kegiatan
                                 where auto='".$rDr['auto']."' and regional='".$_SESSION['empl']['regional']."'";
                       $qumingg=mysql_query($summing) or die(mysql_error());
                       $rumingg=mysql_fetch_assoc($qumingg);
                       
                        $qwe=date('D', strtotime($rvhc['tanggal']));
                       if($qwe=='Sun'){
                            $totPremi=$rumingg['minggu'];
                       }else{
                            $totPremi=round(($rumingg['hrgsat']-($rumingg['basis']/$jmlRow))*2+$rumingg['basis']/$jmlRow);
                       }
                       $dtPremi[$rvhc['idkaryawan'].$rvhc['tanggal'].$rvhc['jenispekerjaan'].$itung2]=$totPremi; */
                   break;
                   case'2':
                        $qwe=date('D', strtotime($rDr['tanggal']));
                       if(($qwe=='Sun')||($optHariLbr[$_SESSION['empl']['regional']]!='')){
                           $totPremi=$rvhc['beratmuatan']*$rDr['hargaminggu'];
                       }else{
                          $totPremi=$rDr['hargaslebihbasis'];
                       }
                   break;
                   case'3':
                       if($rDr['satuan']=='JJG'){
                           if(($rvhc['alokasibiaya']=='')||(strlen($rvhc['alokasibiaya'])!='10')){
                               exit("error: detail alokasi biaya harus diisi : ".$rvhc['notransaksi']);
                           }
                           $sBjr="select distinct sum(a.totalkg)/sum(a.jjg) as bjr, tanggal from ".$dbname.".kebun_spbdt a left join 
                                  ".$dbname.".kebun_spbht b on a.nospb=b.nospb where tanggal='".$rvhc['tanggal']."' 
                                  and blok like '".substr($rvhc['alokasibiaya'],0,6)."%' order by tanggal desc limit 1";
                           $qBjr=mysql_query($sBjr) or die(mysql_error($conn));
                           $rBjr=mysql_fetch_assoc($qBjr);
						   if(($rBjr['bjr']=='')||intval($rBjr['bjr'])==0){
							   $sBjr="select distinct sum(a.totalkg)/sum(a.jjg) as bjr, tanggal from ".$dbname.".kebun_spbdt a left join 
											  ".$dbname.".kebun_spbht b on a.nospb=b.nospb where left(tanggal,7)='".substr($rvhc['tanggal'],0,7)."' 
											  and blok like '".substr($rvhc['alokasibiaya'],0,6)."%' group by left(blok,6)";
							   $qBjr=mysql_query($sBjr) or die(mysql_error($conn));
							   $rBjr=mysql_fetch_assoc($qBjr);
							   if(($rBjr['bjr']=='')||intval($rBjr['bjr'])==0){
									$sTblBjr="select bjr from ".$dbname.".kebun_5bjr where kodeorg='".$rvhc['alokasibiaya']."' and tahunproduksi='".substr($rvhc['tanggal'],-4,4)."'";
									$qTblBjr=mysql_query($sTblBjr) or die(mysql_error($conn));
									$rTblBjr=mysql_fetch_assoc($qTblBjr);
									$rBjr['bjr']=$rTblBjr['bjr'];
								}
						   }
                           $rvhc['beratmuatan']=$rvhc['beratmuatan']*$rBjr['bjr'];
                       }
                       
                       $qwe=date('D', strtotime($rDr['tanggal']));
                       if(($qwe=='Sun')||($optHariLbr[$_SESSION['empl']['regional']]!='')){
                            $totPremi=$rvhc['beratmuatan']*floatval(str_replace(',','',number_format($rDr['hargaminggu'],2)));   
                        }
                        else{
                           $totPremi=$rvhc['beratmuatan']*floatval(str_replace(',','',number_format($rDr['hargasatuan'],2)));
                        }
                   break;
                   case'4':
                       $summing="select sum(hargasatuan) as hrgsat from ".$dbname.".vhc_kegiatan
                                 where auto='".$rDr['auto']."' and regional='".$_SESSION['empl']['regional']."'";
                       $qumingg=mysql_query($summing) or die(mysql_error());
                       $rumingg=mysql_fetch_assoc($qumingg);
                       $totPremi=round($rumingg['hrgsat']);
                       //$dtPremi[$rvhc['idkaryawan'].$rvhc['tanggal'].$rvhc['jenispekerjaan'].$itung2]=$totPremi;
                   break;
                   case'6':
                       $lbhbasis=0;
                       if($rDr['satuan']=='TBS'){
                           if(($rvhc['alokasibiaya']=='')||(strlen($rvhc['alokasibiaya'])!='10')){
                               exit("error: detail alokasi biaya harus diisi : ".$rvhc['notransaksi']);
                           }
                           $sBjr="select distinct sum(a.totalkg)/sum(a.jjg) as bjr, tanggal from ".$dbname.".kebun_spbdt a left join 
                                  ".$dbname.".kebun_spbht b on a.nospb=b.nospb where tanggal='".$rvhc['tanggal']."' 
                                  and blok like '".substr($rvhc['alokasibiaya'],0,6)."%' order by tanggal desc limit 1";
                           $qBjr=mysql_query($sBjr) or die(mysql_error($conn));
                           $rBjr=mysql_fetch_assoc($qBjr);
						   if(($rBjr['bjr']=='')||intval($rBjr['bjr'])==0){
							   $sBjr="select distinct sum(a.totalkg)/sum(a.jjg) as bjr, tanggal from ".$dbname.".kebun_spbdt a left join 
											  ".$dbname.".kebun_spbht b on a.nospb=b.nospb where left(tanggal,7)='".substr($rvhc['tanggal'],0,7)."' 
											  and blok like '".substr($rvhc['alokasibiaya'],0,6)."%' group by left(blok,6)";
							   $qBjr=mysql_query($sBjr) or die(mysql_error($conn));
							   $rBjr=mysql_fetch_assoc($qBjr);
							   if(($rBjr['bjr']=='')||intval($rBjr['bjr'])==0){
									$sTblBjr="select bjr from ".$dbname.".kebun_5bjr where kodeorg='".$rvhc['alokasibiaya']."' and tahunproduksi='".substr($rvhc['tanggal'],-4,4)."'";
									$qTblBjr=mysql_query($sTblBjr) or die(mysql_error($conn));
									$rTblBjr=mysql_fetch_assoc($qTblBjr);
									$rBjr['bjr']=$rTblBjr['bjr'];
								}
						   }
                           $rvhc['beratmuatan']=$rvhc['beratmuatan']*$rBjr['bjr'];
                       }
					   if($_SESSION['empl']['regional']=='SULAWESI'){
							if(substr($rvhc['jenispekerjaan'],0,2)=='MJ'){
								$cek=0;
								#perhitungan premi progresif, konsepnya. total semua prestasi di cek sudah lebih basis atau belum jika sudah maka pengalinya adalah harga lebih basisnya berlaku di sulawesi
								$totBrtMuat[$rvhc['tanggal'].$rvhc['jenispekerjaan'].$rvhc['idkaryawan']]+=$rvhc['beratmuatan'];
									if($totBrtMuat[$rvhc['tanggal'].$rvhc['jenispekerjaan'].$rvhc['idkaryawan']]>$rDr['basis']){
										$cek=$totBrtMuat[$rvhc['tanggal'].$rvhc['jenispekerjaan'].$rvhc['idkaryawan']]-$rvhc['beratmuatan'];
										if($cek==0){
											/* $totPremi=(($rvhc['beratmuatan']-$rDr['basis'])*floatval((str_replace(',','',(number_format($rDr['hargaslebihbasis'],2))))))+($rDr['basis']*floatval((str_replace(',','',number_format($rDr['hargasatuan'],2)))));  */
											$totPremi=($rDr['basis']*floatval((str_replace(',','',number_format($rDr['hargasatuan'],2)))));
											$totPremi2=(($rvhc['beratmuatan']-$rDr['basis'])*floatval((str_replace(',','',(number_format($rDr['hargaslebihbasis'],2))))));
										}else{
											$totPremi2=($rvhc['beratmuatan']*floatval((str_replace(',','',(number_format($rDr['hargaslebihbasis'],2)))))); 
										}
									}else{
										$totPremi=($rDr['basis']*floatval((str_replace(',','',number_format($rDr['hargasatuan'],2)))));
										$totPremi2=(($rvhc['beratmuatan']-$rDr['basis'])*floatval((str_replace(',','',(number_format($rDr['hargaslebihbasis'],2))))));
									}
							}else{
								if($rvhc['beratmuatan']>$rDr['basis']){
									$lbhbasis=$rvhc['beratmuatan']-$rDr['basis'];
								}
								if($rvhc['beratmuatan']>$rDr['basis']){
									$totPremi2=(($rvhc['beratmuatan']-$rDr['basis'])*floatval((str_replace(',','',(number_format($rDr['hargaslebihbasis'],2))))));
									$totPremi=($rDr['basis']*floatval((str_replace(',','',number_format($rDr['hargasatuan'],2))))); 
								}
								else{
									 $totPremi=($rvhc['beratmuatan']*floatval((str_replace(',','',number_format($rDr['hargasatuan'],2))))); 
								}
							}
							
					   }else{
						   if($rvhc['beratmuatan']>$rDr['basis']){
							   $lbhbasis=$rvhc['beratmuatan']-$rDr['basis'];
						   }
						   //echo $lbhbasis."__";.
							if($rvhc['beratmuatan']>$rDr['basis']){
								$totPremi2=(($rvhc['beratmuatan']-$rDr['basis'])*floatval((str_replace(',','',(number_format($rDr['hargaslebihbasis'],2))))));
								$totPremi=($rDr['basis']*floatval((str_replace(',','',number_format($rDr['hargasatuan'],2))))); 
							}
							else{
								 $totPremi=($rvhc['beratmuatan']*floatval((str_replace(',','',number_format($rDr['hargasatuan'],2))))); 
							}
						}
                   break;
				   
                   case'7':
                       //$_POST['tanggal'] premi minggu atau jam setelah luar jam kerja atau di panggil lagi di nolkan
                       $totPremi=$rDr['hargasatuan'];
					   //$dtPremi[$rvhc['idkaryawan'].$rvhc['tanggal'].$rvhc['jenispekerjaan'].$itung2]=$totPremi;
                   break;	   
                   case'8':
                       $totPremi2=round($rDr['hargaslebihbasis']*$rvhc['beratmuatan']);
                       //$dtPremi[$rvhc['idkaryawan'].$rvhc['tanggal'].$rvhc['jenispekerjaan'].$itung2]=$totPremi;
                   break;
               }
			    $dtSatuan[$rvhc['idkaryawan'].$rvhc['jenispekerjaan']]=$rDr['satuan'];
				$dtNamaKeg[$rvhc['idkaryawan'].$rvhc['jenispekerjaan']]=$rDr['namakegiatan'];
           }
			$dtRupTrfDua[$rvhc['idkaryawan'].$rvhc['jenispekerjaan']]+=$totPremi2;
			$dtRupTrfSt[$rvhc['idkaryawan'].$rvhc['jenispekerjaan']]+=$totPremi;
			$totRup[$rvhc['idkaryawan'].$rvhc['jenispekerjaan']]+=($totPremi+$totPremi2);
			$dtHasil[$rvhc['idkaryawan'].$rvhc['jenispekerjaan']]+=$rvhc['beratmuatan'];
			$dtJnsKrj[$rvhc['jenispekerjaan']]=$rvhc['jenispekerjaan'];
			$dtKary[$rvhc['idkaryawan']]=$rvhc['idkaryawan'];
    }
    
}
switch($proses){
    case'preview':
        echo $tab;
    break;
case'excel':
 $tab.="Print Time:".date('d-m-Y H:i:s')."<br>By:".$_SESSION['empl']['name'];
 $nop_="kartuKerjaPerKary__".$periode."__".$kdUnit;
    if(strlen($tab)>0){
     $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
     gzwrite($gztralala, $tab);
     gzclose($gztralala);
     echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls.gz';
        </script>";
	}
break;
case'pdf':
        $bln=explode('-',$periode);
        $idBln=intval($bln[1]);	
		$nodt=0;
        class PDF extends FPDF
        {
        var $col=0;
        var $dbname;
                function Header()
                {    
                        //$this->lMargin=5;  
						
                }
        }
                $pdf=new PDF('P','mm','letter');
                $pdf->AddPage();
                //periode gaji
                $bln=explode('-',$periode);
                $prd=$bln[0].$bln[1];
				$tglAwal=explode("-",$tglKmrn);
				$tglAkhir=explode("-",$optTglCutoff[$periode]);
				if(count($dtKary)==0){
					$pdf->Cell(60,3,'NOT FOUND','T',0,'L');
				}else{
					foreach($dtKary as $lstKary){
						$no+=1;
						$nodt+=1;
						$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;						 
											 
						$pdf->SetFont('Arial','',9);	
						$pdf->Cell(75,4,$_SESSION['org']['namaorganisasi'],0,1,'L');
						$pdf->Cell(75,4,"SLIP PERINCIAN HASIL KERJA SATUAN OPERATOR ".$optTpkar[$tpKary],0,1,'L');
						$pdf->Cell(75,4,"PERIODE  (".$tglAwal[2]."-".strtoupper($arrBln[intval($tglAwal[1])])."  s.d. ".$tglAkhir[2]."-".strtoupper($arrBln[intval($tglAkhir[1])])." ) ".$tglAwal[0]."",0,1,'L');
						$pdf->Cell(25,4,"NO.    ",0,0,'L');
						$pdf->Cell(15,4,":  ".$no,0,1,'L');
						$whrKary="karyawanid='".$lstKary."'";
						$arrNik=makeOption($dbname,'datakaryawan','karyawanid,nik',$whrKary);
						$arrNmKary=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',$whrKary);
						$pdf->SetFont('Arial','B',8);
						$pdf->Cell(25,4,"NIK / NAMA   ",0,0,'L');
						$pdf->Cell(20,4,":  ".$arrNik[$lstKary]." / ",0,0,'L');
						$pdf->Cell(41,4,strtoupper($arrNmKary[$lstKary]),0,1,'L');
						$pdf->SetFont('Arial','B',8);
						$pdf->ln(5);
						$pdf->Cell(5,7,"No.",'TB',0,'L');
						$pdf->Cell(55,7,"Jenis Pekerjaan",'TB',0,'C');
						$pdf->Cell(10,7,"SATUAN",'TB',0,'C');
						$pdf->Cell(15,7,"HASIL",'TB',0,'R');
						$pdf->Cell(25,7,"TARIF I",'TB',0,'R');
						$pdf->Cell(25,7,"TARIF II",'TB',0,'R');
						$pdf->Cell(25,7,"JUMLAH",'TB',1,'R');
						$pdf->SetFont('Arial','',7);
						$nod=1;
						
						foreach($dtJnsKrj as $lstJnsKerja){
							if($dtHasil[$lstKary.$lstJnsKerja]!=''){
								$pdf->Cell(5,5,($nod++),0,0,'R');
								$pdf->Cell(55,5,$dtNamaKeg[$lstKary.$lstJnsKerja],0,0,'L');
								$pdf->Cell(10,5,$dtSatuan[$lstKary.$lstJnsKerja],0,0,'C');
								$pdf->Cell(15,5,number_format($dtHasil[$lstKary.$lstJnsKerja],0),0,0,'R');
								$pdf->Cell(25,5,number_format($dtRupTrfSt[$lstKary.$lstJnsKerja],0),0,0,'R');
								$pdf->Cell(25,5,number_format($dtRupTrfDua[$lstKary.$lstJnsKerja],0),0,0,'R');
								$pdf->Cell(25,5,number_format($totRup[$lstKary.$lstJnsKerja],0),0,1,'R');
								$totTrfI[$lstKary]+=$dtRupTrfSt[$lstKary.$lstJnsKerja];
								$totTrfII[$lstKary]+=$dtRupTrfDua[$lstKary.$lstJnsKerja];
								$totSma[$lstKary]+=$totRup[$lstKary.$lstJnsKerja];
							}
						}
						$pdf->SetFont('Arial','B',8);
						$pdf->Cell(85,5,$_SESSION['lang']['total'],'TB',0,'C');
						$pdf->Cell(25,5,number_format($totTrfI[$lstKary],0),'TB',0,'R');
						$pdf->Cell(25,5,number_format($totTrfII[$lstKary],0),'TB',0,'R');
						$pdf->Cell(25,5,number_format($totSma[$lstKary],0),'TB',1,'R');		
						$pdf->ln(10);
						if($nodt==2){
							$pdf->AddPage();
							$nodt=0;
						}						
					}
				}
        $pdf->Output();
        break;
}
?>