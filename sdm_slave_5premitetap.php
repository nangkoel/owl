<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

//##thn##pilInp##karyawanId##idKomponen##jmlhDt##method##tpKary
$param=$_POST;
$method=$_POST['method'];
$tpKary=$_POST['tpKary'];
$optThn=$_POST['optThn'];
$pilInp=$_POST['pilInp'];
$karyawanId=$_POST['karyawanId'];
$idKomponen=$_POST['idKomponen'];
$jmlhDt=$_POST['jmlhDt'];
$thn=$_POST['thn'];

$optTip=makeOption($dbname, 'sdm_5tipekaryawan', 'id,tipe');
$optNmKar=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optTipe=makeOption($dbname, 'datakaryawan', 'karyawanid,tipekaryawan');
$optKomponen=makeOption($dbname, 'sdm_ho_component', 'id,name');
        switch($method)
        {
               
            case'getTipe':
                //exit("error:".$param['kdjbn']);
                $optPil="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
            if($param['tpTransaksi']=='0'){
                $sjbtn="select distinct * from ".$dbname.".sdm_5jabatan order by namajabatan";
                //exit("error:".$sjbtn);
                $qjbtn=mysql_query($sjbtn) or die(mysql_error());
                while($rjbtn=  mysql_fetch_assoc($qjbtn)){
                    if($param['kdjbn']!=''){
                        $optPil.="<option value='".$rjbtn['kodejabatan']."' ".($rjbtn['kodejabatan']==$param['kdjbn']?"selected":"").">".$rjbtn['namajabatan']."</option>";
                    }else{
                        $optPil.="<option value='".$rjbtn['kodejabatan']."'>".$rjbtn['namajabatan']."</option>";
                    }
                }
            }else if($param['tpTransaksi']=='1'){
                $sjbtn="select distinct * from ".$dbname.".sdm_5tipekaryawan order by tipe";
                //exit("error:".$sjbtn);
                $qjbtn=mysql_query($sjbtn) or die(mysql_error());
                while($rjbtn=  mysql_fetch_assoc($qjbtn)){
                     if($param['kdjbn']!=''){
                        $optPil.="<option value='".$rjbtn['id']."' ".($rjbtn['id']==$param['kdjbn']?"selected":"").">".$rjbtn['tipe']."</option>";
                     }else{
                         $optPil.="<option value='".$rjbtn['id']."'>".$rjbtn['tipe']."</option>";
                     }
                }
            }
            echo $optPil;
            break;
            
            case'loadData':     
                $arrd=array("0"=>"Premi Tetap","1"=>"Insentif");
                $limit=30;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;
                $no=0;	 
                 if($param['tpTransaksi']==0){
                     $optdata=makeOption($dbname, 'sdm_5jabatan','kodejabatan,namajabatan');
                    $str="select * from ".$dbname.".sdm_5premitetap  
                          where kodeorg='".$_SESSION['empl']['lokasitugas']."' ";
                 }else{
                     $optdata=makeOption($dbname, 'sdm_5tipekaryawan','id,tipe');
                     $str="select tipekaryawan as kodejabatan,insentif as premitetap from ".$dbname.".sdm_5insentif   
                          where kodeorg='".$_SESSION['empl']['lokasitugas']."' ";
                 }
                //exit("error".$str);
                $res=mysql_query($str);
                $oow=mysql_num_rows($res);
                if($oow==0){
                    echo"<tr class=rowcontent><td colspan=5>".$_SESSION['lang']['dataempty']."</td></tr>";
                }
                else{
                    while($bar=mysql_fetch_assoc($res))
                    {
                    echo"<tr class=rowcontent>
                    <td>".$arrd[$param['tpTransaksi']]."</td>    
                    <td>".$optdata[$bar['kodejabatan']]."</td> 
                    <td align=right>".number_format($bar['premitetap'],0)."</td>  
                    <td align=center>
                              <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$param['tpTransaksi']."','".$bar['kodejabatan']."','".$bar['premitetap']."');\">
                      </td>
                    </tr>";	
                    }
//                    echo"<tr class=rowheader><td colspan=6 align=center>
//                ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $oow."<br />
//                <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
//                <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
//                </td>
//                </tr>";
                }
                break;
                case'getForm':
                $tab.="<fieldset><legend>".$_SESSION['lang']['kodejabatan']."/".$_SESSION['lang']['tipekaryawan']."</legend>";
                $tab.="<table cellpadding=1 cellspacing=1 border=0>";
                $tab.="<tr><td>".$_SESSION['lang']['find']."</td><td><input type=text class=myinputtext id=no_brg style=width:150px />
                      <button class=mybutton onclick=cariTipe()>".$_SESSION['lang']['find']."</button></td></tr>";
                $tab.="</table></fieldset>";
                $tab.="<fieldset><legend>".$_SESSION['lang']['result']."</legend><div id=container2 style=overflow:auto;width:300px;height:200px;></div></fieldset><input type=hidden id=tptrans value='".$param['tpTransaksi']."' />";
                echo $tab;
                break;
                case'cariTipe':
                if($param['tpTransaksi']=='0'){
                $sjbtn="select distinct kodejabatan as id,namajabatan as tipe from ".$dbname.".sdm_5jabatan 
                        where namajabatan like '%".$param['txtfind']."%'  order by namajabatan";
                //exit("error:".$sjbtn);
                $qjbtn=mysql_query($sjbtn) or die(mysql_error());
               
                }else if($param['tpTransaksi']=='1'){
                    $sjbtn="select distinct * from ".$dbname.".sdm_5tipekaryawan
                            where tipe like '%".$param['txtfind']."%' order by tipe";
                    //exit("error:".$sjbtn);
                    $qjbtn=mysql_query($sjbtn) or die(mysql_error());
                   
                }
                $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>";
                $tab.="<tr><td>id</td><td>Tipe</td></tr></thead><tbody>";
                 while($rjbtn=  mysql_fetch_assoc($qjbtn)){
                     $tab.="<tr class=rowcontent onclick=setPo('".$rjbtn['id']."')>";
                     $tab.="<td>".$rjbtn['id']."</td>";
                     $tab.="<td>".$rjbtn['tipe']."</td></tr>";
                    }
                $tab.="</tbody></table>";
                echo $tab;
                break;
                case'insert':
                    //$arr="##tpTransaksi##pilInp##premiIns##method";
                if($param['premiIns']==''){
                    exit("error:Premi/Insetif Tidak boleh kosong");
                }
                if($param['pilInp']==''){
                    exit("error:".$_SESSION['lang']['kodejabatan']."/".$_SESSION['lang']['tipekaryawan']." Tidak boleh kosong");
                }
                if($param['tpTransaksi']==''){
                    exit("error:Tipe transaksi tidak boleh kosong");
                }
                if($param['tpTransaksi']==0){
                    $sins="delete from ".$dbname.".sdm_5premitetap where 
                           kodeorg='".$_SESSION['empl']['lokasitugas']."' and kodejabatan='".$param['pilInp']."'";
                    if(mysql_query($sins)){
                        $sinsd="insert into ".$dbname.".sdm_5premitetap (`kodeorg`,`kodejabatan`,`premitetap`,`updateby`)
                                values ('".$_SESSION['empl']['lokasitugas']."','".$param['pilInp']."','".$param['premiIns']."','".$_SESSION['standard']['userid']."')";
                        if(!mysql_query($sinsd)){
                            exit("error:\n".$sinsd.mysql_error($conn));
                        }
                    }else{
                        exit("error:\n".$sinsd.mysql_error($conn));
                    }
                }elseif($param['tpTransaksi']==1){
                    $sins="delete from ".$dbname.".sdm_5insentif where 
                           kodeorg='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan='".$param['pilInp']."'";
                    if(mysql_query($sins)){
                        $sinsd="insert into ".$dbname.".sdm_5insentif (`kodeorg`,`tipekaryawan`,`insentif`,`updateby`)
                                values ('".$_SESSION['empl']['lokasitugas']."','".$param['pilInp']."','".$param['premiIns']."','".$_SESSION['standard']['userid']."')";
                        if(!mysql_query($sinsd)){
                            exit("error:\n".$sinsd.mysql_error($conn));
                        }
                    }else{
                        exit("error:\n".$sinsd.mysql_error($conn));
                    }
                }
                break;
        }
?>