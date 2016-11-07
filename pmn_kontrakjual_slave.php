<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
$method=$_POST['method'];
$kdBrg=$_POST['kdBrg'];
$noKntrk=$_POST['noKntrk'];
$custId=$_POST['custId'];
$tlgKntrk=tanggalsystem($_POST['tlgKntrk']);
$kdBrg=$_POST['kdBrg'];
$satuan=$_POST['satuan'];
$tBlg=$_POST['tBlg'];
$qty=$_POST['qty'];
$tglKrm=tanggalsystem($_POST['tglKrm']);
$tglSd=tanggalsystem($_POST['tglSd']);
$kualitas=$_POST['kualitas'];
$syrtByr=$_POST['syrtByr'];
$tmbngn=$_POST['tmbngn'];
$pnyrhn=$_POST['pnyrhn'];
$cttn1=$_POST['cttn1'];
$cttn2=$_POST['cttn2'];
$cttn3=$_POST['cttn3'];
$cttn4=$_POST['cttn4'];
$cttn5=$_POST['cttn5'];
$HrgStn=$_POST['HrgStn'];
$tndTng=$_POST['tndtng'];
$noDo=$_POST['noDo'];
$othCttn=$_POST['othCttn'];
$tlransi=$_POST['tlransi'];
$kdPt=$_POST['kdPt'];
$lokasiTugas=substr($_SESSION['empl']['lokasitugas'],0,4);
$txtSearch=$_POST['txtSearch'];
$kurs=$_POST['kurs'];



$ppn=$_POST['ppn'];
$lamamuat=$_POST['lamamuat'];
$pelabuhan=$_POST['pelabuhan'];
$demurage=$_POST['demurage'];

//exit("Error:$kualitas");

        switch($method)
        {
			
			case'cekDate':
			
				if($tglSd<$tglKrm)
				{
					$a='a';
				}
				else
				{
					$a='b';
				}//exit("Error:Date not valid");
				
				echo $a;
			break;
			
			case'posting':
				$sekarang=date('Y-m-d');
				$i="update  ".$dbname.".pmn_kontrakjual set posting=1,postingdate='".$sekarang."',postingby='".$_SESSION['standard']['userid']."' where nokontrak='".$noKntrk."'";
				//exit("Error:$i");
				if(mysql_query($i))
				{
				}
				else
				echo " Gagal,".addslashes(mysql_error($conn));
			break;
			
			
                case'LoadNew':
                $limit=10;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;

                $ql2="select count(*) as jmlhrow from ".$dbname.".pmn_kontrakjual  order by `tanggalkontrak` desc";// echo $ql2;
                $query2=mysql_query($ql2) or die(mysql_error());
                while($jsl=mysql_fetch_object($query2)){
                $jlhbrs= $jsl->jmlhrow;
                }


                $slvhc="select * from ".$dbname.".pmn_kontrakjual  order by `tanggalkontrak` desc limit ".$offset.",".$limit."";
                $qlvhc=mysql_query($slvhc) or die(mysql_error());
                $user_online=$_SESSION['standard']['userid'];
                while($res=mysql_fetch_assoc($qlvhc))
                {
                        $sCust="select namacustomer  from ".$dbname.".pmn_4customer where kodecustomer = '".$res['koderekanan']."'"; //echo $sCust;
                        $qCUst=mysql_query($sCust) or die(mysql_error());
                        $rCust=mysql_fetch_assoc($qCUst);

                        $sBrg="select namabarang from ".$dbname.".log_5masterbarang where `kodebarang`='".$res['kodebarang']."'";
                        $qBrg=mysql_query($sBrg) or die(mysql_error());
                        $rBrg=mysql_fetch_assoc($qBrg);

                        $sOrg="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$res['kodept']."'";
                        $qOrg=mysql_query($sOrg) or die(mysql_error());
                        $rOrg=mysql_fetch_assoc($qOrg);

                $no+=1;
				
				
				if($res['posting']=='0')
				{
					$isi="<td>
							<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res['nokontrak']."');\">
							<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$res['nokontrak']."');\" >	<br />
							<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('pmn_kontrakjual','".$res['nokontrak']."','','pmn_kontakjual_pdf',event)\">
							<img src=images/icons/04/10/01.png  title='Posting' class=zImgBtn caption='Posting' onclick=\"posting('".$res['nokontrak']."');\">
						</td>";
				}
				else
				{
					$isi="<td>
							<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('pmn_kontrakjual','".$res['nokontrak']."','','pmn_kontakjual_pdf',event)\">
							<img src=images/icons/04/10/02.png class=zImgBtn>
						</td>";
				}
				
				
				
                echo"
                        <tr class=rowcontent>
                        <td>".$no."</td>
                        <td>".$res['nokontrak']."</td>
                        <td>".$rOrg['namaorganisasi']."</td>
                        <td>".$rCust['namacustomer']."</td>
                        <td>".tanggalnormal($res['tanggalkontrak'])."</td>
                        <td>".$res['kodebarang']."</td>
                        <td>".$rBrg['namabarang']."</td>
                        <td>".tanggalnormal($res['tanggalkirim'])."</td>";
                        echo $isi;
                        echo"</tr>";
                }
                echo"
                <tr class=rowheader><td colspan=9 align=center>
                ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
                <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                </td>
                </tr>";
                break;
                case'getSatuan':
                //echo"warning:masuk".$kdBrg;
                $sSat2="select distinct satuan from ".$dbname.".log_5masterbarang where kodebarang='".$kdBrg."'";
                $qSat2=mysql_query($sSat2) or die(mysql_error());
                $rsat2=mysql_fetch_assoc($qSat2);

                $sSat="select distinct a.satuan,b.satuankonversi from ".$dbname.".log_5masterbarang a inner join ".$dbname.".log_5stkonversi b on a.satuan=b.satuankonversi where a.kodebarang='".$kdBrg."' "; //echo"warning:".$sSat;
                $qSat=mysql_query($sSat) or die(mysql_query());
                $optSatuan.="<option value=".$rsat2['satuan']."  ".($rsat2['satuan']==$satuan?'selected':'').">".$rsat2['satuan']."</option>";
                while($rSat=mysql_fetch_assoc($qSat))
                {
                        $optSatuan.="<option value=".$rSat['satuankonversi']." ".($rSat['satuankonversi']==$satuan?'selected':'').">".$rSat['satuankonversi']."</option>";
                }
                echo $optSatuan;
                break;
                case'getLastData':
                $sql="select * from ".$dbname.".pmn_kontrakjual order by tanggalkontrak desc limit 0,1";
                $query=mysql_query($sql) or die(mysql_error());
                $res=mysql_fetch_assoc($query);
        /*	, tanggalkontrak, koderekanan, kodebarang, satuan, hargasatuan, terbilang, kualitas, tanggalkirim, sdtanggal, syratpembayaran, do, catatan1, catatan2, catatan3, catatan4, catatan5, standartimbangan, penandatangan, penandatangan2, catatanlain, kuantitaskirim, kuantitaskontrak*/
                echo $res['nokontrak']."###".$res['koderekanan']."###".tanggalnormal($res['tanggalkontrak'])."###".$res['kodebarang']."###".$res['hargasatuan']."###".$res['terbilang']."###".$res['kuantitaskontrak']."###".tanggalnormal($res['tanggalkirim'])."###".tanggalnormal($res['sdtanggal'])."###".$res['toleransi']."###".$res['nodo']."###".$res['kualitas']."###".$res['syratpembayaran']."###".$res['penandatangan']."###".$res['standartimbangan']."###".$res['catatan1']."###".$res['catatan2']."###".$res['catatan3']."###".$res['catatan4']."###".$res['catatan5']."###".$res['catatanlain']."###".$res['satuan']."###".$res['kodept'];
                break;

                case'getEditData':
                $sql="select * from ".$dbname.".pmn_kontrakjual where nokontrak='".$noKntrk."'";
                $query=mysql_query($sql) or die(mysql_error());
                $res=mysql_fetch_assoc($query);
        /*	, tanggalkontrak, koderekanan, kodebarang, satuan, hargasatuan, terbilang, kualitas, tanggalkirim, sdtanggal, syratpembayaran, do, catatan1, catatan2, catatan3, catatan4, catatan5, standartimbangan, penandatangan, penandatangan2, catatanlain, kuantitaskirim, kuantitaskontrak*/
                echo $res['nokontrak']."###".$res['koderekanan']."###".tanggalnormal($res['tanggalkontrak'])."###".$res['kodebarang']."###".$res['hargasatuan']."###".$res['terbilang']."###".$res['kuantitaskontrak']."###".tanggalnormal($res['tanggalkirim'])."###".tanggalnormal($res['sdtanggal'])."###".$res['toleransi']."###".$res['nodo']."###".$res['kualitas']."###".$res['syratpembayaran']."###".$res['penandatangan']."###".$res['standartimbangan']."###".$res['catatan1']."###".$res['catatan2']."###".$res['catatan3']."###".$res['catatan4']."###".$res['catatan5']."###".$res['catatanlain']."###".$res['satuan']."###".$res['kodept']."###".$res['matauang']."###".$res['ppn']."###".$res['lamamuat']."###".$res['pelabuhan']."###".$res['demurage'];
                break;
				
				
				
				
                case'insert':
                /*if(($noKntrk=='')||($custId=='')||($kdBrg=='')||($HrgStn=='')||($tBlg=='')||($qty=='')||($tlgKntrk=='')||($satuan=='')||($tglKrm=='')||($tglSd==''))
                {
                        //print_r($_POST);
                        echo"Warning: Please complete the form";
                        exit();
                }*/
				
				
				
				if($noKntrk=='')
					exit("Error:No. Contract empty");
				if($custId=='')
					exit("Error: Customer empty");
				if($kdBrg=='')
					exit("Error: Item empty");
				if($HrgStn=='')
					exit("Error: Price empty");
				if($qty=='')
					exit("Error: Qty empty");	
				if($tlgKntrk=='')
					exit("Error: Contract Date empty");	
				if($satuan=='')
					exit("Error: UOM empty");	
				if($kualitas=='')
					exit("Error: Quality empty");	
				if($kualitas=='')
					exit("Error: empty");		
				if($tglKrm=='')
					exit("Error: Delivery Date empty");	
					
				if($tglSd<$tglKrm)
					exit("Error:Delivery Date not valid");
					
				
				
					
				
                $sCek="select nokontrak from ".$dbname.".pmn_kontrakjual where nokontrak='".$noKntrk."'";
                $qCek=mysql_query($sCek) or die(mysql_error());
                $rCek=mysql_num_rows($qCek);
                if($rCek<1)
                {
                        $sCust="select kontakperson from ".$dbname.".pmn_4customer where kodecustomer = '".$custId."'";
                        $qCUst=mysql_query($sCust) or die(mysql_error());
                        $rCust=mysql_fetch_assoc($qCUst);
                        $sIns="insert into ".$dbname.".pmn_kontrakjual (nokontrak, tanggalkontrak, koderekanan, kodebarang, satuan, hargasatuan, terbilang, kualitas, tanggalkirim, sdtanggal, syratpembayaran, catatan1, catatan2, catatan3, catatan4, catatan5, standartimbangan, penandatangan, penandatangan2, catatanlain,  kuantitaskontrak,toleransi,nodo,kodeorg,kodept,matauang,ppn,lamamuat,pelabuhan,demurage) 
                               values ('".$noKntrk."','".$tlgKntrk."','".$custId."','".$kdBrg."','".$satuan."','".$HrgStn."','".$tBlg."','".$kualitas."','".$tglKrm."','".$tglSd."','".$syrtByr."','".$cttn1."','".$cttn2."','".$cttn3."','".$cttn4."','".$cttn5."','".$tmbngn."','".$tndTng."','".$rCust['kontakperson']."','".$othCttn."','".$qty."','".$tlransi."','".$noDo."','".$lokasiTugas."','".$kdPt."','".$kurs."','".$ppn."','".$lamamuat."','".$pelabuhan."','".$demurage."')";   /**/ //echo"warning:".$sIns;exit();
                        //exit("Error".$sIns);
                        if(mysql_query($sIns))
                        echo"";
                        else
                        echo "DB Error : ".mysql_error($conn);	
                }
                else
                {
                        echo"warning: Contract already exist";
                        exit();
                }
                break;
				
				
                case'update':
				
				
				
				if($noKntrk=='')
					exit("Error:No. Contract empty");
				if($custId=='')
					exit("Error: Customer empty");
				if($kdBrg=='')
					exit("Error: Item empty");
				if($HrgStn=='')
					exit("Error: Price empty");
				if($tBlg=='')
					exit("Error: Date empty");	
				if($qty=='')
					exit("Error: Qty empty");	
				if($tlgKntrk=='')
					exit("Error: Contract Date empty");	
				if($satuan=='')
					exit("Error: UOM empty");	
				if($kualitas=='')
					exit("Error: Quality empty");	
				if($kualitas=='')
					exit("Error: empty");		
				if($tglKrm=='')
					exit("Error: Delivery Date empty");	
					
				if($tglSd<$tglKrm)
					exit("Error:Delivery Date not valid");
								
				
				
               /* if(($noKntrk=='')||($custId=='')||($kdBrg=='')||($HrgStn=='')||($tBlg=='')||($qty=='')||($tlgKntrk=='')||($satuan=='')||($kualitas=='')||($tglKrm=='')||($tglSd==''))
                {
                        echo"Warning: Please complete the form";
                        exit();
                }*/
				
				
				
                $sCust="select kontakperson from ".$dbname.".pmn_4customer where kodecustomer = '".$custId."'";
                $qCUst=mysql_query($sCust) or die(mysql_error());
                $rCust=mysql_fetch_assoc($qCUst);

                $sUpd="update ".$dbname.".pmn_kontrakjual set  tanggalkontrak='".tanggalsystem($_POST['tlgKntrk'])."', koderekanan='".$custId."', kodebarang='".$kdBrg."', satuan='".$satuan."', hargasatuan='".$HrgStn."', terbilang='".$tBlg."', kualitas='".$kualitas."', 
                       tanggalkirim='".tanggalsystem($_POST['tglKrm'])."', sdtanggal='".tanggalsystem($_POST['tglSd'])."', syratpembayaran='".$syrtByr."', catatan1='".$cttn1."', catatan2='".$cttn2."', catatan3='".$cttn3."', catatan4='".$cttn4."', catatan5='".$cttn5."', standartimbangan='".$tmbngn."'
                       , penandatangan='".$tndTng."', penandatangan2='".$rCust['kontakperson']."', catatanlain='".$othCttn."',  kuantitaskontrak='".$qty."',toleransi='".$tlransi."',nodo='".$noDo."',kodeorg='".$lokasiTugas."',kodept='".$kdPt."',
                       matauang='".$kurs."',ppn='".$ppn."',lamamuat='".$lamamuat."',pelabuhan='".$pelabuhan."',demurage='".$demurage."' 
					   
					   where nokontrak='".$noKntrk."'";// echo"warning:".$sUpd;exit();
                        //exit("Error".$sUpd);
                        if(mysql_query($sUpd))
                        echo"";
                        else
                        echo "DB Error : ".mysql_error($conn);	
                break;
				
				
				
                case'getCust':
                $sCust="select kontakperson,telepon  from ".$dbname.".pmn_4customer where kodecustomer = '".$custId."'";
                $qCUst=mysql_query($sCust) or die(mysql_error());
                $rCust=mysql_fetch_assoc($qCUst);
                echo $rCust['kontakperson']."###".$rCust['telepon'];
                break;

                case'dataDel':
                $sDel="delete from ".$dbname.".pmn_kontrakjual where nokontrak='".$noKntrk."'" ; //echo "warning:".$sDel;
                if(mysql_query($sDel))
                        echo"";
                        else
                        echo "DB Error : ".mysql_error($conn);	
                break;

                case'cariNokntrk':
                if($txtSearch!='')
                {
                        $where=" where nokontrak like '%".$txtSearch."%'";
                }
                else
                {
                        $where="";
                }
                $sCek="select * from ".$dbname.".pmn_kontrakjual  ".$where." order by tanggalkontrak desc"; //echo"warning:".$sCek;

                $qCek=mysql_query($sCek) or die(mysql_error());
                $rCek=mysql_num_rows($qCek);
                if($rCek>0)
                {
                        $limit=10;
                        $page=0;
                        if(isset($_POST['page']))
                        {
                        $page=$_POST['page'];
                        if($page<0)
                        $page=0;
                        }
                        $offset=$page*$limit;

                        $ql2="select count(*) as jmlhrow from ".$dbname.".pmn_kontrakjual ".$where." ";// echo $ql2;
                        $query2=mysql_query($ql2) or die(mysql_error());
                        while($jsl=mysql_fetch_object($query2)){
                        $jlhbrs= $jsl->jmlhrow;
                        }


                        $slvhc="select * from ".$dbname.".pmn_kontrakjual ".$where." order by tanggalkontrak desc limit ".$offset.",".$limit." ";
                        $qlvhc=mysql_query($slvhc) or die(mysql_error());
                        $user_online=$_SESSION['standard']['userid'];
                        while($res=mysql_fetch_assoc($qlvhc))
                        {
                                $sCust="select namacustomer  from ".$dbname.".pmn_4customer where kodecustomer = '".$res['koderekanan']."'"; //echo $sCust;
                                $qCUst=mysql_query($sCust) or die(mysql_error());
                                $rCust=mysql_fetch_assoc($qCUst);

                                $sBrg="select namabarang from ".$dbname.".log_5masterbarang where `kodebarang`='".$res['kodebarang']."'";
                                $qBrg=mysql_query($sBrg) or die(mysql_error());
                                $rBrg=mysql_fetch_assoc($qBrg);

                                $sOrg="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$res['kodept']."'";
                                $qOrg=mysql_query($sOrg) or die(mysql_error());
                                $rOrg=mysql_fetch_assoc($qOrg);

                        $no+=1;
						if($res['posting']=='0')
						{
							$isi="<td>
									<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res['nokontrak']."');\">
									<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$res['nokontrak']."');\" >	<br />
									<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('pmn_kontrakjual','".$res['nokontrak']."','','pmn_kontakjual_pdf',event)\">
									<img src=images/icons/04/10/01.png  title='Posting' class=zImgBtn caption='Posting' onclick=\"posting('".$res['nokontrak']."');\">
								</td>";
						}
						else
						{
							$isi="<td>
									<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('pmn_kontrakjual','".$res['nokontrak']."','','pmn_kontakjual_pdf',event)\">
									<img src=images/icons/04/10/02.png class=zImgBtn>
								</td>";
						}
                        echo"
                                <tr class=rowcontent>
                                <td>".$no."</td>
                                <td>".$res['nokontrak']."</td>
                                <td>".$rOrg['namaorganisasi']."</td>   
                                <td>".$rCust['namacustomer']."</td>
                                <td>".tanggalnormal($res['tanggalkontrak'])."</td>
                                <td>".$res['kodebarang']."</td>
                                <td>".$rBrg['namabarang']."</td>
                                <td>".$res['tanggalkirim']."</td>";
                                echo $isi;
                                echo"</tr>";
                        }
                        echo"
                        <tr class=rowheader><td colspan=9 align=center>
                        ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
                        <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                        <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                        </td>
                        </tr>";
                }
                else
                {
                        echo"<tr class=rowheader><td colspan=9 align=center>Not Found</td></tr>";
                }

                break;

                default:
                break;
        }

?>