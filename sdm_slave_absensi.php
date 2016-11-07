<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

$proses=$_POST['proses'];
$txtFind=$_POST['txtfind'];
$absnId=explode("###",$_POST['absnId']);
$tgl=tanggalsystem($absnId[1]);
$kdOrg=$absnId[0];
$krywnId=$_POST['krywnId'];
$shifTid=$_POST['shifTid'];
$asbensiId=$_POST['asbensiId'];
$Jam=$_POST['Jam'];
$Jam2=$_POST['Jam2'];
$ket=$_POST['ket'];
$periode=$_POST['period'];
$idOrg=substr($_SESSION['empl']['lokasitugas'],0,4);
$catu=$_POST['catu'];
$penaltykehadiran=$_POST['dendakehadiran'];
//$periodeAkutansi=$_SESSION['org']['period']['tahun']."-".$_SESSION['org']['period']['bulan'];
$kdJbtn=makeOption($dbname, 'datakaryawan', 'karyawanid,kodejabatan',$where);
$tipeKary=makeOption($dbname, 'datakaryawan', 'karyawanid,tipekaryawan',$where);
$optKary=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan',"lokasitugas in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$_SESSION['empl']['kodeorganisasi']."')");
$optTipe=makeOption($dbname,'organisasi','kodeorganisasi,tipe');
        switch($proses){
                case'cariOrg':
                //echo"warning:masuk";
                $str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where namaorganisasi like '%".$txtFind."%' or kodeorganisasi like '%".$txtFind."%' "; //echo "warning:".$str;exit();
                if($res=mysql_query($str))
                {
                        echo"
          <fieldset>
        <legend>Result</legend>
        <div style=\"overflow:auto; height:300px;\" >
        <table class=data cellspacing=1 cellpadding=2  border=0>
                                 <thead>
                                 <tr class=rowheader>
                                 <td class=firsttd>
                                 No.
                                 </td>
                                 <td>".$_SESSION['lang']['kodeorg']."</td>
                                 <td>".$_SESSION['lang']['namaorganisasi']."</td>
                                 </tr>
                                 </thead>
                                 <tbody>";
                        $no=0;	 
                        while($bar=mysql_fetch_object($res))
                        {
                                $no+=1;
                                echo"<tr class=rowcontent style='cursor:pointer;' onclick=\"setOrg('".$bar->kodeorganisasi."','".$bar->namaorganisasi."')\" title='Click' >
                                          <td class=firsttd>".$no."</td>
                                          <td>".$bar->kodeorganisasi."</td>
                                          <td>".$bar->namaorganisasi."</td>
                                         </tr>";
                        }	 
                        echo "</tbody>
                                  <tfoot>
                                  </tfoot>
                                  </table></div></fieldset>";
                  }	
                  else
                        {
                                echo " Gagal,".addslashes(mysql_error($conn));
                        }	
                break;
                case'cariOrg2':
                //echo"warning:masuk";
                $str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where namaorganisasi like '%".$txtFind."%' or kodeorganisasi like '%".$txtFind."%' "; //echo "warning:".$str;exit();
                if($res=mysql_query($str))
                {
                        echo"
          <fieldset>
        <legend>Result</legend>
        <div style=\"overflow:auto; height:300px;\" >
        <table class=data cellspacing=1 cellpadding=2  border=0>
                                 <thead>
                                 <tr class=rowheader>
                                 <td class=firsttd>
                                 No.
                                 </td>
                                 <td>".$_SESSION['lang']['kodeorg']."</td>
                                 <td>".$_SESSION['lang']['namaorganisasi']."</td>
                                 </tr>
                                 </thead>
                                 <tbody>";
                        $no=0;	 
                        while($bar=mysql_fetch_object($res))
                        {
                                $no+=1;
                                echo"<tr class=rowcontent style='cursor:pointer;' onclick=\"setOrg2('".$bar->kodeorganisasi."','".$bar->namaorganisasi."')\" title='Click' >
                                          <td class=firsttd>".$no."</td>
                                          <td>".$bar->kodeorganisasi."</td>
                                          <td>".$bar->namaorganisasi."</td>
                                         </tr>";
                        }	 
                        echo "</tbody>
                                  <tfoot>
                                  </tfoot>
                                  </table></div></fieldset>";
                  }	
                  else
                        {
                                echo " Gagal,".addslashes(mysql_error($conn));
                        }	
                break;
                case'cekData':

                       
               
                    
                if($kdOrg==''){
                    exit("error: Unit code must filled");
                }
				if($asbensiId==''){
                    exit("error: Absen tipe empty");
                }

                // khusus KBL
                $whrTpKr="karyawanid='".$krywnId."'";
                $optTipeKary=  makeOption($dbname, "datakaryawan", "karyawanid,tipekaryawan", $whrTpKr);
                if ($optTipeKary[$krywnId]=="3"){
                    if($_POST['premidt']==''){
                        $_POST['premidt']=0;
                    }
                    if(($asbensiId=='H')||($asbensiId=='AS')){
                        if($_POST['premidt']!=$_POST['premi']){
                            $_POST['premi']=$_POST['premidt'];
                        }
                    }
                    
                    if($_POST['insentif']==''){
                        $_POST['insentif']='0';
                    }
                     
                    $sdtCek="select distinct * from ".$dbname.".kebun_kehadiran_vw 
                             where tanggal='".$tgl."' and karyawanid='".$krywnId."'";
                    $qDtCek=mysql_query($sdtCek) or die(mysql_error($conn));
                    $rSource=mysql_fetch_assoc($qDtCek);
                    $rDtCek=mysql_num_rows($qDtCek);
                    if($rDtCek>0){
                        exit("error: ".$_SESSION['lang']['emplregontran']." : ".$rSource['notransaksi']);
                    }
                    $scek="select * from ".$dbname.".sdm_absensidt where karyawanid='".$krywnId."' and tanggal='".$tgl."'";
                    $qcek=  mysql_query($scek) or die(mysql_error());
                    $rcek=  mysql_fetch_assoc($qcek);
                    if($rcek['karyawanid']!=''){
                        exit("error: ".$_SESSION['lang']['emplregondate']." : ".tanggalnormal($rcek['tanggal'])." ".$_SESSION['lang']['dan']." ".$_SESSION['lang']['unit']." :". $rcek['kodeorg']);
                    }
                    
                    
                    #cek apakah header kosong
                    #jika kosong insert header
                    
                  
                    
                    $iCekHead="select count(*) as jumHead from ".$dbname.".sdm_absensiht where kodeorg='".$kdOrg."' and tanggal='".$tgl."' ";
                    //exit("Error:$iCekHead");
                    $nCekHead=mysql_query($iCekHead) or die (mysql_error($conn));
                    $dCekHead=mysql_fetch_assoc($nCekHead);
                        if($dCekHead['jumHead']<1)
                        {
                            $period=substr($tgl,0,4)."-".substr($tgl,4,2);
                            $iInsHead="insert into ".$dbname.".sdm_absensiht (`tanggal`,`kodeorg`, `periode`, `updateby`) 
                              values ('".$tgl."','".$kdOrg."','".$period."','".$_SESSION['standard']['userid']."')";
                           // exit("error: ".$sDetIns);

                             if(mysql_query($iInsHead)) {
                                     echo"";
                             }
                             else
                             {echo "DB Error : ".mysql_error($conn);}
                        }
                            
                    
                    
                    $sDetIns="insert into ".$dbname.".sdm_absensidt (`kodeorg`,`tanggal`, `karyawanid`, `shift`, `absensi`, `jam`,`jamPlg`, `penjelasan`,`penaltykehadiran`,`premi`,`insentif`) 
                              values ('".$kdOrg."','".$tgl."','".$krywnId."','".$shifTid."','".$asbensiId."','".$Jam."','".$Jam2."','".$ket."',".$penaltykehadiran.",".$_POST['premidt'].",".$_POST['insentif'].")";
                   //exit("error: ".$sDetIns);

                    if(mysql_query($sDetIns)) {
                            echo"";
                    }
                    else
                    {echo "DB Error : ".mysql_error($conn);}
                } 
                else {
                    
                    
                 ###KHT    
                    
                #cek tutup buku
                $scek="select * from ".$dbname.".setup_periodeakuntansi where kodeorg='".$_SESSION['empl']['lokasitugas']."' and tanggalmulai<='".$tgl."' and tanggalsampai>='".$tgl."'";
		$qcek=  mysql_query($scek) or die(mysql_error($conn));
                $rcek=  mysql_fetch_assoc($qcek);
                $rRowcek=mysql_num_rows($qcek);
                if($rRowcek>0){
                    if($rcek['tutupbuku']==1){
                        exit("error:  This periode ".$rcek['periode']." already closed");
                    }
                }
                $sCek="select DISTINCT tanggalmulai,tanggalsampai,periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and sudahproses=0 and tanggalmulai<='".$tgl."' and tanggalsampai>='".$tgl."'";
                //exit("error:".$sCek);
                $qCek=mysql_query($sCek) or die(mysql_error());
                $rCek=mysql_num_rows($qCek);
                if($rCek>0){
                    
                      

                        $sCek="select kodeorg,tanggal from ".$dbname.".sdm_absensiht where tanggal='".$tgl."' and kodeorg='".$kdOrg."'"; //echo "warning".$sCek;nospb
                        //exit("error:".$sCek);
                        $qCek=mysql_query($sCek) or die(mysql_error());
                        $rCek=mysql_fetch_row($qCek);

                        if($rCek<1){
                                $sIns="insert into ".$dbname.".sdm_absensiht (`kodeorg`,`tanggal`,`periode`,`updateby`,`updatetime`) 
                                       values ('".$kdOrg."','".$tgl."','".$periode."','".$_SESSION['standard']['userid']."','".date("Y-m-d H:i:s")."')"; //echo"warning:".$sIns;
                                if(mysql_query($sIns))
                                {
                                        if($_POST['premidt']==''){
                                            $_POST['premidt']=0;
                                        }
                                         
                                        if(($asbensiId=='H')||($asbensiId=='AS')){
                                            if($_POST['premidt']!=$_POST['premi']){
                                                $_POST['premi']=$_POST['premidt'];
                                            }
                                        }
                                        
                                        
                                        #cek kebun kehadiran
                                        $sdtCek="select distinct * from ".$dbname.".kebun_kehadiran_vw 
                                                 where tanggal='".$tgl."' and karyawanid='".$krywnId."'";
                                        $qDtCek=mysql_query($sdtCek) or die(mysql_error($conn));
                                        $rSource=mysql_fetch_assoc($qDtCek);
                                        $rDtCek=mysql_num_rows($qDtCek);
                                        if($rDtCek>0){
                                            exit("error: ".$_SESSION['lang']['emplregontran']." : ".$rSource['notransaksi']);
                                        }
                                       
                                        #cek absensi sdm
                                        $scek="select * from ".$dbname.".sdm_absensidt where karyawanid='".$krywnId."' and tanggal='".$tgl."'";
                                        $qcek=  mysql_query($scek) or die(mysql_error());
                                        $rcek=  mysql_fetch_assoc($qcek);
                                        if($rcek['karyawanid']!=''){
                                            exit("error: ".$_SESSION['lang']['emplregondate']." : ".tanggalnormal($rcek['tanggal'])." ".$_SESSION['lang']['dan']." ".$_SESSION['lang']['unit']." :". $rcek['kodeorg']);
                                        }
                                        
                                        #cek data dari vhc
                                        $iVhc="select distinct * from ".$dbname.".vhc_runhk where idkaryawan='".$krywnId."' and tanggal='".$tgl."'";
                                        $nVhc=mysql_query($iVhc) or die (mysql_error($conn));
                                        $dVhc=mysql_fetch_assoc($nVhc);
                                        $wVhc=mysql_num_rows($nVhc);
                                        if($wVhc>0)
                                        {
                                            //exit("Error:MASUK");
                                            exit("error: Absence already input in : ".$dVhc['notransaksi']);
                                        }
                                        
                                        $sDetIns="insert into ".$dbname.".sdm_absensidt (`kodeorg`,`tanggal`, `karyawanid`, `shift`, `absensi`, `jam`,`jamPlg`, `penjelasan`,`penaltykehadiran`,`premi`,`insentif`) 
                                                  values ('".$kdOrg."','".$tgl."','".$krywnId."','".$shifTid."','".$asbensiId."','".$Jam."','".$Jam2."','".$ket."',".$penaltykehadiran.",".$_POST['premidt'].",".$_POST['insentif'].")";
                                
                                       
                                        if(mysql_query($sDetIns))
                                        {
                                                echo"";
                                        }
                                        else
                                        {echo "DB Error : ".mysql_error($conn);}
                                }
                                else
                                {
                                        echo "DB Error : ".mysql_error($conn);
                                }
                        }
                        else
                        {
                           // exit("Error:MASUK COI");   
                             if($_POST['premidt']==''){
                                $_POST['premidt']=0;
                              }

                                        $sdtCek="select distinct * from ".$dbname.".kebun_kehadiran_vw 
                                                 where tanggal='".$tgl."' and karyawanid='".$krywnId."'";
                                        //exit("error:dapet lho".$sdtCek);
                                        $qDtCek=mysql_query($sdtCek) or die(mysql_error($conn));
                                        $rSource=mysql_fetch_assoc($qDtCek);
                                        $rDtCek=mysql_num_rows($qDtCek);
                                        if($rDtCek>0){
                                            exit("error: ".$_SESSION['lang']['emplregontran']." : ".$rSource['notransaksi']);
                                        }
                                        $scek="select * from ".$dbname.".sdm_absensidt where karyawanid='".$krywnId."' and tanggal='".$tgl."'";
                                        $qcek=  mysql_query($scek) or die(mysql_error());
                                        $rcek=  mysql_fetch_assoc($qcek);
                                        if($rcek['karyawanid']!=''){
                                            exit("error: ".$_SESSION['lang']['emplregondate']." : ".tanggalnormal($rcek['tanggal'])." ".$_SESSION['lang']['dan']." ".$_SESSION['lang']['unit']." :". $rcek['kodeorg']);
                                        }
                                        
                                        
                                        #cek traksi inputan untuk KHT #cek data dari vhc
                                        $iVhc="select distinct * from ".$dbname.".vhc_runhk where idkaryawan='".$krywnId."' and tanggal='".$tgl."'";
                                        $nVhc=mysql_query($iVhc) or die (mysql_error($conn));
                                        $dVhc=mysql_fetch_assoc($nVhc);
                                        $wVhc=mysql_num_rows($nVhc);
                                        if($wVhc>0)
                                        {
                                            //exit("Error:MASUK");
                                            exit("error: Absence already input in : ".$dVhc['notransaksi']);
                                        }
                                        
                                        
                                       
                                //$sDetIns="insert into ".$dbname.".sdm_absensidt (`kodeorg`,`tanggal`, `karyawanid`, `shift`, `absensi`, `jam`,`jamPlg`, `penjelasan`,`catu`,`penaltykehadiran`) values ('".$kdOrg."','".$tgl."','".$krywnId."','".$shifTid."','".$asbensiId."','".$Jam."','".$Jam2."','".$ket."',".$catu.",".$penaltykehadiran.")";
                                         $sDetIns="insert into ".$dbname.".sdm_absensidt (`kodeorg`,`tanggal`, `karyawanid`, `shift`, `absensi`, `jam`,`jamPlg`, `penjelasan`,`penaltykehadiran`,`premi`,`insentif`) 
                                                  values ('".$kdOrg."','".$tgl."','".$krywnId."','".$shifTid."','".$asbensiId."','".$Jam."','".$Jam2."','".$ket."',".$penaltykehadiran.",".$_POST['premidt'].",".$_POST['insentif'].")";
                                        //exit("error:".$sDetIns);
                                        //echo "warning:test".$dins;
                                        if(mysql_query($sDetIns))
                                        {
                                                echo"";
                                        }
                                        else
                                        {
                                        //echo "warning:masuk";
                                        echo "DB Error : ".mysql_error($conn);
                                        }
                        }
              // exit(" Error:".$sDetIns);
                }
                else
                {
                        echo"warning:Date out of payment period";
                        exit();
                }
                }
                break;
                case'loadNewData':
                   
                echo"
                <table cellspacing=1 border=0 class=sortable>
                <thead>
                <tr class=rowheader>
                <td>No.</td>
                <td>".$_SESSION['lang']['kodeorg']."</td>
                <td>".$_SESSION['lang']['tanggal']."</td>
                <td>".$_SESSION['lang']['periode']."</td>
                <td>".$_SESSION['lang']['updateby'] ."</td>
                <td>Action</td>
                </tr>
                </thead>
                <tbody>
                ";
                $limit=20;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;

                $ql2="select count(*) as jmlhrow from ".$dbname.".sdm_absensiht where substring(kodeorg,1,4)='".$idOrg."' order by `tanggal` desc";// echo $ql2;
                $query2=mysql_query($ql2) or die(mysql_error());
                while($jsl=mysql_fetch_object($query2)){
                $jlhbrs= $jsl->jmlhrow;
                }


                $slvhc="select * from ".$dbname.".sdm_absensiht where substring(kodeorg,1,4)='".$idOrg."' order by `tanggal` desc limit ".$offset.",".$limit."";
                $qlvhc=mysql_query($slvhc) or die(mysql_error());
                $user_online=$_SESSION['standard']['userid'];
                while($rlvhc=mysql_fetch_assoc($qlvhc))
                {
                        $sOrg="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$rlvhc['kodeorg']."'";
                        $qOrg=mysql_query($sOrg) or die(mysql_error());
                        $rOrg=mysql_fetch_assoc($qOrg);
                        $sGp="select DISTINCT sudahproses from ".$dbname.".sdm_5periodegaji where kodeorg='".substr($rlvhc['kodeorg'],0,4)."' and `periode`='".$rlvhc['periode']."' and tanggalmulai<='".$rlvhc['tanggal']."' and tglcutoff>='".$rlvhc['tanggal']."'";
                        $qGp=mysql_query($sGp) or die(mysql_error());
                        $rGp=mysql_fetch_assoc($qGp);

					$no+=1;
					echo"
					<tr class=rowcontent>
					<td>".$no."</td>
					<td>".$rlvhc['kodeorg']."</td>
					<td>".tanggalnormal($rlvhc['tanggal'])."</td>
					<td>".substr(tanggalnormal($rlvhc['periode']),1,7)."</td>
                                        <td>".$optKary[$rlvhc['updateby']]."</td>
					<td>";
					
						$scek="select distinct jabatan from ".$dbname.".setup_posting where kodeaplikasi='absensi'";
						$qcek=mysql_query($scek) or die(mysql_error($conn));
						$rcek=mysql_fetch_assoc($qcek);
						if($rGp['sudahproses']==0){
                                                            $sLok="select distinct * from ".$dbname.".setup_temp_lokasitugas where karyawanid='".$_SESSION['standard']['userid']."'";
                                                            $qLok=  mysql_query($sLok) or die(mysql_error($conn));
                                                            $rLok=  mysql_fetch_assoc($qLok);
                                                            $rowLok=  mysql_num_rows($qLok);
                                                            if($rowLok>0){
                                                                if ($optTipe[$_SESSION['empl']['subbagian']]=='TRAKSI' && $rlvhc['kodeorg']==$_SESSION['empl']['subbagian'] && $_SESSION['standard']['userid']==$rlvhc['updateby']){
                                                                        echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['periode']."');\">";
                                                                } else {
                                                                    if($rLok['kodeorg']==substr($rlvhc['kodeorg'],0,4))
                                                                    {
                                                                            if(($_SESSION['empl']['kodejabatan']==$rcek['jabatan'])||($_SESSION['standard']['userid']==$rlvhc['updateby'])||($rlvhc['updateby']=='0000000000')||($_SESSION['empl']['bagian']=='IT'))
                                                                            {
                                                                                    echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['periode']."');\">";
                                                                                         //<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\" >";
                                                                            }
                                                                    }
                                                                }
                                                            }
                                                            else
                                                            {
//                                                                    if(($_SESSION['empl']['kodejabatan']==$rcek['jabatan'] and $_SESSION['standard']['userid']==$rlvhc['updateby'])||($rlvhc['updateby']=='0000000000')||($_SESSION['empl']['bagian']=='IT'))
                                                                    if((($_SESSION['empl']['kodejabatan']==$rcek['jabatan']||$_SESSION['empl']['kodejabatan']==3) and $_SESSION['standard']['userid']==$rlvhc['updateby']) 
                                                                            || $rlvhc['updateby']=='0000000000' || $_SESSION['empl']['bagian']=='IT')
                                                                    {
                                                                                    echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['periode']."');\">";
                                                                                         //<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\" >";
                                                                    }
                                                            }
							
                                                }
					echo"<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_absensiht','".$rlvhc['kodeorg'].",".tanggalnormal($rlvhc['tanggal'])."','','sdm_absensiPdf',event)\">";
					
					echo"</td>
					</tr>
					";
                }
                echo"
                <tr class=rowheader><td colspan=5 align=center>
                ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
                <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                </td>
                </tr>";
                echo"</tbody></table>";
                break;
				
				
                case'delData':
//                $sCek="select posting from ".$dbname.".sdm_gaji where periodegaji='".substr(tanggaldgnbar($absnId[1]),0,7)."' and kodeorg='".substr($kdOrg,0,4)."'"; //\\echo "warning".$sCek;;
//                $qCek=mysql_query($sCek) or die(mysql_error());
//                $rCek=  mysql_num_rows($qCek);
//                if($rCek>0){
//                        echo"warning:This Period Already Close";
//                        exit();
//                }
                $sDel="delete from ".$dbname.".sdm_absensiht where tanggal='".$tgl."' and kodeorg='".$kdOrg."'";// echo "___".$sDel;exit();
                if(mysql_query($sDel))
                {
                        $sDelDetail="delete from ".$dbname.".sdm_absensidt where tanggal='".$tgl."' and kodeorg='".$kdOrg."'";
                        if(mysql_query($sDelDetail))
                        echo"";
                        else
                        echo "DB Error : ".mysql_error($conn);
                }
                else
                {echo "DB Error : ".mysql_error($conn);}

                break;
                case'cekHeader':
                //echo"warning:masuk";
                    $abs=explode("###",$_POST['absnId']);
                    if($abs[0]==''){
                        exit("error: Unit code must filled");
                    }
                 $sCek="select DISTINCT tanggalmulai,tanggalsampai,periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and periode='".$periode."' and sudahproses=0 and tanggalmulai<='".$tgl."' and tanggalsampai>='".$tgl."'";
                //    $sCek="select DISTINCT tanggalmulai,tanggalsampai,periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and periode='".$periode."' and sudahproses=0";
                $qCek=mysql_query($sCek) or die(mysql_error());
                $rCek=mysql_num_rows($qCek);
                //$rCek=mysql_fetch_assoc($qCek);
                if($rCek<1)
               // if($rCek['tanggalmulai']<=$tgl || $rCek['tanggalsampai']>=$tgl)
                {
                        echo"warning:Date out of range";
                        exit();
                }
                //echo"warning:masuk".$aktif;exit();
                $sCek="select kodeorg,tanggal from ".$dbname.".sdm_absensiht where tanggal='".$tgl."' and kodeorg='".$kdOrg."'"; //echo "warning".$sCek;nospb
               
                $qCek=mysql_query($sCek) or die(mysql_error());
                $rCek=mysql_fetch_row($qCek);
                if($rCek>0)
                {
                        echo"warning:This date and Organization Name already exist";
                        exit();
                }


                $str="select * from ".$dbname.".setup_periodeakuntansi where periode='".$periode."' and
                kodeorg='".$_SESSION['empl']['lokasitugas']."' and tutupbuku=1";
               // exit("Error".$str) ;
                $res=mysql_query($str);
                if(mysql_num_rows($res)>0)
                $aktif=true;
                else
                $aktif=false;
                if($aktif==true)
                {
                exit("Error: This period ".$periode." already closed");
                }
               
                break;
				
				
                case'cariAbsn':
              
                    if($kdOrg!='')
                    {
                        $where.=" and kodeorg='".$kdOrg."'";
                    }
                    if($tgl!='')
                    {
                        $bln=explode("-",$absnId[1]);

                        $where.=" and tanggal='".$bln[2]."-".$bln[1]."-".$bln[0]."'";
                    }
                    if($tgl==''){
                        if($_POST['periode']!=''){
                            $where.=" and tanggal like '".$_POST['periode']."%'";
                        }
                    }
                    if($_POST['niknm']!=''){
                        if(($_POST['periode']=='')||($tgl=='')){
                            exit("error: Please fill period or date for filter");
                        }
                        $where.=" and karyawanid in (select distinct karyawanid from ".$dbname.".datakaryawan where nik like '%".$_POST['niknm']."%' or namakaryawan like '%".$_POST['niknm']."%')";
                    }
                echo"
                <div style=overflow:auto; height:350px;>
                <table cellspacing=1 border=0>
                <thead>
                <tr class=rowheader>
                <td>No.</td>
                <td>".$_SESSION['lang']['kodeorg']."</td>
                <td>".$_SESSION['lang']['tanggal']."</td>
                <td>".$_SESSION['lang']['periode']."</td>
                <td>".$_SESSION['lang']['updateby']."</td>
                <td>Action</td>
                </tr>
                </thead>
                <tbody>
                ";
                
                /*$sCek="select distinct kodeorg,tanggal,left(tanggal,7) as periode from ".$dbname.".sdm_absensidt "
                    . "where left(kodeorg,4)='".$_SESSION['empl']['lokasitugas']."' ".$where." order by tanggal desc";//echo "warning".$sCek;exit();*/
                $sCek="select distinct kodeorg,tanggal,left(tanggal,7) as periode,updateby from ".$dbname.".sdm_absensiht "
                    . "where left(kodeorg,4)='".$_SESSION['empl']['lokasitugas']."' ".$where." order by tanggal desc";//echo "warning".$sCek;exit();
                //echo $sCek;
                $qCek=mysql_query($sCek) or die(mysql_error());
                $rCek=mysql_num_rows($qCek);
                if($rCek>0){
                        $qlvhc=mysql_query($sCek) or die(mysql_error());
                        $user_online=$_SESSION['standard']['userid'];
                        while($rlvhc=mysql_fetch_assoc($qlvhc))
                        {
                                $sOrg="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$rlvhc['kodeorg']."'";
                                $qOrg=mysql_query($sOrg) or die(mysql_error());
                                $rOrg=mysql_fetch_assoc($qOrg);
                                $sGp="select DISTINCT sudahproses from ".$dbname.".sdm_5periodegaji where kodeorg='".$rlvhc['kodeorg']."' and `periode`='".$rlvhc['periode']."' and tanggalmulai<='".$rlvhc['tanggal']."' and tglcutoff>='".$rlvhc['tanggal']."'";
                                $qGp=mysql_query($sGp) or die(mysql_error());
                                $rGp=mysql_fetch_assoc($qGp);
                                        $no+=1;
                                echo"
                                <tr class=rowcontent>
                                <td>".$no."</td>
                                <td>".$rlvhc['kodeorg']."</td>
                                <td>".tanggalnormal($rlvhc['tanggal'])."</td>
                                <td>".substr(tanggalnormal($rlvhc['periode']),1,7)."</td>
                                <td>".$optKary[$rlvhc['updateby']]."</td>
                                <td>";
				
					$scek="select distinct jabatan from ".$dbname.".setup_posting where kodeaplikasi='absensi'";
					$qcek=mysql_query($scek) or die(mysql_error($conn));
					$rcek=mysql_fetch_assoc($qcek);
			        if($rGp['sudahproses']==0){
						$sLok="select distinct * from ".$dbname.".setup_temp_lokasitugas where karyawanid='".$_SESSION['standard']['userid']."'";
						$qLok=  mysql_query($sLok) or die(mysql_error($conn));
						$rLok=  mysql_fetch_assoc($qLok);
						$rowLok=  mysql_num_rows($qLok);
						if($rowLok>0){
                                                    if ($optTipe[$_SESSION['empl']['subbagian']]=='TRAKSI' && $rlvhc['kodeorg']==$_SESSION['empl']['subbagian'] && $_SESSION['standard']['userid']==$rlvhc['updateby']){
                                                        echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['periode']."');\">
                                                             <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\" >";
                                                    } else {
							if($rLok['kodeorg']==substr($rlvhc['kodeorg'],0,4)){
								if(($_SESSION['empl']['kodejabatan']==$rcek['jabatan'])||($_SESSION['standard']['userid']==$rlvhc['updateby'])||($rlvhc['updateby']=='0000000000')||($_SESSION['empl']['bagian']=='IT')){
									echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['periode']."');\">
								             <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\" >";
								}
							}
                                                    }
					   }else{
						   if(($_SESSION['empl']['kodejabatan']==$rcek['jabatan'])||($_SESSION['standard']['userid']==$rlvhc['updateby'])||($rlvhc['updateby']=='0000000000')||($_SESSION['empl']['bagian']=='IT')){
								echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['periode']."');\">
							             <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\" >";
						    }
					   }
				}
               
                        echo"<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_absensiht','".$rlvhc['kodeorg'].",".tanggalnormal($rlvhc['tanggal'])."','','sdm_absensiPdf',event)\">";
               
                echo"</td>
                </tr>
                ";
                        }

                        echo"</tbody></table></div>";
                }
                else
                {
                        echo"<tr class=rowcontent><td colspan=5 align=center>Not Found</td></tr></tbody></table></div>";
                }
                break;
                case'updateData':
                     if($_POST['premidt']==''){
                        $_POST['premidt']=0;
                     }
                                
                    if($kdOrg==''){
                        exit("error:Unit code must filled");
                    }
                    #cek tutup buku
//                    $scek="select * from ".$dbname.".setup_periodeakuntansi where kodeorg='".$_SESSION['empl']['lokasitugas']."' and tanggalmulai<='".$tgl."' and tanggalsampai>='".$tgl."'";
//                    //exit("error:".$scek);
//                    $qcek=  mysql_query($scek) or die(mysql_error($conn));
//                    $rcek=  mysql_fetch_assoc($qcek);
//                    $rRowcek=mysql_num_rows($qcek);
//                    if($rRowcek>0){
//                        if($rcek['tutupbuku']==1){
//                            exit("error:  This period ".$rcek['periode']." already closed");
//                        }
//                    }
            $sdtCek="select distinct * from ".$dbname.".kebun_kehadiran_vw 
                                 where tanggal='".$tgl."' and karyawanid='".$krywnId."'";
                        $qDtCek=mysql_query($sdtCek) or die(mysql_error($conn));
                        $rSource=mysql_fetch_assoc($qDtCek);
                        $rDtCek=mysql_num_rows($qDtCek);
                        if($rDtCek>0){
                            exit("error: ".$_SESSION['lang']['emplregontran']." : ".$rSource['notransaksi']);
                        }
                $sUpd="update ".$dbname.".sdm_absensidt set shift='".$shifTid."',absensi='".$asbensiId."',jam='".$Jam."',jamPlg='".$Jam2."',penjelasan='".$ket."',
                       penaltykehadiran=".$penaltykehadiran." ,`premi` ='".$_POST['premidt']."',`insentif` ='".$_POST['insentif']."'
                       where kodeorg='".$kdOrg."' and tanggal='".$tgl."' and karyawanid='".$krywnId."'";
                //exit("error:".$sUpd);
                        if(mysql_query($sUpd))
                        echo"";
                        else
                        echo "DB Error : ".mysql_error($conn);
                break;
                case'delDetail':
                        $sDelDetail="delete from ".$dbname.".sdm_absensidt where tanggal='".$tgl."' and kodeorg='".$kdOrg."' and karyawanid='".$krywnId."'";
                        if(mysql_query($sDelDetail))
                        echo"";
                        else
                        echo "DB Error : ".mysql_error($conn);
                break;
                case'getPremi':
                    $upahHarian=0;
                    $where="karyawanid='".$_POST['karyId']."'";
                    $tpKary=makeOption($dbname, 'datakaryawan', 'karyawanid,tipekaryawan',$where);
                    $tgl=explode("-",$_POST['tglDt']);
                    $periode=$tgl[2]."-".$tgl[1];
                    $isi=$tgl[2]."-".$tgl[1]."-".$tgl[0];
                    
                    if($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
                        exit();
                    }
                    if($tpKary[$_POST['karyId']]==4){
                    $sUmr="select sum(jumlah) as jumlah from ".$dbname.".sdm_5gajipokok 
                        where karyawanid='".$_POST['karyId']."' and tahun=".$tgl[2]."  and idkomponen='1'";
                        $qUmr=mysql_query($sUmr) or die(mysql_error());
                        $rUmr=mysql_fetch_assoc($qUmr);
                        @$umr=$rUmr['jumlah']/25;
                        if($umr==0){
                            exit("error: ".$_SESSION['lang']['basicsalarynotfound']);
                        }
                    $sAbsnGkByr="select distinct kodeabsen from ".$dbname.".sdm_5absensi where kelompok=0";
                    $qAbsnGkByr=mysql_query($sAbsnGkByr) or die(mysql_error($conn));
                    while($rAbnGkByr=  mysql_fetch_assoc($qAbsnGkByr)){
                        $arrGkDbyr[$rAbnGkByr['kodeabsen']]=$rAbnGkByr['kodeabsen'];
                    }
					if(($_POST['absnId']=='MG')||($_POST['absnId']=='L')){	
						$arrGkDbyr[$_POST['absnId']]=$_POST['absnId'];
					}
                    $arrExc=array("C");
                             if($arrExc[$_POST['absnId']]!=''){
                                 $upahHarian=$umr;
                             }elseif($arrGkDbyr[$_POST['absnId']]){
                                 $upahHarian=0;
                             }else{
                                    if($_POST['jamPlg']=='00:00'){
                                        $_POST['jmMulai']="00:00";
                                    }
                                    $jm1=explode(":",$_POST['jmMulai']);
                                    $jm2=explode(":",$_POST['jamPlg']);

                                    $dtTmbh=0;
                                    if($jm2<$jm1){
                                        $dtTmbh=1;
                                    }
                                    $qwe=date('D', strtotime($isi));
                                    //$hari=date('D',  strtotime($t));
                                    //exit("error: ".$qwe);
                                    
                                    $wktmsk=mktime(intval($jm1[0]),intval($jm1[1]),intval($jm1[2]),intval(substr($_POST['tglDt'],3,2)),intval(substr($_POST['tglDt'],0,2)),substr($_POST['tglDt'],6,4));
                                    $wktplg=mktime(intval($jm2[0]),intval($jm2[1]),intval($jm2[2]),intval(substr($_POST['tglDt'],3,2)),intval(substr($_POST['tglDt'],0,2)+$dtTmbh),substr($_POST['tglDt'],6,4));
                                    $slsihwaktu=$wktplg-$wktmsk;
                                    $sisa = $slsihwaktu % 86400;
                                    $jumlah_jam = $sisa/3600;  
                                    //exit("error:".$jumlah_jam);
                                    if($qwe=='Sat'){
                                        if($jumlah_jam>=5){
                                            $upahHarian=$umr;
                                        }else{
                                            $upahHarian=($jumlah_jam/5)*$umr;    
                                        }    
                                    }else{
                                        if($jumlah_jam>=7){
                                            $upahHarian=$umr;
                                        }else{
                                            $upahHarian=($jumlah_jam/7)*$umr;    
                                        }
                                    }
                             }
                    }
                    echo $upahHarian;
                break;
        }
//tentukan waktu tujuan
//$waktu_tujuan = mktime(8,0,0,9,20,2012);exit
//
////tentukan waktu saat ini
//$waktu_sekarang = mktime(date(“H”), date(“i”), date(“s”), date(“m”), date(“d”), date(“Y”));
//
////hitung selisih kedua waktu
//$selisih_waktu = $waktu_tujuan – $waktu_sekarang;
//
////Untuk menghitung jumlah dalam satuan hari:
//$jumlah_hari = floor($selisih_waktu/86400);
//
////Untuk menghitung jumlah dalam satuan jam:
//$sisa = $selisih_waktu % 86400;
//$jumlah_jam = floor($sisa/3600);
?>