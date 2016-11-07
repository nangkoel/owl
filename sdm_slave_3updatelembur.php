<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

if(isset($_POST['proses']))
{
	$proses=$_POST['proses'];
}
else
{
	$proses=$_GET['proses'];
}


$optNmOrg=makeOption($dbname, 'organisasi','kodeorganisasi,namaorganisasi');

$_POST['kdOrg']==''?$kdOrg=$_GET['kdOrg']:$kdOrg=$_POST['kdOrg'];
$_POST['thnId']==''?$thnId=$_GET['thnId']:$thnId=$_POST['thnId'];
$_POST['kdProj']==''?$kdProj=$_GET['kdProj']:$kdProj=$_POST['kdProj'];
$tipe='PNN';

$unitId=$_SESSION['lang']['all'];
$dktlmpk=$_SESSION['lang']['all'];
if($proses=='preview'){
    if(tanggalsystem($_POST['tanggal2'])<tanggalsystem($_POST['tanggal1'])){
        exit("error: Tolong gunakan urutan tanggal yang benar");
    }
    $tglPP=explode("-",$_POST['tanggal1']);
    $date1 = $tglPP[0];
    $month1 = $tglPP[1];
    $year1 = $tglPP[2];
    //$tgl1 = $bar->tanggal;
    $tgl2 = $_POST['tanggal2']; 
    $pecah2 = explode("-", $tgl2);
    $date2 = $pecah2[0];
    $month2 = $pecah2[1];
    $year2 =  $pecah2[2];
    $jd1 = GregorianToJD($month1, $date1, $year1);
    $jd2 = GregorianToJD($month2, $date2, $year2);
    $jmlHari=$jd2-$jd1;
//    if($jmlHari>6){
//        exit("error: Tidak Boleh Lebih Dari 7 Hari");
//    }
    if(($_POST['tanggal1']=='')||($_POST['tanggal2']=='')){
        exit("error: ".$_SESSION['lang']['tanggal']."1 dan ".$_SESSION['lang']['tanggal']." 2 tidak boleh kosong");
    }
}

$_POST['tanggal1']==''?$tanggal1=$_GET['tanggal1']:$tanggal1=$_POST['tanggal1'];
$_POST['tanggal2']==''?$tanggal2=$_GET['tanggal2']:$tanggal2=$_POST['tanggal2'];

function putertanggal($tanggal)
{
    $qwe=explode('-',$tanggal);
    return $qwe[2].'-'.$qwe[1].'-'.$qwe[0];
} 

$tangsys1=putertanggal($tanggal1);
$tangsys2=putertanggal($tanggal2);

$wheretang=" b.tanggal like '%%' ";
if($tanggal1!=''){
    $wheretang=" b.tanggal = '".$tangsys1."' ";
    if($tanggal2!=''){
        $wheretang=" b.tanggal between '".$tangsys1."' and '".$tangsys2."' ";
    }
}
if($tanggal2!=''){
    $wheretang=" b.tanggal = '".$tangsys2."' ";
    if($tanggal1!=''){
        $wheretang=" b.tanggal between '".$tangsys1."' and '".$tangsys2."' ";
    }
}
$arr="##kdOrg##tanggal1##tanggal2";
if($proses=='preview'||$proses=='excel')
{


$brdr=0;
$bgcoloraja='';
if($proses=='excel'){
    $brdr=1;
    $bgcoloraja='green';
}
        
      
        $sData="select * from ".$dbname.".sdm_lemburdt where left(kodeorg,4)='".$kdOrg."' and tanggal between '".$tangsys1."' and '".$tangsys2."' order by tanggal,kodeorg asc";
 //echo $sData;
        $qData=mysql_query($sData) or die(mysql_error($conn));
        $rowdt=mysql_num_rows($qData);
        if($_SESSION['empl']['bagian']=='IT' || ($_SESSION['empl']['bagian']=='FIN' && $_SESSION['empl']['tipelokasitugas']=='KANWIL')){
            $tab.="<button class=mybutton onclick=postingDat(".$rowdt.")  id=revTmbl>Update Data</button>&nbsp;<button class=mybutton onclick=zExcel(event,'sdm_slave_3updatelembur.php','".$arr."')>Excel</button>";
        }else{
            $tab.="<button class=mybutton onclick=zExcel(event,'sdm_slave_3updatelembur.php','".$arr."')>Excel</button>";
        }
        
        #sulawesi kembali ke KTU dan finance
        if($_SESSION['empl']['regional']=='KALIMANTAN'){
            if($_SESSION['empl']['bagian']=='FIN' || $_SESSION['empl']['kodejabatan']=='98'){
                $tab.="<button class=mybutton onclick=postingDat(".$rowdt.")  id=revTmbl>Update Data</button>&nbsp;<button class=mybutton onclick=zExcel(event,'sdm_slave_3updatelembur.php','".$arr."')>Excel</button>";
            }else{
                $tab.="<button class=mybutton onclick=zExcel(event,'sdm_slave_3updatelembur.php','".$arr."')>Excel</button>";
            }
        }
        $optTpLembur= array(0=>"normal",1=>"minggu",2=>"hari libur bukan minggu", 3=>"hari raya");
        $regData=  makeOption($dbname, 'bgt_regional_assignment', 'kodeunit,regional');
	$tab.="<table cellspacing=1 border=".$brdr." class=sortable>
	<thead class=rowheader>
	<tr>
        <td ".$bgcoloraja." align=center rowspan=2>No.</td>
        <td ".$bgcoloraja." align=center rowspan=2>".$_SESSION['lang']['karyawanid']."</td>
        <td ".$bgcoloraja." align=center rowspan=2>".$_SESSION['lang']['nik']."</td>
        <td ".$bgcoloraja." align=center rowspan=2>".$_SESSION['lang']['namakaryawan']."</td>
        <td ".$bgcoloraja." align=center rowspan=2>".$_SESSION['lang']['tanggal']."</td>
        <td ".$bgcoloraja." align=center rowspan=2>".$_SESSION['lang']['tipelembur']."</td>
        <td ".$bgcoloraja." align=center rowspan=2>".$_SESSION['lang']['jamaktual']."</td>
        <td ".$bgcoloraja." align=center rowspan=2>Jam Lembur</td>
        <td ".$bgcoloraja." align=center rowspan=2>".$_SESSION['lang']['tipekaryawan']."</td>
        
        <td  align=center>Sebelum</td>
        <td align=center>Sesudah</td></tr>
        <tr><td ".$bgcoloraja.">".$_SESSION['lang']['uangkelebihanjam']."</td>
        <td ".$bgcoloraja.">".$_SESSION['lang']['uangkelebihanjam']."</td>
        </tr>";
        $tab.="</tr></thead><tbody>";
        $optTipeKar=makeOption($dbname, 'sdm_5tipekaryawan','id,tipe');
        while($rData=  mysql_fetch_assoc($qData)){
           $nor+=1;
           $whr="karyawanid='".$rData['karyawanid']."'";
           $whrlm="kodeorg='".substr($rData['kodeorg'],0,4)."' and tipelembur='".$rData['tipelembur']."' and jamaktual='".$rData['jamaktual']."'";
           $optNmKar=makeOption($dbname, 'datakaryawan','karyawanid,namakaryawan',$whr);
           $optNikKar=makeOption($dbname, 'datakaryawan','karyawanid,nik',$whr);
           $optTipekary=makeOption($dbname, 'datakaryawan','karyawanid,tipekaryawan',$whr);
           $optJamLembur=makeOption($dbname, 'sdm_5lembur','jamaktual,jamlembur',$whrlm);
           $tab.="<tr class=rowcontent id=rowDt_".$nor."><td align=center>".$nor."</td>";
           if($proses=='preview'){
                $tab.="<td><input type=hidden  id=karyawanid_".$nor." value='".$rData['karyawanid']."'>".$rData['karyawanid']."</td>";
                $tab.="<td>".$optNikKar[$rData['karyawanid']]."</td>";
           }else{
                $tab.="<td><input type=hidden  id=karyawanid_".$nor." value='".$rData['karyawanid']."'>'".$rData['karyawanid']."</td>";
                $tab.="<td>'".$optNikKar[$rData['karyawanid']]."</td>";
           }
           $tab.="<td>".$optNmKar[$rData['karyawanid']]."</td>";
           $tab.="<td id=tanggal_".$nor.">".$rData['tanggal']."</td>";
           $tab.="<td id=tipelembur_".$nor.">".$rData['tipelembur']."</td>";
           $tab.="<td align=right id=jamaktual_".$nor.">".$rData['jamaktual']."</td>";
           $tab.="<td align=right>".$optJamLembur[$rData['jamaktual']]."</td>";
           $tab.="<td>".$optTipeKar[$optTipekary[$rData['karyawanid']]]."</td>";
           $tab.="<td align=right id=uanglembur_".$nor.">".$rData['uangkelebihanjam']."</td>";
           $sGt="select sum(jumlah) as gapTun from ".$dbname.".sdm_5gajipokok where karyawanid='".$rData['karyawanid']."' and idkomponen in (31,1,2) and tahun='".substr($rData['tanggal'],0,4)."'";
           $qGt=mysql_query($sGt) or die(mysql_error($conn));
           $rGt=mysql_fetch_assoc($qGt);
           $uangLembur=0;
            if($regData[$kdOrg]=='SULAWESI'){
                if($optTipekary[$rData['karyawanid']]>3){
                    //$uangLembur=0.15*(($rGt['gapTun']*$optJamLembur[$rData['jamaktual']])/173);
                    $uangLembur=0.15*$optJamLembur[$rData['jamaktual']]*($rGt['gapTun']/25);//(($rGt['gapTun']*)/173);
                }else{
                    //$uangLembur=($rGt['gapTun']*$optJamLembur[$rData['jamaktual']])/173;
                    $uangLembur=($rGt['gapTun'])*($optJamLembur[$rData['jamaktual']]/173);
                }
            }else{
                
                if($optTipekary[$rData['karyawanid']]>3){
                     $uangLembur=$optJamLembur[$rData['jamaktual']]*(($rGt['gapTun']/25)*3/20);
                }
                else
                {
                    $uangLembur=($rGt['gapTun'])*($optJamLembur[$rData['jamaktual']]/173);
                }
                
            }
            
            
            
            
            
            $tab.="<td align=right id=kelebihanjam_".$nor.">".intval($uangLembur)."</td></tr>";
        }
         
         $tab.="</tbody></table>";
}     
switch($proses)
{ 
	case'preview':
	echo $tab;
	break;
        case'getPeriode': 
            $optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
            $sPeriodeAkut="select distinct periode from ".$dbname.".setup_periodeakuntansi 
                         where kodeorg='".$_POST['kdOrg']."' and tutupbuku=0";
            $qPeriodeCari=mysql_query($sPeriodeAkut) or die(mysql_error());
            while($rPeriodeCari=mysql_fetch_assoc($qPeriodeCari))
            {
               $optPeriode.="<option value='".$rPeriodeCari['periode']."'>".$rPeriodeCari['periode']."</option>";
            }
            echo $optPeriode;
        break;
        case'updateData':
            $scek="select * from ".$dbname.".sdm_5periodegaji where kodeorg='".$kdOrg."' and tanggalmulai<='".$_POST['tanggal']."' and tanggalsampai>='".$_POST['tanggal']."'";
            $qcek=  mysql_query($scek) or die(mysql_error($conn));
            $rcek= mysql_fetch_assoc($qcek);
            if($rcek['sudahproses']==0){
              
                $supdate="update ".$dbname.".sdm_lemburdt set uangkelebihanjam='".$_POST['klbhanjam']."'"
                        . "where karyawanid='".$_POST['karyId']."' and jamaktual='".$_POST['jmaktual']."' and tanggal='".$_POST['tanggal']."'  and tipelembur='".$_POST['tplembur']."'";
                //exit("error:".$supdate);
                if(!mysql_query($supdate)){
                 exit("error: db bermasalah ".mysql_error($conn)."___".$supdate);   
                }
            }else{
                exit("error: tanggal di luar periode gaji yang masih aktif");
            }
        break;
        case'excel':
        
        $thisDate=date("YmdHms");
                   //$nop_="Laporan_Pembelian";
                   $nop_="laporanUpdateBjr_".$thisDate;
                   $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
                    gzwrite($gztralala, $tab);
                    gzclose($gztralala);
                    echo "<script language=javascript1.2>
                       window.location='tempExcel/".$nop_.".xls.gz';
                       </script>";
        break;
	default:
	break;
}

?>