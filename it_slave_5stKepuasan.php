<?php
require_once('master_validation.php');
require_once('config/connection.php');
//$arr="##kode##nilKode##ket##method";
$kode=$_POST['kode'];
$ket=$_POST['ket'];
$nilKode=$_POST['nilKode'];

$method=$_POST['method'];

switch($method)
{
case 'update':	
	$str="update ".$dbname.".it_stkepuasan set keterangan='".$ket."',
	       where kode='".$kode."' and nilai='".$nilKode."'";
            //exit("Error:".$str);
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case 'insert':
	$str="insert into ".$dbname.".it_stkepuasan (kode,nilai,keterangan)
	      values('".$kode."','".$nilKode."','".$ket."')";
        //exit("Error:".$str);
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}	
	break;
case 'delete':
	$str="delete from ".$dbname.".it_stkepuasan
	where kode='".$kode."' and nilai='".$nilKode."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
        case'loadData':
        $str1="select * from ".$dbname.".it_stkepuasan order by kode asc";
if($res1=mysql_query($str1))
{
	echo"<table class=sortable cellspacing=1 border=0 style='width:650px;'>
	     <thead>
		 <tr class=rowheader>
                 <td style='width:150px;'>".$_SESSION['lang']['kodekegiatan']."</td>
                 <td>Nilai</td>
                 <td>".$_SESSION['lang']['keterangan']."</td>
                 <td style='width:70px;'>*</td></tr>
		 </thead>
		 <tbody>";
	while($bar1=mysql_fetch_object($res1))
	{
		echo"<tr class=rowcontent>
                     <td align=center>".$bar1->kode."</td>
                     <td>".$bar1->nilai."</td><td>".$bar1->keterangan."</td>
                     <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar1->kode."','".$bar1->nilai."','".$bar1->keterangan."');\"> 
                         <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPendidikan('".$bar1->kode."','".$bar1->nilai."');\"></td></tr>";
	}	 
	echo"	 
		 </tbody>
		 <tfoot>
		 </tfoot>
		 </table>";
}
        break;
default:
   break;					
}

?>
