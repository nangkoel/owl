<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
require_once('config/connection.php');

$method=$_POST['method'];

$sreg="select distinct regional from ".$dbname.".bgt_regional_assignment 
                where kodeunit='".$_SESSION['empl']['lokasitugas']."' ";
$qreg=mysql_query($sreg) or die(mysql_error($conn));
$rreg=mysql_fetch_assoc($qreg);


switch($method){
case 'update':	
	$str="update ".$dbname.".kebun_5denda set nama='".$_POST['tanggal']."',jumlah='".$_POST['ket']."'
               ,updateby='".$_SESSION['standard']['userid']."'
	       where kode='".$_POST['regId']."' ";
        //exit("error:".$str);
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
break;
case 'insert':
        $scek="select distinct * from ".$dbname.".kebun_5denda where kode='".$_POST['regId']."'";
        $qcek=mysql_query($scek) or die(mysql_error($conn));
        if(mysql_num_rows($qcek)==0){
            $sIns="insert into ".$dbname.".kebun_5denda (`kode`, `nama`, `jumlah`, `updateby`) values 
                   ('".$_POST['regId']."','".$_POST['tanggal']."','".$_POST['ket']."','".$_SESSION['standard']['userid']."')";

            if(!mysql_query($sIns)){
                echo " error,".addslashes(mysql_error($conn));
            }	
        }else{
            exit("error: Data already exist");
        }

break;
case 'delete':
	$str="delete from ".$dbname.".sdm_5harilibur
	 where regional='".$_POST['regId']."' and tanggal='".tanggalsystem($_POST['tanggal'])."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case'loadData':
   
    echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
	     <thead>
		 <tr class=rowheader>
		    <td style='width:150px;'>".$_SESSION['lang']['kode']."</td>
			<td>".$_SESSION['lang']['nama']."</td>
			<td>".$_SESSION['lang']['jumlah']."</td>
			<td style='width:30px;'>*</td></tr>
		 </thead><tbody >"; 
        
        $sdata="select distinct * from ".$dbname.".kebun_5denda";
        $res1=mysql_query($sdata) or die(mysql_error($conn));
        if(mysql_num_rows($res1)>0){
	while($bar1=mysql_fetch_object($res1)){
		echo"<tr class=rowcontent>
		      <td align=center>".$bar1->kode."</td>
		      <td>".$bar1->nama."</td>
		      <td align=right>".$bar1->jumlah."</td>
		      <td><img src=images/application/application_edit.png class=resicon  caption='Edit' 
                                       onclick=\"fillField('".$bar1->kode."','".$bar1->nama."','".$bar1->jumlah."');\"></td></tr>";
	}	 
	echo"	 
		 </tbody>
		 <tfoot>
         
		 </tfoot>
		 </table>";
        }else{
            echo"<tr class=rowcontent><td colspan=4>".$_SESSION['lang']['dataempty']."</td></tr>";
        }
   break;					
}

?>