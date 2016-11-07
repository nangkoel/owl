<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
$kodeorg	=$_POST['kodeorgJ'];
$karyawanid	=$_POST['karyawanidJ'];
$periode	=$_POST['periodeJ'];
$dari		=tanggalsystem($_POST['dariJ']);
$sampai		=tanggalsystem($_POST['sampaiJ']);
$jam            =$_POST['Jam'];
$jam2            =$_POST['Jam2'];
$diambil	=$_POST['diambilJ'];
$keterangan	=$_POST['keteranganJ'];
$method     =$_POST['method'];

//periksa apakah ada yang tidak benar
//==============================================
if($method=='insert'){
$strc="select * from ".$dbname.".sdm_cutidt
       where karyawanid = '".$karyawanid."' and ((daritanggal>=".$dari." and daritanggal<=".$sampai.")
	   or (sampaitanggal>=".$dari." and sampaitanggal<=".$sampai.")
	   or (daritanggal<=".$dari." and sampaitanggal>=".$sampai."))";
$res=mysql_query($strc);
	if(mysql_num_rows($res)>0)
	{
            echo strtoupper($_SESSION['lang']['irisan'])."\n";
            while($bar=mysql_fetch_object($res)){
		echo "Data cuti utk tanggal yang dimaksud sudah digunakan utk periode cuti ".$bar->periodecuti."\n";
            }
            exit('Error');
	}
	else if($sampai<$dari)
	{
		echo " Error < >";
		exit(0);
	} 
}
  
//===============================================

	if($diambil==''){
		$diambil=0;
	}
	
	switch($method)
	{
	case 'delete':	
		$str="delete from ".$dbname.".sdm_cutidt
		       where kodeorg='".$kodeorg."'
			   and karyawanid=".$karyawanid."
			   and periodecuti='".$periode."'
			   and daritanggal='".$_POST['dariJ']."'";
		$sData="select * from ".$dbname.".sdm_cutidt
		       where kodeorg='".$kodeorg."'
			   and karyawanid=".$karyawanid."
			   and periodecuti='".$periode."'
			   and daritanggal='".$_POST['dariJ']."'";
		$qData=mysql_query($sData) or die(mysql_error($conn));
		$rData=mysql_fetch_assoc($qData);
		$dari=$rData['daritanggal'];
		$sampai=$rData['sampaitanggal'];
		break;	   
	case 'insert':
		$str="insert into ".$dbname.".sdm_cutidt 
		      (kodeorg,karyawanid,periodecuti,daritanggal,
			  sampaitanggal,jam,jamPlg,jumlahcuti,keterangan
			  )
		      values('".$kodeorg."',".$karyawanid.",
			  '".$periode."','".$dari."','".$sampai."','".$jam."','".$jam2."',
			  ".$diambil.",'".$keterangan."'
			  )";
		break;
	default:
	   break;					
	}
	#sumber utk tanggal,regional,subbagian/lokasitugas jika memiliki subbagian maka terisi kodeorg dengan subbagian
	$tgl1=$dari;
	$tgl2=$sampai;
	$test = dates_inbetween($tgl1, $tgl2);
	$whrTp="karyawanid='".$karyawanid."'";
	$optTipe=makeOption($dbname,'datakaryawan','karyawanid,tipekaryawan',$whrTp);
	$optSub=makeOption($dbname,'datakaryawan','karyawanid,subbagian',$whrTp);
	$optLksi=makeOption($dbname,'datakaryawan','karyawanid,lokasitugas',$whrTp);
	$whrReg="kodeunit='".$optLksi[$karyawanid]."'";
	$optReg=makeOption($dbname,'bgt_regional_assignment','kodeunit,regional',$whrReg);
	
	if(mysql_query($str))
		{
		//ambil sum jumlah diambil dan update table header
		$strx="select sum(jumlahcuti) as diambil from ".$dbname.".sdm_cutidt
		       where kodeorg='".$kodeorg."'
			   and karyawanid=".$karyawanid."
			   and periodecuti='".$periode."'";
			   
		$diambil=0;
		$resx=mysql_query($strx);
		while($barx=mysql_fetch_object($resx))
		{
			$diambil=$barx->diambil;
		}
                if($diambil=='')
                    $diambil=0;
		$strup="update ".$dbname.".sdm_cutiht set diambil=".$diambil.",sisa=(hakcuti-".$diambil.")	
		       where kodeorg='".$kodeorg."'
			   and karyawanid=".$karyawanid."
			   and periodecuti='".$periode."'";
			if(mysql_query($strup)){
				if($method=='insert'){
					if($optTipe[$karyawanid]>2){
						#jika karyawan bertipe KHT/KBL di isikan absennya jika jenis ijinnya adalah cuti
						if($optTipe[$karyawanid]==4){
							$whrGj="karyawanid='".$karyawanid."' and tahun='".substr($tgl1,0,4)."' and idkomponen=1";
							$optGaji=makeOption($dbname,'sdm_5gajipokok','karyawanid,jumlah',$whrGj);
                                                        $umr=$optGaji[$karyawanid]/25;
                                                        if($umr==0){
                                                            exit("error: ".$_SESSION['lang']['basicsalarynotfound']);
                                                        }
						}
						if($optSub[$karyawanid]!=''){
							$kdOrg=$optSub[$karyawanid];
						}else{
							$kdOrg=$optLksi[$karyawanid];
						}
                                                // Menghitung insentif khusus KHT
                                                if($jam2=='00:00') $jam="00:00";
                                                $jm1=explode(":",$jam);
                                                $jm2=explode(":",$jam2);
                                                $dtTmbh=0;
                                                if($jm2<$jm1){
                                                    $dtTmbh=1;
                                                }
                                                $tgl=explode("-",$dtTgl);
                                                $isi=$tgl[2]."-".$tgl[1]."-".$tgl[0];
                                                $qwe=date('D', strtotime($isi));

                                                $wktmsk=mktime(intval($jm1[0]),intval($jm1[1]),intval($jm1[2]),intval(substr($dtTgl,3,2)),intval(substr($dtTgl,0,2)),substr($dtTgl,6,4));
                                                $wktplg=mktime(intval($jm2[0]),intval($jm2[1]),intval($jm2[2]),intval(substr($dtTgl,3,2)),intval(substr($dtTgl,0,2)+$dtTmbh),substr($dtTgl,6,4));
                                                $slsihwaktu=$wktplg-$wktmsk;
                                                $sisa = $slsihwaktu % 86400;
                                                $jumlah_jam = $sisa/3600;  
                                                if($qwe=='Sat'){
                                                    if($jumlah_jam>=5){
                                                        $_POST['insentif']=$umr;
                                                    }else{
                                                        $_POST['insentif']=($jumlah_jam/5)*$umr;    
                                                    }    
                                                }else{
                                                    if($jumlah_jam>=7){
                                                        $_POST['insentif']=$umr;
                                                    }else{
                                                        $_POST['insentif']=($jumlah_jam/7)*$umr;    
                                                    }
                                                }
//						if($optGaji[$karyawanid]==''){
//							$_POST['insentif']=0;
//						}else{
//							@$_POST['insentif']=$optGaji[$karyawanid]/25;
//						}
						foreach($test as $rwTgl=>$dtTgl){
								$qwe=date('D', strtotime($dtTgl));
								if($qwe=='Sun'){
									continue;
								}else{
									$whr="regional='".$optReg[$optLksi[$karyawanid]]."' and tanggal='".$dtTgl."'";
									$optLbr=makeOption($dbname,'sdm_5harilibur','regional,tanggal',$whr);
									if($optLbr[$optReg[$optLksi[$karyawanid]]]!=''){
										continue;
									}
								}
								$sCek="select * from ".$dbname.".sdm_absensiht where tanggal='".$dtTgl."' and kodeorg='".$kdOrg."'";
								$qCek=mysql_query($sCek) or die(mysql_error($conn));
								$rCek=mysql_num_rows($qCek);
								if($rCek!=0){
									$ssel="select * from ".$dbname.".sdm_absensidt where karyawanid='".$karyawanid."' and tanggal='".$dtTgl."'";
                                                                        $qsel=mysql_query($ssel) or die(mysql_error($conn));
                                                                        $rsel=mysql_fetch_assoc($qSel);
                                                                        if ($rsel['absensi']!='L' and $rsel['absensi']!='P2'){
                                                                            $sdel="delete from ".$dbname.".sdm_absensidt where karyawanid='".$karyawanid."' and tanggal='".$dtTgl."'";
                                                                            if(mysql_query($sdel)){
										
										$sDetIns="insert into ".$dbname.".sdm_absensidt (`kodeorg`,`tanggal`, `karyawanid`, `shift`, `absensi`, `jam`,`jamPlg`, `penjelasan`,`penaltykehadiran`,`premi`,`insentif`) 
										values ('".$kdOrg."','".$dtTgl."','".$karyawanid."','".$shifTid."','C','".$jam."','".$jam2."','".$keterangan."','0','0',".$_POST['insentif'].")";
										  if(!mysql_query($sDetIns)){
											exit("warning: ".mysql_error($conn)."____".$sDetIns);
										  }
                                                                            }
                                                                        }
								}else{
									#ambil periode gaji
									$sPrd="select distinct periode from ".$dbname.".sdm_5periodegaji where tanggalsampai>='".$dtTgl."' and tglcutoff<='".$dtTgl."' and kodeorg='".substr($dtTgl,0,4)."'";
									$qPrd=mysql_query($sPrd) or die(mysql_error($conn));
									$rPrd=mysql_fetch_assoc($qPrd);
									if($rPrd['periode']==''){
										$rPrd['periode']=substr($dtTgl,0,7);
									}
										#insert ke sdm_absensidt
										$sdel="delete from ".$dbname.".sdm_absensidt where karyawanid='".$karyawanid."' and tanggal='".$dtTgl."'";
										if(mysql_query($sdel)){
											$sDetIns="insert into ".$dbname.".sdm_absensidt (`kodeorg`,`tanggal`, `karyawanid`, `shift`, `absensi`, `jam`,`jamPlg`, `penjelasan`,`penaltykehadiran`,`premi`,`insentif`) 
											values ('".$kdOrg."','".$dtTgl."','".$karyawanid."','".$shifTid."','C','".$jam."','".$jam2."','".$keterangan."','0','0',".$_POST['insentif'].")";
											  if(!mysql_query($sDetIns)){
												exit("warning: ".mysql_error($conn)."____".$sDetIns);
											  }
										}
									
								}
						}
					}
				}elseif($method=='delete'){
					foreach($test as $rwTgl=>$dtTgl){
						$qwe=date('D', strtotime($dtTgl));
						if($qwe=='Sun'){
							continue;
						}else{
							$whr="regional='".$optReg[$optLksi[$karyawanid]]."' and tanggal='".$dtTgl."'";
							$optLbr=makeOption($dbname,'sdm_5harilibur','regional,tanggal',$whr);
							if($optLbr[$optReg[$optLksi[$karyawanid]]]!=''){
								continue;
							}
						}
						$sdel="delete from ".$dbname.".sdm_absensidt where karyawanid='".$karyawanid."' and tanggal='".$dtTgl."' and absensi='C'";
							if(!mysql_query($sdel)){
								exit("warning: ".mysql_error($conn)."____".$sdel);
							}
					}
				}
				
			}	   
		}
	else
		{echo " Gagal,".addslashes(mysql_error($conn));}
		
		
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
