<?php
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
require_once('config/connection.php');
require_once('lib/zLib.php');

  $userid=$_POST['userid'];
  $component  =$_POST['idx'];
  $total=$_POST['total'];
  $start=$_POST['start'];
  $lama=$_POST['lama'];
  $active=$_POST['active'];

  $method=$_POST['method'];


$nmOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
  $dt=mktime(0,0,0,intval(substr($start,5,2))+($lama-1),15,substr($start,0,4));
  $end=date('Y-m',$dt);


;	 

				





                if($method=='update')
                {
         $angsbln=$total/$lama;
                 //update
                 $str="update ".$dbname.".sdm_angsuran
                       set total=".$total.",updateby='".$_SESSION['standard']['username']."',
                           active=".$active.",jlhbln=".$lama.",
                           start='".$start."',end='".$end."',bulanan=".$angsbln."
                           where karyawanid=".$userid."
                           and jenis=".$component;
				//exit("Error:$str");		   
                }
                else if($method=='insert')
                {
                 //insert
                   $angsbln=$total/$lama;
                 $str="insert into ".$dbname.".sdm_angsuran (karyawanid,jenis,total,updateby,jlhbln,bulanan,active,start,end)
                       values(".$userid.",".$component.",".$total.",'".$_SESSION['standard']['username']."',".$lama.",".$angsbln.",".$active.",'".$start."','".$end."')";	
                }
                else if($method=='delete')
                {
           		  $str="delete from ".$dbname.".sdm_angsuran  
                                   where karyawanid=".$userid."
                               and jenis=".$component;
                }		
				
				

				
				
                if(mysql_query($str,$conn))
                {
                                $str="select * from ".$dbname.".sdm_ho_component
                                      where name like '%Angs%'";
                                $res=mysql_query($str,$conn);
                                $arr=Array();
                                $opt='';
                                while($bar=mysql_fetch_object($res))
                                {
                                        $arr[$bar->id]=$bar->name;
                                }	
                       // if($_SESSION['org']['tipeorganisasi']=='HOLDING')
                         if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
						{			    
								/*$str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
									  where a.karyawanid=u.karyawanid
										  (a.tipekaryawan=0 or a.lokasitugas='".$_SESSION['empl']['lokasitugas']."')
									  order by namakaryawan";*/
								$str="select a.*,u.namakaryawan,u.tipekaryawan,u.lokasitugas,u.nik from ".$dbname.".sdm_angsuran a left join ".$dbname.".datakaryawan u on a.karyawanid=u.karyawanid
									  where u.tipekaryawan=0 or u.lokasitugas='".$_SESSION['empl']['lokasitugas']."'
									  order by namakaryawan";	  
									  
									  
						}
						else  if($_SESSION['empl']['tipelokasitugas']=='KANWIL')
						{
							$str="select a.*,u.namakaryawan,u.tipekaryawan,u.lokasitugas,u.nik from ".$dbname.".sdm_angsuran a left join ".$dbname.".datakaryawan u on a.karyawanid=u.karyawanid
									  where u.tipekaryawan!=0 and 
									  u.lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
									  order by namakaryawan";	
						}
						else
						{
							$str="select a.*,u.namakaryawan,u.tipekaryawan,u.lokasitugas,u.nik from ".$dbname.".sdm_angsuran a left join ".$dbname.".datakaryawan u on a.karyawanid=u.karyawanid
									  where u.tipekaryawan!=0 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
									  order by namakaryawan";	 
							   /* $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
									  where a.karyawanid=u.karyawanid
										  and tipekaryawan!=0 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
									  order by namakaryawan";*/		
						}
						
                        $res=mysql_query($str,$conn);
                        $no=0;
                        while($bar=mysql_fetch_object($res))
                        {			  
                           $no+=1;
                           echo"<tr class=rowcontent>
                                    <td class=firsttd>".$no."</td>
                                    <td>".$bar->nik."</td>
									<td>".$bar->namakaryawan."</td>
									<td>".$bar->lokasitugas." -- ".$nmOrg[$bar->lokasitugas]." </td>
									<td>".$arr[$bar->jenis]."</td>
									<td align=right>".number_format($bar->total,2,'.',',')."</td>
									<td align=center>".$bar->start."</td>
									<td align=center>".$bar->end."</td>
									<td align=right>".$bar->jlhbln."</td>
									<td align=right>".number_format($bar->bulanan,2,'.',',')."</td>				
									<td align=center>".($bar->active==1?"Active":"Not Active")."</td>
                                        <td>
                             <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editAngsuran('".$bar->karyawanid."','".$bar->jenis."','".$bar->total."','".$bar->start."','".$bar->jlhbln."','".$bar->active."');\">
                             &nbsp <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delAngsuran('".$bar->karyawanid."','".$bar->jenis."');\">			
                                        </td>
                                  </tr>"; 			
                        }				
                }
                else
                {
                        echo " Error: ".addslashes(mysql_error($conn));
                } 	
?>
