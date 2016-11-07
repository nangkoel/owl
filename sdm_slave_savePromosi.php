<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$tanggalsk	=tanggalsystem($_POST['tanggalsk']);
$tanggalberlaku	=tanggalsystem($_POST['tanggalberlaku']);
$oldgaji	=$_POST['oldgaji'];
$newgaji	=$_POST['newgaji'];
$penandatangan	=$_POST['penandatangan'];
$tembusan1	=$_POST['tembusan1'];
$tembusan2	=$_POST['tembusan2'];
$tembusan3	=$_POST['tembusan3'];
$tembusan4	=$_POST['tembusan4'];
$tembusan5	=$_POST['tembusan5'];
$tipetransaksi	=$_POST['tipetransaksi'];
$karyawanid	=$_POST['karyawanid'];
$oldokasitugas	=$_POST['oldokasitugas'];
$oldlokasitugassub=$_POST['oldlokasitugassub'];
$oldjabatan	=$_POST['oldjabatan'];
$oldtipekaryawan=$_POST['oldtipekaryawan'];
$oldgolongan	=$_POST['oldgolongan'];
$newlokasitugas	=$_POST['newlokasitugas'];
$newlokasitugassub=$_POST['newlokasitugassub'];
$newjabatan	=$_POST['newjabatan'];
$newgolongan	=$_POST['newgolongan'];
$newtipekaryawan=$_POST['newtipekaryawan'];
$method		=$_POST['method'];
$atasanbaru	=$_POST['atasanbaru'];
 if($atasanbaru=='')
    $atasanbaru=0;
$tjjabatan	=$_POST['tjjabatan'];
$ketjjabatan	=$_POST['ketjjabatan'];
$olddepartemen  =$_POST['olddepartemen'];
$newdepartemen  =$_POST['newdepartemen'];
$statustransaksi=$_POST['statustransaksi'];

/*
$tjkebun	=$_POST['tjkebun'];
$ketjkebun	=$_POST['ketjkebun'];
$tjlokasi	=$_POST['tjlokasi'];
$ketjlokasi	=$_POST['ketjlokasi'];
*/
$tjsdaerah   =$_POST['tjsdaerah']; 
$ketjsdaerah =$_POST['ketjsdaerah']; 
$tjmahal     =$_POST['tjmahal'];
$ketjmahal   =$_POST['ketjmahal']; 
$tjpembantu  =$_POST['tjpembantu']; 
$ketjpembantu=$_POST['ketjpembantu'];

$tjkota      =$_POST['tjkota'];
$ketjkota    =$_POST['ketjkota']; 
$tjtransport =$_POST['tjtransport']; 
$ketjtransport=$_POST['ketjtransport'];
$tjmakan     =$_POST['tjmakan'];
$ketjmakan   =$_POST['ketjmakan'];

$noskedit	=$_POST['nosk'];
$paragraf1      =$_POST['paragraf1'];
$paragraf2      =$_POST['paragraf2'];
$namajabatan	=$_POST['namajabatan'];
 



if($method=='insert')
{
//get number
$potSK=substr($_SESSION['empl']['lokasitugas'],0,4).strtoupper(substr($tipetransaksi,0,2)).substr($tanggalsk,0,4);
$str="select nomorsk from ".$dbname.".sdm_riwayatjabatan
      where  nomorsk like '".$potSK."%'
	  order by nomorsk desc limit 1";  
$notrx=0;
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$notrx=substr($bar->nomorsk,10,5);
}
$notrx=intval($notrx);
$notrx=$notrx+1;
$notrx=str_pad($notrx, 5, "0", STR_PAD_LEFT);
$notrx=$potSK.$notrx;

	$str="insert into ".$dbname.".sdm_riwayatjabatan (
		  `karyawanid`,`nomorsk`,`tanggalsk`,
		  `mulaiberlaku`,`darikodeorg`,`darilokasitugassub`,
		  `darikodejabatan`,`daritipe`,`tipesk`,
		  `darikodegolongan`,`kekodeorg`,`kelokasitugassub`,`kekodejabatan`,
		  `ketipekaryawan`,`kekodegolongan`,`darigaji`,
		  `kegaji`,`namadireksi`,`tembusan1`,`tembusan2`,
		  `tembusan3`,`tembusan4`,`updateby`,
		  `tjjabatan`,`ketjjabatan`,`tjsdaerah`,
		  `ketjsdaerah`,`tjmahal`,`ketjmahal`,
                  `tjpembantu`, `ketjpembantu`,
                  `tjkota`, `ketjkota`, 
                  `tjtransport`, `ketjtransport`, 
                  `tjmakan`, `ketjmakan`,                   
                  `tembusan5`,`atasanbaru`,
		  `namajabatan`,`pg1`,`pg2`,`bagian`,`kebagian`,`statussk`
		  ) values(
		   ".$karyawanid.",'".$notrx."',".$tanggalsk.",
		   ".$tanggalberlaku.",'".$oldokasitugas."','".$oldlokasitugassub."',
		   ".$oldjabatan.",".$oldtipekaryawan.",'".$tipetransaksi."',
		   '".$oldgolongan."','".$newlokasitugas."','".$newlokasitugassub."',".$newjabatan.",
		   ".$newtipekaryawan.",'".$newgolongan."',".$oldgaji.",
		   ".$newgaji.",'".$penandatangan."','".$tembusan1."','".$tembusan2."',
		   '".$tembusan3."','".$tembusan4."',".$_SESSION['standard']['userid'].",
		   ".$tjjabatan.",".$ketjjabatan.",
                   ".$tjsdaerah.",".$ketjsdaerah.",".$tjmahal.",".$ketjmahal.",
                   ".$tjpembantu.",".$ketjpembantu.",
                   ".$tjkota.",".$ketjkota.",
                   ".$tjtransport.",".$ketjtransport.", 
                   ".$tjmakan.",".$ketjmakan.",   
                   '".$tembusan5."',
		   ".$atasanbaru.",'".$namajabatan."','".$paragraf1."','".$paragraf2."',
                   '".$olddepartemen."','".$newdepartemen."','".$statustransaksi."'    
		  )";  		  	  
}
else if($method=='delete')
{
  $nosk=$_POST['nosk'];
	$str="delete from ".$dbname.".sdm_riwayatjabatan
	      where karyawanid=".$karyawanid." and nomorsk='".$nosk."'"; 
}
else if($method=='update')
{
	$str="update ".$dbname.".sdm_riwayatjabatan set
		  `tanggalsk`=".$tanggalsk.",
		  `mulaiberlaku`=".$tanggalberlaku.",
		  `darikodeorg`='".$oldokasitugas."',
                  `darilokasitugassub`='".$oldlokasitugassub."',
		  `darikodejabatan`=".$oldjabatan.",
		  `daritipe`=".$oldtipekaryawan.",
		  `tipesk`='".$tipetransaksi."',
		  `statussk`='".$statustransaksi."',
		  `darikodegolongan`='".$oldgolongan."',
		  `kekodeorg`='".$newlokasitugas."',
                  `kelokasitugassub`='".$newlokasitugassub."',
		  `kekodejabatan`=".$newjabatan.",
		  `ketipekaryawan`=".$newtipekaryawan.",
		  `kekodegolongan`='".$newgolongan."',
		  `darigaji`=".$oldgaji.",
		  `kegaji`=".$newgaji.",
		  `namadireksi`='".$penandatangan."',
		  `tembusan1`='".$tembusan1."',
		  `tembusan2`='".$tembusan2."',
		  `tembusan3`='".$tembusan3."',
		  `tembusan4`='".$tembusan4."',
		  `updateby`=".$_SESSION['standard']['userid'].",
		  `bagian`='".$olddepartemen."',
		  `kebagian`='".$newdepartemen."',                      
		  `tjjabatan`=".$tjjabatan.",
		  `ketjjabatan`=".$ketjjabatan.",
                      
                  `tjsdaerah`=".$tjsdaerah.",
                  `ketjsdaerah`=".$ketjsdaerah.",
                  `tjmahal`=".$tjmahal.",
                  `ketjmahal`=".$ketjmahal.",
                  `tjpembantu`=".$tjpembantu.",
                  `ketjpembantu`=".$ketjpembantu.",
                  `tjkota`=".$tjkota.",
                  `ketjkota`=".$ketjkota.",
                  `tjtransport`=".$tjtransport.",
                  `ketjtransport`=".$ketjtransport.", 
                  `tjmakan`=".$tjmakan.",
                  `ketjmakan`=".$ketjmakan.",
                      
		  `tembusan5`='".$tembusan5."',
		  `atasanbaru`=".$atasanbaru.",
		  `namajabatan`='".$namajabatan."',
                  `pg1`='".$paragraf1."',
                  `pg2`='".$paragraf2."'    
		  where `karyawanid`=".$karyawanid." and `nomorsk`='".$noskedit."'";
}

if(mysql_query($str))
{
	
}
else
   echo " Gagal:".addslashes(mysql_error($conn));


?>