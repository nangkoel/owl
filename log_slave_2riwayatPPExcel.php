<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$nmKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',"bagian in ('PUR','LOG')");


/*	$nopp=$_GET['nopp'];
        $kdorg=explode("/",$nopp);*/

/*	print"<pre>";
        print_r($_GET);
        print"<pre>";*/
 $totalSmaData=$dsetujui=$dtolak=$dmenungguKptsn=$blmDiajukan=$pros=0;
//======================================
$proses = $_GET['proses'];
$nopp=$_GET['nopp'];
$tglSdt=tanggalsystem($_GET['tglSdt']);
$statusPP=$_GET['statPP'];
$periode1=$_GET['periodedari'];
$periode2=$_GET['periodesampai'];
$lokBeli=$_GET['lokBeli'];  	
//ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'";
$namapt='COMPANY NAME';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
        $namapt=strtoupper($bar->namaorganisasi);
}
                $stream.="<table  cellspacing='1' border='0'>
                                <tr><td colspan=10  align=center>".strtoupper($_SESSION['lang']['riwayatPP'])."</td></tr>
                                <tr><td colspan=3  align='left'>".$_SESSION['lang']['user'].":".$_SESSION['standard']['username']."</td></tr>
                                <tr><td colspan=3  align='left'>".$_SESSION['lang']['tanggal'].":".date('d-m-Y H:i:s')."</td></tr></table>
                                <table  cellspacing='1' border='1'>
                                <tr>
                                <td bgcolor=#DEDEDE align=center>No.</td>
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nopp']."</td>
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']."</td>
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodebarang']."</td>
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namabarang']."</td>
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['satuan']."</td>
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jumlah']."</td> 
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jumlahrealisasi']."</td>    
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['keterangan']."</td>    
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['status']."</td>
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nopo']."</td>
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tgl_po']."</td>
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['status']." </td>
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namasupplier']."</td>
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['purchaser']."</td>
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['rapbNo']."</td>
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']."</td>
                                </tr></table><table  cellspacing='1' border='1'>";

					if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
				{
				}
				else
				{
					$sortLokasi="and a.nopp like '%".$_SESSION['empl']['lokasitugas']."%'";
				}
				
				//print_r($_SESSION['empl']['tipelokasitugas']);
                if(($nopp=='')&&($tglSdt=='')&&($statusPP=='')&&($periode1=='')&&($periode2=='')&&($lokBeli=='')&&($_GET['supplier_id']=='')&&($_GET['txNmbrg']=='')&&($_GET['purchsrId']=='0000000000'))
                {
                    $tglSkrng=date("Y-m");	
                    //$sql="select a.*,b.* FROM ".$dbname.".log_prapodt a left join ".$dbname.".log_prapoht b on a.nopp=b.nopp where substr(b.tanggal,1,7)='".$tglSkrng."' ".$sortLokasi."  order by a.nopp desc ";
                    $sql="select a.nopp, a.kodebarang, a.jumlah, a.realisasi, a.hargasatuan, a.keterangan AS keterangan_detail, " .
                            "a.tgl_sdt, a.create_po, a.pembelian, a.lokalpusat, a.status, a.tglalokasi, a.alasanstatus, a.purchaser, a.ditolakoleh, a.satuanrealisasi, " .
                            "b.* FROM ".$dbname.".log_prapodt a left join ".$dbname.".log_prapoht b on a.nopp=b.nopp where substr(b.tanggal,1,7)='".$tglSkrng."' ".$sortLokasi."  order by a.nopp desc ";
                    
                }
                else
                {
                    if($tglSdt!='')
                    {
                        $where=" where b.tanggal='".$tglSdt."'";
                    }
                    else
                    {
                        //$thn=date("Y");
                        $where=" where a.nopp!=''";
                    }
                    if($statusPP!='')
                    {
                        if($statusPP=='3')
                        {
                           if($tglSdt=='')
                            {
                               if($periode1=='' && $periode2=='')

                            {
                                exit("Error: Periode Tidak Boleh Kosong");
                            }
                             else {
                                 $where="where  a.create_po!=''  and b.tanggal between '".$periode1."-01' and '".$periode2."-31' ";
                            }
                            }

                        }
                        elseif($statusPP=='4')
                        {
                            if($tglSdt=='')
                            {
                                if($periode1=='' && $periode2=='')
                                {
                                    exit("Error: Periode Tidak Boleh Kosong");
                                }
                                else {
                                    $where="where  a.create_po=''  and b.tanggal between '".$periode1."-01' and '".$periode2."-31'";
                                }
                            }

                        }
                        elseif(($statusPP=='1')||($statusPP!='2'))
                        {
                            if($tglSdt=='')
                            {
								if($periode1!='' && $periode2!='')
								{
									$where=" where b.close='".$statusPP."' and b.tanggal between '".$periode1."-01' and '".$periode2."-31'";  
								}
                            else
                            {
								$where.=" and b.close='".$statusPP."'";}      
                            }
                        }						
		        elseif($statusPP=='2')
                        {
                            //indra update 10 februari 2014
			    //penambahan statusPP untuk 2 di query
                            if($tglSdt=='')
                            {
								if($periode1!='' && $periode2!='')
								{
									$where=" where b.close='2' and b.tanggal between '".$periode1."-01' and '".$periode2."-31'";
								}
                            else
                            {
								$where.=" and b.close='2'";}      
                            }
                        }
						//tutup update indra
						
						
                    }
                    elseif($periode1!='' && $periode2!='')
                    {
                        if($tglSdt=='')
                        {
                            $where.=" and b.tanggal between '".$periode1."-01' and '".$periode2."-31' ";
                        }
                    }
                    if($lokBeli!='')
                    {
                        $where.=" and lokalpusat= '".$lokBeli."'";
                    }
                    if($nopp!='')
                    {
                        $where.=" and b.nopp like '%".$nopp."%'";
                    }
                    if($_GET['supplier_id']!='')
                    {
                        //exit("Error:masuk");
                        $where.=" and a.nopp in (select distinct nopp from ".$dbname.".log_po_vw where kodesupplier='".$_GET['supplier_id']."')";
                    }
                    if($_GET['txNmbrg']!='')
                    {
                        $where.=" and kodebarang in (select distinct kodebarang from ".$dbname.".log_5masterbarang where namabarang like '%".$_GET['txNmbrg']."%')";
                    }
                    if($_GET['purchsrId']!='0000000000'){
                        $where.=" and purchaser='".$_GET['purchsrId']."'";
                    }

                    //$sql="select a.*,b.* FROM ".$dbname.".log_prapodt a left join ".$dbname.".log_prapoht b on a.nopp=b.nopp  ".$where." ".$sortLokasi." order by a.nopp desc ";
                    $sql="select a.nopp, a.kodebarang, a.jumlah, a.realisasi, a.hargasatuan, a.keterangan AS keterangan_detail, " .
                            "a.tgl_sdt, a.create_po, a.pembelian, a.lokalpusat, a.status, a.tglalokasi, a.alasanstatus, a.purchaser, a.ditolakoleh, a.satuanrealisasi, " .
                            "b.* FROM ".$dbname.".log_prapodt a left join ".$dbname.".log_prapoht b on a.nopp=b.nopp  ".$where." ".$sortLokasi." order by a.nopp desc ";
                    
                }
                //exit("error:".$sql);
                $query=mysql_query($sql) or die(mysql_error());
                        $row=mysql_num_rows($query);
                        if($row>0)
                        {
                                $query2=mysql_query($sql) or die(mysql_error());
                                while($res=mysql_fetch_assoc($query2))
                                {
                                        $no+=1;
                                        //get data nopp
                                        $dtolak=0;
                                        //$sPp="select * from ".$dbname.".log_prapoht where nopp='".$res['nopp']."'";
                                        //$qPp=mysql_query($sPp) or die(mysql_error());
                                        //$rPp=mysql_fetch_assoc($qPp);
                                         if($res['close']=='2')
                                        {
                                            //$sTgl="SELECT DISTINCT `tglp1` , `tglp2` , `tglp3` , `tglp4` , `tglp5` FROM ".$dbname.".`log_prapoht` WHERE nopp='".$res['nopp']."'";
                                            //$qTgl=mysql_query($sTgl) or die(mysql_error($qTgl));
                                            //$rTgl=mysql_fetch_assoc($qTgl);
                                            if(!is_null($res['tglp5']))
                                            {
                                               $tgl=tanggalnormal($res['tglp5']) ;
                                            }
                                            else if(!is_null($res['tglp4']))
                                            {
                                                $tgl=tanggalnormal($res['tglp4']) ;
                                            }
                                            else if(!is_null($res['tglp3']))
                                            {
                                                $tgl=tanggalnormal($res['tglp3']) ;
                                            }
                                            else if(!is_null($res['tglp2']))
                                            {
                                                $tgl=tanggalnormal($res['tglp2']) ;
                                            }
                                            else if(!is_null($res['tglp1']))
                                            {
                                                $tgl=tanggalnormal($res['tglp1']) ;
                                            }
                                            if($res['status']=='3')
                                            {
                                                $statPp=$_SESSION['lang']['ditolak'].",".$tgl;
                                                $npo="";
                                                $dtolak+=1;
                                            }
                                            else  if($res['status']=='0')
                                            {

                                                $statPp=$_SESSION['lang']['disetujui'].",".$tgl;
                                                $npo="Purchasing Process";
                                                $pros+=1;
                                            }
                                        }
                                        else if($res['close']=='1')
                                        {
                                           //  $sTgl="SELECT DISTINCT `tglp1` , `tglp2` , `tglp3` , `tglp4` , `tglp5`,hasilpersetujuan1,hasilpersetujuan2,hasilpersetujuan3,hasilpersetujuan4,hasilpersetujuan5 
                                           //         FROM ".$dbname.".`log_prapoht` WHERE nopp='".$res['nopp']."'";
                                           // $qTgl=mysql_query($sTgl) or die(mysql_error($qTgl));
                                           // $rTgl=mysql_fetch_assoc($qTgl);
                                            if(!is_null($res['hasilpersetujuan5']))
                                            {
                                               $tgl=tanggalnormal($res['tglp5']) ;
                                               if($res['hasilpersetujuan5']=='1')
                                               {
                                                    $statPp=$_SESSION['lang']['disetujui'].",".$tgl;
                                               }
                                               else if($res['hasilpersetujuan5']=='0')
                                               {
                                                 $statPp=$_SESSION['lang']['wait_approval'];  
                                                   $npo="";
                                                   $dmenungguKptsn+=1;
                                               }
                                               else if($res['hasilpersetujuan5']=='3')
                                               {
                                                    $statPp=$_SESSION['lang']['ditolak'].",".$tgl;
                                                    $dtolak=1;
                                                     $npo="";
                                               }
                                            }
                                            else if(!is_null($res['hasilpersetujuan4']))
                                            {
                                                $tgl=tanggalnormal($res['tglp4']) ;
                                               if($res['hasilpersetujuan4']=='1')
                                               {
                                                    $statPp=$_SESSION['lang']['disetujui'].",".$tgl;
                                               }
                                               else if($res['hasilpersetujuan4']=='3')
                                               {
                                                   $statPp=$_SESSION['lang']['ditolak'].",".$tgl;
                                                   $dtolak=1;
                                                    $npo="";
                                               }
                                               else if($res['hasilpersetujuan4']=='0')
                                               {
                                                   $statPp=$_SESSION['lang']['wait_approval']; 
                                                   $npo="";
                                                   $dmenungguKptsn+=1;
                                               }
                                            }
                                            else if(!is_null($rTgl['hasilpersetujuan3']))
                                            {
                                                $tgl=tanggalnormal($res['tglp3']);
                                                 if($res['hasilpersetujuan3']=='1')
                                                   {
                                                        $statPp=$_SESSION['lang']['disetujui'].",".$tgl;
                                                   }
                                                   else if($res['hasilpersetujuan3']=='3')
                                                   {
                                                       $statPp=$_SESSION['lang']['ditolak'].",".$tgl;
                                                       $dtolak=1;
                                                   }
                                                   else if($res['hasilpersetujuan3']=='0')
                                                   {
                                                       $statPp=$_SESSION['lang']['wait_approval']; 
                                                       $npo="";
                                                       $dmenungguKptsn+=1;
                                                   }
                                            }
                                            else if(!is_null($res['hasilpersetujuan2']))
                                            {
                                                $tgl=tanggalnormal($res['tglp2']) ;
                                                if($res['hasilpersetujuan2']=='1')
                                                   {
                                                        $statPp=$_SESSION['lang']['disetujui'].",".$tgl;
                                                   }
                                                   else if($res['hasilpersetujuan2']=='3')
                                                   {
                                                       $statPp=$_SESSION['lang']['ditolak'].",".$tgl;
                                                       $dtolak=1;
                                                        $npo="";
                                                   }
                                                   else if($res['hasilpersetujuan2']=='0')
                                                   {
                                                       $statPp=$_SESSION['lang']['wait_approval']; 
                                                        $npo="";
                                                       $dmenungguKptsn+=1;
                                                   }
                                            }
                                            else if(!is_null($res['hasilpersetujuan1']))
                                            {
                                                $tgl=tanggalnormal($res['tglp1']) ;
                                                if($res['hasilpersetujuan1']=='1')
                                                   {
                                                        $statPp=$_SESSION['lang']['disetujui'].",".$tgl;
                                                   }
                                                   else if($res['hasilpersetujuan1']=='3')
                                                   {
                                                       $statPp=$_SESSION['lang']['ditolak'].",".$tgl;
                                                       $dtolak=1;
                                                       $npo="";
                                                   }
                                                   else if($res['hasilpersetujuan1']=='0')
                                                   {
                                                       $statPp=$_SESSION['lang']['wait_approval'];  
                                                       $dmenungguKptsn+=1;
                                                        $npo="";
                                                   }
                                            }
                                        }
                                        else if(($res['close']==0)||($res['close']==''))
                                        {
                                                //$statBrg="Belum Diajukan";
                                                $statPp=$_SESSION['lang']['belumdiajukan'];
                                                 $npo="";
                                                $blmDiajukan+=1;
                                        }


                                        $statPo='';//default
                                        //get data barang
                                        $sBrg="select namabarang,satuan from ".$dbname.".log_5masterbarang where kodebarang='".$res['kodebarang']."'";
                                        $qBrg=mysql_query($sBrg) or die(mysql_error());
                                        $rBrg=mysql_fetch_assoc($qBrg);

                                        //get data po and all related					
                                        $sDet="select nopo from ".$dbname.".log_podt  where nopp='".$res['nopp']."' and kodebarang='".$res['kodebarang']."'"; 
                                        $qDet=mysql_query($sDet) or die(mysql_error());
                                        $rDet=mysql_fetch_assoc($qDet);

                                        $sPo2="select * from ".$dbname.".log_poht  where nopo='".$rDet['nopo']."'"; 
                                        $qPo2=mysql_query($sPo2) or die(mysql_error());
                                        $rPo2=mysql_fetch_assoc($qPo2);
                                        $tglPO='';
                                        if($rDet['nopo']!='')
                                        {
                                            if($rPo2['tanggal']!=0000-00-00)
                                            {$tglPO=tanggalnormal($rPo2['tanggal']);}

                                            $sSup="select namasupplier from ".$dbname.".log_5supplier where supplierid='".$rPo2['kodesupplier']."'";
                                            $qSup=mysql_query($sSup) or die(mysql_error());
                                            $rSup=mysql_fetch_assoc($qSup);

                                            $sRapb="select notransaksi,tanggal from ".$dbname.".log_transaksi_vw 
                                                    where nopo='".$rPo2['nopo']."' and kodebarang='".$res['kodebarang']."'";
                                            $qRapb=mysql_query($sRapb) or die(mysql_error());
                                            $rRapb=mysql_fetch_assoc($qRapb);
                                            if($rPo2['statuspo']=='3')
                                            {
                                                $tglR="";
                                                $statPo=$_SESSION['lang']['disetujui'].",".$tgl;
                                                if($rRapb['notransaksi']!='')
                                                {
                                                    $tglR=tanggalnormal($rRapb['tanggal']);
                                                    $statPo="Brg Sdh Di gudang ,".$tglR;
                                                }   
                                            }
                                            else if($rPo2['statuspo']=='2')
                                            {
                                                    $accept=0;
                                                    for($i=1;$i<4;$i++)
                                                    {
                                                            if($rPo2['hasilpersetujuan'.$i]=='2')
                                                            {
                                                                    $accept=2;
                                                                    $tgl=tanggalnormal($rPo2['tglp'.$i]);
                                                                    break;
                                                            }
                                                            elseif($rPo2['hasilpersetujuan'.$i]=='1')
                                                            {
                                                                    $accept=1;

                                                            }
                                                    }
                                                    if($accept=='2') {
                                                            //echo"<td colspan=3>".$_SESSION['lang']['ditolak']."</td>";
                                                            $statPo=$_SESSION['lang']['ditolak'].",".$tgl;
                                                    } elseif($accept=='1') {
                                                            //echo"<td colspan=3>".$_SESSION['lang']['disetujui']."</td>";
                                                            $statPo=$_SESSION['lang']['disetujui'].",".$tgl;
                                                    }

                                            }
                                            else if($rPo2['statuspo']=='1')
                                            {
                                                    for($i=1;$i<4;$i++)
                                                    {
                                                            if($rPo2['tglp'.$i]=='')
                                                            {
                                                                    $j=$i-1;
                                                                    if($j!=0)
                                                                    {
                                                                            $tgl=tanggalnormal($rPo2['tglp'.$j]);
                                                                            if($rPo2['hasilpersetujuan'.$j]==2)
                                                                            {
                                                                                    $statPo="Persetujuan".$j.", ".$_SESSION['lang']['ditolak'].$tgl;
                                                                            }
                                                                            elseif($rPo2['hasilpersetujuan'.$j]==1)
                                                                            {
                                                                                    $statPo="Persetujuan".$j.", ".$_SESSION['lang']['disetujui'].$tgl;
                                                                            }
                                                                    }
                                                                    break;
                                                             }
                                                      }

                                            }
                                            else if($rPo2['statuspo']=='0')
                                            {

                                                    $statPo="Approval Process";
                                            }

                                        }

                                        if($rDet['nopo']!='')
                                        {
                                                $res['lokalpusat']==0?$npo=$rPo2['nopo']:$npo=$rPo2['nopo'];
                                                $dsetujui+=1;
                                        }
                                        else
                                        {
                                                $npo="";
                                                $tglPO="";
                                                $statPo="";
                                                $rSup['namasupplier']="";
                                                $rRapb['notransaksi']="";
                                                $rRapb['tanggal']="0000-00-00";
                                        }
                               if($res['hasilpersetujuan1']==3 or $res['hasilpersetujuan2']==3 or $res['hasilpersetujuan3']==3 or $res['hasilpersetujuan4']==3 or $res['hasilpersetujuan5']==3 or $res['status']==3)
                                {
                                   $npo=''; 
                                }



                                        $stream.="<tr class=rowcontent>
                                                <td>".$no."</td>
                                                <td>".$res['nopp']."</td>
                                                <td>".tanggalnormal($res['tanggal'])."</td>
                                                <td>".$res['kodebarang']."</td>
                                                <td>".$rBrg['namabarang']."</td>
                                                <td>".$rBrg['satuan']."</td>
                                                <td align=right>".$res['jumlah']."</td>
                                                <td align=right>".$res['realisasi']."</td>
                                                <td align=left>".$res['keterangan_detail']."</td>
                                                ";
                                                $stream.="<td>".$statPp."</td>";

                                                $stream.="
                                                <td>".$npo."</td>
                                                <td>".$tglPO."</td>";
                                                $stream.="<td>".$statPo."</td>";		
                                                $stream.="<td>".$rSup['namasupplier']."</td>";
                                                $stream.="<td>".$nmKar[$res['purchaser']]."</td>
                                                <td>".$rRapb['notransaksi']."</td>";
                                                if(($rRapb['tanggal']!=0000-00-00))
                                                {
                                                        $stream.="<td>".tanggalnormal($rRapb['tanggal'])."</td></tr>";
                                                }
                                                else
                                                {
                                                        $stream.="<td></td></tr>";
                                                }
                                }
                                $stream.="</table>";
                        }
                        else
                        {
                                $stream.="<tr class=rowcontent><td colspan=16 align=center>Not Found</td></tr></table>";		
                        }
                         $stream.="<tr class=rowcontent><td colspan=16 align=left><table cellpadding=1 cellspacing=1 border=1 class=sortable>";
                                $stream.="<thead><tr class=rowheader>";
                                $stream.="<td  bgcolor=#DEDEDE align=center>Purchased</td>";
                                $stream.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['ditolak']."</td>";
                                $stream.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['wait_approval']."</td>";
                                $stream.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['belumdiajukan']."</td>";
                                $stream.="<td bgcolor=#DEDEDE align=center>Purchasing Process</td>";
                                $stream.="<td bgcolor=#DEDEDE align=center>Total</td>";
                                $stream.="</thead><tbody>";
                                $totalSmaData=$dtolak+$dmenungguKptsn+$blmDiajukan+($pros-$dsetujui)+$dsetujui;
                                $stream.="<tr class=rowcontent>";
                                $stream.="<td align=right>".$dsetujui."</td>";
                                $stream.="<td align=right>".$dtolak."</td>";
                                $stream.="<td align=right>".$dmenungguKptsn."</td>";
                                $stream.="<td align=right>".$blmDiajukan."</td>";
                                $stream.="<td align=right>".($pros-$dsetujui)."</td>";
                                $stream.="<td align=right>".$totalSmaData."</td>";
                                $stream.="</tr>";
                                $stream.="</tbody></table></tr>";


        //echo "warning:".$strx;
//=================================================

        $stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	

$nop_="ReportRiwayatPermintaanBarang".date('YmdHis');
if(strlen($stream)>0)
{
     $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
     gzwrite($gztralala, $stream);
     gzclose($gztralala);
     echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls.gz';
        </script>";
}
?>