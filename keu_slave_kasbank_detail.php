<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
#include_once('lib/zGrid.php');
#include_once('lib/rGrid.php');
include_once('lib/formTable.php');
require_once('lib/tanaman.php');
?><?php

$proses = $_GET['proses'];
$param = $_POST;

function cekSupp($dbname,$noTrans) {
	$query = selectQuery($dbname,'keu_kasbankdt','kodesupplier',"notransaksi='".$noTrans."' and kodesupplier!=''");
	$res = fetchData($query);
	$optSupp = array();
	foreach($res as $row) {
		$optSupp[$row['kodesupplier']] = $row['kodesupplier'];
	}
	return $optSupp;
}

function cekUangMuka($dbname,$noakun) {
	$optParam = makeOption($dbname,'setup_parameterappl','kodeparameter,nilai',"kodeaplikasi='UM'");
	$stat = false;
	foreach($optParam as $nilai) {
		if($nilai==$noakun) {
			$stat = true;
		}
	}
	return $stat;
}

switch($proses) {
    case 'showDetail':
		$whereAKB = "kodeaplikasi='GL' and aktif=1 and jurnalid!= 'M'";
		$queryAKB = selectQuery($dbname,'keu_5parameterjurnal',
			'jurnalid,noakundebet,sampaidebet,noakunkredit,sampaikredit',$whereAKB);
		$optAKB = fetchData($queryAKB);
		$tipe = "";
		foreach($optAKB as $row) {
			if($param['tipetransaksi']=='K') {
			if($param['noakun']>=$row['noakunkredit'] and $param['noakun']<=$row['sampaikredit']) {
				$tipe = $row['jurnalid'];
			}
			} else {
			if($param['noakun']>=$row['noakundebet'] and $param['noakun']<=$row['sampaidebet']) {
				$tipe = $row['jurnalid'];
			}
			}
		}
        
        // Get Header
        $whereHead = "notransaksi='".
            $param['notransaksi']."' and kodeorg='".$param['kodeorg'].
            "' and noakun='".$param['noakun']."' and tipetransaksi='".
            $param['tipetransaksi']."'";
        $qHead = selectQuery($dbname,'keu_kasbankht','*',$whereHead);
        $resHead = fetchData($qHead);
        if(empty($resHead)) {
            $defMU = 'IDR';
            $defKurs = 1;
        } else {
            $defMU = $resHead[0]['matauang'];
            $defKurs = $resHead[0]['kurs'];
        }
		
		# Cek Kelompok Jurnal
		$whereKel = "kodeorg='".$_SESSION['org']['kodeorganisasi'].
			"' and kodekelompok='".$tipe."'";
		$optKel = makeOption($dbname,'keu_5kelompokjurnal','kodekelompok,keterangan',$whereKel);
		if(empty($optKel)) {
			echo "Warning : Journal Group  ".$tipe." not assign for your unit/Company\n";
			echo "Please contact  IT Dept.";
			exit;
		}
		
		# Options
			
		$whereJam=" detail=1 and noakun <> '".$param['noakun']."' and (pemilik='".$_SESSION['empl']['tipelokasitugas']."' or pemilik='GLOBAL' or pemilik='".$_SESSION['empl']['lokasitugas']."')";
			if($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
				$whereKary = "kodeorganisasi='".$_SESSION['empl']['kodeorganisasi']."' and tipekaryawan in ('0','1','2','3')";
			}else{
				$whereKary = "kodeorganisasi='".$_SESSION['empl']['kodeorganisasi']."'";
			}
                        
		#$whereAsset = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
		#$optAsset = makeOption($dbname,'sdm_daftarasset','kodeasset,namasset',$whereAsset,'2',true);
		#asset dialihkan ke aktiva dalam konstruksi
		$whereAsset = "kodeorg='".$_SESSION['empl']['lokasitugas']."' and posting=0";
		$optAsset = makeOption($dbname,'project','kode,nama',$whereAsset,'2',true);
		//$whereSup = " namasupplier asc";	
						$optMataUang = makeOption($dbname,'setup_matauang','kode,matauang');
						
		$optSupplier = makeOption($dbname,'log_5supplier','supplierid,namasupplier','status=1','0',true);
		
		
		$optCustomer = makeOption($dbname,'pmn_4customer','kodecustomer,namacustomer',null,'0',true);
						if($_SESSION['language']=='EN'){
							$optKegiatan = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan1,satuan,noakun',null,'6',true);
						}else{
								$optKegiatan = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan,satuan,noakun',null,'6',true);
						}
			if($_SESSION['language']=='EN'){
				$optAkun = makeOption($dbname,'keu_5akun','noakun,namaakun1',$whereJam,'2',true);
			}else{
				$optAkun = makeOption($dbname,'keu_5akun','noakun,namaakun',$whereJam,'2',true);
			}
		$optVhc = makeOption($dbname,'vhc_5master','kodevhc,kodeorg','','2',true);
			if($_SESSION['empl']['tipelokasitugas']=='KEBUN')
		$optOrgAl = makeOption($dbname,'setup_blok','kodeorg,kodeorg',"
						kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and luasareaproduktif!=0",'',true);  
			else
			$optOrgAl = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',"left(kodeorganisasi,4) in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$_SESSION['org']['kodeorganisasi']."')",'0',true);
	//	$optOrgAl = getOrgBelow($dbname,$param['kodeorg'],false,'blok',true);
	
		$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay',
			"tipe='Detail' and namalaporan='CASH FLOW DIRECT'",'2',true);
			
			
			$optHutangUnit = array('0'=>$_SESSION['lang']['no'],'1'=>$_SESSION['lang']['yes']);
		if($param['tipetransaksi']=='K') {
			$invTab = 'keu_tagihanht';
		} else {
			$invTab = 'keu_penagihanht';
		}
		$optInvoice = makeOption($dbname,$invTab,'noinvoice,noinvoice',
			"kodeorg='".$_SESSION['org']['kodeorganisasi']."'",'0',true);
		
		# Field Aktif
		$optField = makeOption($dbname,'keu_5akun','noakun,fieldaktif',
			"noakun='".end(array_reverse(array_keys($optAkun)))."'");
		$fieldAktif = '0000000';
		if(isset($optField[end(array_reverse(array_keys($optAkun)))])) {
			$fieldAktif = $optField[end(array_reverse(array_keys($optAkun)))];
		}
		
		# Get Data
		$where = "notransaksi='".$param['notransaksi'].
			"' and kodeorg='".$param['kodeorg'].
			"' and tipetransaksi='".$param['tipetransaksi'].
			"' and noakun2a='".$param['noakun']."'";
		$cols = "kode,keterangan1,noakun,noaruskas,matauang,kurs,keterangan2,jumlah,".
			"kodekegiatan,kodeasset,kodebarang,nik,kodecustomer,kodesupplier,kodevhc,orgalokasi,nodok,hutangunit1,notransaksi_adv";
		$query = selectQuery($dbname,'keu_kasbankdt',$cols,$where);
		$data = fetchData($query);
		$dataShow = $data;
		$totalJumlah = 0; // Init Total Jumlah
                $res2=mysql_query($query) or die(mysql_error());
                if (mysql_num_rows($res2)>0){
                    $optKary = makeOption($dbname,'datakaryawan','karyawanid,nik,namakaryawan','','5',true);
                } else {
                    $optKary = makeOption($dbname,'datakaryawan','karyawanid,nik,namakaryawan',$whereKary,'5',true);
                }
                
		$ptKary = makeOption($dbname,'datakaryawan','karyawanid,kodeorganisasi','','0',false);
		foreach($dataShow as $key=>$row) {
                        if ($row['nik']!='' && $ptKary[$row['nik']]==$_SESSION['empl']['kodeorganisasi']){
                            //exit('error:masuk');
                            $optKary = makeOption($dbname,'datakaryawan','karyawanid,nik,namakaryawan',$whereKary,'5',true);
                        }
			$dataShow[$key]['noakun'] = $optAkun[$row['noakun']];
			$dataShow[$key]['kode'] = $optKel[$row['kode']];
			$dataShow[$key]['nik'] = $optKary[$row['nik']];
			$dataShow[$key]['noaruskas'] = $optCashFlow[$row['noaruskas']];
			$dataShow[$key]['kodekegiatan'] = $optKegiatan[$row['kodekegiatan']];
			$dataShow[$key]['kodecustomer'] = $optCustomer[$row['kodecustomer']];
			$dataShow[$key]['kodesupplier'] = $optSupplier[$row['kodesupplier']];
			$dataShow[$key]['kodevhc'] = $optVhc[$row['kodevhc']];
			$dataShow[$key]['matauang'] = $optMataUang[$row['matauang']];
			$dataShow[$key]['noakun'] = $optAkun[$row['noakun']];
			$dataShow[$key]['orgalokasi'] = $optOrgAl[$row['orgalokasi']];
			$dataShow[$key]['hutangunit1'] = $optHutangUnit[$row['hutangunit1']];
			 $whr="notransaksi='".$dataShow[$key]['notransaksi_adv']."'";
                    $optNotr=makeOption($dbname,'keu_kasbankht','notransaksi,nobayar',$whr);
            if($dataShow[$key]['notransaksi_adv']!=''){
            	$dataShow[$key]['notransaksi_adv']=$optNotr[$dataShow[$key]['notransaksi_adv']];#mengisi row data dngan no bayar,jamhari perbaikan tgl 09-04-2015
            	$data[$key]['notransaksi_adv']=$dataShow[$key]['notransaksi_adv'];#mengisi row data dngan no bayar,jamhari perbaikan tgl 09-04-2015	
            }       
			// Count Total Jumlah
			$totalJumlah += $row['jumlah'];
		}

                # Form
		$theForm2 = new uForm('kasbankForm','Form Kas Bank',2);
		$theForm2->addEls('kode',$_SESSION['lang']['kode'],'','select','L',25,$optKel);
		$theForm2->addEls('keterangan1',$_SESSION['lang']['noinvoice'],'','text','L',25);
		$theForm2->_elements[1]->_attr['onclick'] = "searchNopo('".$_SESSION['lang']['find']." ".$_SESSION['lang']['noinvoice']." (".$_SESSION['lang']['matauang']." ".$resHead[0]['matauang'].")','<div id=formPencariandata></div>',event)";
		$theForm2->_elements[1]->_attr['readonly'] = true;
		$theForm2->_elements[1]->_attr['placeholder'] = "Click to search invoice";
		$theForm2->addEls('noakun',$_SESSION['lang']['noakun'],'','selectsearch','L',25,$optAkun);
		$theForm2->_elements[2]->_attr['onchange'] = 'updFieldAktif()';
		$theForm2->addEls('noaruskas',$_SESSION['lang']['noaruskas'],'','selectsearch','L',25,$optCashFlow);
		$theForm2->_elements[3]->_attr['disabled'] = 'disabled';
		
		$theForm2->addEls('matauang',$_SESSION['lang']['matauang'],$defMU,'select','L',25,$optMataUang);
//        $theForm2->_elements[4]->_attr['disabled'] = 'disabled';
		//$theForm2->_elements[4]->_attr['onchange'] = 'kurs()';
		
		$theForm2->addEls('kurs',$_SESSION['lang']['kurs'],$defKurs,'textnum','L',10);//ind
//        $theForm2->_elements[5]->_attr['readonly'] = true;
		
		$theForm2->addEls('keterangan2',$_SESSION['lang']['keterangan2'],'','text','L',40);

			$theForm2->addEls('jumlah',$_SESSION['lang']['jumlah'],$param['jumlahHeader']-$totalJumlah,'textnumw-','R',10);
		$theForm2->_elements[7]->_attr['onchange'] = 'this.value=remove_comma(this);this.value = _formatted(this)';
		$theForm2->addEls('kodekegiatan',$_SESSION['lang']['kodekegiatan'],'','selectsearch','L',35,$optKegiatan);
		if($fieldAktif[0]=='0') {
			$theForm2->_elements[8]->_attr['disabled'] = 'disabled';
		}
			
		$theForm2->addEls('kodeasset',$_SESSION['lang']['aktivadalam'],'','select','L',35,$optAsset);
		if($fieldAktif[1]=='0') {
			$theForm2->_elements[9]->_attr['disabled'] = 'disabled';
		}
		$theForm2->addEls('kodebarang',$_SESSION['lang']['kodebarang'],'','searchBarang','L',10);
		if($fieldAktif[2]=='0') {
			$theForm2->_elements[10]->_attr['disabled'] = 'disabled';
		}
		$theForm2->addEls('nik',$_SESSION['lang']['nik'],'','selectsearch','L',35,$optKary);
		if($fieldAktif[3]=='0') {
			$theForm2->_elements[11]->_attr['disabled'] = 'disabled';
		}
		$theForm2->addEls('kodecustomer',$_SESSION['lang']['kodecustomer'],'','select','L',35,$optCustomer);
		if($fieldAktif[4]=='0') {
			$theForm2->_elements[12]->_attr['disabled'] = 'disabled';
		}
		$theForm2->addEls('kodesupplier',$_SESSION['lang']['kodesupplier'],'','selectsearch','L',35,$optSupplier);
		if($fieldAktif[5]=='0') {
			$theForm2->_elements[13]->_attr['disabled'] = 'disabled';
		}
		$theForm2->addEls('kodevhc',$_SESSION['lang']['kodevhc'],'','selectsearch','L',35,$optVhc);
		if($fieldAktif[6]=='0') {
			$theForm2->_elements[14]->_attr['disabled'] = 'disabled';
		}
		$theForm2->addEls('orgalokasi',$_SESSION['lang']['kodeorg'],'','selectsearch','L',35,$optOrgAl);
                //$theForm2->_elements[15]->_attr['onchange'] = 'ambilDatakary()';
		$theForm2->addEls('nodok',$_SESSION['lang']['nodok'],'','text','L',35);
		$theForm2->_elements[16]->_attr['onclick'] = "searchDoc('".$_SESSION['lang']['find']."','<div id=formPencariandata2></div>',event)";
	//	$theForm2->addEls('hutangunit1',$_SESSION['lang']['hutangunit'],'','checkbox','L',35);
		$theForm2->addEls('hutangunit1',$_SESSION['lang']['hutangunit'],'','select','L',25,$optHutangUnit);

		// ..update notransaksi_adv 28/1/2015
		$theForm2->addEls('notransaksi_adv',$_SESSION['lang']['notransaksi']." Advance",'','text','L',35);
		$theForm2->_elements[18]->_attr['onclick'] = "searchAdv('".$_SESSION['lang']['find']."','<div id=formPencariandata3></div>',event)";
		
		

	//	$theForm2->_elements[17]->_attr['onclick'] = 'gantiValue(this)';
			
		# Table
		$theTable2 = new uTable('kasbankTable','Tabel Kas Bank',$cols,$data,$dataShow);
		
		# FormTable
		$formTab2 = new uFormTable('ftPrestasi',$theForm2,$theTable2,null,
			array('notransaksi','kodeorg','noakun2a','tipetransaksi','hutangunit'));
		$formTab2->_target = "keu_slave_kasbank_detail";
		$formTab2->_noClearField = '##keterangan1##nodok##jumlah'; // dz: buat nambahin exception yang ga di-clear
		$formTab2->_defValue = '##matauang='.$defMU.'##kurs='.$defKurs;
        $formTab2->_noEnable = '##matauang##kurs';
		$formTab2->_numberFormat = '##jumlah';
		$formTab2->_afterCrud = 'afterCrud';
		
		#== Display View
		# Draw Tab
		echo "<fieldset><legend><b>Detail</b></legend>";
                $tambah="<input type=checkbox id=allptnik onclick=allPtKaryawan('nik',this) title='Show All Employee in Plantations Group'>Show All Employee</checkbox>";
                $tambah.= $formTab2->prep();
                echo $tambah;
//		$formTab2->render();
		echo "</fieldset>";
		// $page = "<fieldset><legend><b>Detail</b></legend>";
		// $page = $formTab2->prep();
		// $page = "</fieldset>";
		
		// $arrRes = array(
			// 'page' => $page,
			// 'total' => $totalJumlah
		// );
		// return json_encode($arrRes); // Ganti Response Text jadi JSON
		break;
	
    case 'add':
		$data = $param;
                $data['jumlah']=round($param['jumlah'],2);
		$supp = cekSupp($dbname,$param['notransaksi']);
		
		if($data['matauang']!='IDR'){
			if(($data['kurs']=='')||intval($data['kurs'])==0){
				exit("error: Currency can't empty or zero");
			}
		}else{
			$data['kurs']=1;
		}
		if(cekUangMuka($dbname,$param['noakun'])) {
			if(empty($data['nodok'])) {
				exit("Warning: Nomor Dokumen harus diisi untuk akun Uang Muka");
			}
		}

		if(!empty($supp)) {
			if(!isset($supp[$param['kodesupplier']])) {
				exit("Warning: Supplier harus sama dengan supplier yang sudah ada di notransaksi ini");
			}
		}
		
		// Validasi Hutang Unit
		#tambahan ind 5 april 2014 : jika noakun 2111103 (accrue) 
		#maka warning no invoice di hilangkan karna mengakomodasi pembayaran po cash
		#tambahan cosa 18 agt 2014 : jika di header noakun 2111103 (accrue) 
		#maka warning pada no dokumen karna mengakomodasi pembayaran po cash
		$query = "select noakunhutang,matauang from ".$dbname.".keu_kasbankht where notransaksi='".$param['notransaksi']."'";
                $qaccrue = mysql_query($query);
                $raccrue=  mysql_fetch_assoc($qaccrue);
                if($raccrue['matauang']!=$data['matauang']){
                    exit("Warning: Mata uang tidak sama dengan header: ".$raccrue['matauang']);
                }
                if($raccrue['noakunhutang']=='2111103' and empty($param['nodok'])) {
                    exit("Warning: No Documen harus dipilih (diisikan No. PO)");          
                } else {
                    if($param['hutangunit']==1 and substr($param['noakun'],0,3)=='211') {
                        if(empty($param['keterangan1']) and $param['noakun']!='2111103' and $param['noakun']!='2111104') 
                        {
                                exit("Warning: No Invoice harus dipilih");
                        }
                    }

                    if($param['noakun']=='2111103')
                    {
                            if(empty($param['nodok'])) 
                            {
                                    exit("Warning: No Documen harus dipilih (diisikan No. PO)");
                            }
                    }
                }
//                } else {
//                    if(empty($param['nodok'])) 
//                    {
//                        exit("Warning: No Documen harus dipilih (diisikan No. PO");
//                    }
//                }
		
		
		$cols = array(
			'kode','keterangan1','noakun','noaruskas','matauang','kurs','keterangan2',
			'jumlah','kodekegiatan','kodeasset','kodebarang','nik','kodecustomer',
			'kodesupplier','kodevhc','orgalokasi','nodok','hutangunit1','notransaksi_adv','notransaksi','kodeorg','noakun2a','tipetransaksi'
		);
		unset($data['numRow']);
		unset($data['hutangunit']);

		//=====tambahan ginting
		#periksa apakah akun tanaman, dan jika akun tanaman maka harus ada kodeblok
			$blk=str_replace(" ","",$data['orgalokasi']);
			$nik=str_replace(" ","",$data['nik']);        
			$sup=str_replace(" ","",$data['kodesupplier']);
			$vhc=str_replace(" ","",$data['kodevhc']);             
			if(cekAkun($data['noakun']) and $blk=='')
			{
				exit("[ Error ]: Plant Account must comply with Block Code.");
			}else if(cekAkun($data['noakun']) and $data['kodekegiatan']==''){
				exit("[ Error ]: Activity is obligatory.");
			}else  if(cekAkunPiutang($data['noakun']) and $nik=='')
			{
				exit("[ Error ]: Employee ID is Obligatory to this Account.");
			}else if(cekAkunHutang($data['noakun']) and $sup=='')
			{
				exit("[ Error ]: Supplier Code is obligatory to this Account.");
			}else if(cekAkunTrans($data['noakun']) and $vhc=='')
			{
				exit("[ Error ]: Vehicle Code is obligatory to this account.");
			}
			//=====end tambahan ginting
			//
			//              
			# Additional Default Data
		$data['jumlah'] = str_replace(',','',$data['jumlah']);
                if (trim($data['notransaksi_adv'])!=''){
                	if(($data['notransaksi_adv']!=0)||(strlen($data['notransaksi_adv'])!=1)){
                		$whr="nobayar='".$data['notransaksi_adv']."'";
                    	$optNotr=makeOption($dbname,'keu_kasbankht','nobayar,notransaksi',$whr);
                    	$data['notransaksi_adv']=$optNotr[$data['notransaksi_adv']];	
                	}else{
                		$data['notransaksi_adv']='';
                	}
                }

		$query = insertQuery($dbname,'keu_kasbankdt',$data,$cols);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
//		if ($data['keterangan1']!=''){
//                    $tagihan = selectQuery($dbname,'keu_tagihanht',"*","noinvoice=".$data['keterangan1']);
//                    $dataTagihan = fetchData($tagihan);
//                    if ($dataTagihan[0]!=''){
//                        $query = updateQuery($dbname,'keu_tagihanht',array('nilaiinvoice'=>$data['jumlah']),"noinvoice='".$data['keterangan1']."' and tipeinvoice='k'");
//                                if(!mysql_query($query)) {
//                                echo "DB Error : ".mysql_error();
//                                exit;
//                        }
//                        
//                    }
//                }
		unset($data['notransaksi']);unset($data['kodeorg']);
		unset($data['noakun2a']);unset($data['tipetransaksi']);
		
		$res = "";
		foreach($data as $cont) {
			$res .= "##".$cont;
		}
		
		$result = "{res:\"".$res."\",theme:\"".$_SESSION['theme']."\"}";
		echo $result;
		break;
	
    case 'edit':
		$data = $param;
		if($data['matauang']!='IDR'){
			if(($data['kurs']=='')||intval($data['kurs'])==0){
				exit("error: Currency can't empty or zero");
			}
		}else{
			$data['kurs']=1;
		}
		if(cekUangMuka($dbname,$param['noakun'])) {
			if(empty($data['nodok'])) {
				exit("Warning: Nomor Dokumen harus diisi untuk akun Uang Muka");
			}
		}

		##############
        if($param['hutangunit']==1 or substr($param['noakun'],0,3)=='211') {
			
			if(empty($param['keterangan1']) and $param['noakun']!='2111103' and $param['noakun']!='2111104') 
			{
				exit("Warning: No Invoice harus dipilih");
			}
		}
		
		if($param['noakun']=='2111103')
		{
			if(empty($param['nodok'])) 
			{
				exit("Warning: No Documen harus dipilih (diisikan No. PO");
			}
		}
                #############3
                
		
		// Validasi Hutang Unit
		/*if($param['hutangunit']==1 or substr($param['noakun'],0,3)=='211' and $param['noakun']!='2111104') {
			if(empty($param['keterangan1'])) {
				exit("Warning: No Invoice harus dipilih");
			}
		}*/
		
		unset($data['notransaksi']);
		unset($data['hutangunit']);
		foreach($data as $key=>$cont) {
			if(substr($key,0,5)=='cond_') {
			unset($data[$key]);
			}
		}

		$data['jumlah'] = str_replace(',','',$data['jumlah']);
                if (!empty($data['notransaksi_adv'])){
                    $whr="nobayar='".$data['notransaksi_adv']."'";
                    $optNotr=makeOption($dbname,'keu_kasbankht','nobayar,notransaksi',$whr);//perbaikan jamhari, sebelumnya ada if di hapus
                    $data['notransaksi_adv']=$optNotr[$data['notransaksi_adv']];
                }
		$where = "notransaksi='".$param['notransaksi'].
			"' and noakun='".$param['cond_noakun'].
			"' and tipetransaksi='".$param['tipetransaksi'].
			"' and noakun2a='".$param['noakun2a'].
			"' and keterangan1='".$param['cond_keterangan1'].
			"' and keterangan2='".$param['cond_keterangan2'].
				"' and kodeorg='".$param['kodeorg']."'";
	//	$where = "notransaksi='".$param['notransaksi'].
	//	    "' and kodeorg='".$param['kodeorg'].
	//	    "' and noakun2a='".$param['noakun2a'].
	//	    "' and tipetransaksi='".$param['tipetransaksi'].
	//	    "' and noakun='".$param['cond_noakun'].
	//	    "' and keterangan2='".$param['cond_keterangan2']."'";
		$query = updateQuery($dbname,'keu_kasbankdt',$data,$where);
			if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error()."____".$query;
			exit;
		}
	//        echo "warning: ".$query;
	//        echo "warning:<pre>";
	//        print_r($param);
	//        echo "</pre>";
	//        exit;
		echo json_encode($param);
		break;
    case 'delete':
		$where = "notransaksi='".$param['notransaksi'].
			"' and kodeorg='".$param['kodeorg'].
			"' and noakun='".$param['noakun'].
			"' and noakun2a='".$param['noakun2a'].
			"' and tipetransaksi='".$param['tipetransaksi'].
			"' and keterangan1='".$param['keterangan1']."'
				 and keterangan2='".$param['keterangan2']."'";
		$query = "delete from `".$dbname."`.`keu_kasbankdt` where ".$where;
			if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		break;
    case 'updField':
//            if ($param['nodok']!=''){
//		$query = "select * from ".$dbname.".log_poht where nopo='".$param['nodok']."'";
//                $res2=fetchData($query);
//                $result=$res2[0]['kodesupplier']."##".$res2[0]['purchaser']."##";
//            }
            
            $optField = makeOption($dbname,'keu_5akun','noakun,fieldaktif',
                    "noakun='".$param['noakun']."'");
            $result.=$optField[$param['noakun']];
            
            echo $result;
            break;
	
    case'getForminvoice':
        $form="<fieldset style=float: left;>
				<legend>".$_SESSION['lang']['find']." ".$_SESSION['lang']['noinvoice']." (".$_SESSION['lang']['matauang']." ".$_POST['matauang'].")</legend>
					".$_SESSION['lang']['tahun']."&nbsp;<input type=text class=myinputtext size=5 id=no_brg value=".date('Y').">&nbsp;
					".$_SESSION['lang']['namasupplier']."&nbsp;<input id=supplierIdcr onkeypress=\"return validatNodok(event);\" style=width:150px>&nbsp;
					".$_SESSION['lang']['nopo']."&nbsp;<input id=nopocr onkeypress=\"return validatNodok(event);\" style=width:150px>&nbsp;
					<button class=mybutton onclick=findNoinvoice()>Find</button></fieldset><div id=container2><fieldset><legend>".$_SESSION['lang']['result']."</legend></fieldset></div>";
        echo $form;
    break;
	case'getForm':
	$arrTipe=array("PO"=>"PO","SPK"=>"Kontrak","KL"=>"Claim Pengobatan");
	foreach($arrTipe as $rwTp =>$dtTipe){
		$optTipe.="<option value='".$rwTp."'>".$dtTipe."</option>";
	}
        $form="<fieldset style=float: left;>
				<legend>".$_SESSION['lang']['find']."</legend>
				<input type=checkbox id=allPO value=false>".$_SESSION['lang']['POallPT']."</checkbox><br>
					".$_SESSION['lang']['find']."<input type=text onkeypress=\"return validatNodok2(event);\" class=myinputtext id=txtCari value=''>&nbsp;
					".$_SESSION['lang']['tipe']."<select id=tipeDt style=width:150px>".$optTipe."</select>
					<button class=mybutton onclick=findNoinvoice2()>Find</button>
			</fieldset>
			<div id=container2>
				<fieldset>
					<legend>".$_SESSION['lang']['result']."</legend>
				</fieldset>
			</div>";
        echo $form;
    break;
    case'getInvoice':
        $arrTipe=array("p"=>$_SESSION['lang']['pesananpembelian'],"k"=>$_SESSION['lang']['kontrak']);
        $dat="<fieldset><legend>".$_SESSION['lang']['result']."</legend>";
        $dat.="<div style=overflow:auto;width:100%;height:500px;>";
        $dat.="<table cellpadding=1 cellspacing=1 border=0 class='sortable'><thead>";
        $dat.="<tr class='rowheader'><td>No.</td>";
        $dat.="<td>".$_SESSION['lang']['noinvoice']."</td>";
        $dat.="<td>".$_SESSION['lang']['nopo']."</td>";
		$dat.="<td>".$_SESSION['lang']['novp']."</td>";
        $dat.="<td>".$_SESSION['lang']['namasupplier']."</td>";
        $dat.="<td>".$_SESSION['lang']['tipeinvoice']."</td>";
        $dat.="<td>".$_SESSION['lang']['nilaiinvoice']."</td>";
        $dat.="<td>".$_SESSION['lang']['nilaippn']."</td>";
        $dat.="<td>".$_SESSION['lang']['noakun']."</td>";
        $dat.="</tr></thead><tbody>";
        
        $str="select distinct noinvoice from ".$dbname.".aging_sch_vw  "
           . "where (((dibayar<nilaipo)or(dibayar<nilaikontrak)or(dibayar<nilaiinvoice))or(dibayar is null or dibayar=0)) and kodeorg='".$_SESSION['org']['kodeorganisasi']."' and noinvoice like '".$param['txtfind']."%'";
//echo "error:".$str;		
        $qstr=mysql_query($str) or die(mysql_error($conn));
        while($rstr=mysql_fetch_assoc($qstr))
        {
            $belumlunas[$rstr['noinvoice']]=$rstr['noinvoice'];
        }
        
//        print_r($belumlunas);
        
//        if($param['txtfind']!='')
//        {
//            $whereCr=" and noinvoice like '".$param['txtfind']."%'";
//        }
//        else
//        {
////            $whereCr=" and noinvoice in (select distinct noinvoice from ".$dbname.".aging_sch_vw where (dibayar is null or dibayar=0) and kodeorg='".$_SESSION['org']['kodeorganisasi']."')";
//            $whereCr=" and noinvoice in ()";
//        } 
//        if($param['idSupplier']!='')
//        {
//            $whereCr.=" and kodesupplier='".$param['idSupplier']."'";
//        }
//        
//        $sPo="select distinct kodesupplier,noinvoice,nopo,tipeinvoice,nilaiinvoice,nilaippn,noakun,keterangan from ".$dbname.".keu_tagihanht where kodeorg='".$_SESSION['org']['kodeorganisasi']."' ".$whereCr." order by tanggal asc";
 
 		
		if(!isset($param['idSupplier']) or $param['idSupplier']=='')
		{
			$kdsup=" ";
		}
		else
		{
			$kdsup=" and c.namasupplier like '%".$param['idSupplier']."%'  ";
		}
		
		if($param['nopocr']!='')
		{
			$nopocr="and a.nopo like '%".$param['nopocr']."%' ";
		}
		else
		{
		}
				
        $sPo="select distinct kodesupplier,noinvoice,nopo,tipeinvoice,nilaiinvoice,nilaippn,a.noakun,keterangan,posting,b.namakaryawan,c.namasupplier
            from ".$dbname.".keu_tagihanht a left join ".$dbname.".datakaryawan b on a.postingby=b.karyawanid
			left join ".$dbname.".log_5supplier c on a.kodesupplier=c.supplierid
            where kodeorg='".$_SESSION['org']['kodeorganisasi']."' and noinvoice like '".$param['txtfind']."%' ".$kdsup." ".$nopocr."  order by tanggal asc";
       //echo $sPo;
        $qPo=mysql_query($sPo) or die(mysql_error($conn));
		$no=0;
        while($rPo=mysql_fetch_assoc($qPo)){
//            echo $rPo['noinvoice'];
            if($rPo['noinvoice']==$belumlunas[$rPo['noinvoice']]){
            $sJmlh="select distinct sum(jumlah) as jmlhKas from ".$dbname.".keu_kasbankdt where keterangan1='".$rPo['noinvoice']."'";
            //echo $sJmlh;
            $qJmlh=mysql_query($sJmlh) or die(msyql_error($conn));
            $rJmlh=mysql_fetch_assoc($qJmlh);
            $sCek="select distinct sum(nilaiinvoice+nilaippn) as jmlhinvoice from ".$dbname.".keu_tagihanht where  noinvoice='".$rPo['noinvoice']."'";
            $qCek=mysql_query($sCek) or die(mysql_error($conn));
            $rCek=mysql_fetch_assoc($qCek);
            // Cek VP
            $qVp = "select a.noinv,b.posting,b.novp from ".$dbname.".keu_vp_inv a left join ".$dbname.
                    ".keu_vpht b on a.novp=b.novp where a.noinv='".$rPo['noinvoice']."' and b.posting=1";
            $cekVp = fetchData($qVp);
			
            $no+=1;
            if($rPo['posting']==0 or empty($cekVp)){
				if(empty($cekVp)) {
					$dat.="<tr class='rowcontent' title='".$_SESSION['lang']['vpnotexist2']."'><td>".$no."</td>";
				} else {
					$dat.="<tr class='rowcontent' title='".$_SESSION['lang']['vpnotexist'].$rPo['namakaryawan']."'><td>".$no."</td>";
				}
				$dat.="<td style='background-color:red;'>".$rPo['noinvoice']."</td>"; 
            } else { 
				if($rJmlh['jmlhKas']>=$rCek['jmlhinvoice'])
				{
                                    $cekKas="select * from ".$dbname.".keu_kasbankdt where keterangan1='".$rPo['noinvoice']."'";
                                    $qcekKas=mysql_query($cekKas) or die(msyql_error($conn));
                                    $rcekKas=mysql_fetch_assoc($qcekKas);
                    $dat.="<tr class='rowcontent' title='".$_SESSION['lang']['vpexistonkas'].$rcekKas['notransaksi']."'><td>".$no."</td>"; 
                    $dat.="<td style='background-color:red;'>".$rPo['noinvoice']."</td>"; 
				} else {
					$sakun="select noakun,kurs,matauang,jumlah from ".$dbname.".keu_vpdt a left join ".$dbname.".keu_vp_inv b on a.novp=b.novp "
                        . "where b.noinv='".$rPo['noinvoice']."' and left(noakun,3)='211' and noakun<>'2111103'";
					$qakun=  mysql_query($sakun) or die(mysql_error($conn));
					$rakun=  mysql_fetch_assoc($qakun);
					
					$sakun2="select noakun,kurs,matauang,jumlah from ".$dbname.".keu_vpdt a left join ".$dbname.".keu_vp_inv b on a.novp=b.novp "
                        . "where b.noinv='".$rPo['noinvoice']."' and left(noakun,3)='116' and noakun<>'2111103'";
					$qakun2=  mysql_query($sakun2) or die(mysql_error($conn));
					$rakun2=  mysql_fetch_assoc($qakun2);
    
                                        
#di non aktifkan di karenakan 
/*if($rakun2['noakun']!=''){
        if($rakun['matauang']!='IDR'){
                    $rCek['jmlhinvoice']=$rakun['kurs']*$rCek['jmlhinvoice'];
                    $rakun['matauang']='IDR';
                    $rakun['kurs']='1';
        }
}*/
//                    if($rakun2['noakun']!=''){
//                            if($param['matauang']=='IDR'){
//                                        $rCek['jmlhinvoice']=$rakun['kurs']*$rCek['jmlhinvoice'];
//                                        $rakun['matauang']='IDR';
//                                        $rakun['kurs']='1';
//                            }
//		    }
                    $whrmt="notransaksi='".$param['notransaksi']."'";                    
                    $optMtuang=  makeOption($dbname, 'keu_kasbankht', 'notransaksi,matauang',$whrmt);
                    if($optMtuang[$param['notransaksi']]=='IDR'){
                        if($rPo['tipeinvoice']=='k'){
                            $uangMuka=0;
                            $nilPPn=0;
                            $nilPph=0;
                            $der="2111103";
                            $iCek="select jumlah,noakun,kurs from ".$dbname.".keu_vpdt a left join ".$dbname.".keu_vp_inv b on a.novp=b.novp "
                                . " where (left(noakun,3)='212' or noakun='2111103' or noakun='1160100' or left(noakun,3)='118') and b.noinv='".$rPo['noinvoice']."'  ";
                            //exit("error:".$iCek);
                            //echo $iCek;
                            $nCek=mysql_query($iCek) or die (mysql_error($conn));
                            while($dCek=mysql_fetch_assoc($nCek)){
                                if(substr($dCek['noakun'],0,3)=='212'){
                                    $nilPph+=(($dCek['jumlah']*-1)*$dCek['kurs']);
                                }else if(substr($dCek['noakun'],0,3)=='118'){
                                    $uangMuka+=(($dCek['jumlah']*-1)*$dCek['kurs']);
                                }else if($dCek['noakun']=='1160100'){
                                    $nilPPn+=($dCek['jumlah']*$dCek['kurs']);
                                }else{
                                    $nilKon[$der]=($dCek['jumlah']*$dCek['kurs']);
                                }
                                
                            }
                            $persn=($nilPph/$nilKon[$der])*100;
                            $rCek['jmlhinvoice']=((($rPo['nilaiinvoice']+$nilPPn)-$uangMuka)-($rPo['nilaiinvoice']*$persn/100));
                            //$rCek['jmlhinvoice']=((($rPo['nilaiinvoice']+$nilPPn)-$uangMuka)-(($rPo['nilaiinvoice']-$uangMuka)*round($persn)/100)); //tolong konfirmasi perhitungan pph
                            //$rCek['jmlhinvoice']=($rPo['nilaiinvoice']+-$uangMuka)-((($rPo['nilaiinvoice']+$nilPPn-$uangMuka)*round($persn))/100);
                            //$rCek['jmlhinvoice']=($rPo['nilaiinvoice']-$uangMuka)-(($rPo['nilaiinvoice']*round($persn))/100);
                            //$rCek['jmlhinvoice']=$rPo['nilaiinvoice']-(($rPo['nilaiinvoice']*round($persn))/100);
                            //$nilPph=0;
                            //$persn=0;
                            //$uangMuka=0;
                            //$nilPPn=0;
                        }elseif($rPo['tipeinvoice']=='p' or $rPo['tipeinvoice']=='o'){
                            // Untuk PO yang memiliki PPh harusnya nilainya dikurangi nilai PPh nya
                            $pph23="2120200"; // 2120200 Hutang PPh Ps.23
                            $nilPph=0;
                            $iCek="select jumlah,noakun,kurs from ".$dbname.".keu_vpdt a left join ".$dbname.".keu_vp_inv b on a.novp=b.novp "
                                . " where noakun='".$pph23."' and b.noinv='".$rPo['noinvoice']."'";
                            $nCek=mysql_query($iCek) or die (mysql_error($conn));
                            while($dCek=mysql_fetch_assoc($nCek)){
                                $nilPph+=(($dCek['jumlah']*-1)*$dCek['kurs']);
                            }
                            $rCek['jmlhinvoice']=($rakun['kurs']*$rCek['jmlhinvoice'])-$nilPph;
                        } else {
                            $rCek['jmlhinvoice']=$rakun['kurs']*$rCek['jmlhinvoice'];
                        }
                            $rakun['matauang']='IDR';
                            $rakun['kurs']='1';
                            $dat.="<tr class='rowcontent' onclick=\"setPo('".$rPo['noinvoice']."','".$rCek['jmlhinvoice']."','".$rakun['noakun']."','".$rPo['keterangan']."','".$rPo['kodesupplier']."','".$rPo['nopo']."','".$rakun['matauang']."','".$rakun['kurs']."')\" style='pointer:cursor;'><td>".$no."</td>";
			    $dat.="<td>".$rPo['noinvoice']."</td>";
                            $rPo['nilaiinvoice']=$rCek['jmlhinvoice'];
                    }
                    //exit("error:".$optMtuang[$param['notransaksi']]);
                    if($optMtuang[$param['notransaksi']]!='IDR'){
                        if($rakun['matauang']!=$optMtuang[$param['notransaksi']]){
                            continue;
//                            $dat.="<tr class='rowcontent' title='".$rakun['matauang']."'><td>".$no."</td>";
//                            $dat.="<td style='background-color:red;'>".$rPo['noinvoice']."</td>";
                        }else{
                            $rCek['jmlhinvoice']=$rPo['nilaiinvoice'];
                            $dat.="<tr class='rowcontent' onclick=\"setPo('".$rPo['noinvoice']."','".$rCek['jmlhinvoice']."','".$rakun['noakun']."','".$rPo['keterangan']."','".$rPo['kodesupplier']."','".$rPo['nopo']."','".$rakun['matauang']."','".$rakun['kurs']."')\" style='pointer:cursor;'><td>".$no."</td>";
			    $dat.="<td>".$rPo['noinvoice']."</td>";
                        }
                    }
                    /*if($rakun['matauang']!='USD'){
                            $rCek['jmlhinvoice']=$rakun['kurs']*$rCek['jmlhinvoice'];
                            $rakun['matauang']='IDR';
                            $rakun['kurs']='1';
                    }else{
                            if($rCek['jmlhinvoice']<=100){
                                    $rCek['jmlhinvoice']=$rakun['kurs']*$rCek['jmlhinvoice'];
                                    $rakun['matauang']='IDR';
                                    $rakun['kurs']='1';
                            }
                    }*/
                                        
					
				}
            }
            
            $dat.="<td>".$rPo['nopo']."</td>";
            if(!empty($cekVp)) {
				$dat.="<td>".$cekVp[0]['novp']."</td>";
			} else {
				$dat.="<td></td>";
			}
			$dat.="<td>".$rPo['namasupplier']."</td>";
			$dat.="<td>".$arrTipe[$rPo['tipeinvoice']]."</td>";
            $dat.="<td style='text-align:right'>".number_format($rPo['nilaiinvoice'],2)."</td>";
            $dat.="<td style='text-align:right'>".number_format($rPo['nilaippn'],2)."</td>";
            $dat.="<td>".$rPo['noakun']." </td></tr>";
        }    
        }// while
        $dat.="</tbody></table></div>#Status S atau K, refer To S=Supplier,K=Contractor</fieldset>";
        echo $dat;
    break;

    // ..update crud notransaksi_adv
    case'getFormAdv':
    	# code...
		$formAdv="<fieldset style=float: left;>
					<legend>".$_SESSION['lang']['find']." </legend>
					".$_SESSION['lang']['find']." ".$_SESSION['lang']['nobayar']."<input type=text onkeypress=\"return validatAdv(event);\" class=myinputtext id=txtCari value=''>&nbsp;
					<button class=mybutton onclick=findNoAdv()>Find</button>
				</fieldset>
					<fieldset style=float:left;>
						<legend>".$_SESSION['lang']['result']."</legend>
						<div id=container2>
						</div>
					</fieldset>
				";
	        echo $formAdv;
    	break;

    // ..update notransaksi_adv
    case 'getAdv':
    	# code...
    	$adv.="<div style=overflow:auto;width:560px;height:300px;><table class=sortable border=0 cellpadding=1 cellspacing=1>";
		$adv.="<thead><tr>";
		$adv.="<td>".$_SESSION['lang']['nik']."</td>";
		$adv.="<td>".$_SESSION['lang']['nama']."</td>";
		$adv.="<td>".$_SESSION['lang']['nobayar']."</td>";
		$adv.="<td>".$_SESSION['lang']['tanggal']."</td>"; 
		$adv.="<td>Uang Muka</td>"; 
		$adv.="<td align=center>Dipertanggungjawabkan<br>(Rp.)</td>"; 
		$adv.="</tr></thead><tbody>";

			// ..query
			$whrnik = "";
			if ($param['nik']!='') {
				$whrnik= " and c.nik='".$param['nik']."'";
				# code...
			}
			$sTr = "select c.notransaksi_adv,sum(c.jumlah) as jmlhbyr 
					from ".$dbname.".keu_kasbankht a 
					left join ".$dbname.".keu_kasbankdt c on a.notransaksi=c.notransaksi 
					left join ".$dbname.".datakaryawan b on c.nik=b.karyawanid 
					where a.posting=1 and a.kodeorg='".$_SESSION['empl']['lokasitugas']."' 
					and c.noakun='".$param['noakun']."' 
					".$whrnik."
					and (notransaksi_adv is not null and notransaksi_adv!='') 
					group by notransaksi_adv";
			//echo $sTr;
			$qTr = mysql_query($sTr) or die(mysql_error());
			while ($rTr = mysql_fetch_assoc($qTr)) {
				# code...
				$ArrJum[$rTr['notransaksi_adv']] = $rTr['jumlah'];
			}



			$sAdv = "select a.tanggalposting,a.nobayar,b.nik,b.namakaryawan,c.noakun,c.jumlah,a.notransaksi,b.karyawanid
					from ".$dbname.".keu_kasbankht a 
					left join ".$dbname.".keu_kasbankdt c on a.notransaksi=c.notransaksi
					left join ".$dbname.".datakaryawan b on c.nik=b.karyawanid 
					where a.posting=1 
					".$whrnik."
					and a.kodeorg='".$_SESSION['empl']['lokasitugas']."' 
					and a.nobayar like '%".$param['nobayar']."%'
					and c.noakun='".$param['noakun']."' and notransaksi_adv is null
					";
			//echo $sAdv;
			$qAdv = mysql_query($sAdv) or die(mysql_error());
			while ($rAdv = mysql_fetch_assoc($qAdv)) {
				# code...
				$JPlus = $ArrJum[$rAdv['notransaksi']]-$rAdv['jumlah'];
				if ($JPlus==0) {
					# code...
					continue;
				}
				$adv.="<tr class=rowcontent style=cursor:pointer; onclick=setNobayar('".$rAdv['nobayar']."','".$rAdv['karyawanid']."')>";
					$adv.="<td>".$rAdv['nik']."</td>";
					$adv.="<td>".$rAdv['namakaryawan']."</td>";
					$adv.="<td>".$rAdv['nobayar']."</td>";
					$adv.="<td nowrap>".tanggalnormal($rAdv['tanggalposting'])."</td>";
					$adv.="<td align=right>".number_format($rAdv['jumlah'],2)."</td>"; // ..posisi right dan number format
					$adv.="<td align=right>".number_format($JPlus,2)."</td>";  
				$adv.="</tr>";
			}
			$adv.="</tbody></table></div>";
		echo $adv;
    	break;

	case'getInvoice2':
		$tab.="<div style=overflow:auto;width:700px;height:300px;><table class=sortable border=0 cellpadding=1 cellspacing=1>";
		$tab.="<thead><tr>";
		$tab.="<td>No.</td>";
		$tab.="<td>No. Dokumen</td>
			   <td align=right>".$_SESSION['lang']['nilai']."</td>
			   <td align=right>".$_SESSION['lang']['ppn']."</td>
			   <td>".$_SESSION['lang']['matauang']."</td>
			   <td>".$_SESSION['lang']['karyawan']."/<br>".$_SESSION['lang']['purchaser']."</td>
			   <td>".$_SESSION['lang']['supplier']."</td>
		       </tr><thead><tbody>";
		switch($param['tipeDt']){
			case'PO':
			$tipePo=" and lokalpusat=0";
                        if ($param['allPO']=='false'){
                            if($_SESSION['empl']['tipelokasitugas']!='HOLDING'){
                                    $tipePo=" and lokalpusat=1";
                            }
                                    $sData="select distinct nopo as nodokumen,kodesupplier,purchaser as userdata,subtotal-nilaidiskon as nilairupiah,0 as nilaippn,matauang,kurs,namasupplier,namakaryawan from ".$dbname.".log_poht a
                                            left join ".$dbname.".log_5supplier b on a.kodesupplier=b.supplierid 
                                            left join ".$dbname.".datakaryawan c on purchaser=c.karyawanid
                                            where statusbayar='CASH' and kodeorg='".$_SESSION['org']['kodeorganisasi']."' 
                                                    and nopo like '%".$param['txtfind']."%' ".$tipePo." UNION ".
                                            "select distinct nopo as nodokumen,kodesupplier,purchaser as userdata,0 as nilairupiah,ppn as nilaippn,matauang,kurs,namasupplier,namakaryawan from ".$dbname.".log_poht a
                                            left join ".$dbname.".log_5supplier b on a.kodesupplier=b.supplierid 
                                            left join ".$dbname.".datakaryawan c on purchaser=c.karyawanid
                                            where statusbayar='CASH' and kodeorg='".$_SESSION['org']['kodeorganisasi']."' and ppn>0 
                                                    and nopo like '%".$param['txtfind']."%' ".$tipePo;
                        } else {
                            if($_SESSION['empl']['tipelokasitugas']!='HOLDING'){
                                    $tipePo=" and lokalpusat=1";
                            }
                                    $sData="select distinct nopo as nodokumen,kodesupplier,purchaser as userdata,subtotal-nilaidiskon as nilairupiah,0 as nilaippn,matauang,kurs,namasupplier,namakaryawan from ".$dbname.".log_poht a
                                            left join ".$dbname.".log_5supplier b on a.kodesupplier=b.supplierid 
                                            left join ".$dbname.".datakaryawan c on purchaser=c.karyawanid
                                            where statusbayar='CASH' and nopo like '%".$param['txtfind']."%' ".$tipePo." UNION ".
                                            "select distinct nopo as nodokumen,kodesupplier,purchaser as userdata,0 as nilairupiah,ppn as nilaippn,matauang,kurs,namasupplier,namakaryawan from ".$dbname.".log_poht a
                                            left join ".$dbname.".log_5supplier b on a.kodesupplier=b.supplierid 
                                            left join ".$dbname.".datakaryawan c on purchaser=c.karyawanid
                                            where statusbayar='CASH' and kodeorg='".$_SESSION['org']['kodeorganisasi']."' and ppn>0 
                                                    and nopo like '%".$param['txtfind']."%' ".$tipePo;
                        }
			break;
			case'KL':
                        if ($param['allPO']=='false'){
				$sData="select distinct notransaksi as nodokumen,'' as kodesupplier,a.karyawanid as userdata,jlhbayar as nilairupiah,0 as nilaippn,namakaryawan from ".$dbname.".sdm_pengobatanht a
				        left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
				        where tipe='KARYAWAN' and kodeorg='".$_SESSION['empl']['lokasitugas']."' 
						and notransaksi like '%".$param['txtfind']."%' ";
                        } else {
				$sData="select distinct notransaksi as nodokumen,'' as kodesupplier,a.karyawanid as userdata,jlhbayar as nilairupiah,0 as nilaippn,namakaryawan from ".$dbname.".sdm_pengobatanht a
				        left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
				        where tipe='KARYAWAN' and notransaksi like '%".$param['txtfind']."%' ";
                        }
			break;
			case'SPK':
                        if ($param['allPO']=='false'){
				$sData="select distinct notransaksi as nodokumen,koderekanan as kodesupplier,'' as userdata,nilaikontrak as nilairupiah,0 as nilaippn,namasupplier from ".$dbname.".log_spkht a 
				        left join ".$dbname.".organisasi b on a.kodeorg=b.kodeorganisasi 
				        left join ".$dbname.".keu_tagihanht c on a.notransaksi=c.nopo
                                        left join ".$dbname.".log_5supplier d on koderekanan=d.supplierid 
				        where c.nopo IS NULL and b.induk='".$_SESSION['empl']['kodeorganisasi']."' 
						and notransaksi like '%".$param['txtfind']."%' ";
                        } else {
				$sData="select distinct notransaksi as nodokumen,koderekanan as kodesupplier,'' as userdata,nilaikontrak as nilairupiah,0 as nilaippn,namasupplier from ".$dbname.".log_spkht a 
				        left join ".$dbname.".organisasi b on a.kodeorg=b.kodeorganisasi 
				        left join ".$dbname.".keu_tagihanht c on a.notransaksi=c.nopo
                                        left join ".$dbname.".log_5supplier d on koderekanan=d.supplierid 
				        where c.nopo IS NULL and notransaksi like '%".$param['txtfind']."%' ";
                        }
			break;
		}
                //echo $sData;
		$qData=mysql_query($sData) or die(mysql_error($conn));
		while($rData=mysql_fetch_assoc($qData)){
			$no+=1;
			if($rData['matauang']!=''){
				if($rData['matauang']!='USD'){
					$rData['nilairupiah']=$rData['nilairupiah']*$rData['kurs'];
					$rData['nilaippn']=$rData['nilaippn']*$rData['kurs'];
                                        $rData['matauang']='IDR';
                                        $rData['kurs']='1';
				} else {
                                    if ($rData['matauang']=='USD' && $rData['nilaippn']>0){
					$rData['nilaippn']=$rData['nilaippn']*$rData['kurs'];
                                        $rData['matauang']='IDR';
                                        $rData['kurs']='1';
                                    }
                                }
			}else{
				$rData['matauang']='IDR';
				$rData['kurs']='1';
			}
                        $purchaser=$rData['userdata'];
                        if (strlen(trim($purchaser))<10){
                            $purchaser=substr("0000000000".$purchaser,-10);
                        }
                        $nodok=str_replace(" ","_", $rData['nodokumen']);
			$tab.="<tr class=rowcontent style='cursor:pointer' onclick=setData('".$nodok."','".$rData['kodesupplier']."','".$purchaser."','".$rData['nilairupiah']."','".$rData['nilaippn']."','".$rData['matauang']."','".$rData['kurs']."','".$param['tipeDt']."')>";
			$tab.="<td>".$no."</td>";
			$tab.="<td>".$rData['nodokumen']."</td>";
			$tab.="<td align=right>".number_format($rData['nilairupiah'],2)."</td>";
			$tab.="<td align=right>".number_format($rData['nilaippn'],2)."</td>";
			$tab.="<td>".$rData['matauang']."</td>";
			$tab.="<td>".$rData['namakaryawan']."</td>";
			$tab.="<td>".$rData['namasupplier']."</td>";
			$tab.="</tr>";
		}
		$tab.="</tbody></table></div>";
		echo $tab;
	break;
	case 'getAllPt':
                if($param['tipe']=='all') {
                    if($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
                            $whereKary = "tipekaryawan in ('0','1','2','3')";
                    }else{
                            $whereKary = "lokasitugas not like '%HO' and lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";
                    }
                } else {
                    if($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
                            $whereKary = "kodeorganisasi='".$_SESSION['empl']['kodeorganisasi']."' and tipekaryawan in ('0','1','2','3')";
                    }else{
                            $whereKary = "kodeorganisasi='".$_SESSION['empl']['kodeorganisasi']."'";
                    }
                }   

                $str="select karyawanid,nik,namakaryawan,kodeorganisasi from ".$dbname.".datakaryawan where ".$whereKary." order by namakaryawan";
                $res=mysql_query($str);
                while($bar=mysql_fetch_object($res))
                {
                    echo"<option value='".$bar->karyawanid."'>".$bar->nik." - ".$bar->namakaryawan." (".$bar->kodeorganisasi.")</option>";
                }
//                $optKary = makeOption($dbname,'datakaryawan','karyawanid,nik,namakaryawan',$whereKary,'5',true);
		break;
	default:
	break;
    default:
	break;
}
?>