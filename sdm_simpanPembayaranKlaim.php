<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');


$notransaksi=$_POST['notransaksi'];
$bayar		=$_POST['bayar'];
$tglbayar	=tanggalsystem($_POST['tglbayar']);
$karIddt=  makeOption($dbname, 'sdm_pengobatanht', 'notransaksi,karyawanid');
$whr="karyawanid='".$karIddt[$notransaksi]."'";
$optTipe=  makeOption($dbname, 'datakaryawan', 'karyawanid,tipekaryawan',$whr);
$optLokasitugas=  makeOption($dbname, 'datakaryawan', 'karyawanid,lokasitugas',$whr);
$hwrpt="kodeorganisasi='".$optLokasitugas[$karIddt[$notransaksi]]."'";
$optPt=makeOption($dbname,'organisasi','kodeorganisasi,induk',$hwrpt);



$ptS=$_SESSION['empl']['kodeorganisasi'];
$unitS=$_SESSION['empl']['lokasitugas'];


#cek datakaryawan posting
$iCek="select induk from ".$dbname.".organisasi where kodeorganisasi='".$unitS."'";
$nCek=mysql_query($iCek) or die (mysql_error($conn));
$dCek=mysql_fetch_assoc($nCek);
    $ptCek=$dCek['induk'];

if($ptS!=$ptCek)
{
    exit("Error:Data karyawan anda seharusnya $ptCek ,  bukan $ptS ");
}

   


$cekkbn=false;	
if($_SESSION['empl']['lokasitugas']!=$optLokasitugas[$karIddt[$notransaksi]]){//jika mutasi dan gudang tujuan ada di unit berbeda
        
            $str="select tutupbuku from ".$dbname.".setup_periodeakuntansi where periode='".substr(tanggaldgnbar($_POST['tglbayar']),0,7)."' and kodeorg='".$optLokasitugas[$karIddt[$notransaksi]]."'";
            $res=mysql_query($str);
            $close=0;
            while($bar=mysql_fetch_object($res))
            {
                $close=$bar->tutupbuku;
            }
            if($close=='1')#khusus penerimaan mutasi dikecualikan boleh di jurnal walau pengirim sudah utup bk
            {
//                exit (" Error: Keuangan unit tujuan sudah tutup buku");
                exit (" Error: Receiver Accounting Period has been closed.");
            }      		
			$cekkbn=true;			
}


//$a=$optPt[$optLokasitugas[$karIddt[$notransaksi]]];
//$b=$_SESSION['org']['kodeorganisasi'];

//exit("Error:$a.__.$b");


#jika pt tidak sama maka pakai akun interco
     $akunspl='';
     if($optPt[$optLokasitugas[$karIddt[$notransaksi]]]!=$_SESSION['org']['kodeorganisasi']){
         #ambil akun interco
         $str="select akunpiutang from ".$dbname.".keu_5caco where kodeorg='".$optLokasitugas[$karIddt[$notransaksi]]."' and jenis='inter'";
            $res=mysql_query($str);
            $akunspl='';
            while($bar=mysql_fetch_object($res))
            {
                $akunspl=$bar->akunpiutang;
            }  
        if($akunspl=='')
//           exit(" Error: Akun intraco  atau interco belum ada untuk unit ".substr($gudangx,0,4)); 
           exit(" Error: Account intraco or interco not available for ".substr($optLokasitugas[$karIddt[$notransaksi]],0,4)); 
     }
     else if($cekkbn!=false){ #jika satu pt beda kebun
          #ambil akun intraco
         $str="select akunpiutang from ".$dbname.".keu_5caco where kodeorg='".$optLokasitugas[$karIddt[$notransaksi]]."' and jenis='intra'";
            $res=mysql_query($str);
            $akunspl='';
            while($bar=mysql_fetch_object($res))
            {
                $akunspl=$bar->akunpiutang;
            } 
         if($akunspl=='')
//            exit(" Error: Akun intraco  atau interco belum ada untuk unit ".substr($gudangx,0,4));    
            exit(" Error: Account intraco / interco not available for ".substr($optLokasitugas[$karIddt[$notransaksi]],0,4));    
     }

$whrtorg="kodeorganisasi='".$optLokasitugas[$karIddt[$notransaksi]]."'";
$optOrgCekTipe=  makeOption($dbname, 'organisasi', 'kodeorganisasi,tipe',$whrtorg);
//exit("error:".$optOrgCekTipe[$optLokasitugas[$karIddt[$notransaksi]]]);
if($optOrgCekTipe[$optLokasitugas[$karIddt[$notransaksi]]]=='HOLDING'){
        $kodeJurnal = 'MED03';
}else{
    if($optTipe[$karIddt[$notransaksi]]==0){
        $kodeJurnal = 'MED01';
    }else{
        $kodeJurnal = 'MED02';
    }
}
$hre="notransaksi='".$notransaksi."'";
$optTipe=makeOption($dbname, 'sdm_pengobatanht', 'notransaksi,klaimoleh',$hre);
$optCekPost=makeOption($dbname, 'sdm_pengobatanht', 'notransaksi,posting',$hre);
$hre2="noreferensi='".$notransaksi."'";
$optCekPost2=makeOption($dbname, 'keu_jurnalht', 'noreferensi,nojurnal',$hre2);
if($optCekPost[$notransaksi]=='1'){
    exit("error: This Transaction Number ".$notransaksi." already posted");
}
if($optCekPost2[$notransaksi]!=''){
    exit("error: This Transaction Number ".$notransaksi." already posted");
}

if(($optTipe[$notransaksi]=='0')||($optTipe[$notransaksi]=='1')){
    

			if($akunspl==''){
					#jurnal disini
					$sJurnal="select distinct noakundebet,noakunkredit from ".$dbname.".keu_5parameterjurnal 
						  where kodeaplikasi='MED' and jurnalid='".$kodeJurnal."'";
					$qJurnal=mysql_query($sJurnal) or die(mysql_error($conn));
					$rJurnal=mysql_fetch_assoc($qJurnal);
					# Get Journal Counter
					$queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
						"kodekelompok='".$kodeJurnal."'  and kodeorg='".$_SESSION['org']['kodeorganisasi']."'");
					//exit("Error:$queryJ");
                                        $tmpKonter = fetchData($queryJ);
					$konter = addZero($tmpKonter[0]['nokounter']+1,3);
					
					# Transform No Jurnal dari No Transaksi
					$tmpNoJurnal = tanggalsystem($_POST['tglbayar']);
					$nojurnal = $tmpNoJurnal."/".substr($notransaksi,0,4)."/".$kodeJurnal."/".$konter;
					
					$sInsJnr="insert into ".$dbname.".keu_jurnalht (nojurnal,kodejurnal,tanggal,tanggalentry,posting,totaldebet,totalkredit,amountkoreksi,noreferensi,autojurnal,matauang,kurs,revisi) 
							  values ('".$nojurnal."','".$kodeJurnal."','".$tglbayar."','".date('Ymd')."','1','".$bayar."','".($bayar*(-1))."','0','".$notransaksi."','1','IDR','1','0')";
					
					if(mysql_query($sInsJnr)){
						$sInsJnr2="insert into ".$dbname.".keu_jurnaldt (nojurnal,tanggal,nourut,noakun,keterangan,jumlah,matauang,kurs,kodeorg,noreferensi,noaruskas,revisi,nik) 
							  values ('".$nojurnal."','".$tglbayar."','1','".$rJurnal['noakundebet']."','Klaim Pengobatan ".$kodeJurnal." Notransaksi ".$notransaksi."','".$bayar."','IDR','1','".substr($notransaksi,0,4)."','".$notransaksi."','','0','".$karIddt[$notransaksi]."')";
						$sInsJnr2.=",('".$nojurnal."','".$tglbayar."','2','".$rJurnal['noakunkredit']."','Klaim Pengobatan ".$kodeJurnal." Notransaksi ".$notransaksi."','".($bayar*(-1))."','IDR','1','".substr($notransaksi,0,4)."','".$notransaksi."','','0','".$karIddt[$notransaksi]."');";
						//exit("error:".$sInsJnr2);
						if(!mysql_query($sInsJnr2)){
							exit("error:".mysql_error($conn)."___".$sInsJnr2);
						}else{
							$supdteCounter="update ".$dbname.".keu_5kelompokjurnal set nokounter='".(intval($tmpKonter[0]['nokounter'])+1)."' 
											where kodekelompok='".$kodeJurnal."' and  kodeorg='".$_SESSION['org']['kodeorganisasi']."'";
							if(!mysql_query($supdteCounter)){
								exit("error:".mysql_error($conn)."___".$supdteCounter);
							}
						}
					}else{
						exit("error:".mysql_error($conn)."___".$sInsJnr);
					}
		    }else{//exit("Error:MASUKAa");
                    
				    #jurnal disini
					$sJurnal="select distinct noakundebet,noakunkredit from ".$dbname.".keu_5parameterjurnal 
						  where kodeaplikasi='MED' and jurnalid='".$kodeJurnal."'";
					$qJurnal=mysql_query($sJurnal) or die(mysql_error($conn));
					$rJurnal=mysql_fetch_assoc($qJurnal);
					# Get Journal Counter
					$queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
						"kodekelompok='".$kodeJurnal."' and kodeorg='".$_SESSION['org']['kodeorganisasi']."'");
					//exit("Error:$queryJ");
                                        $tmpKonter2 = fetchData($queryJ);
					$konter1 = addZero($tmpKonter2[0]['nokounter']+1,3);
					
					$queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
						"kodekelompok='".$kodeJurnal."' and kodeorg='".$optPt[$optLokasitugas[$karIddt[$notransaksi]]]."'");
                                        //exit("Error:$queryJ");
					$tmpKonter3 = fetchData($queryJ);
					$konter3 = addZero($tmpKonter3[0]['nokounter']+1,3);
                                        
					
					# Transform No Jurnal dari No Transaksi $akunspl
					$tmpNoJurnal = tanggalsystem($_POST['tglbayar']);
					$nojurnal1 = $tmpNoJurnal."/".substr($notransaksi,0,4)."/".$kodeJurnal."/".$konter1;
					$nojurnal2 = $tmpNoJurnal."/".$optLokasitugas[$karIddt[$notransaksi]]."/".$kodeJurnal."/".$konter3;
					#caco akun
					if($optPt[$optLokasitugas[$karIddt[$notransaksi]]]!=$_SESSION['org']['kodeorganisasi']){
						$sCaco="select akunpiutang from ".$dbname.".keu_5caco where kodeorg='".$_SESSION['empl']['lokasitugas']."' and jenis='inter'";
					}else{
						$sCaco="select akunpiutang from ".$dbname.".keu_5caco where kodeorg='".$_SESSION['empl']['lokasitugas']."' and jenis='intra'";
					}
					$qCaco=mysql_query($sCaco) or die(mysql_error($conn));
					$rCaco=mysql_fetch_assoc($qCaco);
					
					$sInsJnr="insert into ".$dbname.".keu_jurnalht (nojurnal,kodejurnal,tanggal,tanggalentry,posting,totaldebet,totalkredit,amountkoreksi,noreferensi,autojurnal,matauang,kurs,revisi) 
							  values ('".$nojurnal1."','".$kodeJurnal."','".$tglbayar."','".date('Ymd')."','1','".$bayar."','".($bayar*(-1))."','0','".$notransaksi."','1','IDR','1','0')";
					$sInsJnr.=",('".$nojurnal2."','".$kodeJurnal."','".$tglbayar."','".date('Ymd')."','1','".$bayar."','".($bayar*(-1))."','0','".$notransaksi."','1','IDR','1','0');";
					
					if(mysql_query($sInsJnr)){
						$sInsJnr2="insert into ".$dbname.".keu_jurnaldt (nojurnal,tanggal,nourut,noakun,keterangan,jumlah,matauang,kurs,kodeorg,noreferensi,noaruskas,revisi,nik) 
							  values ('".$nojurnal1."','".$tglbayar."','1','".$akunspl."','Klaim Pengobatan ".$kodeJurnal." Notransaksi ".$notransaksi."','".$bayar."','IDR','1','".substr($notransaksi,0,4)."','".$notransaksi."','','0','".$karIddt[$notransaksi]."')";
						$sInsJnr2.=",('".$nojurnal1."','".$tglbayar."','2','".$rJurnal['noakunkredit']."','Klaim Pengobatan ".$kodeJurnal." Notransaksi ".$notransaksi."','".($bayar*(-1))."','IDR','1','".substr($notransaksi,0,4)."','".$notransaksi."','','0','".$karIddt[$notransaksi]."')";
						$sInsJnr2.=",('".$nojurnal2."','".$tglbayar."','1','".$rJurnal['noakundebet']."','Klaim Pengobatan ".$kodeJurnal." Notransaksi ".$notransaksi."','".$bayar."','IDR','1','".substr($notransaksi,0,4)."','".$notransaksi."','','0','".$karIddt[$notransaksi]."'),
						            ('".$nojurnal2."','".$tglbayar."','2','".$rCaco['akunpiutang']."','Klaim Pengobatan ".$kodeJurnal." Notransaksi ".$notransaksi."','".($bayar*(-1))."','IDR','1','".substr($notransaksi,0,4)."','".$notransaksi."','','0','".$karIddt[$notransaksi]."');";
						//echo $sInsJnr2;
						//exit("error:");
						if(!mysql_query($sInsJnr2)){
							exit("error:".mysql_error($conn)."___".$sInsJnr2);
						}else{
							$supdteCounter="update ".$dbname.".keu_5kelompokjurnal set nokounter='".(intval($tmpKonter2[0]['nokounter'])+1)."' 
											where kodekelompok='".$kodeJurnal."' and kodeorg='".$_SESSION['org']['kodeorganisasi']."'";
                                                        //exit("Error:$supdteCounter");
							if(!mysql_query($supdteCounter)){
								exit("error:".mysql_error($conn)."___".$supdteCounter);
							}
                                                        
                                                        $supdteCounter2="update ".$dbname.".keu_5kelompokjurnal set nokounter='".(intval($tmpKonter3[0]['nokounter'])+1)."' 
											where kodekelompok='".$kodeJurnal."' and  kodeorg='".$optPt[$optLokasitugas[$karIddt[$notransaksi]]]."'";
                                                        //exit("Error:$supdteCounter");
							if(!mysql_query($supdteCounter2)){
								exit("error:".mysql_error($conn)."___".$supdteCounter2);
							}
                                                        
                                                        
						}
					}else{
						exit("error:".mysql_error($conn)."___".$sInsJnr);
					}
			}
			
}
//exit("error:masuk wwww");
$str="update ".$dbname.".sdm_pengobatanht set jlhbayar=".$bayar.",
      tanggalbayar=".$tglbayar.",
	  posting=1
	  where notransaksi='".$notransaksi."'";
if(mysql_query($str))
{}
else
{
	echo " Gagal ".addslashes(mysql_error($conn));
}	  
?>
