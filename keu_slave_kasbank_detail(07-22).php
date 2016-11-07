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
				$whereKary = "kodeorganisasi='".$_SESSION['empl']['kodeorganisasi']."' and (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].") and tipekaryawan in ('0','1','2')";
			}else{
				$whereKary = "kodeorganisasi='".$_SESSION['empl']['kodeorganisasi']."' and (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].")";
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
			"kodekegiatan,kodeasset,kodebarang,nik,kodecustomer,kodesupplier,kodevhc,orgalokasi,nodok,hutangunit1";
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
			
			// Count Total Jumlah
			$totalJumlah += $row['jumlah'];
		}

                # Form
		$theForm2 = new uForm('kasbankForm','Form Kas Bank',2);
		$theForm2->addEls('kode',$_SESSION['lang']['kode'],'','select','L',25,$optKel);
		$theForm2->addEls('keterangan1',$_SESSION['lang']['noinvoice'],'','text','L',25);
		$theForm2->_elements[1]->_attr['onclick'] = "searchNopo('".$_SESSION['lang']['find']." ".$_SESSION['lang']['noinvoice']."','<div id=formPencariandata></div>',event)";
		$theForm2->_elements[1]->_attr['readonly'] = true;
		$theForm2->_elements[1]->_attr['placeholder'] = "Click to search invoice";
		$theForm2->addEls('noakun',$_SESSION['lang']['noakun'],'','selectsearch','L',25,$optAkun);
		$theForm2->_elements[2]->_attr['onchange'] = 'updFieldAktif()';
		$theForm2->addEls('noaruskas',$_SESSION['lang']['noaruskas'],'','selectsearch','L',25,$optCashFlow);
		$theForm2->_elements[3]->_attr['disabled'] = 'disabled';
		
		$theForm2->addEls('matauang',$_SESSION['lang']['matauang'],$defMU,'select','L',25,$optMataUang);
        $theForm2->_elements[4]->_attr['disabled'] = 'disabled';
		//$theForm2->_elements[4]->_attr['onchange'] = 'kurs()';
		
		$theForm2->addEls('kurs',$_SESSION['lang']['kurs'],$defKurs,'textnum','L',10);//ind
        $theForm2->_elements[5]->_attr['readonly'] = true;
		
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
		
		$cols = array(
			'kode','keterangan1','noakun','noaruskas','matauang','kurs','keterangan2',
			'jumlah','kodekegiatan','kodeasset','kodebarang','nik','kodecustomer',
			'kodesupplier','kodevhc','orgalokasi','nodok','hutangunit1','notransaksi','kodeorg','noakun2a','tipetransaksi'
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
				exit("[ Error ]: Vehicle Code is obligatory to this accout.");
			}
			//=====end tambahan ginting
			//
			//              
			# Additional Default Data
		$data['jumlah'] = str_replace(',','',$data['jumlah']);
		
		$query = insertQuery($dbname,'keu_kasbankdt',$data,$cols);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		
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
	//        echo "warning: ".$query;
			if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
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
		$optField = makeOption($dbname,'keu_5akun','noakun,fieldaktif',
			"noakun='".$param['noakun']."'");
		echo $optField[$param['noakun']];
		break;
	
    case'getForminvoice':
        $form="<fieldset style=float: left;>
				<legend>".$_SESSION['lang']['find']." ".$_SESSION['lang']['noinvoice']."</legend>
					".$_SESSION['lang']['find']."<input type=text class=myinputtext id=no_brg value=".date('Y').">&nbsp;
					".$_SESSION['lang']['namasupplier']."<input id=supplierIdcr style=width:150px>&nbsp;
					".$_SESSION['lang']['nopo']."<input id=nopocr style=width:150px>&nbsp;
					<button class=mybutton onclick=findNoinvoice()>Find</button></fieldset><div id=container2><fieldset><legend>".$_SESSION['lang']['result']."</legend></fieldset></div>";
        echo $form;
    break;
	case'getForm':
	$arrTipe=array("PO"=>"PO","KL"=>"Claim Pengobatan");
	foreach($arrTipe as $rwTp =>$dtTipe){
		$optTipe.="<option value='".$rwTp."'>".$dtTipe."</option>";
	}
        $form="<fieldset style=float: left;>
				<legend>".$_SESSION['lang']['find']." </legend>
					".$_SESSION['lang']['find']."<input type=text class=myinputtext id=txtCari value=''>&nbsp;
					".$_SESSION['lang']['tipe']."<select id=tipeDt style=width:150px>".$optTipe."</select>
					<button class=mybutton onclick=findNoinvoice2()>Find</button></fieldset><div id=container2><fieldset><legend>".$_SESSION['lang']['result']."</legend></fieldset></div>";
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
					$dat.="<tr class='rowcontent' title='Document not complete:VP NOT EXIST'><td>".$no."</td>";
				} else {
					$dat.="<tr class='rowcontent' title='Document not complete:".$rPo['namakaryawan']."'><td>".$no."</td>";
				}
				$dat.="<td style='background-color:red;'>".$rPo['noinvoice']."</td>"; 
            } else { 
				if($rJmlh['jmlhKas']>=$rCek['jmlhinvoice'])
				{
                    $dat.="<tr class='rowcontent' title='Already exist'><td>".$no."</td>"; 
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
                                        
                                        #cek ada akun hutang
                                        /*$iCek="select jumlah from ".$dbname.".keu_vpdt a left join ".$dbname.".keu_vp_inv b on a.novp=b.novp "
                        . "where b.noinv='".$rPo['noinvoice']."' and noakun in (select noakun from ".$dbname.".keu_5akun where namaakun like '%Hutang PPh%') or left(noakun,3)='212'";
                                        $nCek=mysql_query($iCek) or die (mysql_error($conn));
                                        $dCek=mysql_fetch_assoc($nCek);*/
                                        
                                        if($rPo['tipeinvoice']=='k'){
						$rCek['jmlhinvoice']=$rPo['nilaiinvoice'];
						//$rPo['nilaiinvoice']=($rakun['jumlah']*-1);
					}   
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
                            $rCek['jmlhinvoice']=$rakun['kurs']*$rCek['jmlhinvoice'];
                            $rakun['matauang']='IDR';
                            $rakun['kurs']='1';
                            $dat.="<tr class='rowcontent' onclick=\"setPo('".$rPo['noinvoice']."','".$rCek['jmlhinvoice']."','".$rakun['noakun']."','".$rPo['keterangan']."','".$rPo['kodesupplier']."','".$rPo['nopo']."','".$rakun['matauang']."','".$rakun['kurs']."')\" style='pointer:cursor;'><td>".$no."</td>";
			    $dat.="<td>".$rPo['noinvoice']."</td>";
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
	case'getInvoice2':
		$tab.="<div style=overflow:auto;width:445px;height:300px;><table class=sortable border=0 cellpadding=1 cellspacing=1>";
		$tab.="<thead><tr>";
		$tab.="<td>No.</td>";
		$tab.="<td>No. Dokumen</td>
			   <td>".$_SESSION['lang']['rp']."</td>
		       </tr><thead><tbody>";
		switch($param['tipeDt']){
			case'PO':
			$tipePo=" and lokalpusat=0";
			if($_SESSION['empl']['tipelokasitugas']!='HOLDING'){
				$tipePo=" and lokalpusat=1";
			}
				$sData="select distinct nopo as nodokumen,purchaser as userdata,subtotal as nilairupiah,matauang,kurs from ".$dbname.".log_poht 
				        where statusbayar='CASH' and kodeorg='".$_SESSION['org']['kodeorganisasi']."' 
						and nopo like '%".$param['txtfind']."%' ".$tipePo."";
			break;
			case'KL':
				$sData="select distinct notransaksi as nodokumen,karyawanid as userdata,jlhbayar as nilairupiah from ".$dbname.".sdm_pengobatanht 
				        where tipe='KARYAWAN' and kodeorg='".$_SESSION['empl']['lokasitugas']."' 
						and notransaksi like '%".$param['txtfind']."%' ";
			break;
		}
		$qData=mysql_query($sData) or die(mysql_error($conn));
		while($rData=mysql_fetch_assoc($qData)){
			$no+=1;
			if($rData['matauang']!=''){
				if($rData['matauang']!='USD'){
					$rData['nilairupiah']=$rData['nilairupiah']*$rData['kurs'];
				}
			}else{
				$rData['matauang']='IDR';
				$rData['kurs']='1';
			}
			$tab.="<tr class=rowcontent style='cursor:pointer' onclick=setData('".$rData['nodokumen']."','".$rData['userdata']."','".$rData['nilairupiah']."','".$rData['matauang']."','".$rData['kurs']."')>";
			$tab.="<td>".$no."</td>";
			$tab.="<td>".$rData['nodokumen']."</td>";
			$tab.="<td align=right>".$rData['nilairupiah']."</td>";
			$tab.="</tr>";
		}
		$tab.="</tbody></table></div>";
		echo $tab;
	break;
	case 'getAllPt':
                if($param['tipe']=='all') {
                    if($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
                            $whereKary = "(tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].") and tipekaryawan in ('0','1','2')";
                    }else{
                            $whereKary = "(tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].")";
                    }
                } else {
                    if($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
                            $whereKary = "kodeorganisasi='".$_SESSION['empl']['kodeorganisasi']."' and (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].") and tipekaryawan in ('0','1','2')";
                    }else{
                            $whereKary = "kodeorganisasi='".$_SESSION['empl']['kodeorganisasi']."' and (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].")";
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