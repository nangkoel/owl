<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');
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

$proses=$_GET['proses'];//
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['period']==''?$period=$_GET['period']:$period=$_POST['period'];
$_POST['perod']==''?$perod=$_GET['perod']:$perod=$_POST['perod'];
$_POST['idKry']==''?$idKry=$_GET['idKry']:$idKry=$_POST['idKry'];
$_POST['idAfd']==''?$idAfd=$_GET['idAfd']:$idAfd=$_POST['idAfd'];
$_POST['tPkary']==''?$tPkary=$_GET['tPkary']:$tPkary=$_POST['tPkary'];
$_POST['kdOrg']==''?$kdOrg=$_GET['kdOrg']:$kdOrg=$_POST['kdOrg'];
$_POST['kdBag']==''?$kdBag=$_GET['kdBag']:$kdBag=$_POST['kdBag'];
$_POST['kdBag2']==''?$kdBag2=$_GET['kdBag2']:$kdBag2=$_POST['kdBag2'];
       
if($idKry==''){
    if($kdBag!='')
    {
        $tam="  and b.bagian='".$kdBag."'";
    }
    if($idAfd==''){
        $idAfd=$kdOrg;
    }else{
    
    if(strlen($idAfd)>4){
        $afdId=$idAfd;
    }
}
}else{
    $idAfd=$_SESSION['empl']['lokasitugas'];
}
//$arrAfd="##perod##idAfd##tPkary2";        
$rNmTipe=makeOption($dbname,'sdm_5tipekaryawan','id,tipe');
$dtTipe="";
$arrBln=array(1=>"Januari",2=>"Februari",3=>"Maret",4=>"April",5=>"Mei",6=>"Juni",7=>"Juli",8=>"Agustus",9=>"September",10=>"Oktober",11=>"November",12=>"Desembe");
$optRegional=  makeOption($dbname, 'bgt_regional_assignment', 'kodeunit,regional');
$optNmKeg=  makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
$optSatKeg=  makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,satuan');

if($periode!=''){
    $where="a.sistemgaji='Harian' and a.periodegaji='".$periode."'  
            and a.kodeorg='".$idAfd."'".$tam;
}
else{
    if($period!='')
    {
        $periode=$period;
    }
    $where="a.sistemgaji='Harian' and a.periodegaji='".$periode."' and a.karyawanid='".$idKry."'";
}
if($perod!=''){
    if($kdBag2!='')
    {
        $tam="  and b.bagian='".$kdBag2."'";
    }
     
    if($afdId!=''){
        $idAfd=substr($afdId,0,4);
    $where="a.sistemgaji='Harian' and a.periodegaji='".$perod."'  
            and a.kodeorg='".$idAfd."' and b.subbagian='".$afdId."'".$tam;
    }else{
        $where="a.sistemgaji='Harian' and a.periodegaji='".$perod."'  
            and a.kodeorg='".substr($idAfd,0,4)."' and (b.subbagian is null or subbagian='')".$tam;
    }
    $periode=$perod;
}

$sKemandoran="select karyawanid,jabatan,sum(potongan) as potongan from ".$dbname.".kebun_premikemandoran where periode='".$periode."' and kodeorg='".$idAfd."' group by karyawanid,jabatan";
$qKemandoran=mysql_query($sKemandoran) or die(mysql_error($conn));
while ($rKemandoran=mysql_fetch_object($qKemandoran)){
    $arrPotMandor[$rKemandoran->karyawanid]['jabatan']=$rKemandoran->jabatan;
    $arrPotMandor[$rKemandoran->karyawanid]['potongan']=$rKemandoran->potongan;
    $arrPotMandor[$rKemandoran->karyawanid]['premi']=$rKemandoran->premiinput;
}

switch($proses)
{
        case'preview':
        if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
        $sOrg="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$idAfd."'";
        $qOrg=mysql_query($sOrg) or die(mysql_error($conn));
        $rOrg=mysql_fetch_assoc($qOrg);

        //periode gaji
        $bln=explode('-',$periode);
        $idBln=intval($bln[1]);	
          //prepare array data gaji karyawan,nama,jabatan,tmk dan bagian
        $sSlip="select distinct a.*,b.tipekaryawan,b.statuspajak,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama from 
               ".$dbname.".sdm_gaji_vw a  left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
               left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan 
               left join ".$dbname.".sdm_5departemen d on b.bagian=d.kode where ".$where." ".$dtTipe." and tipekaryawan=4";
        //exit("Error".$sSlip);
        $qSlip=mysql_query($sSlip) or die(mysql_error());
        $rCek=mysql_num_rows($qSlip);
        if($rCek>0)
        {
                while($rSlip=mysql_fetch_assoc($qSlip))
                {
                    if($rSlip['karyawanid']!='')
                    {
                    $arrKary[$rSlip['karyawanid']]=$rSlip['karyawanid'];
                    $arrKomp[$rSlip['karyawanid']]=$rSlip['idkomponen'];
                    $arrTglMsk[$rSlip['karyawanid']]=$rSlip['tanggalmasuk'];
                    $arrNik[$rSlip['karyawanid']]=$rSlip['nik'];
                    $arrNmKary[$rSlip['karyawanid']]=$rSlip['namakaryawan'];
                    $arrBag[$rSlip['karyawanid']]=$rSlip['bagian'];
                    $arrJbtn[$rSlip['karyawanid']]=$rSlip['namajabatan'];
                    $arrDept[$rSlip['karyawanid']]=$rSlip['nama'];
                    $arrJmlh[$rSlip['karyawanid'].$rSlip['idkomponen']]=$rSlip['jumlah'];
                    }
                }
                //array data komponen penambah dan pengurang
                $sKomp="select id,name from ".$dbname.".sdm_ho_component where plus='1' and id not in ('28','26') ";
                $qKomp=mysql_query($sKomp) or die(mysql_error());
                while($rKomp=mysql_fetch_assoc($qKomp))
                {
                      $arrIdKompPls[]=$rKomp['id'];
                      $arrNmKomPls[$rKomp['id']]=$rKomp['name'];
                }
                $sKomp="select id,name from ".$dbname.".sdm_ho_component where plus='0'  ";
                $qKomp=mysql_query($sKomp) or die(mysql_error());
                while($rKomp=mysql_fetch_assoc($qKomp))
                {
                      $arrIdKompMin[]=$rKomp['id'];
                      $arrNmKomMin[$rKomp['id']]=$rKomp['name'];
                }


                foreach($arrKary as $dtKary)
                {

                    echo"<table cellspacing=1 border=0 width=500>
                    <tr><td> <h2><img src=".$path." width=60 height=35>&nbsp;".$_SESSION['org']['namaorganisasi']."</h2></td></tr>
                    <tr style='border-bottom:#000 solid 2px; border-top:#000 solid 2px;'><td valign=top>
                    <table border=0 width=110%>
                    <tr><td width=49% valign=top><table border=0>
                    <tr><td colspan=3>PAY SLYP/SLIP GAJI: ".$arrBln[$idBln]."-".$bln[0]."</td></tr>
                    <tr><td>NIP/TMK</td><td>:</td><td>".$arrNik[$dtKary]."/".tanggalnormal($arrTglMsk[$dtKary])."</td></tr>
                    <tr><td>NAMA</td><td>:</td><td>".$arrNmKary[$dtKary]."</td></tr>
                    </table></td><td width=51% valign=top>
                    <table border=0>
                    <tr><td colspan=3>&nbsp;</td></tr>
                    <tr><td>UNIT/BAGIAN</td><td>:</td><td>".$rOrg['namaorganisasi']."/".$arrBag[$dtKary]."</td></tr>
                    <tr><td>JABATAN</td><td>:</td><td>".$arrJbtn[$dtKary]."</td></tr>
                    </table></td></tr>
                    </table>
                    </td></tr>
                    <tr>
                    <td>
                    <table width=100%>
                    <thead>
                    <tr><td align=center>PENAMBAH</td><td align=center>PENGURANG</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                    <td valign=top>
                    <table width=100%>";
                     $arrPlus=Array();
                      $s=0;
                      foreach($arrIdKompPls as $idKompPls)
                      {
                          echo"<tr><td>".$arrNmKomPls[$idKompPls]."</td><td>:Rp.</td><td align=right> ".number_format($arrJmlh[$dtKary.$idKompPls],2)."</td></tr>";
                            $arrPlus[$s]=$arrJmlh[$dtKary.$idKompPls];
                            $s++;
                      }

                                    echo"</table>

                    </td>
                    <td valign=top>
                    <table width=100%>";
                    $arrMin=Array();
                        $q=0;
                        foreach($arrIdKompMin as $idKompMin)
                          {
                              echo"<tr><td>".$arrNmKomMin[$idKompMin]."</td><td>:Rp.</td><td align=right> ".number_format($arrJmlh[$dtKary.$idKompMin],2)."</td></tr>";
                                $arrMin[$q]=$arrJmlh[$dtKary.$idKompMin];
                                $q++;
                          }
                    $gajiBersih=array_sum($arrPlus)-array_sum($arrMin);				
                    echo"</table>
                    </td></tr>
                    <tr><td colspan=2><table width=100%>
                    <tr><td>Total Penambahan</td><td>:Rp.</td><td align=right> ".number_format(array_sum($arrPlus),2)."</td><td>Total Pengurangan</td><td>:Rp.</td><td align=right> ".number_format(array_sum($arrMin),2)."</td></tr>
                    <tr><td>Gaji Bersih</td><td>:Rp.</td><td align=right> ".number_format((array_sum($arrPlus)-array_sum($arrMin)),2)."</td><td>&nbsp;</td><td>&nbsp;</td><td align=right> &nbsp;</td></tr>
                    <tr><td>Terbilang</td><td>:</td><td colspan=4> ".terbilang($gajiBersih,2)." rupiah</td></tr></table></td></tr></tbody>
                    </table></td>
                    </tr>


                    <tr>
                    <td>&nbsp;</td>
                    </tr>
                    </table>
                    ";
        }
        }
        else
        {
                echo"Not Found";
        }
        break;
        case'pdf':
        $bln=explode('-',$periode);
        $idBln=intval($bln[1]);	
        class PDF extends FPDF
        {
        var $col=0;
        var $dbname;
                function Header()
                {    
                        //$this->lMargin=5;  
                }
        }
                $pdf=new PDF('P','mm','letter');
                $pdf->AddPage();
                //periode gaji
                $bln=explode('-',$periode);
                $prd=$bln[0].$bln[1];
                $idBln=intval($bln[1]);	
                 $sTgl="select distinct tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji 
                                       where kodeorg='".$idAfd."' and periode='".$periode."' 
                        and jenisgaji='H'";
                 //exit("error:".$sTgl);
                 $qTgl=mysql_query($sTgl) or die(mysql_error($conn));
                 $rTgl=mysql_fetch_assoc($qTgl);
                 $test = dates_inbetween($rTgl['tanggalmulai'], $rTgl['tanggalsampai']);
                 ##tambahan absen permintaan dari pak ujang#
                
            
                        $sKehadiran="select absensi,tanggal,karyawanid from ".$dbname.".kebun_kehadiran_vw 
                                     where tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and kodeorg like '".$idAfd."%'";
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
                        $sPrestasi="select a.nik,b.tanggal from ".$dbname.".kebun_prestasi a left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi 
                                    where b.notransaksi like '%PNN%' and b.kodeorg like '".$idAfd."%' and b.tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."'";
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
            where a.tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and b.kodeorg like '".$idAfd."%' and c.namakaryawan is not NULL
            union select tanggal,nikmandor1 FROM ".$dbname.".kebun_aktifitas a 
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".datakaryawan c on a.nikmandor1=c.karyawanid
            where a.tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and b.kodeorg like '".$idAfd."%' and c.namakaryawan is not NULL";
        // exit("Error".$dzstr);
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
            where a.tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and b.kodeorg like '".$idAfd."%' and c.namakaryawan is not NULL
            union select tanggal,keranimuat FROM ".$dbname.".kebun_aktifitas a 
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".datakaryawan c on a.keranimuat=c.karyawanid
            where a.tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and b.kodeorg like '".$idAfd."%' and c.namakaryawan is not NULL";
         //exit("Error".$dzstr);
        $dzres=mysql_query($dzstr);
        while($dzbar=mysql_fetch_object($dzres))
        {
            $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][]=array(
            'absensi'=>'H');
            $resData[$dzbar->nikmandor][]=$dzbar->nikmandor;
        }
        // ambil traksi                       
        $dzstr="SELECT a.tanggal,idkaryawan FROM ".$dbname.".vhc_runhk a
        left join ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
        where a.tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and notransaksi like '%".$idAfd."%'";
        //exit("Error".$dzstr);
        $dzres=mysql_query($dzstr);
        while($dzbar=mysql_fetch_object($dzres))
        {
        $hasilAbsn[$dzbar->idkaryawan][$dzbar->tanggal][]=array(
        'absensi'=>'H');
        $resData[$dzbar->idkaryawan][]=$dzbar->idkaryawan;
        }    
        
        
        $sAbsn="select absensi,tanggal,karyawanid from ".$dbname.".sdm_absensidt 
                            where tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and kodeorg like '".$idAfd."%' and absensi  in ('P0','S1','H')";//P0 s1 H ||and absensi not in ('MG','L','C')";
                         //exit("Error".$sAbsn);
                        $rAbsn=fetchData($sAbsn);
                        foreach ($rAbsn as $absnBrs =>$resAbsn)
                        {
                                if(!is_null($resAbsn['absensi']))
                                {
                                        $hasilAbsn[$resAbsn['karyawanid']][$resAbsn['tanggal']][]=array(
                'absensi'=>$resAbsn['absensi']);
                                $resData[$resAbsn['karyawanid']][]=$resAbsn['karyawanid'];
                                }

                        }
        
        
        foreach($resData as $hslBrs => $hslAkhir)
        {	
            if($hslAkhir[0]!='')
            {
                foreach($test as $barisTgl =>$isiTgl)
                {
                        $brt[$hslAkhir[0]][$hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi']]+=1;
                }
            }	
        }
         $shkdbyr="select distinct kodeabsen from ".$dbname.".sdm_5absensi where kelompok=1 order by kodeabsen";
         $qhkdbyr=mysql_query($shkdbyr) or die(mysql_error($conn));
         while($rHkbyr=  mysql_fetch_assoc($qhkdbyr)){
             $dtAbsByr[]=$rHkbyr['kodeabsen'];
         }
        
        #tambahan absen permintaan abis disini#
                #dasar brutto
                #pendapatan lain
                #gapok=jumlah absensi atau hk yang di dapat
                #premi perawatan
                #premi panen
                #premi ford
                #premi kehadiran
                #lembur
                #premi rajin
                 #gapok
                 $sGpk="select sum(jumlah) as jumlah,idkomponen,a.karyawanid from ".$dbname.".sdm_gaji a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid "
                    . " where b.tipekaryawan=4 and a.kodeorg='".$idAfd."' and periodegaji='".$periode."' group by a.karyawanid,a.idkomponen";
                 $qGpk=mysql_query($sGpk) or die(mysql_error());
                 while($rGpk=  mysql_fetch_assoc($qGpk)){
                    $sKomp="select * from ".$dbname.".sdm_ho_component where id='".$rGpk['idkomponen']."' and plus=1";
                    $qKomp=mysql_query($sKomp) or die(mysql_error());
                    $rKomp=  mysql_num_rows($qKomp);
                    if($rKomp>0){
                        $arrPenambah[$rGpk['karyawanid']]+=$rGpk['jumlah'];
                    }
                    
                 }
                 $optNamAbns=  makeOption($dbname, 'sdm_5absensi', 'kodeabsen,keterangan');
                $arrPlusId=array("1"=>"Tarif Upah Pokok","2"=>"Hari Premi","3"=>"Gaji Pokok","4"=>"Job Insentif","5"=>"Lembur"
                           ,"6"=>"Rapel/Cuti diuangkn","7"=>"Satuan","8"=>"Tam.Insentif Satuan","9"=>"Premi Hadir","10"=>"Simpanan","11"=>"Premi M/K/C/R");
                //prepare array data gaji karyawan,nama,jabatan,tmk dan bagian
                $sSlip="select distinct a.*,b.tipekaryawan,b.statuspajak,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama,b.lokasitugas,b.statuspajak from 
                       ".$dbname.".sdm_gaji_vw a  left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
                       left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan 
                       left join ".$dbname.".sdm_5departemen d on b.bagian=d.kode where ".$where." ".$dtTipe." and tipekaryawan=4";
                //echo $sSlip
                //kompecho $sSlip;
                $qSlip=mysql_query($sSlip) or die(mysql_error());
                $rCek=mysql_num_rows($qSlip);
                if($rCek>0){
                        while($rSlip=mysql_fetch_assoc($qSlip)){
                            if($rSlip['karyawanid']!=''){
                            $sKomp="select id,name,plus from ".$dbname.".sdm_ho_component where id='".$rSlip['idkomponen']."'";
                            $qKomp=mysql_query($sKomp) or die(mysql_error());
                            $rKomp=  mysql_fetch_assoc($qKomp);
                            if($rKomp['plus']==0){
                                if($rSlip['karyawanid']!=$temId){
                                    $nor=1;
                                    $temId=$rSlip['karyawanid'];
                                }else{
                                    $nor+=1;
                                }
                                if($rSlip['idkomponen']!=61){
                                    if($rSlip['idkomponen']==34){
                                        $dendaPnn[$rSlip['karyawanid']]+=$rSlip['jumlah'];
                                    }
                                        $arrPengurang[$rSlip['karyawanid']]+=$rSlip['jumlah'];
                                    
                                }else{
                                    $arrDsmpn[$rSlip['karyawanid']]=$rSlip['jumlah'];
                                }
                                $arrNamMin[$rSlip['idkomponen']]=$rKomp['name'];
                                $arrPeng[$rSlip['karyawanid'].$rSlip['idkomponen']]=$rSlip['jumlah'];
                                if($rSlip['idkomponen']!=34){
                                    $arrKompPeng[$rSlip['karyawanid'].$nor]=$rSlip['idkomponen'];
                                 }
                                $jmPeng[$rSlip['karyawanid']]+=$nor;
                            }
                            $arrKary[$rSlip['karyawanid']]=$rSlip['karyawanid'];
                            $arrKomp[$rSlip['karyawanid']]=$rSlip['idkomponen'];
                            $arrTglMsk[$rSlip['karyawanid']]=$rSlip['tanggalmasuk'];
                            $arrNik[$rSlip['karyawanid']]=$rSlip['nik'];
                            $arrNmKary[$rSlip['karyawanid']]=$rSlip['namakaryawan'];
                            $arrBag[$rSlip['karyawanid']]=$rSlip['bagian'];
                            $arrLok[$rSlip['karyawanid']]=$rSlip['lokasitugas'];
                            $arrJbtn[$rSlip['karyawanid']]=$rSlip['namajabatan'];
                            $arrDept[$rSlip['karyawanid']]=$rSlip['nama'];
                            $arrStataPajak[$rSlip['karyawanid']]=$rSlip['statuspajak'];
                            $arrJmlh[$rSlip['karyawanid'].$rSlip['idkomponen']]=$rSlip['jumlah'];
                            $komp="1";
                            if($arrPles[$rSlip['karyawanid'].$komp]==''){
                                $sdt="select sum(jumlah) as jmlgapok,karyawanid from ".$dbname.".sdm_5gajipokok where tahun='".substr($periode,0,4)."' "
                                    . " and karyawanid='".$rSlip['karyawanid']."' and idkomponen in ('1','2')";
                                $qdt=  mysql_query($sdt) or die(mysql_error($conn));
                                $rdt=  mysql_fetch_assoc($qdt);
                                $arrPles[$rdt['karyawanid'].$komp]=$rdt['jmlgapok']/25;
                            }
                                if(($rSlip['idkomponen']=='43')||($rSlip['idkomponen']=='14')){
                                    $komp="6";
                                    $arrPles[$rSlip['karyawanid'].$komp]+=$rSlip['jumlah'];
                                }
                                if($rSlip['idkomponen']=='60'){
                                        $komp=10;
                                        $arrPles[$rSlip['karyawanid'].$komp]+=$rSlip['jumlah'];
                                }
                            }
                        }
                       #pendapatan lain
                       $sDptLaen="select sum(jumlah) as jumlah,a.karyawanid from ".$dbname.".sdm_pendapatanlaindt a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid "
                               . " where periodegaji='".$periode."' and a.kodeorg='".$idAfd."' and tipekaryawan=4 group by karyawanid";
                       $qDptLaen=  mysql_query($sDptLaen) or die(mysql_error($conn));
                       while($rDptLaen=  mysql_fetch_assoc($qDptLaen)){
                           $komp=6;
                           $arrPles[$rDptLaen['karyawanid'].$komp]=$rDptLaen['jumlah'];
                       }
                       
                       
                        #premi kehadrian
                        $sKehadiran="select a.`karyawanid`,`jabatan`,`pembagi`,`premiinput` from ".$dbname.".kebun_premikemandoran a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid"
                                .   " where jabatan='PREMIHADIR' and periode='".$periode."' and kodeorg='".$idAfd."' and tipekaryawan=4 ".$add."";
                        ///exit("error:".$sKehadiran);
                        $qKehadiran=  mysql_query($sKehadiran) or die(mysql_error($conn));
                        while($rKehadiran=  mysql_fetch_assoc($qKehadiran)){
                            $komp=2;
                            $arrPles[$rKehadiran['karyawanid'].$komp]=$rKehadiran['premiinput']/1000;
                            $komp=9;
                            $arrPles[$rKehadiran['karyawanid'].$komp]=$rKehadiran['premiinput'];
                            $premiKehadiran[$rKehadiran['karyawanid']]=$rKehadiran['premiinput'];
                        }
                        
                        
                    
                        
                        ##ind
                        #premi kemandoran $kom11
                        $iMandor="select a.`karyawanid`,`jabatan`,`pembagi`,`premiinput` from ".$dbname.".kebun_premikemandoran a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid"
                                .   " where jabatan in('RECORDER','MANDOR','KERANI','CONDUCTOR') and periode='".$periode."' and kodeorg='".$idAfd."' and tipekaryawan=4 ".$add."";
                        $nMandor=  mysql_query($iMandor) or die(mysql_error($conn));
                        while($dMandor=  mysql_fetch_assoc($nMandor)){
                            $komp=11;
                            $arrPles[$dMandor['karyawanid'].$komp]+=$dMandor['premiinput'];
                        }
                        /*echo"<pre>";
                        print_r($arrPles);
                        echo"</pre>";*/
                        //exit("Error:$iMandor");
                        
                        #ambil data perawatan
                        $sKehadiran="select karyawanid,sum(umr) as upah,kodekegiatan,sum(hasilkerja) as hslkrj,sum(insentif) as premikrj from ".$dbname.".kebun_kehadiran_vw 
                                     where tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and unit='".substr($idAfd,0,4)."' ".$addKhdrn." and tipekaryawan='KHT' and jurnal=1
                                     group by karyawanid,kodekegiatan";
                        //exit("Error".$sKehadiran);
                        $rkehadiran=fetchData($sKehadiran);
                        foreach ($rkehadiran as $khdrnBrs =>$resKhdrn){	
                            if(substr($optNmKeg[$resKhdrn['kodekegiatan']],-3,3)=="[S]"){
                                $komp=7;//satuan
                                $arrPles[$resKhdrn['karyawanid'].$komp]+=$resKhdrn['upah'];
                                $hslKrj[$resKhdrn['karyawanid'].$resKhdrn['kodekegiatan']]=$resKhdrn['hslkrj'];
                                $hslRup[$resKhdrn['karyawanid'].$resKhdrn['kodekegiatan']]=$resKhdrn['upah'];
                                $gapSat[$resKhdrn['karyawanid']]+=$resKhdrn['upah'];
                            }else{
                                $komp=3;//gapok karna kerja harian
                                $arrPles[$resKhdrn['karyawanid'].$komp]+=$resKhdrn['upah'];
                                $hslKrj[$resKhdrn['karyawanid'].$resKhdrn['kodekegiatan']]=$resKhdrn['hslkrj'];
                                $hslRup[$resKhdrn['karyawanid'].$resKhdrn['kodekegiatan']]=$resKhdrn['upah'];
                                $gapHar[$resKhdrn['karyawanid']]+=$resKhdrn['upah'];
                            }
                            $komp=4;//job insntif
                            $arrPles[$resKhdrn['karyawanid'].$komp]+=$resKhdrn['premikrj'];
                            $premiKerja[$resKhdrn['karyawanid']]+=$resKhdrn['premikrj'];
                            $lstKeg[$resKhdrn['karyawanid']][]=$resKhdrn['kodekegiatan'];
                        }
                        
                        #ambil data panen
                        $sPrestasi="select  a.nik,sum(hasilkerja) as hasilkerja,sum(upahkerja) as upahkerja,sum(upahpremi) as upahpremi,tarif from ".$dbname.".kebun_prestasi a left join ".$dbname.".datakaryawan b on a.nik=b.karyawanid
                                    left join ".$dbname.".kebun_aktifitas c on a.notransaksi=c.notransaksi
                                    where c.tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and a.kodeorg like '".$idAfd."%' and tipekaryawan='4' ".$addKhdrn."  and jurnal=1
                                    group by a.nik,tarif";
                        //echo $sPrestasi;
                        $rPrestasi=fetchData($sPrestasi);
                        foreach ($rPrestasi as $presBrs =>$resPres){
                            if($resPres['tarif']=='harian'){
                                $komp=3;//gapok untuk panen harian
                                $arrPles[$resPres['nik'].$komp]+=$resPres['upahkerja'];                                
                                $keg="pnnhar";
                                $hslRup[$resPres['nik'].$keg]=$resPres['upahkerja'];
                                $hslKrj[$resPres['nik'].$keg]=$resPres['hasilkerja'];
                                $lstKeg[$resPres['nik']][]=$keg;
                                $gapHar[$resPres['nik']]+=$resPres['upahkerja'];
                            }else{
                                $komp=7;//gapok untuk panen satuan
                                $arrPles[$resPres['nik'].$komp]+=$resPres['upahkerja'];
                                if($resPres['upahpremi']!=''){
                                    $keg="pnnbukitsat";
                                    $lstKeg[$resPres['nik']][]=$keg;
                                    $hslRup[$resPres['nik'].$keg]+=($resPres['upahkerja']);
                                    $hslKrj[$resPres['nik'].$keg]+=$resPres['hasilkerja'];
                                    
                                    $komp=8;//premi bukit
                                    $arrPles[$resPres['nik'].$komp]+=$resPres['upahpremi'];
                                }else{
                                    $keg="pnnsat";
                                    $hslRup[$resPres['nik'].$keg]+=$resPres['upahkerja'];
                                    $hslKrj[$resPres['nik'].$keg]+=$resPres['hasilkerja'];
                                    $lstKeg[$resPres['nik']][]=$keg;
                                    $komp=7;//gapok untuk panen satuan
                                    $arrPles[$resPres['nik'].$komp]+=$resPres['upahkerja'];
                                }
                                $gapSat[$resPres['nik']]+=$resPres['upahkerja'];
                                
                            }
                        }
                    #data absen
                    $sAbsn="select karyawanid,count(absensi) as kehdrn,sum(upah) as insentif,absensi from ".$dbname.".sdm_absensidt_vw "
                         . " where tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and left(kodeorg,4)='".$idAfd."' and tipekaryawan=4 ".$add." and upah!=0 group by karyawanid,absensi";
                    //echo $sAbsn;
                    $qAbsn=  mysql_query($sAbsn) or die(mysql_error($conn));
                    while($rAbsn=  mysql_fetch_assoc($qAbsn)){
                            $scek="select * from ".$dbname.".sdm_5absensi where kelompok=1 and kodeabsen='".$rAbsn['absensi']."'";
                            $qcek=  mysql_query($scek) or die(mysql_error($con));
                            $rcek=  mysql_num_rows($qcek);
                            if($rcek!=0){
                                    if($rAbsn['absensi']!='L'){
                                        $keg=$rAbsn['absensi'];
                                        $lstKeg[$rAbsn['karyawanid']][]=$keg;
                                        $hslRup[$rAbsn['karyawanid'].$keg]+=$rAbsn['insentif'];
                                        $hslKrj[$rAbsn['karyawanid'].$keg]+=$rAbsn['kehdrn'];
                                        $gapHar[$rAbsn['karyawanid']]+=$rAbsn['insentif'];
                                        $komp=3;//gapok dari kehadiran
                                        $arrPles[$rAbsn['karyawanid'].$komp]+=$rAbsn['insentif'];
                                    }elseif(($rAbsn['absensi']=='L')&&($rAbsn['insentif']!='0')){
                                        #libnas
                                        $keg='L';
                                        $sLibnas="select tanggal from ".$dbname.".sdm_5harilibur where regional='".$optRegional[$idAfd]."' and left(tanggal,7)='".$periode."'";
                                        $qLibnas=  mysql_query($sLibnas) or die(mysql_error($conn));
                                        $rLibnas=  mysql_fetch_assoc($qLibnas);
                                        if($rAbsn['insentif']!=''){
                                            $hslRup[$rAbsn['karyawanid'].$keg]+=$rAbsn['insentif'];
                                            $hslKrj[$rAbsn['karyawanid'].$keg]+=$rAbsn['kehdrn'];
                                            $gapHar[$rAbsn['karyawanid']]+=$rAbsn['insentif'];
                                            $lstKeg[$rAbsn['karyawanid']][]=$keg;
                                            $komp=3;//gapok libnas dari kehadiran
                                            $arrPles[$rAbsn['karyawanid'].$komp]+=$rAbsn['insentif'];
                                        }
                                    }else{
                                        continue;
                                    }
                            }
                     }
        
                    #data lembur
                    $sLem="select a.karyawanid,a.tanggal,tipelembur,jamaktual,uangkelebihanjam from ".$dbname.".sdm_lemburdt a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid"
                          . " where tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and left(a.kodeorg,4)='".$idAfd."' and b.tipekaryawan=4  ".$add." order by tanggal asc";
                    //echo $sLem;
                    $qLem=  mysql_query($sLem) or die(mysql_error($conn));
                    while($rLem=  mysql_fetch_assoc($qLem)){
                        $lemJamAk[$rLem['karyawanid']]+=$rLem['jamaktual'];
                        $whr="kodeorg='".$idAfd."' and jamaktual='".$rLem['jamaktual']."' and tipelembur='".$rLem['tipelembur']."'";
                        $optMasLembur=  makeOption($dbname, 'sdm_5lembur', 'jamaktual,jamlembur',$whr );
                        $lemJamLem[$rLem['karyawanid']]+=$optMasLembur[$rLem['jamaktual']];
                        $komp=5;
                        $arrPles[$rLem['karyawanid'].$komp]+=$rLem['uangkelebihanjam'];
                    }
        
                    
                    #premi panen
                    $urut2=0;
                    $urut=0;
                    $sSetupPrem="select kodeorg,hasilkg,rupiah,premirajin from ".$dbname.".kebun_5premipanen where kodeorg in ('SULAWESI','H12E02','H12E01','KALIMANTAN') order by lebihbasiskg desc";
                    //echo $sSetupPrem;
                    $qSetupPrem=  mysql_query($sSetupPrem) or die(mysql_error($conn));
                    while($rSetupPrem=  mysql_fetch_assoc($qSetupPrem)){
                        if(($rSetupPrem['kodeorg']=='SULAWESI')||strlen($rSetupPrem['kodeorg'])==6){
                            $urut+=1;
                            $kgPrem[$rSetupPrem['kodeorg'].$urut]=$rSetupPrem['hasilkg'];
                            $rpPrem[$rSetupPrem['kodeorg'].$urut]=$rSetupPrem['rupiah'];
                            $rajinPrem[$rSetupPrem['kodeorg']]=$rSetupPrem['premirajin'];
                            $kdOrg[$rSetupPrem['kodeorg']]=$rSetupPrem['kodeorg'];
                        }elseif($rSetupPrem['kodeorg']=='KALIMANTAN'){
                            $urut2+=1;
                            $kgPrem[$rSetupPrem['kodeorg'].$urut2]=$rSetupPrem['hasilkg'];
                            $rpPrem[$rSetupPrem['kodeorg'].$urut2]=$rSetupPrem['rupiah'];
                            $kdOrg[$rSetupPrem['kodeorg']]=$rSetupPrem['kodeorg'];
                        }
                    }
                        $sJjg="select karyawanid,sum(hasilkerja) as jmlhjjg from ".$dbname.".kebun_prestasi_vw "
                                . " where tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and unit='".$idAfd."' and tipekaryawan='KHT' ".$addKhdrn." group by karyawanid,tanggal";
                        //exit("error:".$sJjg);
                        $qJjg=  mysql_query($sJjg) or die(mysql_error($conn));
                        while($rJjg=  mysql_fetch_assoc($qJjg)){
                            if($rJjg['jmlhjjg']!=''){
                                $hrEfektif[$rJjg['karyawanid']]+=1;
                                $totJjg[$rJjg['karyawanid']]+=$rJjg['jmlhjjg'];
                            }
                        }
                        $sPremiPanen="select a.karyawanid,totalkg,rupiahpremi from ".$dbname.".kebun_premipanen a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid "
                                    . " where a.kodeorg='".substr($idAfd,0,4)."' and periode='".$periode."' and tipekaryawan=4 and rupiahpremi!=0 ".$add;
                         //exit("error:".$sPremiPanen);
                         $qPremiPanen=  mysql_query($sPremiPanen) or die(mysql_error());
                         while($rPremiPanen=  mysql_fetch_assoc($qPremiPanen)){
                             if($rPremiPanen['rupiahpremi']!='0'){
                                     $angk1=1;//nourut dari premi panen
                                     $angk2=2;
                                     $angk3=3;
                                     $angk4=4;
                                     $angk5=5;
                                     $ws="karyawanid='".$rPremiPanen['karyawanid']."'";
                                     $optSubbgain=  makeOption($dbname, 'datakaryawan', 'karyawanid,subbagian', $ws);
                                     //exit("error:".$optSubbgain[$rPremiPanen['karyawanid']]."___".$kdOrg[$optSubbgain[$rPremiPanen['karyawanid']]]);
                                     $kdData=$optRegional[substr($idAfd,0,4)];
                                     //exit("error:".$rpPrem[$kdData.$angk5]."____".round($rPremiPanen['rupiahpremi']));
                                     if($kdData=='KALIMANTAN'){
                                         //echo $rpPrem[$kdData.$angk5]."____".round($rPremiPanen['rupiahpremi']);
                                             if($rpPrem[$kdData.$angk1]==round($rPremiPanen['rupiahpremi'])){
                                                $keg="prpanen20";
                                                $lstKeg[$rPremiPanen['karyawanid']][]=$keg;
                                                $hslRup[$rPremiPanen['karyawanid'].$keg]=$rPremiPanen['rupiahpremi'];
                                                $hslKrj[$rPremiPanen['karyawanid'].$keg]=$rPremiPanen['totalkg'];
                                                $komp=8;//satuan sumber dari premi panen
                                                $arrPles[$rPremiPanen['karyawanid'].$komp]+=$rPremiPanen['rupiahpremi'];
                                           }elseif($rpPrem[$kdData.$angk2]==round($rPremiPanen['rupiahpremi'])){
                                                $keg="prpanen15";
                                                $hslRup[$rPremiPanen['karyawanid'].$keg]=$rPremiPanen['rupiahpremi'];
                                                $hslKrj[$rPremiPanen['karyawanid'].$keg]=$rPremiPanen['totalkg'];
                                                $lstKeg[$rPremiPanen['karyawanid']][]=$keg;
                                                $komp=8;//satuan sumber dari premi panen
                                                $arrPles[$rPremiPanen['karyawanid'].$komp]+=$rPremiPanen['rupiahpremi'];
                                            }elseif($rpPrem[$kdData.$angk3]==round($rPremiPanen['rupiahpremi'])){
                                                $keg="prpanen10";
                                                $hslRup[$rPremiPanen['karyawanid'].$keg]=$rPremiPanen['rupiahpremi'];
                                                $hslKrj[$rPremiPanen['karyawanid'].$keg]=$rPremiPanen['totalkg'];
                                                $lstKeg[$rPremiPanen['karyawanid']][]=$keg;
                                                $komp=8;//satuan sumber dari premi panen
                                                $arrPles[$rPremiPanen['karyawanid'].$komp]+=$rPremiPanen['rupiahpremi'];
                                            }elseif($rpPrem[$kdData.$angk4]==round($rPremiPanen['rupiahpremi'])){
                                                $keg="prpanen05";
                                                $hslRup[$rPremiPanen['karyawanid'].$keg]=$rPremiPanen['rupiahpremi'];
                                                $hslKrj[$rPremiPanen['karyawanid'].$keg]=$rPremiPanen['totalkg'];
                                                $lstKeg[$rPremiPanen['karyawanid']][]=$keg;
                                                $komp=9;//satuan sumber dari premi panen
                                                $arrPles[$rPremiPanen['karyawanid'].$komp]+=$rPremiPanen['rupiahpremi'];
                                            }
                                            }elseif($rpPrem[$kdData.$angk5]==round($rPremiPanen['rupiahpremi'])){
                                                $keg="prpanen01";
                                                $hslRup[$rPremiPanen['karyawanid'].$keg]=$rPremiPanen['rupiahpremi'];
                                                $hslKrj[$rPremiPanen['karyawanid'].$keg]=$rPremiPanen['totalkg'];
                                                $lstKeg[$rPremiPanen['karyawanid']][]=$keg;
                                                $komp=8;//satuan sumber dari premi panen
                                                $arrPles[$rPremiPanen['karyawanid'].$komp]+=$rPremiPanen['rupiahpremi'];
                                            }
                                        }
                             
                                      }
                                    
                         //exit("error:".$coba);
                         #loading ford dan dt
                        $sFord="select a.karyawanid,jabatan,premiinput from ".$dbname.".kebun_premikemandoran a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid "
                             . " where periode='".$periode."' and a.kodeorg='".$idAfd."' and tipekaryawan=4 and jabatan like 'LOADING%' ".$add."";
                         //echo $sFord;
                        $qForm=  mysql_query($sFord) or die(mysql_error($conn));
                        while($rForm=  mysql_fetch_assoc($qForm)){
                             if($rForm['premiinput']!=0){
                            $keg=$rForm['jabatan'];
                                if($rForm['jabatan']=='LOADINGFORD'){
                                    $sKg="select sum(hasilkerja) as totalkg from ".$dbname.".kebun_kehadiran_vw "
                                        . " where karyawanid='".$rForm['karyawanid']."' and kodekegiatan in('611020228','611020224') and left(tanggal,7)='".$periode."' ";
                                }else{
                                    $sKg="select sum(hasilkerja) as totalkg  from ".$dbname.".kebun_kehadiran_vw "
                                        . " where karyawanid='".$rForm['karyawanid']."' and kodekegiatan in('611020221') and left(tanggal,7)='".$periode."' ";
                                }
                                //exit("error".$sKg);
                                $qKg=  mysql_query($sKg) or die(mysql_error($conn));
                                $rKg=  mysql_fetch_assoc($qKg);
                                $pkkRpSat[$rForm['karyawanid'].$keg]=$rForm['premiinput'];
                                $lstKeg[$rForm['karyawanid']][]=$keg;
                                $hslRup[$rForm['karyawanid'].$keg]=$rForm['premiinput'];
                                $hslKrj[$rForm['karyawanid'].$keg]=$rKg['totalkg'];
                                $komp=7;//satuan sumber premi loading
                                $arrPles[$rForm['karyawanid'].$komp]+=$rForm['premiinput'];
                            }
                            
                        }
                        #dari traksi
                        $sTksi="select sum(upah) as upah,sum(premi) as premi,idkaryawan from ".$dbname.".vhc_runhk_vw "
                                . " where statuskaryawan='KHT' and tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' group by idkaryawan";
                        //echo $sTksi;
                        $qTksi=  mysql_query($sTksi) or die(mysql_error($conn));
                        while($rTksi=  mysql_fetch_assoc($qTksi)){
                            $komp=8;//tam inentif dari premi traksi
                            $arrPles[$rTksi['idkaryawan'].$komp]+=$rTksi['premi'];
                            $komp=3;//gapok dari kegiatan traksi
                            $arrPles[$rTksi['idkaryawan'].$komp]+=$rTksi['upah'];
                        }
                        #loading PREMI RAWAT dan dt
                        $sFord="select a.karyawanid,jabatan,premiinput from ".$dbname.".kebun_premikemandoran a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid "
                             . " where periode='".$periode."' and a.kodeorg='".$idAfd."' and tipekaryawan=4 and jabatan like 'PREMIRAWAT%' ".$add."";
                         //echo $sFord;
                        $qForm=  mysql_query($sFord) or die(mysql_error($conn));
                        while($rForm=  mysql_fetch_assoc($qForm)){
                           $keg=$rForm['jabatan'];
                            $pkkRpSat[$rForm['karyawanid'].$keg]=$rForm['premiinput'];
                            $lstKeg[$rForm['karyawanid']][]=$keg;
                            $hslRup[$rForm['karyawanid'].$keg]=$rForm['premiinput'];
                            $hslKrj[$rForm['karyawanid'].$keg]=$rForm['totalkg'];
                            $komp=8;//tam insentif dari traksi
                            $arrPles[$rForm['karyawanid'].$komp]+=$rForm['premiinput'];
                        }
                        
        
                        
        #HK
        $iHk="select * from ".$dbname.".sdm_hkbulanan where periode='".$periode."' and kodeorg='".$idAfd."' ";
        $nHk=mysql_query($iHk) or die (mysql_error($conn));
        while($dHk=mysql_fetch_assoc($nHk))
        {
            $hk[$dHk['karyawanid']]=$dHk['hkabsen'];
        }
        
       //print_r($hk);
        
        $iKs="select kodekegiatan from ".$dbname.".kebun_5psatuan where rupiah>0 and regional='".$_SESSION['empl']['regional']."' ";
        
        $nKs=mysql_query($iKs) or die (mysql_error($conn));
        while($dKs=  mysql_fetch_assoc($nKs))
        {
            $kegSat[$dKs['kodekegiatan']]=$dKs['kodekegiatan'];
            
        }
                        
        //$sKehadiran="select absensi,tanggal,karyawanid from ".$dbname.".kebun_kehadiran_vw 
          //                           where tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and kodeorg like '".$idAfd."%'";
        $iKehadiran="select count(distinct(tanggal)) as hari,karyawanid,kodekegiatan from ".$dbname.".kebun_kehadiran_vw "
                . "where kodekegiatan not in (select kodekegiatan from ".$dbname.".kebun_5psatuan where rupiah>0 and regional='".$_SESSION['empl']['regional']."')"
                . "and tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and unit='".$idAfd."' group by karyawanid,kodekegiatan";
        $nKehadiran=mysql_query($iKehadiran) or die (mysql_error($conn));
        while($dKehadiran=mysql_fetch_assoc($nKehadiran))
        {
            $hari[$dKehadiran['karyawanid']][$dKehadiran['kodekegiatan']]=$dKehadiran['hari'];
            
        }
                
                /*echo "<pre>";
                print_r($hari);
                echo "</pre>";
         //print_r($kegSata);*/               
                        
        $sDt="select distinct tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji "
           . " where periode='".$periode."' and kodeorg='".$idAfd."'";
        $qDt=  mysql_query($sDt) or die(mysql_error($conn));
        $rDt=  mysql_fetch_assoc($qDt);
        $tglCutblnini=$rDt['tanggalmulai'];
        $tglSmpini=explode("-",$tglCutblnini);
                foreach($arrKary as $dtKary){
                $no+=1;
                $arden='52';
                  foreach($dtAbsByr as $dtJmlhAbsDbyr){
                    $hrHadir[$dtKary]+=$brt[$dtKary][$dtJmlhAbsDbyr];
                  }
                
                                $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
                                $pdf->SetX($pdf->getX());
                                $pdf->SetFont('Arial','',11);	
                                $pdf->Cell(($width),4,"SLIP PEMBAYARAN UPAH",0,1,'C');
                                $pdf->SetFont('Arial','',9);	
                                $pdf->Cell(75,4,$_SESSION['org']['namaorganisasi'],0,1,'L');
                                $pdf->Cell(75,4,"BULAN ".strtoupper($arrBln[$idBln])." (".$tglSmpini[2]."-".substr($rDt['tanggalsampai'],-2,2).") ".$tglSmpini[0],0,1,'L');
                                $pdf->Cell(25,4,"NO.    ",0,0,'L');
                                $pdf->Cell(15,4,":  ".$no,0,0,'L');
                                $pdf->Cell(16,4,"U N I T",0,0,'L');
                                $whr="kodeorganisasi='".$idAfd."'";
                                $optNmOrg=  makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi',$whr);
                                $pdf->Cell(25,4,$idAfd." - ".$optNmOrg[$idAfd],0,1,'L');
                                $pdf->Cell(25,4,"GROUP",0,0,'L');
                                $whrr="karyawanid='".$dtKary."'";
                                $optSbbagin=  makeOption($dbname, 'datakaryawan', 'karyawanid,subbagian',$whrr);
                                $whr="kodeorganisasi='".$optSbbagin[$dtKary]."'";
                                $optNmOrg=  makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi',$whr);
                                $pdf->Cell(15,4,":  ".$optSbbagin[$dtKary],0,0,'L');
                                $pdf->Cell(41,4,$optNmOrg[$optSbbagin[$dtKary]],0,1,'L');
                                $pdf->SetFont('Arial','B',9);
                                $pdf->Cell(25,4,"NAMA / NIK    ",0,0,'L');
                                $pdf->Cell(20,4,":  ".$arrNik[$dtKary]." / ",0,0,'L');
                                $pdf->Cell(41,4,strtoupper($arrNmKary[$dtKary]),0,1,'L');
                                $pdf->Cell(25,4,"STATUS PAJAK    ",0,0,'L');
                                $pdf->Cell(15,4,":  ".$arrStataPajak[$dtKary],0,1,'L');
                               // $pdf->Cell(99,4,"",0,1,'L');
        
                                $pdf->SetFont('Arial','',8);
                                $pdf->Cell(25,4,"Hari Hadir/Kerja    ",0,0,'L');//indra
                                $pdf->Cell(3,4,":",0,0,'L');
                                $pdf->Cell(17,4,$hrHadir[$dtKary].'/'.$hk[$dtKary],0,0,'L');
                                $pdf->Cell(25,4,"Resetant TBS    ",0,0,'L');
                                $pdf->Cell(3,4,":",0,0,'L');
                                $pdf->Cell(10,4,'',0,1,'L');
                                
//                                if ($arrPotMandor[$dtKary]['potongan']!=''){
//                                    $dendaPnn[$dtKary]+=$arrPotMandor[$dtKary]['potongan'];
//                                    $arrPenambah[$dtKary]+=$dendaPnn[$dtKary];
//                                    $arrPengurang[$dtKary]+=$dendaPnn[$dtKary];
//                                }
                                $pdf->Cell(25,4,"Jam Lembur/Faktor   ",0,0,'L');
                                $pdf->Cell(3,4,":",0,0,'L');
                                $pdf->Cell(17,4,number_format($lemJamAk[$dtKary],1)." / ".number_format($lemJamLem[$dtKary],1),0,0,'L');
                                $pdf->Cell(25,4,"Denda Panen    ",0,0,'L');
                                $pdf->Cell(3,4,":",0,0,'L');
                                $pdf->Cell(10,4,number_format($dendaPnn[$dtKary],0),0,1,'L');
								 
								 $ytakhir=$pdf->GetY();
								 
                                for($mn=1;$mn<=11;$mn++){
                                    if ($mn==8){
                                        //echo $arrPles[$dtKary.$mn];
                                    }
                                    $pdf->Cell(25,4,$arrPlusId[$mn],0,0,'L');
                                    $pdf->Cell(3,4,":",0,0,'L');
                                    $pdf->Cell(17,4,number_format($arrPles[$dtKary.$mn],0),0,0,'L');
                                
                                        $idKomp=$arrKompPeng[$dtKary.$mn];
                                        if($arrPeng[$dtKary.$idKomp]!=0){
                                        $pdf->Cell(25,4,$arrNamMin[$idKomp],0,0,'L');
                                        $pdf->Cell(3,4,":",0,0,'L');
                                        $pdf->Cell(17,4,number_format($arrPeng[$dtKary.$idKomp],0),0,1,'L');
                                        }else{
                                            $pdf->Cell(33,4,$arrNamMin[$idKomp],0,1,'L');
                                        }
                                        $gapok[$dtKary]+=round($arrPles[$dtKary.$idKomp]);
                                }

                                 
                                 //exit("error".$pdf->GetX());
                                  
                                    $ytakhir=$pdf->GetY();
                                    $pdf->SetY($ytakhir-48);
                                    $xtakhir=$pdf->GetX();
                                    $pdf->SetX($xtakhir+84);
                                    //$pdf->SetFont('Arial','',5);
                                    $pdf->Cell(40,4,"PERINCIAN HASIL KERJA  ",0,0,'L');
                                    $pdf->Cell(14,4,"HASIL   ",0,0,'L');
                                    $pdf->Cell(14,4,"UNIT   ",0,0,'L');
                                    $pdf->Cell(20,4,"TOTAL UPAH   ",0,0,'L');
                                    $pdf->Cell(10,4,"FL   ",0,0,'L');
                                    $pdf->Cell(20,4,"NILAI LEMBUR   ",0,1,'l');
                                    
                                    foreach($lstKeg[$dtKary] as $baris=>$keg){
                                    $xtakhir=$pdf->GetX();
                                    $pdf->SetX($xtakhir+84);
                                
                                        $isihsl=$hslKrj[$dtKary.$keg];
                                        $isiRp=$hslKrj[$dtKary.$keg];
                                        if($optNmKeg[$keg]==''){
                                         if($optNamAbns[$keg]!='L'){
                                            $optNmKeg[$keg]=  strtoupper($optNamAbns[$keg]);
                                            $optSatKeg[$keg]="HARI";  
                                          }
                                        } 
                                        switch(trim($keg)){ 
                                            case'prpanen01':
                                                 $optNmKeg[$keg]="INSENTIF PANEN 1 TON";
                                                 $optSatKeg[$keg]="KG";
                                            break;
                                            case'prpanen05':
                                                 $optNmKeg[$keg]="INSENTIF PANEN 5 TON";
                                                 $optSatKeg[$keg]="KG";
                                            break;
                                            case'prpanen10':
                                                $optNmKeg[$keg]="INSENTIF PANEN 10 TON";
                                                $optSatKeg[$keg]="KG";
                                            break;
                                            case'prpanen15':
                                                $optNmKeg[$keg]="INSENTIF PANEN 15 TON";
                                                $optSatKeg[$keg]="KG";
                                            break;
                                            case'prpanen20':
                                                 $optNmKeg[$keg]="INSENTIF PANEN 20 TON";
                                                 $optSatKeg[$keg]="KG";
                                            break;
                                            case'LOADINGFORD':
                                                $optNmKeg[$keg]="LOADINGFORD";
                                                $optSatKeg[$keg]="KG";
                                            break;
                                            case'LOADINGDT':
                                                  $optNmKeg[$keg]="LOADINGDT";
                                                  $optSatKeg[$keg]="KG";
                                            break;
                                            case'L':
                                            $optNmKeg[$keg]="LIBUR NASIONAL";
                                            $optSatKeg[$keg]="HARI";    
                                            break;
                                            
                                            case'pnnbukithar':
                                            $optNmKeg[$keg]="PANEN BUKIT HARIAN";
                                            $optSatKeg[$keg]="JJG";    
                                            break;
                                            case'pnnbukitsat':
                                            $optNmKeg[$keg]="PANEN BUKIT SATUAN";
                                            $optSatKeg[$keg]="JJG";    
                                            break;
                                            case'pnnhar':
                                                    $optNmKeg[$keg]="PANEN HARIAN";
                                                    $optSatKeg[$keg]="JJG";
                                            break;//
                                            case'pnnsat':
                                                    $optNmKeg[$keg]="PANEN SATUAN";
                                                    $optSatKeg[$keg]="JJG";
                                            break;
                                        
                                            
                                        
                                            default:
                                                $optNmKeg[$keg]=$optNmKeg[$keg];
                                                
                                                //indra 
                                                if($kegSat[$keg]==$keg){
                                                    $optSatKeg[$keg]=$optSatKeg[$keg];
                                                    $hslKrj[$dtKary.$keg]= $hslKrj[$dtKary.$keg];
                                                }
                                                else{   
                                                     $optSatKeg[$keg]='Hari';
                                                     $hslKrj[$dtKary.$keg]=$hari[$dtKary][$keg];
                                                }
                                            break;
                                        }
                                        
                                        $nmKeg=explode(" ",$optNmKeg[$keg]);
                                        
                                        //echo $nmKeg[0]._.$nmKeg[1]._.$nmKeg[2]._.$nmKeg[3]._.$nmKeg[4]._.$nmKeg[5]._.$nmKeg[6]._.$nmKeg[7].________;
                                       
                                        $pdf->SetFont('Arial','B',5);
                                        $lemtId=5;
                                        $jamlem=$lemJamLem[$dtKary];
                                        $rpLem=$arrPles[$dtKary.$lemtId];
                                        $totHsl[$dtKary]+=round($hslRup[$dtKary.$keg]);
                                        $pdf->Cell(40,4,$optNmKeg[$keg],0,0,'L');
                                        //$pdf->MultiCell(40, 4, $optNmKeg[$keg]);
                                        
                                        //$ytakhir=$pdf->GetY();
                                        //$pdf->SetY($ytakhir-5);
                                        //$xtakhir=$pdf->GetX();
                                        //$pdf->SetX($xtakhir+124);
                                        
                                        
                                        
                                        $pdf->Cell(14,4,number_format($hslKrj[$dtKary.$keg],0),0,0,'L');
                                        $pdf->Cell(14,4,$optSatKeg[$keg],0,0,'L');
                                        $pdf->Cell(20,4,number_format($hslRup[$dtKary.$keg],0),0,0,'L');
                                        if($jamlem!=$aeut){
                                            $aeut=$jamlem;
                                            $pdf->Cell(10,4,number_format($jamlem,1),0,0,'L');
                                            $pdf->Cell(20,4,number_format($rpLem,0),0,1,'L');
                                        }else{
                                            $pdf->Cell(10,4,"",0,0,'L');
                                            $pdf->Cell(20,4,"",0,1,'L');
                                        }
                                        //$lemJamLem[$dtKary]=0;
                                        //$arrPles[$dtKary.$lemtId]=0;
                                    }
                                   // $ytakhir=$pdf->GetY();
                                    $pdf->SetY($ytakhir);
                                    
                                    $pdf->SetFont('Arial','B',8);
                        $pdf->Cell(25,4,"TOTAL BRUTTO",0,0,'L');
                        $pdf->Cell(3,4,":",0,0,'L');
                        $pdf->Cell(17,4,number_format($arrPenambah[$dtKary],0),0,0,'L');
                        $pdf->Cell(32,4,"TOTAL POTONGAN",0,0,'L');
                        $pdf->Cell(3,4,":",0,0,'L');
                        $pdf->Cell(10,4,number_format($arrPengurang[$dtKary],0),0,1,'L');
						
						$pdf->Cell(45,4,"",0,0,'L');
						
                        /*$pdf->Cell(20,4,"",0,0,'L');
                        $pdf->Cell(3,4,"",0,0,'L');
                        $pdf->Cell(12,4,"",0,0,'L');*/
						
                        $pdf->Cell(32,4,"TOTAL HASIL KERJA",0,0,'L');
                        $pdf->Cell(3,4,":",0,0,'L');
                        //$pdf->Cell(99,4,"",0,0,'L');
                        $pdf->Cell(10,4,number_format($totHsl[$dtKary],0),0,1,'L');
                        $pdf->SetFont('Arial','',10);
                        $diterma[$dtKary]=(round($arrPenambah[$dtKary])-round($arrPengurang[$dtKary]))-$arrDsmpn[$dtKary];
                        $pdf->Cell(25,4,"DITERIMA",0,0,'L');
                        $pdf->Cell(3,4,":",0,0,'L');
                        $pdf->Cell(12,4,number_format($diterma[$dtKary],0),0,0,'L');
                        if ($arrPotMandor[$dtKary]['potongan']!=''){
                            $pdf->Cell(40,4,"",0,0,'L');
                            $pdf->SetFont('Arial','I',9);
                            $pdf->Cell(20,4,'* Denda Panen : ',0,0,'L');
                            $pdf->Cell(18,4,number_format($arrPotMandor[$dtKary]['potongan'],0,'.',','),0,1,'R');
                        } else {
                            $pdf->Cell(37,4,"",0,1,'L');
                        }
                        $pdf->Cell(25,4,"TERBILANG",0,0,'L');
                        $pdf->Cell(3,4,":",0,0,'L');
                        $pdf->Cell(12,4,"# ".terbilang(round($diterma[$dtKary]),1)." #",0,0,'L');
                        $pdf->Cell(37,4,"",0,1,'L');
                        $pdf->SetFont('Arial','',9);
                        $pdf->Cell(20,4,"DIBAYAR OLEH,",0,0,'L');
                        $pdf->Cell(40,4,"",0,0,'L');
                        $pdf->Cell(20,4,"DISERAHKAN OLEH,",0,0,'L');
                        $pdf->Cell(40,4,"",0,0,'L');
                        $pdf->Cell(20,4,"PENERIMA,",0,1,'L');
                        $pdf->Ln(10);
                        $pdf->Cell(20,4,"(                              )",0,0,'L');
                        $pdf->Cell(40,4,"",0,0,'L');
                        $pdf->Cell(20,4,"(                               )",0,0,'L');
                        $pdf->Cell(40,4,"",0,0,'L');
                        $pdf->Cell(20,4,strtoupper($arrNmKary[$dtKary]),0,1,'L');
                        //$pdf->SetAutoPageBreak($auto, $margin);
                          $akhirY=$pdf->GetY();
                          $selisihtinggi=$pdf->h-$akhirY;
                          $pdf->SetY($akhirY+20);
           }
        }
        else
        {
                $pdf->Cell(60,3,'NOT FOUND','T',0,'L');
        }
        $pdf->Output();



        break;
        case'excel':
        $sAbsen="select kodeabsen from ".$dbname.".sdm_5absensi order by kodeabsen";
        $qAbsen=mysql_query($sAbsen) or die(mysql_error());
        while($rKet=mysql_fetch_assoc($qAbsen))
        {
                $klmpkAbsn[]=$rKet;
        }
            $sTgl="select distinct tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji 
                   where kodeorg='".$idAfd."' and periode='".$periode."' 
                   and jenisgaji='H'";
            //exit("error:".$sTgl);
            $qTgl=mysql_query($sTgl) or die(mysql_error($conn));
            $rTgl=mysql_fetch_assoc($qTgl);

            $test = dates_inbetween($rTgl['tanggalmulai'], $rTgl['tanggalsampai']);
            ##tambahan absen permintaan dari pak ujang#
            $sAbsn="select absensi,tanggal,karyawanid from ".$dbname.".sdm_absensidt 
                            where tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and kodeorg like '".$idAfd."%'";
                         //exit("Error".$sAbsn);
                        $rAbsn=fetchData($sAbsn);
                        foreach ($rAbsn as $absnBrs =>$resAbsn)
                        {
                                if(!is_null($resAbsn['absensi']))
                                {
                                        $hasilAbsn[$resAbsn['karyawanid']][$resAbsn['tanggal']][]=array(
                'absensi'=>$resAbsn['absensi']);
                                $resData[$resAbsn['karyawanid']][]=$resAbsn['karyawanid'];
                                }

                        }

                        $sKehadiran="select absensi,tanggal,karyawanid from ".$dbname.".kebun_kehadiran_vw 
                                     where tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and kodeorg like '".$idAfd."%'";
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
                        $sPrestasi="select a.nik,b.tanggal from ".$dbname.".kebun_prestasi a left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi 
                                    where b.notransaksi like '%PNN%' and b.kodeorg like '".$idAfd."%' and b.tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."'";
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
            where a.tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and b.kodeorg like '".$idAfd."%' and c.namakaryawan is not NULL
            union select tanggal,nikmandor1 FROM ".$dbname.".kebun_aktifitas a 
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".datakaryawan c on a.nikmandor1=c.karyawanid
            where a.tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and b.kodeorg like '".$idAfd."%' and c.namakaryawan is not NULL";
        // exit("Error".$dzstr);
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
            where a.tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and b.kodeorg like '".$idAfd."%' and c.namakaryawan is not NULL
            union select tanggal,keranimuat FROM ".$dbname.".kebun_aktifitas a 
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".datakaryawan c on a.keranimuat=c.karyawanid
            where a.tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and b.kodeorg like '".$idAfd."%' and c.namakaryawan is not NULL";
         //exit("Error".$dzstr);
        $dzres=mysql_query($dzstr);
        while($dzbar=mysql_fetch_object($dzres))
        {
            $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][]=array(
            'absensi'=>'H');
            $resData[$dzbar->nikmandor][]=$dzbar->nikmandor;
        }
        // ambil traksi                       
        $dzstr="SELECT a.tanggal,idkaryawan FROM ".$dbname.".vhc_runhk a
        left join ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
        where a.tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and notransaksi like '%".$idAfd."%'";
        //exit("Error".$dzstr);
        $dzres=mysql_query($dzstr);
        while($dzbar=mysql_fetch_object($dzres))
        {
        $hasilAbsn[$dzbar->idkaryawan][$dzbar->tanggal][]=array(
        'absensi'=>'H');
        $resData[$dzbar->idkaryawan][]=$dzbar->idkaryawan;
        }      
        foreach($resData as $hslBrs => $hslAkhir)
        {	
            if($hslAkhir[0]!='')
            {
                foreach($test as $barisTgl =>$isiTgl)
                {
                        $brt[$hslAkhir[0]][$hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi']]+=1;
                }
            }	
        }
        #tambahan absen permintaan abis disini#

        $bln=explode('-',$periode);
        $idBln=intval($bln[1]);	

          //array data komponen penambah dan pengurang
          $sKomp="select id,name from ".$dbname.".sdm_ho_component where plus='1'  and id not in ('26','28') ";
          $qKomp=mysql_query($sKomp) or die(mysql_error());
          while($rKomp=mysql_fetch_assoc($qKomp))
          {
              $arrIdKompPls[]=$rKomp['id'];
              $arrNmKomPls[$rKomp['id']]=$rKomp['name'];
          }
          $totPlus=count($arrIdKompPls);
          $brsPlus=0;
          $sKomp="select id,name from ".$dbname.".sdm_ho_component where plus='0'  ";
          $qKomp=mysql_query($sKomp) or die(mysql_error());
          while($rKomp=mysql_fetch_assoc($qKomp))
          {
              $arrIdKompMin[]=$rKomp['id'];
              $arrNmKomMin[$rKomp['id']]=$rKomp['name'];
          }

                        $sPeriod="select tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji where jenisgaji='H' and periode='".$periode."' and kodeorg='".$idAfd."'";	
                        $qPeriod=mysql_query($sPeriod) or die(mysql_error());
                        $rPeriod=mysql_fetch_assoc($qPeriod);
                        $mulai=tanggalnormal($rPeriod['tanggalmulai']);
                        $selesi=tanggalnormal($rPeriod['tanggalsampai']);

                        $stream.="
                        <table>
                        <tr><td colspan=15 align=center>List Data Gaji Harian, Unit : ".$idAfd."</td></tr>
                        <tr><td colspan=15 align=center>Periode : ".$mulai." s.d. ".$selesi."</td></tr>
                        </table>
                        <table border=1>
                        <tr>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>No.</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['namakaryawan']."</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['nik']."/".$_SESSION['lang']['tmk']."</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['unit']."/".$_SESSION['lang']['bagian']."</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>No. Rekening</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['totLembur']."</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['tipekaryawan']."</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['statuspajak']."</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['jabatan']."</td>";
                        //absen di bayar
                        $shkdbyr="select distinct kodeabsen from ".$dbname.".sdm_5absensi where kelompok=1 and kodeabsen!='MG' order by kodeabsen";
                        $qhkdbyr=mysql_query($shkdbyr) or die(mysql_error($conn));
                        $rowabs=mysql_num_rows($qhkdbyr);
                        //absen tidak di bayar
                        $shkdbyr2="select distinct kodeabsen from ".$dbname.".sdm_5absensi where kelompok=0 or kodeabsen='MG' order by kodeabsen";
                        $qhkdbyr2=mysql_query($shkdbyr2) or die(mysql_error($conn));
                        $rowabs2=mysql_num_rows($qhkdbyr2);

                        $stream.="<td bgcolor=#DEDEDE align=center  colspan='".($rowabs+1)."'>".$_SESSION['lang']['hkdibayar']."</td>";
                        $stream.="<td bgcolor=#DEDEDE align=center colspan='".($rowabs2+1)."'>".$_SESSION['lang']['hktdkdibayar']."</td>";
                        $plsCol=count($arrIdKompPls);
                        $minCol=count($arrIdKompMin);
                        $stream.="<td bgcolor=#DEDEDE align=center colspan='".($plsCol+3)."'>".$_SESSION['lang']['penambah']."</td>";
                        $stream.="<td bgcolor=#DEDEDE align=center colspan='".($minCol-1)."'>".$_SESSION['lang']['pengurang']."</td>";
                        $stream.="<td bgcolor=#DEDEDE align=center rowspan='2'>GAJI BERSIH</td></tr><tr>";
                        while($rdbyr=mysql_fetch_assoc($qhkdbyr)){
                            $stream.="<td bgcolor=#DEDEDE align=center>".$rdbyr['kodeabsen']."</td>";
                            $dtAbsByr[]=$rdbyr['kodeabsen'];
                        }
                        $stream.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['total']."</td>";
                        while($rdbyr=mysql_fetch_assoc($qhkdbyr2)){
                            $stream.="<td bgcolor=#DEDEDE align=center>".$rdbyr['kodeabsen']."</td>";
                            $dtAbsTdkByr[]=$rdbyr['kodeabsen'];
                        }
                           $stream.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['total']."</td>";
                        foreach($arrIdKompPls as $lstKompPls)
                                {
                                    $brsPlus++;
                                    $stream.="<td bgcolor=#DEDEDE align=center>".$arrNmKomPls[$lstKompPls]."</td>";
                                    if($brsPlus==1)
                                    {
                                        $stream.="<td bgcolor=#DEDEDE align=center>".$arrNmKomMin[37]."</td>";
                                        $stream.="<td bgcolor=#DEDEDE align=center>".$arrNmKomMin[36]."</td>";
                                    }

                                }
                        $stream.="<td bgcolor=#DEDEDE align=center >".$_SESSION['lang']['totalPendapatan']."</td>";

                                foreach($arrIdKompMin as $lstKompMin)
                                {
                                    if(($lstKompMin!=37)&&($lstKompMin!=36))
                                    {
                                         $stream.="<td bgcolor=#DEDEDE align=center>".$arrNmKomMin[$lstKompMin]."</td>";
                                    }
                                }			

                      $stream.="<td bgcolor=#DEDEDE align=center >".$_SESSION['lang']['totalPotongan']."</td></tr>";

                         //prepare array data gaji karyawan,nama,jabatan,tmk dan bagian
         $sSlip="select distinct a.*,b.tipekaryawan,b.statuspajak,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama,
                b.norekeningbank from ".$dbname.".sdm_gaji_vw a  left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan 
               left join ".$dbname.".sdm_5departemen d on b.bagian=d.kode where ".$where."  ".$dtTipe." and b.tipekaryawan=4";	
        // exit("Error:".$sSlip);
        $qSlip=mysql_query($sSlip) or die(mysql_error());
        $rCek=mysql_num_rows($qSlip);
        if($rCek>0)
        {
                while($rSlip=mysql_fetch_assoc($qSlip))
                {
                    if($rSlip['karyawanid']!='')
                    {
                    $arrKary[$rSlip['karyawanid']]=$rSlip['karyawanid'];
                    $arrKomp[$rSlip['karyawanid']]=$rSlip['idkomponen'];
                    $arrTglMsk[$rSlip['karyawanid']]=$rSlip['tanggalmasuk'];
                    $arrNik[$rSlip['karyawanid']]=$rSlip['nik'];
                    $arrNmKary[$rSlip['karyawanid']]=$rSlip['namakaryawan'];
                    $arrBag[$rSlip['karyawanid']]=$rSlip['bagian'];
                    $arrJbtn[$rSlip['karyawanid']]=$rSlip['namajabatan'];
                    $arrTipekary[$rSlip['karyawanid']]=$rSlip['tipekaryawan'];
                    $arrStatPjk[$rSlip['karyawanid']]=$rSlip['statuspajak'];
                    $arrDept[$rSlip['karyawanid']]=$rSlip['nama'];
                    $arrRek[$rSlip['karyawanid']]=$rSlip['norekeningbank'];
                    $arrJmlh[$rSlip['karyawanid'].$rSlip['idkomponen']]=$rSlip['jumlah'];
                    $arrTotal[$rSlip['idkomponen']]+=$rSlip['jumlah'];
                    }
                }
                $sTot="select tipelembur,jamaktual,karyawanid from ".$dbname.".sdm_lemburdt where substr(kodeorg,1,4)='".$idAfd."' and tanggal between '".$rPeriod['tanggalmulai']."' and '".$rPeriod['tanggalsampai']."'";		
                $qTot=mysql_query($sTot) or die(mysql_error($conn));
                while($rTot=mysql_fetch_assoc($qTot))
                {
                        $sJum="select jamlembur as totalLembur from ".$dbname.".sdm_5lembur where tipelembur='".$rTot['tipelembur']."'
                        and jamaktual='".$rTot['jamaktual']."' and kodeorg='".$idAfd."'";
                        $qJum=mysql_query($sJum) or die(mysql_error());
                        $rJum=mysql_fetch_assoc($qJum);
                        $jumTot[$rTot['karyawanid']]+=$rJum['totalLembur'];
                }
                $peng1=37;
                $peng2=36;
                    foreach($arrKary as $dtKary)
                    {			
                                $no+=1;
                                $stream.="<tr class=rowcontent>
                                <td>".$no."</td>
                                <td>".$arrNmKary[$dtKary]."</td>
                                <td>'".$arrNik[$dtKary]."</td>
                                <td>".$arrDept[$dtKary]."</td>
                                <td>".$arrRek[$dtKary]."</td>
                                <td>".$jumTot[$dtKary]."</td>
                                <td>".$rNmTipe[$arrTipekary[$dtKary]]."</td> 
                                <td>".$arrStatPjk[$dtKary]."</td>
                                <td>".$arrJbtn[$dtKary]."</td>";

                                foreach($dtAbsByr as $dtJmlhAbsDbyr){
                                    $stream.="<td align=right>".number_format($brt[$dtKary][$dtJmlhAbsDbyr])."</td>";
                                    $totAbsen[$dtKary]+=$brt[$dtKary][$dtJmlhAbsDbyr];
                                    $grTotDbyr[$dtJmlhAbsDbyr]+=$brt[$dtKary][$dtJmlhAbsDbyr];
                                }
                                $stream.="<td align=right>".number_format($totAbsen[$dtKary])."</td>";
                                foreach($dtAbsTdkByr as $dtTidakDbyr){
                                    $stream.="<td align=right>".number_format($brt[$dtKary][$dtTidakDbyr])."</td>";
                                    $totAbsenTdkDbyr[$dtKary]+=$brt[$dtKary][$dtTidakDbyr];
                                    $grTotTdkDbyr[$dtTidakDbyr]+=$brt[$dtKary][$dtTidakDbyr];
                                }
                                $stream.="<td align=right>".number_format($totAbsenTdkDbyr[$dtKary])."</td>";

                                $arrPlus=Array();
                                $s=0;
                                $brsPlus2=0;
                                foreach($arrIdKompPls as $lstKompPls)
                                {

                                    $stream.="<td align=right>".number_format($arrJmlh[$dtKary.$lstKompPls],2)."</td>";
                                    $arrPlus[$s]=$arrJmlh[$dtKary.$lstKompPls];
                                    $s++;
                                    $brsPlus2++;
                                    if($brsPlus2==1)
                                    {

                                        $stream.="<td>-".number_format($arrJmlh[$dtKary.$peng1],2)."</td>";
                                        $stream.="<td>-".number_format($arrJmlh[$dtKary.$peng2],2)."</td>";
                                    }

                                }

                                $totDpt=array_sum($arrPlus)-($arrJmlh[$dtKary.$peng1]+$arrJmlh[$dtKary.$peng2]);
                                $stream.="<td align=right>".number_format($totDpt,2)."</td>";


                                $arrMin=Array();
                                $q=0;
                                foreach($arrIdKompMin as $lstKompMin)
                                {
                                    if(($lstKompMin!=37)&&($lstKompMin!=36))
                                    {
                                         $stream.="<td align=right>".number_format($arrJmlh[$dtKary.$lstKompMin])."</td>";
                                         $arrMin[$q]=$arrJmlh[$dtKary.$lstKompMin];
                                         $q++;
                                    }
                                }
                                $gajiBersih=$totDpt-array_sum($arrMin);				

                                //$stream.="<td align=right>".number_format(array_sum($arrPlus),2)."</td>";
                                $stream.="<td align=right>".number_format(array_sum($arrMin),2)."</td>";
                                $stream.="<td align=right>".number_format($gajiBersih,0)."</td></tr>";	

                                }
                                $stream.="<tr><td colspan=".(9+$rowabs+$rowabs2+2)." align=right>".$_SESSION['lang']['total']."</td>";

                                $s=0;
                                $brsPlus2=0;
                                $arrPlus=array();
                                foreach($arrIdKompPls as $lstKompPls)
                                {
                                    $stream.="<td align=right>".number_format($arrTotal[$lstKompPls],2)."</td>";
                                    $arrPlus[$s]=$arrTotal[$lstKompPls];
                                    $s++;
                                    $brsPlus2++;
                                    if($brsPlus2==1)
                                    {

                                        $stream.="<td>-".number_format($arrTotal[$peng1],2)."</td>";
                                        $stream.="<td>-".number_format($arrTotal[$peng2],2)."</td>";
                                    }
                                }
                                $totDpt=array_sum($arrPlus)-($arrTotal[$peng1]+$arrTotal[$peng2]);
                                $stream.="<td align=right>".number_format($totDpt,2)."</td>";


                                $arrMin=Array();
                                $q=0;
                                foreach($arrIdKompMin as $lstKompMin)
                                {
                                    if(($lstKompMin!=37)&&($lstKompMin!=36))
                                    {
                                         $stream.="<td align=right>".number_format($arrTotal[$lstKompMin])."</td>";
                                         $arrMin[$q]=$arrTotal[$lstKompMin];
                                         $q++;
                                    }
                                }
                                $gajiBersih=$totDpt-array_sum($arrMin);				

                                //$stream.="<td align=right>".number_format(array_sum($arrPlus),2)."</td>";
                                $stream.="<td align=right>".number_format(array_sum($arrMin),2)."</td>";
                                $stream.="<td align=right>".number_format($gajiBersih,0)."</td>";	
                                $stream.="</tr>";
                }

                        //=================================================


                        $stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];

                        $dte=date("YmdHms");
                        $nop_="GajiHarian".$idAfd.$dte;
                         $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
                         gzwrite($gztralala, $stream);
                         gzclose($gztralala);
                         echo "<script language=javascript1.2>
                            window.location='tempExcel/".$nop_.".xls.gz';
                            </script>";
        break;
        default:
        break;
}
?>