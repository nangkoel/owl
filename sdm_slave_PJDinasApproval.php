<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once ('lib/nangkoelib.php');
$notransaksi=$_POST['notransaksi'];
$karyawanid=$_POST['karyawanid'];
$status=$_POST['status'];
$kolom=$_POST['kolom'];
$tanggal=date('Ymd');

$kolomstatus='status'.$kolom;
$kolomtanggal='tanggal'.$kolom;

$i="select  * from ".$dbname.".sdm_pjdinasht where notransaksi='".$notransaksi."'";
$n=mysql_query($i) or die (mysql_error($conn));
$d=  mysql_fetch_assoc($n);
//exit("Error:$kolomstatus");	
if(($d['persetujuan']!=0)&&($d['statuspersetujuan']==0)){
    if($kolomstatus=='statuspersetujuan2'){
            $status1=$d['statuspersetujuan2'];
            if($status1!='1'){
                    exit("Error:Sorry you can't approve this document,  because the first approver has not given approval or the first approver has been rejected");
            }

    }
}
$setHrd="";
if(($d['persetujuan']==$d['hrd'])||($d['persetujuan2']==$d['hrd'])){
    $setHrd=",statushrd=".$status.", tanggalhrd=".$tanggal;
}
if($kolom=='hrd')
{
	$i="select  * from ".$dbname.".sdm_pjdinasht where notransaksi='".$notransaksi."'";
	$n=mysql_query($i) or die (mysql_error($conn));
	$d=mysql_fetch_assoc($n);
		$status1=$d['statuspersetujuan2'];
		
		if($status1=='0')
		{
			exit("Error:Sorry you can't approve this document,  because the second approver has not given approval yet.");
		}
		
}

$str="update ".$dbname.".sdm_pjdinasht set ".$kolomstatus."=".$status.", 
      ".$kolomtanggal."=".$tanggal.$setHrd." where notransaksi='".$notransaksi."'";	  
//exit("Error:$str");	  
	  
if(mysql_query($str))
{
    //ambil email notifikasi ke GA
	
	if($kolomstatus=='statuspersetujuan')
	{
		$iEmail="select karyawanid,persetujuan2,persetujuan from ".$dbname.".sdm_pjdinasht where notransaksi='".$notransaksi."'";
		$nEmail=mysql_query($iEmail) or die (mysql_error($conn));
		$dEmail=mysql_fetch_assoc($nEmail);
                if ($dEmail['persetujuan'] == $dEmail['persetujuan2'])
                {
                    $str="update ".$dbname.".sdm_pjdinasht set statuspersetujuan2=".$status.", 
                        ".$kolomtanggal."=".$tanggal." where notransaksi='".$notransaksi."'";
                    mysql_query($str);
                } 
                else
                {
                    $to=getUserEmail($d['persetujuan2']);
                    $namakaryawanPengaju=getNamaKaryawan($dEmail['karyawanid']);
                    $namakaryawan=getNamaKaryawan($dEmail['persetujuan']);
                    $nmpnlk=getNamaKaryawan($dEmail['persetujuan2']);
                    $subject="[Notifikasi]".$_SESSION['lang']['persetujuan']." Perjalanan Dinas";
                    $body="<html>
                                     <head>
                                     <body>
                                       <dd>Dengan Hormat, Mr./Mrs. ".$nmpnlk."</dd><br>
                                       Pada hari ini, tanggal ".date('d-m-Y')." karyawan a/n  ".$namakaryawan." mengajukan Persertujuan Perjalanan Dinas atas nama ".$namakaryawanPengaju."
                                       kepada bapak/ibu. Untuk menindak-lanjuti, silahkan ikuti link dibawah.
                                       <br>
                                       Regards,<br>
                                       Owl-Plantation System.
                                     </body>
                                     </head>
                               </html>";//exit("Error:$body");
                    $kirim=kirimEmail($to,$subject,$body);
                }
		
	}
	else
	{
		$str="select nilai from ".$dbname.".setup_parameterappl where kodeaplikasi='X2' limit 1";
		$res=mysql_query($str);
		while($bar=mysql_fetch_object($res))
		{
		 $to=$bar->nilai;
		}
		
		if($status=='1' and $to!='')
		{
			$str="select a.tanggalperjalanan,a.kodeorg,a.tujuan1,a.tugas1,b.namakaryawan,b.bagian,c.namaorganisasi from ".$dbname.".sdm_pjdinasht a
				  left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
				  left join ".$dbname.".organisasi c on a.kodeorg=c.kodeorganisasi
				  where a.notransaksi='".$notransaksi."'";
			
			$res=mysql_query($str);
	
			while($bar=mysql_fetch_object($res))
			{
				$nama=$bar->namakaryawan;
				$tanggal=tanggalnormal($bar->tanggalperjalanan);
				$tujuan=$bar->tujuan1;
				$bagian=$bar->bagian;
				$lokasitugas=$bar->namaorganisasi;
				$tugas=$bar->tugas1;
			}
			
			$subject="[Notifikasi] Perjalanan Dinas";
			$body="<html>
					 <head>
					 <body>
					   <dd>Dengan Hormat,</dd><br>
					   <br>
					   Telah disetujui perjalanan dinas  A/n: ".$nama." (".$bagian." - ".$lokasitugas.")<br>
					   Tujuan:".$tujuan."<br>
					   Tugas :".$tugas."<br>
					   Tanggal:".$tanggal."
					   <br>
					   <br>
					   <br>
					   Regards,<br>
					   Owl-Plantation System.
					 </body>
					 </head>
				   </html>
				   ";
			$kirim=kirimEmail($to,$subject,$body);#this has return but disobeying;  
		}
	}

}
else
{
	echo addslashes(mysql_error($conn));
}	  
?>
