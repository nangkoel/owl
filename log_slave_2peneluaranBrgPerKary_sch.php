<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');



$proses=$_POST['proses'];
$schBarang=$_POST['schBarang'];
$kdorg=$_POST['kdorg'];
$kddept=$_POST['kddept'];

switch($proses)
{
	
	case'getKar':
	//exit("Error:MAI");
                if ($kddept!='') $whrdept=" and bagian='".$kddept."' ";
		$optKar="<option value=''>".$_SESSION['lang']['all']."</option>";
		$j="select distinct namapenerima,nik,namakaryawan from ".$dbname.".log_transaksiht left join ".$dbname.".datakaryawan on namapenerima=karyawanid where untukunit='".$kdorg."' ".$whrdept;
		//exit("Error:$j");
		
		$k=mysql_query($j) or die (mysql_error($conn));
		while($l=mysql_fetch_assoc($k))
		{
			$optKar.="<option value='".$l['namapenerima']."'>".$l['nik']." - ".$l['namakaryawan']."</option>";
		}
		echo $optKar;
		
	break;
	
	
	case'goCariBarang':
	echo"
		<table cellspacing=1 border=0 class=data>
		<thead>
			<tr class=rowheader>
				<td>No</td>
				<td>".$_SESSION['lang']['kodebarang']."</td>
				<td>".$_SESSION['lang']['namabarang']."</td>
			</tr>
	</thead>
	</tbody>";
	
	$i="select * from ".$dbname.".log_5masterbarang where kodebarang like '%".$schBarang."%' or namabarang like '%".$schBarang."%'";
	//echo $i;
	$n=mysql_query($i) or die (mysql_error($conn));
	while ($d=mysql_fetch_assoc($n))
	{
		$no+=1;
	echo"
		<tr class=rowcontent  style='cursor:pointer;' title='Click It' onclick=\"goPickBarang('".$d['kodebarang']."','".$d['namabarang']."');\">
			<td>".$no."</td>
			<td>".$d['kodebarang']."</td>
			<td>".$d['namabarang']."</td>
		</tr>";
	}
	break;	




default;
	
	
	
	
	
	
	
	
	
	
}

