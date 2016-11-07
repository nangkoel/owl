<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
?>


<?php
$method=$_POST['method'];
$thnbudget=$_POST['thnbudget'];
$kdorg=$_POST['kdorg'];
$kdtrak=$_POST['kdtrak'];
$totjamthn=$_POST['totjamthn'];
$totRow=$_POST['totRow'];
$kodews=$_POST['kodews'];
$kodetraksi=$_POST['kodetraksi'];

$total=$_POST['total'];
$totbrtthn=$_POST['totbrtthn'];
$totCol=$_POST['totCol'];

$arrBln=array(
"1"=>substr($_SESSION['lang']['jan'],0,3),
"2"=>substr($_SESSION['lang']['peb'],0,3),
"3"=>substr($_SESSION['lang']['mar'],0,3),
"4"=>substr($_SESSION['lang']['apr'],0,3),
"5"=>substr($_SESSION['lang']['mei'],0,3),
"6"=>substr($_SESSION['lang']['jun'],0,3),
"7"=>substr($_SESSION['lang']['jul'],0,3),
"8"=>substr($_SESSION['lang']['agt'],0,3),
"9"=>substr($_SESSION['lang']['sep'],0,3),
"10"=>substr($_SESSION['lang']['okt'],0,3),
"11"=>substr($_SESSION['lang']['nov'],0,3),
"12"=>substr($_SESSION['lang']['dec'],0,3),
);

$optNmOrg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
//untuk cek

$where="tahunbudget='".$thnbudget."' and kodetraksi='".$kdorg."' and kodews='".$kdtrak."'";

switch($method)
{
        //buat ambil data dari input kode traksi  untuk pilihan box kode ws
        case'getws':

                $sOpt="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi WHERE induk='".$kdorg."' and tipe='WORKSHOP'";
                        $qOpt=mysql_query($sOpt) or die(mysql_error());
                        $optws="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                        while($rOpt=mysql_fetch_assoc($qOpt))
                        {
                                if($kodews!='')
                                {
                                        $optws.="<option value=".$rOpt['kodeorganisasi']." ".($rOpt['kodeorganisasi']==$kodews?'selected':'').">".$rOpt['namaorganisasi']."</option>";
                                }
                                else
                                {
                                        $optws.="<option value=".$rOpt['kodeorganisasi'].">".$rOpt['namaorganisasi']."</option>";
                                }
                        }
                echo $optws;
        break;

        //untuk input tahun budget. jadi tidak ada kesamaan data dalam tahun budgetnya. di pakai untuk validasi simpan form 1
        case'cekHead':

        $thisThn=date("Y");
        $dtK=substr($thnbudget,0,1);
        $dtA=substr($thisThn,0,1);
        if($dtK!=$dtA)
        {
                exit("Error:Budget year required");
        }           
        $sGet="select * from ".$dbname.".bgt_ws_jam where ".$where." ";
        $qGet=mysql_query($sGet) or die(mysql_error($conn));
        $rCek=mysql_num_rows($qGet);
        if($rCek=='1')
        {
                exit("Error:Data already exist");
        }
                //$sBr=floor($totjamthn/12);
        $sBr=floor($totjamthn/12);
                echo $sBr;
    break;

        //insert data ke dalam database
    case'saveData':

        for($a=1;$a<=$totRow;$a++)
        {
                        if($_POST['arrJam'][$a]=='')
            {
                $_POST['arrJam'][$a]=0;
            }
        $totalSum+=$_POST['arrJam'][$a];
        }
        if($totalSum>$totjamthn)
        {
            exit("Error:Mothly working hour greater than annual working hours");
                }
                $sCek="select distinct * from ".$dbname.".bgt_ws_jam where tahunbudget='".$thnbudget."' and kodetraksi='".$kdorg."' and kodews='".$kdtrak."'";
                $qCek=mysql_query($sCek) or die(mysql_error());
                $rCek=mysql_num_rows($qCek);
                if($rCek<1)
        {
                        $sInsert="insert into ".$dbname.".bgt_ws_jam (tahunbudget, kodetraksi, kodews, jampertahun, updateby, jam01, jam02, jam03, jam04, jam05, jam06, jam07, jam08, jam09, jam10, jam11, jam12)";
                        $sInsert.=" values ('".$thnbudget."','".$kdorg."','".$kdtrak."','".$totjamthn."','".$_SESSION['standard']['userid']."',";
                        for($a=1;$a<$totRow;$a++)
            {
                                $sInsert.="'".$_POST['arrJam'][$a]."',";
                                if($a==($totRow-1))
                                {
                                        $sInsert.="'".$_POST['arrJam'][$a]."')";

                                }
                        }

                        if(!mysql_query($sInsert))
                        {
                                echo " Gagal,_".$sInsert."__".(mysql_error($conn));
                        }   
        }
                else
                {
                        exit("Error:Data already exist");
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

                $ql2="select count(*) as jmlhrow from ".$dbname.".bgt_ws_jam where kodetraksi like '%".$_SESSION['empl']['lokasitugas']."%'";
                $query2=mysql_query($ql2) or die(mysql_error());
                while($jsl=mysql_fetch_object($query2)){
                $jlhbrs= $jsl->jmlhrow;
                }
                $totRowDlm=count($arrBln);
                $tab="<table cellpadding=1 cellspacing=1 border=0 class=sortable>";
                $tab.="<thead><tr class=rowheader><td width=20>".substr($_SESSION['lang']['nomor'],0,2)."</td>";
                $tab.="<td align=center width=100>".$_SESSION['lang']['budgetyear']."</td>";
                $tab.="<td align=center width=75>".$_SESSION['lang']['traksi']." </td>";
                $tab.="<td align=center width=100>".$_SESSION['lang']['workshop']."</td>";
                $tab.="<td align=center width=125>".$_SESSION['lang']['totJamThn']."</td>";


                foreach($arrBln as $brs5=>$dtBln5)
                {
                        $tab.="<td align=center width=45>".$dtBln5."</td>";
                }
                $tab.="<td>".$_SESSION['lang']['action']."</td></tr></thead><tbody>";
                                $sList="select * from ".$dbname.".bgt_ws_jam where kodetraksi like '%".$_SESSION['empl']['lokasitugas']."%' order by tahunbudget desc limit ".$offset.",".$limit."";
                $qList=mysql_query($sList) or die(mysql_error());
                while($rList=  mysql_fetch_assoc($qList))
                {
                    $no+=1;
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td align=center>".$no."</td>";
                    $tab.="<td align=right>".$rList['tahunbudget']."</td>";
                    $tab.="<td align=left>".$rList['kodetraksi']."</td>";
                    $tab.="<td align=left>".$rList['kodews']."</td>";
                                        $tab.="<td align='right'>".$rList['jampertahun']."</td>";
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
                        $tab.="<td align='right'>".number_format($rList['jam'.$b],2)."</td>";
                    }
                    $tab.="<td align='center'><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rList['tahunbudget']."','".$rList['kodetraksi']."','".$rList['kodews']."','".$rList['jampertahun']."');\"></td>";
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


                        case 'update':
                                        if(($totjamthn==0)||($totjamthn==''))
                    {
                        exit("Error:Total working hours required");
                    }
                     for($a=1;$a<=$totRow;$a++)
                    {
                        if($_POST['arrJam'][$a]=='')
                        {
                            $_POST['arrJam'][$a]=0;
                        }
                        $totalSum+=$_POST['arrJam'][$a];
                    }
                    if($totalSum>$totjamthn)
                    {
                        exit("Error:Monthly working hours greater than annually working hours");
                    }


                                $sUpdate="update ".$dbname.".bgt_ws_jam set jampertahun='".$totjamthn."',updateby='".$_SESSION['standard']['userid']."'";
                            // exit("Error".$sUpdate);
                                                            for($a=1;$a<=$totRow;$a++)
                                                            {
                                                                    if(strlen($a)=='1')
                                                                    {
                                                                            $c="0".$a;
                                                                    }
                                                                    else
                                                                    {
                                                                            $c=$a;
                                                                    }

                                                                     $sUpdate.=" ,jam".$c."='".$_POST['arrJam'][$a]."'";

                                                            }
                             $sUpdate.=" where  ".$where."";
                            // exit("Error".$sUpdate);
                            if(!mysql_query($sUpdate))
                            {
                            echo " Gagal,_".$sUpdate."__".(mysql_error($conn));
                            }   

                        break;



                                case'getDataEdit':
                    $sData="select * from ".$dbname.".bgt_ws_jam where ".$where."";
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
}
?>