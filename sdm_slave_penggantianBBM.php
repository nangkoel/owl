<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$periode	=$_POST['periode'];
$kodeorg	=substr($_SESSION['empl']['lokasitugas'],0,4);
$karyawanid	=$_POST['karyawanid'];
$pt			=$_POST['pt'];   	
$notransaksi=$_POST['notransaksi'];
$keterangan	=$_POST['keterangan'];
$bytransport=$_POST['bytransport'];
$byperawatan=$_POST['byperawatan'];
$bytoll		=$_POST['bytoll'];
$bylain		=$_POST['bylain'];
$total		=$_POST['total'];
$method		=$_POST['method'];
$userid=$_SESSION['standard']['userid'];            
			
if($method=='delete')
{
	$str="delete from ".$dbname.".sdm_penggantiantransport where notransaksi='".$notransaksi."'";
}			
else if($method=='insert')
{
	$str="insert into ".$dbname.".sdm_penggantiantransport 
	      (`notransaksi`,`karyawanid`,`periode`,
		  `keterangan`,`toll`,`trans`,
		  `perawatan`,`kodeorg`,`alokasi`,
		  `updateby`,`bylain`,`totalklaim`)
		  values(
		   '".$notransaksi."',".$karyawanid.",'".$periode."',
		   '".$keterangan."',".$bytoll.",".$bytransport.",
		   ".$byperawatan.",'".$kodeorg."','".$pt."',
		   ".$userid.",".$bylain.",".$total."
		  )";
}
else if($method=='update')
{
	$str="update ".$dbname.".sdm_penggantiantransport
	      set 
		  `karyawanid`=".$karyawanid.",
		  `periode`='".$periode."',
		  `keterangan`='".$keterangan."',
		  `toll`=".$bytoll.",
		  `trans`=".$bytransport.",
		  `perawatan`=".$byperawatan.",
		  `kodeorg`='".$kodeorg."',
		  `alokasi`='".$pt."',
		  `updateby`=".$userid.",
		  `bylain`=".$bylain.",
		  `totalklaim`=".$total."
		  where notransaksi='".$notransaksi."'";
}
else
{
	$str="select 1=1";
}
if(mysql_query($str))
{
	if($periode=='')
	   $periode=date('Y-m');
	$str="select a.*,sum(b.jlhbbm) as bbm,c.namakaryawan from ".$dbname.".sdm_penggantiantransport a
	      left join ".$dbname.".sdm_penggantiantransportdt b 
		  on a.notransaksi=b.notransaksi
		  left join ".$dbname.".datakaryawan c
		  on a.karyawanid=c.karyawanid
		   where periode='".$periode."' and 
		  kodeorg='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
		  group by notransaksi";
	$res=mysql_query($str);
	$no=0;
	while($bar=mysql_fetch_object($res))
	{
		$no+=1;
		$add='';
	if($bar->posting==0)
	{
		$add.=" <img src='images/close.png' class=resicon onclick=deleteBBM('".$bar->notransaksi."') title='delete'>";
		//$add.=" <img src='images/tool.png' class=resicon onclick=editBBM('".$bar->notransaksi."') title='edit'>";
	}
		$add.=" <img src='images/pdf.jpg' class=resicon onclick=previewBBM('".$bar->notransaksi."',event) title='view'>";
		echo"<tr class=rowcontent>
		     <td>".$no."</td>
			 <td>".$bar->notransaksi."</td>
			 <td>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</td>
			 <td>".$bar->alokasi."</td>
			 <td>".$bar->namakaryawan."</td>
			 <td align=right>".number_format($bar->totalklaim,2,',','.')."</td>
			 <td align=right>".number_format($bar->dibayar,2,',','.')."</td>
			 <td>".tanggalnormal($bar->tanggalbayar)."</td>
			 <td align=right>".number_format($bar->bbm,2,',','.')."</td>
			 <td>".$bar->keterangan."</td>	
			 <td>".$add."</td>	 
		   </tr>";	
	}	
}
else
{
	echo " Gagal ".addslashes(mysql_error($conn));
}
?>