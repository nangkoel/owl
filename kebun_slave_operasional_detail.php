<?php //@Copy nan`koelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
#include_once('lib/zGrid.php');
#include_once('lib/rGrid.php');
include_once('lib/formTable.php');
?><?php
$proses = $_GET['proses'];
$param = $_POST;

switch($proses) {
    case 'cekSisip': // by dz March 13, 2012
        $kegiatan = $param['kodekegiatan'];
        $where = "nilai = '".$kegiatan."'";
        $cols = "kodeaplikasi";
		$query = selectQuery($dbname,'setup_parameterappl',$cols,$where);
        $res=mysql_query($query);
        while($bar=mysql_fetch_object($res))
        {
            $kodeaplikasi=$bar->kodeaplikasi;
        }
        echo $kodeaplikasi;        
        
    break;
    case 'saveSisip': // by dz March 15, 2012
	
//INSERT INTO `owlv1`.`kebun_sisip` (
//`notransaksi` ,
//`tanggal` ,
//`kodeorg` ,
//`jumlah` ,
//`penyebab`
//)
//VALUES (
//'nnnn', '2012-03-15', 'SOGE', '1', 'qwe '
//);

        $notrans = $param['notrans'];
        $kodeorg = $param['kodeorg'];
        $jumlah = $param['jumlah'];
        $penyebab = $param['penyebab'];
        $where = "notransaksi = '".$notrans."'";
        $cols = "tanggal";
		$query = selectQuery($dbname,'kebun_aktifitas',$cols,$where);
        $res=mysql_query($query);
        while($bar=mysql_fetch_object($res))
        {
            $tanggal=$bar->tanggal;
        }
        $qwe="INSERT INTO `".$dbname."`.`kebun_sisip` (`notransaksi` ,`tanggal` ,`kodeorg` ,`jumlah` ,`penyebab`)
        VALUES ('".$notrans."', '".$tanggal."', '".$kodeorg."', '".$jumlah."', '".$penyebab." ')";
        if(!mysql_query($qwe)) {
            echo "Error:".addslashes(mysql_error($conn).$str);
        }
		break;
	
	/***********************************************************************************************
	 ** Show Detail ********************************************************************************
	 ***********************************************************************************************/
	case 'showDetail':
	//buat edit biar jjg disabled
	$notransaksi=$param['notransaksi'];
	
	//621010304
	$i="select * from ".$dbname.".kebun_prestasi where notransaksi='".$notransaksi."'";
	$n=mysql_query($i);
	$d=mysql_fetch_assoc($n);
	$kodekegiatan=$d['kodekegiatan'];
	
	// Get Konversi
	$w="select konversi,kodekegiatan from ".$dbname.".kebun_5psatuan where kodekegiatan='".$kodekegiatan."' and regional='".$_SESSION['empl']['regional']."' ";
	$i=mysql_query($w) or die (mysql_error($conn));
	$b=mysql_fetch_assoc($i);
	$konversi=$b['konversi'];
	$kdKeg=$b['kodekegiatan'];
	
	//exit("Error:$kdKeg");
	//tutup	
	
	//cek tanggal libur untuk absensi libur
	$notransaksi=$param['notransaksi'];
	$tglTran=substr($notransaksi,0,8);//exit("$tglCek");
	$x="select tanggal from ".$dbname.".sdm_5harilibur where tanggal='".$tglTran."' and regional='".$_SESSION['empl']['regional']."'";
	$y=mysql_query($x) or die (mysql_error($conn));
	$z=mysql_fetch_assoc($y);
	$tglCek=$z['tanggal'];
		
	#== Prep Tab
	$headFrame = array(
	    $_SESSION['lang']['prestasi'],
	    $_SESSION['lang']['absensi'],
	    $_SESSION['lang']['material']
	);
	$contentFrame = array();
	
	$blokStatus = $_SESSION['tmp']['actStat'];
	
	# Options
	//$whereKary = "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan<>0";	
	$whereKeg = "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and ";
	switch($blokStatus) {
	    case 'lc':
		$whereKeg = "(kelompok='TB' or kelompok='TBM')";
		break;
	    case 'bibit':
		$whereKeg = "(kelompok='BBT' or kelompok='PN' or kelompok='MN')";
		break;
	    case 'tbm':
		$whereKeg = "(kelompok='TBM')";
		break;
	    case 'tm':
		$whereKeg = "kelompok='TM'";
		break;
	    default:
		break;
	}
    
	if($blokStatus=='bibit'){
		$whereOrg = " statusblok='BBT' and left(kodeorg,4)='".$param['afdeling']."'";
	} else {    
		$whereOrg= " luasareaproduktif>0 and statusblok!='BBT' and left(kodeorg,4)='".$param['afdeling']."'";
	}
	
	$whereAbsen="kodeabsen in ('H','L','MG')";
	
	if($_SESSION['language']=='EN'){
		$optKeg = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan1,satuan,noakun',$whereKeg,'2',true);
	}else{
	   $optKeg = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan,satuan',$whereKeg,'2',true);
	}
	$optOrg = makeOption($dbname,'setup_blok','kodeorg,bloklama,kodeorg',$whereOrg,'9',true);
	$optAbs = makeOption($dbname,'sdm_5absensi','kodeabsen,keterangan',$whereAbsen);
	$optBin = array('1'=>$_SESSION['lang']['yes'],'0'=>$_SESSION['lang']['no']);
	
	#===================================================================================================
	#================ Prestasi Tab =====================================================================
	#===================================================================================================
	# Get Data
	$where = "notransaksi='".$param['notransaksi']."'";
	$cols = "kodekegiatan,kodeorg,jjg,hasilkerja,jumlahhk,upahkerja,umr,upahpremi";
	$query = selectQuery($dbname,'kebun_prestasi',$cols,$where);
	$data = fetchData($query);
	$dataShow = $data;
	foreach($dataShow as $key=>$row) {
	    #$dataShow[$key]['nik'] = $optKary[$row['nik']];
		$kodekegiatan = $row['kodekegiatan'];
	    $dataShow[$key]['kodekegiatan'] = $optKeg[$row['kodekegiatan']];
	    $dataShow[$key]['kodeorg'] = $optOrg[$row['kodeorg']];
	    #$dataShow[$key]['pekerjaanpremi'] = $optBin[$row['pekerjaanpremi']];
	}
	
	if(!empty($data)) {
		$qKonv="select konversi from ".$dbname.".kebun_5psatuan where kodekegiatan='".$kodekegiatan
			."' and regional='".$_SESSION['empl']['regional']."' ";
		$resKonv = fetchData($qKonv);
		if($resKonv[0]['konversi']==1) {
			$disabled = "";
		} else {
			$disabled = "disabled";
		}
		
		$data = $data[0];
		$dataShow = $dataShow[0];
		$cont = '<table class="sortable" cellspacing="1" border="0" id="prestasiTable">
			<thead id="thead_ftPrestasi">
				<tr class="rowheader">
					<td id="head_kodekegiatan" align="center" style="width:250px">'.$_SESSION['lang']['kodekegiatan'].'</td>
					<td id="head_kodeorg" align="center" style="width:250px">'.$_SESSION['lang']['kodeorg'].'</td>
					<td id="head_jjg" align="center" style="width:100px">'.$_SESSION['lang']['jjg'].'</td>
					<td id="head_hasilkerja" align="center" style="width:100px">'.$_SESSION['lang']['hasilkerjajumlah'].'</td>
					<td id="head_jumlahhk" align="center" style="width:100px">'.$_SESSION['lang']['jumlahhk'].'</td>
					<td>*</td>
				</tr>
			</thead>
			<tbody id="tbody_ftPrestasi">
				<tr id="tr_ftPrestasi_0" class="rowcontent">
					<td id="ftPrestasi_kodekegiatan_0" align="left" style="width:25px" value="'.$data['kodekegiatan'].'">
						'.$dataShow['kodekegiatan'].'
					</td>
					<td id="ftPrestasi_kodeorg_0" align="left" style="width:25px" value="'.$data['kodeorg'].'">'.$dataShow['kodeorg'].'</td>
					<td id="ftPrestasi_jjg_0" align="right" style="width:10px" value="'.$data['jjg'].'">'.
						makeElement('jjg','textnum',$dataShow['jjg'],array($disabled=>$disabled,'onchange'=>'getHasilKerja(true);getById(\'tr_ftPrestasi_0\').style.background=\'#FC8848\'')).
					'</td>
					<td id="ftPrestasi_hasilkerja_0" align="right" style="width:10px" value="'.$data['hasilkerja'].'">'.
						makeElement('hasilkerja','textnum',$dataShow['hasilkerja'],array('onchange'=>'getById(\'tr_ftPrestasi_0\').style.background=\'#FC8848\'')).
					'</td>
					<td id="ftPrestasi_jumlahhk_0" align="right" style="width:10px" value="'.$data['jumlahhk'].'">'.
						makeElement('jumlahhk','textnum',$dataShow['jumlahhk'],array('onchange'=>'getById(\'tr_ftPrestasi_0\').style.background=\'#FC8848\'','onkeyup'=>'totalVal()')).
					'</td>
					<td>
						<span id=ftPrestasi_umr_0 value=0 style=\'display:none\'>0</span>
						<span id=ftPrestasi_upahkerja_0 value=0 style=\'display:none\'>0</span>
						<span id=ftPrestasi_upahpremi_0 value=0 style=\'display:none\'>0</span>
						<img src=images/save.png class=zImgBtn onclick="savePrestasi()">
					</td>
				</tr>
			</tbody>
		</table>';
		$contentFrame[0] = $cont;
		$theBlok = $data['kodeorg'];
	} else {
		# Form #indra
		$theForm2 = new uForm('prestasiForm',$_SESSION['lang']['form'].' '.$_SESSION['lang']['prestasi'],2);
		
		//$theForm2->addEls('a','a','a','a','a','a',
		$theForm2->addEls('kodekegiatan',$_SESSION['lang']['kodekegiatan'],'','selectsearch','L',25,$optKeg);
		$theForm2->_elements[0]->_attr['onchange'] = 'cekKonversi()';
		
		$theForm2->addEls('kodeorg',$_SESSION['lang']['kodeorg'],'','selectsearch','L',25,$optOrg,null,null,null,'ftPrestasi_kodeorg');
		$theForm2->_elements[1]->_attr['onchange'] = 'changeOrg();getHasilKerja()';
			$theForm2->_elements[1]->_attr['title'] = 'Please choose block';
		
		//tambahan
		$theForm2->addEls('jjg',$_SESSION['lang']['jjg'],'0','textnum','R',10);
		$theForm2->_elements[2]->_attr['onblur'] = 'getHasilKerja();getKg()';
		
		$theForm2->addEls('hasilkerja',$_SESSION['lang']['hasilkerjajumlah'],'0','textnum','R',10);
		//$theForm2->_elements[3]->_attr['onblur'] = 'getKg()';
		
		$theForm2->addEls('jumlahhk',$_SESSION['lang']['jumlahhk'],'0','textnum','R',10);
		$theForm2->_elements[4]->_attr['onfocus'] =
			"document.getElementById('tmpValHk').value = this.value";
		$theForm2->_elements[4]->_attr['onkeyup'] = "totalVal();";
		/*$theForm2->addEls('norma',$_SESSION['lang']['norma'],'0','textnum','R',10);*/
		
		$theForm2->addEls('upahkerja',$_SESSION['lang']['upahkerja'],'0','textnum','R',10);
		$theForm2->_elements[5]->_attr['disabled'] = 'disabled';
		
		$theForm2->addEls('umr',$_SESSION['lang']['umr'],'0','textnum','R',10);
		$theForm2->_elements[6]->_attr['disabled'] = 'disabled';
		$theForm2->_elements[6]->_attr['onfocus'] =
			"document.getElementById('tmpValUmr').value = this.value";
		$theForm2->_elements[6]->_attr['onkeyup'] = "totalVal();";
		
		$theForm2->addEls('upahpremi',$_SESSION['lang']['upahpremi'],'0','textnum','R',10);
		$theForm2->_elements[7]->_attr['disabled'] = 'disabled';
		$theForm2->_elements[7]->_attr['onfocus'] =
			"document.getElementById('tmpValIns').value = this.value";
		$theForm2->_elements[7]->_attr['onkeyup'] = "totalVal();";
		
		/*$theForm2->addEls('statusblok',$_SESSION['lang']['statusblok'],'-','text','L',4);
		$theForm2->addEls('pekerjaanpremi',$_SESSION['lang']['pekerjaanpremi'],'0','select','R',10,$optBin);
		$theForm2->addEls('penalti1',$_SESSION['lang']['penalti1'],'0','textnum','R',3);
		$theForm2->addEls('penalti2',$_SESSION['lang']['penalti2'],'0','textnum','R',3);
		$theForm2->addEls('penalti3',$_SESSION['lang']['penalti3'],'0','textnum','R',3);
		$theForm2->addEls('penalti4',$_SESSION['lang']['penalti4'],'0','textnum','R',3);
		$theForm2->addEls('penalti5',$_SESSION['lang']['penalti5'],'0','textnum','R',3);*/
		
		# Table
		$theTable2 = new uTable('prestasiTable',$_SESSION['lang']['tabel'].' '.$_SESSION['lang']['prestasi'],$cols,$data,$dataShow);
		
		# FormTable
		$formTab2 = new uFormTable('ftPrestasi',$theForm2,$theTable2,null,array('notransaksi'));
		$formTab2->_target = "kebun_slave_operasional_prestasi";
		$formTab2->_onedata = true;
		if(!empty($data)) {
			$formTab2->_noaction = true;
			$theBlok = $data[0]['kodeorg'];
		} else {
			$theBlok = "";
		}
		#$formTab2->setFreezeEls("##kodekegiatan##kodeorg");
		
		$contentFrame[0] = $formTab2->prep();
	}
	#===================================================================================================
	#================ Absensi Tab ======================================================================
	#===================================================================================================
	# Get Data
	$where = "notransaksi='".$param['notransaksi']."'";
	$cols = "nourut,nik,absensi,jjg,hasilkerja,jhk,umr,insentif";
	$query = selectQuery($dbname,'kebun_kehadiran',$cols,$where);
	$data = fetchData($query);
	
	$nikList = "";
	foreach($data as $row) {
		if($nikList!="") {$nikList .= ',';}
		$nikList .= $row['nik'];
	}
	
	// Option Karyawan
	$whereKary = "(lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan in('2','3','4','6')";
	$whereKary .= " and (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start']."))";
	if(!empty($nikList)) {
		$whereKary .= " or karyawanid in (".$nikList.")";
	}
	$optKary = makeOption($dbname,'datakaryawan','karyawanid,nik,namakaryawan',$whereKary,'5',true);
	
	$dataShow = $data;
	foreach($dataShow as $key=>$row) {
	    $dataShow[$key]['nik'] = $optKary[$row['nik']];
	    $dataShow[$key]['absensi'] = $optAbs[$row['absensi']];
	    $dataShow[$key]['umr'] = number_format($row['umr'],0);
	}
	
	#=============================== Get UMR ==============================
	#dicomen karna get umr dari function lain
	
/*	if(!empty($optKary)) {
		$firstKary = getFirstKey($optKary);
		$qUMR = selectQuery($dbname,'sdm_5gajipokok','sum(jumlah) as nilai',
			"karyawanid=".$firstKary." and tahun=".date('Y')." and idkomponen in (1,31)");
		$Umr = fetchData($qUMR);
	} else {
		$Umr = 0;
	}*/
	#=============================== Get UMR ==============================
	
	# Form #indra
	$theForm1 = new uForm('absensiForm',$_SESSION['lang']['form'].' '.$_SESSION['lang']['absensi'],2);
	$theForm1->addEls('nourut',$_SESSION['lang']['nourut'],'0','textnum','R',3);
	$theForm1->_elements[0]->_attr['disabled'] = 'disabled';
	
    $theForm1->addEls('nik',$_SESSION['lang']['nik'],'','selectsearch','L',25,$optKary,null,null,null,'ftAbsensi_nik');
	//$theForm1->_elements[1]->_attr['onkeyup'] = "updateUMR(this);getAbsen();";
	//$theForm1->_elements[1]->_attr['onkeyup'] = 'updateUMR(this)';
	$theForm1->_elements[1]->_attr['onchange'] = 'cekAbsensiAll()';
	
	
	if($tglCek!='')
	{
		$cekMG=date('D', strtotime($tglCek));
		if($cekMG=='Sun')
		{
			$theForm1->addEls('absensi',$_SESSION['lang']['absensi'],'MG','select','L',25,$optAbs);//2
			$theForm1->_elements[2]->_attr['disabled'] = 'disabled';
		}
		else
		{
			$theForm1->addEls('absensi',$_SESSION['lang']['absensi'],'L','select','L',25,$optAbs);//2
			$theForm1->_elements[2]->_attr['disabled'] = 'disabled';
		}
	}
	else if($tglCek=='')
	{
		$theForm1->addEls('absensi',$_SESSION['lang']['absensi'],'H','select','L',25,$optAbs);//2
		$theForm1->_elements[2]->_attr['disabled'] = 'disabled';	
	}
	
	
	
	
		/*$theForm1->addEls('jjg',$_SESSION['lang']['jjg'],'0','textnum','R',10);//3
		$theForm1->_elements[3]->_attr['onkeyup'] = 'getHasilKerja()';
		$theForm1->_elements[3]->_attr['disabled'] = 'disabled';*/
	
	//tambahan
	// if($konversi=='1')
	// {
		// $theForm1->addEls('jjg',$_SESSION['lang']['jjg'],'0','textnum','R',10);//3
		// $theForm1->_elements[3]->_attr['onkeyup'] = 'cekAbsensiAll()';
	// }
	// else
	// {
		// $theForm1->addEls('jjg',$_SESSION['lang']['jjg'],'0','textnum','R',10);//3
		// $theForm1->_elements[3]->_attr['onkeyup'] = 'getHasilKerja()';
		// $theForm1->_elements[3]->_attr['disabled'] = 'disabled';
	// }
	
	
	if($konversi=='' or $konversi=='0')
	{
		$theForm1->addEls('jjg',$_SESSION['lang']['jjg'],'0','textnum','R',10);//3
		$theForm1->_elements[3]->_attr['onchange'] = 'cekAbsensiAll()';
		$theForm1->_elements[3]->_attr['disabled'] = 'disabled';
	}
	else
	{
		$theForm1->addEls('jjg',$_SESSION['lang']['jjg'],'0','textnum','R',10);//3
		$theForm1->_elements[3]->_attr['onchange'] = 'cekAbsensiAll()';
	}
	
	
	$theForm1->addEls('hasilkerja',$_SESSION['lang']['hasilkerjajumlah'],'0','textnum','R',10);//4
	$theForm1->_elements[4]->_attr['onchange'] = 'cekAbsensiAll(true)';
	//tutup tambahan
	
	//exit("Error:$kdKeg");
	//indra

		$theForm1->addEls('jhk',$_SESSION['lang']['jhk'],'0','textnum','R',10);//5
		$theForm1->_elements[5]->_attr['onkeyup'] = "totalVal();";
		$theForm1->_elements[5]->_attr['onchange'] = 'getUMR1()';
	
		//$theForm1->addEls('jhk',$_SESSION['lang']['jhk'],'0','textnum','R',10);//5
		//$theForm1->_elements[5]->_attr['onkeyup'] = "totalVal();";
		
		
	
	
	
	
	//$q="";
	//$b=
	
	
	$theForm1->addEls('umr',$_SESSION['lang']['umrhari'],'','textnum','R',10);//6
	#$theForm1->_elements[4]->_attr['onkeyup'] = "totalVal();cekVal(this,'Abs','Umr')";
	$theForm1->_elements[6]->_attr['onkeyup'] = "totalVal();";
        $theForm1->_elements[6]->_attr['disabled'] = 'disabled';	
	
	$theForm1->addEls('insentif',$_SESSION['lang']['insentif'],'0','textnum','R',10);//7
	#$theForm1->_elements[5]->_attr['onkeyup'] = "totalVal();cekVal(this,'Abs','Ins')";
	$theForm1->_elements[7]->_attr['onkeyup'] = "totalVal();";
	
	# Table
	$theTable1 = new uTable('absensiTable',$_SESSION['lang']['tabel'].' '.$_SESSION['lang']['absensi'],$cols,$data,$dataShow);
	
	# FormTable
	$formTab1 = new uFormTable('ftAbsensi',$theForm1,$theTable1,null,array('notransaksi'));
	$formTab1->_target = "kebun_slave_operasional_absensi";
	$formTab1->_noEnable = '##nourut##jjg##absensi';//ini untuk ngikutin format form
	$formTab1->_defValue = '##umr='.$Umr[0]['nilai']/25;
	$formTab1->_afterCrud = 'totalVal';
	
	$contentFrame[1] = "<input id=bjrKeg type=hidden value='0'>";
	$contentFrame[1] .="<input type=checkbox id=filternik onclick=filterKaryawan('nik',this) title='Filter Employee'>Filter Employee</checkbox>";
	$contentFrame[1] .="<input type=checkbox id=allptnik onclick=allPtKaryawan('nik',this) title='Show All Employee in Region'>All Employee in Region</checkbox>";
    $contentFrame[1] .= $formTab1->prep();
	
	#===================================================================================================
	#================ Material Tab =====================================================================
	#===================================================================================================
	# Get Data
	$where = "notransaksi='".$param['notransaksi']."'";
	$cols = "kodeorg,kwantitasha,kodegudang,kodebarang,kwantitas";
	$query = selectQuery($dbname,'kebun_pakaimaterial',$cols,$where);
	$data = fetchData($query);
	
	if(!empty($data)) {
	    $whereBarang = "";
	    $i=0;
	    foreach($data as $row) {
		if($i==0) {
		    $whereBarang .= "kodebarang='".$row['kodebarang']."'";
		} else {
		    $whereBarang .= " or kodebarang='".$row['kodebarang']."'";
		}
		$i++;
	    }
	    $optBarang = makeOption($dbname,'log_5masterbarang','kodebarang,namabarang',$whereBarang);
	} else {
	    $optBarang = array();
	}
    $optGudang=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi'," kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%' and tipe in ('GUDANG','GUDANGTEMP')");
	
	$dataShow = $data;
	foreach($dataShow as $key=>$row) {
	    $dataShow[$key]['kodeorg'] = $optOrg[$row['kodeorg']];
            $dataShow[$key]['kwantitasha'] = number_format($row['kwantitasha'],2);
            $dataShow[$key]['kodegudang'] = $optGudang[$row['kodegudang']];
	    $dataShow[$key]['kodebarang'] = $optBarang[$row['kodebarang']];
            $dataShow[$key]['kwantitas'] = number_format($row['kwantitas'],2);
	    
            
	}
	
	# Form
	$theForm3 = new uForm('materialForm',$_SESSION['lang']['form'].' '.$_SESSION['lang']['pakaimaterial'],2);
	$theForm3->addEls('kodeorg',$_SESSION['lang']['kodeorg'],$theBlok,'select','L',25,$optOrg);
	$theForm3->_elements[0]->_attr['disabled'] = 'disabled';
	$theForm3->addEls('kwantitasha',$_SESSION['lang']['kwantitasha'],'0','textnum','R',10);        
	$theForm3->addEls('kodegudang',$_SESSION['lang']['pilihgudang'],'','select','L',25,$optGudang);        
	$theForm3->addEls('kodebarang',$_SESSION['lang']['kodebarang'],'','searchBarang','L',20);
	$theForm3->addEls('kwantitas',$_SESSION['lang']['kwantitas'].' Barang','0','textnum','R',10);

	
	# Table
	$theTable3 = new uTable('materialTable',$_SESSION['lang']['tabel'].' '.$_SESSION['lang']['pakaimaterial'],$cols,$data,$dataShow);
	
	# FormTable
	$formTab3 = new uFormTable('ftMaterial',$theForm3,$theTable3,null,array('notransaksi'));
	$formTab3->_target = "kebun_slave_operasional_material";
	$formTab3->_noClearField = '##kodebarang##kodeorg';
	$formTab3->_noEnable = '##kodebarang##kodeorg';
	
	$contentFrame[2] = $formTab3->prep();
	
	#== Display View
	# Draw Tab
	echo "<fieldset><legend><b>Detail</b></legend>";
	drawTab('FRM',$headFrame,$contentFrame,150,'100%');
	echo "</fieldset>";
	break;
    case 'updateUMR':
	$firstKary = $param['nik'];
	$jhk = $param['jhk'];
	$qUMR = selectQuery($dbname,'sdm_5gajipokok','sum(jumlah) as nilai',
	    "karyawanid=".$firstKary." and tahun=".$param['tahun']." and idkomponen in (1,31)");
	$Umr = fetchData($qUMR);
        @$zUmr=$jhk*$Umr[0]['nilai']/25;
	echo $zUmr;
	break;
    case 'gatKarywanAFD':
        if($param['tipe']=='afdeling')
        {
            $subbagian=substr($param['kodeorg'],0,6);
            $str="select karyawanid,namakaryawan,subbagian from ".$dbname.".datakaryawan where subbagian='".$subbagian."'  and (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].")
                and tipekaryawan in('2','3','4','6') order by namakaryawan";
        }
        else
        {    
            $subbagian=substr($param['kodeorg'],0,4);
            $str="select karyawanid,namakaryawan,subbagian from ".$dbname.".datakaryawan where lokasitugas='".$subbagian."'  and (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].")
                and tipekaryawan in('2','3','4','6') order by namakaryawan";
        }   
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
            echo"<option value='".$bar->karyawanid."'>".$bar->namakaryawan." - ".$bar->subbagian."</option>";
        }
      break;  
    default:
	break;
}
?>