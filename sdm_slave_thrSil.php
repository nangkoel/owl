<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$kdOrg=$_POST['kdOrg'];
$per=$_POST['per'];
$tk=$_POST['tk'];
$agama=$_POST['agama'];
$tgl=  tanggaldgnbar($_POST['tgl']);

$tahun=$_POST['tahun'];
if($proses=='excel')
{
    $kdOrg=$_GET['kdOrg'];
    $per=$_GET['per'];
    $tk=$_GET['tk'];
    $agama=$_GET['agama'];
    $tgl=  tanggaldgnbar($_GET['tgl']);
    $tahun=$_GET['tahun'];
}


$regional=makeOption($dbname,'bgt_regional_assignment','kodeunit,regional');

$tglMulai=$per.'-01';


$nmTk=makeOption($dbname,'sdm_5tipekaryawan','id,tipe');
$nmBag=makeOption($dbname,'sdm_5departemen','kode,nama');
$nmJab=makeOption($dbname,'sdm_5jabatan','kodejabatan,namajabatan');


if ($proses == 'excel') 
    {
        $stream = "<table class=sortable cellspacing=1 border=1>";
    } else 
    {
        $stream = "<table class=sortable cellspacing=1>";
    }


if($tk=='3')
{
    #####KBLL
    ####
    

    $stream.="<thead class=rowheader>
        <tr class=rowheader>
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['nourut']."</td>
            <td bgcolor=#CCCCCC hidden align=center>".$_SESSION['lang']['karyawanid']."</td>    
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['namakaryawan']."</td>
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['nik']."</td>    
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['tipekaryawan']."</td>
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['lokasitugas']."</td>
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['subbagian']."</td>
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['bagian']."</td>
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['jabatan']."</td>
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['tmk']."</td>
            <td bgcolor=#CCCCCC align=center>Tanggal Pengangkatan<br>Karyawan Tetap</td>
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['masakerja']."</td> 
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['gajipokok']."</td>
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['pengali']."</td>
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['thr']."</td>
            
        </tr>
        <tr>";
       $stream.="</thead>";

       $iKar="select karyawanid,namakaryawan,nik,tipekaryawan,kodejabatan,lokasitugas,subbagian,bagian,tanggalmasuk,tanggalpengangkatan"
               . " from ".$dbname.".datakaryawan where  agama='".$agama."' and tipekaryawan='".$tk."' and "
               . " lokasitugas='".$kdOrg."' and (tanggalkeluar>'".$tglMulai."' or tanggalkeluar='0000-00-00') ";
       $nKar=mysql_query($iKar) or die (mysql_error($conn));
       while($dKar=  mysql_fetch_assoc($nKar))
       {
            $kar[$dKar['karyawanid']]=$dKar['karyawanid'];
            $nama[$dKar['karyawanid']]=$dKar['namakaryawan'];
            $tk[$dKar['karyawanid']]=$dKar['tipekaryawan'];
            $nik[$dKar['karyawanid']]=$dKar['nik'];
            $lokasi[$dKar['karyawanid']]=$dKar['lokasitugas'];
            $subBag[$dKar['karyawanid']]=$dKar['subbagian'];
            $jab[$dKar['karyawanid']]=$dKar['kodejabatan'];
            $bag[$dKar['karyawanid']]=$dKar['bagian'];
            $tglAngkat[$dKar['karyawanid']]=$dKar['tanggalpengangkatan'];
            $tglMasuk[$dKar['karyawanid']]=$dKar['tanggalmasuk'];
       }

       $iGaji="select karyawanid,jumlah from ".$dbname.".sdm_5gajipokok where tahun='".$tahun."' and idkomponen=1 ";
       $nGaji=  mysql_query($iGaji) or die (mysql_error($conn));
       while($dGaji=  mysql_fetch_assoc($nGaji))
       {
           $gaji[$dGaji['karyawanid']]=$dGaji['jumlah'];
       }
       
       //print_r($tglAngkat);
       
       foreach($kar as $karId)
       {
            $selisihHari=days_360($tgl,$tglAngkat[$karId]);
            $masaKerja=number_format($selisihHari/360,5);
            $cekDpt=$masaKerja*12;
            if($cekDpt<3)
            {
                $pengali=0;
            }
            else if($cekDpt<12&&$cekDpt>=3)
            {
                $pengali=$masaKerja;
            }
            else if($cekDpt>=12)
            {
                $pengali=1;
            }
            //$pengali=number_format($pengali,5);
            $no+=1;
            $stream.="<tr class=rowcontent id=row".$no.">";
                $stream.="<td>".$no."</td>";
                $stream.="<td hidden id=karyawanid".$no.">".$karId."</td>";
                $stream.="<td>".$nama[$karId]."</td>";
                $stream.="<td>".$nik[$karId]."</td>";
                $stream.="<td>".$nmTk[$tk[$karId]]."</td>";
                $stream.="<td id=kdorg".$no.">".$lokasi[$karId]."</td>";
                $stream.="<td>".$subBag[$karId]."</td>";
                $stream.="<td>".$nmJab[$jab[$karId]]."</td>";
                $stream.="<td>".$nmBag[$bag[$karId]]."</td>";
                $stream.="<td>".tanggalnormal($tglMasuk[$karId])."</td>";
                $stream.="<td>".tanggalnormal($tglAngkat[$karId])."</td>";
                $stream.="<td align=right>".$masaKerja."</td>";
                
                $stream.="<td align=right>".$gaji[$karId]."</td>";
                $stream.="<td align=right>".$pengali."</td>";
                
                $thr=$pengali*$gaji[$karId];
                $thr=number_format($thr/1000);
                $thr=str_replace(",","",$thr)*1000;
                //$stream.="<td><input type=text id=jumlah".$no." value=".number_format($thr)." keypress=\"return angka_doang(event);\" class=myinputtext style=\"width:50px;\"></td>";
                $stream.="<td align=right id=jumlah".$no.">".number_format($thr)."</td>";
            $stream.="</tr>";    
       }
}
else 
{
    #HARIANNNNNNNNNNNNNNNNNNNNNNNN
    #############################
    
    $iPerThr="select * from ".$dbname.".sdm_5periodethr where regional='".$regional[$kdOrg]."' and tahun='".$tahun."' and agama='".$agama."' ";
    //echo $iPerThr;
    $nPerThr=mysql_query($iPerThr) or die (mysql_error($conn));
    $dPerThr=  mysql_fetch_assoc($nPerThr);
        $perMulai=$dPerThr['periodemulai'];
        $perSampai=$dPerThr['periodesampai'];
        
        $blnMulai=substr($perMulai,5,2);
        $thnMulai=substr($perMulai,0,4);
        
        $blnSampai=substr($perSampai,5,2);
        $thnSampai=substr($perSampai,0,4);
        
        #bentuk tanggal mulai
        for($i=$blnMulai;$i<=12;$i++)
        {
            if(strlen($i)=='1')
            {
                $i="0".$i;
            } 
            $per=$thnMulai.'-'.$i;
            $periode[$per]=$per;
        }
        
        #bentuk tanggal sampai
        for($i=1;$i<=$blnSampai;$i++)
        {
            if(strlen($i)=='1')
            {
                $i="0".$i;
            } 
            $per=$thnSampai.'-'.$i;
            $periode[$per]=$per;
        }
        
      
    /*echo"<pre>";
    print_r($periode);
    echo"</pre>";*/  
    
    #bentuk karyawan harian
     $iKar="select karyawanid,namakaryawan,nik,tipekaryawan,kodejabatan,lokasitugas,subbagian,bagian,tanggalmasuk,tanggalpengangkatan"
               . " from ".$dbname.".datakaryawan where agama='".$agama."' and tipekaryawan='".$tk."' and "
               . " lokasitugas='".$kdOrg."' and (tanggalkeluar>'".$tglMulai."' or tanggalkeluar='0000-00-00') order by namakaryawan";    
        
        
        
    /*$iKar="select karyawanid,namakaryawan,nik,tipekaryawan,kodejabatan,lokasitugas,subbagian,bagian,tanggalmasuk,"
               . " COALESCE(ROUND(DATEDIFF('".$tglAbis."',tanggalmasuk)/365.25,3),0) as masakerja "
               . " from ".$dbname.".datakaryawan where agama='".$agama."' and tipekaryawan='".$tk."' and "
               . " lokasitugas='".$kdOrg."' and (tanggalkeluar>'".$tglMulai."' or tanggalkeluar='0000-00-00') ";*/
   
    $nKar=mysql_query($iKar) or die (mysql_error($conn));
    while($dKar=  mysql_fetch_assoc($nKar))
    {
         $kar[$dKar['karyawanid']]=$dKar['karyawanid'];
         $nama[$dKar['karyawanid']]=$dKar['namakaryawan'];
         $tk[$dKar['karyawanid']]=$dKar['tipekaryawan'];
         $nik[$dKar['karyawanid']]=$dKar['nik'];
         $lokasi[$dKar['karyawanid']]=$dKar['lokasitugas'];
         $subBag[$dKar['karyawanid']]=$dKar['subbagian'];
         $jab[$dKar['karyawanid']]=$dKar['kodejabatan'];
         $bag[$dKar['karyawanid']]=$dKar['bagian'];
         $tglMasuk[$dKar['karyawanid']]=$dKar['tanggalmasuk'];
         $tglAngkat[$dKar['karyawanid']]=$dKar['tanggalpengangkatan'];
         
    }    
    $iGaji="select karyawanid,jumlah from ".$dbname.".sdm_5gajipokok where tahun='".$tahun."' and idkomponen=1 ";
    $nGaji=  mysql_query($iGaji) or die (mysql_error($conn));
    while($dGaji=  mysql_fetch_assoc($nGaji))
    {
        $gaji[$dGaji['karyawanid']]=$dGaji['jumlah']/25;
    }
        
    #bentuk HK dari database  
    $iHk="select * from ".$dbname.".sdm_hkbulanan where periode between '".$perMulai."' and '".$perSampai."' and kodeorg='".$kdOrg."'";
    //echo $iHk;
    $nHk=mysql_query($iHk) or die (mysql_error($conn));
    while($dHk=mysql_fetch_assoc($nHk))
    {
        $hk[$dHk['karyawanid']][$dHk['periode']]=$dHk['hk'];
    }
    
    
    $stream.="<thead class=rowheader>
        <tr class=rowheader>
            <td bgcolor=#CCCCCC rowspan=2 align=center>".$_SESSION['lang']['nourut']."</td>
            <td hidden bgcolor=#CCCCCC rowspan=2 align=center>".$_SESSION['lang']['karyawanid']."</td>    
            <td bgcolor=#CCCCCC rowspan=2 align=center>".$_SESSION['lang']['namakaryawan']."</td>
            <td bgcolor=#CCCCCC rowspan=2 align=center>".$_SESSION['lang']['nik']."</td>    
            <td bgcolor=#CCCCCC rowspan=2 align=center>".$_SESSION['lang']['tipekaryawan']."</td>
            <td bgcolor=#CCCCCC rowspan=2 align=center>".$_SESSION['lang']['lokasitugas']."</td>
            <td bgcolor=#CCCCCC rowspan=2 align=center>".$_SESSION['lang']['subbagian']."</td>
            <td bgcolor=#CCCCCC rowspan=2 align=center>".$_SESSION['lang']['bagian']."</td>
            <td bgcolor=#CCCCCC rowspan=2 align=center>".$_SESSION['lang']['jabatan']."</td>
            <td bgcolor=#CCCCCC rowspan=2 align=center>Tanggal Masuk <br>Karyawan</td>
            <td bgcolor=#CCCCCC align=center rowspan=2>Tanggal Pengangkatan<br>Karyawan Tetap</td>
            <td bgcolor=#CCCCCC rowspan=2 align=center>".$_SESSION['lang']['masakerja']."</td>
            <td bgcolor=#CCCCCC colspan=12 align=center>".$_SESSION['lang']['periode']."</td> 
            <td bgcolor=#CCCCCC rowspan=2 align=center>".$_SESSION['lang']['total']." HK</td>  
            <td bgcolor=#CCCCCC rowspan=2 align=center>Bulan Aktif Bekerja</td>
            <td bgcolor=#CCCCCC rowspan=2 align=center>Faktor</td>
            <td bgcolor=#CCCCCC rowspan=2 align=center>".$_SESSION['lang']['thr']."</td> 
        </tr>
        <tr>";
           foreach($periode as $perList)
           {
               $stream.="<td bgcolor=#CCCCCC align=center>".$perList."</td>"; 
           }
       $stream.="</tr></thead>";
       
       #tampilkan data
     
       foreach($kar as $karId)
       {
            $selisihHari=days_360($tgl,$tglMasuk[$karId]);
            $perMsk=substr($tglMasuk[$karId],0,7);    

            $no+=1;
            $stream.="<tr class=rowcontent id=row".$no.">";
                $stream.="<td>".$no."</td>";
                $stream.="<td hidden id=karyawanid".$no.">".$karId."</td>";
                $stream.="<td>".$nama[$karId]."</td>";
                $stream.="<td>".$nik[$karId]."</td>";
                $stream.="<td>".$nmTk[$tk[$karId]]."</td>";
                $stream.="<td id=kdorg".$no.">".$lokasi[$karId]."</td>";
                $stream.="<td>".$subBag[$karId]."</td>";
                $stream.="<td>".$nmJab[$jab[$karId]]."</td>";
                $stream.="<td>".$nmBag[$bag[$karId]]."</td>";
                $stream.="<td>".tanggalnormal($tglMasuk[$karId])."</td>";
                $stream.="<td>".tanggalnormal($tglAngkat[$karId])."</td>";
               
                $stream.="<td  align=right>".$selisihHari."</td>";
                
                
                $blnAktif=0;
                foreach($periode as $perList)
                {
                    #untuk cek periode jika kurang dari tanggal masuk karyawan maka di 0kan
                    if($perList<$perMsk)
                    {$hk[$karId][$perList]=0;}
                    else
                    {$hk[$karId][$perList]=$hk[$karId][$perList];}
                    
                    $stream.="<td align=right>".$hk[$karId][$perList]."</td>";
                    
                    #untuk bentuk tanggal aktif kerja
                    if($hk[$karId][$perList]==0 || $hk[$karId][$perList]=='')
                    { $noZ=0;}
                    else
                    {$noZ=1;}
                    $blnAktif+=$noZ;
                    $total[$karId]+=$hk[$karId][$perList];
                }
                
                $faktor=number_format($blnAktif/12,2);   
                $a=$total[$karId]/$blnAktif;

                $upahThr=$a*$faktor*$gaji[$karId];
                #bentuk pembulatan
                $upahThr=number_format(($upahThr/1000));
                $upahThr=str_replace(",","",$upahThr)*1000;
                
                
                #kunci jika <3 bulan / 90 hari maka thr = 0
                if($tglAngkat[$karId]=='0000-00-00')
                {
                   if($selisihHari<90)
                    {$upahThr=0;} 
                }
                
                

                $stream.="<td align=right>".$total[$karId]."</td>";  
                $stream.="<td align=right>".$blnAktif."</td>";   
                $stream.="<td align=right>".$faktor."</td>";
                $stream.="<td align=right id=jumlah".$no.">".number_format($upahThr)."</td>";
                $stream.="</tr>";    
        }
       echo"pada saat karyawan <b>KHT menjadi KBL</b> harap info ke <b>FINANCE DAHULU</b> untuk melakukan proses THR sebagai KHTnya,<br>"
        . "baru selanjutnya ke HRD untuk update karyawna ke KBL. ";
}$stream.="<button class=mybutton onclick=saveAll(".$no.");>".$_SESSION['lang']['proses']."</button>";
$stream.="</table>";	
	   
   

   
   
  
   
   
   
   
   
  
   
  
   



$stream.="<tbody></table>";
switch($proses)
{
######PREVIEW
	case 'preview':
		echo $stream;
    break;

######EXCEL	
	case 'excel':
		//$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="laporan_daftar_panen".$pt."_".$per;
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
}
?>