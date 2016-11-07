<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
if(isset($_POST['kodeorg2'])){
    $param=$_POST;
}else{
    $param=$_GET;
}
$tgl=explode("-",$param['tanggal2']);
$tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>";
$tab.="<tr>";
$tab.="<td>No.</td>";
$tab.="<td>".$_SESSION['lang']['produk']."</td>";
$tab.="<td>".$_SESSION['lang']['kodetangki']."</td>";
$tab.="<td>".$_SESSION['lang']['tanggalsounding']."</td>";
$tab.="<td>".$_SESSION['lang']['stock']." KG</td></tr></thead><tbody>";
#cpo awal

 #ambil sounding cpo terakhir
    $sCpo="select  * from ".$dbname.".pabrik_masukkeluartangki where kodeorg='".$param['kodeorg2']."' and left(tanggal,10)<='".tanggaldgnbar($param['tanggal2'])."'"
        . "and kodetangki='ST01'"
        . "order by tanggal desc limit 1";
   $qCpo=  mysql_query($sCpo) or die(mysql_error($conn));
   $rCpo=  mysql_fetch_assoc($qCpo);
        $no+=1;
        $tab.="<tr class=rowcontent>";
        $tab.="<td>".$no."</td>";
        $tab.="<td>CPO</td>";
        $tab.="<td>ST01</td>";
        $tab.="<td>".$rCpo['tanggal']."</td>";
        $tab.="<td align=right>".number_format($rCpo['kuantitas'],0)."</td></tr>";
        $sbTotCpo+=$rCpo['kuantitas'];
   $sCpo2="select  * from ".$dbname.".pabrik_masukkeluartangki where kodeorg='".$param['kodeorg2']."' and left(tanggal,10)<='".tanggaldgnbar($param['tanggal2'])."'"
        . "and kodetangki='ST02'"
        . "order by tanggal desc limit 1";
   $qCpo2=  mysql_query($sCpo2) or die(mysql_error($conn));
   $rCpo2=  mysql_fetch_assoc($qCpo2);
        $no+=1;
        $tab.="<tr class=rowcontent>";
        $tab.="<td>".$no."</td>";
        $tab.="<td>CPO</td>";
        $tab.="<td>ST02</td>";
        $tab.="<td>".$rCpo2['tanggal']."</td>";
        $tab.="<td align=right>".number_format($rCpo2['kuantitas'],0)."</td></tr>";
        $sbTotCpo+=$rCpo2['kuantitas'];
  $sCpo3="select  * from ".$dbname.".pabrik_masukkeluartangki where kodeorg='".$param['kodeorg2']."' and left(tanggal,10)<='".tanggaldgnbar($param['tanggal2'])."'"
        . "and kodetangki='ST03'"
        . "order by tanggal desc limit 1";
   $qCpo3=  mysql_query($sCpo3) or die(mysql_error($conn));
   $rCpo3=  mysql_fetch_assoc($qCpo3);
        $no+=1;
        $tab.="<tr class=rowcontent>";
        $tab.="<td>".$no."</td>";
        $tab.="<td>CPO</td>";
        $tab.="<td>ST03</td>";
        $tab.="<td>".$rCpo3['tanggal']."</td>";
        $tab.="<td align=right>".number_format($rCpo3['kuantitas'],0)."</td></tr>";       
        $sbTotCpo+=$rCpo3['kuantitas'];
  $sCpo4="select  * from ".$dbname.".pabrik_masukkeluartangki where kodeorg='".$param['kodeorg2']."' and left(tanggal,10)<='".tanggaldgnbar($param['tanggal2'])."'"
        . "and kodetangki='ST04'"
        . "order by tanggal desc limit 1";
   $qCpo4=  mysql_query($sCpo4) or die(mysql_error($conn));
   $rCpo4=  mysql_fetch_assoc($qCpo4);
        $no+=1;
        $tab.="<tr class=rowcontent>";
        $tab.="<td>".$no."</td>";
        $tab.="<td>CPO</td>";
        $tab.="<td>ST04</td>";
        $tab.="<td>".$rCpo4['tanggal']."</td>";
        $tab.="<td align=right>".number_format($rCpo4['kuantitas'],0)."</td></tr>";       
        $sbTotCpo+=$rCpo4['kuantitas'];
 $sCpo5="select  * from ".$dbname.".pabrik_masukkeluartangki where kodeorg='".$param['kodeorg2']."' and left(tanggal,10)<='".tanggaldgnbar($param['tanggal2'])."'"
        . "and kodetangki='ST05'"
        . "order by tanggal desc limit 1";
   $qCpo5=  mysql_query($sCpo5) or die(mysql_error($conn));
   $rCpo5=  mysql_fetch_assoc($qCpo5);
        $no+=1;
        $tab.="<tr class=rowcontent>";
        $tab.="<td>".$no."</td>";
        $tab.="<td>CPO</td>";
        $tab.="<td>ST05</td>";
        $tab.="<td>".$rCpo5['tanggal']."</td>";
        $tab.="<td align=right>".number_format($rCpo5['kuantitas'],0)."</td></tr>";       
        $sbTotCpo+=$rCpo5['kuantitas'];
    
    #total cpo
        $tab.="<tr class=rowcontent>";
        $tab.="<td colspan=4 align=center><b>".$_SESSION['lang']['total']."</b></td>";
        $tab.="<td align=right><b>".number_format($sbTotCpo,0)."</b></td></tr>";
        
    #cari pengiriman ke komaligon    
//    $tglMax="select max(tanggal) as tanggal from ".$dbname.".pabrik_masukkeluartangki "
//          . "where kodetangki in ('ST03','ST04','ST05') and kodeorg='".$param['kodeorg2']."' and left(tanggal,10)<='".tanggaldgnbar($param['tanggal2'])."'";
////    echo $tglMax;
//    $qtglMax=  mysql_query($tglMax) or die(mysql_error($conn));
//    $rTglMax=  mysql_fetch_assoc($qtglMax);
#    $tglAwal=explode("-",$param['tanggal2']);
#    $periode=$tglAwal[2]."-".$tglAwal[1]."-01";
#ambil tanggal terakhir sounding komaligon
if($rCpo3['tanggal']>$rCpo4['tanggal'])
	$tglpengiriman=$rCpo3['tanggal'];
else
	$tglpengiriman=$rCpo4['tanggal'];
	
if($rCpo5['tanggal']>$tglpengiriman)
	$tglpengiriman=$rCpo5['tanggal'];
	
    $sCpoKoma="select sum(beratbersih) as kuantitas from ".$dbname.".pabrik_timbangan 
              where kodebarang='40000007' and sloc='ST01' and left(tanggal,10)>='".substr($tglpengiriman,0,10).
              "' and left(tanggal,10)<='".tanggaldgnbar($param['tanggal2'])."' and millcode='".$param['kodeorg2']."'";
//   echo $sCpoKoma;
    $qCpoKoma=  mysql_query($sCpoKoma) or die(mysql_error($conn));
    $rCpoKoma=  mysql_fetch_assoc($qCpoKoma);
    $sCpoKoma2="select sum(beratbersih) as kuantitas from ".$dbname.".pabrik_timbangan 
              where kodebarang='40000007' and sloc='ST02' and left(tanggal,10)>='".substr($tglpengiriman,0,10).
              "' and left(tanggal,10)<='".tanggaldgnbar($param['tanggal2'])."' and millcode='".$param['kodeorg2']."'";
    $qCpoKoma2=  mysql_query($sCpoKoma2) or die(mysql_error($conn));
    $rCpoKoma2=  mysql_fetch_assoc($qCpoKoma2);
    $pengiriman=$rCpoKoma['kuantitas']+$rCpoKoma2['kuantitas'];
    $grndCpo=$sbTotCpo;
        $no+=1;
        $tab.="<tr class=rowcontent>";
        $tab.="<td>".$no."</td>";
        $tab.="<td colspan=3>Pengiriman CPO Ke Komaligon (Kg)</td>";
        $tab.="<td align=right>".number_format($pengiriman,0)."</td></tr>";
        $tab.="<tr class=rowcontent>";
        $tab.="<td colspan=4 align=center><b>".$_SESSION['lang']['total']." CPO ".$_SESSION['lang']['tanggal']." (Kg) ".tanggaldgnbar($param['tanggal2'])."</b></td>";
        $tab.="<td align=right><b>".number_format($grndCpo,0)."</b></td></tr>";
    #cpo ending
	
	
    #kernel
    $sKer="select  * from ".$dbname.".pabrik_masukkeluartangki where kodeorg='".$param['kodeorg2']."' 
	       and left(tanggal,10)<='".tanggaldgnbar($param['tanggal2'])."'"
        . "and kodetangki='BLK01'"
        . "order by tanggal desc limit 1";
    //echo $sKer;
    $qKer=  mysql_query($sKer) or die(mysql_error($conn));
    $rKer=  mysql_fetch_assoc($qKer);
        $no+=1;
        $tab.="<tr class=rowcontent>";
        $tab.="<td>".$no."</td>";
        $tab.="<td>KERNEL</td>";
        $tab.="<td>BLK01</td>";
        $tab.="<td>".$rKer['tanggal']."</td>";
        $tab.="<td align=right>".number_format($rKer['kuantitas'],0)."</td></tr>";
        $sbKer+=$rKer['kuantitas'];
    $sKer2="select  * from ".$dbname.".pabrik_masukkeluartangki where kodeorg='".$param['kodeorg2']."' 
	       and left(tanggal,10)<='".tanggaldgnbar($param['tanggal2'])."'"
        . "and kodetangki='BLK02'"
        . "order by tanggal desc limit 1";
    //echo $sKer;
    $qKer2=  mysql_query($sKer2) or die(mysql_error($conn));
    $rKer2=  mysql_fetch_assoc($qKer2);
        $no+=1;
        $tab.="<tr class=rowcontent>";
        $tab.="<td>".$no."</td>";
        $tab.="<td>KERNEL</td>";
        $tab.="<td>BLK02</td>";
        $tab.="<td>".$rKer2['tanggal']."</td>";
        $tab.="<td align=right>".number_format($rKer2['kuantitas'],0)."</td></tr>";
        $sbKer+=$rKer2['kuantitas'];
    $tab.="<tr class=rowcontent>";
    $tab.="<td colspan=4 align=center>".$_SESSION['lang']['total']."</td>";
    $tab.="<td align=right>".number_format($sbKer,0)."</td></tr>";
    #pengiriman kernel
//    $tglMax2="select   max(tanggal) as tanggal from ".$dbname.".pabrik_masukkeluartangki "
//          . "where kodetangki in ('BLK02') and kodeorg='".$param['kodeorg2']."' and left(tanggal,10)<='".tanggaldgnbar($param['tanggal2'])."'";
//    //echo $tglMax;
//    $qtglMax2=  mysql_query($tglMax2) or die(mysql_error($conn));
//    $rTglMax2=  mysql_fetch_assoc($qtglMax2);
    $sKerKrm="select sum(beratbersih) as kuantitas from ".$dbname.".pabrik_timbangan 
            where kodebarang='40000006'  and tanggal>='".substr($rKer2['tanggal'],0,10).
            "' and left(tanggal,10)<='".tanggaldgnbar($param['tanggal2'])."' and millcode='".$param['kodeorg2']."'";
//echo $sKerKrm;			
    $qKerKrm=  mysql_query($sKerKrm) or die(mysql_error($conn));
    $rKerKrm=  mysql_fetch_assoc($qKerKrm);
    $grndKer=$sbKer+$rKerKrm['kuantitas'];
        $no+=1;
        $tab.="<tr class=rowcontent>";
        $tab.="<td>".$no."</td>";
        $tab.="<td colspan=3>Pengiriman KERNEL Ke Komaligon (Kg)</td>";
        $tab.="<td align=right>".number_format($rKerKrm['kuantitas'],0)."</td></tr>";
        $tab.="<tr class=rowcontent>";
        $tab.="<td colspan=4 align=center><b>".$_SESSION['lang']['total']." KERNEL ".$_SESSION['lang']['tanggal']." (Kg) ".tanggaldgnbar($param['tanggal2'])."</b></td>";
        $tab.="<td align=right><b>".number_format($grndKer,0)."</b></td></tr>";
$tab.="</tbody></table>";
switch($proses)
{
	case'preview':
        
		echo $tab;
	break;
	
	case'excel':
            //<td align=right>".$_SESSION['lang']['cporendemen']." (%)</td>
            //<td align=right>".$_SESSION['lang']['kernelrendemen']." (%)</td>
	$periode=$_GET['periode'];
			$stream.="
			<table>
			<tr><td>".$_SESSION['lang']['laporanstok']." ".$kdPbrik." ".$kdTangki."</td></tr>
			<tr><td>".$_SESSION['lang']['periode']."</td><td>".$tampilperiode."</td></tr>
			<tr></tr>
			</table>
			<table border=1>
			<tr bgcolor=#DEDEDE>
			
		<td>".$_SESSION['lang']['kodeorg']."</td>
		<td>".$_SESSION['lang']['tanggal']."</td>
		<td>".$_SESSION['lang']['kodetangki']."</td>
		<td>".$_SESSION['lang']['max']." Kg</td>
		<td align=right>".$_SESSION['lang']['cpokuantitas']." (KG)</td>
		
		<td align=right>".$_SESSION['lang']['cpoffa']." (%)</td>
		<td align=right>".$_SESSION['lang']['cpokdair']." (%)</td>
		<td align=right>".$_SESSION['lang']['cpokdkot']." (%)</td>
		<td align=right>".$_SESSION['lang']['kernelquantity']." (KG)</td>
		
		<td align=right>".$_SESSION['lang']['kernelffa']." (%)</td>
		<td align=right>".$_SESSION['lang']['kernelkdair']." (%)</td>
		<td align=right>".$_SESSION['lang']['kernelkdkot']." (%)</td>
			
			
			
			</tr>";

if(!empty($tanger))foreach($tanger as $tgl){
		$stream.="<tr class=rowcontent>
		<td>".$tanker[$tgl]['kodorg']."</td>
		<td>".$tgl."</td>
		<td>".$tanker[$tgl]['kotang']."</td>
		
		<td>".number_format($cMax['volume'])."</td>
		
		<td align=right>".number_format($tanker[$tgl]['cpokua'],0)."</td>
		
		<td align=right>".number_format($tanker[$tgl]['cpoffa'],2)."</td>
		<td align=right>".number_format($tanker[$tgl]['cpokai'],2)."</td>
		<td align=right>".number_format($tanker[$tgl]['cpokko'],2)."</td>
		<td align=right>".number_format($tanker[$tgl]['kerkua'],0)."</td>
		
		<td align=right>".number_format($tanker[$tgl]['kerffa'],2)."</td>
		<td align=right>".number_format($tanker[$tgl]['kerkai'],2)."</td>
		<td align=right>".number_format($tanker[$tgl]['kerkko'],2)."</td>
		</tr>
		";    
}                                    
                        
//	$sql="select * from ".$dbname.".pabrik_masukkeluartangki where ".$where."";
//	$query=mysql_query($sql) or die(mysql_error());
//	$row=mysql_fetch_row($query);
//	if($row<1)
//	{
//		$stream.="<tr class=rowcontent>
//		<td colspan=8 align=center>Not Avaliable</td></tr>
//		";
//	}
//	$query=mysql_query($sql) or die(mysql_error());
//	while($res=mysql_fetch_assoc($query))
//	{
//		$stream.="<tr class=rowcontent>
//		<td>".$res['kodeorg']."</td>
//		<td>".$res['tanggal']."</td>
//		<td>".$res['kodetangki']."</td>
//		<td align=right>".number_format($res['kuantitas'],0)."</td>
//		<td align=right>".$res['cporendemen']."</td>
//		<td align=right>".$res['cpoffa']."</td>
//		<td align=right>".$res['cpokdair']."</td>
//		<td align=right>".$res['cpokdkot']."</td>
//		<td align=right>".number_format($res['kernelquantity'],0)."</td>
//		<td align=right>".$res['kernelrendemen']."</td>
//		<td align=right>".$res['kernelffa']."</td>
//		<td align=right>".$res['kernelkdair']."</td>
//		<td align=right>".$res['kernelkdkot']."</td>
//		</tr>";
//
//	}
			//echo "warning:".$strx;
			//=================================================
			$stream.="</table>";
						$stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
			
			$nop_="Laporan Stok-".$kdPbrik.$periode.$kdTangki;
			if(strlen($stream)>0)
			{
			if ($handle = opendir('tempExcel')) {
			while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
			@unlink('tempExcel/'.$file);
			}
			}	
			closedir($handle);
			}
			$handle=fopen("tempExcel/".$nop_.".xls",'w');
			if(!fwrite($handle,$stream))
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
	break;

	case'getTangki':
	$sGet="select kodetangki,keterangan from ".$dbname.".pabrik_5tangki where kodeorg='".$kdPbrik."'";
	$qGet=mysql_query($sGet) or die(mysql_error());
		$optTangki.="<option value=''>".$_SESSION['lang']['all']."</option>";
	while($rGet=mysql_fetch_assoc($qGet))
	{
		$optTangki.="<option value=".$rGet['kodetangki'].">".$rGet['keterangan']."</option>";
	}
	echo $optTangki;
	break;
	default:
	break;
}

?>