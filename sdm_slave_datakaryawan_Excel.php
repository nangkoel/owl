<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zFunction.php');

//exit('HAHAHA');

if(isset($_GET['txtsearch']))
{
	$txtsearch=$_GET['txtsearch'];
	$orgsearch=$_GET['orgsearch'];	
	$tipesearch=$_GET['tipesearch'];
	$statussearch=$_GET['statussearch'];	
	$thnmsk=$_GET['thnmsk'];
	$blnmsk=$_GET['blnmsk'];
	$thnkel=$_GET['thnkel'];
	$blnkel=$_GET['blnkel'];
	$schjk=$_GET['schjk'];
	$nik=$_GET['nik'];
        $schpt=$_GET['schpt'];
        $schdept=$_GET['schdept'];
	

}
else
{
	$txtsearch='';
	$orgsearch='';	
	$tipesearch='';
	$statussearch='';	
	$thnmsk='';
	$blnmsk='';
	$thnkel='';
	$blnkel='';
	$schjk='';
	$nik='';
        $schpt='';
        $schdept='';
	
}

$where='';
if($schpt!='')
   $where.= " and a.kodeorganisasi = '".$schpt."'";
if($txtsearch!='')
   $where.= " and a.namakaryawan like '%".$txtsearch."%'";
if($orgsearch!='')
   $where .=" and (a.lokasitugas='".$orgsearch."' or a.subbagian='".$orgsearch."') "; 
if($nik!='')
	$where.=" and nik like '%".$nik."%'";   
if($schdept!='')
	$where.=" and bagian = '".$schdept."'";    
	      
if($tipesearch!='')
{
if($tipesearch==100){
    $where.=" and a.tipekaryawan!=4 ";
}
else{
   $where .=" and a.tipekaryawan='".$tipesearch."'"; 
}
}
	if($thnmsk!='')
	{
		$where.="and left(a.tanggalmasuk,4)='".$thnmsk."'   ";
	}
	

	if($blnmsk!='')
	{
		$where.="and mid(a.tanggalmasuk,6,2)='".$blnmsk."'  ";
	}

	if($thnkel!='')
	{
		$where.="and left(a.tanggalkeluar,4)='".$thnkel."'  ";
	}
	

	if($blnkel!='')
	{
		$where.="and mid(a.tanggalkeluar,6,2)='".$blnkel."' ";
	}   
   
        $hariini = date("Y-m-d");
	if($statussearch=='*')
//	   $where .=" and (a.tanggalkeluar!='0000-00-00')";
	   $where .=" and (a.tanggalkeluar='0000-00-00' or a.tanggalkeluar<='".$hariini."')"; // tidak aktif
	else if($statussearch=='0000-00-00')
//	   $where .=" and (a.tanggalkeluar='0000-00-00')";
	   $where .=" and (a.tanggalkeluar='0000-00-00' or a.tanggalkeluar>'".$hariini."')"; // masih aktif
	else
	{} 
	 
	 if($schjk!='')
	 {
		 $where.=" and a.jeniskelamin='".$schjk."'";
	 }
	 
//make sure user can only access allowed data   
$listOrg=ambilLokasiTugasDanTurunannya('list',$_SESSION['empl']['lokasitugas']);
$list=str_replace("|","','",$listOrg);
$list="'".$list."'";

if(trim($_SESSION['empl']['tipelokasitugas'])=='HOLDING')
{   

  $str="select a.*,b.namajabatan,c.namagolongan,d.tipe,e.kelompok from ".$dbname.".datakaryawan a
left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan
left join ".$dbname.".sdm_5golongan c on a.kodegolongan=c.kodegolongan
left join ".$dbname.".sdm_5tipekaryawan d on d.id=a.tipekaryawan
left join ".$dbname.".sdm_5pendidikan e on a.levelpendidikan=e.levelpendidikan
where 1=1 " .$where;
  
$strd="select b.*,a.namakaryawan,c.kelompok, case b.status when 1 then 'Y' when 0 then 'T' end as statusx
       from ".$dbname.".sdm_karyawankeluarga b
       left join ".$dbname.".datakaryawan a
	   on b.karyawanid=a.karyawanid
	   left join ".$dbname.".sdm_5pendidikan c on b.levelpendidikan=c.levelpendidikan
	   where 1=1 ".$where;
}
else if(trim($_SESSION['empl']['tipelokasitugas'])=='KANWIL')
{

$str="select a.*,b.namajabatan,d.tipe,c.namagolongan,e.kelompok from ".$dbname.".datakaryawan a 
    left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan
    left join ".$dbname.".sdm_5golongan c on a.kodegolongan=c.kodegolongan
    left join ".$dbname.".sdm_5tipekaryawan d on d.id=a.tipekaryawan
    left join ".$dbname.".sdm_5pendidikan e on a.levelpendidikan=e.levelpendidikan    
    where 1=1 ".$where." 
    and lokasitugas in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
    order by a.nik asc";
$strd="select b.*,a.namakaryawan,c.kelompok, case b.status when 1 then 'Y' when 0 then 'T' end as statusx
       from ".$dbname.".sdm_karyawankeluarga b
       left join ".$dbname.".datakaryawan a
	   on b.karyawanid=a.karyawanid
	   left join ".$dbname.".sdm_5pendidikan c on b.levelpendidikan=c.levelpendidikan
	   where a.lokasitugas in(".$list.") ".$where; 
}
else
{
//a.tipekaryawan!=0 orang yang tidak di pusat tidak dapat melihat data orang permanent
$str="select a.*,b.namajabatan,c.namagolongan,d.tipe,e.kelompok from ".$dbname.".datakaryawan a 
      left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan
      left join ".$dbname.".sdm_5golongan c on a.kodegolongan=c.kodegolongan
      left join ".$dbname.".sdm_5tipekaryawan d on d.id=a.tipekaryawan 
      left join ".$dbname.".sdm_5pendidikan e on a.levelpendidikan=e.levelpendidikan  where 
      lokasitugas in(".$list.") and  a.tipekaryawan!=0 ".$where;

$strd="select b.*,a.namakaryawan,c.kelompok, case b.status when 1 then 'Y' when 0 then 'T' end as statusx
       from ".$dbname.".sdm_karyawankeluarga b
       left join ".$dbname.".datakaryawan a
	   on b.karyawanid=a.karyawanid
	   left join ".$dbname.".sdm_5pendidikan c on b.levelpendidikan=c.levelpendidikan
	   where a.lokasitugas in(".$list.") ".$where; 
}
$stream='';

 
   $stream.="
       Daftar karyawan:
	   <table border=1>
	   <tr>
	     <td align=center>No.</td>
 		 <td align=center>".$_SESSION['lang']['nokaryawan']."</td>		 
		 <td align=center>".$_SESSION['lang']['nik']."</td>
		 <td align=center>".$_SESSION['lang']['nama']."</td>
		 <td align=center>".$_SESSION['lang']['functionname']."</td>
		 <td align=center>".$_SESSION['lang']['kodegolongan']."</td>
		 <td align=center>".$_SESSION['lang']['pangkat']."</td>
		 <td align=center>".$_SESSION['lang']['lokasitugas']."</td>
		 <td align=center>".$_SESSION['lang']['pt']."</td>
		 <td align=center>".$_SESSION['lang']['noktp']."</td>
		 <td align=center>".$_SESSION['lang']['pendidikan']."</td>
		 <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['statuspajak'])."</td>
		 <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['statusperkawinan'])."</td>
		 <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['jumlahanak'])."</td>
		 <td align=center>".$_SESSION['lang']['tanggalmasuk']."</td>
		 <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['tipekaryawan'])."</td>
		 <td align=center>".$_SESSION['lang']['tempatlahir']."</td>
		 <td align=center>".$_SESSION['lang']['tanggallahir']."</td>
		 <td align=center>".$_SESSION['lang']['warganegara']."</td>
		 <td align=center>".$_SESSION['lang']['jeniskelamin']."</td>
		 <td align=center>".$_SESSION['lang']['tanggalmenikah']."</td>
		 <td align=center>".$_SESSION['lang']['agama']."</td>
		 <td align=center>".$_SESSION['lang']['golongandarah']."</td>
		 <td align=center>".$_SESSION['lang']['alamataktif']."</td>
		 <td align=center>".$_SESSION['lang']['provinsi']."</td>
		 <td align=center>".$_SESSION['lang']['kota']."</td>
		 <td align=center>".$_SESSION['lang']['kecamatan']."</td>
		 <td align=center>".$_SESSION['lang']['desa']."</td>
		 <td align=center>".$_SESSION['lang']['kodepos']."</td>
		 <td align=center>".$_SESSION['lang']['noteleponrumah']."</td>
		 <td align=center>".$_SESSION['lang']['nohp']."</td>
		 <td align=center>".$_SESSION['lang']['norekeningbank']."</td>
		 <td align=center>".$_SESSION['lang']['namabank']."</td>
		 <td align=center>".$_SESSION['lang']['sistemgaji']."</td>
		 <td align=center>".$_SESSION['lang']['nopaspor']."</td>
		 <td align=center>".$_SESSION['lang']['notelepondarurat']."</td>
   		 <td align=center>".$_SESSION['lang']['tanggalkeluar']."</td>
		 <td align=center>".$_SESSION['lang']['jumlahtanggungan']."</td>
		 <td align=center>".$_SESSION['lang']['npwp']."</td>
		 <td align=center>".$_SESSION['lang']['lokasipenerimaan']."</td>
		 <td align=center>".$_SESSION['lang']['bagian']."</td>
		 <td align=center>".$_SESSION['lang']['subbagian']."</td>
                 <td align=center>".$_SESSION['lang']['jms']."</td>    
		 <td align=center>".$_SESSION['lang']['email']."</td>
	     </tr>";
$res=mysql_query($str);
$numrows=mysql_numrows($res);
if($numrows<1)
{
	$stream.="<tr><td>NOT FOUND</td></tr>";
}
else
{
	$no=0;
	while($bar=mysql_fetch_object($res))
	{
		$no+=1;
		$stream.="<tr>
		     <td>".$no."</td>
			 <td>'".$bar->karyawanid."</td>
			 <td>'".$bar->nik."</td>
			 <td>".$bar->namakaryawan."</td>
			 <td>".$bar->namajabatan."</td>
			 <td>".$bar->namagolongan."</td>
			 <td>".$bar->pangkat."</td>
			 <td>".$bar->lokasitugas."</td>
			 <td>".$bar->kodeorganisasi."</td>
			 <td>'".$bar->noktp."</td>
			 <td>".$bar->kelompok."</td>
			 <td>".$bar->statuspajak."</td>
			 <td>".$bar->statusperkawinan."</td>
			 <td align=right >".$bar->jumlahanak."</td>
			 <td>".$bar->tanggalmasuk."</td>
			 <td>".$bar->tipe."</td>
			 <td>".$bar->tempatlahir."</td>
			 <td>".$bar->tanggallahir."</td>
			 <td>".$bar->warganegara."</td>
			 <td>".$bar->jeniskelamin."</td>
			 <td>".$bar->tanggalmenikah."</td>
			 <td>".$bar->agama."</td>
			 <td>".$bar->golongandarah."</td>
			 <td>".$bar->alamataktif."</td>
			 <td>".$bar->provinsi."</td>
			 <td>".$bar->kota."</td>
			 <td>".$bar->kecamatan."</td>
			 <td>".$bar->desa."</td>
			 <td>".$bar->kodepos."</td>
			 <td>".$bar->noteleponrumah."</td>
			 <td>'".$bar->nohp."</td>
			 <td>'".$bar->norekeningbank."</td>
			 <td>".$bar->namabank."</td>
			 <td>".$bar->sistemgaji."</td>
			 <td>'".$bar->nopaspor."</td>
			 <td>'".$bar->notelepondarurat."</td>
			 <td>".$bar->tanggalkeluar."</td>
			 <td>".$bar->jumlahtanggungan."</td>
			 <td>'".$bar->npwp."</td>
			 <td>".$bar->lokasipenerimaan."</td>
			 <td>".$bar->bagian."</td>
			 <td>".$bar->subbagian."</td>
                         <td>'".$bar->jms."</td>    
			 <td>".$bar->email."</td>	 
		  </tr>";			 		  
	}
	$stream.="</table>";
	
//============================keluarga
$stream.= "KELUARGA";
   $stream.="<table border=1>
	   <tr>
	     <td align=center>No.</td>
 		 <td align=center>".$_SESSION['lang']['nokaryawan']."</td>		 
		 <td align=center>".$_SESSION['lang']['nama']."</td>
		 <td align=center>".$_SESSION['lang']['anggotakeluarga']."</td>
		 <td align=center>".$_SESSION['lang']['jeniskelamin']."</td>
		 <td align=center>".$_SESSION['lang']['hubungan']."</td>
	 	 <td align=center>".$_SESSION['lang']['tempatlahir']."</td>
		 <td align=center>".$_SESSION['lang']['tanggallahir']."</td>		 		 
		 <td align=center>".$_SESSION['lang']['pekerjaan']."</td> 
		 <td align=center>".$_SESSION['lang']['statusperkawinan']."</td>	 
		 <td align=center>".$_SESSION['lang']['pendidikan']."</td>		 
		 <td align=center>".$_SESSION['lang']['email']."</td>
		 <td align=center>".$_SESSION['lang']['telp']."</td>	 
		 <td align=center>".$_SESSION['lang']['tanggungan']."</td>
	     </tr>";
$res=mysql_query($strd);
	$no=0;
	while($bar=mysql_fetch_object($res))
	{
		$no+=1;
   $stream.="<table border=1>
	   <tr>
	     <td>".$no."</td>
 		 <td>'".$bar->karyawanid."</td>		 
		 <td>".$bar->namakaryawan."</td>
		 <td>".$bar->nama."</td>
		 <td>".$bar->jeniskelamin."</td>
		 <td>".$bar->hubungankeluarga."</td>
	 	 <td>".$bar->tempatlahir."</td>
		 <td>".$bar->tanggallahir."</td>		 		 
		 <td>".$bar->pekerjaan."</td> 
		 <td>".$bar->status."</td>	 
		 <td>".$bar->kelompok."</td>		 
		 <td>".$bar->email."</td>
		 <td>".$bar->telp."</td>	 
		 <td>".$bar->statusx."</td>
	     </tr>";		
	}
$stream.="</table>";
}
$wktu=date("Hms");
$nop_="DT_Employee_".$wktu."__".date('Y');
if(strlen($stream)>0)
{
     $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
     gzwrite($gztralala, $stream);
     gzclose($gztralala);
     echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls.gz';
        </script>";
} 
?>
