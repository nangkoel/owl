<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
#include_once('lib/zGrid.php');
#include_once('lib/rGrid.php');
include_once('lib/formTable.php');

$proses = $_GET['proses'];
$param = $_POST;

switch($proses) {
    case 'showDetail':
            # Options
  
            #khusus jika project
               if(substr($param['divisi'],0,2)=='AK' or substr($param['divisi'],0,2)=='PB'){
                   $optBlok = makeOption($dbname,'project','kode,nama',"kode='".$param['divisi']."' and posting=0");
                   $str="select kegiatan,namakegiatan from ".$dbname.".project_dt where kodeproject='".$param['divisi']."'";
                   $res=mysql_query($str);
                   while($bar=mysql_fetch_object($res)){
                           $optAct[$bar->kegiatan]=$bar->namakegiatan;        
                   }               
               }
               else
               {
//            $optBlok = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
//                    "kodeorganisasi like '".substr($param['divisi'],0,4)."%' and length(kodeorganisasi)>6");
              
				// $str_blok="SELECT b.kodeorganisasi as kodeorg, b.namaorganisasi as namaorg FROM ".$dbname.".setup_blok a LEFT JOIN ".$dbname.".organisasi b 
                     // ON a.kodeorg = b.kodeorganisasi 
                     // WHERE a.luasareaproduktif >0 and b.kodeorganisasi like '".substr($param['divisi'],0,4)."%' 
                     // and length(b.kodeorganisasi)>6";
			    // $res_blok=mysql_query($str_blok);
			    // while($bar=mysql_fetch_object($res_blok))
                // {
                  // $optBlok[$bar->kodeorg]=$bar->namaorg;
                // }
				$optBlok = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
					"induk='".$param['divisi']."' or kodeorganisasi like '".substr($param['divisi'],0,4)."%'");
                
//                echo "<pre>";
//                print_r($str_blok);
//                echo "</pre>";
            $optAct = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan');                         
               }    
            # Get Data
            $where = "notransaksi='".$param['notransaksi']."' and kodeblok like '".substr($param['divisi'],0,4)."%'";
            $cols = "kodeblok,kodekegiatan,hk,hasilkerjajumlah,satuan,jumlahrp";
            $query = selectQuery($dbname,'log_spkdt',$cols,$where);
            $data = fetchData($query);
            $dataShow = array();
            foreach($data as $key=>$row) {

                    $dataShow[$key]['kodeblok'] = $optBlok[$row['kodeblok']];
                    $dataShow[$key]['kodekegiatan'] = $optAct[$row['kodekegiatan']];
                    $dataShow[$key]['hk'] = $row['hk'];
                    $dataShow[$key]['hasilkerjajumlah'] = $row['hasilkerjajumlah'];
                    $dataShow[$key]['satuan'] = $row['satuan'];
                    $dataShow[$key]['jumlahrp'] = $row['jumlahrp'];
            }

                    #== Grid
                    $headName = array(
                            $_SESSION['lang']['subunit'],
                            $_SESSION['lang']['kodekegiatan'],
                            $_SESSION['lang']['hk'],
                            $_SESSION['lang']['hasilkerjajumlah'],
                    $_SESSION['lang']['satuan'],
                            $_SESSION['lang']['jumlahrp'],
                    );

                    # Grid Header
                    $grid = "<table class='sortable'><thead><tr class='rowheader'>";
                    foreach($headName as $head) {
                            $grid .= "<td>".$head."</td>";
                    }
                    $grid .= "</tr></thead>";

                    # Grid Content
                    $grid .= "<tbody>";
                    if(empty($data)) {
                            $grid .= "<tr class='rowcontent'><td colspan='9'>Data Empty</td></tr>";
                    } else {
                            foreach($dataShow as $key=>$row) {
                                    $grid .= "<tr class='rowcontent' onclick=\"manageDetail(".$key.")\" style='cursor:pointer'>";
                                    foreach($row as $head=>$cont) {
                                            $grid .= "<td id='".$head."_".$key."' ";
                            if(isset($data[$key][$head])) {
                            $grid .= "value='".$data[$key][$head]."' ";
                            } else {
                            $grid .= "value='' ";
                            }
                                            if($head=='kodeblok' or $head=='kodekegiatan') {
                                                    $grid .= "align='left'";
                                            } else {
                                                    $grid .= "align='right'";
                                            }
                            if($head=='jumlahrp') {
                            $grid .= ">".number_format($cont)."</td>";
                            } else {
                            $grid .= ">".$cont."</td>";
                            }
                                    }
                                    $grid .= "</tr>";
                    $grid .= "<tr><td colspan='6'><div id='detail_".$key."'></div></td></tr>";
                            }
                    }
                    $grid .= "</tbody>";

                    $grid .= "</table>";

            #== Display View
            # Draw Tab
            echo "<fieldset><legend><b>Detail</b></legend>";
            echo $grid;
            echo "</fieldset>";
            break;
    case 'manageDetail':
            # Get Data
            $cols = 'kodeblok,tanggal,hasilkerjarealisasi,hkrealisasi,jumlahrealisasi,statusjurnal,jjgkontanan';
            $where = "notransaksi='".$param['notransaksi']
                    . "' and kodekegiatan='".$param['kodekegiatan']
                    . "' and blokspkdt='".$param['kodeblok']."'";
            $query = selectQuery($dbname,'log_baspk',$cols,$where);
            
               	$resDetail = fetchData($query);
            foreach($resDetail as $key=>$row) {
                    $resDetail[$key]['jumlahrealisasi'] = number_format($row['jumlahrealisasi']);
            }
                # Options
            if($_SESSION['empl']['tipelokasitugas']!='KEBUN')
                {
//                    $optBlok = makeOption($dbname,'organisasi','kodeorganisasi,kodeorganisasi',
//                          # "tipe='BLOK' and kodeorganisasi like '".substr($param['divisi'],0,4)."%' and length(kodeorganisasi)>5");
//			      "induk='".$param['divisi']."' or kodeorganisasi='".$param['divisi']."'");
                     // $str_blok="SELECT b.kodeorganisasi as kodeorg, b.namaorganisasi as namaorg FROM ".$dbname.".setup_blok a LEFT JOIN ".$dbname.".organisasi b 
                     // ON a.kodeorg = b.kodeorganisasi 
                     // WHERE a.luasareaproduktif >0 and b.kodeorganisasi like '".substr($param['divisi'],0,4)."%' 
                     // and length(b.kodeorganisasi)>6";
               // $res_blok=mysql_query($str_blok);
               // while($bar=mysql_fetch_object($res_blok))
                // {
                  // $optBlok[$bar->kodeorg]=$bar->namaorg;
                // }
				$optBlok = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
					"induk='".$param['divisi']."' or kodeorganisasi like '".$param['divisi']."%'");
                
                    #khusus jika project
                       if(substr($param['divisi'],0,2)=='AK' or substr($param['divisi'],0,2)=='PB'){
                           $optBlok = makeOption($dbname,'project','kode,nama',"kode='".$param['divisi']."' and posting=0");
                       }                    
                }
                else
                {
                    // $str_blok="SELECT b.kodeorganisasi as kodeorg, b.namaorganisasi as namaorg FROM ".$dbname.".setup_blok a LEFT JOIN ".$dbname.".organisasi b 
                     // ON a.kodeorg = b.kodeorganisasi 
                     // WHERE a.luasareaproduktif >0 and b.kodeorganisasi like '".substr($param['divisi'],0,4)."%' 
                     // and length(b.kodeorganisasi)>6";
               // $res_blok=mysql_query($str_blok);
               // while($bar=mysql_fetch_object($res_blok))
                // {
                  // $optBlok[$bar->kodeorg]=$bar->namaorg;
                // }
				$optBlok = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
					"induk='".$param['divisi']."' or kodeorganisasi like '".$param['divisi']."%'");
                
                    #"induk='".$param['divisi']."' or kodeorganisasi='".$param['divisi']."'");
                        #khusus jika project
                        if(substr($param['divisi'],0,2)=='AK' or substr($param['divisi'],0,2)=='PB'){
                            $optBlok = makeOption($dbname,'project','kode,nama',"kode='".$param['divisi']."' and posting=0");
                        }                    
                }
		
		# Setting Table
		$header = array(
			$_SESSION['lang']['subunit'],
			$_SESSION['lang']['tanggal'],
			$_SESSION['lang']['hkrealisasi'],
			$_SESSION['lang']['hasilkerjarealisasi'],
			$_SESSION['lang']['jumlahrealisasi'],
                        $_SESSION['lang']['jjgkontanan'],
			$_SESSION['lang']['action']
		);
		
		# Table
		$table = "";
		$table .= "<table class='sortable' style='margin-bottom:15px'>";
		$table .= "<thead><tr class='rowheader'>";
		foreach($header as $head) {
			$table .= "<td>".$head."</td>";
		}
		$table .= "</tr></thead>";
		$table .= "<tbody id='detailBody_".$param['numRow']."'>";
		$i=0;
		foreach($resDetail as $row) {
			# Exist Row
			$tanggal = tanggalnormal($row['tanggal']);
			$table .= "<tr id='tr_".$param['numRow'].'_'.$i."' class='rowcontent'>";
			$table .= "<td>".makeElement('blokalokasi_'.$param['numRow'].'_'.$i,'select',$row['kodeblok'],array('disabled'=>'disabled'),$optBlok)."</td>";
			$table .= "<td>".makeElement('tanggal_'.$param['numRow'].'_'.$i,'text',$tanggal,array('disabled'=>'disabled'))."</td>";
			if($row['statusjurnal']==0) {
			$table .= "<td>".makeElement('hkrealisasi_'.$param['numRow'].'_'.$i,'textnum',$row['hkrealisasi'])."</td>";
			$table .= "<td>".makeElement('hasilkerjarealisasi_'.$param['numRow'].'_'.$i,
				'textnum',$row['hasilkerjarealisasi'],array('onkeyup'=>"calJumlah(".$param['numRow'].",".$i.")"))."</td>";
			$table .= "<td>".makeElement('jumlahrealisasi_'.$param['numRow'].'_'.$i,'textnum',
				$row['jumlahrealisasi'],array('onchange'=>'this.value=remove_comma(this);this.value = _formatted(this)'))."</td>";
                        $table .= "<td>".makeElement('jjgkontanan_'.$param['numRow'].'_'.$i,'textnum',
				$row['jjgkontanan'],array('onchange'=>'this.value=remove_comma(this);this.value = _formatted(this)'))."</td>";
			$table .= "<td><img id='btn_".$param['numRow'].'_'.$i."' class='zImgBtn' ";
			$table .= "src='images/".$_SESSION['theme']."/save.png' ";
			$table .= "onclick='saveData(".$param['numRow'].",".$i.")'>&nbsp;";
			$table .= "<img id='btnDel_".$param['numRow'].'_'.$i."' class='zImgBtn' ";
			$table .= "src='images/".$_SESSION['theme']."/delete.png' ";
			$table .= "onclick='deleteData(".$param['numRow'].",".$i.")'>&nbsp;";
			$table .= "<img id='btnPost_".$param['numRow'].'_'.$i."' class='zImgBtn' ";
			$table .= "src='images/".$_SESSION['theme']."/posting.png' ";
			$table .= "onclick=\"postingData(".$param['numRow'].",".$i.",'".$_SESSION['theme']."')\">";
			} else {
			$table .= "<td>".makeElement('hkrealisasi_'.$param['numRow'].'_'.$i,'textnum',
				$row['hkrealisasi'],array('disabled'=>'disabled'))."</td>";
			$table .= "<td>".makeElement('hasilkerjarealisasi_'.$param['numRow'].'_'.$i,
				'textnum',$row['hasilkerjarealisasi'],array('disabled'=>'disabled'))."</td>";
			$table .= "<td>".makeElement('jumlahrealisasi_'.$param['numRow'].'_'.$i,'textnum',
				$row['jumlahrealisasi'],array('disabled'=>'disabled',
				'onchange'=>'this.value = _formatted(this)'))."</td>";
                        $table .= "<td>".makeElement('jjgkontanan_'.$param['numRow'].'_'.$i,'textnum',
				$row['jjgkontanan'],array('disabled'=>'disabled',
				'onchange'=>'this.value = _formatted(this)'))."</td>";
			$table .= "<td>&nbsp;&nbsp;<img id='btnPost_".$param['numRow'].'_'.$i."' class='zImgBtn' ";
			$table .= "src='images/".$_SESSION['theme']."/posted.png'>";
			}
			$table .= "</td>";
			$table .= "</tr>";
			$i++;
		}
		# New Row
		$table .= "<tr id='tr_".$param['numRow'].'_'.$i."' class='rowcontent'>";
		$table .= "<td>".makeElement('blokalokasi_'.$param['numRow'].'_'.$i,'select','',array(),$optBlok)."</td>";
		$table .= "<td>".makeElement('tanggal_'.$param['numRow'].'_'.$i,'date')."</td>";
		$table .= "<td>".makeElement('hkrealisasi_'.$param['numRow'].'_'.$i,'textnum',0)."</td>";
		$table .= "<td>".makeElement('hasilkerjarealisasi_'.$param['numRow'].'_'.$i,
			'textnum',0,array('onkeyup'=>"calJumlah(".$param['numRow'].",".$i.")"))."</td>";
		$table .= "<td>".makeElement('jumlahrealisasi_'.$param['numRow'].'_'.$i,'textnum',0,
			array('onchange'=>'this.value=remove_comma(this);this.value = _formatted(this)'))."</td>";
                $table .= "<td>".makeElement('jjgkontanan_'.$param['numRow'].'_'.$i,'textnum',0,
			array('onchange'=>'this.value=remove_comma(this);this.value = _formatted(this)'))."</td>";
		$table .= "<td><img id='btn_".$param['numRow'].'_'.$i."' class='zImgBtn' ";
		$table .= "src='images/".$_SESSION['theme']."/plus.png' ";
		$table .= "onclick=\"addData(".$param['numRow'].",".$i.",'".$_SESSION['theme']."')\">&nbsp;";
		$table .= "<img id='btnDel_".$param['numRow'].'_'.$i."' class='zImgBtn' ";
		$table .= "src='images/".$_SESSION['theme']."/delete.png' style='display:none'";
		$table .= "onclick='deleteData(".$param['numRow'].",".$i.")'>&nbsp;";
		$table .= "<img id='btnPost_".$param['numRow'].'_'.$i."' class='zImgBtn' ";
		$table .= "src='images/".$_SESSION['theme']."/posting.png' ";
		$table .= "onclick=\"postingData(".$param['numRow'].",".$i.",'".$_SESSION['theme']."')\" style='display:none'>";
		$table .= "</td>";
		$table .= "</tr>";
		$i++;
		$table .= "</tbody>";
		$table .= "</table>";
		
		echo $table;
/*
echo "<pre>";
print_r($optBlok);
echo "</pre>";

*/		break;
    case 'add':
		$data = $param;
		unset($data['numRow1']);
		unset($data['divisi']);
		unset($data['blokalokasi']);
		unset($data['numRow2']);
		unset($data['tanggalSpk']);
		$data['kodeblok'] = $param['blokalokasi'];
		$data['posting'] = '0';
		$data['statusjurnal'] = '0';
		$data['blokspkdt'] = $param['kodeblok'];
		
		// Validasi Realisasi harus setelah atau sama dengan tanggal SPK
		if(tanggalsystem($param['tanggal'])<tanggalsystem($param['tanggalSpk'])) {
			exit("Warning: Tanggal Realisasi tidak boleh sebelum tanggal SPK");
		}
                
		$data['jumlahrealisasi'] = str_replace(',','',$data['jumlahrealisasi']);
                $data['jjgkontanan'] = str_replace(',','',$data['jjgkontanan']);
		$dtCol=array('notransaksi', 'kodeblok', 'kodekegiatan', 'tanggal', 'hasilkerjarealisasi', 'hkrealisasi', 'jumlahrealisasi', 'jjgkontanan', 'posting', 'statusjurnal', 'blokspkdt');
		# Options
/*
		$optBlok = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
			"induk='".$param['divisi']."' or kodeorganisasi='".$param['divisi']."'");

*/
		// $str_blok="SELECT b.kodeorganisasi as kodeorg, b.namaorganisasi as namaorg FROM ".$dbname.".setup_blok a LEFT JOIN ".$dbname.".organisasi b 
                     // ON a.kodeorg = b.kodeorganisasi 
                     // WHERE a.luasareaproduktif >0 and b.kodeorganisasi like '".substr($param['divisi'],0,4)."%' 
                     // and length(b.kodeorganisasi)>6";
               // $res_blok=mysql_query($str_blok);
               // while($bar=mysql_fetch_object($res_blok))
                // {
                  // $optBlok[$bar->kodeorg]=$bar->namaorg;
                // }
		$optBlok = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
			"induk='".$param['divisi']."' or kodeorganisasi like '".$param['divisi']."%'");
                #khusus jika project
			   if(substr($param['divisi'],0,2)=='AK' or substr($param['divisi'],0,2)=='PB'){
				   $optBlok = makeOption($dbname,'project','kode,nama',"kode='".$param['divisi']."' and posting=0");
			   }    		
		# Empty Data
//                echo"<pre>";
//                print_r($data);
//                echo"</pre>";
//                exit("Error:masuk");
		foreach($data as $cont) {
			if($cont=='') {
			echo 'Warning : Data tidak boleh ada yang kosong';
			exit;
			}
		}
		
		# Convert Tanggal
		$data['tanggal'] = tanggalsystem($data['tanggal']);
		
		$query = insertQuery($dbname,'log_baspk',$data,$dtCol);
                //exit("Error:".$query);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		} else {
			# Prepare New
			$i = $param['numRow2']+1;
			$row = "<td>".makeElement('blokalokasi_'.$param['numRow1'].'_'.$i,'select','',array(),$optBlok)."</td>";
			$row .= "<td>".makeElement('tanggal_'.$param['numRow1'].'_'.$i,'date')."</td>";
			$row .= "<td>".makeElement('hkrealisasi_'.$param['numRow1'].'_'.$i,'textnum',0)."</td>";
			$row .= "<td>".makeElement('hasilkerjarealisasi_'.$param['numRow1'].'_'.$i,
			'textnum',0,array('onkeyup'=>"calJumlah(".$param['numRow1'].",".$i.")"))."</td>";
			$row .= "<td>".makeElement('jumlahrealisasi_'.$param['numRow1'].'_'.$i,'textnum',0,
			array('onchange'=>'this.value=remove_comma(this);this.value = _formatted(this)'))."</td>";
                        $row .= "<td>".makeElement('jjgkontanan_'.$param['numRow1'].'_'.$i,'textnum',0,
			array('onchange'=>'this.value=remove_comma(this);this.value = _formatted(this)'))."</td>";
			$row .= "<td><img id='btn_".$param['numRow1']."_".$i."' class='zImgBtn' ";
			$row .= "src='images/".$_SESSION['theme']."/plus.png' ";
			$row .= "onclick=\"addData(".$param['numRow1'].",".$i.",'".$_SESSION['theme']."')\">&nbsp;";
			$row .= "<img id='btnDel_".$param['numRow1'].'_'.$i."' class='zImgBtn' ";
			$row .= "src='images/".$_SESSION['theme']."/delete.png' style='display:none'";
			$row .= "onclick='deleteData(".$param['numRow1'].",".$i.")'>&nbsp;";
			$row .= "<img id='btnPost_".$param['numRow1'].'_'.$i."' class='zImgBtn' ";
			$row .= "src='images/".$_SESSION['theme']."/posting.png' ";
			$row .= "onclick=\"postingData(".$param['numRow1'].",".$i.",'".$_SESSION['theme']."')\" style='display:none'>";
			$row .= "</td>";
			
			echo $row;
		}
		break;
    case 'edit':
		$data = $param;
		unset($data['notransaksi']);
		unset($data['kodeblok']);
		unset($data['blokalokasi']);
		unset($data['kodekegiatan']);
		unset($data['tanggal']);
		unset($data['tanggalSpk']);
		unset($data['numRow1']);
		unset($data['numRow2']);
		$data['jumlahrealisasi'] = str_replace(',','',$data['jumlahrealisasi']);
                $data['jjgkontanan'] = str_replace(',','',$data['jjgkontanan']);
		
		// Validasi Realisasi harus setelah atau sama dengan tanggal SPK
		if(tanggalsystem($param['tanggal'])<tanggalsystem($param['tanggalSpk'])) {
			exit("Warning: Tanggal Realisasi tidak boleh sebelum tanggal SPK");
		}
		
		# Empty Data
		foreach($data as $cont) {
			if($cont=='') {
			echo 'Warning : Data tidak boleh ada yang kosong';
			exit;
			}
		}
		
		# Convert Tanggal
		$param['tanggal'] = tanggalsystem($param['tanggal']);
		
		$where = "notransaksi='".$param['notransaksi'].
			"' and kodeblok='".$param['blokalokasi'].
			"' and kodekegiatan='".$param['kodekegiatan'].
			"' and tanggal='".$param['tanggal'].
			"' and blokspkdt='".$param['kodeblok']."'";
		$query = updateQuery($dbname,'log_baspk',$data,$where);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		break;
    case 'delete':
	# Convert Tanggal
	$param['tanggal'] = tanggalsystem($param['tanggal']);
	$where = "notransaksi='".$param['notransaksi'].
	    "' and kodeblok='".$param['blokalokasi'].
	    "' and kodekegiatan='".$param['kodekegiatan'].
	    "' and tanggal='".$param['tanggal'].
		"' and blokspkdt='".$param['kodeblok']."'";
	$query = "delete from `".$dbname."`.`log_baspk` where ".$where;
	
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	break;
    default:
	break;
}
?>