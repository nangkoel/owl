<?php
session_start();
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

#=========== Page Prep =============
$mode = $_POST['mode'];
$num = $_POST['num'];
$idK = $_POST['karyawanid'];

#=========== Get Data ==============
$where = "karyawanid=".$idK;
$query = selectQuery($dbname,'datakaryawan','*',$where);
$data = fetchData($query);

if($mode=='add') {
    foreach($data[0] as $key=>$row) {
        $data[0][$key] = '';
    }
}

#=========== Prepare Tab and Content ==================
$hfrm = array(
    'Utama','Perkawinan','Pendidikan','Alamat','Pangalaman Kerja',
    'Riwayat','Penghargaan','Inventaris','Kondite'
);
$frm = array(
    'Utama',
    makeElement('perkawinan','button','Refresh',array('onclick'=>"refreshTab('perkawinan','".$mode."')")).
    "<div id='tabPerkawinan'></div>",
    makeElement('pendidikan','button','Refresh',array('onclick'=>"refreshTab('pendidikan','".$mode."')")).
    "<div id='tabPendidikan'></div>",
    makeElement('alamat','button','Refresh',array('onclick'=>"refreshTab('alamat','".$mode."')")).
    "<div id='tabAlamat'></div>",
    makeElement('pengalamankerja','button','Refresh',array('onclick'=>"refreshTab('pengalamankerja','".$mode."')")).
    "<div id='tabPengalamanKerja'></div>",
    makeElement('riwayat','button','Refresh',array('onclick'=>"refreshTab('riwayat','".$mode."')")).
    "<div id='tabRiwayat'></div>",
    makeElement('penghargaan','button','Refresh',array('onclick'=>"refreshTab('penghargaan','".$mode."')")).
    "<div id='tabPenghargaan'></div>",
    makeElement('inventaris','button','Refresh',array('onclick'=>"refreshTab('inventaris','".$mode."')")).
    "<div id='tabInventaris'></div>",
    makeElement('kondite','button','Refresh',array('onclick'=>"refreshTab('kondite','".$mode."')")).
    "<div id='tabKondite'></div>"
);

#=========== Tab Utama =============
# Prep Options
$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
$optPend = makeOption($dbname,'sdm_5pendidikan','levelpendidikan,pendidikan');
$optJab = makeOption($dbname,'sdm_5jabatan','kodejabatan,namajabatan');
$optGol = makeOption($dbname,'sdm_5golongan','golongan,keterangan');
$optGender = array('P'=>$_SESSION['lang']['pria'],'W'=>$_SESSION['lang']['wanita']);
$optStatMarr = array(
    'Bujang'=>$_SESSION['lang']['bujang'],
    'Menikah'=>$_SESSION['lang']['menikah'],
    'Janda'=>$_SESSION['lang']['janda'],
    'Duda'=>$_SESSION['lang']['duda']
);
$optAgama = array(
    'Islam'=>$_SESSION['lang']['islam'],
    'Protestan'=>$_SESSION['lang']['protestan'],
    'Katolik'=>$_SESSION['lang']['katolik'],
    'Hindu'=>$_SESSION['lang']['hindu'],
    'Budha'=>$_SESSION['lang']['budha'],
    'Konghucu'=>$_SESSION['lang']['konghucu'],
    'Lainnya'=>$_SESSION['lang']['lain']
);
$optBlood = array(
    'A+'=>'A+','A-'=>'A-',
    'B+'=>'B+','B-'=>'B-',
    'O+'=>'O+','O-'=>'O-',
    'AB+'=>'AB+','AB-'=>'AB-'
);

# Fields
$els = array();
$els[] = array(
  makeElement('nik','label',$_SESSION['lang']['nik']),
  makeElement('nik','text',$data[0]['nik'],array('style'=>'width:100px','maxlength'=>'10'))
);
$els[] = array(
  makeElement('namakaryawan','label',$_SESSION['lang']['namakaryawan']),
  makeElement('namakaryawan','text',$data[0]['namakaryawan'],array('style'=>'width:250px','maxlength'=>'40',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('lahir','label',$_SESSION['lang']['lahir']),
  makeElement('tempatlahir','text',$data[0]['tempatlahir'],array('style'=>'width:250px','maxlength'=>'30',
    'onkeypress'=>'return tanpa_kutip(event)')).
   makeElement('tanggallahir','text',$data[0]['tanggallahir'],array('style'=>'width:250px','readonly'=>'readonly',
    'onmousemove'=>'setCalendar(this.id)'))
);
$els[] = array(
  makeElement('warganegara','label',$_SESSION['lang']['warganegara']),
  makeElement('warganegara','text',$data[0]['warganegara'],array('style'=>'width:250px','maxlength'=>'45',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('jeniskelamin','label',$_SESSION['lang']['jeniskelamin']),
  makeElement('jeniskelamin','select',$data[0]['jeniskelamin'],array('style'=>'width:250px'),$optGender)
);
$els[] = array(
  makeElement('statusperkawinan','label',$_SESSION['lang']['statusperkawinan']),
  makeElement('statusperkawinan','select',$data[0]['statusperkawinan'],array('style'=>'width:250px'),$optStatMarr)
);
$els[] = array(
  makeElement('tanggalmenikah','label',$_SESSION['lang']['tanggalmenikah']),
  makeElement('tanggalmenikah','text',$data[0]['tanggalmenikah'],array('style'=>'width:250px','readonly'=>'readonly',
    'onmousemove'=>'setCalendar(this.id)'))
);
$els[] = array(
  makeElement('agama','label',$_SESSION['lang']['agama']),
  makeElement('agama','text',$data[0]['agama'],array('style'=>'width:250px'),$optAgama)
);
$els[] = array(
  makeElement('golongandarah','label',$_SESSION['lang']['golongandarah']),
  makeElement('golongandarah','select',$data[0]['golongandarah'],array('style'=>'width:70px'),$optBlood)
);
$els[] = array(
  makeElement('levelpendidikan','label',$_SESSION['lang']['levelpendidikan']),
  makeElement('levelpendidikan','select',$data[0]['levelpendidikan'],
    array('style'=>'width:250px'),$optPend)
);
$els[] = array(
  makeElement('alamataktif','label',$_SESSION['lang']['alamataktif']),
  makeElement('alamataktif','text',$data[0]['alamataktif'],array('style'=>'width:250px','maxlength'=>'100',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('provinsi','label',$_SESSION['lang']['provinsi']),
  makeElement('provinsi','text',$data[0]['provinsi'],array('style'=>'width:250px','maxlength'=>'45',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('kota','label',$_SESSION['lang']['kota']),
  makeElement('kota','text',$data[0]['kota'],array('style'=>'width:250px','maxlength'=>'45',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('kodepos','label',$_SESSION['lang']['kodepos']),
  makeElement('kodepos','text',$data[0]['kodepos'],array('style'=>'width:250px','maxlength'=>'5',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('noteleponrumah','label',$_SESSION['lang']['noteleponrumah']),
  makeElement('noteleponrumah','text',$data[0]['noteleponrumah'],array('style'=>'width:250px','maxlength'=>'15',
    'onkeypress'=>'return angka_doang(event)'))
);
$els[] = array(
  makeElement('nohp','label',$_SESSION['lang']['nohp']),
  makeElement('nohp','text',$data[0]['nohp'],array('style'=>'width:250px','maxlength'=>'15',
    'onkeypress'=>'return angka_doang(event)'))
);
$els[] = array(
  makeElement('norekeningbank','label',$_SESSION['lang']['norekeningbank']),
  makeElement('norekeningbank','text',$data[0]['norekeningbank'],array('style'=>'width:250px','maxlength'=>'30',
    'onkeypress'=>'return angka_doang(event)'))
);
$els[] = array(
  makeElement('namabank','label',$_SESSION['lang']['namabank']),
  makeElement('namabank','text',$data[0]['namabank'],array('style'=>'width:250px','maxlength'=>'45',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('sistemgaji','label',$_SESSION['lang']['sistemgaji']),
  makeElement('sistemgaji','text',$data[0]['sistemgaji'],array('style'=>'width:250px','maxlength'=>'3',
    'onkeypress'=>'return angka_doang(event)'))
);
$els[] = array(
  makeElement('nopaspor','label',$_SESSION['lang']['nopaspor']),
  makeElement('nopaspor','text',$data[0]['nopaspor'],array('style'=>'width:250px','maxlength'=>'30',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('noktp','label',$_SESSION['lang']['noktp']),
  makeElement('noktp','text',$data[0]['noktp'],array('style'=>'width:250px','maxlength'=>'30',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('notelepondarurat','label',$_SESSION['lang']['notelepondarurat']),
  makeElement('notelepondarurat','text',$data[0]['notelepondarurat'],array('style'=>'width:250px','maxlength'=>'15',
    'onkeypress'=>'return angka_doang(event)'))
);
$els[] = array(
  makeElement('tanggalmasuk','label',$_SESSION['lang']['tanggalmasuk']),
  makeElement('tanggalmasuk','text',$data[0]['tanggalmasuk'],array('style'=>'width:250px','readonly'=>'readonly',
    'onmousemove'=>'setCalendar(this.id)'))
);
$els[] = array(
  makeElement('tanggalkeluar','label',$_SESSION['lang']['tanggalkeluar']),
  makeElement('tanggalkeluar','text',$data[0]['tanggalkeluar'],array('style'=>'width:250px','maxlength'=>'maxlength',
    'onmousemove'=>'setCalendar(this.id)'))
);
$els[] = array(
  makeElement('tipekaryawan','label',$_SESSION['lang']['tipekaryawan']),
  makeElement('tipekaryawan','text',$data[0]['tipekaryawan'],array('style'=>'width:250px','maxlength'=>'3',
    'onkeypress'=>'return angka_doang(event)'))
);
$els[] = array(
  makeElement('jumlahanak','label',$_SESSION['lang']['jumlahanak']),
  makeElement('jumlahanak','text',$data[0]['jumlahanak'],array('style'=>'width:250px','maxlength'=>'10',
    'onkeypress'=>'return angka_doang(event)'))
);
$els[] = array(
  makeElement('jumlahtanggungan','label',$_SESSION['lang']['jumlahtanggungan']),
  makeElement('jumlahtanggungan','text',$data[0]['jumlahtanggungan'],array('style'=>'width:250px','maxlength'=>'10',
    'onkeypress'=>'return angka_doang(event)'))
);
$els[] = array(
  makeElement('statuspajak','label',$_SESSION['lang']['statuspajak']),
  makeElement('statuspajak','text',$data[0]['statuspajak'],array('style'=>'width:250px','maxlength'=>'4',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('npwp','label',$_SESSION['lang']['npwp']),
  makeElement('npwp','text',$data[0]['npwp'],array('style'=>'width:250px','maxlength'=>'25',
    'onkeypress'=>'return angka_doang(event)'))
);
$els[] = array(
  makeElement('lokasipenerimaan','label',$_SESSION['lang']['lokasipenerimaan']),
  makeElement('lokasipenerimaan','text',$data[0]['lokasipenerimaan'],array('style'=>'width:250px','maxlength'=>'30',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('kodeorganisasi','label',$_SESSION['lang']['kodeorganisasi']),
  makeElement('kodeorganisasi','select',$data[0]['kodeorganisasi'],array('style'=>'width:250px'),$optOrg)
);
$els[] = array(
  makeElement('bagian','label',$_SESSION['lang']['bagian']),
  makeElement('bagian','text',$data[0]['bagian'],array('style'=>'width:250px','maxlength'=>'8',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('kodejabatan','label',$_SESSION['lang']['kodejabatan']),
  makeElement('kodejabatan','select',$data[0]['kodejabatan'],
    array('style'=>'width:250px'),$optJab)
);
$els[] = array(
  makeElement('kodegolongan','label',$_SESSION['lang']['kodegolongan']),
  makeElement('kodegolongan','select',$data[0]['kodegolongan'],
    array('style'=>'width:250px'),$optGol)
);
$els[] = array(
  makeElement('lokasitugas','label',$_SESSION['lang']['lokasitugas']),
  makeElement('lokasitugas','text',$data[0]['lokasitugas'],array('style'=>'width:250px','maxlength'=>'8',
    'onkeypress'=>'return tanpa_kutip(event)'))
);

$frm[0] = "<div style='width:783px;height:345px;overflow:auto'>".genElementMultiDim('Header Data Karyawan',$els,2)."</div>";

#=========== Draw Tab and Content ==================
# Karyawan id
if($mode=='edit') {
    echo makeElement('karyawanid','hidden',$idK);
} else {
    echo makeElement('karyawanid','hidden','');
}

# Tab
drawTab('tabKary',$hfrm,$frm,80,775);
?>