<?php
require_once('master_validation.php');
require_once('config/connection.php');

$kodeorg=$_POST['kodeorg'];
$blok=$_POST['blok'];
$norumah=$_POST['norumah'];

$str=" select a.kodeasset,b.tipeasset,b.namasset,b.status,b.keterangan,
       case b.status
	   when 0 then 'Retire/pensiun'
	   when 1 then 'Aktif'
	   when 2 then 'Broken/Rusak'
	   when 3 then 'Missing'
	   else 'Unknown'
	   end as sts 
       from ".$dbname.".sdm_perumahandt a 
       left join ".$dbname.".sdm_daftarasset b
	   on a.kodeasset=b.kodeasset
	   where a.kodeorg='".$kodeorg."' and a.blok='".$blok."'
	   and a.norumah='".$norumah."'";
	   	   
$res=mysql_query($str);
echo mysql_error($conn);
$no=0;
echo"".$_SESSION['lang']['kodeorg'].": ".$kodeorg."<br>
     ".$_SESSION['lang']['blok'].": ".$blok."<br>
	 ".$_SESSION['lang']['no_rmh'].": ".$norumah."
     <table class=sortable cellspacing=1 border=0>
     <thead><tr class=rowheader>
	    <td>No.</td>
		<td>".$_SESSION['lang']['kodeasset']."</td>		
		<td>".$_SESSION['lang']['tipeasset']."</td>
		<td>".$_SESSION['lang']['namaaset']."</td>
		<td>".$_SESSION['lang']['status']."</td>
		<td>".$_SESSION['lang']['keterangan']."</td>
		</tr>
	 </thead><tbody>";
while($bar=mysql_fetch_object($res))
{
	$no+=1;
 echo"<tr class=rowcontent>
		 <td>".$no."</td>
		 <td>".$bar->kodeasset."</td>
		 <td>".$bar->tipeasset."</td>
		 <td>".$bar->namasset."</td>
		 <td>".$bar->sts."</td>
		 <td>".$bar->keterangan."</td>
		 </tr>";	
}
 echo"</tbody><tfoot></tfoot></table>";
?>
