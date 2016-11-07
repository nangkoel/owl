<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
?>	

<?php		

$nmBrg=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang');
$_POST['method']==''?$method=$_GET['method']:$method=$_POST['method'];

$_POST['tanggal']==''?$tanggal=tanggalsystem($_GET['tanggal']):$tanggal=tanggalsystem($_POST['tanggal']);

$_POST['kodeblok']==''?$kodeblok=$_GET['kodeblok']:$kodeblok=$_POST['kodeblok'];

$barang=$_POST['barang'];
$tanggalpanen=tanggalsystem($_POST['tanggalpanen']);
$kodedivisi=$_POST['kodedivisi'];
$kodeafdeling=$_POST['kodeafdeling'];
//$kodeblok=$_POST['kodeblok'];
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
                    `jumlahhk`,`dosis`,`teraplikasi`,`kondisilahan`,`idqc`,`divisi`,`mengetahui`,`comment`,kodebarang)
			
		values ('".$kodeblok."','".$tanggal."','".$namapengawas."','".$darijam."','".$sampaijam."','".$jumlahpekerja."',
                        '".$dosis."','".$teraplikasi."','".$kondisilahan."','".$asisten."','".$kodedivisi."','".$mengetahui."','".$comment."','".$barang."')";
								
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
			lokasitugas='".$kodedivisi."'
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
		echo $optMandor."###".$optAstn."###".$optKadiv;
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
					 <td align=center>Divisi</td>
					 <td align=center>".$_SESSION['lang']['afdeling']."</td>
					 <td align=center>".$_SESSION['lang']['blok']."</td>
					 <td align=center>Nama Pengawas</td>
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
						<img src=images/application/application_delete.png class=resicon title='Delete' caption='Delete' onclick=\"del('".tanggalnormal($d['tanggal'])."','".$d['kodeblok']."');\">		
						<img onclick=datakeExcel(event,'".tanggalnormal($d['tanggal'])."','".$d['kodeblok']."') src=images/excel.jpg class=resicon title='MS.Excel'>
                                                <img onclick=\"previewQCPemupukanPDF('".$d['tanggal']."','".$d['kodeblok']."',event)\" class=\"resicon\" src=\"images/pdf.jpg\">";
                                                //<img onclick=datakePdf(event,'".tanggalnormal($d['tanggal'])."','".$d['kodeblok']."') src=images/pdf.jpg class=resicon title='PDF'></td>";
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
	
        $i="select * from ".$dbname.".kebun_qc_pemupukanht where kodeblok='".$kodeblok."' and tanggal='".$tanggal."'  ";
	$n=mysql_query($i) or die (mysql_error($conn));
	//$jml_brs=mysql_num_rows($n);
                //exit("error:$jml_brs");
        $d=mysql_fetch_assoc($n);
               
        //mencari topografi
        $str_sql_topo="select*from ".$dbname.".setup_blok where kodeorg='".$kodeblok."'";
        $query_topo=mysql_query($str_sql_topo) or die (mysql_error($conn));
        while($b=mysql_fetch_array($query_topo))
	{
            $topo=$b['topografi'];
            //$topo=$b->topografi; untuk mysql_fetch_object
        }
        
        //mencari nama pengawas
        $str_sql="select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$d['pengawas']."'";
	$coba1=mysql_query($str_sql) or die (mysql_error($conn));
        $coba2=mysql_fetch_assoc($coba1);
        
        //mencari nama qc
        $str_sql_idqc="select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$d['idqc']."'";
	$query_sql_idqc=mysql_query($str_sql_idqc) or die (mysql_error($conn));
        $exec_query_idqc=mysql_fetch_assoc($query_sql_idqc);
        
        //mencari nama mengetahui
        $str_sql2="select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$d['mengetahui']."'";
	$coba12=mysql_query($str_sql) or die (mysql_error($conn));
        $coba22=mysql_fetch_assoc($coba12);
        
	//print_r($_SESSION['org']['namaorganisasi']);
	$ctkexcel=$_SESSION['org']['namaorganisasi'];
	$ctkexcel.="<BR>QUALITY CONTROL";
	
	$ctkexcel.="<table>
                    <tr>
                        <td colspan=9 align=center><b><u>CHECKLIST PEMUPUKAN</u></b></td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Tanggal</td><td colspan=4>: ".tanggalnormal($d['tanggal'])."</td>
                        <td colspan=2>Jam Kerja</td><td colspan=2>: ".$d['darijam']." s.d ".$d['sampaijam']."</td>                        
                    </tr>
                    <tr>
                        <td>Divisi</td><td colspan=4>: ".$d['divisi']."</td>
                        <td colspan=2>Jumlah Pekerja</td><td colspan=2>: ".$d['jumlahhk']."</td>
                    </tr>
                    <tr>
                        <td>".$_SESSION['lang']['afdeling']."</td><td colspan=4>: ".substr($d['kodeblok'],0,6)."</td>
                        <td colspan=2>Pupuk & Dosis</td><td colspan=2>: ".$nmBrg[$d['kodebarang']].", Dosis : ".$d['dosis']."</td>
                    </tr>
                    <tr>
                        <td>".$_SESSION['lang']['blok']."</td><td colspan=4>: ".$d['kodeblok']."</td>
                        <td colspan=2>Total pupuk teraplikasi</td><td colspan=2>: ".$d['teraplikasi']." Sak</td>
                    </tr>
                    <tr>
                        <td>Topo</td><td colspan=4>: ".$topo."</td>
                        <td colspan=2>Kondisi Lahan</td><td colspan=2>: ".$d['kondisilahan']."</td>
                    </tr>
                    <tr>
                        <td>Nama Pengawas</td><td colspan=2>: ".$coba2['namakaryawan']."</td>
                    </tr>
                    <tr></tr>                                
                  </table>";
	
	$ctkexcel.="<table class=sortable border=1 cellspacing=1>
                    <thead>
                        <tr>
                            <td align=center valign=center rowspan=2 bgcolor=#CCCCCC>
                                <p align=center style=margin-top:0; margin-bottom:0>No.Jalur
                                <p align=center style=margin-top:0; margin-bottom:0>Diperiksa
                            </td>

                            <td align=center valign=center  rowspan=2 colspan=2 bgcolor=#CCCCCC>
                                <p align=center style=margin-top:0; margin-bottom:0>Jumlah Pokok
                                <p align=center style=margin-top:0; margin-bottom:0>dipupuk
                            </td>
                            <td align=center valign=top  rowspan=2 colspan=2 bgcolor=#CCCCCC>
                             <p align=center style=margin-top:0; margin-bottom:0>Missed Out Palms
                             <p align=center style=margin-top:0; margin-bottom:0>(Jumlah pokok tdk dipupuk)
                            </td>

                            <td align=center valign=top  rowspan=2 bgcolor=#CCCCCC>
                             <p align=center style=margin-top:0; margin-bottom:0>Aplikasi
                             <p align=center style=margin-top:0; margin-bottom:0>Tdk Standar
                            </td>

                            <td align=center valign=top  rowspan=2 colspan=3 bgcolor=#CCCCCC><p align=center>Keterangan</td>
                        </tr>";
					 
	$w="select * from ".$dbname.".kebun_qc_pemupukandt where kodeblok='".$kodeblok."' and tanggal='".$tanggal."' order by nojalur asc";
	$i=mysql_query($w) or die (mysql_error($conn));
        //$jml_brs=mysql_num_rows($i);
                //exit("error:$jml_brs");
	while($b=mysql_fetch_assoc($i))
	{
            $ctkexcel.="<table class=sortable border=1 cellspacing=1>
                        <tr class=rowcontent>
                            <td align=center>".$b['nojalur']."</td>
                            <td align=center colspan=2>".$b['pkkdipupuk']."</td>
                            <td align=center colspan=2>".$b['pkktdkdipupuk']."</td>
                            <td align=center>".$b['apltdkstandar']."</td>
                            <td align=center colspan=3>".$b['keterangan']."</td>
                        </tr>";

            $totjmlpkkdipupuk+=$b['pkkdipupuk'];
            $totjmlpkktdkdipupuk+=$b['pkktdkdipupuk'];
            $totapltdkstandar+=$b['apltdkstandar'];
	}
        
        $totket=round(($totjmlpkktdkdipupuk/($totjmlpkkdipupuk+$totjmlpkktdkdipupuk))*100,0);

        $ctkexcel.="<tr>
                    <td align=center>".$_SESSION['lang']['total']." :</td>
                    <td align=center colspan=2>".$totjmlpkkdipupuk."</td>
                    <td align=center colspan=2>".$totjmlpkktdkdipupuk."</td>
                    <td align=center>".$totapltdkstandar."</td>

                    <td align=center colspan=3>".$totket."%</td>
                  </tr>
                  </table>";
	
	$ctkexcel.="<table>
                    <tr></tr>
                    <tr>
                        <td valign=top rowspan=5>Comment :</td><td align=justify valign=top rowspan=5 colspan=8>".$d['comment']."</td>
                    </tr>
                  </table>
                  
                  <table>
                    <tr></tr>
                    <tr>
                        <td colspan=4>Yang melakukan pemeriksaan</td>
                    </tr>
                    <tr>
                        <td align=left colspan=3>Quality Control</td>
                        <td align=left colspan=3>Divisi</td>
                        <td align=left colspan=3>Mengetahui</td>
                    </tr>
                    <tr></tr>
                    <tr></tr>
                    <tr></tr>
                    <tr></tr>
                        <td align=left colspan=3><u>".$exec_query_idqc['namakaryawan']."</u></td>
                        <td align=left colspan=3>________________</td>
                        <td align=left colspan=3><u>".$coba22['namakaryawan']."</u></td>
                    </tr>                        
                    <tr></tr>
                    <tr>
                        <td><u>Distribusi :</u></td>
                    </tr>
                    <tr>
                        <td>1. GM Operational</td>
                    </tr>
                    <tr>
                        <td>2. Ka.Divisi</td>
                    </tr>
                  </table>";             
	
        //$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
        //$tglSkrg=date("Ymd");
        $nop_="QC_Checklist_Pemupukan_".tanggalnormal($d['tanggal']);
		//exit("Error:$ctkexcel");
        if(strlen($ctkexcel)>0)
        {
            if ($handle = opendir('tempExcel')) 
            {
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
            if(!fwrite($handle,$ctkexcel))
            {
                echo "<script language=javascript1.2>
                parent.window.alert('Tidak dapat mengkonversi ke excel!');
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
                
	default;
}
?>