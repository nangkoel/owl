<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$kdorg=$_POST['kdorg'];
$kddept=$_POST['kddept'];
$tgl1=$_POST['tgl1'];
$tgl2=$_POST['tgl2'];
$karyawanid=$_POST['karyawanid'];
$klmpkBrg=$_POST['klmpkBrg'];
$kdKeg=$_POST['kdKeg'];

if($proses=='excel'){
	
	$kdorg=$_GET['kdorg'];
	$kddept=$_GET['kddept'];
	//echo $kdorg;
	$tgl1=$_GET['tgl1'];
	$tgl2=$_GET['tgl2'];
	$karyawanid=$_GET['karyawanid'];
	$klmpkBrg=$_GET['klmpkBrg'];
	$kdKeg=$_GET['kdKeg'];
}
if(($kdorg=='') || ($tgl1=='') || ($tgl2=='') ){
		echo"Error: Field ".$_SESSION['lang']['kodeorg']."/".$_SESSION['lang']['tanggal']." can't Empty"; 
		exit;
}

$tgl1=tanggaldgnbar($tgl1);
$tgl2=tanggaldgnbar($tgl2);
$tglPP=explode("-",$tgl1);
$date1 = $tglPP[2];
$month1 = $tglPP[1];
$year1 = $tglPP[0];
$pecah2 = explode("-", $tgl2);
$date2 = $pecah2[2];
$month2 = $pecah2[1];
$year2 =  $pecah2[0];
$jd1 = GregorianToJD($month1, $date1, $year1);
$jd2 = GregorianToJD($month2, $date2, $year2);
$jmlHari=$jd2-$jd1;
if($jmlHari>30){
    exit("error: Please insert range of date in 30 days");
}   
$nmDept=makeOption($dbname,'sdm_5departemen','kode,nama');
$nmBarang=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang');
$saBbarang=makeOption($dbname,'log_5masterbarang','kodebarang,satuan');
$nmGudang=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
$nmKegiatan=  makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan','kelompok="KNT"');
$border="border='0'";
if($proses=='excel'){
	$bgcolor="bgcolor=#CCCCCC";
	$border="border='1'";
}


			$stream="
				<table>
					<tr>
						<td>".$_SESSION['lang']['unitkerja']."</td>
						<td>".$kdorg." / ".$nmGudang[$kdorg]."</td>
					</tr>";
                        if ($kddept!=''){
                            $stream.="<tr>
						<td>".$_SESSION['lang']['departemen']."</td>
						<td>".$kddept." / ".$nmDept[$kddept]."</td>
					</tr>";
                        }
			$stream.="<tr>
						<td>".$_SESSION['lang']['tanggal']."</td>
						<td>".tanggalnormal($tgl1)." ".$_SESSION['lang']['sampai']." ".tanggalnormal($tgl2)."</td>
						
					</tr>
				</table>";
			
             $stream.="<table cellspacing='1' class='sortable' ".$border.">
               			<thead class=rowheader>
						  <tr  ".$bgcolor.">
							<td align=center>".$_SESSION['lang']['nourut']."</td>
                                                        <td align=center>".$_SESSION['lang']['nik']."</td>    
                                                        <td align=center>".$_SESSION['lang']['namakaryawan']."</td>	
                                                        <td align=center>".$_SESSION['lang']['departemen']."</td>	
							<td align=center>".$_SESSION['lang']['tanggal']."</td>
							<td align=center>Dari ".$_SESSION['lang']['gudang']."</td>
							<td align=center>".$_SESSION['lang']['kodebarang']."</td>
                                                        <td align=center>".$_SESSION['lang']['namabarang']."</td>
                                                        <td align=center>".$_SESSION['lang']['jumlah']."</td>
                                                        <td align=center>".$_SESSION['lang']['satuan']."</td>
							
							
							<td align=center>".$_SESSION['lang']['notransaksi']."</td>
                                                        <td align=center>".$_SESSION['lang']['keterangan']."</td>
                                                        <td align=center>".$_SESSION['lang']['namakegiatan']."</td>
						  </tr>
						</thead>
              		 <tbody>";
if($kddept!=''){
    $wher.=" and bagian='".$kddept."'";
}
if($karyawanid!=''){
    $wher.=" and namapenerima='".$karyawanid."'";
}
if($klmpkBrg!=''){
    $wher.=" and left(kodebarang,3)='".$klmpkBrg."'";
}
if($klmpkBrg!=''){
    $wher.=" and left(kodebarang,3)='".$klmpkBrg."'";
}
if($kdKeg!=''){
	$wher.=" and kodekegiatan='".$kdKeg."'";
}else{
	$wher.=" and kodekegiatan in ('114020001','127030101','127040201','127050101','711010401','712070001','712040001') ";
}
$i="select notransaksi,tanggal,kodegudang,jumlah,hartot,namapenerima,keterangan,kodebarang,kodekegiatan,satuan,nik,namakaryawan,bagian 
    FROM ".$dbname.".log_transaksi_vw left join ".$dbname.".datakaryawan on namapenerima=karyawanid where untukunit='".$kdorg."' "
 . "and tanggal between '".$tgl1."' and '".$tgl2."' ".$wher." and tipetransaksi='5' and namapenerima!='MASYARAKAT' order by namapenerima,kodebarang";
//echo $i;
$n=mysql_query($i);	
while($d=mysql_fetch_assoc($n))
{
	$totbaris=$d['jumlah']*$d['total'];
	$no+=1;
        if(($kdBrgDet!=$d['kodebarang'])||($d['namapenerima']!=$trima)){
            $sRow="select * from ".$dbname.".log_transaksi_vw left join ".$dbname.".datakaryawan on namapenerima=karyawanid where untukunit='".$kdorg."' and "
                . "kodekegiatan in ('114020001','127030101','127040201','127050101','711010401','712070001','712040001') " 
                . "and tanggal between '".$tgl1."' and '".$tgl2."' ".$wher." and tipetransaksi='5' and kodebarang='".$d['kodebarang']."' and namapenerima='".$d['namapenerima']."'";
            $qRow=  mysql_query($sRow) or die(mysql_error($conn));
            $rRow=  mysql_num_rows($qRow);
            $kdBrgDet=$d['kodebarang'];
            $trima=$d['namapenerima'];
            $brsdt=$rRow;
            $totJmlh=0;
        }
	$stream.="<tr class=rowcontent>
		<td align=center>".$no."</td>
                <td align=left>".$d['nik']."</td>
                <td align=left>".trim($d['namakaryawan'])."</td>
                <td align=left>".$nmDept[$d['bagian']]."</td>
		<td align=center nowrap>".$d['tanggal']."</td>
		<td align=left>".$nmGudang[$d['kodegudang']]."</td>
		<td align=left>".$d['kodebarang']."</td>
                <td align=left>".$nmBarang[$d['kodebarang']]."</td>
                <td align=right>".number_format($d['jumlah'])."</td>
                <td align=left>".$d['satuan']."</td>";
       $stream.="
		<td align=left style='cursor:pointer' onclick=previewBast('".$d['notransaksi']."',event);>".$d['notransaksi']."</td>
                <td align=left>".$d['keterangan']."</td>
                <td align=left>".$nmKegiatan[$d['kodekegiatan']]."</td>
	</tr>";
        $brsdt-=1;
        $totJmlh+=$d['jumlah'];
        $totPergdng[$d['kodegudang'].$d['kodebarang']]+=$d['jumlah'];
        $lstGdng[$d['kodegudang']]=$d['kodegudang'];
        $lstBrg[$d['kodebarang']]=$d['kodebarang'];
        if($brsdt==0){
            $stream.="<thead><tr class=rowcontent>";
            $stream.="<td colspan=8 align=center>".$_SESSION['lang']['subtotal']." ".$nmBarang[$d['kodebarang']]."-".$nmNik[$d['namapenerima']]."-".$nmKar[$d['namapenerima']]."</td>";
            $stream.="<td align=right>".number_format($totJmlh,0)."</td>";
            $stream.="<td colspan=5></td></tr></thead>";
        }
	
}
	$stream.="</tbody></table>";

#######################################################################
############PANGGGGGGGGGGGGGGGGGGILLLLLLLLLLLLLLLLLLLLLLLLLL###########   
#######################################################################

switch($proses)
{
######HTML
	case 'preview':
		echo $stream;
    break;

######EXCEL	
	case 'excel':
	
		//exit("Error:$stream");
		
		$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="Laporan_penggunaan_barang_perkaryawan".$tglSkrg;
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
	
	


	
	
	default:
	break;
}

?>