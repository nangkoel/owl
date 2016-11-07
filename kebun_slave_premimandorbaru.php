<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

	$param=$_POST;
        
$str="select * from ".$dbname.".sdm_5periodegaji where kodeorg='".$param['kodeorg']."' 
      and periode='".$param['periode']."' order by periode desc";
$per=fetchData($str);

if($_GET['proses']!=''){
	$param=$_GET;
}

	$sTarif="select distinct * from ".$dbname.".kebun_5basispanen where 
			 kodeorg='".$_SESSION['empl']['regional']."' and jenis='satuan' order by bjr desc";
	$qTarif=mysql_query($sTarif) or die(mysql_error($conn));
	while($rTarif=  mysql_fetch_assoc($qTarif)){
		$rpLbh[$rTarif['bjr']]=$rTarif['rplebih'];
		$basisPanen[$rTarif['bjr']]=$rTarif['basisjjg'];
		$lstBjr[]=$rTarif['bjr'];
		$lstBjr2[$lstert]=$rTarif['bjr'];
		$lstert++;
	}
	$brd="0";
		$bgrond="class=rowheader";
	if($param['proses']=='excel'){
		$brd="1";
		$bgrond="bgcolor=#DEDEDE align=center";
	}

$tipDt=array("nikasisten"=>"RECORDER","keranimuat"=>"KERANI","nikmandor"=>"MANDOR","nikmandor1"=>"CONDUCTOR");
if($tipDt[$param['tpDt']]=='MANDOR'){
    $pengali=1.5;
}else if($tipDt[$param['tpDt']]=='KERANI' || $tipDt[$param['tpDt']]=='RECORDER'){
    $pengali=1.25;
}
if($tipDt[$param['tpDt']]=='CONDUCTOR'){
    $pengali=1.5;    
}

if(($param['proses']=='preview')||($param['proses']=='excel')){
	    if($tipDt[$param['tpDt']]=='MANDOR'){
                #jumlhhk
//                $blnthn=explode("-",$param['periode']);
//                $jumHari = cal_days_in_month(CAL_GREGORIAN, $blnthn[1], $blnthn[0]);
//                $tgl1=$param['periode']."-01";
//                $tgl2=$param['periode']."-".$jumHari;
//                $date2=tanggalnormal($tgl2);

//                $totHari=dates_inbetween($per[0]['tanggalmulai'],$per[0]['tanggalsampai']);
                #cari jumlah hari minggu
//                $pecahTgl1 = explode("-", $per[0]['tanggalmulai']);
//                $tgl1 = $pecahTgl1[2];
//                $bln1 = $pecahTgl1[1];
//                $thn1 = $pecahTgl1[0];
                $i = 0;
                $sum = $param['hk'];
                do{
                   // mengenerate tanggal berikutnyahttp://blog.rosihanari.net/menghitung-jumlah-hari-minggu-antara-dua-tanggal/
                   $tanggal = nambahHari(tanggalnormal($per[0]['tanggalmulai']),$i,1);
                   $sLbr="select distinct * from ".$dbname.".sdm_5harilibur where 
                                  tanggal='".tanggalsystem($tanggal)."' and regional='".$_SESSION['empl']['regional']."'";
                   $qLbr=mysql_query($sLbr) or die(mysql_error($conn));
                   if(mysql_num_rows($qLbr)==1){
                           $sum-=1;
                   }
                   // increment untuk counter looping
                   $i++;
                }
                while ($tanggal != $per[0]['tanggalsampai']);  
                        #ambil recorder,kerani,mandor
                        $sDt="select distinct nikasisten as recorder,keranimuat,nikmandor,nikmandor1 as conductor from ".$dbname.".kebun_aktifitas
                              where tipetransaksi='PNN' and kodeorg='".$param['kodeorg']."' and tanggal between '".$per[0]['tanggalmulai']."' and '".$per[0]['tanggalsampai']."'";
                        $qDt=mysql_query($sDt) or die(mysql_error($conn));
                        while($rDt=mysql_fetch_assoc($qDt)){
//                                $dtRecorder[$rDt['recorder']]=$rDt['recorder'];
//                                $dtAll[$rDt['recorder']]=$rDt['recorder'];
//                                $dtKrani[$rDt['keranimuat']]=$rDt['keranimuat'];
//                                $dtAll[$rDt['keranimuat']]=$rDt['keranimuat'];
                                $dtMandor[$rDt['nikmandor']]=$rDt['nikmandor'];
                                //$dtAll[$rDt['nikmandor']]=$rDt['nikmandor'];
                                //$dtConductor[$rDt['conductor']]=$rDt['conductor'];
								 $sHk="select count(distinct tanggal) as hk from ".$dbname.".kebun_aktifitas "
									   . " where kodeorg='".$param['kodeorg']."' and tanggal between '".$per[0]['tanggalmulai']."' and '".$per[0]['tanggalsampai']."' and "
									   . " tipetransaksi='PNN' and ".$param['tpDt']."='".$rDt['nikmandor']."'";
									   //exit("error:".$sHk);
								$qHk=  mysql_query($sHk) or die(mysql_error($conn));
								$rHk=  mysql_fetch_assoc($qHk);
								$whrAfd="karyawanid='".$rDt['nikmandor']."'";
								$AfdAsal=makeOption($dbname,'datakaryawan','karyawanid,subbagian',$whrAfd);
								//$jmlHk[$rDt['nikmandor'].$AfdAsal[$rDt['nikmandor']]]=$rHk['hk'];
								$sAct="select * from ".$dbname.".kebun_actingmandor "
										. "where karyid_acting='".$rDt['nikmandor']."' and periodegaji='".$param['periode']."' "
										. "and kodeorg='".$param['kodeorg']."'";
								$qAct=mysql_query($sAct) or die(mysql_error($conn));
								$rAct= mysql_num_rows($qAct);
								if($rAct>0){
									//$jmlHk[$rDt['nikmandor']]=$rHk['hk'];
									$jmlHk[$rDt['nikmandor'].$AfdAsal[$rDt['nikmandor']]]=$rHk['hk'];
								} else {
									$sAct="select * from ".$dbname.".kebun_actingmandor "
										. "where karyawanid='".$rDt['nikmandor']."' and periodegaji='".$param['periode']."' "
										. "and kodeorg='".$param['kodeorg']."'";
									$qAct=mysql_query($sAct) or die(mysql_error($conn));
									$rAct= mysql_num_rows($qAct);
									if($rAct>0){
										//$jmlHk[$rDt['nikmandor']]=$rHk['hk'];
										$jmlHk[$rDt['nikmandor'].$AfdAsal[$rDt['nikmandor']]]=$rHk['hk'];
									}else{
										$jmlHk[$rDt['nikmandor'].$AfdAsal[$rDt['nikmandor']]]=$sum;
										//$jmlHk[$rDt['nikmandor']]=$sum;
									}
								}
                        }
                        foreach($dtMandor as $lstDt=>$dtKary){
                            if($dtKary!=''){
								#query sblmnya
                                /*$sSum="select sum(hasilkerja) as jmltndn,a.nik,left(a.kodeorg,6) as afd from ".$dbname.".kebun_prestasi a left join ".$dbname.".kebun_aktifitas b on     "
                                    . " a.notransaksi=b.notransaksi where  ".$param['tpDt']."='".$dtKary."' and tanggal like '".$param['periode']."%' and a.kodeorg like '".$param['kodeorg']."%' and tarif='SATUAN' and a.kodeorg!=''
                                group by a.nik,left(a.kodeorg,6) order by left(a.kodeorg,6),a.nik asc";*/
								$sSum="select sum(hasilkerja) as jmltndn,a.nik,left(a.kodeorg,6) as afd from ".$dbname.".kebun_prestasi a left join ".$dbname.".kebun_aktifitas b on     "
                                    . " a.notransaksi=b.notransaksi where  ".$param['tpDt']."='".$dtKary."' and tanggal between '".$per[0]['tanggalmulai']."' and '".$per[0]['tanggalsampai']."'  and tarif='SATUAN' and a.kodeorg!=''
                                group by a.nik,".$param['tpDt'].",left(a.kodeorg,6) order by left(a.kodeorg,6),a.nik asc";
                                $qSum=mysql_query($sSum) or die(mysql_error($conn));
                                $rCek=  mysql_num_rows($qSum);
                                if($rCek!=0){
                                    while($rSum=mysql_fetch_assoc($qSum)){	
                                        if($dtKary!=$tempKary){
                                            $tempKary=$dtKary;
                                            $nor=0;
											$whrAfd="karyawanid='".$dtKary."'";
                                            $AfdAsal=makeOption($dbname,'datakaryawan','karyawanid,subbagian',$whrAfd);
											$tempAfd="";
											$areto=true;
                                        }     
										if($rSum['afd']!=$AfdAsal[$dtKary]){
											#ambil hk pemanen dan hk asistensi
											if($areto==true){
												$sHk2="select count(distinct tanggal) as hk from ".$dbname.".kebun_aktifitas a left join 
												 ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi"
												  . " where b.kodeorg like '".$rSum['afd']."%' and tanggal between '".$per[0]['tanggalmulai']."' and '".$per[0]['tanggalsampai']."' and "
												  . " tipetransaksi='PNN' and nikmandor='".$dtKary."'";
												$qHk2=mysql_query($sHk2) or die(mysql_error($conn));
												$rHk2=mysql_fetch_assoc($qHk2);
												$jmlHk[$dtKary.$rSum['afd']]=$rHk2['hk'];
												$jmlhHkAs[$dtKary]=$rHk2['hk'];
												//$jmlhHkAs[$dtKary.$rSum['afd']]=18;
												$areto=false;
											}
										}
											if($rSum['afd']!=$tempAfd){
                                                   $tempAfd=$rSum['afd'];
                                                   $nor+=1;
											}										
                                        $jmlAfd[$dtKary]=$nor;
                                        $dtPemanen[$dtKary.$rSum['nik'].$tempAfd]=$rSum['nik'];
        //                                if($dtPemanen[$dtKary.$rSum['nik'].$tempAfd]!=''){
        //                                    $jmlhPemanen[$dtKary.$tempAfd]+=1;
        //                                }
                                        $dtTandan[$dtKary.$rSum['nik'].$tempAfd]=$rSum['jmltndn'];
                                        $dtAfd[$dtKary.$nor]=$rSum['afd'];
                                        $dtAllPemanen[$rSum['nik']]=$rSum['nik'];
                                    }
                                }
                            }
                        }    
                        foreach($dtMandor as $lstDt=>$dtKary){
                            if($jmlAfd[$dtKary]!=0){
                                $afdTemp="";
                                for($as=1;$as<=$jmlAfd[$dtKary];$as++){
                                    if($afdTemp!=$dtAfd[$dtKary.$as]){
                                    $sBjr="select distinct sum(a.totalkg)/sum(a.jjg) as bjr from ".$dbname.".kebun_spbdt a left join 
                                              ".$dbname.".kebun_spbht b on a.nospb=b.nospb where tanggal between '".$per[0]['tanggalmulai']."' and '".$per[0]['tanggalsampai']."' 
                                              and blok like '".$dtAfd[$dtKary.$as]."%' order by tanggal desc limit 1";
                                    $qBjr=mysql_query($sBjr) or die(mysql_error($conn));
                                    $rBjr=mysql_fetch_assoc($qBjr);
                                        $afdTemp=$dtAfd[$dtKary.$as];
                                    }

                                foreach($lstBjr as $lstRow=>$dtIsiBjr){
                                    if($lstRow==0){
                                        if(intval($rBjr['bjr'])>=$dtIsiBjr){
                                            $rpperkg=$rpLbh[$dtIsiBjr];
                                            break;
                                        }
                                    }else{
                                        if($lstRow!=$MaxRow){
                                            $leapdt=$lstRow-1;
                                            $leapdt2=$lstRow+1;
                                            if((intval($rBjr['bjr'])>=$dtIsiBjr)&&(intval($rBjr['bjr'])<$lstBjr2[$leapdt])){
                                                $rpperkg=$rpLbh[$dtIsiBjr];
                                                break;
                                            }
                                        }else{
                                            $dmin=$dtIsiBjr-1;
                                            $dtbjr=$dtIsiBjr;
                                            if(intval($rBjr['bjr'])>=$dmin){
                                                $rpperkg=$rpLbh[$dtIsiBjr];
                                                break;
                                            }else{
                                                 $rpperkg=$rpLbh[$dtIsiBjr];
                                            }
                                        }
                                    }
                                }
                                $whr="karyawanid='".$dtKary."'";
                                $optNmKar2=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',$whr);                   
                                    //if(count($jmlhPemanen[$dtKary.$dtAfd[$dtKary.$as]])!=0){
                                            $tab.="<table cellpaddng=1 cellspacing=1 border=".$brd." class=sortable><thead>";
                                            $tab.="<tr><td colspan=11>".strtoupper($_SESSION['lang']['namakaryawan'])." : ".strtoupper($optNmKar2[$dtKary]).", BJR : ".$rBjr['bjr'].", Afdeling : ".$dtAfd[$dtKary.$as]."</td></tr>";
                                            $tab.="<tr ".$bgrond."><td>No.</td>";
                                            $tab.="<td>".$_SESSION['lang']['nik']."</td>";
                                            $tab.="<td>".$_SESSION['lang']['namakaryawan']."</td>";
                                            $tab.="<td>".$_SESSION['lang']['bjr']."</td>";
                                            $tab.="<td>".$_SESSION['lang']['hk']."</td>";
                                            $tab.="<td>Jumlah Tandan</td>";
                                            $tab.="<td>Realisasi Panen (KG)</td>";
                                            $tab.="<td>Basic Borong Panen (KG)</td>";
                                            $tab.="<td>Lebih Basis Borong Panen (KG)</td>";
                                            $tab.="<td>Rp/Kg</td>";
                                            $tab.="<td>Premi Panen</td></tr></thead><tbody>";
                                            foreach($dtAllPemanen as $lstPemanen){
													if($dtPemanen[$dtKary.$lstPemanen.$afdTemp]!=''){
													$whrAfd="karyawanid='".$dtKary."'";
													$AfdAsal=makeOption($dbname,'datakaryawan','karyawanid,subbagian',$whrAfd);
													if($jmlhHkAs[$dtKary]!=0){
														if($AfdAsal[$dtKary]==$afdTemp){
															if($jmlHk[$dtKary.$afdTemp]>$sum){
																$jmlHk[$dtKary.$afdTemp]=$sum;
															}
															if($jmlhHkAs[$dtKary]!=0){
																$jmlHk[$dtKary.$afdTemp]=$jmlHk[$dtKary.$afdTemp]-$jmlhHkAs[$dtKary];
															}
															$jmlhHkAs[$dtKary]=0;
														}
													}else{
														if($jmlHk[$dtKary.$afdTemp]>$sum){
																$jmlHk[$dtKary.$afdTemp]=$sum;
														}
													}
														
													
                                                            /* if($jmlHk[$dtKary.$afdTemp]>$sum){
                                                                    $jmlHk[$dtKary.$afdTemp]=$sum;
                                                            }else{
																
																if($areod==1){
																	if($AfdAsal[$dtKary]==$afdTemp){
																		if($jmlhHkAs[$dtKary]!=0){
																			$jmlHk[$dtKary.$afdTemp]=$jmlHk[$dtKary.$afdTemp]-$jmlhHkAs[$dtKary];
																			$areod=0;
																		}
																	}
																}else{
																	if($AfdAsal[$dtKary]!=$afdTemp){
																		$areod=1;
																	}
																}
																
															} */
															//if(count($jmlhHkAs[$dtKary.$afdTemp])!=0){
																//$jmlHk[$dtKary]=count($jmlhHkAs[$dtKary.$afdTemp]);
																//$jmlHk[$dtKary]=$jmlhHkAs[$dtKary.$afdTemp];
															//}
                                                            
															$dtRealPnn[$dtKary.$lstPemanen.$afdTemp]=floatval(str_replace(",","",number_format($dtTandan[$dtKary.$lstPemanen.$afdTemp],2)))*floatval(str_replace(",","",number_format($rBjr['bjr'],2)));
                                                            $dtBasicBrg[$dtKary.$lstPemanen.$afdTemp]=$basisPanen[intval($rBjr['bjr'])]*$jmlHk[$dtKary.$afdTemp]*floatval(str_replace(",","",number_format($rBjr['bjr'],2)));
                                                            $lbhBrg[$dtKary.$lstPemanen.$afdTemp]=$dtRealPnn[$dtKary.$lstPemanen.$afdTemp]-$dtBasicBrg[$dtKary.$lstPemanen.$afdTemp];
                                                            if($lbhBrg[$dtKary.$lstPemanen.$afdTemp]<0){
                                                                $lbhBrg[$dtKary.$lstPemanen.$afdTemp]=0;
                                                            }
                                                           // if($lbhBrg[$dtKary.$lstPemanen.$afdTemp]>0){
                                                            $no+=1;
                                                            $whr="karyawanid='".$dtPemanen[$dtKary.$lstPemanen.$afdTemp]."'";
                                                            $optNmKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',$whr);
                                                            $optNikKar=makeOption($dbname,'datakaryawan','karyawanid,nik',$whr);
                                                            $tab.="<tr class=rowcontent>";
                                                            $tab.="<td>".$no."</td>";
															if($param['proses']=='excel'){
																$tab.="<td>'".$optNikKar[$dtPemanen[$dtKary.$lstPemanen.$afdTemp]]."</td>";
															}else{
																$tab.="<td>".$optNikKar[$dtPemanen[$dtKary.$lstPemanen.$afdTemp]]."</td>";
															}
                                                            
                                                            $tab.="<td>".$optNmKar[$dtPemanen[$dtKary.$lstPemanen.$afdTemp]]."</td>";
                                                            $tab.="<td>".number_format($rBjr['bjr'],2)."</td>";
                                                            $tab.="<td>".$jmlHk[$dtKary.$afdTemp]."</td>";
                                                            $tab.="<td align=right>".number_format($dtTandan[$dtKary.$lstPemanen.$afdTemp],0)."</td>";

                                                            $premiAdlh[$dtKary.$lstPemanen.$afdTemp]=$lbhBrg[$dtKary.$lstPemanen.$afdTemp]*$rpperkg;
                                                            $tab.="<td align=right>".number_format($dtRealPnn[$dtKary.$lstPemanen.$afdTemp],0)."</td>";
                                                            $tab.="<td align=right>".number_format($dtBasicBrg[$dtKary.$lstPemanen.$afdTemp],0)."</td>";
                                                            $tab.="<td align=right>".number_format($lbhBrg[$dtKary.$lstPemanen.$afdTemp],0)."</td>";
                                                            $tab.="<td align=right>".number_format($rpperkg,0)."</td>";
                                                            $tab.="<td align=right>".number_format($premiAdlh[$dtKary.$lstPemanen.$afdTemp],0)."</td>";
                                                            $tab.="</tr>";
                                                                $totTandan[$dtKary.$afdTemp]+=$dtTandan[$dtKary.$lstPemanen.$afdTemp];
                                                                $totRealPanen[$dtKary.$afdTemp]+=$dtRealPnn[$dtKary.$lstPemanen.$afdTemp];
                                                                $totBscBrg[$dtKary.$afdTemp]+=$dtBasicBrg[$dtKary.$lstPemanen.$afdTemp];
                                                                $totLbhBrg[$dtKary.$afdTemp]+=$lbhBrg[$dtKary.$lstPemanen.$afdTemp];
                                                                $totPremi[$dtKary.$afdTemp]+=$premiAdlh[$dtKary.$lstPemanen.$afdTemp];
                                                          //  }
                                                    }
                                            }
                                            $jmlhPemanen=10;
                                            if($no>10){
                                                $jmlhPemanen=$no;
                                            }
                                            $no=0;
                                            $noIdKar+=1;
                                            $tab.="<tr>";
                                            $tab.="<td colspan=5>".$_SESSION['lang']['jumlah']."</td>";
                                            $tab.="<td align=right>".number_format($totTandan[$dtKary.$afdTemp],0)."</td>";
                                            $tab.="<td align=right>".number_format($totRealPanen[$dtKary.$afdTemp],0)."</td>";
                                            $tab.="<td align=right>".number_format($totBscBrg[$dtKary.$afdTemp],0)."</td>";
                                            $tab.="<td align=right>".number_format($totLbhBrg[$dtKary.$afdTemp],0)."</td>";
                                            $tab.="<td align=right>".$rpperkg."</td>";
                                            $tab.="<td align=right>".number_format($totPremi[$dtKary.$afdTemp],0)."</td>";
                                            $tab.="</tr>";
                                            $tab.="<tr>";
                                            $tab.="<td colspan=10>RATA-RATA PREMI PEMANEN </td>";
                                            @$rataPremi[$dtKary.$afdTemp]=$totPremi[$dtKary.$afdTemp]/$jmlhPemanen;
                                            $tab.="<td align=right>".number_format($rataPremi[$dtKary.$afdTemp],0)."<input type=hidden id=rataPremi_".$noIdKar." value='".number_format($rataPremi[$dtKary.$afdTemp],0)."' /></td>";
                                            $tab.="</tr>";
                                            $tab.="<tr>";
                                            $tab.="<td colspan=10>PREMI MANDOR SEBELUM POTONGAN (Pengali : ".$pengali.")</td>";
                                            @$PremiMndr[$dtKary.$afdTemp]=$rataPremi[$dtKary.$afdTemp]*$pengali;
                                            $tab.="<td align=right><input type=hidden value='".number_format($PremiMndr[$dtKary.$afdTemp],0)."'  id='prmiBlmPtg_".$noIdKar."' />".number_format($PremiMndr[$dtKary.$afdTemp],0).
                                                    "<input type=hidden id=premiSblmPtg_".$noIdKar." value='".number_format($PremiMndr[$dtKary.$afdTemp],0)."' />
                                                     <input type=hidden id=premiSblmPtg1_".$noIdKar." value='".number_format($PremiMndr[$dtKary.$afdTemp],0)."' />
                                                     <input type=hidden id=afdId_".$noIdKar." value='".$afdTemp."' /></td>";
                                            $tab.="</tr>";
                                            $tab.="<tr>";
                                            $tab.="<td colspan=10>DENDA (POTONGAN)</td>";
                                            $tab.="<td align=right>";
											if($param['proses']=='excel'){
												$tab.="";
											}else{
												$tab.="<input type=text id=totPtg_".$noIdKar." class='myinputtextnumber' onkeypress='return angka_doang(event)' style='width:150px' onfocus='bersihForm(".$noIdKar.")' onblur=updatePremi(".$noIdKar.") />";
											}
											$tab.="</td>";
                                            $tab.="</tr>";
                                            $tab.="<tr>";
                                            if($PremiDbyr[$dtKary.$afdTemp]<0){
                                                $PremiDbyr[$dtKary.$afdTemp]=0;
                                            }
                                            $PremiDbyr[$dtKary.$afdTemp]=$PremiMndr[$dtKary.$afdTemp]-$param['totPtg'];
                                            $tab.="<td colspan=10>PREMI ".strtoupper($optNmKar2[$dtKary])."<input type=hidden id=mandorId_".$noIdKar." value='".$dtKary."' /><input type=hidden id=premiId_".$noIdKar." value='".number_format($PremiDbyr[$dtKary.$afdTemp],0)."' /></td>";
                                            $tab.="<td align=right  id=totPremiMandor_".$noIdKar.">".number_format($PremiDbyr[$dtKary.$afdTemp],0)."</td>";
                                            $tab.="</tr>";
                                            $tab.="</tbody></table>";
                                    //}
                                }
                            }
                        }
        }else{
            #proses premi untuk recorder/kerani/conductor
				#jumlhhk 
//                $blnthn=explode("-",$param['periode']);
//                $jumHari = cal_days_in_month(CAL_GREGORIAN, $blnthn[1], $blnthn[0]);
//                $tgl1=$param['periode']."-01";
//                $tgl2=$param['periode']."-".$jumHari;
//                $date2=tanggalnormal($tgl2);
//
//                $totHari=dates_inbetween($per[0]['tanggalmulai'],$per[0]['tanggalsampai']);
                #cari jumlah hari minggu
//                $pecahTgl1 = explode("-", $per[0]['tanggalmulai']);
//                $tgl1 = $pecahTgl1[2];
//                $bln1 = $pecahTgl1[1];
//                $thn1 = $pecahTgl1[0];
                $i = 0;
                $sum = $param['hk'];
                do{
                   // mengenerate tanggal berikutnyahttp://blog.rosihanari.net/menghitung-jumlah-hari-minggu-antara-dua-tanggal/
//                   $tanggal = date("d-m-Y", mktime(0, 0, 0, $bln1, $tgl1+$i, $thn1));
                   $tanggal = nambahHari(tanggalnormal($per[0]['tanggalmulai']),$i,1);
                   $sLbr="select distinct * from ".$dbname.".sdm_5harilibur where 
                                  tanggal='".$tanggal."' and regional='".$_SESSION['empl']['regional']."'";
                   $qLbr=mysql_query($sLbr) or die(mysql_error($conn));
                   if(mysql_num_rows($qLbr)==1){
                           $sum-=1;
                   }
                   // increment untuk counter looping
                   $i++;
                }
                while ($tanggal != $per[0]['tanggalsampai']);  
            $sCek="select distinct * from ".$dbname.".kebun_premikemandoran where periode='".$param['periode']."' and kodeorg='".$param['kodeorg']."' and jabatan='MANDOR'";
            $qCek=  mysql_query($sCek) or die(mysql_error($conn));
            $rCek= mysql_num_rows($qCek);
            if($rCek==0){
                exit("warning: Tolong proses jabatan mandor terlebih dahulu");
            }
            $sRKC="select distinct ".$param['tpDt']." from ".$dbname.".kebun_aktifitas where "
                        . "tipetransaksi='PNN' and kodeorg='".$param['kodeorg']."' and tanggal between '".$per[0]['tanggalmulai']."' and '".$per[0]['tanggalsampai']."' and ".$param['tpDt']."!=0";
			//exit("error:".$sRKC);
            $qRKC=  mysql_query($sRKC) or die(mysql_error($conn));
            while($rRKC=  mysql_fetch_assoc($qRKC)){
                $dtAllRKC[$rRKC[$param['tpDt']]]=$rRKC[$param['tpDt']];#data seluruh recorder/kerani/conductor
            }
            foreach($dtAllRKC as $lstRKC){
                    $whr="karyawanid='".$lstRKC."'";
                    $optSbKar=makeOption($dbname,'datakaryawan','karyawanid,subbagian',$whr);
                    $sData="select distinct nikmandor,a.premi as premi,a.afdeling as afdeling,a.premiinput as premiinput,a.potongan as potongan from ".$dbname.".kebun_aktifitas b left join ".$dbname.".kebun_premikemandoran a on b.nikmandor=a.karyawanid"
                         . " where jabatan='MANDOR' and tanggal between '".$per[0]['tanggalmulai']."' and '".$per[0]['tanggalsampai']."' and ".$param['tpDt']."='".$lstRKC."'  and tipetransaksi='PNN'  and periode='".$param['periode']."' group by nikmandor,a.afdeling";
				 //echo  $sData;
                //exit("error:  ".$sData);//and a.afdeling='".$optSbKar[$lstRKC]."'
                $qData=  mysql_query($sData) or die(mysql_error($conn));
                while($rData=  mysql_fetch_assoc($qData)){    
                        $dtMandor[$lstRKC.$rData['afdeling']][$rData['nikmandor']]=$rData['nikmandor'];
                        $sCek="select karyawanid,karyid_acting from ".$dbname.".kebun_actingmandor "
                                . "where karyid_acting='".$rData['nikmandor']."' and periodegaji='".$param['periode']."' "
                                . "and afdeling='".$rData['afdeling']."'";
                        $qCek=  mysql_query($sCek) or die(mysql_error($conn));
						$rRow=mysql_num_rows($qCek);#cek apakah ada acting
                        $rCek=  mysql_fetch_assoc($qCek);
                        if($rRow!=0){     
							#jika ada karyawan acting,maka mandornya (karyawanid) di ambil
                            //$rData['nikmandor']=$rCek['karyawanid'];
							unset($dtMandor[$lstRKC.$rData['afdeling']][$rCek['karyid_acting']]);
                            //unset($dtAllMandor[$rCek['karyid_acting']]);
							//$jmlhOrg[$lstRKC.$rData['afdeling']]-=1;
							
                        }else{
							$jmlhOrg[$lstRKC.$rData['afdeling']]+=1;//pembagi di perhitungan
						}
                        
                     
                    $sjmlKrm="SELECT count(distinct tanggal) as jmlhKirim from ".$dbname.".kebun_spbht a left join ".$dbname.".kebun_spbdt b on a.nospb=b.nospb
                              where blok like '".$rData['afdeling']."%' and tanggal between '".$per[0]['tanggalmulai']."' and '".$per[0]['tanggalsampai']."'";
                    $qjmlKrm=  mysql_query($sjmlKrm) or die(mysql_error($conn));
                    $rjmlKrm=  mysql_fetch_assoc($qjmlKrm);
                    $jmlKirim[$lstRKC.$rData['afdeling']]=$rjmlKrm['jmlhKirim'];
                    $sHk="select count(distinct tanggal) as hk from ".$dbname.".kebun_aktifitas "
                       . " where kodeorg='".$param['kodeorg']."' and tanggal between '".$per[0]['tanggalmulai']."' and '".$per[0]['tanggalsampai']."' and "
                       . " tipetransaksi='PNN' and ".$param['tpDt']."='".$lstRKC."'";
					 //exit("error:".$sHk);
                    $qHk=  mysql_query($sHk) or die(mysql_error($conn));
                    $rHk=  mysql_fetch_assoc($qHk);
					$jmlHk[$lstRKC.$rData['afdeling']]=$rHk['hk'];
					$whrAfd="karyawanid='".$lstRKC."'";
					$AfdAsal=makeOption($dbname,'datakaryawan','karyawanid,subbagian',$whrAfd);
					if($AfdAsal[$lstRKC]!=$rData['afdeling']){
						$sHk2="select count(distinct tanggal) as hk from ".$dbname.".kebun_aktifitas a left join 
							 ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi"
							  . " where b.kodeorg like '".$rData['afdeling']."%' and tanggal between '".$per[0]['tanggalmulai']."' and '".$per[0]['tanggalsampai']."' and "
							  . " tipetransaksi='PNN' and ".$param['tpDt']."='".$lstRKC."'";
						$qHk2=mysql_query($sHk2) or die(mysql_error($conn));
						$rHk2=mysql_fetch_assoc($qHk2);
						$jmlAsis[$lstRKC]=$rHk2['hk'];
						$jmlHk[$lstRKC.$rData['afdeling']]=$rHk2['hk'];
					}
						
                    $afdIdDt[$lstRKC]=$rData['afdeling'];
					$lstAfdeling[$rData['afdeling']]=$rData['afdeling'];
                }
            }
            foreach($dtAllRKC as $lstRKC){
                $whr="karyawanid='".$lstRKC."'";
                $optSbKar=makeOption($dbname,'datakaryawan','karyawanid,subbagian',$whr);
                /*$sData2="select distinct nikmandor,a.premi,a.afdeling,a.premiinput,a.potongan from ".$dbname.".kebun_aktifitas b left join ".$dbname.".kebun_premikemandoran a on b.nikmandor=a.karyawanid"
                             . " where left(tanggal,7)='".$param['periode']."' and jabatan='MANDOR' and periode='".$param['periode']."' and a.afdeling like '".$param['kodeorg']."%'";*/
                $sData2="select distinct nikmandor,a.premi as premi,a.afdeling as afdeling,a.premiinput as premiinput,a.potongan as potongan  from ".$dbname.".kebun_aktifitas b left join ".$dbname.".kebun_premikemandoran a on b.nikmandor=a.karyawanid"
                         . " where jabatan='MANDOR' and tanggal between '".$per[0]['tanggalmulai']."' and '".$per[0]['tanggalsampai']."' and  tipetransaksi='PNN'  and ".$param['tpDt']."='".$lstRKC."' and periode='".$param['periode']."' 
						 group by nikmandor,a.afdeling";
						 //echo $sData2;
                //exit("error:".$sData2);
                $qData2=  mysql_query($sData2) or die(mysql_error($conn));
                while($rData2=  mysql_fetch_assoc($qData2)){
					#jika ada karyawan acting,maka mandornya (karyawanid) di ambil
                    $sCek="select karyawanid,karyid_acting from ".$dbname.".kebun_actingmandor "
                                    . "where karyid_acting='".$rData2['nikmandor']."' and periodegaji='".$param['periode']."' "
                                    . "and afdeling='".$rData2['afdeling']."'";
                    $qCek=  mysql_query($sCek) or die(mysql_error($conn));
					$rRow=mysql_num_rows($qCek);
                    $rCek=  mysql_fetch_assoc($qCek);
                    if($rRow!=0){        
						$rData2['nikmandor']=$rCek['karyawanid'];
                    } 
                    $dtAllMandor[$rData2['nikmandor']]=$rData2['nikmandor'];
                    if($tipDt[$param['tpDt']]=='CONDUCTOR'){
                         $rpRataPremi[$lstRKC.$rData2['afdeling']][$rData2['nikmandor']]+=($rData2['premiinput']+$rData2['potongan']);
                    }else{
                        $rpRataPremi[$lstRKC.$rData2['afdeling']][$rData2['nikmandor']]+=$rData2['premi'];
                    }
					$lstAfdeling[$rData2['afdeling']]=$rData2['afdeling'];//Daftar afdeling yang ada
                }
            }
             foreach($dtAllRKC as $lstRKC){
                    $whr="karyawanid='".$lstRKC."'";
                    $optSbKar=makeOption($dbname,'datakaryawan','karyawanid,subbagian',$whr);
                    $sData="select distinct nikmandor,a.premi as premi,a.afdeling as afdeling,a.premiinput,a.potongan as potongan  from ".$dbname.".kebun_aktifitas b left join ".$dbname.".kebun_premikemandoran a on b.nikmandor=a.karyawanid"
                         . " where jabatan='MANDOR' and tanggal between '".$per[0]['tanggalmulai']."' and '".$per[0]['tanggalsampai']."' and "
                       . " tipetransaksi='PNN' and periode='".$param['periode']."' and ".$param['tpDt']."='".$lstRKC."' group by nikmandor,a.afdeling";
					  //echo $sData;
                    //exit("error".$sData);
                   $qData=  mysql_query($sData) or die(mysql_error($conn));
                   while($rData=  mysql_fetch_assoc($qData)){
                    if($tipDt[$param['tpDt']]=='CONDUCTOR'){
                         $totalRataPremi[$lstRKC.$rData['afdeling']]+=intval($rData['premiinput']+$rData['potongan']);
                         $totPremi[$lstRKC.$rData['afdeling']]+=intval($rData['premiinput']+$rData['potongan']);
                    }else{
                        $totalRataPremi[$lstRKC.$rData['afdeling']]+=$rData['premi'];
                        $totPremi[$lstRKC.$rData['afdeling']]+=$rData['premi'];
                    }
						$lstAfdeling[$rData['afdeling']]=$rData['afdeling'];
						$dafKary[$lstRKC.$rData['afdeling']]=$lstRKC;
                   }
             }
			 
            $tab.="<table cellpadding=1 cellspacing=1 border=".$brd." class=sortable>";
            $tab.="<thead><tr ".$bgrond.">";
            $tab.="<td>No.</td>";
            $tab.="<td>".$_SESSION['lang']['nik']."</td>";
            $tab.="<td>".$_SESSION['lang']['namakaryawan']."</td>";
            $tab.="<td>Rata-rata Premi Pemanen</td>";
            $tab.="<td>Total</td>";
            $tab.="<td>Premi</td>";
            $tab.="<td>Potongan (Rp)</td>";
            $tab.="<td>Premi Bersih</td><td>Premi Didapat</td></tr><thead><tbody>";
            foreach($dtAllRKC as $lstRKC){
					foreach($lstAfdeling as $lstAfd){
						if(count($dtMandor[$lstRKC.$lstAfd])!=0){
							if($jmlHk[$lstRKC.$lstAfd]!=0){
								$whr="karyawanid='".$lstRKC."'";
								$optNmKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',$whr);
								$noKar+=1;
								$tab.="<tr>";
								//$jmlKirim[$lstRKC]=$sum;
								$tab.="<td>".$noKar."</td><td colspan=6>".$optNmKar[$lstRKC].", Jumlah Pengiriman :<span id=jmlKirim_".$noKar.">".$jmlKirim[$lstRKC.$lstAfd]."</span>, Jumlah HK : <span id=jmlhHk_".$noKar.">".$jmlHk[$lstRKC.$lstAfd]."</span>,Pengali : ".$pengali.",".$lstAfd."</td></tr>";
								$noMndr=0;
								foreach($dtAllMandor as $lstAllMandor){
									if($dtMandor[$lstRKC.$lstAfd][$lstAllMandor]!=''){
										/* if($tempIdDt[$lstRKC.$lstAfd][$lstAllMandor]!=$dtMandor[$lstRKC.$lstAfd][$lstAllMandor]){ */
											$tempIdDt[$lstRKC.$lstAfd][$lstAllMandor]=$dtMandor[$lstRKC.$lstAfd][$lstAllMandor];
											$noMndr+=1;
											$whr="karyawanid='".$lstAllMandor."'";
											$optNmKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',$whr);
											$optNikKar=makeOption($dbname,'datakaryawan','karyawanid,nik',$whr);
											$tab.="<tr class=rowcontent>";
											$tab.="<td>".$noMndr."</td>";
											$tab.="<td>".$optNikKar[$lstAllMandor]."</td>";
											$tab.="<td>".$optNmKar[$lstAllMandor]."</td>";
											$tab.="<td align=right>".number_format($rpRataPremi[$lstRKC.$lstAfd][$lstAllMandor],0)."</td>";
											//$art=true;
											if($dafKary[$lstRKC.$lstAfd]!=$tempRKC[$lstRKC.$lstAfd]){
												$tempRKC[$lstRKC.$lstAfd]=$dafKary[$lstRKC.$lstAfd];
												$itung=count($dtMandor[$lstRKC.$lstAfd]);//$dtMandor[$lstRKC.$rData['afdeling']]
												if($itung>1){
													$ades=false;
													//$itung-=1;
												}else{
												   $ades=true;
												}
											}else{
											   continue;
											   $tempRKC="";
											}
											if($ades==false){
												$premiDt[$lstRKC.$lstAfd]=(array_sum($rpRataPremi[$lstRKC.$lstAfd])/$jmlhOrg[$lstRKC.$lstAfd])*$pengali;
												$premiBrsh[$lstRKC.$lstAfd]=$premiDt[$lstRKC.$lstAfd];
												$prmiDpt[$lstRKC.$lstAfd]=$premiBrsh[$lstRKC.$lstAfd]/$jmlKirim[$lstRKC.$lstAfd]*$jmlHk[$lstRKC.$lstAfd];
												$tab.="<td rowspan=".$itung." align=right>".number_format(array_sum($rpRataPremi[$lstRKC.$lstAfd]),0)."</td>";
												$tab.="<td rowspan=".$itung." align=right>".number_format($premiDt[$lstRKC.$lstAfd],0)."</td>";
												$tab.="<td rowspan=".$itung." align=right>";
												if($param['proses']=='excel'){
													$tab.="";
												}else{
													$tab.="<input type=text id=totPtg_".$noKar." class='myinputtextnumber' onkeypress='return angka_doang(event)' style='width:150px' onfocus='bersihForm(".$noKar.")' onblur=updatePremi2(".$noKar.") />";
													$tab.= "<input type=hidden id=premiId_".$noKar." value='".number_format($prmiDpt[$lstRKC.$lstAfd],0)."' />
													   <input type=hidden id=mandorId_".$noKar." value='".$lstRKC."' />
													   <input type=hidden id=rataPremi_".$noKar." value='".number_format($rpRataPremi[$lstAllMandor],0)."' />
													   <input type=hidden id=premiSblmPtg_".$noKar." value='".number_format($premiBrsh[$lstRKC.$lstAfd],0)."' />
													   <input type=hidden id=premiSblmPtg1_".$noKar." value='".number_format($premiBrsh[$lstRKC.$lstAfd],0)."' />                                       
													   <input type=hidden id=premiMandorSblmPtg_".$noKar." value='".number_format($prmiDpt[$lstRKC.$lstAfd],0)."' />                                       
													   <input type=hidden id=premiMandorSblmPtg1_".$noKar." value='".number_format($prmiDpt[$lstRKC.$lstAfd],0)."' />                                       
													   <input type=hidden id=afdId_".$noKar." value='".$lstAfd."' />";
												}
												$tab.="</td>";
												
												$tab.="<td rowspan=".$itung." align=right  id=premiBersih_".$noKar.">".number_format($premiBrsh[$lstRKC.$lstAfd],0)."</td>";
												//$prmiDpt[$lstRKC]=($premiBrsh[$lstRKC]/$jmlKirim[$lstRKC])*$jmlHk[$lstRKC];
												
												$tab.="<td rowspan=".$itung." align=right   id=totPremiMandor_".$noKar.">".number_format($prmiDpt[$lstRKC.$lstAfd],0)."</td>";
												
											}else{
													$premiDt[$lstRKC.$lstAfd]=($totPremi[$lstRKC.$lstAfd]/$jmlhOrg[$lstRKC.$lstAfd])*$pengali;
													$premiBrsh[$lstRKC.$lstAfd]=$premiDt[$lstRKC.$lstAfd];
													$prmiDpt[$lstRKC.$lstAfd]=$premiBrsh[$lstRKC.$lstAfd]/$jmlKirim[$lstRKC.$lstAfd]*$jmlHk[$lstRKC.$lstAfd];
													$tab.="<td align=right>".number_format($totPremi[$lstRKC.$lstAfd],0)."</td>";
													$tab.="<td align=right>".number_format($premiDt[$lstRKC.$lstAfd],0)."</td>";
													$tab.="<td align=right>";
													if($param['proses']=='excel'){
														$tab.="";
													}else{
														$tab.="<input type=text id=totPtg_".$noKar." class='myinputtextnumber' onkeypress='return angka_doang(event)' style='width:150px' onfocus='bersihForm(".$noKar.")' onblur=updatePremi2(".$noKar.") />"
																. "<input type=hidden id=mandorId_".$noKar." value='".$lstRKC."' />
																   <input type=hidden id=rataPremi_".$noKar." value='".number_format($rpRataPremi[$lstAllMandor],0)."' />
																   <input type=hidden id=premiSblmPtg_".$noKar." value='".number_format($premiBrsh[$lstRKC.$lstAfd],0)."' />
																   <input type=hidden id=premiSblmPtg1_".$noKar." value='".number_format($premiBrsh[$lstRKC.$lstAfd],0)."' />
																   <input type=hidden id=premiMandorSblmPtg_".$noKar." value='".number_format($prmiDpt[$lstRKC.$lstAfd],0)."' />                                       
																   <input type=hidden id=premiMandorSblmPtg1_".$noKar." value='".number_format($prmiDpt[$lstRKC.$lstAfd],0)."' />                                       
																   <input type=hidden id=afdId_".$noKar." value='".$lstAfd."' />
																   <input type=hidden id=premiId_".$noKar." value='".number_format($prmiDpt[$lstRKC.$lstAfd],0)."' />";
													}
													$tab.="</td>";
													//$premiBrsh[$lstRKC]=$premiDt[$lstRKC]-$param['totPtg'];
					//                                if($jmlHk[$lstRKC]>$param['hk']){
					//                                    $jmlHk[$lstRKC]=$param['hk'];
					//                                }
													//$prmiDpt[$lstRKC]=($premiBrsh[$lstRKC]/$jmlKirim[$lstRKC])*$jmlHk[$lstRKC];
													
													$tab.="<td align=right id=premiBersih_".$noKar.">".number_format($premiBrsh[$lstRKC.$lstAfd],0)."</td>";
													$tab.="<td align=right id=totPremiMandor_".$noKar.">".number_format($prmiDpt[$lstRKC.$lstAfd],0)."</td>";
											}
											$tab.="</tr>";
										//}
									}
								}
							}
						}
					}
				}
				$noIdKar=$noKar;
				$tab.="</tbody></table>";
			}
		if($param['proses']=='preview'){
			$tab.="<button onclick=saveAll(".$noIdKar.") class=mybutton>".$_SESSION['lang']['save']."</button>";
		}
}

switch($param['proses']){
	case'preview':
    echo $tab;
	break;
	case'excel':
		$nop_="daftarPremiKemandoran";
		if(strlen($tab)>0){
			if ($handle = opendir('tempExcel')) {
					while (false !== ($file = readdir($handle))) {
						if ($file != "." && $file != "..") {
						@unlink('tempExcel/'.$file);
						}
					}	
					closedir($handle);
			}
			$handle=fopen("tempExcel/".$nop_.".xls",'w');
			if(!fwrite($handle,$tab)){
				echo "<script language=javascript1.2>
				parent.window.alert('Can't convert to excel format');
				</script>";
			exit;
			}
			else{
				echo "<script language=javascript1.2>
				window.location='tempExcel/".$nop_.".xls';
				</script>";
			}
			closedir($handle);
		}
	break;
	case'loadData':
		$limit=20;
        $page=0;
        if(isset($_POST['page']))
        {
        $page=$_POST['page'];
        if($page<0)
        $page=0;
        }
        $offset=$page*$limit;
		
		$sql="select count(distinct  periode,jabatan,kodeorg) as jmlhrow from ".$dbname.".kebun_premikemandoran where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by periode desc";
		$query=mysql_query($sql) or die(mysql_error());
        while($jsl=mysql_fetch_object($query)){
			$jlhbrs= $jsl->jmlhrow;
        }
		
		$sData="select distinct periode,jabatan,kodeorg from ".$dbname.".kebun_premikemandoran where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by periode desc limit ".$offset.",".$limit."";
		$qData=mysql_query($sData) or die(mysql_error($conn));
		while($rData=mysql_fetch_assoc($qData)){
			$no+=1;
			$tab.="<tr class=rowcontent>";
			$tab.="<td>".$no."</td>";
			$tab.="<td>".$rData['periode']."</td>";
			$tab.="<td>".$rData['jabatan']."</td>";
			$tab.="<td><img onclick=\"dataKeExcel(event,'kebun_slave_premimandorbaru.php','".$rData['periode'].",".$rData['jabatan'].",".$rData['jabatan'].",".$rData['kodeorg']."')\" src=\"images/excel.jpg\" class=\"resicon\" title=\"MS.Excel\"></td>";
			$tab.="</tr>";
		}
		$tab2="<tr><td colspan=4 align=center>
                                ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
                                <br />
                                <button class=mybutton onclick=loadKemandoran(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                                <button class=mybutton onclick=loadKemandoran(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                                </td>
                                </tr>";
		echo $tab."####".$tab2;
	break;
        case'saveAll':
            $sCek="select sudahproses from ".$dbname.".sdm_5periodegaji where periode='".$param['periode']."' and kodeorg='".$param['kodeorg']."'";
            $qCek=  mysql_query($sCek) or die(mysql_error($conn));
            $rCek=  mysql_fetch_assoc($qCek);
            if($rCek['sudahproses']!=0){
                exit("warning: Periode gaji ".$param['periode']." Sudah di tutup");
            }
            $sDel="delete from ".$dbname.".kebun_premikemandoran where periode='".$param['periode']."' and kodeorg='".$param['kodeorg']."' and jabatan='".$tipDt[$param['tpDt']]."'";
            //exit("warning:".$sDel);
            if(!mysql_query($sDel)){
                    exit("error: ".mysql_error($conn)."___".$sDel);
            }else{
                foreach($_POST['karyId'] as $row=>$lstKary){
                    if(($_POST['premiRp'][$row]!=0)||($_POST['premiRp'][$row]>0)){
                        if($tipDt[$param['tpDt']]=='MANDOR'){
                            if(floatval(str_replace(",","",$_POST['premiRp'][$row]))==floatval(str_replace(",","",$_POST['premiSblmPtg'][$row]))){
                                exit("warning: Denda Potongan Tidak Boleh Kosong");
                            }
                            $potongan=floatval(str_replace(",","",$_POST['premiSblmPtg'][$row]))-floatval(str_replace(",","",$_POST['premiRp'][$row]));
                        } else if($tipDt[$param['tpDt']]=='KERANI' or $tipDt[$param['tpDt']]=='CONDUCTOR' or $tipDt[$param['tpDt']]=='RECORDER'){
//                            if(floatval(str_replace(",","",$_POST['premiRp'][$row]))==floatval(str_replace(",","",$_POST['premiSblmPtg'][$row]))){
//                                exit("warning: Denda Potongan Tidak Boleh Kosong");
//                            }
//                            $potongan=round(floatval(str_replace(",","",$_POST['premiSblmPtg'][$row]))-floatval(str_replace(",","",$_POST['premiRp'][$row])),0);
                            $potongan=round(floatval(str_replace(",","",$_POST['totPtg'][$row])),0);
                            if ($potongan<0)$potongan=0;
                        } else {
                            if(floatval(str_replace(",","",$_POST['premiRp'][$row]))==floatval(str_replace(",","",$_POST['premiSblmPtg1'][$row]))){
                                exit("warning: Denda Potongan Tidak Boleh Kosong");
                            }
                            $potongan=floatval(str_replace(",","",$_POST['premiSblmPtg1'][$row]))-floatval(str_replace(",","",$_POST['premiRp'][$row]));
                        }
                        if($tipDt[$param['tpDt']]=='MANDOR'){
                            $sInsert="insert into ".$dbname.".kebun_premikemandoran (periode,kodeorg,pembagi,jabatan,karyawanid,potongan,premiinput,premi,posting,afdeling,updateby) values ";
                            $sInsert.="('".$param['periode']."','".$param['kodeorg']."','1','".$tipDt[$param['tpDt']]."','".$lstKary."','".$potongan."','".str_replace(",","",$_POST['premiRp'][$row])."','".str_replace(",","",$_POST['rataPremiPemanen'][$row])."','1','".$_POST['afdId'][$row]."','".$_SESSION['standard']['userid']."');";
                            //exit("error:".$sInsert);
                            if(!mysql_query($sInsert)){
                                exit("error: ".mysql_error($conn)."___".$sInsert);
                            }
                        }elseif($tipDt[$param['tpDt']]=='RECORDER'){
                            $sInsert="insert into ".$dbname.".kebun_premikemandoran (periode,kodeorg,pembagi,jabatan,karyawanid,potongan,premiinput,posting,afdeling,updateby) values ";
                            $sInsert.="('".$param['periode']."','".$param['kodeorg']."','1','".$tipDt[$param['tpDt']]."','".$lstKary."','".$potongan."','".str_replace(",","",$_POST['premiRp'][$row])."','1','".$_POST['afdId'][$row]."','".$_SESSION['standard']['userid']."');";
                            //exit("error:".$sInsert);
                            if(!mysql_query($sInsert)){
                                exit("error: ".mysql_error($conn)."___".$sInsert);
                            }
                        }else{
                            $sInsert="insert into ".$dbname.".kebun_premikemandoran (periode,kodeorg,pembagi,jabatan,karyawanid,potongan,premiinput,posting,afdeling,updateby) values ";
                            $sInsert.="('".$param['periode']."','".$param['kodeorg']."','1','".$tipDt[$param['tpDt']]."','".$lstKary."','".$potongan."','".str_replace(",","",$_POST['premiRp'][$row])."','1','".$_POST['afdId'][$row]."','".$_SESSION['standard']['userid']."');";
                            if(!mysql_query($sInsert)){
                                exit("error: ".mysql_error($conn)."___".$sInsert);
                            }
                        }
                    }
                }
            }
        
        break;
}
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
?>
