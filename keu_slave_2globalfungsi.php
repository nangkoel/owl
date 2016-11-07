<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
$param=$_POST;

switch($param['proses']){
    case'getPeriodeRev':
        if($param['pt']!=''){//
            $wher="and kodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$param['divisi']."')";
        }
        if($param['divisi']!=''){//keu_jurnaldt_vw
            $wher="";
            $wher="and kodeorg='".$param['divisi']."'";
        }
        if($param['revisi']!=''){//keu_jurnaldt_vw
            $wher.="and revisi='".$param['revisi']."'";
        }
        $sPeriodeRev="select distinct periode from ".$dbname.".keu_jurnaldt_vw where kodeorg!='' ".$wher." order by periode desc";
        $qPeriodeRev=mysql_query($sPeriodeRev) or die(mysql_error($conn));
        $rowcek=mysql_num_rows($qPeriodeRev);
        if(($rowcek==0)||($param['revisi']==0)){
            //get existing period
            $str="select distinct periode as periode from ".$dbname.".setup_periodeakuntansi
                  order by periode desc";
            $res=mysql_query($str);
            //$optper="<option value=''>".$_SESSION['lang']['sekarang']."</option>";
            while($bar=mysql_fetch_object($res))
            {
                    $optPeriode.="<option value='".$bar->periode."'>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</option>";
            }
        }else{
                while($rPeriodeRev=  mysql_fetch_assoc($result)){
                    $optPeriode.="<option value='".$rPeriodeRev['periode']."'>".$rPeriodeRev['periode']."</option>";
                }
        }
        
        echo $optPeriode;
    break;
    case'getKary':
        if($param['unit']==''){
            exit("error:".$_SESSION['lang']['untukunit']."/".$_SESSION['lang']['subunit']." can't empty");
        }

        $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>";
        $tab.="<thead>";
        $tab.="<tr><td>".$_SESSION['lang']['nik']."</td>";
        $tab.="<td>".$_SESSION['lang']['namakaryawan']."</td>";
        $tab.="</tr></thead><tbody>";
        if(strlen($param['subunit'])=='6'){
            $wher="subbagian='".$param['subunit']."'";
        }else{
            $wher="lokasitugas='".$param['unit']."'";
        }
        $sDt="select distinct karyawanid,nik,namakaryawan from ".$dbname.".datakaryawan where ".$wher." and namakaryawan like '".$param['nmkary']."%' 
              and tanggalkeluar='0000-00-00' order by namakaryawan asc";
        //exit("error:".$sDt);
        $qDt=mysql_query($sDt) or die(mysql_error($conn));
        while($rDt=  mysql_fetch_assoc($qDt)){
            $clid="onclick=setKary('".$rDt['karyawanid']."') style=cursor:pointer;";
            $tab.="<tr ".$clid." class=rowcontent><td>".$rDt['nik']."</td>";
            $tab.="<td>".$rDt['namakaryawan']."</td>";
            $tab.="</tr>";

        }
        $tab.="</tbody></table>";
        echo $tab;
    break;
    case'getSupp':
        if($param['unit']==''){
            exit("error:".$_SESSION['lang']['untukunit']."/".$_SESSION['lang']['subunit']." can't empty");
        }

        $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>";
        $tab.="<thead>";
        $tab.="<tr><td>".$_SESSION['lang']['kodesupplier']."</td>";
        $tab.="<td>".$_SESSION['lang']['namasupplier']."</td>";
        $tab.="</tr></thead><tbody>";
        $sDt="select supplierid,namasupplier from ".$dbname.".log_5supplier where status=1 and kodekelompok!='S005' and namasupplier like '%".$param['nmsupp']."%'";
        //exit("error:".$sDt);
        $qDt=mysql_query($sDt) or die(mysql_error($conn));
        while($rDt=  mysql_fetch_assoc($qDt)){
            $clid="onclick=setSupp('".$rDt['supplierid']."') style=cursor:pointer;";
            $tab.="<tr ".$clid." class=rowcontent><td>".$rDt['supplierid']."</td>";
            $tab.="<td>".$rDt['namasupplier']."</td>";
            $tab.="</tr>";

        }
        $tab.="</tbody></table>";
        echo $tab;
    break;
    case'getBlok':
        $tmplPro=0;
        
         if(strlen($param['subunit'])=='6'){
            $induk=$param['subunit'];
            $wher="induk='".$param['subunit']."' and tipe='BLOK'";
        }else{
            $induk=$param['unit'];
            $wher="induk='".$param['unit']."' and tipe='AFDELING'";
        }
        //$whr="kodeorg like '".$induk."%'";
        //$optBlokLm=makeOption($dbname, 'setup_blok', 'kodeorg,bloklama',$whr);
        $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>";
        $tab.="<thead>";
        $tab.="<tr><td>".$_SESSION['lang']['kodeorganisasi']."</td>";
        $tab.="<td>".$_SESSION['lang']['bloklama']."</td>";
        $tab.="<td>".$_SESSION['lang']['statusblok']."</td>";
        $tab.="<td>".$_SESSION['lang']['namaorganisasi']."</td>";
        $tab.="</tr></thead><tbody>";
        if(substr($induk,0,2)=='AK' or substr($induk,0,2)=='PB')
        {
            $str="select distinct kode as kodeorganisasi,nama as namaorganisasi from ".$dbname.".project where kode='".$induk."'";    
        }else{
            $str="select distinct kodeorganisasi,namaorganisasi,tipe,bloklama,statusblok from ".$dbname.".organisasi a 
                  left join ".$dbname.".setup_blok b on a.kodeorganisasi=b.kodeorg where ".$wher."
                  and tipe not like '%gudang%' and (bloklama like '%".$param['nmkary']."%' or kodeorganisasi like '%".$param['nmkary']."%'  or namaorganisasi like '%".$param['nmkary']."%')  order by kodeorganisasi";
            $tmplPro=1;
        }
        //exit("error:".$str);
        $res=mysql_query($str) or die(mysql_error($conn));
        while($rDt=  mysql_fetch_assoc($res)){
            $clid="onclick=setBlok('".$rDt['kodeorganisasi']."','BLOK') style=cursor:pointer;";
            $tab.="<tr ".$clid." class=rowcontent>
                  <td>".$rDt['kodeorganisasi']."</td>
                  <td>".$rDt['bloklama']."</td>
                  <td>".$rDt['statusblok']."</td>";
            $tab.="<td>".$rDt['namaorganisasi']."</td>";
            $tab.="</tr>";
        }
        if($tmplPro==1){
            $str="select kode as kodeorganisasi,nama as namaorganisasi from ".$dbname.".project where kodeorg='".$induk."' and posting=0";    
            $res=mysql_query($str);
            while($rDt=  mysql_fetch_assoc($res)){
                $clid="onclick=setBlok('".$rDt['kodeorganisasi']."','BLOK') style=cursor:pointer;";
                $tab.="<tr ".$clid." class=rowcontent>
                       <td>".$rDt['kodeorganisasi']."</td>
                       <td></td><td></td>";
                $tab.="<td>".$rDt['namaorganisasi']."</td>";
                $tab.="</tr>";
             }
        }
        $tab.="</tbody></table>";
        echo $tab;
    break;
    case'getMesin':
        $optJns=makeOption($dbname, 'vhc_5jenisvhc', 'jenisvhc,namajenisvhc');
        $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>";
        $tab.="<thead>";
        $tab.="<tr>
               <td>".$_SESSION['lang']['kodevhc']."</td>
               <td>".$_SESSION['lang']['jenisvch']."</td>
               <td>".$_SESSION['lang']['detail']."</td>";
        
        $tab.="</tr></thead><tbody>";
        
        $whr="kodeorganisasi in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
               and tipe='KANWIL'";
        $sOrg="select distinct kodeorganisasi from ".$dbname.".organisasi where ".$whr."";
        $qOrg=mysql_query($sOrg) or die(mysql_error($conn));
        $rOrg=mysql_fetch_assoc($qOrg);
        if($param['nmkary']!=''){
            $isi=" and kodevhc like '%".$param['nmkary']."%'";
        }
        $sVhc="select distinct kodevhc,jenisvhc,detailvhc from ".$dbname.".vhc_5master where kodetraksi like '%".$rOrg['kodeorganisasi']."%' ".$isi."";
        
        $qVHc=mysql_query($sVhc) or die(mysql_error($conn));
        while($rDt=  mysql_fetch_assoc($qVHc)){
                $clid="onclick=setBlok('".$rDt['kodevhc']."','TRAKSI') style=cursor:pointer;";
                $tab.="<tr ".$clid." class=rowcontent>
                       <td>".$rDt['kodevhc']."</td>
                       <td>".$optJns[$rDt['jenisvhc']]."</td>
                       <td>".$rDt['detailvhc']."</td>";
                $tab.="</tr>";
             }
       $tab.="</tbody></table>";
       echo $tab;
    break;
    case'getKeg':
        $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>";
        $tab.="<thead>";
        $tab.="<tr>
               <td>".$_SESSION['lang']['kodekgiatan']."</td>
               <td>".$_SESSION['lang']['kelompok']."</td>
               <td>".$_SESSION['lang']['namakegiatan']."</td>";
        $tab.="</tr></thead><tbody>";
        if($param['kdDet']==''){
            if(strlen($param['subunit'])==6){
                $param['kdDet']=$param['subunit'];
            }else{
                $param['kdDet']=$param['unit'];
            }
        }
        $optTipe=makeOption($dbname, 'organisasi', 'kodeorganisasi,tipe');
        if($param['nmkary']!=''){
            $whrdt="and (namakegiatan like '%".$param['nmkary']."%' or kodekegiatan like  '%".$param['nmkary']."%')";
        }
        echo $param['kdDet'];
        switch($optTipe[$param['kdDet']]){
            case'STENGINE':
            case'STATION':    
                $strf="select kodekegiatan,kelompok,namakegiatan from ".$dbname.".setup_kegiatan 
			       where kelompok='MIL' ".$whrdt." order by kelompok,namakegiatan";
            break;
            case'BLOK':
                $optTpBlok=makeOption($dbname, 'setup_blok', 'kodeorg,statusblok');
                if($optTpBlok[$param['kdDet']]=='TM')
		     $strf="select kodekegiatan,kelompok,namakegiatan from ".$dbname.".setup_kegiatan 
                            where (kelompok='TM' or kelompok='PNN')  ".$whrdt."  order by kelompok,namakegiatan";
		else
                {
                    $strf="select kodekegiatan,kelompok,namakegiatan from ".$dbname.".setup_kegiatan 
                           where kelompok='".$optTpBlok[$param['kdDet']]."'  ".$whrdt."  order by kelompok,namakegiatan"; 
                } 
            break;
            case'WORKSHOP':
                $strf="select kodekegiatan,kelompok,namakegiatan from ".$dbname.".setup_kegiatan 
			       where kelompok='WSH'  ".$whrdt."  order by kelompok,namakegiatan";
            break;
            case'SIPIL':
                $strf="select kodekegiatan,kelompok,namakegiatan from ".$dbname.".setup_kegiatan 
			       where kelompok='SPL'  ".$whrdt."  order by kelompok,namakegiatan";
            break;
            case'BIBITAN':
                $strf="select kodekegiatan,kelompok,namakegiatan from ".$dbname.".setup_kegiatan 
			       where  kelompok in ('BBT','MN','PN')  ".$whrdt."  order by kelompok,namakegiatan";
            break;
            default:
                 $strf="select kodekegiatan,kelompok,namakegiatan from ".$dbname.".setup_kegiatan 
                                   where kelompok='KNT'  ".$whrdt."  order by kelompok,namakegiatan";
            break;
        }
        
        $resf=mysql_query($strf) or die(mysql_error($conn));
        while($rData=  mysql_fetch_assoc($resf)){
            $clid="onclick=setDtKeg('".$rData['kodekegiatan']."') style=cursor:pointer;";
             $tab.="<tr class=rowcontent ".$clid.">
               <td>".$rData['kodekegiatan']."</td>
               <td>".$rData['kelompok']."</td>
               <td>".$rData['namakegiatan']."</td>";
        }
        $tab.="</tbody></table>";
        echo $tab;
    break;
}

?>