<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/zLib.php');

$periodegaji=$_POST['periodegaji'];
$idkaryawan=$_POST['idkaryawan'];
$upahpremi=$_POST['upahpremi'];
$komponenpayroll=$_POST['komponenpayroll'];
$method=$_POST['method'];

      #periksa tutupbuku
       $str="select * from ".$dbname.".setup_periodeakuntansi where periode='".$periodegaji."' and 
             kodeorg='".$_SESSION['empl']['lokasitugas']."' and tutupbuku=1";
       $res=mysql_query($str);
       if(mysql_num_rows($res)>0)
           $aktif=false;
       else
           $aktif=true;
       
switch($method)
{
case 'show':	
	break;	
case 'insert':  
	$str="insert into ".$dbname.".sdm_gaji 
	      (kodeorg,periodegaji,karyawanid,idkomponen,jumlah,pengali)
	      values('".$_SESSION['empl']['lokasitugas']."','".$periodegaji."','".$idkaryawan."','".$komponenpayroll."','".$upahpremi."','1')";
        if($aktif)
        {
            if(mysql_query($str))
            {}
            else
            {echo " Gagal,".mysql_error($conn); exit;}	
        }
        else
        {
            exit("Error:Periode sudah tutup buku");
        }  
	break;
case 'delete':
	$str="delete from ".$dbname.".sdm_gaji
	where kodeorg='".$_SESSION['empl']['lokasitugas']."' and periodegaji='".$periodegaji."' and karyawanid='".$idkaryawan."' and idkomponen='".$komponenpayroll."'";

        if($aktif)
        {
            if(mysql_query($str))
            {}
            else
            {echo " Gagal,".mysql_error($conn); exit;}
        }
        else
        {
            exit("Error:Periode sudah tutup buku");
        } 
	break;
default:
   break;					
}




if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{
	$strRes="select a.*, b.kodejabatan, b.lokasitugas,b.nik from ".$dbname.".sdm_gaji a 
	left join ".$dbname.".datakaryawan b
	on a.karyawanid = b.karyawanid
	where a.idkomponen in ('16','43','58') and  a.periodegaji ='".$periodegaji."' 
	order by a.karyawanid";
}
else if($_SESSION['empl']['tipelokasitugas']=='KANWIL')
{
	$strRes="select a.*, b.kodejabatan, b.lokasitugas,b.nik from ".$dbname.".sdm_gaji a 
	left join ".$dbname.".datakaryawan b
	on a.karyawanid = b.karyawanid
	where a.idkomponen in ('16','43','58') and  a.periodegaji ='".$periodegaji."' and b.lokasitugas in 
	(select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') 
	order by a.karyawanid";
}
else
{
	$strRes="select a.*, b.kodejabatan, b.lokasitugas,b.nik from ".$dbname.".sdm_gaji a 
	left join ".$dbname.".datakaryawan b
	on a.karyawanid = b.karyawanid
	where a.idkomponen in ('16','43','58') and  a.periodegaji ='".$periodegaji."' and b.lokasitugas = '".$_SESSION['empl']['lokasitugas']."'
	order by a.karyawanid";
}



	$resRes=mysql_query($strRes);
	while($bar1=mysql_fetch_object($resRes))
	{
		$nm=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',"karyawanid='".$bar1->karyawanid."'");
		$nmjb=makeOption($dbname,'datakaryawan','karyawanid,kodejabatan',"karyawanid='".$bar1->karyawanid."'");
		$kenmjb=makeOption($dbname,'sdm_5jabatan','kodejabatan,namajabatan',"kodejabatan='".$nmjb[$bar1->karyawanid]."'");
		
		echo"<tr class=rowcontent>
		           <td align=center>".$nm[$bar1->karyawanid]."</td>
				   <td align=center>".$bar1->nik."</td>
				   <td align=center>".$bar1->lokasitugas."</td>
				   <td>".$kenmjb[$nmjb[$bar1->karyawanid]]."</td>
		           <td align=center>".$bar1->periodegaji."</td>
				   <td align=right width=100>".number_format($bar1->jumlah,2)."</td>
				   <td><img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"delPremi('".$bar1->periodegaji."','".$bar1->karyawanid."','".$bar1->jumlah."','".$bar1->idkomponen."');\"></td></tr>";
	}	 
?>
