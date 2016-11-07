<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zFunction.php');

//Numrows perpage==20;
$getrows=20;
//default query
if($_POST['page'])
   $page=$_POST['page'];
else
   $page=1; 
  
$maxdisplay=($page*$getrows-20);

if(isset($_POST['txtsearch']))
{
	$txtsearch=$_POST['txtsearch'];
	$orgsearch=$_POST['orgsearch'];	
	$tipesearch=$_POST['tipesearch'];	
	$statussearch=$_POST['statussearch'];
}
else
{
	$txtsearch='';
	$orgsearch='';	
	$tipesearch='';
	$statussearch='';	
}
$where='';
if($txtsearch!='')
   $where= " and a.namakaryawan like '%".$txtsearch."%'";
if($orgsearch!='')
   $where .=" and (a.lokasitugas='".$orgsearch."' or a.subbagian='".$orgsearch."') ";  
if($tipesearch!='')
   $where .=" and a.tipekaryawan='".$tipesearch."'";  
	if($statussearch=='*')
	   $where .=" and (a.tanggalkeluar<".$_SESSION['org']['period']['start']." and tanggalkeluar!='0000-00-00')";
	else if($statussearch=='0000-00-00')
	   $where .=" and (a.tanggalkeluar>".$_SESSION['org']['period']['start']." or tanggalkeluar='0000-00-00')";
	else
	{}   
    
      
//make sure user can only access allowed data   
$listOrg=ambilLokasiTugasDanTurunannya('list',$_SESSION['empl']['lokasitugas']);
$list=str_replace("|","','",$listOrg);
$list="'".$list."'";

if(trim($_SESSION['empl']['tipelokasitugas'])=='HOLDING')
{
$str="select a.*,b.namajabatan,c.namagolongan,d.tipe from ".$dbname.".datakaryawan a, 
      ".$dbname.".sdm_5jabatan b, ".$dbname.".sdm_5golongan c,  ".$dbname.".sdm_5tipekaryawan d where 
	  a.kodejabatan=b.kodejabatan and a.kodegolongan=c.kodegolongan
	  and d.id=a.tipekaryawan 
	  ".$where."
	  limit ".$maxdisplay.",".$getrows
	  ;    
}
else if(trim($_SESSION['empl']['tipelokasitugas'])=='KANWIL')
{
$str="select a.*,b.namajabatan,c.namagolongan,d.tipe from ".$dbname.".datakaryawan a, 
      ".$dbname.".sdm_5jabatan b, ".$dbname.".sdm_5golongan c,  ".$dbname.".sdm_5tipekaryawan d where 
	  a.kodejabatan=b.kodejabatan and a.kodegolongan=c.kodegolongan
	  and d.id=a.tipekaryawan and a.tipekaryawan!=0
	  ".$where."
	  limit ".$maxdisplay.",".$getrows
	  ;    
}
else
{
//a.tipekaryawan!=0 orang yang tidak di pusat tidak dapat melihat data orang permanent
$str="select a.*,b.namajabatan,c.namagolongan,d.tipe from ".$dbname.".datakaryawan a, 
      ".$dbname.".sdm_5jabatan b, ".$dbname.".sdm_5golongan c,  ".$dbname.".sdm_5tipekaryawan d where 
      lokasitugas in(".$list.")
	  and a.kodejabatan=b.kodejabatan and a.kodegolongan=c.kodegolongan
	  and d.id=a.tipekaryawan and a.tipekaryawan!=0
	  ".$where."
	  limit ".$maxdisplay.",".$getrows
	  ;   	
}
//exit("Error:".$str);
$res=mysql_query($str);
$numrows=mysql_num_rows($res);
if($numrows<1)
{
	echo "<tr><td>NOT FOUND</td></tr>";
}
else
{
	$no=$maxdisplay;
	while($bar=mysql_fetch_object($res))
	{
		//get pendidikan terakhir
		$str1="select a.kelompok from ".$dbname.".sdm_5pendidikan a
		       where a.levelpendidikan=".$bar->levelpendidikan; 
		$res1=mysql_query($str1);	
		$pendidikan="";
		while($barpendidikan=mysql_fetch_object($res1))
		{
			$pendidikan=$barpendidikan->kelompok;
		}	   
		$no+=1;
		echo "<tr class=rowcontent>
		     <td>".$no."</td>
			 <td>".$bar->nik."</td>
			 <td>".$bar->namakaryawan."</td>
			 <td>".$bar->namajabatan."</td>
			 <td>".$bar->namagolongan."</td>
			 <td>".$bar->lokasitugas."</td>
			 <td>".$bar->kodeorganisasi."</td>
			 <td>".$bar->noktp."</td>
			 <td>".$pendidikan."</td>
			 <td>".$bar->statuspajak."</td>
			 <td>".$bar->statusperkawinan."</td>
			 <td align=right >".$bar->jumlahanak."</td>
			 <td>".tanggalnormal($bar->tanggalmasuk)."</td>
			 <td>".$bar->tipe."</td>
			 <td>
				    <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editKaryawan('".$bar->karyawanid."','".$bar->namakaryawan."');\"> 
				    <img src=images/zoom.png class=resicon  title='".$_SESSION['lang']['view']."' onclick=\"previewKaryawan('".$bar->karyawanid."','".$bar->namakaryawan."',event);\">
					<img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewKaryawanPDF('".$bar->karyawanid."','".$bar->namakaryawan."',event);\">		 
			 </td>
			  </tr>";
			  
	
			 		  
	}
}
?>
