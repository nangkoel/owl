<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zMysql.php');
	$pt=$_GET['pt'];
	$periode=$_GET['periode'];

	
/*	print"<pre>";
	print_r($_GET);
	print"<pre>";*/
	
//======================================

  	
//ambil namapt
$query2 = selectQuery($dbname,'organisasi','namaorganisasi',
"kodeorganisasi='".$pt."'");
$orgData2 = fetchData($query2);	
			//echo"warning:masuk vvv";
			if(strlen($pt)<6)
			{
				$kdOrg="substr(b.blok,1,4)";
			}
			else
			{
				$kdOrg="substr(b.blok,1,6)";
			}
			$strx="select a.tanggal,b.* from ".$dbname.".kebun_spbht a inner join ".$dbname.".kebun_spbdt b on a.nospb=b.nospb 
		where a.tanggal like '%".$periode."%' and ".$kdOrg."='".$pt."' order by a.tanggal asc "; 
		//echo"warning:".$strx;exit();
			$stream.="
			<table>
			<tr><td colspan=11 align=center>".$_SESSION['lang']['listSpb']."</td></tr>
			<tr><td colspan=3>".$_SESSION['lang']['periode'].":".$periode."</td></tr>
			<tr><td colspan=3>".$_SESSION['lang']['unit'].":".$orgData2[0]['namaorganisasi']."</td></tr>
			<tr><td colspan=3>&nbsp;</td></tr>
			</table>
			<table border=1>
						<tr>
							<td bgcolor=#DEDEDE align=center>No.</td>
							<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nospb']."</td>
							<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']."</td>
							<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['blok']."</td>
							<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['janjang']."</td>	
							<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['bjr']."</td>	
							<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['brondolan']."</td>	
							<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['mentah']."</td>	
							<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['busuk']."</td>	
							<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['matang']."</td>	
							<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['lewatmatang']."</td>	
						</tr>";
		
		$resx=mysql_query($strx);
		$row=mysql_fetch_row($resx);
		if($row<1)
		{
			$stream.="	<tr class=rowcontent>
			<td colspan=11 align=center>Not Found</td></tr>
			";
		}
		else
		{
			$no=0;
			$resx=mysql_query($strx);
			while($barx=mysql_fetch_assoc($resx))
			{
				$no+=1;
						
				$stream.="	<tr class=rowcontent>
					<td>".$no."</td>
					<td>".$barx['nospb']."</td>
					<td>".$barx['tanggal']."</td>
					<td>".$barx['blok']."</td>
					<td>".number_format($barx['jjg'],2)."</td>	
					<td>".number_format($barx['bjr'],2)."</td>	
					<td>".number_format($barx['brondolan'],2)."</td>	
					<td>".number_format($barx['mentah'],2)."</td>	
					<td>".number_format($barx['busuk'],2)."</td>
					<td>".number_format($$barx['matang'],2)."</td>	
					<td>".number_format($barx['lewatmatang'],2)."</td>	
					</tr>";
			}
		}
	
	//echo "warning:".$strx;
//=================================================
		
	$stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	

$nop_="".$_SESSION['lang']['listSpb']."";
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