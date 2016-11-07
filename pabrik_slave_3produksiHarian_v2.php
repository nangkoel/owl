<?php
//@Copy nangkoelframework
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
$param=$_POST;
if($_GET['proses']!=''){
$param=$_GET;
}
	if(($param['proses']=='preview')||($param['proses']=='excel')){
                  
		#ambil data timbangan tbs internal dan afiliasi
		$sTbsInAf="select sum(beratbersih-kgpotsortasi) as totKg,left(tanggal,10) as tanggal,kodeorg from ".$dbname.".pabrik_timbangan where kodebarang='40000003' and millcode='".$param['pabrik']."' and left(tanggal,7)='".$param['periode']."' and kodeorg!='' and nospb!='' group by kodeorg,left(tanggal,10) order by kodeorg,left(tanggal,10) asc";
		//echo $sTbsInAf;
		$qTbsAnf=mysql_query($sTbsInAf) or die(mysql_error($conn));
		while($rTbsInAf=mysql_fetch_assoc($qTbsAnf)){
			$whrind="kodeorganisasi='".$rTbsInAf['kodeorg']."'";
			$optInduk=makeOption($dbname,'organisasi','kodeorganisasi,induk',$whrind);
			$tglDt[$rTbsInAf['tanggal']]=$rTbsInAf['tanggal'];
			$kdOrgDt[$rTbsInAf['kodeorg']]=$rTbsInAf['kodeorg'];
			if($tempPt!=$rTbsInAf['kodeorg']){
				$tempPt=$rTbsInAf['kodeorg'];
				$jlhCols[$optInduk[$rTbsInAf['kodeorg']]]+=1;
				$lstKdorg[$optInduk[$rTbsInAf['kodeorg']].$rTbsInAf['kodeorg']]=$rTbsInAf['kodeorg'];
			}
			$jmlTbs[$rTbsInAf['tanggal'].$optInduk[$rTbsInAf['kodeorg']].$rTbsInAf['kodeorg']]=$rTbsInAf['totKg'];
			$ptInduk[$optInduk[$rTbsInAf['kodeorg']]]=$optInduk[$rTbsInAf['kodeorg']];
		}
		#ambil data timbangan tbs eksternal
		$sTbsInAf2="select sum(beratbersih-kgpotsortasi) as totKg,left(tanggal,10) as tanggal,kodecustomer from ".$dbname.".pabrik_timbangan where kodebarang='40000003' and millcode='".$param['pabrik']."' and left(tanggal,7)='".$param['periode']."' and nospb='' group by kodecustomer,left(tanggal,10) order by left(tanggal,10) asc";
                //echo $sTbsInAf2;
		$qTbsAnf2=mysql_query($sTbsInAf2) or die(mysql_error($conn));
		while($rTbsInAf2=mysql_fetch_assoc($qTbsAnf2)){
                        if($rTbsInAf2['totKg']!=''){
                            $whr="kodetimbangan='".$rTbsInAf2['kodecustomer']."'";
                            $optSupp=  makeOption($dbname, 'log_5supplier', 'kodetimbangan,supplierid', $whr);
                            if($optSupp[$rTbsInAf2['kodecustomer']]!=''){
                                $tglDt[$rTbsInAf2['tanggal']]=$rTbsInAf2['tanggal'];
                                $EksternalDt[$optSupp[$rTbsInAf2['kodecustomer']]]=$optSupp[$rTbsInAf2['kodecustomer']];
                                $eksjmlTbs[$rTbsInAf2['tanggal'].$optSupp[$rTbsInAf2['kodecustomer']]]=$rTbsInAf2['totKg'];
                            }
                        }
		}
                  
		$tglAwl=$param['periode']."-01";
		$tglSblm=nambahHari($tglAwl,1,0);
		
		#ambil sisa tbs kemarin dan tbs di proses
		$sProd="select tbsmasuk,tbsdiolah,sisatbskemarin,sisahariini from ".$dbname.".pabrik_produksi where kodeorg='".$param['pabrik']."' and left(tanggal,7)='".substr($tglSblm,0,7)."' order by tanggal desc limit 1";
		$qProd=mysql_query($sProd) or die(mysql_error($conn));
		$rProd=mysql_fetch_assoc($qProd);
		
		$colsInt=count($kdOrgDt)+1;
		
		$nourutdta=0;
		$nourutdta2=0;
		array_multisort($tglDt,SORT_ASC);
              
		
		#Data CPO
		#tangki
		$sTangki="select kodetangki,komoditi from ".$dbname.".pabrik_5tangki where kodeorg='".$param['pabrik']."'";
		$qTangki=mysql_query($sTangki) or die(mysql_error($conn));
		while($rTangki=mysql_fetch_assoc($qTangki)){
			$lstTangki[strtoupper($rTangki['komoditi']).$rTangki['kodetangki']]=$rTangki['kodetangki'];
			$jmlTngki[strtoupper($rTangki['komoditi'])]+=1;
			$dtTangki[$rTangki['kodetangki']]=$rTangki['kodetangki'];
		}
		
		#quality dan hasil produksi harian cpo dan pk
                $sQty="select distinct tanggal,jumlah,kodetangki from ".$dbname.".pabrik_produksidetail 
			       where left(tanggal,7)='".$param['periode']."' and kodeorg='".$param['pabrik']."'";
                $qQty=mysql_query($sQty) or die(mysql_error($conn));
                while($rQty=mysql_fetch_assoc($qQty)){
                    $whrKomoditi="kodetangki='".$rQty['kodetangki']."'";
                    $optKomoditi=makeOption($dbname,'pabrik_5tangki','kodetangki,komoditi',$whrKomoditi);
                    if(strtoupper($optKomoditi[$rQty['kodetangki']])=='CPO'){
                            $ProdHarianCpo[$rQty['tanggal'].$rQty['kodetangki']]=$rQty['jumlah'];
                    }else{
                            $ProdHarianKer[$rQty['tanggal'].$rQty['kodetangki']]=$rQty['jumlah'];
                    }
                }
		$sQuality="select * from ".$dbname.".pabrik_produksi where kodeorg='".$param['pabrik']."' and tanggal like '".$param['periode']."%'";
		$qQuality=mysql_query($sQuality) or die(mysql_error($conn));
		while($rQuality=mysql_fetch_assoc($qQuality)){
			
			$FfaCpo[$rQuality['tanggal']]=$rQuality['ffa'];
			$MoistCpo[$rQuality['tanggal']]=$rQuality['kadarair'];
			$DirtCpo[$rQuality['tanggal']]=$rQuality['kadarkotoran'];
			$DobiCpo[$rQuality['tanggal']]=$rQuality['dobi'];
			$FfaKer[$rQuality['tanggal']]=$rQuality['ffapk'];
			$MoistKer[$rQuality['tanggal']]=$rQuality['kadarairpk'];
			$DirtKer[$rQuality['tanggal']]=$rQuality['kadarkotoranpk'];
			
			#oil losses dan kernel losses
			$cpoEbs[$rQuality['tanggal']]=$rQuality['ebstalk'];
			$cpoFruitLoss[$rQuality['tanggal']]=$rQuality['fibre'];
			$cpoNut[$rQuality['tanggal']]=$rQuality['nut'];
			$cpoSliddnter[$rQuality['tanggal']]=$rQuality['soliddecanter'];
			$cpoEffluent[$rQuality['tanggal']]=$rQuality['effluent'];
			 
			$kerFruitLoss[$rQuality['tanggal']]=$rQuality['fruitinebker'];
			$kerCyclone[$rQuality['tanggal']]=$rQuality['cyclone'];
			$kerLtds[$rQuality['tanggal']]=$rQuality['ltds'];
			$kerHydro[$rQuality['tanggal']]=$rQuality['claybath'];
                        $tglDt[$rQuality['tanggal']]=$rQuality['tanggal'];
			$tbsProses[$rQuality['tanggal']]=$rQuality['tbsdiolah'];#tbs di proses
		}
			
			
		#Kapasitas Olah
		$sOlah="select jamdinasbruto as jampengolahan, jamstagnasi as jamstagnasi,tanggal,jamdinasbruto,nopengolahan from 
		        ".$dbname.".pabrik_pengolahan  
			    where kodeorg='".$param['pabrik']."' and tanggal like '".$param['periode']."%' order by tanggal asc";
		$qOlah=mysql_query($sOlah) or die(mysql_error($conn));
		while($rOlah=mysql_fetch_assoc($qOlah)){
			$dd=split("\.",$rOlah['jampengolahan']);
			$ee=split("\.",$rOlah['jamstagnasi']);
			@$jamOlah[$rOlah['tanggal']]+=  $dd[0]+($dd[1]/60); 
			@$jamStag[$rOlah['tanggal']]+=$ee[0]+($ee[1]/60); 
			$sKet="select keterangan from ".$dbname.".pabrik_pengolahanmesin where nopengolahan='".$rOlah['nopengolahan']."'";
			$qKet=mysql_query($sKet) or die(mysql_error($conn));
			$rKet=mysql_fetch_assoc($qKet);
			if($rKet['keterangan']!=''){
				$ketProses[$rOlah['tanggal']]=$rKet['keterangan'];
			}
			 
		}
		#sales & dispatch
		$sSales="select sum(beratbersih) as jumlah,left(tanggal,10) as tanggal,sloc,kodebarang from ".$dbname.".pabrik_timbangan where millcode='".$param['pabrik']."' and tanggal like '".$param['periode']."%' and kodebarang in ('40000001','40000002','40000007') group by sloc,kodebarang,left(tanggal,10) order by kodebarang asc";
		//echo $sSales;
		$qSales=mysql_query($sSales) or die(mysql_error($conn));
		while($rSales=mysql_fetch_assoc($qSales)){
                        if($rSales['sloc']==''){
                            if($rSales['kodebarang']!='40000002'){
                                $rSales['sloc']='ST03';
                            }else{
                                $rSales['sloc']='BLK02';
                            }
                        }
			if($rSales['kodebarang']!='40000002'){
				$krmCpo[$rSales['tanggal'].$rSales['sloc']]+=$rSales['jumlah'];
			}else{
				$krmKer[$rSales['tanggal'].$rSales['sloc']]+=$rSales['jumlah'];
			}
		}
                
                #stock tanggal terakhir bulan lalu
		$sProdTglLalu="select kodetangki,left(tanggal,10) as tanggal,kuantitas,cporendemen,cpoffa,cpokdair,cpodobi,cpokdkot
		        ,kernelquantity,kernelrendemen,kernelkdair,kernelkdkot,kernelffa,kerneldobi
				from ".$dbname.".pabrik_masukkeluartangki 
		        where left(tanggal,10)= '".$tglSblm."' and kodeorg='".$param['pabrik']."' order by left(tanggal,10) desc";
		$qProdTglLalu=mysql_query($sProdTglLalu) or die(mysql_error($conn));
		while($rProdTglLalu=mysql_fetch_assoc($qProdTglLalu)){
			$qtyLaluCpo[$rProdTglLalu['kodetangki']]=$rProdTglLalu['kuantitas'];
			$qtyLaluKer[$rProdTglLalu['kodetangki']]=$rProdTglLalu['kernelquantity'];
			$CpoFfaLalu[$rProdTglLalu['kodetangki']]=$rProdTglLalu['cpoffa'];
			$CpoMoistLalu[$rProdTglLalu['kodetangki']]=$rProdTglLalu['cpokdair'];
			$CpoDirtLalu[$rProdTglLalu['kodetangki']]=$rProdTglLalu['cpokdkot'];
			$CpoDobiLalu[$rProdTglLalu['kodetangki']]=$rProdTglLalu['cpodobi'];
			$KerFfaLalu[$rProdTglLalu['kodetangki']]=$rProdTglLalu['cpoffa'];
			$KerMoistLalu[$rProdTglLalu['kodetangki']]=$rProdTglLalu['cpokdair'];
			$KerDirtLalu[$rProdTglLalu['kodetangki']]=$rProdTglLalu['cpokdair'];
			$KerDobiLalu[$rProdTglLalu['kodetangki']]=$rProdTglLalu['kerneldobi'];
		}
		
		#stock per tangki dan kualitas 
		$sProd="select kodetangki,left(tanggal,10) as tanggal,kuantitas,cporendemen,cpoffa,cpokdair,cpodobi
		        ,kernelquantity,kernelrendemen,kernelkdair,kernelkdkot,kernelffa,kerneldobi
				from ".$dbname.".pabrik_masukkeluartangki where tanggal like '".$param['periode']."%' and kodeorg='".$param['pabrik']."'";
		$qProd=mysql_query($sProd) or die(mysql_error($conn));
		while($rProd=mysql_fetch_assoc($qProd)){
			$qtyCpo[$rProd['tanggal'].$rProd['kodetangki']]=$rProd['kuantitas'];
			$qtyKer[$rProd['tanggal'].$rProd['kodetangki']]=$rProd['kernelquantity'];
			$dtCpoFfa[$rProd['tanggal'].$rProd['kodetangki']]=$rProd['cpoffa'];
			$dtCpoMoist[$rProd['tanggal'].$rProd['kodetangki']]=$rProd['cpokdair'];
			$dtCpoDirt[$rProd['tanggal'].$rProd['kodetangki']]=$rProd['cpokdair'];
			$dtCpoDobi[$rProd['tanggal'].$rProd['kodetangki']]=$rProd['cpodobi'];
			$dtKerFfa[$rProd['tanggal'].$rProd['kodetangki']]=$rProd['cpoffa'];
			$dtKerMoist[$rProd['tanggal'].$rProd['kodetangki']]=$rProd['cpokdair'];
			$dtKerDirt[$rProd['tanggal'].$rProd['kodetangki']]=$rProd['cpokdair'];
			$dtKerDobi[$rProd['tanggal'].$rProd['kodetangki']]=$rProd['kerneldobi'];
		}
		#budget bulan ini
		$jmlBln=explode("-",$param['periode']);
                $whrreg="kodeunit='".$param['pabrik']."'";
                $optReg=makeOption($dbname,'bgt_regional_assignment','kodeunit,regional',$whrreg);
		#budget produksi kebun BI (bulan ini)
		$sKgTbsBgd="select sum(kg".$jmlBln[1].") as kgblnini,kodeunit from ".$dbname.".bgt_produksi_kbn_kg_vw 
		            where kodeunit in (select kodeorg from ".$dbname.".pabrik_timbangan where millcode='".$optReg[$param['pabrik']]."' and nospb!='' and left(tanggal,7)='".$param['periode']."' and kodebarang='40000003')  and tahunbudget='".substr($param['periode'],0,4)."' group by kodeunit";
		$qKgTbsBgd=mysql_query($sKgTbsBgd) or die(mysql_error($conn));
		while($rKgTbsBgd=mysql_fetch_assoc($qKgTbsBgd)){
			$whrind="kodeorganisasi='".$rKgTbsBgd['kodeunit']."'";
			$optInduk=makeOption($dbname,'organisasi','kodeorganisasi,induk',$whrind);
			$bgdProdBlnini[$optInduk[$rKgTbsBgd['kodeunit']].$rKgTbsBgd['kodeunit']]=$rKgTbsBgd['kgblnini'];
			$totBgtBlnini+=$rKgTbsBgd['kgblnini'];
		}
		
		#budget dan realisasi produksi kebun Bulan Sebelumnya
		$jmlBlnSblm=explode("-",$tglSblm);
		$listSblm.="sum(";
		$listSblm2.="sum(";
		for($awal=1;$awal<=intval($jmlBlnSblm[1]);$awal++){
			if($awal==1){
				$listSblm.="kg01";
				$listSblm2.="olah01";
			}else{
				if($awal<10){
					$listSblm.="+kg0".$awal;
					$listSblm2.="+olah0".$awal;
				}else{
					$listSblm.="+kg".$awal;
					$listSblm2.="+olah".$awal;
				}
			}
		}
		$listSblm.=")";
		$listSblm2.=")";
		$sKgTbsBgd="select ".$listSblm." as kgblnlalu,kodeunit from ".$dbname.".bgt_produksi_kbn_kg_vw 
		            where kodeunit in (select kodeorg from ".$dbname.".pabrik_timbangan where millcode='".$param['pabrik']."' and nospb!='' and left(tanggal,7)='".$param['periode']."' and kodebarang='40000003')  and tahunbudget='".substr($param['periode'],0,4)."' group by kodeunit";
		$qKgTbsBgd=mysql_query($sKgTbsBgd) or die(mysql_error($conn));
		while($rKgTbsBgd=mysql_fetch_assoc($qKgTbsBgd)){
			$whrind="kodeorganisasi='".$rKgTbsBgd['kodeunit']."'";
			$optInduk=makeOption($dbname,'organisasi','kodeorganisasi,induk',$whrind);
			$bgdProdBlnLalu[$optInduk[$rKgTbsBgd['kodeunit']].$rKgTbsBgd['kodeunit']]=$rKgTbsBgd['kgblnlalu'];
			$totBgtBlnLalu+=$rKgTbsBgd['kgblnini'];
		}
		#realisasi bulan lalu
		$sRealBlnLalu="select sum(beratbersih-kgpotsortasi) as jumlah,kodeorg from ".$dbname.".pabrik_timbangan where left(tanggal,7)<'".$param['periode']."' and  left(tanggal,4)='".substr($param['periode'],0,4)."'
			       and millcode='".$param['pabrik']."' and char_length(nospb)='22' and 
			       kodebarang='40000003' and kodeorg!='' group by kodeorg";
                //echo $sRealBlnLalu;
		$qRealBlnLalu=mysql_query($sRealBlnLalu) or die(mysql_error($conn));
		while($rRealBlnLalu=mysql_fetch_assoc($qRealBlnLalu)){
			$whrind="kodeorganisasi='".$rRealBlnLalu['kodeorg']."'";
			$optInduk=makeOption($dbname,'organisasi','kodeorganisasi,induk',$whrind);
			$realBlnLalu[$optInduk[$rRealBlnLalu['kodeorg']].$rRealBlnLalu['kodeorg']]=$rRealBlnLalu['jumlah'];
			$totRealBlnLalu+=$rRealBlnLalu['jumlah'];
		}
		$sRealBlnIniEks2="select sum(beratbersih-kgpotsortasi) as totKg,kodecustomer from ".$dbname.".pabrik_timbangan 
		                  where left(tanggal,7)<'".$param['periode']."' and left(tanggal,4)='".substr($param['periode'],0,4)."' and  kodebarang='40000003' and millcode='".$param['pabrik']."'"
                              . "  and char_length(nospb)='0' group by kodecustomer";
                
		$qRealBlnIniEks2=mysql_query($sRealBlnIniEks2) or die(mysql_error($conn));
		while($rRealBlnIniEks2=mysql_fetch_assoc($qRealBlnIniEks2)){
                        if($rRealBlnIniEks2['totKg']!=''){
                        $whr="kodetimbangan='".$rRealBlnIniEks2['kodecustomer']."'";
                        $optSupp=  makeOption($dbname, 'log_5supplier', 'kodetimbangan,supplierid', $whr);
                            if($optSupp[$rRealBlnIniEks2['kodecustomer']]!=''){
                                if(intval($rRealBlnIniEks2['totKg'])!=0){
                                    $realBlnLaluEkst[$optSupp[$rRealBlnIniEks2['kodecustomer']]]=$rRealBlnIniEks2['totKg'];
                                    $EksternalDt[$optSupp[$rRealBlnIniEks2['kodecustomer']]]=$optSupp[$rRealBlnIniEks2['kodecustomer']];
                                }
                            }
                        }
		}
                  
		
		#budget kebun s.d buln ini
		for($awal=1;$awal<=intval($jmlBln[1]);$awal++){
			if($awal==1){
				$list.="sum(kg01";
				$list2.="sum(olah01";
			}else{
				if($awal<10){
					$list.="+kg0".$awal;
					$list2.="+olah0".$awal;
				}else{
					$list.="+kg".$awal;
					$list2.="+olah".$awal;
				}
			}
		}
		$list.=")";
		$list2.=")";
               
		$sKgTbsBgdIni="select ".$list." as kgblnsd,kodeunit from ".$dbname.".bgt_produksi_kbn_kg_vw 
        where kodeunit in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment  where regional='".$optReg[$param['pabrik']]."') and tahunbudget='".substr($param['periode'],0,4)."' group by kodeunit";
		$qKgTbsBgdIni=mysql_query($sKgTbsBgdIni) or die(mysql_error($conn));
		while($rKgTbsBgdIni=mysql_fetch_assoc($qKgTbsBgdIni)){
			$whrind="kodeorganisasi='".$rKgTbsBgdIni['kodeunit']."'";
			$optInduk=makeOption($dbname,'organisasi','kodeorganisasi,induk',$whrind);
			$bgdProdBlnSd[$optInduk[$rKgTbsBgdIni['kodeunit']].$rKgTbsBgdIni['kodeunit']]=$rKgTbsBgdIni['kgblnsd'];
			$totBgtBlnSd+=$rKgTbsBgdIni['kgblnsd'];
		}
		#tbs di proses dan produksi cpo,kernel s.d bulan lalu
		$sProdLalu="select sum(tbsdiolah) as tbsproses from ".$dbname.".pabrik_produksi 
		          where kodeorg='".$param['pabrik']."' and left(tanggal,7)<'".$param['periode']."' or left(tanggal,4)='".substr($param['periode'],0,4)."'  order by tanggal desc limit 1";
		$qProdLalu=mysql_query($sProdLalu) or die(mysql_error($conn));
		$rProdLalu=mysql_fetch_assoc($qProdLalu);
		$sCpoLalu="select sum(jumlah) as cpoProd from ".$dbname.".pabrik_produksidetail 
			   where left(tanggal,7)<'".$param['periode']."' and left(tanggal,4)='".substr($param['periode'],0,4)."'  and kodeorg='".$param['pabrik']."'
			   and kodetangki in (select kodetangki from ".$dbname.".pabrik_5tangki where kodeorg='".$param['pabrik']."' and komoditi='CPO')";
		$qCpoLalu=mysql_query($sCpoLalu) or die(mysql_error($conn));
		$rCpoLalu=mysql_fetch_assoc($qCpoLalu);
		
		$sKerLalu="select sum(jumlah) as kerProd from ".$dbname.".pabrik_produksidetail 
			   where left(tanggal,7)<'".$param['periode']."' and left(tanggal,4)='".substr($param['periode'],0,4)."' and kodeorg='".$param['pabrik']."'
			   and kodetangki in (select kodetangki from ".$dbname.".pabrik_5tangki where kodeorg='".$param['pabrik']."' and (komoditi='KER' or left(kodetangki,1)='B'))";
		$qKerLalu=mysql_query($sKerLalu) or die(mysql_error($conn));
		$rKerLalu=mysql_fetch_assoc($qKerLalu);
		
		
		#realisasi s.d bulan ini
		$sRealBlnIni="select sum(beratbersih-kgpotsortasi) as jumlah,kodeorg from ".$dbname.".pabrik_timbangan where left(tanggal,7)<='".$param['periode']."' and left(tanggal,4)='".substr($param['periode'],0,4)."'
		              and kodebarang='40000003' and millcode='".$param['pabrik']."' and char_length(nospb)='22' and kodeorg!='' group by kodeorg";
                //echo $sRealBlnIni;
		$qRealBlnIni=mysql_query($sRealBlnIni) or die(mysql_error($conn));
		while($rRealBlnIni=mysql_fetch_assoc($qRealBlnIni)){
			$whrind="kodeorganisasi='".$rRealBlnIni['kodeorg']."'";
			$optInduk=makeOption($dbname,'organisasi','kodeorganisasi,induk',$whrind);
			$realBlnSd[$optInduk[$rRealBlnIni['kodeorg']].$rRealBlnIni['kodeorg']]=$rRealBlnIni['jumlah'];
		}
		$sRealBlnIniEks="select sum(beratbersih-kgpotsortasi) as totKg,kodecustomer from ".$dbname.".pabrik_timbangan 
		                 where left(tanggal,7)<='".$param['periode']."' and left(tanggal,4)='".substr($param['periode'],0,4)."' and kodebarang='40000003' and millcode='".$param['pabrik']."' and char_length(nospb)='0'
				  group by kodecustomer";
                //echo $sRealBlnIniEks;
		$qRealBlnIniEks=mysql_query($sRealBlnIniEks) or die(mysql_error($conn));
		while($rRealBlnIniEks=mysql_fetch_assoc($qRealBlnIniEks)){
                    if(intval($rRealBlnIniEks['totKg'])!=0){
                        $whr="kodetimbangan='".$rRealBlnIniEks['kodecustomer']."'";
                        $optSupp=  makeOption($dbname, 'log_5supplier', 'kodetimbangan,supplierid', $whr);
                        if($optSupp[$rRealBlnIniEks['kodecustomer']]!=''){
                            $realBlnSdEkst[$optSupp[$rRealBlnIniEks['kodecustomer']]]=$rRealBlnIniEks['totKg'];
                            $EksternalDt[$optSupp[$rRealBlnIniEks['kodecustomer']]]=$optSupp[$rRealBlnIniEks['kodecustomer']];
                        }
                    }
		}
                  
		#tbs di proses dan produksi cpo,kernel s.d bulan ini
		$sProdSd="select sum(tbsdiolah) as tbsproses from ".$dbname.".pabrik_produksi 
		          where kodeorg='".$param['pabrik']."' and left(tanggal,7)<='".$param['periode']."' or left(tanggal,4)='".substr($param['periode'],0,4)."' order by tanggal desc limit 1";
		$qProdSd=mysql_query($sProdSd) or die(mysql_error($conn));
		$rProdSd=mysql_fetch_assoc($qProdSd);
		
		$sCpoSd="select sum(jumlah) as cpoProd from ".$dbname.".pabrik_produksidetail 
			   where left(tanggal,7)<='".$param['periode']."' and left(tanggal,4)='".substr($param['periode'],0,4)."' and kodeorg='".$param['pabrik']."'
			   and kodetangki in (select kodetangki from ".$dbname.".pabrik_5tangki where kodeorg='".$param['pabrik']."' and komoditi='CPO')";
                //echo $sCpoSd;
		$qCposd=mysql_query($sCpoSd) or die(mysql_error($conn));
		$rCposd=mysql_fetch_assoc($qCposd);
		
		$sKerSd="select sum(jumlah) as kerProd from ".$dbname.".pabrik_produksidetail 
			   where left(tanggal,7)<='".$param['periode']."' or left(tanggal,4)='".substr($param['periode'],0,4)."' and kodeorg='".$param['pabrik']."'
			   and kodetangki in (select kodetangki from ".$dbname.".pabrik_5tangki where kodeorg='".$param['pabrik']."' and komoditi='KER' or left(kodetangki,1)='B')";
		$qKersd=mysql_query($sKerSd) or die(mysql_error($conn));
		$rKersd=mysql_fetch_assoc($qKersd);
		
		#budget pabrik proses olah bulan ini
		$sBgtOlah="select sum(olah".$jmlBln[1].") as prosesblnini,oerbunch,oerkernel from ".$dbname.".bgt_produksi_pks 
		           where millcode='".$param['pabrik']."'  and tahunbudget='".substr($param['periode'],0,4)."'";
		$qBgtOlah=mysql_query($sBgtOlah) or die(mysql_error($conn));
		$rBgtOlah=mysql_fetch_assoc($qBgtOlah);
		
		$sBgtOlah2="select sum(olah".$jmlBln[1].") as tbsEks,kodeunit from ".$dbname.".bgt_produksi_pks 
		           where millcode='".$param['pabrik']."'  and tahunbudget='".substr($param['periode'],0,4)."'
				   and char_length(kodeunit)>4 group by kodeunit";
                //echo $sBgtOlah2;
		$qBgtOlah2=mysql_query($sBgtOlah2) or die(mysql_error($conn));
		while($rBgtOlah2=mysql_fetch_assoc($qBgtOlah2)){
                    $whr="kodetimbangan='".$rBgtOlah2['kodeunit']."'";
                    $optSupp=  makeOption($dbname, 'log_5supplier', 'kodetimbangan,supplierid', $whr);
                    if($optSupp[$rBgtOlah2['kodeunit']]!=''){
                        $bgtTbsEksBi[$optSupp[$rBgtOlah2['kodeunit']]]=$rBgtOlah2['tbsEks'];
                        $EksternalDt[$optSupp[$rBgtOlah2['kodeunit']]]=$optSupp[$rBgtOlah2['kodeunit']];
                    }
                }
                  
		#budget pabrik proses olah s.d bulan lalu
		$sBgtOlahLalu="select ".$listSblm2." as prosesblnlalu,oerbunch,oerkernel from ".$dbname.".bgt_produksi_pks 
		           where millcode='".$param['pabrik']."'  and tahunbudget='".substr($param['periode'],0,4)."'";
		
		$qBgtOlahLalu=mysql_query($sBgtOlahLalu) or die(mysql_error($conn));
		$rBgtOlahLalu=mysql_fetch_assoc($qBgtOlahLalu);
		
		$sBgtOlahLalu2="select ".$listSblm2." as tbsEksLalu,kodeunit from ".$dbname.".bgt_produksi_pks 
		           where millcode='".$param['pabrik']."'  and tahunbudget='".substr($param['periode'],0,4)."'
				   and char_length(kodeunit)>4 group by kodeunit";
		$qBgtOlahLalu2=mysql_query($sBgtOlahLalu2) or die(mysql_error($conn));
		while($rBgtOlahLalu2=mysql_fetch_assoc($qBgtOlahLalu2)){
                    $whr="kodetimbangan='".$rBgtOlahLalu2['kodeunit']."'";
                    $optSupp=  makeOption($dbname, 'log_5supplier', 'kodetimbangan,supplierid', $whr);
                    if($optSupp[$rBgtOlahLalu2['kodeunit']]!=''){
                        $bgtTbsEksBlalu[$optSupp[$rBgtOlahLalu2['kodeunit']]]=$rBgtOlahLalu2['tbsEks'];
                        $EksternalDt[$optSupp[$rBgtOlahLalu2['kodeunit']]]=$optSupp[$rBgtOlahLalu2['kodeunit']];
                    }
                }
		
		#budget pabrik proses oleh bulan sd
		$sBgtOlahSd="select ".$list2." as prosesblnsd,oerbunch,oerkernel from ".$dbname.".bgt_produksi_pks 
		           where millcode='".$param['pabrik']."'  and tahunbudget='".substr($param['periode'],0,4)."'";
		$qBgtOlahSd=mysql_query($sBgtOlahSd) or die(mysql_error($conn));
		$rBgtOlahSd=mysql_fetch_assoc($qBgtOlahSd);
		
		$sBgtOlahSd2="select ".$list2." as tbsEksSd from ".$dbname.".bgt_produksi_pks 
		           where millcode='".$param['pabrik']."'  and tahunbudget='".substr($param['periode'],0,4)."'
				   and char_length(kodeunit)>4";
		$qBgtOlahSd2=mysql_query($sBgtOlahSd2) or die(mysql_error($conn));
		$rBgtOlahSd2=mysql_fetch_assoc($qBgtOlahSd2);
		$brd=0;
		$bgcolordt="";
		if($param['proses']=='excel'){
			$brd=1;
			$bgcolordt=" bgcolor=#DEDEDE align=center";
		}
                $colsEks=count($EksternalDt)+1;
		if($colsEks==1){
			$colEk=0;
		}else{
			$colEk=$colsEks;
		}
		
		$tab.="<table cellpadding=1 cellspacing=1 border=".$brd." class=sortable>";
		$tab.="<thead><tr>";
		$tab.="<td rowspan=4  ".$bgcolordt.">".$_SESSION['lang']['tanggal']."</td>";//
		$tab.="<td colspan=".($colsInt+$colEk+3)."  ".$bgcolordt.">TANDAN BUAH SEGAR ( Kg )</td>";
		$tab.="<td colspan='".($jmlTngki['CPO']+7)."'  ".$bgcolordt.">CRUDE PALM OIL ( Kg )</td>";
		$tab.="<td colspan='".($jmlTngki['KER']+6)."' ".$bgcolordt.">PALM KERNEL ( Kg )</td>";
		$tab.="<td colspan='4' ".$bgcolordt.">Kapasitas Olah (T/J)</td>";
		$tab.="<td colspan='".($jmlTngki['CPO']+$jmlTngki['KER']+2)."'  ".$bgcolordt.">SALES & DISPATCH</td>";
		$tab.="<td colspan='".(($jmlTngki['CPO']*5)+($jmlTngki['KER']*5)+2)."'  ".$bgcolordt.">STOCK DI PKS</td>";
		$tab.="<td rowspan=4  ".$bgcolordt.">&nbsp;</td>";
		$tab.="<td colspan=26  ".$bgcolordt.">Rasio</td>";
		$tab.="</tr><tr>";
		$tab.="<td colspan=".$colsInt."  ".$bgcolordt.">TBS Kebun Sendiri (internal)</td>";
		if($colsEks!=1){
			$tab.="<td colspan=".$colsEks."  ".$bgcolordt.">TBS Luar Kebun (eksternal)</td>";
		}
		$tab.="<td rowspan=3  ".$bgcolordt.">Total Penerimaan</td>";
		$tab.="<td rowspan=3  ".$bgcolordt.">Proses</td>";
		$tab.="<td rowspan=3 ".$bgcolordt.">Saldo Akhir</td>";
		$tab.="<td rowspan=2 colspan=".($jmlTngki['CPO']+1)."  ".$bgcolordt.">Produksi Hari   ini</td>";
		$tab.="<td rowspan=2 ".$bgcolordt.">OER</td>";							
		$tab.="<td rowspan=2 ".$bgcolordt.">FFA</td>";
		$tab.="<td rowspan=2 ".$bgcolordt.">DOBI</td>";
		$tab.="<td rowspan=2 ".$bgcolordt.">Dirt</td>";
		$tab.="<td rowspan=2 ".$bgcolordt.">Moist</td>";
		$tab.="<td rowspan=2 ".$bgcolordt.">Total Loses</td>";
		$tab.="<td rowspan=2 colspan=".($jmlTngki['KER']+1)."  ".$bgcolordt.">Produksi Hari   ini</td>";
		$tab.="<td rowspan=2 ".$bgcolordt.">KER</td>";							
		$tab.="<td rowspan=2 ".$bgcolordt.">FFA</td>";
		//$tab.="<td rowspan=2 ".$bgcolordt.">DOBI</td>";
		$tab.="<td rowspan=2 ".$bgcolordt.">Dirt</td>";
		$tab.="<td rowspan=2 ".$bgcolordt.">Moist</td>";
		$tab.="<td rowspan=2 ".$bgcolordt.">Total Loses</td>";
		$tab.="<td rowspan=3 ".$bgcolordt.">Jam Proses</td>";
		$tab.="<td rowspan=3 ".$bgcolordt.">Jam Rusak</td>";
		$tab.="<td rowspan=3 ".$bgcolordt.">Remarks</td>";
		$tab.="<td rowspan=3 ".$bgcolordt.">Kap. Olah</td>";
		$tab.="<td rowspan=2 colspan=".($jmlTngki['CPO']+1)." ".$bgcolordt.">CPO</td>";
		$tab.="<td rowspan=2 colspan=".($jmlTngki['KER']+1)." ".$bgcolordt.">KERNEL</td>";
		$tab.="<td colspan=".(($jmlTngki['CPO']*5)+1)." ".$bgcolordt.">CPO</td>";
		$tab.="<td colspan=".(($jmlTngki['KER']*5)+1)." ".$bgcolordt.">KERNEL</td>";
		$tab.="<td rowspan=2 colspan=6 ".$bgcolordt.">CPO</td>";
		$tab.="<td rowspan=3 ".$bgcolordt.">&nbsp;</td>";
		$tab.="<td rowspan=2 colspan=5 ".$bgcolordt.">Kernel</td>";
		$tab.="<td rowspan=3 ".$bgcolordt.">&nbsp;</td>";
		$tab.="<td rowspan=3 ".$bgcolordt.">USB</td>";
		$tab.="<td rowspan=2 colspan=6 ".$bgcolordt.">OIL LOSSES %</td>";
		$tab.="<td rowspan=2 colspan=6 ".$bgcolordt.">KERNEL LOSSES %</td>";
		$tab.="</tr>";
		$tab.="<tr ".$bgcolordt.">";
			foreach($ptInduk as $lstIndk){
				$tab.="<td colspan=".$jlhCols[$lstIndk]." ".$bgcolordt.">".$lstIndk."</td>";
			}
			$tab.="<td rowspan=2 ".$bgcolordt.">".$_SESSION['lang']['total']."</td>";
			if($colsEks!=1){
				if(!empty($EksternalDt)){
					foreach($EksternalDt as $lstEks){
						$whr="supplierid='".$lstEks."'";
						$optSupp=makeOption($dbname,'log_5supplier','supplierid,namasupplier',$whr);
							$tab.="<td rowspan=2 ".$bgcolordt.">".$optSupp[$lstEks]."</td>";
					}
				}
				$tab.="<td rowspan=2 ".$bgcolordt.">".$_SESSION['lang']['total']."</td>";
			}else{
				$colEk=0;
			}
			foreach($dtTangki as $rowTangki){
				if($lstTangki['CPO'.$rowTangki]!=''){
						$tab.="<td colspan=5 ".$bgcolordt.">".$lstTangki['CPO'.$rowTangki]."</td>";
				}
			}
			$tab.="<td  rowspan=2>".$_SESSION['lang']['total']." KG</td>";
			foreach($dtTangki as $rowTangki){
				if($lstTangki['KER'.$rowTangki]!=''){
						$tab.="<td colspan=5 ".$bgcolordt.">".$lstTangki['KER'.$rowTangki]."</td>";
				}
			}
			$tab.="<td rowspan=2 ".$bgcolordt.">".$_SESSION['lang']['total']." KG</td>";
		$tab.="</tr>";
		$tab.="<tr>";
			foreach($ptInduk as $lstIndk){
				foreach($kdOrgDt as $lstKbn){
					if($lstKdorg[$lstIndk.$lstKbn]!=''){
					 $tab.="<td ".$bgcolordt.">".$lstKdorg[$lstIndk.$lstKbn]."</td>";
					}
				}				
			}
			foreach($dtTangki as $rowTangki){
				if($lstTangki['CPO'.$rowTangki]!=''){
						$tab.="<td ".$bgcolordt.">".$lstTangki['CPO'.$rowTangki]."</td>";
				}
			}
			$tab.="<td ".$bgcolordt.">".$_SESSION['lang']['total']."</td>";
			$tab.="<td ".$bgcolordt.">%</td>";
			$tab.="<td ".$bgcolordt.">%</td>";
			$tab.="<td ".$bgcolordt.">%</td>";
			$tab.="<td ".$bgcolordt.">%</td>";
			$tab.="<td ".$bgcolordt.">%</td>";
			$tab.="<td ".$bgcolordt.">%</td>";
			foreach($dtTangki as $rowTangki){
				if($lstTangki['KER'.$rowTangki]!=''){
						$tab.="<td ".$bgcolordt.">".$lstTangki['KER'.$rowTangki]."</td>";
				}
			}
			$tab.="<td ".$bgcolordt.">".$_SESSION['lang']['total']."</td>";
			$tab.="<td ".$bgcolordt.">%</td>";
			$tab.="<td ".$bgcolordt.">%</td>";
			$tab.="<td ".$bgcolordt.">%</td>";
			$tab.="<td ".$bgcolordt.">%</td>";
			$tab.="<td ".$bgcolordt.">%</td>";
			//$tab.="<td ".$bgcolordt.">%</td>";
			foreach($dtTangki as $rowTangki){
				if($lstTangki['CPO'.$rowTangki]!=''){
						$tab.="<td ".$bgcolordt.">".$lstTangki['CPO'.$rowTangki]."</td>";
				}
			}
			$tab.="<td ".$bgcolordt.">".$_SESSION['lang']['total']."</td>";
			foreach($dtTangki as $rowTangki){
				if($lstTangki['KER'.$rowTangki]!=''){
						$tab.="<td ".$bgcolordt.">".$lstTangki['KER'.$rowTangki]."</td>";
				}
			}
			$tab.="<td ".$bgcolordt.">".$_SESSION['lang']['total']."</td>";
			foreach($dtTangki as $rowTangki){
				if($lstTangki['CPO'.$rowTangki]!=''){
						$tab.="<td ".$bgcolordt.">KG</td>";  	  	  	  
						$tab.="<td ".$bgcolordt.">FFA</td>";
						$tab.="<td ".$bgcolordt.">DOBI</td>";
						$tab.="<td ".$bgcolordt.">MOIST</td>";
						$tab.="<td ".$bgcolordt.">DIRT</td>";
				}
			}
			foreach($dtTangki as $rowTangki){
				if($lstTangki['KER'.$rowTangki]!=''){
						$tab.="<td ".$bgcolordt.">KG</td>";  	  	  	  
						$tab.="<td ".$bgcolordt.">FFA</td>";
						$tab.="<td ".$bgcolordt.">DOBI</td>";
						$tab.="<td ".$bgcolordt.">MOIST</td>";
						$tab.="<td ".$bgcolordt.">DIRT</td>";
				}
			} 

			$tab.="<td ".$bgcolordt.">Rasio</td>";
			$tab.="<td ".$bgcolordt.">FFA</td>";
			$tab.="<td ".$bgcolordt.">Dobi</td>";
			$tab.="<td ".$bgcolordt.">Dirt</td>";
			$tab.="<td ".$bgcolordt.">Moist</td>";
			$tab.="<td ".$bgcolordt.">Losses</td>";				
			$tab.="<td ".$bgcolordt.">Rasio</td>";
			$tab.="<td ".$bgcolordt.">FFA</td>";
			$tab.="<td ".$bgcolordt.">Dobi</td>";
			$tab.="<td ".$bgcolordt.">Dirt</td>";
			$tab.="<td ".$bgcolordt.">Losses</td>";
			$tab.="<td ".$bgcolordt.">EBS</td>";
			$tab.="<td ".$bgcolordt.">FRUIT LOSS IN EB</td>";
			$tab.="<td ".$bgcolordt.">PRESS CAKE FIBRE</td>";
			$tab.="<td ".$bgcolordt.">SOLID DECANTER</td>";
			$tab.="<td ".$bgcolordt.">FINAL EFFLUEN</td>"; 
			$tab.="<td ".$bgcolordt.">TOTAL OIL LOSSES</td>";
			$tab.="<td ".$bgcolordt.">FRUIT LOSS IN EB</td>";
			$tab.="<td ".$bgcolordt.">CYCLONE FIBRE</td>";
			$tab.="<td ".$bgcolordt.">HYDRO CYCLONE</td>";
			$tab.="<td ".$bgcolordt.">LTDS 1</td>"; 

			$tab.="<td ".$bgcolordt.">KERNEL LOSS</td>";
			$tab.="<td ".$bgcolordt.">BROKEN KERNEL</td>";
		$tab.="</tr></thead><tbody>";
		#data awal start
		$tab.="<tr class=rowcontent>";
		$tab.="<td></td><td colspan=".($colsInt+$colEk+2)."  align=right>".$_SESSION['lang']['saldoawal']."</td>";
		$tab.="<td  align=right>".number_format($rProd['sisahariini'],0)."</td>";
		$tab.="<td  colspan='".($jmlTngki['CPO']+7)."'>&nbsp;</td>";
		$tab.="<td  colspan='".($jmlTngki['KER']+6)."'>&nbsp;</td>";
		$tab.="<td  colspan='4'>&nbsp;</td>";
		$tab.="<td  colspan='".($jmlTngki['CPO']+$jmlTngki['KER']+2)."'>&nbsp;</td>";
		foreach($dtTangki as $rowTangki){
				if($lstTangki['CPO'.$rowTangki]!=''){
						$tab.="<td align=right>".number_format($qtyLaluCpo[$lstTangki['CPO'.$rowTangki]],0)."</td>";  	  	  	  
						$tab.="<td align=right>".number_format($CpoFfaLalu[$lstTangki['CPO'.$rowTangki]],2)."</td>";
						$tab.="<td align=right>".number_format($CpoDobiLalu[$lstTangki['CPO'.$rowTangki]],2)."</td>";
						$tab.="<td align=right>".number_format($CpoMoistLalu[$lstTangki['CPO'.$rowTangki]],2)."</td>";
						$tab.="<td align=right>".number_format($CpoDirtLalu[$lstTangki['CPO'.$rowTangki]],2)."</td>";
						$totCpoKgLalu+=$qtyLaluCpo[$lstTangki['CPO'.$rowTangki]];
				}
		}
		$tab.="<td align=right>".number_format($totCpoKgLalu,0)."</td>"; 
		foreach($dtTangki as $rowTangki){
				if($lstTangki['KER'.$rowTangki]!=''){
						$tab.="<td align=right>".number_format($qtyLaluKer[$lstTangki['KER'.$rowTangki]],0)."</td>";  	  	  	  
						$tab.="<td align=right>".number_format($KerFfaLalu[$lstTangki['KER'.$rowTangki]],2)."</td>";
						$tab.="<td align=right>".number_format($KerDobiLalu[$lstTangki['KER'.$rowTangki]],2)."</td>";
						$tab.="<td align=right>".number_format($KerMoistLalu[$lstTangki['KER'.$rowTangki]],2)."</td>";
						$tab.="<td align=right>".number_format($KerDirtLalu[$lstTangki['KER'.$rowTangki]],2)."</td>";
						$totKerKgLalu+=$qtyLaluCpo[$lstTangki['KER'.$rowTangki]];
				}
		}
		$tab.="<td align=right>".number_format($totKerKgLalu,0)."</td>"; 
		$tab.="<td>&nbsp;</td>";
		$tab.="<td colspan=26>&nbsp;</td>";
		$tab.="</tr>";
		#data awal end
                array_multisort($tglDt,SORT_ASC);
		foreach($tglDt as $lstTgl){
                    if($lstTgl!=''){
			$tglSblmnya=nambahHari($lstTgl,1,0);#mengurangi satu hari
			$tab.="<tr class=rowcontent>";
			$tab.="<td>".$lstTgl."</td>";
			#tbs internal dan eksternal
			foreach($ptInduk as $lstIndk){
				foreach($kdOrgDt as $lstKbn){
					if($lstKdorg[$lstIndk.$lstKbn]!=''){
						$tab.="<td align=right>".number_format($jmlTbs[$lstTgl.$lstIndk.$lstKbn],0)."</td>";
						$totTbs[$lstIndk]+=$jmlTbs[$lstTgl.$lstIndk.$lstKbn];
						$totPerTgl[$lstTgl]+=$jmlTbs[$lstTgl.$lstIndk.$lstKbn];#total tbs internal per tanggal
						$grandDiv[$lstIndk.$lstKbn]+=$jmlTbs[$lstTgl.$lstIndk.$lstKbn];#total tbs per divisi
						$grandTotInt+=$jmlTbs[$lstTgl.$lstIndk.$lstKbn];
					}
				}
			}
			$tab.="<td align=right>".number_format($totPerTgl[$lstTgl],0)."</td>";
			if($colsEks!=1){
				if(!empty($EksternalDt)){
					foreach($EksternalDt as $lstEks){
                                            if($eksjmlTbs[$lstTgl.$lstEks]!=''){
							$tab.="<td  align=right>".number_format($eksjmlTbs[$lstTgl.$lstEks],0)."</td>";
							$totPerTglEks[$lstTgl]+=$eksjmlTbs[$lstTgl.$lstEks];#total tbs eksternal per tanggal
							$grandEksTbs[$lstEks]+=$eksjmlTbs[$lstTgl.$lstEks];
							$grandTotEks+=$eksjmlTbs[$lstTgl.$lstEks];
                                            }else{
                                                $tab.="<td  align=right>0</td>";
                                            }
					}
				}
				$tab.="<td align=right>".number_format($totPerTglEks[$lstTgl],0)."</td>";
			}
			#totalpenerimaan tbs
			$totPenerimaan[$lstTgl]=$totPerTglEks[$lstTgl]+$totPerTgl[$lstTgl];
			$grandTotTbsTerima+=$totPenerimaan[$lstTgl];
			$tab.="<td align=right>".number_format($totPenerimaan[$lstTgl],0)."</td>";
			$tab.="<td align=right>".number_format($tbsProses[$lstTgl],0)."</td>";
			$grandTbsProses+=$tbsProses[$lstTgl];
			#saldoAkhir tbs
			if($nourutdta==0){
				$salAkhirTbs[$lstTgl]=($totPenerimaan[$lstTgl]+$rProd['sisahariini'])-$tbsProses[$lstTgl];
				$nourutdta=1;
			}else{
				$salAkhirTbs[$lstTgl]=($totPenerimaan[$lstTgl]+$salAkhirTbs[$tglSblmnya])-$tbsProses[$lstTgl];
			}
			$tab.="<td align=right>".number_format($salAkhirTbs[$lstTgl],0)."</td>";
		 
			
			foreach($dtTangki as $rowTangki){
				if($lstTangki['CPO'.$rowTangki]!=''){
					$tab.="<td align=right>".number_format($ProdHarianCpo[$lstTgl.$lstTangki['CPO'.$rowTangki]],0)."</td>";
					$grandCpoPerTangki[$lstTangki['CPO'.$rowTangki]]+=$ProdHarianCpo[$lstTgl.$lstTangki['CPO'.$rowTangki]];
                                        if($optReg[$param['pabrik']]=='SULAWESI'){
                                            if(($lstTangki['CPO'.$rowTangki]=='ST01')||($lstTangki['CPO'.$rowTangki]=='ST02')){
                                                $granTotProdCpo+=$ProdHarianCpo[$lstTgl.$lstTangki['CPO'.$rowTangki]];
                                                $totProdCpo[$lstTgl]+=$ProdHarianCpo[$lstTgl.$lstTangki['CPO'.$rowTangki]];
                                            }
                                        }else{
                                            $granTotProdCpo+=$ProdHarianCpo[$lstTgl.$lstTangki['CPO'.$rowTangki]];
                                            $totProdCpo[$lstTgl]+=$ProdHarianCpo[$lstTgl.$lstTangki['CPO'.$rowTangki]];
                                        }
					
				}
				
			}
			#oerCpo dan produksi harian
			@$oerCpo[$lstTgl]=($totProdCpo[$lstTgl]/$tbsProses[$lstTgl])*100;
			$tab.="<td align=right>".number_format($totProdCpo[$lstTgl],0)."</td>";
			$tab.="<td  align=right>".number_format($oerCpo[$lstTgl],2)."</td>";							
			$tab.="<td  align=right>".number_format($FfaCpo[$lstTgl],2)."</td>";			
			$tab.="<td  align=right>".number_format($DobiCpo[$lstTgl],2)."</td>";
			$tab.="<td  align=right>".number_format($DirtCpo[$lstTgl],2)."</td>";
			$tab.="<td  align=right>".number_format($MoistCpo[$lstTgl],2)."</td>";
			$tab.="<td>%</td>";
			foreach($dtTangki as $rowTangki){
				if($lstTangki['KER'.$rowTangki]!=''){
					$tab.="<td align=right>".number_format($ProdHarianKer[$lstTgl.$lstTangki['KER'.$rowTangki]],0)."</td>";
					$totProdKer[$lstTgl]+=$ProdHarianKer[$lstTgl.$lstTangki['KER'.$rowTangki]];
					$grandKerPerTangki[$lstTangki['KER'.$rowTangki]]+=$ProdHarianKer[$lstTgl.$lstTangki['KER'.$rowTangki]];
					$granTotProdKer+=$ProdHarianKer[$lstTgl.$lstTangki['KER'.$rowTangki]];
				}
			}
			@$oerKer[$lstTgl]=($totProdKer[$lstTgl]/$tbsProses[$lstTgl])*100;
			$tab.="<td align=right>".number_format($totProdKer[$lstTgl],0)."</td>";
			$tab.="<td  align=right>".number_format($oerKer[$lstTgl],2)."</td>";							
			$tab.="<td  align=right>".number_format($FfaKer[$lstTgl],2)."</td>";			
			//$tab.="<td  align=right>".number_format($DobiKer[$lstTgl],2)."</td>";
			$tab.="<td  align=right>".number_format($DirtKer[$lstTgl],2)."</td>";
			$tab.="<td  align=right>".number_format($MoistKer[$lstTgl],2)."</td>";
			$tab.="<td>%</td>";
			$tab.="<td align=right>".number_format($jamOlah[$lstTgl],0)."</td>";
			$tab.="<td align=right>".number_format($jamStag[$lstTgl],0)."</td>";
			$tab.="<td>".$ketProses[$lstTgl]."</td>";
			$grandJamOlah+=$jamOlah[$lstTgl];
			$grandJamStag+=$jamStag[$lstTgl];
			#Ton/Jam
			@$tonJam[$lstTgl]=($tbsProses[$lstTgl]/1000)/$jamOlah[$lstTgl];
			$tab.="<td align=right>".number_format($tonJam[$lstTgl],0)."</td>";
			#sales and dispatch
			foreach($dtTangki as $rowTangki){
				if($lstTangki['CPO'.$rowTangki]!=''){
					$tab.="<td align=right>".number_format($krmCpo[$lstTgl.$lstTangki['CPO'.$rowTangki]],0)."</td>";
					$totKrmCpo[$lstTgl]+=$krmCpo[$lstTgl.$lstTangki['CPO'.$rowTangki]];
					$grandKrmCpoPerTangki[$lstTangki['CPO'.$rowTangki]]+=$krmCpo[$lstTgl.$lstTangki['CPO'.$rowTangki]];
					$grandKrmCpo+=$krmCpo[$lstTgl.$lstTangki['CPO'.$rowTangki]];
				} 
				
			}
			$tab.="<td align=right>".number_format($totKrmCpo[$lstTgl],0)."</td>";
			foreach($dtTangki as $rowTangki){
				if($lstTangki['KER'.$rowTangki]!=''){
					$tab.="<td align=right>".number_format($krmKer[$lstTgl.$lstTangki['KER'.$rowTangki]],0)."</td>";
					$totKrmKer[$lstTgl]+=$krmKer[$lstTgl.$lstTangki['KER'.$rowTangki]];
					$grandKrmKerPerTangki[$lstTangki['KER'.$rowTangki]]+=$krmKer[$lstTgl.$lstTangki['KER'.$rowTangki]];
					$grandKrmKer+=$krmKer[$lstTgl.$lstTangki['KER'.$rowTangki]];
				} 
				
			}
			$tab.="<td align=right>".number_format($totKrmKer[$lstTgl],0)."</td>";
			#stock per tangki di pks
			#rumus
			#utk pertama kali stock=saldo awal(saldo akhir bulan kmrn)+produksi hari ini-pengiriman
			#selanjutnya stock=stock kmrn+produksi hari ini-pengiriman
			foreach($dtTangki as $rowTangki){
				if($lstTangki['CPO'.$rowTangki]!=''){
					if($nourutdta2==0){
						$stockCpo[$lstTgl.$lstTangki['CPO'.$rowTangki]]=$ProdHarianCpo[$lstTgl.$lstTangki['CPO'.$rowTangki]]+$qtyLaluCpo[$lstTangki['CPO'.$rowTangki]]-$krmCpo[$lstTgl.$lstTangki['CPO'.$rowTangki]];
					}else{
						$stockCpo[$lstTgl.$lstTangki['CPO'.$rowTangki]]=$ProdHarianCpo[$lstTgl.$lstTangki['CPO'.$rowTangki]]+$stockCpo[$tglSblmnya.$lstTangki['CPO'.$rowTangki]]-$krmCpo[$lstTgl.$lstTangki['CPO'.$rowTangki]];        
					}
					$tab.="<td align=right>".number_format($stockCpo[$lstTgl.$lstTangki['CPO'.$rowTangki]],0)."</td>";  	  	  	  
					$tab.="<td align=right>".number_format($dtCpoFfa[$lstTgl.$lstTangki['CPO'.$rowTangki]],2)."</td>";
					$tab.="<td align=right>".number_format($dtCpoDobi[$lstTgl.$lstTangki['CPO'.$rowTangki]],2)."</td>";
					$tab.="<td align=right>".number_format($dtCpoMoist[$lstTgl.$lstTangki['CPO'.$rowTangki]],2)."</td>";
					$tab.="<td align=right>".number_format($dtCpoDirt[$lstTgl.$lstTangki['CPO'.$rowTangki]],2)."</td>";
					$totCpo[$lstTgl]+=$stockCpo[$lstTgl.$lstTangki['CPO'.$rowTangki]];
                                        $grndCpo[$lstTangki['CPO'.$rowTangki]]+=$stockCpo[$lstTgl.$lstTangki['CPO'.$rowTangki]];
					
				} 
			}
			$tab.="<td align=right>".number_format($totCpo[$lstTgl],0)."</td>";
			$grandTotStockCpo=$totCpo[$lstTgl];
			foreach($dtTangki as $rowTangki){
				if($lstTangki['KER'.$rowTangki]!=''){
					if($nourutdta2==0){
						$stockKer[$lstTgl.$lstTangki['KER'.$rowTangki]]=$ProdHarianKer[$lstTgl.$lstTangki['KER'.$rowTangki]]+$qtyLaluKer[$lstTangki['KER'.$rowTangki]]-$krmKer[$lstTgl.$lstTangki['KER'.$rowTangki]];
						$nourutdta2=1;
					}else{
						$stockKer[$lstTgl.$lstTangki['KER'.$rowTangki]]=$ProdHarianKer[$lstTgl.$lstTangki['KER'.$rowTangki]]+$stockKer[$tglSblmnya.$lstTangki['KER'.$rowTangki]]-$krmKer[$lstTgl.$lstTangki['KER'.$rowTangki]];
					}
					$tab.="<td align=right>".number_format($stockKer[$lstTgl.$lstTangki['KER'.$rowTangki]],0)."</td>";  	  	  	  
					$tab.="<td align=right>".number_format($dtKerFfa[$lstTgl.$lstTangki['KER'.$rowTangki]],2)."</td>";
					$tab.="<td align=right>".number_format($dtKerDobi[$lstTgl.$lstTangki['KER'.$rowTangki]],2)."</td>";
					$tab.="<td align=right>".number_format($dtKerMoist[$lstTgl.$lstTangki['KER'.$rowTangki]],2)."</td>";
					$tab.="<td align=right>".number_format($dtKerDirt[$lstTgl.$lstTangki['KER'.$rowTangki]],2)."</td>";
					$totKer[$lstTgl]+=$stockKer[$lstTgl.$lstTangki['KER'.$rowTangki]];
                                        $grndKer[$lstTangki['KER'.$rowTangki]]+=$stockKer[$lstTgl.$lstTangki['KER'.$rowTangki]];
				} 
			}
			$tab.="<td align=right>".number_format($totKer[$lstTgl],0)."</td>";
			$grandTotStockKer=$totKer[$lstTgl];
			$tab.="<td></td>";//pemisah
			#rasio cpo dan kernel
			@$rasCpo[$lstTgl]=$totProdCpo[$lstTgl]/$granTotProdCpo;
			$RasFfaCpo[$lstTgl]=$rasCpo[$lstTgl]*$FfaCpo[$lstTgl];
			$RasDobiCpo[$lstTgl]=$rasCpo[$lstTgl]*$DobiCpo[$lstTgl];
			$RasDirtCpo[$lstTgl]=$rasCpo[$lstTgl]*$DirtCpo[$lstTgl];
			$RasMoistCpo[$lstTgl]=$rasCpo[$lstTgl]*$MoistCpo[$lstTgl];
			
			$tab.="<td align=right>".number_format($rasCpo[$lstTgl],0)."</td>";
			$tab.="<td align=right>".number_format($RasFfaCpo[$lstTgl],2)."</td>";
			$tab.="<td align=right>".number_format($RasDobiCpo[$lstTgl],2)."</td>";
			$tab.="<td align=right>".number_format($RasDirtCpo[$lstTgl],2)."</td>";
			$tab.="<td align=right>".number_format($RasMoistCpo[$lstTgl],2)."</td>";
			$tab.="<td>&nbsp;</td>";//total loses
			$tab.="<td>&nbsp;</td>";//pemisah
			@$rasKer[$lstTgl]=$totProdKer[$lstTgl]/$granTotProdKer;
			$RasFfaKer[$lstTgl]=$rasKer[$lstTgl]*$FfaKer[$lstTgl];
			$RasDirtKer[$lstTgl]=$rasKer[$lstTgl]*$DirtKer[$lstTgl];
			$RasMoistKer[$lstTgl]=$rasKer[$lstTgl]*$MoistKer[$lstTgl];
			$tab.="<td align=right>".number_format($rasKer[$lstTgl],0)."</td>";
			$tab.="<td align=right>".number_format($RasFfaKer[$lstTgl],2)."</td>";
			$tab.="<td align=right>".number_format($RasDirtKer[$lstTgl],2)."</td>";
			$tab.="<td align=right>".number_format($RasMoistKer[$lstTgl],2)."</td>";
			$grandFfaCpo+=$RasFfaCpo[$lstTgl];
			$grandDobiCpo+=$RasDobiCpo[$lstTgl];
			$grandDirtCpo+=$RasDirtCpo[$lstTgl];
			$grandMoistCpo+=$RasMoistCpo[$lstTgl];
			$grandRasKer+=$rasKer[$lstTgl];
			$grandRasCpo+=$rasCpo[$lstTgl];
			$grandFfaKer+=$RasFfaKer[$lstTgl];
			$grandDirtKer+=$RasDirtKer[$lstTgl];
			$grandMoistKer+=$RasMoistKer[$lstTgl];
			$tab.="<td>&nbsp;</td>";//total loses
			$tab.="<td>&nbsp;</td>";//pemisah
			$tab.="<td  align=right>&nbsp;</td>";//USB
			$tab.="<td align=right>".number_format($cpoEbs[$lstTgl],2)."</td>";
			$tab.="<td align=right>".number_format($cpoFruitLoss[$lstTgl],2)."</td>";
			$tab.="<td align=right>".number_format($cpoNut[$lstTgl],2)."</td>";
			$tab.="<td align=right>".number_format($cpoSliddnter[$lstTgl],2)."</td>";
			$tab.="<td align=right>".number_format($cpoEffluent[$lstTgl],2)."</td>";
			$totalLosesCpo[$lstTgl]=$cpoEbs[$lstTgl]+$cpoFruitLoss[$lstTgl]+$cpoNut[$lstTgl]+$cpoSliddnter[$lstTgl]+$cpoEffluent[$lstTgl];
                        $grndTotalLosesCpo+=$totalLosesCpo[$lstTgl];
			$tab.="<td align=right>".number_format($totalLosesCpo[$lstTgl],2)."</td>";
			$tab.="<td align=right>".number_format($kerFruitLoss[$lstTgl],2)."</td>";
			$tab.="<td align=right>".number_format($kerCyclone[$lstTgl],2)."</td>";
			$tab.="<td align=right>".number_format($kerHydro[$lstTgl],2)."</td>";
			$tab.="<td align=right>".number_format($kerLtds[$lstTgl],2)."</td>";
			$totalLosesKer[$lstTgl]=$kerFruitLoss[$lstTgl]+$kerCyclone[$lstTgl]+$kerHydro[$lstTgl]+$kerLtds[$lstTgl];
                        $grndTotalLosesKer+=$totalLosesKer[$lstTgl];
			$tab.="<td align=right>".number_format($totalLosesKer[$lstTgl],2)."</td>";
			$tab.="<td>&nbsp;</td>";//BROKEN KERNEL
			$tab.="</tr>";
		}
                }
		$tab.="</tbody>";
		$tab.="<tfoot>";
                
                
		$tab.="<tr>";
		$tab.="<td align=right><b>".$_SESSION['lang']['grnd_total']."</b></td>";
		foreach($ptInduk as $lstIndk){
			foreach($kdOrgDt as $lstKbn){
				if($lstKdorg[$lstIndk.$lstKbn]!=''){
					$tab.="<td align=right>".number_format($grandDiv[$lstIndk.$lstKbn],0)."</td>";
				}
			}
		}
		$tab.="<td align=right>".number_format($grandTotInt,0)."</td>";
		foreach($EksternalDt as $lstEks){
			$tab.="<td  align=right>".number_format($grandEksTbs[$lstEks],0)."</td>";
		}
		$tab.="<td align=right>".number_format($grandTotEks,0)."</td>";
		$tab.="<td align=right>".number_format($grandTotTbsTerima,0)."</td>";
		$tab.="<td align=right>".number_format($grandTbsProses,0)."</td>";
		$grandSalakTbs=$salAkhirTbs[$lstTgl];
		$tab.="<td align=right>".number_format($grandSalakTbs,0)."</td>";
		foreach($dtTangki as $rowTangki){
				if($lstTangki['CPO'.$rowTangki]!=''){
					$tab.="<td align=right>".number_format($grandCpoPerTangki[$lstTangki['CPO'.$rowTangki]],0)."</td>";
				}
		}
		$tab.="<td align=right>".number_format($granTotProdCpo,0)."</td>";
		@$oerCpoGrnd=($granTotProdCpo/$grandTbsProses)*100;
		$tab.="<td align=right>".number_format($oerCpoGrnd,2)."</td>";
		$tab.="<td align=right>".number_format($grandFfaCpo,2)."</td>";
		$tab.="<td align=right>".number_format($grandMoistCpo,2)."</td>";
		$tab.="<td align=right>".number_format($grandDirtCpo,2)."</td>";
		$tab.="<td align=right>".number_format($grandDobiCpo,2)."</td>";
		$tab.="<td align=right>%</td>";
		
		foreach($dtTangki as $rowTangki){
				if($lstTangki['KER'.$rowTangki]!=''){
					$tab.="<td align=right>".number_format($grandKerPerTangki[$lstTangki['KER'.$rowTangki]],0)."</td>";
				}
		}
		$tab.="<td align=right>".number_format($granTotProdKer,0)."</td>";
		@$oerKerGrnd=($granTotProdKer/$grandTbsProses)*100;
		$tab.="<td align=right>".number_format($oerKerGrnd,2)."</td>";
		$tab.="<td align=right>".number_format($grandFfaKer,2)."</td>";
		//$tab.="<td align=right>Dobi</td>";
		$tab.="<td align=right>".number_format($grandMoistKer,2)."</td>";
		$tab.="<td align=right>".number_format($grandDirtKer,2)."</td>";
		$tab.="<td align=right>%</td>";
		$tab.="<td align=right>".number_format($grandJamOlah,0)."</td>";
		$tab.="<td align=right>".number_format($grandJamStag,0)."</td>";
		@$grandTperJam=$grandTbsProses/$grandJamOlah/1000;
		$tab.="<td>&nbsp;</td>";
		$tab.="<td align=right>".number_format($grandTperJam,0)."</td>";
		#sales dispatch
		foreach($dtTangki as $rowTangki){
				if($lstTangki['CPO'.$rowTangki]!=''){
					$tab.="<td align=right>".number_format($grandKrmCpoPerTangki[$lstTangki['CPO'.$rowTangki]],0)."</td>";
				} 
		}
		$tab.="<td align=right>".number_format($grandKrmCpo,0)."</td>";
		foreach($dtTangki as $rowTangki){
				if($lstTangki['KER'.$rowTangki]!=''){
					$tab.="<td align=right>".number_format($grandKrmKerPerTangki[$lstTangki['KER'.$rowTangki]],0)."</td>";
				} 
		}
		$tab.="<td align=right>".number_format($grandKrmKer,0)."</td>";
		foreach($dtTangki as $rowTangki){
				if($lstTangki['CPO'.$rowTangki]!=''){
					$tab.="<td align=right>".number_format($grndCpo[$lstTangki['CPO'.$rowTangki]],0)."</td>";  	
					$sAvg="select AVG(cpoffa) as ffacpo,AVG(cpokdair) as moistcpo,AVG(cpokdkot) as dirtcpo,AVG(cpodobi) as dobicpo from 
				 	      ".$dbname.".pabrik_masukkeluartangki where kodetangki='".$lstTangki['CPO'.$rowTangki]."' and left(tanggal,7)= '".$param['periode']."' group by kodetangki";
					$qAvg=mysql_query($sAvg) or die(mysql_error($conn));
					$rAvg=mysql_fetch_assoc($qAvg);
					$tab.="<td align=right>".number_format($rAvg['ffacpo'],2)."</td>";
					$tab.="<td align=right>".number_format($rAvg['dobicpo'],2)."</td>";
					$tab.="<td align=right>".number_format($rAvg['moistcpo'],2)."</td>";
					$tab.="<td align=right>".number_format($rAvg['dirtcpo'],2)."</td>";
					
				} 
			}
			$tab.="<td align=right>".number_format($grandTotStockCpo,0)."</td>";
			foreach($dtTangki as $rowTangki){
				if($lstTangki['KER'.$rowTangki]!=''){
					$tab.="<td align=right>".number_format($grndKer[$lstTangki['KER'.$rowTangki]],0)."</td>";  	  
					$sAvg="select AVG(kernelffa) as ffaker,AVG(kernelkdair) as moistker,AVG(kernelkdkot) as dirtker,AVG(kerneldobi) as dobiker from 
				 	      ".$dbname.".pabrik_masukkeluartangki where kodetangki='".$lstTangki['CPO'.$rowTangki]."' and left(tanggal,7)= '".$param['periode']."' group by kodetangki";
					$qAvg=mysql_query($sAvg) or die(mysql_error($conn));
					$rAvg=mysql_fetch_assoc($qAvg);					
					$tab.="<td align=right>".number_format($rAvg['ffaker'],2)."</td>";
					$tab.="<td align=right>".number_format($rAvg['dobiker'],2)."</td>";
					$tab.="<td align=right>".number_format($rAvg['moistker'],2)."</td>";
					$tab.="<td align=right>".number_format($rAvg['dirtker'],2)."</td>";
				} 
			}
			$tab.="<td align=right>".number_format($grandTotStockKer,0)."</td>";
			$tab.="<td>&nbsp;</td>";
			$tab.="<td align=right>".number_format($grandRasCpo,0)."</td>";
			$tab.="<td align=right>".number_format($grandFfaCpo,2)."</td>";
			$tab.="<td align=right>".number_format($grandDobiCpo,2)."</td>";
			$tab.="<td align=right>".number_format($grandDirtCpo,2)."</td>";
			$tab.="<td align=right>".number_format($grandMoistCpo,2)."</td>";
			$tab.="<td>&nbsp;</td>";//total loses
			$tab.="<td>&nbsp;</td>";
			$tab.="<td align=right>".number_format($grandRasKer,2)."</td>";
			$tab.="<td align=right>".number_format($grandFfaKer,2)."</td>";
			$tab.="<td align=right>".number_format($grandMoistKer,2)."</td>";
			$tab.="<td align=right>".number_format($grandDirtKer,2)."</td>";			
			$tab.="<td>&nbsp;</td>";//total loses
			$tab.="<td>&nbsp;</td>";
			$tab.="<td>&nbsp;</td>";//USB
			$sAvgQuality="select avg(ebstalk) as ebstalk,avg(fibre) as fibre,avg(nut) as nut,avg(soliddecanter) as soliddecanter,avg(effluent) as effluent
					   ,avg(fruitinebker) as fruitinebker,avg(cyclone) as cyclone,avg(ltds) as ltds,avg(claybath) as claybath
			           from ".$dbname.".pabrik_produksi where kodeorg='".$param['pabrik']."' and tanggal like '".$param['periode']."%'";
			$qAvgQuality=mysql_query($sAvgQuality) or die(mysql_error($conn));
			$rAvgQuality=mysql_fetch_assoc($qAvgQuality);
			$tab.="<td align=right>".number_format($rAvgQuality['ebstalk'],2)."</td>";
			$tab.="<td align=right>".number_format($rAvgQuality['fibre'],2)."</td>";
			$tab.="<td align=right>".number_format($rAvgQuality['nut'],2)."</td>";
			$tab.="<td align=right>".number_format($rAvgQuality['soliddecanter'],2)."</td>";
			$tab.="<td align=right>".number_format($rAvgQuality['effluent'],2)."</td>";		
			$totalLossCpo=$rAvgQuality['ebstalk']+$rAvgQuality['fibre']+$rAvgQuality['nut']+$rAvgQuality['soliddecanter']+$rAvgQuality['effluent'];
			$tab.="<td  align=right>".number_format($grndTotalLosesCpo,2)."</td>";
			#oil losses dan kernel losses
			$tab.="<td align=right>".number_format($rAvgQuality['fruitinebker'],2)."</td>";
			$tab.="<td align=right>".number_format($rAvgQuality['cyclone'],2)."</td>";
			$tab.="<td align=right>".number_format($rAvgQuality['claybath'],2)."</td>";
			$tab.="<td align=right>".number_format($rAvgQuality['ltds'],2)."</td>";
			$totalLossKer=$rAvgQuality['fruitinebker']+$rAvgQuality['cyclone']+$rAvgQuality['claybath']+$rAvgQuality['ltds'];
			$tab.="<td  align=right>".number_format($grndTotalLosesKer,2)."</td>";
			$tab.="<td>&nbsp;</td>";//BROKEN KERNEL
		$tab.="</tr>";
              
		#mulai budget
		$tab.="<tr>";
		$tab.="<td>Budget BI (b)</td>";
		foreach($ptInduk as $lstIndk){
				foreach($kdOrgDt as $lstKbn){
					if($lstKdorg[$lstIndk.$lstKbn]!=''){
						$tab.="<td align=right>".number_format($bgdProdBlnini[$lstIndk.$lstKbn],0)."</td>";
					}
				}
		}
		$tab.="<td align=right>".number_format($totBgtBlnini,0)."</td>";
		if($colsEks!=1){
			if(!empty($EksternalDt)){
                                foreach($EksternalDt as $lstEks){
                                        $tab.="<td align=right>".number_format($bgtTbsEksBi[$lstEks],0)."</td>";
                                        $totTbsEks+=$bgtTbsEksBi[$lstEks];
                                }
			}
		}
                $tab.="<td align=right>".number_format($totTbsEks,0)."</td>";
		#total penerimaan budget
		$grandBgdPenerimaan=$totBgtBlnini+$totTbsEks;
		$tab.="<td align=right>".number_format($grandBgdPenerimaan,0)."</td>";
		$tab.="<td align=right>".number_format($rBgtOlah['prosesblnini'],0)."</td>";
                $saldAkhirBgt=$grandBgdPenerimaan-$rBgtOlah['prosesblnini'];
                $tab.="<td align=right>".number_format($saldAkhirBgt,0)."</td>";
		$tab.="<td colspan=".($jmlTngki['CPO']).">&nbsp;</td>";
		@$bgtProdCpo=($rBgtOlah['prosesblnini']*$rBgtOlah['oerbunch'])/100;
		$tab.="<td align=right>".number_format($bgtProdCpo,0)."</td>";
		$tab.="<td align=right>".number_format($rBgtOlah['oerbunch'],2)."</td>";
		$tab.="<td colspan=".($jmlTngki['KER']+5).">&nbsp;</td>";
		@$bgtProdKer=($rBgtOlah['prosesblnini']*$rBgtOlah['oerkernel'])/100;
		$tab.="<td align=right>".number_format($bgtProdKer,0)."</td>";
		$tab.="<td align=right>".number_format($rBgtOlah['oerkernel'],2)."</td>";
		$tab.="<td colspan=".(9+($jmlTngki['CPO']+$jmlTngki['KER']+2)+(($jmlTngki['CPO']*5)+($jmlTngki['KER']*5)+2)+26).">&nbsp;</td>";
		$tab.="</tr>";
		
		$tab.="<tr>";
		$tab.="<td>Selisih Budget dengan Realisasi BI</td>";
		foreach($ptInduk as $lstIndk){
				foreach($kdOrgDt as $lstKbn){
					if($lstKdorg[$lstIndk.$lstKbn]!=''){
						$bgtVsRealTbs[$lstIndk.$lstKbn]=$grandDiv[$lstIndk.$lstKbn]-$bgdProdBlnini[$lstIndk.$lstKbn];
						$tab.="<td align=right>".number_format($bgtVsRealTbs[$lstIndk.$lstKbn],0)."</td>";
					}
				}
		}
		$bgtVsRealTotPnriman=$grandTotInt-$totBgtBlnini;
		$tab.="<td align=right>".number_format($bgtVsRealTotPnriman,0)."</td>";
		if($colsEks!=1){
			if(!empty($EksternalDt)){
                                foreach($EksternalDt as $lstEks){
                                        $bgtVsRealTbsEksPer[$lstEks]=$grandEksTbs[$lstEks]-$bgtTbsEksBi[$lstEks];
                                        $tab.="<td align=right>".number_format($bgtVsRealTbsEksPer[$lstEks],0)."</td>";
                                }
			}
		}
		$bgtVsRealTbsEks=$grandTotEks-$totTbsEks;
		$tab.="<td align=right>".number_format($bgtVsRealTbsEks,0)."</td>";
		$grandBgtVsRealPnrimaan=$bgtVsRealTotPnriman+$bgtVsRealTbsEks;
		$tab.="<td align=right>".number_format($grandBgtVsRealPnrimaan,0)."</td>";
		$grandProsRealVsBgt=$grandTbsProses-$rBgtOlah['prosesblnini'];
		$tab.="<td align=right>".number_format($grandProsRealVsBgt,0)."</td>";
                $grandSalakRealVsBgt=$grandSalakTbs-$saldAkhirBgt;
                $tab.="<td align=right>".number_format($grandSalakRealVsBgt,0)."</td>";
		$tab.="<td colspan=".($jmlTngki['CPO']).">&nbsp;</td>";
		$grandProdCpoBgtVsReal=$granTotProdCpo-$bgtProdCpo;
		$tab.="<td align=right>".number_format($grandProdCpoBgtVsReal,0)."</td>";
		$grandOerBgtVsReal=$oerCpoGrnd-$rBgtOlah['oerbunch'];
		$tab.="<td align=right>".number_format($grandOerBgtVsReal,2)."</td>";
		$tab.="<td colspan=".($jmlTngki['KER']+5).">&nbsp;</td>";
		$grandProdKerBgtVsReal=$granTotProdKer-$bgtProdKer;
		$tab.="<td align=right>".number_format($grandProdKerBgtVsReal,0)."</td>";
		$grandOerKerBgtVsReal=$oerKerGrnd-$rBgtOlah['oerkernel'];
		$tab.="<td align=right>".number_format($grandOerKerBgtVsReal,2)."</td>";
		$tab.="<td colspan=".(9+($jmlTngki['CPO']+$jmlTngki['KER']+2)+(($jmlTngki['CPO']*5)+($jmlTngki['KER']*5)+2)+26).">&nbsp;</td>";
		$tab.="</tr>";
		#persen bi
		$tab.="<tr>";
		$tab.="<td>&nbsp;</td>";
		foreach($ptInduk as $lstIndk){
				foreach($kdOrgDt as $lstKbn){
					if($lstKdorg[$lstIndk.$lstKbn]!=''){
						@$presnTbsDiv[$lstIndk.$lstKbn]=($bgtVsRealTbs[$lstIndk.$lstKbn]/$bgdProdBlnini[$lstIndk.$lstKbn])*100;
						$tab.="<td align=right>".number_format($presnTbsDiv[$lstIndk.$lstKbn],2)."</td>";
					}
				}
		}
		@$persenTbsPnrman=($bgtVsRealTotPnriman/$totBgtBlnini)*100;
		$tab.="<td align=right>".number_format($persenTbsPnrman,2)."</td>";
		if($colsEks!=1){
			if(!empty($EksternalDt)){
                                foreach($EksternalDt as $lstEks){
                                        @$presnTbsEks[$lstEks]=($bgtVsRealTbsEksPer[$lstEks]/$bgtTbsEksBi[$lstEks])*100;
                                        $tab.="<td align=right>".number_format($presnTbsEks[$lstEks],0)."</td>";
                                }
			}
		}
                @$presnTbsEksAll=($bgtVsRealTbsEks/$totTbsEks)*100;
		$tab.="<td align=right>".number_format($presnTbsEksAll,0)."</td>";
		@$persenTbsTotPnrman=($grandBgtVsRealPnrimaan/$grandBgdPenerimaan)*100;
		$tab.="<td align=right>".number_format($persenTbsTotPnrman,2)."</td>";
		@$persenTbsProses=($grandProsRealVsBgt/$rBgtOlah['prosesblnini'])*100;
		$tab.="<td align=right>".number_format($persenTbsProses,2)."</td>";
                
                @$persenSalakRealVsBgt=($grandSalakTbs/$saldAkhirBgt)*100;
                $tab.="<td align=right>".number_format($persenSalakRealVsBgt,2)."</td>";
		$tab.="<td colspan=".($jmlTngki['CPO']).">&nbsp;</td>";
		@$persenProdCpo=($grandProdCpoBgtVsReal/$bgtProdCpo)*100;
		$tab.="<td align=right>".number_format($persenProdCpo,2)."</td>";
		
		$tab.="<td align=right>&nbsp;</td>";
		$tab.="<td colspan=".($jmlTngki['KER']+5).">&nbsp;</td>";
		@$persenProdKer=($grandProdKerBgtVsReal/$bgtProdKer)*100;
		$tab.="<td align=right>".number_format($persenProdKer,2)."</td>";
		
		$tab.="<td align=right>&nbsp;</td>";
		$tab.="<td colspan=".(9+($jmlTngki['CPO']+$jmlTngki['KER']+2)+(($jmlTngki['CPO']*5)+($jmlTngki['KER']*5)+2)+26).">&nbsp;</td>";
		$tab.="</tr>";
		#pemisah 
		$tab.="<tr><td colspan=".(($colsInt+$colEk+4)+($jmlTngki['CPO']+7)+($jmlTngki['KER']+6)+4+($jmlTngki['CPO']+$jmlTngki['KER']+2)+(($jmlTngki['CPO']*5)+($jmlTngki['KER']*5)+2)+27).">&nbsp;</td>";
		$tab.="</tr>";
		
		#mulai realisasi s.d bulan lalu
		$tab.="<tr>";
		$tab.="<td>Realisasi Bulan Lalu</td>";
		foreach($ptInduk as $lstIndk){
				foreach($kdOrgDt as $lstKbn){
					if($lstKdorg[$lstIndk.$lstKbn]!=''){
						$tab.="<td align=right>".number_format($realBlnLalu[$lstIndk.$lstKbn],0)."</td>";
						$totRealLalu+=$realBlnLalu[$lstIndk.$lstKbn];
					}
				}
		}
		$tab.="<td align=right>".number_format($totRealLalu,0)."</td>";
		if($colsEks!=1){
				if(!empty($EksternalDt)){
					foreach($EksternalDt as $lstEks){
							$tab.="<td  align=right>".number_format($realBlnLaluEkst[$lstEks],0)."</td>";
							$totRealLaluEks+=$realBlnLaluEkst[$lstEks];
					}
				}
				$tab.="<td align=right>".number_format($totRealLaluEks,0)."</td>";
		}
		 
		$totRealPnrmanLalu=$totRealLalu+$totRealLaluEks;
		$tab.="<td align=right>".number_format($totRealPnrmanLalu,0)."</td>";
		$tab.="<td align=right>".number_format($rProdLalu['tbsproses'],0)."</td>";
		$salAkRealLalu=$totRealPnrmanLalu-$rProdLalu['tbsproses'];
		$tab.="<td align=right>".number_format($salAkRealLalu,0)."</td>";
		$tab.="<td colspan=".($jmlTngki['CPO']).">&nbsp;</td>";
		
		$tab.="<td align=right>".number_format($rCpoLalu['cpoProd'],0)."</td>";
		@$oerLaluCpo=($rCpoLalu['cpoProd']/$rProdLalu['tbsproses'])*100;
		$tab.="<td align=right>".number_format($oerLaluCpo,2)."</td>";
		$tab.="<td colspan=".($jmlTngki['KER']+5).">&nbsp;</td>";
		@$oerLaluKer=($rKerLalu['kerProd']/$rProdLalu['tbsproses'])*100;
		$tab.="<td align=right>".number_format($rKerLalu['kerProd'],0)."</td>";
		$tab.="<td align=right>".number_format($oerLaluKer,2)."</td>";
		$tab.="<td colspan=".(9+($jmlTngki['CPO']+$jmlTngki['KER']+2)+(($jmlTngki['CPO']*5)+($jmlTngki['KER']*5)+2)+26).">&nbsp;</td>";
		$tab.="</tr>";
               
		#budget s.d bulan lalu 
		$tab.="<tr>";
		$tab.="<td>Budget S/D Bulan Lalu </td>";
		foreach($ptInduk as $lstIndk){
				foreach($kdOrgDt as $lstKbn){
					if($lstKdorg[$lstIndk.$lstKbn]!=''){
						$tab.="<td align=right>".number_format($bgdProdBlnLalu[$lstIndk.$lstKbn],0)."</td>";
						$totBgtLalu+=$bgdProdBlnLalu[$lstIndk.$lstKbn];
					}
				}
		}
		$tab.="<td align=right>".number_format($totBgtLalu,0)."</td>";
		if($colsEks!=1){
				if(!empty($EksternalDt)){
					foreach($EksternalDt as $lstEks){
					  $tab.="<td  align=right>".number_format($bgtTbsEksBlalu[$lstEks],0)."</td>";
                                          $totBgtEksBlnLalu+=$bgtTbsEksBlalu[$lstEks];
					}
				}
				$tab.="<td align=right>".number_format($totBgtEksBlnLalu,0)."</td>";
		}
		 
		$totBgtPnrmanLalu=$totBgtLalu+$totBgtEksBlnLalu;
		$tab.="<td align=right>".number_format($totBgtPnrmanLalu,0)."</td>";
		$tab.="<td align=right>".number_format($rBgtOlahLalu['prosesblnlalu'],0)."</td>";
		$salAkBgtLalu=$totBgtPnrmanLalu-$rBgtOlahLalu['prosesblnlalu'];
		$tab.="<td align=right>".number_format($salAkBgtLalu,0)."</td>";
		$tab.="<td colspan=".($jmlTngki['CPO']).">&nbsp;</td>";
		@$bgtcpoProdLalu=($rBgtOlahLalu['prosesblnlalu']/$rBgtOlahLalu['oerbunch'])*100;
		$tab.="<td align=right>".number_format($bgtcpoProdLalu,0)."</td>";
		$tab.="<td align=right>".number_format($rBgtOlahLalu['oerbunch'],2)."</td>";
		$tab.="<td colspan=".($jmlTngki['KER']+5).">&nbsp;</td>";
		
		@$bgtkerProdLalu=($rBgtOlahLalu['prosesblnlalu']/$rBgtOlahLalu['oerkernel'])*100;
		$tab.="<td align=right>".number_format($bgtkerProdLalu,0)."</td>";
		$tab.="<td align=right>".number_format($rBgtOlahLalu['oerkernel'],2)."</td>";
		$tab.="<td colspan=".(9+($jmlTngki['CPO']+$jmlTngki['KER']+2)+(($jmlTngki['CPO']*5)+($jmlTngki['KER']*5)+2)+26).">&nbsp;</td>";
		$tab.="</tr>";
		
		#selsih budget dengan realisasi bln lalu
		$tab.="<tr>";
		$tab.="<td>Selisih Budget dengan Realisasi S.D. Bulan Lalu</td>";
		foreach($ptInduk as $lstIndk){
				foreach($kdOrgDt as $lstKbn){
					if($lstKdorg[$lstIndk.$lstKbn]!=''){
						$bgtVsRealTbsLalu[$lstIndk.$lstKbn]=$realBlnLalu[$lstIndk.$lstKbn]-$bgdProdBlnLalu[$lstIndk.$lstKbn];
						$tab.="<td align=right>".number_format($bgtVsRealTbsLalu[$lstIndk.$lstKbn],0)."</td>";
					}
				}
		}
		$bgtVsRealTotPnrimanLalu=$totRealLalu-$totBgtLalu;
		$tab.="<td align=right>".number_format($bgtVsRealTotPnrimanLalu,0)."</td>";
		if($colsEks!=1){
				if(!empty($EksternalDt)){
					foreach($EksternalDt as $lstEks){
                                           $bgtVsRealTbsEksPer[$lstEks]=$realBlnLaluEkst[$lstEks]-$bgtTbsEksBlalu[$lstEks];
					   $tab.="<td  align=right>".$bgtVsRealTbsEksPer[$lstEks]."</td>";
					}
				}
				$bgtVsRealTbsEksLalu=$totRealLaluEks-$totBgtEksBlnLalu;
				$tab.="<td align=right>".number_format($bgtVsRealTbsEksLalu,0)."</td>";
		}
		$grandBgtVsRealPnrimaanLalu=$totRealPnrmanLalu+$totBgtPnrmanLalu;
		$tab.="<td align=right>".number_format($grandBgtVsRealPnrimaanLalu,0)."</td>";
		$grandProsRealVsBgtLalu=$rProdLalu['tbsproses']-$rBgtOlahLalu['prosesblnlalu'];
		$tab.="<td align=right>".number_format($grandProsRealVsBgtLalu,0)."</td>";
                $grandSalakRealVsBgtLalu=$salAkRealLalu-$salAkBgtLalu;
                $tab.="<td align=right>".number_format($grandSalakRealVsBgtLalu,0)."</td>";
		$tab.="<td colspan=".($jmlTngki['CPO']).">&nbsp;</td>";
		$grandProdCpoBgtVsRealLalu=$rCpoLalu['cpoProd']-$bgtcpoProdLalu;
		$tab.="<td align=right>".number_format($grandProdCpoBgtVsRealLalu,0)."</td>";
		$grandOerBgtVsRealLalu=$oerLaluCpo-$rBgtOlahLalu['oerbunch'];
		$tab.="<td align=right>".number_format($grandOerBgtVsRealLalu,2)."</td>";
		$tab.="<td colspan=".($jmlTngki['KER']+5).">&nbsp;</td>";
		$grandProdKerBgtVsRealLalu=$rKerLalu['kerProd']-$bgtkerProdLalu;
		$tab.="<td align=right>".number_format($grandProdKerBgtVsRealLalu,0)."</td>";
		$grandOerKerBgtVsRealLalu=$oerLaluKer-$rBgtOlahLalu['oerkernel'];
		$tab.="<td align=right>".number_format($grandOerKerBgtVsRealLalu,2)."</td>";
		$tab.="<td colspan=".(9+($jmlTngki['CPO']+$jmlTngki['KER']+2)+(($jmlTngki['CPO']*5)+($jmlTngki['KER']*5)+2)+26).">&nbsp;</td>";
		$tab.="</tr>";
		
		
		#persen antara selisih budgetVsRealisasi bulan lalu
		$tab.="<tr>";
		$tab.="<td>&nbsp;</td>";
		foreach($ptInduk as $lstIndk){
				foreach($kdOrgDt as $lstKbn){
					if($lstKdorg[$lstIndk.$lstKbn]!=''){
						@$presnTbsDivLalu[$lstIndk.$lstKbn]=($bgtVsRealTbsLalu[$lstIndk.$lstKbn]/$bgdProdBlnLalu[$lstIndk.$lstKbn])*100;
						$tab.="<td align=right>".number_format($presnTbsDivLalu[$lstIndk.$lstKbn],2)."</td>";
					}
				}
		}
		@$persenTbsPnrmanLalu=($bgtVsRealTotPnrimanLalu/$totBgtLalu)*100;
		$tab.="<td align=right>".number_format($persenTbsPnrmanLalu,2)."</td>";
                if($colsEks!=1){
				if(!empty($EksternalDt)){
					foreach($EksternalDt as $lstEks){
                                            @$presnTbsEksPerLaluPer[$lstEks]=($realBlnLaluEkst[$lstEks]/$bgtTbsEksBlalu[$lstEks])*100;
                                            $tab.="<td  align=right>".$presnTbsEksPerLaluPer[$lstEks]."</td>";
					}
				}
                                 @$presnTbsEksPerLalu=($totRealLaluEks/$totBgtEksBlnLalu)*100;
				$tab.="<td align=right>".number_format($presnTbsEksPerLalu,0)."</td>";
		}
		@$persenTbsTotPnrmanLalu=($grandBgtVsRealPnrimaanLalu/$totBgtPnrmanLalu)*100;
		$tab.="<td align=right>".number_format($persenTbsTotPnrmanLalu,2)."</td>";
		@$persenTbsProsesLalu=($grandProsRealVsBgtLalu/$rBgtOlahLalu['prosesblnlalu'])*100;
		$tab.="<td align=right>".number_format($persenTbsProsesLalu,2)."</td>";
                @$persenSalakRealVsBgtLalu=($salAkRealLalu/$salAkBgtLalu)*100;
                $tab.="<td align=right>".number_format($persenSalakRealVsBgtLalu,0)."</td>";
		$tab.="<td colspan=".($jmlTngki['CPO']).">&nbsp;</td>";
		@$persenProdCpoLalu=($grandProdCpoBgtVsRealLalu/$bgtcpoProdLalu)*100;
		$tab.="<td align=right>".number_format($persenProdCpoLalu,2)."</td>";
		
		$tab.="<td align=right>&nbsp;</td>";
		$tab.="<td colspan=".($jmlTngki['KER']+5).">&nbsp;</td>";
		@$persenProdKerLalu=($grandProdKerBgtVsRealLalu/$bgtkerProdLalu)*100;
		$tab.="<td align=right>".number_format($persenProdKerLalu,2)."</td>";
		
		$tab.="<td align=right>&nbsp;</td>";
		$tab.="<td colspan=".(9+($jmlTngki['CPO']+$jmlTngki['KER']+2)+(($jmlTngki['CPO']*5)+($jmlTngki['KER']*5)+2)+26).">&nbsp;</td>";
		$tab.="</tr>";
		$tab.="<tr>";
		#pemisah 
		$tab.="<td colspan=".(($colsInt+$colEk+4)+($jmlTngki['CPO']+7)+($jmlTngki['KER']+7)+4+($jmlTngki['CPO']+$jmlTngki['KER']+2)+(($jmlTngki['CPO']*5)+($jmlTngki['KER']*5)+2)+26).">&nbsp;</td>";
		$tab.="</tr>";
		
		#mulai realisasi s.d bulan ini
		$tab.="<tr>";
		$tab.="<td>S/D Real. Bulan Ini (g=a+c)</td>";
		foreach($ptInduk as $lstIndk){
				foreach($kdOrgDt as $lstKbn){
					if($lstKdorg[$lstIndk.$lstKbn]!=''){
						$tab.="<td align=right>".number_format($realBlnSd[$lstIndk.$lstKbn],0)."</td>";
						$totRealSd+=$realBlnSd[$lstIndk.$lstKbn];
					}
				}
		}
		$tab.="<td align=right>".number_format($totRealSd,0)."</td>";
		if($colsEks!=1){
				if(!empty($EksternalDt)){
					foreach($EksternalDt as $lstEks){
							$tab.="<td  align=right>".number_format($realBlnSdEkst[$lstEks],0)."</td>";
							$totRealSdEks+=$realBlnSdEkst[$lstEks];
					}
				}
				$tab.="<td align=right>".number_format($totRealSdEks,0)."</td>";
		}
		 
		$totRealPnrmanSd=$totRealSd+$totRealSdEks;
		$tab.="<td align=right>".number_format($totRealPnrmanSd,0)."</td>";
		$tab.="<td align=right>".number_format($rProdSd['tbsproses'],0)."</td>";
		$salAkReal=$totRealPnrmanSd-$rProdSd['tbsproses'];
		$tab.="<td align=right>".number_format($salAkReal,0)."</td>";
		$tab.="<td colspan=".($jmlTngki['CPO']).">&nbsp;</td>";
		
		$tab.="<td align=right>".number_format($rCposd['cpoProd'],0)."</td>";
		@$oerSdCpo=($rCposd['cpoProd']/$rProdSd['tbsproses'])*100;
		$tab.="<td align=right>".number_format($oerSdCpo,2)."</td>";
		$tab.="<td colspan=".($jmlTngki['KER']+5).">&nbsp;</td>";
		@$oerSdKer=($rKersd['kerProd']/$rProdSd['tbsproses'])*100;
		$tab.="<td align=right>".number_format($rKersd['kerProd'],0)."</td>";
		$tab.="<td align=right>".number_format($oerSdKer,2)."</td>";
		$tab.="<td colspan=".(9+($jmlTngki['CPO']+$jmlTngki['KER']+2)+(($jmlTngki['CPO']*5)+($jmlTngki['KER']*5)+2)+26).">&nbsp;</td>";
		$tab.="</tr>";
                
		#budget s.d bulan ini
		$tab.="<tr>";
		$tab.="<td>Budget S/D Bulan Ini </td>";
		foreach($ptInduk as $lstIndk){
				foreach($kdOrgDt as $lstKbn){
					if($lstKdorg[$lstIndk.$lstKbn]!=''){
						$tab.="<td align=right>".number_format($bgdProdBlnSd[$lstIndk.$lstKbn],0)."</td>";
						$totBgtSd+=$bgdProdBlnSd[$lstIndk.$lstKbn];
					}
				}
		}
		$tab.="<td align=right>".number_format($totBgtSd,0)."</td>";
		if($colsEks!=1){
				if(!empty($EksternalDt)){
					foreach($EksternalDt as $lstEks){
							$tab.="<td  align=right>&nbsp;</td>";
					}
				}
				$tab.="<td align=right>".number_format($rBgtOlahSd2['tbsEksSd'],0)."</td>";
		}
		 
		$totBgtPnrmanSd=$totBgtSd+$rBgtOlahSd2['tbsEksSd'];
		$tab.="<td align=right>".number_format($totBgtPnrmanSd,0)."</td>";
		$tab.="<td align=right>".number_format($rBgtOlahSd['prosesblnsd'],0)."</td>";
		$salAkBgt=$totBgtPnrmanSd-$rBgtOlahSd['prosesblnsd'];
		$tab.="<td align=right>".number_format($salAkBgt,0)."</td>";
		$tab.="<td colspan=".($jmlTngki['CPO']).">&nbsp;</td>";
		@$bgtcpoProdSd=($rBgtOlahSd['prosesblnsd']*$rBgtOlahSd['oerbunch'])*100;
		$tab.="<td align=right>".number_format($bgtcpoProdSd,0)."</td>";
		$tab.="<td align=right>".number_format($rBgtOlahSd['oerbunch'],2)."</td>";
		$tab.="<td colspan=".($jmlTngki['KER']+5).">&nbsp;</td>";
		
		@$bgtkerProdSd=($rBgtOlahSd['prosesblnsd']*$rBgtOlahSd['oerkernel'])*100;
		$tab.="<td align=right>".number_format($bgtkerProdSd,0)."</td>";
		$tab.="<td align=right>".number_format($rBgtOlahSd['oerkernel'],2)."</td>";
		$tab.="<td colspan=".(9+($jmlTngki['CPO']+$jmlTngki['KER']+2)+(($jmlTngki['CPO']*5)+($jmlTngki['KER']*5)+2)+26).">&nbsp;</td>";
		$tab.="</tr>";
		 
		#selsih budget dengan realisasi s.d bln ini`
		$tab.="<tr>";
		$tab.="<td>Selisih Budget dengan Realisasi S.D. BI</td>";
		foreach($ptInduk as $lstIndk){
				foreach($kdOrgDt as $lstKbn){
					if($lstKdorg[$lstIndk.$lstKbn]!=''){
						$bgtVsRealTbsSd[$lstIndk.$lstKbn]=$realBlnSd[$lstIndk.$lstKbn]-$bgdProdBlnSd[$lstIndk.$lstKbn];
						$tab.="<td align=right>".number_format($bgtVsRealTbsSd[$lstIndk.$lstKbn],0)."</td>";
					}
				}
		}
		$bgtVsRealTotPnrimanSd=$totRealSd-$totBgtSd;
		$tab.="<td align=right>".number_format($bgtVsRealTotPnrimanSd,0)."</td>";
		if($colsEks!=1){
				if(!empty($EksternalDt)){
					foreach($EksternalDt as $lstEks){
							$tab.="<td  align=right>&nbsp;</td>";
					}
				}
				$bgtVsRealTbsEksSd=$totRealSdEks-$rBgtOlahSd2['tbsEksSd'];
				$tab.="<td align=right>".number_format($bgtVsRealTbsEksSd,0)."</td>";
		}
		$grandBgtVsRealPnrimaanSd=$totRealPnrmanSd+$totBgtPnrmanSd;
		$tab.="<td align=right>".number_format($grandBgtVsRealPnrimaanSd,0)."</td>";
		$grandProsRealVsBgtSd=$rProdSd['tbsproses']-$rBgtOlahSd['prosesblnsd'];
		$tab.="<td align=right>".number_format($grandProsRealVsBgtSd,0)."</td>";
                $grandSalakRealVsBgtSd=$salAkReal-$salAkBgt;
		$tab.="<td align=right>".number_format($grandSalakRealVsBgtSd,0)."</td>";
		$tab.="<td colspan=".($jmlTngki['CPO']).">&nbsp;</td>";
		$grandProdCpoBgtVsRealSd=$rCposd['cpoProd']-$bgtcpoProdSd;
		$tab.="<td align=right>".number_format($grandProdCpoBgtVsRealSd,0)."</td>";
		$grandOerBgtVsRealSd=$oerSdCpo-$rBgtOlahSd['oerbunch'];
		$tab.="<td align=right>".number_format($grandOerBgtVsRealSd,2)."</td>";
		$tab.="<td colspan=".($jmlTngki['KER']+5).">&nbsp;</td>";
		$grandProdKerBgtVsRealSd=$rKersd['kerProd']-$bgtkerProdSd;
		$tab.="<td align=right>".number_format($grandProdKerBgtVsRealSd,0)."</td>";
		$grandOerKerBgtVsRealSd=$oerSdKer-$rBgtOlahSd['oerkernel'];
		$tab.="<td align=right>".number_format($grandOerKerBgtVsRealSd,2)."</td>";
		$tab.="<td colspan=".(9+($jmlTngki['CPO']+$jmlTngki['KER']+2)+(($jmlTngki['CPO']*5)+($jmlTngki['KER']*5)+2)+26).">&nbsp;</td>";
		$tab.="</tr>";
		
                  
		#persen antara selisih budgetVsRealisasi s.d bulan ini
		$tab.="<tr>";
		$tab.="<td>&nbsp;</td>";
		foreach($ptInduk as $lstIndk){
				foreach($kdOrgDt as $lstKbn){
					if($lstKdorg[$lstIndk.$lstKbn]!=''){
						@$presnTbsDivSd[$lstIndk.$lstKbn]=($bgtVsRealTbsSd[$lstIndk.$lstKbn]/$bgdProdBlnSd[$lstIndk.$lstKbn])*100;
						$tab.="<td align=right>".number_format($presnTbsDivSd[$lstIndk.$lstKbn],2)."</td>";
					}
				}
		}
		@$persenTbsPnrmanSd=($bgtVsRealTotPnrimanSd/$totBgtSd)*100;
		$tab.="<td align=right>".number_format($persenTbsPnrman,2)."</td>";
		$tab.="<td>&nbsp;</td>";
		$tab.="<td align=right>&nbsp;</td>";
		@$persenTbsTotPnrmanSd=($grandBgtVsRealPnrimaanSd/$totBgtPnrmanSd)*100;
		$tab.="<td align=right>".number_format($persenTbsTotPnrmanSd,2)."</td>";
		@$persenTbsProsesSd=($grandProsRealVsBgtSd/$rBgtOlahSd['prosesblnsd'])*100;
		$tab.="<td align=right>".number_format($persenTbsProsesSd,2)."</td>";
                @$persenTbsProsesSd=($grandProsRealVsBgtSd/$rBgtOlahSd['prosesblnsd'])*100;
                @$persenSalakRealVsBgtSd=($salAkReal/$salAkBgt)*100;
		$tab.="<td align=right>".number_format($persenSalakRealVsBgtSd,2)."</td>";
		$tab.="<td colspan=".($jmlTngki['CPO']).">&nbsp;</td>";
		@$persenProdCpoSd=($grandProdCpoBgtVsRealSd/$bgtcpoProdSd)*100;
		$tab.="<td align=right>".number_format($persenProdCpoSd,2)."</td>";
		
		$tab.="<td align=right>&nbsp;</td>";
		$tab.="<td colspan=".($jmlTngki['KER']+5).">&nbsp;</td>";
		@$persenProdKerSd=($grandProdKerBgtVsRealSd/$bgtkerProdSd)*100;
		$tab.="<td align=right>".number_format($persenProdKerSd,2)."</td>";
		
		$tab.="<td align=right>&nbsp;</td>";
		$tab.="<td colspan=".(9+($jmlTngki['CPO']+$jmlTngki['KER']+2)+(($jmlTngki['CPO']*5)+($jmlTngki['KER']*5)+2)+26).">&nbsp;</td>";
		$tab.="</tr>";
		
		$tab.="</tr>";
		$tab.="</tfoot>";
		 
		
	}
	
	  switch($param['proses']){
		  case'getPeriode':
			$optper="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
			$sPrd="select distinct left(tanggal,7) as periode from ".$dbname.".pabrik_produksi where kodeorg='".$param['kdPabrik']."' order by tanggal desc";
			$qPrd=mysql_query($sPrd) or die(mysql_error($conn));
			while($rPrd=mysql_fetch_assoc($qPrd)){
					$optper.="<option value='".$rPrd['periode']."'>".$rPrd['periode']."</option>";
			}
			echo $optper;
		  break;
		  case'preview':
			echo $tab;
		  break;
		  case'excel':
			$dte=date("YmdHis");
            $nop_="laporan_produksi__".$param['pabrik']."___".$dte;
			if(strlen($tab)>0){
				 $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
				 gzwrite($gztralala, $tab);
				 gzclose($gztralala);
				 echo "<script language=javascript1.2>
					window.location='tempExcel/".$nop_.".xls.gz';
					</script>";
			} 
		  break;
	  }

?>