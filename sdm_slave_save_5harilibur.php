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
	$str="update ".$dbname.".sdm_5harilibur set tanggal='".tanggalsystem($_POST['tanggal'])."',keterangan='".$_POST['ket']."'
               ,updateby='".$_SESSION['standard']['userid']."'
	       where regional='".$_POST['regId']."' and tanggal='".tanggalsystem($_POST['tglOld'])."'";
        //exit("error:".$str);
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
break;
case 'insert':
        $scek="select distinct * from ".$dbname.".sdm_5harilibur where regional='".$_POST['regId']."' and tanggal='".tanggalsystem($_POST['tanggal'])."'";
        $qcek=mysql_query($scek) or die(mysql_error($conn));
        if(mysql_num_rows($qcek)==0){
            $sIns="insert into ".$dbname.".sdm_5harilibur (`regional`, `tanggal`, `keterangan`, `updateby`) values 
                   ('".$_POST['regId']."','".tanggalsystem($_POST['tanggal'])."','".$_POST['ket']."','".$_SESSION['standard']['userid']."')";

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
    if($_POST['ktrnganCr']!=''){
        $whr.=" and keterangan like '%".$_POST['ktrnganCr']."%'";
    }
    if($_POST['tgl_cari']!=''){
        $whr.=" and tanggal like '".tanggalsystem($_POST['tgl_cari'])."'";
    }
    echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
	     <thead>
		 <tr class=rowheader>
		    <td style='width:150px;'>".$_SESSION['lang']['regional']."</td>
			<td>".$_SESSION['lang']['tanggal']."</td>
			<td>".$_SESSION['lang']['keterangan']."</td>
			<td style='width:30px;'>*</td></tr>
		 </thead><tbody >"; 
        $limit=20;
        $page=0;
        if(isset($_POST['page'])){
            $page=$_POST['page'];
            if($page<0)
            $page=0;
        }
        $offset=$page*$limit;
        $sql2="select count(*) as jmlhrow from ".$dbname.".sdm_5harilibur 
                where regional='".$rreg['regional']."' ".$whr." order by tanggal desc";
        $query2=mysql_query($sql2) or die(mysql_error());
        while($jsl=mysql_fetch_object($query2)){
                    $jlhbrs= $jsl->jmlhrow;
        }
        $sdata="select distinct * from ".$dbname.".sdm_5harilibur 
                where regional='".$rreg['regional']."' ".$whr." 
                order by tanggal desc limit ".$offset.",".$limit."";
        $res1=mysql_query($sdata) or die(mysql_error($conn));
        if(mysql_num_rows($res1)>0){
	while($bar1=mysql_fetch_object($res1)){
		echo"<tr class=rowcontent>
		           <td align=center>".$bar1->regional."</td>
				   <td>".tanggalnormal($bar1->tanggal)."</td>
				   <td align=center>".$bar1->keterangan."</td>
				   <td><img src=images/application/application_edit.png class=resicon  caption='Edit' 
                                       onclick=\"fillField('".tanggalnormal($bar1->tanggal)."','".$bar1->keterangan."');\"></td></tr>";
	}	 
	echo"	 
		 </tbody>
		 <tfoot>
                  <tr><td colspan=4 align=center>
                    ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
                    <button class=mybutton onclick=loadData(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                    <button class=mybutton onclick=loadData(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                    </td>
                    </tr>
		 </tfoot>
		 </table>";
        }else{
            echo"<tr class=rowcontent><td colspan=4>".$_SESSION['lang']['dataempty']."</td></tr>";
        }
   break;					
}

?>