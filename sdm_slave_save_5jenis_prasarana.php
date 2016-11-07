<?php
require_once('master_validation.php');
require_once('config/connection.php');
//param+='&satuan='+satuan+'&idKlmpk='+idKlmpk;
$kodejabatan=$_POST['kodejabatan'];
$namajabatan=$_POST['namajabatan'];
$satuan=$_POST['satuan'];
$idKlmpk=$_POST['idKlmpk'];
$method=$_POST['method'];

switch($method)
{
case 'update':	
	$str="update ".$dbname.".sdm_5jenis_prasarana set nama='".$namajabatan."',satuan='".$satuan."'
	       where jenis='".$kodejabatan."' and kelompok='".$idKlmpk."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case 'insert':
	$str="insert into ".$dbname.".sdm_5jenis_prasarana (jenis,nama,satuan,kelompok)
	      values('".$kodejabatan."','".$namajabatan."','".$satuan."','".$idKlmpk."')";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}	
	break;
case 'delete':
	$str="delete from ".$dbname.".sdm_5jenis_prasarana
	where jenis='".$kodejabatan."' and kelompok='".$idKlmpk."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
default:
   break;					
}
$sKlmpk="select distinct * from ".$dbname.".sdm_5kl_prasarana order by kode asc";
$qKlmpk=mysql_query($sKlmpk) or die(mysql_error());
while($rKlmpk=mysql_fetch_assoc($qKlmpk))
{
    $orgNmKlmpk[$rKlmpk['kode']]=$rKlmpk['nama'];
}
$str1="select * from ".$dbname.".sdm_5jenis_prasarana order by nama";
if($res1=mysql_query($str1))
{
echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
	     <thead>
		 <tr class=rowheader><td>".$_SESSION['lang']['namakelompok']."</td><td>".$_SESSION['lang']['jenis']."</td><td>".$_SESSION['lang']['namajenisvhc']."</td><td>".$_SESSION['lang']['satuan']."</td><td style='width:30px;'>*</td></tr>
		 </thead>
		 <tbody>";
	while($bar1=mysql_fetch_object($res1))
	{
		echo"<tr class=rowcontent>
                    <td align=center>".$orgNmKlmpk[$bar1->kelompok]."</td>
                    <td>".$bar1->jenis."</td>
                    <td>".$bar1->nama."</td>
                    <td>".$bar1->satuan."</td>
                        
                    <td align=center><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kelompok."','".$bar1->jenis."','".$bar1->satuan."','".$bar1->nama."');\"></td></tr>";
	}	 
echo"	 
	 </tbody>
	 <tfoot>
	 </tfoot>
	 </table>";
}
?>
