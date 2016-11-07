<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

$proses=$_GET['proses'];
$per=$_POST['per'];
$unit=$_POST['unit'];
$proses2=$_POST['proses'];
$periode=$_POST['periode'];
$karyawanid=$_POST['karyawanid'];
$hk=$_POST['hk'];
$tglMulai=$_POST['tglMulai'];
$tglSampai=$_POST['tglSampai'];
$hkAbs=$_POST['hkAbs'];
if($proses=='excel'){
    $per=$_GET['per'];
    $unit=$_GET['unit'];
}


//sdm_slave_prosesAbs

$tahunGaji=substr($per,0,4);


$atgl="select * from ".$dbname.".sdm_5periodegaji where periode='".$per."' and kodeorg='".$unit."'";
//echo $atgl;
$btgl=mysql_query($atgl) or die(mysql_error($conn));
$ctgl=mysql_fetch_assoc($btgl);

	$tgl1=$ctgl['tanggalmulai'];
	$tgl2=$ctgl['tanggalsampai'];

//$golkar=makeOption($dbname,'datakaryawan','karyawanid','kodegolongan');
$namagol=makeOption($dbname,'sdm_5golongan','kodegolongan','namagolongan');
$namatipe=makeOption($dbname,'sdm_5tipekaryawan','id,tipe');

#algoritmanya
#ambil data karyawan yang ada di sdm_gaji left join dengan datakaryawan untuk memfilter tipekaryawan hanya KHT=4
#ambil absensi khusus untuk kebun_kehadiran_Vw yang di ambil jhknya


#get list kary from sdm_gaji
$sGetKary="select distinct a.karyawanid,b.namakaryawan,b.nik,b.kodejabatan,b.subbagian from ".$dbname.".sdm_gaji a left join ".$dbname.".datakaryawan b
          on a.karyawanid=b.karyawanid
           where periodegaji='".$per."' and kodeorg='".$unit."' and tipekaryawan=4";   
           //echo $sGetKary;
$rGetkary=fetchData($sGetKary);
foreach($rGetkary as $row => $kar){
  $lstDt[$kar['karyawanid']]=$kar['karyawanid'];
  $lstNmKar[$kar['karyawanid']][nmkar]=$kar['namakaryawan'];
  $lstNmKar[$kar['karyawanid']][nik]=$kar['nik'];
  $lstNmKar[$kar['karyawanid']][jbtn]=$kar['kodejabatan'];
  $lstNmKar[$kar['karyawanid']][sbbgn]=$kar['subbagian'];
}  

//echo '<pre>'.print_r($lstNmKar).'</pre>';

# ambil gaji pokok karyawan
$sGKary = "select jumlah,a.karyawanid from ".$dbname.".sdm_5gajipokok a 
          left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
          where tahun='".substr($per, 0,4)."' 
          and idkomponen=1 and lokasitugas='".$unit."'";
$rGkary=fetchData($sGKary);
  foreach($rGkary as $row => $kar){
    $lstDtGK[$kar['karyawanid']]=$kar['jumlah']/25;
  }  


          ##tambahan absen permintaan dari pak ujang#
          $sAbsn="select absensi,tanggal,karyawanid,insentif from ".$dbname.".sdm_absensidt 
                  where tanggal between '".$tgl1."' and '".$tgl2."' 
                  and substr(kodeorg,1,4) = '".$unit."' 
                  and absensi in (select kodeabsen from ".$dbname.".sdm_5absensi where kelompok=1 and kodeabsen in ('H','HL','P0','P1','PC','S1'))";
                         //exit("Error".$sAbsn);
                        $rAbsn=fetchData($sAbsn);
                        foreach ($rAbsn as $absnBrs =>$resAbsn){
                          if(!is_null($resAbsn['absensi'])){
                              $hasilAbsn[$resAbsn['karyawanid'].$resAbsn['tanggal']]=1;
                              $resData[$resAbsn['karyawanid']][]=$resAbsn['karyawanid'];
                            }
                        }
            $sKehadiran="select sum(jhk) as jhk,tanggal,karyawanid from ".$dbname.".kebun_kehadiran_vw 
                                 where tanggal between '".$tgl1."' and '".$tgl2."' and substr(kodeorg,1,4)= '".$unit."'
                                 group by karyawanid,tanggal";
                
                        $rkehadiran=fetchData($sKehadiran);
                        foreach ($rkehadiran as $khdrnBrs =>$resKhdrn)
                        { 
                                
                                    $hasilAbsn[$resKhdrn['karyawanid'].$resKhdrn['tanggal']]=$resKhdrn['jhk'];
                                    $resData[$resKhdrn['karyawanid']][]=$resKhdrn['karyawanid'];

                        }
                        //echo $sKehadiran;

            $sPrestasi="select upahkerja,tanggal,karyawanid from ".$dbname.".kebun_prestasi_vw 
                                 where tipetransaksi='PNN' and tanggal between '".$tgl1."' and '".$tgl2."' and  unit='".$unit."'";
                
                        $rPrestasi=fetchData($sPrestasi);
                        foreach ($rPrestasi as $khdrnBrs =>$resKhdrn)
                        { 
                                $hasilAbsn[$resKhdrn['karyawanid'].$resKhdrn['tanggal']]=1;
                                if($resKhdrn['upahkerja']<$lstDtGK[$resKhdrn['karyawanid']]){
                                  @$proposi = ($resKhdrn['upahkerja']/$lstDtGK[$resKhdrn['karyawanid']]);
                                  $hasilAbsn[$resKhdrn['karyawanid'].$resKhdrn['tanggal']]=round($proposi,2);
                                  //exit("Error:".round($proposi,2)."______".$resKhdrn['upahkerja']."____".$lstDtGK[$resKhdrn['karyawanid']]."____".$resKhdrn['karyawanid']."___".$resKhdrn['tanggal']);
                                  //echo $sPrestasi;
                                }

                        }
        
    
        #tambahan absen permintaan abis disini#

            #cek gaji sudah tutup /belum	
/*xi="select distinct * from ".$dbname.".sdm_5periodegaji where periode='".$per."' 
            and kodeorg='".$unit."' and sudahproses='1'";
            $xu=mysql_query($xi) or die(mysql_error($conn));
            if(mysql_num_rows($xu)>0)
                $aktif2=false;
                   else
                 $aktif2=true;
              if(!$aktif2)
              {
                  exit("Error:Periode gaji untuk ".$unit." sudah ditutup");
              }


             #periksa apakah sudah tutup buku
             $str="select * from ".$dbname.".setup_periodeakuntansi where periode='".$per."' and 
                   kodeorg='".$unit."' and tutupbuku=1";
             $res=mysql_query($str);
             if(mysql_num_rows($res)>0)
                 $aktif=false;
             else
                 $aktif=true;
            if(!$aktif){
                exit("Error:Periode akuntansi untuk ".$unit." sudah tutup buku");
            } 
*/
             
	      
  $test = rangeTanggal($tgl1, $tgl2);
	$jmlHari=count($test);
	//cek max hari inputan
	if($jmlHari>32){
		echo"warning:Range tanggal tidak valid";
		exit();
	}
        

        
	$sAbsen="select kodeabsen from ".$dbname.".sdm_5absensi order by kodeabsen";
	$qAbsen=mysql_query($sAbsen) or die(mysql_error());
	$jmAbsen=mysql_num_rows($qAbsen);
	$colSpan=intval($jmAbsen)+2;
        
       if($proses=='excel'){
           $border="border=1";
       }
       else{
           $border="border=0";
       }
      /* echo "<pre>";
       print_r($lstDt);
       echo"</pre>";*/
       if($proses2!='savedata'){
	$ind="<table cellspacing='1' $border class='sortable'>
	<thead class=rowheader>
	<tr>
	<td align=center>No</td>
	<td align=center>".$_SESSION['lang']['nama']."</td>
	<td align=center>".$_SESSION['lang']['nik']."</td>
	<td align=center>".$_SESSION['lang']['jabatan']."</td>
	<td align=center>".$_SESSION['lang']['subbagian']."</td>
	<td align=center>".$_SESSION['lang']['karyawanid']."</td>
	<td align=center>".$_SESSION['lang']['periode']."</td>
	";/*<td>UMP Bulan</td>
	<td>UMP Harian</td>*/
	foreach($test as $ar => $isi)
	{
		$qwe=date('D', strtotime($isi));
		$ind.="<td width=5px align=center>";
		if($qwe=='Sun')
      $ind.="<font color=red   align=center>".substr($isi,8,2)."</font>"; 
			//echo"<font color=red>".str_replace("-","/",substr(tanggalnormal($isi),0,5))."</font>"; 
                       
		//echo str_replace("-","/",substr(tanggalnormal($isi),0,5)); 
                else  
                $ind.= substr($isi,8,2);
		$ind.="</td>";
		
	}
	
	
	$ind.="
	<td align=center>".$_SESSION['lang']['total']." ".$_SESSION['lang']['hk']."</td>
        <td align=center>".$_SESSION['lang']['total']." ".$_SESSION['lang']['hk']." Absensi</td>";
	$klmpkAbsn=array();
	foreach($test as $ar => $isi)
	{
            $qwe=date('D', strtotime($isi));
	}
	while($rKet=mysql_fetch_assoc($qAbsen))
	{
            $klmpkAbsn[]=$rKet;
	}
	$ind.="
	</tr></thead>
	<tbody>";
  foreach($lstDt as $lstKary){
    $no+=1;
    $ind.="<tr class=rowcontent id=row".$no.">";
    $ind.="<td>".$no."</td>";
    $ind.="<td>".$lstNmKar[$lstKary][nmkar]."</td>";
    $ind.="<td>'".$lstNmKar[$lstKary][nik]."</td>";
    $whrjbtn=" kodejabatan='".$lstNmKar[$lstKary][jbtn]."'";
    $namajbtn=makeOption($dbname,'sdm_5jabatan','kodejabatan,namajabatan', $whrjbtn);
    $jabtn = $namajbtn[$lstNmKar[$lstKary][jbtn]];
    $ind.="<td>".$jabtn."</td>";
    $ind.="<td>".$lstNmKar[$lstKary][sbbgn]."</td>";
    $ind.="<td align=right id=karyawanid".$no.">".$lstKary."</td>";
    $ind.="<td align=right id=periode".$no.">".$per."</td>";
    foreach($test as $ar => $isi) {
      $ind.="<td align=right>".$hasilAbsn[$lstKary.$isi]."</td>";
      if ($hasilAbsn[$lstKary.$isi]!=0) {
        # code...
        $jmlhHrkrj[$lstKary]+=1;
      }
      $jmlhAbsn[$lstKary]+=$hasilAbsn[$lstKary.$isi];
    }
    $ind.="<td align=right id=hk".$no.">".$jmlhAbsn[$lstKary]."</td>";
    $ind.="<td align=right id=hkAbs".$no.">".$jmlhHrkrj[$lstKary]."</td>";
    $ind.="</tr>";

  }
	
  if($proses!='excel'){
      $ind.="<button class=mybutton onclick=saveAll(".$no.");>".$_SESSION['lang']['proses']."</button>";
  }
	$ind.="</tbody></table>";
	
       }
        
switch($proses)
{
######PREVIEW
	case 'preview':
             if($per=='')
              {
                      exit("Error:Periode masih kosong");
                      
              }
            
		echo $ind;
    break;
        
        ######EXCEL	
	case 'excel':
            
             if($per=='')
              {
                      exit("Error:Periode masih kosong");
                      
              }
            
		//$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="laporan_hk_bulanan_".$pt."_".$per;
		if(strlen($ind)>0)
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
			if(!fwrite($handle,$ind))
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
        
        
        

	default:
}


switch($proses2)
{
	case'savedata':
	
		if($hk=='0' or $hk=='')
		{
		}
		else
		{
			$str="insert into ".$dbname.".sdm_hkbulanan (`kodeorg`,`periode`,`karyawanid`,`hk`,`hkabsen`,`updateby`)
			values ('".$unit."','".$periode."','".$karyawanid."','".$hk."','".$hkAbs."','".$_SESSION['standard']['userid']."')";
	
			if(mysql_query($str))
			{
			}
			else
			{
                            $str="update ".$dbname.".sdm_hkbulanan set hk='".$hk."',hkabsen='".$hkAbs."' where kodeorg='".$unit."' and periode='".$periode."' and karyawanid='".$karyawanid."'";
                            if(mysql_query($str))
                            {
                            }
                            else
                            {
                                    echo " Gagal,".addslashes(mysql_error($conn));
                            }
			}
		}
	break;
	
	break;
	default;	
	
	
}

?>