<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

$tanggal=isset($_POST['tanggal'])?$_POST['tanggal']:'';
$kodetraksi=isset($_POST['kodetraksi'])?$_POST['kodetraksi']:'';
$kde_vhc=isset($_POST['kd_vhc'])?$_POST['kd_vhc']:'';
$operator=isset($_POST['operator'])?$_POST['operator']:'';
$security=isset($_POST['security'])?$_POST['security']:'';
$karymekanik=isset($_POST['karymekanik'])?$_POST['karymekanik']:'';
$managerunit=isset($_POST['managerunit'])?$_POST['managerunit']:'';
$karyworkshop=isset($_POST['karyworkshop'])?$_POST['karyworkshop']:'';
$kronologiskejadian=isset($_POST['kronologiskejadian'])?$_POST['kronologiskejadian']:'';
$akibatkejadian=isset($_POST['akibatkejadian'])?$_POST['akibatkejadian']:'';

$method=$_POST['method'];
$optNmKar=  makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');


switch($method){
        case 'getData':
            $sql="select * from ".$dbname.".vhc_balaka where notransaksi='".$_POST['notransaksi']."'"; 
            $query=mysql_query($sql) or die(mysql_error());
            $res1=mysql_fetch_assoc($query);
            //exit('error:test');
            
            $sql1="select karyawanid, nama from ".$dbname.".vhc_5operator
            where vhc='".$res1['kodealat']."'"; 
            //exit('error:test2');
            //echo "rning:".$sql;
            $query=mysql_query($sql1) or die(mysql_error());
            while($resopr=mysql_fetch_assoc($query))
            {
                if($res1['operator']==$resopr['karyawanid']){
                    $optkaryoperator.="<option value='".$resopr['karyawanid']."' selected>[".$resopr['nama']."]</option>";
                }
            }
            //exit('error:test2');
          
           $sql="select kodevhc,kodetraksi from ".$dbname.".vhc_5master 
            where kodetraksi like '%".$res1['kodetraksi']."%' and status=1"; 
            //echo "warning:".$sql;
           //exit('error:test3');

            $query=mysql_query($sql) or die(mysql_error());
            while($resvhc=mysql_fetch_assoc($query))
            {
                if($res1['kodealat']==$resvhc['kodevhc']){
                 $optKdvhc.="<option value='".$resvhc['kodevhc']."' selected>[".$resvhc['kodevhc']."] [".$resvhc['kodetraksi']."]</option>";
                }else{
                    $optKdvhc.="<option value='".$resvhc['kodevhc']."' ".($resvhc['kodevhc']==$kdVhc?'selected=selected':'').">[".$resvhc['kodevhc']."] [".$resvhc['kodetraksi']."]</option>";
                }
            }
            echo $res1['notransaksi']."####".tanggalnormal($res1['tanggal'])."####".$res1['kodetraksi']."####".$optKdvhc."####".$optkaryoperator."####".$res1['security']."####".$res1['mekanik']."####".$res1['managerunit']."####".$res1['kaworkshop']."####".$res1['kronologis']."####".$res1['akibatkejadian'];
            break;
        case 'getkodefiled':
            $sql="select kodevhc,kodetraksi from ".$dbname.".vhc_5master 
            where kodetraksi like '%".$kdtrs."%' and status=1"; 
            //echo "warning:".$sql;

            $query=mysql_query($sql) or die(mysql_error());
            while($res=mysql_fetch_assoc($query))
            {
                if($_POST['kdevhc']==$res['kodevhc']){
                 $optKdvhc.="<option value='".$res['kodevhc']."' ".($res['kodevhc']==$kdVhc?'selected=selected':'').">[".$res['kodevhc']."] [".$res['kodetraksi']."]</option>";
                }else{
                    $optKdvhc.="<option value='".$res['kodevhc']."' ".($res['kodevhc']==$kdVhc?'selected=selected':'').">[".$res['kodevhc']."] [".$res['kodetraksi']."]</option>";
                }
            }
            echo $optKdvhc;
           break; 
        
        case 'getkode_vhc':
            $sql="select karyawanid, nama from ".$dbname.".vhc_5operator 
            where vhc='".$kde_vhc."'"; 
            //echo "warning:".$sql;
            

            $query=mysql_query($sql) or die(mysql_error());
            while($res=mysql_fetch_assoc($query))
            {
                    $optoperator.="<option value='".$res['karyawanid']."'>".$res[nama]."</option>";	
            }
            echo $optoperator;
            
            break;
        case 'baru':
            $thn=date('Y');
            $notrans=$_SESSION['empl']['lokasitugas']."/".$_SESSION['org']['kodeorganisasi']."/".date('m')."/".$thn;
            $sql="SELECT notransaksi FROM ".$dbname.".vhc_balaka WHERE notransaksi like '%".$notrans."' ORDER BY notransaksi desc";
            $query=mysql_query($sql) or die(mysql_error());
            $rdata=  mysql_fetch_assoc($query);
            $eksplot=explode("/",$rdata['notransaksi']);
            $awal=$eksplot[0];
            
            
            //exit("error:".$awal);
            $awal=intval($awal);
        $cekbln=isset($eksplot[1])?$eksplot[1]:'';
        $cekthn=isset($eksplot[4])?$eksplot[4]:'';
	if($thn!=$cekthn){
                $awal=1;
        }
        else{
                $awal++;
        }
        $counter=$awal;
        if($awal<1000)
        {$counter=addZero($awal,3);}

		$notransaksi=$counter."/".$notrans;
                echo $notransaksi;
            break;
	case 'insert':
            
            if($tanggal==''){
                exit("error: ".$_SESSION['lang']['tanggal']." is empty!");
            }
            if($_POST['kodetraksi']==''){
                exit("error: ".$_SESSION['lang']['kodetraksi']." is empty!");
            }
            if($_POST['kd_vhc']==''){
                exit("error: ".$_SESSION['lang']['kde_vhc']." is empty!");
            }

                    $i="insert into ".$dbname.".vhc_balaka (notransaksi,kodetraksi,tanggal,kodealat,operator,security,mekanik,managerunit,kaworkshop,kronologis,akibatkejadian,updateby)
                    values ('".$_POST['notransaksi']."','".$kodetraksi."','".tanggalsystem($tanggal)."','".$kde_vhc."','".$_POST['operator']."','".$_POST['security']."','".$_POST['karymekanik']."'
                            ,'".$_POST['managerunit']."','".$_POST['karyworkshop']."','".$_POST['kronologiskejadian']."','".$_POST['akibatkejadian']."','".$_SESSION['standard']['userid']."')";
                    //exit("error.$i");
                    
                    if(mysql_query($i))
                    echo"";
                    else
                    echo " Gagal,".addslashes(mysql_error($conn));
	break;
	
	case 'update':
            if($tanggal==''){
                exit("error: ".$_SESSION['lang']['tanggal']." is empty!");
            }
            if($_POST['kodetraksi']==''){
                exit("error: ".$_SESSION['lang']['kodetraksi']." is empty!");
            }
            if($_POST['kd_vhc']==''){
                exit("error: ".$_SESSION['lang']['kde_vhc']." is empty!");
            }
        
		
		$i="update ".$dbname.".vhc_balaka set kodetraksi='".$kodetraksi."',tanggal='".tanggalsystem($tanggal)."',kodealat='".$kde_vhc."'
                    ,operator='".$_POST['operator']."',security='".$_POST['security']."',mekanik='".$_POST['karymekanik']."'
                        ,managerunit='".$_POST['managerunit']."',kaworkshop='".$_POST['karyworkshop']."',kronologis='".$_POST['kronologiskejadian']."',akibatkejadian='".$_POST['akibatkejadian']."'
                    ,updateby='".$_SESSION['standard']['userid']."'
		 where notransaksi='".$_POST['notransaksi']."' ";
		//exit("Error.$str");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
	
		
case'loadData':
    
	echo"
	
		<table class=sortable cellspacing=1 border=0>
	     <thead>
			 <tr class=rowheader>
				 <td align=center>".$_SESSION['lang']['nourut']."</td>
                                 <td align=center>".$_SESSION['lang']['notransaksi']."</td>
                                 <td align=center>".$_SESSION['lang']['kodetraksi']."</td>
				 <td align=center>".$_SESSION['lang']['tanggal']."</td>
				 <td align=center>".$_SESSION['lang']['kendaraan']."</td>
				 <td align=center>".$_SESSION['lang']['operator']."</td>
				 <td align=center>".$_SESSION['lang']['security']."</td>
				 <td align=center>".$_SESSION['lang']['mekanik']."</td>
                                 <td align=center>".$_SESSION['lang']['managerunit']."</td>
                                 <td align=center>".$_SESSION['lang']['kaworkshop']."</td>
                                 <td align=center>".$_SESSION['lang']['action']."</td>
				 
			 </tr>
		</thead>
		<tbody>";
 		
		$limit=15;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		$maxdisplay=($page*$limit);
		
		$where='';
		if(!empty($_POST['noTransCr'])) {
			$where = "notransaksi like '%".$_POST['noTransCr']."%'";
		}
		if(!empty($where)) {
			$where = " where ".$where;
		}
		$ql2="select count(*) as jmlhrow from ".$dbname.".vhc_balaka".$where;
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
                
		$i="select * from ".$dbname.".vhc_balaka".$where."
                                     order by tanggal asc  limit ".$offset.",".$limit."";
                
		
		$n=mysql_query($i) or die(mysql_error());
		$no=$maxdisplay;
		while($d=mysql_fetch_assoc($n)){
			$no+=1;
			echo "<tr class=rowcontent>";
			echo "<td align=center>".$no."</td>";
                        echo "<td align=left>".$d['notransaksi']."</td>";
                        echo "<td align=left>".$d['kodetraksi']."</td>";
			echo "<td align=left>".$d['tanggal']."</td>";
                        echo "<td align=left>".$d['kodealat']."</td>";
                        echo "<td align=left>".$optNmKar[$d['operator']]."</td>";
                        echo "<td>".$optNmKar[$d['security']]."</td>";
                        echo "<td>".$optNmKar[$d['mekanik']]."</td>";
                        echo "<td>".$optNmKar[$d['managerunit']]."</td>";
                        echo "<td>".$optNmKar[$d['kaworkshop']]."</td>";
			echo "<td align=center>
			<img src=images/application/application_edit.png title=Edit class=resicon  caption='Edit' onclick=\"edit('".$d['notransaksi']."');\">
                        <img src=images/application/application_delete.png class=resicon title='Delete' onclick=\"delData('".$d['notransaksi']."');\">`
                        <img src=images/pdf.jpg class=resicon title=Print onclick=\"masterPDF('vhc_balaka','".$d['notransaksi']."','','vhc_slave_acara_laka_pdf',event);\">";
			echo "</tr>";
		}
		echo"</tbody><tfoot>
		<tr class=rowheader><td colspan=18 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=loadData(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=loadData(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
		echo"</tfoot></table>";
    break;

	case 'delete':
	//exit("Error:hahaha");
		$i="delete from ".$dbname.".vhc_balaka where notransaksi='".$_POST['notransaksi']."'";
		//exit("Error.$str");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
        case'upGradeData':
            $sData="select * from ".$dbname.".vhc_kegiatan where regional='".$_SESSION['empl']['regional']."'";
            //exit("error:masuk___".$sData);
            $qData=mysql_query($sData) or die(mysql_error($conn));
            if(mysql_num_rows($qData)==0){
                exit("error:Data Kosong");
            }
            while($rData=  mysql_fetch_assoc($qData)){
                @$basis=$rData['basis']+($rData['basis']*$_POST['bsisPrsn']/100);
                @$hrgsat=$rData['hargasatuan']+($rData['hargasatuan']*$_POST['hrgStnPrsn']/100);
                @$hrgLbh=$rData['hargaslebihbasis']+($rData['hargaslebihbasis']*$_POST['hrgLbhBsisPrsn']/100);
                @$hrgming=$rData['hargaminggu']+($rData['hargaminggu']*$_POST['hrgMnggPrsn']/100);
                $supdate="update ".$dbname.".vhc_kegiatan set basis='".$basis."',hargasatuan='".$hrgsat."'
                          ,hargaslebihbasis='".$hrgLbh."',hargaminggu='".$hrgming."',updateby='".$_SESSION['standard']['userid']."'
                          where regional='".$_SESSION['empl']['regional']."'";
                //exit("error:".$supdate);
                if(!mysql_query($supdate)){
                    exit("error: db gagal ".mysql_error($conn)."__".$supdate);
                }
            }
        break;

}
?>

