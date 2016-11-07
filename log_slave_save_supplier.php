<?php
require_once('master_validation.php');
require_once('config/connection.php');

	$kelompok	=$_POST['kelompok'];
	$telp		=$_POST['telp'];
	$fax		=$_POST['fax'];
	$idsupplier		=$_POST['idsupplier'];
	$email			=$_POST['email'];
	$namasupplier	=$_POST['namasupplier'];
	$npwp			=$_POST['npwp'];
	$cperson		=$_POST['cperson'];
	$kota			=$_POST['kota'];		
	$plafon			=$_POST['plafon'];
	$method			=$_POST['method'];
	$alamat			=$_POST['alamat'];
	
    $strx="select 1=1";

	switch($method){
		case 'delete':
			$strx="delete from ".$dbname.".log_5supplier where supplierid='".$idsupplier."'"; 
		break;
		case 'update':
			$strx="update ".$dbname.".log_5supplier set
                   kodekelompok='".$kelompok."',
				   namasupplier='".$namasupplier."',
				   alamat='".$alamat."',
				   kota='".$kota."',
				   telepon='".$telp."',
				   kontakperson='".$cperson."',
				   plafon=".$plafon.",
			       npwp='".$npwp."',
				   fax='".$fax."',
				   email='".$email."'
				   where supplierid='".$idsupplier."'
				  "; 			
		break;	
		case 'insert':
			$strx="insert into ".$dbname.".log_5supplier(
			kodekelompok,namasupplier,alamat,
			kota,telepon,kontakperson,plafon,
			npwp,supplierid,fax,email)
			values('".$kelompok."','".$namasupplier."','"
			         .$alamat."','".$kota."','".$telp."','"
					 .$cperson."',".$plafon.",'".$npwp."','"
					 .$idsupplier."','".$fax."','".$email."')";	   			 
		break;
            case'updStatus':
                if($_POST['status']==1){
                    $strx="update ".$dbname.".log_5supplier set status=0 where supplierid='".$_POST['supplierid']."'";
                }else{
                    $strx="update ".$dbname.".log_5supplier set status=1 where supplierid='".$_POST['supplierid']."'";
                }
                
            break;
        default:
        break;	
	}
  if(mysql_query($strx))
  {}	
  else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}	
	

$str=" select * from ".$dbname.".log_5supplier where kodekelompok='".$kelompok."' order by supplierid";
  if($res=mysql_query($str))
  {
	while($bar=mysql_fetch_object($res))
	{
		$no+=1;
                $bg="class=rowcontent";
                $bger="onclick=updateStatus('".$bar->supplierid."','".$bar->status."') style='cursor:pointer' title='Non Aktifkan ".$bar->namasupplier."'";
                if($bar->status==0){
                    $bger="onclick=updateStatus('".$bar->supplierid."','".$bar->status."') style='cursor:pointer' title='Aktifkan ".$bar->namasupplier."'";
                    $bg="bgcolor=orange";
                }
		echo"<tr ".$bg.">
		     <td ".$bger.">".$kelompok."</td>
			 <td ".$bger.">".$bar->supplierid."</td>
			 <td ".$bger.">".$bar->namasupplier."</td>
			 <td ".$bger.">".$bar->alamat."</td>
			 <td ".$bger.">".$bar->kontakperson."</td>
			 <td ".$bger.">".$bar->kota."</td>
			 <td ".$bger.">".$bar->telepon."</td>		 
			 <td ".$bger.">".$bar->fax."</td>		 
			 <td ".$bger.">".$bar->email."</td>		 
			 <td ".$bger.">".$bar->npwp."</td>	 
			 <td align=right>".number_format($bar->plafon,0,',','.')."</td>
			  <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delSupplier('".$bar->supplierid."','".$bar->namasupplier."');\"></td>
			  <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editSupplier('".$bar->supplierid."','".$bar->namasupplier."','".$bar->alamat."','".$bar->kontakperson."','".$bar->kota."','".$bar->telepon."','".$bar->fax."','".$bar->email."','".$bar->npwp."','".$bar->plafon."');\"></td>
			 </tr>";
	}	 	   	
  }	
  else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}	
?>