<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');


$proses=$_GET['proses'];
$lokasitugas=$_SESSION['empl']['lokasitugas'];
$_POST['tanggal21']==''?$tanggal1=$_GET['tanggal21']:$tanggal1=$_POST['tanggal21'];
$_POST['tanggal22']==''?$tanggal2=$_GET['tanggal22']:$tanggal2=$_POST['tanggal22'];
$_POST['karyawanid2']==''?$karyawanid=$_GET['karyawanid2']:$karyawanid=$_POST['karyawanid2'];

function putertanggal($tanggal)
{
    $qwe=explode('-',$tanggal);
    return $qwe[2].'-'.$qwe[1].'-'.$qwe[0];
} 

$tangsys1=putertanggal($tanggal1);
$tangsys2=putertanggal($tanggal2);

function dates_inbetween($date1, $date2){
    $day = 60*60*24;
    $date1 = strtotime($date1);
    $date2 = strtotime($date2);
    $days_diff = round(($date2 - $date1)/$day); // Unix time difference devided by 1 day to get total days in between
    $dates_array = array();
    $dates_array[date('Y-m-d',$date1)] = date('Y-m-d',$date1);   
    for($x = 1; $x < $days_diff; $x++){
        $dates_array[date('Y-m-d',($date1+($day*$x)))] = date('Y-m-d',($date1+($day*$x)));
    }
    $dates_array[date('Y-m-d',$date2)] = date('Y-m-d',$date2);
    if($date1==$date2){
        $dates_array = array();
        $dates_array[date('Y-m-d',$date1)] = date('Y-m-d',$date1);        
    }
    return $dates_array;
}

//ambil query untuk data karyawan
$skaryawan="select a.karyawanid, a.kodejabatan, b.namajabatan, a.namakaryawan, c.nama from ".$dbname.".datakaryawan a 
    left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan 
    left join ".$dbname.".sdm_5departemen c on a.bagian=c.kode 
    where a.lokasitugas like '%HO' and ((a.tanggalkeluar >= '".$tangsys1."' and a.tanggalkeluar <= '".$tangsys2."') or a.tanggalkeluar='0000-00-00')
        and a.karyawanid like '%".$karyawanid."%' AND a.`tipekaryawan` !=0
    order by namakaryawan asc";    
$rkaryawan=fetchData($skaryawan);
foreach($rkaryawan as $row => $kar)
{
    $karyawan[$kar['karyawanid']]['id']=$kar['karyawanid'];
    $karyawan[$kar['karyawanid']]['nama']=$kar['namakaryawan'];
    $namakar[$kar['karyawanid']]=$kar['namakaryawan'];
    $jabakar[$kar['karyawanid']]=$kar['namajabatan'];
    $kojabakar[$kar['karyawanid']]=$kar['kodejabatan'];
    $bagikar[$kar['karyawanid']]=$kar['bagian'];
}  

// cek inputan tanggal
if(($tanggal1=="")||($tanggal2==""))
{
    echo"warning: Please fill all fields.";
    exit();
}

// cek apakah tanggal2 lebih besar
if($tangsys1>$tangsys2)
{
    echo"warning: Lower date first.";
    exit();
}

//cek max hari inputan
$tanggaltanggal = dates_inbetween($tangsys1, $tangsys2);
$jumlahhari=count($tanggaltanggal);
//if($jumlahhari>31||$jumlahhari<1)
//{
//    echo"warning: Invalid date range.";
//    exit();
//}

//// karyawan ijin & cuti
//$str="SELECT a.karyawanid, substr(a.darijam,1,10) as daritanggal, substr(a.sampaijam,1,10) as sampaitanggal, a.jenisijin, c.namakaryawan, c.lokasitugas, a.jenisijin 
//    FROM ".$dbname.".sdm_ijin a
//    LEFT JOIN ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid        
//    WHERE substr(a.darijam,1,10) <= '".$tangsys2."' and substr(a.sampaijam,1,10) >= '".$tangsys1."' and stpersetujuan1 = '1' and stpersetujuanhrd = '1'
//        and a.karyawanid like '%".$karyawanid."%'
//    ORDER BY a.darijam, a.sampaijam";
//$res=mysql_query($str);
//echo mysql_error($conn);
//while($bar=mysql_fetch_object($res))
//{
//    if(substr($bar->lokasitugas,2,2)=='HO'){
//        $karyawan[$bar->karyawanid]['id']=$bar->karyawanid;
//        $karyawan[$bar->karyawanid]['nama']=$bar->namakaryawan;
//    }    
//    $presensi[$bar->karyawanid]['ijin1']=$bar->daritanggal;
//    $presensi[$bar->karyawanid]['ijin2']=$bar->sampaitanggal;
//    $presensi[$bar->karyawanid]['x'.$bar->daritanggal]=$bar->jenisijin;
//}


//// karyawan dinas
//$str="SELECT a.karyawanid, a.tanggalperjalanan, a.tanggalkembali, a.tujuan1, a.tujuan2, a.tujuan3, c.namakaryawan, a.kodeorg FROM ".$dbname.".sdm_pjdinasht a
//    LEFT JOIN ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid        
//    WHERE a.tanggalperjalanan <= '".$tangsys2."' and a.tanggalkembali >= '".$tangsys1."' 
//        and a.karyawanid like '%".$karyawanid."%'
//        and statuspersetujuan='1' and statushrd='1'
//    order by a.tanggalperjalanan, a.tanggalkembali";
//$res=mysql_query($str);
//echo mysql_error($conn);
//while($bar=mysql_fetch_object($res))
//{
//    if($bar->karyawanid>''){
//    if(substr($bar->kodeorg,-2)=='HO'){
//        $karyawan[$bar->karyawanid]['id']=$bar->karyawanid;
//        $karyawan[$bar->karyawanid]['nama']=$bar->namakaryawan;
//    }    
//    $presensi[$bar->karyawanid]['dinas1']=$bar->tanggalperjalanan;
//    $presensi[$bar->karyawanid]['dinas2']=$bar->tanggalkembali;
//    }
//}

// karyawan masuk absensi
$str="SELECT a.tanggal, a.jam, a.karyawanid, c.namakaryawan FROM ".$dbname.".sdm_absensidt_vw a
    LEFT JOIN ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid        
    WHERE a.tanggal between '".$tangsys1."' and '".$tangsys2."' and a.jam < '12:00:00'
        and a.karyawanid like '%".$karyawanid."%' AND c.`tipekaryawan` !=0 and kodeorg like '%ho%'
    ORDER BY a.tanggal DESC, a.jam DESC";
//echo $str.'</br>';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    if(!isset($bar->karyawanid)){

    }else{
        $karyawan[$bar->karyawanid]['id']=$bar->karyawanid;
        $karyawan[$bar->karyawanid]['nama']=$bar->namakaryawan;
        $presensi[$bar->karyawanid]['m'.$bar->tanggal]=$bar->jam;
    }
}

// karyawan keluar absensi
$str="SELECT a.tanggal, a.jamPlg, a.karyawanid, c.namakaryawan FROM ".$dbname.".sdm_absensidt_vw a
    LEFT JOIN ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid        
    WHERE a.tanggal between '".$tangsys1."' and '".$tangsys2."' and a.jamPlg >= '12:00:00'
        and a.karyawanid like '%".$karyawanid."%' AND c.`tipekaryawan` !=0 and kodeorg like '%ho%'
    ORDER BY a.tanggal ASC, a.jamPlg ASC";
//echo $str.'</br>';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    if(!isset($bar->karyawanid)){

    }else{
        $karyawan[$bar->karyawanid]['id']=$bar->karyawanid;
        $karyawan[$bar->karyawanid]['nama']=$bar->namakaryawan;
        $presensi[$bar->karyawanid]['k'.$bar->tanggal]=$bar->jamPlg;
    }
}

// karyawan masuk
$str="SELECT a.pin, substr(a.scan_date,1,10) as tanggal, substr(a.scan_date,12,8) as jam, b.karyawanid, c.namakaryawan FROM ".$dbname.".att_log a
    LEFT JOIN ".$dbname.".att_adaptor b on a.pin=b.pin
    LEFT JOIN ".$dbname.".datakaryawan c on b.karyawanid=c.karyawanid        
    WHERE substr(scan_date,1,10) between '".$tangsys1."' and '".$tangsys2."' and substr(scan_date,12,8) < '12:00:00'
        and b.karyawanid like '%".$karyawanid."%' AND c.`tipekaryawan` !=0
    ORDER BY scan_date DESC";
//echo $str.'</br>';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    if(!isset($bar->karyawanid)){

    }else{
        $karyawan[$bar->karyawanid]['id']=$bar->karyawanid;
        $karyawan[$bar->karyawanid]['nama']=$bar->namakaryawan;
        $presensi[$bar->karyawanid]['m'.$bar->tanggal]=$bar->jam;
    }
}

// karyawan keluar
$str="SELECT a.pin, substr(a.scan_date,1,10) as tanggal, substr(a.scan_date,12,8) as jam, b.karyawanid, c.namakaryawan FROM ".$dbname.".att_log a
    LEFT JOIN ".$dbname.".att_adaptor b on a.pin=b.pin
    LEFT JOIN ".$dbname.".datakaryawan c on b.karyawanid=c.karyawanid        
    WHERE substr(scan_date,1,10) between '".$tangsys1."' and '".$tangsys2."' and substr(scan_date,12,8) >= '12:00:00'
        and b.karyawanid like '%".$karyawanid."%' AND c.`tipekaryawan` !=0
    ORDER BY scan_date ASC";
$res=mysql_query($str);
echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{
    if(!isset($bar->karyawanid)){

    }else{
        $karyawan[$bar->karyawanid]['id']=$bar->karyawanid;
        $karyawan[$bar->karyawanid]['nama']=$bar->namakaryawan;
        $presensi[$bar->karyawanid]['k'.$bar->tanggal]=$bar->jam;
    }
}

// sort berdasarkan nama
if(!empty($karyawan)) foreach($karyawan as $c=>$key) {
    $sort_nama[] = $key['nama'];
}
if(!empty($karyawan))array_multisort($sort_nama, SORT_ASC, $karyawan);

if($proses=='excel'){
     $bgcolor=" bgcolor=#DEDEDE";
     $border=1;
}else{
    $bgcolor="";
     $border=0;
}

// BEGIN STREAM
$stream='';
$no=0;
$kolomtanggal=$jumlahhari+5;
$stream.="<table class=sortable cellspacing=1 border=".$border.">";
$stream.="<thead><tr class=rowtitle>";
$stream.="<td rowspan=2 align=center".$bgcolor.">".$_SESSION['lang']['nourut']."</td>";
$stream.="<td rowspan=2 align=center".$bgcolor.">".$_SESSION['lang']['namakaryawan']."</td>";
$stream.="<td colspan=".$kolomtanggal." align=center".$bgcolor.">".$_SESSION['lang']['tanggal']."</td>";
$stream.="</tr>";
$stream.="<tr class=rowtitle>";
if(!empty($tanggaltanggal))foreach($tanggaltanggal as $tang)
{
    $hari=date('D', strtotime($tang));
    if($proses=='excel'){
        $qwe=substr($tang,5,2).'/'.substr($tang,8,2);
    }else{
        $qwe=substr($tang,8,2).'/'.substr($tang,5,2);        
    }
    if($hari=='Sat'||$hari=='Sun')$qwe="<font color='#FF0000'>".$qwe."</font>";
    $stream.="<td align=center".$bgcolor.">";
    $stream.=$qwe;
    $stream.="</td>";
}    
if($_SESSION['language']=='ID'){
$stream.="<td align=center".$bgcolor.">Hadir</td>";
$stream.="<td align=center".$bgcolor.">Telat</td>";
$stream.="<td align=center".$bgcolor.">Jam Telat</td>";
$stream.="<td align=center".$bgcolor.">Lembur</td>";
$stream.="<td align=center".$bgcolor.">Lembur-Telat</td>";
}else{
$stream.="<td align=center".$bgcolor.">Present</td>";
$stream.="<td align=center".$bgcolor.">Late</td>";
$stream.="<td align=center".$bgcolor.">Hours Late</td>";
$stream.="<td align=center".$bgcolor.">Overtime</td>";
$stream.="<td align=center".$bgcolor.">Overtime-Late</td>";    
}
$stream.="</tr></thead>";
$stream.="<tbody>";

function tambahlembur($totallembur,$lembur)
{
    $i=explode(':',$totallembur);
    $j=explode(':',$lembur);
    $menit=$j[1]+$i[1];
    $jam=$j[0]+$i[0];
    if($menit>=60){
        $menit-=60;
        $jam+=1;
    }
    if(strlen($menit)==1)$menit='0'.$menit;
    if(strlen($jam)==1)$jam='0'.$jam;
    return $jam.':'.$menit;
}

function kuranglembur($lembur,$totallembur)
{
    if($lembur>$totallembur){
        $i=explode(':',$totallembur);
        $j=explode(':',$lembur);
    }else{
        $j=explode(':',$totallembur);
        $i=explode(':',$lembur);
    }
    $menit=$j[1]-$i[1];
    $jam=$j[0]-$i[0];
    if($menit<0){
        $menit+=60;
        $jam-=1;
    }
    if(strlen($menit)==1)$menit='0'.$menit;
    if(strlen($jam)==1)$jam='0'.$jam;
    return $jam.':'.$menit;
}

if(!empty($karyawan))foreach($karyawan as $kar)
{
    $no+=1;
    $hadir=0;
    $telat=0;
    $totallembur='00:00';
    $totaltelat='00:00';
    $stream.="<tr class=rowcontent>";
    $stream.="<td align=right>".$no.".</td>";    
    $stream.="<td>".$kar['nama']."</td>";    
    if(!empty($tanggaltanggal))foreach($tanggaltanggal as $tang)
    {    
        $hari=date('D', strtotime($tang));
        $pres='';
//        if(isset($presensi[$kar['id']]['ijin1'])){
//            if(($presensi[$kar['id']]['ijin1']<=$tang)&&($presensi[$kar['id']]['ijin2']>=$tang)){
//                if($hari!='Sat'&&$hari!='Sun')$pres=$presensi[$kar['id']]['x'.$presensi[$kar['id']]['ijin1']];
//                if($hari!='Sat'&&$hari!='Sun')$cuti+=1;
//            }
//        }

//        if(isset($presensi[$kar['id']]['dinas1'])){
//            if(($presensi[$kar['id']]['dinas1']<=$tang)&&($presensi[$kar['id']]['dinas2']>=$tang)){
//                $pres='DINAS';
//            }
//        }

        if(isset($presensi[$kar['id']]['m'.$tang])||isset($presensi[$kar['id']]['k'.$tang])){
            $ontime=true;
            if(isset($presensi[$kar['id']]['m'.$tang])){
                if(($tang>='2013-07-09')and($tang<='2013-08-08')){              // puasa 2013
                    $jammasuk='07:00';
                    if($kojabakar[$kar['id']]=='40'or'377')$jammasuk='06:00';
                    $a=$jammasuk;
                    $b=substr($presensi[$kar['id']]['m'.$tang],0,5);
                    $jamlembur = kuranglembur($a,$b);
//                    $jamlembur = $interval->format("%H").':'.$interval->format("%I");
                    if(substr($presensi[$kar['id']]['m'.$tang],0,5)<=$jammasuk){ // masuk ontime
                        $totallembur=tambahlembur($totallembur,$jamlembur);
                        $pres='&nbsp;'.substr($presensi[$kar['id']]['m'.$tang],0,5).'['.$jamlembur.']';
                    }else{
                        $totaltelat=tambahlembur($totaltelat,$jamlembur);
                        $pres='&nbsp;<font color=red>'.substr($presensi[$kar['id']]['m'.$tang],0,5).'['.$jamlembur.']</font>';                    
                        $ontime=false;
                    }
                }else{
                    $jammasuk='08:00';
                    if($kojabakar[$kar['id']]=='40'or'377')$jammasuk='07:00';
                    $a=$jammasuk;
                    $b=substr($presensi[$kar['id']]['m'.$tang],0,5);
                    $jamlembur = kuranglembur($a,$b);
                    if(substr($presensi[$kar['id']]['m'.$tang],0,5)<=$jammasuk){ // masuk ontime
                        $totallembur=tambahlembur($totallembur,$jamlembur);
                        $pres='&nbsp;'.substr($presensi[$kar['id']]['m'.$tang],0,5).'['.$jamlembur.']';                    
                    }else{
                        $totaltelat=tambahlembur($totaltelat,$jamlembur);
                        $pres='&nbsp;<font color=red>'.substr($presensi[$kar['id']]['m'.$tang],0,5).'['.$jamlembur.']</font>';                    
                        $ontime=false;
                    }
                }                
            } else $ontime=false;
            if(isset($presensi[$kar['id']]['k'.$tang])){
                if(($tang>='2013-07-09')and($tang<='2013-08-08')){              // puasa 2013
                    $jamkeluar='16:00';
                    if($kojabakar[$kar['id']]=='40'or'377')$jamkeluar='17:00';
                    $b=$jamkeluar;
                    $a=substr($presensi[$kar['id']]['k'.$tang],0,5);
                    $jamlembur = kuranglembur($a,$b);
                    if(substr($presensi[$kar['id']]['k'.$tang],0,5)>=$jamkeluar){ // pulang ontime
                        $totallembur=tambahlembur($totallembur,$jamlembur);
                        $pres.='</br>&nbsp;'.substr($presensi[$kar['id']]['k'.$tang],0,5).'['.$jamlembur.']';                    
                    }else{
                        $totaltelat=tambahlembur($totaltelat,$jamlembur);
                        $pres.='</br>&nbsp;<font color=red>'.substr($presensi[$kar['id']]['k'.$tang],0,5).'['.$jamlembur.']</font>';                    
                        $ontime=false;
                    }            
                }else
                if($tang=='2013-10-14'){                                        // idul adha 2013 -1
                    $jamkeluar='15:00';
                    if($kojabakar[$kar['id']]=='40'or'377')$jamkeluar='16:00';
                    $b=$jamkeluar;
                    $a=substr($presensi[$kar['id']]['k'.$tang],0,5);
                    $jamlembur = kuranglembur($a,$b);
                    if(substr($presensi[$kar['id']]['k'.$tang],0,5)>=$jemkeluar){ // pulang ontime
                        $totallembur=tambahlembur($totallembur,$jamlembur);
                        $pres.='</br>&nbsp;'.substr($presensi[$kar['id']]['k'.$tang],0,5).'['.$jamlembur.']';                    
                    }else{
                        $totaltelat=tambahlembur($totaltelat,$jamlembur);
                        $pres.='</br>&nbsp;<font color=red>'.substr($presensi[$kar['id']]['k'.$tang],0,5).'['.$jamlembur.']</font>';                    
                        $ontime=false;
                    }            
                }else{
                    $jamkeluar='17:00';
                    if($kojabakar[$kar['id']]=='40'or'377')$jamkeluar='18:00';
                    $b=$jamkeluar;
                    $a=substr($presensi[$kar['id']]['k'.$tang],0,5);
                    $jamlembur = kuranglembur($a,$b);
                    if(substr($presensi[$kar['id']]['k'.$tang],0,5)>=$jamkeluar){ // pulang ontime
                        $totallembur=tambahlembur($totallembur,$jamlembur);
                        $pres.='</br>&nbsp;'.substr($presensi[$kar['id']]['k'.$tang],0,5).'['.$jamlembur.']';                    
                    }else{
                        $totaltelat=tambahlembur($totaltelat,$jamlembur);
                        $pres.='</br>&nbsp;<font color=red>'.substr($presensi[$kar['id']]['k'.$tang],0,5).'['.$jamlembur.']</font>';                    
                        $ontime=false;
                    }            
                }
            } else $ontime=false;
            if($ontime)$hadir+=1; else $telat+=1;
        }

        if($hari=='Sat'||$hari=='Sun'){
            $bgcolor=" bgcolor='#FFCCCC'";
            if($pres=='')$pres=' ';
        }else{
            $bgcolor="";
        }

//        if($pres=='DINAS')$dinas+=1;

//        if($pres=='')$mangkir+=1;

        $stream.="<td valign=top align=center".$bgcolor.">".$pres."</td>";    
    }
    $stream.="<td align=right>".$hadir."</td>";
    $stream.="<td align=right>".$telat."</td>";
    $stream.="<td align=right>".$totaltelat."</td>";
    $stream.="<td align=right>".$totallembur."</td>";
    $lemburbersih=kuranglembur($totallembur,$totaltelat);
    $stream.="<td align=right>".$lemburbersih."</td>";
    $stream.="</tr>";    
}    
$stream.="</tbody></table>";
if($_SESSION['language']=='ID'){
$stream.="Data yang ditampilkan adalah karyawan non staf HO.</br>";
$stream.="Lembur karyawan dengan kode jabatan Office Boy/Girl (40) dihitung bila karyawan datang 1 jam sebelum jam masuk dan/atau pulang 1 jam setelah jam keluar.</br>";
}else{
$stream.="Data displayed is non-staff HO employees.</br>";
$stream.="Overtime for employees with title Office Boy/Girl (40) is counted if a person come 1 hour before opening hour and/or home 1 hour after closing hour.</br>";
}


switch($proses)
{
        case'preview':
echo $stream;

        break;
//        case'pdf':
//
////create Header
//
//class PDF extends FPDF
//{
//    function Header() {
//        global $conn;
//        global $dbname;
//        global $align;
//        global $length;
//        global $colArr;
//        global $title;
//        global $tanggal1;				
//        global $tanggal2;				
//        global $karyawanid;				
//        global $tangsys1;				
//        global $tangsys2;				
//        global $tanggaltanggal;				
//        global $jumlahhari;				
//        $cols=247.5;
//
//        # Alamat & No Telp
//        $query = selectQuery($dbname,'organisasi','alamat,telepon',
//            "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
//        $orgData = fetchData($query);
//
//        $width = $this->w - $this->lMargin - $this->rMargin;
//        $height = 12;
//        if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
//        $this->Image($path,$this->lMargin,$this->tMargin,70);	
//        $this->SetFont('Arial','B',9);
//        $this->SetFillColor(255,255,255);	
//        $this->SetX(100);   
//        $this->Cell($width-100,$height,$_SESSION['org']['namaorganisasi'],0,1,'L');	 
//        $this->SetX(100); 		
//        $this->Cell($width-100,$height,$orgData[0]['alamat'],0,1,'L');	
//        $this->SetX(100); 			
//        $this->Cell($width-100,$height,"Tel: ".$orgData[0]['telepon'],0,1,'L');	
//        $this->Line($this->lMargin,$this->tMargin+($height*3),
//        $this->lMargin+$width,$this->tMargin+($height*3));
//        $this->Ln();
//
//        $this->SetFont('Arial','B',10);
//        $this->Cell(($width)-5,$height,$_SESSION['lang']['rkpAbsen'].' HO','',0,'C');
//        $this->Ln();
//        $this->Cell(($width)-5,$height,$_SESSION['lang']['periode']." : ". $tanggal1." s.d. ". $tanggal2,'',0,'C');
//        $this->Ln();
//        $this->Ln();
//        $this->SetFont('Arial','B',7);
//        $this->SetFillColor(220,220,220);
//
//        $this->Cell(2/100*$width,$height,'No',TRL,0,'C',1);
//        $this->Cell(7.3/100*$width,$height,$_SESSION['lang']['namakaryawan'],TRL,0,'C',1);		
//        $this->Cell(2.7/100*$width*$jumlahhari,$height,$_SESSION['lang']['tanggal'],1,0,'C',1);
//        if($_SESSION['language']=='ID'){
//            $this->Cell(2.7/100*$width,$height,'Hadir',TRL,0,'C',1);	
//            $this->Cell(2.7/100*$width,$height,'Telat',TRL,0,'C',1);	
//            $this->Cell(2.7/100*$width,$height,'Dinas',TRL,0,'C',1);
//        }else{
//
//            $this->Cell(2.7/100*$width,$height,'Present',TRL,0,'C',1);	
//            $this->Cell(2.7/100*$width,$height,'Late',TRL,0,'C',1);	
//            $this->Cell(2.7/100*$width,$height,'Duty',TRL,0,'C',1);
//         }
//
//	
//        $this->Ln();
//        $this->Cell(2/100*$width,$height,'',BRL,0,'C',1);
//        $this->Cell(7.3/100*$width,$height,'',BRL,0,'C',1);		
//        if(!empty($tanggaltanggal))foreach($tanggaltanggal as $tang)
//        {
//            $hari=date('D', strtotime($tang));
//            $qwe=substr($tang,8,2);
//            if($hari=='Sat'||$hari=='Sun')$this->SetTextColor(255,0,0);
//            else $this->SetTextColor(0,0,0);
//            $this->Cell(2.7/100*$width,$height,$qwe,1,0,'C',1);	
//        }
//        $this->Cell(2.7/100*$width,$height,'',BRL,0,'C',1);	
//        $this->Cell(2.7/100*$width,$height,'',BRL,0,'C',1);	
//        $this->Cell(2.7/100*$width,$height,'',BRL,0,'C',1);	
//        $this->Ln();
//    }
//
//    function Footer()
//    {
//        $this->SetY(-15);
//        $this->SetFont('Arial','I',8);
//        $this->Cell(10,10,'Page '.$this->PageNo()." Print Time:".date('Y-m-d H:i:s')." By:".$_SESSION['empl']['name'],0,0,'L');
//    }
//}
//
//$pdf=new PDF('L','pt','Legal');
//$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
//$height = 12;
//$pdf->AddPage();
//$pdf->SetFillColor(255,255,255);
//$pdf->SetTextColor(0,0,0);
//$pdf->SetFont('Arial','',7);
//
//$no=0;
//
//if(!empty($karyawan))foreach($karyawan as $kar)
//{
//    $hadir=0;
//    $telat=0;
//    $dinas=0;
////    $cuti=0;
//    $no+=1;
//
//    // LINE PERTAMA
//    $pdf->Cell(2/100*$width,$height,$no,TRL,0,'R',1);
//    $pdf->Cell(7.3/100*$width,$height,$kar['nama'],TRL,0,'L',1);
//    if(!empty($tanggaltanggal))foreach($tanggaltanggal as $tang)
//    {    
//        $hari=date('D', strtotime($tang));
//        $pres='';
//        if(isset($presensi[$kar['id']]['ijin1'])){
//            if(($presensi[$kar['id']]['ijin1']<=$tang)&&($presensi[$kar['id']]['ijin2']>=$tang)){
//                if($hari!='Sat'&&$hari!='Sun')$pres=$presensi[$kar['id']]['x'.$presensi[$kar['id']]['ijin1']];
//                if($hari!='Sat'&&$hari!='Sun')$cuti+=1;
//            }
//        }
//
//        if(isset($presensi[$kar['id']]['dinas1'])){
//            if(($presensi[$kar['id']]['dinas1']<=$tang)&&($presensi[$kar['id']]['dinas2']>=$tang)){
//                $pres='DINAS';
//            }
//        }
//
//        if(isset($presensi[$kar['id']]['m'.$tang])||isset($presensi[$kar['id']]['k'.$tang])){
//            $ontime=true; // buat deteksi allday telat/nggak
//            $ontime2=true; // buat deteksi dateng telat/nggak
//            if(isset($presensi[$kar['id']]['m'.$tang])){
//                $pres=substr($presensi[$kar['id']]['m'.$tang],0,5);                
//                if(($tang>='2013-07-09')and($tang<='2013-08-08')){              // puasa 2013
//                    if(substr($presensi[$kar['id']]['m'.$tang],0,5)<='07:30'){ // masuk ontime
//
//                    }else{
//                        $ontime=false;
//                        $ontime2=false;
//                    }
//                }else{
//                    if(substr($presensi[$kar['id']]['m'.$tang],0,5)<='08:00'){ // masuk ontime
//
//                    }else{
//                        $ontime=false;
//                        $ontime2=false;
//                    }
//                }    
//            } else $ontime=false;
//            if(isset($presensi[$kar['id']]['k'.$tang])){
//                if(($tang>='2013-07-09')and($tang<='2013-08-08')){              // puasa 2013
//                    if(substr($presensi[$kar['id']]['k'.$tang],0,5)>='16:00'){ // pulang ontime
//
//                    }else{
//                        $ontime=false;
//                    }            
//                }else
//                if($tang=='2013-10-14'){                                        // idul adha 2013 -1
//                    if(substr($presensi[$kar['id']]['k'.$tang],0,5)>='15:00'){ // pulang ontime
//
//                    }else{
//                        $ontime=false;
//                    }            
//                }else{
//                    if(substr($presensi[$kar['id']]['k'.$tang],0,5)>='17:00'){ // pulang ontime
//                }else{
//                    $ontime=false;
//                } 
//                }
//            } else $ontime=false;
//
//
//            if($ontime)$hadir+=1; else $telat+=1;
//            if($ontime2)$pdf->SetTextColor(0,0,0); else $pdf->SetTextColor(255,0,0);
//        }
//
//        if($hari=='Sat'||$hari=='Sun'){
//            $pdf->SetFillColor(255,224,224);
//            if($pres=='')$pres=' ';
//        }else{
//            $pdf->SetFillColor(255,255,255);
//        }
//
//        if($pres=='DINAS')$dinas+=1;
//
//        if($pres=='')$mangkir+=1;
//
//        $pdf->Cell(2.7/100*$width,$height,$pres,TRL,0,'L',1);
//    }
//    $pdf->SetFillColor(255,255,255);
//    $pdf->SetTextColor(0,0,0);
//    $pdf->Cell(2.7/100*$width,$height,$hadir,TRL,0,'R',1);
//    $pdf->Cell(2.7/100*$width,$height,$telat,TRL,0,'R',1);
//    $pdf->Cell(2.7/100*$width,$height,$dinas,TRL,0,'R',1);
////    $stream.="<td align=right>".$cuti."</td>";
////    $stream.="<td align=right>".$dinas."</td>";
//    $pdf->Ln();
//
//    // LINE KEDUA
//    $pdf->Cell(2/100*$width,$height,'',BRL,0,'R',1);
//    $pdf->Cell(7.3/100*$width,$height,$jabakar[$kar['id']],BRL,0,'L',1);
//    if(!empty($tanggaltanggal))foreach($tanggaltanggal as $tang)
//    {            
//        $pres='';
//        if(isset($presensi[$kar['id']]['k'.$tang])){
//            $ontime=true;
//            $pres.=substr($presensi[$kar['id']]['k'.$tang],0,5);
//            if(($tang>='2013-07-09')and($tang<='2013-08-08')){              // puasa 2013
//                if(substr($presensi[$kar['id']]['k'.$tang],0,5)>='16:00'){ // pulang ontime
//
//                }else{
//                    $ontime=false;
//                }
//            }else{
//                if(substr($presensi[$kar['id']]['k'.$tang],0,5)>='17:00'){ // pulang ontime
//
//                }else{
//                    $ontime=false;
//                }
//            }
//            if($ontime)$pdf->SetTextColor(0,0,0); else $pdf->SetTextColor(255,0,0);
//        }
//
//        $hari=date('D', strtotime($tang));
//        if($hari=='Sat'||$hari=='Sun')$pdf->SetFillColor(255,224,224); else $pdf->SetFillColor(255,255,255);
//        $pdf->Cell(2.7/100*$width,$height,$pres,BRL,0,'L',1);
//    }
//    $pdf->SetFillColor(255,255,255);
//    $pdf->SetTextColor(0,0,0);
//    $pdf->Cell(2.7/100*$width,$height,'',BRL,0,'R',1);
//    $pdf->Cell(2.7/100*$width,$height,'',BRL,0,'R',1);
//    $pdf->Cell(2.7/100*$width,$height,'',BRL,0,'R',1);
//    $pdf->Ln();        
//}    
//$stream.=".</br>";
//$stream.=".</br>";
//$stream.="</br>";
//$stream.=".</br>";   
//
//if($_SESSION['language']=='ID'){
//    $pdf->Cell($width,$height,'Bila karyawan tertentu tidak/muncul, harap dipastikan data Lokasi Tugas-nya dan telah terdaftar PIN Fingerprint-nya.',T,0,'L',1);
//$pdf->Ln();                
//$pdf->Cell($width,$height,'Hanya Ijin/Cuti yang telah disetujui oleh atasan dan HRD yang ditampilkan. Cuti Sabtu/Minggu tidak dihitung.',0,0,'L',1);
//$pdf->Ln();                
//$pdf->Cell($width,$height,'Bila karyawan tidak absen masuk/pulang maka dianggap telat.',0,0,'L',1);
//$pdf->Ln();                
//$pdf->Cell($width,$height,'Absen masuk 00:00 - 11:59. Absen pulang 12:00 - 23:59.',0,0,'L',1);
//}else{
//$pdf->Cell($width,$height,'If any employee not listed, please make sure duty location of the employee and fingerprint has been registred.',T,0,'L',1);
//$pdf->Ln();                
//$pdf->Cell($width,$height,'For leave data, only approved leave are displayed. Leave on Saturday and Sunday are not counted.',0,0,'L',1);
//$pdf->Ln();                
//$pdf->Cell($width,$height,'If employee out earlier, then system will recognize it as late.',0,0,'L',1);
//$pdf->Ln();                
//$pdf->Cell($width,$height,'In between 00:00 - 11:59. Out between 12:00 - 23:59.',0,0,'L',1);
//$pdf->Ln();                
//}
//        $pdf->Output();
//
//        break;
        case'excel':

                        $stream.="<br><br>Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
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
                        $nop_="RekapAbsen_HO_".$tangsys1."_".$tangsys2;
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
        if($kdUnit=='')
        {
            $kdUnit=$_SESSION['empl']['lokasitugas'];
        }
        $sTgl="select distinct tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji where kodeorg='".substr($kdUnit,0,4)."' ".$dmna." ";
        //echo"warning".$sTgl;
        $qTgl=mysql_query($sTgl) or die(mysql_error());
        $rTgl=mysql_fetch_assoc($qTgl);
        echo tanggalnormal($rTgl['tanggalmulai'])."###".tanggalnormal($rTgl['tanggalsampai']);
        break;
        case'getKry':
        $optKry="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        if(strlen($kdeOrg)>4)
        {
                $where=" subbagian='".$kdeOrg."'";
        }
        else
        {
                $where=" lokasitugas='".$kdeOrg."' and (subbagian='0' or subbagian is null or subbagian='')";
        }
        $sKry="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where ".$where." order by namakaryawan asc";
        $qKry=mysql_query($sKry) or die(mysql_error());
        while($rKry=mysql_fetch_assoc($qKry))
        {
                $optKry.="<option value=".$rKry['karyawanid'].">".$rKry['namakaryawan']."</option>";
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
        if($periodeGaji!='')
        {
                $were=" kodeorg='".$kdUnit."' and periode='".$periodeGaji."' and jenisgaji='".$sistemGaji."'";
        }
        else
        {
                $were=" kodeorg='".$kdUnit."'";
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
        $qSub=mysql_query($sSub) or die(mysql_error($conn));
        while($rSub=  mysql_fetch_assoc($qSub))
        {
             $optAfd.="<option value='".$rSub['kodeorganisasi']."'>".$rSub['namaorganisasi']."</option>";
        }
        echo $optAfd."####".$optPeriode;
        break;
        default:
        break;
}
?>