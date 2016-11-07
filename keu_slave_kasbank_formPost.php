<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');

include_once('lib/terbilang.php');



$notrans=$_POST['notrans'];
$kodeorg=$_POST['kodeorg'];
$noakun=$_POST['noakun'];
$tipetransaksi=$_POST['tipetransaksi'];
$method=$_POST['method'];
$numRow=$_POST['numRow'];

switch($method)
{
	case'getFormPost':
		$cols = array();	
			
		$x="select * from ".$dbname.".keu_kasbankht where notransaksi='".$notrans."'";
		$y=mysql_query($x) or die (mysql_error($x));
		$param=mysql_fetch_assoc($y);
			
		#=============================== Header =======================================
		$whereH = "notransaksi='".$param['notransaksi'].
			"' and kodeorg='".$param['kodeorg'].
			"' and noakun='".$param['noakun'].
			"' and tipetransaksi='".$param['tipetransaksi']."'";
		$queryH = selectQuery($dbname,'keu_kasbankht','*',$whereH);
		$resH = fetchData($queryH);
		
		//echo "<pre>";
		//print_r($resH);
		//echo "</pre>";
		
		# Get Nama Pembuat
		$userId = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',
			"karyawanid='".$resH[0]['userid']."'");
		# Get Nama Akun Hutang
		$namaakunhutang = makeOption($dbname,'keu_5akun','noakun,namaakun',
			"noakun='".$resH[0]['noakunhutang']."'");
		
		#=============================== Detail =======================================
		# Data
		$col1 = 'noakun,jumlah,noaruskas,matauang,kode,nik,keterangan2,kodesupplier';//lama
		
		$cols = array('nomor','noakun','namaakun','matauang','keterangan','kodesupplier','debet','kredit');
		//$col1 = 'noakun,jumlah,noaruskas,matauang,kode,hutangunit1';
		//$cols = array('nomor','noakun','namaakun','matauang','debet','kredit','hutangunit');
		$where = "notransaksi='".$param['notransaksi'].
			"' and kodeorg='".$param['kodeorg'].
			"' and noakun2a='".$param['noakun'].
			"' and tipetransaksi='".$param['tipetransaksi']."'";
		$query = selectQuery($dbname,'keu_kasbankdt',$col1,$where);
		//$query="select ".$col1." from ".$dbname.".keu_kasbankdt where ".$where." group by noakun ";
		$res = fetchData($query);
		
		$kary = $supp = array();
		foreach($res as $row) {
			if(!empty($row['nik'])) $kary[$row['nik']] = $row['nik'];
			if(!empty($row['kodesupplier'])) $supp[$row['kodesupplier']] = $row['kodesupplier'];
		}
		
		# Data Empty
		if(empty($res)) {
			echo 'Data Empty';
			exit;
		}
		
		# Options
		$whereAkun = "noakun in (";
		$whereAkun .= "'".$resH[0]['noakun']."'";
		$whereAkun .= ",'".$resH[0]['noakunhutang']."'"; // tambahin kamus nama akun hutangunit
		$whereKary = $whereSupp = "";
		foreach($res as $key=>$row) {
			if(!empty($whereKary)) $whereKary .= ",";
			if(!empty($whereSupp)) $whereSupp .= ",";
			$whereAkun .= ",'".$row['noakun']."'";
			$whereKary .= "'".$row['nik']."'";
			$whereSupp .= "'".$row['kodesupplier']."'";
		}
		$whereAkun .= ")";
		$optKary = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',"karyawanid in (".$whereKary.")");
		$optSupp = makeOption($dbname,'log_5supplier','supplierid,namasupplier',"supplierid in (".$whereSupp.")");
		$optAkun = makeOption($dbname,'keu_5akun','noakun,namaakun',$whereAkun);
		$optHutangUnit = array('0'=>'Tidak','1'=>'Ya');
		
		# Data Show
		$data = array();
		
		#================================ Prep Data ===================================
		# Total
		$totalDebet = 0;$totalKredit = 0;
		
		# Dari Header
		$i=1;
		$data[$i] = array(
			'nomor'=>$i,
			'noakun'=>$resH[0]['noakun'],
			'namaakun'=>$optAkun[$resH[0]['noakun']],
			'matauang'=>$resH[0]['matauang'],
			'-'=>'',
			'- '=>'',
			'debet'=>0,
			'kredit'=>0
		);
		//    'hutangunit'=>$optHutangUnit[$resH[0]['hutangunit']],
		if($param['tipetransaksi']=='M') {
			$data[$i]['debet'] = $resH[0]['jumlah'];
			$totalDebet += $resH[0]['jumlah'];
		} else {
			$data[$i]['kredit'] = $resH[0]['jumlah'];
			$totalKredit += $resH[0]['jumlah'];
		}
		$i++;
		
		# Dari Detail
		foreach($res as $row) {
			$data[$i] = array(
			'nomor'=>$i,
			'noakun'=>$row['noakun'],
			'namaakun'=>isset($optAkun[$row['noakun']])?$optAkun[$row['noakun']]:'',
			'matauang'=>$row['matauang'],
			'keterangan2'=>$row['keterangan2'],
			'kodesupplier'=>$row['kodesupplier'],
			'debet'=>0,
			'kredit'=>0
			);
		//	'hutangunit1'=>$optHutangUnit[$row['hutangunit1']]
			if($param['tipetransaksi']=='M' and $row['jumlah']>0) {
			$data[$i]['kredit'] = $row['jumlah'];
			$totalKredit += $row['jumlah'];
			}
			else if($param['tipetransaksi']=='K' and $row['jumlah']<0){
			$data[$i]['kredit'] = $row['jumlah']*-1;
			$totalKredit += $row['jumlah']*-1;        
			}
			else if($param['tipetransaksi']=='M' and $row['jumlah']<0){
			$data[$i]['debet'] = $row['jumlah']*-1;
			$totalDebet += $row['jumlah']*-1;        
			}    
			else {
			$data[$i]['debet'] = $row['jumlah'];
			$totalDebet += $row['jumlah'];
			}
			$i++;
		}
		
		// nyusun berdasarkan debet dulu, abis itu baru kredit. by dz
		if(!empty($data)) foreach($data as $c=>$key) {
			$sort_debet[] = $key['debet'];
			$sort_kredit[] = $key['kredit'];
		}
		
		// sort
		if(!empty($data))array_multisort($sort_debet, SORT_DESC, $sort_kredit, SORT_ASC, $data);
		
		$align = explode(",","R,R,L,L,R,R");
		$length = explode(",","7,12,35,10,18,18");
		//$align = explode(",","R,R,L,L,R,R,C");
		//$length = explode(",","7,12,35,10,13,13,10");
		$title = $_SESSION['lang']['kasbank'];
		$titleDetail = 'Detail';
	
        $tab.="<link rel=stylesheet type=text/css href=style/generic.css>";//style=\"height:275px;width:800px;overflow:scroll;\" //style='float:left;'
        $tab.="<fieldset style=\"height:200px;width:760px;overflow:scroll;\"><legend>".$titleDetail." ".$title."</legend>";
        $tab.="<table cellpadding=1 cellspacing=1 border=0 width=100% class=sortable><tbody class=rowcontent>";
        $tab.="<tr><td>".$_SESSION['lang']['kodeorganisasi']."</td><td> :</td><td> ".$_SESSION['empl']['lokasitugas']."</td></tr>";
        $tab.="<tr><td>".$_SESSION['lang']['notransaksi']."</td><td> :</td><td> ".$res[0]['kode']."/".$param['notransaksi']."</td></tr>";
        $tab.="<tr><td>".$_SESSION['lang']['cgttu']."</td><td> :</td><td> ".$resH[0]['cgttu']."</td></tr>";
        if ($resH[0]['matauang']=='IDR'){
            $tab.="<tr><td>".$_SESSION['lang']['terbilang']."</td><td> :</td><td> ".terbilang($resH[0]['jumlah'],2).
                ' rupiah'."</td></tr>";
        } else {
            $tab.="<tr><td>".$_SESSION['lang']['terbilang']."</td><td> :</td><td> ".terbilang($resH[0]['jumlah'],2).
                " ".$resH[0]['matauang']."</td></tr>";
        }
        if($resH[0]['hutangunit']==1){
            $tab.="<tr><td>".$_SESSION['lang']['hutangunit']."</td><td> :</td><td> ".'Unit payable Account '.$resH[0]['pemilikhutang'].' : '.$namaakunhutang[$resH[0]['noakunhutang']]."</td></tr>";            
        }
        $tab.="</tbody></table><br />";
       
            $tab.="<table cellpadding=1 cellspacing=1 border=0 width=100% class=sortable><thead><tr class=rowheader>";
            foreach($cols as $column) {
                $tab.="<td>".$_SESSION['lang'][$column]."</td>";
            }
            $tab.="</tr></thead><tbody class=rowcontent>";
        // nyusun ulang nomor setelah disort by debet. dz
            $nyomor=0;
            foreach($data as $key=>$row) {    
                $nyomor+=1;
                $tab.="<tr>";
                foreach($row as $key=>$cont) {
                    if($key=='nomor')
					{
                        $tab.="<td>".$nyomor."</td>";	
                    } 
					else  if($key=='kodesupplier') 
					{
						$tab.="<td>".$optSupp[$cont]."</td>";	
                    }
					else{
                        if($key=='debet' or $key=='kredit') {
                            $tab.="<td align=right>".number_format($cont,0)."</td>";
                        } 
						
						else {
                            $tab.="<td>".$cont."</td>";
                        }                    
                    }
                }
                $tab.="</tr>";
            }
        $tab.="<tr><td colspan=6 align=center>Total</td><td align=right>".number_format($totalDebet,0)."</td><td align=right>".number_format($totalKredit,0)."</td></tr>";
             $tab.="</tbody></table></fieldset> <br />";
      
			
			
						$tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable><tbody class=rowcontent>
							<tr>
								<td>".$_SESSION['lang']['nobayar']."</td> 
								<td>:</td>
						";
                                                if ($resH[0]['cgttu']=='MCM'){
                                                    $tab.="<td  colspan=2><input type=text id=nobayar disabled style=\"width:270px;\" value='(".$_SESSION['lang']['autoMCM'].")'></td>";
                                                } else {
                                                    $tab.="<td  colspan=2><input type=text id=nobayar onkeypress=\"return tanpa_kutip(event);\" class=myinputtext  style=\"width:150px;\"></td>";
                                                }
						$tab.="</tr>
							<tr>
								<td>".$_SESSION['lang']['tanggal']."</td> 
								<td>:</td>
								<td><input type=text class=myinputtext readonly  id=tglpost onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=\"width:150px;\"/></td>
								<td><button class=mybutton onclick=savePosting('".$notrans."','".$kodeorg."','".$noakun."','".$tipetransaksi."','".$numRow."')>Simpan</button></td>
							</tr>
							
							
						</table>
						";
						
						echo $tab;

		break;
		default;
}
?>
	