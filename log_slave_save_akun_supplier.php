<?php
require_once('master_validation.php');
require_once('config/connection.php');

	 $noakun	=$_POST['noakun'];
	 $akunpajak	=$_POST['akunpajak'];
	 $idsupplier=$_POST['idsupplier'];
	 $an		=$_POST['an'];
	 $bank		=$_POST['bank'];
	 $rek		=$_POST['rek'];
//	 $namasupplier	=$_POST['namasupplier'];
	 $noseripajak	=$_POST['noseripajak'];
	 $nilaihutang	=$_POST['nilaihutang'];
	 $method=trim($_POST['method']);
	 
//make sure nilaihutang has a value
     if($nilaihutang=='')
	    $nilaihutang=0;	 
	
    $strx="select 1=1";

	switch($method){
		case 'update':
			$strx="update ".$dbname.".log_5supplier set
                   noakun='".$noakun."',
				   akunpajak='".$akunpajak."',
				   an='".$an."',
				   bank='".$bank."',
				   rekening='".$rek."',
				   noseripajak='".$noseripajak."',
				   nilaihutang=".$nilaihutang."
				   where supplierid='".$idsupplier."'
				  "; 			
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
	
if(isset($_POST['txt']))//find supplier
{
	$txt=$_POST['txt'];
$str=" select * from ".$dbname.".log_5supplier where namasupplier like '%".$txt."%' order by supplierid";	
}
else//normal do
{
$str=" select * from ".$dbname.".log_5supplier where supplierid='".$idsupplier."' order by supplierid";
}
  if($res=mysql_query($str))
  {
	while($bar=mysql_fetch_object($res))
	{
		$no+=1;
		echo"<tr class=rowcontent>
		     <td>".$bar->kodekelompok."</td>
			 <td>".$bar->supplierid."</td>
			 <td>".$bar->namasupplier."</td>
			 <td>".$bar->alamat."</td>
			 <td>".$bar->kontakperson."</td>
			 <td>".$bar->kota."</td>
			 <td>".$bar->telepon."</td>		 
			 <td>".$bar->fax."</td>		 
			 <td>".$bar->email."</td>		 
			 <td>".$bar->npwp."</td>	 
			 <td align=right>".number_format($bar->plafon,0,',','.')."</td>
			 <td>".$bar->noakun."</td>
			 <td>".$bar->akunpajak."</td>
			 <td>".$bar->noseripajak."</td>
			 <td>".$bar->bank."</td>
			 <td>".$bar->rekening."</td>
			 <td>".$bar->an."</td>
			 <td align=right>".number_format($bar->nilaihutang,0,',','.')."</td>
			  <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editAkunSupplier('".$bar->supplierid."','".$bar->namasupplier."','".$bar->noakun."','".$bar->nilaihutang."','".$bar->noseripajak."','".$bar->akunpajak."','".$bar->bank."','".$bar->rekening."','".$bar->an."');\"></td>
			 </tr>";
	}	 	   	
  }	
  else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}	
?>