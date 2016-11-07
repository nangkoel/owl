<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

$proses=$_GET['proses'];
$_POST['periode2']==''?$periode=$_GET['periode2']:$periode=$_POST['periode2'];
$_POST['regionId2']==''?$ptId=$_GET['regionId2']:$ptId=$_POST['regionId2'];
$_POST['tpKary2']==''?$tpKary=$_GET['tpKary2']:$tpKary=$_POST['tpKary2'];

$wrt="periode='".$periode."' and kodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$ptId."')";
#tgl cut off
if(substr($periode,0,4)=='2014'){
	if($_SESSION['empl']['regional']=='SULAWESI'){
		if($tpKary==3){
			#bentuk tanggal cut off lalu plus satu dan tanggal cut off bulan berjalan
			$optTglMulai=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tanggalmulai', $wrt);
			$tglKmrn=nambahHari($optTglMulai[$periode],1,0);
			$wrt2="periode='".substr($tglKmrn,0,7)."' and kodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$ptId."')";
			$tglCutoffLalu=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tglcutoff', $wrt2);
			$tglKmrn=nambahHari($tglCutoffLalu[substr($tglKmrn,0,7)],1,1);	
			$optTglCutoff=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tglcutoff', $wrt);
		}else{
			$optTglCutoff=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tanggalsampai', $wrt);
			$optTglMulai=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tanggalmulai', $wrt);
			$tglKmrn=$optTglMulai[$periode];
		}	
	}else{
		$optTglCutoff=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tanggalsampai', $wrt);
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
 
$prd=explode("-",$periode);
$arrBln=array(1=>"Januari",2=>"Februari",3=>"Maret",4=>"April",5=>"Mei",6=>"Juni",7=>"Juli",8=>"Agustus",9=>"September",10=>"Oktober",11=>"November",12=>"Desember");
$arrPlusId=array("1"=>"HARI_H","2"=>"FAKTOR LEMBUR","3"=>"H_KERJA","4"=>"UPAH POKOK","5"=>"LEMBUR","6"=>"PREMI LAINNYA","7"=>"UPAH SATUAN","8"=>"JOB INSENTIF","9"=>"PREMI HADIR","10"=>"TMASA","11"=>"LAIN2");

$garis=0;
if($proses=='excel'){
    $garis=1;
   $bgcolordt=" bgcolor=#DEDEDE";
}
if(($proses=='excel')||($proses=='preview')){
     if($periode==''){
		exit("warning: Periode Tidak Boleh Kosong");
	}
		$tglGaji="tanggal between '".$tglKmrn."' and '".$optTglCutoff[$periode]."'";
		$qDiv="select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$ptId."'";
             
            ##tambahan absen permintaan dari pak ujang#
            $sAbsn="select count(absensi) as jmlabsn,kodeorg,absensi from 
			        ".$dbname.".sdm_absensidt a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
					where ".$tglGaji." and left(kodeorg,4) in  (".$qDiv.") and tipekaryawan='".$tpKary."' group by kodeorg,absensi";
                        //exit("Error".$sAbsn);
                        $rAbsn=fetchData($sAbsn);
                        foreach ($rAbsn as $absnBrs =>$resAbsn){
                                if(!is_null($resAbsn['absensi'])){
                                  //$hasilAbsn[$resAbsn['karyawanid']][$resAbsn['tanggal']][]=array('absensi'=>$resAbsn['absensi']);
                                  //$resData[$resAbsn['karyawanid']][]=$resAbsn['karyawanid'];
								  $sCek="select kelompok from ".$dbname.".sdm_5absensi where kodeabsen='".$resAbsn['absensi']."'";
								  $qCek=mysql_query($sCek) or die(mysql_error($conn));
								  $rCek=mysql_fetch_assoc($qCek);
								  $hasilAbsn[$resAbsn['kodeorg'].$resAbsn['absensi']]=$resAbsn['jmlabsn'];
								  if($rCek['kelompok']==0){
									$klmpkAbsnGdbyr[$resAbsn['absensi']]=$resAbsn['absensi'];
								  }else{
									$klmpkAbsn[$resAbsn['absensi']]=$resAbsn['absensi'];
								  }
								  $dtKdorg[$resAbsn['kodeorg']]=$resAbsn['kodeorg'];
                                }
                        }
						$optTipekar=makeOption($dbname,'sdm_5tipekaryawan','id,tipe');
                        $sKehadiran="select count(absensi) as jmlabsn,left(kodeorg,6) as kodeorg from 
						             ".$dbname.".kebun_kehadiran_vw 
									 where ".$tglGaji." and left(kodeorg,4) in  (".$qDiv.") and tipekaryawan='".$optTipekar[$tpKary]."' group by left(kodeorg,6)";
                        //exit("Error".$sKehadiran);
                        $rkehadiran=fetchData($sKehadiran);
                        foreach ($rkehadiran as $khdrnBrs =>$resKhdrn){	
                                if($resKhdrn['absensi']!=''){
                                    //$hasilAbsn[$resKhdrn['karyawanid']][$resKhdrn['tanggal']][]=array('absensi'=>$resKhdrn['absensi']);
                                    //$resData[$resKhdrn['karyawanid']][]=$resKhdrn['karyawanid'];
									$hasilAbsn[$resKhdrn['kodeorg'].$resKhdrn['absensi']]=$resKhdrn['jmlabsn'];
									$klmpkAbsn[$resKhdrn['absensi']]=$resKhdrn['absensi'];
									$dtKdorg[$resKhdrn['kodeorg']]=$resKhdrn['kodeorg'];
                                }
                        }
						$tglGaji2="b.tanggal between '".$tglKmrn."' and '".$optTglCutoff[$periode]."'";
                        $sPrestasi="select a.nik,b.tanggal,left(b.kodeorg,6) as kodeorg 
						            from ".$dbname.".kebun_prestasi a left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi 
									left join ".$dbname.".datakaryawan c on a.nik=c.karyawanid
                                    where ".$tglGaji2." and b.notransaksi like '%PNN%' and left(b.kodeorg,4) in (".$qDiv.") and tipekaryawan='".$tpKary."'";
                        //exit("Error".$sPrestasi);
                        $rPrestasi=fetchData($sPrestasi);
                        foreach ($rPrestasi as $presBrs =>$resPres){
                            //$hasilAbsn[$resPres['nik']][$resPres['tanggal']][]=array('absensi'=>'H');
                            //$resData[$resPres['nik']][]=$resPres['nik'];
							$kdAbsn='H';
							$hasilAbsn[$resPres['kodeorg'].$kdAbsn]+=1;
							$klmpkAbsn[$kdAbsn]=$kdAbsn;
							$dtKdorg[$resPres['kodeorg']]=$resPres['kodeorg'];
                        } 

        // ambil pengawas 
		$tglGaji3="a.tanggal between '".$tglKmrn."' and '".$optTglCutoff[$periode]."'";		
        $dzstr="SELECT tanggal,nikmandor,left(b.kodeorg,4) as kodeorg FROM ".$dbname.".kebun_aktifitas a
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
            where ".$tglGaji3." and left(b.kodeorg,4) in (".$qDiv.") and tipekaryawan='".$tpKary."' and c.namakaryawan is not NULL
            union select tanggal,nikmandor1,left(b.kodeorg,4) as kodeorg FROM ".$dbname.".kebun_aktifitas a 
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".datakaryawan c on a.nikmandor1=c.karyawanid
            where ".$tglGaji3." and left(b.kodeorg,4) in (".$qDiv.") and tipekaryawan='".$tpKary."' and c.namakaryawan is not NULL ";

		//exit("Error".$dzstr);
        $dzres=mysql_query($dzstr);
        while($dzbar=mysql_fetch_object($dzres)){
            //$hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][]=array('absensi'=>'H');
            //$resData[$dzbar->nikmandor][]=$dzbar->nikmandor;
			$kdAbsn='H';
			$hasilAbsn[$dzbar->kodeorg.$kdAbsn]+=1;
			$klmpkAbsn[$kdAbsn]=$kdAbsn;
			$dtKdorg[$dzbar->kodeorg]=$dzbar->kodeorg;
        }
        // ambil administrasi                       
        $dzstr="SELECT tanggal,nikmandor,left(b.kodeorg,4) as kodeorg FROM ".$dbname.".kebun_aktifitas a
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
            where ".$tglGaji3." and left(b.kodeorg,4) in (".$qDiv.") and tipekaryawan='".$tpKary."' and c.namakaryawan is not NULL
            union select tanggal,keranimuat,left(b.kodeorg,4) as kodeorg FROM ".$dbname.".kebun_aktifitas a 
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".datakaryawan c on a.keranimuat=c.karyawanid
            where ".$tglGaji3." and left(b.kodeorg,4) in (".$qDiv.") and tipekaryawan='".$tpKary."' and c.namakaryawan is not NULL";
         //exit("Error".$dzstr);
        $dzres=mysql_query($dzstr);
        while($dzbar=mysql_fetch_object($dzres)){
            //$hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][]=array('absensi'=>'H');
            //$resData[$dzbar->nikmandor][]=$dzbar->nikmandor;
			$kdAbsn='H';
			$hasilAbsn[$dzbar->kodeorg.$kdAbsn]+=1;
			$klmpkAbsn[$kdAbsn]=$kdAbsn;
			$dtKdorg[$dzbar->kodeorg]=$dzbar->kodeorg;
        }
        #tambahan absen permintaan abis disini#

        $bln=explode('-',$periode);
        $idBln=intval($bln[1]);	

          //array data komponen penambah dan pengurang
          $sKomp="select id,name from ".$dbname.".sdm_ho_component ";
          $qKomp=mysql_query($sKomp) or die(mysql_error());
          while($rKomp=mysql_fetch_assoc($qKomp)){
              $arrNmKomPls[$rKomp['id']]=$rKomp['name'];
          }
         
      //prepare array data gaji karyawan,nama,jabatan,tmk dan bagian
         $sSlip="select sum(jumlah) as jumlah,idkomponen,kodeorg,a.subbagian from ".$dbname.".sdm_gaji a
		 left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
		 where periodegaji='".$periode."' and kodeorg in (".$qDiv.") and tipekaryawan='".$tpKary."' group by kodeorg,a.subbagian,idkomponen";	
        // exit("Error:".$sSlip);
        $qSlip=mysql_query($sSlip) or die(mysql_error());
        $rCek=mysql_num_rows($qSlip);
        if($rCek>0){
                while($rSlip=mysql_fetch_assoc($qSlip)){
                    
                		if($rSlip['subbagian']==''){
                			$rSlip['subbagian']=$rSlip['kodeorg'];
                		}
                     $dtKdorg[$rSlip['subbagian']]=$rSlip['subbagian'];
                     $rupGaji[$rSlip['subbagian'].$rSlip['idkomponen']]=$rSlip['jumlah'];
					 $sKomp="select plus from ".$dbname.".sdm_ho_component where id='".$rSlip['idkomponen']."'";
				     $qKomp=mysql_query($sKomp) or die(mysql_error());
					 $rKomp=mysql_fetch_assoc($qKomp);
					 if($rKomp['plus']==0){
						$arrminId[$rSlip['idkomponen']]=$rSlip['idkomponen'];
					 }else{
						$arrplsId[$rSlip['idkomponen']]=$rSlip['idkomponen'];
					 }
                }
                $sTot="select tipelembur,jamaktual,kodeorg from ".$dbname.".sdm_lemburdt a
					   left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
				       where ".$tglGaji." and left(kodeorg,4) in (".$qDiv.") and tipekaryawan='".$tpKary."'";		
                $qTot=mysql_query($sTot) or die(mysql_error($conn));
                while($rTot=mysql_fetch_assoc($qTot))
                {
                        $sJum="select jamlembur as totalLembur from ".$dbname.".sdm_5lembur where tipelembur='".$rTot['tipelembur']."'
                        and jamaktual='".$rTot['jamaktual']."' and kodeorg='".$rTot['kodeorg']."'";
                        $qJum=mysql_query($sJum) or die(mysql_error());
                        $rJum=mysql_fetch_assoc($qJum);
						$kompd="LEMBUR";
                        $jumTot[$rTot['kodeorg'].$kompd]+=$rJum['totalLembur'];
                }
                        $tab.="
                        <table border=1>
                        <tr>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>No.</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['divisi']."</td>";
                   
						$colsdt=count($klmpkAbsn);
						$colsdt2=count($klmpkAbsnGdbyr);
                        $tab.="<td bgcolor=#DEDEDE align=center  colspan='".($colsdt+1)."'>".$_SESSION['lang']['hkdibayar']."</td>";
                        $tab.="<td bgcolor=#DEDEDE align=center colspan='".($colsdt2+1)."'>".$_SESSION['lang']['hktdkdibayar']."</td>";
                        $plsCol=count($arrplsId);
                        $minCol=count($arrminId);
                        $tab.="<td bgcolor=#DEDEDE align=center colspan='".($plsCol+3)."'>".$_SESSION['lang']['penambah']."</td>";
                        $tab.="<td bgcolor=#DEDEDE align=center colspan='".(($minCol-1)+2)."'>".$_SESSION['lang']['pengurang']."</td>";
                        $tab.="<td bgcolor=#DEDEDE align=center rowspan='2'>GAJI BERSIH</td></tr><tr>";
                        foreach($klmpkAbsn as $lstAbsn){
							$tab.="<td bgcolor=#DEDEDE align=center>".$lstAbsn."</td>";
						}
                        $tab.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['total']."</td>";
                        foreach($klmpkAbsnGdbyr as $lstAbsn2){
							$tab.="<td bgcolor=#DEDEDE align=center>".$lstAbsn2."</td>";
						}
                        $tab.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['total']."</td>";
                           foreach($arrplsId as $lstKompPls){
                                    $brsPlus++;
                                    $tab.="<td bgcolor=#DEDEDE align=center>".$arrNmKomPls[$lstKompPls]."</td>";
                                    if($brsPlus==1)
                                    {
                                        $tab.="<td bgcolor=#DEDEDE align=center>".$arrNmKomPls[37]."</td>";
                                        $tab.="<td bgcolor=#DEDEDE align=center>".$arrNmKomPls[36]."</td>";
                                    }
							}
                        $tab.="<td bgcolor=#DEDEDE align=center >".$_SESSION['lang']['totalPendapatan']."</td>";

                                foreach($arrminId as $lstKompMin)
                                {
                                    if(($lstKompMin!=37)&&($lstKompMin!=36)){
                                         $tab.="<td bgcolor=#DEDEDE align=center>".$arrNmKomPls[$lstKompMin]."</td>";
                                    }
                                }			

                      $tab.="<td bgcolor=#DEDEDE align=center >".$_SESSION['lang']['totalPotongan']."</td></tr>";

                   
                $peng1=37;
                $peng2=36;
				$waktuItu=1;
                  foreach($dtKdorg as $lstKbn){
				  
					$no+=1;
					$tab.="<tr class=rowcontent>";
					$tab.="<td>".$no."</td>";
					$tab.="<td>".$lstKbn."</td>";
						foreach($klmpkAbsn as $lstAbsn){
							$tab.="<td  align=right>".$hasilAbsn[$lstKbn.$lstAbsn]."</td>";
							$totAbsn[$lstKbn]+=$hasilAbsn[$lstKbn.$lstAbsn];
							$totAbsnId[$lstAbsn]+=$hasilAbsn[$lstKbn.$lstAbsn];
						}
						$tab.="<td  align=right>".$totAbsn[$lstKbn]."</td>";
						foreach($klmpkAbsnGdbyr as $lstAbsn2){
							$tab.="<td  align=right>".$hasilAbsn[$lstKbn.$lstAbsn2]."</td>";
							$totAbsn2[$lstKbn]+=$hasilAbsn[$lstKbn.$lstAbsn2];
							$totAbsnId2[$lstAbsn2]+=$hasilAbsn[$lstKbn.$lstAbsn2];
						}
						$tab.="<td  align=right>".$totAbsn2[$lstKbn]."</td>";
						foreach($arrplsId as $lstKompPls){
							$tab.="<td  align=right>".number_format($rupGaji[$lstKbn.$lstKompPls],0)."</td>";
							if($waktuItu==1){
								$idKoma=37;
								$idKomb=36;
								if($rupGaji[$lstKbn.$idKoma]==0){
									$tab.="<td align=right>".number_format($rupGaji[$lstKbn.$idKoma],0)."</td>";
								}else{
									$tab.="<td align=right>-".number_format($rupGaji[$lstKbn.$idKoma],0)."</td>";
								}
								if($rupGaji[$lstKbn.$idKomb]==0){
									$tab.="<td align=right>".number_format($rupGaji[$lstKbn.$idKomb],0)."</td>";
								}else{
									$tab.="<td align=right>-".number_format($rupGaji[$lstKbn.$idKomb],0)."</td>";
								}
								
								$totPeng=$rupGaji[$lstKbn.$idKoma]+$rupGaji[$lstKbn.$idKomb];
								$totPendptn[$lstKbn]-=$totPeng;
								$totGajiPerId[$idKoma]+=$rupGaji[$lstKbn.$idKoma];
								$totGajiPerId[$idKomb]+=$rupGaji[$lstKbn.$idKomb];
								$grndPendptn-=$totPeng;
								$waktuItu=0;
							}
							$totGajiPerId[$lstKompPls]+=$rupGaji[$lstKbn.$lstKompPls];
							$totPendptn[$lstKbn]+=$rupGaji[$lstKbn.$lstKompPls];
							$grndPendptn+=$rupGaji[$lstKbn.$lstKompPls];
						}
						$tab.="<td align=right>".number_format($totPendptn[$lstKbn],0)."</td>";
						foreach($arrminId as $lstKompMin){
							if(($lstKompMin!=37)&&($lstKompMin!=36)){
								 $tab.="<td  align=right>".number_format($rupGaji[$lstKbn.$lstKompMin],0)."</td>";
								 $totPengrng[$lstKbn]+=$rupGaji[$lstKbn.$lstKompMin];
								 $totPengrngId[$lstKompMin]+=$rupGaji[$lstKbn.$lstKompMin];
							}
						}	
						$tab.="<td align=right>".number_format($totPengrng[$lstKbn],0)."</td>";
						$gajBersih[$lstKbn]=$totPendptn[$lstKbn]-$totPengrng[$lstKbn];
						$tab.="<td align=right>".number_format($gajBersih[$lstKbn],0)."</td>";
					    $tab.="</tr>";
						$waktuItu=1;
				  }
					$tab.="<tr class=rowcontent>";
					$tab.="<td colspan=2>".$_SESSION['lang']['grnd_total']."</td>";
					foreach($klmpkAbsn as $lstAbsn){
						$tab.="<td  align=right>".$totAbsnId[$lstAbsn]."</td>";
						$grAbsn+=$totAbsnId[$lstAbsn];
					}
						$tab.="<td  align=right>".$grAbsn."</td>";
					foreach($klmpkAbsnGdbyr as $lstAbsn2){
						$tab.="<td  align=right>".$totAbsnId2[$lstAbsn2]."</td>";
						$grAbsn2+=$totAbsnId2[$lstAbsn2];
					}
						$tab.="<td  align=right>".$grAbsn2."</td>";
					foreach($arrplsId as $lstKompPls){
							$tab.="<td  align=right>".number_format($totGajiPerId[$lstKompPls],0)."</td>";
							if($waktuItu==1){
								$idKoma=37;
								$idKomb=36;
								if($totGajiPerId[$idKoma]==0){
									$tab.="<td align=right>".number_format($totGajiPerId[$idKoma],0)."</td>";
								}else{
									$tab.="<td align=right>-".number_format($totGajiPerId[$idKoma],0)."</td>";
								}
								if($totGajiPerId[$idKomb]==0){
									$tab.="<td align=right>".number_format($totGajiPerId[$idKomb],0)."</td>";
								}else{
									$tab.="<td align=right>-".number_format($totGajiPerId[$idKomb],0)."</td>";
								}
								$totPeng=$totGajiPerId[$idKoma]+$totGajiPerId[$idKomb];
								$waktuItu=0;
							}
					}
					$tab.="<td  align=right>".number_format($grndPendptn,0)."</td>";
					foreach($arrminId as $lstKompMin){
							if(($lstKompMin!=37)&&($lstKompMin!=36)){
								 $tab.="<td  align=right>".number_format($totPengrngId[$lstKompMin],0)."</td>";
								 $grnPngrang+=$totPengrngId[$lstKompMin];
							}
					}	
					$tab.="<td  align=right>".number_format($grnPngrang,0)."</td>";
					$granBersih=$grndPendptn-$grnPngrang;
					$tab.="<td  align=right>".number_format($granBersih,0)."</td>";
					$tab.="</tr>";
                        //=================================================
					$tab.="</table>";
 
    }    
	}
switch($proses){
    case'preview':
        echo $tab;
    break;
case'getKary':
    $optKary="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
    if(strlen($kdUnit)>4){
		$dtisi=0;
        $whr=" subbagian='".$kdUnit."' ";
    }else{
        $whr=" lokasitugas='".$kdUnit."' ";
		$dtisi=1;
		$optAfd=$optKary;
		$safd="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where induk='".$kdUnit."'";
		$qafd=mysql_query($safd) or die(mysql_error($conn));
		while($rafd=mysql_fetch_assoc($qafd)){
			$optAfd.="<option value='".$rafd['kodeorganisasi']."'>".$rafd['namaorganisasi']."</option>";
		}
    }
    
    $sData="select distinct nik,karyawanid,namakaryawan from ".$dbname.".datakaryawan "
         . " where ".$whr." and tipekaryawan=4 order by namakaryawan asc";
    $qData=  mysql_query($sData) or die(mysql_error($conn));
    while($rData=  mysql_fetch_assoc($qData)){
        $optKary.="<option value='".$rData['karyawanid']."'>".$rData['nik']."-".$rData['namakaryawan']."</option>";
    }
	if($dtisi==1){
		echo $optAfd."####".$optKary;
	}else{
		echo $optKary;
	}
break;
case'excel':
 $tab.="Print Time:".date('d-m-Y H:i:s')."<br>By:".$_SESSION['empl']['name'];
 $tmz=date('dmYHis');
 $nop_="rekapGaji_".$periode."__".$tmz;
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