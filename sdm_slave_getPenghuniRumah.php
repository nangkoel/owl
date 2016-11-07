<?php
require_once('master_validation.php');
require_once('config/connection.php');

$kodeorg=$_POST['kodeorg'];
$blok=$_POST['blok'];
$norumah=$_POST['norumah'];

$str=" select a.karyawanid,b.namakaryawan,b.lokasitugas,b.bagian,b.jeniskelamin 
       from ".$dbname.".sdm_penghunirumah a 
       left join ".$dbname.".datakaryawan b
	   on a.karyawanid=b.karyawanid
	   where a.kodeorg='".$kodeorg."' and a.blok='".$blok."'
	   and a.norumah='".$norumah."'";	   
$res=mysql_query($str);
$no=0;
echo"".$_SESSION['lang']['kodeorg'].": ".$kodeorg."<br>
     ".$_SESSION['lang']['blok'].": ".$blok."<br>
	 ".$_SESSION['lang']['no_rmh'].": ".$norumah."
     <table class=sortable cellspacing=1 border=0>
     <thead><tr class=rowheader>
	    <td>No.</td>
		<td>".$_SESSION['lang']['nokaryawan']."</td>
		<td>".$_SESSION['lang']['namakaryawan']."</td>
		<td>".$_SESSION['lang']['jeniskelamin']."</td>
		<td>".$_SESSION['lang']['lokasitugas']."</td>
		<td>".$_SESSION['lang']['bagian']."</td>
		</tr>
	 </thead><tbody>";
while($bar=mysql_fetch_object($res))
{
	$no+=1;
 echo"<tr class=rowcontent>
		 <td>".$no."</td>
		 <td>".$bar->karyawanid."</td>
		 <td>".$bar->namakaryawan."</td>
		 <td>".$bar->jeniskelamin."</td>
		 <td>".$bar->lokasitugas."</td>
		 <td>".$bar->bagian."</td>
		 </tr>";	
}
 echo"</tbody><tfoot></tfoot></table>";
?>
