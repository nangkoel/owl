<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/biReport.php');
include_once('lib/zPdfMaster.php');
include_once('lib/terbilang.php');

$otNmAkun=makeOption($dbname, 'keu_5akun', 'noakun,namaakun');

$param=$_POST;
$level = $_GET['level'];
if(isset($_GET['mode'])) {
    $mode = $_GET['mode'];
} else {
    $mode = 'preview';
}
if($mode=='pdf') {
    $param = $_GET;
    unset($param['mode']);
    unset($param['level']);
} else {
    $param = $_POST;
}
# Validasi Periode
$periode1 = $param['periode_from'];
$periode2 = $param['periode_until'];
#1. Empty
if($param['proses']!='getNoakun'){
    if($periode1=='' or $periode2=='') {
        echo 'Warning : Transaction period required';
        exit;
    }
}
#2. Range Terbalik
if(tanggalsystem($periode1)>tanggalsystem($periode2)) {
    $tmp = $periode1;
    $periode1 = $periode2;
    $periode2 = $tmp;
}
#3. akun terbalik--tambahan jamhari
if($param['noakun']>$param['noakunsmp']){
    $tmp=$param['noakunsmp'];
    $param['noakunsmp']=$param['noakun'];
    $param['noakun']=$tmp;
}
if($param['noakunsmp']==''){
    $param['noakunsmp']=$param['noakun'];
}

switch($level){
    case'0':
        if(isset($param['kodeorg']))
            $kodeorg=$param['kodeorg'];
        else
            $kodeorg=$_SESSION['empl']['lokasitugas'];
        $wherd="kasbank=1 and (pemilik='HOLDING' or pemilik='".$param['kodeorg']."' or pemilik='GLOBAL')";
        $cols = "noakun,notransaksi,kodeorg,tanggalposting,tipetransaksi,jumlah,keterangan";
        $where = "tanggalposting>='".tanggalsystem($periode1)."' and tanggalposting<='".
            tanggalsystem($periode2)."' and noakun in (select distinct noakun from  ".$dbname.".keu_5akun where ".$wherd.") and ".
            "kodeorg='".$kodeorg."' and posting=1 and cgttu='MCM'";  //and cgttu='MCM'
        $sQue="select ".$cols." from ".$dbname.".keu_kasbankht where ".$where." order by noakun,tanggal asc";
        //exit("error:".$sQue);
        $query = mysql_query($sQue) or die(mysql_error($conn));
        while($res=  mysql_fetch_assoc($query)){
            $resH[]=$res;
        }
        
	$data = array();
        $varTemp="";
	foreach($resH as $key=>$row) {
            if($row['noakun']!=$varTemp){
                $nodt=1;
                $varTemp=$row['noakun'];
                $lstZkey[$row['noakun']]=$nodt;
            }else{
                $nodt+=1;
                $lstZkey[$row['noakun']]=$nodt;
            }
	    $data[$row['noakun']][$nodt] = array(
		'no'=>$nodt,
                'notransaksi'=>$row['notransaksi'],
                'kodeorg'=>$row['kodeorg'],
                'tipetransaksi'=>$row['tipetransaksi'],
                'tanggalposting'=>tanggalnormal($row['tanggalposting']),
		'keterangan'=>$row['keterangan'],
		'jumlah'=>$row['jumlah'],
		'matauang'=>$row['matauang']
	    );
            if(($row['jumlah']!='')||($row['jumlah']!=0)){
                $sort_noakun[$row['noakun']]=$row['noakun'];
                $grndTotal+=$row['jumlah'];
            }
            
	}
        
        array_multisort($sort_noakun, SORT_ASC);
        # Report Gen
        $theCols = array(
                          'No',
                          $_SESSION['lang']['notransaksi'],
			  $_SESSION['lang']['unit'],$_SESSION['lang']['tanggalposting'],
			  $_SESSION['lang']['keterangan'],$_SESSION['lang']['tipe'],
			  $_SESSION['lang']['jumlah']
        );
        $align = explode(',','C,L,C,L,C,R,C');
	
	
    break;
}
if($param['pildt']==1)
{
    $brd=0;
    $bgclr="";
    $mode='totalAkun';
    if($_GET['mode']=='excel')
	{
        $mode="";
        $mode='totalAkun';
        $isiDari="cetakexcel";
        $brd=1;
        $bgclr="bgcolor:#DEDEDE";
    }
	elseif($_GET['mode']=='pdf')
	{  $mode="";
        $mode="cetakpdf";   
    }
}

   
switch($mode){
	
	
    default:
	# Redefine Align
	$alignPrev = array();
	foreach($align as $key=>$row) {
	    switch($row) {
		case 'L':
		    $alignPrev[$key] = 'left';
		    break;
		case 'R':
		    $alignPrev[$key] = 'right';
		    break;
		case 'C':
		    $alignPrev[$key] = 'center';
		    break;
		default:
	    }
	}
	
        $bgclr="";
	/** Mode Header **/
        if($mode=='excel') {
            $tab = strtoupper($_SESSION['lang']['kasharian'])." : ".$namagudang."<br>".
            strtoupper($_SESSION['lang']['noakun'])." : ".$param['noakun']."<br>".
            strtoupper($_SESSION['lang']['periode'])." : ".$periode1." s/d ".$periode2.
                $brd="border=1";
                $bgclr="bgcolor:#DEDEDE";
        } 
        $tab .= "<table id='kasharian' class='sortable' ".$brd.">";
        $tab .= "<thead><tr class='rowheader' ".$bgclr.">";
	/** Generate Table **/
        foreach($theCols as $head) {
           $tab .= "<td>".$head."</td>";
        }
        $tab .= "</tr></thead>";
        $tab .= "<tbody>";
        foreach($sort_noakun as $lstNoakun){
            if($erts!=$lstNoakun){
                # Saldo Awal
                $tab .= "<tr class='rowcontent'>";
                $tab .= "<td colspan=5><b>".$lstNoakun." ".$otNmAkun[$lstNoakun]."</b></td>";
                $tab .= "<td></td><td></td>";
                $tab .="</tr>";
                $erts=$lstNoakun;
            }
            $jumlah[$lstNoakun]=0;
            for($key=1;$key<=$lstZkey[$lstNoakun];$key++) {
                $tab .= "<tr class='rowcontent'>";
                $tab .= "<td ".$alignPrev[$key].">".$data[$lstNoakun][$key][no]."</td>";
                $tab .= "<td ".$alignPrev[$key].">".$data[$lstNoakun][$key][notransaksi]."</td>";
                $tab .= "<td ".$alignPrev[$key].">".$data[$lstNoakun][$key][kodeorg]."</td>";
                $tab .= "<td ".$alignPrev[$key].">".$data[$lstNoakun][$key][tanggalposting]."</td>";
                $tab .= "<td ".$alignPrev[$key].">".$data[$lstNoakun][$key][keterangan]."</td>";
                $tab .= "<td ".$alignPrev[$key].">".$data[$lstNoakun][$key][tipetransaksi]."</td>";
                $tab .= "<td ".$alignPrev[$key]." align=right>".number_format($data[$lstNoakun][$key][jumlah],0)."</td>";
                $tab .="</tr>";
                $jumlah[$lstNoakun]+=$data[$lstNoakun][$key][jumlah];
            }
            # Jumlah
            $tab .= "<tr class='rowcontent'>";
            $tab .= "<td colspan=6 align='right'>".$_SESSION['lang']['jumlah']." ".$erts."</td>";
            $tab .= "<td align='right'>".number_format($jumlah[$lstNoakun],0)."</td>";
            $tab .= "</tr>";
        }
            # Saldo
            
        $tab .= "</tbody>";
        $tab .= "</table>";
        
        /** Output Type **/
        if($mode=='excel') {
            $stream = $tab;
            $nop_="KasHarian_".$kodeorg;
            if(strlen($stream)>0) {
                # Delete if exist
                if ($handle = opendir('tempExcel')) {
                    while (false !== ($file = readdir($handle))) {
                        if ($file != "." && $file != "..") {
                            @unlink('tempExcel/'.$file);
                        }
                    }	
                    closedir($handle);
                }
                
                # Write to File
                $handle=fopen("tempExcel/".$nop_.".xls",'w');
                if(!fwrite($handle,$stream)) {
                    echo "Error : Tidak bisa menulis ke format excel";
                    exit;
                } else {
                    echo $nop_;
                }
                fclose($handle);
            }
        } else {
            echo $tab;
        }
        break;
}



switch($param['proses']){
    case'getNoakun':
        $optNoakun.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $optTipe=makeOption($dbname, 'organisasi', 'kodeorganisasi,tipe');
        $wherd="kasbank=1 and (pemilik='".$param['kodeorg']."' or pemilik='GLOBAL')";
        if($optTipe[$param['kodeorg']]=='HOLDING'){
            $wherd="kasbank=1 and (pemilik='HOLDING' or pemilik='".$param['kodeorg']."' or pemilik='GLOBAL')";
        }
        $optAkun = makeOption($dbname,'keu_5akun','noakun,namaakun',$wherd,'noakun');
        foreach($optAkun as $row=>$lstAkun){
            $optNoakun.="<option value='".$row."'>".$lstAkun."</option>";
        }
        echo $optNoakun;
    break;
	
}
?>