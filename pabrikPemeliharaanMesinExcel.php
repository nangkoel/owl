<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

	$pabrik=$_GET['pabrik'];
	$statId=$_GET['statId'];
	$periode=substr($_GET['periode'],0,7);
	$kdBrg=$_GET['kdBrg'];
	$msnId=$_GET['msnId'];
	
/*	print"<pre>";
	print_r($_GET);
	print"<pre>";*/
	
//======================================
$optNmMsn=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
  	
//ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$pabrik."'";
$namapt='COMPANY NAME';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namapt=strtoupper($bar->namaorganisasi);
}
$sNm="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$statId."'";
$qNm=mysql_query($sNm);
$rNm=mysql_fetch_assoc($qNm);
	
	if($periode=='0')
	{
		$strx="select a.*,b.* from ".$dbname.".pabrik_rawatmesinht a inner join ".$dbname.".pabrik_rawatmesindt b on a.notransaksi=b.notransaksi 
		where a.pabrik='".$pabrik."' and a.statasiun='".$statId."' order by a.tanggal asc"; 	
	}
	elseif($periode!='0')
	{
		$strx="select a.*,b.* from ".$dbname.".pabrik_rawatmesinht a inner join ".$dbname.".pabrik_rawatmesindt b on a.notransaksi=b.notransaksi 
		where a.pabrik='".$pabrik."'and a.statasiun='".$statId."' and tanggal like '%".$periode."%'  order by a.tanggal asc"; 	
	}
			//echo"warning:".$strx;
			$stream.="<table><tr><td colspan=11 align=center>".$_SESSION['lang']['pemeliharaanMesinReport']."</td></tr>
			<tr><td colspan=3>".$_SESSION['lang']['pabrik'].":".$namapt."</td><td colspan=2>&nbsp;</td>
			<td colspan=3>".$_SESSION['lang']['statasiun'].":".$rNm['namaorganisasi']."</td></tr>
			";
			if($periode!='0')
			{$stream.="<tr><td colspan=3>".$_SESSION['lang']['periode'].":".$periode."</td></tr>";}
			$stream.="</table>
			<table border=1>
						<tr>
                                                <td bgcolor=#DEDEDE align=center>No.</td>
                                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['notransaksi']."</td>
                                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']."</td>
                                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kegiatan']."</td>
                                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jammulai']."</td>
                                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jamselesai']."</td>
                                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodebarang']."</td>
                                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namabarang']."</td>
                                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['satuan']."</td>
                                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jumlah']."</td>	
                                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['shift']."</td>	
                                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['mesin']."</td>
                                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nmmesin']."</td>
                                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['keterangan']."</td>
						</tr>";
		
		$resx=mysql_query($strx);
		$no=0;
		while($barx=mysql_fetch_assoc($resx))
		{
			$no+=1;
			$sBrg="select namabarang,kodebarang from ".$dbname.".log_5masterbarang where kodebarang='".$barx['kodebarang']."'";
			$qBrg=mysql_query($sBrg) or die(mysql_error());
			$rBrg=mysql_fetch_assoc($qBrg);
			$stream.="	<tr class=rowcontent>
				<td>".$no."</td>
				<td>".$barx['notransaksi']."</td>
				<td>".tanggalnormal($barx['tanggal'])."</td>
                                <td>".$barx['kegiatan']."</td>
                                <td>".tanggalnormald($barx['jammulai'])."</td>
                                <td>".tanggalnormald($barx['jamselesai'])."</td>
				<td>".$barx['kodebarang']."</td>
				<td>".$rBrg['namabarang']."</td>
				<td>".$barx['satuan']."</td>
				<td>".$barx['jumlah']."</td>
				<td>".$barx['shift']."</td>	
				<td>".$barx['mesin']."</td>	
				<td>".$optNmMsn[$barx['mesin']]."</td>
                                <td>".$barx['keterangan']."</td>
				</tr>";
		}		

	
	//echo "warning:".$strx;
//=================================================
		
	$stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	

$nop_="ReportPemeliharaanMesin";
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
?>