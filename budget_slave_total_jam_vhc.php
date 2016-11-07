<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses=$_POST['proses'];
$kdTraksi=$_POST['kdTraksi'];
$thnBudget=$_POST['thnBudget'];
$totJamThn=$_POST['totJamThn'];
$kdVhc=$_POST['kdVhc'];
$kdUnit=$_POST['kdUnit'];
$totRow=$_POST['totRow'];
$arrBln=array("1"=>"Jan","2"=>"Feb","3"=>"Mar","4"=>"Apr","5"=>"Mei","6"=>"Jun","7"=>"Jul","8"=>"Aug","9"=>"Sept","10"=>"Okt","11"=>"Nov","12"=>"Des");
$optNmOrg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
 $where="tahunbudget='".$thnBudget."' and kodevhc='".$kdVhc."' and unitalokasi='".$kdUnit."'";
	switch($proses)
	{
		case'getKdVhc':
                $optVhc="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                    
                $sSup="select distinct kodevhc from ".$dbname.".vhc_5master where kodetraksi='".$kdTraksi."' order by kodevhc asc";
                //echo $sSup;
                $qSup=mysql_query($sSup) or die(mysql_error($conn));
                while($rSup=mysql_fetch_assoc($qSup))
                {
                    if($kdVhc!='')
                    {
                        $optVhc.="<option value='".$rSup['kodevhc']."' ".($rSup['kodevhc']==$kdVhc?'selected':'').">".$rSup['kodevhc']."</option>";
                    }
                    else
                    {
                        $optVhc.="<option value='".$rSup['kodevhc']."' >".$rSup['kodevhc']."</option>";
                    }
                }
                echo $optVhc;
		break;
                case'cekHead':
                    $thisThn=date("Y");
                    $dtK=substr($thnBudget,0,1);
                    $dtA=substr($thisThn,0,1);
                    if($dtK!=$dtA)
                    {
                        exit("Error:Budget year incorrect");
                    }
                    
                    $sGet="select * from ".$dbname.".bgt_vhc_jam where ".$where." ";
                    //exit("Error".$sGet);
                    $qGet=mysql_query($sGet) or die(mysql_error($conn));
                    $rCek=mysql_num_rows($qGet);
                    if($rCek=='1')
                    {
                        exit("Error:Date already exist");
                    }
                    $sBr=floor($totJamThn/12);
                    echo $sBr;
                break;
                case'saveData':
//                    echo "<pre>";
//                    print_r($_POST['arrJam']);
//                    echo"</pre>";
                    for($a=1;$a<=$totRow;$a++)
                    {
                        if($_POST['arrJam'][$a]=='')
                        {
                            $_POST['arrJam'][$a]=0;
                        }
                        $totalSum+=$_POST['arrJam'][$a];
                    }
                    if($totalSum>$totJamThn)
                    {
                        exit("Error:Monthly hours greater than annually hours");
                    }

                    $sCek="select distinct * from ".$dbname.".bgt_vhc_jam where tahunbudget='".$thnBudget."' and kodevhc='".$kdVhc."' and unitalokasi='".kdUnit."'";
                    $qCek=mysql_query($sCek) or die(mysql_error());
                    $rCek=mysql_num_rows($qCek);
                    if($rCek<1)
                    {
                        $sInsert="insert into ".$dbname.".bgt_vhc_jam (tahunbudget, kodevhc, unitalokasi, jumlahjam,kodetraksi,updateby, jam01, jam02, jam03, jam04, jam05, jam06, jam07, jam08, jam09, jam10, jam11, jam12)";
                        $sInsert.=" values ('".$thnBudget."','".$kdVhc."','".$kdUnit."','".$totJamThn."','".$kdTraksi."','".$_SESSION['standard']['userid']."',";
                        for($a=1;$a<$totRow;$a++)
                        {
                            $sInsert.="'".$_POST['arrJam'][$a]."',";
                            if($a==($totRow-1))
                            {
                                $sInsert.="'".$_POST['arrJam'][$a]."')";
                            }
                        }
                       // exit("Error \n\n".$sInsert);
                        if(!mysql_query($sInsert))
                        {
                            echo " Gagal,_".$sInsert."__".(mysql_error($conn));
                        }   
                        
                        //$qInsert=mysql_query($sInsert)or die(mysql_error($conn));
                    }
                    else
                    {
                        exit("Error: Data already exist");
                    }
                break;
		case'loadData':
                    if($thnBudget!='')
                    {
                        $where5.=" and tahunbudget='".$thnBudget."'";
                    }
                    if($kdVhc!='')
                    {
                        $where5.=" and kodevhc='".$kdVhc."'";
                    }
                    if($kdUnit!='')
                    {
                        $where5.=" and unitalokasi='".$kdUnit."'";
                    }

                $limit=20;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;

                $ql2="select count(*) as jmlhrow from ".$dbname.".bgt_vhc_jam where kodetraksi like '%".$_SESSION['empl']['lokasitugas']."%' ".$where5."";// echo $ql2;
                $query2=mysql_query($ql2) or die(mysql_error());
                while($jsl=mysql_fetch_object($query2)){
                $jlhbrs= $jsl->jmlhrow;
                }
                    $totRowDlm=count($arrBln);
                $tab="<table cellpadding=1 cellspacing=1 border=0 class=sortable>";
                $tab.="<thead><tr class=rowheader><td>No</td>";
                $tab.="<td>".$_SESSION['lang']['budgetyear']."</td>";
                $tab.="<td>".$_SESSION['lang']['kodetraksi']."</td>";
                $tab.="<td>".$_SESSION['lang']['kodevhc']."</td>";
                $tab.="<td>".$_SESSION['lang']['unit']."</td>";
                $tab.="<td>".$_SESSION['lang']['totJamThn']."</td>";
                foreach($arrBln as $brs5=>$dtBln5)
                {
                $tab.="<td>".$dtBln5."</td>";
                }

                $tab.="<td>Action</td></tr></thead><tbody>";
                $sList="select * from ".$dbname.".bgt_vhc_jam 
                    where kodetraksi like '%".$_SESSION['empl']['lokasitugas']."%' ".$where5." order by tahunbudget desc limit ".$offset.",".$limit."";
                //exit("error".$sList);
                $qList=mysql_query($sList) or die(mysql_error());
                while($rList=  mysql_fetch_assoc($qList))
                {
                    $no+=1;
                    $rtp=" style='cursor:pointer;' title='Edit ".$rList['kodevhc']."' onclick=\"fillField('".$rList['tahunbudget']."','".$rList['kodevhc']."','".$rList['unitalokasi']."','".$rList['kodetraksi']."','".$rList['jumlahjam']."');\"";
                    $tab.="<tr class=rowcontent >";
                    $tab.="<td ".$rtp.">".$no."</td>";
                    $tab.="<td ".$rtp.">".$rList['tahunbudget']."</td>";
                    $tab.="<td ".$rtp.">".$rList['kodetraksi']."</td>";
                    $tab.="<td ".$rtp.">".$rList['kodevhc']."</td>";
                    $tab.="<td ".$rtp.">".$rList['unitalokasi']."</td>";
                    $tab.="<td align='right' ".$rtp.">".number_format($rList['jumlahjam'],2)."</td>";
                     for($a=1;$a<=$totRowDlm;$a++)
                    {
                        if(strlen($a)=='1')
                        {
                            $b="0".$a;
                        }
                        else
                        {
                            $b=$a;
                        }
                            
                        if($rList['jam'.$b]=='')
                        {
                            $rList['jam'.$b]=0;
                        }
                        $tab.="<td align='right' ".$rtp.">".number_format($rList['jam'.$b],2)."</td>";
                    }
                    $tab.="<td align='center'><img src='images/application/application_delete.png' class=resicon  title='Delete ".$rList['kodevhc']."' onclick=\"deleteData('".$rList['tahunbudget']."','".$rList['kodevhc']."','".$rList['unitalokasi']."','".$rList['kodetraksi']."');\"></td>";
                    $tab.="</tr>";
                }
                $spnCol=$totRowDlm+6;
                $tab.="
			<tr><td colspan='".$spnCol."' align=center>
			".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
			<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
			<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
			</td>
			</tr>"; 
                $tab.="</tbody></table>";
                echo $tab;
                break;
                case'update':
                    if(($totJamThn==0)||($totJamThn==''))
                    {
                        exit("Error:Total hours required");
                    }
                     for($a=1;$a<=$totRow;$a++)
                    {
                        if($_POST['arrJam'][$a]=='')
                        {
                            $_POST['arrJam'][$a]=0;
                        }
                        $totalSum+=$_POST['arrJam'][$a];
                    }
                    if($totalSum>$totJamThn)
                    {
                        exit("Error:Monthly hours geater than annually hours");
                    }
                   

                $sUpdate="update ".$dbname.".bgt_vhc_jam set jumlahjam='".$totJamThn."',updateby='".$_SESSION['standard']['userid']."',";
               // 
                for($a=1;$a<=12;$a++)
                {
                    if(strlen($a)=='1')
                    {
                        $c="0".$a;
                    }
                    else
                    {
                        $c=$a;
                    }
                     $sUpdate.=" jam".$c."='".$_POST['arrJam'][$a]."',";
                    if($a==12)
                    {
                     $sUpdate.=" jam".$c."='".$_POST['arrJam'][$a]."'";
                    }
                }
//                
                $sUpdate.="  where ".$where."";
                //exit("Error".$sUpdate);
                if(!mysql_query($sUpdate))
                {
                    echo " Gagal,_".$sUpdate."__".(mysql_error($conn));
                }  
                break;
                case'deleteData':
                $sDel="delete from ".$dbname.".bgt_vhc_jam where ".$where."";
                if(!mysql_query($sDel))
                {
                    echo"DB:Error ___".mysql_error($sDel);
                }
                
                break;
                case'getDataEdit':
                    $sData="select * from ".$dbname.".bgt_vhc_jam where ".$where."";
                    $qData=mysql_query($sData) or die(mysql_error($conn));
                    $rData=mysql_fetch_assoc($qData);
                    for($r=1;$r<13;$r++)
                    {
                        if(strlen($r)<2)
                        {
                            $b="0".$r;
                        }
                        else
                        {
                            $b=$r;
                        }
                       
                         echo $rData['jam'.$b]."###";
                       
                    }
                break;
		default:
		break;
	}


?>