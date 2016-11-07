<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$levelpendidikan=$_POST['levelpendidikan'];
$tahunlulus=$_POST['tahunlulus'];
$spesialisasi=$_POST['spesialisasi'];
$gelar=$_POST['gelar'];
$namasekolah=$_POST['namasekolah'];
$nilai=$_POST['nilai'];
$pendidikankota=$_POST['pendidikankota'];
$keterangan=$_POST['pendidikanketerangan'];
$karyawanid=$_POST['karyawanid'];
$kode=$_POST['kode'];

if($nilai=='')
   $nilai=0;
if(isset($_POST['del']) and $_POST['del']=='true')
{
	$str="delete from ".$dbname.".sdm_karyawanpendidikan where kode=".$kode;
}
else if( isset($_POST['queryonly']))
{
	$str="select 1=1";
}
else
{
	$str="insert into ".$dbname.".sdm_karyawanpendidikan
	     (    `karyawanid`,
		      `levelpendidikan`,
			  `spesialisasi`,
			  `gelar`,
			  `tahunlulus`,
			  `namasekolah`,
			  `nilai`,
			  `kota`,
			  `keterangan`
		  )
		  values(".$karyawanid.",
		  ".$levelpendidikan.",
		  '".$spesialisasi."',
		  '".$gelar."',
		  '".$tahunlulus."',
		  '".$namasekolah."',
		  ".$nilai.",
		  '".$pendidikankota."',
		  '".$keterangan."'
		  )";
}
if(mysql_query($str))
   {
	 $str="select a.*,b.kelompok from ".$dbname.".sdm_karyawanpendidikan a,".$dbname.".sdm_5pendidikan b
	 		where a.karyawanid=".$karyawanid." 
	 		and a.levelpendidikan=b.levelpendidikan
			order by a.levelpendidikan desc";
	 $res=mysql_query($str);
	 $no=0;
	 while($bar=mysql_fetch_object($res))
	 {
	 $no+=1;	
	 echo"	  <tr class=rowcontent>
			  <td class=firsttd>".$no."</td>
			  <td>".$bar->kelompok."</td>			  
			  <td>".$bar->namasekolah."</td>
			  <td>".$bar->kota."</td>			  
			  <td>".$bar->spesialisasi."</td>			  
			  <td>".$bar->tahunlulus."</td>
			  <td>".$bar->gelar."</td>
			  <td>".$bar->nilai."</td>
			  <td>".$bar->keterangan."</td>
			  <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPendidikan('".$karyawanid."','".$bar->kode."');\"></td>
			</tr>";	 	
	 }
    }
	else
	{
		echo " Gagal:".addslashes(mysql_error($conn)).$str;
	}

?>
