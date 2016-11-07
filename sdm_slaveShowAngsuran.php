<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
//+++++++++++++++++++++++++++++++++++++++++++++
require_once('config/connection.php');
$str="select * from ".$dbname.".sdm_ho_component
      where name like '%Angs%'";
$res=mysql_query($str,$conn);
$arr=Array();
$opt='';
while($bar=mysql_fetch_object($res))
{
        $arr[$bar->id]=$bar->name;
}
$val=trim($_POST['string']);
switch ($val){
        case 'lunas':
                        if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
                        {			    
                                $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid and
                                          (u.tipekaryawan=0 or u.lokasitugas='".$_SESSION['empl']['lokasitugas']."')
                                      and `end`< '".date('Y-m')."'
                                          order by namakaryawan";
                        }
                        else
                        {
                                $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid
                                          and tipekaryawan!=0 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
                                      and `end`< '".date('Y-m')."'
                                          order by namakaryawan";		
                        }				
        break;
        case 'blmlunas':
                        if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
                        {			    
                                $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid and
                                          (u.tipekaryawan=0 or u.lokasitugas='".$_SESSION['empl']['lokasitugas']."')
                                      and `end`> '".date('Y-m')."'
                                          order by namakaryawan";
                        }
                        else
                        {
                                $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid
                                          and tipekaryawan!=0 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
                                      and `end`> '".date('Y-m')."'
                                          order by namakaryawan";		
                        }
        break;
        case 'active':
                        if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
                        {			    
                                $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid and
                                          (u.tipekaryawan=0 or u.lokasitugas='".$_SESSION['empl']['lokasitugas']."')
                                      and `active`=1
                                          order by namakaryawan";
                        }
                        else
                        {
                                $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid
                                          and tipekaryawan!=0 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
                                      and `active`=1
                                          order by namakaryawan";		
                        }
        break;
        case 'notactive':
                        if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
                        {			    
                                $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid and
                                          (u.tipekaryawan=0 or u.lokasitugas='".$_SESSION['empl']['lokasitugas']."')
                                      and `active`=0
                                          order by namakaryawan";
                        }
                        else
                        {
                                $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid
                                          and tipekaryawan!=0 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
                                      and `active`=0
                                          order by namakaryawan";		
                        }
        break;
        case '':
                        if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
                        {			    
                                $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid and
                                          (u.tipekaryawan=0 or u.lokasitugas='".$_SESSION['empl']['lokasitugas']."')
                                      order by namakaryawan";
                        }
                        else
                        {
                                $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid
                                          and tipekaryawan!=0 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
                                      order by namakaryawan";		
                        }
                break;	
        default:
                        if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
                        {			    
                                $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid and
                                          (u.tipekaryawan=0 or u.lokasitugas='".$_SESSION['empl']['lokasitugas']."')
                                          and (`start`<='".$val."' AND `end`>='".$val."')
                                          order by namakaryawan";
                        }
                        else
                        {
                                $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid 
                                          and tipekaryawan!=0 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
                                          and (`start`<='".$val."' AND `end`>='".$val."')
                                          order by namakaryawan";		
                        }		  					  					  			  
}
                if($res=mysql_query($str,$conn))
                {        
                $no=0;

                    while($bar=mysql_fetch_object($res))
                    {			  
                       $no+=1;
                       echo"<tr class=rowcontent>
                                <td class=firsttd>".$no."</td>
                                <td>".$bar->karyawanid."</td>
                                    <td>".$bar->namakaryawan."</td>
                                    <td>".$arr[$bar->jenis]."</td>
                                    <td align=right>".number_format($bar->total,2,'.',',')."</td>
                                    <td align=center>".$bar->start."</td>
                                    <td align=center>".$bar->end."</td>
                                    <td align=right>".$bar->jlhbln."</td>
                                    <td align=right>".number_format($bar->bulanan,2,'.',',')."</td>				
                                    <td align=center>".($bar->active==1?"Active":"Not Active")."</td>
                              </tr>"; 			
                      $ttl+=$bar->bulanan;	
                    }  
                }
                else
                    {
                      echo "Error:".mysql_error($conn);
                    } 
?>
