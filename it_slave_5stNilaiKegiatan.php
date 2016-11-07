<?php
require_once('master_validation.php');
require_once('config/connection.php');
//##kdkegiatan##ket##satuan##nilsngtbaik##nilbaik##nilckp##nilkrg##method
$kdkegiatan=$_POST['kdkegiatan'];
$ket=$_POST['ket'];
$satuan=$_POST['satuan'];
$nilsngtbaik=$_POST['nilsngtbaik'];
$nilbaik=$_POST['nilbaik'];
$nilckp=$_POST['nilckp'];
$nilkrg=$_POST['nilkrg'];
$method=$_POST['method'];

switch($method)
{
case 'update':	
	$str="update ".$dbname.".it_standard set keterangan='".$ket."',
	      satuan='".$satuan."',sangatbaik='".$nilsngtbaik."',baik='".$nilbaik."',cukup='".$nilckp."',kurang='".$nilkrg."'
	       where kodekegiatan='".$kdkegiatan."'";
            //exit("Error:".$str);
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case 'insert':
	$str="insert into ".$dbname.".it_standard (kodekegiatan,keterangan,satuan,sangatbaik,baik,cukup,kurang)
	      values('".$kdkegiatan."','".$ket."','".$satuan."','".$nilsngtbaik."','".$nilbaik."','".$nilckp."','".$nilkrg."')";
        //exit("Error:".$str);
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}	
	break;
case 'delete':
	$str="delete from ".$dbname.".it_standard
	where kodekegiatan='".$kdkegiatan."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
        case'loadData':
        $str1="select * from ".$dbname.".it_standard order by kodekegiatan asc";
if($res1=mysql_query($str1))
{
	echo"<table class=sortable cellspacing=1 border=0 style='width:800px;'>
	     <thead>
		 <tr class=rowheader>
                 <td style='width:150px;'>".$_SESSION['lang']['kodekegiatan']."</td>
                 <td>".$_SESSION['lang']['keterangan']."</td>
                 <td>".$_SESSION['lang']['satuan']."</td>
                <td>Sangat Baik</td>
                <td>Baik</td>
                <td>Cukup</td>
                <td>Kurang</td>
                 <td style='width:70px;'>*</td></tr>
		 </thead>
		 <tbody>";
	while($bar1=mysql_fetch_object($res1))
	{
		echo"<tr class=rowcontent>
                     <td align=center>".$bar1->kodekegiatan."</td>
                     <td>".$bar1->keterangan."</td><td>".$bar1->satuan."</td>
                        <td>".$bar1->sangatbaik."</td>
                        <td>".$bar1->baik."</td>
                        <td>".$bar1->cukup."</td>
                        <td>".$bar1->kurang."</td>
                     <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar1->kodekegiatan."','".$bar1->keterangan."','".$bar1->satuan."','".$bar1->sangatbaik."','".$bar1->baik."','".$bar1->cukup."','".$bar1->kurang."');\"> 
                         <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPendidikan('".$bar1->kodekegiatan."');\"></td></tr>";
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
