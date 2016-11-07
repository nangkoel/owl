<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

$proses = $_GET['proses'];
$param = $_POST;

switch($proses) {
    # Daftar Header
		case 'showHeadList':
		if($_SESSION['empl']['tipelokasitugas']=='TRAKSI' or
			$_SESSION['empl']['tipelokasitugas']=='HOLDING') {
			$where = "length(kodeorg)=4";
                } elseif ($_SESSION['empl']['tipelokasitugas']=='KANWIL'){
                    $where = "kodeorg in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";
		} else {
			$where = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
		}
		if(isset($param['where'])) {
			$arrWhere = json_decode(str_replace('\\','',$param['where']),true);
			if(!empty($arrWhere)) {
			foreach($arrWhere as $key=>$r1) {
				$where .= " and ".$r1[0]." like '%".$r1[1]."%'";
			}
			}
		}
		

		# Header
		$header = array(
			$_SESSION['lang']['kodeorg'],
				$_SESSION['lang']['notransaksi'],
				$_SESSION['lang']['tanggal'],
				$_SESSION['lang']['subunit'],
				$_SESSION['lang']['koderekanan'],
			$_SESSION['lang']['nilaikontrak'],
				$_SESSION['lang']['dari'],
				$_SESSION['lang']['sampai'],
				$_SESSION['lang']['jumlahrealisasi'],
				
				$_SESSION['lang']['status']            
		);
		
		# Content
		$cols = "kodeorg,notransaksi,tanggal,divisi,koderekanan,nilaikontrak,dari,sampai";
		$query = selectQuery($dbname,'log_spkht',$cols,$where." order by tanggal desc","",false,$param['shows'],$param['page']);
			//exit("Error:".$query);
			$data = fetchData($query);
		$totalRow = getTotalRow($dbname,'log_spkht',$where);
		foreach($data as $key=>$row) {
			$data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
				$data[$key]['dari'] = tanggalnormal($row['dari']);
				$data[$key]['sampai'] = tanggalnormal($row['sampai']);
				//=================ambil realisasi
				$data[$key]['realisasi'] =0;
				$strx="select sum(jumlahrealisasi) from ".$dbname.".log_baspk 
					  where notransaksi='".$data[$key]['notransaksi']."'";
				$resx=mysql_query($strx);
				while($barx=mysql_fetch_array($resx))
				{
				  $data[$key]['realisasi']= number_format($barx[0]); 
				}   
				//lihat postingan-=============================
				$data[$key]['status'] ='';
				$strx="select statusjurnal from ".$dbname.".log_baspk 
					  where notransaksi='".$data[$key]['notransaksi']."' and statusjurnal=0";
				$resx=mysql_query($strx);           
				if(mysql_num_rows($resx)>0)
					$data[$key]['status'] ='?';
				else if($data[$key]['realisasi']==0 and $data[$key]['status']=='')
					$data[$key]['status'] ='?';
				else                
				   $data[$key]['status'] ='Ready to Post';
			 //cek postingan spkht
				$stru="select posting from ".$dbname.".log_spkht where notransaksi='".$data[$key]['notransaksi']."'";
				$resu=mysql_query($stru);
				$post=0;
				while($baru=mysql_fetch_array($resu))
				{
					$post=$baru[0];
				}
				if($post==1)
					$data[$key]['status']='Posted';            

		}
		
		# Options
		if(!empty($data)) {
			$whereSupp = "supplierid in (";
			foreach($data as $key=>$row) {
			  if($key==0) {
			$whereSupp .= "'".$row['koderekanan']."'";
			  } else {
			$whereSupp .= ",'".$row['koderekanan']."'";
			  }
			}
			$whereSupp .= ")";
		} else {
			$whereSupp = null;
		}
		$optSupp = makeOption($dbname,'log_5supplier','supplierid,namasupplier',
			$whereSupp);
		
		# Data Show
		$dataShow = $data;
		foreach($dataShow as $key=>$row) {
		  $dataShow[$key]['koderekanan'] = $optSupp[$row['koderekanan']];
		  $dataShow[$key]['nilaikontrak'] = number_format($row['nilaikontrak'],0);
		}
		$qPosting = selectQuery($dbname,'setup_posting','jabatan',"kodeaplikasi='panen'");
		$tmpPost = fetchData($qPosting);
		$postJabatan = $tmpPost[0]['jabatan'];
		# Make Table
		$tHeader = new rTable('headTable','headTableBody',$header,$data,$dataShow);
		$tHeader->addAction('showEdit','Edit','images/'.$_SESSION['theme']."/edit.png");
		$tHeader->addAction('deleteData','Delete','images/'.$_SESSION['theme']."/delete.png");
		
		$tHeader->addAction('postingData','Posting','images/'.$_SESSION['theme']."/posting.png");
		$tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme']."/posted.png");
	//	if($postJabatan!=$_SESSION['empl']['kodejabatan']) {
	//	    $tHeader->_actions[2]->_name='';
	//	}
	if($_SESSION['empl']['tipelokasitugas'!='HOLDING']){
	if($postJabatan!=$_SESSION['empl']['kodejabatan']) {
	$tHeader->_actions[2]->_name='';
	}
	}	
		//$tHeader->addAction('postingData','Posting','images/'.$_SESSION['theme']."/posting.png");
	//	$tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme']."/posted.png");
		$tHeader->addAction('detailPDF','Print Data Detail','images/'.$_SESSION['theme']."/pdf.jpg");
		$tHeader->_actions[3]->addAttr('event');
		$tHeader->_switchException = array('detailPDF');
		$tHeader->pageSetting($param['page'],$totalRow,$param['shows']);
		if(isset($param['where'])) {
			$tHeader->setWhere($arrWhere);
		}
		
		# View
		$tHeader->renderTable();
		break;
    # Form Add Header
    case 'showAdd':
		// View
		echo formHeader('add',array());
		echo "<div id='detailField' style='clear:both'></div>";
		break;
    # Form Edit Header
    case 'showEdit':
        $where = "notransaksi='".$param['notransaksi']."' and kodeorg='".$param['kodeorg']."'";
		$query = selectQuery($dbname,'log_spkht',"*",$where);
		$tmpData = fetchData($query);
		$data = $tmpData[0];
		$data['tanggal'] = tanggalnormal($data['tanggal']);
			$data['dari'] = tanggalnormal($data['dari']);
			$data['sampai'] = tanggalnormal($data['sampai']);
		echo formHeader('edit',$data);
		echo "<div id='detailField' style='clear:both'></div>";
		break;
    # Proses Add Header
    case 'add':
		$data = $_POST;
		
		// Error Trap
		$warning = "";
		if($data['notransaksi']=='') {$warning .= "No SPK harus diisi\n";}
		if($data['tanggal']=='') {$warning .= "Tanggal harus diisi\n";}
		if($data['kodeorg']=='') {$warning = "Lokasi Tugas harus Kebun\n";}
		if($warning!=''){echo "Warning :\n".$warning;exit;}
		
		$data['tanggal'] = tanggalsystem($data['tanggal']);
			 $data['dari'] = tanggalsystem($data['dari']);
			$data['sampai'] = tanggalsystem($data['sampai']);
		$data['nilaikontrak'] = str_replace(',','',$data['nilaikontrak']);
		
		$cols = array('kodeorg','notransaksi','tanggal','divisi',
			'koderekanan','nilaikontrak','dari','sampai','keterangan');
		$query = insertQuery($dbname,'log_spkht',$data,$cols);
		//exit("Error:".$query);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
		}
		break;
    # Proses Edit Header
    case 'edit':
		$data = $_POST;
		$where = "notransaksi='".$data['notransaksi']."'";
		unset($data['notransaksi']);
		$data['tanggal'] = tanggalsystem($data['tanggal']);
			$data['dari'] = tanggalsystem($data['dari']);
			$data['sampai'] = tanggalsystem($data['sampai']);
		$data['nilaikontrak'] = str_replace(',','',$data['nilaikontrak']);
			
		$query = updateQuery($dbname,'log_spkht',$data,$where);
			//exit("Error:".$query);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
		}
		break;
    case 'delete':
        //================periksa realisasi
             $m =0;
            $strx="select sum(jumlahrealisasi) from ".$dbname.".log_baspk 
                  where notransaksi='".$param['notransaksi']."'";
            $resx=mysql_query($strx);
            while($barx=mysql_fetch_array($resx))
            {
              $m= $barx[0]; 
            }   
            //lihat postingan-=============================
            $n ='';
            $strx="select statusjurnal from ".$dbname.".log_baspk 
                  where notransaksi='".$param['notransaksi']."' and statusjurnal=0";
            $resx=mysql_query($strx);           
            if(mysql_num_rows($resx)>0)
                $n ='?';
            
            if($n=='' and $m==0)
            {     
        //=================================
                $where = "notransaksi='".$param['notransaksi']."'";
                $query = "delete from `".$dbname."`.`log_spkht` where ".$where;
                if(!mysql_query($query)) {
                    echo "DB Error : ".mysql_error();
                    exit;
                }
            }
            else
            {
                exit('Error:Realisasi sudah terisi');
            }   
                
	break;
    case 'updSub':
		$whereDiv = "induk='".$param['kodeorg']."' or kodeorganisasi='".
			$param['kodeorg']."'";
		$optDiv = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whereDiv);
						#tambahkan dari  project=================================================================
						$str="select kode,nama from ".$dbname.".project where kodeorg='".$param['kodeorg']."' and posting=0";
						$res=mysql_query($str);
						while($bar=mysql_fetch_object($res))
						{
							$optDiv[$bar->kode]="[Project]-".$bar->nama;
						}
					   #===================================================================================         
		echo json_encode($optDiv);
		break;
    default:
	break;
}

function formHeader($mode,$data) {
    global $dbname;
    
    # Default Value
    if(empty($data)) {
	$data['kodeorg'] = '';
	$data['notransaksi'] = '0';
	$data['tanggal'] = '';
	$data['divisi'] = '';
	$data['koderekanan'] = '';
	$data['nilaikontrak'] = '0';
        $data['keterangan'] = '';
        $data['dari'] = '';
        $data['sampai'] = '';
    } else {
	$data['nilaikontrak'] = number_format($data['nilaikontrak']);
    }
    
    # Disabled Primary
    if($mode=='edit') {
	$disabled = 'disabled';
    } else {
	$disabled = '';
    }
    
    # Options
    if($_SESSION['empl']['tipelokasitugas']=='TRAKSI' or
	$_SESSION['empl']['tipelokasitugas']=='HOLDING' or
	$_SESSION['empl']['tipelokasitugas']=='KANWIL') {
	$whereOrg = "length(kodeorganisasi)=4";
    } else {
	$whereOrg = "kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
    }
    $optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whereOrg);
    if($data['divisi']=='') {
		if($_SESSION['empl']['tipelokasitugas']=='TRAKSI') {
			$whereDiv = "induk='".getFirstKey($optOrg)."' or kodeorganisasi='".getFirstKey($optOrg)."'";
		} else {
			$whereDiv = "induk='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
		}
    } else {
		$whereDiv = "induk='".$data['kodeorg']."' or kodeorganisasi='".$data['kodeorg']."'";
    }
    $optDiv = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whereDiv);
    
    #tambahkan dari  project=================================================================
	if(!empty($data['kodeorg'])) {
		$tmpDiv = $data['kodeorg'];
	} else {
		$tmpDiv = $_SESSION['empl']['lokasitugas'];
	}
    $str="select kode,nama from ".$dbname.".project where kodeorg='".$tmpDiv."' and posting=0";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $optDiv[$bar->kode]="[Project]-".$bar->nama;
    }
   #=================================================================================== 
    $optSup = makeOption($dbname,'log_5supplier','supplierid,namasupplier',"left(kodekelompok,1)='K' or kodekelompok like 'S003%'");
    
    $els = array();
    if($_SESSION['empl']['tipelokasitugas']=='TRAKSI' or
	$_SESSION['empl']['tipelokasitugas']=='HOLDING' or
	$_SESSION['empl']['tipelokasitugas']=='KANWIL') {
	$els[] = array(
	    makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
	    makeElement('kodeorg','select',$data['kodeorg'],
		array('style'=>'width:150px',$disabled=>$disabled,'onchange'=>'updSub()'),$optOrg)
	);
    } else {
	$els[] = array(
	    makeElement('kodeorg','label',$_SESSION['lang']['kebun']),
	    makeElement('kodeorg','select',$data['kodeorg'],
		array('style'=>'width:150px',$disabled=>$disabled),$optOrg)
	);
    }
    $els[] = array(
	makeElement('notransaksi','label',$_SESSION['lang']['notransaksi']),
	makeElement('notransaksi','text',$data['notransaksi'],
	    array('style'=>'width:150px','maxlength'=>'50',$disabled=>$disabled))
    );
    $els[] = array(
	makeElement('tanggal','label',$_SESSION['lang']['tanggal']),
	makeElement('tanggal','text',$data['tanggal'],array('style'=>'width:150px',
	'readonly'=>'readonly','onmousemove'=>'setCalendar(this.id)'))
    );
    $els[] = array(
	makeElement('divisi','label',$_SESSION['lang']['subunit']),
	makeElement('divisi','select',$data['divisi'],
	    array('style'=>'width:150px',$disabled=>$disabled),$optDiv)
    );
    $els[] = array(
	makeElement('koderekanan','label',$_SESSION['lang']['koderekanan']),
	makeElement('koderekanan','selectsearch',$data['koderekanan'],
            array('style'=>'width:150px'),$optSup)
    );
    $els[] = array(
	makeElement('nilaikontrak','label',$_SESSION['lang']['nilaikontrak']),
	makeElement('nilaikontrak','textnum',$data['nilaikontrak'],
	    array('style'=>'width:150px','maxlength'=>'15',
		'this.value=remove_comma(this);onchange'=>'this.value = _formatted(this)'))
    );
    $els[] = array(
	makeElement('keterangan','label',$_SESSION['lang']['project']),
	makeElement('keterangan','text',$data['keterangan'],
	    array('style'=>'width:150px','maxlength'=>'50'))
    );
    $els[] = array(
	makeElement('dari','label',$_SESSION['lang']['dari']),
	makeElement('dari','text',$data['dari'],array('style'=>'width:150px',
	'readonly'=>'readonly','onmousemove'=>'setCalendar(this.id)'))
    );
    $els[] = array(
	makeElement('sampai','label',$_SESSION['lang']['sampai']),
	makeElement('sampai','text',$data['sampai'],array('style'=>'width:150px',
	'readonly'=>'readonly','onmousemove'=>'setCalendar(this.id)'))
    );
    if($mode=='add') {
	$els['btn'] = array(
	    makeElement('addHead','btn',$_SESSION['lang']['save'],
		array('onclick'=>"addDataTable()"))
	);
    } elseif($mode=='edit') {
	$els['btn'] = array(
	    makeElement('editHead','btn',$_SESSION['lang']['save'],
		array('onclick'=>"editDataTable()"))
	);
    }
    
    if($mode=='add') {
	return genElementMultiDim($_SESSION['lang']['addheader'],$els,2);
    } elseif($mode=='edit') {
	return genElementMultiDim($_SESSION['lang']['editheader'],$els,2);
    }
}
?>