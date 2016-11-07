<?php
        require_once('master_validation.php');
        include('lib/nangkoelib.php');
        require_once('config/connection.php');
        include_once('lib/zLib.php');
        $method=$_POST['method'];
        isset($_POST['nopp']) ? $nopp=$_POST['nopp'] : null;
        isset($_POST['kolom']) ? $kolom=$_POST['kolom'] : null;
        $kolom_persetujuan='hasilpersetujuan'.$kolom;	
        isset($_POST['cm_hasil']) ? $comment=$_POST['cm_hasil'] : null;	
        isset($_POST['userid']) ? $user_id=$_POST['userid'] : null;
        $tglSkrng=date("Y-m-d");
        $nmBarang=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
        isset($_POST['dibuat']) ? $dibuat2=$_POST['dibuat'] : null;
        isset($_POST['approveid']) ? $approveid=$_POST['approveid'] : null;
        switch ($method){
        case 'insert_forward_pp' :
        /*	echo "warning:masuk";
                exit();*/
                $hasil_prstjn=1;
                $sql="select * from ".$dbname.".log_prapoht where `nopp`='".$nopp."'";
                $query=mysql_query($sql) or die(mysql_error());
                $res=mysql_fetch_assoc($query);


                if($res['close']==2)
                {
                        exit("Error:Sudah di Approved");
                }		
                elseif($res['close']==1)
                {			
                        if(($res['persetujuan1']!='') and ($res['persetujuan1']!=0000000000))
                        {		


                                        $a=1;		
                                        for($i=2;$i<6;$i++)
                                        {	
                                                if($user_id==$res['persetujuan'.$a])
                                                {
                                                        exit("Error: ".getNamaKaryawan($user_id)." Sudah di gunakan");			
                                                }
                                                elseif($user_id==$res['dibuat'])
                                                {
                                                        exit("Error: ".getNamaKaryawan($user_id)." Pembuat PP");										
                                                }
                                                elseif(($res['persetujuan'.$i]==0000000000)||(is_null($res['persetujuan'.$i])))
                                                {
                                                        //echo "warning masuk".$i."-"."-".$kolom_persetujuan;exit();

                                                        $strx="update ".$dbname.".log_prapoht set persetujuan".$i."='".$user_id."',
                                                              ".$kolom_persetujuan."='1',komentar".$kolom."='".$comment."',
                                                              tglp".$kolom."='".$tglSkrng."' where `nopp`='".$nopp."'"; 					

                                                        //echo "warning:".$strx;exit();
                                                        if($res=mysql_query($strx))
                                                        {
                                                            $sCek="select distinct hasilpersetujuan".$kolom." from ".$dbname.".log_prapoht
                                                                   where nopp='".$nopp."'";
                                                            $qCek=mysql_query($sCek) or die(mysql_error($conn));
                                                            $rCek=mysql_fetch_assoc($qCek);
                                                            if($rCek['hasilpersetujuan'.$kolom]==0)
                                                            {
                                                                $strx="update ".$dbname.".log_prapoht set hasilpersetujuan".$kolom."=1
                                                                       where nopp='".$nopp."'";
                                                                if(mysql_query($strx))
                                                                {
                                                                    mailCoy($user_id);
                                                                    exit();
                                                                }
                                                                else
                                                                {
                                                                    echo $strx;
                                                                    echo " Gagal,".addslashes(mysql_error($conn));
                                                                }
                                                            }
                                                            else
                                                            {
                                                                mailCoy($user_id);
                                                                exit();
                                                            }
                                                        }
                                                        else
                                                        {
                                                                echo $strx;
                                                                echo " Gagal,".addslashes(mysql_error($conn));
                                                        }
                                                }
                                                elseif($res['persetujuan5']!='')
                                                {
                                                        $strx="update ".$dbname.".log_prapoht set hasilpersetujuan5='1',komentar5='".$comment."',
                                                               close='2',tglp5='".$tglSkrng."' where `nopp`='".$nopp."'";	
                                                        if($res=mysql_query($strx))
                                                        {
                                                                //echo $stat;
                                                                mailCoy($user_id);
                                                                break;
                                                                exit();
                                                        }
                                                        else
                                                        {
                                                                echo $strx;
                                                                echo " Gagal,".addslashes(mysql_error($conn));
                                                        }				
                                                }				
                                                $a++;
                                        }	

                        }//echo "WARNING:".$strx; exit();
                }


        break;
        case 'insert_close_pp':
                $hasil=$_POST['stat_hasil'];
                $comment=$_POST['cmnt'];
                $user_id=$_POST['user_id'];
                $sql="select * from ".$dbname.".log_prapoht where nopp='".$nopp."'";
                $query=mysql_query($sql) or die(mysql_error());
                $res=mysql_fetch_assoc($query);

                if(($res['persetujuan5']!='')&&($user_id==$res['persetujuan5']))
                {
                        $sql2="update ".$dbname.".log_prapoht set `close`=2,komentar5='".$comment."',hasilpersetujuan5=1,tglp5='".$tglSkrng."' where nopp='".$nopp."'";	
                        if($query2=mysql_query($sql2))
                        {
                            exit();
                        }
                        else
                        {
                                echo $sql2;exit();
                                echo " Gagal,".addslashes(mysql_error($conn));
                        }
                }
                elseif(($res['persetujuan5']=='') || ($res['persetujuan5']==0000000000))
                {
                                if(($res['close']==1)&&($res['dibuat']!=$user_id))
                                {
                                         // echo "warning:".$kolom;
                                        if($res['persetujuan'.$kolom]==$user_id)
                                        {
                                                        $sql2="update ".$dbname.".log_prapoht set `close`=2,
                                                        komentar".$kolom."='".$comment."',".$kolom_persetujuan."=1,tglp".$kolom."='".$tglSkrng."' where nopp='".$nopp."'";				                           // echo "warning:".$sql2."___".$i."___".$_SESSION['standard']['userid']; exit();
                                                        if($query2=mysql_query($sql2))
                                                        {
                                                                exit();
                                                        }
                                                        else
                                                        {
                                                                echo $sql2;
                                                                echo " Gagal,".addslashes(mysql_error($conn));
                                                        }
                                        }
                                        else
                                        {
                                                echo "Warning: Anda tidak memiliki autorisasi untuk No PP Ini";
                                                exit();
                                        }
                                        /*for($i=1;$i<6;)
                                        {
                                                if(($res['persetujuan'.$i]==$user_id)&&($res['persetujuan'.$i]!='0000000000') )
                                                {
                                                        $sql2="update ".$dbname.".log_prapoht set `close`='2',persetujuan".$i."='".$user_id."',
                                                        komentar".$kolom."='".$comment."',".$kolom_persetujuan."='1',tglp".$i."='".$tglSkrng."' where nopp='".$nopp."'";				                            echo "warning:".$sql2."___".$i."___".$_SESSION['standard']['userid']; exit();
                                                        if($query2=mysql_query($sql2))
                                                        {
                                                                exit();
                                                        }
                                                        else
                                                        {
                                                                echo $sql2;exit();
                                                                echo " Gagal,".addslashes(mysql_error($conn));
                                                        }
                                                }
                                                else
                                                {

                                                }
                                                $i++;
                                        }*/
                                }
                        }
                        else
                        {
                                echo "Warning: Anda tidak memiliki autorisasi untuk No PP Ini";
                                exit();
                        }

        break;
        case 'rejected_pp_ex':
            $ardt=0;
                $comment=$_POST['comment'];
                $user_id=$_POST['user_id'];
                $sql="select* from ".$dbname.".log_prapoht where nopp='".$nopp."'";
                $hasil=2;
                $query=mysql_query($sql) or die(mysql_error());
                $res=mysql_fetch_assoc($query);
                if(($res['close']==1)&&($res['dibuat']!=$user_id))
                {
                        for($c=1;$c<6;$c++)
                        {

                            if($res['persetujuan'.$c]!='')
                            {
                                if(($res['hasilpersetujuan'.$c]=='' or $res['hasilpersetujuan'.$c]==0000000000)&&($res['persetujuan'.$c]==$_SESSION['standard']['userid']))
                                    {

                                                  $sql2="update ".$dbname.".log_prapoht set close='".$hasil."',komentar".$c."='".$comment."',".$kolom_persetujuan."='3',tglp".$c."='".$tglSkrng."' where nopp='".$nopp."'" ;					//echo "Warning:".$sql2; exit();
                                                  if(mysql_query($sql2))
                                                    {
                                                        $sql3="update ".$dbname.".log_prapodt set status='3',ditolakoleh='".$user_id."' where nopp='".$nopp."'";
                                                        if(mysql_query($sql3))
                                                        {
                                                           $ardt+=1;
                                                        }                                                       
                                                    }
                                                    else
                                                    {

                                                            echo " Gagal,".addslashes(mysql_error($conn));
                                                            echo $sql2;exit();
                                                    }

                                      }
                                      elseif(($bar['persetujuan'.$a]==$_SESSION['standard']['userid'])&&($bar['hasilpersetujuan'.$a]!=''))
                                      {
                                                    echo "Warning: You already proceccd this  PP";
                                                    exit();

                                       }
                            }
                        }
                         if($ardt!=0)
                            {
                            $sData="select distinct *
                            from ".$dbname.".log_prapoht where nopp='".$nopp."'";
                            $qData=mysql_query($sData) or die(mysql_error($conn));
                            $rData=mysql_fetch_assoc($qData);
                            if($rData['persetujuan1']!='')
                                $to=$rData['persetujuan1'];
                               if($rData['persetujuan2']!='')
                                $to.=",".$rData['persetujuan2'];
                                if($rData['persetujuan3']!='')
                                $to.=",".$rData['persetujuan3'];
                                 if($rData['persetujuan4']!='')
                                $to.=",".$rData['persetujuan4'];
                                 if($rData['persetujuan5']!='')
                                $to.=",".$rData['persetujuan5'];

                                    #send an email to incharge person
                                    $to=getUserEmail($to);
                                    $namakaryawan=getNamaKaryawan($rData['dibuat']);
                                    $nmpnlk=getNamaKaryawan($rData['persetujuan'.$_POST['kolom']]);
                                    $subject="[Notifikasi] Sebagian atau Seluruhnya PP No :".$_POST['nopp']." dari ".$namakaryawan." ditolak oleh ".$nmpnlk;
                                    $body="<html>
                                             <head>
                                             <body>
                                               <dd>Dengan Hormat,</dd><br>
                                               <br>
                                               Permintaan pembelian no.".$_POST['nopp']." ditolak oleh [".$nmpnlk."] dengan alasan ". $rData['komentar'.$_POST['kolom']]."
                                               <br>
                                               Item yang ditolak adalah : <ul>";
                                    $sBrg="select kodebarang,alasanstatus from ".$dbname.".log_prapodt where nopp='".$nopp."' and status='3'";
                                    $qBrg=mysql_query($sBrg) or die(mysql_error($conn));
                                    while($rBrg=mysql_fetch_assoc($qBrg))
                                    {
                                       $body.="<li>".$nmBarang[$rBrg['kodebarang']]."</li>";
                                    }
                                    $body.="</ul><br>
                                               <br>
                                               Regards,<br>
                                               Owl-Plantation System.
                                             </body>
                                             </head>
                                           </html>
                                           ";
                                   $x=kirimEmail($to,$subject,$body);#this has return but disobeying;
                                   echo $x;
                                } 
                  }
                  else
                  {
                        echo "Warning: You don`t have Authorizde for this PP";
                        exit();
                  }

        break;
        case 'rejected_some_input' :
                $nopp=$_POST['nopp'];
                $kode_brg=$_POST['kd_brg'];
                $user_id=$_POST['user_id'];
                $alsnDtolak=$_POST['alsnDtolk'];
                $where=" nopp='".$nopp."' and kodebarang='".$kode_brg."'";
                $sCek="select status from ".$dbname.".log_prapodt where nopp='".$nopp."' and status='0' ";
                $qCek=mysql_query($sCek) or die(mysql_error());
                $rCek=mysql_num_rows($qCek);
                if($rCek>1)
                {
                        $sql="select * from ".$dbname.".log_prapodt where".$where; 
                //	echo "warning:".$sql; exit();
                        $query=mysql_query($sql) or die(mysql_error());

                        $res=mysql_fetch_assoc($query);
                        if(($res['status']=='0')&&($res['ditolakoleh']==0000000000 or $res['ditolakoleh']==''))
                        {
                                $sql2="update ".$dbname.".log_prapodt set status='3',ditolakoleh='".$user_id."',alasanstatus='".$alsnDtolak."' where".$where;
                                if($query2=mysql_query($sql2))
                                {
                                        echo"";
                                }
                                else
                                {
                                        echo $sql2;exit();
                                        echo " Gagal,".addslashes(mysql_error($conn));
                                }
                        }
                        else
                        {
                                echo"warning: Already Fill";
                                exit();
                        }
                }
                else
                {
                        echo"warning:Item Barang Hanya Satu";
                        exit();
                }
        break;
        case'data_refresh':
        $limit=20;
        $page=0;
        if(isset($_POST['page']))
        {
        $page=$_POST['page'];
        if($page<0)
        $page=0;
        }
        $offset=$page*$limit;

        if($_SESSION['empl']['tipeinduk']=='HOLDING')
        {
                //close = '1'
                $str="SELECT * FROM ".$dbname.".log_prapoht where close!=2 and (persetujuan1='".$_SESSION['standard']['userid']."' or persetujuan2='".$_SESSION['standard']['userid']."' or persetujuan3='".$_SESSION['standard']['userid']."' or persetujuan4='".$_SESSION['standard']['userid']."' or persetujuan5='".$_SESSION['standard']['userid']."') 
                     ORDER BY hasilpersetujuan1,hasilpersetujuan2,persetujuan3,hasilpersetujuan4,hasilpersetujuan5 ASC LIMIT ".$offset.",".$limit."";//echo $str;

                $sql="SELECT count(*) as jmlhrow FROM ".$dbname.".log_prapoht where close!=2 and  (persetujuan1='".$_SESSION['standard']['userid']."' or persetujuan2='".$_SESSION['standard']['userid']."' or persetujuan3='".$_SESSION['standard']['userid']."' or persetujuan4='".$_SESSION['standard']['userid']."' or persetujuan5='".$_SESSION['standard']['userid']."')";
        } else {
                //close = '1'
                $str="SELECT * FROM ".$dbname.".log_prapoht where close!=2 and (persetujuan1='".$_SESSION['standard']['userid']."' or persetujuan2='".$_SESSION['standard']['userid']."' or persetujuan3='".$_SESSION['standard']['userid']."' or persetujuan4='".$_SESSION['standard']['userid']."' or persetujuan5='".$_SESSION['standard']['userid']."') 
                      ORDER BY hasilpersetujuan1,hasilpersetujuan2,persetujuan3,hasilpersetujuan4,hasilpersetujuan5 ASC LIMIT ".$offset.",".$limit."";
                $sql="SELECT count(*) as jmlhrow FROM  ".$dbname.".log_prapoht where close!=2 and  (persetujuan1='".$_SESSION['standard']['userid']."' or persetujuan2='".$_SESSION['standard']['userid']."' or persetujuan3='".$_SESSION['standard']['userid']."' or persetujuan4='".$_SESSION['standard']['userid']."' or persetujuan5='".$_SESSION['standard']['userid']."') ";
        }
        //echo $str;
        $query=mysql_query($sql) or die(mysql_error());
        while($jsl=mysql_fetch_object($query)) {
                $jlhbrs= $jsl->jmlhrow;
        }

        if($res=mysql_query($str))
        {
                while($bar=mysql_fetch_assoc($res))
                {
                        $koderorg=substr($bar['nopp'],15,4);
                        $spr="select namaorganisasi from  ".$dbname.".organisasi where  kodeorganisasi='".$koderorg."' or induk='".$koderorg."'"; //echo $spr;
                        $rep=mysql_query($spr) or die(mysql_error($conn));
                        $bas=mysql_fetch_object($rep);
                        $no+=1;
                        $waiting=$waitingname="";
                        echo"<tr class=rowcontent id='tr_".$no."'>
                                  <td>".$no."</td>
                                  <td id=td_".$no.">".$bar['nopp']."</td>
                                  <td>".tanggalnormal($bar['tanggal'])."</td>
                                  <td>".$bas->namaorganisasi."</td>
                                  <td align=center>
                                  <img src=images/pdf.jpg class=resicon width='30' height='30' title='Print' onclick=\"masterPDF('log_prapoht','".$bar['nopp']."','','log_slave_print_log_pp',event);\"> &nbsp
                                  <img src=images/zoom.png class=resicon height='30' title='Preview' onclick=\"previewDetail('".$bar['nopp']."',event);\">    
                                  </td>";      
                                        if($bar['close']==2)
                                        {
                                                $accept=0;
                                                for($i=1;$i<6;$i++)
                                                {
                                                        if($bar['hasilpersetujuan'.$i]=='3')
                                                        {
                                                                $accept=3;
                                                                break;
                                                        }
                                                        elseif($bar['hasilpersetujuan'.$i]=='1')
                                                        {
                                                                $accept=1;

                                                        }
                                                }
                                                if($accept==3) {
                                                        echo"<td colspan=3>".$_SESSION['lang']['ditolak']."</td>";
                                                } elseif($accept==1) {
                                                        echo"<td colspan=3>".$_SESSION['lang']['disetujui']."</td>";
                                                }
                                        }
                                        elseif($bar['close']<2)
                                        {
                                                for($a=1;$a<6;$a++)
                                                {
                                                        if($bar['persetujuan'.$a]!='')
                                                        {
                                                                if(($bar['persetujuan'.$a]==$_SESSION['standard']['userid'])&&(($bar['hasilpersetujuan'.$a]!='')
                                                                and $bar['hasilpersetujuan'.$a]!=0))
                                                                {
                                                                        echo"<td colspan=3>&nbsp;</td>";
                                                                }
                                                                elseif(($bar['persetujuan'.$a]==$_SESSION['standard']['userid'])&&($bar['hasilpersetujuan'.$a]=='' 
                                                                or $bar['hasilpersetujuan'.$a]==0))
                                                                {
                                                                echo"
                                                                <td><a href=# onclick=\"get_data_pp('".$bar['nopp']."','".$a."')\">".$_SESSION['lang']['approve']."</a></td>
                                                                <td><a href=# onclick=rejected_pp('".$bar['nopp']."','".$a."') >".$_SESSION['lang']['ditolak']."</a></td>
                                                                <td><a href=# onclick=\"rejected_some_proses('".$bar['nopp']."','".$a."')\" >
                                                                ".$_SESSION['lang']['ditolak_some']."</a></td>";
                                                                }
                                                                if (($bar['hasilpersetujuan'.$a]=='' or $bar['hasilpersetujuan'.$a]==0)){
                                                                    $waiting=$bar['persetujuan'.$a];
                                                                }
                                                        }
                                                }
                                        }
                                 for($i=1;$i<6;$i++)
                                 {
                                        //echo $bar['hasilpersetujuan'.$i];
                                        if(($bar['persetujuan'.$i]!='')||($bar['persetujuan'.$i]!=0))
                                        {	
                                                $kr=$bar['persetujuan'.$i];
                                                $sql="select * from ".$dbname.".datakaryawan where karyawanid='".$kr."'";
                                                $query=mysql_query($sql) or die(mysql_error());
                                                $yrs=mysql_fetch_assoc($query);
                                                $waitingname=$yrs['namakaryawan'];
                                                echo"<td><a href=# onclick=\"cek_status_pp('".$bar['hasilpersetujuan'.$i]."')\">".$yrs['namakaryawan']."</a></td>";
                                        }
                                        else
                                        {
                                                echo"<td>&nbsp;</td>";
                                        }
                                 }
                                 if($waiting!='')
                                    echo"<td><a title='Kirim kembali email notifikasi kepada ".$waitingname."' href=# onclick=\"kirimEmailTo('".$bar['nopp']."','".$waiting."')\">Email Notifikasi</a></td>";
                                 else
                                    echo"<td></td>";
                                 echo"</tr>";
                }	 	
                echo"
                                 <tr><td colspan=13 align=center>
                                ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
                                <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                                <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                                </td>
                                </tr><input type=hidden id=nopp_".$no." name=nopp_".$no." value='".$bar['nopp']."' />";   	
          }	
          else
                {
                        echo " Gagal,".(mysql_error($conn));
                }	

        break;
        case 'data_refresh2':
        $limit=10;
        $page=0;
        if(isset($_POST['page']))
        {
        $page=$_POST['page'];
        if($page<0)
        $page=0;
        }
        $offset=$page*$limit;
          if($_SESSION['empl']['tipeinduk']=='HOLDING')
            {
                        //close = '1'
                    $str="SELECT * FROM ".$dbname.".log_prapoht where  (persetujuan1='".$_SESSION['standard']['userid']."' or persetujuan2='".$_SESSION['standard']['userid']."' or persetujuan3='".$_SESSION['standard']['userid']."' or persetujuan4='".$_SESSION['standard']['userid']."' or persetujuan5='".$_SESSION['standard']['userid']."') ORDER BY `tanggal` DESC LIMIT ".$offset.",".$limit."";//echo $str;
                                        $sql="SELECT count(*) as jmlhrow FROM ".$dbname.".log_prapoht where  close!='2'and substring(nopp,16,4)='".$_SESSION['empl']['lokasitugas']."'  and (persetujuan1='".$_SESSION['standard']['userid']."' or persetujuan2='".$_SESSION['standard']['userid']."' or persetujuan3='".$_SESSION['standard']['userid']."' or persetujuan4='".$_SESSION['standard']['userid']."' or persetujuan5='".$_SESSION['standard']['userid']."') ORDER BY `tanggal` DESC";

            }
            else
            {
                        //close = '1'
                    $str="SELECT * FROM ".$dbname.".log_prapoht where  (persetujuan1='".$_SESSION['standard']['userid']."' or persetujuan2='".$_SESSION['standard']['userid']."' or persetujuan3='".$_SESSION['standard']['userid']."' or persetujuan4='".$_SESSION['standard']['userid']."' or persetujuan5='".$_SESSION['standard']['userid']."') ORDER BY `tanggal` DESC";
                                        $sql="SELECT count(*) as jmlhrow FROM  ".$dbname.".log_prapoht where  (persetujuan1='".$_SESSION['standard']['userid']."' or persetujuan2='".$_SESSION['standard']['userid']."' or persetujuan3='".$_SESSION['standard']['userid']."' or persetujuan4='".$_SESSION['standard']['userid']."' or persetujuan5='".$_SESSION['standard']['userid']."') ORDER BY `tanggal` DESC";
            }
              /*   if($_SESSION['empl']['tipeinduk']=='HOLDING')
            {
                                $str="SELECT * FROM ".$dbname.".log_prapoht where close!='2' and substring(nopp,16,4)='".$_SESSION['empl']['lokasitugas']."'  
                                and (persetujuan1='".$_SESSION['standard']['userid']."' or persetujuan2='".$_SESSION['standard']['userid']."' 
                                or persetujuan3='".$_SESSION['standard']['userid']."' or persetujuan4='".$_SESSION['standard']['userid']."' 
                                or persetujuan5='".$_SESSION['standard']['userid']."') ORDER BY `tanggal` DESC LIMIT ".$offset.",".$limit."";//echo $str;
                                $sql="SELECT count(*) as jmlhrow FROM ".$dbname.".log_prapoht where close!='2' and substring(nopp,16,4)='".$_SESSION['empl']['lokasitugas']."'  
                                  and (persetujuan1='".$_SESSION['standard']['userid']."' or persetujuan2='".$_SESSION['standard']['userid']."' 
                                  or persetujuan3='".$_SESSION['standard']['userid']."' or persetujuan4='".$_SESSION['standard']['userid']."' or persetujuan5='".$_SESSION['standard']['userid']."') 				  ORDER BY `tanggal` DESC";
            }
            else
            {
                                $str="SELECT * FROM ".$dbname.".log_prapoht where substring(nopp,16,4)='".$_SESSION['empl']['lokasitugas']."'  
                                and (persetujuan1='".$_SESSION['standard']['userid']."' or persetujuan2='".$_SESSION['standard']['userid']."' 
                                or persetujuan3='".$_SESSION['standard']['userid']."' or persetujuan4='".$_SESSION['standard']['userid']."' 
                                or persetujuan5='".$_SESSION['standard']['userid']."') ORDER BY `tanggal` DESC LIMIT ".$offset.",".$limit."";//echo $str;
                                  $sql="SELECT count(*) as jmlhrow FROM ".$dbname.".log_prapoht where substring(nopp,16,4)='".$_SESSION['empl']['lokasitugas']."'  
                                  and (persetujuan1='".$_SESSION['standard']['userid']."' or persetujuan2='".$_SESSION['standard']['userid']."' 
                                  or persetujuan3='".$_SESSION['standard']['userid']."' or persetujuan4='".$_SESSION['standard']['userid']."' or persetujuan5='".$_SESSION['standard']['userid']."') 				  ORDER BY `tanggal` DESC";
            }*/
                        $query=mysql_query($sql) or die(mysql_error());
                        while($jsl=mysql_fetch_object($query)){
                        $jlhbrs= $jsl->jmlhrow;
                        }
          if($res=mysql_query($str))
          {
                while($bar=mysql_fetch_assoc($res))
                {
                        $koderorg=substr($bar['nopp'],15,4);
                        $spr="select * from  ".$dbname.".organisasi where  kodeorganisasi='".$koderorg."' or induk='".$koderorg."'"; //echo $spr;
                        $rep=mysql_query($spr) or die(mysql_error($conn));
                        $bas=mysql_fetch_object($rep);
                        $no+=1;
                        echo"<tr class=rowcontent id='tr_".$no."'>
                                  <td>".$no."</td>
                                  <td id=td_".$no.">".$bar['nopp']."</td>
                                  <td>".tanggalnormal($bar['tanggal'])."</td>
                                  <td>".$bas->namaorganisasi."</td>
                                  <td align=center><img src=images/pdf.jpg class=resicon width='30' height='30' title='Print' onclick=\"masterPDF('log_prapoht','".$bar['nopp']."','','log_slave_print_log_pp',event);\">
                                  <img src=images/zoom.png class=resicon  height='30' title='Preview' onclick=\"previewDetail('".$bar['nopp']."',event);\">     
                                  </td>";                            
                         for ($a=1;$a<6;$a++)
                                 {	
                                                                                if($bar['close']==2)
                                                                                {
                                                                                        if($bar['hasilpersetujuan'.$a]=='3')
                                                                                        {
                                                                                                //echo"<td colspan=3>".$_SESSION['lang']['ditolak']."</td>";
                                                                                                $abc=3;
                                                                                        }
                                                                                        elseif($bar['hasilpersetujuan'.$a]=='1')
                                                                                        {
                                                                                                //echo"<td colspan=3>".$_SESSION['lang']['ditolak']."</td>";
                                                                                                $abc=1;
                                                                                        }
                                                                                }
                                                                                elseif($bar['close']<2)
                                                                                {
                                                                                        if($bar['persetujuan'.$a]!='')
                                                                                        {
                                                                                                        if(($bar['persetujuan'.$a]==$_SESSION['standard']['userid'])&&(($bar['hasilpersetujuan'.$a]!='')
                                                                                                        and $bar['hasilpersetujuan'.$a]!=0))
                                                                                                         {
                                                                                                          echo"<td colspan=3>&nbsp;</td>";
                                                                                                         }
                                                                                                          elseif(($bar['persetujuan'.$a]==$_SESSION['standard']['userid'])&&($bar['hasilpersetujuan'.$a]=='' 
                                                                                                         or $bar['hasilpersetujuan'.$a]==0))
                                                                                                         {
                                                                                                                echo"
                                                                                                           <td><a href=# onclick=\"get_data_pp('".$bar['nopp']."','".$a."')\">".$_SESSION['lang']['approve']."</a></td>
                                                                                                                <td><a href=# onclick=rejected_pp('".$bar['nopp']."','".$a."') >".$_SESSION['lang']['ditolak']."</a></td>
                                                                                                                <td><a href=# onclick=\"rejected_some_proses('".$bar['nopp']."','".$a."')\" >
                                                                                                                ".$_SESSION['lang']['ditolak_some']."</a></td>";
                                                                                                         }
                                                                                        }
                                                                                }
                                                                 }
                                                                 if($abc!='')
                                                                 {
                                                                         if($abc==3)
                                                                         {
                                                                                 echo"<td colspan=3>".$_SESSION['lang']['ditolak']."</td>";
                                                                         }
                                                                         elseif($abc==1)
                                                                         {
                                                                                echo"<td colspan=3>".$_SESSION['lang']['approve']."</td>";
                                                                         }
                                                                 }

                                 for($i=1;$i<6;$i++)
                                 {
                                        //echo $bar['hasilpersetujuan'.$i];
                                        if($bar['persetujuan'.$i]!='')
                                        {	
                                                $kr=$bar['persetujuan'.$i];
                                                $sql="select * from ".$dbname.".datakaryawan where karyawanid='".$kr."'";
                                                $query=mysql_query($sql) or die(mysql_error());
                                                $yrs=mysql_fetch_assoc($query);

                                                echo"<td><a href=# onclick=\"cek_status_pp('".$bar['hasilpersetujuan'.$i]."')\">".$yrs['namakaryawan']."</a></td>";
                                        }
                                        else
                                        {
                                                echo"<td>&nbsp;</td>";
                                        }
                                 }
                                 echo"</tr><input type=hidden id=nopp_".$no." name=nopp_".$no." value='".$bar['nopp']."' />";
                }	
                echo"
                                 <tr><td colspan=13 align=center>
                                ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
                                <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                                <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                                </td>
                                </tr><input type=hidden id=nopp_".$no." name=nopp_".$no." value='".$bar['nopp']."' />";  	   	
          }	
          else
                {
                        echo " Gagal,".(mysql_error($conn));
                }	

        break;
        case 'kirimEmailTo':
            $to=getUserEmail($approveid);
            $str="SELECT * FROM ".$dbname.".log_prapoht where nopp='".$nopp."'";
            $res=fetchData($str);
            $namakaryawan=getNamaKaryawan($res[0]['dibuat']);
            if($_SESSION['language']=='EN'){    
                            $subject="[Notifikasi] PR Submission for approval, submitted by: ".$namakaryawan;
                            $body="<html>ss
                                     <head>
                                     <body>
                                       <dd>Dear Sir/Madam,</dd><br>
                                       <br>
                                       Dated on ".tanggalnormal($res[0]['tanggal']).",  on behalf of ".$namakaryawan." submit a PR (No ".$nopp."), requesting for your approval. To follow up, please follow the link below.
                                       <br>
                                       <br>
                                       <br>
                                       Regards,<br>
                                       Owl-Plantation System.
                                     </body>
                                     </head>
                                   </html>
                                   ";
                        }else{
                            $subject="[Notifikasi] Persetujuan PP a/n ".$namakaryawan;
                            $body="<html>
                                     <head>
                                     <body>
                                       <dd>Dengan Hormat,</dd><br>
                                       <br>
                                       Pada tanggal ".tanggalnormal($res[0]['tanggal'])." karyawan a/n  ".$namakaryawan." mengajukan Permintaan Pembelian Barang (No ".$nopp.") 
                                       kepada bapak/ibu. Untuk menindak-lanjuti, silahkan ikuti link dibawah.
                                       <br>
                                       <br>
                                       <br>
                                       Regards,<br>
                                       Owl-Plantation System.
                                     </body>
                                     </head>
                                   </html>
                                   ";                                            
                        }
//                        echo $body;
            $kirim=kirimEmail($to,$subject,$body);#this has return but disobeying;    
            
        break;
        default:
        break;
        }
//========================================================================	
function mailCoy($userid)
{
 #send an email to incharge person
    $to=getUserEmail($userid);
    $namakaryawan=getNamaKaryawan($_SESSION['standard']['userid']);
    if($_SESSION['language']=='EN'){    
                    $subject="[Notifikasi] PR Submission for approval, submitted by: ".$namakaryawan;
                    $body="<html>
                             <head>
                             <body>
                               <dd>Dear Sir/Madam,</dd><br>
                               <br>
                               Today,  ".date('d-m-Y').",  on behalf of ".$namakaryawan." submit a PR (No ".$nopp."), requesting for your approval. To follow up, please follow the link below.
                               <br>
                               <br>
                               <br>
                               Regards,<br>
                               Owl-Plantation System.
                             </body>
                             </head>
                           </html>
                           ";
                }else{
                    $subject="[Notifikasi] Persetujuan PP a/n ".$namakaryawan;
                    $body="<html>
                             <head>
                             <body>
                               <dd>Dengan Hormat,</dd><br>
                               <br>
                               Pada hari ini, tanggal ".date('d-m-Y')." karyawan a/n  ".$namakaryawan." mengajukan Permintaan Pembelian Barang (No ".$nopp.") 
                               kepada bapak/ibu. Untuk menindak-lanjuti, silahkan ikuti link dibawah.
                               <br>
                               <br>
                               <br>
                               Regards,<br>
                               Owl-Plantation System.
                             </body>
                             </head>
                           </html>
                           ";                                            
                }
    $kirim=kirimEmail($to,$subject,$body);#this has return but disobeying;    
}        
?>
