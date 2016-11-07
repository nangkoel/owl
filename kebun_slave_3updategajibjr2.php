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

$_POST['kdOrgb']==''?$kdOrg=$_GET['kdOrgb']:$kdOrg=$_POST['kdOrgb'];
$_POST['thnId']==''?$thnId=$_GET['thnId']:$thnId=$_POST['thnId'];
$_POST['kdKegiatan']==''?$kdKegiatan=$_GET['kdKegiatan']:$kdKegiatan=$_POST['kdKegiatan'];

$tipe='PNN';

$unitId=$_SESSION['lang']['all'];
$dktlmpk=$_SESSION['lang']['all'];
if($proses=='preview'){
//    if($_POST['tanggal2b']<$_POST['tanggal1b']){
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
//    if($jmlHari>6){
//        exit("error: Tidak Boleh Lebih Dari 7 Hari");
//    }
    if(($_POST['tanggal1b']=='')||($_POST['tanggal2b']=='')){
        exit("error: ".$_SESSION['lang']['tanggal']."1 dan ".$_SESSION['lang']['tanggal']." 2 tidak boleh kosong");
    }
}

$_POST['tanggal1b']==''?$tanggal1=$_GET['tanggal1b']:$tanggal1=$_POST['tanggal1b'];
$_POST['tanggal2b']==''?$tanggal2=$_GET['tanggal2b']:$tanggal2=$_POST['tanggal2b'];

function putertanggal($tanggal)
{
    $qwe=explode('-',$tanggal);
    return $qwe[2].'-'.$qwe[1].'-'.$qwe[0];
} 

$tangsys1=putertanggal($tanggal1);
$tangsys2=putertanggal($tanggal2);

$wheretang=" c.tanggal like '%%' ";
if($tanggal1!=''){
    $wheretang=" c.tanggal = '".$tangsys1."' ";
    if($tanggal2!=''){
        $wheretang=" c.tanggal between '".$tangsys1."' and '".$tangsys2."' ";
    }
}
if($tanggal2!=''){
    $wheretang=" b.tanggal = '".$tangsys2."' ";
    if($tanggal1!=''){
        $wheretang=" c.tanggal between '".$tangsys1."' and '".$tangsys2."' ";
    }
}
$arr2="##kdOrgb##tanggal1b##tanggal2b##kdKegiatan";
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
        $sData="select distinct a.notransaksi,a.nik,a.umr,a.hasilkerja,a.jjg,b.kodeorg,b.kodekegiatan,a.jhk"
             . ",c.tanggal,b.jumlahhk,b.hasilkerja as hsilKg,b.jjg as jjgprest from ".$dbname.".kebun_kehadiran "
             . "a left join ".$dbname.".kebun_prestasi b
               on a.notransaksi=b.notransaksi"
            . " left join ".$dbname.".kebun_aktifitas c
               on a.notransaksi=c.notransaksi 
               where  left(b.kodeorg,4)='".$kdOrg."' and c.jurnal=0  and b.kodekegiatan='".$kdKegiatan."'
               and ".$wheretang."
               ".$whre." order by a.notransaksi asc";
        //echo $sData;      
        $qData=mysql_query($sData) or die(mysql_error($conn));
        $rowdt=mysql_num_rows($qData);
        if(($_SESSION['empl']['bagian']=='IT')||($_SESSION['empl']['kodejabatan']=='98')){
            $tab.="<button class=mybutton onclick=postingDat2(".$rowdt.")  id=revTmbl2>Update Data</button>&nbsp;<button class=mybutton onclick=zExcel(event,'kebun_slave_3updategajibjr2.php','".$arr2."')>Excel</button>";
        }else{
            $tab.="<button class=mybutton onclick=zExcel(event,'kebun_slave_3updategajibjr2.php','".$arr2."')>Excel</button>";
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
        <td ".$bgcoloraja." align=center rowspan=2>".$_SESSION['lang']['jhk']."</td>
        <td ".$bgcoloraja." align=center rowspan=2>".$_SESSION['lang']['jjg']."</td>
        <td ".$bgcoloraja." align=center rowspan=2>".$_SESSION['lang']['hasilkerja']."</td>
            <td colspan=5 align=center>Sebelum</td>
            <td colspan=5 align=center>Sesudah</td></tr>
        <tr><td ".$bgcoloraja." align=center>".$_SESSION['lang']['bjr']."</td>
        <td ".$bgcoloraja." align=center>".$_SESSION['lang']['jjg']."</td>
        <td ".$bgcoloraja." align=center>".$_SESSION['lang']['kg']."</td>
        <td ".$bgcoloraja." align=center>".$_SESSION['lang']['upah']."</td>
        
        <td ".$bgcoloraja." align=center>HK</td>
        <td ".$bgcoloraja." align=center>".$_SESSION['lang']['bjr']."</td>
        <td ".$bgcoloraja." align=center>".$_SESSION['lang']['jjg']."</td>
        <td ".$bgcoloraja." align=center>".$_SESSION['lang']['kg']."</td>
        <td ".$bgcoloraja." align=center>".$_SESSION['lang']['upah']."</td>
        
        <td ".$bgcoloraja." align=center>HK</td>
         </tr>";
        $tab.="</tr></thead><tbody>";
        while($rData=  mysql_fetch_assoc($qData)){
		   #update bjr dari perbaikan spb
           #ambilbjraktual
           //if(substr($rData['kodeorg'],0,6)!=$afdDet){
          /*     $afdDet=substr($rData['kodeorg'],0,6);
               $sBjr="SELECT sum(a.totalkg)/sum(a.jjg) as bjr,tanggal 
                       FROM ".$dbname.".`kebun_spbdt` a left join ".$dbname.".kebun_spbht b on 
                       a.nospb=b.nospb where blok like '".$afdDet."%'
                       and tanggal = '".$rData['tanggal']."' group by tanggal order by tanggal desc limit 1";
                $qBjr=mysql_query($sBjr) or die(mysql_error($conn));
                $rBjr=mysql_fetch_assoc($qBjr);
				if($rBjr['bjr']==''){
					$sBjr="SELECT sum(a.totalkg)/sum(a.jjg) as bjr,tanggal 
                       FROM ".$dbname.".`kebun_spbdt` a left join ".$dbname.".kebun_spbht b on 
                       a.nospb=b.nospb where blok like '".$afdDet."%'
                       and left(tanggal,7) = '".substr($rData['tanggal'],0,7)."' group by left(blok,6) order by tanggal desc limit 1";
					   //echo $sBjr;
					$qBjr=mysql_query($sBjr) or die(mysql_error($conn));
				    $rBjr=mysql_fetch_assoc($qBjr);
					if($rBjr['bjr']==''){
						$sTblBjr="select bjr from ".$dbname.".kebun_5bjr where kodeorg='".$rData['kodeorg']."' and tahunproduksi='".substr($rData['tanggal'],-4,4)."'";
						$qTblBjr=mysql_query($sTblBjr) or die(mysql_error($conn));
						$rTblBjr=mysql_fetch_assoc($qTblBjr);
						$rBjr['bjr']=$rTblBjr['bjr'];
					}
				}
				$hasilKg=0;
                $hasilKg=$rData['jjg']*(number_format($rBjr['bjr'],2));
			*/
			// Update Hasil Kerja
			// Cari umr dari kebun_5psatuan
			$hasilKg=$rData['hasilkerja'];
			$i="select rupiah,insentif,konversi from ".$dbname.".kebun_5psatuan where kodekegiatan='".$rData['kodekegiatan']."' and regional='".$_SESSION['empl']['regional']."' ";
			$n=mysql_query($i) or die (mysql_error($conn));
			$d=mysql_fetch_assoc($n);
			if($d['konversi']==1) {
				#ambil kodeblok dari notransaksi
				$a="select kodeorg from ".$dbname.".kebun_prestasi where notransaksi='".$rData['notransaksi']."'";
				$b=mysql_query($a);
				$c=mysql_fetch_assoc($b);
					$kdBlok=$c['kodeorg'];
					$kdAfd=substr($c['kodeorg'],0,6);//exit("Error:$kdAfd");
				$x="select sum(a.totalkg)/sum(a.jjg) as bjr,tanggal FROM ".$dbname.".kebun_spbdt a left join ".$dbname.".kebun_spbht b on a.nospb=b.nospb 
					where blok like '%".$kdAfd."%' and tanggal='".$rData['tanggal']."' group by tanggal order by tanggal desc limit 1";
				
				$y=mysql_query($x) or die (mysql_error($conn));
				$z=mysql_fetch_assoc($y);
				$bjr=floatval($z['bjr']);
				if($bjr==0 or $bjr==''){
				$x="select sum(a.totalkg)/sum(a.jjg) as bjr,tanggal FROM ".$dbname.".kebun_spbdt a left join ".$dbname.".kebun_spbht b on a.nospb=b.nospb 
					where blok like '%".$kdAfd."%' and left(tanggal,7)='".substr($rData['tanggal'],0,7)."' group by tanggal order by tanggal desc limit 1";
				$qX=mysql_query($x) or die(mysql_error($conn));
				$rX=mysql_fetch_assoc($qX);
					if(($rX['bjr']=='')||intval($rX['bjr'])==0){
						$a="select bjr from ".$dbname.".kebun_5bjr where kodeorg='".$kdBlok."' ";
						//exit("Error:$a");
						$b=mysql_query($a) or die (mysql_error($conn));
						$c=mysql_fetch_assoc($b);
							$bjr=$c['bjr'];	
					}else{
						$bjr=$rX['bjr'];
					}
				}
				//exit("error:".number_format($bjr,2)."___".$jjg);
				$hasilKg=(number_format($bjr,2))*$rData['jjg'];
			}
			
			$rupiah=$d['rupiah'];
			$insentif=$d['insentif'];
			$upah=$rupiah*$hasilKg;
			
			#cari umr dari sdm_5gajipokok untuk perbandingan umr dari kegiatan
			$tahun=substr($rData['tanggal'],0,4);//exit("Error:$tahun");
                        $zUmr=0;
			$qUMR = selectQuery($dbname,'sdm_5gajipokok','sum(jumlah) as nilai',
					"karyawanid='".$rData['nik']."' and tahun='".substr($rData['tanggal'],0,4)."' and idkomponen in (1,31)");
                        //exit("error".$qUMR);
			$Umr = fetchData($qUMR);
			$zUmr=$Umr[0]['nilai']/25;	
			
			if(intval($rupiah)==0){
				$upah = $zUmr*$rData['jhk'];
			}
                        
			#buat perbandingan HK
                        if($upah>=$zUmr) {
                                $jhk=1;
                        } else {
                                @$jhk=$upah/$zUmr;
                        }
			   $nor+=1;
			   @$rData['bjraktual']=$rData['hasilkerja']/$rData['jjg'];
			   $whr="karyawanid='".$rData['nik']."'";
			   $reg=  makeOption($dbname, 'bgt_regional_assignment', 'kodeunit,regional');
			   $regionalDt=$reg[$kdOrg];
			   $whrtr="kodekegiatan='".$kdKegiatan."' and regional='".$regionalDt."'";
			   $optNmKar=makeOption($dbname, 'datakaryawan','karyawanid,namakaryawan',$whr);
			   $optNikKar=makeOption($dbname, 'datakaryawan','karyawanid,nik',$whr);
			   $optTarif=makeOption($dbname, 'kebun_5psatuan','kodekegiatan,rupiah',$whrtr);
			   $tab.="<tr class=rowcontent id=rowDt2_".$nor."><td align=center>".$nor."</td>";
			   $tab.="<td id=notransaksi2_".$nor.">".$rData['notransaksi']."</td>";
			   $tab.="<td id=kodeblok2_".$nor.">".$rData['kodeorg']."</td>";
			   $tab.="<td id='tanggal2_".$nor."'>".$rData['tanggal']."</td>";
			   $tab.="<td><input type=hidden id=karyawanid2_".$nor." value=".$rData['nik']." />".$optNikKar[$rData['nik']]."</td>";
			   $tab.="<td>".$optNmKar[$rData['nik']]."</td>";
			   $tab.="<td>".$optTarif[$rData['kodekegiatan']]."</td>";
			   $tab.="<td  align=right>".number_format($rData['jumlahhk'],2)."</td>";
			   $tab.="<td  align=right>".$rData['jjgprest']."</td>";
			   $tab.="<td  align=right>".number_format($rData['hsilKg'],2)."</td>";
			   $tab.="<td align=right>".number_format($rData['bjraktual'],2)."</td>";
			   $tab.="<td align=right>".$rData['jjg']."</td>";
			   $tab.="<td align=right>".number_format($rData['hasilkerja'],2)."</td>";
			   $tab.="<td align=right>".number_format($rData['umr'],2)."</td>";
			   $tab.="<td align=right>".number_format($rData['jhk'],2)."</td>";
			   

			   //}
				$tab.="<td align=right id=brjAktual_".$nor.">".number_format($rBjr['bjr'],2)."</td>";
				$tab.="<td align=right>".$rData['jjg']."</td>";
				$tab.="<td align=right><input type=hidden id=hasilKg2_".$nor." value=".$hasilKg." />".number_format($hasilKg,2)."</td>";
				
		/* 	#cek gaji pokok
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
			$hk=0;
			$upah=$hasilKg*$optTarif[$rData['kodekegiatan']];
			if($upah>$uphHarian){
				$hk=1;
			}else{
				$hk=$upah/$uphHarian;
			} */
					$tab.="<td align=right><input type=hidden id=updUpah2_".$nor." value=".$upah." />".number_format($upah,2)."</td>";
					$tab.="<td align=right><input type=hidden id=hkData_".$nor." value=".$jhk." />".number_format($jhk,2)."</td>";
					$tab.="</tr>";
					$upah=0;
					
			
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
		case'getKegiatan':
		$optKeg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
			$sKeg="select distinct kodekegiatan from ".$dbname.".kebun_kehadiran_vw where tanggal between '".tanggaldgnbar($_POST['tanggal1b'])."' and '".tanggaldgnbar($_POST['tanggal2b'])."' and unit='".$_POST['kdOrgb']."'";
			//exit("error:".$sKeg);
			$qKeg=mysql_query($sKeg) or die(mysql_error($conn));
			while($rKeg=mysql_fetch_assoc($qKeg)){
				$whrt="kodekegiatan='".$rKeg['kodekegiatan']."'";
				$optNmkeg=makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan',$whrt);
				$optKeg.="<option value='".$rKeg['kodekegiatan']."'>".$optNmkeg[$rKeg['kodekegiatan']]."</option>";
			}
			echo $optKeg;
		break;
        case'updateData':
            foreach($_POST['notrans'] as $rowdt=>$isiRow){
                $scek="select distinct * from ".$dbname.".kebun_aktifitas where notransaksi='".$isiRow."' and jurnal=1";
                $qcek=  mysql_query($scek) or die(mysql_error($conn));
                $rcek=  mysql_num_rows($qcek);
                if($rcek==1){
                    continue;
                }else{
                    if(($_POST['kdorg'][$rowdt]=='')&&($_POST['nik'][$rowdt]=='')){
                        continue;
                    }
                    $jhk=number_format($_POST['updHk'][$rowdt],2);
                    $jhk=  str_replace(",","", $jhk);
                    $hslKg=number_format($_POST['hasilKg2'][$rowdt],2);
                    $hslKg=  str_replace(",","", $hslKg);
                    $updupah=number_format($_POST['updUpah'][$rowdt],2);
                    $updupah=  str_replace(",","", $updupah);
                    $suphadir="update ".$dbname.".kebun_kehadiran set jhk='".$jhk."',hasilkerja='".$hslKg."'"
                            . ",umr='".$updupah."' where notransaksi='".$isiRow."' and nik='".$_POST['nik'][$rowdt]."'";
                    if(!mysql_query($suphadir)){
                        $hk[$isiRow]+=$jhk;
                        $hslkerja[$isiRow]+=$hslKg;
                        $supdate="update ".$dbname.".kebun_prestasi set hasilkerja='".$hslkerja[$isiRow]."',"
                                . "jumlahhk='".$hk[$isiRow]."'"
                                . "where kodeorg='".$_POST['kdorg'][$rowdt]."' and notransaksi='".$isiRow."' and kodeorg='".$_POST['kdorg'][$rowdt]."'";
                        //exit("error:".$supdate);
                        if(!mysql_query($supdate)){
                         exit("error: db bermasalah ".mysql_error($conn)."___".$supdate);   
                        }
                    }else{
                        $hk[$isiRow]+=$jhk;
                        $hslkerja[$isiRow]+=$hslKg;
                        $supdate="update ".$dbname.".kebun_prestasi set hasilkerja='".$hslkerja[$isiRow]."',"
                                . "jumlahhk='".$hk[$isiRow]."'"
                                . "where kodeorg='".$_POST['kdorg'][$rowdt]."' and notransaksi='".$isiRow."' and kodeorg='".$_POST['kdorg'][$rowdt]."'";
                        //exit("error:".$supdate);
                        if(!mysql_query($supdate)){
                         exit("error: db bermasalah ".mysql_error($conn)."___".$supdate);   
                        }
                    }
                }
            }
        break;
        case'excel':
        
        $thisDate=date("YmdHms");
                   //$nop_="Laporan_Pembelian";
                   $nop_="laporanUpdatePerawatan_".$thisDate;
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