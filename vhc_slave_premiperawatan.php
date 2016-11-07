<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/fpdf.php');

if(($_GET['proses']=='excel')||($_GET['proses']=='pdf')){
    $param=$_GET;
}else{
    $param=$_POST;
}

$optThn=makeOption($dbname,'vhc_5master','kodevhc,tahunperolehan');
$optKlmpk=makeOption($dbname,'vhc_5jenisvhc','jenisvhc,kelompokvhc');
$optNmKary=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optNmOrg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
switch($param['proses']){
    case'preview':
        $periodeAKtif=$_SESSION['org']['period']['tahun']."-".$_SESSION['org']['period']['bulan'];
        if($param['periode']!=$periodeAKtif){
            exit("error: Periode diffrent with active periode");
        }
        #kendaraan
        $sKend="select distinct sum(jumlah) as jmlh,kodevhc,jenisvhc,tanggal from ".$dbname.".vhc_rundt a 
                left join ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi 
                where tanggal like '".$param['periode']."%' group by kodevhc,tanggal 
                order by kodevhc asc";
        $qKend=mysql_query($sKend) or die(mysql_error($conn));
        while($rKend=mysql_fetch_assoc($qKend)){
            $dtVhc[$rKend['kodevhc']]=$rKend['kodevhc'];
            $dtPrest[$rKend['kodevhc']]+=$rKend['jmlh'];
            $jmlHar[$rKend['kodevhc']]+=1;
            $jnsVhc[$rKend['kodevhc']]=$rKend['jenisvhc'];
        }
        $jmlhRowKary=count($dtVhc);
         if($jmlhRowKary==0){
            exit("error: Data Empty");
        }
        #jmlhcuci
        $sKary="SELECT distinct kodevhc,premicuci,a.tanggal
                FROM ".$dbname.".vhc_runhk a
                LEFT JOIN ".$dbname.".vhc_runht b ON a.notransaksi = b.notransaksi
                WHERE a.tanggal LIKE '".$param['periode']."%'
                AND premicuci !=0
                ORDER BY kodevhc ASC ";
        $qKary=mysql_query($sKary) or die(mysql_error($conn));
        while($rKary=  mysql_fetch_assoc($qKary)){
            $jmlCuci[$rKary['kodevhc']]+=$rKary['premicuci'];
        }
        #operator
        $sOpt="select distinct karyawanid,vhc 
               from ".$dbname.".vhc_5operator order by vhc asc";
        $qOpt=mysql_query($sOpt) or die(mysql_error($conn));
        while($rOpt=  mysql_fetch_assoc($qOpt)){
            $operator[$rOpt['vhc']]=$rOpt['karyawanid'];
        }
        $tab.="<button class=mybutton onclick=saveAll('".$jmlhRowKary."')>".$_SESSION['lang']['save']."</button>
              <table cellpadding=1 cellspacing=1 border=0 class=sortable><thead><tr align=center>";
        $tab.="<td>No.</td>";
        $tab.="<td>".$_SESSION['lang']['kodevhc']."</td>";
        $tab.="<td>".$_SESSION['lang']['hasilkerjad']."</td>";
        $tab.="<td>".$_SESSION['lang']['jumlahhari']."</td>";
        $tab.="<td>".$_SESSION['lang']['jumlahcuci']."</td>";
        $tab.="<td>".$_SESSION['lang']['namakaryawan']."</td>";
        $tab.="<td>".$_SESSION['lang']['premi']."</td>";
        $tab.="</tr></thead><tbody>";
        foreach($dtVhc as $lsVhc){
            $no+=1;
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$no."</td>";
            $tab.="<td >".$lsVhc."</td>";
            $tab.="<td align=right>".number_format($dtPrest[$lsVhc],0)."</td>";
            $tab.="<td align=right>".$jmlHar[$lsVhc]."</td>";
            $tab.="<td align=right>".$jmlCuci[$lsVhc]."</td>";
            if(intval($operator[$lsVhc])=='0'){
                $operator[$lsVhc]=0;
            }
            $tab.="<td>".$optNmKary[$operator[$lsVhc]]."<input type=hidden id=karyId_".$no." value='".$operator[$lsVhc]."' /></td>";
            $premi[$lsVhc]=0;
            $thn[$lsVhc]=intval(date('Y'))-$optThn[$lsVhc];
            if($jmlCuci[$lsVhc]!=''){
                if((substr($lsVhc,0,2)=='MJ')&&($optKlmpk[$jnsVhc[$lsVhc]]=='KD')){
                    $premi[$lsVhc]=4500*$jmlCuci[$lsVhc];
                }
                elseif((substr($lsVhc,0,2)=='DT')&&($optKlmpk[$jnsVhc[$lsVhc]]=='KD')){
                    $premi[$lsVhc]=20000*$jmlHar[$lsVhc];
                }elseif((substr($lsVhc,0,2)!='DT')&&($optKlmpk[$jnsVhc[$lsVhc]]=='KD')){
                    if(($optThn[$lsVhc]<6)&&($jmlHar[$lsVhc]>24)){
                        $premi[$lsVhc]=11000*$jmlHar[$lsVhc];
                    }elseif(($thn[$lsVhc]>5)&&($jmlHar[$lsVhc]>22)){
                        $premi[$lsVhc]=11000*$jmlHar[$lsVhc];
                    }
                }elseif($optKlmpk[$jnsVhc[$lsVhc]]=='AB'){
                    
                    if(($thn[$lsVhc]<6)&&($dtPrest[$lsVhc]>=175)){
                        $premi[$lsVhc]=5000*$dtPrest[$lsVhc];
                    }elseif(($thn[$lsVhc]>5)&&($dtPrest[$lsVhc]>=150)){
                        $premi[$lsVhc]=5000*$dtPrest[$lsVhc];
                    }elseif(($thn[$lsVhc]>10)&&($dtPrest[$lsVhc]>=125)){
                        $premi[$lsVhc]=5000*$dtPrest[$lsVhc];
                        
                    }
                }
            }
            $tab.="<td align=right>".number_format($premi[$lsVhc],2)."<input type=hidden id=premiDt_".$no." value=".$premi[$lsVhc]." /></td>";
            $tab.="</tr>";
        }
        $tab.="</tbody></table><button class=mybutton onclick=saveAll('".$jmlhRowKary."')>".$_SESSION['lang']['save']."</button>";
        echo $tab;
    break;
    case'saveAll':
        
        for($awal=1;$awal<=$param['jmlhRow'];$awal++){
            if(intval($param['karyId'][$awal])!=0){
                $sdel="delete from ".$dbname.".`kebun_premikemandoran`  where 
                       kodeorg='".$param['kodeorg']."' and `karyawanid`='".$param['karyId'][$awal]."'
                       and periode='".$param['periode']."'";
                //exit("error:".$sdel);
                if(mysql_query($sdel)){
                    $sinsert="insert into ".$dbname.".`kebun_premikemandoran` (`kodeorg`,`karyawanid`,`periode`,`jabatan`,`premi`,`updateby`) values";
                    $sinsert.="('".$param['kodeorg']."','".$param['karyId'][$awal]."','".$param['periode']."','RAWATKD','".$param['premiDt'][$awal]."','".$_SESSION['standard']['userid']."')";
                    //exit("error:".$sinsert);
                    if(!mysql_query($sinsert)){
                        exit("error: db error ".mysql_error($conn)."___".$sinsert);
                    }
                }else{
                        exit("error: db error ".mysql_error($conn)."___".$sdel);
                }
            }
        }
    break;
    case'loadData':
        $periodeAktif=$_SESSION['org']['period']['tahun']."-".$_SESSION['org']['period']['bulan'];
        $sData="select distinct kodeorg,periode,premi,karyawanid from ".$dbname.".kebun_premikemandoran where 
                kodeorg='".$_SESSION['empl']['lokasitugas']."' and jabatan='RAWATKD'  kodeorg,periode order by periode desc";
        //exit("error:".$sData);
        $qData=mysql_query($sData) or die(mysql_error($conn));
        while($rData=  mysql_fetch_assoc($qData)){
            $no+=1;
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$no."</td>";
            $tab.="<td>".$rData['kodeorg']."</td>";
            $tab.="<td>".$rData['karyawanid']."</td>";
            $tab.="<td>".$rData['premi']."</td>";
            if($rData['periode']==$periodeAktif){
                $tab.="<td>
                       <img src='images/excel.jpg' class='resicon' title='Excel' onclick=getExcel(event,'vhc_slave_premiperawatan.php','".$rData['kodeorg']."','".$rData['periode']."','".$rData['RAWATKD']."') >
                       &nbsp;
                       <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rData['kodeorg']."','".$rData['periode']."','".$rData['RAWATKD']."');\" >
                       &nbsp;
                      </td>";
            }else{
                $tab.="<td><img src='images/excel.jpg' class='resicon' title='Excel' onclick=getExcel(event,'kebun_slave_premipanen.php','".$rData['kodeorg']."','".$rData['periode']."','".$rData['kodepremi']."') ></td>";
            }
            $tab.="</tr>";
        }
        echo $tab;
    break;
    case'delData':
        $sdel="delete from ".$dbname.".`kebun_premipanen`  where 
               kodeorg='".$param['kodeorg']."' and periode='".$param['periode']."'";
            if(!mysql_query($sdel)){
                exit("error: db error ".mysql_error($conn)."___".$sdel);
            }
    break;
    case'excel':
        $tab.="<table>";
        $tab.="<tr><td colspan=5>".$_SESSION['lang']['kodeorg']." : ".$optNmOrg[$param['kodeorg']]."</td></tr>";
        $tab.="<tr><td colspan=5>".$_SESSION['lang']['periode']." : ".$param['periode']."</td></tr>";
        $tab.="</table>";
        $periodeAKtif=$_SESSION['org']['period']['tahun']."-".$_SESSION['org']['period']['bulan'];
        if($param['periode']!=$periodeAKtif){
            exit("error: Periode diffrent with active periode");
        }
        #kendaraan
        $sKend="select distinct sum(jumlah) as jmlh,kodevhc,jenisvhc,tanggal from ".$dbname.".vhc_rundt a 
                left join ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi 
                where tanggal like '".$param['periode']."%' group by kodevhc,tanggal 
                order by kodevhc asc";
        $qKend=mysql_query($sKend) or die(mysql_error($conn));
        while($rKend=mysql_fetch_assoc($qKend)){
            $dtVhc[$rKend['kodevhc']]=$rKend['kodevhc'];
            $dtPrest[$rKend['kodevhc']]+=$rKend['jmlh'];
            $jmlHar[$rKend['kodevhc']]+=1;
            $jnsVhc[$rKend['kodevhc']]=$rKend['jenisvhc'];
        }
        $jmlhRowKary=count($dtVhc);
        if($jmlhRowKary==0){
            exit("error: Data Empty");
        }
        #jmlhcuci
        $sKary="SELECT distinct kodevhc,premicuci,a.tanggal
                FROM ".$dbname.".vhc_runhk a
                LEFT JOIN ".$dbname.".vhc_runht b ON a.notransaksi = b.notransaksi
                WHERE a.tanggal LIKE '".$param['periode']."%'
                AND premicuci !=0
                ORDER BY kodevhc ASC ";
        $qKary=mysql_query($sKary) or die(mysql_error($conn));
        while($rKary=  mysql_fetch_assoc($qKary)){
            $jmlCuci[$rKary['kodevhc']]+=$rKary['premicuci'];
        }
        #operator
        $sOpt="select distinct karyawanid,vhc 
               from ".$dbname.".vhc_5operator order by vhc asc";
        $qOpt=mysql_query($sOpt) or die(mysql_error($conn));
        while($rOpt=  mysql_fetch_assoc($qOpt)){
            $operator[$rOpt['vhc']]=$rOpt['karyawanid'];
        }
        $tab.="<table cellpadding=1 cellspacing=1 border=1 class=sortable><thead><tr bgcolor=#DEDEDE align=center>";
        $tab.="<td>No.</td>";
        $tab.="<td>".$_SESSION['lang']['kodevhc']."</td>";
        $tab.="<td>".$_SESSION['lang']['hasilkerjad']."</td>";
        $tab.="<td>".$_SESSION['lang']['jumlahhari']."</td>";
        $tab.="<td>".$_SESSION['lang']['jumlahcuci']."</td>";
        $tab.="<td>".$_SESSION['lang']['namakaryawan']."</td>";
        $tab.="<td>".$_SESSION['lang']['premi']."</td>";
        $tab.="</tr></thead><tbody>";
        foreach($dtVhc as $lsVhc){
            $no+=1;
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$no."</td>";
            $tab.="<td >".$lsVhc."</td>";
            $tab.="<td align=right>".number_format($dtPrest[$lsVhc],0)."</td>";
            $tab.="<td align=right>".$jmlHar[$lsVhc]."</td>";
            $tab.="<td align=right>".$jmlCuci[$lsVhc]."</td>";
            if(intval($operator[$lsVhc])=='0'){
                $operator[$lsVhc]=0;
            }
            $tab.="<td>".$optNmKary[$operator[$lsVhc]]."<input type=hidden id=karyId_".$no." value='".$operator[$lsVhc]."' /></td>";
            $premi[$lsVhc]=0;
            $thn[$lsVhc]=intval(date('Y'))-$optThn[$lsVhc];
            if($jmlCuci[$lsVhc]!=''){
                if((substr($lsVhc,0,2)=='MJ')&&($optKlmpk[$jnsVhc[$lsVhc]]=='KD')){
                    $premi[$lsVhc]=4500*$jmlCuci[$lsVhc];
                }
                elseif((substr($lsVhc,0,2)=='DT')&&($optKlmpk[$jnsVhc[$lsVhc]]=='KD')){
                    $premi[$lsVhc]=20000*$jmlHar[$lsVhc];
                }elseif((substr($lsVhc,0,2)!='DT')&&($optKlmpk[$jnsVhc[$lsVhc]]=='KD')){
                    if(($optThn[$lsVhc]<6)&&($jmlHar[$lsVhc]>24)){
                        $premi[$lsVhc]=11000*$jmlHar[$lsVhc];
                    }elseif(($thn[$lsVhc]>5)&&($jmlHar[$lsVhc]>22)){
                        $premi[$lsVhc]=11000*$jmlHar[$lsVhc];
                    }
                }elseif($optKlmpk[$jnsVhc[$lsVhc]]=='AB'){
                    
                    if(($thn[$lsVhc]<6)&&($dtPrest[$lsVhc]>=175)){
                        $premi[$lsVhc]=5000*$dtPrest[$lsVhc];
                    }elseif(($thn[$lsVhc]>5)&&($dtPrest[$lsVhc]>=150)){
                        $premi[$lsVhc]=5000*$dtPrest[$lsVhc];
                    }elseif(($thn[$lsVhc]>10)&&($dtPrest[$lsVhc]>=125)){
                        $premi[$lsVhc]=5000*$dtPrest[$lsVhc];
                        
                    }
                }
            }
            $tab.="<td align=right>".number_format($premi[$lsVhc],2)."<input type=hidden id=premiDt_".$no." value=".$premi[$lsVhc]." /></td>";
            $tab.="</tr>";
        }
        $tab.="</tbody></table>";
        $tab.="Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];          
        $nop_="premiPerawatan_".$param['kodeorg']."__".$param['periode'];
        if(strlen($tab)>0)
        {
        if ($handle = opendir('tempExcel')) {
        while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
        @unlink('tempExcel/'.$file);
        }
        }	
        closedir($handle);
        }
        $handle=fopen("tempExcel/".$nop_.".xls",'w');
        if(!fwrite($handle,$tab))
        {
        echo "<script language=javascript1.2>
        parent.window.alert('Can't convert to excel format');
        </script>";
        exit;
        }
        else
        {
        echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls';
        </script>";
        }
        closedir($handle);
        }
    break;
   
}
?>
