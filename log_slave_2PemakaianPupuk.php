<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

if(($_GET['proses']=='preview')||($_GET['proses']=='excel'))
{
	if($_GET['proses']=='excel')
	{
		$tanggalAwal = $_GET['tglAwal'];
		$tanggalAkhir = $_GET['tglAkhir'];
		$kodeBarang = $_GET['kdBrg'];
		$brd=1;		
	}
	else
	{
		$tanggalAwal = $_POST['tglAwal'];
		$tanggalAkhir = $_POST['tglAkhir'];
		$kodeBarang = $_POST['kdBrg'];
		$brd=0;
	}	

	if($tanggalAwal==''){ exit("warning: ".$_SESSION['lang']['tgldari']." tidak boleh kosong"); }
	if($tanggalAkhir==''){ exit("warning: ".$_SESSION['lang']['tglsmp']." tidak boleh kosong"); }

	if(strtotime($tanggalAkhir) < strtotime($tanggalAwal))	{ exit("warning: ".$_SESSION['lang']['tglsmp']." tidak boleh lebih kecil dari".$_SESSION['lang']['tgldari']); }

	$fromDate = DateTime::createFromFormat('d-m-Y', $tanggalAwal);
	$toDate = DateTime::createFromFormat('d-m-Y', $tanggalAkhir);

	$str.="SELECT 
			YOP,Divisi,Afdeling,Blok,Luas,JumlahPKK,NamaBarang,
			SUM(Jumlah) AS Pemakaian,Satuan,Tanggal
		   FROM ".$dbname.".log_PemakaianPupuk
		   WHERE tanggal BETWEEN '".$fromDate->format('Y-m-d')."' AND '".$toDate->format('Y-m-d')."' ";
	
	$bgwarna="bgcolor=#DEDEDE align=center";
	$table.="<table cellpading=1 cellspacing=1 border=".$brd." class=sortable>
			 <thead>
			 <tr>
				<td ".$bgwarna.">YOP</td>
				<td ".$bgwarna.">Tanggal</td>
				<td ".$bgwarna.">Divisi</td>
				<td ".$bgwarna.">Afdeling</td>
				<td ".$bgwarna.">Blok</td>
				<td ".$bgwarna.">Luas</td>
				<td ".$bgwarna.">Jumlah PKK</td>
				<td ".$bgwarna.">Jenis Pupuk</td>
				<td ".$bgwarna.">Jumlah Pemakaian</td>
				<td ".$bgwarna.">Satuan</td>
			</tr>
			</thead><tbody>";
	if($kodeBarang=='')
	{
		$str.=" GROUP BY YOP,Divisi,Afdeling,Blok,Luas,JumlahPKK,NamaBarang,Satuan ORDER BY Divisi ASC, Blok ASC";		
	}
	else
	{
		$str.=" AND kodebarang = '".$kodeBarang."' GROUP BY YOP,Divisi,Afdeling,Blok,Luas,JumlahPKK,NamaBarang,Satuan ORDER BY Divisi ASC, Blok ASC";
	}
	
	$eReport = mysql_query($str) or die(mysql_error($conn));
	while ($baris = mysql_fetch_array($eReport, MYSQL_ASSOC)) 
	{
                $tgl=($_GET['proses']=='preview')?tanggalnormal($baris['Tanggal']):$baris['Tanggal'];
		$table.="<tr><td>".$baris['YOP']."</td>
					 <td>".$tgl."</td>
					 <td>".$baris['Divisi']."</td>
					 <td>".$baris['Afdeling']."</td>
					 <td>".$baris['Blok']."</td>
					 <td align=right>".number_format($baris['Luas'], 2, '.', ',')."</td>
					 <td align=right>".number_format($baris['JumlahPKK'], 0, '.', ',')."</td>
					 <td>".$baris['NamaBarang']."</td>
					 <td align=right>".number_format($baris['Pemakaian'], 2, '.', ',')."</td>
					 <td>".$baris['Satuan']."</td></tr>";
	}
	$table.="</tbody></table>";		
}

switch($_GET['proses'])
{
	case'preview':
		echo $table;
        break;
	case'excel':
		$dte=date("YmdHis");
		$nop_="lap_pemakaian_pupuk_".$dte;
        if(strlen($table)>0)
        {
           if ($handle = opendir('tempExcel')) {
               while (false !== ($file = readdir($handle))) 
			   {
                   if ($file != "." && $file != "..") 
				   {
                       @unlink('tempExcel/'.$file);
                   }
               }	
              closedir($handle);
           }
            $handle=fopen("tempExcel/".$nop_.".xls",'w');
            if(!fwrite($handle,$table))
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
}

?>