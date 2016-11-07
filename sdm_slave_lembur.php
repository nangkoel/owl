<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');


$proses=$_POST['proses'];
$txtFind=isset($_POST['txtfind'])? $_POST['txtfind']:'';
$absnId=isset($_POST['absnId'])? explode("###",$_POST['absnId']): array('','');
$tgl=tanggalsystem($absnId[1]);
$kdOrg=$absnId[0];
$krywnId=isset($_POST['krywnId'])? $_POST['krywnId']:'';
$tpLmbr=isset($_POST['tpLmbr'])? $_POST['tpLmbr']:'';
$ungTrans=isset($_POST['ungTrans'])? $_POST['ungTrans']:'';
$ungMkn=isset($_POST['ungMkn'])? $_POST['ungMkn']:'';
$Jam=isset($_POST['Jam'])? $_POST['Jam']:'';
$ungLbhjm=isset($_POST['ungLbhjm'])? $_POST['ungLbhjm']:'';
$optKry='';
$optTipelembur="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$arrsstk=array("0"=>$_SESSION['lang']['haribiasa'],"1"=>$_SESSION['lang']['hariminggu'],"2"=>$_SESSION['lang']['harilibur'],"3"=>$_SESSION['lang']['hariraya']);
$kodeOrg=isset($_POST['kodeOrg'])? $_POST['kodeOrg']:'';
$basisJam=isset($_POST['basisJam'])? $_POST['basisJam']:'';
$periodeAkutansi=$_SESSION['org']['period']['tahun']."-".$_SESSION['org']['period']['bulan'];
$optKary=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
//$arrsstk=getEnum($dbname,'sdm_5lembur','tipelembur');
foreach($arrsstk as $kei=>$fal)
{
	//print_r($kei);exit();
	$optTipelembur.="<option value='".$kei."'>".ucfirst($fal)."</option>";
} 

$tpLembur=isset($_POST['tpLembur'])? $_POST['tpLembur']:'';
$basisJam=isset($_POST['basisJam'])? $_POST['basisJam']:'';
        switch($proses)
        {
                case'cekData':
                //echo"warning:masuk";
                #cek tutup buku
//                $scek="select * from ".$dbname.".setup_periodeakuntansi where kodeorg='".$_SESSION['empl']['lokasitugas']."' and tanggalmulai<='".$tgl."' and tanggalsampai>='".$tgl."'";
//                $qcek=  mysql_query($scek) or die(mysql_error($conn));
//                $rcek=  mysql_fetch_assoc($qcek);
//                $rRowcek=mysql_num_rows($qcek);
//                if($rRowcek>0){
//                    if($rcek['tutupbuku']==1){
//                        exit("error:  This period ".$rcek['periode']." already closed");
//                    }
//                }
                $_SESSION['temp']['OrgKd2']=$kdOrg;
                $sCek="select kodeorg,tanggal from ".$dbname.".sdm_lemburht where tanggal='".$tgl."' and kodeorg='".$kdOrg."'"; //echo "warning".$sCek;nospb
                $qCek=mysql_query($sCek) or die(mysql_error());
                $rCek=mysql_fetch_row($qCek);
                if($rCek<1)
                {
                        $sIns="insert into ".$dbname.".sdm_lemburht (`kodeorg`,`tanggal`,`updateby`,`updatetime`) 
                               values ('".$kdOrg."','".$tgl."','".$_SESSION['standard']['userid']."','".date("Y-m-d H:i:s")."')"; //echo"warning:".$sIns;
                        if(mysql_query($sIns))
                        {
                                if(($tpLmbr!='')&&($Jam!=''))
                                {
                                        $sDetIns="insert into ".$dbname.".sdm_lemburdt 
                                        (`kodeorg`,`tanggal`,`karyawanid`,`tipelembur`,`jamaktual`,`uangmakan`,`uangtransport`,`uangkelebihanjam`) values ('".$kdOrg."','".$tgl."','".$krywnId."','".$tpLmbr."','".$Jam."','".$ungMkn."','".$ungTrans."','".$ungLbhjm."')";
                                        //echo"warning:".$sDetIns;exit();

                                        if(mysql_query($sDetIns))
                                        echo"";
                                        else
                                        echo "DB Error : ".mysql_error($conn);
                                }
                                else
                                {
                                       if($_SESSION['language']=='ID'){ 
                                       echo"warning: Masukkan tipe lembur dan basis jam";
                                       }else{
                                        echo"warning: Please choose overtime type and actual hours";
                                       }
                                        exit();
                                }
                        }
                        else
                        {
                                echo "DB Error : ".mysql_error($conn);
                        }
                }
                else
                {
                        if(($tpLmbr!='')&&($Jam!=''))
                        {

                                $sDetIns="insert into ".$dbname.".sdm_lemburdt 
                                (`kodeorg`,`tanggal`,`karyawanid`,`tipelembur`,`jamaktual`,`uangmakan`,`uangtransport`,`uangkelebihanjam`) values ('".$kdOrg."','".$tgl."','".$krywnId."','".$tpLmbr."','".$Jam."','".$ungMkn."','".$ungTrans."','".$ungLbhjm."')";
                        //echo"warning:".$sDetIns;exit();

                        if(mysql_query($sDetIns))
                        echo"";
                        else
                        echo "DB Error : ".mysql_error($conn);
                        }
                        else
                        {
                                       if($_SESSION['language']=='ID'){ 
                                       echo"warning: Masukkan tipe lembur dan basis jam";
                                       }else{
                                        echo"warning: Please choose overtime type and actual hours";
                                       }
                                exit();
                        }
                }
                break;
                case'loadNewData':
                echo"<table cellspacing='1' border='0' class='sortable'>
                <thead>
                <tr class=rowheader>
                <td>No.</td>
                <td>". $_SESSION['lang']['kodeorg'] ."</td>
                <td>". $_SESSION['lang']['namaorganisasi'] ."</td>
                <td>". $_SESSION['lang']['tanggal'] ."</td>
                <td>". $_SESSION['lang']['updateby'] ."</td>
                <td>Action</td>
                </tr>
                </thead><tbody>";
                $limit=20;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;

                $ql2="select count(*) as jmlhrow from ".$dbname.".sdm_lemburht where substring(kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."' order by `tanggal` desc";// echo $ql2;

                $query2=mysql_query($ql2) or die(mysql_error());
                while($jsl=mysql_fetch_object($query2)){
                $jlhbrs= $jsl->jmlhrow;
                }
				
				
				
				
				
                #cek data jabatan
                $scek="select distinct jabatan from ".$dbname.".setup_posting where kodeaplikasi='absensi'";
                $qcek=mysql_query($scek) or die(mysql_error($conn));
                $rcek=mysql_fetch_assoc($qcek);
                
                $slvhc="select * from ".$dbname.".sdm_lemburht where substring(kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."' order by `tanggal` desc limit ".$offset.",".$limit."";
                $qlvhc=mysql_query($slvhc) or die(mysql_error());
                $user_online=$_SESSION['standard']['userid'];
				$no=0;
                while($rlvhc=mysql_fetch_assoc($qlvhc)){
                        $thnPeriod=substr($rlvhc['tanggal'],0,7);
                        
                        $sOrg="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$rlvhc['kodeorg']."'";
                        $qOrg=mysql_query($sOrg) or die(mysql_error());
                        $rOrg=mysql_fetch_assoc($qOrg);
                        $sGp="select DISTINCT sudahproses from ".$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and periode='".$thnPeriod."' and tanggalmulai<='".$rlvhc['tanggal']."' and tglcutoff>='".$rlvhc['tanggal']."'";
                        $qGp=mysql_query($sGp) or die(mysql_error());
                        $rGp=mysql_fetch_assoc($qGp);

                        $no+=1;
                        echo"
                        <tr class=rowcontent>
                        <td>".$no."</td>
                        <td>".$rlvhc['kodeorg']."</td>
                        <td>".$rOrg['namaorganisasi']."</td>
                        <td>".tanggalnormal($rlvhc['tanggal'])."</td>
                        <td>".$optKary[$rlvhc['updateby']]."</td>
                        <td>";
						
						
						$iCek="select distinct periodegaji from ".$dbname.".sdm_gaji where substr(kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."' and periodegaji='".$rlvhc['periode']."'";
						//echo $iCek;
						$nCek=mysql_query($iCek) or die (mysql_error($conn));
						$dCek=mysql_num_rows($nCek);
//						if($dCek>0)
//						{	
//						}
//						else
//						{//if(($thnPeriod==$periodeAkutansi)||($rGp['sudahproses']==0)){
							if($rGp['sudahproses']==0){
								$sLok="select distinct * from ".$dbname.".setup_temp_lokasitugas where karyawanid='".$_SESSION['standard']['userid']."'";
								$qLok=  mysql_query($sLok) or die(mysql_error($conn));
								$rLok=  mysql_fetch_assoc($qLok);
								$rowLok=  mysql_num_rows($qLok);
								if($rowLok>0){
										if($rLok['kodeorg']==substr($rlvhc['kodeorg'],0,4)){
												if(($_SESSION['empl']['kodejabatan']==$rcek['jabatan'])||($_SESSION['standard']['userid']==$rlvhc['updateby'])||($_SESSION['empl']['bagian']=='IT')){
													echo"
													<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\">
													<!--<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\" >-->";
												}
										}
								}else{
									 if(($_SESSION['empl']['kodejabatan']==$rcek['jabatan'])||($_SESSION['standard']['userid']==$rlvhc['updateby'])||($_SESSION['empl']['bagian']=='IT')){
										echo"
										<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\">
										<!--<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\" >-->";
									}
								}
							}
//						}
                                    echo"<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_lemburht','".$rlvhc['kodeorg'].",".tanggalnormal($rlvhc['tanggal'])."','','sdm_slave_lemburPdf',event)\">";

                        
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
                $sCek="select posting from ".$dbname.".sdm_absensiht where tanggal='".$tgl."' and kodeorg='".$kdOrg."'"; //echo "warning".$sCek;;
                $qCek=mysql_query($sCek) or die(mysql_error());
                $rCek=mysql_fetch_assoc($qCek);
                if($rCek['posting']=='1')
                {
                        echo"warning: This data has been confirmed, can not continue";
                        exit();
                }
                $sDel="delete from ".$dbname.".sdm_lemburht where tanggal='".$tgl."' and kodeorg='".$kdOrg."'";// echo "___".$sDel;exit();
                if(mysql_query($sDel))
                {
                        $sDelDetail="delete from ".$dbname.".sdm_lemburdt where tanggal='".$tgl."' and kodeorg='".$kdOrg."'";
                        if(mysql_query($sDelDetail))
                        echo"";
                        else
                        echo "DB Error : ".mysql_error($conn);
                }
                else
                {echo "DB Error : ".mysql_error($conn);}

                break;
                case'cekHeader':
                $thn=substr($tgl,0,4);
                $bln=substr($tgl,4,2);
                $periode=$thn."-".$bln;

                $sCek="select kodeorg,tanggal from ".$dbname.".sdm_lemburht where tanggal='".$tgl."' and kodeorg='".$kdOrg."'"; //echo "warning".$sCek;nospb
                $qCek=mysql_query($sCek) or die(mysql_error());
                $rCek=mysql_fetch_row($qCek);
                if($rCek>0)
                {
                        echo"warning: Data already exist";
                        exit();
                }

//                $str="select * from ".$dbname.".setup_periodeakuntansi where periode='".$periode."' and
//                kodeorg='".$_SESSION['empl']['lokasitugas']."' and tutupbuku=1";
//                //exit("Error".$str) ;
//                $res=mysql_query($str);
//                if(mysql_num_rows($res)>0)
//                $aktif=true;
//                else
//                $aktif=false;
//                if($aktif==true)
//                {
//                exit("Error: Accounting period has been closed to this date");
//                }
                break;
                case'cariAbsn':
                if(($tgl=='')||($kdOrg=='')){
                        echo"Silahkan pilih lokasi tugas dan tanggal.";
                        exit();
                }
                echo"
                <div style='overflow:auto;height:400px'>
                <table cellspacing='1' border='0' class='sortable'>
<thead>
<tr class=rowheader>
<td>No.</td>
<td>". $_SESSION['lang']['kodeorg'] ."</td>
<td>". $_SESSION['lang']['namaorganisasi'] ."</td>
<td>". $_SESSION['lang']['tanggal'] ."</td>
<td>". $_SESSION['lang']['updateby'] ."</td>
<td>Action</td>
</tr>
</thead><tbody>";
$limit=20;
$page=0;
if(isset($_POST['page']))
{
$page=$_POST['page'];
if($page<0)
$page=0;
}
$offset=$page*$limit;
                if(($tgl!='')&&($kdOrg!=''))
                {
                        $where=" kodeorg = '".$kdOrg."' and tanggal='".$tgl."'";
                }
                elseif($kdOrg!='')
                {
                        $where=" kodeorg ='".$kdOrg."'";
                }
                elseif($tgl!='')
                {
                        $where="kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' and tanggal='".$tgl."'";	
                }
                elseif(($tgl=='')&&($kdOrg==''))
                {
                        echo"warning: Please insert data";
                        exit();
                }
                //paging data
                $ql2="select count(*) as jmlhrow from ".$dbname.".sdm_lemburht where ".$where." order by `tanggal`";// echo $ql2;

                $query2=mysql_query($ql2) or die(mysql_error());
                while($jsl=mysql_fetch_object($query2)){
                $jlhbrs= $jsl->jmlhrow;
                }
                #cek data jabatan
                $scek="select distinct jabatan from ".$dbname.".setup_posting where kodeaplikasi='absensi'";
                $qcek=mysql_query($scek) or die(mysql_error($conn));
                $rcek=mysql_fetch_assoc($qcek);
                //query data
                $slvhc="select * from ".$dbname.".sdm_lemburht where ".$where." order by `tanggal` limit ".$offset.",".$limit."";// echo "warning:".$slvhc;exit();
                $qlvhc=mysql_query($slvhc) or die(mysql_error());
                $user_online=$_SESSION['standard']['userid'];
                while($rlvhc=mysql_fetch_assoc($qlvhc)){
                        $thnPeriod=substr($rlvhc['tanggal'],0,7);
                        $sOrg="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$rlvhc['kodeorg']."'";
                        $qOrg=mysql_query($sOrg) or die(mysql_error());
                        $rOrg=mysql_fetch_assoc($qOrg);
                        $sGp="select DISTINCT sudahproses from ".$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and periode='".$thnPeriod."' and tanggalmulai<='".$rlvhc['tanggal']."' and tglcutoff>='".$rlvhc['tanggal']."'";
                        $qGp=mysql_query($sGp) or die(mysql_error());
                        $rGp=mysql_fetch_assoc($qGp);
                $no+=1;
                echo"
                <tr class=rowcontent>
                <td>".$no."</td>
                <td>".$rlvhc['kodeorg']."</td>
                <td>".$rOrg['namaorganisasi']."</td>
                <td>".tanggalnormal($rlvhc['tanggal'])."</td>
                <td>".$optKary[$rlvhc['updateby']]."</td>
                <td>";
				
				
				$iCek="select distinct periodegaji from ".$dbname.".sdm_gaji where substr(kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."' and periodegaji='".$rlvhc['periode']."'";
				//echo $iCek;
				$nCek=mysql_query($iCek) or die (mysql_error($conn));
				$dCek=mysql_num_rows($nCek);
//				if($dCek>0)
//				{	
//				}
//				else
//				{
						
					if($rGp['sudahproses']==0){//if(($thnPeriod==$periodeAkutansi)||($rGp['sudahproses']==0)){
					$sLok="select distinct * from ".$dbname.".setup_temp_lokasitugas where karyawanid='".$_SESSION['standard']['userid']."'";
					$qLok=  mysql_query($sLok) or die(mysql_error($conn));
					$rLok=  mysql_fetch_assoc($qLok);
					$rowLok=  mysql_num_rows($qLok);
					if($rowLok>0){
							if($rLok['kodeorg']==substr($rlvhc['kodeorg'],0,4)){
								if(($_SESSION['empl']['kodejabatan']==$rcek['jabatan'])||($_SESSION['standard']['userid']==$rlvhc['updateby'])||($_SESSION['empl']['bagian']=='IT')){
								echo"
								<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\">
								<!--<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\" >-->";
								}
							}
					}else{
							if(($_SESSION['empl']['kodejabatan']==$rcek['jabatan'])||($_SESSION['standard']['userid']==$rlvhc['updateby'])||($_SESSION['empl']['bagian']=='IT')){
								echo"
								<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\">
								<!--<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\" >-->";
							}
						}
					}
//				}
				echo"<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_lemburht','".$rlvhc['kodeorg'].",".tanggalnormal($rlvhc['tanggal'])."','','sdm_slave_lemburPdf',event)\">";

                
                echo"</td>
                </tr>
                ";
                }
                echo"
                <tr class=rowheader><td colspan=5 align=center>
                ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
                <button class=mybutton onclick=cariData(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                <button class=mybutton onclick=cariData(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                </td>
                </tr>";
                echo"</tbody></table></div>";

                break;
                case'updateDetail':
                #cek tutup buku
                $scek="select * from ".$dbname.".setup_periodeakuntansi where kodeorg='".$_SESSION['empl']['lokasitugas']."' and tanggalmulai<='".$tgl."' and tanggalsampai>='".$tgl."'";
                $qcek=  mysql_query($scek) or die(mysql_error($conn));
                $rcek=  mysql_fetch_assoc($qcek);
                $rRowcek=mysql_num_rows($qcek);
                if($rRowcek>0){
                    if($rcek['tutupbuku']==1){
                        exit("error:  This period ".$rcek['periode']." already closed");
                    }
                }
                if(($tpLmbr!='')&&($Jam!=''))
                {
                $sUp="update ".$dbname.".sdm_lemburdt set tipelembur='".$tpLmbr."',jamaktual='".$Jam."',uangmakan='".$ungMkn."',uangtransport='".$ungTrans."',uangkelebihanjam='".$ungLbhjm."' where kodeorg='".$kdOrg."' and tanggal='".$tgl."' and karyawanid='".$krywnId."'";
                if(mysql_query($sUp))
                        echo"";
                        else
                        echo "DB Error : ".mysql_error($conn);
                }
                else
                        {
                                       if($_SESSION['language']=='ID'){ 
                                       echo"warning: Masukkan tipe lembur dan basis jam";
                                       }else{
                                        echo"warning: Please choose overtime type and actual hours";
                                       }
                                exit();
                        }
                break;
                case'delDetail':
                        $sDel="delete from ".$dbname.".sdm_lemburdt where kodeorg='".$kdOrg."' and tanggal='".$tgl."' and karyawanid='".$krywnId."'";
                if(mysql_query($sDel))
                        echo"";
                        else
                        echo "DB Error : ".mysql_error($conn);
                break;
                case'createTable':
                     //$optOrgTem=makeOption($dbname, 'setup_temp_lokasitugas', 'karyawanid,kodeorg');
                if(strlen($kdOrg)>4)
                {
                        $where=" subbagian='".$kdOrg."'  and (tanggalkeluar>".$tgl." or tanggalkeluar='0000-00-00')";
                        $where .= " and left(kodegolongan,1)<=3";
                        $sKry="select namakaryawan,nik,karyawanid from ".$dbname.".datakaryawan where ".$where."";
                }
                else
                {
                        $where=" lokasitugas='".$kdOrg."' and (subbagian IS NULL or subbagian='0' or subbagian='') and (tanggalkeluar>".$tgl." or tanggalkeluar='0000-00-00')";
                        $where .= " and left(kodegolongan,1)<=3";
                        $sKry="select namakaryawan,nik,karyawanid from ".$dbname.".datakaryawan 
                               where ".$where."
                               UNION 
                               select namakaryawan,nik,b.karyawanid as karyawanid from ".$dbname.".setup_temp_lokasitugas a 
                               left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
                               where kodeorg='".substr($kdOrg,0,4)."'";
                }
                
                $optKry.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                $qKry=mysql_query($sKry) or die(mysql_error($conn));
                while($rKry=mysql_fetch_assoc($qKry)){
                    if(strlen($rKry['karyawanid'])<10){
                            $rKry['karyawanid']=  addZero($rKry['karyawanid'], 10);
                            //exit("error:".$rKry['karyawanid']);
                        }
                  $scek="select * from ".$dbname.".setup_temp_lokasitugas where karyawanid='".$rKry['karyawanid']."'";
                  $qcek=mysql_query($scek) or die(mysql_error($conn));
                  $rcek=mysql_num_rows($qcek);
                    if($rcek>0){
                        
                        $rcekd=mysql_fetch_assoc($qcek);
                        if($rcekd['kodeorg']==substr($kdOrg,0,4)){
                            $optKry.="<option value='".$rKry['karyawanid']."'>".$rKry['nik']." - ".$rKry['namakaryawan']."</option>";
                        }
                    }else{
                        $optKry.="<option value='".$rKry['karyawanid']."'>".$rKry['nik']." - ".$rKry['namakaryawan']."</option>";
                    }
                    
                }

                $table="<table id='ppDetailTable' cellspacing='1' border='0' class='sortable'>
                <thead>
                <tr class=rowheader>
                <td>".$_SESSION['lang']['namakaryawan']."</td>
                <td>".$_SESSION['lang']['tipelembur']."</td>
                <td>".$_SESSION['lang']['jamaktual']."</td>
                <td style='display:none'>".$_SESSION['lang']['uangkelebihanjam']."</td>
                <!-- hide permintaan analisa
                <td>".$_SESSION['lang']['penggantiantransport']."</td>
                <td>".$_SESSION['lang']['uangmakan']."</td>-->
                <td>Action</td>
                </tr></thead>
                <tbody id='detailBody'>";

                $table.="<tr class=rowcontent><td><select id=krywnId name=krywnId style='width:200px'>".$optKry."</select>
                         <img class='zImgBtn' style='position:relative;top:5px' src='images/onebit_02.png' onclick=\"getKary('".$_SESSION['lang']['find']." ".$_SESSION['lang']['namakaryawan']."/".$_SESSION['lang']['nik']."','1',event);\"  /></td>
                <td><select id=tpLmbr name=tpLmbr style='width:100px' onchange='getLembur(0,0)'>".$optTipelembur."</select></td>
                <td><select id=jam name=jam style='width:100px' onchange='getUangLem()'><option value=''>".$_SESSION['lang']['pilihdata']."</option></select></td>
                <td style='display:none'><input type='text' class='myinputtextnumber' id='uang_lbhjm' name='uang_lbhjm' style='width:100px' onkeypress='return angka_doang(event)' value=0 />
                <input type='hidden' class='myinputtextnumber' id='uang_trnsprt' name='uang_trnsprt' style='width:100px' onkeypress='return angka_doang(event)' value=0  />
                <input type='hidden' class='myinputtextnumber' id='uang_mkn' name='uang_mkn' style='width:100px' onkeypress='return angka_doang(event)' value=0 />
                </td>
                <!-- hide sesuai dengan analisa, jika ingin mengaktifkan buang comment html dan hapus object yang tipenya hidden
                <td><input type='text' class='myinputtextnumber' id='uang_trnsprt' name='uang_trnsprt' style='width:100px' onkeypress='return angka_doang(event)' value=0  /></td>
                <td><input type='text' class='myinputtextnumber' id='uang_mkn' name='uang_mkn' style='width:100px' onkeypress='return angka_doang(event)' value=0 /></td>-->
                <td><img id='detail_add' title='Simpan' class=zImgBtn onclick=\"addDetail()\" src='images/save.png'/></td>
                </tr>
                ";
                $table.="</tbody></table>";
                echo $table;
                break;
                case'getBasis':
                $dtOrg=$_SESSION['empl']['lokasitugas'];
                $optBasis="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                $sBasis="select jamaktual from ".$dbname.".sdm_5lembur where kodeorg='".$dtOrg."' and tipelembur='".$tpLembur."'";
                $qBasis=mysql_query($sBasis) or die(mysql_error($conn));
                while($rBasis=mysql_fetch_assoc($qBasis))
                {
                        $optBasis.="<option value=".$rBasis['jamaktual']." ".($rBasis['jamaktual']==$basisJam?'selected':'').">".$rBasis['jamaktual']."</option>";
                }
                echo $optBasis;
                break;
				
                case'getUang':
                $optTipe=makeOption($dbname,'datakaryawan','karyawanid,tipekaryawan');
                $uangLembur='';
                $kodeOrg=substr($kodeOrg,0,4);
                $sPengali="select jamlembur from ".$dbname.".sdm_5lembur  where kodeorg='".$kodeOrg."' and tipelembur='".$tpLmbr."' and jamaktual='".$basisJam."' ";
                $qPengali=mysql_query($sPengali) or die(mysql_error());
                $rPengali=mysql_fetch_assoc($qPengali);

                $sGt="select sum(jumlah) as gapTun from ".$dbname.".sdm_5gajipokok where karyawanid='".$krywnId."' and idkomponen=1 and tahun=".$_POST['tahun'];
				
                $qGt=mysql_query($sGt) or die(mysql_error($conn));
                $rGt=mysql_fetch_assoc($qGt);
                 if($_SESSION['empl']['regional']=='SULAWESI'){
                    if($optTipe[$krywnId]>3){//indra update bug
                       //$uangLembur=0.15*(($rGt['gapTun']*$optJamLembur[$rData['jamaktual']])/173);
                       $uangLembur=0.15*$rPengali['jamlembur']*($rGt['gapTun']/25);//(($rGt['gapTun']*)/173);
                    }else{
                       //$uangLembur=($rGt['gapTun']*$optJamLembur[$rData['jamaktual']])/173;
                       $uangLembur=($rGt['gapTun'])*($rPengali['jamlembur']/173);
                    }
                }else{
                    //exit("Error:$krywnId");
                    if($optTipe[$krywnId]>3){//indra update bug
                       //$uangLembur=0.15*(($rGt['gapTun']*$optJamLembur[$rData['jamaktual']])/173);
                       $uangLembur=$rPengali['jamlembur']*(3/20*($rGt['gapTun']/25));//(($rGt['gapTun']*)/173);
                    }else{
                       //$uangLembur=($rGt['gapTun']*$optJamLembur[$rData['jamaktual']])/173;
                       $uangLembur=($rGt['gapTun'])*($rPengali['jamlembur']/173);
                    }
                    
                    //$uangLembur=($rGt['gapTun'])*($rPengali['jamlembur']/173);
                }
//                if($_SESSION['empl']['regional']=='SULAWESI'){
//                    if($optTipe[$krywnId]>3){
//                        $uangLembur=0.15*(($rGt['gapTun']*$rPengali['jamlembur'])/173);
//                    }else{
//                        $uangLembur=($rGt['gapTun']*$rPengali['jamlembur'])/173;
//                    }
//                }else{
//                    $uangLembur=($rGt['gapTun']*$rPengali['jamlembur'])/173;
//                }
                
				//exit("Error:$uangLembur");
                echo intval($uangLembur);
                break;
                default:
                break;
        }
?>