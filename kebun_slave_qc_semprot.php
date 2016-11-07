<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

?>	

<?php		

$_POST['method']==''?$method=$_GET['method']:$method=$_POST['method'];


$tgl=tanggalsystem($_POST['tgl']);
$kdDiv=$_POST['kdDiv'];
$kdAfd=$_POST['kdAfd'];
$kdBlok=$_POST['kdBlok'];
$kdKeg=$_POST['kdKeg'];

$dosis=$_POST['dosis'];
$jenisgulma=$_POST['jenisgulma'];
$kondisigulma=$_POST['kondisigulma'];



$dosismaterial1=$_POST['dosismaterial1'];
$dosismaterial2=$_POST['dosismaterial2'];
$dosismaterial3=$_POST['dosismaterial3'];
$dosisjumlah1=$_POST['dosisjumlah1'];
$dosisjumlah2=$_POST['dosisjumlah2'];
$dosisjumlah3=$_POST['dosisjumlah3'];

$materialdiambil1=$_POST['materialdiambil1'];
$materialdiambil2=$_POST['materialdiambil2'];
$materialdiambil3=$_POST['materialdiambil3'];
$jumlahdiambil1=$_POST['jumlahdiambil1'];
$jumlahdiambil2=$_POST['jumlahdiambil2'];
$jumlahdiambil3=$_POST['jumlahdiambil3'];

$materialdipakai1=$_POST['materialdipakai1'];
$materialdipakai2=$_POST['materialdipakai2'];
$materialdipakai3=$_POST['materialdipakai3'];
$jumlahdipakai1=$_POST['jumlahdipakai1'];
$jumlahdipakai2=$_POST['jumlahdipakai2'];
$jumlahdipakai3=$_POST['jumlahdipakai3'];

$karyawan1=$_POST['karyawan1'];
$karyawan2=$_POST['karyawan2'];
$karyawan3=$_POST['karyawan3'];
$karyawan4=$_POST['karyawan4'];
$karyawan5=$_POST['karyawan5'];
$karyawan6=$_POST['karyawan6'];
$karyawan7=$_POST['karyawan7'];
$karyawan8=$_POST['karyawan8'];
$karyawan9=$_POST['karyawan9'];
$karyawan10=$_POST['karyawan10'];
$karyawan11=$_POST['karyawan11'];
$karyawan12=$_POST['karyawan12'];
$karyawan13=$_POST['karyawan13'];
$karyawan14=$_POST['karyawan14'];
$karyawan15=$_POST['karyawan15'];

$hasilkaryawan1=$_POST['hasilkaryawan1'];
$hasilkaryawan2=$_POST['hasilkaryawan2'];
$hasilkaryawan3=$_POST['hasilkaryawan3'];
$hasilkaryawan4=$_POST['hasilkaryawan4'];
$hasilkaryawan5=$_POST['hasilkaryawan5'];
$hasilkaryawan6=$_POST['hasilkaryawan6'];
$hasilkaryawan7=$_POST['hasilkaryawan7'];
$hasilkaryawan8=$_POST['hasilkaryawan8'];
$hasilkaryawan9=$_POST['hasilkaryawan9'];
$hasilkaryawan10=$_POST['hasilkaryawan10'];
$hasilkaryawan11=$_POST['hasilkaryawan11'];
$hasilkaryawan12=$_POST['hasilkaryawan12'];
$hasilkaryawan13=$_POST['hasilkaryawan13'];
$hasilkaryawan14=$_POST['hasilkaryawan14'];
$hasilkaryawan15=$_POST['hasilkaryawan15'];

$keterangan=$_POST['keterangan'];
$pengawas=$_POST['pengawas'];
$asisten=$_POST['asisten'];
$mengetahui=$_POST['mengetahui'];


$perSch=$_POST['perSch'];
$kdDivSch=$_POST['kdDivSch'];

//exit("Error:$mengetahui");
$nmKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');
$nmKeg=makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan');
$nmCust=makeOption($dbname,'pmn_4customer','kodecustomer,namacustomer');
$nmBarang=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang');
$nmTranp=makeOption($dbname,'log_5supplier','supplierid,namasupplier');

?>

<?php
switch($method)
{	

	case'getAfd':
		//exit("Error:MASUK");
		$optAfd="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$i="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi  where induk='".$kdDiv."' and tipe='AFDELING'";
		//exit("Error:$i");
		$n=mysql_query($i) or die (mysql_error($conn));
		while($d=mysql_fetch_assoc($n))
		{
			$optAfd.="<option value='".$d['kodeorganisasi']."'>".$d['namaorganisasi']."</option>";
		}
	echo $optAfd;
	
	break;
	
	case'getForm':
	
	$i="select * from ".$dbname.".kebun_qc_semprot where tanggal='".$tgl."' and blok='".$kdBlok."'";
	//exit("Error:$i");
	$n=mysql_query($i) or die (mysql_error($conn));
	$d=mysql_fetch_assoc($n);
	
	echo 
		substr($d['blok'],0,6)."###".$d['blok']."###".$d['kodekegiatan']."###".$d['karyawan1']."###".$d['hasilkaryawan1'];
			
			
	/*substr($d['blok'],0,6)."###".$d['blok']."###".$d['']."###".$d['']
		."###".$d['']."###".$d['']."###".$d['']."###".$d['']."###".$d['']."###".$d['']
		."###".$d['']."###".$d['']."###".$d['']."###".$d['']."###".$d['']."###".$d['']	*/
	
	
	
	
	
	break;
	
	
		

	
	case'getBlok':
		$optBlok="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$i="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi  where induk='".$kdAfd."' and tipe='BLOK'";
		//exit("Error:$i");
		$n=mysql_query($i) or die (mysql_error($conn));
		while($d=mysql_fetch_assoc($n))
		{
			$optBlok.="<option value='".$d['kodeorganisasi']."'>".$d['namaorganisasi']."</option>";
		}
	echo $optBlok;
	break;

	case'saveData':
	
		$i="INSERT INTO `".$dbname."`.`kebun_qc_semprot` (`tanggal`, `blok`, `kodekegiatan`, 
			`dosismaterial1`, `dosisjumlah1`, `dosismaterial2`, `dosisjumlah2`, `dosismaterial3`, `dosisjumlah3`, 
			`takaran`, `jenisgulma`, `kondisigulma`, 
			`jumlahdiambil1`, `jumlahdiambil2`, `jumlahdiambil3`, 
			`jumlahdipakai1`,  `jumlahdipakai2`, `jumlahdipakai3`, 
			`karyawan1`, `karyawan2`, `karyawan3`, `karyawan4`, `karyawan5`, 
			`karyawan6`, `karyawan7`, `karyawan8`, `karyawan9`, `karyawan10`, 
			`karyawan11`, `karyawan12`, `karyawan13`, `karyawan14`, `karyawan15`, 
			`hasilkaryawan1`, `hasilkaryawan2`, `hasilkaryawan3`, `hasilkaryawan4`, `hasilkaryawan5`, 
			`hasilkaryawan6`, `hasilkaryawan7`, `hasilkaryawan8`, `hasilkaryawan9`, `hasilkaryawan10`, 
			`hasilkaryawan11`, `hasilkaryawan12`, `hasilkaryawan13`, `hasilkaryawan14`, `hasilkaryawan15`, 
			`keterangan`, `pengawas`, `asisten`, `mengetahui`, `updateby`) 
			
		values ('".$tgl."','".$kdBlok."','".$kdKeg."',
				'".$dosismaterial1."','".$dosisjumlah1."','".$dosismaterial2."','".$dosisjumlah2."','".$dosismaterial3."','".$dosisjumlah3."',
				'".$takaran."','".$jenisgulma."','".$kondisigulma."',
				'".$jumlahdiambil1."','".$jumlahdiambil2."','".$jumlahdiambil3."',
				'".$jumlahdipakai1."','".$jumlahdipakai2."','".$jumlahdipakai3."',
				'".$karyawan1."','".$karyawan2."','".$karyawan3."','".$karyawan4."','".$karyawan5."',
				'".$karyawan6."','".$karyawan7."','".$karyawan8."','".$karyawan9."','".$karyawan10."',
				'".$karyawan11."','".$karyawan12."','".$karyawan13."','".$karyawan14."','".$karyawan15."',
				'".$hasilkaryawan1."','".$hasilkaryawan2."','".$hasilkaryawan3."','".$hasilkaryawan4."','".$hasilkaryawan5."',
				'".$hasilkaryawan6."','".$hasilkaryawan7."','".$hasilkaryawan8."','".$hasilkaryawan9."','".$hasilkaryawan10."',
				'".$hasilkaryawan11."','".$hasilkaryawan12."','".$hasilkaryawan13."','".$hasilkaryawan14."','".$hasilkaryawan15."',
				'".$keterangan."','".$pengawas."','".$asisten."','".$mengetahui."','".$_SESSION['standard']['userid']."')";

		if(mysql_query($i))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}
	break;
	
	case'getData':
		$i="select luasareaproduktif,jumlahpokok from ".$dbname.".setup_blok where kodeorg='".$kdBlok."'";
		//exit("Error:$i");
		$n=mysql_query($i) or die (mysql_error($conn));
		$d=mysql_fetch_assoc($n);
	echo $d['luasareaproduktif']."###".$d['jumlahpokok'];
	break;	
	
	case'getKar':
		#karyawan
		$optKar="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$a="select karyawanid,namakaryawan,nik from ".$dbname.".datakaryawan  where lokasitugas='".$kdDiv."' and tipekaryawan!='0'";
		//exit("Error:$a");
		$b=mysql_query($a) or die (mysql_error($conn));
		while($c=mysql_fetch_assoc($b))
		{
			$optKar.="<option value='".$c['karyawanid']."'>".$c['nik']." - ".$c['namakaryawan']."</option>";
		}

		#pengawas semua QC
		$optMandor="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$j="select karyawanid,namakaryawan,nik from ".$dbname.".datakaryawan  where lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment 
			where regional='".$_SESSION['empl']['regional']."' and kodeunit like '%RO%')  and bagian='QC'";
		//exit("Error:$j");
		$k=mysql_query($j) or die (mysql_error($conn));
		while($l=mysql_fetch_assoc($k))
		{
			$optMandor.="<option value='".$l['karyawanid']."'>".$l['nik']." - ".$l['namakaryawan']."</option>";
		}
		
		#pendamping / asst
		$optAstn="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$d="select karyawanid,namakaryawan,nik from ".$dbname.".datakaryawan  where 
			lokasitugas='".$kdDiv."'
			and kodejabatan in (select kodejabatan from ".$dbname.".sdm_5jabatan where  namajabatan like '%PENGAWAS%' or 
			namajabatan like '%KA. AFDELING%' or namajabatan like '%recorder%' or namajabatan like '%KASUB AFDELING%')";
	//	exit("Error:$d");
		$e=mysql_query($d) or die (mysql_error($conn));
		while($f=mysql_fetch_assoc($e))
		{
			$optAstn.="<option value='".$f['karyawanid']."'>".$f['nik']." - ".$f['namakaryawan']."</option>";
		}
		
		#mengetahui (manager/kadiv)
		$optKadiv="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$g="select karyawanid,namakaryawan,nik from ".$dbname.".datakaryawan  where lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment 
			where regional='".$_SESSION['empl']['regional']."' and kodeunit not like '%HO%')  and bagian='QC'";
		//exit("Error:$i");
		$h=mysql_query($g) or die (mysql_error($conn));
		while($i=mysql_fetch_assoc($h))
		{
			$optKadiv.="<option value='".$i['karyawanid']."'>".$i['nik']." - ".$i['namakaryawan']."</option>";
		}
		echo $optKar."###".$optMandor."###".$optAstn."###".$optKadiv;
	break;	
	
	

	case'loadData':
	
		if($kdDivSch!='')
			$kdDivLoad="blok like '%".$kdDivSch."%'";
		else
			$kdDivLoad="blok!='' ";
			
		if($perSch!='')
			$perLoad="and tanggal like '%".$perSch."%'";
		else
			$perLoad="";	
	
		echo"
		
			<table class=sortable cellspacing=1 border=0>
			 <thead>
				 <tr class=rowheader>
					<td align=center>".$_SESSION['lang']['nourut']."</td>
					 <td align=center>".$_SESSION['lang']['tanggal']."</td>
					 <td align=center>".$_SESSION['lang']['kodeorganisasi']."</td>
					 <td align=center>".$_SESSION['lang']['blok']."</td>
					 <td align=center>".$_SESSION['lang']['vhc_jenis_pekerjaan']."</td>
					 <td align=center>".$_SESSION['lang']['pengawasan']."</td>
					 <td align=center>".$_SESSION['lang']['updateby']."</td>
					 <td align=center>".$_SESSION['lang']['action']."</td>
				 </tr>
			</thead>
			<tbody>";

			$limit=10;
			$page=0;
			if(isset($_POST['page']))
			{
			$page=$_POST['page'];
			if($page<0)
			$page=0;
			}
			$offset=$page*$limit;
			$maxdisplay=($page*$limit);
			
			$ql2="select count(*) as jmlhrow from ".$dbname.".kebun_qc_semprot where ".$kdDivLoad."  ".$perLoad."  ";// where kodeorg='".$kodeorg."' and periode='".$per."'
			//exit("Error:$ql2");
			//where kodeorg='".$kodeorg."' and periode='".$per."' order by lastupdate
			$query2=mysql_query($ql2) or die(mysql_error());
			while($jsl=mysql_fetch_object($query2)){
			$jlhbrs= $jsl->jmlhrow;
			}
			$i="select * from ".$dbname.".kebun_qc_semprot where ".$kdDivLoad."  ".$perLoad."  limit ".$offset.",".$limit."";
			
			//echo $i;
			$n=mysql_query($i) or die(mysql_error());
			$no=$maxdisplay;
			while($d=mysql_fetch_assoc($n))
			{
				$no+=1;
				echo "<tr class=rowcontent>";
				echo "<td align=center>".$no."</td>";
				echo "<td align=left>".tanggalnormal($d['tanggal'])."</td>";
				echo "<td align=left>".substr($d['blok'],0,4)."</td>";
				echo "<td align=left>".$d['blok']."</td>";
				echo "<td align=left>".$nmKeg[$d['kodekegiatan']]."</td>";
				echo "<td align=left>".$nmKar[$d['pengawas']]."</td>";
				echo "<td align=left>".$nmKar[$d['updateby']]."</td>";
				echo "<td align=center>
						<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"del('".tanggalnormal($d['tanggal'])."','".$d['blok']."');\">		
						<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_qc_semprot','".$d['tanggal'].','.$d['blok']."','','kebun_qc_semprot_pdf',event)\">
						
				
				</td></tr>";/*<img src=images/application/application_edit.png class=resicon  caption='Edit' 
					onclick=\"edit('".tanggalnormal($d['tanggal'])."','".substr($d['blok'],0,4)."');\">*/
			}
			echo"
			<tr class=rowheader><td colspan=43 align=center>
			".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
			<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
			<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
			</td>
			</tr>";
			echo"</tbody></table>";
		break;
		
		
		case'delete':
			$i="delete from ".$dbname.".kebun_qc_semprot where tanggal='".$tgl."' and blok='".$kdBlok."'";
			//exit("Error:$i");
			if(mysql_query($i))
			echo"";
			else
			echo " Gagal,".addslashes(mysql_error($conn));
	break;
	
	
	case'update':
	
	//exit("Error:masuk");
		$i="update ".$dbname.".pabrik_kelengkapanloses set nilai='".$inpEdit."',`updateby`='".$_SESSION['standard']['userid']."' where kodeorg='".$kodeorgEdit."' and tanggal='".$tglEdit."' and id='".$idEdit."'";
		//exit("Error:$i");
		if(mysql_query($i))
		{//berhasil kosongin aja
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}
	break;
	

	
	
	
	
	
	default;
}
?>