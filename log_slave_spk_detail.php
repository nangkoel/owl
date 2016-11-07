<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');
?><?php

$proses = $_GET['proses'];
$param = $_POST;

switch($proses) {
    case 'showDetail':
        //exit("error:".$param['divisi']);
	# Options
        if($_SESSION['empl']['tipelokasitugas']=='KEBUN')
        {
            $scek="select distinct tipe from ".$dbname.".organisasi where induk='".$param['divisi']."'";
            $qcek=mysql_query($scek) or die(mysql_error($conn));
            $rcek=mysql_fetch_assoc($qcek);
            $tpdt="BLOK";
            if($rcek['tipe']=='BIBITAN'){
                  $tpdt="BIBITAN";
            }
            // $optBlok = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
                // "tipe='".$tpdt."' 
                 // and kodeorganisasi like '".$param['divisi']."%' 
                 // and length(kodeorganisasi)>5 
                 // and kodeorganisasi in (select distinct kodeorg from ".$dbname.".setup_blok where left(kodeorg,6)='".$param['divisi']."' and luasareaproduktif>0)");
			$optBlok = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
                "induk='".$param['divisi']."' or kodeorganisasi like '".substr($param['divisi'],0,4)."%'");
        }
        else
        {
            $optBlok = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
                "induk='".$param['divisi']."' or kodeorganisasi like '".substr($param['divisi'],0,4)."%'");
        }
		if($_SESSION['empl']['tipelokasitugas']=='KEBUN') {
			$optBlokStat = makeOption($dbname,'setup_blok','kodeorg,statusblok',
			"kodeorg='".getFirstKey($optBlok)."'");
			if(strlen(getFirstKey($optBlokStat))==10) {
				$whereAct = "kelompok='".getFirstContent($optBlokStat)."'";
			} else {
				$whereAct = "kelompok='KNT'";
			}
			$optAct = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan,satuan,noakun',$whereAct,'6');
		} else {
			
			
			$tipeX=makeOption($dbname,'organisasi','kodeorganisasi,tipe');
			
			if(isset($tipeX[$param['divisi']]) and $tipeX[$param['divisi']]=='PABRIK')
			{
				$whereAct = "kelompok in ('KNT','MIL')";
			}
			else
			{
				$whereAct = "kelompok='KNT'";
			}
			
			
			
			//$whereAct = "kelompok in ('KNT','MIL')";
			$optAct = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan,satuan,noakun',$whereAct,'6');
		}
        #khusus jika project
            if(substr($param['divisi'],0,2)=='AK' or substr($param['divisi'],0,2)=='PB'){
                $optBlok = makeOption($dbname,'project','kode,nama',"kode='".$param['divisi']."' and posting=0");
                 $optAct = makeOption($dbname,'project_dt','kegiatan,namakegiatan',"kodeproject='".$param['divisi']."'");
    
        }
	$optActT = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan,satuan,noakun','6');
	
	# Get Data
	$where = "notransaksi='".$param['notransaksi']."'";
	$cols = "kodeblok,kodekegiatan,hk,hasilkerjajumlah,satuan,jumlahrp";
	$query = selectQuery($dbname,'log_spkdt',$cols,$where);
        $data = fetchData($query);
	$dataShow = $data;
	foreach($dataShow as $key=>$row) {
	    $dataShow[$key]['kodeblok'] = $optBlok[$row['kodeblok']];
        $dataShow[$key]['kodekegiatan'] = $optActT[$row['kodekegiatan']];
	}
	
	# Form
	$theForm1 = new uForm('detailForm','Form Detail',2);
	$theForm1->addEls('kodeblok',$_SESSION['lang']['subunit'],'','select','L',25,$optBlok);
	$theForm1->_elements[0]->_attr['onchange'] = "updKegiatan()";
	$theForm1->addEls('kodekegiatan',$_SESSION['lang']['kodekegiatan'],'','selectsearch','L',25,$optAct);
	$theForm1->addEls('hk',$_SESSION['lang']['hk'],'1','textnum','R',10);
	$theForm1->addEls('hasilkerjajumlah',$_SESSION['lang']['volumekontrak'],'0','textnum','R',10);
	$theForm1->addEls('satuan',$_SESSION['lang']['satuan'],'','text','L',10);
	$theForm1->addEls('jumlahrp',$_SESSION['lang']['total']." ".$_SESSION['lang']['rp'],'0','textnum','R',10);
	$theForm1->_elements[5]->_attr['onchange'] = 'this.value=remove_comma(this);this.value = _formatted(this)';
	
	# Table
	$theTable1 = new uTable('detailTable','Tabel Detail',$cols,$data,$dataShow);
        	
	# FormTable
	$formTab1 = new uFormTable('ftDetail',$theForm1,$theTable1,null,array('notransaksi'));
	$formTab1->_target = "log_slave_spk_detail";
	$formTab1->_numberFormat = '##jumlah';
	$formTab1->_beforeEditMode = "beforeEditMode";
	
	#== Display View
	# Draw Tab
	echo "<fieldset><legend><b>Detail</b></legend>";
	$formTab1->render();
        echo "</fieldset>";
	break;
    case 'add':
        $cols = array(
	    'kodeblok','kodekegiatan','hk',
	    'hasilkerjajumlah','satuan','jumlahrp','notransaksi'
	);
	$data = $param;
        unset($data['numRow']);
	$data['jumlahrp'] = str_replace(',','',$data['jumlahrp']);
	
        $query = insertQuery($dbname,'log_spkdt',$data,$cols);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	
        unset($data['notransaksi']);
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
	$data['jumlahrp'] = str_replace(',','',$data['jumlahrp']);
	foreach($data as $key=>$cont) {
	    if(substr($key,0,5)=='cond_') {
		unset($data[$key]);
	    }
	}
	$where = "notransaksi='".$param['notransaksi']."'and kodekegiatan='".
	    $param['cond_kodekegiatan']."' and kodeblok='".$param['kodeblok']."'";
	$query = updateQuery($dbname,'log_spkdt',$data,$where);
//        exit("error".$query);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	echo json_encode($param);
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
                $where = "notransaksi='".$param['notransaksi']."' and kodekegiatan='".
                    $param['kodekegiatan']."'";
                $query = "delete from `".$dbname."`.`log_spkdt` where ".$where;
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
    case 'updKegiatan':
        
        $optTipe=  makeOption($dbname, 'organisasi', 'kodeorganisasi,tipe');
	$optBlokStat = makeOption($dbname,'setup_blok','kodeorg,statusblok',"kodeorg='".$param['kodeblok']."'");
	if(strlen(getFirstKey($optBlokStat))==10) {
	    $whereAct = "kelompok='".getFirstContent($optBlokStat)."'";
	} else {
            $whereAct = "kelompok='KNT'";
            if($optTipe[$param['kodeblok']]=='PABRIK'){
                $whereAct = "kelompok in ('KNT','MIL')";    
            }
	    
	}
	$optAct = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan,satuan,noakun',$whereAct,'6');
	echo json_encode($optAct);
	break;
    default:
	break;
}
?>