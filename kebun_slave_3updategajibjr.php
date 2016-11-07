<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

if(isset($_POST['proses']))
{
	$proses=$_POST['proses'];
}
else
{
	$proses=$_GET['proses'];
}


$optNmOrg=makeOption($dbname, 'organisasi','kodeorganisasi,namaorganisasi');

$_POST['kdOrg']==''?$kdOrg=$_GET['kdOrg']:$kdOrg=$_POST['kdOrg'];
$_POST['thnId']==''?$thnId=$_GET['thnId']:$thnId=$_POST['thnId'];
$_POST['kdProj']==''?$kdProj=$_GET['kdProj']:$kdProj=$_POST['kdProj'];
$tipe='PNN';

$unitId=$_SESSION['lang']['all'];
$dktlmpk=$_SESSION['lang']['all'];
if($proses=='preview'){
//    if($_POST['tanggal2']<$_POST['tanggal1']){
//        exit("error: Tolong gunakan urutan tanggal yang benar");
//    }
    $tglPP=explode("-",$_POST['tanggal1']);
    $date1 = $tglPP[0];
    $month1 = $tglPP[1];
    $year1 = $tglPP[2];
    //$tgl1 = $bar->tanggal;
    $tgl2 = $_POST['tanggal2']; 
    $pecah2 = explode("-", $tgl2);
    $date2 = $pecah2[0];
    $month2 = $pecah2[1];
    $year2 =  $pecah2[2];
    $jd1 = GregorianToJD($month1, $date1, $year1);
    $jd2 = GregorianToJD($month2, $date2, $year2);
    $jmlHari=$jd2-$jd1;
   /* if($jmlHari>31){
        exit("error: Tidak Boleh Lebih Dari 1 bulan");
    }*/
    if(($_POST['tanggal1']=='')||($_POST['tanggal2']=='')){
        exit("error: ".$_SESSION['lang']['tanggal']."1 dan ".$_SESSION['lang']['tanggal']." 2 tidak boleh kosong");
    }
	if($month1!=$month2){
        exit("error: Harus dalam periode yang sama");
    }
}

$_POST['tanggal1']==''?$tanggal1=$_GET['tanggal1']:$tanggal1=$_POST['tanggal1'];
$_POST['tanggal2']==''?$tanggal2=$_GET['tanggal2']:$tanggal2=$_POST['tanggal2'];

function putertanggal($tanggal)
{
    $qwe=explode('-',$tanggal);
    return $qwe[2].'-'.$qwe[1].'-'.$qwe[0];
} 

$tangsys1=putertanggal($tanggal1);
$tangsys2=putertanggal($tanggal2);

$wheretang=" b.tanggal like '%%' ";
if($tanggal1!=''){
    $wheretang=" b.tanggal = '".$tangsys1."' ";
    if($tanggal2!=''){
        $wheretang=" b.tanggal between '".$tangsys1."' and '".$tangsys2."' ";
    }
}
if($tanggal2!=''){
    $wheretang=" b.tanggal = '".$tangsys2."' ";
    if($tanggal1!=''){
        $wheretang=" b.tanggal between '".$tangsys1."' and '".$tangsys2."' ";
    }
}
$arr="##kdOrg##tanggal1##tanggal2";
if($proses=='preview'||$proses=='excel')
{


$brdr=0;
$bgcoloraja='';
if($proses=='excel'){
    $brdr=1;
    $bgcoloraja='green';
}
 if($_POST['tipeTrk']!='')
        {
            $whre=" and tipetransaksi='". $_POST['tipeTrk']."'";
        }
        $sData="select distinct a.notransaksi,a.nik,a.tarif,a.hasilkerja,a.hasilkerjakg,a.bjraktual,a.upahkerja,a.upahpremi,a.kodeorg,a.rupiahpenalty"
             . ",a.penalti1,a.penalti2,a.penalti3,a.penalti4,a.penalti5,a.penalti6,a.penalti7,b.tanggal from ".$dbname.".kebun_prestasi a left join ".$dbname.".kebun_aktifitas b
               on a.notransaksi=b.notransaksi
               where  b.kodeorg='".$kdOrg."' and b.jurnal=0 and b.notransaksi like '%".$tipe."%'
               and ".$wheretang."
               ".$whre." order by tanggal,kodeorg asc";
 //echo $sData;
        $qData=mysql_query($sData) or die(mysql_error($conn));
        $rowdt=mysql_num_rows($qData);
        if(($_SESSION['empl']['bagian']=='IT')||($_SESSION['empl']['kodejabatan']=='98')){
            $tab.="<button class=mybutton onclick=postingDat(".$rowdt.")  id=revTmbl>Update Data</button>&nbsp;<button class=mybutton onclick=zExcel(event,'kebun_slave_3updategajibjr.php','".$arr."')>Excel</button>";
        }else{
            $tab.="<button class=mybutton onclick=zExcel(event,'kebun_slave_3updategajibjr.php','".$arr."')>Excel</button>";
        }
	$tab.="<table cellspacing=1 border=".$brdr." class=sortable>
	<thead class=rowheader>
	<tr>
        <td ".$bgcoloraja." align=center rowspan=2>No.</td>
        <td ".$bgcoloraja." align=center rowspan=2>".$_SESSION['lang']['notransaksi']."</td>
        <td ".$bgcoloraja." align=center rowspan=2>".$_SESSION['lang']['kodeblok']."</td>
        <td ".$bgcoloraja." align=center rowspan=2>".$_SESSION['lang']['tanggal']."</td>
        <td ".$bgcoloraja." align=center rowspan=2>".$_SESSION['lang']['nik']."</td>
        <td ".$bgcoloraja." align=center rowspan=2>".$_SESSION['lang']['namakaryawan']."</td>
        <td ".$bgcoloraja." align=center rowspan=2>".$_SESSION['lang']['tarif']."</td>
            <td colspan=6 align=center>Sebelum</td>
            <td colspan=6 align=center>Sesudah</td></tr>
        <tr><td ".$bgcoloraja." align=center>".$_SESSION['lang']['bjr']."</td>
        <td ".$bgcoloraja." align=center>".$_SESSION['lang']['jjg']."</td>
        <td ".$bgcoloraja." align=center>".$_SESSION['lang']['kg']."</td>
        <td ".$bgcoloraja." align=center>".$_SESSION['lang']['upah']."</td>
        <td ".$bgcoloraja." align=center>".$_SESSION['lang']['premi']."</td>
        <td ".$bgcoloraja." align=center>".$_SESSION['lang']['rupiahpenalty']."</td>
            <td ".$bgcoloraja." align=center>".$_SESSION['lang']['bjr']."</td>
        <td ".$bgcoloraja." align=center>".$_SESSION['lang']['jjg']."</td>
        <td ".$bgcoloraja." align=center>".$_SESSION['lang']['kg']."</td>
        <td ".$bgcoloraja." align=center>".$_SESSION['lang']['upah']."</td>
        <td ".$bgcoloraja." align=center>".$_SESSION['lang']['premi']."</td>
        <td ".$bgcoloraja." align=center>".$_SESSION['lang']['rupiahpenalty']."</td>
            
        </tr>
            

        <!--<td ".$bgcoloraja." align=center>".$_SESSION['lang']['penalti1']."</td>
        <td ".$bgcoloraja." align=center>".$_SESSION['lang']['penalti2']."</td>
        <td ".$bgcoloraja." align=center>".$_SESSION['lang']['penalti3']."</td>
        <td ".$bgcoloraja." align=center>".$_SESSION['lang']['penalti4']."</td>
        <td ".$bgcoloraja." align=center>".$_SESSION['lang']['penalti5']."</td>
        <td ".$bgcoloraja." align=center>".$_SESSION['lang']['penalti6']."</td>
        <td ".$bgcoloraja." align=center>".$_SESSION['lang']['penalti7']."</td>-->
        
        </tr>";
        $tab.="</tr></thead><tbody>";
		while($rData=  mysql_fetch_assoc($qData)){
			#update bjr dari perbaikan spb
			#ambilbjraktual
			//if(substr($rData['kodeorg'],0,6)!=$afdDet){
			$afdDet=substr($rData['kodeorg'],0,6);
			$sBjr="SELECT sum(a.totalkg)/sum(a.jjg) as bjr,tanggal 
				   FROM ".$dbname.".`kebun_spbdt` a left join ".$dbname.".kebun_spbht b on 
				   a.nospb=b.nospb where blok like '".$afdDet."%'
				   and tanggal = '".$rData['tanggal']."' group by tanggal order by tanggal desc limit 1";
			$qBjr=mysql_query($sBjr) or die(mysql_error($conn));
			$rBjr=mysql_fetch_assoc($qBjr);
			$reg=  makeOption($dbname, 'bgt_regional_assignment', 'kodeunit,regional');
			$regionalDt=$reg[substr($afdDet,0,4)];
			//}
			if($rBjr['bjr']==0)
			$rBjr['bjr']=$rData['bjraktual'];
			else
			$rBjr['bjr']=$rBjr['bjr'];	
			$hasilKg=0;
			$hasilKg=$rData['hasilkerja']*(number_format($rBjr['bjr'],2));
		   //if(round($hasilKg-$rData['hasilkerjakg'])!=0){
		   $nor+=1;
           $whr="karyawanid='".$rData['nik']."'";
           $optNmKar=makeOption($dbname, 'datakaryawan','karyawanid,namakaryawan',$whr);
           $optNikKar=makeOption($dbname, 'datakaryawan','karyawanid,nik',$whr);
           $tab.="<tr class=rowcontent id=rowDt_".$nor."><td align=center>".$nor."</td>";
           $tab.="<td id=notransaksi_".$nor.">".$rData['notransaksi']."</td>";
           $tab.="<td id=kodeblok_".$nor.">".$rData['kodeorg']."</td>";
           $tab.="<td id='tanggal_".$nor."' nowrap>".tanggalnormal($rData['tanggal'])."</td>";
           $tab.="<td><input type=hidden id=karyawanid_".$nor." value=".$rData['nik']." />".$optNikKar[$rData['nik']]."</td>";
           $tab.="<td>".$optNmKar[$rData['nik']]."</td>";
           $tab.="<td>".$rData['tarif']."</td>";
           $tab.="<td align=right>".$rData['bjraktual']."</td>";
           $tab.="<td align=right>".$rData['hasilkerja']."</td>";
           $tab.="<td align=right>".$rData['hasilkerjakg']."</td>";
           $tab.="<td align=right>".number_format($rData['upahkerja'],2)."</td>";
           $tab.="<td align=right>".number_format($rData['upahpremi'],0)."</td>";
           $tab.="<td align=right>".number_format($rData['rupiahpenalty'],2)."</td>";
           
            $tab.="<td align=right id=brjAktual_".$nor.">".number_format($rBjr['bjr'],2)."</td>";
            $tab.="<td align=right>".$rData['hasilkerja']."</td>";
            
            $tab.="<td align=right id=hasilKg_".$nor.">".number_format($hasilKg,2)."</td>";
            
	#cek periode
        $qPer = selectQuery($dbname,'sdm_5periodegaji','periode',
	    "tanggalmulai<'".$rData['tanggal']."' and tanggalsampai>'".$rData['tanggal']."' and kodeorg='".substr($rData['kodeorg'],0,4)."'");
        $Per=fetchData($qPer);
	#cek nilai pemanen
        $qGrade = selectQuery($dbname,'kebun_5nilaipemanen','nilai',
	    "karyawanid='".$rData['nik']."' and periodegaji='".$Per[0]['periode']."'");
        $Grade=fetchData($qGrade);
        $grade=($Grade[0]['nilai']!='')?$Grade[0]['nilai']:2;
        #cek gaji pokok
        $qUMR = selectQuery($dbname,'sdm_5gajipokok','sum(jumlah) as nilai',
	    "karyawanid=".$rData['nik']." and tahun=".substr($rData['tanggal'],0,4)." and idkomponen in (1,31)");
        //exit("error:".$qUMR);
	$Umr = fetchData($qUMR);
        $uphHarian=$Umr[0]['nilai']/25;
        if($uphHarian==0){
            $uphHarian=0;
            $insentif=0;
            //continue;
        }
            
        $qwe=date('D', strtotime($rData['tanggal']));
        #cek tanggal apa ada di hari libur
        $dhr="regional='".$regionalDt."' and tanggal='".$rData['tanggal']."'";
        $optHariLbr=makeOption($dbname, 'sdm_5harilibur', 'regional,tanggal',$dhr);
        
        #query kebun_5basispanen buat ambil rplebih,basisjjg,status denda,insentif topografi
        
        $regData=$regionalDt;
        $param['blok']=$rData['kodeorg'];
        $dtr="kodeorg='".$param['blok']."'";
        unset($optTopo);
        $optTopo=makeOption($dbname, 'setup_blok', 'kodeorg,topografi',$dtr);
        if($regData=='SULAWESI'){
            $afd=substr($param['blok'],0,6);
            $dmn="kodeorg='".$afd."' and grade=".$grade;
            $optCek=makeOption($dbname, 'kebun_5basispanen', 'kodeorg,jenis',$dmn);
            if(isset($optCek[$afd]) and $optCek[$afd]!=''){
                $regData=$afd;
            }
        }
        $dmn="kodeorg='".$regData."' and jenis='".$rData['tarif']."' and grade=".$grade;
                            
        $optRp=makeOption($dbname, 'kebun_5basispanen', 'jenis,rplebih',$dmn);
        $optBasis=makeOption($dbname, 'kebun_5basispanen', 'jenis,basisjjg',$dmn);
        $optDenda=makeOption($dbname, 'kebun_5basispanen', 'jenis,dendabasis',$dmn);
        //exit("error".$optTopo[$param['blok']]);
        unset($optIns);
        if($optTopo[$param['blok']]=='B1'){
              $optIns=makeOption($dbname, 'kebun_5basispanen', 'jenis,rptopografi',$dmn);
        }
        #query kebun_5basispanen abis disini
        
        #membentuk array bjr klo regional kalimantan
                $lstert=0;
                $sTarif="select distinct * from ".$dbname.".kebun_5basispanen where 
                         kodeorg='".$regionalDt."' and jenis='satuan' order by bjr desc";
                $qTarif=mysql_query($sTarif) or die(mysql_error($conn));
                while($rTarif=  mysql_fetch_assoc($qTarif)){
                    $rpLbh[$rTarif['bjr']]=$rTarif['rplebih'];
                    $basisPanen[$rTarif['bjr']]=$rTarif['basisjjg'];
                    $lstBjr[]=$rTarif['bjr'];
                    $lstBjr2[$lstert]=$rTarif['bjr'];
                    $lstert++;
                }
                $param['tarif']=$rData['tarif'];
                $param['jmlhJjg']=$rData['hasilkerja'];
                $param['bjraktual']=$rBjr['bjr'];
            #membentuk abis disini
            #mulai cek panen di hari libur
                if(($qwe=='Sun')||(isset($optHariLbr[$regionalDt]) and $optHariLbr[$regionalDt]!='')){
                    $basis=0;
                    if($regionalDt=='SULAWESI'){        
                        switch($param['tarif']){
                            case'harian':
                                $basis=$optBasis[$param['tarif']];
                                 if($basis==0){
                                     $upah=$uphHarian;
                                     $insentif=$optIns[$param['tarif']];
                                 }
                                 if($basis!=0){
                                     if($optDenda[$param['tarif']]=='1'){
                                          if($param['jmlhJjg']<$basis){
                                            $upah=$param['jmlhJjg']/$basis*$uphHarian;
                                          }else if($param['jmlhJjg']>$basis){
                                              if($optRp[$param['tarif']]!=0){
                                                $upah=$uphHarian+($optRp[$param['tarif']]*($param['jmlhJjg']-$basis));
                                              }else{
                                                  $upah=$uphHarian;  
                                              }
                                              //exit("error: __".$optRp[$param['tarif']]."___".$param['jmlhJjg']."___".$basis);
                                          }else{
                                            $upah=$uphHarian;  
                                          }
                                     }else{
                                         $upah=$optRp[$param['tarif']]*$hasilKg;
                                     }
                                     $insentif=$optIns[$param['tarif']];
                                 }
        //                        $upah=$optRp[$param['tarif']]*$hasilKg;
        //                        $insentif=0;
                            break;
                            case'satuan':
                                $upah=$optRp[$param['tarif']]*$hasilKg;
                                $insentif=$optIns[$param['tarif']];
                                //exit("error:".$insentif);
                            break;
                        }
                    }else if($regionalDt=='KALIMANTAN'){

                      switch($param['tarif']){
                    case'harian':
                       $basis=$optBasis[$param['tarif']];
						if($optDenda[$param['tarif']]=='1'){
							if($param['jmlhJjg']<$basis){
							$upah=($param['jmlhJjg']/$basis)*$uphHarian;
						}else{
							$upah=$uphHarian;
						}
						}else{
						$upah=$uphHarian;
						}

						$insentif=0;
                        $basis=$optBasis[$param['tarif']];
                    break;
                    case'satuan':
                        $MaxRow=count($lstBjr);
                        foreach($lstBjr as $lstRow=>$dtIsiBjr){
                            if($lstRow==0){
                                if(intval($param['bjraktual'])>=$dtIsiBjr){
                                    $hsl=$rpLbh[$dtIsiBjr]*$hasilKg;
                                    $dtbjr=$dtIsiBjr;
                                    //exit("error:__".$rpLbh[$dtIsiBjr]."__".$hasilKg."__".$dtIsiBjr."___masuk sini");
                                    break;
                                }
                            }else{
                                if($lstRow!=$MaxRow){
                                    $leapdt=$lstRow-1;
									$leapdt2=$lstRow+1;
									//exit("error:__".$lstBjr2[$leapdt]."__".$hasilKg."__".$dtIsiBjr."___masuk sini b");
                                    if((intval($param['bjraktual'])>=$dtIsiBjr)&&(intval($param['bjraktual'])<$lstBjr2[$leapdt])){
                                        $hsl=$rpLbh[$dtIsiBjr]*$hasilKg;
                                        $dtbjr=$dtIsiBjr;
                                        break;
                                    }
                                }else{
                                    $dmin=$dtIsiBjr-1;
                                    $dtbjr=$dtIsiBjr;
                                    if($param['bjraktual']>=$dmin){
                                        $hsl=$rpLbh[$dtIsiBjr]*$hasilKg;
                                        break;
                                    }else{
                                        $hsl=$param['jmlhJjg']/$basisPanen[$dtIsiBjr]*$uphHarian;
                                    }
                                }
                            }
                        }
						if($param['bjraktual']<4){
							$dmn="kodeorg='".$regData."' and jenis='harian' and bjr='0'";
						}else{
							$dmn="kodeorg='".$regData."' and jenis='".$param['tarif']."' and bjr='".$dtbjr."'";
						}
                        
                        $optRp=makeOption($dbname, 'kebun_5basispanen', 'jenis,rplebih',$dmn);
                        $optBasis=makeOption($dbname, 'kebun_5basispanen', 'jenis,basisjjg',$dmn);
                        $optDenda=makeOption($dbname, 'kebun_5basispanen', 'jenis,dendabasis',$dmn);
                        $upah=$hsl;
                        $insentif=0;
                        $basis=$optBasis[$param['tarif']];
                    break;
                }
            }
                }else{#abis cek panen di hari libur
                      #mulai cek panen hari normal
                    switch($regionalDt){
                        case'SULAWESI':
                            switch($param['tarif']){
                                case'harian':
                                 $basis=$optBasis[$param['tarif']];
                                 if($basis==0){
                                     $upah=$uphHarian;
                                     $insentif=$optIns[$param['tarif']];
                                 }
                                 if($basis!=0){
                                     if($optDenda[$param['tarif']]=='1'){
                                          if($param['jmlhJjg']<$basis){
                                            $upah=$param['jmlhJjg']/$basis*$uphHarian;
                                          }else if($param['jmlhJjg']>$basis){
                                              if($optRp[$param['tarif']]!=0){
                                                $upah=$uphHarian+($optRp[$param['tarif']]*($param['jmlhJjg']-$basis));
                                              }else{
                                                  $upah=$uphHarian;  
                                              }
                                              //exit("error: __".$optRp[$param['tarif']]."___".$param['jmlhJjg']."___".$basis);
                                          }else{
                                            $upah=$uphHarian;  
                                          }
                                     }else{
                                         $upah=$optRp[$param['tarif']]*$hasilKg;
                                     }
                                     $insentif=$optIns[$param['tarif']];
                                 }
                                break;
                                case'satuan':
                                    $basis=$optBasis[$param['tarif']];
                                    //exit("error:".$basis."__".$param['tarif']."__".$regData);
                                    $upah=$hasilKg*$optRp[$param['tarif']];
                                    $insentif=$optIns[$param['tarif']];
                                break;
                            }
                        break;
                        case'KALIMANTAN':
                            switch($param['tarif']){
                                case'harian':
                                    $basis=$optBasis[$param['tarif']];
                                        if($optDenda[$param['tarif']]=='1'){
                                            if($param['jmlhJjg']<$basis){
                                            $upah=($param['jmlhJjg']/$basis)*$uphHarian;
                                        }else{
                                            $upah=$uphHarian;
                                        }
                                    }else{
                                        $upah=$uphHarian;
                                    }

                                    $insentif=0;
                                break;
                                case'satuan':
                                    $insentif=0;
                                    $MaxRow=count($lstBjr);
                                    foreach($lstBjr as $lstRow=>$dtIsiBjr){
                                        if($lstRow==0){
                                            if($param['bjraktual']>$dtIsiBjr){
                                                $upah=$rpLbh[$dtIsiBjr]*$hasilKg;
                                                $dtbjr=$dtIsiBjr;
                                                break;
                                            }
                                        }else{
                                            if($lstRow!=$MaxRow){
                                                $leapdt=$lstRow+1;
                                                if(($param['bjraktual']==$dtIsiBjr)||($param['bjraktual']>$lstBjr2[$leapdt])){
                                                    $upah=$rpLbh[$dtIsiBjr]*$hasilKg;
                                                    $dtbjr=$dtIsiBjr;
                                                    break;
                                                }
                                            }else{
                                                $dmin=$dtIsiBjr-1;
                                                $dtbjr=$dtIsiBjr;
                                                if($param['bjraktual']>=$dmin){
                                                    $upah=$rpLbh[$dtIsiBjr]*$hasilKg;
                                                    break;
                                                }else{
                                                    $upah=$param['jmlhJjg']/$basisPanen[$dtIsiBjr]*$uphHarian;
                                                }
                                            }
                                        }
                                    }
                                    $dmn="kodeorg='".$regionalDt."' and jenis='".$param['tarif']."' and bjr='".$dtbjr."'";
                                    $optBasis=makeOption($dbname, 'kebun_5basispanen', 'jenis,basisjjg',$dmn);
                                    $basis=$optBasis[$param['tarif']];
                                    $insentif=0;
                                    if($optDenda[$param['tarif']]=='1'){
                                            if($param['jmlhJjg']<$basis){
                                            $upah=($param['jmlhJjg']/$basis)*$uphHarian;
                                        }
                                    }


                                break;
                            }

                        break;
                    }
                }
                #upah selesai dsini
                #denda baru mulai
                if($regionalDt=='SULAWESI'){
                    $dtbjr=0;
                }else{
                    $lstert=0;
                    $sTarif="select distinct * from ".$dbname.".kebun_5basispanen where 
                             kodeorg='".$regionalDt."' and jenis='".$param['tarif']."' order by bjr desc";
                    $qTarif=mysql_query($sTarif) or die(mysql_error($conn));
                    while($rTarif=  mysql_fetch_assoc($qTarif)){
                        $rpLbh[$rTarif['bjr']]=$rTarif['rplebih'];
                        $basisPanen[$rTarif['bjr']]=$rTarif['basisjjg'];
                        $lstBjr[]=$rTarif['bjr'];
                        $lstBjr2[$lstert]=$rTarif['bjr'];
                        $lstert++;
                    }
                    $MaxRow=count($lstBjr);
                    foreach($lstBjr as $lstRow=>$dtIsiBjr){
                        if($lstRow==0){
                            if($param['bjraktual']>$dtIsiBjr){
                                $dtbjr=$dtIsiBjr;
                                break;
                            }
                        }else{
                            if($lstRow!=$MaxRow){
                                $leapdt=$lstRow+1;
                                if(($param['bjraktual']==$dtIsiBjr)||($param['bjraktual']>$lstBjr2[$leapdt])){
                                    $dtbjr=$dtIsiBjr;
                                    break;
                                }
                            }else{
                                $dmin=$dtIsiBjr-1;

                                if($param['bjraktual']>=$dmin){
                                   $dtbjr=$dtIsiBjr;
                                    break;
                                }else{
                                    $dtbjr=0;
                                }
                            }
                        }
                    }
                }
            $regData=$regionalDt;
            if($regionalDt=='SULAWESI'){
                $afd=substr($param['blok'],0,6);
                $dmn="kodeorg='".$afd."'";
                $optCek=makeOption($dbname, 'kebun_5basispanen', 'kodeorg,jenis',$dmn);
                if($optCek[$afd]!=''){
                    $regData=$afd;
                }
            }
            $dmn="kodeorg='".$regData."' and jenis='".$param['tarif']."' and bjr='".$dtbjr."'";
            if($_SESSION['empl']['regional']=='SULAWESI'){
                $dmn="kodeorg='".$regData."' and jenis='".$param['tarif']."'";
            }
            if($regData=='H12E02'){
                $dmn="kodeorg='".$regionalDt."' and jenis='satuan'";
            }
            unset($optRp);
            unset($optDenda);
            $denda=0;
            $optRp=makeOption($dbname, 'kebun_5basispanen', 'jenis,rplebih',$dmn);
            $optDenda=makeOption($dbname, 'kebun_5denda', 'kode,jumlah');
            for($der=1;$der<8;$der++){
                if($der==1){
                    $det="BM";#buah mentah#
                    $dend=$rData['penalti'.$der]*$optDenda[$det]*$param['bjraktual']*$optRp[$param['tarif']];
                } else if($der==3){
                    $det="TD";#tidak dipanen#
                    $dend=$rData['penalti'.$der]*$optDenda[$det]*$param['bjraktual']*$optRp[$param['tarif']];
                    //exit("error:".$_POST['isiDt'][$der]." ".$optDenda[$det]."  ".$optRp[$param['tarif']]."  ".$param['bjraktual']);
                } else if($der==5){
                    $det="BT";#brondolan tidak di kutip#
                    $dend=$rData['penalti'.$der]/$optDenda[$det]*$param['bjraktual']*$optRp[$param['tarif']];
                    //exit("error:".$_POST['isiDt'][$der]." ".$optDenda[$det]."  ".$optRp[$param['tarif']]."  ".$param['bjraktual']);
                }else{
                    $det="TP";#tangkai panjang,pelepah tidak disusun,tandan menggantung#
                    $dend=$rData['penalti'.$der]*$optDenda[$det]*$param['bjraktual']*$optRp[$param['tarif']];
                }
                $denda+=$dend;
            }
                $tab.="<td align=right  id=updUpah_".$nor.">".number_format($upah,2)."</td>";
                $tab.="<td align=right  id=updInsentif_".$nor.">".number_format($insentif,0)."</td>";
                $tab.="<td align=right  id=updDenda_".$nor.">".number_format($denda,2)."</td>";
                $tab.="</tr>";
			//	}
        }
        $tab.="</tbody></table>";
}
        
switch($proses)
{ 
	case'preview':
	echo $tab;
	break;
        case'getPeriode': 
            $optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
            $sPeriodeAkut="select distinct periode from ".$dbname.".setup_periodeakuntansi 
                         where kodeorg='".$_POST['kdOrg']."' and tutupbuku=0";
            $qPeriodeCari=mysql_query($sPeriodeAkut) or die(mysql_error());
            while($rPeriodeCari=mysql_fetch_assoc($qPeriodeCari))
            {
               $optPeriode.="<option value='".$rPeriodeCari['periode']."'>".$rPeriodeCari['periode']."</option>";
            }
            echo $optPeriode;
        break;
        case'updateData':
            $scek="select distinct * from ".$dbname.".kebun_aktifitas where notransaksi='".$_POST['notransaksi']."' and jurnal=1";
            $qcek=  mysql_query($scek) or die(mysql_error($conn));
            $rcek=  mysql_num_rows($qcek);
            if($rcek==1){
                echo "1";
                break;
                break;
            }else{
                if(($_POST['kodeorg']=='')&&($_POST['notransaksi']=='')&&($_POST['nik']=='')){
                    echo "1";
                    break;
                }
                if((intval($_POST['upah'])=='0')&&($_POST['nik']=='')&&(intval($_POST['hasilKg'])=='0')&&(intval($_POST['brjAktual'])=='0')){
                    echo "1";
                    break;
                }
                $_POST['upah']=  str_replace(",","",$_POST['upah']);
                $_POST['hasilKg']=  str_replace(",","",$_POST['hasilKg']);
                $_POST['insentif']=  str_replace(",","",$_POST['insentif']);
                $_POST['denda']=  str_replace(",","",$_POST['denda']);
                $supdate="update ".$dbname.".kebun_prestasi set upahkerja='".$_POST['upah']."',"
                        . "hasilkerjakg='".$_POST['hasilKg']."',upahpremi='".$_POST['insentif']."',"
                        . "rupiahpenalty='".$_POST['denda']."',bjraktual='".$_POST['brjAktual']."' "
                        . "where kodeorg='".$_POST['kodeorg']."' and notransaksi='".$_POST['notransaksi']."' and nik='".$_POST['nik']."'";
                //exit("error:".$supdate);
                if(!mysql_query($supdate)){
                 exit("error: db bermasalah ".mysql_error($conn)."___".$supdate);   
                }
            }
        break;
        case'excel':
        
        $thisDate=date("YmdHms");
                   //$nop_="Laporan_Pembelian";
                   $nop_="laporanUpdateBjr_".$thisDate;
                   $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
                    gzwrite($gztralala, $tab);
                    gzclose($gztralala);
                    echo "<script language=javascript1.2>
                       window.location='tempExcel/".$nop_.".xls.gz';
                       </script>";
        break;
	default:
	break;
}
?>