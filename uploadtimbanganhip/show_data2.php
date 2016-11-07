<?php
include "connection2.php";
 	
$proses=$_POST['proses'];
$DtTime=date("Y-m-d H:i:s");
	$idTimbangan2 =$_POST['idTimbangan'];
	$tglData2=$_POST['tglData'];
	$custData2=$_POST['custData'];
	$kbn2=$_POST['kbn'];
	$pabrik2=$_POST['pabrik'];
	$kdBrg2=$_POST['kdBrg'];
	$spbno2=$_POST['spbno'];
	$sibno2=$_POST['sibno'];

	$thnTnm2=intval($_POST['thnTnm']);
	$thnTnm22=intval($_POST['thnTnm2']);
	$thnTnm32=intval($_POST['thnTnm3']);
	$jmlhjjg2=intval($_POST['jmlhjjg']);
	$jmlhjjg22=intval($_POST['jmlhjjg2']);
	$jmlhjjg32=intval($_POST['jmlhjjg3']);
	$brndln2=intval($_POST['brndln']);

	$nodo2=$_POST['nodo'];
	$kdVhc2=$_POST['kdVhc'];
	$spir2=$_POST['spir'];
	$jmMasuk2=$_POST['jmMasuk'];
	$jmKeluar2=$_POST['jmKeluar'];
	$brtBrsih2=$_POST['brtBrsih'];
	$brtMsk2=$_POST['brtMsk'];
	$brtOut2=$_POST['brtOut'];
	$usrNm2=$_POST['usrNm'];
	$kntrkNo2=$_POST['kntrkNo'];
        $potsortasi=$_POST['potsortasi'];
        if($potsortasi=='')
            $potsortasi=0;
	$sloc=$_POST['sloc'];
	$penerima=$_POST['penerima'];
	$buahbusuk=($_POST['buahbusuk']==''?0:$_POST['buahbusuk']);
	$buahkrgmatang=($_POST['buahkrgmatang']==''?0:$_POST['buahkrgmatang']);
	$buahsakit=($_POST['buahsakit']==''?0:$_POST['buahsakit']);
	$janjangkosong=($_POST['janjangkosong']==''?0:$_POST['janjangkosong']);
	$lwtmatang=($_POST['lwtmatang']==''?0:$_POST['lwtmatang']);		
	$mentah=($_POST['mentah']==''?0:$_POST['mentah']);
	$tkpanjang=($_POST['tkpanjang']==''?0:$_POST['tkpanjang']);		
	$tigakilo=($_POST['tigakilo']==''?0:$_POST['tigakilo']);			

 switch($proses)
 {
	 case'showData':
                 $message="Ambil Data";   
                 $sCob="SELECT * from ".$dbnm.".mstrxtbs WHERE (GI='0' or GI='') and OUTIN=0 and PRODUCTCODE in ('40000001','40000002','40000003','40000004','40000005','40000006','40000007') ORDER BY `TICKETNO2` ASC limit 0,1";
                    //echo $sCob;exit();
                    $res=mysql_query($sCob,$corn2) or die(mysql_error());
                if(mysql_num_rows($res)>0){
                    $result=mysql_fetch_assoc($res);
                    $kbn=$result['UNITCODE'];
                    $idTimbangan=$result['TICKETNO2'];
                    $tglData=$result['DATEIN'];
                    $custData=$result['TRPCODE'];
                    $kdBrg=$result['PRODUCTCODE'];
                    $brtMsk=$result['WEI1ST'];
                    $jmMasuk=substr($result['DATEIN'],10,9);
                    $jmKeluar=substr($result['DATEOUT'],10,9);
                    $brtOut=$result['WEI2ND'];
                    $kdVhc=$result['VEHNOCODE'];
                    $spir=$result['DRIVER'];
                    $spbno=$result['SPBNO'];
                    $kntrkNo=$result['CTRNO'];
                    $nodo=$result['NODOTRP'];
                    $sibno=$result['SIPBNO'];
                    $thnTnm=$result['TAHUNTANAM'];
                    $thnTnm2=$result['TAHUNTANAM2'];
                    $thnTnm3=$result['TAHUNTANAM2'];
                    $jmlhjjg2=$result['JMLHJJG2'];
                    $jmlhjjg=$result['JMLHJJG'];
                    $jmlhjjg3=$result['JMLHJJG3'];
                    $brndln=$result['BRONDOLAN'];
                    $usrNm=$result['USERID'];
                    $pabrik=$result['MILLCODE'];
                    $brtBrsih=$result['NETTO'];
                    $potsortasi=$result['KGPOTSORTASI'];
					$sloc=$result['SLOC'];
					$penerima=$result['PENERIMA'];
                    $statusConn=0;
				
                    $buahbusuk=$result['buahbusuk'];
					$buahkrgmatang=$result['buahkrgmatang'];
					$buahsakit=$result['buahsakit'];
					$janjangkosong=$result['janjangkosong'];
					$lwtmatang=$result['lwtmatang'];
					$mentah=$result['mentah'];
					$tkpanjang=$result['tkpanjang'];
					$tigakilo=$result['tigakilo'];
					
                    echo"<tr>
                    <td>".$idTimbangan."</td>
					<td>".$tglData."</td>
                    <td>".$kdVhc."</td>
                    <td>".$brtBrsih."</td>
                    <td id=pesanku>".$message."</td>
                    </tr>###<tr>
                            <td id=tglData>".$tglData."</td>
                            <td id=notiket>".$idTimbangan."</td>
                            <td id=custData>".$custData."</td>
                            <td id=kntrkNo>".$kntrkNo."</td>
                            <td id=kbn>".$kbn."</td>
                            <td id=pabrik>".$pabrik."</td>
                            <td id=kdBrg>".$kdBrg."</td>
                            <td id=spbno>".$spbno."</td>
                            <td id=sibno>".$sibno."</td>
                            <td id=thnTnm>".$thnTnm."</td>
                            <td id=thnTnm2>".$thnTnm2."</td>
                            <td id=thnTnm3>".$thnTnm3."</td>
                            <td id=jmlhjjg>".$jmlhjjg."</td>
                            <td id=jmlhjjg2>".$jmlhjjg2."</td>
                            <td id=jmlhjjg3>".$jmlhjjg3."</td>
                            <td id=brndln>".$brndln."</td>
                            <td id=nodo>".$nodo."</td>
                            <td id=kdVhc>".$kdVhc."</td>
                            <td id=spir>".$spir."</td>
                            <td id=jmMasuk>".$jmMasuk."</td>
                            <td id=jmKeluar>".$jmKeluar."</td>
                            <td align=right id=brtBrsih>".$brtBrsih."</td>
                            <td align=right id=brtMsk>".$brtMsk."</td>
                            <td align=right id=brtOut>".$brtOut."</td>
							<td align=right id=sloc>".$sloc."</td>
							<td align=right id=penerima>".$penerima."</td>
                            <td align=right id=potsortasi>".$potsortasi."</td>
							<td id=usrNm>".$usrNm."</td>								
                            <td id=buahbusuk>".$buahbusuk."</td>
							<td id=buahkrgmatang>".$buahkrgmatang."</td>
							<td id=buahsakit>".$buahsakit."</td>
							<td id=janjangkosong>".$janjangkosong."</td>
							<td id=lwtmatang>".$lwtmatang."</td>
							<td id=mentah>".$mentah."</td>
							<td id=tkpanjang>".$tkpanjang."</td>
							<td id=tigakilo>".$tigakilo."</td>
                            </tr>
                            ";
                    }
                 else {
                    echo"<tr>
                    <td>".$idTimbangan."</td>
					<td>".$tglData."</td>
                    <td>".$kdVhc."</td>
                    <td>".$brtBrsih."</td>
                    <td id=pesanku>".$message."</td>
                    </tr>###null###";
                }
                 break;
                 
	 case'uploadData':
             //hapus dulu jika ada untuk mengantisipasi adanya koreksi di timbangan
             $strx="delete from ".$dbname.".pabrik_timbangan  where notransaksi='".$idTimbangan2."'";
             mysql_query($strx,$conn2);         
                        //intex stat
                        if(($kbn2=='NULL')||($kbn2==''))
                        {
                                $inTex2=0;
                        }
                        else
                        {
                                $sCek="select induk from ".$dbname.".organisasi where kodeorganisasi='".$kbn2."'";
                                $qCek=mysql_query($sCek,$conn2) or die(mysql_error());
                                $rCek=mysql_fetch_assoc($qCek);

                                if($rCek['induk']!='NFS')
                                {
                                $inTex2=2;
                                }
                                elseif(eregi("e$",$kbn2))
                                {
                                $inTex2=1;
                                }
                        }


                        $sIns="INSERT INTO ".$dbname.".pabrik_timbangan (`notransaksi`, `tanggal`, `kodeorg`, `kodecustomer`, `jumlahtandan1`, `kodebarang`, `jammasuk`, `beratmasuk`, `jamkeluar`, `beratkeluar`, `nokendaraan`, `supir`, `nospb`, `nokontrak`, `nodo`, `nosipb`, `thntm1`, `thntm2`, `thntm3`, `jumlahtandan2`, `jumlahtandan3`, `brondolan`, `username`, `millcode`, `beratbersih`,`intex`,`timbangonoff`,`kgpotsortasi`,`sloc`,`penerima`) 
						       VALUES ('".$idTimbangan2."','".$tglData2."','".$kbn2."','".$custData2."','".$jmlhjjg2."','".$kdBrg2."','".$jmMasuk2."','".$brtMsk2."','".$jmKeluar2."','".$brtOut2."','".$kdVhc2."','".$spir2."','".$spbno2."','".$kntrkNo2."','".$nodo2."','".$sibno2."','".$thnTnm2."','".$thnTnm22."','".$thnTnm32."','".$jmlhjjg22."','".$jmlhjjg32."','".$brndln2."','".$usrNm2."','".$pabrik2."','".$brtBrsih2."','".$inTex2."','0',".$potsortasi.",'".$sloc."','".$penerima."')";
						if(mysql_query($sIns,$conn2))
                        {
                                $sUp="update ".$dbnm.".mstrxtbs set GI='".$DtTime."' where TICKETNO2='".$idTimbangan2."' and OUTIN=0";
								$ar=mysql_query($sUp,$corn2);
                                if(!$ar)
                                {
                                        $statusConn=4;
                                }
                                else
                                {
                                        $statusConn=2;
                                }
							
						//update table pabrik_sortasi===========
						  $str="delete from ".$dbname.".pabrik_sortasi where notiket='".$idTimbangan2."'";
                          @mysql_query($str,$conn2);
						  $str="insert into ".$dbname.".pabrik_sortasi(notiket, kodefraksi, jumlah) values
						  ('".$idTimbangan2."','buahbusuk',".$buahbusuk."),
						  ('".$idTimbangan2."','buahkrgmatang',".$buahkrgmatang."),
						  ('".$idTimbangan2."','buahsakit',".$buahsakit."),
						  ('".$idTimbangan2."','janjangkosong',".$janjangkosong."),
						  ('".$idTimbangan2."','lwtmatang',".$lwtmatang."),
						  ('".$idTimbangan2."','mentah',".$mentah."),
						  ('".$idTimbangan2."','tkpanjang',".$tkpanjang."),
						  ('".$idTimbangan2."','tigakilo',".$tigakilo.");";
                           @mysql_query($str,$conn2);
                        //=======================================						   
                       }
                        else
                        {
                                $statusConn=4;

                        }

                echo $statusConn;
                 break;
	 default:
	 break;
 }
?>
