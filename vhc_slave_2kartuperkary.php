<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

$proses=$_GET['proses'];//
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['kdUnit']==''?$kdUnit=$_GET['kdUnit']:$kdUnit=$_POST['kdUnit'];
$_POST['afdId']==''?$afdId=$_GET['afdId']:$afdId=$_POST['afdId'];
$_POST['karyId']==''?$karyId=$_GET['karyId']:$karyId=$_POST['karyId'];
$_POST['tpKaryId']==''?$tpKary=$_GET['tpKaryId']:$tpKary=$_POST['tpKaryId'];
#array tanggal satu periode
function dates_inbetween($date1, $date2){

    $day = 60*60*24;

    $date1 = strtotime($date1);
    $date2 = strtotime($date2);

    $days_diff = round(($date2 - $date1)/$day); // Unix time difference devided by 1 day to get total days in between

    $dates_array = array();
    $dates_array[] = date('Y-m-d',$date1);

    for($x = 1; $x < $days_diff; $x++){
        $dates_array[] = date('Y-m-d',($date1+($day*$x)));
    }

    $dates_array[] = date('Y-m-d',$date2);
    if($date1==$date2){
        $dates_array = array();
        $dates_array[] = date('Y-m-d',$date1);        
    }
    return $dates_array;
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
if(($proses=='excel')||($proses=='preview')){
	if($afdId==''){
		exit("warning: Kode Traksi Harus di pilih");
		//$afdId=$_SESSION['empl']['lokasitugas'];
	}
	$arrPer=explode("-",$periode);
	if (($arrPer[1]-1)==0) {
		$periode2=($arrPer[0]-1)."-12";
	} else {
		$periode2=$arrPer[0]."-".($arrPer[1]-1);
		if (strlen($periode2)==6)
			$periode2=$arrPer[0]."-0".($arrPer[1]-1);
	}
	$wrt="periode='".$periode."' and kodeorg='".substr($afdId,0,4)."'";
	$wrt2="periode='".$periode2."' and kodeorg='".substr($afdId,0,4)."'";
	#tanggal gaji satu periode
        $optTglGj=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tglcutoff', $wrt2);
        $optTglGjS=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tglcutoff', $wrt);
        $arrTgl=dates_inbetween(nambahHari($optTglGj[$periode2],1,1),$optTglGjS[$periode]);

     if($periode==''){
        exit("error:Period can't empty");
    } 
    if($afdId==''){
           exit("error:Unit can't empty");
    }
    if($tpKary!=''){
        $add=" and c.tipekaryawan='".$tpKary."'";
    }
    
    $wrt="tanggal between '".nambahHari($optTglGj[$periode2],1,1)."' and '".$optTglGjS[$periode]."'";
    if($karyId!=''){
		$add.=" and a.idkaryawan='".$karyId."'";
	}
    $svhc="select distinct a.notransaksi,jenispekerjaan,beratmuatan,a.premi,idkaryawan,tanggal,a.notransaksi,b.alokasibiaya from ".$dbname.".vhc_runhk a left join "
        . "".$dbname.".vhc_rundt b on a.notransaksi=b.notransaksi "
        . "left join ".$dbname.".datakaryawan c on a.idkaryawan=c.karyawanid "
        . " where ".$wrt." and lokasitugas='".substr($afdId,0,4)."' ".$add." order by namakaryawan asc";
    //echo $svhc;
    $qVhc=  mysql_query($svhc) or die(mysql_error());
    while($rvhc=  mysql_fetch_assoc($qVhc)){
        if($notans!=$rvhc['notransaksi']){
            $notans=$rvhc['notransaksi'];
            $itung=1;
            $itung2=$itung;
            $jmRow[$rvhc['idkaryawan'].$rvhc['tanggal']]+=1;
            $itung2=$jmRow[$rvhc['idkaryawan'].$rvhc['tanggal']];
        }else{
            //exit("error:masuk sini");
            $jmRow[$rvhc['idkaryawan'].$rvhc['tanggal']]+=1;
            $itung2=$jmRow[$rvhc['idkaryawan'].$rvhc['tanggal']];
        }
        $dtHslKrj[$rvhc['idkaryawan'].$rvhc['tanggal'].$itung2]=$rvhc['beratmuatan'];
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
  					   if($_SESSION['empl']['regional']=='SULAWESI'){
  						  $cek=0;
    							if(substr($rvhc['jenispekerjaan'],0,2)=='MJ'){
    								#perhitungan premi progresif, konsepnya. total semua prestasi di cek sudah lebih basis atau belum jika sudah maka pengalinya adalah harga lebih basisnya berlaku di sulawesi khusus utk MJ
    								$totBrtMuat[$rvhc['tanggal'].$rvhc['jenispekerjaan']]+=$rvhc['beratmuatan'];
    								if($totBrtMuat[$rvhc['tanggal'].$rvhc['jenispekerjaan']]>$rDr['basis']){
    									$cek=$totBrtMuat[$rvhc['tanggal'].$rvhc['jenispekerjaan']]-$rvhc['beratmuatan'];
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
        //$jmRow[$rvhc['idkaryawan'].$rvhc['tanggal']]=$itung;
        
        $dtPremiTot[$rvhc['idkaryawan'].$rvhc['tanggal']]=$rvhc['premi'];
        $dtJnsKerj[$rvhc['jenispekerjaan']]=$rvhc['jenispekerjaan'];
        $dtKary[$rvhc['idkaryawan']]=$rvhc['idkaryawan'];
        $dafkerja[$rvhc['idkaryawan'].$rvhc['tanggal'].$itung2]=$rvhc['jenispekerjaan'];
        $dafNotrans[$rvhc['idkaryawan'].$rvhc['tanggal'].$itung2]=$rvhc['notransaksi'];
        $dafAlokasi[$rvhc['idkaryawan'].$rvhc['tanggal'].$itung2]=$rvhc['alokasibiaya'];   
		$dtPremi[$rvhc['idkaryawan'].$rvhc['tanggal'].$rvhc['jenispekerjaan'].$itung2]=round($totPremi);		
    }
    
    $rtwreg="regional='".$_SESSION['empl']['regional']."'";
    $optJnsKeg=  makeOption($dbname, 'vhc_kegiatan', 'kodekegiatan,namakegiatan', $rtwreg);
    $optSatKeg=  makeOption($dbname, 'vhc_kegiatan', 'kodekegiatan,satuan', $rtwreg);
    array_multisort($arrTgl,SORT_ASC);
    
        foreach($dtKary as $lstKary){
        $no+=1;
            $wkio="karyawanid='".$lstKary."'";
            $dtNik=  makeOption($dbname, 'datakaryawan', 'karyawanid,nik',$wkio);
            $dtNmkar=  makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan',$wkio);
            $dtTpkar=  makeOption($dbname, 'datakaryawan', 'karyawanid,tipekaryawan',$wkio);
            $dtSubdivisi=  makeOption($dbname, 'datakaryawan', 'karyawanid,subbagian',$wkio);
            if($dtSubdivisi[$lstKary]==''){
                $dtSubdivisi[$lstKary]=$dtSubdivisi=  makeOption($dbname, 'datakaryawan', 'karyawanid,lokasitugas',$wkio);;
            }
            $dtNik=  makeOption($dbname, 'datakaryawan', 'karyawanid,nik',$wkio);
            $tab.="<table>";
            $tab.="<tr><td></td>";
            $tab.="<tr><td>No.</td>";
            $tab.="<td>&nbsp;</td>";
            $tab.="<td>: ".$no."</td></tr>";
            $tab.="<tr><td>".strtoupper($_SESSION['lang']['bulan'])."</td>";
            $tab.="<td>&nbsp;</td>";
            $tab.="<td>:  (".tanggalnormal(nambahHari($optTglGj[$periode2],1,1))." s.d ".tanggalnormal($optTglGjS[$periode]).")  </td></tr>";
            $tab.="<tr><td colspan=2>".strtoupper($_SESSION['lang']['nik'])." / ".strtoupper($_SESSION['lang']['namakaryawan'])."</td>";
            $tab.="<td>: ".$dtNik[$lstKary]." / ".strtoupper($dtNmkar[$lstKary])."</td></tr>";
            $tab.="<tr><td>".strtoupper($_SESSION['lang']['unit']." kerja")."</td>";
            $tab.="<td>&nbsp;</td>";
            $sbdiv="kodeorganisasi='".$dtSubdivisi[$lstKary]."'";
            $optDivisi=  makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi',$sbdiv);
            $tab.="<td>: ".$dtSubdivisi[$lstKary]." / ".$optDivisi[$dtSubdivisi[$lstKary]]."</td></tr>";
            $tab.="<tr><td>".strtoupper($_SESSION['lang']['tipekaryawan'])."</td>";
            $tab.="<td>&nbsp;</td>";
            $sbdiv="id='".$dtTpkar[$lstKary]."'";
            $optNmTipe=  makeOption($dbname, 'sdm_5tipekaryawan', 'id,tipe',$sbdiv);
            $tab.="<td>: ".$optNmTipe[$dtTpkar[$lstKary]]."</td></tr>";
            $tab.="</table>";
            $tab.="<table cellpadding=1 cellspacing=1 border='".$garis."' class=sortable><thead>";
            $tab.="<tr ".$bgcolordt." align=center>";
            $tab.="<td >".strtoupper($_SESSION['lang']['tanggal'])."</td>";
            $tab.="<td >".strtoupper($_SESSION['lang']['kodekegiatan'])."</td>";
            $tab.="<td >".strtoupper($_SESSION['lang']['namakegiatan'])."</td>";
            $tab.="<td >".strtoupper($_SESSION['lang']['prestasi'])."</td>";
            $tab.="<td >".strtoupper($_SESSION['lang']['satuan'])."</td>";
            $tab.="<td >".strtoupper($_SESSION['lang']['notransaksi'])."</td>";
            $tab.="<td >".strtoupper("ALOKASI")."</td>";
            $tab.="<td >PREMI</td>";
            $tab.="</thead><tbody>";
            foreach($arrTgl as $dtTgl){
            //foreach($dtJnsKerj as $lstJnsKrj){
                    $aerta=0;
                    for($mule=1;$mule<=$jmRow[$lstKary.$dtTgl];$mule++){
                        $aerta+=1;
                        if($dafkerja[$lstKary.$dtTgl.$mule]!=''){
                               $tab.="<tr class=rowcontent>";
                                if($dtTgl!=$tglTem){
                                     $tglTem=$dtTgl;
                                     $aret=0;
                                     $tab.="<td>".tanggalnormal($dtTgl)."</td>";
                                 }else{
                                    if($aret==0){
                                        if($jmRow[$lstKary.$dtTgl]==1){
                                            $jmrrodawa=$jmRow[$lstKary.$dtTgl]-1;
                                        }else{
                                            $jmrrodawa=$jmRow[$lstKary.$dtTgl]-1;
                                        }
                                       $tab.="<td rowspan=".$jmrrodawa.">&nbsp;</td>";
                                       $aret=1;
                                     }
                                 }
                                 $blama="kodeorg='".$dafAlokasi[$lstKary.$dtTgl.$lstJnsKrj.$mule]."'";
                                 $optBlokLm=  makeOption($dbname, 'setup_blok', 'kodeorg,bloklama', $blama);
                                 if($optBlokLm[$dafAlokasi[$lstKary.$dtTgl.$lstJnsKrj.$mule]]==''){
                                     $optBlokLm[$dafAlokasi[$lstKary.$dtTgl.$lstJnsKrj.$mule]]=$dafAlokasi[$lstKary.$dtTgl.$lstJnsKrj.$mule];
                                 }
                                 $tab.="<td>".$dafkerja[$lstKary.$dtTgl.$mule]."</td>";
                                 $tab.="<td>".$optJnsKeg[$dafkerja[$lstKary.$dtTgl.$mule]]."</td>";
                                 $tab.="<td align=right>".number_format($dtHslKrj[$lstKary.$dtTgl.$mule],0)."</td>";
                                 $tab.="<td>".$optSatKeg[$dafkerja[$lstKary.$dtTgl.$mule]]."</td>";
                                 $tab.="<td>".$dafNotrans[$lstKary.$dtTgl.$lstJnsKrj.$mule]."</td>";
                                 $tab.="<td>".$optBlokLm[$dafAlokasi[$lstKary.$dtTgl.$lstJnsKrj.$mule]]."</td>";
                                 $tab.="<td align=right>".number_format($dtPremi[$lstKary.$dtTgl.$dafkerja[$lstKary.$dtTgl.$mule].$mule],0)."</td>";
                                 $totTgl[$lstKary]+=$dtPremi[$lstKary.$dtTgl.$dafkerja[$lstKary.$dtTgl.$mule].$mule];
                                 $totPertgl[$lstKary.$dtTgl]+=$dtPremi[$lstKary.$dtTgl.$dafkerja[$lstKary.$dtTgl.$mule].$mule];
                                 //$tab.="<td align=right>".$totTgl[$lstKary.$dtTgl]."</td>";
                                 $tab.="</tr>";
                        }
                        
                    //}
                        
                        
                }
                
                //$tab.="<tr class=rowcontent><td colspan=7>".$_SESSION['lang']['subtotal']."</td><td>".$totPertgl[$lstKary.$dtTgl]."</td></tr>";
                 
        }
        
        if($_SESSION['empl']['regional']=='SULAWESI')
            {$tab.="<tr class=rowcontent><td colspan=7>".$_SESSION['lang']['total']."</td><td>".number_format($totTgl[$lstKary],0)."</td></tr>";}
            else
            {}
        
        //$tab.="<tr class=rowcontent><td colspan=7>".$_SESSION['lang']['total']."</td><td>".number_format($totTgl[$lstKary],0)."</td></tr>";
        $tab.="</tbody></table>";
    }
}
switch($proses){
    case'preview':
        echo $tab;
    break;
case'getKary':
    if($tpKary!=''){
        $add=" and b.tipekaryawan='".$tpKary."'";
    }
    $whr=" b.lokasitugas='".substr($afdId,0,4)."' ";
    $optKary="<option value=''>".$_SESSION['lang']['all']."</option>";
    $sData="select distinct nik,a.karyawanid,namakaryawan from ".$dbname.".vhc_5operator a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid "
         . " where ".$whr." ".$add." order by namakaryawan asc";
    //exit("error".$sData);
    $qData=  mysql_query($sData) or die(mysql_error($conn));
    while($rData=  mysql_fetch_assoc($qData)){
        $optKary.="<option value='".$rData['karyawanid']."'>".$rData['nik']."-".$rData['namakaryawan']."</option>";
        
    }
    echo $optKary;
break;
case'excel':
 $tab.="Print Time:".date('d-m-Y H:i:s')."<br>By:".$_SESSION['empl']['name'];
 $wktu=date("Hms");
 $nop_="kartuKerjaTrkPerKary__".$periode."__".$afdId."___".$wktu;
 if(strlen($tab)>0){
	 $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
	 gzwrite($gztralala, $tab);
	 gzclose($gztralala);
	 echo "<script language=javascript1.2>
		window.location='tempExcel/".$nop_.".xls.gz';
		</script>";
} 
    /*if(strlen($tab)>0)
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
    if(!fwrite($handle,$tab))
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
    }*/    
break;
}

?>
