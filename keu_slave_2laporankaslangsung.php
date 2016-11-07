<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

if(isset($_POST)){
	$param=$_POST;
}
if($_GET['proses']=='excel'){
	$param=$_GET;
}else{
    $param['proses']=$_GET['proses'];
}
if(($param['periode']=='')||($param['kdUnit']=='')){
    exit("error: Field can't empty");
}
$bgDr="class=rowheader";
$grs=0;
if($param['proses']=='excel'){
    $bgDr="bgcolor='#DEDEDE'";
    $grs=1;
}
$optNamaAkun=  makeOption($dbname, 'keu_5akun', 'noakun,namaakun');
$optAkunBrg=  makeOption($dbname, 'log_5klbarang', 'kode,noakun');
$dtKode=array("KK"=>"KK","KM"=>"KM","BK"=>"BK","BM"=>"BM");
$tab.="<table cellpadding=1 cellspacing=1 border='".$grs."' class=sortable>";
$tab.="<thead><tr align=center ".$bgDr." >";
$tab.="<td>No.</td>";
$tab.="<td>".$_SESSION['lang']['namaakun']."</td>";
$tab.="<td>".$_SESSION['lang']['keterangan']."</td>";
foreach($dtKode as $lstKode){
    $tab.="<td>".$lstKode."</td>";
}
$tab.="</tr><tbody>";
$sData="select a.keterangandisplay as keterangan,b.keterangan2 as nopo,b.noakun as noakun,b.kode as kode,sum(b.jumlah) as jumlah,b.kodeorg as kodeorg,c.tanggal"
        . " from ".$dbname.".keu_kasbankdt b 
        left join ".$dbname.".keu_kasbankht c on b.notransaksi=c.notransaksi
        left join ".$dbname.".keu_5mesinlaporandt a on a.noakundisplay=b.noakun 
        where c.tanggal like '".$param['periode']."%' and b.kodeorg like '".$param['kdUnit']."%' group by noakun";
 //echo $sData;
// and c.posting=1
$qData=mysql_query($sData) or die(mysql_error($conn));
while($rData=mysql_fetch_assoc($qData)){
    if(($rData['jumlah']!=0)||($rData['jumlah']!='')){
        //$isiDt[$rData['noakun'].$rData['tanggal'].$rData['kode']]=$rData['jumlah'];
        //$ketDt[$rData['noakun'].$rData['tanggal']]=$rData['keterangan'];
        $isiDt[$rData['noakun'].$rData['kode']]+=$rData['jumlah'];
        $ketDt[$rData['noakun']]=$rData['keterangan'];
        $dtNoakun[$rData['noakun']]=$rData['noakun'];
        $dtKode[$rData['kode']]=$rData['kode'];
        $dtTgl[$rData['tanggal']]=$rData['tanggal'];
        //$dtPo[$rData['noakun'].$rData['tanggal']]=$rData['nopo'];
        $dtPo[$rData['noakun']]=$rData['nopo'];
    }
}
$rowData=count($isiDt);
if($rowData!=0){
foreach($dtNoakun as $lstAkun){
    $no+=1;
        //foreach($dtTgl as $lstTgl){
        $tab.="<tr class=rowcontent><td>".$no."</td>";
        $tab.="<td>".$optNamaAkun[$lstAkun]."</td>";
            if(($lstAkun=='2111101')||($lstAkun=='2111101')){
                $sBrg="select distinct left(kodebarang,3) as klmpkbrg from ".$dbname.".log_podt where nopo='".$dtPo[$lstAkun.$lstTgl]."'";
                $qBrg=mysql_query($sBrg) or die(mysql_error($conn));
                $rBrg=  mysql_fetch_assoc($qBrg);
                if($rBrg['klmpkbrg']==''){
                    $tab.="<td>Manul PO ".$dtPo[$lstAkun.$lstTgl]."</td>";
                }else{
                    $tab.="<td>".$optNamaAkun[$optAkunBrg[$rBrg[klmpkbrg]]]."</td>";
                }
            }elseif($lstAkun=='2111201'){
                $sKont="select distinct keterangan from ".$dbname.".log_spkht where notransaksi='".$dtPo[$lstAkun.$lstTgl]."'";
                $qKont=  mysql_query($sKont) or die(mysql_error($conn));
                $rKont=  mysql_fetch_assoc($qKont);
                $tab.="<td>".$rKont['keterangan']."</td>";
            }else{
                $tab.="<td>".$ketDt[$lstAkun.$lstTgl]."</td>";
            }
            foreach($dtKode as $lsKode){
                $tab.="<td align=right>".number_format($isiDt[$lstAkun.$lstTgl.$lsKode],0)."</td>";
            }
             $tab.="</tr>";
        //}
   
}
}else{
    $tab.="<tr class=rowcontent><td colspan=6>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
}
$tab.="</tbody></table>";
        
switch($param['proses']){
	
	case'preview':
	echo $tab;
	break;
	case'excel':
        //echo "warning:".$strx;
        //=================================================
        $tab.="Print Time:".date('Y-m-d H:i:s')." <br />By:".$_SESSION['empl']['name'];	
        $dte=date("YmdHms");
        $nop_="Laporan_ArusKasLangsung_".$dte;
        if(strlen($tab)>0){
                if ($handle = opendir('tempExcel')) {
                        while (false !== ($file = readdir($handle))) {
                        if ($file != "." && $file != "..") {
                                @unlink('tempExcel/'.$file);
                        }
                        }	
                        closedir($handle);
                }
                $handle=fopen("tempExcel/".$nop_.".xls",'w');
                if(!fwrite($handle,$tab))
                {
                        echo "<script language=javascript1.2>
                        parent.window.alert('Can't convert to excel format');
                        </script>";
                        exit;
                }
                else
                {
                        echo "<script language=javascript1.2>
                        window.location='tempExcel/".$nop_.".xls';
                        </script>";
                }
                closedir($handle);
        }     
//        $nop_="Laporan_ArusKasLangsung_".$dte;
//        $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
//        gzwrite($gztralala, $tab);
//        gzclose($gztralala);
//        echo "<script language=javascript1.2>
//        window.location='tempExcel/".$nop_.".xls.gz';
//        </script>";
			
	break;
	default:
	break;
}
?>