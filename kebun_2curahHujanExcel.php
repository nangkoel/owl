<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');


	$pt=$_GET['cmpId'];
	//$periode=substr($_GET['periode'],0,7);
	$periode=explode('-',$_GET['period']);
	//echo"warning:"."-".$periode[1]."-".$periode[0]."-".$_GET['period'];
/*	print"<pre>";
	print_r($_GET);
	print"<pre>";*/
	
//======================================

  	
//ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt='COMPANY NAME';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namapt=strtoupper($bar->namaorganisasi);
}
/*	if($kdVhc=='0')
	{
			//echo"warning:masuk vvv";
			$strx="select a.tanggal,a.kodevhc,b.* from ".$dbname.".vhc_penggantianht a inner join ".$dbname.".vhc_penggantiandt b on a.notransaksi=b.notransaksi 
			where a.kodeorg='".$pt."' and a.tanggal like '%".$periode."%'  order by a.tanggal asc ";
	}
	elseif($kdVhc!='0')
	{
			$strx="select a.tanggal,a.kodevhc,b.* from ".$dbname.".vhc_penggantianht a inner join ".$dbname.".vhc_penggantiandt b on a.notransaksi=b.notransaksi 
			where a.kodeorg='".$pt."' and a.tanggal like '%".$periode."%' and a.kodevhc='".$kdVhc."' order by a.tanggal asc ";
	}*/
		
			$stream.="
			<table>
			<tr><td colspan=6 align=center>".$_SESSION['lang']['laporanCurahHujan']."</td></tr>
			<tr><td colspan=3>".$_SESSION['lang']['kebun'].":".$namapt."</td></tr>";
			$stream.="<tr><td colspan=3>".$_SESSION['lang']['kodeorg'].":".$_SESSION['empl']['lokasitugas']."</td></tr>";
			$stream.="<tr><td colspan=3>".$_SESSION['lang']['periode'].":".$periode[1]."-".$periode[0]."</td></tr>
			<tr><td colspan=3>&nbsp;</td></tr>
			</table>
			<table border=1>
						<tr>
						  <td bgcolor=#DEDEDE align=center>No.</td>
						  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']."</td>
						  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['pagi']."</td>
						  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['sore']."</td>
						   <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['note']."</td>	
						</tr>";
		
		$ts=mktime(0,0,0,$periode[1],1,$periode[0]);
		$jmlhHari=intval(date("t",$ts));
		
		//echo"warning:".$jmlhHari."_".$periode[1]."__".$periode[0];
		for($a=1;$a<=$jmlhHari;$a++)
		{
			$i+=1;
			if(strlen($a)<2)
			{
				$a="0".$a;
			}
			$tglProg=$a."-".$periode[1]."-".$periode[0];
	
		$strx="select * from ".$dbname.".kebun_curahhujan where kodeorg='".$pt."' and tanggal='".tanggalsystem($tglProg)."'";
		//echo "warning:__".$strx;exit();
		$resx=mysql_query($strx);
		$barx=mysql_fetch_assoc($resx);
		
			$no+=1;
			$stream.="	<tr class=rowcontent>
				<td>".$no."</td>
				<td>".$tglProg."</td>
				<td>".$barx['pagi']."</td>
				<td>".$barx['sore']."</td>
				<td>".$barx['catatan']."</td>
				</tr>";
		}
	
	//echo "warning:".$strx;
//=================================================
		
	$stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	

$nop_="ReportCurahHujan";
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