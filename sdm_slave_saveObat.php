<?php
require_once('master_validation.php');
require_once('config/connection.php');

$namaobat=$_POST['namaobat'];
$notransaksi=$_POST['notransaksi'];
	
if(isset($_POST['del']))
{
	$str="delete from ".$dbname.".sdm_pengobatandt where id=".$_POST['id'];
}	
else
{
	$str="insert  into ".$dbname.".sdm_pengobatandt(notransaksi,namaobat,jenis)
	      values('".$_POST['notransaksi']."','".$_POST['namaobat']."','".$_POST['jenisobat']."')"; 
}
if(mysql_query($str))
{

}
else
{
	echo mysql_error($conn);
}

//====================
	$str="select * from ".$dbname.".sdm_pengobatandt 
	      where notransaksi='".$_POST['notransaksi']."'"; 
    $res=mysql_query($str);
	$no=0;
	while($bar=mysql_fetch_object($res))
	{
		$no+=1;
		echo"<tr class=rowcontent>
		    <td>".$no."</td>
			<td>".$bar->notransaksi."</td>
			<td>".$bar->namaobat."</td>
                                                             <td>".$bar->jenis."</td> 
			<td>
			  <img src=images/close.png class=resicon onclick=deleteObat('".$bar->id."','".$bar->notransaksi."')>
			</td>
		   </tr>";
	}
?>