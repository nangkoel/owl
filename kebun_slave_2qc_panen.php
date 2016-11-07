<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$kdorg=$_POST['kdorg'];
$per=$_POST['per'];

if(($proses=='excel')or($proses=='pdf')){
	
	$kdorg=$_GET['kdorg'];
	//echo $kdorg;
	$per=$_GET['per'];

}
$arrSt=array("0"=>"X","1"=>"V");
$nmKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');

if(($proses=='preview')or($proses=='excel')or($proses=='pdf'))
{

    if(($kdorg==''))
	{
		echo"Error: ".$_SESSION['lang']['divisi']." ".$_SESSION['lang']['kosong'].""; 
		exit;
    }
    else if(($per==''))
	{
        echo"Error: ".$_SESSION['lang']['periode']." ".$_SESSION['lang']['kosong'].""; 
		exit;
    }
	
}	

if($proses=='excel')
{
	$border="border='1'";
	$bgcolor="bgcolor=#CCCCCC";
}
else
{
	$border="border='0'";
	$bgcolor="#FFFFFF";
}

			$stream=" ".$_SESSION['lang']['cek']." ".$_SESSION['lang']['panen']."<br />".$_SESSION['lang']['periode']." : ".$per." ";
             $stream.="<table cellspacing='1' ".$border." class='sortable'>";
                $stream.="<thead>
					<tr class=rowheader>
						<td rowspan=2 align=center ".$bgcolor.">".$_SESSION['lang']['divisi']."</td>
						<td rowspan=2 align=center ".$bgcolor.">".$_SESSION['lang']['tanggal']."</td>
						<td rowspan=2 align=center ".$bgcolor.">".$_SESSION['lang']['afdeling']."</td>
						<td rowspan=2 align=center ".$bgcolor.">".$_SESSION['lang']['blok']."</td>
						<td rowspan=2 align=center ".$bgcolor.">".$_SESSION['lang']['tanggal']."<br />".$_SESSION['lang']['panen']."</td>
						<td align=center valign=center  rowspan=2 ".$bgcolor.">".$_SESSION['lang']['nourut']." ".$_SESSION['lang']['pokok']."</td> 
						<td align=center valign=center  rowspan=2 ".$bgcolor.">".$_SESSION['lang']['jjg']."<br />".$_SESSION['lang']['panen']."</td> 
						<td align=center valign=center  rowspan=2 ".$bgcolor.">".$_SESSION['lang']['jjg']."<br />".$_SESSION['lang']['no']." ".$_SESSION['lang']['panen']."</td> 
						<td align=center valign=center  rowspan=2 ".$bgcolor.">".$_SESSION['lang']['jjg']."<br />".$_SESSION['lang']['no']." ".$_SESSION['lang']['dikumpul']."</td> 
						<td align=center valign=center  rowspan=2 ".$bgcolor.">".$_SESSION['lang']['jjg']."<br />".$_SESSION['lang']['menggantung']."</td> 
						<td align=center valign=center  rowspan=2 ".$bgcolor.">".$_SESSION['lang']['brondolan']."<br />".$_SESSION['lang']['tdkdikutip']."</td> 
						<td align=center valign=center  rowspan=2 ".$bgcolor.">".$_SESSION['lang']['rasio']."<br />".$_SESSION['lang']['brondolan']."</td> 
						<td align=center valign=center  rowspan=2 ".$bgcolor.">".$_SESSION['lang']['pusingan']."</td>
						<td align=center valign=center  rowspan=2 ".$bgcolor.">".$_SESSION['lang']['rumpukan']."<br />".$_SESSION['lang']['pelepah']."</td>
						<td align=center valign=center  colspan=3 ".$bgcolor.">".$_SESSION['lang']['kondisi']." ".$_SESSION['lang']['jalan']."</td> 
					</tr>
					<tr>
						<td ".$bgcolor.">".$_SESSION['lang']['piringan']."</td>
						<td ".$bgcolor.">".$_SESSION['lang']['jalur']." ".$_SESSION['lang']['panen']."</td>
						<td ".$bgcolor.">".$_SESSION['lang']['tukulan']."</td>
					</tr> 					 
				</thead>
               <tbody>";

	//u v w
	//rowspan
		$i="SELECT count(*) as jumlah FROM ".$dbname.".kebun_qc_panenht WHERE tanggalcek LIKE  '%".$per."%' AND kodeblok LIKE  '%".$kdorg."%'";
		$n=mysql_query($i) or die (mysql_error($conn));
		$d=mysql_fetch_assoc($n);
			$jumlah=$d['jumlah'];

		$u="SELECT * FROM ".$dbname.".kebun_qc_panenht WHERE tanggalcek LIKE  '%".$per."%' AND kodeblok LIKE  '%".$kdorg."%'
			GROUP BY kodeblok,tanggalcek ORDER BY kodeblok ASC , tanggalcek ASC ";
		//echo $u;
		$v=mysql_query($u) or die (mysql_error($conn));
		while($w=mysql_fetch_assoc($v))
		{

			$x="SELECT nopokok,sum(jjgpanen) as jjgpanen,sum(jjgtdkpanen) as jjgtdkpanen,sum(jjgtdkkumpul) as jjgtdkkumpul,
				sum(jjgmentah) as jjgmentah,sum(jjggantung) as jjggantung,sum(brdtdkdikutip) as brdtdkdikutip,
				sum(rumpukan) as rumpukan,sum(piringan) as piringan,sum(jalurpanen) as jalurpanen,sum(tukulan) as tukulan
			 FROM ".$dbname.".kebun_qc_panendt WHERE kodeblok='".$w['kodeblok']."' AND tanggalcek ='".$w['tanggalcek']."' ORDER BY nopokok DESC LIMIT 1";
			$y=mysql_query($x) or die (mysql_error($conn));
			while($z=mysql_fetch_assoc($y))
			{
				$no+=1;
				if($no==1)
				{
					$stream.="
					<tr class=rowcontent>
						<td align=center valign=center rowspan=".$jumlah.">".substr($w['kodeblok'],0,4)."</td>
						<td align=right>".tanggalnormal($w['tanggalcek'])."</td>
						<td align=right>".substr($w['kodeblok'],0,6)."</td>
						<td align=right>".$w['kodeblok']."</td>
						<td align=right>".tanggalnormal($w['tanggalpanen'])."</td>
						<td align=right>".$z['nopokok']."</td>
						
						<td align=right>".$z['jjgpanen']."</td>
						<td align=right>".$z['jjgtdkpanen']."</td>
						<td align=right>".$z['jjgtdkkumpul']."</td>
						<td align=right>".$z['jjggantung']."</td>
						<td align=right>".$z['brdtdkdikutip']."</td>
						
						<td align=right>".number_format($z['brdtdkdikutip']/$z['jjgpanen'],2)."</td>
						<td align=right>".$w['pusingan']."</td>
						<td align=center>".$z['rumpukan']."</td>
						<td align=center>".$z['piringan']."</td>
						<td align=center>".$z['jalurpanen']."</td>
						<td align=center>".$z['tukulan']."</td>
					</tr>";	
				}
				else
				{
						$stream.="<tr class=rowcontent>
						<td align=right>".tanggalnormal($w['tanggalcek'])."</td>
						<td align=right>".substr($w['kodeblok'],0,6)."</td>
						<td align=right>".$w['kodeblok']."</td>
						<td align=right>".tanggalnormal($w['tanggalpanen'])."</td>
						<td align=right>".$z['nopokok']."</td>
						
						<td align=right>".$z['jjgpanen']."</td>
						<td align=right>".$z['jjgtdkpanen']."</td>
						<td align=right>".$z['jjgtdkkumpul']."</td>
						<td align=right>".$z['jjggantung']."</td>
						<td align=right>".$z['brdtdkdikutip']."</td>
						<td align=right>".number_format($z['brdtdkdikutip']/$z['jjgpanen'],2)."</td>
						
						<td align=right>".$w['pusingan']."</td>
						<td align=center>".$z['rumpukan']."</td>
						<td align=center>".$z['piringan']."</td>
						<td align=center>".$z['jalurpanen']."</td>
						<td align=center>".$z['tukulan']."</td>
					</tr>";	
				}
				$totNoPok+=$z['nopokok'];
				
				$totJjgPenen+=$z['jjgpanen'];
				$totTdkJjgPenen+=$z['jjgtdkpanen'];
				$totJjgTdkKumpul+=$z['jjgtdkkumpul'];
				$totJjgGantung+=$z['jjggantung'];
				$totBrondolTdkKutip+=$z['brdtdkdikutip'];
				//$totRasio+=$z['brdtdkdikutip']/$z['jjgpanen'];



			}
		}
		
		
$stream.="
		<thead><tr class=rowheader>
			<td colspan=6 align=center>".$_SESSION['lang']['total']."</td>
			<td align=right>".$totJjgPenen."</td>
			<td align=right>".$totTdkJjgPenen."</td>
			<td align=right>".$totJjgTdkKumpul."</td>
			<td align=right>".$totJjgGantung."</td>
			<td align=right>".$totBrondolTdkKutip."</td>
			<td align=right>".number_format($totBrondolTdkKutip/$totJjgPenen,2)."</td>
			<td colspan=5></td>
		</thead></tr>
		</table>";
		
		
		
/*$stream.="<table>
			<tr>
				<td colspan=5>".$_SESSION['lang']['diperiksa']."</td>
				
			</tr>";
			
			$h="SELECT distinct diperiksa FROM ".$dbname.".kebun_qc_panenht WHERE tanggalcek LIKE  '%".$per."%' AND kodeblok LIKE  '%".$kdorg."%'";
			
			$i=mysql_query($h) or die (mysql_error($conn));
			while($j=mysql_fetch_assoc($i))
			{
				$noX+=1;
				$stream.="
					<tr>
						<td></td>
						<td>".$noX."</td>
						<td colspan=4 align=left>".$nmKar[$j['diperiksa']]."</td>
					</tr>
				";
							
			}


$stream.="
			<tr>
				
				<td colspan=5>".$_SESSION['lang']['pendamping']."</td>
			</tr>";
			
			$h="SELECT distinct pendamping FROM ".$dbname.".kebun_qc_panenht WHERE tanggalcek LIKE  '%".$per."%' AND kodeblok LIKE  '%".$kdorg."%'";
			
			$i=mysql_query($h) or die (mysql_error($conn));
			while($j=mysql_fetch_assoc($i))
			{
				$noY+=1;
				$stream.="
					<tr>
						<td></td>
						<td>".$noY."</td>
						<td colspan=3>".$nmKar[$j['pendamping']]."</td>
						
						
					</tr>
				";
							
			}
$stream.="</table>";
		*/
		
			
		
	
	
	
	
	
	
	
	
	



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
		//$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="Laporan_QC_Panen_".$per;
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