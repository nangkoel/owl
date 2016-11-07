<?php
require_once('master_validation.php');
require_once('config/connection.php');

	$kodeorg		=$_POST['kodeorg'];
    $switch			=$_POST['switch'];
	$npwp			=$_POST['npwp'];
	$alamatnpwp		=$_POST['alamatnpwp'];
	$alamatdom		=$_POST['alamatdom'];

switch($switch)
{
   case 'delete':
	$stry="delete from ".$dbname.".setup_org_npwp where kodeorg='".$kodeorg."'"; 
    break;
   default:
    $strx="select * from ".$dbname.".setup_org_npwp where kodeorg='".$kodeorg."' limit 1";   	
    $res1=mysql_query($strx);
	if(mysql_num_rows($res1)>0) 
	{
		$stry="update ".$dbname.".setup_org_npwp 
		set alamatnpwp='".$alamatnpwp."',
		npwp='".$npwp."',
		alamatdomisili='".$alamatdom."'
		where kodeorg='".$kodeorg."'";
	}
	else
	{
		$stry="insert into ".$dbname.".setup_org_npwp(kodeorg,alamatnpwp,npwp,alamatdomisili)
		values('".$kodeorg."','".$alamatnpwp."','".$npwp."','".$alamatdom."')";
	}
   	break;
}

if(mysql_query($stry))
{
  $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PT' order by namaorganisasi";
   $res=mysql_query($str);

  while($bar=mysql_fetch_object($res))
  {	 
	   $alamatnpwp='';
	   $npwp	  ='';
	   $alamatdom =''; 
	$str1="select * from ".$dbname.".setup_org_npwp where kodeorg='".$bar->kodeorganisasi."' order by kodeorg";
	$res1=mysql_query($str1);
	while($bar1=mysql_fetch_object($res1))
	{
	   $alamatnpwp=$bar1->alamatnpwp;
	   $npwp	  =$bar1->npwp;
	   $alamatdom =$bar1->alamatdomisili; 
	}
 
	echo"<tr class=rowcontent>
	  <td>".$bar->kodeorganisasi."</td>
	  <td>".$bar->namaorganisasi."</td>
	  <td>".$npwp."</td>
	  <td>".$alamatnpwp."</td>
	  <td>".$alamatdom."</td>
	  <td>
		  <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delnpwp('".$bar->kodeorganisasi."');\">
	  </td>
	  </tr>";
  }
  }
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn)).$stry;
	}
?>
