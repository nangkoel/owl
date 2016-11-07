<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
#include_once('lib/zGrid.php');
#include_once('lib/rGrid.php');
include_once('lib/formTable.php');
?>

<?php

$proses = $_GET['proses'];
$param = $_POST;



switch($proses) {
    case 'showDetail':
	#== Prep Tab
	$headFrame = array(
	    $_SESSION['lang']['prestasi'],
	    $_SESSION['lang']['absensi'],
	    $_SESSION['lang']['material']
	);
	$contentFrame = array();
	
	# Options
	#$whereOrg = "kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' and ";
	#$whereOrg .= "tipe='BLOK' and induk='".$param['afdeling']."'";
	$whereKeg = "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and ";
	$whereKeg .= "kelompok='PNN'";
	
	$optKeg = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan',$whereKeg);
	
	
	$whereOrg = "kodeorg like '%".$param['afdeling']."%'";
        $optOrg = makeOption($dbname,'setup_blok','kodeorg,bloklama,kodeorg',$whereOrg,'9',true);
	$firstOrg = end(array_reverse(array_keys($optOrg)));
	
	$optThTanam= makeOption($dbname,'setup_blok','kodeorg,tahuntanam',
	    "kodeorg='".end(array_reverse(array_keys($optOrg)))."'");
	$optBin = array('1'=>'Ya','0'=>'Tidak');
	$thTanam = $optThTanam[end(array_reverse(array_keys($optOrg)))];
	
	#ambilbjraktual
	
	//exit("Error:A");
	
	$tgld = explode('-',$param['tanggal']);
	$sBjr="SELECT sum(a.totalkg)/sum(a.jjg) as bjr,tanggal 
		   FROM ".$dbname.".`kebun_spbdt` a left join ".$dbname.".kebun_spbht b on 
		   a.nospb=b.nospb where blok like '".substr($firstOrg,0,6)."%'
		   and tanggal = '".tanggalsystem($param['tanggal'])."' group by tanggal order by tanggal desc limit 1";
		  // exit("Error:$sBjr");
	$qBjr=mysql_query($sBjr) or die(mysql_error($conn));
	$rBjr=mysql_fetch_assoc($qBjr);
	$rBjrCek=mysql_num_rows($qBjr);
	if((intval($rBjr['bjr'])==0)||($rBjrCek==0)){
		$query = selectQuery($dbname,'kebun_5bjr','kodeorg,bjr',
		"kodeorg='".$firstOrg."' and tahunproduksi = '".$tgld[2]."'");
		$res = fetchData($query);
		if(!empty($res)) {
			$rBjr['bjr']=$res[0]['bjr'];
		} else {
			$rBjr['bjr'] = 0;
		}
	}
	
	#================ Prestasi =============================
	# Get Data
	$where = "notransaksi='".$param['notransaksi']."'";
	$cols = "nik,kodeorg,bjraktual,tahuntanam,tarif,norma,hasilkerja,hasilkerjakg,upahkerja,upahpremi,".
	    "penalti1,penalti2,penalti3,penalti4,penalti5,penalti6,penalti7,rupiahpenalty,luaspanen";
	$query = selectQuery($dbname,'kebun_prestasi',$cols,$where);
	$data = fetchData($query);
	
	$nikList = "";
        $totalhasilkerja=0;
	foreach($data as $row) {
                $totalhasilkerja+=$row['hasilkerja'];
		if($nikList!="") {$nikList .= ',';}
		$nikList .= $row['nik'];
	}
	
	#============== KHT, KHL dan Kontrak ======================
/*	$whereKary = "(lokasitugas='".$_SESSION['empl']['lokasitugas']."' and ".
	    "tipekaryawan in (2,3,6))";*/
	$whereKary = "(lokasitugas='".$_SESSION['empl']['lokasitugas']."' and ".
	    "tipekaryawan='4')";	
	if(!empty($nikList)) {
		$whereKary .= " or karyawanid in (".$nikList.")";
	}
	$qKary = selectQuery($dbname,'datakaryawan','karyawanid,namakaryawan,nik,subbagian',$whereKary);
	$resKary = fetchData($qKary);
	$optKary = array();
	$optKary[]='';
	foreach($resKary as $kary) {
		$optKary[$kary['karyawanid']].= $kary['nik']."-".$kary['namakaryawan'].'('.$kary['subbagian'].')';
	}
	// $optKary = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan,subbagian',$whereKary,'8');
	#============== KHT, KHL dan Kontrak ======================
	
	#=============================== Get UMR ==============================
	$firstKary = getFirstKey($optKary);
	$qUMR = selectQuery($dbname,'sdm_5gajipokok','sum(jumlah) as nilai',
	    "karyawanid=".$firstKary." and tahun=".date('Y')." and idkomponen in (1,31)");
	$Umr = fetchData($qUMR);
	#=============================== Get UMR ==============================
	
	$dataShow = $data;
	foreach($dataShow as $key=>$row) {
	    $dataShow[$key]['nik'] = $optKary[$row['nik']];
	    #$dataShow[$key]['kodekegiatan'] = $optKeg[$row['kodekegiatan']];
	    $dataShow[$key]['kodeorg'] = $optOrg[$row['kodeorg']];
	    #$dataShow[$key]['pekerjaanpremi'] = $optBin[$row['pekerjaanpremi']];
	}
	$arrData=array("satuan"=>"satuan","harian"=>"harian");
    #function addEls($cId,$cName,$cCont=null,$cType=null,$cAlign=null,$cLength=null,$cRefer=null,$cCont2=null,$cTSatuan=null,$cTHarga=null,$cParent=null)
	# Form
	$theForm2 = new uForm('prestasiForm','Form Prestasi',3);       
	$theForm2->addEls('nik',$_SESSION['lang']['nik'],'','selectsearch','L',25,$optKary);
	$theForm2->_elements[0]->_attr['onchange'] = "updUpah()";
	$theForm2->addEls('kodeorg',$_SESSION['lang']['kodeorg'],'','selectsearch','L',25,$optOrg,null,null,null,'ftPrestasi_kodeorg');
	$theForm2->_elements[1]->_attr['onchange'] = "updTahunTanam();";         
	$theForm2->addEls('bjraktual',$_SESSION['lang']['bjraktual'],number_format($rBjr['bjr'],2),'textnum','R',6);
	$theForm2->_elements[2]->_attr['disabled'] = 'disabled';
	$theForm2->addEls('tahuntanam',$_SESSION['lang']['tahuntanam'],$thTanam,'textnum','R',6);
	$theForm2->_elements[3]->_attr['disabled'] = 'disabled';
//        $theForm2->addEls('bjr',$_SESSION['lang']['bjr'],'','textnum','R',6);
//	$theForm2->_elements[3]->_attr['disabled'] = 'disabled';
//        $theForm2->_elements[3]->_attr['onchange'] = "updBjr();";   
	$theForm2->addEls('tarif',$_SESSION['lang']['tarif'],'','select','L',25,$arrData);
	$theForm2->_elements[4]->_attr['onchange'] = "updUpah();";         
	$theForm2->addEls('norma',$_SESSION['lang']['basisjjg'],'0','textnum','R',10);
	$theForm2->_elements[5]->_attr['disabled'] = 'disabled';
	$theForm2->addEls('hasilkerja',$_SESSION['lang']['hasilkerja'],'0','textnum','R',10);
	$theForm2->_elements[6]->_attr['onblur'] = "updUpah();";         
	$theForm2->addEls('hasilkerjakg',$_SESSION['lang']['hasilkerjakg'],'0','textnum','R',10);
	$theForm2->_elements[7]->_attr['disabled'] = 'disabled';
	$theForm2->_elements[7]->_attr['title'] = 'Hasil Kerja (JJG) * BJR [Kebun - Setup - Tabel BJR]';
	$theForm2->addEls('upahkerja',$_SESSION['lang']['upahkerja'],$Umr[0]['nilai']/25,'textnum','R',10);
	$theForm2->_elements[8]->_attr['disabled'] = 'disabled';
	$theForm2->addEls('upahpremi',$_SESSION['lang']['upahpremi'],'0','textnum','R',10);
	$theForm2->_elements[9]->_attr['disabled'] = 'disabled';
	/*$theForm2->addEls('umr',$_SESSION['lang']['umr'],'0','textnum','R',10);
	$theForm2->addEls('statusblok',$_SESSION['lang']['statusblok'],'-','text','L',4);
	$theForm2->addEls('pekerjaanpremi',$_SESSION['lang']['pekerjaanpremi'],'0','select','R',10,$optBin);*/
	$theForm2->addEls('penalti1',$_SESSION['lang']['penalti1'],'0','textnum','R',10);
	$theForm2->addEls('penalti2',$_SESSION['lang']['penalti2'],'0','textnum','R',10);
	$theForm2->addEls('penalti3',$_SESSION['lang']['penalti3'],'0','textnum','R',10);
	$theForm2->addEls('penalti4',$_SESSION['lang']['penalti4'],'0','textnum','R',10);
	$theForm2->addEls('penalti5',$_SESSION['lang']['penalti5'],'0','textnum','R',10);
	$theForm2->addEls('penalti6',$_SESSION['lang']['penalti6'],'0','textnum','R',10);
	$theForm2->addEls('penalti7',$_SESSION['lang']['penalti7'],'0','textnum','R',10);
	$theForm2->_elements[10]->_attr['onblur'] = "updDenda('BM');";   
	$theForm2->_elements[11]->_attr['onblur'] = "updDenda('TP');";   
	$theForm2->_elements[12]->_attr['onblur'] = "updDenda('TD');";   
	
	$theForm2->_elements[14]->_attr['onblur'] = "updDenda('BT');";   
	$theForm2->_elements[15]->_attr['onblur'] = "updDenda('PT');";   
	$theForm2->_elements[16]->_attr['onblur'] = "updDenda('TM');";   
	$theForm2->addEls('rupiahpenalty',$_SESSION['lang']['rupiahpenalty'],'0','textnum','R',10);
        $theForm2->_elements[17]->_attr['disabled'] = 'disabled';
	$theForm2->addEls('luaspanen',$_SESSION['lang']['luaspanen'],'0','textnum','R',10);
	
	# Table
	$theTable2 = new uTable('prestasiTable','Tabel Prestasi',$cols,$data,$dataShow);
	
	# FormTable
	$formTab2 = new uFormTable('ftPrestasi',$theForm2,$theTable2,null,array('notransaksi'));
	$formTab2->_target = "kebun_slave_panen_detail";
	$formTab2->_noClearField = '##kodeorg##tahuntanam##bjraktual##norma##luaspanen';
	$formTab2->_noEnable = '##tahuntanam##bjraktual##upahkerja##upahpremi##rupiahpenalty##hasilkerjakg##norma';
	$formTab2->_defValue = '##upahkerja='.$Umr[0]['nilai']/25;
	
	#== Display View
	# Draw Tab
	echo "<fieldset><legend><b>Detail</b></legend>";
       # echo "<button class=mybutton id=filternik onclick=filterKaryawan(val='null') title='Tampilkan Semua Karyawan'>Show All</button>";
	echo "<input type=checkbox id=allptnik onclick=allPtKaryawan('nik',this) title='Show All Employee in Company'>All Employee in Company</checkbox>";
	$formTab2->render();
	echo "<table><tr><td>".$totalhasilkerja."</td></tr></table>";
	echo "</fieldset>";
	break;
    case 'add':
        
	$cols = array(
	    'nik','kodeorg','bjraktual','tahuntanam','tarif','norma','hasilkerja','hasilkerjakg','upahkerja','upahpremi',
	    'penalti1','penalti2','penalti3','penalti4','penalti5','penalti6','penalti7',
	    'rupiahpenalty','luaspanen','notransaksi','kodekegiatan','statusblok','pekerjaanpremi'
	);
	$data = $param;
	unset($data['numRow']);
	# Additional Default Data
	$data['kodekegiatan'] = '0';
	$data['statusblok'] = 0;$data['pekerjaanpremi'] = 0;
        $dmn="notransaksi='".$data['notransaksi']."' and nik='".$data['nik']."' and kodekegiatan='".$data['kodekegiatan']."'";
        $optCek=makeOption($dbname, 'kebun_prestasi', 'notransaksi,nik',$dmn);
        if(isset($optCek[$data['notransaksi']]) and $optCek[$data['notransaksi']]!=''){
            $warning="Data sudah ada";
            echo "error:  ".$warning.".";
            exit();
        }
        if($data['upahkerja']==0){
            $warning="Upah tidak boleh kosong";
            echo "error:  ".$warning.".";
            exit();
        }
        if($data['luaspanen']==0){
            $warning="Luas Panen(Ha)";
            echo "error: Silakan mengisi ".$warning.".";
            exit();
        }
        else{
            $query = insertQuery($dbname,'kebun_prestasi',$data,$cols);
            //echo "error: Silakan mengisi ".$query.".";
            //exit();
            if(!mysql_query($query)) {
                echo "DB Error : ".mysql_error();
                exit;
            }
        }
        unset($data['notransaksi']);unset($data['kodekegiatan']);
        unset($data['statusblok']);
        unset($data['pekerjaanpremi']);

        $res = "";
        foreach($data as $cont) {
            $res .= "##".$cont;
        }

        $result = "{res:\"".$res."\",theme:\"".$_SESSION['theme']."\"}";
        echo $result;
        break;
    case 'edit':
	$data = $param;
	unset($data['notransaksi']);
	foreach($data as $key=>$cont) {
	    if(substr($key,0,5)=='cond_') {
		unset($data[$key]);
	    }
	}
        $dmn="notransaksi='".$data['notransaksi']."' and nik='".$data['nik']."' and kodekegiatan='".$data['kodekegiatan']."'";
        $optCek=makeOption($dbname, 'kebun_prestasi', 'notransaksi,nik',$dmn);
        if($optCek[$data['notransaksi']]!=''){
            $warning="Data sudah ada";
            echo "error:  ".$warning.".";
            exit();
        }
        if($data['upahkerja']==0){
            $warning="Upah tidak boleh kosong";
            echo "error:  ".$warning.".";
            exit();
        }
        if($data['luaspanen']==0){
            $warning="Luas Panen(Ha)";
            echo "error: Silakan mengisi ".$warning.".";
            exit();
        }
	$where = "notransaksi='".$param['notransaksi']."' and nik='".$param['cond_nik'].
	    "' and kodeorg='".$param['cond_kodeorg']."'";
	$query = updateQuery($dbname,'kebun_prestasi',$data,$where);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	echo json_encode($param);
	break;
    case 'delete':
	$where = "notransaksi='".$param['notransaksi']."' and nik='".$param['nik'].
	    "' and kodeorg='".$param['kodeorg']."'";
	$query = "delete from `".$dbname."`.`kebun_prestasi` where ".$where;
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	break;
    case 'updTahunTanam':
	$query = selectQuery($dbname,'setup_blok','kodeorg,tahuntanam',
	    "kodeorg='".$param['kodeorg']."'");
	$res = fetchData($query);
	if(!empty($res)) {
	    $thntnm= $res[0]['tahuntanam'];
	} else {
	    $thntnm= 0;
	}
        $tgld=explode("-",$param['tanggal']);
        #ambilbjraktual
        $sBjr="SELECT sum(a.totalkg)/sum(a.jjg) as bjr,tanggal 
               FROM ".$dbname.".`kebun_spbdt` a left join ".$dbname.".kebun_spbht b on 
               a.nospb=b.nospb where blok like '".substr($param['kodeorg'],0,6)."%'
               and tanggal = '".tanggalsystem($param['tanggal'])."' group by tanggal order by tanggal desc limit 1";
        //exit("error:".$sBjr);
        $qBjr=mysql_query($sBjr) or die(mysql_error($conn));
        $rBjr=mysql_fetch_assoc($qBjr);
        $rBjrCek=mysql_num_rows($qBjr);
        if((intval($rBjr['bjr'])==0)||($rBjrCek==0)){
            $query = selectQuery($dbname,'kebun_5bjr','kodeorg,bjr',
            "kodeorg='".$param['kodeorg']."' and tahunproduksi = '".$tgld[2]."'");
            $res = fetchData($query);
            if(!empty($res)) {
            $rBjr['bjr']=$res[0]['bjr'];
            } else {
                exit("error: BJR is not exist");
            }
        }
        echo $thntnm."####".number_format($rBjr['bjr'],2);
	break;
    case 'updBjr':
        $tahuntahuntahun=substr($param['notransaksi'],0,4);
        $hasilhasilhasil=$param['hasilkerja'];
	$query = selectQuery($dbname,'kebun_5bjr','kodeorg,bjr',
	    "kodeorg='".$param['kodeorg']."' and tahunproduksi = '".$tahuntahuntahun."'");
	$res = fetchData($query);
	if(!empty($res)) {
            $hasilhasil=$hasilhasilhasil*$res[0]['bjr'];
	    echo $hasilhasil;
	} else {
	    echo '0';
	}
	break;
    case 'updUpah':
        $dtr="kodeorg='".$param['blok']."'";
        $optTopo=makeOption($dbname, 'setup_blok', 'kodeorg,topografi',$dtr);
        $hasilKg=$param['bjraktual']*$param['jmlhJjg'];
	    $firstKary = $param['nik'];
        $tgl=explode("-",$param['tanggal']);
        $tnggl=$tgl[2]."-".$tgl[1]."-".$tgl[0];
	#cek periode
        $qPer = selectQuery($dbname,'sdm_5periodegaji','periode',
	    "tanggalmulai<'".$tnggl."' and tanggalsampai>'".$tnggl."' and kodeorg='".substr($param['blok'],0,4)."'");
        $Per=fetchData($qPer);
	#cek nilai pemanen
        $qGrade = selectQuery($dbname,'kebun_5nilaipemanen','nilai',
	    "karyawanid='".$param['nik']."' and periodegaji='".$Per[0]['periode']."'");
        $Grade=fetchData($qGrade);
        $grade=($Grade[0]['nilai']!='')?$Grade[0]['nilai']:2;
	#cek gaji pokok
        $qUMR = selectQuery($dbname,'sdm_5gajipokok','sum(jumlah) as nilai',
	    "karyawanid=".$firstKary." and tahun=".$tgl[2]." and idkomponen in (1,31)");
        //exit("error:".$qUMR);
	$Umr = fetchData($qUMR);
        $uphHarian=$Umr[0]['nilai']/25;
        if($uphHarian==0){
            exit("error: Don't have basic salary !!");
        }
        $qwe=date('D', strtotime($tnggl));
        #cek tanggal apa ada di hari libur
        $dhr="regional='".$_SESSION['empl']['regional']."' and tanggal='".$tnggl."'";
        $optHariLbr=makeOption($dbname, 'sdm_5harilibur', 'regional,tanggal',$dhr);
        
        #query kebun_5basispanen buat ambil rplebih,basisjjg,status denda,insentif topografi
        
        $regData=$_SESSION['empl']['regional'];
        if($_SESSION['empl']['regional']=='SULAWESI'){
            $afd=substr($param['blok'],0,6);
            $dmn="kodeorg='".$afd."' and grade=".$grade;
            $optCek=makeOption($dbname, 'kebun_5basispanen', 'kodeorg,jenis',$dmn);
            if(isset($optCek[$afd]) and $optCek[$afd]!=''){
                $regData=$afd;
            }
        }
        $dmn="kodeorg='".$regData."' and jenis='".$param['tarif']."' and grade=".$grade;
        $optRp=makeOption($dbname, 'kebun_5basispanen', 'jenis,rplebih',$dmn);
        $optBasis=makeOption($dbname, 'kebun_5basispanen', 'jenis,basisjjg',$dmn);
        $optDenda=makeOption($dbname, 'kebun_5basispanen', 'jenis,dendabasis',$dmn);
        if($optTopo[$param['blok']]=='B1'){
              $optIns=makeOption($dbname, 'kebun_5basispanen', 'jenis,rptopografi',$dmn);
        }
        #query kebun_5basispanen abis disini
        
        #membentuk array bjr klo regional kalimantan
                $lstert=0;
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
        #membentuk abis disini
        
        #mulai cek panen di hari libur
        if(($qwe=='Sun')||(isset($optHariLbr[$_SESSION['empl']['regional']]) and $optHariLbr[$_SESSION['empl']['regional']]!='')){
            $basis=0;
            if($_SESSION['empl']['regional']=='SULAWESI'){        
                switch($param['tarif']){
                    case'harian':
                        $basis=$optBasis[$param['tarif']];
                         if($basis==0){
                             $upah=$uphHarian;
                             $insentif=$optIns[$param['tarif']];
                         }
                         if($basis!=0){
                             if($optDenda[$param['tarif']]=='1'){
                                  if($param['jmlhJjg']<$basis){
                                    $upah=$param['jmlhJjg']/$basis*$uphHarian;
                                  }else if($param['jmlhJjg']>$basis){
                                      if($optRp[$param['tarif']]!=0){
                                        $upah=$uphHarian+($optRp[$param['tarif']]*($param['jmlhJjg']-$basis));
                                      }else{
                                          $upah=$uphHarian;  
                                      }
                                      //exit("error: __".$optRp[$param['tarif']]."___".$param['jmlhJjg']."___".$basis);
                                  }else{
                                    $upah=$uphHarian;  
                                  }
                             }else{
                                 $upah=$optRp[$param['tarif']]*$hasilKg;
                             }
                             $insentif=$optIns[$param['tarif']];
                         }
//                        $upah=$optRp[$param['tarif']]*$hasilKg;
//                        $insentif=0;
                    break;
                    case'satuan':
                        $upah=$optRp[$param['tarif']]*$hasilKg;
                        $insentif=$optIns[$param['tarif']];
                        //exit("error:".$insentif);
                    break;
                }
            }else if($_SESSION['empl']['regional']=='KALIMANTAN'){
                
                switch($param['tarif']){
                    case'harian':
                       $basis=$optBasis[$param['tarif']];
						if($optDenda[$param['tarif']]=='1'){
							if($param['jmlhJjg']<$basis){
							$upah=($param['jmlhJjg']/$basis)*$uphHarian;
						}else{
							$upah=$uphHarian;
						}
						}else{
						$upah=$uphHarian;
						}

						$insentif=0;
                        $basis=$optBasis[$param['tarif']];
                    break;
                    case'satuan':
                        $MaxRow=count($lstBjr);
                        foreach($lstBjr as $lstRow=>$dtIsiBjr){
                            if($lstRow==0){
                                if(intval($param['bjraktual'])>=$dtIsiBjr){
                                    $hsl=$rpLbh[$dtIsiBjr]*$hasilKg;
                                    $dtbjr=$dtIsiBjr;
                                    //exit("error:__".$rpLbh[$dtIsiBjr]."__".$hasilKg."__".$dtIsiBjr."___masuk sini");
                                    break;
                                }
                            }else{
                                if($lstRow!=$MaxRow){
                                    $leapdt=$lstRow-1;
									$leapdt2=$lstRow+1;
									//exit("error:__".$lstBjr2[$leapdt]."__".$hasilKg."__".$dtIsiBjr."___masuk sini b");
                                    if((intval($param['bjraktual'])>=$dtIsiBjr)&&(intval($param['bjraktual'])<$lstBjr2[$leapdt])){
                                        $hsl=$rpLbh[$dtIsiBjr]*$hasilKg;
                                        $dtbjr=$dtIsiBjr;
                                        break;
                                    }
                                }else{
                                    $dmin=$dtIsiBjr-1;
                                    $dtbjr=$dtIsiBjr;
                                    if($param['bjraktual']>=$dmin){
                                        $hsl=$rpLbh[$dtIsiBjr]*$hasilKg;
                                        break;
                                    }else{
										$hsl=$rpLbh[$dtIsiBjr]*$hasilKg;
                                        //$hsl=$param['jmlhJjg']/$basisPanen[$dtIsiBjr]*$uphHarian;
                                    }
                                }
                            }
                        }
						if($param['bjraktual']<4){
							$dmn="kodeorg='".$regData."' and jenis='harian' and bjr='0'";
						}else{
							$dmn="kodeorg='".$regData."' and jenis='".$param['tarif']."' and bjr='".$dtbjr."'";
						}
                        
                        $optRp=makeOption($dbname, 'kebun_5basispanen', 'jenis,rplebih',$dmn);
                        $optBasis=makeOption($dbname, 'kebun_5basispanen', 'jenis,basisjjg',$dmn);
                        $optDenda=makeOption($dbname, 'kebun_5basispanen', 'jenis,dendabasis',$dmn);
                        $upah=$hsl;
                        $insentif=0;
                        $basis=$optBasis[$param['tarif']];
                    break;
                }
            }
        }else{#abis cek panen di hari libur
              #mulai cek panen hari normal
            switch($_SESSION['empl']['regional']){
                case'SULAWESI':
                    switch($param['tarif']){
                        case'harian':
                         $basis=$optBasis[$param['tarif']];
                         if($basis==0){
                             $upah=$uphHarian;
                             $insentif=$optIns[$param['tarif']];
                         }
                         if($basis!=0){
                             if($optDenda[$param['tarif']]=='1'){
                                  if($param['jmlhJjg']<$basis){
                                    $upah=$param['jmlhJjg']/$basis*$uphHarian;
                                  }else if($param['jmlhJjg']>$basis){
                                      if($optRp[$param['tarif']]!=0){
                                        $upah=$uphHarian+($optRp[$param['tarif']]*($param['jmlhJjg']-$basis));
                                      }else{
                                          $upah=$uphHarian;  
                                      }
                                      //exit("error: __".$optRp[$param['tarif']]."___".$param['jmlhJjg']."___".$basis);
                                  }else{
                                    $upah=$uphHarian;  
                                  }
                             }else{
                                 $upah=$optRp[$param['tarif']]*$hasilKg;
                             }
                             $insentif=$optIns[$param['tarif']];
                         }
                        break;
                        case'satuan':
                            $basis=$optBasis[$param['tarif']];
                            //exit("error:".$basis."__".$param['tarif']."__".$regData);
                            $upah=$hasilKg*$optRp[$param['tarif']];
                            $insentif=$optIns[$param['tarif']];
                        break;
                    }
                break;
                case'KALIMANTAN':
                    switch($param['tarif']){
                        case'harian':
                            $basis=$optBasis[$param['tarif']];
                                if($optDenda[$param['tarif']]=='1'){
                                    if($param['jmlhJjg']<$basis){
                                    $upah=($param['jmlhJjg']/$basis)*$uphHarian;
                                }else{
                                    $upah=$uphHarian;
                                }
                            }else{
                                $upah=$uphHarian;
                            }
                            
                            $insentif=0;
                        break;
                        case'satuan':
						
                            $insentif=0;
                            $MaxRow=count($lstBjr);
                            foreach($lstBjr as $lstRow=>$dtIsiBjr){
                                if($lstRow==0){
                                    if(intval($param['bjraktual'])>=$dtIsiBjr){
                                        $upah=$rpLbh[$dtIsiBjr]*$hasilKg;
                                        $dtbjr=$dtIsiBjr;
										//exit("error:__".$rpLbh[$dtIsiBjr]."__".$hasilKg."__".$dtIsiBjr."___kesini");
                                        break;
                                    }
                                }else{
                                    if($lstRow!=$MaxRow){
                                        $leapdt=$lstRow-1;
										$leapdt2=$lstRow+1;
										//exit("error:__".$lstBjr2[$leapdt2]."__".intval($param['bjraktual'])."__".$lstBjr2[$leapdt]."___masuk sini b");
										if((intval($param['bjraktual'])>=$dtIsiBjr)&&(intval($param['bjraktual'])<$lstBjr2[$leapdt])){
                                            $upah=$rpLbh[$dtIsiBjr]*$hasilKg;
                                            $dtbjr=$dtIsiBjr;
                                            break;
                                        }
                                    }else{
                                        $dmin=$dtIsiBjr-1;
                                        $dtbjr=$dtIsiBjr;
                                        if($param['bjraktual']>=$dmin){
                                            $upah=$rpLbh[$dtIsiBjr]*$hasilKg;
                                            break;
                                        }else{
											$upah=$rpLbh[$dtIsiBjr]*$hasilKg;
                                           // $upah=$param['jmlhJjg']/$basisPanen[$dtIsiBjr]*$uphHarian;
                                        }
                                    }
                                }
                            }
                            $dmn="kodeorg='".$regData."' and jenis='".$param['tarif']."' and bjr='".$dtbjr."'";
							//exit("error".$dmn);
                            $optBasis=makeOption($dbname, 'kebun_5basispanen', 'jenis,basisjjg',$dmn);
                            $basis=$optBasis[$param['tarif']];
                            $insentif=0;
							if($_SESSION['empl']['regional']!='KALIMANTAN'){
								if($optDenda[$param['tarif']]=='1'){
										if($param['jmlhJjg']<$basis){
										$upah=($param['jmlhJjg']/$basis)*$uphHarian;
									}
								}
							}
                             
                            
                        break;
                    }
                    
                break;
            }
        }
        //exit("error:__".$optIns[$param['tarif']]."__".$param['blok']);
        echo $upah."####".number_format($basis,0)."####".$insentif."####".$hasilKg;
        
	break;
    case'updDenda':
//            echo"<pre>";
//            print_r($_POST['isiDt']);
//            echo"</pre>";
//               
            if($_SESSION['empl']['regional']=='SULAWESI'){
                $dtbjr=0;
            }else{
                $lstert=0;
                $sTarif="select distinct * from ".$dbname.".kebun_5basispanen where 
                         kodeorg='".$_SESSION['empl']['regional']."' and jenis='".$param['tarif']."' order by bjr desc";
                $qTarif=mysql_query($sTarif) or die(mysql_error($conn));
                while($rTarif=  mysql_fetch_assoc($qTarif)){
                    $rpLbh[$rTarif['bjr']]=$rTarif['rplebih'];
                    $basisPanen[$rTarif['bjr']]=$rTarif['basisjjg'];
                    $lstBjr[]=$rTarif['bjr'];
                    $lstBjr2[$lstert]=$rTarif['bjr'];
                    $lstert++;
                }
                $MaxRow=count($lstBjr);
                foreach($lstBjr as $lstRow=>$dtIsiBjr){
                    if($lstRow==0){
                        if($param['bjraktual']>$dtIsiBjr){
                            $dtbjr=$dtIsiBjr;
                            break;
                        }
                    }else{
                        if($lstRow!=$MaxRow){
                            $leapdt=$lstRow+1;
                            if(($param['bjraktual']==$dtIsiBjr)||($param['bjraktual']>$lstBjr2[$leapdt])){
                                $dtbjr=$dtIsiBjr;
                                break;
                            }
                        }else{
                            $dmin=$dtIsiBjr-1;
                           
                            if($param['bjraktual']>=$dmin){
                               $dtbjr=$dtIsiBjr;
                                break;
                            }else{
                                $dtbjr=0;
                            }
                        }
                    }
                }
            }
            $regData=$_SESSION['empl']['regional'];
            if($_SESSION['empl']['regional']=='SULAWESI'){
                $afd=substr($param['blok'],0,6);
                $dmn="kodeorg='".$afd."'";
                $optCek=makeOption($dbname, 'kebun_5basispanen', 'kodeorg,jenis',$dmn);
                if($optCek[$afd]!=''){
                    $regData=$afd;
                }
            }
            $dmn="kodeorg='".$regData."' and jenis='".$param['tarif']."' and bjr='".$dtbjr."'";
            if($_SESSION['empl']['regional']=='SULAWESI'){
                $dmn="kodeorg='".$regData."' and jenis='".$param['tarif']."'";
            }
            if($regData=='H12E02'){
                $dmn="kodeorg='".$_SESSION['empl']['regional']."' and jenis='satuan'";
            }
            
            $optRp=makeOption($dbname, 'kebun_5basispanen', 'jenis,rplebih',$dmn);
            $optDenda=makeOption($dbname, 'kebun_5denda', 'kode,jumlah');
            for($der=1;$der<8;$der++){
                
                if($der==1){
                    $det="BM";#buah mentah#
                    $dend=$_POST['isiDt'][$der]*$optDenda[$det]*$param['bjraktual']*$optRp[$param['tarif']];
                } else if($der==3){
                    $det="TD";#tidak dipanen#
                    $dend=$_POST['isiDt'][$der]*$optDenda[$det]*$param['bjraktual']*$optRp[$param['tarif']];
                    //exit("error:".$_POST['isiDt'][$der]." ".$optDenda[$det]."  ".$optRp[$param['tarif']]."  ".$param['bjraktual']);
                } else if($der==5){
                    $det="BT";#brondolan tidak di kutip#
                    $dend=$_POST['isiDt'][$der]/$optDenda[$det]*$param['bjraktual']*$optRp[$param['tarif']];
                    //exit("error:".$_POST['isiDt'][$der]." ".$optDenda[$det]."  ".$optRp[$param['tarif']]."  ".$param['bjraktual']);
                }else{
                    $det="TP";#tangkai panjang,pelepah tidak disusun,tandan menggantung#
                    $dend=$_POST['isiDt'][$der]*$optDenda[$det]*$param['bjraktual']*$optRp[$param['tarif']];
                }
                $denda+=$dend;
            }
            echo $denda;
        break;
}
?>