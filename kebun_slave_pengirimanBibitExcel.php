<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zMysql.php');

/*	print"<pre>";
	print_r($_GET);
	print"<pre>";*/
	
//======================================

//ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'";
$namapt='COMPANY NAME';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namapt=strtoupper($bar->namaorganisasi);
}
	
			//echo"warning:masuk vvv";
			$period=date("Y-m");
			$strx="select * from ".$dbname.".kebun_pengirimanbbt where tanggal like '%".$period."%' order by tanggal desc";
		//echo"warning:".$strx;exit();
			$stream.="
			<table>
			<tr><td colspan=7 align=center>".$_SESSION['lang']['pengirimanBibit']."</td></tr>
			<tr><td colspan=3>&nbsp;</td></tr>
			</table>
			<table border=1>
						<tr>
							<td bgcolor=#DEDEDE align=center>No.</td>
							<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['notransaksi']."</td>
							<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namaorganisasi']."</td>	
							<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nmcust']."</td>	
							<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['OrgTujuan']."</td>	
							<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']."</td>
							<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jenisbibit']."</td>
							<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jumlah']."</td>	
							<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namakegiatan']."</td>							  
						</tr>";
		
		$resx=mysql_query($strx);
		$row=mysql_fetch_row($resx);
		if($row<1)
		{
			$stream.="	<tr class=rowcontent>
			<td colspan=8 align=center>Not Avaliable</td></tr>
			";
		}
		else
		{
			$no=0;
			$resx=mysql_query($strx);
			while($barx=mysql_fetch_assoc($resx))
			{
				$no+=1;
				$sKdOrg="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$barx['kodeorg']."'";
				$qKdOrg=mysql_query($sKdOrg) or die(mysql_error($conn));
				$rKdOrg=mysql_fetch_assoc($qKdOrg);
				
				$sKeg="select kelompok,namakegiatan from ".$dbname.".setup_kegiatan where kodekegiatan='".$barx['kodekegiatan']."'";
				$qKeg=mysql_query($sKeg) or die(mysql_error($conn));
				$rKeg=mysql_fetch_assoc($qKeg);
				
				$sCust="select namacustomer from ".$dbname.".pmn_4customer where kodecustomer='".$barx['pembeliluar']."'";
				$qCust=mysql_query($sCust) or die(mysql_error($conn));
				$rCust=mysql_fetch_assoc($qCust);
				
				$sKdOrg2="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$barx['orgtujuan']."'";
				$qKdOrg2=mysql_query($sKdOrg2) or die(mysql_error($conn));
				$rKdOrg2=mysql_fetch_assoc($qKdOrg2);
				
				$stream.="	<tr class=rowcontent>
							<td>".$no."</td>
							<td>".$barx['notransaksi']."</td>
							<td>".$rKdOrg['namaorganisasi']."</td>
							<td>".$rCust['namacustomer']."</td>
							<td>".$rKdOrg2['namaorganisasi']."</td>
							<td>".tanggalnormal($barx['tanggal'])."</td>
							<td>".$barx['jenisbibit']."</td>
							<td>".$barx['jumlah']."</td>	
							<td>".$rKeg['kelompok']."-".$rKeg['namakegiatan']."</td>	
					</tr>";
			}
		}
	
	//echo "warning:".$strx;
//=================================================
		
	$stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	

$nop_="PengirimanBibit";
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