<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$namaKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');
$proses=$_POST['proses'];
$kdPabrik=$_POST['kdOrg'];
$kdStatsiun=$_POST['statId'];
$noTrans=$_POST['noTrans'];
$pbrkId=$_POST['pbrkId'];
$shft=$_POST['shft'];
$statid=$_POST['statid'];
$mesinId=$_POST['mesinId'];
$tgl=tanggalsystem($_POST['tgl']);
$jmAwal=substr(tanggalsystemd($_POST['jmAwal']),0,10);
$jmAkhir=substr(tanggalsystemd($_POST['jmAkhir']),0,10);
$kdbrg=$_POST['kdbrg'];
$satuan=$_POST['satuan'];
$jmlhMinta=$_POST['jmlhMinta'];
$ketrngn=$_POST['ketrngn'];
$userOnline=$_SESSION['standard']['userid'];
//$userOnline=$_SESSION['standard']['userid'];
$kegiatan=$_POST['kgtn'];
$keterangan=$_POST['keterangan'];
$pbrikId=$_POST['kdrg'];
//$sttId=$_POST[''];
$jamMulai=$_POST['jamMulai'];
$mntMulai=$_POST['mntMulai'];
$jamSlsi=$_POST['jamSlsi'];
$mntSlsi=$_POST['mntSlsi'];
$jmAwal=$jmAwal." ".$jamMulai.":".$mntMulai;
$jmAkhir=$jmAkhir." ".$jamSlsi.":".$mntSlsi;
//exit("Error".$jmAwal);
        switch($proses)
        {

                case'GetStat':
                //echo"warning:masuk";
                if($kdPabrik!='')
                {
                $sOrg="select kodeorganisasi, namaorganisasi from ".$dbname.".organisasi where induk='".$kdPabrik."'";
                $qOrg=mysql_query($sOrg) or die(mysql_error());
                 $optStat.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                while($rOrg=mysql_fetch_assoc($qOrg))
                {
                        if($statid!='')
                        {
                               //exit("Error:masuk".$statid);

                                $optStat.="<option value=".$rOrg['kodeorganisasi']." ".($rOrg['kodeorganisasi']==$statid?'selected':'').">".$rOrg['namaorganisasi']."</option>";
                        }
                        else
                        {
                                $optStat.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
                        }
                }
                $sShft="select shift from ".$dbname.".pabrik_5shift where kodeorg='".$kdPabrik."' and shift!='0'";
                $qShft=mysql_query($sShft) or die(mysql_error());
                while($rShft=mysql_fetch_assoc($qShft))
                {
                        if($shft!='')
                        {
                                $optShift.="<option value=".$rShft['shift']." ".($rShft['shift']==$shft?'selected':'').">".$rShft['shift']."</option>";
                        }
                        else
                        {
                                $optShift.="<option value=".$rShft['shift'].">".$rShft['shift']."</option>";
                        }
                }

                echo $optStat."###".$optShift;
                }
                else 
                {
                        echo"warning : Organization code is obligatory";	
                }
                break;


                case'GetMsn':
                $sOrg="select kodeorganisasi, namaorganisasi from ".$dbname.".organisasi where induk='".$kdStatsiun."'"; //echo "warning".$sOrg;
                $qOrg=mysql_query($sOrg) or die(mysql_error());
                while($rOrg=mysql_fetch_assoc($qOrg))
                {
                    if($mesinId!='')
                    {
                        $optMsn.="<option value=".$rOrg['kodeorganisasi']." ".($rOrg['kodeorganisasi']==$mesinId?'selected':'').">".$rOrg['namaorganisasi']."</option>";
                    }
                    else
                    {
                        $optMsn.="<option value=".$rOrg['kodeorganisasi']." >".$rOrg['namaorganisasi']."</option>";
                    }
                }
                echo $optMsn;
                break;

                case'CreateNo':
                $jmAwal=explode(" ",$jmAwal);
                $jmAkhir=explode(" ",$jmAkhir);
                if($jmAkhir[0]<$jmAwal[0])
                {
                        echo"warning: Start time must lower then end time";
                        exit();
                }

                $tgl=  date('Ymd');
                $bln = substr($tgl,4,2);
                $thn = substr($tgl,0,4);

                $notransaksi="/".$kdStatsiun."/".date('m')."/".date('Y');
        $ql="select `notransaksi` from ".$dbname.".`pabrik_rawatmesinht` where notransaksi like '%".$notransaksi."%' order by `notransaksi` desc limit 0,1";
        $qr=mysql_query($ql) or die(mysql_error());
        $rp=mysql_fetch_object($qr);
        $awal=substr($rp->notransaksi,0,4);
                //echo "warning:".$awal;exit();
        $awal=intval($awal);
        $cekbln=substr($rp->notransaksi,-7,2);
        $cekthn=substr($rp->notransaksi,-12,4);

        if(($bln!=$cekbln)&&($thn!=$cekthn))
        {
            $awal=1;
        }
        else
        {
                    $awal++;
        }
        $counter=addZero($awal,4);
                $notransaksi=$counter."/".$kdStatsiun."/".$bln."/".$thn;
                echo $notransaksi;
                break;

                case'cekData':
                /*echo"warning:masuk";
                exit();*/
                if(($shft=='')||($statid=='')||($mesinId=='')||($tgl=='')||($kdbrg==''))
                {
                        echo"warning: Please complete the form";
                        exit();
                }
                $sCek="select notransaksi from ".$dbname.".pabrik_rawatmesinht where notransaksi='".$noTrans."'"; //echo "warning:".$sCek;
                $qCek=mysql_query($sCek) or die(mysql_error());
                $rCek=mysql_fetch_row($qCek);
                if($rCek<1)
                {
                        $sIns="insert into ".$dbname.".pabrik_rawatmesinht (notransaksi, pabrik, tanggal, shift, statasiun, mesin, kegiatan, jammulai, jamselesai, updateby, keterangan) 
                        values ('".$noTrans."','".$pbrkId."','".$tgl."','".$shft."','".$statid."','".$mesinId."','".$kegiatan."','".$jmAwal."','".$jmAkhir."','".$userOnline."','".$keterangan."')";
                        
                        //echo"warning:".$sIns;exit();
                        if(mysql_query($sIns))
                        {
                                $sInd="insert into ".$dbname.".pabrik_rawatmesindt (notransaksi, kodebarang, satuan, jumlah, keterangan) values ('".$noTrans."','".$kdbrg."','".$satuan."','".$jmlhMinta."','".$ketrngn."')";
                                if(mysql_query($sInd))
                                echo"";
                                else
                                echo "DB Error : ".mysql_error($conn);
                        }
                        else
                        {
                                echo "DB Error : ".mysql_error($conn);
                        }
                }
                $test=count($_POST['kdbrg']);
                echo $test;
                break;
                case'saveHeader':
                if(($shft=='')||($statid=='')||($mesinId=='')||($tgl==''))
                {
                        echo"warning: Please complete the form";
                        exit();
                }
                $sCek="select notransaksi from ".$dbname.".pabrik_rawatmesinht where notransaksi='".$noTrans."'"; //echo "warning:".$sCek;
                $qCek=mysql_query($sCek) or die(mysql_error());
                $rCek=mysql_fetch_row($qCek);
                if($rCek<1)
                {
                        $sIns="insert into ".$dbname.".pabrik_rawatmesinht (notransaksi, pabrik, tanggal, shift, statasiun, mesin, kegiatan, jammulai, jamselesai, updateby, keterangan) 
                        values ('".$noTrans."','".$pbrkId."','".$tgl."','".$shft."','".$statid."','".$mesinId."','".$kegiatan."','".$jmAwal."','".$jmAkhir."','".$userOnline."','".$keterangan."')";
                        //echo"warning:".$sIns;exit();
                        if(mysql_query($sIns))
                        echo"";
                        else
                        echo "DB Error : ".mysql_error($conn);
                }
                break;
                case'cari_barang':
                        $txtcari=$_POST['txtcari'];
                $str="select a.kodebarang,a.namabarang,a.satuan from
                      ".$dbname.".log_5masterbarang a where a.namabarang like '%".$txtcari."%' or a.kodebarang like '%".$txtcari."'";
                         // echo $str;
                $res=mysql_query($str);
                if(mysql_num_rows($res)<1)
                {
                        echo"Error: ".$_SESSION['lang']['tidakditemukan'];			
                }
                else
                {
                                echo"
                                <fieldset>
                                <legend>".$_SESSION['lang']['result']."</legend>
                                <div style=\"width:450px; height:300px; overflow:auto;\">
                                        <table class=sortable cellspacing=1 border=0>
                                         <thead>
                                                  <tr class=rowheader>
                                                          <td>No</td>
                                                          <td>".$_SESSION['lang']['kodebarang']."</td>
                                                          <td>".$_SESSION['lang']['namabarang']."</td>
                                                          <td>".$_SESSION['lang']['satuan']."</td>
                                                  </tr>
                                         </thead>
                                         <tbody>";
                                        $no=0;	 
                                        while($bar=mysql_fetch_object($res))
                                        {
                                                $no+=1;
                                                echo"<tr class=rowcontent style='cursor:pointer;' title='Click' onclick=\"throwThisRow('".$bar->kodebarang."','".$bar->namabarang."','".$bar->satuan."');\">
                                                   <td>".$no."</td>
                                                  <td>".$bar->kodebarang."</td>
                                                  <td>".$bar->namabarang."</td>
                                                  <td>".$bar->satuan."</td>
                                                  </tr>";			   	
                                        }
                                echo    "
                                                 </tbody>
                                                 <tfoot></tfoot>
                                                 </table></div></fieldset>";	
                }  
                break;
                case'loadData':
                   // exit("Error:X");
                $limit=25;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;

                $ql2="select count(*) as jmlhrow from ".$dbname.".pabrik_rawatmesinht where pabrik='".$_SESSION['empl']['lokasitugas']."' order by tanggal desc";// echo $ql2;
                $query2=mysql_query($ql2) or die(mysql_error());
                while($jsl=mysql_fetch_object($query2)){
                $jlhbrs= $jsl->jmlhrow;
                }

                $slvhc="select * from ".$dbname.".pabrik_rawatmesinht where pabrik='".$_SESSION['empl']['lokasitugas']."'  order by tanggal desc limit ".$offset.",".$limit." ";
                $qlvhc=mysql_query($slvhc) or die(mysql_error());
                $user_online=$_SESSION['standard']['userid'];
                while($rlvhc=mysql_fetch_assoc($qlvhc))
                {
					$no+=1;
					$dtJamMulai=explode(" ",$rlvhc['jammulai']);
					$jamMulai=explode(":",$dtJamMulai[1]);
	
					$dtJamSlsi=explode(" ",$rlvhc['jamselesai']);
					$jamSlsi=explode(":",$dtJamSlsi[1]);
					echo"
					<tr class=rowcontent>
					<td>".$no."</td>
					<td>".$rlvhc['notransaksi']."</td>
					<td>".tanggalnormal($rlvhc['tanggal'])."</td>
					<td>".$rlvhc['shift']."</td>
					<td>".$rlvhc['statasiun']."</td>
					<td>".$rlvhc['mesin']."</td>
					<td>".tanggalnormald($rlvhc['jammulai'])."</td>
					<td>".tanggalnormald($rlvhc['jamselesai'])."</td>
					<td>".$namaKar[$rlvhc['updateby']]."</td>
					<td>";
					if($rlvhc['updateby']==$userOnline  )
					{
						echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['notransaksi']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['pabrik']."','".$rlvhc['shift']."','".$rlvhc['statasiun']."','".$rlvhc['mesin']."','".$rlvhc['kegiatan']."','".tanggalnormal($dtJamMulai[0])."','".tanggalnormal($dtJamSlsi[0])."','".$jamMulai[0]."','".$jamMulai[1]."','".$jamSlsi[0]."','".$jamSlsi[1]."','".$rlvhc['keterangan']."');\">
							<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['notransaksi']."');\" >
							<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('pabrik_rawatmesinht','".$rlvhc['notransaksi']."','','pabrik_slavePemeliharaanPdf',event)\">";
					 } 
					 else 
					 {
						echo"
							<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('pabrik_rawatmesinht','".$rlvhc['notransaksi']."','','pabrik_slavePemeliharaanPdf',event)\">";}
				 
							
        		}//end while
				
				echo"</td></tr>";
		
		
                echo"
                </tr><tr class=rowheader><td colspan=9 align=center>
                ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
                <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                </td>
                </tr>";
                break;
                case'cariTransaksi':

                $limit=20;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;		
                if(isset($_POST['txtSearch']))
                {
                        $txt_search=$_POST['txtSearch'];
                        $txt_tgl=tanggalsystem($_POST['txtTgl']);
                        $txt_tgl_a=substr($txt_tgl,0,4);
                        $txt_tgl_b=substr($txt_tgl,4,2);
                        $txt_tgl_c=substr($txt_tgl,6,2);
                        $txt_tgl=$txt_tgl_a."-".$txt_tgl_b."-".$txt_tgl_c;
                }
                else
                {
                        $txt_search='';
                        $txt_tgl='';			
                }
                        if($txt_search!='')
                        {
                                $where=" notransaksi LIKE  '%".$txt_search."%'";
                        }
                        elseif($txt_tgl!='')
                        {
                                $where.=" tanggal LIKE '".$txt_tgl."'";
                        }
                        elseif(($txt_tgl!='')&&($txt_search!=''))
                        {
                                $where.=" notransaksi LIKE '%".$txt_search."%' and tanggal LIKE '%".$txt_tgl."%'";
                        }
                //echo $strx; exit();
                if($txt_search==''&&$txt_tgl=='')
                {
                        $strx="select * from ".$dbname.".pabrik_rawatmesinht where kodeorg='".$lokasi."' ".$where." order by notransaksi desc limit ".$offset.",".$limit."";
                        $sql="select count(*) jmlhrow from ".$dbname.".pabrik_rawatmesinht 	where  kodeorg='".$lokasi."' ".$where." order by notransaksi desc";			 
                }
                else
                {
                                $strx="select * from ".$dbname.".pabrik_rawatmesinht where ".$where." order by notransaksi desc 
                                limit ".$offset.",".$limit."";
                                $sql="select count(*) jmlhrow from ".$dbname.".pabrik_rawatmesinht where  ".$where." order by notransaksi desc";
                }
                //echo "warning:".$strx; exit();
                $query=mysql_query($sql) or die(mysql_error());
                while($jsl=mysql_fetch_object($query)){
                $jlhbrs= $jsl->jmlhrow;
                }
                        if($res=mysql_query($strx))
                        {
                                $numrows=mysql_num_rows($res);
                                if($numrows<1)
                                {
                                        echo"<tr class=rowcontent><td colspan=9>Not Found</td></tr>";
                                }
                                else
                                {
                                        while($rlvhc=mysql_fetch_assoc($res))
                                        {

                                                $dtJamMulai=explode(" ",$rlvhc['jammulai']);
                                                $jamMulai=explode(":",$dtJamMulai[1]);

                                                $dtJamSlsi=explode(" ",$rlvhc['jamselesai']);
                                                $jamSlsi=explode(":",$dtJamSlsi[1]);
                                                $no+=1;
                                        echo"
                                                <tr class=rowcontent>
                                                <td>".$no."</td>
                                                <td>".$rlvhc['notransaksi']."</td>
                                                <td>".tanggalnormal($rlvhc['tanggal'])."</td>
                                                <td>".$rlvhc['shift']."</td>
                                                <td>".$rlvhc['statasiun']."</td>
                                                <td>".$rlvhc['mesin']."</td>
                                                <td>".tanggalnormald($rlvhc['jammulai'])."</td>
                                                <td>".tanggalnormald($rlvhc['jamselesai'])."</td>
												 <td>".$namaKar[$rlvhc['updateby']]."</td>";
                                               echo"<td>";
												if($rlvhc['updateby']==$userOnline)
												{
													echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['notransaksi']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['pabrik']."','".$rlvhc['shift']."','".$rlvhc['statasiun']."','".$rlvhc['mesin']."','".$rlvhc['kegiatan']."','".tanggalnormal($dtJamMulai[0])."','".tanggalnormal($dtJamSlsi[0])."','".$jamMulai[0]."','".$jamMulai[1]."','".$jamSlsi[0]."','".$jamSlsi[1]."','".$rlvhc['keterangan']."');\">
														<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['notransaksi']."');\" >
														<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('pabrik_rawatmesinht','".$rlvhc['notransaksi']."','','pabrik_slavePemeliharaanPdf',event)\">";
												 } 
												 else 
												 {
													echo"
														<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('pabrik_rawatmesinht','".$rlvhc['notransaksi']."','','pabrik_slavePemeliharaanPdf',event)\">";}
											 
													
											}//end while
				
											echo"</td></tr>";
												  
                                                echo"
                                                </tr><tr class=rowheader><td colspan=9 align=center>
                                                ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
                                                <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                                                <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                                                </td>
                                                </tr>";
                                }
                         }	
                        else
                        {
                                echo "Gagal,".(mysql_error($conn));
                        }	
                break;
				
				
				case'posting':
				
					//postingby	postingdate
					$flag=0;
					$sekarang=date('Y-m-d');
					$x="select kodejabatan from ".$dbname.".sdm_5jabatan
								  where namajabatan like '%ka.%' or namajabatan like '%kepala%' ";
					/*$x="select karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."' and kodejabatan in (select kodejabatan from ".$dbname.".sdm_5jabatan
								  where namajabatan like '%ka.%' or namajabatan like '%kepala%') ";*/			  
					$y=mysql_query($x) or die (mysql_error($conn));
					while($z=mysql_fetch_assoc($y))
					{
						$pos=$z['kodejabatan'];
						echo $pos;
						if($pos==$_SESSION['empl']['kodejabatan'])
						{
							$flag=1;
						}
					}
				//	echo $flag;
					
					if($flag==1)
					{
						$i="update  ".$dbname.".pabrik_rawatmesinht set statPost=1,postingdate='".$sekarang."',postingby='".$_SESSION['standard']['userid']."' where notransaksi='".$noTrans."'";
						//exit("Error:$i");
						if(mysql_query($i))
						{
						}
						else
						echo " Gagal,".addslashes(mysql_error($conn));
					}
					else
					{
						exit("Error:Sory you can't posting this transaction");
					}
				break;
				

                case'deletData':
                $sDel="delete from ".$dbname.".pabrik_rawatmesinht where notransaksi='".$noTrans."'";
                if(mysql_query($sDel))
                {
                        $sdelDet="delete from ".$dbname.".pabrik_rawatmesindt where notransaksi='".$noTrans."'";
                        mysql_query($sdelDet) or die(mysql_error());
                }
                else
                {
                        echo "DB Error : ".mysql_error($conn);
                }
                //$qDel=mysql_query($sDel) or die(mysql_error());
                break;

                case'upDate':
                if(($jmAkhir=='')||($jmAwal=='')||($tgl==''))
                {
                        echo"warning: Please complete the form";
                        exit();
                }
                $sUp="update  ".$dbname.".pabrik_rawatmesinht set kegiatan='".$kegiatan."', jammulai='".$jmAwal."', jamselesai='".$jmAkhir."', tanggal='".$tgl."' where notransaksi='".$noTrans."'";
                mysql_query($sUp) or die(mysql_error());
                break;
                default:
                break;
        }
?>