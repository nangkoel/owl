<?php
session_start();
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('config/connection.php');
$optNmblk=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
$param=$_POST;

        switch($param['proses'])
        {
                case'createTable':
                      //$thisDate=date("Y-m-d");
                $table .= "<table id='ppDetailTable'>";
                //echo"warning:".$table;
                # Header
                $table .= "<thead>";
                $table .= "<tr class=rowheader>";
                $table .= "<td>".$_SESSION['lang']['pokokdiamati']."</td>";
                $table .= "<td>".$_SESSION['lang']['luaspengamatan']."</td>";
                $table .= "<td>Darna Trima</td>"; 
                $table .= "<td>Setothosea Asigna</td>";
                $table .= "<td>Setora Nitens</td>";
				 $table .= "<td>Ulat Kantong</td>";
                $table .= "<td>Keterangan</td>";
                $table .= "<td>Action</td>";
                $table .= "</tr>";
                $table .= "</thead>";

                # Data
                $table .= "<tr id='detail_tr' class='rowcontent'>";
                $table .= "<td>".makeElement("pkkId",'textnum','0',array('style'=>'width:150px'))."</td>";
                $table .= "<td>".makeElement("luasPengamatan",'textnum','0',array('style'=>'width:150px'))."</td>";
                $table .= "<td>".makeElement("darnaTrima",'textnum','0',array('style'=>'width:150px'))."</td>";
                $table .= "<td>".makeElement("Asigna",'textnum','0',array('style'=>'width:150px'))."</td>";
                $table .= "<td>".makeElement("Nitens",'textnum','0',array('style'=>'width:150px'))."</td>";
				$table .= "<td>".makeElement("Kantong",'textnum','0',array('style'=>'width:150px'))."</td>";
                $table .= "<td>".makeElement("ktrangan",'text','',array('style'=>'width:150px'))."</td>";
                # Add, Container Delete
                $table .= "<td><input type=hidden id=nourut />
                           <img id='detail_add' title='".$_SESSION['lang']['save']."' class=zImgBtn onclick=\"svDetail()\" src='images/save.png'/>
                           <img title='".$_SESSION['lang']['clear']."' class=resicon onclick=\"clearData()\" src='images/clear.png'/>";
                $table .= "&nbsp;<img id='detail_delete' /></td>";
                $table .= "</tr>";
                $table .= "</tbody>";
                $table .= "</table>";
                    if($param['status']=='updateForm'){
                        $sdata="select distinct * from ".$dbname.".kebun_qc_ulatapiht 
                                where kodeblok='".$param['kodeblok']."' and tanggal='".  tanggaldgnbar($param['tanggal'])."'";
                        $qdata=mysql_query($sdata) or die(mysql_error($conn));
                        $rdata=mysql_fetch_assoc($qdata);
                        $optKary3=$optKary2=$optKary.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                        $sData="select distinct nik,karyawanid,namakaryawan from ".$dbname.".datakaryawan where lokasitugas='".substr($rdata['kodeblok'],0,4)."'
                                and tipekaryawan=3 and tanggalkeluar='0000-00-00' order by namakaryawan asc";
                        $qData=mysql_query($sData) or die(mysql_error($conn));
                        while($rData=  mysql_fetch_assoc($qData)){
                            if($rdata['pengawas']!=''){
                                $optKary.="<option value='".$rData['karyawanid']."' ".($rdata['pengawas']==$rData['karyawanid']?"selected":"").">".$rData['nik']."-".$rData['namakaryawan']."</option>";
                            }else{
                                $optKary.="<option value='".$rData['karyawanid']."'>".$rData['nik']."-".$rData['namakaryawan']."</option>";
                            }
                            if($rdata['pendamping']!=''){
                                $optKary2.="<option value='".$rData['karyawanid']."' ".($rdata['pendamping']==$rData['karyawanid']?"selected":"").">".$rData['nik']."-".$rData['namakaryawan']."</option>";
                            }else{
                                $optKary2.="<option value='".$rData['karyawanid']."'>".$rData['nik']."-".$rData['namakaryawan']."</option>";
                            }
                            if($rdata['mengetahui']!=''){
                                $optKary3.="<option value='".$rData['karyawanid']."'  ".($rdata['mengetahui']==$rData['karyawanid']?"selected":"").">".$rData['nik']."-".$rData['namakaryawan']."</option>";
                            }else{
                                $optKary3.="<option value='".$rData['karyawanid']."'>".$rData['nik']."-".$rData['namakaryawan']."</option>";
                            }
                        }
                        $dert=substr($rdata['kodeblok'],0,4)."####".$rdata['kodeblok']."####".$optNmblk[$rdata['kodeblok']]."####".tanggalnormal($rdata['tanggal'])."####";
                        $dert.=tanggalnormal($rdata['tanggalpengendalian'])."####".$rdata['jenissensus']."####".$rdata['catatan']."####";
                        $dert.=$optKary."####".$optKary2."####".$optKary3;
                    }else{
                         $dert="";
                    }
             
                if($param['status']=='updateForm'){
                    echo  $table."####".$dert;
                }else{
                    echo $table;
                }
                break;
                case'loadDetail':
                $sDt="select * from ".$dbname.".kebun_qc_ulatapidt
                      where kodeblok='".$param['kodeBlok']."' and tanggal='".tanggaldgnbar($param['tanggal'])."'";
                    //echo $sDt;
                $qDt=mysql_query($sDt) or die(mysql_error());
                while($rDet=mysql_fetch_assoc($qDt)){
                        $no+=1;
                        echo"
                        <tr class=rowcontent>
                        <td>".$no."</td>
                        <td align=right>".$rDet['pokokdiamati']."</td>
                        <td align=right>".$rDet['luasdiamati']."</td>
                        <td align=right>".$rDet['jlhdarnatrima']."</td>
                        <td align=right>".$rDet['jlhsetothosea']."</td>
                        <td align=right>".$rDet['jlhsetoranitens']."</td>
						<td align=right>".$rDet['jlhulatkantong']."</td>
                        <td>".$rDet['keterangan']."</td>
                        <td><img src=images/application/application_edit.png class=resicon  title='Edit' 
                        onclick=\"editDetail('".$rDet['pokokdiamati']."','".$rDet['luasdiamati']."','".$rDet['jlhdarnatrima']."','".$rDet['jlhsetothosea']."','".$rDet['jlhsetoranitens']."','".$rDet['jlhulatkantong']."','".$rDet['keterangan']."','".$rDet['nourut']."');\">
                        <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delDetail('".$rDet['kodeblok']."','".tanggalnormal($rDet['tanggal'])."','".$rDet['nourut']."');\" ></td>
                        </tr>
                        ";
                }

                break;
                case'insertDetail':
                    if($param['nourut']==''){
                        $sck="select distinct * from ".$dbname.".kebun_qc_ulatapidt where 
                              kodeblok='".$param['kodeBlok']."' and tanggal='".tanggaldgnbar($param['tanggal'])."'
                              order by nourut desc limit 0,1";
                        //exit("error:".$sck);
                        $qck=mysql_query($sck) or die(mysql_error($conn));
                        $rck=mysql_fetch_assoc($qck);
                        if($rck['nourut']==''){
                            $param['nourut']=1;
                        }else{
                            $param['nourut']=$rck['nourut']+1;
                        }
                        $sinsert="insert into ".$dbname.".kebun_qc_ulatapidt values ";
                        $sinsert.="('".$param['kodeBlok']."','".tanggaldgnbar($param['tanggal'])."','".$param['nourut']."'
                        ,'".$param['pkkId']."','".$param['luasPengamatan']."','".$param['darnaTrima']."','".$param['Asigna']."'
                        ,'".$param['Nitens']."','".$param['Kantong']."','".$param['ktrangan']."')";
                        if(!mysql_query($sinsert)){
                                 exit("error: dberror".mysql_error($conn)."___".$sinsert);
                        }
                    }else{
                        $sdel="delete from ".$dbname.".kebun_qc_ulatapidt where 
                               kodeblok='".$param['kodeBlok']."'  and tanggal='".tanggaldgnbar($param['tanggal'])."'
                               and nourut='".$param['nourut']."'";
                        if(mysql_query($sdel)){
                            $sinsert="insert into ".$dbname.".kebun_qc_ulatapidt values ";
                            $sinsert.="('".$param['kodeBlok']."','".tanggaldgnbar($param['tanggal'])."','".$param['nourut']."'
                                        ,'".$param['pkkId']."','".$param['luasPengamatan']."','".$param['darnaTrima']."','".$param['Asigna']."'
                                        ,'".$param['Nitens']."','".$param['Kantong']."','".$param['ktrangan']."')";
                            if(!mysql_query($sinsert)){
                                 exit("error: dberror".mysql_error($conn)."___".$sinsert);
                            }
                            
                        }else{
                             exit("error: dberror".mysql_error($conn)."___".$sdel);
                        } 
                    }
                break;
                case'delDetail':
                        $sdel="delete from ".$dbname.".kebun_qc_ulatapidt where kodeblok='".$param['kodeBlok']."'  and tanggal='".tanggaldgnbar($param['tanggal'])."'
                               and nourut='".$param['nourut']."'";
                        if(!mysql_query($sdel)){
                                 exit("error: dberror".mysql_error($conn)."___".$sdel);
                        }
                break;
        }

?>
