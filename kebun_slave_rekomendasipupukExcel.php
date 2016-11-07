<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zMysql.php');

/*	print"<pre>";
	print_r($_GET);
	print"<pre>";*/
	
//======================================
$query = selectQuery($dbname,'organisasi','alamat,telepon',
	"kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
$orgData = fetchData($query);
  	
//ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'";
$namapt='COMPANY NAME';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namapt=strtoupper($bar->namaorganisasi);
}
	
			$sOrg="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
			$qOrg=mysql_query($sOrg) or die(mysql_error());
			$rOrg=mysql_fetch_assoc($qOrg);	
			//echo"warning:masuk vvv";
			$strx="select * from ".$dbname.".kebun_rekomendasipupuk where substring(kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."' order by periodepemupukan asc";
		//echo"warning:".$strx;exit();
			$stream.="
			<table>
			<tr><td colspan=9 align=center>".$_SESSION['lang']['rekomendasiPupuk']."</td></tr>
			<tr><td colspan=3>".$_SESSION['lang']['kebun']."</td><td colspan=3 align=left>".$rOrg['namaorganisasi']."</td></tr>
			</table>
			<table border=1>
						<tr>
						  <td bgcolor=#DEDEDE align=center>No.</td>
						  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tahunpupuk']."</td>
						  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['afdeling']."</td>
						   <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['blok']."</td>
						  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tahuntanam']."</td>
						  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jenisPupuk']."</td>	
						  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['dosis']." ".$_SESSION['lang']['rotasi']." 1</td>	
						  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['dosis']." ".$_SESSION['lang']['rotasi']." 2</td>	
						  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['dosis']." ".$_SESSION['lang']['rotasi']." 3</td>	
						  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['satuan']."</td>	
						  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jenisbibit']."</td>	
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
				$skdBrg="select  namabarang,satuan from ".$dbname.".log_5masterbarang where kodebarang='".$barx['kodebarang']."'";//echo $skdBrg;
				$qkdBrg=mysql_query($skdBrg) or die(mysql_error());
				$rBrg=mysql_fetch_assoc($qkdBrg);
				
				$sBibit="select jenisbibit  from ".$dbname.".setup_jenisbibit where jenisbibit='".$barx['jenisbibit']."'" ;
				$qBibit=mysql_query($sBibit) or die(mysql_error());
				$rBibit=mysql_fetch_assoc($qBibit);
				
				$sOrg="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$barx['kodeorg']."'";
				$qOrg=mysql_query($sOrg) or die(mysql_error());
				$rOrg=mysql_fetch_assoc($qOrg);	
				$stream.="	<tr class=rowcontent>
					<td>".$no."</td>
					<td>".$barx['periodepemupukan']."</td>
					<td>".$rOrg['namaorganisasi']."</td>
					<td>".$barx['blok']."</td>
					<td>".$barx['tahuntanam']."</td>
					<td>".$rBrg['namabarang']."</td>	
					<td>".$barx['dosis']."</td>	
					<td>".$barx['dosis2']."</td>	
					<td>".$barx['dosis3']."</td>	
					<td>".$barx['satuan']."</td>	
					<td>".$barx['jenisbibit']."</td>	
					</tr>";
			}
		}
	
	//echo "warning:".$strx;
//=================================================
		
	$stream.="</table>Print Time:".date('d-m-Y H:i:s')."<br>By:".$_SESSION['empl']['name'];	

$nop_="RekomendasiPupuk";
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