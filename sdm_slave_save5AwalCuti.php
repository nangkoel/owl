<?php //@Copy nangkoelframework
require_once('master_validation.php');
require_once('config/connection.php');
$karyawanid=$_POST['karyawanid'];
$lokasitugas=$_POST['lokasitugas'];
$periode=$_POST['periode'];
$dari=$_POST['dari'];
$sampai=$_POST['sampai'];
$hak=$_POST['hak'];


#ambil sisa periode lalu
$periodelalu=$periode-1;
$str="select sisa from ".$dbname.".sdm_cutiht where karyawanid=".$karyawanid." and periodecuti='".$periodelalu."'";
$sisalalu=0;
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $sisalalu=$bar->sisa;
}
if($sisalalu<0)#jika saldolalu minus maka dibawa
     $hak+=$sisalalu;

//ambil sum jumlah diambil dan update table header
    $strx="select diambil from ".$dbname.".sdm_cutiht where karyawanid=".$karyawanid." and periodecuti='".$periode."'";

    $diambilh=0;
    $resx=mysql_query($strx);
    while($barx=mysql_fetch_object($resx))
    {
            $diambilh=$barx->diambil;
    }
    $strx="select sum(jumlahcuti) as diambil from ".$dbname.".sdm_cutidt
           where karyawanid=".$karyawanid." and periodecuti='".$periode."'";

    $diambil=0;
    $resx=mysql_query($strx);
    while($barx=mysql_fetch_object($resx))
    {
            $diambil=$barx->diambil;
    }
    
if ($diambilh!=$diambil)
    $hak-=$diambil;

$str="update ".$dbname.".sdm_cutiht 
      set dari=".$dari.",
	  sampai=".$sampai.",
	  hakcuti=".$hak.",
          sisa=".$hak."-diambil
     where 
	  karyawanid=".$karyawanid."
	  and periodecuti='".$periode."'";	  
mysql_query($str);
if(mysql_affected_rows($conn)<1)
{	  
$str="insert into ".$dbname.".sdm_cutiht(kodeorg,`karyawanid`,
      `periodecuti`,`dari`,`sampai`,`hakcuti`,`sisa`)
	  values(
	  '".$lokasitugas."',".$karyawanid.",'".$periode."',
	  ".$dari.",".$sampai.",".$hak.",".$hak."
	  )";
  if(mysql_query($str))
  {
  	
  }
  else
  {
  	echo addslashes(mysql_error($conn));
  }
}

		 
?>