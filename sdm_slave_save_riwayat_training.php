<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$jenistraining=$_POST['jenistraining'];
$judultraining=$_POST['judultraining'];
$penyelenggara=$_POST['penyelenggara'];
$trainingblnmulai=$_POST['trainingblnmulai'];
$trainingthnmulai=$_POST['trainingthnmulai'];
$trainingblnselesai=$_POST['trainingblnselesai'];
$trainingthnselesai=$_POST['trainingthnselesai'];
$sertifikat=$_POST['sertifikat'];
$biaya=$_POST['biaya'];

$karyawanid=$_POST['karyawanid'];
$nomor=$_POST['nomor'];

if($nilai=='')
   $nilai=0;
if(isset($_POST['del']) or ($trainingblnmulai!='' and $trainingthnmulai!='' and $trainingblnselesai!='' and $trainingthnselesai!='') or isset($_POST['queryonly']))
{
if(isset($_POST['del']) and $_POST['del']=='true')
{
	$str="delete from ".$dbname.".sdm_karyawantraining where nomor=".$nomor;
}
else if(isset($_POST['queryonly']))
{
	$str="select 1=1";
}
else
{
$trainingblnmulai=$trainingblnmulai."-".$trainingthnmulai;
$trainingblnselesai=$trainingblnselesai."-".$trainingthnselesai;
	$str="insert into ".$dbname.".sdm_karyawantraining
	     (	`karyawanid`,
			`jenistraining`,
			`bulanmulai`,
			`bulanselesai`,
			`judultraining`,
			`penyelenggara`,
			`sertifikat`,
                        `biaya`
		  )
		  values(".$karyawanid.",
		  '".$jenistraining."',
		  '".$trainingblnmulai."',
		  '".$trainingblnselesai."',
		  '".$judultraining."',
		  '".$penyelenggara."',
		  ".$sertifikat.",
                  ".$biaya."
		  )";
}
if(mysql_query($str))
   {
	 $str="select *,case sertifikat when 0 then 'N' else 'Y' end as bersertifikat 
	       from ".$dbname.".sdm_karyawantraining
	 		where karyawanid=".$karyawanid." 
			order by bulanmulai desc";	
	 $res=mysql_query($str);
	 $no=0;
	 while($bar=mysql_fetch_object($res))
	 {
	 $no+=1;	
	 echo"	  <tr class=rowcontent>
			  <td class=firsttd>".$no."</td>
			  <td>".$bar->jenistraining."</td>			  
			  <td>".$bar->judultraining."</td>
			  <td>".$bar->penyelenggara."</td>			  
			  <td>".$bar->bulanmulai."</td>			  
			  <td>".$bar->bulanselesai."</td>
			  <td>".$bar->bersertifikat."</td>
                          <td align=right>".$bar->biaya."</td>
			  <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delTraining('".$karyawanid."','".$bar->nomor."');\"></td>
			</tr>";	 	
	 }
    }
	else
	{
		echo " Gagal:".addslashes(mysql_error($conn)).$str;
	}
}
else
{
	echo " Error; Data incomplete";
}
?>
