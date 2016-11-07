<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$param=$_POST;
$optNmKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');
        switch($param['proses'])
        {
                case'getKary':
                    $optKary="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                    $sunit="select distinct nik,karyawanid,namakaryawan from ".$dbname.".datakaryawan 
                            where lokasitugas='".$param['kodeOrg']."' and tanggalkeluar='0000-00-00' 
                            order by namakaryawan asc";
                    $qunit=mysql_query($sunit) or die(mysql_error($conn));
                    while($runit=mysql_fetch_assoc($qunit)){
                        $optKary.="<option value='".$runit['karyawanid']."' ".($runit['karyawanid']==$param['karyId']?"selected":"").">".$runit['nik']."-".$runit['namakaryawan']."</option>";
                    }
                    echo $optKary;
                break;
                case'getForm':
                    $dtForm.="<table>";
                    $dtForm.="<tr><td colspan=6>".$_SESSION['lang']['keluarga']." ".$optNmKar[$param['karyawanId']]." <br />
                              <button class=mybutton onclick=tutupForm()>".$_SESSION['lang']['tutup']."</button>
                              </td></tr></table>";
                    $dtForm.="<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>";
                    $dtForm.="<tr class=rowheader>";
                    $dtForm.="<td>".$_SESSION['lang']['nama']."</td>";
                    $dtForm.="<td>".$_SESSION['lang']['hubungan']."</td>";
                    $dtForm.="<td>".$_SESSION['lang']['umur']."</td>";
                    $dtForm.="<td>".$_SESSION['lang']['jeniskelamin']."</td>";
                    $dtForm.="<td>".$_SESSION['lang']['medicalId']."</td>";
                    $dtForm.="<td>".$_SESSION['lang']['action']."</td></tr></thead><tbody>";
                    $sdata="select distinct a.*,ROUND(DATEDIFF(NOW(),tanggallahir)/365,2) as umur from ".$dbname.".sdm_karyawankeluarga a where karyawanid='".$param['karyawanId']."'";
                    $qdata=mysql_query($sdata) or die(mysql_error($conn));
                    while($rdata=  mysql_fetch_assoc($qdata)){
                        $no+=1;
                        $dtForm.="<tr class=rowcontent>";
                        $dtForm.="<td>".$rdata['nama']."</td>";
                        $dtForm.="<td>".$rdata['hubungankeluarga']."</td>";
                        $dtForm.="<td>".$rdata['umur']."</td>";
                        $dtForm.="<td>".$rdata['jeniskelamin']."</td>";
                        $dtForm.="<td><input type=text class=myinputtext id=medicalId_".$no." onkeypress='return tanpa_kutip(event)' value='".$rdata['idmedicalklrg']."' style=width:150px /></td>";
                        $dtForm.="<td><button class=mybutton onclick=saveDtKlrg('".$rdata['nomor']."','".$rdata['karyawanid']."','".$no."') >".$_SESSION['lang']['save']."</button></td>";
                    }
                     $dtForm.="</tbody></table>";
                     echo $dtForm;
                break;
                case'instData':
                    if($param['medicalId']==''){
                        exit("error: ".$_SESSION['lang']['medicalid']." can't empty");
                    }
                    $supdate="update ".$dbname.".datakaryawan set idmedical='".$param['medicalId']."'  
                              where karyawanid='".$param['karyId']."' ";
                    if(!mysql_query($supdate)){
                        exit("error: db gak berhasil__".$supdate."___".mysql_error($conn));
                    }
                break;
                case'smpnData':
                    if($param['medicalId']==''){
                        exit("error: ".$_SESSION['lang']['medicalid']." can't empty");
                    }
                    $supdate="update ".$dbname.".sdm_karyawankeluarga set idmedicalklrg='".$param['medicalId']."'  
                              where karyawanid='".$param['karyawanId']."' and nomor='".$param['nourut']."'";
                    if(!mysql_query($supdate)){
                        exit("error: db gak berhasil__".$supdate."___".mysql_error($conn));
                    }
                    
                break;
                case'loadData':
                    if($param['nikcari']!=''){
                        $whr.=" and nik like '".$param['nikcari']."%'";
                    }
                    if($param['namakary']!=''){
                        $whr.=" and namakaryawan like '".$param['namakary']."%'";
                    }
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
		$ql2="select count(*) as jmlhrow from ".$dbname.".datakaryawan  
                      where idmedical!='' ".$whr." and lokasitugas in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
                      order by namakaryawan asc ";// echo $ql2;notran
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		$i="select * from ".$dbname.".datakaryawan 
                    where idmedical!='' ".$whr." and lokasitugas in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
                     order by namakaryawan asc  limit ".$offset.",".$limit."";
                $qData=mysql_query($i) or die(mysql_error($conn));
                while($rData=  mysql_fetch_assoc($qData)){
                    $lstData.="<tr class=rowcontent>";
                    $lstData.="<td>".$rData['nik']."</td>";
                    $lstData.="<td>".$rData['namakaryawan']."</td>";
                    $lstData.="<td>".$rData['idmedical']."</td>
                    <td>
                    <img src='images/addplus.png' class=resicon title='add ".$_SESSION['lang']['keluarga']."' onclick=addFamily('".$rData['karyawanid']."') />
                    <img src='images/application/application_edit.png' class=resicon title='edit Data' onclick=fillField('".$rData['lokasitugas']."','".$rData['idmedical']."','".$rData['karyawanid']."') />
                    </td>";
                    $lstData.="</tr>";
                }
                $tab.="<tr class=rowheader><td colspan=5 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".$jlhbrs."<br />
		<button class=mybutton onclick=loadData(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=loadData(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
                echo $lstData."####".$tab;
                break;
        }
?>
