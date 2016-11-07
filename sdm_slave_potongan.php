<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
$param=$_POST;
$optLokasiTugas=  makeOption($dbname, 'datakaryawan', 'karyawanid,lokasitugas');
$periodeAkutansi=$_SESSION['org']['period']['tahun']."-".$_SESSION['org']['period']['bulan'];
$whrPot="name like 'pot.%' or name like 'potongan%'";
//$whrPrdData=" periodegaji='".$param['periode']."' and tipepotongan='".$param['tipePot']."'";

//$whrPrdData=" periodegaji='".$param['periode']."' and tipepotongan='".$param['tipePot']."' and kodeorg='".$param['kdOrg']."'";
//$whrPrdData="updateby='".$_SESSION['standard']['userid']."' and periodegaji='".$param['periode']."' and tipepotongan='".$param['tipePot']."'";




if(($_SESSION['empl']['bagian']=='FIN')||($_SESSION['empl']['bagian']=='IT')){
$whrPrdData="kodeorg='".$param['kdOrg']."' and periodegaji='".$param['periode']."' and tipepotongan='".$param['tipePot']."'  ";
}else{
    $whrPrdData="kodeorg='".$param['kdOrg']."' and updateby='".$_SESSION['standard']['userid']."' and periodegaji='".$param['periode']."' and tipepotongan='".$param['tipePot']."' ";
}



if(($_SESSION['empl']['bagian']=='FIN')||($_SESSION['empl']['bagian']=='IT')){
    $whrPrdDataDetail="periodegaji='".$param['periode']."' and tipepotongan='".$param['tipePot']."'  ";
}else{
    $whrPrdDataDetail="updateby='".$_SESSION['standard']['userid']."' and periodegaji='".$param['periode']."' and tipepotongan='".$param['tipePot']."' ";
}


$optNmPotongan=makeOption($dbname, 'sdm_ho_component', 'id,name',$whrPot);
                
$optNmKar=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optNikKar=makeOption($dbname, 'datakaryawan', 'karyawanid,nik');
$tgl=date("Y-m-d");
$optTipe=  makeOption($dbname, 'organisasi', 'kodeorganisasi,tipe');

        switch($param['proses']){
                case'loadNewData':
//param='proses=loadNewData'+'&kdOrgCr='+kdorg;
//param+='&periodecr='+tgl+'&tipePotCr='+tppot;
                echo"<table cellspacing='1' border='0' class='sortable'>
                     <thead>
                     <tr class=rowheader>
                     <td>No.</td>
                     <td>". $_SESSION['lang']['kodeorg'] ."</td>
                     <td>". $_SESSION['lang']['namaorganisasi'] ."</td>
                     <td>". $_SESSION['lang']['periodegaji'] ."</td>
                     <td>". $_SESSION['lang']['potongan'] ."</td>
                     <td>Action</td>
                     </tr>
                     </thead><tbody>";
                        if($param['periodecr']!=''){
                            $whrCr.=" and periodegaji like '%".$param['periodecr']."%'";
                        }
                        if($param['tipePotCr']!=''){
                            $whrCr.=" and tipepotongan= '".$param['tipePotCr']."'";
                        }
                        if($param['kdOrgCr']!=''){
                            $whrCr.=" and kodeorg= '".$param['kdOrgCr']."'";
                        }
                        $limit=20;
                        $page=0;
                        if(isset($_POST['page'])){
                        $page=$_POST['page'];
                        if($page<0)
                        $page=0;
                        }
                        $offset=$page*$limit;
//                        if($_SESSION['empl']['tipelokasitugas']=='KANWIL'){
//                            $ql2="select count(*) as jmlhrow from ".$dbname.".sdm_potonganht where 
//                                  substring(kodeorg,1,4) in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') ".$whrCr." order by `periodegaji` desc";// echo $ql2;
//                            $slvhc="select * from ".$dbname.".sdm_potonganht 
//                                    where substring(kodeorg,1,4) in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')  ".$whrCr."
//                                    order by `periodegaji` desc limit ".$offset.",".$limit."";
//                        }else{
                            $ql2="select count(*) as jmlhrow from ".$dbname.".sdm_potonganht 
                                  where substring(kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."'  and tipepotongan in (select distinct id from ".$dbname.".sdm_ho_component where name like 'pot.%' or name like 'potongan%' and pinjamanid=0 order by name asc)  ".$whrCr." order by `periodegaji` desc";// echo $ql2;
                            $slvhc="select * from ".$dbname.".sdm_potonganht 
                                    where substring(kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."' and tipepotongan in (select distinct id from ".$dbname.".sdm_ho_component where name like 'pot.%' or name like 'potongan%' and pinjamanid=0 order by name asc)  ".$whrCr."
                                    order by `periodegaji` desc limit ".$offset.",".$limit."";
                        //}
                        //echo $slvhc;
                        $query2=mysql_query($ql2) or die(mysql_error());
                        while($jsl=mysql_fetch_object($query2)){
                        $jlhbrs= $jsl->jmlhrow;
                        }
             // print_r($optNmPotongan);          
                        $qlvhc=mysql_query($slvhc) or die(mysql_error());
                        
                        $user_online=$_SESSION['standard']['userid'];
                                        $no=0;
                        while($rlvhc=mysql_fetch_assoc($qlvhc))
                        {
                                $thnPeriod=substr($rlvhc['tanggal'],0,7);
                                $whrd="kodeorganisasi='".$rlvhc['kodeorg']."'";
                                $optNmOrg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi',$whrd);

                        $no+=1;
                        echo"
                        <tr class=rowcontent>
                        <td>".$no."</td>
                        <td>".$rlvhc['kodeorg']."</td>
                        <td>".$optNmOrg[$rlvhc['kodeorg']]."</td>
                        <td>".$rlvhc['periodegaji']."</td>
                        <td>".$optNmPotongan[$rlvhc['tipepotongan']]."</td>
                        <td>";
						
						$arr="##kdorg##per";	
						
						
						
//						$iCek="select distinct periodegaji from ".$dbname.".sdm_gaji where periodegaji='".$rlvhc['periodegaji']."' and kodeorg='".$rlvhc['kodeorg']."'";
//						//echo $iCek;
//						$nCek=mysql_query($iCek) or die (mysql_error($conn));
//						$dCek=mysql_num_rows($nCek);
//						if($dCek>0)
//						{	
//									echo"<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_potonganht','".$rlvhc['kodeorg'].",".$rlvhc['periodegaji'].",".$rlvhc['tipepotongan']."','','sdm_slave_potonganPdf',event)\">
//									 <img onclick=excel(event,'".$rlvhc['kodeorg']."','".$rlvhc['periodegaji']."','".$rlvhc['tipepotongan']."') src=images/excel.jpg class=resicon title='MS.Excel'>";
//						}
//						else
//						{
							$sGp="select DISTINCT sudahproses from ".$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and periode='".$rlvhc['periodegaji']."' ";
							$qGp=mysql_query($sGp) or die(mysql_error());
							$rGp=mysql_fetch_assoc($qGp);
						   // if($rlvhc['periodegaji']==$periodeAkutansi){//	if(($_SESSION['standard']['userid']==$rlvhc['updateby'])||(($_SESSION['empl']['tipelokasitugas']=='KANWIL')||($_SESSION['empl']['kodejabatan']==98))){
							if($rGp['sudahproses']==0){
								if($_SESSION['standard']['userid']==$rlvhc['updateby'] || $_SESSION['empl']['bagian']=='IT'|| $_SESSION['empl']['bagian']=='FIN'){
								echo"
									<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".$rlvhc['periodegaji']."','".$rlvhc['tipepotongan']."');\">";
//									<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['kodeorg']."','".$rlvhc['periodegaji']."','".$rlvhc['tipepotongan']."');\" >	
								echo"	<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_potonganht','".$rlvhc['kodeorg'].",".$rlvhc['periodegaji'].",".$rlvhc['tipepotongan']."','','sdm_slave_potonganPdf',event)\">
									 <img onclick=excel(event,'".$rlvhc['kodeorg']."','".$rlvhc['periodegaji']."','".$rlvhc['tipepotongan']."') src=images/excel.jpg class=resicon title='MS.Excel'>";
								}else{
								echo"<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_potonganht','".$rlvhc['kodeorg'].",".$rlvhc['periodegaji'].",".$rlvhc['tipepotongan']."','','sdm_slave_potonganPdf',event)\">
											 <img onclick=excel(event,'".$rlvhc['kodeorg']."','".$rlvhc['periodegaji']."','".$rlvhc['tipepotongan']."') src=images/excel.jpg class=resicon title='MS.Excel'>";

								}
							}
							else{
							 echo"<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_potonganht','".$rlvhc['kodeorg'].",".$rlvhc['periodegaji'].",".$rlvhc['tipepotongan']."','','sdm_slave_potonganPdf',event)\">";

							}
//						}
                        echo"</td>
                        </tr>
                        ";
                        }
                        echo"</tbody><tfoot>
                        <tr><td colspan=6 align=center>
                        ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
                        <button class=mybutton onclick=loadData(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                        <button class=mybutton onclick=loadData(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                        </td>
                        </tr>";
                        echo"</tfoot></table>";
                break;
                case'getPrd':
                    $optPrd.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>"; 
                    $sGet="select distinct periode from ".$dbname.".sdm_5periodegaji 
                           where kodeorg='".$param['kdOrg']."' and sudahproses=0 and jenisgaji='H' order by periode desc";
                    $qGet=mysql_query($sGet) or die(mysql_error($conn));
                    while($rGet=  mysql_fetch_assoc($qGet)){
                        $optPrd.="<option value=".$rGet['periode'].">".$rGet['periode']."</option>"; 
                    }
                    echo $optPrd;
                break;
                case'saveData':
                    if(($param['rupPot']==0)||($param['rupPot']=='')){
                        exit("error: ".$_SESSION['lang']['potongan']." can't empty");
                    }
                    if($param['krywnId']==''){
                        exit("error: ".$_SESSION['lang']['namakaryawan']." can't empty");
                    }
                    
                    $optData=makeOption($dbname, 'sdm_potonganht', 'periodegaji,tipepotongan', $whrPrdData);
                    $scek="select distinct * from ".$dbname.".sdm_potonganht where periodegaji='".$param['periode']."' "
                        . " and tipepotongan='".$param['tipePot']."' and kodeorg='".$param['kdOrg']."'";
                    $qcek=  mysql_query($scek) or die(mysql_error($conn));
                    $rcek=  mysql_num_rows($qcek);
                
                    $sInsHt="insert into ".$dbname.".sdm_potonganht (`kodeorg`,`periodegaji`,`tipepotongan`,`updateby`) values ";
                    $sDet="insert into ".$dbname.".sdm_potongandt (`kodeorg`,`periodegaji`,`keterangan`,`nik`,`jumlahpotongan`,`tipepotongan`,`updateby`) values";
                    if($rcek<1){
                        $sInsHt.="('".$param['kdOrg']."','".$param['periode']."','".$param['tipePot']."','".$_SESSION['standard']['userid']."')";
                        if(!mysql_query($sInsHt)){
                            exit("error: DB Error ".mysql_error($conn)."___".$sInsHt);
                        }else{
                            $sDet.="('".$optLokasiTugas[$param['krywnId']]."','".$param['periode']."','".$param['ketPot']."','".$param['krywnId']."','".$param['rupPot']."'
                                    ,'".$param['tipePot']."','".$_SESSION['standard']['userid']."')";
                            if(!mysql_query($sDet)){
                                exit("error: DB Error ".mysql_error($conn)."___".$sDet);
                            }
                        }
                     }else{
                            $sDet.="('".$optLokasiTugas[$param['krywnId']]."','".$param['periode']."','".$param['ketPot']."','".$param['krywnId']."','".$param['rupPot']."'
                                    ,'".$param['tipePot']."','".$_SESSION['standard']['userid']."')";
                            //exit("error".$sDet);
                            if(!mysql_query($sDet)){
                                exit("error: DB Error ".mysql_error($conn)."___".$sDet);
                            }
                     }
                break;
				
				
				
				
                case'updateDetail':
                    if(($param['rupPot']=='')||(intval($param['rupPot'])=='0')){
                        exit("error: ".$_SESSION['lang']['potongan']." can't empty");
                    }
                    $sUpd="update ".$dbname.".sdm_potongandt set";
                    $sUpd.=" jumlahpotongan='".$param['rupPot']."',keterangan='".$param['ketPot']."'";
                    $sUpd.=" where tipepotongan='".$param['tipePot']."' and nik='".$param['krywnId']."' 
                             and kodeorg='".$optLokasiTugas[$param['krywnId']]."' and periodegaji='".$param['periode']."'";
                    if(!mysql_query($sUpd)){
                        exit("error: db error".mysql_error($conn)."___".$sUpd);
                    }
                break;
                case'delData':
                
                $sDel="delete from ".$dbname.".sdm_potonganht where ".$whrPrdData."";// echo "___".$sDel;exit();
                if(mysql_query($sDel))
                {
                        $sDelDetail="delete from ".$dbname.".sdm_potongandt where ".$whrPrdData."";
                        if(mysql_query($sDelDetail))
                        echo"";
                        else
                        echo "DB Error : ".mysql_error($conn);
                }
                else
                {echo "DB Error : ".mysql_error($conn);}

                break;
              
                case'delDetail':
                $sDel="delete from ".$dbname.".sdm_potongandt where ".$whrPrdDataDetail." and nik='".$param['krywnId']."'";
                    //exit("error:".$sDel);
                if(mysql_query($sDel))
                        echo"";
                        else
                        echo "DB Error : ".mysql_error($conn);
                break;
                case'createTable':
				//$x=$param['kdOrg'];
				//exit("Error:$x");
                if($param['statUpdate']!=1){
                    #cek dah ada atau belum sm masih di dalam periode akutansi
                    $whrPrd="kodeorg='".$param['kdOrg']."' and periode='".$param['periode']."'";
                    $optPeriodeAkn=makeOption($dbname, 'setup_periodeakuntansi', 'periode,tutupbuku', $whrPrd);
                    $optData=makeOption($dbname, 'sdm_potonganht', 'periodegaji,tipepotongan', $whrPrdData);
                    if($optPeriodeAkn[$param['periode']]==1){
                        exit("Error: Accounting period has been closed");
                    }
					//$y=$optData;
					
					//print_r($y);
					
					//$x=$optData[$param['periode']];exit("Error:$whrPrdData");
                    if($optData[$param['periode']]!=''){
                        exit("error: This date and Organization Name already exist");
                    }
					
					
					
					
					
					
					
					
                }
               
                $where=" lokasitugas='".$param['kdOrg']."' and (tanggalkeluar='0000-00-00' or tanggalkeluar<'".$tgl."')";
                if($optTipe[$param['kdOrg']]=='KANWIL'){
                    $where=" lokasitugas in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')"
                         . " and (tanggalkeluar='0000-00-00' or tanggalkeluar<'".$tgl."')";
                }
                $where .= " and left(kodegolongan,1)<=3";
                $optKry.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                $sKry="select namakaryawan,nik,karyawanid,lokasitugas from ".$dbname.".datakaryawan where ".$where." order by namakaryawan asc";
                $qKry=mysql_query($sKry) or die(mysql_error($conn));
                while($rKry=mysql_fetch_assoc($qKry)){
                    $optKry.="<option value=".$rKry['karyawanid'].">".$rKry['nik']." - ".$rKry['namakaryawan']." [".$rKry['lokasitugas']."]</option>";
                }

                $table="<table id='ppDetailTable' cellspacing='1' border='0' class='sortable'>
                <thead>
                <tr class=rowheader>
                <td>".$_SESSION['lang']['namakaryawan']."</td>
                <td>".$_SESSION['lang']['potongan']."</td>
                <td>".$_SESSION['lang']['keterangan']."</td>
                <td>Action</td>
                </tr></thead>
                <tbody id='detailBody'>";
                $table.="<tr class=rowcontent>
                <td><select id=krywnId name=krywnId style='width:200px'>".$optKry."</select>
                <img class='zImgBtn' style='position:relative;top:5px' src='images/onebit_02.png' onclick=\"getKary('".$_SESSION['lang']['find']." ".$_SESSION['lang']['namakaryawan']."','1',event);\"  />
                </td>
                <td><input type=text class='myinputtextnumber' id=rpPot style=width:150px onkeypress='return angka_doang(event)' /></td>
                <td><input type=text class=myinputtext id=ketPot style=width:150px onkeypress='return tanpa_kutip(event)' /></td>
                <td align=center><img id='detail_add' title='Simpan' class=zImgBtn onclick=\"addDetail()\" src='images/save.png'/></td>
                </tr>
                ";
                $table.="</tbody></table>";
                echo $table;
                break;
                case'loadDetail':
                    
                    
                    
                if($_SESSION['empl']['tipelokasitugas']=='KANWIL'){
                    if($_SESSION['empl']['regional']=='SULAWESI'){
                        $sDet="select * from ".$dbname.".sdm_potongandt where periodegaji='".$param['periode']."' "
                           . "and kodeorg in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
                              and tipepotongan='".$param['tipePot']."' order by nik asc";// echo $str;exit();
                    } else {
                        $sDet="select * from ".$dbname.".sdm_potongandt where periodegaji='".$param['periode']."' "
                           . "and kodeorg in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
                              and tipepotongan='".$param['tipePot']."' and updateby='".$_SESSION['standard']['userid']."' order by nik asc";// echo $str;exit();
                    }
                }else{
                        $sDet="select * from ".$dbname.".sdm_potongandt where periodegaji='".$param['periode']."' "
                           . "and kodeorg='".$_SESSION['empl']['lokasitugas']."'
                              and tipepotongan='".$param['tipePot']."' and updateby='".$_SESSION['standard']['userid']."' order by nik asc";
                }
                    
        
        //$whrPrdDataDetail="periodegaji='".$param['periode']."' and tipepotongan='".$param['tipePot']."'  ";
                    
                    //$sDet="select * from ".$dbname.".sdm_potongandt 
                      //     where ".$whrPrdDataDetail." order by nik";
						   //echo $sDet;
						/*$sDet="select distinct * from ".$dbname.".sdm_potongandt 
                           where ".$whrPrdData." order by nik";*/   
						   
                
                    $qDet=mysql_query($sDet) or die(mysql_error($conn));
                    while($rDet=  mysql_fetch_assoc($qDet)){
                        $no+=1;
                        $tab.="<tr class=rowcontent>";
                        $tab.="<td>".$no."</td>";
                        $tab.="<td>".$optNikKar[$rDet['nik']]."</td>";
                        $tab.="<td>".$optNmKar[$rDet['nik']]."</td>";
                        $tab.="<td align=right>".number_format($rDet['jumlahpotongan'],0)."</td>";
                        $tab.="<td>".$rDet['keterangan']."</td>";
                        $tab.="<td>".$optNmKar[$rDet['updateby']]."</td>";
                        $tab.="<td>
                            <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editDetail('".$rDet['nik']."','".$rDet['jumlahpotongan']."','".$rDet['keterangan']."');\">
                            <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delDetail('".$rDet['kodeorg']."','".$rDet['periodegaji']."','".$rDet['nik']."','".$rDet['tipepotongan']."');\" >	</td>";
                        $tab.="</tr>";
                        $tot+=$rDet['jumlahpotongan'];
                    }
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td colspan=3>".$_SESSION['lang']['total']."</td>";
                    $tab.="<td align=right>".number_format($tot,0)."</td><td  colspan=2>&nbsp;</td></tr>";
                    echo $tab;
                break;
				
				
				
                case'getKary':
                    // param='unit='+lokTgs+'&tppot='+tppotongan+'&periode='+prd+'&nmkary='+txtnamabarang
                    $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>";
                    $tab.="<thead>";
                    $tab.="<tr><td>".$_SESSION['lang']['nik']."</td>";
                    $tab.="<td>".$_SESSION['lang']['namakaryawan']."</td>";
                    $tab.="<td>".$_SESSION['lang']['lokasitugas']."</td>";
                    $tab.="</tr></thead><tbody>";
                    $where=" lokasitugas='".$param['unit']."' and (tanggalkeluar='0000-00-00' or tanggalkeluar<'".$tgl."')  and tipekaryawan!=0 ";
                    if($optTipe[$param['unit']]=='KANWIL'){
                        $where=" lokasitugas in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')"
                             . " and (tanggalkeluar='0000-00-00' or tanggalkeluar<'".$tgl."') and tipekaryawan!=0 ";
                    }
                    if($param['nmkary']!=''){
                        $where.="and (namakaryawan like '%".$param['nmkary']."%' or nik like '%".$param['nmkary']."%')";
                    }
                    $sKry="select namakaryawan,nik,karyawanid,lokasitugas from ".$dbname.".datakaryawan where ".$where." order by namakaryawan asc";
                    //echo $sKry;
                    $qDt=mysql_query($sKry) or die(mysql_error($conn));
                    while($rDt=  mysql_fetch_assoc($qDt)){
                        $clid="onclick=setKary('".$rDt['karyawanid']."') style=cursor:pointer;";
                        $tab.="<tr ".$clid." class=rowcontent><td>".$rDt['nik']."</td>";
                        $tab.="<td>".$rDt['namakaryawan']."</td>";
                        $tab.="<td>".$rDt['lokasitugas']."</td>";
                        $tab.="</tr>";

                    }
                    $tab.="</tbody></table>";
                    echo $tab;
                break;
                default:
                break;
        }
?>