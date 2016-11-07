<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

if(isTransactionPeriod())//check if transaction period is normal
{
   $nopo=isset($_POST['nopo'])? $_POST['nopo']: null;
   $supp=isset($_POST['supplierNm'])? $_POST['supplierNm']: null;
   echo"<table cellspacing=1 border=0 class=data>
        <thead>
		<tr class=rowheader><td>No</td>
		    <td>".$_SESSION['lang']['nopo']."</td>
			<td>".$_SESSION['lang']['pt']."</td>
			<td>".$_SESSION['lang']['tanggal']."</td>
			<td>".$_SESSION['lang']['purchaser']."</td>
		</tr>
		</thead>
		</tbody>";
   if($nopo!=''){
       $whr.="and nopo like '%".$nopo."%'";
   }
   if($supp!=''){
       $whr.="and kodesupplier in (select distinct supplierid from ".$dbname.".log_5supplier where namasupplier like '%".$supp."%')";
   }
  $str="select distinct * from ".$dbname.".log_poht where stat_release=1 and statuspo in ('2','3') ".$whr."";
  $str.="order by tanggal desc,nopo desc";
  $res=mysql_query($str);
  $no=0;
  while($bar=mysql_fetch_object($res)){
   //ambil userid purchaser
   $hwr="nopo='".$bar->nopo."'";
   $optPur=makeOption($dbname, 'log_poht', 'nopo,purchaser', $hwr);
   $purchaser='';
   
	   $str="select namauser from ".$dbname.".user where karyawanid='".$optPur[$bar->nopo]."'";
	   $resv=mysql_query($str);
	   $barv=mysql_fetch_object($resv);
           $purchaser=$barv->namauser;
	   
  	$no+=1;
	echo"
		<tr class=rowcontent  style='cursor:pointer;' title='Click It' onclick=goPickPo('".$bar->nopo."')><td>".$no."</td>
		    <td>".$bar->nopo."</td>
			<td>".$bar->kodeorg."</td>
			<td>".tanggalnormal($bar->tanggal)."</td>
			<td>".$purchaser."</td>
		</tr>
	";
	
	
  }	 	
					
	echo"</tbody>
	     <tfoot>
		 </tfoot>
		 </table>";		
}
else
{
	echo " Error: Transaction Period missing";
}
?>