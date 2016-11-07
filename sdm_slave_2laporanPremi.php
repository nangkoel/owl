<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

// get post =========================================================================
$proses=$_GET['proses'];
$periode=$_POST['periode'];
$lksiTgs=$_SESSION['empl']['lokasitugas'];
$kdOrg=$_POST['kdOrg'];
if(!$periode)$periode=$_GET['periode'];
if(!$kdOrg)$kdOrg=$_GET['kdOrg'];
if(!$kdOrg)$kdOrg=$_SESSION['empl']['lokasitugas'];
$_POST['tpKary']==''?$tpKary=$_GET['tpKary']:$tpKary=$_POST['tpKary'];
$optTipe=makeOption($dbname, 'sdm_5tipekaryawan', 'id,tipe');

// get namaorganisasi =========================================================================
        $sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where kodeorganisasi ='".$kdOrg."' ";	
        $qOrg=mysql_query($sOrg) or die(mysql_error($conn));
        while($rOrg=mysql_fetch_assoc($qOrg))
        {
                $nmOrg=$rOrg['namaorganisasi'];
        }
        if(!$nmOrg)$nmOrg=$kdOrg;

// determine begin end =========================================================================
        $lok=substr($kdOrg,0,4); //$_SESSION['empl']['lokasitugas'];
        $sDatez = "select tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji where periode = '".$periode."' and kodeorg= '".$lok."'";
        $qDatez=mysql_query($sDatez) or die(mysql_error($conn));
        while($rDatez=mysql_fetch_assoc($qDatez))
        {
                $tanggalMulai=$rDatez['tanggalmulai'];
                $tanggalSampai=$rDatez['tanggalsampai'];
        }

// setup wheres =========================================================================
        $where3=" and a.kodeorg like '".substr($kdOrg,0,4)."%'";
        if($kdOrg=='') // seluruhnya 
        {

                if($_SESSION['empl']['tipelokasitugas']!='HOLDING')
                {
                        $kodeOrg=$_SESSION['empl']['lokasitugas'];
                        $where.=" and a.kodeorg like '%".$kodeOrg."%'";
                        $where2.=" and b.lokasitugas like '%".$kodeOrg."%'";
                }
                else
                {
                        $sKebun="select kodeorganisasi from organisasi where tipe in ('KEBUN','PABRIK','KANWIL') ";
                        $qKebun=mysql_query($sKebun) or die(mysql_error());
                        while($rKebun=mysql_fetch_assoc($qKebun))
                        {
                                $kodeOrg="'".$rKebun['kodeorganisasi']."'";
                                $kodeOrg.=",'".$rKebun['kodeorganisasi']."'";
                        }
                        $where.=" and a.kodeorg in(".$kodeOrg.")";		
                        $where2.=" and b.lokasitugas in(".$kodeOrg.")";		
                }

        }
        else
        {

            if(strlen($kdOrg)<5)
            {
                $where=" and a.kodeorg like '".$kdOrg."%'";
                $where2=" and b.lokasitugas like '".$kdOrg."%'";
            }
            else
              {  
                $where=" and a.kodeorg like '".$kdOrg."%'";
                $where2=" and b.subbagian like '".$kdOrg."%'";
              }
//                $where=" and a.kodeorg like '".$kdOrg."%'";
//                $where2=" and b.lokasitugas like '".$kdOrg."%'";
        }

        if($tpKary!=''){
            $where2.=" and b.tipekaryawan='".$tpKary."'";
            $where.=" and b.tipekaryawan='".$tpKary."'";
            }
            else
            {
                $where2.=" and b.tipekaryawan in (select distinct id from ".$dbname.".sdm_5tipekaryawan where id!=0) ";
                $where.=" and b.tipekaryawan in (select distinct id from ".$dbname.".sdm_5tipekaryawan where id!=0)";
            }
// building array: jabatan =========================================================================	
         $strJ="select * from ".$dbname.".sdm_5jabatan";
        $resJ=mysql_query($strJ,$conn);
        while($barJ=mysql_fetch_object($resJ))
        {
                $jab[$barJ->kodejabatan]=$barJ->namajabatan;
        }

// building array: dzArr (main data) =========================================================================
        $dzArr=array();
        $thk = 0;$tnr = 0;$tup = 0;$trp = 0;$ttt = 0;$tin = 0;$tpr = 0;$tpe = 0;$tjm = 0; // nolin total
    $sPeople="SELECT b.karyawanid as karyawanid, b.namakaryawan as namakaryawan, b.kodejabatan as kodejabatan,b.tipekaryawan,b.subbagian 
                          FROM ".$dbname.".datakaryawan b 
                          WHERE (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].")  ".$where2." 
                          GROUP BY b.karyawanid ORDER BY b.namakaryawan";
        $query=mysql_query($sPeople) or die(mysql_error());
        while($res=mysql_fetch_assoc($query))
        {
        $dzArr[$res['karyawanid']][id]=$res['karyawanid'];
        $dzArr[$res['karyawanid']][nm]=$res['namakaryawan'];
        $dzArr[$res['karyawanid']][jb]=$jab[$res['kodejabatan']];
        $dzArr[$res['karyawanid']][tk]=$optTipe[$res['tipekaryawan']];
        $dzArr[$res['karyawanid']][sb]=$res['subbagian'];
        $dzArr[$res['karyawanid']][hk]=0;
        $dzArr[$res['karyawanid']][nr]=0;
        $dzArr[$res['karyawanid']][up]=0;
        $dzArr[$res['karyawanid']][rp]=0;
        $dzArr[$res['karyawanid']][tt]=0;
        $dzArr[$res['karyawanid']][in]=0;
        $dzArr[$res['karyawanid']][pr]=0;
        $dzArr[$res['karyawanid']][pe]=0;
        $dzArr[$res['karyawanid']][jm]=0;
        $dzArr[$res['karyawanid']][km]=0;
        }
        //panen
        $sPeople="SELECT b.namakaryawan, b.kodejabatan, a.karyawanid as karyawanid, sum(a.hasilkerja) as hasilkerja, avg(a.norma) as norma, sum(a.upahpremi) as upahpremi, sum(a.rupiahpenalty) as rupiahpenalty 
                          FROM ".$dbname.".kebun_prestasi_vw a
                          LEFT JOIN ".$dbname.".datakaryawan b on a.karyawanid = b.karyawanid 
                          WHERE a.tanggal>='".$tanggalMulai."' and a.tanggal<='".$tanggalSampai."' ".$where." 
                          GROUP BY a.karyawanid ORDER BY a.karyawanid";
        $query=mysql_query($sPeople) or die(mysql_error());
        while($res=mysql_fetch_assoc($query))
        {
        $dzArr[$res['karyawanid']][id]=$res['karyawanid'];
//		if($dzArr[$res['karyawanid']][nm]=='')$dzArr[$res['karyawanid']][nm]=$res['namakaryawan']." *";
                if($dzArr[$res['karyawanid']][nm]=='')$dzArr[$res['karyawanid']][nm]=$res['namakaryawan']."";
                if($dzArr[$res['karyawanid']][jb]=='')$dzArr[$res['karyawanid']][jb]=$jab[$res['kodejabatan']];
        $dzArr[$res['karyawanid']][hk]=$res['hasilkerja']; $thk+=$res['hasilkerja'];
        $dzArr[$res['karyawanid']][nr]=$res['norma']; $tnr+=$res['norma'];
        $dzArr[$res['karyawanid']][up]=$res['upahpremi']; $tup+=$res['upahpremi'];
        $dzArr[$res['karyawanid']][rp]=$res['rupiahpenalty']; $trp+=$res['rupiahpenalty'];
                $dzArr[$res['karyawanid']][tt]+=($res['upahpremi']-$res['rupiahpenalty']);
                $ttt+=($res['upahpremi']-$res['rupiahpenalty']);
        }
      //perawatan
        $sPeople="SELECT b.namakaryawan, b.kodejabatan, a.karyawanid as karyawanid, sum(a.insentif) as insentif 
                          FROM ".$dbname.".kebun_kehadiran_vw a
                          LEFT JOIN ".$dbname.".datakaryawan b on a.karyawanid = b.karyawanid
                          WHERE a.tanggal>='".$tanggalMulai."' and a.tanggal<='".$tanggalSampai."' ".$where2." 
                          GROUP BY a.karyawanid ORDER BY a.karyawanid";
        //echo $sPeople;
        $query=mysql_query($sPeople) or die(mysql_error());
        while($res=mysql_fetch_assoc($query))
        {
                $dzArr[$res['karyawanid']][id]=$res['karyawanid'];
//		if($dzArr[$res['karyawanid']][nm]=='')$dzArr[$res['karyawanid']][nm]=$res['namakaryawan']." *";
                if($dzArr[$res['karyawanid']][nm]=='')$dzArr[$res['karyawanid']][nm]=$res['namakaryawan']." *";
                if($dzArr[$res['karyawanid']][jb]=='')$dzArr[$res['karyawanid']][jb]=$jab[$res['kodejabatan']];
                $dzArr[$res['karyawanid']][in]=$res['insentif']; 
                $tin+=$res['insentif'];
                $dzArr[$res['karyawanid']][tt]+=$res['insentif']; $ttt+=$res['insentif'];
        }
       //premi traksi
        $sPeople="SELECT b.namakaryawan, b.kodejabatan, a.idkaryawan as karyawanid, sum(a.premi) as premi, sum(a.penalty) as penalty 
                          FROM ".$dbname.".vhc_runhk a
                          LEFT JOIN ".$dbname.".datakaryawan b on a.idkaryawan = b.karyawanid
                          WHERE a.tanggal>='".$tanggalMulai."' and a.tanggal<='".$tanggalSampai."' ".$where2." 
                          GROUP BY a.idkaryawan ORDER BY a.idkaryawan";
        $query=mysql_query($sPeople) or die(mysql_error());
        while($res=mysql_fetch_assoc($query))
        {
        $dzArr[$res['karyawanid']][id]=$res['karyawanid'];
//		if($dzArr[$res['karyawanid']][nm]=='')$dzArr[$res['karyawanid']][nm]=$res['namakaryawan']." *";
                if($dzArr[$res['karyawanid']][nm]=='')$dzArr[$res['karyawanid']][nm]=$res['namakaryawan']."";
                if($dzArr[$res['karyawanid']][jb]=='')$dzArr[$res['karyawanid']][jb]=$jab[$res['kodejabatan']];
        $dzArr[$res['karyawanid']][pr]=$res['premi']; $tpr+=$res['premi'];
        $dzArr[$res['karyawanid']][pe]=$res['penalty']; $tpe+=$res['penalty']; 
                $dzArr[$res['karyawanid']][tt]+=($res['premi']-$res['penalty']); $ttt+=($res['premi']-$res['penalty']);
        }
    //premi pengawasan
    $sPeople="SELECT b.namakaryawan, b.kodejabatan, a.karyawanid as karyawanid, sum(a.jumlah) as jumlah 
                          FROM ".$dbname.".sdm_gaji a
                          LEFT JOIN ".$dbname.".datakaryawan b on a.karyawanid = b.karyawanid
                          WHERE a.idkomponen = '16' and a.periodegaji='".$periode."' ".$where2." 
                          GROUP BY a.karyawanid ORDER BY a.karyawanid";
        $query=mysql_query($sPeople) or die(mysql_error());
        while($res=mysql_fetch_assoc($query))
        {
        $dzArr[$res['karyawanid']][id]=$res['karyawanid'];
                if($dzArr[$res['karyawanid']][nm]=='')$dzArr[$res['karyawanid']][nm]=$res['namakaryawan']." *";
                if($dzArr[$res['karyawanid']][jb]=='')$dzArr[$res['karyawanid']][jb]=$jab[$res['kodejabatan']];
        $dzArr[$res['karyawanid']][jm]=$res['jumlah']; $tjm+=$res['jumlah'];
                $dzArr[$res['karyawanid']][tt]+=$res['jumlah']; $ttt+=$res['jumlah'];
        }
         //premi kemandoran
    $sPeople="SELECT b.namakaryawan, b.kodejabatan, a.karyawanid as karyawanid, sum(a.premiinput) as jumlah 
                          FROM ".$dbname.".kebun_premikemandoran a
                          LEFT JOIN ".$dbname.".datakaryawan b on a.karyawanid = b.karyawanid
                          WHERE a.periode like '".$periode."%' ".$where3." and a.posting=1
                          GROUP BY a.karyawanid ORDER BY a.karyawanid";
        $query=mysql_query($sPeople) or die(mysql_error());
        while($res=mysql_fetch_assoc($query))
        {
        $dzArr[$res['karyawanid']][id]=$res['karyawanid'];
                if($dzArr[$res['karyawanid']][nm]=='')$dzArr[$res['karyawanid']][nm]=$res['namakaryawan']." *";
                if($dzArr[$res['karyawanid']][jb]=='')$dzArr[$res['karyawanid']][jb]=$jab[$res['kodejabatan']];
        $dzArr[$res['karyawanid']][km]=$res['jumlah']; $tkm+=$res['jumlah'];
        $dzArr[$res['karyawanid']][tt]+=$res['jumlah']; $ttt+=$res['jumlah'];
        }


// determine child =========================================================================
switch($proses)
{
// preview header =========================================================================
        case'preview':
        echo"<table cellspacing='1' border='0' class='sortable'>
        <thead class=rowheader>
        <tr>
        <td>No</td>
        <td>".$_SESSION['lang']['nama']."</td>
        <td>".$_SESSION['lang']['tipekaryawan']."</td>
        <td>".$_SESSION['lang']['jabatan']."</td>
        <td>".$_SESSION['lang']['subbagian']."</td>
        <td>".$_SESSION['lang']['hasilkerja']."(".$_SESSION['lang']['panen'].")</td>
        <td>Norma</td>
        <td>Premi ".$_SESSION['lang']['panen']."</td>
        <td>Insentif ".$_SESSION['lang']['pemeltanaman']."</td>
        <td>Premi Traksi</td>
        <td>Premi ".$_SESSION['lang']['pengawasan']."</td>
        <td>Premi ".$_SESSION['lang']['mandor']." ". $_SESSION['lang']['panen']."</td>
        <td>Penalty ". $_SESSION['lang']['panen']."</td>
        <td>Penalty Traksi</td>
        <td>Total</td></tr></thead>
        <tbody>
        ";
        $no=0;
// preview array content =========================================================================
foreach($dzArr as $qwe)
        {	
            if($qwe['tt']>0){	
                $no+=1;
              echo"<tr class=rowcontent><td>".$no."</td>
                <td>".$qwe['nm']."</td>
                <td>".$qwe['tk']."</td>
                <td>".$qwe['jb']."</td>
                <td>".$qwe['sb']."</td>";
                if($qwe['hk']!=0)echo"<td align=right>".number_format($qwe['hk'])."</td>"; else echo"<td align=right></td>";
                if($qwe['nr']!=0)echo"<td align=right>".number_format($qwe['nr'])."</td>"; else echo"<td align=right></td>";
                if($qwe['up']!=0)echo"<td align=right>".number_format($qwe['up'])."</td>"; else echo"<td align=right></td>";
                if($qwe['in']!=0)echo"<td align=right>".number_format($qwe['in'])."</td>"; else echo"<td align=right></td>";
                if($qwe['pr']!=0)echo"<td align=right>".number_format($qwe['pr'])."</td>"; else echo"<td align=right></td>";

                if($qwe['jm']!=0)echo"<td align=right>".number_format($qwe['jm'])."</td>"; else echo"<td align=right></td>";
                if($qwe['km']!=0)echo"<td align=right>".number_format($qwe['km'])." </td>"; else echo"<td align=right></td>";
                if($qwe['rp']!=0)echo"<td align=right>".number_format($qwe['rp'])."</td>"; else echo"<td align=right></td>";
                if($qwe['pe']!=0)echo"<td align=right>".number_format($qwe['pe'])."</td>"; else echo"<td align=right></td>";
                echo"<td align=right>".number_format($qwe['tt'])."</td>";
                echo"</tr>";
            }
        }
// preview array totals =========================================================================
        echo"<tr class=rowheader><td colspan=5>Total</td>
                <td align=right>".number_format($thk)."</td>
                <td align=right>".number_format($tnr)."</td>
                <td align=right>".number_format($tup)."</td>
                <td align=right>".number_format($tin)."</td>
                <td align=right>".number_format($tpr)."</td>

                <td align=right>".number_format($tjm)."</td>
                <td align=right>".number_format($tkm)."</td>
                <td align=right>".number_format($trp)."</td>
                <td align=right>".number_format($tpe)."</td>
                <td align=right>".number_format($ttt)."</td>";
        echo"</tr>";
        echo"</tbody></table>"; 
        break;

        case'excel':
// excel header =========================================================================
        $stream.="
        <table border='0'>
          <tr>
          <td colspan='12' align=center>".strtoupper("Laporan Premi")." ".$nmOrg."</td></tr>
        <tr>
          <td colspan='12' align=center>".strtoupper($_SESSION['lang']['periode'])." : ". $tanggalMulai." s.d. ". $tanggalSampai."</td></tr>
          <tr><td colspan='12'>&nbsp;</td></tr>
        </table>";
        $stream.="<table border='1'>
        <thead class=rowheader>
        <tr>
        <td bgcolor=#DEDEDE align=center>No</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nama']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jabatan']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['hasilkerja']."(".$_SESSION['lang']['panen'].")</td>
        <td bgcolor=#DEDEDE align=center>Norma</td>
        <td bgcolor=#DEDEDE align=center>Premi ".$_SESSION['lang']['panen']."</td>
        <td bgcolor=#DEDEDE align=center>Insentif ".$_SESSION['lang']['pemeltanaman']."</td>
        <td bgcolor=#DEDEDE align=center>Premi Traksi</td>
        <td bgcolor=#DEDEDE align=center>Premi ".$_SESSION['lang']['pengawasan']."</td>
        <td bgcolor=#DEDEDE align=center>Penalty ". $_SESSION['lang']['panen']."</td>
        <td bgcolor=#DEDEDE align=center>Penalty Traksi</td>
        <td bgcolor=#DEDEDE align=center>Total</td></tr></thead>
        <tbody>
    ";
        $no=0;
// excel array content =========================================================================
foreach($dzArr as $qwe)
        {	
            if($qwe['tt']>0){	
                $no+=1;
                $stream.="<tr class=rowcontent><td>".$no."</td>
                <td>".$qwe['nm']."</td>
                <td>".$qwe['jb']."</td>";
                if($qwe['hk']!=0)$stream.="<td align=right>".number_format($qwe['hk'])."</td>"; else $stream.="<td align=right></td>";
                if($qwe['nr']!=0)$stream.="<td align=right>".number_format($qwe['nr'])."</td>"; else $stream.="<td align=right></td>";
                if($qwe['up']!=0)$stream.="<td align=right>".number_format($qwe['up'])."</td>"; else $stream.="<td align=right></td>";
                if($qwe['in']!=0)$stream.="<td align=right>".number_format($qwe['in'])."</td>"; else $stream.="<td align=right></td>";
                if($qwe['pr']!=0)$stream.="<td align=right>".number_format($qwe['pr'])."</td>"; else $stream.="<td align=right></td>";
                if($qwe['jm']!=0)$stream.="<td align=right>".number_format($qwe['jm'])."</td>"; else $stream.="<td align=right></td>";
                if($qwe['rp']!=0)$stream.="<td align=right>".number_format($qwe['rp'])."</td>"; else $stream.="<td align=right></td>";
                if($qwe['pe']!=0)$stream.="<td align=right>".number_format($qwe['pe'])."</td>"; else $stream.="<td align=right></td>";
                $stream.="<td align=right>".number_format($qwe['tt'])."</td>";
                $stream.="</tr>";
            }  
        }
// excel array totals =========================================================================
        $stream.="<tr class=rowheader><td colspan=3>Total</td>
                <td align=right>".number_format($thk)."</td>
                <td align=right>".number_format($tnr)."</td>
                <td align=right>".number_format($tup)."</td>
                <td align=right>".number_format($tin)."</td>
                <td align=right>".number_format($tpr)."</td>
                <td align=right>".number_format($tjm)."</td>
                <td align=right>".number_format($trp)."</td>
                <td align=right>".number_format($tpe)."</td>
                <td align=right>".number_format($ttt)."</td>";
        $stream.="</tr>";
        $stream.="</tbody>";
                        $stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
// excel create file =========================================================================
                        $nop_="LaporanPremi".$periode."__".$kdOrg;
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

        case'pdf':
        class PDF extends FPDF
    {
        function Header() 
                {
// pdf header =========================================================================
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
//				global $dzArr;
                                global $periode;
                                global $kdOrg;
                                global $nmOrg;
                                global $tanggalMulai;
                                global $tanggalSampai;
                                global $where;
                                $cols=247.5;
                $query = selectQuery($dbname,'organisasi','alamat,telepon',
                    "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                $orgData = fetchData($query);

                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 10;
                if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
                $this->Image($path,$this->lMargin,$this->tMargin,70);	
                $this->SetFont('Arial','B',8);
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

                $this->SetFont('Arial','B',8);
                                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['laporanPremi'],'',0,'L');
                                $this->Ln();

                                $this->Cell($width,$height,strtoupper($_SESSION['lang']['laporanPremi']." : ".$nmOrg),'',0,'C');
                                $this->Ln();
                                $this->Cell($width,$height,strtoupper($_SESSION['lang']['periode'])." :". $tanggalMulai." s/d ". $tanggalSampai,'',0,'C');
                                $this->Ln();
                                $this->Ln();
                $this->SetFont('Arial','',5);
                $this->SetFillColor(220,220,220);
                                $this->Cell(3/100*$width,$height,'No',1,0,'C',1);
                                $this->Cell(15/100*$width,$height,$_SESSION['lang']['nama'],1,0,'C',1);		
                                $this->Cell(13/100*$width,$height,$_SESSION['lang']['jabatan'],1,0,'C',1);	
                                $this->Cell(7/100*$width,$height,"Hasil PNN",1,0,'C',1);	
                                $this->Cell(7/100*$width,$height,"Norma",1,0,'C',1);	
                                $this->Cell(7/100*$width,$height,"Premi PNN",1,0,'C',1);	
                                $this->Cell(7/100*$width,$height,"Premi PRW",1,0,'C',1);	
                                $this->Cell(7/100*$width,$height,"Premi Trak",1,0,'C',1);	
                                $this->Cell(7/100*$width,$height,"Pngawsan",1,0,'C',1);	
                                $this->Cell(7/100*$width,$height,"Kemandoran PNN",1,0,'L',1);	
                                $this->Cell(7/100*$width,$height,"Pnalty PNN",1,0,'C',1);	
                                $this->Cell(7/100*$width,$height,"Pnalty Trak",1,0,'C',1);	
                                $this->Cell(8/100*$width,$height,"Total (Rp)",1,0,'C',1);
                                        $this->Ln();

        }

        function Footer()
        {
// pdf footer =========================================================================
            $this->SetY(-15);
            $this->SetFont('Arial','I',8);
            $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
        }
    }
// pdf init =========================================================================
        $pdf=new PDF('P','pt','A4');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
                $pdf->AddPage();
                $pdf->SetFillColor(255,255,255);
                $pdf->SetFont('Arial','',6);
                $no = 0;
// pdf array content =========================================================================
                        foreach($dzArr as $qwe)
                        { 
                            if($qwe['tt']>0){	
                                $no+=1;
                                $pdf->Cell(3/100*$width,$height,$no,1,0,'C',l);
                                $pdf->Cell(15/100*$width,$height,$qwe['nm'],1,0,'L',1);		
                                $pdf->Cell(13/100*$width,$height,$qwe['jb'],1,0,'L',1);	
                                if($qwe['hk']!=0)$pdf->Cell(7/100*$width,$height,number_format($qwe['hk']),1,0,'R',1); else $pdf->Cell(7/100*$width,$height,'',1,0,'R',1);
                                if($qwe['nr']!=0)$pdf->Cell(7/100*$width,$height,number_format($qwe['nr']),1,0,'R',1); else $pdf->Cell(7/100*$width,$height,'',1,0,'R',1);
                                if($qwe['up']!=0)$pdf->Cell(7/100*$width,$height,number_format($qwe['up']),1,0,'R',1); else $pdf->Cell(7/100*$width,$height,'',1,0,'R',1); 
                                if($qwe['in']!=0)$pdf->Cell(7/100*$width,$height,number_format($qwe['in']),1,0,'R',1); else $pdf->Cell(7/100*$width,$height,'',1,0,'R',1);	
                                if($qwe['pr']!=0)$pdf->Cell(7/100*$width,$height,number_format($qwe['pr']),1,0,'R',1); else $pdf->Cell(7/100*$width,$height,'',1,0,'R',1);	
                                if($qwe['jm']!=0)$pdf->Cell(7/100*$width,$height,number_format($qwe['jm']),1,0,'R',1); else $pdf->Cell(7/100*$width,$height,'',1,0,'R',1);	
                                if($qwe['km']!=0)$pdf->Cell(7/100*$width,$height,number_format($qwe['km']),1,0,'R',1); else $pdf->Cell(7/100*$width,$height,'',1,0,'R',1);	
                                if($qwe['rp']!=0)$pdf->Cell(7/100*$width,$height,number_format($qwe['rp']),1,0,'R',1); else $pdf->Cell(7/100*$width,$height,'',1,0,'R',1);	
                                if($qwe['pe']!=0)$pdf->Cell(7/100*$width,$height,number_format($qwe['pe']),1,0,'R',1); else $pdf->Cell(7/100*$width,$height,'',1,0,'R',1);	
                                $pdf->Cell(8/100*$width,$height,number_format($qwe['tt']),1,0,'R',1);
                                $pdf->Ln(); 
                            }   
                        }
// pdf array totals =========================================================================
                                $pdf->Cell(31/100*$width,$height,"TOTAL",1,0,'C',l);
                                $pdf->Cell(7/100*$width,$height,number_format($thk),1,0,'R',1);	
                                $pdf->Cell(7/100*$width,$height,number_format($tnr),1,0,'R',1);	
                                $pdf->Cell(7/100*$width,$height,number_format($tup),1,0,'R',1);	
                                $pdf->Cell(7/100*$width,$height,number_format($tin),1,0,'R',1);	
                                $pdf->Cell(7/100*$width,$height,number_format($tpr),1,0,'R',1);	
                                $pdf->Cell(7/100*$width,$height,number_format($tjm),1,0,'R',1);	
                                $pdf->Cell(7/100*$width,$height,number_format($tkm),1,0,'R',1);
                                $pdf->Cell(7/100*$width,$height,number_format($trp),1,0,'R',1);	
                                $pdf->Cell(7/100*$width,$height,number_format($tpe),1,0,'R',1);	
                                $pdf->Cell(8/100*$width,$height,number_format($ttt),1,0,'R',1);
                                $pdf->Ln(); 
        $pdf->Output();
        break;

        default:
        break;
}
?>