<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

$proses = $_GET['proses'];
$param = $_POST;

//cari nama orang
$str="select karyawanid, namakaryawan from ".$dbname.".datakaryawan";
$res=mysql_query($str);
while($bar= mysql_fetch_object($res))
{
   $nama[$bar->karyawanid]=$bar->namakaryawan;
}    

switch($proses) {
    # Daftar Header
    case 'showHeadList':    
		$where = "kodeorg='".$_SESSION['org']['kodeorganisasi']."'";
                if ($_SESSION['empl']['bagian']!='IT'){
                    $where.= " and updateby='".$_SESSION['standard']['userid']."'";
                }
			if($_SESSION['empl']['kodejabatan']==5)$where = "kodeorg like '%' and updateby like '%'";
		if(isset($param['where'])) {
			$tmpW = str_replace('\\','',$param['where']);
			$arrWhere = json_decode($tmpW,true);
			if(!empty($arrWhere)) {
			foreach($arrWhere as $key=>$r1) {
				$where .= " and ".$r1[0]." like '%".$r1[1]."%'";
			}
				   
			} 
		}
			
		# Header
		$header = array(
			"No Transaksi",
			$_SESSION['lang']['noinvoice']." Supplier",$_SESSION['lang']['pt'],
			$_SESSION['lang']['tanggal'],'Last Update',$_SESSION['lang']['nopo'],
			$_SESSION['lang']['keterangan'],$_SESSION['lang']['subtotal'],'postingby',"Posting Date"
		);
		
		# Content
		$cols = "noinvoice,noinvoicesupplier,kodeorg,tanggal,updateby,nopo,keterangan,nilaiinvoice,postingby,tanggalposting,posting";
			$order="tanggal desc";
		$query = selectQuery($dbname,'keu_tagihanht',$cols,$where,$order,false,$param['shows'],$param['page']);

		$data = fetchData($query);
		$totalRow = getTotalRow($dbname,'keu_tagihanht',$where);
		foreach($data as $key=>$row) {
			if($row['posting']==1) {
				$data[$key]['switched']=true;
			}
			if(!empty($row['tanggalposting'])) {
				$data[$key]['tanggalposting'] = tanggalnormal($row['tanggalposting']);
			}
			unset($data[$key]['posting']);
			$data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
			$data[$key]['nilaiinvoice'] = number_format($row['nilaiinvoice'],2);
			$data[$key]['updateby'] = $nama[$row['updateby']];
			if($row['postingby']==0) {
				$data[$key]['postingby'] = "";
			} else {
				$data[$key]['postingby'] = $nama[$row['postingby']];
			}
		}
		
		# Make Table
		$tHeader = new rTable('headTable','headTableBody',$header,$data);
		$tHeader->addAction('showEdit','Edit','images/'.$_SESSION['theme']."/edit.png");
			

//		if($_SESSION['empl']['tipelokasitugas']=='HOLDING' or $_SESSION['empl']['tipelokasitugas']=='KANWIL'){
//		} else {//hanya HO dan region yang boleh menghapus
//			$tHeader->addAction('','Delete','images/'.$_SESSION['theme'].'/delete.png');
//		}
			$tHeader->addAction('deleteData','Delete','images/'.$_SESSION['theme']."/delete.png");
			
		$tHeader->addAction('postingData','Posting','images/'.$_SESSION['theme']."/posting.png");
		$tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme']."/posted.png");
		// $tHeader->addAction('detailPDF','Print Data Detail','images/'.$_SESSION['theme']."/pdf.jpg");
		// $tHeader->_actions[3]->addAttr('event');
		// $tHeader->_switchException = array('detailPDF');
			
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
		$query = selectQuery($dbname,'keu_tagihanht',"*","noinvoice='".$param['noinvoice']."'");
		$tmpData = fetchData($query);
		$data = $tmpData[0];
		$data['tanggal'] = tanggalnormal($data['tanggal']);
		$data['jatuhtempo'] = tanggalnormal($data['jatuhtempo']);
		echo formHeader('edit',$data);
		echo "<div id='detailField' style='clear:both'></div>";
		break;
    # Proses Add Header
    case 'add':
		$data = $_POST;
    
		if($data['tipeinvoice']=='po') {
			$optPO = makeOption($dbname,'log_poht','nopo,kodesupplier',"stat_release=1");
                        //jmlh po di dari po
                        $sCek2="select distinct  nilaipo as jmlhpo from ".$dbname.".log_poht where nopo='".$data['nopo']."' ";
                        $qCek2=mysql_query($sCek2) or die(mysql_error($conn));
                        $rCek2=mysql_fetch_assoc($qCek2);
		} elseif($data['tipeinvoice']=='kontrak') {
                        $sCek2="select distinct nilaikontrak as jmlhpo from ".$dbname.".log_spkht where notransaksi='".$data['nopo']."' ";
                        $qCek2=mysql_query($sCek2) or die(mysql_error($conn));
                        $rCek2=mysql_fetch_assoc($qCek2);
			$optPO = makeOption($dbname,'log_spkht','notransaksi,koderekanan');
		} elseif($data['tipeinvoice']=='sj') {
			$optPO = makeOption($dbname,'log_suratjalanht','nosj,expeditor',"nosj='".$data['nopo']."'");
		} elseif($data['tipeinvoice']=='ns') {
			$optPO = makeOption($dbname,'log_konosemenht','nokonosemen,shipper',"nokonosemen='".$data['nopo']."'");
		} elseif($data['tipeinvoice']=='ot') {
                    if($data['nopo']==''){
                        exit("error: Field No.PO can't empty is represent a refrence document");
                    }
                }
		#$optPO = makeOption($dbname,'log_poht','nopo,kodesupplier',"nopo='".$data['nopo']."'");
		// Error Trap
		$warning = "";
		if($data['noinvoice']=='') {$warning .= "Invoice number is obligatory\n";}
		if($data['tanggal']=='') {$warning .= "Date is obligatory\n";}
		if($warning!=''){echo "Warning :\n".$warning;exit;}
		
		$data['tipeinvoice'] = substr($data['tipeinvoice'],0,1);
		$data['tanggal'] = tanggalsystem($data['tanggal']);
		$data['nilaiinvoice'] = str_replace(',','',$data['nilaiinvoice']);
		$data['uangmuka'] = str_replace(',','',$data['uangmuka']);
		$data['nilaippn'] = str_replace(',','',$data['nilaippn']);
		if($data['jatuhtempo']!='') {
			$data['jatuhtempo'] = tanggalsystem($data['jatuhtempo']);
		} else {
			$data['jatuhtempo'] = '0000-00-00';
		}
                
                if($data['tipeinvoice']!='o'){
                    $data['kodesupplier'] = $optPO[$data['nopo']];
                } 
		$data['updateby'] = $_SESSION['standard']['userid'];
                if(($data['tipeinvoice']=='p')||($data['tipeinvoice']=='k')){
                    $sCek="select distinct sum(nilaiinvoice+nilaippn) as jmlhinvoice,noinvoice,updateby "
                        . "from ".$dbname.".keu_tagihanht where nopo='".$data['nopo']."' order by noinvoice desc";
                    $qCek=mysql_query($sCek) or die(mysql_error($conn));
                    $rCek=mysql_fetch_assoc($qCek);
                        
                    if(($rCek['jmlhinvoice']+$data['nilaiinvoice'])>$rCek2['jmlhpo']){
                         exit("Error: Previous invoice : ".$rCek['noinvoice'].", amount: ".number_format($rCek['jmlhinvoice'],2)." greater than PO/Contract amount ".number_format($rCek2['jmlhpo'],2).",update by : ".$nama[$rCek['updateby']]);
                    }
                }
		$cols = array();
		foreach($data as $key=>$row){
                             $cols[] = $key;
		}
		$query = insertQuery($dbname,'keu_tagihanht',$data,$cols);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
		}
		break;
    # Proses Edit Header
    case 'edit':
		$data = $_POST;
		$where = "noinvoice='".$data['noinvoice']."'";
		unset($data['noinvoice']);
                if($data['tipeinvoice']=='po') {
			$optPO = makeOption($dbname,'log_poht','nopo,kodesupplier',"stat_release=1");
                        //jmlh po di dari po
                        $sCek2="select distinct  nilaipo as jmlhpo from ".$dbname.".log_poht where nopo='".$data['nopo']."' ";
                        $qCek2=mysql_query($sCek2) or die(mysql_error($conn));
                        $rCek2=mysql_fetch_assoc($qCek2);
		} elseif($data['tipeinvoice']=='kontrak') {
                        $sCek2="select distinct nilaikontrak as jmlhpo from ".$dbname.".log_spkht where notransaksi='".$data['nopo']."' ";
                        $qCek2=mysql_query($sCek2) or die(mysql_error($conn));
                        $rCek2=mysql_fetch_assoc($qCek2);
			$optPO = makeOption($dbname,'log_spkht','notransaksi,koderekanan');
		} elseif($data['tipeinvoice']=='sj') {
			$optPO = makeOption($dbname,'log_suratjalanht','nosj,expeditor',"nosj='".$data['nopo']."'");
		} elseif($data['tipeinvoice']=='ns') {
			$optPO = makeOption($dbname,'log_konosemenht','nokonosemen,shipper',"nokonosemen='".$data['nopo']."'");
		} elseif($data['tipeinvoice']=='ot') {
                    if($data['nopo']==''){
                        exit("error: Field No.PO can't empty is represent a refrence document");
                    }
                }
                if(($data['tipeinvoice']=='po')||($data['tipeinvoice']=='kontrak')){
                   $sCek="select distinct sum(nilaiinvoice+nilaippn) as jmlhinvoice,noinvoice,updateby "
                        . "from ".$dbname.".keu_tagihanht where nopo='".$data['nopo']."'  order by noinvoice desc";
                    $qCek=mysql_query($sCek) or die(mysql_error($conn));
                    $rCek=mysql_fetch_assoc($qCek);
                    //exit("error:___".$rCek['jmlhinvoice']."____".$rCek2['jmlhpo']);
                    if(($rCek['jmlhinvoice']+$data['nilaiinvoice'])>$rCek2['jmlhpo']){
                         exit("Error: Previous invoices : ".$rCek['noinvoice'].",amount: ".number_format($rCek['jmlhinvoice'],2)." greater than PO/Contract amount ".number_format($rCek2['jmlhpo'],2));
                    }
                }
		$data['tanggal'] = tanggalsystem($data['tanggal']);
		$data['jatuhtempo'] = tanggalsystem($data['jatuhtempo']);
		$data['tipeinvoice'] = substr($data['tipeinvoice'],0,1);
		$data['nilaiinvoice'] = str_replace(',','',$data['nilaiinvoice']);
		$data['uangmuka'] = str_replace(',','',$data['uangmuka']);
		$data['nilaippn'] = str_replace(',','',$data['nilaippn']);
	        $data['updateby'] = $_SESSION['standard']['userid'];
		$query = updateQuery($dbname,'keu_tagihanht',$data,$where);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
		}
		break;
    case 'delete':
		$where = "noinvoice='".$param['noinvoice']."'";
		$query = "delete from `".$dbname."`.`keu_tagihanht` where ".$where;
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		break;
    case 'updpo':
		$pokontrak = $_POST['pokontrak'];
		if($pokontrak=='po') {
			$resPO = makeOption($dbname,'log_poht','nopo,nopo',"stat_release=1",'0',true);
		} if($pokontrak=='sj') {
			$resPO = makeOption($dbname,'log_pengiriman_ht','nosj,nosj','0',true);
		} else {
			$resPO = makeOption($dbname,'log_spkht','notransaksi,notransaksi',
			"kodeorg='".$_SESSION['empl']['lokasitugas']."'",'0',true);
		}
		
		echo json_encode($resPO);
		break;
    case 'updInvoice':
		# Check existing PO
		$query = selectQuery($dbname,'keu_tagihanht','nilaiinvoice',"nopo='".$_POST['nopo']."'");
		$res = fetchData($query);
		if(!empty($res)) {
			echo $res[0]['nilaiinvoice'];
		}
		break;
    case'getPo':
		$jenisInvoice = $_POST['jnsInvoice'];
		
        $optNmsupp=makeOption($dbname, 'log_5supplier','supplierid,namasupplier');
        $dat="<fieldset><legend>".$_SESSION['lang']['result']."</legend>";
        $dat.="<div style=overflow:auto;width:100%;height:310px;>";
        $dat.="<table cellpadding=1 cellspacing=1 border=0 class='sortable'><thead>";
        $dat.="<tr class='rowheader'><td>No.</td>";
        $rPo['ppn']=0;
		
		$where = '';
		switch($jenisInvoice) {
			case 'po':
				if($param['txtfind']!='')
				{
					$where=" and nopo like '%".$param['txtfind']."%'";
				}
				//$where.=" and closed=0";
				$addlokal=" and lokalpusat=0 ";
				$addkdorg=" and kodeorg='".$_SESSION['org']['kodeorganisasi']."'";
				if($_SESSION['empl']['tipelokasitugas']=='KANWIL')
				{
					$addlokal=" and lokalpusat=1 ";
					$addkdorg="";
				}
				$sPo="select distinct nopo,sum(subtotal+ongkosangkutan+misc) as nilaipo,nilaipo as jumlahnilaipo,matauang,sum(ppn+ongkirimppn) as ppn,kodesupplier,closed,nilaidiskon from ".$dbname.".log_poht where 
                                      kodeorg='".$_SESSION['org']['kodeorganisasi']."' and stat_release=1 and statusbayar='CREDIT' ".$where." ".$addlokal." group by nopo  order by tanggal desc";//and nopo not in (select distinct nopo from ".$dbname.".keu_tagihanht where tipeinvoice='p') ".$where." and kodeorg='".$_SESSION['org']['kodeorganisasi']."'order by tanggal desc";
				
				// Table Header
				$dat.="<td>".$_SESSION['lang']['nopo']."</td>";
				$dat.="<td>".$_SESSION['lang']['namasupplier']."</td></tr></thead><tbody>";
				break;
			case 'sj':
				if($param['txtfind']!='')
				{
					$where="where nosj like '%".$param['txtfind']."%'";
				}
				$sPo="select distinct nosj as nopo,expeditor as kodesupplier from ".$dbname.". log_suratjalanht 
					   ".$where."  order by nosj desc";
				
				// Table Header
				$dat.="<td>".$_SESSION['lang']['nosj']."</td>";
				$dat.="<td>".$_SESSION['lang']['expeditor']."</td></tr></thead><tbody>";
				break;
			case 'ns':
				if($param['txtfind']!='')
				{
					$where="where nokonosemen like '%".$param['txtfind']."%'";
				}
				$sPo="select distinct nokonosemen as nopo,shipper as kodesupplier from ".$dbname.". log_konosemenht 
					   ".$where."  order by nokonosemen desc";
				
				// Table Header
				$dat.="<td>".$_SESSION['lang']['nokonosemen']."</td>";
				$dat.="<td>".$_SESSION['lang']['shipper']."</td></tr></thead><tbody>";
				break;
			default:
				if($param['txtfind']!='')
				{
					$where=" and notransaksi like '%".$param['txtfind']."%'";
				}
			   if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
			   {
				   $sPo="select distinct notransaksi as nopo,nilaikontrak as nilaipo,koderekanan as kodesupplier from ".$dbname.".log_spkht where kodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$_SESSION['org']['kodeorganisasi']."')  ".$where."  order by tanggal desc";
			   }
			   else
			   {
				   $sPo="select distinct notransaksi as nopo,nilaikontrak as nilaipo,koderekanan as kodesupplier from ".$dbname.".log_spkht where  kodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$_SESSION['org']['kodeorganisasi']."') ".$where."   order by tanggal desc";
			   }
               $dat.="<td>".$_SESSION['lang']['kontrak']."</td>";
				$dat.="<td>".$_SESSION['lang']['kontraktor']."</td></tr></thead><tbody>";
			   break;
        }
        //echo $sPo;
        //echo $_SESSION['org']['kodeorganisasi'];
       /*  if($jenisInvoice=='po'){
            $sCek="select sum(jumlahpesan-jumlahterima) as selisih,kodebarang,nopo,jumlahpesan,jumlahterima from ".$dbname.".log_po_terima_vw
                   where kodeorg='".$_SESSION['org']['kodeorganisasi']."' ".$where."  group by nopo,kodebarang order by nopo asc";
          //  echo $sCek;
            $qCek=mysql_query($sCek) or die(mysql_error($conn));
            while($rCek=  mysql_fetch_assoc($qCek)){
                if($nomoPo!=$rCek['nopo']){
                    $nomoPo=$rCek['nopo'];
                    $sJmlhBrg="select count(kodebarang) as jmlbrg from ".$dbname.".log_podt where nopo='".$rCek['nopo']."'";
                    $qJmlBrg=mysql_query($sJmlhBrg) or die(mysql_error($conn));
                    $rJmlBrg=mysql_fetch_assoc($qJmlBrg);
                    $totBrg[$nomoPo]=$rJmlBrg['jmlbrg'];
                }
                $whrdt=" nopo='".$rCek['nopo']."' and kodebarang='".$rCek['kodebarang']."'";
                $optSatuan=  makeOption($dbname, 'log_podt', 'kodebarang,satuan',$whrdt);
                $scekst="select distinct satuankonversi,jumlah from ".$dbname.".log_5stkonversi "
                       . "where kodebarang='".$rCek['kodebarang']."' and satuankonversi='".$optSatuan[$rCek['kodebarang']]."'";
                $qcekst=  mysql_query($scekst) or die(mysql_error($conn));
                $rcekst=  mysql_fetch_assoc($qcekst);
                if($rcekst['jumlah']!=''){
                    $rCek['selisih']=($rCek['jumlahpesan']/$rcekst['jumlah'])-$rCek['jumlahterima'];
                }
                if($rCek['selisih']==0){
                    $brgCompr[$rCek['nopo']]+=1;
                }
            }
        } */
		//echo $sPo;
        $qPo=mysql_query($sPo) or die(mysql_error($conn));
		$no=0;
        while($rPo=mysql_fetch_assoc($qPo)){
            if($jenisInvoice=='po'){
                $sCek="select sum(nilaiinvoice) as jmlhinvoice,sum(nilaippn) as jmlppn,noinvoice,updateby "
                    . "from ".$dbname.".keu_tagihanht where nopo='".$rPo['nopo']."' order by noinvoice";
                $qCek=  mysql_query($sCek) or die(mysql_error($conn));
                $rCek=  mysql_fetch_assoc($qCek);
                $rPo['nilaipo']=$rPo['nilaipo']-$rPo['nilaidiskon'];
                $cekNilaiPo=$rPo['nilaipo'];
                
                if($rCek['jmlhinvoice']!=''){
                    //nilai 
                    //jumlahnilaipo
                    $cekNilaiPo=$rPo['nilaipo']-$rCek['jmlhinvoice'];
                    $rPo['ppn']=$rPo['ppn']-$rCek['jmlppn'];
                    if($cekNilaiPo=='0' && $rPo['ppn']!='0')
                    {
                        $cekNilaiPo=$rPo['ppn'];
                        $rPo['nilaipo']='0';
                    }
                    
                }
                if($cekNilaiPo>0){
                    if($rPo['closed']=='1'){
                        //if($brgCompr[$rPo['nopo']]==$totBrg[$rPo['nopo']]){
                        //}
						$no+=1;
                        $dat.="<tr class='rowcontent' onclick=\"setPo('".$rPo['nopo']."','";
                                    $dat.=isset($rPo['nilaipo'])? $rPo['nilaipo']: 0;
                                    $dat.="','".$param['jnsInvoice']."','";
                                    $dat.=isset($rPo['ppn'])? $rPo['ppn']: 0;
                                    $dat.="')\" style='pointer:cursor;'><td>".$no."</td>";
                        $dat.="<td>".$rPo['nopo']."</td>";
                        $dat.="<td>".$optNmsupp[$rPo['kodesupplier']]."</td></tr>";
                    }else{
						$no+=1;
                        $dat.="<tr class='rowcontent' onclick=\"setPo('".$rPo['nopo']."','";
						 
                                    $dat.=isset($rPo['nilaipo'])? $rPo['nilaipo']: 0;
                                    $dat.="','".$param['jnsInvoice']."','";
                                    $dat.=isset($rPo['ppn'])? $rPo['ppn']: 0;
                                    $dat.="')\" style='pointer:cursor;'><td>".$no."</td>";
                        $dat.="<td>".$rPo['nopo']."</td>";
                        $dat.="<td>".$optNmsupp[$rPo['kodesupplier']]."</td></tr>";
                    }
                }else{
//                    $no+=1;
//                    $dat.="<tr class='rowcontent'";
//                    $dat.="title='this PO already input on invoice no: ".$rCek['noinvoice'].",update by : ".$nama[$rCek['updateby']]."'><td>".$no."</td>";
//                    $dat.="<td>".$rPo['nopo']."</td>";
//                    $dat.="<td>".$rCek['noinvoice']."</td></tr>";
                }
        } else{
                $no+=1;
                $dat.="<tr class='rowcontent' onclick=\"setPo('".$rPo['nopo']."','";
                            $dat.=isset($rPo['nilaipo'])? $rPo['nilaipo']: 0;
                            $dat.="','".$param['jnsInvoice']."','";
                            $dat.=isset($rPo['ppn'])? $rPo['ppn']: 0;
                            $dat.="')\" style='pointer:cursor;'><td>".$no."</td>";
                $dat.="<td>".$rPo['nopo']."</td>";
                $dat.="<td>".$optNmsupp[$rPo['kodesupplier']]."</td></tr>";
            }
        }
        $dat.="</tbody></table></div></fieldset>";
        echo $dat;
        break;
	case'cekStatus':
		$np=$param['np'];
		$sb=$param['sb'];
		if($sb=='CREDIT')
		{
			$a="select notransaksi from ".$dbname.".log_transaksiht where nopo='".$np."' and post=1 ";
			$b=mysql_query($a) or die (mysql_error($conn));
			$c=mysql_fetch_assoc($b);
			$notran=$c['notransaksi'];
			if($notran=='')
			{
				echo 'A';
			}
		}
		break;
    default:
	break;
}

function formHeader($mode,$data) {
    global $dbname;
    
    # Default Value
    if(empty($data)) {
	$data['noinvoice'] = date('Ymdhis');
	$data['noinvoicesupplier'] = "";
	$data['nilaiinvoice'] = '0';
	$data['noakun'] = '';
	$data['tanggal'] = '';
	$data['tipeinvoice'] = 'po';
	$data['nopo'] = '';
	$data['jatuhtempo'] = '';
	$data['nofp'] = '';
	$data['keterangan'] = '';
	$data['uangmuka'] = '0';
	$data['nilaippn'] = '0';
	$data['kodeorg'] = '';
    } else {
	$data['nilaiinvoice'] = number_format($data['nilaiinvoice'],2);
	$data['uangmuka'] = number_format($data['uangmuka'],2);
	$data['nilaippn'] = number_format($data['nilaippn'],2);
        $whrdt="noinvoice='".$data['noinvoice']."'";
	$tmpNopo = makeOption($dbname, 'keu_tagihanht', 'noinvoice,tipeinvoice',$whrdt);
        if($tmpNopo[$data['noinvoice']]=='p'){
            $data['tipeinvoice'] = 'po';
        }elseif($tmpNopo[$data['noinvoice']]=='s'){
            $data['tipeinvoice'] = 'sj';
        }elseif($tmpNopo[$data['noinvoice']]=='n'){
            $data['tipeinvoice'] = 'ns';
        }elseif($tmpNopo[$data['noinvoice']]=='k'){
            $data['tipeinvoice'] = 'kontrak';
        }elseif($tmpNopo[$data['noinvoice']]=='o'){
            $data['tipeinvoice'] = 'ot';
        }
    }
    
    # Disabled Primary
    if($mode=='edit') {
	$disabled = 'disabled';
    } else {
	$disabled = '';
    }
     $disabled2 = 'disabled';
    # Options
    $optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',"kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
    $optAkun = makeOption($dbname,'keu_5akun','noakun,namaakun',"kasbank=1 and detail=1");
    
    $optSupplier= makeOption($dbname,'log_5supplier','supplierid,namasupplier');
   
                       
    if($data['tipeinvoice']=='po') {
	$optPO = makeOption($dbname,'log_poht','nopo,nopo',"stat_release=1",'0',true);
    } 
    if($data['tipeinvoice']=='sj') {
	$optPO = makeOption($dbname,' log_suratjalanht','nosj,nosj',null,'0',true);
    }
    if($data['tipeinvoice']=='ns') {
	$optPO = makeOption($dbname,' log_konosemenht','nokonosemen,nokonosemen',null,'0',true);
    }
    else {
	$optPO = makeOption($dbname,'log_spkht','notransaksi,notransaksi',null,'0',true);
    }
    $optCgt = getEnum($dbname,'keu_kasbankht','cgttu');
    $optYn = array(0=>$_SESSION['lang']['belumposting'],1=>$_SESSION['lang']['posting']);
	$optTipe = array(
		'po'=>'PO',
		'kontrak'=>$_SESSION['lang']['kontrak'],
		'sj'=>$_SESSION['lang']['suratjalan'],
		'ns'=>$_SESSION['lang']['konosemen'],
                'ot'=>$_SESSION['lang']['lain'],
	);
    
    $els = array();
    $els[] = array(
	makeElement('noinvoice','label',$_SESSION['lang']['notransaksi']),
	makeElement('noinvoice','text',$data['noinvoice'],
	    array('style'=>'width:150px','maxlength'=>'20','disabled' => 'disabled'))
    );
	$els[] = array(
	makeElement('noinvoicesupplier','label',$_SESSION['lang']['noinvoice']." Supplier"),
	makeElement('noinvoicesupplier','text',$data['noinvoicesupplier'],
	    array('style'=>'width:150px','maxlength'=>'25'))
    );
    $els[] = array(
	makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
	makeElement('kodeorg','select',$data['kodeorg'],
	    array('style'=>'width:150px'),$optOrg)
    );
    $els[] = array(
	makeElement('tanggal','label',$_SESSION['lang']['tanggal']),
	makeElement('tanggal','text',$data['tanggal'],array('style'=>'width:150px',
	'readonly'=>'readonly','onmousemove'=>'setCalendar(this.id)'))
    );
    $els[] = array(
	makeElement('tipeinvoice','label',$_SESSION['lang']['jenis']),
	makeElement('tipeinvoice','select',$data['tipeinvoice'],
	    array('style'=>'width:150px',$disabled=>$disabled,'onchange'=>'updPO()'),
	    $optTipe)
    );
//    $els[] = array(
//	makeElement('nopo','label',$_SESSION['lang']['nopo']),
//	makeElement('nopo','select',$data['nopo'],
//	    array('style'=>'width:150px','onchange'=>'updInvoice()',$disabled=>$disabled),$optPO)
//    );
    $onpress="";
//    $onpress="onkeypress=\"return validat(event);\"";
    $els[] = array(
	makeElement('nopo','label',$_SESSION['lang']['nopo']),
	makeElement('nopo','text',$data['nopo'],array('style'=>'width:150px',$disabled=>$disabled,
		'onclick'=>"searchNopo('".$_SESSION['lang']['find']." ".$_SESSION['lang']['nopo']."','".$_SESSION['lang']['find']."<input type=text class=myinputtext id=no_brg ".$onpress."><button class=mybutton onclick=findNopo()>Find</button><br><i>Data PO yang tampil adalah PO yang sudah Release</i><br><div id=container2></div>',event)"))
    );
    $els[] = array(
	makeElement('keterangan','label',$_SESSION['lang']['keterangan']),
	makeElement('keterangan','text',$data['keterangan'],array('style'=>'width:150px'))
    );
    $els[] = array(
	makeElement('jatuhtempo','label',$_SESSION['lang']['jatuhtempo']),
	makeElement('jatuhtempo','text',$data['jatuhtempo'],
	    array('style'=>'width:150px','readonly'=>'readonly','onmousemove'=>'setCalendar(this.id)'))
    );
    $els[] = array(
	makeElement('nofp','label',$_SESSION['lang']['nofp']),
	makeElement('nofp','text',$data['nofp'],
	    array('style'=>'width:150px','maxlength'=>'20'))
    );
    $els[] = array(
	makeElement('nilaiinvoice','label',$_SESSION['lang']['nilaiinvoice']),
	makeElement('nilaiinvoice','textnum',$data['nilaiinvoice'],
	    array('style'=>'width:150px','onchange'=>'this.value=remove_comma(this);this.value = _formatted(this)'))
    );
    $els[] = array(
	makeElement('noakun','label',$_SESSION['lang']['noakun']),
	makeElement('noakun','select',$data['noakun'],
	    array('style'=>'width:150px'),$optAkun)
    );
    $els[] = array(
	makeElement('uangmuka','label',$_SESSION['lang']['uangmuka']),
	makeElement('uangmuka','textnum',$data['uangmuka'],
	    array('style'=>'width:150px','onchange'=>'this.value=remove_comma(this);this.value = _formatted(this)'))
    );
//    if($mode!='edit')
//    {
//     $els[] =array(
//       makeElement('gmbrCari','gambar','images/search.png',
//	    array('title'=>'Cari PO/Kontrak','onclick'=>"searchNopo('".$_SESSION['lang']['find']." ".$_SESSION['lang']['nopo']."','<fieldset><legend>".$_SESSION['lang']['find']." ".$_SESSION['lang']['nopo']."</legend>".$_SESSION['lang']['find']."<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findNopo()>Find</button></fieldset><div id=container2></div>',event)"))  
//    );
//    }
    $els[] = array(
	makeElement('nilaippn','label',$_SESSION['lang']['nilaippn']),
	makeElement('nilaippn','textnum',$data['nilaippn'],
	    array('style'=>'width:150px','onchange'=>'this.value=remove_comma(this);this.value = _formatted(this)'))
    );
   
     $els[] = array(
	makeElement('kodesupplier','label',$_SESSION['lang']['namasupplier']),
	makeElement('kodesupplier','selectsearch',$data['kodesupplier'],
	    array('style'=>'width:150px', 'disabled' => 'disabled'),$optSupplier)
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
    
    $catatan="<fieldset style='float:left; width:300px;'><legend id='catatan'><b>".$_SESSION['lang']['catatan']."</b></legend>
        Untuk jenis KONTRAK harap mengisi nilai invoice dengan NILAI HUTANG, bukan NILAI ACCRUE
	</fieldset>";

    
    if($mode=='add') {
	return genElementMultiDim($_SESSION['lang']['addheader'],$els,2,null,1,false);
    } elseif($mode=='edit') {
	return genElementMultiDim($_SESSION['lang']['editheader'],$els,2,null,1,false);
    }
}

?>