<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$nmKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');

        $proses=$_POST['proses'];
        $nopp=$_POST['nopp'];
        $tglSdt=tanggalsystem($_POST['tglSdt']);
        $statusPP=$_POST['statusPP'];
        $periode=$_POST['periode'];
        $lokBeli=$_POST['lokBeli'];
        $totalSmaData=$dsetujui=$dtolak=$dmenungguKptsn=$blmDiajukan=$pros=0;

        switch($proses)
        {
                case'getData':
                        $tglSkrng=date("Y-m");
                        $limit=100;
                        $page=0;
                        if(isset($_POST['page']))
                        {
                        $page=$_POST['page'];
                        if($page<0)
                        $page=0;
                        }
                        $offset=$page*$limit;
                        $sLim="SELECT count(*) as jmlhrow FROM ".$dbname.".log_prapodt a left join ".$dbname.".log_prapoht b on a.nopp=b.nopp where substr(b.tanggal,1,7) like '".$tglSkrng."%' order by a.nopp desc";
                        $query2=mysql_query($sLim) or die(mysql_error());
                        while($jsl=mysql_fetch_object($query2)){
                        $jlhbrs= $jsl->jmlhrow;
                        }
                        //$sql="select * from ".$dbname.".log_podt where nopp='".$nopp."'";
                        $sql="select a.*,b.*, a.keterangan as keterangan2 FROM ".$dbname.".log_prapodt a left join ".$dbname.".log_prapoht b on a.nopp=b.nopp where substr(b.tanggal,1,7) like '".$tglSkrng."%'  order by a.nopp desc limit ".$offset.",".$limit."";
                        //echo"warning:".$sql;exit();
                        //echo $sql;
                        $query=mysql_query($sql) or die(mysql_error());
                        $row=mysql_num_rows($query);
                        if($row>0)
                        {
                                $query2=mysql_query($sql) or die(mysql_error());
                                while($res=mysql_fetch_assoc($query2))
                                {
                                        $no+=1;
                                         $tolak=0;
                                         $statpppp=0;
                                         $pros=0;
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
                                                 $tolak=1;
                                            }
                                            else  if($res['status']=='0')
                                            {

                                                $statPp=$_SESSION['lang']['disetujui'].",".$tgl;
                                                $npo="Purchasing Process";
                                                $pros=1;

                                            }
                                        }
                                        else if($res['close']=='1')
                                        {
                                            if(!is_null($res['hasilpersetujuan5']))
                                            {
                                               $tgl=tanggalnormal($res['tglp5']) ;
                                               if($rTgl['hasilpersetujuan5']=='1')
                                               {
                                                    $statPp=$_SESSION['lang']['disetujui'].",".$tgl;
                                               }
                                               else if($res['hasilpersetujuan5']=='0')
                                               {
                                                 $statPp=$_SESSION['lang']['wait_approval'];
                                                 $statpppp=1;
                                               }
                                               else if($res['hasilpersetujuan5']=='3')
                                               {
                                                    $statPp=$_SESSION['lang']['ditolak'].",".$tgl;
                                                     $tolak=1;
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
                                                   $tolak=1;
                                               }
                                               else if($res['hasilpersetujuan4']=='0')
                                               {
                                                   $statPp=$_SESSION['lang']['wait_approval'];
                                                   $statpppp=1;
                                               }
                                            }
                                            else if(!is_null($res['hasilpersetujuan3']))
                                            {
                                                $tgl=tanggalnormal($res['tglp3']);
                                                 if($res['hasilpersetujuan3']=='1')
                                                   {
                                                        $statPp=$_SESSION['lang']['disetujui'].",".$tgl;
                                                   }
                                                   else if($res['hasilpersetujuan3']=='3')
                                                   {
                                                       $statPp=$_SESSION['lang']['ditolak'].",".$tgl;
                                                        $tolak=1;
                                                   }
                                                   else if($res['hasilpersetujuan3']=='0')
                                                   {
                                                       $statPp=$_SESSION['lang']['wait_approval'];
                                                       $statpppp=1;
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
                                                       $tolak=1;
                                                   }
                                                   else if($res['hasilpersetujuan2']=='0')
                                                   {
                                                       $statPp=$_SESSION['lang']['wait_approval'];
                                                       $statpppp=1;
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
                                                       $tolak=1;
                                                   }
                                                   else if($res['hasilpersetujuan1']=='0')
                                                   {
                                                       $statPp=$_SESSION['lang']['wait_approval'];
                                                       $statpppp=1;
                                                   }
                                            }
                                        }
                                        else if(($res['close']==0)||($res['close']==''))
                                        {
                                                //$statBrg="Belum Diajukan";
                                                $statPp=$_SESSION['lang']['belumdiajukan'];
                                        }


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
				
                                if(!is_null($rDet['nopo'])||$rDet['nopo']!='')
                                {

                                        $tglA=substr($rPo2['tanggal'],0,4);
                                        $tglB=substr($rPo2['tanggal'],5,2);
                                        $tglC=substr($rPo2['tanggal'],8,2);
                                        $tgl2=$tglA.$tglB.$tglC;

                                        $tGl1=substr($res['tanggal'],0,4);
                                        $tGl2=substr($res['tanggal'],5,2);
                                        $tGl3=substr($res['tanggal'],8,2);
                                        $tgl2=$tglA.$tglB.$tglC;
                                        $tgl1 =$tGl1.$tGl2.$tGl3;

                                        $stat=1;
                                        $nopo=$rPo2['nopo'];

                                }
                                else
                                {	
                                        //echo"B";
                                        $tGl1=substr($res['tanggal'],0,4);
                                        $tGl2=substr($res['tanggal'],5,2);
                                        $tGl3=substr($res['tanggal'],8,2);
                                        //$tgl1 = $bar->tanggal;
                                        $tgl1 =$tGl1.$tGl2.$tGl3;
                                        $Tgl2 = date('Y-m-d');			
                                        $tglA=substr($Tgl2,0,4);
                                        $tglB=substr($Tgl2,5,2);
                                        $tglC=substr($Tgl2,8,2);	
                                        $tgl2=$tglA.$tglB.$tglC;	

                                        $stat=0;	
                                        $nopo="Blm PO";				
                                }

                        $starttime=strtotime($tgl1);//time();// tanggal sekarang
                        $endtime=strtotime($tgl2);//tanggal pembuatan dokumen
                        $timediffSecond = abs($endtime-$starttime);
                        $base_year = min(date("Y", $tGl1), date("Y", $tglA));
                        $diff = mktime(0, 0, $timediffSecond, 1, 1, $base_year);
                        $jmlHari=date("j", $diff) - 1;

                                        //cek status barang dan tanggal
                                        $sSup="select namasupplier from ".$dbname.".log_5supplier where supplierid='".$rPo2['kodesupplier']."'";
                                        $qSup=mysql_query($sSup) or die(mysql_error());
                                        $rSup=mysql_fetch_assoc($qSup);
                                        if(($tolak!=0)||($statpppp!=0)||($pros!=0))
                                        {
                                            $npo="";
                                            $tglPO="";
                                            $statPo="";
                                            $rSup['namasupplier']="";
                                            $rRapb['notransaksi']="";
                                            $rRapb['tanggal']="0000-00-00";
                                        }
                                        if($rDet['nopo']!='')
                                        {
                                            $sRapb="select notransaksi,tanggal from ".$dbname.".log_transaksi_vw 
                                                    where nopo='".$rDet['nopo']."' and kodebarang='".$res['kodebarang']."'";
                                            $qRapb=mysql_query($sRapb) or die(mysql_error());
                                            $rRapb=mysql_fetch_assoc($qRapb);



                                            if($rPo2['tanggal']!=0000-00-00)
                                            {$tglPO=tanggalnormal($rPo2['tanggal']);}

                                            if($rPo2['statuspo']==3)
                                            {
                                                $tglR="";
                                                $statPo=$_SESSION['lang']['disetujui'].",".$tgl;
                                                if($rRapb['notransaksi']!='')
                                                {
                                                    $tglR=tanggalnormal($rRapb['tanggal']);
                                                    $statPo="Brg Sdh Di gudang ,".$tglR;
                                                }   
                                            }
                                            else if($rPo2['statuspo']==2)
                                            {
                                                    $accept=0;
                                                    for($i=1;$i<4;$i++)
                                                    {
                                                            if($rPo2['hasilpersetujuan'.$i]==2)
                                                            {
                                                                    $accept=2;
                                                                    $tgl=tanggalnormal($rPo2['tglp'.$i]);
                                                                    break;
                                                            }
                                                            elseif($rPo2['hasilpersetujuan'.$i]==1)
                                                            {
                                                                    $accept=1;

                                                            }
                                                    }
                                                    if($accept==2) {
                                                            //echo"<td colspan=3>".$_SESSION['lang']['ditolak']."</td>";
                                                            $statPo=$_SESSION['lang']['ditolak'].",".$tgl;
                                                    } elseif($accept==1) {
                                                            //echo"<td colspan=3>".$_SESSION['lang']['disetujui']."</td>";
                                                            $statPo=$_SESSION['lang']['disetujui'].",".$tgl;
                                                    }

                                            }
                                            else if($rPo2['statuspo']==1)
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
                                                                                    $statPo=$_SESSION['lang']['persetujuan']." ".$j.", ".$_SESSION['lang']['ditolak'].$tgl;
                                                                            }
                                                                            elseif($rPo2['hasilpersetujuan'.$j]==1)
                                                                            {
                                                                                    $statPo=$_SESSION['lang']['persetujuan']." ".$j.", ".$_SESSION['lang']['disetujui'].$tgl;
                                                                            }
                                                                    }
                                                                    break;
                                                             }
                                                      }

                                            }
                                            else if($rPo2['statuspo']==0)
                                            {

                                                    $statPo="";
                                            }
                                        }





                        //periksa chat==================================
                        $strChat="select * from ".$dbname.".log_pp_chat where 
                                  kodebarang='".$res['kodebarang']."' and nopp='".$res['nopp']."'";
                        $resChat=mysql_query($strChat);
//			echo mysql_error($conn);
                        if(mysql_num_rows($resChat)>0)
                        {
                                $ingChat="<img src='images/chat1.png' onclick=\"loadPPChat('".$res['nopp']."','".$res['kodebarang']."',event);\" class=resicon>";
								$ingChat.="<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_pp_chat','".$res['nopp'].",".$res['tanggal'].",".$res['kodebarang']."','','log_pp_chat_pdf',event)\">";
								//indra
								// <td style=\"cursor:pointer;\" title='PDF Detail PP' onclick=\"masterPDF('log_prapoht','".$res['nopp']."','','log_slave_print_log_pp',event);\">".$tulisannya."</td>
                        }		  
                        else

                        {
                                $ingChat="<img src='images/chat0.png'  onclick=\"loadPPChat('".$res['nopp']."','".$res['kodebarang']."',event);\" class=resicon>";
								//$ingChat.="<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_pp_chat','".$res['nopp'].",".$res['tanggal'].",".$res['kodebarang']."','','log_pp_chat_pdf',event)\">";
						}
                        //===============================================
                                      
                                       if($rDet['nopo']!='')
                                        {
                                            $res['lokalpusat']==0?$npo=$rDet['nopo']:$npo=$rDet['nopo'];
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
                                        if($tolak!=0)
                                        {
                                             $npo="";
                                             $tglPO="";
                                             $statPo="";
                                             $rSup['namasupplier']="";
                                             $rRapb['notransaksi']="";
                                             $rRapb['tanggal']="0000-00-00";
                                        }
                                        $tulisannya=$rBrg['namabarang'];
                                        if(substr($res['kodebarang'],0,3)=='800')$tulisannya.=' '.$res['keterangan2'];
										
										
										
										$x="select sum(jumlahpesan) as jumlahpesan from ".$dbname.".log_po_vw where nopp='".$res['nopp']."' and kodebarang='".$res['kodebarang']."' group by nopp,kodebarang";
										$y=mysql_query($x) or die (mysql_error($conn));
										$z=mysql_fetch_assoc($y);
										
										$a="select sum(jumlah) as jumlah from ".$dbname.".log_transaksi_vw where nopo='".$res['nopo']."' and kodebarang='".$res['kodebarang']."' group by nopo,kodebarang";
										$b=mysql_query($a) or die (mysql_error($conn));
										$c=mysql_fetch_assoc($b);
										
										
										
										
										
										
										
										
                                        echo"<tr class=rowcontent >
                                                <td style=\"cursor:pointer;\" title='Detail PP' onclick=\"previewDetail('".$res['nopp']."',event);\">".$no."</td>
                                                <td style=\"cursor:pointer;\" title='Detail PP' onclick=\"previewDetail('".$res['nopp']."',event);\">".$res['nopp']."</td>
                                                <td>".tanggalnormal($res['tanggal'])."</td>
                                                <td style=\"cursor:pointer;\" title='PDF Detail PP' onclick=\"masterPDF('log_prapoht','".$res['nopp']."','','log_slave_print_log_pp',event);\">".$tulisannya."</td>
                                                <td align='right'>".$res['jumlah']."</td>
                                                <td>".$rBrg['satuan']."</td>";
                                                echo"<td>".$statPp."</td>
                                                <td align=center>".$jmlHari."</td>
                                                <td align=center>".$ingChat."</td>
                                                <td>".$npo."</td>
                                                <td>".$tglPO."</td>";
                                                $qwe=$statPo;
                                                if(trim($rRapb['notransaksi'])=='')$qwe="";//indra
                                                echo"
												<td>".$qwe."</td>
												<td align=center>".$nmKar[$res['purchaser']]."</td>
												<td align=center>".$z['jumlahpesan']."</td>
												<td align=center>".$c['jumlah']."</td>
												";		
                                                echo"<td>".$rSup['namasupplier']."</td>
                                                <td>".$rRapb['notransaksi']."</td>";
                                                if(($rRapb['tanggal']!=0000-00-00))
                                                {
                                                        echo"<td>".tanggalnormal($rRapb['tanggal'])."</td>";
                                                }
                                                else
                                                {
                                                        echo"<td></td>";
                                                }
                                                echo"<td><img onclick=\"previewDetail('".$res['nopp']."',event);\" title=\"Detail PP\" class=\"resicon\" src=\"images/zoom.png\"><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_prapoht','".$res['nopp']."','','log_slave_print_log_pp',event);\"></td></tr>";
                                }
                                echo"
                                 <tr><td colspan=14 align=center>
                                ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
                                <br />
                                <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                                <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                                </td>
                                </tr>";
                        }
                        else
                        {
                                echo"<tr class=rowcontent><td colspan=16 align=center>Not Found</td></tr>";		
                        }
                
                break;
				
				
				
				
				
				
				
				
				
                case'cariData':
                if(($nopp=='')&&($tglSdt=='')&&($statusPP=='')&&($periode=='')&&($lokBeli=='')&&($_POST['supplier_id']=='')&&($_POST['txNmbrg']==''))
                {
                    $tglSkrng=date("Y-m");	
                    $sql="select a.*,b.* FROM ".$dbname.".log_prapodt a left join ".$dbname.".log_prapoht b on a.nopp=b.nopp where substr(b.tanggal,1,7)='".$tglSkrng."'  order by a.nopp desc ";
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
                               if($periode=='')

                            {
                                exit("Error: Periode Tidak Boleh Kosong");
                            }
                             else {
                                 $where="where  a.create_po!=''  and substr(b.tanggal,1,7) like '".$periode."%' ";
                            }
                            }

                        }
                        elseif($statusPP=='4')
                        {
                            if($tglSdt=='')
                            {
                                if($periode=='')
                                {
                                    exit("Error: Periode Tidak Boleh Kosong");
                                }
                                else {
                                    $where="where  a.create_po=''  and substr(b.tanggal,1,7) like '".$periode."%'";
                                }
                            }

                        }
                        elseif(($statusPP=='1')||($statusPP!='2'))
                         {
                            if($tglSdt=='')
                            {
                            if($periode!='')
                            {$where=" where b.close='".$statusPP."' and substr(b.tanggal,1,7) like '".$periode."%'";  }
                            else
                            {$where.=" and b.close='".$statusPP."'";}      
                            }
                        }
                    }
                    elseif($periode!='')
                    {
                        if($tglSdt=='')
                        {
                        $where=" where substr(b.tanggal,1,7) like '".$periode."%'";
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
                    if($_POST['supplier_id']!='')
                    {
                        //exit("Error:masuk");
                        $where.=" and a.nopp in (select distinct nopp from ".$dbname.".log_po_vw where kodesupplier='".$_POST['supplier_id']."')";
                    }
                    if($_POST['txNmbrg']!='')
                    {
                        $where.=" and kodebarang in (select distinct kodebarang from ".$dbname.".log_5masterbarang where namabarang like '%".$_POST['txNmbrg']."%')";
                    }

                    $sql="select a.*,b.* FROM ".$dbname.".log_prapodt a left join ".$dbname.".log_prapoht b on a.nopp=b.nopp  ".$where." order by a.nopp desc ";
                }
                   //exit("Error".$sql);

                        //echo $sql;
                        $query=mysql_query($sql) or die(mysql_error());
                        $row=mysql_num_rows($query);
                        if($row>0)
                        {
                                $query2=mysql_query($sql) or die(mysql_error());
                                while($res=mysql_fetch_assoc($query2))
                                {
                                        $no+=1;
                                        $dtolak=0;
                                        $statpppp=0;
                                        $blmDiajukan=0;
                                        //get data nopp lokalpusat
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
                                                $dtolak=1;
                                            }
                                            else  if($res['status']=='0')
                                            {
                                                $statPp=$_SESSION['lang']['disetujui'].",".$tgl;
                                                $npo="Purchasing Process";
                                                $pros=1;
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
                                                 $statpppp=1;
                                               }
                                               else if($res['hasilpersetujuan5']=='3')
                                               {
                                                    $statPp=$_SESSION['lang']['ditolak'].",".$tgl;
                                                    $dtolak+=1;
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
                                               }
                                               else if($res['hasilpersetujuan4']=='0')
                                               {
                                                   $statPp=$_SESSION['lang']['wait_approval'];
                                                   $npo="";
                                                   $statpppp=1;
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
                                                       $dtolak+=1;
                                                   }
                                                   else if($res['hasilpersetujuan3']=='0')
                                                   {
                                                       $statPp=$_SESSION['lang']['wait_approval'];  
                                                       $npo="";
                                                       $statpppp=1;
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
                                                   }
                                                   else if($res['hasilpersetujuan2']=='0')
                                                   {
                                                       $statPp=$_SESSION['lang']['wait_approval']; 
                                                       $npo="";
                                                       $statpppp=1;
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
                                                   }
                                                   else if($res['hasilpersetujuan1']=='0')
                                                   {
                                                       $statPp=$_SESSION['lang']['wait_approval']; 
                                                       $npo="";
                                                       $statpppp=1;
                                                   }
                                            }
                                        }
                                        else if(($res['close']==0)||($res['close']==''))
                                        {
                                                //$statBrg="Belum Diajukan";
                                                $statPp=$_SESSION['lang']['belumdiajukan'];
                                                 $npo="";
                                                $blmDiajukan=1;
                                        }


                                        //get data barang
                                        $sBrg="select satuan,namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$res['kodebarang']."'";
                                        $qBrg=mysql_query($sBrg) or die(mysql_error());
                                        $rBrg=mysql_fetch_assoc($qBrg);

                                        //get data po and all related					
                                        $sDet="select nopo from ".$dbname.".log_podt  where nopp='".$res['nopp']."' and kodebarang='".$res['kodebarang']."'"; 
                                        $qDet=mysql_query($sDet) or die(mysql_error());
                                        $rDet=mysql_fetch_assoc($qDet);



                                                $nopo="";

                                                $sPo2="select * from ".$dbname.".log_poht  where nopo='".$rDet['nopo']."'"; 
                                                $qPo2=mysql_query($sPo2) or die(mysql_error());
                                                $rPo2=mysql_fetch_assoc($qPo2);
                                                $rCek=mysql_num_rows($qPo2);
                                        //if($rCek<1)
//					{
//						$arrDt=array("0"=>"Pusat","1"=>"Lokal");
//						$nopo=$arrDt[$res['lokalpusat']];
//					}
//					else
//					{
//						$nopo=$rPo2['nopo'];
//					}
                                                $tglPO='';

                                        if($rDet['nopo']!='')
                                        {
                                            if($rPo2['tanggal']!=0000-00-00)
                                            {$tglPO=tanggalnormal($rPo2['tanggal']);}

                                            if($rPo2['statuspo']==3)
                                            {
                                                    $tglR=tanggalnormal($rPo2['tglrelease']);
                                                    $statPo=" PO has been receipt in warehouse ,".$tglR;
                                            }
                                            else if($rPo2['statuspo']==2)
                                            {
                                                    $accept=0;
                                                    for($i=1;$i<4;$i++)
                                                    {
                                                            if($rPo2['hasilpersetujuan'.$i]==2)
                                                            {
                                                                    $accept=2;
                                                                    $tgl=tanggalnormal($rPo2['tglp'.$i]);
                                                                    break;
                                                            }
                                                            elseif($rPo2['hasilpersetujuan'.$i]==1)
                                                            {
                                                                    $accept=1;

                                                            }
                                                    }
                                                    if($accept==2) {
                                                            //echo"<td colspan=3>".$_SESSION['lang']['ditolak']."</td>";
                                                            $statPo=$_SESSION['lang']['ditolak'].",".$tgl;

                                                    } elseif($accept==1) {
                                                            //echo"<td colspan=3>".$_SESSION['lang']['disetujui']."</td>";
                                                            $statPo=$_SESSION['lang']['disetujui'].",".$tgl;

                                                    }

                                            }
                                            else if($rPo2['statuspo']==1)
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
                                                                                    $statPo=$_SESSION['lang']['persetujuan']." ".$j.", ".$_SESSION['lang']['ditolak'].$tgl;
                                                                            }
                                                                            elseif($rPo2['hasilpersetujuan'.$j]==1)
                                                                            {
                                                                                    $statPo=$_SESSION['lang']['persetujuan']." ".$j.", ".$_SESSION['lang']['disetujui'].$tgl;
                                                                            }
                                                                    }
                                                                    break;
                                                             }
                                                      }

                                            }
                                            else if($rPo2['statuspo']==0)
                                            {

                                                    $statPo="";
                                            }


                                            $sSup="select namasupplier from ".$dbname.".log_5supplier where supplierid='".$rPo2['kodesupplier']."'";
                                            $qSup=mysql_query($sSup) or die(mysql_error());
                                            $rSup=mysql_fetch_assoc($qSup);

                                            $sRapb="select notransaksi,tanggal from ".$dbname.".log_transaksi_vw
                                                    where nopo='".$rPo2['nopo']."' and kodebarang='".$res['kodebarang']."'";
                                            $qRapb=mysql_query($sRapb) or die(mysql_error());
                                            $rRapb=mysql_fetch_assoc($qRapb);
                                        }
                                   if($res['nopo']!='')
                                   {
                                        //echo"warning:A";
                                        //$rPoDet=mysql_fetch_assoc($qPoDet);
                                        $sPo="select tanggal from ".$dbname.".log_poht where nopo='".$res['nopo']."'";
                                        //echo $sPo;
                                        $qPo=mysql_query($sPo) or die(mysql_error());
                                        $rPo=mysql_fetch_assoc($qPo);

                                        $tglA=substr($rPo['tanggal'],0,4);
                                        $tglB=substr($rPo['tanggal'],5,2);
                                        $tglC=substr($rPo['tanggal'],8,2);
                                        $tgl2=$tglA.$tglB.$tglC;

                                        $tGl1=substr($res['tanggal'],0,4);
                                        $tGl2=substr($res['tanggal'],5,2);
                                        $tGl3=substr($res['tanggal'],8,2);
                                        $tgl2=$tglA.$tglB.$tglC;
                                        $tgl1 =$tGl1.$tGl2.$tGl3;

                                        $stat=1;
                                        //$nopo=$rPoDet['nopo'];

                                }
                                else
                                {	
                                        //echo"B";
                                        $tGl1=substr($res['tanggal'],0,4);
                                        $tGl2=substr($res['tanggal'],5,2);
                                        $tGl3=substr($res['tanggal'],8,2);
                                        //$tgl1 = $bar->tanggal;
                                        $tgl1 =$tGl1.$tGl2.$tGl3;
                                        $Tgl2 = date('Y-m-d');			
                                        $tglA=substr($Tgl2,0,4);
                                        $tglB=substr($Tgl2,5,2);
                                        $tglC=substr($Tgl2,8,2);	
                                        $tgl2=$tglA.$tglB.$tglC;	

                                        $stat=0;					
                                }

                        $starttime=strtotime($tgl1);//time();// tanggal sekarang
                        $endtime=strtotime($tgl2);//tanggal pembuatan dokumen
                        $timediffSecond = abs($endtime-$starttime);
                        $base_year = min(date("Y", $tGl1), date("Y", $tglA));
                        $diff = mktime(0, 0, $timediffSecond, 1, 1, $base_year);
                        $jmlHari=date("j", $diff) - 1;

                                        if($rDet['nopo']!='')
                                        {
                                            $res['lokalpusat']==0?$npo=$rDet['nopo']:$npo=$rDet['nopo'];
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
                        //periksa chat==================================
                        $strChat="select * from ".$dbname.".log_pp_chat where 
                                  kodebarang='".$res['kodebarang']."' and nopp='".$res['nopp']."'";
                        $resChat=mysql_query($strChat);
//			echo mysql_error($conn);
                        if(mysql_num_rows($resChat)>0)
                        {
                                $ingChat="<img src='images/chat1.png' onclick=\"loadPPChat('".$res['nopp']."','".$res['kodebarang']."',event);\" class=resicon>";
								$ingChat.="<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_pp_chat','".$res['nopp'].",".$res['tanggal'].",".$res['kodebarang']."','','log_pp_chat_pdf',event)\">";
                        }		  
                        else

                        {
                                $ingChat="<img src='images/chat0.png'  onclick=\"loadPPChat('".$res['nopp']."','".$res['kodebarang']."',event);\" class=resicon>";
                        		//$ingChat.="<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_pp_chat','".$res['nopp'].",".$res['tanggal'].",".$res['kodebarang']."','','log_pp_chat_pdf',event)\">";
						}
                        //===============================================
                                if($res['hasilpersetujuan1']==3 or $res['hasilpersetujuan2']==3 or $res['hasilpersetujuan3']==3 or $res['hasilpersetujuan4']==3 or $res['hasilpersetujuan5']==3 or $res['status']==3)
                                {
                                   $npo=''; 
                                }	
								
										$x="select sum(jumlahpesan) as jumlahpesan from ".$dbname.".log_po_vw where nopp='".$res['nopp']."' and kodebarang='".$res['kodebarang']."' group by nopp,kodebarang";
										$y=mysql_query($x) or die (mysql_error($conn));
										$z=mysql_fetch_assoc($y);
										
										$a="select sum(jumlah) as jumlah from ".$dbname.".log_transaksi_vw where nopo='".$res['nopo']."' and kodebarang='".$res['kodebarang']."' group by nopo,kodebarang";
										$b=mysql_query($a) or die (mysql_error($conn));
										$c=mysql_fetch_assoc($b);

                                        echo"<tr class=rowcontent>
                                                <td style=\"cursor:pointer;\" title='Detail PP' onclick=\"previewDetail('".$res['nopp']."',event);\">".$no."</td>
                                                <td style=\"cursor:pointer;\" title='Detail PP' onclick=\"previewDetail('".$res['nopp']."',event);\">".$res['nopp']."</td>
                                                <td>".tanggalnormal($res['tanggal'])."</td>
                                                <td style=\"cursor:pointer;\" title='PDF Detail PP' onclick=\"masterPDF('log_prapoht','".$res['nopp']."','','log_slave_print_log_pp',event);\">".$rBrg['namabarang']."</td>
                                                <td align='right'>".$res['jumlah']."</td>
                                                <td>".$rBrg['satuan']."</td>";
                                                echo"<td>".$statPp."</td>
												
												
												
                                                <td align=center>".$jmlHari."</td>
                                                <td align=center>".$ingChat."</td>
                                                <td>".$npo."</td>
                                                <td>".$tglPO."</td>";
                                                $qwe=$statPo;
                                                if(trim($rRapb['notransaksi'])=='')$qwe="";
                                                echo"<td>".$qwe."</td>
												
												
													<td align=center>".$nmKar[$res['purchaser']]."</td>
												<td align=center>".$z['jumlahpesan']."</td>
												<td align=center>".$c['jumlah']."</td>
												
												";		
                                                echo"<td>".$rSup['namasupplier']."</td>
                                                <td>".$rRapb['notransaksi']."</td>";
                                                if(($rRapb['tanggal']!=0000-00-00))
                                                {
                                                        echo"<td>".tanggalnormal($rRapb['tanggal'])."</td>";
                                                }
                                                else
                                                {
                                                        echo"<td></td>";
                                                }
                                                echo"<td><img onclick=\"previewDetail('".$res['nopp']."',event);\" title=\"Detail PP\" class=\"resicon\" src=\"images/zoom.png\"><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_prapoht','".$res['nopp']."','','log_slave_print_log_pp',event);\"></td></tr>";

                                }


                        }
                        else
                        {
                                echo"<tr class=rowcontent><td colspan=16 align=center>Not Found</td></tr>";		
                        }
                        echo"<tr class=rowcontent><td colspan=16 align=left><table cellpadding=1 cellspacing=1 border=0 class=sortable>";
                                echo"<thead><tr class=rowheader>";
                                echo"<td>Purchased</td>";
                                echo"<td>".$_SESSION['lang']['ditolak']."</td>";
                                echo"<td>".$_SESSION['lang']['wait_approval']."</td>";
                                echo"<td>".$_SESSION['lang']['belumdiajukan']."</td>";
                                echo"<td>Purchsing Process</td>";
                                echo"<td>Total</td>";
                                echo"</thead><tbody>";
                                $totalSmaData=$dtolak+$dmenungguKptsn+$blmDiajukan+($pros-$dsetujui)+$dsetujui;
                                echo"<tr class=rowcontent>";
                                echo"<td align=right>".$dsetujui."</td>";
                                echo"<td align=right>".$dtolak."</td>";
                                echo"<td align=right>".$dmenungguKptsn."</td>";
                                echo"<td align=right>".$blmDiajukan."</td>";
                                echo"<td align=right>".($pros-$dsetujui)."</td>";
                                echo"<td align=right>".$totalSmaData."</td>";
                                echo"</tr>";
                                echo"</tbody></table></tr>";
                break;


                default:
                break;
        }
?>