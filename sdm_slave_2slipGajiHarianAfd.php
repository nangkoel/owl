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

$proses=$_GET['proses'];

$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['period']==''?$period=$_GET['period']:$period=$_POST['period'];
$_POST['perod']==''?$perod=$_GET['perod']:$perod=$_POST['perod'];
$_POST['idKry']==''?$idKry=$_GET['idKry']:$idKry=$_POST['idKry'];
$_POST['tPkary2']==''?$tPkary=$_GET['tPkary2']:$tPkary=$_POST['tPkary2'];
$arrBln=array(1=>"Januari",2=>"Februari",3=>"Maret",4=>"April",5=>"Mei",6=>"Juni",7=>"Juli",8=>"Agustus",9=>"September",10=>"Oktober",11=>"November",12=>"Desember");
$_POST['idAfd']==''?$idAfd=$_GET['idAfd']:$idAfd=$_POST['idAfd'];

// Cek Proses Pembulatan Gaji sudah dilakukan atau belum
if ($tPkary!=''){
    $str="select count(*) as hitung from ".$dbname.".sdm_gaji a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
            WHERE b.tipekaryawan=".$tPkary." AND idkomponen=61 AND periodegaji='".$perod."' AND kodeorg='".$idAfd."'";
} else {
    $str="select count(*) as hitung from ".$dbname.".sdm_gaji
            WHERE kodeorg='".$idAfd."' AND idkomponen=61 AND periodegaji='".$perod."'";
}
//echo $str;
$rCek=fetchData($str);
if ($rCek[0]['hitung']==0){
    exit("error: Proses Pembulatan Gaji belum dilakukan.");
}


$rNmTipe=makeOption($dbname, 'sdm_5tipekaryawan', 'id,tipe');
$lksiTgs=substr($idAfd,0,4);
$kdBag2=$_POST['kdBag2'];
 if($_SESSION['empl']['tipelokasitugas']=='HOLDING'||$_SESSION['empl']['tipelokasitugas']=='KANWIL')
                {
                    if($idAfd!='')
                    {
                     $add="b.lokasitugas='".$idAfd."' ";
                    }
                    else
                    {
                        exit("Error: Work unit required");
                    }


                    if($kdBag2!='')
                    {
                     $add.=" and b.bagian='".$kdBag2."'";
                    }
                }
                else
                {
                if(strlen($idAfd)<6)
                {
                    $add="b.lokasitugas='".$idAfd."' and (b.subbagian is null or b.subbagian='') ";
                }
                else
                {
                    $add="b.subbagian='".$idAfd."'";
                }
                                if($kdBag2!='')
                                {
                                        $add.=" and b.bagian='".$kdBag2."'";
                                }
                }
                if($tPkary!='')
                {
                    $dtTipe=" and b.tipekaryawan='".$tPkary."'";
                }
switch($proses)
{
        case'preview':

                $sOrg="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$idAfd."'";
                $qOrg=mysql_query($sOrg) or die(mysql_error($conn));
                $rOrg=mysql_fetch_assoc($qOrg);
                if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }


        //periode gaji
        $bln=explode('-',$perod);
        $idBln=intval($bln[1]);	

        //prepare array data gaji karyawan,nama,jabatan,tmk dan bagian
        $sSlip="select distinct a.*,b.tipekaryawan,b.statuspajak,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama,e.kodeorg templok from 
               ".$dbname.".sdm_gaji_vw a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
               left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan 
               left join ".$dbname.".sdm_5departemen d on b.bagian=d.kode
               left join ".$dbname.".setup_temp_lokasitugas e on e.karyawanid=b.karyawanid
               where b.sistemgaji='Harian' and a.periodegaji='".$perod."' and ".$add." ".$dtTipe."";
        //exit("Error".$sSlip);
        $qSlip=mysql_query($sSlip) or die(mysql_error());
        $rCek=mysql_num_rows($qSlip);
        if($rCek>0)
        {
                $trig=1;
                while($rSlip=mysql_fetch_assoc($qSlip))
                {
                    if($trig==1){
                        $trig=2;
                        #ambil karyawan yang pindah lokasi tugas
                        $scek="select a.* from ".$dbname.".setup_temp_lokasitugas a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid "
                            . "where a.kodeorg='".$idAfd."'";
                        $qcek=mysql_query($scek) or die(mysql_error($conn));
                        while($rcek=mysql_fetch_assoc($qcek)){
                            $sTemp="select distinct a.*,b.tipekaryawan,b.statuspajak,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama from 
                            ".$dbname.".sdm_gaji_vw a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
                            left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan 
                            left join ".$dbname.".sdm_5departemen d on b.bagian=d.kode
                            where a.karyawanid='".$rcek["karyawanid"]."'";
                            $qTemp=mysql_query($sTemp) or die(mysql_error());
                            while($rTemp=mysql_fetch_assoc($qTemp)){
                                if($rTemp['karyawanid']!='') {
                                $arrKary[$rTemp['karyawanid']]=$rTemp['karyawanid'];
                                $arrKomp[$rTemp['karyawanid']]=$rTemp['idkomponen'];
                                $arrTglMsk[$rTemp['karyawanid']]=$rTemp['tanggalmasuk'];
                                $arrNik[$rTemp['karyawanid']]=$rTemp['nik'];
                                $arrNmKary[$rTemp['karyawanid']]=$rTemp['namakaryawan'];
                                $arrBag[$rTemp['karyawanid']]=$rTemp['bagian'];
                                $arrJbtn[$rTemp['karyawanid']]=$rTemp['namajabatan'];
                                $arrDept[$rTemp['karyawanid']]=$rTemp['nama'];
                                $arrJmlh[$rTemp['karyawanid'].$rTemp['idkomponen']]=$rTemp['jumlah'];
                                }
                            }
                        } 
                    } // end cek
                    if($rSlip['karyawanid']!='') {
                        if ($rSlip['templok']=='' || $rSlip['templok']==$idAfd){
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
                }
          //array data komponen penambah dan pengurang
          $sKomp="select id,name from ".$dbname.".sdm_ho_component where plus='1'  and id not in ('26','28')  ";
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
          $jmlhKary=count($arrKary);
        //  exit("Error".$sSlip);
                  foreach($arrKary as $dtKary)
                  {
                      echo"<table cellspacing=1 border=0 width=500>
                        <tr style='border-bottom:#000 solid 2px; border-top:#000 solid 2px;'><td valign=top>
                        <table border=0 width=110%>
                        <tr><td width=49% valign=top><table border=0><tr><td colspan=3>".$_SESSION['lang']['slipGaji'].": ".$arrBln[$idBln]."-".$bln[0]."</td></tr>
                        <tr><td>".$_SESSION['lang']['nik']."/".$_SESSION['lang']['tmk']."</td><td>:</td><td>".$arrNik[$dtKary]."/".tanggalnormal($arrTglMsk[$dtKary])."</td></tr>
                        <tr><td>".$_SESSION['lang']['nama']."</td><td>:</td><td>".$arrNmKary[$dtKary]."</td></tr>
                        </table></td><td width=51% valign=top><table border=0>
                        <tr><td colspan=3>&nbsp;</td></tr>
                        <tr><td>".$_SESSION['lang']['unit']."/".$_SESSION['lang']['bagian']."</td><td>:</td><td>".$rOrg['namaorganisasi']."/".$arrBag[$dtKary]."</td></tr>
                        <tr><td>".$_SESSION['lang']['jabatan']."</td><td>:</td><td>".$arrJbtn[$dtKary]."</td></tr>
                        </table></td></tr>
                        </table>
                        </td></tr>
                        <tr>
                        <td><table width=100%>
                        <thead>
                        <tr><td align=center>".$_SESSION['lang']['penambah']."</td><td align=center>".$_SESSION['lang']['pengurang']."</td>
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
                      echo"</table></td>
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
                echo" Not Found";
        }

        break;
        case'pdf':
        $perod=$_GET['perod'];
        $idAfd=$_GET['idAfd'];
        $idKry=$_GET['idKry'];
        $kdBag2=$_GET['kdBag2'];

        //+++++++++++++++++++++++++++++++++++++++++++++++++++++
//create Header

class PDF extends FPDF
{
var $col=0;
var $dbname;

        function Header()
        {    
                //$this->lMargin=5;  
        }

//        function Footer()
//        {
//            $this->SetY(-15);
//            $this->SetFont('Arial','I',8);
//            $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
//        }
}
        $pdf=new PDF('P','mm','letter');
        $pdf->AddPage();
        $pdf->SetFont('Arial','',5);
        //periode gaji
        $bln=explode('-',$perod);
        $idBln=intval($bln[1]);	

        //prepare array data gaji karyawan,nama,jabatan,tmk dan bagian
        $sSlip="select distinct a.*,b.tipekaryawan,b.statuspajak,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama,b.lokasitugas,b.statuspajak from 
               ".$dbname.".sdm_gaji_vw a  left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
               left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan 
               left join ".$dbname.".sdm_5departemen d on b.bagian=d.kode
               where b.sistemgaji='Harian' and a.periodegaji='".$perod."' and ".$add."  ".$dtTipe."";
        $qSlip=mysql_query($sSlip) or die(mysql_error());
        $rCek=mysql_num_rows($qSlip);
        if($rCek>0)
        {
                $trig=1;
                while($rSlip=mysql_fetch_assoc($qSlip))
                {
                    if($trig==1){
                        $trig=2;
                        #ambil karyawan yang pindah lokasi tugas
                        $scek="select a.* from ".$dbname.".setup_temp_lokasitugas a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid "
                            . "where a.kodeorg='".$idAfd."'";
                        $qcek=  mysql_query($scek) or die(mysql_error($conn));
                        while($rcek=mysql_fetch_assoc($qcek)){
                            $sTemp="select distinct a.*,b.tipekaryawan,b.statuspajak,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama from 
                            ".$dbname.".sdm_gaji_vw a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
                            left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan 
                            left join ".$dbname.".sdm_5departemen d on b.bagian=d.kode
                            where a.karyawanid='".$rcek["karyawanid"]."'";
                            $qTemp=mysql_query($sTemp) or die(mysql_error());
                            while($rTemp=mysql_fetch_assoc($qTemp)){
                                if($rTemp['karyawanid']!='') {
                                $arrKary[$rTemp['karyawanid']]=$rTemp['karyawanid'];
                                $arrKomp[$rTemp['karyawanid']]=$rTemp['idkomponen'];
                                $arrTglMsk[$rTemp['karyawanid']]=$rTemp['tanggalmasuk'];
                                $arrNik[$rTemp['karyawanid']]=$rTemp['nik'];
                                $arrNmKary[$rTemp['karyawanid']]=$rTemp['namakaryawan'];
                                $arrLok[$rTemp['karyawanid']]=$idAfd;
                                $arrBag[$rTemp['karyawanid']]=$rTemp['bagian'];
                                $arrJbtn[$rTemp['karyawanid']]=$rTemp['namajabatan'];
                                $arrDept[$rTemp['karyawanid']]=$rTemp['nama'];
                                $arrStataPajak[$rTemp['karyawanid']]=$rTemp['statuspajak'];
                                if($rTemp['idkomponen']!=61){
                                    $arrJmlh[$rTemp['karyawanid'].$rTemp['idkomponen']]=$rTemp['jumlah'];
                                }else{
                                    $arrJmlhSimp[$rTemp['karyawanid'].$rTemp['idkomponen']]=$rTemp['jumlah'];
                                }
                                }
                            }
                        } 
                    } // end cek
                    if($rSlip['karyawanid']!=''){
                        if ($rSlip['templok']=='' || $rSlip['templok']==$idAfd){
                            $arrKary[$rSlip['karyawanid']]=$rSlip['karyawanid'];
                            $arrKomp[$rSlip['karyawanid']]=$rSlip['idkomponen'];
                            $arrTglMsk[$rSlip['karyawanid']]=$rSlip['tanggalmasuk'];
                            $arrNik[$rSlip['karyawanid']]=$rSlip['nik'];
                            $arrNmKary[$rSlip['karyawanid']]=$rSlip['namakaryawan'];
                            $arrLok[$rSlip['karyawanid']]=$rSlip['lokasitugas'];
                            $arrBag[$rSlip['karyawanid']]=$rSlip['bagian'];
                            $arrJbtn[$rSlip['karyawanid']]=$rSlip['namajabatan'];
                            $arrDept[$rSlip['karyawanid']]=$rSlip['nama'];
                            $arrStataPajak[$rSlip['karyawanid']]=$rSlip['statuspajak'];
                            if($rSlip['idkomponen']!=61){
                                $arrJmlh[$rSlip['karyawanid'].$rSlip['idkomponen']]=$rSlip['jumlah'];
                            }else{
                                $arrJmlhSimp[$rSlip['karyawanid'].$rSlip['idkomponen']]=$rSlip['jumlah'];
                            }
                        }
                    }
                }

          //array data komponen penambah dan pengurang
          $sKomp="select id,name,plus from ".$dbname.".sdm_ho_component where plus=1 ";
          $qKomp=mysql_query($sKomp) or die(mysql_error());
          while($rKomp=mysql_fetch_assoc($qKomp))
          {
              $arrIdKompPls[]=$rKomp['id'];
              $arrNmKomPls[$rKomp['id']][1]=$rKomp['name'];
          }
          $sKomp2="select id,name,plus from ".$dbname.".sdm_ho_component where plus=0 ";
          $qKomp2=mysql_query($sKomp2) or die(mysql_error());
          while($rKomp2=mysql_fetch_assoc($qKomp2))
          {
              $arrIdKompPls[]=$rKomp2['id'];
              $arrNmKomPls[$rKomp2['id']][0]=$rKomp2['name'];
          }
           $arrValPlus=Array();
           $arrValMinus=Array();
           $str3="select jumlah,idkomponen,a.karyawanid,c.plus from ".$dbname.".sdm_gaji_vw a 
                  left join ".$dbname.".sdm_ho_component c on a.idkomponen=c.id
                 where a.sistemgaji='Harian' and a.periodegaji='".$perod."' group by a.karyawanid,idkomponen";
           //exit("Error:".$str3);
           $res3=mysql_query($str3,$conn);
           while($bar3=mysql_fetch_assoc($res3))
           {
               if($bar3['plus']=='1')
               {
                    if($bar3['jumlah']!='')
                    {
                        $arrValPlus[$bar3['karyawanid']][$bar3['idkomponen']]=$bar3['jumlah'];
                        if ($bar3['idkomponen']!='28' && $bar3['idkomponen']!='71'){
                            $totBrutto[$bar3['karyawanid']]+=$bar3['jumlah'];
                        }
                    }
               }
               elseif($bar3['plus']=='0')
               {
                    if($bar3['jumlah']!='')
                    {
                        $arrValMinus[$bar3['karyawanid']][$bar3['idkomponen']]=$bar3['jumlah'];
                        $totPengurang[$bar3['karyawanid']]+=$bar3['jumlah'];
                    }
               } 
            }	 
        $prd=explode("-",$perod);
        if($prd[1]-1==0){
            $prdlalu=($prd[0]-1)."-12";
        }else{
            $bln=strlen(($prd[1]-1))>1?($prd[1]-1):"0".($prd[1]-1);
            $prdlalu=$prd[0]."-".$bln;
        }
        #ambil cut off bulan lalu
        $sDt="select distinct tglcutoff from ".$dbname.".sdm_5periodegaji where periode='".$prdlalu."' and kodeorg='".substr($idAfd,0,4)."'";
        $qDt=  mysql_query($sDt) or die(mysql_error($conn));
        $rDtLalu=  mysql_fetch_assoc($qDt);
        $tglCutblnlalu=$rDtLalu['tglcutoff'];
        $tglcutblnIni=nambahHari(tanggalnormal($tglCutblnlalu),1,1);//ditambahkan satu hari dari hari cut off untuk perhitungan lembur dan premi
        $tglKmrnini=explode("-",$tglcutblnIni); 
        
        #ambil cut off bulan ini
        $sDt="select distinct tglcutoff from ".$dbname.".sdm_5periodegaji where periode='".$perod."' and kodeorg='".substr($idAfd,0,4)."'";
        $qDt=  mysql_query($sDt) or die(mysql_error($conn));
        $rDt=  mysql_fetch_assoc($qDt);
        $tglCutblnini=$rDt['tglcutoff'];
        $tglSmpini=explode("-",$tglCutblnini);
        foreach($arrKary as $dtKary){
        $no+=1;
          //komponen
            $arrMinusId=Array();
            $arrMinusName=Array();
            //$str="select id,name from ".$dbname.".sdm_ho_component where plus='0' order by id";
            $str="select distinct id,name from ".$dbname.".sdm_ho_component  a
                   left join ".$dbname.".sdm_gaji_vw b on a.id=b.idkomponen
                   where plus=0 and jumlah!=0 and b.periodegaji='".$perod."' 
                   and b.kodeorg='".substr($idAfd,0,4)."' and karyawanid='".$dtKary."' order by id";
            // echo $str;exit();
            $res=mysql_query($str,$conn);
            while($bar=mysql_fetch_object($res))
            {
                array_push($arrMinusId,$bar->id);
                array_push($arrMinusName,$bar->name);
            }
            //samakan
            $arrPlusId=$arrMinusId;
            $arrPlusName=$arrMinusName;
            //Kosongkan
            for($r=0;$r<count($arrMinusId);$r++)
            {
                 $arrPlusId[$r]='';
                 $arrPlusName[$r]='';
            }
            //$str="select  id,name from ".$dbname.".sdm_ho_component where plus='1'  and id not in ('26','28') order by id";
            $str="select distinct id,name from ".$dbname.".sdm_ho_component  a
                   left join ".$dbname.".sdm_gaji_vw b on a.id=b.idkomponen
                   where plus=1 and jumlah!=0 and b.periodegaji='".$perod."' 
                   and b.kodeorg='".substr($idAfd,0,4)."' and karyawanid='".$dtKary."' and id not in ('26','28')  order by id";
            $res=mysql_query($str,$conn);
            $n=-1;
            while($bar=mysql_fetch_object($res))
            {
                $n+=1;
                $arrPlusId[$n]=$bar->id;
                $arrPlusName[$n]=$bar->name;
            }
                        $pdf->SetX($pdf->getX());
                        $pdf->SetFont('Arial','',10);	
                        $pdf->Cell(75,4,$_SESSION['org']['namaorganisasi'],0,1,'L');
                        $pdf->Cell(75,4,"SLIP TANDA TERIMA UPAH / GAJI BULAN ".strtoupper($arrBln[$idBln])." ".$tglSmpini[0],0,1,'L');
                        $pdf->Cell(75,4,"PERHITUNGAN PERIODE   LEMBUR / SATUAN   ".$tglKmrnini[2]."  ".strtoupper($arrBln[intval($tglKmrnini[1])])."  ".$tglKmrnini[0]." - ".$tglSmpini[2]."  ".strtoupper($arrBln[intval($tglSmpini[1])])."  ".$tglSmpini[0],0,1,'L');
                        $pdf->Cell(150,4,"==================================================================================================",'0',1,'L');
                        //$pdf->Cell(150,4,$_SESSION['lang']['slipGaji'].': '.$arrBln[$idBln]."-".$bln[0],'T',0,'L');	
                        $pdf->Cell(16,4,"NO.    ".$no,0,0,'L');
                        $pdf->Cell(16,4,"N I K",0,0,'L');
                        $pdf->Cell(25,4,$arrNik[$dtKary],0,0,'L');
                        $pdf->Cell(30,4," ",0,0,'L');
                        $pdf->Cell(25,4,$_SESSION['lang']['nama'],0,0,'L');
                        $pdf->Cell(60,4,strtoupper($arrNmKary[$dtKary]),0,0,'L');
                        
                        $pdf->Cell(25,4,$_SESSION['lang']['statuspajak'],0,0,'L');
						$pdf->Cell(16,4,$arrStataPajak[$dtKary],0,1,'L');
                        
                        $pdf->Cell(16,4,'',0,0,'L');
                        $pdf->Cell(16,4,"U N I T",0,0,'L');
                        $whr="kodeorganisasi='".substr($idAfd,0,4)."'";
                        $optNmOrg=  makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi',$whr);
                        $pdf->Cell(25,4,substr($idAfd,0,4)." - ".$optNmOrg[substr($idAfd,0,4)],0,0,'L');
                        $pdf->Cell(30,4,'',0,0,'L');
                        $pdf->Cell(25,4,$_SESSION['lang']['bagian'],0,0,'L');
                        $pdf->Cell(35,4,strtoupper($arrDept[$dtKary]),0,1,'L');
                        $pdf->Cell(150,4,"==================================================================================================",'0',1,'L');
                        //$pdf->Cell(16,4,"STATUS ".$arrStataPajak[$dtKary],0,0,'L');
        $pdf->Cell(16,4,'',0,0,'L');
                         for($mn=0;$mn<count($arrPlusId);$mn++){	
                             if($mn!=0){
                                 $pdf->Cell(16,4," ",0,0,'L');
                             }
                             if($arrValPlus[$dtKary][$arrPlusId[$mn]]!=0){
                             $pdf->Cell(55,4,$arrPlusName[$mn],0,0,'L');
                             $pdf->Cell(5,4,":Rp.",0,0,'L');
                             $pdf->Cell(20,4,number_format($arrValPlus[$dtKary][$arrPlusId[$mn]],0,'.',','),'0',0,'R');
                                $arrPlus[$dtKary]+=$arrValPlus[$dtKary][$arrPlusId[$mn]];
                             }else{
                                if($arrPlusName[$mn]!=''){
                                 $pdf->Cell(55,4,$arrPlusName[$mn],0,0,'L');
                                 $pdf->Cell(5,4,":Rp",0,0,'L');
                                 $pdf->Cell(20,4,"0",'0',0,'R');
                                }else{
                                 $pdf->Cell(55,4,"",0,0,'L');
                                 $pdf->Cell(5,4,"",0,0,'L');
                                 $pdf->Cell(20,4,"",'0',0,'R');
                                }
                             }
                             
                             $pdf->Cell(15,4,'','0',0,'R');
                             if($arrValMinus[$dtKary][$arrMinusId[$mn]]!=0){
                                $pdf->Cell(55,4,$arrMinusName[$mn],0,0,'L');
                                $pdf->Cell(5,4,":Rp.",0,0,'L');
                                $pdf->Cell(20,4,number_format(($arrValMinus[$dtKary][$arrMinusId[$mn]]),0,'.',','),0,1,'R');
                                if($arrMinusId[$mn]!=61){
                                  $arrMin[$dtKary]+=$arrValMinus[$dtKary][$arrMinusId[$mn]];
                                }
                                
                             }else{
                                if($arrMinusName[$mn]!=''){
                                    $pdf->Cell(55,4,$arrMinusName[$mn],0,0,'L');
                                    $pdf->Cell(5,4,":Rp.",0,0,'L');
                                    $pdf->Cell(20,4,"0",0,1,'R');
                                }else{
                                    $pdf->Cell(55,4,"",0,0,'L');
                                    $pdf->Cell(5,4,"",0,0,'L');
                                    $pdf->Cell(20,4,"",0,1,'R');

                                }
                             }
                         }
                    $peng2="37";
                    
                    $totDpt[$dtKary]=array_sum($arrPlus[$dtKary]);
                  $pdf->Cell(16,4," ",0,0,'L');
                  $pdf->Cell(55,4,"Total Brutto",0,0,'L');
                  $pdf->Cell(5,4,":Rp",0,0,'L');
                  $pdf->Cell(20,4,number_format($totBrutto[$dtKary],0,'.',','),'0',0,'R');
                  $pdf->Cell(15,4,'','0',0,'R');
                  $pdf->Cell(55,4,"Total Potongan",0,0,'L');
                  $pdf->Cell(5,4,":Rp",0,0,'L');
                  $pdf->Cell(20,4,number_format($totPengurang[$dtKary],0,'.',','),0,1,'R');
                  $pdf->Cell(16,4,"",0,0,'L');
                  $pdf->Cell(55,4,"Jumlah diterima ",0,0,'L');
                  $pdf->Cell(5,4,":Rp",0,0,'L');
                    $drkompak="61";
                    $gajiBersih=$totBrutto[$dtKary]-$totPengurang[$dtKary];	
                    $hslmod=$gajiBersih%1000;
                    $jmlhdtrima=round($gajiBersih-$arrJmlh[$dtKary.$drkompak]);
                  
                    
                  
                  $pdf->Cell(20,4,number_format($jmlhdtrima),'0',1,'R');
                  $pdf->Cell(16,4,"",0,0,'L'); 
				  $pdf->Cell(55,4,"Terbilang",0,0,'L');
				  $pdf->SetFont('Arial','IB',10);
                  //$pdf->MultiCell(130,4,"# ".terbilang($jmlhdtrima,1)." RUPIAH  #",0,'L');                
                  $pdf->cell(130,4,"# ".terbilang($jmlhdtrima,1)." RUPIAH  #",0,1,'L');
                 // $pdf->Cell(5,4,"",0,1,'L');
                  
                  //$pdf->Ln(5);
                  $pdf->Cell(16,4," ",0,0,'L');
                  $pdf->Cell(55,4,"Pembayar,",0,0,'C');
                  $pdf->Cell(5,4,"",0,0,'L');
                  $pdf->Cell(20,4,"",'0',0,'R');
                  $pdf->Cell(15,4,'','0',0,'R');
                  $pdf->Cell(55,4,"Penerima,",0,0,'C');
                  $pdf->Cell(5,4,"",0,0,'L');
                  $pdf->Cell(20,4,"",0,1,'R');
                  $pdf->Ln(6);
                  $pdf->Cell(16,4," ",0,0,'L');
                  $pdf->Cell(57,4,"(                                                         )",0,0,'C');
                  $pdf->Cell(3,4,"",0,0,'L');
                  $pdf->Cell(20,4,"",'0',0,'R');
                  $pdf->Cell(15,4,'','0',0,'R');
                  $pdf->Cell(57,4,  strtoupper($arrNmKary[$dtKary]),0,0,'C');
                  $pdf->Cell(3,4,"",0,0,'L');
                  $pdf->Cell(20,4,"",0,1,'R');
                  $akhirY=$pdf->GetY();
                  $pdf->SetY($akhirY+5);
                  
                   $barisMod=$no%2;
                   if($barisMod=='1')
                       $akhirY='132.00125';
                   else
                       $akhirY='259.00125';
                   
                   
                  
                   
                  // echo $akhirY."<br>";
                  
                  $pdf->SetY($akhirY+5);
                  //$selisihtinggi=$pdf->h-$akhirY;//error:165.39875___114.00125__279.4__
                  //exit("error:".$selisihtinggi."___".$akhirY."__".$pdf->h."__".$totTinggHal);
                    
   }
}
else
{
        $pdf->Cell(60,3,'NOT FOUND','T',0,'L');
}
        $pdf->Output();

        break;

        case'excel':

//

//

//            $mulai=tanggalnormal($rPeriod2['tglcutoff']);
//            $selesi=tanggalnormal($rPeriod['tglcutoff']);

//            //exit("error".$tglLemburawal."___".$rPeriod2['tglcutoff']);

//            $sTgl="select distinct tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji 
//                   where kodeorg='".substr($idAfd,0,4)."' and periode='".$perod."' 
//                   and jenisgaji='H'";
//            //exit("error:".$sTgl);
//            $qTgl=mysql_query($sTgl) or die(mysql_error($conn));
//            $rTgl=mysql_fetch_assoc($qTgl);
//
//            $test = dates_inbetween($tglLemburawal, $rPeriod2['tglcutoff']);
                $sAbsen="select kodeabsen from ".$dbname.".sdm_5absensi order by kodeabsen";
                $qAbsen=mysql_query($sAbsen) or die(mysql_error());
                while($rKet=mysql_fetch_assoc($qAbsen)){
                   $klmpkAbsn[]=$rKet;
                }
            if($tPkary==3){
                    $prdlalu=explode("-",$perod);
                    if($prdlalu[1]-1==0){
                        $periodeGjLalu=($prdlalu[0]-1)."-12";
                    }else{
                        $bln=strlen(($prdlalu[1]-1))>1?($prdlalu[1]-1):"0".($prdlalu[1]-1);
                        $periodeGjLalu=$prdlalu[0]."-".$bln;
                    }
                    $sPeriod="select distinct tglcutoff from ".$dbname.".sdm_5periodegaji "
                           . "where jenisgaji='H' and periode='".$perod."' and kodeorg='".substr($idAfd,0,4)."'";	
                    $qPeriod=mysql_query($sPeriod) or die(mysql_error());//periode yang sekarang
                    $rPeriod=mysql_fetch_assoc($qPeriod);
                    $sPeriod2="select distinct tglcutoff from ".$dbname.".sdm_5periodegaji "
                            . "where jenisgaji='H' and periode='".$periodeGjLalu."' and kodeorg='".substr($idAfd,0,4)."'";	
                    $qPeriod2=mysql_query($sPeriod2) or die(mysql_error());//periode bulan lalu
                    $rPeriod2=mysql_fetch_assoc($qPeriod2);
                    $tglLemburawal=nambahHari($rPeriod2['tglcutoff'],1,1);//ditambahkan satu hari dari hari cut off untuk perhitungan lembur dan premi
                    $mulai=tanggalnormal($tglLemburawal);
                    $selesi=tanggalnormal($rPeriod['tglcutoff']);
                    $rTgl['tanggalmulai']=$tglLemburawal;
                    $rTgl['tanggalsampai']=$rPeriod['tglcutoff'];
                    $test = dates_inbetween($rTgl['tanggalmulai'], $rTgl['tanggalsampai']);
                    //exit("error:".$rTgl['tanggalmulai']."___".$rTgl['tanggalsampai']);
            }else{
                $sTgl="select distinct tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji 
                       where kodeorg='".substr($idAfd,0,4)."' and periode='".$perod."' 
                       and jenisgaji='H'";
                //exit("error:".$sTgl);
                $qTgl=mysql_query($sTgl) or die(mysql_error($conn));
                $rTgl=mysql_fetch_assoc($qTgl);
                $test = dates_inbetween($rTgl['tanggalmulai'], $rTgl['tanggalsampai']);
                $mulai=tanggalnormal($rTgl['tanggalmulai']);
                $selesi=tanggalnormal($rTgl['tanggalsampai']);
            }
            
            ##tambahan absen permintaan dari pak ujang#
            if($tPkary==3){
                $sAbsn="select absensi,tanggal,karyawanid from ".$dbname.".sdm_absensidt 
                                where tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and substr(kodeorg,1,4) = '".substr($idAfd,0,4)."'";
            }else{
                $sAbsn="select absensi,tanggal,karyawanid from ".$dbname.".sdm_absensidt 
                                where tanggal like '".$perod."%' and substr(kodeorg,1,4) = '".substr($idAfd,0,4)."'";
            }
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
                if($tPkary==3){
                    $sKehadiran="select absensi,tanggal,karyawanid from ".$dbname.".kebun_kehadiran_vw 
                                 where tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and substr(kodeorg,1,4) = '".substr($idAfd,0,4)."'";
                }else{
                    $sKehadiran="select absensi,tanggal,karyawanid from ".$dbname.".kebun_kehadiran_vw 
                                 where tanggal like '".$perod."%' and substr(kodeorg,1,4) = '".substr($idAfd,0,4)."'";
                }
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
        
                if($tPkary==3){
                    $sPrestasi="select a.nik,b.tanggal from ".$dbname.".kebun_prestasi a left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi 
                                    where b.notransaksi like '%PNN%' and substr(b.kodeorg,1,4) = '".substr($idAfd,0,4)."' and b.tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."'";
                }else{
                        $sPrestasi="select a.nik,b.tanggal from ".$dbname.".kebun_prestasi a left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi 
                                    where b.notransaksi like '%PNN%' and substr(b.kodeorg,1,4) = '".substr($idAfd,0,4)."' and b.tanggal like '".$perod."%'";
                }
                        //exit("Error".$sPrestasi);
                        $rPrestasi=fetchData($sPrestasi);
                        foreach ($rPrestasi as $presBrs =>$resPres)
                        {
                            $hasilAbsn[$resPres['nik']][$resPres['tanggal']][]=array(
                            'absensi'=>'H');
                            $resData[$resPres['nik']][]=$resPres['nik'];
                        } 

        // ambil pengawas                        
               if($tPkary==3){
                $dzstr="SELECT tanggal,nikmandor FROM ".$dbname.".kebun_aktifitas a
                    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
                    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
                    where a.tanggal like '".$perod."%' and substr(b.kodeorg,1,4) = '".substr($idAfd,0,4)."' and c.namakaryawan is not NULL
                    union select tanggal,nikmandor1 FROM ".$dbname.".kebun_aktifitas a 
                    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
                    left join ".$dbname.".datakaryawan c on a.nikmandor1=c.karyawanid
                    where a.tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and substr(b.kodeorg,1,4) = '".substr($idAfd,0,4)."' and c.namakaryawan is not NULL";
               }else{
                   $dzstr="SELECT tanggal,nikmandor FROM ".$dbname.".kebun_aktifitas a
                        left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
                        left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
                        where a.tanggal like '".$perod."%' and substr(b.kodeorg,1,4) = '".substr($idAfd,0,4)."' and c.namakaryawan is not NULL
                        union select tanggal,nikmandor1 FROM ".$dbname.".kebun_aktifitas a 
                        left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
                        left join ".$dbname.".datakaryawan c on a.nikmandor1=c.karyawanid
                        where a.tanggal like '".$perod."%' and substr(b.kodeorg,1,4) = '".substr($idAfd,0,4)."' and c.namakaryawan is not NULL";
               }
        // exit("Error".$dzstr);
        $dzres=mysql_query($dzstr);
        while($dzbar=mysql_fetch_object($dzres))
        {
            $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][]=array(
            'absensi'=>'H');
            $resData[$dzbar->nikmandor][]=$dzbar->nikmandor;
        }
        // ambil administrasi      
        if($tPkary==3){
        $dzstr="SELECT tanggal,nikmandor FROM ".$dbname.".kebun_aktifitas a
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
            where a.tanggal like '".$perod."%' and substr(b.kodeorg,1,4) = '".substr($idAfd,0,4)."' and c.namakaryawan is not NULL
            union select tanggal,keranimuat FROM ".$dbname.".kebun_aktifitas a 
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".datakaryawan c on a.keranimuat=c.karyawanid
            where a.tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and substr(b.kodeorg,1,4) = '".substr($idAfd,0,4)."' and c.namakaryawan is not NULL";
        }else{
      $dzstr="SELECT tanggal,nikmandor FROM ".$dbname.".kebun_aktifitas a
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
            where a.tanggal like '".$perod."%' and substr(b.kodeorg,1,4) = '".substr($idAfd,0,4)."' and c.namakaryawan is not NULL
            union select tanggal,keranimuat FROM ".$dbname.".kebun_aktifitas a 
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".datakaryawan c on a.keranimuat=c.karyawanid
            where a.tanggal like '".$perod."%' and substr(b.kodeorg,1,4) = '".substr($idAfd,0,4)."' and c.namakaryawan is not NULL";
        }
         //exit("Error".$dzstr);
        $dzres=mysql_query($dzstr);
        while($dzbar=mysql_fetch_object($dzres))
        {
            $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][]=array(
            'absensi'=>'H');
            $resData[$dzbar->nikmandor][]=$dzbar->nikmandor;
        }
        // ambil traksi                
        if($tPkary==3){
        $dzstr="SELECT a.tanggal,idkaryawan FROM ".$dbname.".vhc_runhk a
                left join ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
                where a.tanggal between '".$rTgl['tanggalmulai']."' and '".$rTgl['tanggalsampai']."' and notransaksi like '%".substr($idAfd,0,4)."%'";
        }else{
             $dzstr="SELECT a.tanggal,idkaryawan FROM ".$dbname.".vhc_runhk a
                    left join ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
                    where a.tanggal like '".$perod."%' and notransaksi like '%".substr($idAfd,0,4)."%'";
        }
        //exit("Error".$dzstr);
        $dzres=mysql_query($dzstr) or die(mysql_error());
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
//        echo"<pre>";
//        print_r($brt);
//        echo"</pre>";
//        exit("error");
        //periode gaji
        $bln=explode('-',$perod);
        $idBln=intval($bln[1]);	

          //array data komponen penambah dan pengurang
          $sKomp="select id,name from ".$dbname.".sdm_ho_component where plus='1'  and id not in ('26','28') ";
          $qKomp=mysql_query($sKomp) or die(mysql_error());
          while($rKomp=mysql_fetch_assoc($qKomp))
          {
              $sfilter="select distinct idkomponen from ".$dbname.".sdm_gaji_vw "
                     . "where periodegaji='".$perod."' and kodeorg='".substr($idAfd,0,4)."' and idkomponen='".$rKomp['id']."'";
               //exit("error".$sfilter);
              $qfilter=  mysql_query($sfilter) or die(mysql_error($conn));
              $rfilter=  mysql_fetch_assoc($qfilter);
              if($rfilter['idkomponen']!=''){
                $arrIdKompPls[]=$rKomp['id'];
                $arrNmKomPls[$rKomp['id']]=$rKomp['name'];
              }
          }
          $totPlus=count($arrIdKompPls);
          $brsPlus=0;
          $sKomp="select id,name from ".$dbname.".sdm_ho_component where plus='0' and id<>'61'  ";
          $qKomp=mysql_query($sKomp) or die(mysql_error());
          while($rKomp=mysql_fetch_assoc($qKomp))
          {
              $sfilter="select distinct idkomponen from ".$dbname.".sdm_gaji_vw "
                     . "where periodegaji='".$perod."' and kodeorg='".substr($idAfd,0,4)."' and idkomponen='".$rKomp['id']."'";
               //exit("error".$sfilter);
              $qfilter=  mysql_query($sfilter) or die(mysql_error($conn));
              $rfilter=  mysql_fetch_assoc($qfilter);
              if($rfilter['idkomponen']!=''){
                $arrIdKompMin[]=$rKomp['id'];
                $arrNmKomMin[$rKomp['id']]=$rKomp['name'];
              }
          }
                        
                        $mulai=tanggalnormal($tglLemburawal);
                        $stream.="
                        <table>
                        <tr><td colspan=15 align=center>List Data Gaji Harian, Unit : ".$idAfd."</td></tr>
                        <tr><td colspan=15 align=center>Periode : ".$mulai." s.d. ".$selesi."</td></tr>
                        </table>
                        <table border=1>
                        <tr>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>No.</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['namakaryawan']."</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['nik']."/".$_SESSION['lang']['tmk']."</td>";
                         if($_SESSION['empl']['tipelokasitugas']=='HOLDING'||$_SESSION['empl']['tipelokasitugas']=='KANWIL')
                         {
                            $stream.="<td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['subbagian']."</td>";
                         }
                         $stream.="<td bgcolor=#DEDEDE align=center rowspan='2'>No. Rekening</td>";
                         $stream.="<td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['tipekaryawan']."</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['totLembur']."</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['unit']."/".$_SESSION['lang']['bagian']."</td>
                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['statuspajak']."</td>

                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['jabatan']."</td>";
                                //absen di bayar
                        $shkdbyr="select distinct kodeabsen from ".$dbname.".sdm_5absensi where kelompok=1 order by kodeabsen";
                        $qhkdbyr=mysql_query($shkdbyr) or die(mysql_error($conn));
                        $rowabs=mysql_num_rows($qhkdbyr);
                        //absen tidak di bayar
                        $shkdbyr2="select distinct kodeabsen from ".$dbname.".sdm_5absensi where kelompok=0 order by kodeabsen";
                        $qhkdbyr2=mysql_query($shkdbyr2) or die(mysql_error($conn));
                        $rowabs2=mysql_num_rows($qhkdbyr2);

                        $stream.="<td bgcolor=#DEDEDE align=center  colspan='".($rowabs+1)."'>".$_SESSION['lang']['hkdibayar']."</td>";
                        $stream.="<td bgcolor=#DEDEDE align=center colspan='".($rowabs2+1)."'>".$_SESSION['lang']['hktdkdibayar']."</td>";
                        $plsCol=count($arrIdKompPls);
                        $minCol=count($arrIdKompMin);
                        $stream.="<td bgcolor=#DEDEDE align=center colspan='".($plsCol+2)."'>".$_SESSION['lang']['penambah']."</td>";
                        $stream.="<td bgcolor=#DEDEDE align=center colspan='".($minCol)."'>".$_SESSION['lang']['pengurang']."</td>";
                        $stream.="<td bgcolor=#DEDEDE align=center rowspan='2'>GAJI NETTO</td>";
                        $stream.="<td bgcolor=#DEDEDE align=center rowspan='2'>GAJI DITERIMA</td>";
                        $stream.="<td bgcolor=#DEDEDE align=center rowspan='2'>SIMPANAN</td>";
                        $stream.="</tr><tr>";
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
        $sSlip="select distinct a.*,b.tipekaryawan,b.statuspajak,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama,b.subbagian,
               b.norekeningbank,e.kodeorg templok from
               ".$dbname.".sdm_gaji_vw a  left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
               left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan 
               left join ".$dbname.".sdm_5departemen d on b.bagian=d.kode
               left join ".$dbname.".setup_temp_lokasitugas e on e.karyawanid=b.karyawanid
               where b.sistemgaji='Harian' and a.periodegaji='".$perod."'  and ".$add." ".$dtTipe."";
        $qSlip=mysql_query($sSlip) or die(mysql_error());
        $rCek=mysql_num_rows($qSlip);
        if($rCek>0)
        {
                $trig=1;
                while($rSlip=mysql_fetch_assoc($qSlip))
                {
                    if($trig==1){
                        $trig=2;
                        #ambil karyawan yang pindah lokasi tugas
                        $scek="select a.* from ".$dbname.".setup_temp_lokasitugas a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid "
                            . "where a.kodeorg='".$idAfd."'";
                        $qcek=mysql_query($scek) or die(mysql_error($conn));
                        while($rcek=mysql_fetch_assoc($qcek)){
                            $sTemp="select distinct a.*,b.tipekaryawan,b.statuspajak,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama from 
                            ".$dbname.".sdm_gaji_vw a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
                            left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan 
                            left join ".$dbname.".sdm_5departemen d on b.bagian=d.kode
                            where a.karyawanid='".$rcek["karyawanid"]."'";
                            $qTemp=mysql_query($sTemp) or die(mysql_error());
                            while($rTemp=mysql_fetch_assoc($qTemp)){
                                if($rTemp['karyawanid']!='') {
                                $arrKary[$rTemp['karyawanid']]=$rTemp['karyawanid'];
                                $arrKomp[$rTemp['karyawanid']]=$rTemp['idkomponen'];
                                $arrTglMsk[$rTemp['karyawanid']]=$rTemp['tanggalmasuk'];
                                $arrNik[$rTemp['karyawanid']]=$rTemp['nik'];
                                $arrNmKary[$rTemp['karyawanid']]=$rTemp['namakaryawan'];
                                $arrBag[$rTemp['karyawanid']]=$rTemp['bagian'];
                                $arrJbtn[$rTemp['karyawanid']]=$rTemp['namajabatan'];
                                $arrTipekary[$rTemp['karyawanid']]=$rTemp['tipekaryawan'];
                                $arrStatPjk[$rTemp['karyawanid']]=$rTemp['statuspajak'];
                                $arrDept[$rTemp['karyawanid']]=$rTemp['nama'];
                                $arrSubbagian[$rTemp['karyawanid']]=$rTemp['subbagian'];
                                $arrRek[$rTemp['karyawanid']]=$rTemp['norekeningbank'];
                                $arrJmlh[$rTemp['karyawanid'].$rTemp['idkomponen']]=$rTemp['jumlah'];
                                $arrTotal[$rTemp['idkomponen']]+=$rTemp['jumlah'];
                                }
                            }
                        } 
                    } // end cek
                    if($rSlip['karyawanid']!='') {
                        if ($rSlip['templok']=='' || $rSlip['templok']==$idAfd){
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
                            $arrSubbagian[$rSlip['karyawanid']]=$rSlip['subbagian'];
                            $arrRek[$rSlip['karyawanid']]=$rSlip['norekeningbank'];
                            $arrJmlh[$rSlip['karyawanid'].$rSlip['idkomponen']]=$rSlip['jumlah'];
                            $arrTotal[$rSlip['idkomponen']]+=$rSlip['jumlah'];
                        }
                    }
                }
                $sTot="select tipelembur,jamaktual,karyawanid from ".$dbname.".sdm_lemburdt where substr(kodeorg,1,4)='".substr($idAfd,0,4)."' "
                     . "and tanggal between '".$tglLemburawal."' and '".$rPeriod['tglcutoff']."'";		
                $qTot=mysql_query($sTot) or die(mysql_error($conn));
                while($rTot=mysql_fetch_assoc($qTot))
                {
                        $sJum="select jamlembur as totalLembur from ".$dbname.".sdm_5lembur where tipelembur='".$rTot['tipelembur']."'
                        and jamaktual='".$rTot['jamaktual']."' and kodeorg='".substr($idAfd,0,4)."'";
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
                                <td>".$arrNmKary[$dtKary]."</td>";
                                $stream.="<td>'".$arrNik[$dtKary]."</td>";
                                $ocldt=9;
                                if($_SESSION['empl']['tipelokasitugas']=='HOLDING'||$_SESSION['empl']['tipelokasitugas']=='KANWIL')
                                {
                                    $ocldt=10;
                                    $stream.="<td>".$arrSubbagian[$dtKary]."</td>";
                                }
                                $stream.="
                                <td>".$arrRek[$dtKary]."</td>
                                <td>".$rNmTipe[$arrTipekary[$dtKary]]."</td>
                                <td>".$jumTot[$dtKary]."</td>
                                <td>".$arrDept[$dtKary]."</td> 
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

                                    $stream.="<td align=right>".number_format(intval($arrJmlh[$dtKary.$lstKompPls]),0)."</td>";
                                    $arrPlus[$s]=$arrJmlh[$dtKary.$lstKompPls];
                                    $s++;
                                    $brsPlus2++;
                                    if($brsPlus2==1){
                                        $stream.="<td>-".number_format(intval($arrJmlh[$dtKary.$peng1]),0)."</td>";
                                        //$stream.="<td>-".number_format($arrJmlh[$dtKary.$peng2],2)."</td>";
                                    }

                                }

                                $totDpt=array_sum($arrPlus)-($arrJmlh[$dtKary.$peng1]+$arrJmlh[$dtKary.$peng2]);
                                $stream.="<td align=right>".number_format(intval($totDpt),0)."</td>";


                                $arrMin=Array();
                                $q=0;
                                foreach($arrIdKompMin as $lstKompMin)
                                {
                                    if(($lstKompMin!=37)&&($lstKompMin!=36))
                                    {
                                         $stream.="<td align=right>".number_format(intval($arrJmlh[$dtKary.$lstKompMin]))."</td>";
                                         $arrMin[$q]=$arrJmlh[$dtKary.$lstKompMin];
                                         $q++;
                                    }
                                }
                                $drkompak="61";
                                $gajiBersih=$totDpt-array_sum($arrMin);	
                                $hslmod=$gajiBersih%1000;
                                $jmlhdtrima=$gajiBersih-$arrJmlh[$dtKary.$drkompak];
                                //$stream.="<td align=right>".number_format(array_sum($arrPlus),2)."</td>";
                                $stream.="<td align=right>".number_format(array_sum($arrMin),0)."</td>";
                                $stream.="<td align=right>".number_format($gajiBersih,0)."</td>";	
                                $stream.="<td align=right>".number_format($jmlhdtrima,0)."</td>";
                                $stream.="<td align=right>".number_format($arrJmlh[$dtKary.$drkompak],0)."</td></tr>";
                      }
                                $stream.="<tr><td colspan=".($ocldt+$rowabs+$rowabs2+2)." align=right>".$_SESSION['lang']['total']."</td>";

                                $s=0;
                                $brsPlus2=0;
                                $arrPlus=array();
                                foreach($arrIdKompPls as $lstKompPls)
                                {
                                    $stream.="<td align=right>".number_format(intval($arrTotal[$lstKompPls]),0)."</td>";
                                    $arrPlus[$s]=$arrTotal[$lstKompPls];
                                    $s++;
                                    $brsPlus2++;
                                    if($brsPlus2==1)
                                    {
                                        $stream.="<td>-".number_format(intval($arrTotal[$peng1]),0)."</td>";
                                        //$stream.="<td>-".number_format(intval($arrTotal[$peng2]),0)."</td>";
                                    }
                                }
                                $totDpt=array_sum($arrPlus)-($arrTotal[$peng1]+$arrTotal[$peng2]);
                                $stream.="<td align=right>".number_format(intval($totDpt),0)."</td>";

                                $arrMin=Array();
                                $q=0;
                                foreach($arrIdKompMin as $lstKompMin)
                                {
                                    if(($lstKompMin!=37)&&($lstKompMin!=36))
                                    {
                                         $stream.="<td align=right>".number_format(intval($arrTotal[$lstKompMin]))."</td>";
                                         $arrMin[$q]=$arrTotal[$lstKompMin];
                                         $q++;
                                    }
                                }
                                
                                $gajiBersih=$totDpt-array_sum($arrMin);				
                                
                                //$stream.="<td align=right>".number_format(array_sum($arrPlus),2)."</td>";
                                $stream.="<td align=right>".number_format(intval(array_sum($arrMin)),0)."</td>";
                                $stream.="<td align=right>".number_format(intval($gajiBersih),0)."</td>";	
                                	
                                $stream.="</tr>";
                }
                else
                {
                    $stream.="<tr><td colspan=20>&nbsp;</td></tr>";
                }

                        //echo "warning:".$strx;
                        //=================================================


                        $stream.="</table>Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];
                        //echo $stream;exit();
                        $dte=date("YmdHms");
                        $nop_="GajiHarianAfdeling_".$_SESSION['empl']['lokasitugas'].$dte;
                         $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
                         gzwrite($gztralala, $stream);
                         gzclose($gztralala);
                         echo "<script language=javascript1.2>
                            window.location='tempExcel/".$nop_.".xls.gz';
                            </script>";

        break;
         case'getPeriode':
            $optPeriode="<option value''>".$_SESSION['lang']['pilihdata']."</option>";
            $sPeriode="select periode from ".$dbname.".sdm_5periodegaji where kodeorg='".substr($idAfd,1,4)."' and jenisgaji='H'";
            $qPeriode=mysql_query($sPeriode) or die(mysql_error());
            while($rPeriode=mysql_fetch_assoc($qPeriode))
            {
                $optPeriode.="<option value=".$rPeriode['periode'].">".$rPeriode['periode']."</option>";
            }
            echo $optPeriode;
        break;
        default:
        break;
}
?>