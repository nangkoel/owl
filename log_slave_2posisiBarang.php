<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
	
$method=$_POST['method'];	
$noPo=$_POST['noPo'];
$nopo=$_POST['nopo'];

$_POST['nopo']==''?$nopo=$_GET['nopo']:$nopo=$_POST['nopo'];
$_POST['method']==''?$method=$_GET['method']:$method=$_POST['method'];

$nmBarang=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang');
$nmKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');

if($method=='excel')	
{
    $border="border=1";
    $bgCol="bgcolor=#999999 ";
}



##isi priv
$stream="<table cellspacing='1' ".$border." class='sortable'>
			<thead>
				<tr class=rowheader>
					<td align=center ".$bgCol.">".$_SESSION['lang']['nourut']."</td>
					<td align=center ".$bgCol.">".$_SESSION['lang']['kodebarang']."</td>
					<td align=center ".$bgCol.">".$_SESSION['lang']['namabarang']."</td>
					
					<td align=center ".$bgCol.">".$_SESSION['lang']['nopo']."</td>
					<td align=center ".$bgCol.">".$_SESSION['lang']['tglpo1']."</td>
					
					<td align=center ".$bgCol.">".$_SESSION['lang']['nobpb1']."</td>
					<td align=center ".$bgCol.">".$_SESSION['lang']['tanggal']." BPB</td>
					
					<td align=center ".$bgCol.">".$_SESSION['lang']['nopacking1']."</td>
					<td align=center ".$bgCol.">".$_SESSION['lang']['tanggal']." Packing</td>
					
					<td align=center ".$bgCol.">".$_SESSION['lang']['nosj1']."</td>
					
					<td align=center ".$bgCol.">".$_SESSION['lang']['tglsj1']."</td>
					<td align=center ".$bgCol.">".$_SESSION['lang']['tglkirim1']."</td>
					<td align=center ".$bgCol.">".$_SESSION['lang']['tglcutisampai']."</td>
					<td align=center ".$bgCol.">".$_SESSION['lang']['nokonosemen']."</td>
					<td align=center ".$bgCol.">".$_SESSION['lang']['tglpengapalan1']."</td>
					<td align=center ".$bgCol.">".$_SESSION['lang']['tanggalberangkat']."</td>
					<td align=center ".$bgCol.">".$_SESSION['lang']['tanggaltiba']."</td>
					<td align=center ".$bgCol.">".$_SESSION['lang']['diterimaoleh']."</td>
				</tr>
			</thead>
		<tbody>";
		
		//kdbarang PO dan nopo
/*		$aPo="select kodebarang,nopo from ".$dbname.".log_po_vw where nopo='".$nopo."'";
		$bPo=mysql_query($aPo) or die(mysql_error());
		while($cPo=mysql_fetch_assoc($bPo))
		{
			$listPo[$cPo['nopo']]=$cPo['nopo'];
			$listKdBarang[$cPo['nopo']]=$cPo['kodebarang'];
		}
		
		$aGud="select notransaksi,nopo,kodebarang from ".$dbname.".log_transaksi_vw where nopo='".$nopo."' and tipetransaksi='1' ";
		$bGud=mysql_query($aGud) or die(mysql_error());
		while($cGud=mysql_fetch_assoc($bGud))
		{
			$listPo[$cGud['nopo']]=$cPo['notransaksi'];
			$listKdBarang[$cPo['nopo']]=$cPo['kodebarang'];
		}*/

		$tglPo=makeOption($dbname, "log_poht", "nopo,tanggal","nopo='".$nopo."'");
		//kdbarang nopo
		$aPo="select notransaksi,kodebarang,nopo,nopp,tanggal from ".$dbname.".log_transaksi_vw where nopo='".$nopo."' and tipetransaksi='1' and post=1";
		//echo $aPo;
		$bPo=mysql_query($aPo) or die(mysql_error());
		while($cPo=mysql_fetch_assoc($bPo))
		{
			$aPl="select notransaksi,tanggal from ".$dbname.".log_packing_vw where nopo='".$cPo['nopo']."' and kodebarang='".$cPo['kodebarang']."' and posting=1 ";
			$bPl=mysql_query($aPl) or die (mysql_error($conn));
			$cPl=mysql_fetch_assoc($bPl);
				$nPl=$cPl['notransaksi'];	
				$tPl=$cPl['tanggal'];	
			
			//SJ
			$aSj="select nosj,tanggal,tanggalkirim,tanggaltiba from ".$dbname.".log_suratjalan_vw where nopo='".$cPo['nopo']."' and kodebarang='".$cPo['kodebarang']."' and posting=1";
			$bSj=mysql_query($aSj) or die (mysql_error($conn));
			$cSj=mysql_fetch_assoc($bSj);
				$nSj=$cSj['nosj'];
				$tglSj=$cSj['tanggal'];
				$tglKSj=$cSj['tanggalkirim'];
				$tglTSj=$cSj['tanggaltiba'];
				
		
			$xSj="select nosj,tanggal,tanggalkirim,tanggaltiba from ".$dbname.".log_suratjalan_vw where  kodebarang='".$nPl."' and posting=1";
			$ySj=mysql_query($xSj) or die (mysql_error($conn));
			$zSj=mysql_fetch_assoc($ySj);
				$nSj1=$zSj['nosj'];
				$tglSj1=$zSj['tanggal'];
				$tglKSj1=$zSj['tanggalkirim'];
				$tglTSj1=$zSj['tanggaltiba'];	
				
			
			//KNO
			$aK="select nokonosemen,tanggal,tanggaltiba,tanggalberangkat,penerima from ".$dbname.".log_konosemen_vw where nopo='".$cPo['nopo']."' and kodebarang='".$cPo['kodebarang']."'";
			$bK=mysql_query($aK) or die (mysql_error($conn));
			$cK=mysql_fetch_assoc($bK);
				$nK=$cK['nokonosemen'];
				$tglK=$cK['tanggal'];
				$tglBK=$cK['tanggalberangkat'];
				$tglTK=$cK['tanggaltiba'];
				$dtK=$cK['penerima'];
				
			$xK="select nokonosemen,tanggal,tanggaltiba,tanggalberangkat,penerima from ".$dbname.".log_konosemen_vw where kodebarang='".$nPl."'";
			$yK=mysql_query($xK) or die (mysql_error($conn));
			$zK=mysql_fetch_assoc($yK);
				$nK1=$zK['nokonosemen'];
				$tglK1=$zK['tanggal'];
				$tglBK1=$zK['tanggalberangkat'];
				$tglTK1=$zK['tanggaltiba'];
				$dtK1=$zK['penerima'];
			
			
			
			if($nPl=='' || $nPl=='NULL'){$nSj=$nSj;$tglSj=$tglSj;$tglKSj=$tglKSj;$tglTSj=$tglTSj;$nK=$nK;$tglK=$tglK;$tglBK=$tglBK;$tglTK=$tglTK;$dtK=$dtK;}
			else{$nSj=$nSj1;$tglSj=$tglSj1;$tglKSj=$tglKSj1;$tglTSj=$tglTSj1;$nK=$nK1;$tglK=$tglK1;$tglBK=$tglBK1;$tglTK=$tglTK1;$dtK=$dtK1;}
				
				
			
			
			$no+=1;
			$stream.="
			<tr class=rowcontent>
				<td>".$no."</td>
				<td>".$cPo['kodebarang']."</td>
				<td nowrap>".$nmBarang[$cPo['kodebarang']]."</td>
				<td>".$cPo['nopo']."</td>
				
				<td nowrap>".tanggalnormal($tglPo[$cPo['nopo']])."</td>
				
				<td nowrap>".$cPo['notransaksi']."</td>
				<td nowrap>".tanggalnormal($cPo['tanggal'])."</td>
				
				<td>".$nPl."</td>
				<td nowrap>".tanggalnormal($tPl)."</td>
				
				<td>".$nSj."</td>
				
				<td nowrap>".tanggalnormal($tglSj)."</td>
				<td nowrap>".tanggalnormal($tglKSj)."</td>
				<td nowrap>".tanggalnormal($tglTSj)."</td>
				
				<td>".$nK."</td>
				<td nowrap>".tanggalnormal($tglK)."</td>
				<td nowrap>".tanggalnormal($tglBK)."</td>
				<td nowrap>".tanggalnormal($tglTK)."</td>
				<td>".$nmKar[$dtK]."</td>
			</tr>";
		}
		$stream.="</tbody></table>";



switch($method)
{
	case'goCariPo':
		echo"
			<table cellspacing=1 border=0 class=data>
			<thead>
				<tr class=rowheader>
					<td align=center>".$_SESSION['lang']['nourut']."</td>
					<td align=center>".$_SESSION['lang']['nopo']."</td>
				</tr>
		</thead>
		</tbody>";
	
		$i="select distinct(nopo) as nopo from ".$dbname.".log_po_vw where statuspo='3'  and nopo like '%".$noPo."%'  ";
		$n=mysql_query($i) or die (mysql_error($conn));
		while ($d=mysql_fetch_assoc($n))
		{
			$no+=1;
			echo"<tr class=rowcontent  style='cursor:pointer;' title='Click It' onclick=\"goPickPO('".$d['nopo']."');\">
					<td>".$no."</td>
					<td>".$d['nopo']."</td>
				</tr>";
		}		
	break;
	
		
	
	
		
   
	

######HTML
	case 'preview':
	
	//exit("Error:MASUK");
		echo $stream;
    break;

######EXCEL	
	case 'excel':
	
		$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="Laporan Posisi Barang".$tglSkrg;
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
	
	
	
	

	
	default;	
}

?>