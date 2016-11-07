<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

?>	

<?php		

$_POST['method']==''?$method=$_GET['method']:$method=$_POST['method'];

/*$mulaijam=$_POST['mulaijam'];
$sampaijam=$_POST['sampaijam'];*/
$mulaijam=$_POST['jm1'].":".$_POST['mn1'].":00";
//exit("Error:$jam1");
$sampaijam=$_POST['jm2'].":".$_POST['mn2'].":00";

$tgl=tanggalsystem($_POST['tgl']);
$kdDiv=$_POST['kdDiv'];
$kdAfd=$_POST['kdAfd'];
$kdBlok=$_POST['kdBlok'];
$userId=$_POST['userId'];

$tenagakerja=$_POST['tenagakerja'];

$alat=$_POST['alat'];
$bahan1=$_POST['bahan1'];
$bahan2=$_POST['bahan2'];
$bahan3=$_POST['bahan3'];
$dosis1=$_POST['dosis1'];
$dosis2=$_POST['dosis2'];
$dosis3=$_POST['dosis3'];
$pokok=$_POST['pokok'];
$bensin=$_POST['bensin'];
$oli=$_POST['oli'];
$catatan=$_POST['catatan'];
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
	
		$i="INSERT INTO `".$dbname."`.`kebun_qc_hama` (`tanggal`, `blok`, `tenagakerja`, `mulaijam`, `sampaijam`, `alat`, 
			`bahan1`, `bahan2`, `bahan3`, `dosis1`, `dosis2`, `dosis3`, 
			`pokok`, `bensin`, `oli`, `catatan`, `pengawas`, `asisten`, `mengetahui`, `updateby`) 
			
		values ('".$tgl."','".$kdBlok."','".$tenagakerja."','".$mulaijam."','".$sampaijam."','".$alat."',
				'".$bahan1."','".$bahan2."','".$bahan3."','".$dosis1."','".$dosis2."','".$dosis3."',
				'".$pokok."','".$bensin."','".$oli."','".$catatan."','".$pengawas."','".$asisten."','".$mengetahui."','".$_SESSION['standard']['userid']."')";
		//exit("Error:$i");		
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
		
		#pengawas semua QC
		$optMandor="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$j="select karyawanid,namakaryawan,nik from ".$dbname.".datakaryawan  where lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment 
			where regional='".$_SESSION['empl']['regional']."' and kodeunit like '%RO%')  and bagian='QC'";
		
		$k=mysql_query($j) or die (mysql_error($conn));
		while($l=mysql_fetch_assoc($k))
		{
			$optMandor.="<option value='".$l['karyawanid']."'>".$l['nik']." - ".$l['namakaryawan']."</option>";
		}
		
		#asisten/pengawas
		$optAstn="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$d="select karyawanid,namakaryawan,nik from ".$dbname.".datakaryawan  where 
			lokasitugas='".$kdDiv."'
			and kodejabatan in (select kodejabatan from ".$dbname.".sdm_5jabatan where  namajabatan like '%PENGAWAS%' or 
			namajabatan like '%KA. AFDELING%' or namajabatan like '%recorder%' or namajabatan like '%KASUB AFDELING%')";
		//exit("Error:$d");
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
		echo $optMandor."###".$optAstn."###".$optKadiv;
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
					 <td align=center>".$_SESSION['lang']['divisi']."</td>
					 <td align=center>".$_SESSION['lang']['afdeling']."</td>
					 <td align=center>".$_SESSION['lang']['blok']."</td>
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
			
			$ql2="select count(*) as jmlhrow from ".$dbname.".kebun_qc_hama where ".$kdDivLoad."  ".$perLoad."  ";// where kodeorg='".$kodeorg."' and periode='".$per."'
			//exit("Error:$ql2");
			//where kodeorg='".$kodeorg."' and periode='".$per."' order by lastupdate
			$query2=mysql_query($ql2) or die(mysql_error());
			while($jsl=mysql_fetch_object($query2)){
			$jlhbrs= $jsl->jmlhrow;
			}
			$i="select * from ".$dbname.".kebun_qc_hama where ".$kdDivLoad."  ".$perLoad."  limit ".$offset.",".$limit."";
			
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
				echo "<td align=left>".substr($d['blok'],0,6)."</td>";
				echo "<td align=left>".$d['blok']."</td>";
				echo "<td align=left>".$nmKar[$d['pengawas']]."</td>";
				echo "<td align=left>".$nmKar[$d['updateby']]."</td>";
				echo "<td align=center>
						<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"del('".tanggalnormal($d['tanggal'])."','".$d['blok']."','".$d['updateby']."');\">
						<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_qc_hama','".$d['tanggal'].','.$d['blok']."','','kebun_qc_hamaUlat_pdf',event)\">
						</td>";		
				echo "</tr>";/*<img src=images/application/application_edit.png class=resicon  caption='Edit' 
					onclick=\"edit('".tanggalnormal($d['tanggal'])."','".substr($d['blok'],0,4)."','".$d['blok']."');\">*/
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
			if($_SESSION['standard']['userid']==$userId)
			$i="delete from ".$dbname.".kebun_qc_hama where tanggal='".$tgl."' and blok='".$kdBlok."'";
			else
				exit("Error:You Can't Delete");
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