<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

?>	

<?php		

$_POST['method']==''?$method=$_GET['method']:$method=$_POST['method'];

$_POST['tanggal']==''?$tanggal=tanggalsystem($_GET['tanggal']):$tanggal=tanggalsystem($_POST['tanggal']);

$_POST['kodeblok']==''?$kodeblok=$_GET['kodeblok']:$kodeblok=$_POST['kodeblok'];


$tanggalpanen=tanggalsystem($_POST['tanggalpanen']);
$kodedivisi=$_POST['kodedivisi'];
$kodeafdeling=$_POST['kodeafdeling'];
$namapengawas=$_POST['namapengawas'];
$jumlahpekerja=$_POST['jumlahpekerja'];
$dosis=$_POST['dosis'];
$teraplikasi=$_POST['teraplikasi'];
$kondisilahan=$_POST['kondisilahan'];

$jamMulai=$_POST['jamMulai'];
$mntMulai=$_POST['mntMulai'];
$jamSelesai=$_POST['jamSelesai'];
$mntSelesai=$_POST['mntSelesai'];
$darijam=$jamMulai.":".$mntMulai;
$sampaijam=$jamSelesai.":".$mntSelesai;

$comment=$_POST['comment'];
$pengawas=$_POST['pengawas'];
$asisten=$_POST['asisten'];
$mengetahui=$_POST['mengetahui'];

$nojalur=$_POST['nojalur'];
$pkkdipupuk=$_POST['pkkdipupuk'];
$pkktdkdipupuk=$_POST['pkktdkdipupuk'];
$apltdkstandar=$_POST['apltdkstandar'];
$keterangan=$_POST['keterangan'];

$perSch=$_POST['perSch'];
$kdKebunSch=$_POST['kdKebunSch'];

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
	case'getAfdeling':
		$optAfd="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$i="SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi WHERE induk='".$kodedivisi."' AND tipe='AFDELING'";
		$n=mysql_query($i) or die (mysql_error($conn));
		while($d=mysql_fetch_assoc($n)) {
                    $optAfd.="<option value='".$d['kodeorganisasi']."'>".$d['namaorganisasi']."</option>";
		}
	echo $optAfd;
	break;
	
	case'getBlok':
		$optBlok="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$i="SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi WHERE induk='".$kodeafdeling."' AND tipe='BLOK'";
		//exit("Error:$i");
		$n=mysql_query($i) or die (mysql_error($conn));
		while($d=mysql_fetch_assoc($n)) {
                    $optBlok.="<option value='".$d['kodeorganisasi']."'>".$d['namaorganisasi']."</option>";
		}
	echo $optBlok;
	break;

	case'saveHeader':
		$i="INSERT INTO ".$dbname.".`kebun_qc_pemupukanht`(`kodeblok`,`tanggal`,`pengawas`,`darijam`,`sampaijam`,
                    `jumlahhk`,`dosis`,`teraplikasi`,`kondisilahan`,`idqc`,`divisi`,`mengetahui`,`comment`)
			
		values ('".$kodeblok."','".$tanggal."','".$namapengawas."','".$darijam."','".$sampaijam."','".$jumlahpekerja."',
                        '".$dosis."','".$teraplikasi."','".$kondisilahan."','".$asisten."','".$kodedivisi."','".$mengetahui."','".$comment."')";
				
				
		if(mysql_query($i))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}
	break;
	
	
	case'insertDetail':
	$i="INSERT INTO ".$dbname.".`kebun_qc_pemupukandt` 
                (`tanggal`, `kodeblok`, `nojalur`, `pkkdipupuk`, `pkktdkdipupuk`, `apltdkstandar`, `keterangan`)
		values ('".$tanggal."','".$kodeblok."','".$nojalur."','".$pkkdipupuk."','".$pkktdkdipupuk."','".$apltdkstandar."','".$keterangan."')";
        echo 'err'.$i;
		if(mysql_query($i)) {
		} else {
                    echo " Gagal,".addslashes(mysql_error($conn));
		}
	break;	
	
	case'updateDetail':
	$i="UPDATE ".$dbname.".`kebun_qc_pemupukandt` SET `pkkdipupuk`='".$pkkdipupuk."', `pkktdkdipupuk`='".$pkktdkdipupuk."', 
                `apltdkstandar`='".$apltdkstandar."', `keterangan`='".$keterangan."' WHERE
		`tanggal`='".$tanggal."' AND `kodeblok`='".$kodeblok."' AND `nojalur`='".$nojalur."'";
        echo 'err'.$i;
		if(mysql_query($i)) {
		} else {
                    echo " Gagal,".addslashes(mysql_error($conn));
		}
	break;	
	
	case'getKar':
		#pendamping
		$optKar="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$d="select karyawanid,namakaryawan,nik from ".$dbname.".datakaryawan  where lokasitugas='".$kodedivisi."'";
		$e=mysql_query($d) or die (mysql_error($conn));
		while($f=mysql_fetch_assoc($e))
		{
			$optKar.="<option value='".$f['karyawanid']."'>".$f['nik']." - ".$f['namakaryawan']."</option>";
		}
		
		#mengetahui (manager/kadiv)
		$optKadiv="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$g="select karyawanid,namakaryawan,nik from ".$dbname.".datakaryawan  where lokasitugas='".$kodedivisi."' and kodejabatan in (		
			select kodejabatan from ".$dbname.".sdm_5jabatan where kodejabatan in ('5'))";
		$h=mysql_query($g) or die (mysql_error($conn));
		while($i=mysql_fetch_assoc($h))
		{
			$optKadiv.="<option value='".$i['karyawanid']."'>".$i['nik']." - ".$i['namakaryawan']."</option>";
		}
		echo $optKar."###".$optKadiv;
	break;	

        #####LOAD DETAIL DATA	
	case 'loadDetail';	
	//No.Jalur	Pokok Dipupuk       Pokok Tdk Dipupuk	Aplikasi Tdk Standar	Keterangan	Aksi
        //`nojalur`, `pkkdipupuk`, `pkktdkdipupuk`, `apltdkstandar`, `keterangan`) 
		echo"<fieldset><legend>Data Tersimpan</legend>
			<table class=sortable cellspacing=1 border=0>
			 <thead>
				 <tr class=rowheader>
					<td align=center>".$_SESSION['lang']['nourut']." ".$_SESSION['lang']['jalur']."</td> 
					<td align=center>".$_SESSION['lang']['pokok']." ".$_SESSION['lang']['dipupuk']."</td>
					<td align=center>".$_SESSION['lang']['pokok']." ".$_SESSION['lang']['no']." ".$_SESSION['lang']['dipupuk']."</td> 
					<td align=center>".$_SESSION['lang']['apl']." ".$_SESSION['lang']['no']." ".$_SESSION['lang']['standar']."</td> 
					
					<td align=left>".$_SESSION['lang']['keterangan']."</td>					 
					<td align=center>".$_SESSION['lang']['action']."</td>
					
				 </tr>
			</thead>
			<tbody></fieldset>";
	
		$no=0;
		$a="SELECT * FROM ".$dbname.".kebun_qc_pemupukandt WHERE tanggal='".$tanggal."' AND kodeblok='".$kodeblok."' ";
		//exit("Error:$a");
		$b=mysql_query($a) or die(mysql_error());
		while($c=mysql_fetch_assoc($b)) {
                    $no+=1;
                    echo"<tr class=rowcontent>
                            <td align=right>".$c['nojalur']."</td>
                            <td align=right>".$c['pkkdipupuk']."</td>
                            <td align=right>".$c['pkktdkdipupuk']."</td>
                            <td align=right>".$c['apltdkstandar']."</td>
                            <td align=left>".$c['keterangan']."</td>
                            <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillFieldDetail('".$c['nojalur']."','".$c['pkkdipupuk']."','".$c['pkktdkdipupuk']."','".$c['apltdkstandar']."','".$c['keterangan']."');\" >
                            <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"DelDetail('".tanggalnormal($c['tanggal'])."','".$c['kodeblok']."','".$c['nojalur']."');\" ></td></tr>";
		}
		echo"</table>";
	break;	
	

	case'loadData':
	
		if($kdKebunSch!='') {
			$kodedivisiLoad="kodeblok like '%".$kdKebunSch."%'";
                } else { 
			$kodedivisiLoad="kodeblok!='' ";
                }
		if($perSch!='') {
			$perLoad="AND tanggal like '%".$perSch."%'";
                } else {
			$perLoad="";
                }
	
		echo"
		
			<table class=sortable cellspacing=1 border=0>
			 <thead>
				 <tr class=rowheader>
					<td align=center>".$_SESSION['lang']['nourut']."</td>
					 <td align=center>".$_SESSION['lang']['tanggal']."</td>
					 <td align=center>".$_SESSION['lang']['divisi']."</td>
					 <td align=center>".$_SESSION['lang']['afdeling']."</td>
					 <td align=center>".$_SESSION['lang']['blok']."</td>
					 <td align=center>".$_SESSION['lang']['pengawas']."</td>
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
			
			$ql2="SELECT count(*) as jmlhrow FROM ".$dbname.".kebun_qc_pemupukanht WHERE ".$kodedivisiLoad."  ".$perLoad."  ";// WHERE kodeorg='".$kodeorg."' AND periode='".$per."'
			//exit("Error:$ql2");
			//WHERE kodeorg='".$kodeorg."' AND periode='".$per."' order by lastupdate
			$query2=mysql_query($ql2) or die(mysql_error());
			while($jsl=mysql_fetch_object($query2)){
			$jlhbrs= $jsl->jmlhrow;
			}
			$i="SELECT * FROM ".$dbname.".kebun_qc_pemupukanht WHERE ".$kodedivisiLoad."  ".$perLoad."  limit ".$offset.",".$limit."";
			
			//echo $i;
			$n=mysql_query($i) or die(mysql_error());
			$no=$maxdisplay;
			while($d=mysql_fetch_assoc($n))
			{
				$arr="##".$d['kodeblok']."##".$d['tanggal']."";	
				$no+=1;
				echo "<tr class=rowcontent>";
				echo "<td align=center>".$no."</td>";
				echo "<td align=left>".tanggalnormal($d['tanggal'])."</td>";
				echo "<td align=left>".substr($d['kodeblok'],0,4)."</td>";
				echo "<td align=left>".substr($d['kodeblok'],0,6)."</td>";
				echo "<td align=left>".$d['kodeblok']."</td>";
				echo "<td align=left>".$nmKar[$d['pengawas']]."</td>";
				echo "<td align=center>
						<img src=images/application/application_delete.png class=resicon caption='Delete' onclick=\"del('".tanggalnormal($d['tanggal'])."','".$d['kodeblok']."');\">		
						<img onclick=datakeExcel(event,'".tanggalnormal($d['tanggal'])."','".$d['kodeblok']."') src=images/excel.jpg class=resicon title='MS.Excel'>
                                                <img onclick=datakePdf(event,'".tanggalnormal($d['tanggal'])."','".$d['kodeblok']."') src=images/pdf.jpg class=resicon title='PDF'></td>";
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
			$i="DELETE FROM ".$dbname.".kebun_qc_pemupukanht WHERE tanggal='".$tanggal."' AND kodeblok='".$kodeblok."'";
			
			if(mysql_query($i)) {
				$n="DELETE FROM ".$dbname.".kebun_qc_pemupukandt WHERE tanggal='".$tanggal."' AND kodeblok='".$kodeblok."'";
				if(mysql_query($n)) {
				} else {
                                    echo " Gagal,".addslashes(mysql_error($conn));
                                }
			} else {
                            echo " Gagal,".addslashes(mysql_error($conn));
                        }
		break;
		
		case'deleteDetail':
			$i="DELETE FROM ".$dbname.".kebun_qc_pemupukandt WHERE tanggal='".$tanggal."' AND kodeblok='".$kodeblok."' AND nojalur='".$nojalur."'";
			//exit("Error:$i");
			if(mysql_query($i))
                            echo"";
			else
                            echo " Gagal,".addslashes(mysql_error($conn));
		break;

	case'printExcel':
	
	break;
	
	case'printPdf':
	
	break;
	
                
	default;
}
?>