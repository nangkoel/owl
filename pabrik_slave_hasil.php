<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');
?>

<?php
$proses = $_GET['proses'];
$param = $_POST;

switch($proses) {
	
	case 'posting':
		//exit("Errorg:IND"); 
		$data = $_POST;
		$where = "notransaksi='".$data['notransaksi']."'";
		
		$query = updateQuery($dbname,'pabrik_masukkeluartangki',array('posting'=>'1'),$where);
		//exit("Error:$query");
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
		}
	break;

	
	
    # Daftar Header
    case 'showHeadList':
	$where = "kodeorg='".$_SESSION['empl']['lokasitugas']."' order by tanggal desc";
        if(isset($param['where'])) {
	    $arrWhere = json_decode($param['where'],true);
//            echo "<pre>";
//            print_r($arrWhere);
//            echo "</pre>";
	    if(!empty($arrWhere)) {
		foreach($arrWhere as $key=>$r1) {
		    if($key==0) {
			$where .= " and ".$r1[0]." like '%".$r1[1]."%'";
		    } else {
			$where .= " and ".$r1[0]." like '%".$r1[1]."%'";
		    }
		}
	    } else {
		$where .= null;
	    }
	} else {
	    $where .= null;
	}
	
	# Header
	$header = array(
	    $_SESSION['lang']['nomor'],$_SESSION['lang']['tanggal'],$_SESSION['lang']['pabrik'],
            $_SESSION['lang']['kodetangki'],$_SESSION['lang']['kwantitas'],$_SESSION['lang']['kernelquantity'],$_SESSION['lang']['suhu']
	);
	
	# Content
	$cols = "notransaksi,tanggal,kodeorg,kodetangki,kuantitas,kernelquantity,suhu,posting";
	$query = selectQuery($dbname,'pabrik_masukkeluartangki',$cols,$where,"",false,$param['shows'],$param['page']);
//	exit("error: ".$query);
        $data = fetchData($query);
	$totalRow = getTotalRow($dbname,'pabrik_masukkeluartangki',$where);
	foreach($data as $key=>$row) {
	    $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
		
		if($row['posting']==1) {
	    $data[$key]['switched']=true;
		}
		unset($data[$key]['posting']);
	}


	##############

	$x="select kodejabatan from ".$dbname.".sdm_5jabatan where namajabatan like '%ka.%' or namajabatan like '%kepala%' ";
	//echo $x;
	$y=mysql_query($x) or die (mysql_error($conn));
	while($z=mysql_fetch_assoc($y))
	{
		$pos=$z['kodejabatan'];
		if($pos==$_SESSION['empl']['kodejabatan'])
		{
			$flag=1;
		}
	}
	
	##############
	
	# Make Table
	$tHeader = new rTable('headTable','headTableBody',$header,$data);
	$tHeader->addAction('showEdit','Edit','images/'.$_SESSION['theme']."/edit.png");
	$tHeader->addAction('deleteData','Delete','images/'.$_SESSION['theme']."/delete.png");
	
	
	$tHeader->addAction('postingData','Posting','images/'.$_SESSION['theme']."/posting.png");
	$tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme']."/posted.png");
	if($flag!=1) {
    $tHeader->_actions[2]->_name='';
	}
	
	//$tHeader->addAction('postingData','Posting','images/'.$_SESSION['theme']."/posting.png");
	//$tHeader->_actions[1]->setAltImg('images/'.$_SESSION['theme']."/posted.png");
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
	$query = selectQuery($dbname,'pabrik_masukkeluartangki',"*","notransaksi='".$param['notransaksi']."'");
        //exit("error:".$query);
	$tmpData = fetchData($query);
	$data = $tmpData[0];
	//$data['tanggal'] = tanggalnormal($data['tanggal']);
	echo formHeader('edit',$data);
	echo "<div id='detailField' style='clear:both'></div>";
	break;
    # Proses Add Header
    case 'add':
	$data = $_POST;
	
	// Error Trap
	$warning = "";
	if($data['notransaksi']=='') {$warning .= "No Transaksi harus diisi\n";}
	if($data['tanggal']=='') {$warning .= "Tanggal harus diisi\n";}
	if($warning!=''){echo "Warning :\n".$warning;exit;}
	$tgl= explode("-",$data['tanggal']);
        $tglck=$tgl[2]."-".$tgl[1]."-".$tgl[0];
        $tglKmrn = strtotime('-1 day', strtotime($tglck)) ; 
        $tglKmrn=date ( 'Y-m-d' , $tglKmrn );
        
        if(substr($data['kodetangki'],0,3)!='BKL'){
            $scekThn="select distinct * from ".$dbname.".pabrik_masukkeluartangki where tanggal like '".$tgl[2]."%'";
            $qCekThn=mysql_query($scekThn) or die(msyql_error($conn));
            if(mysql_num_rows($qCekThn)!=0){
                $whrcek="kodeorg='".$data['kodeorg']."' and left(tanggal,10)='".$tglKmrn."' and kodetangki='".$data['kodetangki']."'";
                $optcek=makeOption($dbname,'pabrik_masukkeluartangki','kodetangki,kuantitas',$whrcek);
                if($optcek[$data['kodetangki']]==''){
                    exit("error: Sounding data for ".$tglKmrn." is empty!");
                }
            }
        }else{
            if($data['kernelquantity']==''){
                exit("error: ".$_SESSION['lang']['kernelquantity']." can't empty");
            }
        }
        
	$data['tanggal']=$tgl[2]."-".$tgl[1]."-".$tgl[0]." ".$data['jam'].":".$data['jam_menit'];
	unset($data['notransaksi']);
        unset($data['jam']);
        unset($data['jam_menit']);
        
//	$cols = array('tanggal','kodeorg','kodetangki','kuantitas','suhu',
//	    'cporendemen','cpoffa','cpokdair','cpokdkot',
//	    'kernelquantity','kernelrendemen','kernelkdair','kernelkdkot','kernelffa');
	$cols = array('tanggal','kodeorg','kodetangki','kuantitas','suhu',
	    'cpoffa','cpokdair','cpokdkot',
	    'kernelquantity','kernelkdair','kernelkdkot','kernelffa','tinggi');
	$query = insertQuery($dbname,'pabrik_masukkeluartangki',$data,$cols);
        //exit("error:        ".$query);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	}
	break;
    # Proses Edit Header
    case 'edit':
	$data = $_POST;
	$where = "notransaksi='".$data['notransaksi']."'";
	unset($data['notransaksi']);
	$tgl= explode("-",$data['tanggal']);
        $tglck=$tgl[2]."-".$tgl[1]."-".$tgl[0];
        $tglKmrn = strtotime('-1 day', strtotime($tglck)) ; 
        $tglKmrn=date ( 'Y-m-d' , $tglKmrn );
        if(substr($data['kodetangki'],0,3)!='BKL'){
            $whrcek="kodeorg='".$data['kodeorg']."' and left(tanggal,10)='".$tglKmrn."' and kodetangki='".$data['kodetangki']."'";
            $optcek=makeOption($dbname,'pabrik_masukkeluartangki','kodetangki,kuantitas',$whrcek);
            if($optcek[$data['kodetangki']]==''){
                exit("error: Sounding data for ".$tglKmrn." is empty!");
            }
        }else{
            if($data['kernelquantity']==''){
                exit("error: ".$_SESSION['lang']['kernelquantity']." can't empty");
            }
        }
	$data['tanggal']=$tgl[2]."-".$tgl[1]."-".$tgl[0]." ".$data['jam'].":".$data['jam_menit'];
        unset($data['jam']);
        unset($data['jam_menit']);
	$query = updateQuery($dbname,'pabrik_masukkeluartangki',$data,$where);
        //exit("error:".$query);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	}
	break;
    case 'delete':
	$where = "notransaksi='".$param['notransaksi']."'";
	$query = "delete from `".$dbname."`.`pabrik_masukkeluartangki` where ".$where;
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	break;
    case'getVolume':
      
        
           if($_SESSION['empl']['lokasitugas']=='L01M') // Khusus tangki SIL tingginya dalam mm
           {
               $param['tinggi']=$param['tinggi']/10;
               $b=$param['tinggi'];
           }
           else
           {
               $param['tinggi']=$param['tinggi'];
               $b=floor($param['tinggi']);
           }
        
           
           
           $whr="kodeorg='".$_SESSION['empl']['lokasitugas']."' and kodetangki='".$param['kodetangki']."' and tinggicm='".$param['tinggi']."'";
           $optVol= makeOption($dbname,'pabrik_5vtangki','kodetangki,volume',$whr);
           
           $whr2="kodeorg='".$_SESSION['empl']['lokasitugas']."' and kodetangki='".$param['kodetangki']."' and tinggicm='".$d."'";
           $optVol2= makeOption($dbname,'pabrik_5vtangki','kodetangki,volume',$whr2);
           //$g=$optVol2[$param['kodetangki']]-$optVol[$param['kodetangki']];
           $g=$optVol[$param['kodetangki']];
           $tingg=$optVol[$param['kodetangki']];
           if($g==0){
                $tinggiRendah=intval($param['tinggi']);
                $tinggiDiantaranya=$tinggiRendah+1;
                $diffTinggi=$param['tinggi']-$tinggiRendah;
                $whr="kodeorg='".$_SESSION['empl']['lokasitugas']."' and kodetangki='".$param['kodetangki']."' and tinggicm='".$tinggiRendah."'";
                $optVol= makeOption($dbname,'pabrik_5vtangki','kodetangki,volume',$whr);
                $whr2="kodeorg='".$_SESSION['empl']['lokasitugas']."' and kodetangki='".$param['kodetangki']."' and tinggicm='".$tinggiDiantaranya."'";
                $optVol2= makeOption($dbname,'pabrik_5vtangki','kodetangki,volume',$whr2);
                $xTinggi=($optVol2[$param['kodetangki']]-$optVol[$param['kodetangki']])*$diffTinggi;
                $tingg=$optVol[$param['kodetangki']]+$xTinggi;
           }
           
           $j=$param['suhu'];
           $whr3="kodeorg='".$_SESSION['empl']['lokasitugas']."' and kodetangki='".$param['kodetangki']."' and suhu='".$j."'";
           $k= makeOption($dbname,'pabrik_5ketetapansuhu','kodetangki,kepadatan',$whr3);
           $l= makeOption($dbname,'pabrik_5ketetapansuhu','kodetangki,ketetapan',$whr3);
           if(($k[$param['kodetangki']]==0)||($k[$param['kodetangki']]=='')){
               exit("error: Data Suhu Kosong");
           }
           
           $jumlah=$tingg*$k[$param['kodetangki']]*$l[$param['kodetangki']];
           
           //exit("Error:".($optVol2[$param['kodetangki']]-$optVol[$param['kodetangki']]));
           //exit("Error:".$tingg.$k[$param['kodetangki']].$l[$param['kodetangki']]);
           
         //  exit("error:".$jumlah.__.$tingg."___".$k[$param['kodetangki']]."__".$l[$param['kodetangki']]);
           echo ceil($jumlah);
           
           
    break;
    default:
	break;
}

function formHeader($mode,$data) {
    global $dbname;
    
    # Default Value
    if(empty($data)) {
	$data['notransaksi'] = '0';
	$data['kodeorg'] = '';
	$data['tanggal'] = '';
	$data['kodetangki'] = '';
	$data['kuantitas'] = '0';
	$data['suhu'] = '0';
	$data['tinggi'] = '0';
//	$data['cporendemen'] = '0';
        $data['cpoffa'] = '0';$data['cpokdair'] = '0';
	$data['cpokdkot'] = '0';$data['kernelquantity'] = '0';
//        $data['kernelrendemen'] = '0';
	$data['kernelkdair'] = '0';$data['kernelkdkot'] = '0';$data['kernelffa'] = '0';
    }
    
    # Disabled Primary
    if($mode=='edit') {
	$disabled = 'disabled';
    } else {
	$disabled = '';
    }
    
    //$lkstgs=
    if($_SESSION['empl']['lokasitugas']=='H01M')
        $satuan="cm";
    else
        $satuan="mm";
    
    # Options
    $optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
	"tipe='PABRIK' and kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
    $whrTngki="kodeorg='".$_SESSION['empl']['lokasitugas']."'";
    $optTangki = makeOption($dbname,'pabrik_5tangki','kodetangki,kodetangki,keterangan',$whrTngki,'5');
    $tgl=explode(" ",$data['tanggal']);
    if($tgl[0]==''){
        $tgl[0]=date("Y-m-d");
    }
    $data['tanggal']=tanggalnormal($tgl[0]);
    //$jam=substr($data['tanggal'],10,5);
    //exit("error:".$tgl[1]);
    $els = array();
    $els[] = array(
	makeElement('notransaksi','label',$_SESSION['lang']['notransaksi']),
	makeElement('notransaksi','text',$data['notransaksi'],
	    array('style'=>'width:150px','maxlength'=>'12','disabled'=>'disabled'))
    );
    $els[] = array(
	makeElement('tanggal','label',$_SESSION['lang']['tanggal']),
	makeElement('tanggal','text',$data['tanggal'],array('style'=>'width:150px',
	'readonly'=>'readonly','onmousemove'=>'setCalendar(this.id)')),
    );
    $els[] = array (
        makeElement('jam','label',$_SESSION['lang']['jam']),
	makeElement('jam','jammenit',$tgl[1]),
    );
    $els[] = array(
	makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
	makeElement('kodeorg','select',$data['kodeorg'],
	    array('style'=>'width:150px'),$optOrg)
    );
    $els[] = array(
	makeElement('kodetangki','label',$_SESSION['lang']['kodetangki']),
	makeElement('kodetangki','select',$data['kodetangki'],
	    array('style'=>'width:150px','onchange'=>'getVolCpo()'),$optTangki)
    );
    $els[] = array(
	makeElement('suhu','label',$_SESSION['lang']['suhu']),
	makeElement('suhu','textnumw-',$data['suhu'],array('style'=>'width:100px','maxlength'=>'4','onblur'=>'getVolCpo()'))."C"
    );
    $els[] = array(
	makeElement('tinggi','label',$_SESSION['lang']['tinggi']),
	makeElement('tinggi','textnum',$data['tinggi'],array('style'=>'width:100px','onblur'=>'getVolCpo()')).$satuan
    );
    $els[] = array(
	makeElement('kuantitas','label',$_SESSION['lang']['cpokuantitas']),
	makeElement('kuantitas','textnum',$data['kuantitas'],array('style'=>'width:100px'))."kg"
    );
//    $els[] = array(
//	makeElement('cporendemen','label',$_SESSION['lang']['cporendemen']),
//	makeElement('cporendemen','textnum',$data['cporendemen'],array('style'=>'width:100px'))."%"
//    );
    $els[] = array(
	makeElement('cpoffa','label',$_SESSION['lang']['cpoffa']),
	makeElement('cpoffa','textnum',$data['cpoffa'],array('style'=>'width:100px'))."%"
    );
    $els[] = array(
	makeElement('cpokdair','label',$_SESSION['lang']['cpokdair']),
	makeElement('cpokdair','textnum',$data['cpokdair'],array('style'=>'width:100px'))."%"
    );
    $els[] = array(
	makeElement('cpokdkot','label',$_SESSION['lang']['cpokdkot']),
	makeElement('cpokdkot','textnum',$data['cpokdkot'],array('style'=>'width:100px'))."%"
    );
    $els[] = array(
	makeElement('kernelquantity','label',$_SESSION['lang']['kernelquantity']),
	makeElement('kernelquantity','textnum',$data['kernelquantity'],array('style'=>'width:100px'))."kg"
    );
//    $els[] = array(
//	makeElement('kernelrendemen','label',$_SESSION['lang']['kernelrendemen']),
//	makeElement('kernelrendemen','textnum',$data['kernelrendemen'],array('style'=>'width:100px'))."%"
//    );
    $els[] = array(
	makeElement('kernelkdair','label',$_SESSION['lang']['kernelkdair']),
	makeElement('kernelkdair','textnum',$data['kernelkdair'],array('style'=>'width:100px'))."%"
    );
    $els[] = array(
	makeElement('kernelkdkot','label',$_SESSION['lang']['kernelkdkot']),
	makeElement('kernelkdkot','textnum',$data['kernelkdkot'],array('style'=>'width:100px'))."%"
    );
    $els[] = array(
	makeElement('kernelffa','label',$_SESSION['lang']['kernelffa']),
	makeElement('kernelffa','textnum',$data['kernelffa'],array('style'=>'width:100px'))."%"
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
	return genElementMultiDim($_SESSION['lang']['addheader']."(Data sounding)",$els,3);
    } elseif($mode=='edit') {
	return genElementMultiDim($_SESSION['lang']['editheader']."(Data  sounding)",$els,3);
    }
}
?>