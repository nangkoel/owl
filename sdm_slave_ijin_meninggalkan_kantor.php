<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$ganti=$_POST['ganti'];
$proses=$_POST['proses'];
$tglijin=tanggalsystem($_POST['tglijin']);
$jnsIjin=$_POST['jnsIjin'];
$jamDr=$_POST['jamDr'];
$jamSmp=$_POST['jamSmp'];
$keperluan=$_POST['keperluan'];
$ket=$_POST['ket'];
$atasan=$_POST['atasan'];
$atasan2=$_POST['atasan2'];
$tglAwal=explode("-",$_POST['tglAwal']);
$tgl1=$tglAwal[2]."-".$tglAwal[1]."-".$tglAwal[0];
$tglEnd=explode("-",$_POST['tglEnd']);
$tgl2=$tglEnd[2]."-".$tglEnd[1]."-".$tglEnd[0];
$jamDr1=$tgl1." ".$jamDr;
$jamSmp1=$tgl2." ".$jamSmp;
$arrNmkary=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$arrKeputusan=array("0"=>$_SESSION['lang']['diajukan'],"1"=>$_SESSION['lang']['disetujui'],"2"=>$_SESSION['lang']['ditolak']);
$where=" tanggal='".$tglijin."' and karyawanid='".$_SESSION['standard']['userid']."'";
$atsSblm=$_POST['atsSblm'];
$hk=$_POST['jumlahhk'];
$hrd=$_POST['hrd'];
$periodec=$_POST['periodec'];

if(($proses=='update' or $proses=='insert') and $jnsIjin=='CUTI'){
//===============ambil sisa cuti
//
                //ambil cuti ybs
                $strf="select sisa from ".$dbname.".sdm_cutiht where karyawanid=".$_SESSION['standard']['userid']." 
                    and periodecuti=".$periodec;
                $res=mysql_query($strf);

                $sisa='';
                while($barf=mysql_fetch_object($res))
                {
                    $sisa=$barf->sisa;
                }
                if($sisa=='')
                    $sisa=0;
                //=============================      
            #ambil periode cuti terakhir
                $strfx="select max(periodecuti) as periodecuti from ".$dbname.".sdm_cutiht where karyawanid=".$_SESSION['standard']['userid'];
                $resx=mysql_query($strfx);
                while($barx=mysql_fetch_object($resx)){
                    $lastp=$barx->periodecuti;
                }
                
              //periksa apakah mengajukan cuti sebelum periode cuti berjalan
                $zz=substr($tgl1,0,4);
                if($lastp<$zz and $lastp!=''){
                //insert cuti baru dan ubah sisa   
                    #ambil tanggal masuk
                    $str1="select karyawanid,namakaryawan,tanggalmasuk,lokasitugas from ".$dbname.".datakaryawan
	       where  karyawanid='".$_SESSION['standard']['userid']."'";
                        $res1=mysql_query($str1);
                        while($bar1=mysql_fetch_object($res1))
                        {
                                    //=================================
                                    //default
                                    $x=readTextFile('config/jumlahcuti.lst');
                                    if(intval($x)>0)
                                        $hakcuti=$x;
                                    else
                                        $hakcuti=15;  //default
                                    #jika bukan orang HO maka dapat 
                                    if($bar1->tipekaryawan==0 and substr($bar1->lokasitugas,2,2)!='HO')
                                            $hakcuti=18;
                                    else if($bar1->tipekaryawan!=0 and substr($bar1->lokasitugas,2,2)!='HO')
                                            $hakcuti=12;
                                    $sisa=$hakcuti;
                                    
                                    //lanjut jika tahun pertama
                                    if(substr($bar1->tanggalmasuk,0,4)>=$zz){
                                        continue;//tidak melakukan apa apa, karena belum berhak dapat cuti
                                    }
                                    
                                    //=================================
                                    $tgl=substr(str_replace("-","",$bar1->tanggalmasuk),4,4);		
                                    $dari=mktime(0,0,0,substr($tgl,0,2),substr($tgl,2,2),$zz);
                                    $dari=date('Ymd',$dari);
                                    $sampai=mktime(0,0,0,substr($tgl,0,2),substr($tgl,2,2),$zz+1);		
                                    $sampai=date('Ymd',$sampai);
                                    #jika periode masuk masih belum 1tahun maka 0
                                     $d=substr(str_replace("-","",$bar1->tanggalmasuk),0,4);
                                #ambil sisa cuti YBS
                                     $str="select sisa from ".$dbname.".sdm_cutiht where karyawanid=".$bar1->karyawanid." 
                                               and periodecuti>".($periodec-2)." order by periodecuti desc limit 1";
                                     $resx=mysql_query($str);
                                     $sisalalu=0;
                                     while($barx=mysql_fetch_object($resx))
                                     {
                                         $sisalalu=$barx->sisa;
                                     }
                                #periksa apakah sudah ada pada periode yang sama
                                      $str="select * from ".$dbname.".sdm_cutiht where karyawanid=".$bar1->karyawanid." 
                                               and periodecuti=".$periodec." order by periodecuti desc limit 1";
                                     $resy=mysql_query($str);
                                     if(mysql_num_rows($resy)>0)
                                     {
                                         #berarti  saldo saat ini adalah sisalalu
                                         #$saldo=$sisalalu;
                                         #tidak ada perubahan
                                     }
                                     else
                                     {   
                                         $saldo=$hakcuti;
                                        #==========================periksa apakah sudah ada pengambilan cuti sebelum ada header (sebelum cuti baru muncul)
                                                        $strx="select sum(jumlahcuti) as diambil from ".$dbname.".sdm_cutidt
                                                            where karyawanid=".$bar1->karyawanid."
                                                             and  daritanggal >=".$dari." and daritanggal<=".$sampai;
                                                        $diambil=0;#sudah diambil diambil tahun ini
                                                        $resx=mysql_query($strx);
                                                        while($barx=mysql_fetch_object($resx))
                                                        {
                                                                $diambil=$barx->diambil;
                                                                if($diambil=='')
                                                                    $diambil=0;
                                                        }
                                            $saldo=$saldo-$diambil;       
                                            $sisa=$saldo;
                                         #================================================================
                                         #maka insert periode baru
                                         $str="insert into ".$dbname.".sdm_cutiht(kodeorg, karyawanid, periodecuti, keterangan, dari, sampai, hakcuti, diambil, sisa)
                                                   values('".$bar1->lokasitugas."',".$bar1->karyawanid.",".$periodec.",'',".$dari.",".$sampai.",".$hakcuti.",0,".$saldo.")";
                                         mysql_query($str);
                                     } 
                        }              
                }
}



        switch($proses)
        {//sampe sini tinggal insert

                case'insert':
                              
                 // exit("Error:$tglijin.___.$jnsIjin.___.$jamDr1.____.$jamSmp1.____.$keperluan.____.$atasan.____.$atasan2.____.$ganti.___.$hrd");
                if(($tglijin=='')||($jnsIjin=='')||($jamDr1=='')||($jamSmp1=='')||($keperluan=='') ||($atasan=='') ||($ganti==''))
                {
                        echo"warning:Please Complete The Form";
                        exit();
                }
                $wktu="0000-00-00 00:00:00";
                $sCek="select tanggal from ".$dbname.".sdm_ijin where  ".$where.""; //echo "warning:".$sCek;
                $qCek=mysql_query($sCek) or die(mysql_error());
                $rCek=mysql_fetch_row($qCek);
                if($rCek<1)
                {
                    if($atasan!='')
                    {
                        $wktu=date("Y-m-d H:i:s");
                    }

                        $sIns="insert into ".$dbname.".sdm_ijin (karyawanid, tanggal, keperluan, keterangan, persetujuan1, waktupengajuan, darijam, sampaijam, jenisijin,hrd,periodecuti,jumlahhari,persetujuan2,ganti) 
                        values ('".$_SESSION['standard']['userid']."','".$tglijin."','".$keperluan."','".$ket."','".$atasan."','".$wktu."','".$jamDr1."','".$jamSmp1."','".$jnsIjin."',".intval($hrd).",".$periodec.",".$hk.",'".intval($atasan2)."','".$ganti."')";

                        if(mysql_query($sIns))
                        {
                            $tglMulai=$tglAwal[0]."-".$tglAwal[1]."-".$tglAwal[2];
                            if($atasan!='')
                            {
                                #send an email to incharge person
                                    $to=getUserEmail($atasan);
                                            $namakaryawan=getNamaKaryawan($_SESSION['standard']['userid']);
                                            $subject="[Notifikasi]Persetujuan Ijin Keluar Kantor a/n ".$namakaryawan;
                                            $body="<html>
                                                     <head>
                                                     <body>
                                                       <dd>Dengan Hormat,</dd><br>
                                                       <br>
                                                       Pada hari ini, tanggal ".date('d-m-Y')." karyawan a/n  ".$namakaryawan." mengajukan Ijin/".$jnsIjin." (".$keperluan.")
                                                       kepada bapak/ibu.
                                                       <br>
                                                       Yang bersangkutan mengajukan Ijin/".$jnsIjin." untuk ".$hk." hari mulai dari hari ".hari($tgl1).", tanggal ".$tglMulai.".
                                                       <br>
                                                       Untuk menindak-lanjuti, silahkan ikuti link dibawah.
                                                       <br>
                                                       <br>
                                                       <i>Note: Sisa cuti ybs periode ".$periodec.":".$sisa." Hari</i>
                                                       <br>
                                                       <br>
                                                       Regards,<br>
                                                       Owl-Plantation System.
                                                     </body>
                                                     </head>
                                                   </html>
                                                   ";
                                            $kirim=kirimEmail($to,$subject,$body);#this has return but disobeying;		
                            }
                            if($atasan2!='')
                            {
                                #send an email to incharge person
                                    $to=getUserEmail($atasan2);
                                            $namakaryawan=getNamaKaryawan($_SESSION['standard']['userid']);
                                            $kirim=kirimEmail($to,$subject,$body);#this has return but disobeying;		
                            }
                            if($hrd!='')
                            {
                                #send an email to incharge person
                                    $to=getUserEmail($atasan2);
                                            $namakaryawan=getNamaKaryawan($_SESSION['standard']['userid']);
                                            $kirim=kirimEmail($to,$subject,$body);#this has return but disobeying;		
                            }
							if($ganti!='')
                            {
                                #send an email to incharge person
                                    $to=getUserEmail($ganti);
                                            $namakaryawan=getNamaKaryawan($_SESSION['standard']['userid']);
                                            $subject="[Notifikasi]Pengalihan tugas a/n ".$namakaryawan;
                                            $body="<html>
                                                     <head>
                                                     <body>
                                                       <dd>Dengan Hormat,</dd><br>
                                                       <br>
                                                       Pada hari ini, tanggal ".date('d-m-Y')." karyawan a/n  ".$namakaryawan." melakukan ".$jnsIjin." (".$keperluan.")
                                                       dan mengalihkan sementara pekerjaan kepada bapak/ibu untuk sementara selama ".$hk." hari mulai dari hari ".hari($tgl1).", tanggal ".$tglMulai."
                                                       <br>
                                                       <br>
                                                       Regards,<br>
                                                       Owl-Plantation System.
                                                     </body>
                                                     </head>
                                                   </html>
                                                   ";
                                            $kirim=kirimEmail($to,$subject,$body);#this has return but disobeying;		
											
                            }
                        }
                        else
                        {
                            echo "DB Error : ".mysql_error($conn);
                        }
                }
                else
                {
                    exit("Error:Data Pada Tanggal ".$_POST['tglijin']." Sudah ada");
                }
                break;

                case'loadData':
                $limit=10;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;

                $ql2="select count(*) as jmlhrow from ".$dbname.".sdm_ijin where karyawanid='".$_SESSION['standard']['userid']."'  order by `tanggal` desc";// echo $ql2;
                $query2=mysql_query($ql2) or die(mysql_error());
                while($jsl=mysql_fetch_object($query2)){
                $jlhbrs= $jsl->jmlhrow;
                }

                $slvhc="select * from ".$dbname.".sdm_ijin where karyawanid='".$_SESSION['standard']['userid']."'   order by `tanggal` desc limit ".$offset.",".$limit." ";
                $qlvhc=mysql_query($slvhc) or die(mysql_error());
                $user_online=$_SESSION['standard']['userid'];
                while($rlvhc=mysql_fetch_assoc($qlvhc))
                {
                $no+=1;

                echo"
                <tr class=rowcontent>
                <td>".$no."</td>
                <td>".tanggalnormal($rlvhc['tanggal'])."</td>
                <td>".$rlvhc['keperluan']."</td>
                <td>".$rlvhc['jenisijin']."</td>
                <td>".$arrNmkary[$rlvhc['persetujuan1']]."</td>
				<td>".$arrNmkary[$rlvhc['persetujuan2']]."</td>
                <td>".$arrKeputusan[$rlvhc['stpersetujuan1']]."</td>";
				if(intval($rlvhc['persetujuan2'])==0){
					echo"<td>&nbsp;</td>";
				}else{
					echo"<td>".$arrKeputusan[$rlvhc['stpersetujuan2']]."</td>";
				}
                if(intval($rlvhc['hrd'])==0){
					echo"<td>&nbsp;</td>";
				}else{
					echo"<td>".$arrKeputusan[$rlvhc['stpersetujuanhrd']]."</td>";
				}
                echo"
                <td>".tanggalnormald($rlvhc['darijam'])."</td>
                <td>".tanggalnormald($rlvhc['sampaijam'])."</td>
				<td>".$arrNmkary[$rlvhc['ganti']]."</td>";
                if($rlvhc['stpersetujuan1']==0 and $rlvhc['stpersetujuanrd']==0)
                {
                echo"<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['keperluan']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['jenisijin']."','".$rlvhc['persetujuan1']."','".$rlvhc['stpersetujuan1']."','".$rlvhc['darijam']."','".$rlvhc['sampaijam']."','".$rlvhc['ganti']."','".$rlvhc['jumlahhari']."','".$rlvhc['periodecuti']."','".$rlvhc['hrd']."','".$rlvhc['persetujuan2']."');\">
                    <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".tanggalnormal($rlvhc['tanggal'])."');\" ></td>";
                    //<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_ijin','".$rlvhc['tanggal'].",".$rlvhc['karyawanid']."','','sdm_slave_ijin_meninggalkan_kantor',event)\"></td>";
                }
                else
                {
                    echo "<td>".$arrKeputusan[$rlvhc['stpersetujuan1']]."</td>";
                   // echo"<td> <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_ijin','".$rlvhc['tanggal'].",".$rlvhc['karyawanid']."','','sdm_slave_ijin_meninggalkan_kantor',event)\"></td>";
                }//end if updateby

        }//end while
                echo"
                </tr><tr class=rowheader><td colspan=13 align=center>
                ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
                <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                </td>
                </tr>";
                break;
                case'getKet':
                $sket="select distinct keterangan from ".$dbname.".sdm_ijin where ".$where."";
                $qKet=mysql_query($sket) or die(mysql_error($conn));
                $rKet=mysql_fetch_assoc($qKet);

                echo $rKet['keterangan'];
				
                break;

                case'deleteData':
                $sket="select distinct stpersetujuan1 from ".$dbname.".sdm_ijin where ".$where."";
                $qKet=mysql_query($sket) or die(mysql_error($conn));
                $rKet=mysql_fetch_assoc($qKet); 
                if($rKet['stpersetujuan1']==0 or $rKet['stpersetujuan2']==0)
                {
                        $sDel="delete from ".$dbname.".sdm_ijin where ".$where."";
                        //exit("Error".$sDel);
                        if(mysql_query($sDel))
                        echo"";
                        else
                        echo "DB Error : ".mysql_error($conn);                        
                }
                else
                {
                    exit("Error:Sudah ada keputusan");
                }
                break;

                case'update':
 
                    //=============================		
                if(($jnsIjin=='')||($jamDr=='')||($jamSmp=='')||($keperluan=='') || ($atasan=='') || ($ganti==''))
                {
                        echo"warning:Please Complete The Form";
                        exit();
                }
                $sket="select distinct stpersetujuan1,persetujuan2,ganti from ".$dbname.".sdm_ijin where ".$where."";
                $qKet=mysql_query($sket) or die(mysql_error($conn));
                $rKet=mysql_fetch_assoc($qKet); 
                if($rKet['stpersetujuan1']==0)
                {
                    //(karyawanid, tanggal, keperluan, keterangan, persetujuan1, waktupengajuan, darijam, sampaijam, jenisijin) 
                        //values ('".$_SESSION['standard']['userid']."','".$tglijin."','".$keperluan."','".$ket."','".$atasan."','".$wktu."','".$jamDr."','".$jamSmp."','".$jnsIjin."')
                    $sUp="update  ".$dbname.".sdm_ijin set keperluan='".$keperluan."', keterangan='".$ket."', darijam='".$jamDr1."', 
                          sampaijam='".$jamSmp1."',jenisijin='".$jnsIjin."',persetujuan2='".intval($atasan2)."',
                          hrd=".intval($hrd).",periodecuti=".$periodec.",jumlahhari=".$hk." ";
					//exit("Error:$sUp");	  
                    if($atsSblm!=$atasan)
                    {
                         $wktu=date("Y-m-d H:i:s");
                         $sUp.=",persetujuan1='".$atasan."',waktupengajuan='".$wktu."'";
                    }
					if($rKet['persetujuan2']!=$atasan2)
					{
						$sUp.=",persetujuan2='".$atasan2."'";
					}
					if($rKet['ganti']!=$ganti)
					{
						$sUp.=",ganti='".$ganti."'";
					}
					
					
				//indz
					//if(
                    $sUp.=" where ".$where."";
                    if(mysql_query($sUp))
                    {
                        if($atsSblm!=$atasan)
                        {
                               #send an email to incharge person
                                    $to=getUserEmail($atasan);
                                            $namakaryawan=getNamaKaryawan($_SESSION['standard']['userid']);
                                            $subject="[Notifikasi]Persetujuan Ijin Keluar Kantor a/n ".$namakaryawan;
                                            $body="<html>
                                                     <head>
                                                     <body>
                                                       <dd>Dengan Hormat,</dd><br>
                                                       <br>
                                                       Pada hari ini, tanggal ".date('d-m-Y')." karyawan a/n  ".$namakaryawan." mengajukan Ijin/".$jnsIjin." (".$keperluan.")
                                                       kepada bapak/ibu. Untuk menindak-lanjuti, silahkan ikuti link dibawah.
                                                       <br>
                                                       <br>
                                                       Note: Sisa cuti ybs periode ".$periodec.":".$sisa." Hari
                                                       <br>
                                                       <br>
                                                       Regards,<br>
                                                       Owl-Plantation System.
                                                     </body>
                                                     </head>
                                                   </html>
                                                   ";
                                            $kirim=kirimEmail($to,$subject,$body);#this has return but disobeying;
                        }
						
						
						if($rKet['ganti']!=$ganti)
						{
							#send an email to incharge person
                                    $to=getUserEmail($ganti);
                                            $namakaryawan=getNamaKaryawan($_SESSION['standard']['userid']);
                                            $subject="[Notifikasi]Pengalihan tugas a/n ".$namakaryawan;
                                            $body="<html>
                                                     <head>
                                                     <body>
                                                       <dd>Dengan Hormat,</dd><br>
                                                       <br>
                                                       Pada hari ini, tanggal ".date('d-m-Y')." karyawan a/n  ".$namakaryawan." melakukan ".$jnsIjin." (".$keperluan.")
                                                       dan mengalihkan pekerjaan kepada bapak/ibu untuk sementar.
                                                       <br>
                                                       <br>
                                                       Regards,<br>
                                                       Owl-Plantation System.
                                                     </body>
                                                     </head>
                                                   </html>
                                                   ";
                                            $kirim=kirimEmail($to,$subject,$body);#this has return but disobeying;		
							
							
						}
						
						
						
                    }
                    //mysql_query($sUp) or die(mysql_error());
                }
                 else
                {
                    exit("Error:Sudah ada keputusan");
                }
                if($atsSblm!=$atasan)
                {
                                    $to=getUserEmail($atsSblm);
                                            $namakaryawan=getNamaKaryawan($_SESSION['standard']['userid']);
                                            $subject="[Notifikasi]Pembatalan Persetujuan Ijin Keluar Kantor a/n ".$namakaryawan;
                                            $body="<html>
                                                     <head>
                                                     <body>
                                                       <dd>Dengan Hormat,</dd><br>
                                                       <br>
                                                       Pada hari ini, tanggal ".date('d-m-Y')." karyawan a/n  ".$namakaryawan." mengajukan Ijin/".$jnsIjin." (".$keperluan.")
                                                       kepada bapak/ibu. Untuk menindak-lanjuti, silahkan ikuti link dibawah.
                                                       <br>
                                                       <br>
                                                       Note: Sisa cuti ybs periode ".$periodec.":".$sisa." Hari
                                                       <br>
                                                       <br>
                                                       Regards,<br>
                                                       Owl-Plantation System.
                                                     </body>
                                                     </head>
                                                   </html>
                                                   ";
                                            $kirim=kirimEmail($to,$subject,$body);#this has return but disobeying;

                }
                break;
                default:
                break;
        }


?>