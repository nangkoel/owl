<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');
?>
<link rel="stylesheet" type="text/css" href="style/generic.css">
<script language=javascript src='js/generic.js'></script>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/keu_vp.js'></script>
<?php
$proses = $_GET['proses'];
$param = $_POST;

switch($proses) {
	
	case 'po':
	
		$tipeTugas=$_SESSION['empl']['tipelokasitugas'];
		$ptTugas=$_SESSION['empl']['kodeorganisasi'];
		$lokasiTugas=$_SESSION['empl']['lokasitugas'];
		$scek="select distinct jumlah,noinv,noakun from ".$dbname.".keu_vpdt a "
                    . "left join ".$dbname.".keu_vp_inv b on a.novp=b.novp "
                    . "left join ".$dbname.".keu_vpht c on a.novp=c.novp "
                    . "where substr(a.novp,2,4)='".$lokasiTugas."' and posting=1";
                //echo $scek;
                $qCek=  mysql_query($scek) or die(mysql_error($conn));
                while($rCek=  mysql_fetch_assoc($qCek)){
                    if($rCek['noakun']=='2111103'){
                        $dtRupiah[$rCek['noinv']]+=$rCek['jumlah'];
                    }
                }
        
		if($tipeTugas=='HOLDING')
		{
			$filterPo="and kodeorg='".$ptTugas."'";
			$filterLain="and kodeorg in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') ";
		}
		else
		{
                    if ($param['po']!=''){
                        $filterPo="and nopo like '%".$param['po']."%'";
                    } else {
                        $filterPo="and nopo like '%".$lokasiTugas."%'";
                    }
			
			//$filterLain="and lokalpusat=1";
		}
		switch($param['tipe']) {
			case 'po':
				$title = $_SESSION['lang']['nopo'];
				$query = selectQuery($dbname,'keu_tagihanht',"'PO',nopo,kodeorg,noinvoice","nopo like '%".$param['po']."%' and posting=1 and tipeinvoice='p' ");
				//$query = selectQuery($dbname,'log_poht',"'PO',nopo,kodeorg","nopo like '%".$param['po']."%' ".$filterPo." and stat_release=1");
				break;
			case 'k':
				$title = $_SESSION['lang']['kontrak'];
				$query = selectQuery($dbname,'keu_tagihanht',"'Kontrak',nopo,kodeorg,noinvoice","nopo like '%".$param['po']."%' and posting=1 and tipeinvoice='k' ");				
			break;
				
			case 'sj':
				$title = $_SESSION['lang']['nosj'];
				$query = selectQuery($dbname,'keu_tagihanht',"'Surat Jalan',nopo,kodeorg,noinvoice","nopo like '%".$param['po']."%' and posting=1  and tipeinvoice='s' ");				
			break;
				
			case 'ns':
				$title = $_SESSION['lang']['nokonosemen'];
				$query = selectQuery($dbname,'keu_tagihanht',"'Konosemen',nopo,kodeorg,noinvoice","nopo like '%".$param['po']."%' and posting=1  and tipeinvoice='n'");
				break;
				
            case 'ot':
			
				$title = $_SESSION['lang']['lain'];
				$query = selectQuery($dbname,'keu_tagihanht',"'Other',nopo,kodeorg,noinvoice","nopo like '%".$param['po']."%' and posting=1  and tipeinvoice='o'");
                       // exit("error".$query);
		        break;
		}
            if($param['tipe']=='po'){
                    $sCek="select sum(jumlahpesan-jumlahterima) as selisih,kodebarang,nopo,nopp,jumlahpesan,jumlahterima from ".$dbname.".log_po_terima_vw
                       where kodeorg='".$_SESSION['org']['kodeorganisasi']."' ".$filterPo." ".$filterLain."  group by nopo,nopp,kodebarang order by nopo asc";
                      //echo $sCek;
                    $qCek=mysql_query($sCek) or die(mysql_error($conn));
                    while($rCek=  mysql_fetch_assoc($qCek)){
                            if($nomoPo!=$rCek['nopo']){
                                $nomoPo=$rCek['nopo'];
                                $sJmlhBrg="select count(kodebarang) as jmlbrg from ".$dbname.".log_podt where nopo='".$rCek['nopo']."'";
                                $qJmlBrg=mysql_query($sJmlhBrg) or die(mysql_error($conn));
                                $rJmlBrg=mysql_fetch_assoc($qJmlBrg);
                                $totBrg[$nomoPo]=$rJmlBrg['jmlbrg'];
                            }
                            $whrdt=" nopo='".$rCek['nopo']."' and kodebarang='".$rCek['kodebarang']."' and nopp='".$rCek['nopp']."'";
                            $optSatuan=  makeOption($dbname, 'log_podt', 'kodebarang,satuan',$whrdt);
                            $scekst="select distinct satuankonversi,jumlah from ".$dbname.".log_5stkonversi "
                                   . "where kodebarang='".$rCek['kodebarang']."' and satuankonversi='".$optSatuan[$rCek['kodebarang']]."'";
                            $qcekst=  mysql_query($scekst) or die(mysql_error($conn));
                            $rcekst=  mysql_fetch_assoc($qcekst);
                            $selisih=$rCek['selisih'];
                            if($rcekst['jumlah']!=''){
                                $selisih=($rCek['jumlahpesan']/$rcekst['jumlah'])-$rCek['jumlahterima'];
                            }
                            $scekretur="select sum(jumlah) as jumlahretur from ".$dbname.".log_transaksi_vw where post=1 and tipetransaksi=6 and ".$whrdt;
                            $qcekretur=  mysql_query($scekretur) or die(mysql_error($conn));
                            $rcekretur=  mysql_fetch_assoc($qcekretur);
                            if($rcekretur['jumlahretur']!=''){
                                $selisih=$rCek['jumlahterima']-$rCek['jumlahpesan']-$rcekretur['jumlahretur'];
                            }
                            if($selisih==0){
                                $brgCompr[$rCek['nopo']]+=1;
                            }
                    }
                }
                //echo $query;
                $data = fetchData($query);
		
		$page = '';
		$page .= "<div style=width:750px;height:320px;overflow:auto;><table class=sortable cellspacing=1  border=0>";
		$page .= "<thead><tr class=rowheader>";
		$page .= "<td>".$_SESSION['lang']['tipe']."</td>";
		$page .= "<td>".$title."</td><td>".$_SESSION['lang']['kodeorganisasi']."</td><td>".$_SESSION['lang']['noinvoice']."</td></tr></thead>";
		$page .= "<tbody>";
		foreach($data as $key=>$row) {
                    $cekjumlahaja=$dtRupiah[$row['noinvoice']];
                    if($cekjumlahaja!=0){
                        $sSumData="select sum(nilaiinvoice+nilaippn) as jmlinvo from ".$dbname.".keu_tagihanht where noinvoice='".$row['noinvoice']."'";
                        $qSumdata=  mysql_query($sSumData) or die(mysql_error($conn));
                        $rSumdata=  mysql_fetch_assoc($qSumdata);
                        $cekjumlahaja=$rSumdata['jmlinvo']-$dtRupiah[$row['noinvoice']];
                    }
                    //if(($cekjumlahaja!='0')&&($cekjumlahaja>'0')){
                    if(($cekjumlahaja!='0')){
			$page .= "<tr id='t_po_".$key."'  style='cursor:pointer'
                nopo = '".$row['nopo']."'
				tipe = '".$param['tipe']."'
				kodeorg = '".$row['kodeorg']."'";
                        if($param['tipe']=='po'){
                            $at=" bgcolor=red title='The item in this PO is not fully accepted'";
                            if($brgCompr[$row['nopo']]==$totBrg[$row['nopo']]){        
                                $page.="onclick='findInvoice(this)'";
                                $at=" class=rowcontent";
                            }
                            $page.=" ".$at.">";
                        }else{
                                $page.="onclick='findInvoice(this)' class=rowcontent >";
                        }
		
			foreach($row as $attr=>$val) {
                            
				$page .= "<td id='t_po_".$key."_".$attr."'>".$val."</td>";
                           
			}
			$page .= "</tr>";
                   }
		}
		$page .= "</tbody>";
		$page .= "</table></div>";
		break;
	case 'invoice':
        $query = selectQuery($dbname,'keu_tagihanht',"*","nopo='".$param['po']."'");
		$data = fetchData($query);
        $optSupp = makeOption($dbname,'log_5supplier','supplierid,namasupplier');
		
		switch($param['tipe']) {
			case 'po':
				$title = $_SESSION['lang']['nopo'];
				break;
			case 'k':
				$title = $_SESSION['lang']['kontrak'];
				break;
			case 'sj':
				$title = $_SESSION['lang']['nosj'];
				break;
			case 'ns':
				$title = $_SESSION['lang']['nokonosemen'];
				break;
                        case 'ot':
				$title = $_SESSION['lang']['lainnya'];
		        break;
		}
		
		$page = "<div>".$title." : <span id=t_inv_nopo>".$param['po']."</span></div>";
		$page .= "<table class=sortable cellspacing=1  border=0>";
        
		$page .= "<thead><tr class=rowheader>";
        $page .= "<td><button class=mybutton onclick='selAll()'>Select All</button></td>";
        $page .= "<td>".$_SESSION['lang']['noinvoice']."</td>";
        $page .= "<td>".$_SESSION['lang']['noinvoice']." ".$_SESSION['lang']['supplier']."</td>";
        $page .= "<td>".$_SESSION['lang']['supplier']."</td>";
        $page .= "<td>".$_SESSION['lang']['nilaiinvoice']."</td>";
        $page .= "<td>".$_SESSION['lang']['ppn']."</td>";
        $page .= "</tr></thead>";
		
        $page .= "<tbody id='t_inv_body'>";
		foreach($data as $key=>$row) {
                    $sCek="select sum(nilaiinvoice+nilaippn) as nilInv from ".$dbname.".keu_tagihanht where noinvoice='".$row['noinvoice']."'";
                    $qCek=  mysql_query($sCek) or die(mysql_error($conn));
                    $rCek=  mysql_fetch_assoc($qCek);
                    
                    $sCekVp="select sum(jumlah) as nilVp from ".$dbname.".keu_vpdt a left join ".$dbname.".keu_vp_inv b "
                           . " on a.novp=b.novp where b.noinv='".$row['noinvoice']."' and noakun='2111103'";
                    $qCekVp=  mysql_query($sCekVp) or die(mysql_error($conn));
                    $rCekVp=  mysql_fetch_assoc($qCekVp);
                    if($rCekVp['nilVp']<$rCek['nilInv']){
                        $page .= "<tr id='t_inv_".$key."' class=rowcontent style='cursor:pointer'>";
                        $page .= "<td id='t_check_".$key."'>".
                        makeElement('el_inv_'.$key,'checkbox')."</td>";
                        $page .= "<td id='t_noinvoice_".$key."'>".$row['noinvoice']."</td>";
                        $page .= "<td id='t_noinvoicesupplier_".$key."'>".$row['noinvoicesupplier']."</td>";
                        $page .= "<td id='t_kodesupplier_".$key."'>".$optSupp[$row['kodesupplier']]."</td>";
                        $page .= "<td id='t_nilaiinvoice_".$key."' align=right value='".$row['nilaiinvoice']."'>".number_format($row['nilaiinvoice'],2)."</td>";
                        $page .= "<td id='t_nilaippn_".$key."' value='".$row['nilaippn']."'>".number_format($row['nilaippn'],2)."</td>";
                        $page .= "</tr>";
                    }else{
                        $page .= "<tr id='t_inv_".$key."' class=rowcontent style='cursor:pointer'>";
                        $page .= "<td id='t_check_".$key."'>".
                        makeElement('el_inv_'.$key,'checkbox','',array('disabled'=>true))."</td>";
                        $page .= "<td id='t_noinvoice_".$key."'>".$row['noinvoice']."</td>";
                        $page .= "<td id='t_noinvoicesupplier_".$key."'>".$row['noinvoicesupplier']."</td>";
                        $page .= "<td id='t_kodesupplier_".$key."'>".$optSupp[$row['kodesupplier']]."</td>";
                        $page .= "<td id='t_nilaiinvoice_".$key."' align=right value='".$row['nilaiinvoice']."'>".number_format($row['nilaiinvoice'],2)."</td>";
                        $page .= "<td id='t_nilaippn_".$key."' value='".$row['nilaippn']."'>".number_format($row['nilaippn'],2)."</td>";
                        $page .= "</tr>";
                    }
		}
		$page .= "</tbody>";
		$page .= "</table>";
        $page .= makeElement('t_inv_saveBtn','btn',$_SESSION['lang']['save'],array('onclick'=>"setPoInv()"));
		break;
	default;
}

echo $page;
?>