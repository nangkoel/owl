<?php
require_once('master_validation.php');
require_once('config/connection.php');

$kodeorg=$_POST['kodeorg'];
$kode=$_POST['kode'];
$tahun=$_POST['tahun'];
$jumlah=$_POST['jumlah'];
$keterangan=$_POST['keterangan'];
$method=$_POST['method'];
		

if($jumlah=='')
   $jumlah=0;

switch($method)
{
case 'update':	
	$str="update ".$dbname.".sdm_5catu set 
	       jumlah=".$jumlah.",
	       keterangan='".$keterangan."'
	       where kodeorg='".$kodeorg."' and kelompok='".$kode."'
	       and tahun='".$tahun."'";
    
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case 'insert':
	$str="insert into ".$dbname.".sdm_5catu 
	      (kodeorg, tahun, kelompok, keterangan, jumlah)
	      values('".$kodeorg."',".$tahun.",'".$kode."','".$keterangan."',".$jumlah.")";
    
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn))."__".$str;}	
	break;
case 'delete':
	$str="delete from ".$dbname.".sdm_5catu
	 where kodeorg='".$kodeorg."' and kelompok='".$kode."'
	 and tahun=".$tahun;
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
default:
   break;					
}
	$str1="select *
	     from ".$dbname.".sdm_5catu 
		   where kodeorg='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
		  order by tahun desc,kelompok"; 
	$res1=mysql_query($str1);

	while($bar1=mysql_fetch_object($res1))
	{
		echo"<tr class=rowcontent>
		        <td align=center>".$bar1->kodeorg."</td>
                                        <td align=center>".$bar1->tahun."</td>
                                        <td align=center>".$bar1->kelompok."</td>    
                                         <td>".$bar1->keterangan."</td>    
                                        <td align=right>".$bar1->jumlah."</td>
                                       
                                        <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodeorg."','".$bar1->tahun."','".$bar1->kelompok."','".$bar1->keterangan."','".$bar1->jumlah."');\"></td></tr>";
	}				 

?>
