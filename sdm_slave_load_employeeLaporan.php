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
	$thnmsk=$_POST['thnmsk'];
	$blnmsk=$_POST['blnmsk'];
	$thnkel=$_POST['thnkel'];
	$blnkel=$_POST['blnkel'];
	$schjk=$_POST['schjk'];
	$nik=$_POST['nik'];
        $schpt=$_POST['schpt'];
        $schdept=$_POST['schdept'];
        $schphoto=$_POST['schphoto'];
        $tgl1=tanggalsystem($_POST['tgl1']);
        $tgl2=tanggalsystem($_POST['tgl2']);
        


//	exit("ok bah:$tgl1");
           

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
        $schphoto='';
        $tgl1='';
        $tgl2='';
        
}


//exit("Error:$nik.__.$schpt");
//echo $schjk;

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
if($schphoto==1)
	$where.=" and LENGTH(photo)>0 AND photo IS NOT NULL";    
if($schphoto==2)
	$where.=" and LENGTH(photo)=0 or photo IS NULL";    
if($tgl1!='' and $tgl2!='')
	$where.=" and a.tanggalmasuk between '".$tgl1."' and '".$tgl2."'";
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
	if($statussearch=='*'){
//	   $where .=" and (a.tanggalkeluar!='0000-00-00')";
	   $where .=" and (a.tanggalkeluar!='0000-00-00' and a.tanggalkeluar<='".$hariini."')"; // tidak aktif
        }else if($statussearch=='0000-00-00'){
//	   $where .=" and (a.tanggalkeluar='0000-00-00')";
	   $where .=" and (a.tanggalkeluar='0000-00-00' or a.tanggalkeluar>'".$hariini."')"; // masih aktif
        }else
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
   /* bad syntag on query 
$str="select a.*,b.namajabatan,c.namagolongan,d.tipe from ".$dbname.".datakaryawan a, 
      ".$dbname.".sdm_5jabatan b, ".$dbname.".sdm_5golongan c,  ".$dbname.".sdm_5tipekaryawan d where 
	  a.kodejabatan=b.kodejabatan and a.kodegolongan=c.kodegolongan
	  and d.id=a.tipekaryawan 
	  ".$where."
	  limit ".$maxdisplay.",".$getrows
	  ;
    */
  $str="select a.*,b.namajabatan,c.namagolongan,d.tipe from ".$dbname.".datakaryawan a
left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan
left join ".$dbname.".sdm_5golongan c on a.kodegolongan=c.kodegolongan
left join ".$dbname.".sdm_5tipekaryawan d on d.id=a.tipekaryawan where 1=1 "
 .$where."  limit ".$maxdisplay.",".$getrows;
    
 $strx="select count(*) as jlh from ".$dbname.".datakaryawan a where 1=1 ".$where."  ";  

}
else if(trim($_SESSION['empl']['tipelokasitugas'])=='KANWIL')
{
/*$str="select a.*,b.namajabatan,d.tipe from ".$dbname.".datakaryawan a, 
      ".$dbname.".sdm_5jabatan b,  ".$dbname.".sdm_5tipekaryawan d where 
	  a.kodejabatan=b.kodejabatan 
	  and d.id=a.tipekaryawan and a.tipekaryawan!=0
	  ".$where." and lokasitugas in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
	  limit ".$maxdisplay.",".$getrows;*/
	
$str="select a.*,b.namajabatan,d.tipe,c.namagolongan from ".$dbname.".datakaryawan a 
    left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan
    left join ".$dbname.".sdm_5golongan c on a.kodegolongan=c.kodegolongan
    left join ".$dbname.".sdm_5tipekaryawan d on d.id=a.tipekaryawan where 1=1 ".$where." 
    and lokasitugas in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
    order by a.nik asc limit ".$maxdisplay.",".$getrows ;  
	  
    $strx="select count(*) as jlh from ".$dbname.".datakaryawan a  
      left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan 
      left join ".$dbname.".sdm_5golongan c on a.kodegolongan=c.kodegolongan
      left join ".$dbname.".sdm_5tipekaryawan d on d.id=a.tipekaryawan where 1=1 
      ".$where." and lokasitugas in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";
}
else
{
//a.tipekaryawan!=0 orang yang tidak di pusat tidak dapat melihat data orang permanent
$str="select a.*,b.namajabatan,c.namagolongan,d.tipe from ".$dbname.".datakaryawan a 
      left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan
      left join ".$dbname.".sdm_5golongan c on a.kodegolongan=c.kodegolongan
      left join ".$dbname.".sdm_5tipekaryawan d on d.id=a.tipekaryawan and a.tipekaryawan!=0 where 
      lokasitugas in(".$list.")  ".$where."  limit ".$maxdisplay.",".$getrows
	  ;
	  
 $strx="select count(*) as jlh from ".$dbname.".datakaryawan a
        where lokasitugas in(".$list.") ".$where."  "; 	     	
}
//echo $str;
//==================jlh karyawan
$jlhkar=0;
$resx=mysql_query($strx);
echo mysql_error($conn);
while($barx=mysql_fetch_object($resx))
{
	$jlhkar=$barx->jlh;
}

//=====================

$res=mysql_query($str);
$numrows=mysql_num_rows($res);
/*if($numrows<1)
{
	echo "<tr><td>NOT FOUND</td></tr>";
}
else
{*/


	

	
	$no=$maxdisplay;
	if($jlhkar==0)
	{
		echo"<tr><td colspan=2>DATA NOT FOUND</td></tr>";	
	}
	if($jlhkar!==0)
	{
		echo"<tr><td colspan=2>Total: ".$jlhkar." Person</td></tr>";	
	}
	while($bar=mysql_fetch_object($res))
	{
		//get pendidikan terakhir
		$str1="select a.kelompok from ".$dbname.".sdm_5pendidikan a
		       where a.levelpendidikan=".$bar->levelpendidikan." "; 
		$res1=mysql_query($str1);	
		$pendidikan="";
		while($barpendidikan=mysql_fetch_object($res1))
		{
			$pendidikan=$barpendidikan->kelompok;
		}
		   
		$no+=1;
		echo "<tr class=rowcontent>
		     <td>".$no."</td>
			 <td width=85>".$bar->nik."</td>
			 <td>".$bar->namakaryawan."</td>
			 <td>".$bar->namajabatan."</td>
			 <td>".$bar->namagolongan."</td>
			 <td>".$bar->lokasitugas."</td>
			 <td>".$bar->kodeorganisasi."</td>
			 <td>".$bar->subbagian."</td>
			 <td>".$pendidikan."</td>
			 <td>".$bar->statuspajak."</td>
			 <td>".$bar->statusperkawinan."</td>
			 <td align=right >".$bar->jumlahanak."</td>
			 <td>".tanggalnormal($bar->tanggalmasuk)."</td>
			 <td>".tanggalnormal($bar->tanggalkeluar)."</td>
			 <td>".$bar->tipe."</td>
			 <td>";
                if ($bar->photo!=''){
                    echo "<img src=images/icons/picture.png class=resicon  title='".$_SESSION['lang']['idcard']."' onclick=\"previewIdCard('".$bar->karyawanid."','".$bar->namakaryawan."',event);\">";
                }
                    echo "<img src=images/zoom.png class=resicon  title='".$_SESSION['lang']['view']."' onclick=\"previewKaryawan('".$bar->karyawanid."','".$bar->namakaryawan."',event);\">
			  <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewKaryawanPDF('".$bar->karyawanid."','".$bar->namakaryawan."',event);\">		 
			 </td>
			  </tr>";			 		  
	}
//}
?>
