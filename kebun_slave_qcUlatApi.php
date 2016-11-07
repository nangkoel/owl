<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
$optNm=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');
$param=$_POST;
        switch($param['proses']){
            case'getDetailPP':
                $tab.="<table cellpadding=1 cellspacing=1 border=0>";
                $tab.="<tr><td>".$_SESSION['lang']['kodeblok']."</td>";
                $tab.="<td>
                       <input type=text style=width:150px class=myinputtext id=fnOrg  onkeypress='return tanpa_kutip(event)' value='".$param['kbnId']."' /></td></tr></table>";
                $tab.="<button class=mybutton onclick=findOrg()>".$_SESSION['lang']['find']."</button>";
                $tab.="<fieldset><legend>".$_SESSION['lang']['hasil']."</legend>
                       <table cellpadding=1 cellspacing=1 border=0 class=sortable>";
                $tab.="<thead><tr class=rowheader>";
                $tab.="<td>".$_SESSION['lang']['kodeblok']."</td>";
                $tab.="<td>".$_SESSION['lang']['namaorganisasi']."</td></tr></thead>
                        <tbody id=hasilpencarian style='overflow:auto; width:300px; height:300px;'>";
                $tab.="</tbody></table></fieldset>";
                echo $tab;
            break;
                case'cariOrg':
                //echo"warning:masuk";
                $str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where 
                      namaorganisasi like '%".$param['txtfind']."%' or kodeorganisasi like '%".$param['txtfind']."%' and tipe='BLOK' "; //echo "warning:".$str;exit();
                if($res=mysql_query($str))
                {
                    while($bar=mysql_fetch_object($res)){
                            $no+=1;
                            echo"<tr class=rowcontent style='cursor:pointer;' onclick=\"setOrg('".$bar->kodeorganisasi."','".$bar->namaorganisasi."')\" title='Click' >
                                      <td>".$bar->kodeorganisasi."</td>
                                      <td>".$bar->namaorganisasi."</td>
                                     </tr>";
                    }	 
                  }	
                  else
                        {
                                echo " Gagal,".addslashes(mysql_error($conn));
                        }	
                break;
                case'getKary':
                    $optKary.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                    $sData="select distinct nik,karyawanid,namakaryawan from ".$dbname.".datakaryawan where lokasitugas='".$param['kbnId']."'
                             and tanggalkeluar='0000-00-00' order by namakaryawan asc";
                    $qData=mysql_query($sData) or die(mysql_error($conn));
                    while($rData=  mysql_fetch_assoc($qData)){
                        $optKary.="<option value='".$rData['karyawanid']."'>".$rData['nik']."-".$rData['namakaryawan']."</option>";
                    }
                    echo $optKary;
                break;
                case'insert':
                    if($param['kodeBlok']==''){
                        exit("error: ".$_SESSION['lang']['kodeblok']." can't empty");
                    }
                    if($param['tglSensus']==''){
                        exit("error: ".$_SESSION['lang']['tglsensus']." can't empty");
                    }
                    if($param['tglPengendalian']==''){
                        exit("error: ".$_SESSION['lang']['tglPengendalian']." can't empty");
                    }
                    
                    $hwre="kodeblok='".$param['kodeBlok']."' and tanggal='".tanggaldgnbar($param['tglSensus'])."'";
                    $optCar=makeOption($dbname,'kebun_qc_ulatapiht','kodeblok,tanggal',$hwre);
                    if($optCar[$param['kodeBlok']]==''){
                        $sInsert="insert into ".$dbname.".kebun_qc_ulatapiht 
                                  (kodeblok,tanggal,tanggalpengendalian,jenissensus,catatan,pengawas,pendamping,mengetahui,updateby) values ";
                        $sInsert.="('".$param['kodeBlok']."','".tanggaldgnbar($param['tglSensus'])."','".tanggaldgnbar($param['tglPengendalian'])."'
                                    ,'".$param['jenisId']."','".$param['cattn']."','".$param['pengawasId']."','".$param['pendampingId']."','".$param['mengetahuiId']."'
                                    ,'".$_SESSION['standard']['userid']."')";
                        if(!mysql_query($sInsert)){
                            exit("error: dberror".mysql_error($conn)."___".$sInsert);
                        }
                    }else{
                        exit("error: Data already exist");
                    }
                break;
                case'updateData':
                    $hwre="kodeblok='".$param['kodeBlok']."' and tanggal='".tanggaldgnbar($param['tglSensus'])."'";
                    $supdate="update ".$dbname.".kebun_qc_ulatapiht set tanggalpengendalian='".tanggaldgnbar($param['tglPengendalian'])."',
                              jenissensus='".$param['jenisId']."',catatan='".$param['cattn']."',pengawas='".$param['pengawasId']."',
                              pendamping='".$param['pendampingId']."',mengetahui='".$param['mengetahuiId']."',updateby='".$_SESSION['standard']['userid']."'
                              where ".$hwre."";
                    //exit("error:".$supdate);
                    if(!mysql_query($supdate)){
                        exit("error: dberror".mysql_error($conn)."___".$supdate);
                    }
                break;
                case'loadNewData':
                echo"
                <table cellspacing=1 border=0 class=sortable>
                <thead>
                <tr class=rowheader>
                <td>No.</td>
                <td>".$_SESSION['lang']['kodeblok']."</td>
                <td>".$_SESSION['lang']['tglsensus']."</td>
                <td>".$_SESSION['lang']['jenis']."</td>
                <td>".$_SESSION['lang']['pengawas']."</td>
                <td>".$_SESSION['lang']['pendamping']."</td>
                <td>".$_SESSION['lang']['mengetahui']."</td>
                <td>Action</td>
                </tr>
                </thead>
                <tbody>";
                    if($param['tanggal']!=''){
                        $whr.="and tanggal='".tanggaldgnbar($param['tanggal'])."'";
                    }
                    if($param['divisiId']!=''){
                        $whr.="and kodeblok like '".$param['divisiId']."%'";
                    }
                $limit=20;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;

                $ql2="select count(*) as jmlhrow from ".$dbname.".kebun_qc_ulatapiht where kodeblok!='' ".$whr." order by `tanggal` desc";// echo $ql2;
                $query2=mysql_query($ql2) or die(mysql_error());
                while($jsl=mysql_fetch_object($query2)){
                $jlhbrs= $jsl->jmlhrow;
                }


                $slvhc="select * from ".$dbname.".kebun_qc_ulatapiht  where kodeblok!='' ".$whr." order by `tanggal` desc limit ".$offset.",".$limit."";
                $qlvhc=mysql_query($slvhc) or die(mysql_error());
                $user_online=$_SESSION['standard']['userid'];
                while($rlvhc=mysql_fetch_assoc($qlvhc)){

                    $no+=1;
                    echo"
                    <tr class=rowcontent>
                    <td>".$no."</td>
                    <td>".$rlvhc['kodeblok']."</td>
                    <td>".tanggalnormal($rlvhc['tanggal'])."</td>
                    <td>".$rlvhc['jenissensus']."</td>
                    <td>".$optNm[$rlvhc['pengawas']]."</td>
                    <td>".$optNm[$rlvhc['pendamping']]."</td>
                    <td>".$optNm[$rlvhc['mengetahui']]."</td>
                    <td>";
                   
                    echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeblok']."','".tanggalnormal($rlvhc['tanggal'])."','updateForm');\">
                    <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['kodeblok']."','".tanggalnormal($rlvhc['tanggal'])."');\" >	
                    <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_qc_ulatapiht','".$rlvhc['kodeblok'].",".$rlvhc['tanggal']."','','kebun_qc_ulatApi_pdf',event)\">";
                
                

                echo"</td>
                </tr>
                ";
                }
                echo"</tbody><tfoot>";
                echo"
                <tr class=rowheader><td colspan=8 align=center>
                ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
                <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                </td>
                </tr>";
                echo"</tfoot></table>";
                break;
                case'delData':
//                $periodeAKtif=$_SESSION['org']['period']['tahun']."-".$_SESSION['org']['period']['bulan'];
//                $tgl=substr(tanggaldgnbar($param['tanggal']),0,7);
//                if($tgl!=$periodeAKtif){
//                    
//                }
                $sDel="delete from ".$dbname.".kebun_qc_ulatapidt 
                       where tanggal='".tanggaldgnbar($param['tanggal'])."' and kodeblok='".$param['kodeBlok']."'";
//                /exit("error:".$sDel);
                //echo "___".$sDel;exit();
                if(mysql_query($sDel))
                {
                        $sDelDetail="delete from ".$dbname.".kebun_qc_ulatapiht 
                                     where tanggal='".tanggaldgnbar($param['tanggal'])."' and kodeblok='".$param['kodeBlok']."'";
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
                exit("Error:Accounting period has been closed");
                }
                break;
               

        }

?>