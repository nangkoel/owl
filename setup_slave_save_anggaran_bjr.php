<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

$tahunbudget=$_POST['tahunbudget'];
$kodeorg=$_POST['kodeorg'];
$thntanam=$_POST['thntanam'];
$bjr=$_POST['bjr'];
$oldtahunbudget=$_POST['oldtahunbudget'];
$oldkodeorg=$_POST['oldkodeorg'];
$oldthntanam=$_POST['oldthntanam'];
$method=$_POST['method'];

$thnclose=$_POST['thnclose'];
$lkstgs=$_POST['lkstgs'];
$thnttp=$_POST['thnttp'];

$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');

switch($method)
{
        case 'insert':
                $aCek="select distinct close from ".$dbname.".bgt_bjr where tahunbudget='".$tahunbudget."' and kodeorg='".$kodeorg."' ";
                $bCek=mysql_query($aCek) or die(mysql_error());
                //exit("error:$aCek");
                while ($cCek=mysql_fetch_assoc($bCek))
                {

                        if($cCek['close']==1)
                        {
                                echo "warning : Data has been closed for ".$tahunbudget.", can not modify";
                                exit();	
                        }
                }

                $oldtahunbudget==''?$oldtahunbudget=$_POST['tahunbudget']:$oldtahunbudget=$_POST['oldtahunbudget'];
                $oldkodeorg==''?$oldkodeorg=$_POST['kodeorg']:$oldkodeorg=$_POST['oldkodeorg'];
                $oldthntanam==''?$oldthntanam=$_POST['thntanam']:$oldthntanam=$_POST['oldthntanam'];

                if(strlen($tahunbudget)<4)
                {
                        exit("Error:Planting year not found");
                }
                else if (strlen($thntanam)<4)
                {
                        exit("Error:Planting year not found");
                }

                $sRicek="select * from ".$dbname.".bgt_bjr where tahunbudget='".$oldtahunbudget."' and kodeorg='".$oldkodeorg."' and thntanam='".$oldthntanam."' ";
                                //exit("Error:$sRicek");
                $qRicek=mysql_query($sRicek) or die(mysql_error($conn));
                $rRicek=mysql_num_rows($qRicek);

                if($rRicek>0)
                {
                $sDel="delete from ".$dbname.".bgt_bjr
                                where tahunbudget='".$oldtahunbudget."' and kodeorg='".$oldkodeorg."' and thntanam='".$oldthntanam."' ";	    
                        if(mysql_query($sDel))
                        {
                        $sDel2="insert into ".$dbname.".bgt_bjr (`tahunbudget`,`kodeorg`,`thntanam`,`bjr`,`updateby`)
                values ('".$tahunbudget."','".$kodeorg."','".$thntanam."','".$bjr."','".$_SESSION['standard']['userid']."')";

                if(mysql_query($sDel2))
                echo"";
                else
                echo " Gagal,".addslashes(mysql_error($conn));
                        }
                        else	
                        {
                                echo " Gagal,".addslashes(mysql_error($conn));
                        }	
                }
                else
                {
                $sDel2="insert into ".$dbname.".bgt_bjr (`tahunbudget`,`kodeorg`,`thntanam`,`bjr`,`updateby`)
                values ('".$tahunbudget."','".$kodeorg."','".$thntanam."','".$bjr."','".$_SESSION['standard']['userid']."')";
                //exit("Error.$sDel2");
                if(mysql_query($sDel2))
                echo"";
                else
                echo " Gagal,".addslashes(mysql_error($conn));
                }
        break;

case'loadData':

                $no=0;
                $str="select * from ".$dbname.".bgt_bjr where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by tahunbudget desc";
                $str2=mysql_query($str) or die(mysql_error());
                //$res1=mysql_query($str2);
                while($bar1=mysql_fetch_assoc($str2))
                {
                        $no+=1;
                        $tab="<tr class=rowcontent>";
                        $tab.="<td align=center>".$no."</td>";

                        $tab.="<td align=right>".$bar1['tahunbudget']."</td>";
                        $tab.="<td align=left>".$optNm[$bar1['kodeorg']]."</td>";
                        $tab.="<td align=right>".$bar1['thntanam']."</td>";
                        $tab.="<td align=right>".$bar1['bjr']."</td>";
                        if($bar1['close']==0)
                    {	
                        $tab.="<td align=center><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1['tahunbudget']."','".$bar1['kodeorg']."','".$bar1['thntanam']."','".$bar1['bjr']."');\"></td>";

                                        }
                                        else {
                                                $tab.="<td>".$_SESSION['lang']['tutup']."</td>";
                                                }
                echo $tab;
                }

         //case close==========================================================================================================
         case'closebjr':
                $sQl="select distinct close from ".$dbname.".bgt_bjr where tahunbudget='".$thnttp."' and kodeorg='".$lkstgs."' and close=1 ";
            //exit("error".$sQl);
                $qQl=mysql_query($sQl) or die(mysql_error($conn));
                $row=mysql_num_rows($qQl);
                if($row!=1)
                {
                        $sUpdate="update ".$dbname.".bgt_bjr set close=1 where tahunbudget='".$thnttp."' and kodeorg='".$lkstgs."'  ";
                    //exit("error".$sUpdate);
                        if(mysql_query($sUpdate))
                                echo"";
                        else
                                 echo " Gagal,_".$sUpdate."__".(mysql_error($conn));
                }
                else
                {
                        exit("Error:Data closed");
                }
    break;





        case 'cekclose':

                /*$aCek="select distinct close from ".$dbname.".bgt_bjr where tahunbudget='".$thnbudget."' and kodeorg='".$kodeorg."' ";
                $bCek=mysql_query($aCek) or die(mysql_error());
                while ($cCek=mysql_fetch_assoc($bCek))
                {//exit("error:$aCek");
                        if($cCek['close']==1)
                        {
                                echo "warning : Input untuk tahun ".$tahunbudget." dengan kode ".$kodeorg." tidak bisa dilakukan karena telah di close";
                                exit();	
                        }
                }*/

        break;

        case 'getThn':
                //$bjr="select bjr from ".$dbname.".bgt_bjr WHERE kodeorg='".substr($kdblok,0,4)."'";
                        $optthnttp="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                        $sql = "SELECT distinct tahunbudget FROM ".$dbname.".bgt_bjr where close=0 order by tahunbudget desc";
                        $qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
                        while ($data=mysql_fetch_assoc($qry))
                                                {
                                                $optthnttp.="<option value=".$data['tahunbudget'].">".$data['tahunbudget']."</option>";
                                                }
                        echo $optthnttp;
        break;


        case 'getOrg':
                $optorgclose="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                $sql = "SELECT distinct kodeorg FROM ".$dbname.".bgt_bjr where close=0 and kodeorg='".$_SESSION['empl']['lokasitugas']."' ";
                $qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
                while ($data=mysql_fetch_assoc($qry))
                                        {
                                        $optorgclose.="<option value=".$data['kodeorg'].">".$optNm[$data['kodeorg']]."</option>";
                                        }
                echo $optorgclose;
        break;

default:
}
?>
