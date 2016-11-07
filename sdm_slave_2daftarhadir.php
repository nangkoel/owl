<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');
//$arrThn="##kdeOrg2##periodThn##periodThnSmp##sistemGaji3";

$proses=$_GET['proses'];

$_POST['kdeOrg2']==''?$kdOrg=$_GET['kdeOrg2']:$kdOrg=$_POST['kdeOrg2'];
$_POST['periodThn']==''?$tgl1=$_GET['periodThn']:$tgl1=$_POST['periodThn'];
$_POST['periodThnSmp']==''?$tgl2=$_GET['periodThnSmp']:$tgl2=$_POST['periodThnSmp'];
$_POST['tipeKary2']==''?$tipeKary=$_GET['tipeKary2']:$tipeKary=$_POST['tipeKary2'];
$_POST['sistemGaji3']==''?$sistemGaji=$_GET['sistemGaji3']:$sistemGaji=$_POST['sistemGaji3'];
$_POST['nilaiMax']==''?$nilaiMax=$_GET['nilaiMax']:$nilaiMax=$_POST['nilaiMax'];
$optTmk=makeOption($dbname, 'datakaryawan', 'karyawanid,tanggalmasuk');

if($kdOrg==''){
    exit("error: Working unit required");
}
if(($tgl1=='')||($tgl2=='')){
    exit("error: Both period required");
}
if($sistemGaji==''){
    exit("error: Payment system required");
}
if($nilaiMax==''){
    exit("error: Minimum presence required, type 0 for all");
}
$optDept=makeOption($dbname, 'sdm_5departemen', 'kode,nama');
$thn=explode("-",$tgl1);
$thn2=explode("-",$tgl2);

$blndt1=intval($thn[1]);
$blndt12=intval($thn2[1]);
if($tgl2<$tgl1){
    exit("error: First period must lower");
}
if($thn[0]!=$thn2[0]){
    for($mule=$blndt1;$mule<13;$mule++){

            $bulan[]=$mule;
    }
    for($mule=1;$mule<=$blndt12;$mule++){
            $bulan[]=$mule;
    }
}
$cerk=count($bulan);
if($cerk>12){
    exit("error: Query maximum 12 months, your query is".$cerk." moths");
}
        //ambil query untuk data karyawan
        $where="  lokasitugas='".$kdOrg."'";

        if($tipeKary!='')
        {
            $where.=" and tipekaryawan='".$tipeKary."'";
            $whrd="and b.tipekaryawan='".$tipeKary."'";
            $whrc="and c.tipekaryawan='".$tipeKary."'";
        }
        if($sistemGaji=='All')$wherez="";        
        if($sistemGaji=='Bulanan')$wherez=" and sistemgaji = 'Bulanan'";        
        if($sistemGaji=='Harian')$wherez=" and sistemgaji = 'Harian'";        

$sGetKary="select a.karyawanid,b.namajabatan,a.namakaryawan,c.nama,d.tipe from ".$dbname.".datakaryawan a 
           left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan
           left join ".$dbname.".sdm_5departemen c on a.bagian=c.kode
           left join ".$dbname.".sdm_5tipekaryawan d on a.tipekaryawan=d.id
           order by namakaryawan asc";    
//echo $sGetKary; exit;
$rGetkary=fetchData($sGetKary);
foreach($rGetkary as $row => $kar)
{
   // $resData[$kar['karyawanid']][]=$kar['karyawanid'];
   $namakar[$kar['karyawanid']]=$kar['namakaryawan'];
    $nmJabatan[$kar['karyawanid']]=$kar['namajabatan'];
    $nmBagian[$kar['karyawanid']]=$kar['nama'];
    $nmTipe[$kar['karyawanid']]=$kar['tipe'];
}  
$bln1=explode("-",$tgl1);
$bln2=explode("-",$tgl2);

        $resData[]=array();
        $hasilAbsn[]=array();
        //get karyawan


    $dimanaPnjng=" kodeorg like '".$kdOrg."%'";


                        $sAbsn="select count(absensi) as total,absensi,a.karyawanid,left(tanggal,7) as periode from ".$dbname.".sdm_absensidt a
                                left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid  where substr(tanggal,1,7) between  '".$tgl1."' and '".$tgl2."' 
                                and ".$dimanaPnjng."  and a.karyawanid!='' ".$whrd."
                                group by absensi,karyawanid,left(tanggal,7)";
                        //exit("Error".$sAbsn);
                        $rAbsn=fetchData($sAbsn);
                        foreach ($rAbsn as $absnBrs =>$resAbsn)
                        {
                                if(!is_null($resAbsn['absensi']))
                                {
                                    $hasilAbsn[$resAbsn['karyawanid']][$resAbsn['periode']][$resAbsn['absensi']]=$resAbsn['total'];
                                    $resData[$resAbsn['karyawanid']][]=$resAbsn['karyawanid'];
                                    $dtPeriode[$resAbsn['periode']]=$resAbsn['periode'];
                                    $klmpkAbsn[$resAbsn['absensi']]=$resAbsn['absensi'];
                                }
                        }

                        $sKehadiran="select count(absensi) as total,absensi,a.karyawanid,left(tanggal,7) as periode from ".$dbname.".kebun_kehadiran_vw a
                                     left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                                     where substr(tanggal,1,7) between  '".$tgl1."' and '".$tgl2."' and substring(unit,1,4)='".$kdOrg."'  ".$whrd."
                                     group by absensi,karyawanid,left(tanggal,7)";
                        //exit("Error".$sKehadiran);
                        $rkehadiran=fetchData($sKehadiran);
                        foreach ($rkehadiran as $khdrnBrs =>$resKhdrn)
                        {	
                                if($resKhdrn['absensi']!='')
                                {
                                    $hasilAbsn[$resKhdrn['karyawanid']][$resKhdrn['periode']][$resKhdrn['absensi']]+=$resKhdrn['total'];
                                    $resData[$resKhdrn['karyawanid']][]=$resKhdrn['karyawanid'];
                                    $dtPeriode[$resKhdrn['periode']]=$resKhdrn['periode'];
                                    $klmpkAbsn[$resAbsn['absensi']]=$resAbsn['absensi'];
                                }

                        }
                        $sPrestasi="select left(c.tanggal,7) as periode,a.jumlahhk,a.nik from ".$dbname.".kebun_prestasi a 
                                    left join ".$dbname.".kebun_aktifitas c on a.notransaksi=c.notransaksi 
                                    left join ".$dbname.".datakaryawan b on a.nik=b.karyawanid
                                    where c.notransaksi like '%PNN%' and a.nik!=''   ".$whrd."
                                    and substr(c.kodeorg,1,4)='".$kdOrg."' and substr(c.tanggal,1,7) between '".$tgl1."' and '".$tgl2."'";
                       // exit("Error".$sPrestasi);
                        $rPrestasi=fetchData($sPrestasi);
                        foreach ($rPrestasi as $presBrs =>$resPres)
                        {
                            $resPres['absensi']='H';
                            $hasilAbsn[$resPres['nik']][$resPres['periode']]['H']+=1;
                            $resData[$resPres['nik']][]=$resPres['nik'];
                            $dtPeriode[$resPres['periode']]=$resPres['periode'];
                            $klmpkAbsn[$resPres['absensi']]=$resPres['absensi'];
                        }

// ambil pengawas                        
$dzstr="SELECT left(a.tanggal,7) as periode,nikmandor FROM ".$dbname.".kebun_aktifitas a
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
    where a.tanggal between '".$tgl1."-01' and LAST_DAY('".$tgl2."-15') and b.kodeorg like '".$kdOrg."%' and c.namakaryawan is not NULL
    union select left(a.tanggal,7) as periode,nikmandor1 FROM ".$dbname.".kebun_aktifitas a 
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.nikmandor1=c.karyawanid
    where a.tanggal between '".$tgl1."-01' and LAST_DAY('".$tgl2."-15') 
    and c.karyawanid!='' and b.kodeorg like '".$kdOrg."%' ".$whrc." and c.namakaryawan is not NULL";
//exit("Error:".$dzstr);
$dzres=mysql_query($dzstr);
while($dzbar=mysql_fetch_object($dzres))
{
    $dzbar->absensi='H';
    $hasilAbsn[$dzbar->nikmandor][$dzbar->periode]['H']+=1;
    $resData[$dzbar->nikmandor][]=$dzbar->nikmandor;
    $dtPeriode[$dzbar->periode]=$dzbar->periode;
    $klmpkAbsn[$dzbar->absensi]=$dzbar->absensi;

}

// ambil administrasi                       
$dzstr="SELECT left(a.tanggal,7) as periode,nikmandor FROM ".$dbname.".kebun_aktifitas a
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
    where a.tanggal between '".$tgl1."-01' and LAST_DAY('".$tgl2."-15') and b.kodeorg like '".$kdOrg."%' and c.namakaryawan is not NULL
    union select left(a.tanggal,7) as periode,keranimuat FROM ".$dbname.".kebun_aktifitas a 
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.keranimuat=c.karyawanid
    where a.tanggal between '".$tgl1."-01' and LAST_DAY('".$tgl2."-15') and c.karyawanid!='' and b.kodeorg like '".$kdOrg."%'  ".$whrc." 
    and c.namakaryawan is not NULL";
$dzres=mysql_query($dzstr);
while($dzbar=mysql_fetch_object($dzres))
{
    $dzbar->absensi='H';
    $hasilAbsn[$dzbar->nikmandor][$dzbar->periode]['H']+=1;
    $resData[$dzbar->nikmandor][]=$dzbar->nikmandor;
    $dtPeriode[$dzbar->periode]=$dzbar->periode;
    $klmpkAbsn[$dzbar->absensi]=$dzbar->absensi;

}       
array_multisort($dtPeriode); 
$bgc="";
$brd="0";
if($proses=='excel'){
    $bgc=" bgcolor=#DEDEDE align=center";
    $brd="1";
}
 $tab.="<table cellspacing='1' border='".$brd."' class='sortable'>
        <thead class=rowheader>
        <tr ".$bgc.">
        <td rowspan=2>No</td>
        <td rowspan=2>".$_SESSION['lang']['nama']."</td>
        <td rowspan=2>".$_SESSION['lang']['tipekaryawan']."</td>
        <td rowspan=2>".$_SESSION['lang']['bagian']."</td>
        <td rowspan=2>".$_SESSION['lang']['jabatan']."</td>
        <td rowspan=2>".$_SESSION['lang']['tmk']."</td>";

        foreach($dtPeriode as $dtprd){
            $tab.="<td align=center colspan='".(count($klmpkAbsn))."'>".$dtprd."</td>";
        }
        $tab.="</tr><tr  ".$bgc.">";
        foreach($dtPeriode as $dtprd){
            foreach($klmpkAbsn as $brsKet =>$hslKet)
            {
                $tab.="<td width=10px align=center>".$hslKet['kodeabsen']."</td>";
            }
        }
        $tab.="
        </tr></thead>
        <tbody>";
       foreach($resData as $hslBrs => $hslAkhir){
           if($hslAkhir[0]!=''){
                $not++;
                $tab.="<tr class=rowcontent><td>".$not."</td>
                <td>".$namakar[$hslAkhir[0]]."</td>
                <td>".$nmTipe[$hslAkhir[0]]."</td>
                <td>".$nmBagian[$hslAkhir[0]]."</td>
                <td>".$nmJabatan[$hslAkhir[0]]."</td>
                <td>".$optTmk[$hslAkhir[0]]."</td>";
                foreach($dtPeriode as $dtprd){
                    foreach($klmpkAbsn as $brsKet =>$hslKet)
                    {
                        $bgrd="";
                        if($hslKet['kodeabsen']=='H'){
                            if($hasilAbsn[$hslAkhir[0]][$dtprd][$hslKet['kodeabsen']]<$nilaiMax){
                                $bgrd="bgcolor=red";
                            }
                        }
                        $tab.="<td width=10px align=center ".$bgrd.">".$hasilAbsn[$hslAkhir[0]][$dtprd][$hslKet['kodeabsen']]."</td>";
                    }
                }
           }
        }
        $tab.="</tbody></table>";

switch($proses)
{
        case'preview':
        echo $tab;
        break;
        case'excel':


                        $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	

                        $nop_="RekapAbsen_PerBulan__".$kdOrg;
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