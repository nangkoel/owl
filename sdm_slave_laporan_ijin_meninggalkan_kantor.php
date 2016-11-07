<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
require_once('lib/fpdf.php');

$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
$_POST['tglijin']==''?$tglijin=tanggalsystem($_GET['tglijin']):$tglijin=tanggalsystem($_POST['tglijin']);
$_POST['krywnId']==''?$krywnId=$_GET['krywnId']:$krywnId=$_POST['krywnId'];
$_POST['kdOrg']==''?$kdOrg=$_GET['kdOrg']:$kdOrg=$_POST['kdOrg'];
$stat=$_POST['stat'];
$ket=$_POST['ket'];
$arrNmkary=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$arrKeputusan=array("0"=>$_SESSION['lang']['diajukan'],"1"=>$_SESSION['lang']['disetujui'],"2"=>$_SESSION['lang']['ditolak']);
$where=" tanggal='".$tglijin."' and karyawanid='".$krywnId."'";
$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$arragama=getEnum($dbname,'sdm_ijin','jenisijin');
$_POST['jnsCuti']==''?$jnsCuti=$_GET['jnsCuti']:$jnsCuti=$_POST['jnsCuti'];
$_POST['karyidCari']==''?$karyidCari=$_GET['karyidCari']:$karyidCari=$_POST['karyidCari'];
$atasan=$_POST['atasan'];

//exit("Error".$jmAwal);
        switch($proses){
                case'loadData':
                    if($karyidCari!='')
                    {
                        $cari.=" and a.karyawanid='".$karyidCari."'";
                    }
                    if($jnsCuti!='')
                    {
                        $cari.=" and jenisijin='".$jnsCuti."'";
                    }
                    if($kdOrg!='')
                    {
                        $cari.=" and b.lokasitugas='".$kdOrg."'";
                    }
                    if($_SESSION['empl']['tipelokasitugas']!='HOLDING') {
                        $cari.=" and tipe!='HOLDING' and b.kodeorganisasi='".$_SESSION['empl']['kodeorganisasi']."'";
                    }
                $limit=10;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;

                $ql2="select count(*) as jmlhrow from ".$dbname.".sdm_ijin a left join ".$dbname.".datakaryawan b 
                      on a.karyawanid=b.karyawanid left join ".$dbname.".organisasi c on b.lokasitugas=c.kodeorganisasi 
                      where 1=1 ".$cari." order by `tanggal` desc";// echo $ql2;
                //$ql2="select count(*) as jmlhrow from ".$dbname.".sdm_ijin where karyawanid in (select karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."') order by `tanggal` desc";// echo $ql2;
                $query2=mysql_query($ql2) or die(mysql_error());
                while($jsl=mysql_fetch_object($query2)){
                $jlhbrs= $jsl->jmlhrow;
                }

                //$slvhc="select * from ".$dbname.".sdm_ijin where  karyawanid in (select karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."') order by `tanggal` desc limit ".$offset.",".$limit." ";
                $slvhc="select a.*,b.lokasitugas from ".$dbname.".sdm_ijin a left join ".$dbname.".datakaryawan b
                        on a.karyawanid=b.karyawanid left join ".$dbname.".organisasi c on b.lokasitugas=c.kodeorganisasi 
                        where 1=1 ".$cari." order by `tanggal` desc limit ".$offset.",".$limit." ";
                $qlvhc=mysql_query($slvhc) or die(mysql_error());
                $user_online=$_SESSION['standard']['userid'];
                while($rlvhc=mysql_fetch_assoc($qlvhc))
                {
                if($_SESSION['language']=='ID'){
                        $dd=$rlvhc['jenisijin'];
                    }else{
                        switch($rlvhc['jenisijin']){
                            case 'TERLAMBAT':
                                $dd='Late for work';
                                break;
                            case 'KELUAR':
                                $dd='Out of Office';
                                break;         
                            case 'PULANGAWAL':
                                $dd='Home early';
                                break;     
                            case 'IJINLAIN':
                                $dd='Other purposes';
                                break;   
                            case 'CUTI':
                                $dd='Leave';
                                break;       
                            case 'MELAHIRKAN':
                                $dd='Maternity';
                                break;           
                            default:
                                $dd='Wedding, Circumcision or Graduation';
                                break;                              
                        }      
                    }
                    
                $no+=1;
                //ambil sisa cuti
                $sSisa="select sisa from ".$dbname.".sdm_cutiht where karyawanid='".$rlvhc['karyawanid']."' 
                        and periodecuti='".$rlvhc['periodecuti']."'";
                $qSisa=mysql_query($sSisa) or die(mysql_error($conn));
                $rSisa=mysql_fetch_assoc($qSisa);
                echo"
                <tr class=rowcontent>
                <td>".$no."</td>
                <td nowrap>".tanggalnormal($rlvhc['tanggal'])."</td>
                <td>".$arrNmkary[$rlvhc['karyawanid']]."</td>
                <td>".$optNm[$rlvhc['lokasitugas']]."</td>
                <td>".$rlvhc['keperluan']."</td>
                <td>".$dd."</td>
				
				<td>".$arrNmkary[$rlvhc['persetujuan1']]."</td>
                <td>".$rlvhc['darijam']."</td>
                <td>".$rlvhc['sampaijam']."</td>
                <td align=center>".$rlvhc['jumlahhari']."</td>
                <td align=center>".$rlvhc['periodecuti']."</td>
                <td align=center>".$rSisa['sisa']."</td>";
				
				
				
//atasan==============================                
                if($rlvhc['persetujuan1']==$_SESSION['standard']['userid'])
                {
                    if($rlvhc['stpersetujuan1']==0){
					#perubahan persetujuan, jika memilih atasan dari atasan/hrd maka di anggap setuju
                      echo"<td align=center>
                         <button class=mybutton id=dtlForm 
						 onclick=\"showAppForw('".tanggalnormal($rlvhc['tanggal'])."','".$_SESSION['lang']['form']."','".$rlvhc['karyawanid']."',event)\">".$_SESSION['lang']['disetujui']."</button>
						 <!--<button class=mybutton id=dtlForm onclick=appSetuju('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."')>".$_SESSION['lang']['disetujui']."</button>-->
                         <button class=mybutton id=dtlForm onclick=showAppTolak('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."',event)>".$_SESSION['lang']['ditolak']."</button>
                         </td>";
                    }
                    else if($rlvhc['stpersetujuan1']==2)
                       echo"<td align=center>".$_SESSION['lang']['ditolak']."</td>";
                   else if($rlvhc['stpersetujuan1']==1)
                        echo"<td align=center>".$_SESSION['lang']['disetujui']."</td>";
                   else if($rlvhc['stpersetujuan1']==0)
                        echo"<td align=center>".$_SESSION['lang']['wait_approval']."</td>";

                }
                else if($rlvhc['stpersetujuan1']==1)
                    echo"<td align=center>".$_SESSION['lang']['disetujui']."</td>";
                else if($rlvhc['stpersetujuan1']==0)
                    echo"<td align=center>".$_SESSION['lang']['wait_approval']."</td>";
                else 
                    echo"<td align=center>".$_SESSION['lang']['ditolak']."</td>";
					
//atasan dari atasan==============================       indra          
                if($rlvhc['persetujuan2']==$_SESSION['standard']['userid'])
                {
                    if($rlvhc['stpersetujuan2']==0){
					#perubahan persetujuan, jika hrd maka di anggap setuju
                      echo"<td align=center>
                         <button class=mybutton id=dtlForm onclick=\"showAppForw2('".tanggalnormal($rlvhc['tanggal'])."','".$_SESSION['lang']['form']." ".$_SESSION['lang']['hrd']."','".$rlvhc['karyawanid']."',event)\">".$_SESSION['lang']['disetujui']."</button>
						 <!--<button class=mybutton id=dtlForm onclick=appSetuju2('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."')>".$_SESSION['lang']['disetujui']."</button>-->
                         <button class=mybutton id=dtlForm onclick=showAppTolak2('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."',event)>".$_SESSION['lang']['ditolak']."</button>
                         </td>";
                    }
                    else if($rlvhc['stpersetujuan2']==2)
                       echo"<td align=center>".$_SESSION['lang']['ditolak']."</td>";
                   else if($rlvhc['stpersetujuan2']==1)
                        echo"<td align=center>".$_SESSION['lang']['disetujui']."</td>";
                   else if($rlvhc['stpersetujuan2']==0)
                        echo"<td align=center>".$_SESSION['lang']['wait_approval']."</td>";

                }else{
					if(intval($rlvhc['persetujuan2'])==0){
						 echo"<td align=center>&nbsp;</td>";
					}else{
						if($rlvhc['stpersetujuan2']==2)
							echo"<td align=center>".$_SESSION['lang']['ditolak']."</td>";
						else if($rlvhc['stpersetujuan2']==1)
							echo"<td align=center>".$_SESSION['lang']['disetujui']."</td>";
						else if($rlvhc['stpersetujuan2']==0)
							echo"<td align=center>".$_SESSION['lang']['wait_approval']."</td>";
					}
				}
					
					
//=============hrd                
                if($rlvhc['hrd']==$_SESSION['standard']['userid'])
                {
                    if($rlvhc['stpersetujuanhrd']==0)
                    {
                      echo"<td align=center><button class=mybutton id=dtlForm onclick=appSetujuHRD('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."')>".$_SESSION['lang']['disetujui']."</button>
                         <button class=mybutton id=dtlForm onclick=showAppTolakHRD('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."',event)>".$_SESSION['lang']['ditolak']."</button></td>";
                    }
                    else if($rlvhc['stpersetujuan1']==2)
                       echo"<td align=center>(Tunggu atasan)</td>"; 
                     else if($rlvhc['stpersetujuanhrd']==2)
                       echo"<td align=center>(".$_SESSION['lang']['ditolak']."</td>"; 
                    else if($rlvhc['stpersetujuanhrd']==1)
                        echo"<td align=center>".$_SESSION['lang']['disetujui']."</td>";
                    else if($rlvhc['stpersetujuanhrd']==0)
                         echo"<td align=center>".$_SESSION['lang']['wait_approval']."</td>";
                }
                else
                {
					if(intval($rlvhc['hrd'])==0){
						echo"<td align=center>&nbsp;</td>";
					}else{
						if($rlvhc['stpersetujuanhrd']=='0')
						echo"<td align=center>".$_SESSION['lang']['wait_approval']."</td>"; 
						else if($rlvhc['stpersetujuanhrd']=='1')
						echo"<td align=center>".$_SESSION['lang']['disetujui']."</td>";
						else 
						echo"<td align=center>".$_SESSION['lang']['ditolak']."</td>";
					}
				}
				echo"<td align=center>".$arrNmkary[$rlvhc['ganti']]."</td>";  
//======================================                

                   echo"<td align=center> <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"previewPdf('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."',event)\"></td>";


              }//end while
              if ((($page+1)*$limit)>$jlhbrs){
                  $disabledNext="disabled";
              }
              if ($page==0){
                  $disabledPrev="disabled";
              }
                echo"
                </tr><tr class=rowheader><td colspan=14 align=center>
                ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
                <button class=mybutton onclick=cariBast(".($page-1)."); ".$disabledPrev.">".$_SESSION['lang']['pref']."</button>
                <button class=mybutton onclick=cariBast(".($page+1)."); ".$disabledNext.">".$_SESSION['lang']['lanjut']."</button>
                </td>
                </tr>";
                break;
				
				
				
				
				
                case'cariData':
                    if($karyidCari!='')
                    {
                        $cari.=" and a.karyawanid='".$karyidCari."'";
                    }
                    if($jnsCuti!='')
                    {
                        $cari.=" and jenisijin='".$jnsCuti."'";
                    }
                    if($kdOrg!='')
                    {
                        $cari.=" and b.lokasitugas='".$kdOrg."'";
                    }
                if($_SESSION['empl']['tipelokasitugas']!='HOLDING') {
                    $cari.=" and b.kodeorganisasi='".$_SESSION['empl']['kodeorganisasi']."'";
                }
                $limit=10;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;

                $ql2="select count(*) as jmlhrow from ".$dbname.".sdm_ijin a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where a.karyawanid!='' ".$cari."  order by `tanggal` desc";// echo $ql2;
                //$ql2="select count(*) as jmlhrow from ".$dbname.".sdm_ijin where karyawanid in (select karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."') order by `tanggal` desc";// echo $ql2;
                $query2=mysql_query($ql2) or die(mysql_error());
                while($jsl=mysql_fetch_object($query2)){
                $jlhbrs= $jsl->jmlhrow;
                }

                //$slvhc="select * from ".$dbname.".sdm_ijin where  karyawanid in (select karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."') order by `tanggal` desc limit ".$offset.",".$limit." ";
                $slvhc="select * from ".$dbname.".sdm_ijin a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where a.karyawanid!='' ".$cari."  order by `tanggal` desc limit ".$offset.",".$limit." ";
                $qlvhc=mysql_query($slvhc) or die(mysql_error());
                $user_online=$_SESSION['standard']['userid'];
                while($rlvhc=mysql_fetch_assoc($qlvhc))
                {
                    if($_SESSION['language']=='ID'){
                        $dd=$rlvhc['jenisijin'];
                    }else{
                        switch($rlvhc['jenisijin']){
                            case 'TERLAMBAT':
                                $dd='Late for work';
                                break;
                            case 'KELUAR':
                                $dd='Out of Office';
                                break;         
                            case 'PULANGAWAL':
                                $dd='Home early';
                                break;     
                            case 'IJINLAIN':
                                $dd='Other purposes';
                                break;   
                            case 'CUTI':
                                $dd='Leave';
                                break;       
                            case 'MELAHIRKAN':
                                $dd='Maternity';
                                break;           
                            default:
                                $dd='Wedding, Circumcision or Graduation';
                                break;                              
                        }      
                    }                    
                $no+=1;
                //ambil sisa cuti
                $sSisa="select sisa from ".$dbname.".sdm_cutiht where karyawanid='".$rlvhc['karyawanid']."' and periodecuti='".$rlvhc['periodecuti']."'";
                $qSisa=mysql_query($sSisa) or die(mysql_error($conn));
                $rSisa=mysql_fetch_assoc($qSisa);
                echo"
                <tr class=rowcontent>
                <td>".$no."</td>
                <td nowrap>".tanggalnormal($rlvhc['tanggal'])."</td>
                <td>".$arrNmkary[$rlvhc['karyawanid']]."</td>
                <td>".$optNm[$rlvhc['lokasitugas']]."</td>
                <td>".$rlvhc['keperluan']."</td>
                <td>".$dd."</td>
                <td>".$arrNmkary[$rlvhc['persetujuan1']]."</td>
                <td>".$rlvhc['darijam']."</td>
                <td>".$rlvhc['sampaijam']."</td>
                <td align=center>".$rlvhc['jumlahhari']."</td>
                <td align=center>".$rlvhc['periodecuti']."</td>
                <td align=center>".$rSisa['sisa']."</td>";
//atasan==============================                
                if($rlvhc['persetujuan1']==$_SESSION['standard']['userid'])
                {
                    if($rlvhc['stpersetujuan1']==0)
                    {
                      echo"<td align=center>
                         <button class=mybutton id=dtlForm 
						 onclick=\"showAppForw('".tanggalnormal($rlvhc['tanggal'])."','".$_SESSION['lang']['form']."','".$rlvhc['karyawanid']."',event)\">".$_SESSION['lang']['disetujui']."</button>
                         <button class=mybutton id=dtlForm onclick=showAppTolak('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."',event)>".$_SESSION['lang']['ditolak']."</button>
                         </td>";
                    }
                    else if($rlvhc['stpersetujuan1']==2)
                       echo"<td align=center>".$_SESSION['lang']['ditolak']."</td>";
                    else
                        echo"<td align=center>".$_SESSION['lang']['disetujui']."</td>";

                }
                else if($rlvhc['stpersetujuan1']==1)
                    echo"<td align=center>".$_SESSION['lang']['disetujui']."</td>";
                else if($rlvhc['stpersetujuan1']==0)
                    echo"<td align=center>".$_SESSION['lang']['wait_approval']."</td>";
                else 
                    echo"<td align=center>".$_SESSION['lang']['ditolak']."</td>";
					
//atasan dr atasan==============================                
                if($rlvhc['persetujuan2']==$_SESSION['standard']['userid'])
                {
                    if($rlvhc['stpersetujuan2']==0)
                    {
                      echo"<td align=center>
                         <button class=mybutton id=dtlForm onclick=\"showAppForw2('".tanggalnormal($rlvhc['tanggal'])."','".$_SESSION['lang']['form']." ".$_SESSION['lang']['hrd']."','".$rlvhc['karyawanid']."',event)\">".$_SESSION['lang']['disetujui']."</button>
                         <button class=mybutton id=dtlForm onclick=showAppTolak2('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."',event)>".$_SESSION['lang']['ditolak']."</button>
                         </td>";
                    }
                    else if($rlvhc['stpersetujuan2']==2)
                       echo"<td align=center>".$_SESSION['lang']['ditolak']."</td>";
                    else
                        echo"<td align=center>".$_SESSION['lang']['disetujui']."</td>";

                }else{
                    if(intval($rlvhc['persetujuan2'])==0){
                             echo"<td align=center>&nbsp;</td>";
                    }else{
                            if($rlvhc['stpersetujuan2']==2)
                                    echo"<td align=center>".$_SESSION['lang']['ditolak']."</td>";
                            else if($rlvhc['stpersetujuan2']==1)
                                    echo"<td align=center>".$_SESSION['lang']['disetujui']."</td>";
                            else if($rlvhc['stpersetujuan2']==0)
                                    echo"<td align=center>".$_SESSION['lang']['wait_approval']."</td>";
                    }
                }
					
					
					
					
					
//=============hrd                
                if($rlvhc['hrd']==$_SESSION['standard']['userid'])
                {
                    if($rlvhc['stpersetujuanhrd']==0)
                    {
                      echo"<td align=center><button class=mybutton id=dtlForm onclick=appSetujuHRD('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."')>".$_SESSION['lang']['disetujui']."</button>
                         <button class=mybutton id=dtlForm onclick=showAppTolakHRD('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."',event)>".$_SESSION['lang']['ditolak']."</button></td>";
                    }
                    else if($rlvhc['stpersetujuan1']==2)
                       echo"<td align=center>(Tunggu atasan)</td>"; 
                     else if($rlvhc['stpersetujuanhrd']==2)
                       echo"<td align=center>(".$_SESSION['lang']['ditolak']."</td>"; 
                    else
                        echo"<td align=center>".$_SESSION['lang']['disetujui']."</td>";
                }
                else
                {
                        if(intval($rlvhc['hrd'])==0){
                                echo"<td align=center>&nbsp;</td>";
                        }else{
                                if($rlvhc['stpersetujuanhrd']=='0')
                                echo"<td align=center>".$_SESSION['lang']['wait_approval']."</td>"; 
                                else if($rlvhc['stpersetujuanhrd']=='1')
                                echo"<td align=center>".$_SESSION['lang']['disetujui']."</td>";
                                else 
                                echo"<td align=center>".$_SESSION['lang']['ditolak']."</td>";
                        }
                }
                
//======================================                
				echo"<td align=center>".$arrNmkary[$rlvhc['ganti']]."</td>";	
                   echo"<td align=center> <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"previewPdf('".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['karyawanid']."',event)\"></td>";
				

              }//end while
              if ((($page+1)*$limit)>$jlhbrs){
                  $disabledNext="disabled";
              }
              if ($page==0){
                  $disabledPrev="disabled";
              }
                echo"
                </tr><tr class=rowheader><td colspan=14 align=center>
                ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
                <button class=mybutton onclick=cariBast(".($page-1)."); ".$disabledPrev.">".$_SESSION['lang']['pref']."</button>
                <button class=mybutton onclick=cariBast(".($page+1)."); ".$disabledNext.">".$_SESSION['lang']['lanjut']."</button>
                </td>
                </tr>";
                break;
				
				
				
				
				
				
                case'appSetuju':
                $sket="select distinct jenisijin,stpersetujuan1,persetujuan1,hrd,tanggal,stpersetujuan2,persetujuan2 from ".$dbname.".sdm_ijin where ".$where."";
                $qKet=mysql_query($sket) or die(mysql_error($conn));
                $rKet=mysql_fetch_assoc($qKet);
//                if(($rKet['stpersetujuan1']=='0')&&($rKet['persetujuan1']==$_SESSION['standard']['userid']))
//                {
                    if($stat==1)
                    {
                        $ket="permintaaan ".$arrNmkary[$krywnId]." ".$arrKeputusan[$stat]."";
                    }

                    $sUpdate="update ".$dbname.".sdm_ijin  set stpersetujuan1='".$stat."',komenst1='".$ket."' where ".$where."";
                    if(mysql_query($sUpdate))
                    {
                          #send an email to incharge person
                            		$to=getUserEmail($rKet['persetujuan2']);////email ke persetujuan 2 setelah persetujuan atasan
                                    $namakaryawan=$arrNmkary[$krywnId];
                                    $subject="[Notifikasi]".$rKet['jenisijin']." a/n ".$namakaryawan;
                                    $body="<html>
                                             <head>
                                             <body>
                                               <dd>Dengan Hormat,</dd><br>
                                               <br>
                                               Permintaan persetujuan Ijin/Cuti pada  ".tanggalnormal($rKet['tanggal'])." karyawan a/n  ".$namakaryawan." telah ".$arrKeputusan[$stat].". 
                                               Oleh atasan ybs. Selanjutnya, mohon untuk memberikan persetujuan lanjutan. Untuk melihat lebih detail, silahkan ikuti link dibawah.
                                               <br>
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
                    else
                    {
                        echo "DB Error : ".mysql_error($conn);     
                    }
//                }
//                else
//                {
//                    exit("Error:Sudah memiliki keputusan");
//                }
                break;
				
				
				
                case'appSetuju2':
                    $sket="select distinct jenisijin,hrd,tanggal,stpersetujuan2,persetujuan2,stpersetujuan1,persetujuan1 from ".$dbname.".sdm_ijin where ".$where."";
                    $qKet=mysql_query($sket) or die(mysql_error($conn));
                    $rKet=mysql_fetch_assoc($qKet);


                if($rKet['stpersetujuan1']=='2')
                        exit("Error:Sorry you can't approve this document,  because the first approver has been rejected");
                else;
				
				
				
//                if(($rKet['stpersetujuan1']=='0')&&($rKet['persetujuan1']==$_SESSION['standard']['userid']))
//                {
                    if($stat==1)
                    {
                        $ket="permintaaan ".$arrNmkary[$krywnId]." ".$arrKeputusan[$stat]."";
                    }

                    $sUpdate="update ".$dbname.".sdm_ijin  set stpersetujuan2='".$stat."',komenstPer2='".$ket."' where ".$where."";
                    if(mysql_query($sUpdate))
                    {
                          #send an email to incharge person
                            		$to=getUserEmail($rKet['hrd']);////email ke hrd setelah persetujuan atasan
                                    $namakaryawan=$arrNmkary[$krywnId];
                                    $subject="[Notifikasi] ".$rKet['jenisijin']." a/n ".$namakaryawan;
                                    $body="<html>
                                             <head>
                                             <body>
                                               <dd>Dengan Hormat,</dd><br>
                                               <br>
                                               Permintaan persetujuan Ijin/Cuti pada  ".tanggalnormal($rKet['tanggal'])." karyawan a/n  ".$namakaryawan." telah ".$arrKeputusan[$stat].". 
                                               Oleh atasan ybs. Selanjutnya, mohon persetujuan dari HRD. Untuk melihat lebih detail, silahkan ikuti link dibawah.
                                               <br>
                                               <br>
                                               <br>
                                               Regards,<br>
                                               Owl-Plantation System.
                                             </body>
                                             </head>
                                           </html>
                                           ";//exit("Error:$to");
                                    $kirim=kirimEmail($to,$subject,$body);#this has return but disobeying;
                    }
                    else
                    {
                        echo "DB Error : ".mysql_error($conn);     
                    }
//                }
//                else
//                {
//                    exit("Error:Sudah memiliki keputusan");
//                }
                break;
                case 'appSetujuHRD':
                $sket="select distinct darijam,sampaijam,jumlahhari,jenisijin,stpersetujuan1,stpersetujuan2,stpersetujuanhrd,hrd,tanggal,periodecuti from ".$dbname.".sdm_ijin where ".$where."";   
                $qKet=mysql_query($sket) or die(mysql_error($conn));
                $rKet=mysql_fetch_assoc($qKet);
//                if(($rKet['stpersetujuanhrd']=='0')&&($rKet['hrd']==$_SESSION['standard']['userid']))
//                {
                    if($stat==1)
                    {
                        $ket="permintaaan ".$arrNmkary[$krywnId]." ".$arrKeputusan[$stat]."";
                        //===============insert to sdm_cuti

                        $stru="select lokasitugas from ".$dbname.".datakaryawan where karyawanid=".$krywnId;
                        $resu=mysql_query($stru);
                        $kodeorg='';
                        while($baru=mysql_fetch_object($resu))
                        {
                            $kodeorg=$baru->lokasitugas;
                        }
                        if($kodeorg=='')
                            exit('warning : Karyawan tidak memiliki lokasi tugas');

                        if($rKet['jenisijin']=='CUTI' or $rKet['jenisijin']=='MELAHIRKAN' or $rKet['jenisijin']=='KAWIN/SUNATAN/WISUDA')
                        {
                              //insert to cuti
                            $str="insert into ".$dbname.".sdm_cutidt 
                                (kodeorg,karyawanid,periodecuti,daritanggal,
                                    sampaitanggal,jumlahcuti,keterangan
                                    )
                                values('".$kodeorg."',".$krywnId.",
                                    '".$rKet['periodecuti']."','".substr($rKet['darijam'],0,10)."','".substr($rKet['sampaijam'],0,10)."',".$rKet['jumlahhari'].",'".$rKet['jenisijin']."'
                                    )";

                            if(mysql_query($str)){
                                //ambil sum jumlah diambil dan update table header
                                $strx="select sum(jumlahcuti) as diambil from ".$dbname.".sdm_cutidt
                                    where kodeorg='".$kodeorg."' and keterangan = 'CUTI'
                                        and karyawanid=".$krywnId."
                                        and periodecuti='".$rKet['periodecuti']."'";
                                $diambil=0;
                                $resx=mysql_query($strx);
                                while($barx=mysql_fetch_object($resx)){
                                        $diambil=$barx->diambil;
                                }
                                if($rKet['jenisijin']=='CUTI'){
									if($diambil==''){
									    $diambil=0;
									}
									$strup="update ".$dbname.".sdm_cutiht set diambil=".$diambil.",sisa=(hakcuti-".$diambil.")	where kodeorg='".$kodeorg."' and karyawanid=".$krywnId." and periodecuti='".$rKet['periodecuti']."'";
									if(mysql_query($strup)){
										$tgl1=substr($rKet['darijam'],0,10);
										$tgl2=substr($rKet['sampaijam'],0,10);
										$test = dates_inbetween($tgl1, $tgl2);
										$whrTp="karyawanid='".$krywnId."'";
										$optTipe=makeOption($dbname,'datakaryawan','karyawanid,tipekaryawan',$whrTp);
										$optSub=makeOption($dbname,'datakaryawan','karyawanid,subbagian',$whrTp);
										$optLksi=makeOption($dbname,'datakaryawan','karyawanid,lokasitugas',$whrTp);
										$whrReg="kodeunit='".$optLksi[$krywnId]."'";
										$optReg=makeOption($dbname,'bgt_regional_assignment','kodeunit,regional',$whrReg);
										if($optTipe[$krywnId]>2){
											#jika karyawan bertipe KHT/KBL di isikan absennya jika jenis ijinnya adalah cuti
											if($optTipe[$krywnId]==4){
												$whrGj="karyawanid='".$krywnId."' and tahun='".substr($rKet['darijam'],0,4)."' and idkomponen=1";
												$optGaji=makeOption($dbname,'sdm_5gajipokok','karyawanid,jumlah',$whrGj);
											}
											if($optSub[$krywnId]!=''){
												$kdOrg=$optSub[$krywnId];
											}else{
												$kdOrg=$optLksi[$krywnId];
											}
											if($optGaji[$krywnId]==''){
												$_POST['insentif']=0;
											}else{
												@$_POST['insentif']=$optGaji[$krywnId]/25;
											}
											foreach($test as $rwTgl=>$dtTgl){
													$qwe=date('D', strtotime($dtTgl));
													if($qwe=='Sun'){
														continue;
													}else{
														$whr="regional='".$optReg[$optLksi[$krywnId]]."' and tanggal='".$dtTgl."'";
														$optLbr=makeOption($dbname,'sdm_5harilibur','regional,tanggal',$whr);
														if($optLbr[$optReg[$optLksi[$krywnId]]]!=''){
															continue;
														}
													}
													$sCek="select * from ".$dbname.".sdm_absensiht where tanggal='".$dtTgl."' and kodeorg='".$kdOrg."'";
													$qCek=mysql_query($sCek) or die(mysql_error($conn));
													$rCek=mysql_num_rows($qCek);
													if($rCek!=0){
														$sdel="delete from ".$dbname.".sdm_absensidt where karyawanid='".$krywnId."' and tanggal='".$dtTgl."'";
														if(mysql_query($sdel)){
															
															$sDetIns="insert into ".$dbname.".sdm_absensidt (`kodeorg`,`tanggal`, `karyawanid`, `shift`, `absensi`, `jam`,`jamPlg`, `penjelasan`,`penaltykehadiran`,`premi`,`insentif`) 
															values ('".$kdOrg."','".$dtTgl."','".$krywnId."','".$shifTid."','C','00:00:00','00:00:00','".$ket."','0','0',".$_POST['insentif'].")";
															  if(!mysql_query($sDetIns)){
																exit("warning: ".mysql_error($conn)."____".$sDetIns);
															  }
														}														
													}else{
														#ambil periode gaji
														$sPrd="select distinct periode from ".$dbname.".sdm_5periodegaji where tanggalsampai>='".$dtTgl."' and tglcutoff<='".$dtTgl."' and kodeorg='".substr($dtTgl,0,4)."'";
														$qPrd=mysql_query($sPrd) or die(mysql_error($conn));
														$rPrd=mysql_fetch_assoc($qPrd);
														if($rPrd['periode']==''){
															$rPrd['periode']=substr($dtTgl,0,7);
														}
															#insert ke sdm_absensidt
															$sdel="delete from ".$dbname.".sdm_absensidt where karyawanid='".$krywnId."' and tanggal='".$dtTgl."'";
															if(mysql_query($sdel)){
																$sDetIns="insert into ".$dbname.".sdm_absensidt (`kodeorg`,`tanggal`, `karyawanid`, `shift`, `absensi`, `jam`,`jamPlg`, `penjelasan`,`penaltykehadiran`,`premi`,`insentif`) 
																values ('".$kdOrg."','".$dtTgl."','".$krywnId."','".$shifTid."','C','00:00:00','00:00:00','".$ket."','0','0',".$_POST['insentif'].")";
																  if(!mysql_query($sDetIns)){
																	exit("warning: ".mysql_error($conn)."____".$sDetIns);
																  }
															}
													}
											}
										}
									}
								}
                            }  
                            else
                            {
                                echo mysql_error($conn);
                                exit("Error: Update table cuti");
                            } 
                    }
                    $sUpdate="update ".$dbname.".sdm_ijin  set stpersetujuanhrd='".$stat."',komenst2='".$ket."' where ".$where."";
                    if(mysql_query($sUpdate))
                    {
                        $tambahan="";
                        if ($rKet['stpersetujuan1']==1 && $rKet['stpersetujuan2']==1 && $stat==1){
                            $tambahan="<br>Oleh karena semua atasan sudah menyetujui, maka sisa cuti ybs untuk periode ".$rKet['periodecuti']." otomatis akan berkurang.";
                        }
                          #send an email to incharge person
                            $to=getUserEmail($rKet['hrd']);
                                    $namakaryawan=getNamaKaryawan($krywnId);
                                    $subject="[Notifikasi] ".$rKet['jenisijin']." a/n ".$namakaryawan;
                                    $body="<html>
                                             <head>
                                             <body>
                                               <dd>Dengan Hormat,</dd><br>
                                               <br>
                                               Permintaan persetujuan Ijin/Cuti pada  ".tanggalnormal($rKet['tanggal'])." karyawan a/n  ".$namakaryawan." telah ".$arrKeputusan[$stat].".".$tambahan."
                                               <br>Untuk melihat lebih detail, silahkan ikuti link dibawah.
                                               <br>
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
                    else
                    {
                        echo "DB Error : ".mysql_error($conn);     
                    }
                }
//                }
//                else
//                {
//                    exit("Error:Sudah memiliki keputusan");
//                }    
                break;

                case'prevPdf':

                class PDF extends FPDF
{

        function Header()
        {
            
            $this->SetMargins(15,10,0);
                if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
            
            //$path='images/logo.jpg';
            $this->Image($path,15,2,50);	
                $this->SetFont('Arial','B',15);
                $this->SetFillColor(255,255,255);	
                //$this->SetY(10);
                $this->SetXY(50,15);
         
                
            $this->Cell(60,5,$_SESSION['org']['namaorganisasi'],0,1,'C');	 
                $this->SetFont('Arial','',15);
            $this->Cell(190,5,'',0,1,'C');
                $this->SetFont('Arial','',6); 
                $this->SetY(30);
                $this->SetX(163);
        $this->Cell(30,10,'PRINT TIME : '.date('d-m-Y H:i:s'),0,1,'L');		
                $this->Line(10,32,200,32);	   

        }

        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial','I',8);
            $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
        }

}

  $str="select * from ".$dbname.".sdm_ijin where ".$where."";	
  //exit("Error".$str);
  $res=mysql_query($str);
  while($bar=mysql_fetch_object($res))
  {
	$ganti=$bar->ganti;
                $jabatan='';
                $namakaryawan='';
                $bagian='';	
                $karyawanid='';
                 $strc="select a.nik,a.namakaryawan,a.karyawanid,a.bagian,b.namajabatan 
                    from ".$dbname.".datakaryawan a left join  ".$dbname.".sdm_5jabatan b
                        on a.kodejabatan=b.kodejabatan
                        where a.karyawanid=".$bar->karyawanid;
      $resc=mysql_query($strc);
          while($barc=mysql_fetch_object($resc))
          {
                $jabatan=$barc->namajabatan;
                $namakaryawan=$barc->namakaryawan;
                $bagian=$barc->bagian;
                $karyawanid=$barc->karyawanid;
                $nikKar=$barc->nik;
          }

          //===============================	  
            
                $perstatus=$bar->stpersetujuan1;
                $tgl=tanggalnormal($bar->tanggal);
                $kperluan=$bar->keperluan;
                $persetujuan=$bar->persetujuan1;
				 $persetujuan2=$bar->persetujuan2;
                                 
                                 $perstatus2=$bar->stpersetujuan2;
                                 //echo $persetujuan2;
                $jns=$bar->jenisijin;
                $jmDr=$bar->darijam;
                $jmSmp=$bar->sampaijam;
                $koments=$bar->komenst1;
                $ket=$bar->keterangan;
                $periode=$bar->periodecuti;
                $sthrd=$bar->stpersetujuanhrd;
                $hk=$bar->jumlahhari;
                $hrd=$bar->hrd;
                $koments2=$bar->komenst2;
                if($_SESSION['language']=='ID'){
                        $dd=$jns;
                    }else{
                        switch($jns){
                            case 'TERLAMBAT':
                                $dd='Late for work';
                                break;
                            case 'KELUAR':
                                $dd='Out of Office';
                                break;         
                            case 'PULANGAWAL':
                                $dd='Home early';
                                break;     
                            case 'IJINLAIN':
                                $dd='Other purposes';
                                break;   
                            case 'CUTI':
                                $dd='Leave';
                                break;       
                            case 'MELAHIRKAN':
                                $dd='Maternity';
                                break;           
                            default:
                                $dd='Wedding, Circumcision or Graduation';
                                break;                              
                        }  
                    }               
                
                
        //ambil bagian,jabatan persetujuan atasan
                $perjabatan='';
                $perbagian='';
                $pernama='';
        $strf="select a.bagian,b.namajabatan,a.namakaryawan from ".$dbname.".datakaryawan a left join
               ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan
                   where karyawanid=".$persetujuan;	   
        $resf=mysql_query($strf);
        while($barf=mysql_fetch_object($resf))
        {
                $perjabatan=$barf->namajabatan;
                $perbagian=$barf->bagian;
                $pernama=$barf->namakaryawan;
        }
		
		
		//ambil bagian,jabatan persetujuan atasan
                $perjabatan2='';
                $perbagian2='';
                $pernama2='';
        $strf="select a.bagian,b.namajabatan,a.namakaryawan from ".$dbname.".datakaryawan a left join
               ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan
                   where karyawanid=".$persetujuan2;	   
        $resf=mysql_query($strf);
        while($barf=mysql_fetch_object($resf))
        {
                $perjabatan2=$barf->namajabatan;
                $perbagian2=$barf->bagian;
                $pernama2=$barf->namakaryawan;
        }
		
		
        //ambil bagian,jabatan persetujuan hrd
                $perjabatanhrd='';
                $perbagianhrd='';
                $pernamahrd='';
        $strf="select a.bagian,b.namajabatan,a.namakaryawan from ".$dbname.".datakaryawan a left join
               ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan
                   where karyawanid=".$hrd;	   
        $resf=mysql_query($strf);
        while($barf=mysql_fetch_object($resf))
        {
                $perjabatanhrd=$barf->namajabatan;
                $perbagianhrd=$barf->bagian;
                $pernamahrd=$barf->namakaryawan;
        }       
  }

        $pdf=new PDF('P','mm','A4');
        $pdf->SetFont('Arial','B',14);
        $pdf->AddPage();
        $pdf->SetY(40);
        $pdf->SetX(20);
        $pdf->SetFillColor(255,255,255); 
        $pdf->Cell(175,5,strtoupper($_SESSION['lang']['ijin']."/".$_SESSION['lang']['cuti']),0,1,'C');
        $pdf->SetX(20);
        $pdf->SetFont('Arial','',8);
        //$pdf->Cell(175,5,'NO : '.$notransaksi,0,1,'C');	

        
        //$nikKar=  makeOption($dbname, 'datakaryawan', 'karyawanid,nik');
        $nmBag=makeOption($dbname,'sdm_5departemen','kode,nama');
        
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();	
        $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['tanggal'],0,0,'L');	
                $pdf->Cell(50,5," : ".$tgl,0,1,'L');	
        $pdf->SetX(20);			
        $pdf->Cell(30,5,$_SESSION['lang']['nokaryawan'],0,0,'L');	
                $pdf->Cell(50,5," : ".$nikKar,0,1,'L');	
        $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['namakaryawan'],0,0,'L');	
                $pdf->Cell(50,5," : ".$namakaryawan,0,1,'L');	
        $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['bagian'],0,0,'L');	
                $pdf->Cell(50,5," : ".$nmBag[$bagian],0,1,'L');	
        $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['functionname'],0,0,'L');	
                $pdf->Cell(50,5," : ".$jabatan,0,1,'L');
        $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['keperluan'],0,0,'L');	
                $pdf->Cell(50,5," : ".$kperluan,0,1,'L');	
        $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['jenisijin'],0,0,'L');	
                $pdf->Cell(50,5," : ".$dd,0,1,'L');	
        $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['keterangan'],0,0,'L');	
                $pdf->Cell(50,5," : ".$ket,0,1,'L');	
         $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['pengabdian']." ".$_SESSION['lang']['tahun'],0,0,'L');	
                $pdf->Cell(50,5," : ".$periode,0,1,'L');               
        $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['dari'],0,0,'L');	
                $pdf->Cell(50,5," : ".$jmDr,0,1,'L');	
        $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['tglcutisampai'],0,0,'L');	
                $pdf->Cell(50,5," : ".$jmSmp,0,1,'L');	
        $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['jumlah']." ".$_SESSION['lang']['hari'],0,0,'L');	
                $pdf->Cell(50,5," : ".$hk." ".$_SESSION['lang']['hari'],0,1,'L');	
		
		$pdf->SetX(20);	
        $pdf->Cell(30,5,'Karyawan Pengganti',0,0,'L');	
                $pdf->Cell(50,5," : ".$arrNmkary[$ganti],0,1,'L');	
						
				
				




        $pdf->Ln();	
        $pdf->SetX(20);	
        $pdf->SetFont('Arial','B',8);		
        $pdf->Cell(172,5,strtoupper($_SESSION['lang']['approval_status']),0,1,'L');	
        $pdf->SetX(30);
                $pdf->Cell(30,5,strtoupper($_SESSION['lang']['bagian']),1,0,'C');
                $pdf->Cell(50,5,strtoupper($_SESSION['lang']['namakaryawan']),1,0,'C');			
                $pdf->Cell(40,5,strtoupper($_SESSION['lang']['functionname']),1,0,'C');
                $pdf->Cell(37,5,strtoupper($_SESSION['lang']['keputusan']),1,1,'C');	 			

        $pdf->SetFont('Arial','',8);

        $pdf->SetX(30);
                $pdf->Cell(30,5,$perbagian,1,0,'L');
                $pdf->Cell(50,5,$pernama,1,0,'L');			
                $pdf->Cell(40,5,$perjabatan,1,0,'L');
                $pdf->Cell(37,5,$arrKeputusan[$perstatus],1,1,'L');
		 $pdf->SetX(30);
                $pdf->Cell(30,5,$perbagian2,1,0,'L');
                $pdf->Cell(50,5,$pernama2,1,0,'L');			
                $pdf->Cell(40,5,$perjabatan2,1,0,'L');
                $pdf->Cell(37,5,$arrKeputusan[$perstatus2],1,1,'L');		
        $pdf->SetX(30);
                $pdf->Cell(30,5,$perbagianhrd,1,0,'L');
                $pdf->Cell(50,5,$pernamahrd,1,0,'L');			
                $pdf->Cell(40,5,$perjabatanhrd,1,0,'L');
                $pdf->Cell(37,5,$arrKeputusan[$sthrd],1,1,'L');

    $pdf->Ln();               

        $pdf->SetX(20);                
        $pdf->Cell(30,5,$_SESSION['lang']['keputusan']." ".$_SESSION['lang']['atasan'],0,0,'L');	
                $pdf->Cell(50,5," : ".$koments,0,1,'L');	

        $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['keputusan']." ".$_SESSION['lang']['hrd'],0,0,'L');	
                $pdf->Cell(50,5," : ".$koments2,0,1,'L');


   $pdf->Ln();	
   $pdf->Ln();	
   $pdf->Ln();	


//footer================================
    $pdf->Ln();		
        $pdf->Output();

                break;
                case'getExcel':
               $tab.=" 
                <table class=sortable cellspacing=1 border=1 width=80%>
                <thead>
                <tr  >
                <td align=center bgcolor='#DFDFDF'>No.</td>
                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['tanggal']."</td>
                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['nama']."</td>
                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['keperluan']."</td>
                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['jenisijin']."</td>  
                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['persetujuan']."</td>    
                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['approval_status']."</td>
                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['dari']."  ".$_SESSION['lang']['jam']."</td>
                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['tglcutisampai']."  ".$_SESSION['lang']['jam']."</td>
                </tr>  
                </thead><tbody>";
                $cari='';
                if($karyidCari!='')
                {
                    $cari.=" and a.karyawanid='".$karyidCari."'";
                }
                if($jnsCuti!='')
                {
                    $cari.=" and jenisijin='".$jnsCuti."'";
                }
                if($kdOrg!='')
                {
                    $cari.=" and b.lokasitugas='".$kdOrg."'";
                }
                if($_SESSION['empl']['tipelokasitugas']!='HOLDING') {
                    $cari.=" and tipe!='HOLDING' and b.kodeorganisasi='".$_SESSION['empl']['kodeorganisasi']."'";
                }
                $slvhc="select * from ".$dbname.".sdm_ijin a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
                        left join ".$dbname.".organisasi c on b.lokasitugas=c.kodeorganisasi 
                        where 1=1 ".$cari."  order by `tanggal` desc";
                $qlvhc=mysql_query($slvhc) or die(mysql_error());
                $user_online=$_SESSION['standard']['userid'];
                while($rlvhc=mysql_fetch_assoc($qlvhc))
                {
                    if($_SESSION['language']=='ID'){
                        $dd=$rlvhc['jenisijin'];
                    }else{
                        switch($rlvhc['jenisijin']){
                            case 'TERLAMBAT': $dd='Late for work'; break;
                            case 'KELUAR': $dd='Out of Office'; break;         
                            case 'PULANGAWAL': $dd='Home early'; break;     
                            case 'IJINLAIN': $dd='Other purposes'; break;   
                            case 'CUTI': $dd='Leave'; break;       
                            case 'MELAHIRKAN': $dd='Maternity'; break;           
                            default: $dd='Wedding, Circumcision or Graduation'; break;                              
                        }      
                    }                     
                $no+=1;

                 $tab.="
                <tr class=rowcontent>
                <td>".$no."</td>
                <td>".$rlvhc['tanggal']."</td>
                <td>".$arrNmkary[$rlvhc['karyawanid']]."</td>
                <td>".$rlvhc['keperluan']."</td>
                <td>".$dd."</td>
                <td>".$arrNmkary[$rlvhc['persetujuan1']]."</td>
                <td>".$arrKeputusan[$rlvhc['stpersetujuan1']]."</td>
                <td>".$rlvhc['darijam']."</td>
                <td>".$rlvhc['sampaijam']."</td>";
                }
                $tab.="</tbody></table>";
                $nop_="listizinkeluarkantor";
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
                case'formForward':
//                    $optKary="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                    if ($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
                         $sKary="select distinct karyawanid,namakaryawan from ".$dbname.".datakaryawan 
                            where alokasi='1' and lokasitugas like '%HO' and karyawanid not in('".$_SESSION['standard']['userid']."','".$krywnId."') 
                            and kodegolongan>='6A' and tanggalkeluar='0000-00-00' order by namakaryawan asc";
                    } else {
                         $sKary="select distinct karyawanid,namakaryawan from ".$dbname.".datakaryawan 
                            where alokasi='1' and karyawanid not in('".$_SESSION['standard']['userid']."','".$krywnId."') 
                            and kodegolongan>='6A' and tanggalkeluar='0000-00-00' and (lokasitugas in (select kodeunit from ".$dbname.
                            ".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') or lokasitugas like '%HO') order by namakaryawan asc";
                    }
                $qKary=mysql_query($sKary) or die(mysql_error($sKary));
                while($rKary=mysql_fetch_assoc($qKary))
                {
                    $optKary.="<option value='".$rKary['karyawanid']."'>".$rKary['namakaryawan']."</option>";
                }
                $tab.="<fieldset><legend>".$arrNmkary[$krywnId].", ".$_SESSION['lang']['tanggal']." : ".tanggalnormal($tglijin)."</legend><table cellpadding=1 cellspacing=1 border=0>";
                $tab.="<tr><td colspan=2>Submit to the next approval</td></tr>";
                $tab.="<tr><td>".$_SESSION['lang']['namakaryawan']."</td><td><select id=karywanId>".$optKary."</select></td></tr>";
                $tab.="<tr><td colspan=2><button class=mybutton title=\" Submit to the next level \" id=dtlForm onclick=AppForw()>".$_SESSION['lang']['disetujui']."</button>
                       <button class=mybutton onclick=cancelForw() title=\" Close this form \">".$_SESSION['lang']['cancel']."</button></td></tr></table>";
                $tab.="</table></fieldset><input type='hidden' id=karyaid value=".$krywnId." /><input type=hidden id=tglIjin value=".tanggalnormal($tglijin)."/>";
                echo $tab;
                break;
				case'formForward2':
				#HRD
				#Modifikasi filter hanya utk Manager HRD -- by Cosa
				if ($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
					if ($_SESSION['empl']['bagian']=='HRD' && $_SESSION['empl']['kodejabatan']=='151'){
						$str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
							  where tipekaryawan=0 and (bagian='HRD' or kodejabatan in (21,33)) and tanggalkeluar='0000-00-00' and 
							  karyawanid <>".$_SESSION['standard']['userid']. "  order by namakaryawan";
					} else {
						$str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
							  where tipekaryawan=0 and (bagian='HRD' or kodejabatan=151) and tanggalkeluar='0000-00-00' and 
							  lokasitugas like '%HO'  order by namakaryawan";
					}
				} else {
					$str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
						  where tipekaryawan=0 and bagian='HRD' and tanggalkeluar='0000-00-00' and 
						  karyawanid <>".$_SESSION['standard']['userid']. " and kodejabatan in (21,33) order by namakaryawan";
				}
				$res=mysql_query($str);
				while($bar=mysql_fetch_object($res))
				{
					$optKary.="<option value='".$bar->karyawanid."'>".$bar->namakaryawan."</option>";
				}
                $tab.="<fieldset><legend>".$arrNmkary[$krywnId].", ".$_SESSION['lang']['tanggal']." : ".tanggalnormal($tglijin)."</legend><table cellpadding=1 cellspacing=1 border=0>";
                $tab.="<tr><td colspan=2>Submit to the next approval by HRD</td></tr>";
                $tab.="<tr><td>".$_SESSION['lang']['namakaryawan']."</td><td><select id=karywanId>".$optKary."</select></td></tr>";
                $tab.="<tr><td colspan=2><button class=mybutton id=dtlForm title=\" Submit to HRD \" onclick=AppForw2()>".$_SESSION['lang']['disetujui']."</button>
                       <button class=mybutton onclick=cancelForw() title=\" Close this form \">".$_SESSION['lang']['cancel']."</button></td></tr></table>";
                $tab.="</table></fieldset><input type='hidden' id=karyaid value=".$krywnId." /><input type=hidden id=tglIjin value=".tanggalnormal($tglijin)."/>";
                echo $tab;
                break;
                case'forwardData':
                    $sCek="select * from ".$dbname.".datakaryawan where karyawanid='".$atasan."'";
                    $rCek=fetchData($sCek);
                    if ($rCek[0]['bagian']=='HRD' and $rCek[0]['kodegolongan']>='7A'){
                        $sup="update ".$dbname.".sdm_ijin set persetujuan2='".$_SESSION['standard']['userid']."',hrd='".$atasan."',stpersetujuan1=1,stpersetujuan2=1 where ".$where."";
                    } else {
                        $sup="update ".$dbname.".sdm_ijin set persetujuan2='".$atasan."',stpersetujuan1=1 where ".$where."";
                    }
                    
                    if(mysql_query($sup))
                    {
                        $sKar="select distinct * from ".$dbname.".sdm_ijin where ".$where."";
                        $qKar=mysql_query($sKar) or die(mysql_error($conn));
                        $rKar=mysql_fetch_assoc($qKar);
                        $strf="select sisa from ".$dbname.".sdm_cutiht where karyawanid=".$krywnId." 
                        and periodecuti=".$rKar['periodecuti'];
                        $res=mysql_query($strf);

                        $sisa='';
                        while($barf=mysql_fetch_object($res))
                        {
                        $sisa=$barf->sisa;
                        }
                        if($sisa=='')
                        $sisa=0;
                    $to=getUserEmail($atasan);
                    $namakaryawan=getNamaKaryawan($krywnId);
                    $subject="[Notifikasi]".$rKar['jenisijin']." a/n ".$namakaryawan;
                    $body="<html>
                    <head>
                    <body>
                    <dd>Dengan Hormat,</dd><br>
                    <br>
                    Pada hari ini, tanggal ".date('d-m-Y')." karyawan a/n  ".$namakaryawan." mengajukan Ijin/".$rKar['jenisijin']." (".$rKar['keperluan'].")
                    kepada bapak/ibu. Untuk menindak-lanjuti, silahkan ikuti link dibawah.
                    <br>
                    <br>
                    Note: Sisa cuti ybs periode ".$rKar['periodecuti'].": ".$sisa." Hari
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
                    else
                    {
                        echo "DB Error : ".mysql_error($conn);
                    }
                break;
				case'forwardData2':
                    $sup="update ".$dbname.".sdm_ijin set hrd='".$atasan."',stpersetujuan2=1 where ".$where."";
                    if(mysql_query($sup))
                    {
                        $sKar="select distinct * from ".$dbname.".sdm_ijin where ".$where."";
                        $qKar=mysql_query($sKar) or die(mysql_error($conn));
                        $rKar=mysql_fetch_assoc($qKar);
                        $strf="select sisa from ".$dbname.".sdm_cutiht where karyawanid=".$krywnId." 
                        and periodecuti=".$rKar['periodecuti'];
                        $res=mysql_query($strf);

                        $sisa='';
                        while($barf=mysql_fetch_object($res))
                        {
                        $sisa=$barf->sisa;
                        }
                        if($sisa=='')
                        $sisa=0;
                    $to=getUserEmail($atasan);
                    $namakaryawan=getNamaKaryawan($krywnId);
                    $subject="[Notifikasi]".$rKar['jenisijin']." a/n ".$namakaryawan;
                    $body="<html>
                    <head>
                    <body>
                    <dd>Dengan Hormat,</dd><br>
                    <br>
                    Pada hari ini, tanggal ".date('d-m-Y')." karyawan a/n  ".$namakaryawan." mengajukan Ijin/".$rKar['jenisijin']." (".$rKar['keperluan'].")
                    kepada bapak/ibu. Untuk menindak-lanjuti, silahkan ikuti link dibawah.
                    <br>
                    <br>
                    Note: Sisa cuti ybs periode ".$rKar['periodecuti'].": ".$sisa." Hari
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
                    else
                    {
                        echo "DB Error : ".mysql_error($conn);
                    }
                break;
                default:
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