<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

if(isset($_POST['proses'])){
	$proses=$_POST['proses'];
}
else{
	$proses=$_GET['proses'];
}
$optNmOrg=makeOption($dbname, 'organisasi','kodeorganisasi,namaorganisasi');

$_POST['kdOrgT']==''?$kdOrg=$_GET['kdOrgT']:$kdOrg=$_POST['kdOrgT'];
$_POST['tanggal1T']==''?$tanggal1=$_GET['tanggal1T']:$tanggal1=$_POST['tanggal1T'];
$_POST['tanggal2T']==''?$tanggal2=$_GET['tanggal2T']:$tanggal2=$_POST['tanggal2T'];

$arr3="##kdOrgT##tanggal1T##tanggal2T##kdKegiatanT";
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
if(($proses=='excel')||($proses=='preview')){
		if(($_POST['tanggal1T']=='')||($_POST['tanggal2T']=='')){
			exit("error: ".$_SESSION['lang']['tanggal']."1 dan ".$_SESSION['lang']['tanggal']." 2 tidak boleh kosong");
		}
		if($tanggal2<$tanggal1){
			exit("error: Tolong gunakan urutan tanggal yang benar");
		}
		$tglPP=explode("-",$tanggal1);
		$date1 = $tglPP[0];
		$month1 = $tglPP[1];
		$year1 = $tglPP[2];
	   
		$tgl2 = $tanggal2; 
		$pecah2 = explode("-", $tgl2);
		$date2 = $pecah2[0];
		$month2 = $pecah2[1];
		$year2 =  $pecah2[2];
	$tangsys1=$year1."-".$month1."-".$date1;
	$tangsys2=$year2."-".$month2."-".$date2;

	$wheretang=" a.tanggal between '".$tangsys1."' and '".$tangsys2."' ";
	#sum premi yang di dapat saat ini
	$sSum="select sum(premi) as premi,idkaryawan,notransaksi from ".$dbname.".vhc_runhk a 
	       left join ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid 
		   where ".$wheretang."  and lokasitugas='".$kdOrg."' group by notransaksi,idkaryawan
		   order by namakaryawan asc";
	$qSum=mysql_query($sSum) or die(mysql_error($conn));
	while($rSum=mysql_fetch_assoc($qSum)){
		$dtKary[$rSum['idkaryawan']]=$rSum['idkaryawan'];
        $dafNotrans[$rSum['notransaksi']]=$rSum['notransaksi'];
		$rupiahPremi[$rSum['idkaryawan'].$rSum['notransaksi']]=$rSum['premi'];
	}
	#itung ulang premi yang seharusnya di dapet
    $svhc="select distinct a.notransaksi,jenispekerjaan,beratmuatan,idkaryawan,tanggal,a.notransaksi,b.alokasibiaya from ".$dbname.".vhc_runhk a left join "
        . "".$dbname.".vhc_rundt b on a.notransaksi=b.notransaksi "
        . "left join ".$dbname.".datakaryawan c on a.idkaryawan=c.karyawanid "
        . " where ".$wheretang." and lokasitugas='".$kdOrg."'  order by a.notransaksi,jenispekerjaan asc";
     
    $qVhc=  mysql_query($svhc) or die(mysql_error());
    while($rvhc=  mysql_fetch_assoc($qVhc)){
         $sdr="select basis,hargasatuan,hargaslebihbasis,hargaminggu,auto,satuan 
               from  ".$dbname.".vhc_kegiatan where kodekegiatan='".$rvhc['jenispekerjaan']."'";
           //exit("error:".$sdr);
           $qdr=mysql_query($sdr) or die(mysql_error($conn));
           while($rDr=  mysql_fetch_assoc($qdr)){
			$dhr="regional='".$_SESSION['empl']['regional']."' and tanggal='".$rDr['tanggal']."'";
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
						#perhitungan normal secara default
					    if($rvhc['beratmuatan']>$rDr['basis']){
								   $lbhbasis=$rvhc['beratmuatan']-$rDr['basis'];
						}
					   //echo $lbhbasis."__";.
						if($rvhc['beratmuatan']>$rDr['basis']){
							$totPremi=(($rvhc['beratmuatan']-$rDr['basis'])*floatval((str_replace(',','',(number_format($rDr['hargaslebihbasis'],2))))))+($rDr['basis']*floatval((str_replace(',','',number_format($rDr['hargasatuan'],2))))); 
							//exit("error:".(($rvhc['beratmuatan']."___".$rDr['basis'])."__".floatval((str_replace(',','',(number_format($rDr['hargaslebihbasis'],2))))))."___".($rDr['basis']."__".floatval((str_replace(number_format($rDr['hargasatuan'],2),',',''))))."___ss".$rDr['hargasatuan']);
						}
						else{
							 $totPremi=($rvhc['beratmuatan']*floatval((str_replace(',','',number_format($rDr['hargasatuan'],2))))); 
						}
						#perhitungan premi progresif, konsepnya. total semua prestasi di cek sudah lebih basis atau belum jika sudah maka pengalinya adalah harga lebih basisnya berlaku di sulawesi dan untuk kegiatan MJ aja
					   if($_SESSION['empl']['regional']=='SULAWESI'){
						$cek=0;
							if(substr($rvhc['jenispekerjaan'],0,2)=='MJ'){
								$totBrtMuat[$rvhc['tanggal'].$rvhc['jenispekerjaan'].$rvhc['idkaryawan']]+=$rvhc['beratmuatan'];
								if($totBrtMuat[$rvhc['tanggal'].$rvhc['jenispekerjaan'].$rvhc['idkaryawan']]>$rDr['basis']){
									$cek=$totBrtMuat[$rvhc['tanggal'].$rvhc['jenispekerjaan'].$rvhc['idkaryawan']]-$rvhc['beratmuatan'];
									if($cek==0){
										$totPremi=(($rvhc['beratmuatan']-$rDr['basis'])*floatval((str_replace(',','',(number_format($rDr['hargaslebihbasis'],2))))))+($rDr['basis']*floatval((str_replace(',','',number_format($rDr['hargasatuan'],2))))); 
									}else{
										$totPremi=($rvhc['beratmuatan']*floatval((str_replace(',','',(number_format($rDr['hargaslebihbasis'],2)))))); 
									}
								}else{
									$totPremi=($rvhc['beratmuatan']*floatval((str_replace(',','',number_format($rDr['hargasatuan'],2))))); 
								}
							} 
					   } 
						//$dtPremi[$rvhc['idkaryawan'].$rvhc['tanggal'].$rvhc['jenispekerjaan'].$itung2]=$totPremi;
                   break;
				   
                   case'7':
                       //$_POST['tanggal'] premi minggu atau jam setelah luar jam kerja atau di panggil lagi di nolkan
                       $totPremi=$rDr['hargasatuan'];
					   //$dtPremi[$rvhc['idkaryawan'].$rvhc['tanggal'].$rvhc['jenispekerjaan'].$itung2]=$totPremi;
                   break;	   
                   case'8':
                       //$totPremi=$rDr['hargasatuan']+(($rDr['beratmuatan']-$rDr['basis'])*$rDr['hargaslebihbasis'])+$rDr['hargaminggu'];
                       $totPremi=round($rDr['hargaslebihbasis']*$rvhc['beratmuatan']);
                       //$dtPremi[$rvhc['idkaryawan'].$rvhc['tanggal'].$rvhc['jenispekerjaan'].$itung2]=$totPremi;
                   break;
               }
           }
			$dtPremiTot[$rvhc['idkaryawan'].$rvhc['notransaksi']]+=$totPremi;
			$dtPrmi[$rvhc['idkaryawan'].$rvhc['notransaksi']][$rvhc['jenispekerjaan']]+=$totPremi;
			$dtTgl[$rvhc['idkaryawan'].$rvhc['notransaksi']]=$rvhc['tanggal'];
			$dtKary[$rvhc['idkaryawan']]=$rvhc['idkaryawan'];
			$dafNotrans[$rvhc['notransaksi']]=$rvhc['notransaksi'];
    }
    
    $rtwreg="regional='".$_SESSION['empl']['regional']."'";
    $optJnsKeg=  makeOption($dbname, 'vhc_kegiatan', 'kodekegiatan,namakegiatan', $rtwreg);
    $optSatKeg=  makeOption($dbname, 'vhc_kegiatan', 'kodekegiatan,satuan', $rtwreg);
	 
	$tab.="<table cellspacing=1 border=".$garis." class=sortable>
	<thead class=rowheader>
		<tr ".$bgcolordt.">
        <td align=center rowspan=2>No.</td>
        <td align=center rowspan=2>".$_SESSION['lang']['notransaksi']."</td>
        <td align=center rowspan=2>".$_SESSION['lang']['tanggal']."</td>
        <td align=center rowspan=2>".$_SESSION['lang']['nik']."</td>
        <td align=center rowspan=2>".$_SESSION['lang']['namakaryawan']."</td>
		<td align=center>Sebelum</td>
        <td align=center>Sesudah</td></tr>
        <tr><td align=center>".$_SESSION['lang']['premi']."</td>
        <td align=center>".$_SESSION['lang']['premi']."</td></tr>";
        $tab.="</tr></thead><tbody>";
		foreach($dtKary as $lstKary){
			foreach($dafNotrans as $lstNotrans){
				if(intval($dtPremiTot[$lstKary.$lstNotrans])!=0){
					if(round($dtPremiTot[$lstKary.$lstNotrans])!=round($rupiahPremi[$lstKary.$lstNotrans])){
						$no+=1;
						$maxRow+=1;
						$wkio="karyawanid='".$lstKary."'";
						$dtNik=  makeOption($dbname, 'datakaryawan', 'karyawanid,nik',$wkio);
						$dtNmkar=  makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan',$wkio);
						$dtTpkar=  makeOption($dbname, 'datakaryawan', 'karyawanid,tipekaryawan',$wkio);
						$dtSubdivisi=  makeOption($dbname, 'datakaryawan', 'karyawanid,subbagian',$wkio);
						if($dtSubdivisi[$lstKary]==''){
							$dtSubdivisi[$lstKary]=$dtSubdivisi=  makeOption($dbname, 'datakaryawan', 'karyawanid,lokasitugas',$wkio);;
						}
						$tab.="<tr class=rowcontent>";
						$tab.="<td>".$no."</td>";
						$tab.="<td id=notransaksidt_".$no.">".$lstNotrans."</td>";
						$tab.="<td id=tgldt_".$no.">".$dtTgl[$lstKary.$lstNotrans]."</td>";
						$tab.="<td><input type=hidden id=nik_".$no." value=".$lstKary." />".$dtNik[$lstKary]."</td>";
						$tab.="<td>".$dtNmkar[$lstKary]."</td>";
						$tab.="<td align=right>".number_format($rupiahPremi[$lstKary.$lstNotrans],2)."</td>";
						$tab.="<td align=right><input type=hidden id=premiApdt_".$no." value=".$dtPremiTot[$lstKary.$lstNotrans]." />".number_format($dtPremiTot[$lstKary.$lstNotrans],2)."</td>";
						$tab.="</tr>";
					}
					
				}
				
			}
		}
		$tab.="</tbody></table>";
         if(($_SESSION['empl']['bagian']=='IT')||($_SESSION['empl']['bagian']=='FIN')){
            $tab.="<button class=mybutton onclick=postingDat3('".$maxRow."')  id=revTmbl3>Update Data</button>&nbsp;<button class=mybutton onclick=zExcel(event,'kebun_slave_3updategajibjr3.php','".$arr3."')>Excel</button>";
        }else{
            $tab.="<button class=mybutton onclick=zExcel(event,'kebun_slave_3updategajibjr3.php','".$arr3."')>Excel</button>";
        }
        
}
switch($proses){
    case'preview':
        echo $tab;
    break;
	case'updateData':
				foreach($_POST['notrans'] as $rowdt=>$isiRow){
					/* $scek="select distinct * from ".$dbname.".setup_periodeakuntansi where "
						. "kodeorg='".substr($isiRow,0,4)."' and periode='".substr($_POST['tgl'][$rowdt],0,7)."'";
						//exit("error:".$scek);
					$qcek=  mysql_query($scek) or die(mysql_error($conn));
					$rcek= mysql_fetch_assoc($qcek);
					if($rcek['tutupbuku']==1){
						continue;
					}else{ */
						if(($_POST['tgl'][$rowdt]=='')&&($_POST['nik'][$rowdt]=='')){
							continue;
						}
						
						$suphadir="update ".$dbname.".vhc_runhk set premi='".$_POST['updUpah'][$rowdt]."'"
								. " where notransaksi='".$isiRow."' and idkaryawan='".$_POST['nik'][$rowdt]."'";
						//exit("error".$suphadir);
						if(!mysql_query($suphadir)){
							 exit("error: db bermasalah ".mysql_error($conn)."___".$suphadir);   
						}
					//}
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
}

?>
