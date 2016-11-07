<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');


$proses=$_GET['proses'];
//$periode=$_POST['periode'];
//$period=$_POST['period'];
$lksiTgs=$_SESSION['empl']['lokasitugas'];
$_POST['kdeOrg']==''?$kdeOrg=$_GET['kdeOrg']:$kdeOrg=$_POST['kdeOrg'];
$_POST['kdOrg']==''?$kdOrg=$_GET['kdOrg']:$kdOrg=$_POST['kdOrg'];
$_POST['afdId']==''?$afdId=$_GET['afdId']:$afdId=$_POST['afdId'];
$_POST['tgl1']==''?$tgl1=tanggalsystem($_GET['tgl1']):$tgl1=tanggalsystem($_POST['tgl1']);
$_POST['tgl2']==''?$tgl2=tanggalsystem($_GET['tgl2']):$tgl2=tanggalsystem($_POST['tgl2']);
$_POST['tgl_1']==''?$tgl_1=tanggalsystem($_GET['tgl_1']):$tgl_1=tanggalsystem($_POST['tgl_1']);
$_POST['tgl_2']==''?$tgl_2=tanggalsystem($_GET['tgl_2']):$tgl_2=tanggalsystem($_POST['tgl_2']);
$_POST['periode']==''?$periodeGaji=$_GET['periode']:$periodeGaji=$_POST['periode'];
$_POST['periode']==''?$periode=explode('-',$_GET['periode']):$periode=explode('-',$_POST['periode']);
$_POST['kdUnit']==''?$kdUnit=$_GET['kdUnit']:$kdUnit=$_POST['kdUnit'];
$_POST['idKry']==''?$idKry=$_GET['idKry']:$idKry=$_POST['idKry'];
$_POST['tipeKary']==''?$tipeKary=$_GET['tipeKary']:$tipeKary=$_POST['tipeKary'];
$_POST['sistemGaji']==''?$sistemGaji=$_GET['sistemGaji']:$sistemGaji=$_POST['sistemGaji'];
//echo $sistemGaji;

function dates_inbetween($date1, $date2){

    $day = 60*60*24;

    $date1 = strtotime($date1);
    $date2 = strtotime($date2);

    $days_diff = round(($date2 - $date1)/$day); // Unix time difference devided by 1 day to get total days in between

    $dates_array = array();
    $dates_array[] = date('Y-m-d',$date1);

    for($x = 1; $x < $days_diff; $x++){
        $dates_array[] = date('Y-m-d',($date1+($day*$x)));
    }

    $dates_array[] = date('Y-m-d',$date2);
    if($date1==$date2){
        $dates_array = array();
        $dates_array[] = date('Y-m-d',$date1);        
    }
    return $dates_array;
}

//ambil query untuk data karyawan
        if($kdOrg!='')
        {
                $kodeOrg=$kdOrg;
                if($_SESSION['empl']['tipelokasitugas']=='HOLDING'||$_SESSION['empl']['tipelokasitugas']=='KANWIL')
                {
                        $where="  lokasitugas in ('".$kodeOrg."')";
                        if ($kdOrg=='PLASMA')
                            $where="  lokasitugas in (select kodeorganisasi from ".$dbname.".organisasi where length(kodeorganisasi)=4 and namaorganisasi like '%plasma%')";
                        if($afdId!='')
                        {			
                            $where="  subbagian='".$afdId."'";		
                        }

                }
                else
                {
                        if(strlen($kodeOrg)>4)
                        {			
                                $where="  subbagian='".$kodeOrg."'";		
                        }
                        else
                        {
                                $where="  lokasitugas='".$kodeOrg."' and (subbagian='0' or subbagian is null or subbagian='')";
                        }
                }
        }
        else
        {
                $kodeOrg=$_SESSION['empl']['lokasitugas'];
                $where="  lokasitugas='".$kodeOrg."'";
        }
        if($tipeKary!='')
        {
            $where.=" and tipekaryawan='".$tipeKary."'";
            $sortTkPrestasi="and b.tipekaryawan='".$tipeKary."'";
        }
        if($sistemGaji=='All')$wherez="";        
        if($sistemGaji=='Bulanan')$wherez=" and sistemgaji = 'Bulanan'";        
        if($sistemGaji=='Harian')$wherez=" and sistemgaji = 'Harian'";        


        if($kdOrg=='')
        {
            $kdOrg=$_SESSION['empl']['lokasitugas'];
        }
        else
        {
           $kdOrg=$kdOrg;
        }
        
        
        
        
        
$sGetKary="select a.karyawanid,a.nik,b.namajabatan,a.namakaryawan,subbagian from ".$dbname.".datakaryawan a 
           left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan where
           ".$where." ".$wherez." order by namakaryawan asc";  
//echo $sGetKary;
  //echo $sGetKary; exit;

$rGetkary=fetchData($sGetKary);
foreach($rGetkary as $row => $kar)
{
   // $resData[$kar['karyawanid']][]=$kar['karyawanid'];
    $namakar[$kar['karyawanid']]=$kar['namakaryawan'];
    $nikkar[$kar['karyawanid']]=$kar['nik'];
    $nmJabatan[$kar['karyawanid']]=$kar['namajabatan'];
    $sbgnb[$kar['karyawanid']]=$kar['subbagian'];
}  
if($_SESSION['empl']['tipelokasitugas']=='HOLDING'||$_SESSION['empl']['tipelokasitugas']=='KANWIL')
{
    $dimanaPnjng=" substring(kodeorg,1,4)='".$kodeOrg."'";
    $dimanaPnjng2=" substring(kodeorg,1,4)='".$kodeOrg."'";
    $dimanaPnjng3=" substr(b.kodeorg,1,4)='".$kodeOrg."'";
    if ($_SESSION['empl']['tipelokasitugas']=='HOLDING' and $kodeOrg=='PLASMA'){
        $dimanaPnjng=" substring(kodeorg,1,4) in (select kodeorganisasi from ".$dbname.".organisasi where length(kodeorganisasi)=4 and namaorganisasi like '%plasma%')";
        $dimanaPnjng2=" substring(kodeorg,1,4) in (select kodeorganisasi from ".$dbname.".organisasi where length(kodeorganisasi)=4 and namaorganisasi like '%plasma%')";
        $dimanaPnjng3=" substr(b.kodeorg,1,4) in (select kodeorganisasi from ".$dbname.".organisasi where length(kodeorganisasi)=4 and namaorganisasi like '%plasma%')";
    }
    if($afdId!='')
    {
        $dimanaPnjng=" kodeorg like '".substr($afdId,0,4)."%'";
        $dimanaPnjng2=" substring(kodeorg,1,4)='".substr($afdId,0,4)."'";
        $dimanaPnjng3=" substr(b.kodeorg,1,4)='".substr($afdId,0,4)."'";
    }
}
else
{
    if(strlen($kodeOrg)>4)
    {
        $dimanaPnjng=" kodeorg='".$kodeOrg."'";
        $dimanaPnjng2=" substring(kodeorg,1,4)='".substr($kodeOrg,0,4)."'";
        $dimanaPnjng3=" substr(b.kodeorg,1,4)='".substr($kodeOrg,0,4)."'";
    }
    else
    {
        $dimanaPnjng=" substring(kodeorg,1,4)='".substr($kodeOrg,0,4)."'";
        $dimanaPnjng2=" substring(kodeorg,1,4)='".substr($kodeOrg,0,4)."'";
        $dimanaPnjng3=" substr(b.kodeorg,1,4)='".substr($kodeOrg,0,4)."'";
    }
}

switch($proses)
{
        case'preview':
        if(($tgl_1!='')&&($tgl_2!=''))
        {
                $tgl1=$tgl_1;
                $tgl2=$tgl_2;
        }

            $test = dates_inbetween($tgl1, $tgl2);
        if(($tgl2=="")&&($tgl1==""))
        {
                echo"warning: Both period required";
                exit();
        }

        $jmlHari=count($test);
        //cek max hari inputan
        if($jmlHari>40)
        {
                echo"warning: Invalid period range";
                exit();
        }
        $sAbsen="select kodeabsen from ".$dbname.".sdm_5absensi order by kodeabsen";
        $qAbsen=mysql_query($sAbsen) or die(mysql_error());
        $jmAbsen=mysql_num_rows($qAbsen);
        $colSpan=intval($jmAbsen)+2;
        $tbw=1820;
        echo"<div style='width:".$tbw."px;display:fixed;'><table cellspacing=1 border=0 style='width:100%'>
        <thead class=rowheader>
        <tr>
        <td width=30>No</td>
        <td width=150>".$_SESSION['lang']['nama']."</td>
        <td width=70>".$_SESSION['lang']['nik']."</td>
        <td width=80>".$_SESSION['lang']['jabatan']."</td>
        <td width=55>".$_SESSION['lang']['subunit']."</td>";
        $klmpkAbsn=array();
        $w=27;
        foreach($test as $ar => $isi)
        {
                $qwe=date('D', strtotime($isi));
                echo"<td width=".$w." align=center>";
                if($qwe=='Sun')echo"<font color=red>".substr($isi,8,2)."</font>"; else echo(substr($isi,8,2)); 
                echo"</td>";
        }
        while($rKet=mysql_fetch_assoc($qAbsen))
        {
                $klmpkAbsn[]=$rKet;
                echo"<td width=".$w." align=center>".$rKet['kodeabsen']."</td>";
        }
        echo"
        <td width=35>".$_SESSION['lang']['total']."</td></tr></thead>
        <tbody></tbody></table></div>";

        $resData[]=array();
        $hasilAbsn[]=array();
        //get karyawan

                        /*$sAbsn="select absensi,tanggal,karyawanid,kodeorg,catu from ".$dbname.".sdm_absensidt 
                            where tanggal between  '".$tgl1."' and '".$tgl2."' and ".$dimanaPnjng."";
                          //exit("Error".$sAbsn);
                        $rAbsn=fetchData($sAbsn);
                        foreach ($rAbsn as $absnBrs =>$resAbsn)
                        {
                                if(!is_null($resAbsn['absensi']))
                                {
                                        $hasilAbsn[$resAbsn['karyawanid']][$resAbsn['tanggal']][]=array(
                'absensi'=>$resAbsn['absensi']);
                                        $notran[$resAbsn['karyawanid']][$resAbsn['tanggal']].='ABSENSI:'.$resAbsn['kodeorg'].'__';
                                	$resData[$resAbsn['karyawanid']][]=$resAbsn['karyawanid'];
 					$catuBerasStat[$resAbsn['karyawanid']][$resAbsn['tanggal']]=$resAbsn['catu'];
                                }

                        }*/

                        $sKehadiran="select absensi,tanggal,karyawanid,notransaksi from ".$dbname.".kebun_kehadiran_vw 
                            where tanggal between  '".$tgl1."' and '".$tgl2."' and ".$dimanaPnjng2;
//                        if ($_SESSION['empl']['regional']!='KALIMANTAN'){
//                            $sKehadiran.=" and ".$dimanaPnjng2;
//                        }
                        //echo $sKehadiran;
                        $rkehadiran=fetchData($sKehadiran);
                        foreach ($rkehadiran as $khdrnBrs =>$resKhdrn)
                        {	
                                if($resKhdrn['absensi']!='')
                                {
                                        $hasilAbsn[$resKhdrn['karyawanid']][$resKhdrn['tanggal']][]=array(
                          'absensi'=>$resKhdrn['absensi']);
                                        $notran[$resKhdrn['karyawanid']][$resKhdrn['tanggal']].='BKM:'.$resKhdrn['notransaksi'].'__';
                                        $resData[$resKhdrn['karyawanid']][]=$resKhdrn['karyawanid'];
                                }

                        }
                       // $sPrestasi="select b.tanggal,a.jumlahhk,a.nik,a.notransaksi from ".$dbname.".kebun_prestasi a left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi 
                         //   where b.notransaksi like '%PNN%' and ".$dimanaPnjng3." and b.tanggal between '".$tgl1."' and '".$tgl2."'";
                        
                        
                      $sPrestasi="select a.tanggal,a.jumlahhk,a.karyawanid as nik,a.notransaksi from ".$dbname.".kebun_prestasi_vw a left join 
                      ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                      where  (b.tanggalkeluar>='".$tgl1."' or b.tanggalkeluar='0000-00-00') 
                      and   unit in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')    
                      and b.alokasi=0 and a.tanggal between  '".$tgl1."' and '".$tgl2."'  ".$sortTkPrestasi."     
                      order by tanggal";
                      //echo $sPrestasi;
                      
                        $rPrestasi=fetchData($sPrestasi);
                        foreach ($rPrestasi as $presBrs =>$resPres)
                        {

                                        $hasilAbsn[$resPres['nik']][$resPres['tanggal']][]=array(
                                        'absensi'=>'H');
                                        $notran[$resPres['nik']][$resPres['tanggal']].='BKM:'.$resPres['notransaksi'].'__';
                                        $resData[$resPres['nik']][]=$resPres['nik'];

                        } 

// ambil pengawas                        
$dzstr="SELECT tanggal,nikmandor,a.notransaksi FROM ".$dbname.".kebun_aktifitas a
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
    where a.tanggal between '".$tgl1."' and '".$tgl2."' and ".$dimanaPnjng3." and c.namakaryawan is not NULL
    union select tanggal,nikmandor1,a.notransaksi FROM ".$dbname.".kebun_aktifitas a 
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.nikmandor1=c.karyawanid
    where a.tanggal between '".$tgl1."' and '".$tgl2."' and ".$dimanaPnjng3." and c.namakaryawan is not NULL";
//echo $dzstr;
//exit("Error".$dzstr);
$dzres=mysql_query($dzstr);
while($dzbar=mysql_fetch_object($dzres))
{
    $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][]=array(
    'absensi'=>'H');
    $notran[$dzbar->nikmandor][$dzbar->tanggal].='BKM:'.$dzbar->notransaksi.'__';
    $resData[$dzbar->nikmandor][]=$dzbar->nikmandor;
}

// ambil administrasi                       
$dzstr="SELECT tanggal,nikmandor,a.notransaksi FROM ".$dbname.".kebun_aktifitas a
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
    where a.tanggal between '".$tgl1."' and '".$tgl2."' and ".$dimanaPnjng3." and c.namakaryawan is not NULL
    union select tanggal,keranimuat,a.notransaksi FROM ".$dbname.".kebun_aktifitas a 
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.keranimuat=c.karyawanid
    where a.tanggal between '".$tgl1."' and '".$tgl2."' and ".$dimanaPnjng3." and c.namakaryawan is not NULL";
//exit("Error".$dzstr);
$dzres=mysql_query($dzstr);
while($dzbar=mysql_fetch_object($dzres))
{
    $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][]=array(
    'absensi'=>'H');
    $notran[$dzbar->nikmandor][$dzbar->tanggal].='BKM:'.$dzbar->notransaksi.'__';
    $resData[$dzbar->nikmandor][]=$dzbar->nikmandor;
}



// ambil recorder                    
$dzstr="SELECT tanggal,nikmandor,a.notransaksi FROM ".$dbname.".kebun_aktifitas a
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
    where a.tanggal between '".$tgl1."' and '".$tgl2."' and ".$dimanaPnjng3." and c.namakaryawan is not NULL
    union select tanggal,nikasisten,a.notransaksi FROM ".$dbname.".kebun_aktifitas a 
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.keranimuat=c.karyawanid
    where a.tanggal between '".$tgl1."' and '".$tgl2."' and ".$dimanaPnjng3." and c.namakaryawan is not NULL";
//exit("Error".$dzstr);
$dzres=mysql_query($dzstr);
while($dzbar=mysql_fetch_object($dzres))
{
    $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][]=array(
    'absensi'=>'H');
    $notran[$dzbar->nikmandor][$dzbar->tanggal].='BKM:'.$dzbar->notransaksi.'__';
    $resData[$dzbar->nikmandor][]=$dzbar->nikmandor;
}



// ambil traksi                       
/*$dzstr="SELECT a.tanggal,idkaryawan, a.notransaksi FROM ".$dbname.".vhc_runhk a
        left join ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
        where a.tanggal between '".$tgl1."' and '".$tgl2."' and notransaksi like '%".substr($kodeOrg,0,4)."%'
        and ".$where."
    ";*/
$dzstr="select a.idkaryawan,a.tanggal,a.notransaksi
                  from ".$dbname.".vhc_runhk_vw a left join 
                 ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
                  where (b.tanggalkeluar>='".$tgl1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
                  and a.tanggal>='".$tgl1."' and a.tanggal<='".$tgl2."'   and ".$where."  
                  order by tanggal";
//echo $dzstr.____.$strux;
 //exit("Error".$dzstr);
$dzres=mysql_query($dzstr);
while($dzbar=mysql_fetch_object($dzres))
{
    $hasilAbsn[$dzbar->idkaryawan][$dzbar->tanggal][]=array(
    'absensi'=>'H');    
    $notran[$dzbar->idkaryawan][$dzbar->tanggal].='TRAKSI:'.$dzbar->notransaksi.'__';
    $resData[$dzbar->idkaryawan][]=$dzbar->idkaryawan;
}


/*$sAbsn="select absensi,tanggal,karyawanid,kodeorg,catu from ".$dbname.".sdm_absensidt 
                            where tanggal between  '".$tgl1."' and '".$tgl2."' and ".$dimanaPnjng."";*/
$sAbsn="select a.karyawanid,tanggal,a.absensi,kodeorg,catu
                  from ".$dbname.".sdm_absensidt a left join 
                 ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                  where  (b.tanggalkeluar>='".$tgl1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
                  and a.tanggal>='".$tgl1."' and a.tanggal<='".$tgl2."' and ".$where."
                  order by tanggal";  
//echo $sAbsn;

                          //exit("Error".$sAbsn);
                        $rAbsn=fetchData($sAbsn);
                        foreach ($rAbsn as $absnBrs =>$resAbsn)
                        {
                                if(!is_null($resAbsn['absensi']))
                                {
                                        //jika S1, C, M, P1, P2 maka hapus yg lainnya
                                        if ($resAbsn['absensi']=='S1' || $resAbsn['absensi']=='C' || $resAbsn['absensi']=='M' 
                                                || $resAbsn['absensi']=='P1' || $resAbsn['absensi']=='P2'){
                                            unset($hasilAbsn[$resAbsn['karyawanid']][$resAbsn['tanggal']]);
                                            unset($notran[$resAbsn['karyawanid']][$resAbsn['tanggal']]);
                                            unset($resData[$resAbsn['karyawanid']]);
                                        }

                                        $hasilAbsn[$resAbsn['karyawanid']][$resAbsn['tanggal']][]=array(
                'absensi'=>$resAbsn['absensi']);
                                        $notran[$resAbsn['karyawanid']][$resAbsn['tanggal']].='ABSENSI:'.$resAbsn['kodeorg'].'__';
                                	$resData[$resAbsn['karyawanid']][]=$resAbsn['karyawanid'];
 					$catuBerasStat[$resAbsn['karyawanid']][$resAbsn['tanggal']]=$resAbsn['catu'];

                                }

                        }


function kirimnama($nama) // buat ngirim nama lewat javascript. spasi diganti __
{
    $qwe=explode(' ',$nama);
    foreach($qwe as $kyu){
        $balikin.=$kyu.'__';
    }    
    return $balikin;
}

function removeduplicate($notransaksi) // buat ngilangin nomor transaksi yang dobel
{
    $notransaksi=substr($notransaksi,0,-2);    
    $qwe=explode('__',$notransaksi);
    foreach($qwe as $kyu){
        $tumpuk[$kyu]=$kyu;
    }    
    foreach($tumpuk as $tumpz){
        $balikin.=$tumpz.'__';
    }    

    return $balikin;
}

        $brt=array();
        $lmit=count($klmpkAbsn);
        $a=0;
        echo "<div style='overflow:scroll;height:250px;width:".($tbw+15)."px;display:fixed;'>
             <table cellspacing=1 border=0 class=sortable style='width:100%'>
             <thead class=rowheader></thead><tbody>";
        foreach($resData as $hslBrs => $hslAkhir)
        {	
                        if($hslAkhir[0]!='' and $namakar[$hslAkhir[0]]!='')
                        {
                                $no+=1;
                                echo"<tr class=rowcontent><td width=30>".$no."</td>";
                                echo"
                                <td width=150>".$namakar[$hslAkhir[0]]."</td>
                                <td width=70>".$nikkar[$hslAkhir[0]]."</td>
                                <td width=80>".$nmJabatan[$hslAkhir[0]]."</td>
                                <td width=50>".$sbgnb[$hslAkhir[0]]."</td>
                                ";
                                foreach($test as $barisTgl =>$isiTgl)
                                {
                                    if($hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi']!='H')
                                    {
                                        echo"<td width=".$w." title='Detil pada tanggal ".tanggalnormal($isiTgl)."' style=\"cursor: pointer\" onclick=showpopup('".$hslAkhir[0]."','".kirimnama($namakar[$hslAkhir[0]])."','".tanggalnormal($isiTgl)."','".removeduplicate($notran[$hslAkhir[0]][$isiTgl])."',event)><font color=red>".$hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi']."</font></td>";
                                    }
                                    else
                                    {
				  $bgdt="";
				   if(count($catuBerasStat[$hslAkhir[0]][$isiTgl])!=0){
			            if($catuBerasStat[$hslAkhir[0]][$isiTgl]==0){
				          $bgdt="bgcolor=yellow";
					}
			          }
                                        echo"<td width=".$w." ".$bgdt." title='Detil pada tanggal ".tanggalnormal($isiTgl)."' style=\"cursor: pointer\" onclick=showpopup('".$hslAkhir[0]."','".kirimnama($namakar[$hslAkhir[0]])."','".tanggalnormal($isiTgl)."','".removeduplicate($notran[$hslAkhir[0]][$isiTgl])."',event)>".$hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi']."</td>";
                                        $totTgl[$isiTgl]+=1;
                                    }
                                        $brt[$hslAkhir[0]][$hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi']]+=1;

                                }

                                foreach($klmpkAbsn as $brsKet =>$hslKet)
                                {
                                    if($hslKet['kodeabsen']!='H')
                                    {
                                        echo"<td width=".$w." align=right><font color=red>".$brt[$hslAkhir[0]][$hslKet['kodeabsen']]."</font></td>";	
                                    }
                                    else
                                    {
                                        echo"<td width=6  align=right>".$brt[$hslAkhir[0]][$hslKet['kodeabsen']]."</td>";	
                                    }
                                        $subtot[$hslAkhir[0]]['total']+=$brt[$hslAkhir[0]][$hslKet['kodeabsen']];
                                }	
                                echo"<td width=35 align=right>".$subtot[$hslAkhir[0]]['total']."</td>";
                                $subtot['total']=0;
                                echo"</tr>";
                        }	
        }
        $coldt=count($klmpkAbsn);
        echo"<tr class=rowcontent><td colspan=5>".$_SESSION['total']." ".$_SESSION['absensi']."</td>";
        foreach($test as $barisTgl =>$isiTgl)
        {
            echo "<td>".$totTgl[$isiTgl]."</td>";
        }
        echo"<td colspan=".($coldt+1).">&nbsp;</td></tr>";
        echo"</tbody><tfoot></tfoot></table></div>";
        break;
        case'pdf':


        $test = dates_inbetween($tgl1, $tgl2);

        if(($tgl2=="")&&($tgl1==""))
        {
                echo"warning: Both period required";
                exit();
        }

        $jmlHari=count($test);
        //cek max hari inputan
        if($jmlHari>40)
        {
                echo"warning:Invalid period range ".$jmlHari;
                exit();
        }
        //ambil query untuk tanggal kehadiran

        $sAbsen="select kodeabsen from ".$dbname.".sdm_5absensi order by kodeabsen";
        $qAbsen=mysql_query($sAbsen) or die(mysql_error());
        $jmAbsen=mysql_num_rows($qAbsen);
        $colSpan=intval($jmAbsen)+2;

        //+++++++++++++++++++++++++++++++++++++++++++++++++++++
//create Header

class PDF extends FPDF
        {
            function Header() {
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
                                global $period;
                                global $periode;
                                global $kdOrg;
                                global $kdeOrg;
                                global $tgl1;
                                global $tgl2;
                                global $where;
                                global $jmlHari;
                                global $test;
                                global $klmpkAbsn;
                                global $tipeKary;
                                global $sistemGaji;
                                global $dimanaPnjng;
                                global $afdId;
                                global $dimanaPnjng2;
                                global $dimanaPnjng3;


                                $jmlHari=$jmlHari*1.5;
                                $cols=247.5;
                            # Alamat & No Telp
                $query = selectQuery($dbname,'organisasi','alamat,telepon',
                    "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                $orgData = fetchData($query);

                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 20;
                if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
                $this->Image($path,$this->lMargin,$this->tMargin,70);	
                $this->SetFont('Arial','B',9);
                $this->SetFillColor(255,255,255);	
                $this->SetX(100);   
                $this->Cell($width-100,$height,$_SESSION['org']['namaorganisasi'],0,1,'L');	 
                $this->SetX(100); 		
                $this->Cell($width-100,$height,$orgData[0]['alamat'],0,1,'L');	
                $this->SetX(100); 			
                $this->Cell($width-100,$height,"Tel: ".$orgData[0]['telepon'],0,1,'L');	
                $this->Line($this->lMargin,$this->tMargin+($height*4),
                $this->lMargin+$width,$this->tMargin+($height*4));
                $this->Ln();

                $this->SetFont('Arial','B',10);
                                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['rkpAbsen']." ".$sistemGaji,'',0,'L');
                                $this->Ln();
                                $this->Ln();

                                $this->Cell($width,$height,strtoupper($_SESSION['lang']['rkpAbsen']),'',0,'C');
                                $this->Ln();
                                $this->Cell($width,$height,strtoupper($_SESSION['lang']['periode'])." :". tanggalnormal($tgl1)." s.d. ". tanggalnormal($tgl2),'',0,'C');
                                $this->Ln();
                                $this->Ln();
                $this->SetFont('Arial','B',7);
                $this->SetFillColor(220,220,220);
                                $this->Cell(3/100*$width,$height,'No',1,0,'C',1);
                                $this->Cell(13/100*$width,$height,$_SESSION['lang']['nama'],1,0,'C',1);		
                                $this->Cell(10/100*$width,$height,$_SESSION['lang']['jabatan'],1,0,'C',1);	

                                //$this->Cell($jmlHari/100*$width,$height-10,$_SESSION['lang']['tanggal'],1,0,'C',1);
                                $this->GetX();
                                $this->SetY($this->GetY());

                                $this->SetX($this->GetX()+$cols);

                                foreach($test as $ar => $isi)
                                {
                                        $this->Cell(1.5/100*$width,$height,substr($isi,8,2),1,0,'C',1);	
                                        $akhirX=$this->GetX();
                                }	
                                $this->SetY($this->GetY());
                                $this->SetX($akhirX);
                                $sAbsen="select kodeabsen from ".$dbname.".sdm_5absensi order by kodeabsen";
                                $qAbsen=mysql_query($sAbsen) or die(mysql_error());
                                while($rAbsen=mysql_fetch_assoc($qAbsen))
                                {
                                        $klmpkAbsn[]=$rAbsen;
                                        $this->Cell(2/100*$width,$height,$rAbsen['kodeabsen'],1,0,'C',1);
                                }
                                $this->Cell(5/100*$width,$height,$_SESSION['lang']['total'],1,1,'C',1);
            }

            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
        }
        $pdf=new PDF('L','pt','Legal');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
                $pdf->AddPage();
                $pdf->SetFillColor(255,255,255);
                $pdf->SetFont('Arial','',7);
                $subtot=array();
                //ambil query untuk data karyawan
        if($kdOrg!='')
        {
                $kodeOrg=$kdOrg;
                if($_SESSION['empl']['tipelokasitugas']=='HOLDING'||$_SESSION['empl']['tipelokasitugas']=='KANWIL')
                {
                        $where="  lokasitugas in ('".$kodeOrg."')";
                        if($afdId!='')
                        {			
                            $where="  subbagian='".$afdId."'";		
                        }

                }
                else
                {
                        if(strlen($kdOrg)>4)
                        {			
                                $where="  subbagian='".$kdOrg."'";		
                        }
                        else
                        {
                                $where="  lokasitugas='".$kdOrg."' and (subbagian='0' or subbagian is null or subbagian='')";
                        }
                }
        }
        else
        {
                $kodeOrg=$_SESSION['empl']['lokasitugas'];
                $where="  lokasitugas='".$kodeOrg."'";
        }
        if($tipeKary!='')
        {
            $where.=" and tipekaryawan='".$tipeKary."'";
        }
        if($sistemGaji=='All')$wherez="";        
        if($sistemGaji=='Bulanan')$wherez=" and sistemgaji = 'Bulanan'";        
        if($sistemGaji=='Harian')$wherez=" and sistemgaji = 'Harian'";        


        $sGetKary="select a.karyawanid,b.namajabatan,a.namakaryawan from ".$dbname.".datakaryawan a 
                   left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan where
                   ".$where." ".$wherez." order by namakaryawan asc";
        //exit("Error:".$sGetKary);
        $rGetkary=fetchData($sGetKary);
        $namakar=Array();
        $nmJabatan=Array();
        foreach($rGetkary as $row => $kar)
        {
           // $resData[$kar['karyawanid']][]=$kar['karyawanid'];
            $namakar[$kar['karyawanid']]=$kar['namakaryawan'];
            $nmJabatan[$kar['karyawanid']]=$kar['namajabatan'];
        }
        

                        $sKehadiran="select absensi,tanggal,karyawanid from ".$dbname.".kebun_kehadiran_vw 
                            where tanggal between  '".$tgl1."' and '".$tgl2."' and ".$dimanaPnjng2."";
                        //exit("Error".$sKehadiran);
                        $rkehadiran=fetchData($sKehadiran);
                        foreach ($rkehadiran as $khdrnBrs =>$resKhdrn)
                        {	
                                if($resKhdrn['absensi']!='')
                                {
                                        $hasilAbsn[$resKhdrn['karyawanid']][$resKhdrn['tanggal']][]=array(
                          'absensi'=>$resKhdrn['absensi']);
                                        $resData[$resKhdrn['karyawanid']][]=$resKhdrn['karyawanid'];
                                }

                        }
                        //$sPrestasi="select b.tanggal,a.jumlahhk,a.nik from ".$dbname.".kebun_prestasi a left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi 
                          //  where b.notransaksi like '%PNN%' and ".$dimanaPnjng3." and b.tanggal between '".$tgl1."' and '".$tgl2."'";
                        
                        $sPrestasi="select a.tanggal,a.jumlahhk,a.karyawanid as nik,a.notransaksi from ".$dbname.".kebun_prestasi_vw a left join 
                      ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                      where  (b.tanggalkeluar>='".$tgl1."' or b.tanggalkeluar='0000-00-00') 
                      and   unit in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')    
                      and b.alokasi=0 and a.tanggal>='".$tgl1."' and a.tanggal<='".$tgl2."'  ".$sortTkPrestasi."     
                      order by tanggal";

                        //exit("Error".$sPrestasi);
                        $rPrestasi=fetchData($sPrestasi);
                        foreach ($rPrestasi as $presBrs =>$resPres)
                        {

                                        $hasilAbsn[$resPres['nik']][$resPres['tanggal']][]=array(
                                        'absensi'=>'H');
                                        $resData[$resPres['nik']][]=$resPres['nik'];

                        } 

// ambil pengawas                        
$dzstr="SELECT tanggal,nikmandor FROM ".$dbname.".kebun_aktifitas a
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
    where a.tanggal between '".$tgl1."' and '".$tgl2."' and ".$dimanaPnjng3." and c.namakaryawan is not NULL
    union select tanggal,nikmandor1 FROM ".$dbname.".kebun_aktifitas a 
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.nikmandor1=c.karyawanid
    where a.tanggal between '".$tgl1."' and '".$tgl2."' and ".$dimanaPnjng3." and c.namakaryawan is not NULL";
//exit("Error".$dzstr);
$dzres=mysql_query($dzstr);
while($dzbar=mysql_fetch_object($dzres))
{
    $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][]=array(
    'absensi'=>'H');
    $resData[$dzbar->nikmandor][]=$dzbar->nikmandor;
}




#recorder  
$dzstr="SELECT tanggal,nikmandor FROM ".$dbname.".kebun_aktifitas a
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
    where a.tanggal between '".$tgl1."' and '".$tgl2."' and ".$dimanaPnjng3." and c.namakaryawan is not NULL
    union select tanggal,nikasisten FROM ".$dbname.".kebun_aktifitas a 
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.nikmandor1=c.karyawanid
    where a.tanggal between '".$tgl1."' and '".$tgl2."' and ".$dimanaPnjng3." and c.namakaryawan is not NULL";
//exit("Error".$dzstr);
$dzres=mysql_query($dzstr);
while($dzbar=mysql_fetch_object($dzres))
{
    $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][]=array(
    'absensi'=>'H');
    $resData[$dzbar->nikmandor][]=$dzbar->nikmandor;
}






// ambil administrasi                       
$dzstr="SELECT tanggal,nikmandor FROM ".$dbname.".kebun_aktifitas a
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
    where a.tanggal between '".$tgl1."' and '".$tgl2."' and ".$dimanaPnjng3." and c.namakaryawan is not NULL
    union select tanggal,keranimuat FROM ".$dbname.".kebun_aktifitas a 
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.keranimuat=c.karyawanid
    where a.tanggal between '".$tgl1."' and '".$tgl2."' and ".$dimanaPnjng3." and c.namakaryawan is not NULL";
 //exit("Error".$dzstr);
$dzres=mysql_query($dzstr);
while($dzbar=mysql_fetch_object($dzres))
{
    $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][]=array(
    'absensi'=>'H');
    $resData[$dzbar->nikmandor][]=$dzbar->nikmandor;
}

// ambil traksi                       
/*$dzstr="SELECT a.tanggal,idkaryawan FROM ".$dbname.".vhc_runhk a
        left join ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
        where a.tanggal between '".$tgl1."' and '".$tgl2."' and notransaksi like '%".$kodeOrg."%'
        and ".$where."
    ";*/

$dzstr="select a.idkaryawan,a.tanggal,a.notransaksi
                  from ".$dbname.".vhc_runhk_vw a left join 
                 ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
                  where  (b.tanggalkeluar>='".$tgl1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
                  and a.tanggal>='".$tgl1."' and a.tanggal<='".$tgl2."'   and ".$where."  
                  order by tanggal";

//exit("Error".$dzstr);
$dzres=mysql_query($dzstr);
while($dzbar=mysql_fetch_object($dzres))
{
    $hasilAbsn[$dzbar->idkaryawan][$dzbar->tanggal][]=array(
    'absensi'=>'H');
    $resData[$dzbar->idkaryawan][]=$dzbar->idkaryawan;
}



/*$sAbsn="select absensi,tanggal,karyawanid from ".$dbname.".sdm_absensidt 
                            where tanggal between  '".$tgl1."' and '".$tgl2."' and ".$dimanaPnjng."";*/


$sAbsn="select a.karyawanid,tanggal,a.absensi,kodeorg,catu
                  from ".$dbname.".sdm_absensidt a left join 
                 ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                  where  (b.tanggalkeluar>='".$tgl1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
                  and a.tanggal>='".$tgl1."' and a.tanggal<='".$tgl2."' and ".$where."
                  order by tanggal"; 


                        //exit("Error".$sAbsn);
                        $rAbsn=fetchData($sAbsn);
                        foreach ($rAbsn as $absnBrs =>$resAbsn)
                        {
                                if(!is_null($resAbsn['absensi']))
                                {
                                        //jika S1, C, M, P1, P2 maka hapus yg lainnya
                                        if ($resAbsn['absensi']=='S1' || $resAbsn['absensi']=='C' || $resAbsn['absensi']=='M' 
                                                || $resAbsn['absensi']=='P1' || $resAbsn['absensi']=='P2'){
                                            unset($hasilAbsn[$resAbsn['karyawanid']][$resAbsn['tanggal']]);
                                            unset($notran[$resAbsn['karyawanid']][$resAbsn['tanggal']]);
                                            unset($resData[$resAbsn['karyawanid']]);
                                        }
                                        
                                        $hasilAbsn[$resAbsn['karyawanid']][$resAbsn['tanggal']][]=array(
                'absensi'=>$resAbsn['absensi']);
                                $resData[$resAbsn['karyawanid']][]=$resAbsn['karyawanid'];
                                }

                        }


        $brt=array();
        $lmit=count($klmpkAbsn);
        $a=0;
        foreach($resData as $hslBrs => $hslAkhir)
        {	
                        if($hslAkhir[0]!=''  and $namakar[$hslAkhir[0]]!='')
                        {
                                $no+=1;
                                $pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);
                                $pdf->Cell(13/100*$width,$height,strtoupper($namakar[$hslAkhir[0]]),1,0,'L',1);		
                                $pdf->Cell(10/100*$width,$height,strtoupper($nmJabatan[$hslAkhir[0]]),1,0,'L',1);	
                                foreach($test as $barisTgl =>$isiTgl)
                                {
                                        $pdf->Cell(1.5/100*$width,$height,$hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi'],1,0,'C',1);	
                                        $akhirX=$pdf->GetX();
                                        $brt[$hslAkhir[0]][$hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi']]+=1;
                                }
                                $a=0;
                                for(;$a<$lmit;$a++)
                                {
                                        $pdf->Cell(2/100*$width,$height,$brt[$hslAkhir[0]][$klmpkAbsn[$a]['kodeabsen']],1,0,'C',1);	
                                        $subtot[$hslAkhir[0]]['total']+=$brt[$hslAkhir[0]][$klmpkAbsn[$a]['kodeabsen']];
                                }	
                                $pdf->Cell(5/100*$width,$height,$subtot[$hslAkhir[0]]['total'],1,1,'R',1);
                                $subtot[$hslAkhir[0]]['total']=0;
                        }	

        }


        $pdf->Output();

        break;
        case'excel':

        $test = dates_inbetween($tgl1, $tgl2);
        if(($tgl2=="")&&($tgl1==""))
        {
                echo"warning: Both period required";
                exit();
        }

        $jmlHari=count($test);
        //cek max hari inputan
        if($jmlHari>40)
        {
                echo"warning: Invalid period range";
                exit();
        }
        $sAbsen="select kodeabsen from ".$dbname.".sdm_5absensi order by kodeabsen";
        $qAbsen=mysql_query($sAbsen) or die(mysql_error());
        $jmAbsen=mysql_num_rows($qAbsen);
        $colSpan=intval($jmAbsen)+2;
        $colatas=$jmlHari+$colSpan+3;
        $stream.="<table border='0'><tr><td colspan='".$colatas."' align=center>".strtoupper($_SESSION['lang']['rkpAbsen'])." ".$sistemGaji."</td></tr>
        <tr><td colspan='".$colatas."' align=center>".strtoupper($_SESSION['lang']['periode'])." :". tanggalnormal($tgl1)." s.d. ". tanggalnormal($tgl2)."</td></tr><tr><td colspan='".$colatas."'>&nbsp;</td></tr></table>";
        $stream.="<table cellspacing='1' border='1' class='sortable'>
        <thead class=rowheader>
        <tr>
        <td bgcolor=#DEDEDE align=center>No</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nama']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nik']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jabatan']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['bagian']."</td>
         <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['subunit']."</td>";
        $klmpkAbsn=array();
        foreach($test as $ar => $isi)
        {
                //exit("Error".$isi);
                $qwe=date('D', strtotime($isi));

                if($qwe=='Sun')
                {
                    $stream.="<td bgcolor=red align=center width=6 align=center><font color=white>".substr($isi,8,2)."</font></td>";
                }
                else
                {
                    $stream.="<td bgcolor=#DEDEDE align=center width=6 align=center>".substr($isi,8,2)."</td>";
                }

        }
        while($rKet=mysql_fetch_assoc($qAbsen))
        {
                $klmpkAbsn[]=$rKet;
                $stream.="<td bgcolor=#DEDEDE align=center width=10px>".$rKet['kodeabsen']."</td>";
        }
        $stream.="
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['total']."</td></tr></thead>
        <tbody>";
        //ambil query untuk data karyawan
              
        $resData[]=array();
        $hasilAbsn[]=array();
        
        
        
      $sGetKary="select a.karyawanid,a.nik,b.namajabatan,a.namakaryawan,subbagian from ".$dbname.".datakaryawan a 
           left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan where
           ".$where." ".$wherez." order by namakaryawan asc";  
        
     // exit("Error:$sGetKary");
      
      
         $namakar=Array();
        $nmJabatan=Array();
        $rGetkary=fetchData($sGetKary);
        foreach($rGetkary as $row => $kar)
    {

          $namakar[$kar['karyawanid']]=$kar['namakaryawan'];
          $nikkar[$kar['karyawanid']]=$kar['nik'];
          $nmJabatan[$kar['karyawanid']]=$kar['namajabatan'];
          $nmBagian[$kar['karyawanid']]=$kar['nama'];
           $sbgnb[$kar['karyawanid']]=$kar['subbagian'];
    }  


                        $sKehadiran="select absensi,tanggal,karyawanid from ".$dbname.".kebun_kehadiran_vw 
                            where tanggal between  '".$tgl1."' and '".$tgl2."' and ".$dimanaPnjng2."";
                        //exit("Error".$sKehadiran);
                        $rkehadiran=fetchData($sKehadiran);
                        foreach ($rkehadiran as $khdrnBrs =>$resKhdrn)
                        {	
                                if($resKhdrn['absensi']!='')
                                {
                                        $hasilAbsn[$resKhdrn['karyawanid']][$resKhdrn['tanggal']][]=array(
                          'absensi'=>$resKhdrn['absensi']);
                                        $resData[$resKhdrn['karyawanid']][]=$resKhdrn['karyawanid'];
                                }

                        }
                        //$sPrestasi="select b.tanggal,a.jumlahhk,a.nik from ".$dbname.".kebun_prestasi a left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi 
                        //    where b.notransaksi like '%PNN%' and ".$dimanaPnjng3." and b.tanggal between '".$tgl1."' and '".$tgl2."'";
                        //exit("Error".$sPrestasi);
                        $sPrestasi="select a.tanggal,a.jumlahhk,a.karyawanid as nik,a.notransaksi from ".$dbname.".kebun_prestasi_vw a left join 
                      ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                      where  (b.tanggalkeluar>='".$tgl1."' or b.tanggalkeluar='0000-00-00') 
                      and   unit in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')    
                      and b.alokasi=0 and a.tanggal>='".$tgl1."' and a.tanggal<='".$tgl2."'  ".$sortTkPrestasi."     
                      order by tanggal";
                        
                        
                        $rPrestasi=fetchData($sPrestasi);
                        foreach ($rPrestasi as $presBrs =>$resPres)
                        {

                                        $hasilAbsn[$resPres['nik']][$resPres['tanggal']][]=array(
                                        'absensi'=>'H');
                                        $resData[$resPres['nik']][]=$resPres['nik'];

                        } 

// ambil pengawas                        
$dzstr="SELECT tanggal,nikmandor FROM ".$dbname.".kebun_aktifitas a
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
    where a.tanggal between '".$tgl1."' and '".$tgl2."' and ".$dimanaPnjng3." and c.namakaryawan is not NULL
    union select tanggal,nikmandor1 FROM ".$dbname.".kebun_aktifitas a 
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.nikmandor1=c.karyawanid
    where a.tanggal between '".$tgl1."' and '".$tgl2."' and ".$dimanaPnjng3." and c.namakaryawan is not NULL";
//exit("Error".$dzstr);
$dzres=mysql_query($dzstr);
while($dzbar=mysql_fetch_object($dzres))
{
    $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][]=array(
    'absensi'=>'H');
    $resData[$dzbar->nikmandor][]=$dzbar->nikmandor;
}


##recorder                    
$dzstr="SELECT tanggal,nikmandor FROM ".$dbname.".kebun_aktifitas a
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
    where a.tanggal between '".$tgl1."' and '".$tgl2."' and ".$dimanaPnjng3." and c.namakaryawan is not NULL
    union select tanggal,nikasisten FROM ".$dbname.".kebun_aktifitas a 
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.nikmandor1=c.karyawanid
    where a.tanggal between '".$tgl1."' and '".$tgl2."' and ".$dimanaPnjng3." and c.namakaryawan is not NULL";
//exit("Error".$dzstr);
$dzres=mysql_query($dzstr);
while($dzbar=mysql_fetch_object($dzres))
{
    $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][]=array(
    'absensi'=>'H');
    $resData[$dzbar->nikmandor][]=$dzbar->nikmandor;
}

// ambil administrasi                       
$dzstr="SELECT tanggal,nikmandor FROM ".$dbname.".kebun_aktifitas a
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
    where a.tanggal between '".$tgl1."' and '".$tgl2."' and ".$dimanaPnjng3." and c.namakaryawan is not NULL
    union select tanggal,keranimuat FROM ".$dbname.".kebun_aktifitas a 
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.keranimuat=c.karyawanid
    where a.tanggal between '".$tgl1."' and '".$tgl2."' and ".$dimanaPnjng3." and c.namakaryawan is not NULL";
 //exit("Error".$dzstr);
$dzres=mysql_query($dzstr);
while($dzbar=mysql_fetch_object($dzres))
{
    $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][]=array(
    'absensi'=>'H');
    $resData[$dzbar->nikmandor][]=$dzbar->nikmandor;
}

// ambil traksi                       
/*$dzstr="SELECT a.tanggal,idkaryawan FROM ".$dbname.".vhc_runhk a
        left join ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
        where a.tanggal between '".$tgl1."' and '".$tgl2."' and notransaksi like '%".$kodeOrg."%'
        and ".$where."
    ";*/


$dzstr="select a.idkaryawan,a.tanggal,a.notransaksi
                  from ".$dbname.".vhc_runhk_vw a left join 
                 ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
                  where  (b.tanggalkeluar>='".$tgl1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
                  and a.tanggal>='".$tgl1."' and a.tanggal<='".$tgl2."'   and ".$where."  
                  order by tanggal";



//exit("Error".$dzstr);
$dzres=mysql_query($dzstr);
while($dzbar=mysql_fetch_object($dzres))
{
    $hasilAbsn[$dzbar->idkaryawan][$dzbar->tanggal][]=array(
    'absensi'=>'H');
    $resData[$dzbar->idkaryawan][]=$dzbar->idkaryawan;
}


/*$sAbsn="select absensi,tanggal,karyawanid,catu from ".$dbname.".sdm_absensidt 
                            where tanggal between  '".$tgl1."' and '".$tgl2."' and ".$dimanaPnjng."";*/


$sAbsn="select a.karyawanid,tanggal,a.absensi,kodeorg,catu
                  from ".$dbname.".sdm_absensidt a left join 
                 ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                  where  (b.tanggalkeluar>='".$tgl1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
                  and a.tanggal>='".$tgl1."' and a.tanggal<='".$tgl2."' and ".$where."
                  order by tanggal"; 


                        //exit("Error".$sAbsn);
                        $rAbsn=fetchData($sAbsn);
                        foreach ($rAbsn as $absnBrs =>$resAbsn)
                        {
                                if(!is_null($resAbsn['absensi']))
                                {
                                        //jika S1, C, M, P1, P2 maka hapus yg lainnya
                                        if ($resAbsn['absensi']=='S1' || $resAbsn['absensi']=='C' || $resAbsn['absensi']=='M' 
                                                || $resAbsn['absensi']=='P1' || $resAbsn['absensi']=='P2'){
                                            unset($hasilAbsn[$resAbsn['karyawanid']][$resAbsn['tanggal']]);
                                            unset($notran[$resAbsn['karyawanid']][$resAbsn['tanggal']]);
                                            unset($resData[$resAbsn['karyawanid']]);
                                        }

                                        $hasilAbsn[$resAbsn['karyawanid']][$resAbsn['tanggal']][]=array(
                'absensi'=>$resAbsn['absensi']);
                                $resData[$resAbsn['karyawanid']][]=$resAbsn['karyawanid'];
				$catuBerasStat[$resAbsn['karyawanid']][$resAbsn['tanggal']]=$resAbsn['catu'];
                                }

                        }



        $brt=array();
        $lmit=count($klmpkAbsn);
        $a=0;
        foreach($resData as $hslBrs => $hslAkhir)
        {	
                        if($hslAkhir[0]!='' and $namakar[$hslAkhir[0]]!='')
                        {
                                $no+=1;
                                $stream.="<tr><td>".$no."</td>";
                                $stream.="
                                <td>".$namakar[$hslAkhir[0]]."</td>
                                <td>'".$nikkar[$hslAkhir[0]]."</td>
                                <td>".$nmJabatan[$hslAkhir[0]]."</td>
                                <td>".$nmBagian[$hslAkhir[0]]."</td>
                                <td>".$sbgnb[$hslAkhir[0]]."</td>
                                ";
                                foreach($test as $barisTgl =>$isiTgl)
                                {
                                    if($hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi']!='H')
                                    {
                                        $stream.="<td><font color=red>".$hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi']."</font></td>";
                                    }
                                    else
                                    {
				  $bgdt="";
				  if(count($catuBerasStat[$hslAkhir[0]][$isiTgl])!=0){
			            if($catuBerasStat[$hslAkhir[0]][$isiTgl]==0){
				          $bgdt="bgcolor=yellow";
					}
			          }
                                        $stream.="<td ".$bgdt.">".$hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi']."</td>";
                                        $totTgl[$isiTgl]+=1;
                                    }
                                        $brt[$hslAkhir[0]][$hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi']]+=1;
                                }

                                foreach($klmpkAbsn as $brsKet =>$hslKet)
                                {
                                    if($hslKet['kodeabsen']!='H')
                                    {
                                        $stream.="<td width=6  align=right><font color=red>".$brt[$hslAkhir[0]][$hslKet['kodeabsen']]."</font></td>";	
                                    }
                                    else
                                    {
                                        $stream.="<td width=6  align=right>".$brt[$hslAkhir[0]][$hslKet['kodeabsen']]."</td>";	
                                    }
                                        $subtot[$hslAkhir[0]]['total']+=$brt[$hslAkhir[0]][$hslKet['kodeabsen']];
                                }	
                                $stream.="<td width=6  align=right>".$subtot[$hslAkhir[0]]['total']."</td>";
                                $subtot['total']=0;
                                $stream.="</tr>";
                        }	
        }
         $coldt=count($klmpkAbsn);
        $stream.="<tr class=rowcontent><td colspan=6>".$_SESSION['lang']['total']['absensi']."</td>";
        foreach($test as $barisTgl =>$isiTgl)
        {
            $stream.= "<td>".$totTgl[$isiTgl]."</td>";
        }
        $stream.="<td colspan=".($coldt+1).">&nbsp;</td></tr>";
        $stream.="</tbody></table>";




                        //echo "warning:".$strx;
                        //=================================================


                        $stream.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
                        if($period!='')
                        {
                                $art=$period;
                                $art=$art[1].$art[0];
                        }
                        if($periode!='')
                        {
                                $art=$periode;
                                $art=$art[1].$art[0];
                        }
                        if($kdeOrg!='')
                        {
                                $kodeOrg=$kdeOrg;
                        }
                        if($kdOrg!='')
                        {
                                $kodeOrg=$kdOrg;
                        }
                        $nop_="RekapAbsen".$art."__".$kodeOrg;
                        if(strlen($stream)>0)
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
                        if(!fwrite($handle,$stream))
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
        case'getTgl':
        if($periode!='')
        {
                $tgl=$periode;
                $tanggal=$tgl[0]."-".$tgl[1];
                $dmna.=" and periode='".$tanggal."'";
        }
        elseif($period!='')
        {
                $tgl=$period;
                $tanggal=$tgl[0]."-".$tgl[1];
                $dmna.=" and periode='".$tanggal."'";
        }
        if($sistemGaji!='')
        {
                $dmna.=" and jenisgaji='".substr($sistemGaji,0,1)."'";
        }
        $whrUnit="kodeorg='".$kdUnit."'";
        if($kdUnit=='')
        {
            $whrUnit="kodeorg='".substr($_SESSION['empl']['lokasitugas'],0,4)."'";
        }
        if ($kdUnit=='PLASMA'){
            $whrUnit="kodeorg in (select kodeorganisasi from ".$dbname.".organisasi where length(kodeorganisasi)=4 and namaorganisasi like '%plasma%')";
        }
        $sTgl="select distinct tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji where ".$whrUnit.$dmna." ";
        //echo"warning".$sTgl;
        $qTgl=mysql_query($sTgl) or die(mysql_error());
        $rTgl=mysql_fetch_assoc($qTgl);
        echo tanggalnormal($rTgl['tanggalmulai'])."###".tanggalnormal($rTgl['tanggalsampai']);
        break;
        case'getKry':
        $optKry="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        if(strlen($kdeOrg)>4)
        {
                $where=" lokasitugas='".substr($kdeOrg,0,4)."'";
        }
        else
        {
                $where=" lokasitugas='".$kdeOrg."' and (subbagian='0' or subbagian is null or subbagian='')";
        }
        $sKry="select nik,karyawanid,namakaryawan from ".$dbname.".datakaryawan where ".$where." order by namakaryawan asc";
        $qKry=mysql_query($sKry) or die(mysql_error());
        while($rKry=mysql_fetch_assoc($qKry))
        {
                $optKry.="<option value=".$rKry['karyawanid'].">".$rKry['nik']."-".$rKry['namakaryawan']."</option>";
        }
        $optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $sPeriode="select distinct periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$kdeOrg."'";
        $qPeriode=mysql_query($sPeriode) or die(mysql_error());
        while($rPeriode=mysql_fetch_assoc($qPeriode))
        {
                $optPeriode.="<option value=".$rPeriode['periode'].">".substr(tanggalnormal($rPeriode['periode']),1,7)."</option>";
        }
        //echo $optPeriode;
        echo $optKry."###".$optPeriode;
        break;
        case'getPeriode':
        if($periodeGaji!='') {
                $were=" kodeorg='".$kdUnit."' and periode='".$periodeGaji."' and jenisgaji='".$sistemGaji."'";
                if ($kdUnit=='PLASMA')
                    $were=" kodeorg in (select kodeorganisasi from ".$dbname.".organisasi where length(kodeorganisasi)=4 and namaorganisasi like '%PLASMA%') and periode='".$periodeGaji."' and jenisgaji='".$sistemGaji."'";
        } else {
                $were=" kodeorg='".$kdUnit."'";
                if ($kdUnit=='PLASMA')
                    $were=" kodeorg in (select kodeorganisasi from ".$dbname.".organisasi where length(kodeorganisasi)=4 and namaorganisasi like '%PLASMA%')";
        }
        $optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $sPeriode="select distinct periode from ".$dbname.".sdm_5periodegaji where ".$were."";
        $qPeriode=mysql_query($sPeriode) or die(mysql_error());
        while($rPeriode=mysql_fetch_assoc($qPeriode))
        {
                $optPeriode.="<option value=".$rPeriode['periode'].">".substr(tanggalnormal($rPeriode['periode']),1,7)."</option>";
        }
        $optAfd="<option value=''>".$_SESSION['lang']['all']."</option>";
        $sSub="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where induk='".$kdUnit."'  order by namaorganisasi asc";
        if ($kdUnit=='PLASMA')
            $sSub="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where length(kodeorganisasi)=6 and namaorganisasi like '%PLASMA%'  order by namaorganisasi asc";
        $qSub=mysql_query($sSub) or die(mysql_error($conn));
        while($rSub=  mysql_fetch_assoc($qSub))
        {
             $optAfd.="<option value='".$rSub['kodeorganisasi']."'>".$rSub['namaorganisasi']."</option>";
        }
        echo $optAfd."####".$optPeriode;
        break;
        case'getPeriodeGaji5':
        $optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $optPeriode2=$optPeriode;
        $sPeriode="select distinct periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$_POST['kdUnit']."' order by periode desc";
        //exit("error:".$sPeriode);
        $qPeriode=mysql_query($sPeriode) or die(mysql_error());
        while($rPeriode=mysql_fetch_assoc($qPeriode))
        {
                $optPeriode.="<option value=".$rPeriode['periode'].">".substr(tanggalnormal($rPeriode['periode']),1,7)."</option>";
        }
        $sPeriode2="select distinct periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$_POST['kdUnit']."' order by periode asc";
        //exit("error:".$sPeriode);
        $qPeriode2=mysql_query($sPeriode2) or die(mysql_error());
        while($rPeriode2=mysql_fetch_assoc($qPeriode2))
        {
                $optPeriode2.="<option value=".$rPeriode2['periode'].">".substr(tanggalnormal($rPeriode2['periode']),1,7)."</option>";
        }
        echo $optPeriode2."####".$optPeriode;
        break;
        default:
        break;
}
?>
