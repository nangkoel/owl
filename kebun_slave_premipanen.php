<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/fpdf.php');

if(($_GET['proses']=='excel')||($_GET['proses']=='pdf')){
    $param=$_GET;
}else{
    $param=$_POST;
}

$optCek=makeOption($dbname, 'kebun_5premipanen', 'kodeorg,premirajin');
$optNmOrg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNmKary=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
switch($param['proses']){
    case'preview':
//        $periodeAKtif=$_SESSION['org']['period']['tahun']."-".$_SESSION['org']['period']['bulan'];
//        if($param['periode']!=$periodeAKtif){
//            exit("error: Periode diffrent with active periode");
//        }
        $blnthn=explode("-",$param['periode']);
        $jumHari = cal_days_in_month(CAL_GREGORIAN, $blnthn[1], $blnthn[0]);
        $tgl1=$param['periode']."-01";
        $tgl2=$param['periode']."-".$jumHari;
        $sTgl="select distinct tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji 
                              where kodeorg='".$param['kodeorg']."' and periode='".$param['periode']."'";
        $rTgl=fetchData($sTgl);
        if ($rTgl[0]['tanggalmulai']!=''){
            $tgl1=$rTgl[0]['tanggalmulai'];
            $tgl2=$rTgl[0]['tanggalsampai'];
        }
        $date2=tanggalnormal($tgl2);

        $totHari=dates_inbetween($tgl1,$tgl2);
        #cari jumlah hari minggu
        $pecahTgl1 = explode("-", $tgl1);
        $tgl1 = $pecahTgl1[2];
        $bln1 = $pecahTgl1[1];
        $thn1 = $pecahTgl1[0];
        $i = 0;
        $sum = 23;
        do{
           // mengenerate tanggal berikutnyahttp://blog.rosihanari.net/menghitung-jumlah-hari-minggu-antara-dua-tanggal/
           $tanggal = date("d-m-Y", mktime(0, 0, 0, $bln1, $tgl1+$i, $thn1));

           /* // cek jika harinya minggu, maka counter $sum bertambah satu, lalu tampilkan tanggalnya
           if (date("w", mktime(0, 0, 0, $bln1, $tgl1+$i, $thn1)) == 0){
              $sum++;
           }  */	 
           $sLbr="select distinct * from ".$dbname.".sdm_5harilibur where 
                  tanggal='".tanggalsystem($tanggal)."' and regional='".$_SESSION['empl']['regional']."'";
           $qLbr=mysql_query($sLbr) or die(mysql_error($conn));
           if(mysql_num_rows($qLbr)==1){
               $sum-=1;
           }
           // increment untuk counter looping
           $i++;
        }
        while ($tanggal != $date2);  

        #array basis
        $is=1;
		 //$sum = 0;
        $sPremi="select distinct * from ".$dbname.".kebun_5premipanen where kodeorg='".$param['kdpremi']."' order by hasilkg desc";
        //exit("error:".$sPremi);
        $qPremi=mysql_query($sPremi) or die(mysql_error($conn));
        while($rPremi=  mysql_fetch_assoc($qPremi)){
            if($_SESSION['empl']['regional']=='SULAWESI'){
                $basisKg[$is]=$rPremi['hasilkg'];
                $premiRajin[$is]=$rPremi['premirajin'];
            }else{
                $basisKg[$is]=$rPremi['lebihbasiskg'];
            }
            $rupiah[$is]=$rPremi['rupiah'];
            $is++;
        }
        $JmlhRow=$is-1;
        $kdOrg=" and left(a.kodeorg,4)='".$param['kodeorg']."'";
        if($param['kodeorg']==substr($param['kdpremi'],0,4)){
            $kdOrg=" and left(a.kodeorg,6)='".$param['kdpremi']."'";
        }
        $sData="select hasilkerjakg,a.nik,tanggal,namakaryawan,hasilkerja,upahkerja,left(a.kodeorg,6) as afdKrj,subbagian from ".$dbname.".kebun_prestasi a 
                left join  ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi
                left join  ".$dbname.".datakaryawan c on a.nik=c.karyawanid where tanggal between '".$rTgl[0]['tanggalmulai']."' and '".$rTgl[0]['tanggalsampai']."' and 
                tipetransaksi='PNN' ".$kdOrg." order by tanggal asc,namakaryawan asc";
				//echo $sData;
        $qData=mysql_query($sData) or die(mysql_error($conn));
        while($rData=  mysql_fetch_assoc($qData)){
            $whrGpk="idkomponen=1 and tahun='".substr($param['periode'],0,4)."' and karyawanid='".$rData['nik']."'";
            $optCek=makeOption($dbname, 'sdm_5gajipokok', 'karyawanid,jumlah',$whrGpk);
            $gapok[$rData['nik']]=$optCek[$rData['nik']]/25;
            if($gapok[$rData['nik']]!=$rData['upahkerja']){
                $dtKaryId[$rData['nik']]=$rData['nik'];
                $dtKaryNm[$rData['nik']]=$rData['namakaryawan'];
                $dtHslKrj[$rData['nik'].$rData['tanggal']]+=$rData['hasilkerjakg'];
                $dtHslJjg[$rData['nik']]+=$rData['hasilkerja'];
				$dtAfdLkKrj[$rData['nik']]=$rData['afdKrj'];
				$dtAfdLkKrj2[$rData['nik']]=$rData['subbagian'];
            }else{
                continue;
            }
        }
		#bjrbulanan
		if($_SESSION['empl']['regional']=='KALIMANTAN'){
			$sBjr="select  (sum(kgwb)/sum(jjg)) as bjr,left(blok,6) as afd from ".$dbname.".kebun_spbdt a left join
                   ".$dbname.".kebun_spbht b on a.nospb=b.nospb			
			       where tanggal between '".$rTgl[0]['tanggalmulai']."' and '".$rTgl[0]['tanggalsampai']."' and left(blok,4)='".$param['kodeorg']."' group by left(blok,6)";
				   //echo $sBjr;
			$qBjr=mysql_query($sBjr) or die(mysql_error($conn));
			while($rBjrDt=mysql_fetch_assoc($qBjr)){
				@$bjrBlnan[$rBjrDt['afd']]=$rBjrDt['bjr'];
			}
		}
        $jmlhRowKary=count($dtKaryId);
        $tab.="<button class=mybutton onclick=saveAll('".$jmlhRowKary."')>".$_SESSION['lang']['save']."</button>
              <table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>
              <tr align=center>";
        $tab.="<td rowspan=2>".$_SESSION['lang']['namakaryawan']."</td>";
        $tab.="<td rowspan=2>".$_SESSION['lang']['periode']."</td>";
	    if($_SESSION['empl']['regional']!='KALIMANTAN'){
			foreach($totHari  as $ar => $isi){
				$qwe=date('D', strtotime($isi));
				$dhr="regional='".$_SESSION['empl']['regional']."' and tanggal='".$isi."'";
				$optHariLbr=makeOption($dbname, 'sdm_5harilibur', 'regional,tanggal',$dhr);
				$tab.="<td width=5px  rowspan=2>";
				if(($qwe=='Sun')||($optHariLbr[$_SESSION['empl']['regional']]!='')){
					$tab.="<font color=red>".substr($isi,8,2)."</font>";
				}else{
					$tab.=(substr($isi,8,2)); 
				}
				$tab.="</td>";
			}
		}
        $tab.="<td colspan=2>".$_SESSION['lang']['total']."</td>";
        if($param['kdpremi']=="KALIMANTAN"){
            $tab.="<td  rowspan=2>".$_SESSION['lang']['pengurang']."</td>";
        }
        $tab.="<td rowspan=2>".$_SESSION['lang']['hk']."</td>";
        $tab.="<td rowspan=2>".$_SESSION['lang']['basis']."</td>";
        $tab.="<td rowspan=2>".$_SESSION['lang']['pengali']."</td>";
        $tab.="<td colspan=3>".$_SESSION['lang']['totalpremi']."</td></tr>
               <tr>
               <td>".$_SESSION['lang']['kg']."</td>
               <td>".$_SESSION['lang']['jjg']."</td>
                    <td>".$_SESSION['lang']['premipanen']."</td>
                    <td>".$_SESSION['lang']['premirajin']."</td>
                    <td>".$_SESSION['lang']['rp']."</td>
               </thead><tbody>";
        foreach($dtKaryId as $karyId){
            $no+=1;
            //$hariAktif[$karyId]=$totHk[$karyId]-$sum;
            $tab.="<tr class=rowcontent>";
            $tab.="<td><input type=hidden id=karyId_".$no." value='".$karyId."' />".$dtKaryNm[$karyId]."</td>";
            $tab.="<td>".$param['periode']."</td>";
			if($_SESSION['empl']['regional']!='KALIMANTAN'){
				foreach($totHari  as $ar => $isi){                 $tab.="<td align=right>".number_format($dtHslKrj[$karyId.$isi],0)."</td>";
					 $totKary[$karyId]+=$dtHslKrj[$karyId.$isi];
					 if($dtHslKrj[$karyId.$isi]!=0){
						 $hariAktif[$karyId]+=1;
					 }
				}
			}else{
				foreach($totHari  as $ar => $isi){                 
				//$tab.="<td align=right>".number_format($dtHslKrj[$karyId.$isi],0)."</td>";
					 $totKary[$karyId]+=$dtHslKrj[$karyId.$isi];
					 if($dtHslKrj[$karyId.$isi]!=0){
						 $hariAktif[$karyId]+=1;
					 }
				}
			}
            $basisData=0;
            $rup=0;
            $rupy=0;
            $totRup=0;
            if($_SESSION['empl']['regional']=='SULAWESI'){
                //exit("error:masuk".$hariAktif[$karyId]);
                $basisData=0;
                $totRup=0;
                $pengaliDt=0;
                if(($hariAktif[$karyId]==22)||($hariAktif[$karyId]>22)){
                        if($param['kdpremi']=='SULAWESI'){
                            //exit("error: masuk sini");
                                    for($awl=1;$awl<=$JmlhRow;$awl++){
                                        if(($totKary[$karyId]>$basisKg[$awl])||($totKary[$karyId]==$basisKg[$awl])){
                                                    $rup=$rupiah[$awl];
                                                    $prmRajin=$premiRajin[$awl];
                                                    break;
                                         }elseif(($totKary[$karyId]<$basisKg[$itungDsr])||($totKary[$karyId]==$basisKg[$itungDsr])){
                                                        $rup=$rupiah[$itungDsr];
                                                        $prmRajin=$premiRajin[$itungDsr];
                                         
                                          break;
                                         }
                                    }
                                    
                                    @$bandingJjg[$karyId]=$dtHslJjg[$karyId]/70;
                                    //if($hariAktif[$karyId]>=16){
                                    if($bandingJjg[$karyId]>$hariAktif[$karyId]){
                                        $rupy=($hariAktif[$karyId])*$prmRajin;
                                    }else{
                                        $rupy=($bandingJjg[$karyId])*$prmRajin;
                                    }
                                    $totRup=$rupy+$rup;
                
                        }else{
                           //exit('error:masuk');
                            for($awl=1;$awl<=$JmlhRow;$awl++){
                                if(($totKary[$karyId]>$basisKg[$awl])||($totKary[$karyId]==$basisKg[$awl])){
                                    $totRup=$rupiah[$awl];
                                    $pengaliDt=1;
                                    break;
                                }
                            }
//                            $awl=1;
//                            if(($totKary[$karyId]>$basisKg[$awl])||($totKary[$karyId]==$basisKg[$awl])){
//                                //$totRup=$totKary[$karyId]*$rupiah[$awl];
//								$totRup=$rupiah[$awl];
//                                $pengaliDt=1;
//                                 //exit("error:masuk sini");
//                             }
                        }
                }
            }else if($_SESSION['empl']['regional']=='KALIMANTAN'){
                    $bjrAktual[$karyId]=$totKary[$karyId]/$dtHslJjg[$karyId];
                    $lstert=0;
                    $sTarif="select distinct * from ".$dbname.".kebun_5basispanen where 
                             kodeorg='".$param['kdpremi']."' and jenis='satuan' order by bjr desc";
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
                            if(intval($bjrAktual[$karyId])>$dtIsiBjr){
                                $dtbjr=$dtIsiBjr;
                                break;
                            }
                        }else{
                            if($lstRow!=$MaxRow){
                                $leapdt=$lstRow-1;
							    $leapdt2=$lstRow+1;
								if((intval($bjrAktual[$karyId])>=$dtIsiBjr)&&(intval($bjrAktual[$karyId])<$lstBjr2[$leapdt])){
                                    $dtbjr=$dtIsiBjr;
                                    break;
                                }
                            }else{
                                $dmin=$dtIsiBjr-1;
								$dtbjr=$dtIsiBjr;
                                if(intval($bjrAktual[$karyId])>=$dmin){
                                   $dtbjr=$dtIsiBjr;
                                    break;
                                }else{
                                    $dtbjr=0;
                                }
                            }
                        }
                    }
                    
                    //$bas[$karyId]=$basisPanen[$dtbjr]*$bjrAktual[$karyId]*($totHk[$karyId]-$sum);
					$totHk[$karyId]=$sum;
					$totKGan[$karyId]=$bjrBlnan[$dtAfdLkKrj[$karyId]]*$dtHslJjg[$karyId];
					$bas[$karyId]=$basisPanen[$dtbjr]*$dtbjr*$totHk[$karyId];
                    $pre[$karyId]=$totKGan[$karyId]-$bas[$karyId];//$totKary[$karyId]
                    for($awl=1;$awl<=$JmlhRow;$awl++){
                                if($awl==1){
                                    if(($pre[$karyId]>$basisKg[$awl])||($pre[$karyId]==$basisKg[$awl])){
                                        $totRup=$rupiah[$awl];
                                        $basisData=$basisKg[$awl];
                                        break;
                                    }
                                }
                                if($awl!=$JmlhRow){
                                    $fwd=$awl+1;
                                    if(($pre[$karyId]>$basisKg[$fwd])||($pre[$karyId]==$basisKg[$awl])){
                                        $totRup=$rupiah[$awl];
                                        $basisData=$basisKg[$awl];
                                        break;
                                    }
                                }else if(($pre[$karyId]>$basisKg[$awl])||($pre[$karyId]==$basisKg[$awl])){
                                        $totRup=$rupiah[$awl];
                                        $basisData=$basisKg[$awl];
                                        break;
                                }
                    }
                }#end kalimantan
            //$tab.="<td align=right><input type=hidden id=totKg_".$no." value='".$totKary[$karyId]."' />".number_format($totKary[$karyId],0)."__".$totKGan[$karyId]."</td>
            if($_SESSION['empl']['regional']=='KALIMANTAN'){
				$tab.="<td align=right><input type=hidden id=totKg_".$no." value='".$totKGan[$karyId]."' />".number_format($totKGan[$karyId],0)."</td>
                   <td  align=right><input type=hidden   value='".$dtHslJjg[$karyId]."' />".number_format($dtHslJjg[$karyId],0)."</td>";
		    }else{
				$tab.="<td align=right><input type=hidden id=totKg_".$no." value='".$totKary[$karyId]."' />".number_format($totKary[$karyId],0)."</td>
                   <td  align=right><input type=hidden   value='".$dtHslJjg[$karyId]."' />".number_format($dtHslJjg[$karyId],0)."</td>";
			}
            if($param['kdpremi']=="KALIMANTAN"){
                $tab.="<td align=right>".number_format($bas[$karyId],0)."</td>";
            }
            $tab.="<td align=right>".$totHk[$karyId]."</td>";
            $tab.="<td align=right>".$basisData."</td>";
            $tab.="<td align=right>".$pengaliDt."</td>";
            $tab.="<td align=right>".number_format($rup,0)."</td>
                   <td align=right>".number_format($rupy,0)."</td>
                   <td align=right><input type=hidden id=rpPremi_".$no." value='".$totRup."' />".number_format($totRup,0)."</td>
                   ";
            $totRup=0;
            $rup=0;
            $rupy=0;
        }
        $tab.="</tbody></table><button class=mybutton onclick=saveAll('".$jmlhRowKary."')>".$_SESSION['lang']['save']."</button>";
        echo $tab;
       
    break;
    case'saveAll':
        
        for($awal=1;$awal<=$param['jmlhRow'];$awal++){
            $sdel="delete from ".$dbname.".`kebun_premipanen`  where 
                   kodeorg='".$param['kodeorg']."' and `karyawanid`='".$param['KaryId'][$awal]."'
                   and periode='".$param['periode']."'";
            if(mysql_query($sdel)){
                $sinsert="insert into ".$dbname.".`kebun_premipanen` (`kodeorg`,`kodepremi`,`karyawanid`,`periode`,`totalkg`,`rupiahpremi`,`updateby`) values";
                $sinsert.="('".$param['kodeorg']."','".$param['kdpremi']."','".$param['KaryId'][$awal]."','".$param['periode']."','".$param['hasilKg'][$awal]."','".$param['rpPremi'][$awal]."','".$_SESSION['standard']['userid']."')";
                if(!mysql_query($sinsert)){
                    exit("error: db error ".mysql_error($conn)."___".$sinsert);
                }
            }else{
                    exit("error: db error ".mysql_error($conn)."___".$sdel);
            }
        }
    break;
    case'loadData':
	
        $periodeAktif=$_SESSION['org']['period']['tahun']."-".$_SESSION['org']['period']['bulan'];
        $sData="select distinct kodeorg,periode,kodepremi from ".$dbname.".kebun_premipanen where 
                kodeorg='".$_SESSION['empl']['lokasitugas']."' group by kodeorg,periode order by periode desc";
        //exit("error:".$sData);
        $qData=mysql_query($sData) or die(mysql_error($conn));
        while($rData=  mysql_fetch_assoc($qData)){
            $no+=1;
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$no."</td>";
            $tab.="<td>".$rData['kodeorg']."</td>";
            $tab.="<td>".$rData['periode']."</td>";
            if($rData['periode']==$periodeAktif){
                $tab.="<td>
                       <img src='images/excel.jpg' class='resicon' title='Excel' onclick=getExcel(event,'kebun_slave_premipanen.php','".$rData['kodeorg']."','".$rData['periode']."','".$rData['kodepremi']."') >
                       &nbsp;
                       <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rData['kodeorg']."','".$rData['periode']."');\" >
                       &nbsp;
                       <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_premipanen','".$rData['kodeorg'].",".$rData['periode'].",".$rData['kodepremi']."','','kebun_slave_premipanen',event);\">
                      </td>";
            }else{
                $tab.="<td><img src='images/excel.jpg' class='resicon' title='Excel' onclick=getExcel(event,'kebun_slave_premipanen.php','".$rData['kodeorg']."','".$rData['periode']."','".$rData['kodepremi']."') ></td>";
            }
            $tab.="</tr>";
        }
        echo $tab;
    break;
    case'delData':
        $sdel="delete from ".$dbname.".`kebun_premipanen`  where 
               kodeorg='".$param['kodeorg']."' and periode='".$param['periode']."'";
            if(!mysql_query($sdel)){
                exit("error: db error ".mysql_error($conn)."___".$sdel);
            }
    break;
    case'excel':
        $tab.="<table>";
        $tab.="<tr><td colspan=5>".$_SESSION['lang']['kodeorg']." : ".$optNmOrg[$param['kodeorg']]."</td></tr>";
        $tab.="<tr><td colspan=5>".$_SESSION['lang']['periode']." : ".$param['periode']."</td></tr>";
        $tab.="<tr><td colspan=5>".$_SESSION['lang']['kodepremi']." : ".$param['kodePremi']."</td></tr>";
        $tab.="</table>";
        $tab.="<table class=sortable border=1 cellpadding=1 cellspacing=1>";
        $tab.="<thead>";
        $tab.="<tr bgcolor=#DEDEDE align=center> ";
        $tab.="<td>No.</td>";
        $tab.="<td>".$_SESSION['lang']['namakaryawan']."</td>";
        $tab.="<td>".$_SESSION['lang']['hasilkerjakg']."</td>";
        $tab.="<td>".$_SESSION['lang']['premi']."</td></tr><tbody>";
        $sData="select a.* from ".$dbname.".kebun_premipanen a 
                left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                where kodeorg='".$param['kodeorg']."' and periode='".$param['periode']."'
                order by namakaryawan asc";
        //exit("error:".$sData);
        $qData=mysql_query($sData) or die(mysql_error($conn));
        while($rData=  mysql_fetch_assoc($qData)){
            $no+=1;
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$no."</td>";
            $tab.="<td>".$optNmKary[$rData['karyawanid']]."</td>";
            $tab.="<td align=right>".number_format($rData['totalkg'],0)."</td>";
            $tab.="<td align=right>".number_format($rData['rupiahpremi'],0)."</td>";
            $tab.="</tr>";
        }
        $tab.="</tbody></table>";
        $tab.="Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];          
        $nop_="premiPanen__".$param['kodeorg']."__".$param['periode'];
        if(strlen($tab)>0)
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
        if(!fwrite($handle,$tab))
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
        $table = $_GET['table'];
        $column = explode(",",$_GET['column']);
        $where = $_GET['cond'];
        

            #====================== Prepare Data
          
            
            #====================== Prepare Header PDF
            class masterpdf extends FPDF {
                function Header() {
                    global $table;
                    global $header;
                    global $column;
                    global $dbname;
                    global $optNmKary;
                    global $optNmOrg;

                    # Panjang, Lebar
                    $width = $this->w - $this->lMargin - $this->rMargin;
                            $height = 12;
                           
                            //$this->Cell(40/100*$width,$height,strtoupper($_SESSION['org']['namaorganisasi']),'',0,'L');
                            //$this->Cell(20,$height,' ','',0,'L');
                            $this->SetFont('Arial','B',10);
                            $this->Cell(12/100*$width,$height,$_SESSION['lang']['unit'],'',0,'L');
                            $this->Cell(2/100*$width,$height,':','',0,'L');
                            $this->Cell(1/100*$width,$height,$optNmOrg[$column[0]]." ".$column[0],'',1,'L');		
                            $this->Cell(12/100*$width,$height,$_SESSION['lang']['periode'],'',0,'L');
                            $this->Cell(2/100*$width,$height,':','',0,'L');
                            $this->Cell(2/100*$width,$height,$column[1],'',1,'L');		
                            $this->Cell(12/100*$width,$height,$_SESSION['lang']['kodepremi'],'',0,'L');
                            $this->Cell(2/100*$width,$height,':','',0,'L');
                            $this->Cell(1/100*$width,$height,$column[2],'',0,'L');		


                    $this->Ln();

                }
            }

            #====================== Prepare PDF Setting
            $pdf = new masterpdf('P','pt','A4');
            $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
            $height = 12;

            $pdf->SetFont('Arial','B',8);
            $pdf->SetFillColor(220,220,220);
            $pdf->AddPage();
                    $pdf->Cell(20,1.5*$height,'No.','TBLR',0,'C');
                    $pdf->Cell(160,1.5*$height,$_SESSION['lang']['namakaryawan'],'TBLR',0,'L');
                    $pdf->Cell(65,1.5*$height,$_SESSION['lang']['hasilkerjakg'],'TBLR',0,'C');
                    $pdf->Cell(65,1.5*$height,$_SESSION['lang']['premi'],'TBLR',1,'L');
                    $no=0;
                    $pdf->SetFillColor(255,255,255);
                    $ql="select a.* from ".$dbname.".".$table." a 
                left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                where kodeorg='".$column[0]."' and periode='".$column[1]."'
                order by namakaryawan asc"; //echo $ql;
                    $qData=mysql_query($ql) or die(mysql_error($conn));
                    while($data=  mysql_fetch_assoc($qData)) {
                        $pdf->SetFont('Arial','',7);
                            $no++;
                            //$tr=substr($data['namabarang'],0,20);
                            $pdf->Cell(20,$height,$no,'TBLR',0,'L');
                            $pdf->Cell(160,$height,$optNmKary[$data['karyawanid']],'TBLR',0,'L');
                            $pdf->Cell(65,$height,number_format($data['totalkg'],0),'TBLR',0,'R');
                            //$pdf->Cell(80,$height,$data['spesifikasi'],'TBLR',0,'L');
                            $pdf->Cell(65,$height,number_format($data['rupiahpremi'],0),'TBLR',1,'R');
                    }
                    
                    $pdf->Cell(15,$height,'Page '.$pdf->PageNo(),'',1,'L');

            # Print Out
            $pdf->Output();

    break;
}



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
?>
