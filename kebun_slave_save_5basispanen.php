<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
require_once('config/connection.php');
include_once('lib/zLib.php');
$nmOrg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$method=$_POST['method'];

$sreg="select distinct regional from ".$dbname.".bgt_regional_assignment 
                where kodeunit='".$_SESSION['empl']['lokasitugas']."' ";
$qreg=mysql_query($sreg) or die(mysql_error($conn));
$rreg=mysql_fetch_assoc($qreg);


switch($method){
case 'update':	
	$str="update ".$dbname.".kebun_5basispanen set kodeorg='".$_POST['regId']."',jenis='".$_POST['jnsId']."',
              bjr='".$_POST['bjr']."',basisjjg='".$_POST['basisjjg']."',rplebih='".$_POST['rpperkg']."'
            ,dendabasis='".$_POST['denda']."',rptopografi='".$_POST['insentif']."'
            ,updateby='".$_SESSION['standard']['userid']."'
	     where kodeorg='".$_POST['oldReg']."' and jenis='".$_POST['oldJns']."' 
                and bjr='".$_POST['oldBjr']."'";
        //exit("error:".$str);
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
break;
case 'insert':
    if($_POST['bjr']==''){
        $_POST['bjr']=0;
    }
        $scek="select distinct * from ".$dbname.".kebun_5basispanen where 
               bjr='".$_POST['bjr']."' and jenis='".$_POST['jnsId']."' and
               kodeorg='".$_POST['regId']."'";
        $qcek=mysql_query($scek) or die(mysql_error($conn));
        if(mysql_num_rows($qcek)==0){
            $sIns="insert into ".$dbname.".kebun_5basispanen (`kodeorg`, `jenis`, `bjr`,`basisjjg`,`rplebih`,`dendabasis`,`rptopografi`,`updateby`) values 
                   ('".$_POST['regId']."','".$_POST['jnsId']."','".$_POST['bjr']."','".$_POST['basisjjg']."','".$_POST['rpperkg']."','".$_POST['denda']."'
                    ,'".$_POST['insentif']."','".$_SESSION['standard']['userid']."')";

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
		    <td style='width:150px;'>".$_SESSION['lang']['unit']."/".$_SESSION['lang']['regional']."</td>
			<td>".$_SESSION['lang']['jenis']."</td>
			<td>".$_SESSION['lang']['bjr']."</td>
                        <td>".$_SESSION['lang']['basisjjg']."</td>
                        <td>".$_SESSION['lang']['rpperkg']."</td>
                        <td>".$_SESSION['lang']['denda']."</td>
                        <td>".$_SESSION['lang']['insentif']." ".$_SESSION['lang']['topografi']."</td>
			<td style='width:30px;'  align=center>*</td></tr>
		 </thead><tbody >"; 
        $limit=20;
        $page=0;
        if(isset($_POST['page'])){
            $page=$_POST['page'];
            if($page<0)
            $page=0;
        }
        $offset=$page*$limit;
        $sql2="select count(*) as jmlhrow from ".$dbname.".kebun_5basispanen 
               where (kodeorg='".$rreg['regional']."' or left(kodeorg,4) in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$rreg['regional']."'))
               order by kodeorg desc";
        $query2=mysql_query($sql2) or die(mysql_error());
        while($jsl=mysql_fetch_object($query2)){
                    $jlhbrs= $jsl->jmlhrow;
        }
        $sdata="select distinct * from ".$dbname.".kebun_5basispanen 
                where (kodeorg='".$rreg['regional']."' or left(kodeorg,4) in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$rreg['regional']."'))
                order by kodeorg desc limit ".$offset.",".$limit."";
        $res1=mysql_query($sdata) or die(mysql_error($conn));
        if(mysql_num_rows($res1)>0){
	while($bar1=mysql_fetch_object($res1)){
            
          //  if(strlen($bar1->kodeorg)==6){
          //      $bar1->kodeorg=$nmOrg[$bar1->kodeorg];
         //   }
		 
		 if(strlen($bar1->kodeorg)==6)
			$a=$nmOrg[$bar1->kodeorg];
		else
			$a=$bar1->kodeorg;
		 
		echo"<tr class=rowcontent>
                    <td>".$a."</td>
                    <td>".$bar1->jenis."</td>
                    <td align=right>".$bar1->bjr."</td>
                    <td align=right>".$bar1->basisjjg."</td>
                    <td align=right>".$bar1->rplebih."</td>
                    <td>".$bar1->dendabasis."</td>
                    <td align=right>".$bar1->rptopografi."</td>
				   <td align=center><img src=images/application/application_edit.png class=resicon  caption='Edit' 
                                       onclick=\"fillField('".$bar1->kodeorg."','".$bar1->jenis."','".$bar1->bjr."','".$bar1->basisjjg."','".$bar1->rplebih."','".$bar1->dendabasis."','".$bar1->rptopografi."');\"></td></tr>";
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