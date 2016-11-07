<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
require_once('config/connection.php');
include_once('lib/zLib.php');


        if(isset($_POST['rnopp']))
        {
                $nopp=$_POST['rnopp'];
                $induk_org=substr($nopp,15,4);
                $kode_org=$_POST['kode_org'];
                $str="SELECT * FROM ".$dbname.".`log_prapoht` WHERE `nopp`='".$nopp."'";

                  if($res=mysql_query($str))
                  {
                        echo"<table class=data cellspacing=1 border=0>
                                 <thead>
                                 <tr><td colspan=3>Pengajuan Ke</td></tr>
                                 </thead>
                                 <tbody>";
                        $no=0;	 

                        while($bar=mysql_fetch_object($res))
                        {

                                $sq="select * ".$dbname.".`datakaryawan` where `lokasitugas`='".$bar->kodeorg."' or `induk`='".$induk_org."'"; //echo $sql;
                                $qty=mysql_query($sq) or die(mysql_error());
                                $opt.="<option value='".$res2->karyawanid."'>".$res2->namakaryawan."</option>";	

                                $no+=1;
                     echo"
                                <tr class=rowcontent style='cursor:pointer;' onclick=\"setDraft( '".$bar->karyawanid."','".$bar->nopp."')\" title='Click' >
                                          <td class=firsttd colspan=3>".$no."</td>
                                                <tr >
                                                        <td>No. PP</td>
                                                        <td>:</td>
                                                        <td><input id=nopp type=text readonly=readonly value=".$bar->nopp."></td>
                                                </tr>
                                                <tr>
                                                        <td>Nama Karyawan</td>
                                                        <td>:</td>
                                                        <td>
                                                        <select id='kd_krywn'>
                                                        <option value=\"\" selected=selected></option>".$opt."
                                                        </select></td>
                                                </tr>";

                         }
                        echo"</tbody>
                                  <tfoot>
                                  </tfoot>
                                  </table>";	  	
                  }	
                  else
                {
                        echo " Gagal,".addslashes(mysql_error($conn));
                }	
        }
        if(isset($_POST['rkrywn_id']))
        {	

                $rkrywn_id=intval($_POST['rkrywn_id']);
                $rkrywn_id=addZero($rkrywn_id,10);
                $no_pp=$_POST['no_pp'];
                $tanggl=date("Y-m-d");
                $ql="update ".$dbname.".log_prapoht set `persetujuan1`='".$rkrywn_id."',`close`='1',`tglp1`='".$tanggl."' where `nopp`='".$no_pp."' ";//	 echo $ql; exit();
                 if($res=mysql_query($ql))
                  {
                  }
                  else
                  {
                        echo " Gagal,".addslashes(mysql_error($conn));
                }	

        }
        if(isset($_POST['hnopp']))
        {
                $nopp=$_POST['hnopp'];
                $krywn=$_POST['karywn_id'];
                $str="select * from ".$dbname.".`log_prapoht` where `nopp`='".$nopp."'"; //echo $str;
                   if($res=mysql_query($str))
                  {
                        echo"<table class=data cellspacing=1 border=0>
                                 <thead>
                                 <tr><td colspan=6>Status Persetujuan</td></tr>
                                 <tr class=rowheader>
                                 <td class=firsttd>
                                 No.
                                 </td>
                                 <td>Nama Karyawan</td>
                                 <td>Jabatan</td>
                                 <td>Lokasi Tugas</td>
                                 <td>Keputusan</td>
                             <td>Catatan</td>
                                 </tr>
                                 </thead>
                                 <tbody>";
                        $no=0;	 
                        while($bar=mysql_fetch_object($res))
                        {

                                $where = "(karyawanid='".$bar->persetujuan1."' OR ".
                                "karyawanid='".$bar->persetujuan2."' OR ".
                                "karyawanid='".$bar->persetujuan3."' OR ".
                                "karyawanid='".$bar->persetujuan4."' OR ".
                                "karyawanid='".$bar->persetujuan5."')AND lokasitugas='".$bar->kodeorg."'" ;
                                $sql="select * from ".$dbname.".`datakaryawan` where ".$where; //echo $sql;
                                $query=mysql_query($sql) or die(mysql_error());
                                $res3=mysql_fetch_object($query);

                                $sql2="select * from ".$dbname.".`sdm_5jabatan` where kodejabatan='".$res3->kodejabatan."'";
                                $query2=mysql_query($sql2) or die(mysql_error());
                                $res2=mysql_fetch_object($query2);


                                if($bar->hasilpersetujuan1 ==''||$bar->hasilpersetujuan2 ==''||$bar->hasilpersetujuan3 ==''||$bar->hasilpersetujuan4==''||$bar->hasilpersetujuan5=='')
                                {
                                        $b="Not Processed";
                                }elseif($bar->hasilpersetujuan1 =='1'||$bar->hasilpersetujuan2 =='1'||$bar->hasilpersetujuan3 =='1'||$bar->hasilpersetujuan4=='1'||$bar->hasilpersetujuan5=='1')
                                {
                                        $b="Approved";
                                }elseif($bar->hasilpersetujuan1 =='2'||$bar->hasilpersetujuan2 =='2'||$bar->hasilpersetujuan3 =='2'||$bar->hasilpersetujuan4=='2'||$bar->hasilpersetujuan5=='2')
                                {
                                        $b="Must Corrected";
                                }elseif($bar->hasilpersetujuan1 =='3'||$bar->hasilpersetujuan2 =='3'||$bar->hasilpersetujuan3 =='3'||$bar->hasilpersetujuan4=='3'||$bar->hasilpersetujuan5=='3')
                                {
                                        $b="Rejected";
                                }
                                $no+=1;
                                echo"<tr class=rowcontent style='cursor:pointer;' onclick=\"setDraft('".$bar->karyawanid."','".$bar->nopp."')\" title='Click' >
                                          <td class=firsttd>".$no."</td>
                                          <td>".$res3->namakaryawan."</td>
                                          <td>".$res2->namajabatan."</td>
                                          <td>".$res3->lokasitugas."</td>
                                          <td>".$b."</td>
                                          <td>".$bar->keterangan."</td>
                                         </tr>";
                        }	 
                        echo "</tbody>
                                  <tfoot>
                                  </tfoot>
                                  </table>";	   	
                  }	
                  else
                {
                        echo " Gagal,".addslashes(mysql_error($conn));
                }	
        }
        if(isset($_POST['method'])=='cari_pp')
        {
        $limit=50;
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
                        $txt_tgl=tanggalsystem($_POST['tglCari']);
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
                                $where="and nopp LIKE  '%".$txt_search."%'";
                                $where2="nopp LIKE  '%".$txt_search."%'";
                        }
                        elseif($txt_tgl!='')
                        {
                                $where.="and tanggal LIKE '".$txt_tgl."'";
                                $where2.="and tanggal LIKE '".$txt_tgl."'";

                        }
                        elseif(($txt_tgl!='')&&($txt_search!=''))
                        {
                                $where.="and nopp LIKE '%".$txt_search."%' and tanggal LIKE '%".$txt_tgl."%'";
                                $where2.=" nopp LIKE '%".$txt_search."%' and tanggal LIKE '%".$txt_tgl."%'";
                        }
                //echo $strx; exit();
                if($txt_search==''&&$txt_tgl=='')
                {
                        if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
                        {
                            $sCek="select bagian from ".$dbname.".datakaryawan where karyawanid='".$_SESSION['standard']['userid']."'";
                            $qCek=mysql_query($sCek) or die(mysql_error($conn));
                            $rCek=mysql_fetch_assoc($qCek);
                            if($rCek['bagian']=='PUR'||$rCek['bagian']=='AGR')
                            {
                                 $sql="select count(*) jmlhrow from ".$dbname.".log_prapoht order by tanggal desc";
                                $str="select * from ".$dbname.".log_prapoht order by tanggal desc limit ".$offset.",".$limit."";
                            }
                            else
                            {
                                $sql="select count(*) jmlhrow from ".$dbname.".log_prapoht where dibuat='".$_SESSION['standard']['userid']."'  order by tanggal desc";
                                $str="select * from ".$dbname.".log_prapoht where  dibuat='".$_SESSION['standard']['userid']."' order by tanggal desc limit ".$offset.",".$limit." ";
                            }

                        }
                        else
                        {
                                $str="select * from ".$dbname.".log_prapoht where substring(nopp,16,4)='".$_SESSION['empl']['lokasitugas']."'  order by tanggal desc limit ".$offset.",".$limit."";

                        }			 
                }
                else
                {
                        if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
                        {
                            $sCek="select bagian from ".$dbname.".datakaryawan where karyawanid='".$_SESSION['standard']['userid']."'";
                            $qCek=mysql_query($sCek) or die(mysql_error($conn));
                            $rCek=mysql_fetch_assoc($qCek);
                            if($rCek['bagian']=='PUR'||$rCek['bagian']=='PUR')
                            {
                                $sql="select count(*) jmlhrow from ".$dbname.".log_prapoht where ".$where2." order by tanggal desc";
                                $str="select * from ".$dbname.".log_prapoht where ".$where2." order by tanggal desc limit ".$offset.",".$limit."";
                            }
                            else
                            {
                                $sql="select count(*) jmlhrow from ".$dbname.".log_prapoht where dibuat='".$_SESSION['standard']['userid']."' ".$where." order by tanggal desc";
                                $str="select * from ".$dbname.".log_prapoht where  dibuat='".$_SESSION['standard']['userid']."' ".$where." order by tanggal desc limit ".$offset.",".$limit." ";
                            }

                        }
                        else
                        {
                             $sql="select count(*) jmlhrow from ".$dbname.".log_prapoht where substring(nopp,16,4)='".$_SESSION['empl']['lokasitugas']."' ".$where." order by tanggal desc";
                            $str="select * from ".$dbname.".log_prapoht where substring(nopp,16,4)='".$_SESSION['empl']['lokasitugas']."' ".$where." order by tanggal limit ".$offset.",".$limit."";

                        }

                }
                $query=mysql_query($sql) or die(mysql_error());
                while($jsl=mysql_fetch_object($query)){
                $jlhbrs= $jsl->jmlhrow;
                }
                        if($res=mysql_query($str))
                        {
                                $numrows=mysql_num_rows($res);
                                if($numrows<1)
                                {
                                        echo"<tr class=rowcontent><td colspan=8>Not Found</td></tr>";
                                }
                                else
                                {
                while($bar=mysql_fetch_assoc($res))
                {
                        $koderorg=substr($bar['nopp'],15,4);
                        $spr="select * from  ".$dbname.".organisasi where  kodeorganisasi='".$koderorg."' or induk='".$koderorg."'"; //echo $spr;
                        $rep=mysql_query($spr) or die(mysql_error($conn));
                        $bas=mysql_fetch_assoc($rep);
                        $cekPt=substr($bar->nopp,12,4);
                        $skry="select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$bar['dibuat']."'";
                        $qkry=mysql_query($skry) or die(mysql_error());
                        $rkry=mysql_fetch_assoc($qkry);
                        $no+=1;

                        if($bar['close']=='0')
                        {

                                $b="<a href=# id=seeprog onclick=frm_ajun('".$bar['nopp']."','".$bar['close']."') title=\"Click To Change The Status \">Need Approval</a>";
                        }
                        elseif($bar['close']=='1')
                        {
                                for($i=0;$i<6;$i++)
                                {
                                        if($bar['persetujuan'.$i]!='' and $bar['hasilpersetujuan'.$i]==0)
                                        {
                                            $nmkry=getNamaKaryawan($bar['persetujuan'.$i]);
                                            $b="<a href=# id=seeprog onclick=frm_ajunemail('".$bar['nopp']."','".$bar['persetujuan'.$i]."','".str_replace(" ", "_", $nmkry)."') title=\"".$_SESSION['lang']['wait_approval'].": ".$nmkry."\">".$_SESSION['lang']['wait_approval']."</a>";
                                        }
                                }
                            
                        }
                        elseif($bar['close']=='2')
                        {
                                for($i=0;$i<6;$i++)
                                {
                                        if($bar['hasilpersetujuan'.$i]==1)
                                        {	
                                                $b="<a href=# id=seeprog  title=\"Can Make PO\">Approved</a>";
                                        }
                                        elseif($bar['hasilpersetujuan'.$i]==3)
                                        {
                                                $b="<a href=# id=seeprog  title=\"Can`t Make PO\">Rejected</a>";
                                        }
                                }
                        }
                        $ed_kd_org=substr($bar['nopp'],15,4);
                        echo"<tr class=rowcontent id='tr_".$no."'>
                                  <td>".$no."</td>
                                  <td>".$bar['nopp']."</td>
                                  <td>".tanggalnormal($bar['tanggal'])."</td>
                                  <td>".$bas['namaorganisasi']."</td>
                                  <td>".$rkry['namakaryawan']."</td>
                                  <td>".$b."</td>";
                          if($bar['dibuat']==$_SESSION['standard']['userid'])
                                  {
                                        echo"<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar['nopp']."','".tanggalnormal($bar['tanggal'])."','".$ed_kd_org."','".$bar['close']."');\" >
                                        <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPp('".$bar['nopp']."','".$bar['close']."');\" >";
                                        echo"<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_prapoht','".$bar['nopp']."','','log_slave_print_log_pp',event);\">
                                            <img onclick=\"previewDetail('".$bar['nopp']."',event);\" title=\"Detail PP\" class=\"resicon\" src=\"images/zoom.png\"><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_prapoht','".$bar['nopp']."','','log_slave_print_log_pp',event);\"></td>
                                 ";
                                  }
                                  else
                                  {
                                  echo"<td><img onclick=\"previewDetail('".$bar['nopp']."',event);\" title=\"Detail PP\" class=\"resicon\" src=\"images/zoom.png\"><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_prapoht','".$bar['nopp']."','','log_slave_print_log_pp',event);\"></td>";
                                        }
                                                echo"</tr>";
                                        }
                                  echo"</tr>
                                 <tr><td colspan=7 align=center>
                                ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
                                <br />
                                <button class=mybutton onclick=cariData(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                                <button class=mybutton onclick=cariData(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                                </td>
                                </tr>";
                                } 		 	   	
                         }	
                        else
                        {
                                echo "Gagal,".(mysql_error($conn));
                        }	
        }
        if(isset($_POST['proses'])=='show_all_data')
        {
                 if($_SESSION['empl']['tipeinduk']=='HOLDING')
                {
                $str="select * from ".$dbname.".log_prapoht where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by nopp desc";

          }
          else
          {
                $str="select * from ".$dbname.".log_prapoht where kodeorg='".substr($_SESSION['empl']['lokasitugas'],0,4)."' order by nopp desc";
          }
          if($res=mysql_query($str))
          {
                while($bar=mysql_fetch_assoc($res))
                {
                        $koderorg=$bar['kodeorg'];
                        $spr="select * from  ".$dbname.".organisasi where  kodeorganisasi='".$koderorg."' or induk='".$koderorg."'"; //echo $spr;
                        $rep=mysql_query($spr) or die(mysql_error($conn));
                        $bas=mysql_fetch_assoc($rep);
                        $cekPt=substr($bar->nopp,12,4);
                        $no+=1;

                        if($bar['close']=='0')
                        {

                                $b="<a href=# id=seeprog onclick=frm_ajun('".$bar['nopp']."','".$bar['close']."') title=\"Click To Change The Status \">Need Approval</a>";
                        }
                        elseif($bar['close']=='1')
                        {
                                $b="<a href=# id=seeprog onclick=frm_ajun('".$bar['nopp']."','".$bar['close']."') title=\"Waiting Approval\">Waiting Approval</a>";
                        }
                        elseif($bar['close']=='2')
                        {
                                for($i=0;$i<6;$i++)
                                {
                                        if($bar['hasilpersetujuan2'.$i]==1)
                                        {	
                                                $b="<a href=# id=seeprog  title=\"Can Make PO\">Approved</a>";
                                        }
                                        elseif($bar['persetujuan'.$i]==3)
                                        {
                                                $b="<a href=# id=seeprog  title=\"Can Make PO\">Rejected</a>";
                                        }
                                }
                        }
                        echo"<tr class=rowcontent id='tr_".$no."'>
                                  <td>".$no."</td>
                                  <td>".$bar['nopp']."</td>
                                  <td>".tanggalnormal($bar['tanggal'])."</td>
                                  <td>".$bas['namaorganisasi']."</td>
                                  <td>".$b."</td>
                         <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar['nopp']."','".tanggalnormal($bar['tanggal'])."','".$bar['kodeorg']."','".$bar['close']."');\"></td>
                                  <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPp('".$bar['nopp']."','".$bar['close']."');\"></td>
                                  <td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_prapoht','".$bar['nopp']."','','log_slave_print_log_pp',event);\"></td>
                                 </tr>";
                }	 	
          }	
          else
                {
                        echo " Gagal,".(mysql_error($conn));
                }	
        }
        if(isset($_POST['proses'])=='cek_data_header')
        {

            if($_POST['cknopp']=='')
            {
                echo"Warning:Please Enter The System Properly";
                exit();
            }
            else
            {
                    $nopp=$_POST['cknopp'];
                    $tgl=tanggalsystem($_POST['tgl_pp']);


                    $kodeorg=$_POST['kd_org'];
                    $id_user=$_POST['user_id'];

                    $sorg="select alokasi from ".$dbname.".organisasi where kodeorganisasi='".$kodeorg."'";
                    $qorg=mysql_query($sorg) or die(mysql_error());
                    $rorg=mysql_fetch_assoc($qorg);

                    $kd_org=$rorg['alokasi'];
                    foreach($_POST['kdbrg']	as $rey=>$Opr)
                    {
                    $tgl_sdt=tanggalsystem($_POST['rtgl_sdt'][$rey]);
                    $starttime=strtotime($_POST['tgl_pp']);//time();// tanggal sekarang
                    $endtime=strtotime($_POST['rtgl_sdt'][$rey]);//tanggal pembuatan dokumen
                    $timediff = $endtime-$starttime;
                    $days=intval($timediff/86400);
                    //echo "Warning :".$days."---".$starttime."--".$endtime."--".$tgl_sdt;exit();
                    if(($Opr=='')||($_POST['rjmlhDiminta'][$rey]=='')|| ($tgl_sdt<$tgl) || $days<7)									
                    {
                    echo "Warning : Data Tidak Boleh Kosong Dan Tanggal Tidak Boleh Lebih Kecil Dari Tanggal PP, Min 7 Hari Dari Tanggal PP";
                    exit();
                    }
                    else
                    {
                    $sql="select * from ".$dbname.".log_prapoht where `nopp`='".$nopp."'"; //echo $sql;
                    $query=mysql_query($sql) or die(mysql_error());
                    $res=mysql_fetch_row($query);
                    //echo $res; exit();
                    if($res<1)
                    {
                    $ins="insert into ".$dbname.".log_prapoht (`nopp`,`kodeorg`,`tanggal`,`dibuat`,`catatan`) values ('".$nopp."','".$kd_org."','".$tgl."','".$id_user."','".$_POST['catatan']."')";
                    //exit("Error:$ins");
				    $qry=mysql_query($ins) or die(mysql_error());
                    $sql2="select * from ".$dbname.".log_prapodt where `nopp`='".$nopp."'";
                    $query2=mysql_query($sql2) or die(mysql_error());
                    $res2=mysql_fetch_row($query2);
                    if($res2<1)
                    {
                            foreach($_POST['kdbrg'] as $row=>$Act)
                            {

                                            $kdbrg=$Act;
                                            $nmbrg=$_POST['nmbrg'][$row];
                                            $rjmlhDiminta=$_POST['rjmlhDiminta'][$row];
                                            $rkd_angrn=$_POST['rkd_angrn'][$row];
                                            $rtgl_sdt=tanggalsystem($_POST['rtgl_sdt'][$row]);
                                            $ketrng=$_POST['ketrng'][$row];
                                            $sSat="select satuan from ".$dbname.".log_5masterbarang where kodebarang='".$data['kd_brg']."'";
                                            $qSat=mysql_query($sSat) or die(mysql_error());
                                            $rSat=mysql_fetch_assoc($qSat);
                                            $sqp="insert into ".$dbname.".log_prapodt(`nopp`, `kodebarang`, `jumlah`,`kd_anggran`,`tgl_sdt`,`keterangan`,`satuanrealisasi`) values('".$nopp."','".$kdbrg."','".$rjmlhDiminta."','".$rkd_angrn."','".$rtgl_sdt."','".$ketrng."','".$rSat['satuan']."')"; //echo $sqp; exit();
                                                    if(!mysql_query($sqp))
                                                    {
                                                            //echo $sqp; 
                                                            echo "Gagal,".(mysql_error($conn));exit();
                                                    }			
                            }
                            $test=count($_POST['kdbrg']);
                            echo $test;
                            }
                    }
            }
                }
            }
        }
?>