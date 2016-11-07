<?php 
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$thnBudget=$_POST['thnBudget'];
$uphSprvisi=$_POST['uphSprvisi'];
$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
$jmlhPerson=$_POST['jmlhPerson'];
$hkEfektif=$_POST['hkEfektif'];
$totUpah=$_POST['totUpah'];
$kdBlok=$_POST['kdBlok'];
$kgtn=$_POST['kgtn'];
$noakn=$_POST['noakn'];
$jmlHk=$_POST['jmlHk'];
$hkSprvisi=$_POST['hkSprvisi'];
$rpsuperVisi=$_POST['superVisi'];
$volKeg=$_POST['volKeg'];
$satKeg=$_POST['satKeg'];
$rotasi=$_POST['rotasi'];
$kunci=$_POST['kunci'];
$arrBln=array("1"=>"Jan","2"=>"Feb","3"=>"Mar","4"=>"Apr","5"=>"Mei","6"=>"Jun","7"=>"Jul","8"=>"Aug","9"=>"Sep","10"=>"Okt","11"=>"Nov","12"=>"Des");



switch($proses)
{
        case'getHk':
            if($thnBudget=='')
            {
                exit("Error:Tahun Budget Tidak Boleh Kosong");
            }
            $sThnCek="select  * from ".$dbname.".bgt_hk where tahunbudget='".$thnBudget."' ";
            $qThnCek=mysql_query($sThnCek) or die(mysql_error());
            $rThnCek=mysql_num_rows($qThnCek);
            if($rThnCek<1)
            {
                exit("Error:Tahun Budget : ".$thnBudget." Belum Memilik HK Efektif");
            }
            else
            {
                $rHasil=mysql_fetch_assoc($qThnCek);
                $hkEfektif=$rHasil['harisetahun']-(($rHasil['hrminggu']+$rHasil['hrlibur'])-$rHasil['hrliburminggu']);
                echo $hkEfektif;
            }
        break;
	case'getPreview':
          if($thnBudget=='')
          {
              exit("Error: Tahun Budget Tidak Boleh Kosong");
          }
            $sCek="SELECT distinct kodeorg,kegiatan FROM ".$dbname.".bgt_budget 
                   where tahunbudget='".$thnBudget."' and 
                   kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and kegiatan is not null";
            
           //$sCek="select sum(jumlahhk) as totalJmlhHk from ".$dbname.".bgt_hkperkegiatan_vw where tahunbudget='".$thnBudget."' and substring(kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."'";
            $qCek=mysql_query($sCek) or die(mysql_error($sCek));
            //$rTot=mysql_fetch_assoc($qCek);
            $rTot=mysql_num_rows($qCek);

            $sCek2="SELECT distinct a.kodeorg,a.kegiatan,b.namakegiatan,a.noakun FROM " . $dbname . ".bgt_budget  a
                    left join " . $dbname . ".setup_kegiatan b  on a.kegiatan=b.kodekegiatan
                   where tahunbudget='" . $thnBudget . "' and 
                   a.kodeorg like '" . $_SESSION['empl']['lokasitugas'] . "%' and kegiatan is not null";
            //$sCek2="select distinct * from ".$dbname.".bgt_hkperkegiatan_vw 
            //        where tahunbudget='".$thnBudget."' and substring(kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."'";

            $str="select distinct nilai from ".$dbname.".setup_parameterappl where kodeaplikasi='SB'";
            $res=mysql_query($str);
            while($bar=mysql_fetch_object($res))
            {
                $akun[substr($bar->nilai,0,3)]=$bar->nilai;
            }
            $qCek2=mysql_query($sCek2) or die(mysql_error($conn));
            $rCek=mysql_num_rows($qCek2);
            if($rCek!=0)
            {
            while($res=mysql_fetch_assoc($qCek2))
            {
                $no+=1;
                //$sData="select distinct volume,satuanv,rotasi from ".$dbname.".bgt_budget where tahunbudget='".$thnBudget."' and kodeorg='".$res['kodeorg']."' and tipebudget='ESTATE' and noakun='".$res['noakun']."' and kegiatan='".$res['kegiatan']."' ";

                //$qData=mysql_query($sData) or die(mysql_error($sData));
                //$rData=mysql_fetch_assoc($qData);
                
                //$nilai=($res['jumlahhk']/$rTot['totalJmlhHk'])*$jmlhPerson*$hkEfektif;
                //$nilai2=($res['jumlahhk']/$rTot['totalJmlhHk'])*$totUpah;
                $nilai=(1/$rTot)*$jmlhPerson*$hkEfektif;
                $nilai2=(1/$rTot)*$totUpah;                
                $tab.="<tr class=rowcontent id=rew_".$no.">";
                $tab.="<td>".$no."</td>";
                $tab.="<td>".$thnBudget."</td><td id=kdBlok_".$no.">".$res['kodeorg']."</td>";
                $tab.="<td id=keg_".$no.">".$res['kegiatan']."</td>";
                //$tab.="<td align=right id=noakun_".$no.">".$res['noakun']."</td>";
                if (array_key_exists(substr($res['kegiatan'],0,3),$akun)){
                $tab.="<td align=right id=noakun_".$no.">".$akun[substr($res['kegiatan'],0,3)]."</td>";
                }
                else
                {
                    $tab.="<td align=right id=noakun_".$no.">".$res['kegiatan']."</td>";
                }
               
                //$tab.="<td id=noakun_".$no.">".$res['noakun']."</td>";
                //$tab.="<td id=vol_".$no.">".$rData['volume']."</td>";
                //$tab.="<td id=satuan_".$no.">".$rData['satuanv']."</td>";
                //$tab.="<td id=rotsi_".$no.">".$rData['rotasi']."</td>";
                $tab.="<td id=vol_".$no.">1</td>";
                $tab.="<td id=satuan_".$no.">HK</td>";
                $tab.="<td id=rotsi_".$no.">1</td>";                
               // $tab.="<td align=right  id=jmlhHk_".$no.">".$res['jumlahhk']."</td>";
                $tab.="<td align=right  id=jmlhHk_".$no.">".number_format($nilai,3)."</td>";
                $tab.="<td>".$res['namakegiatan']."</td>";
                $tab.="<td><input type=text id=hkSupervisi_".$no."  class=myinputtextnumber onkeypress='return angka_doang(event)' value='".$nilai."' onblur=\"hkSupervisi(".$no.")\"  /></td>";
                $tab.="<td><input type=text id=superVisi_".$no."  class=myinputtextnumber onkeypress='return angka_doang(event)' value='".$nilai2."'  onblur=\"supervisi(".$no.")\" /></td>";
                $tab.="</tr>";
            }
        //$tab.="<input type=hidden id=jmlhRow value='".$no."' /><input type=hidden id=totalHk value='".$rTot['totalJmlhHk']."' />"; 
        $tab.="<input type=hidden id=jmlhRow value='".$no."' /><input type=hidden id=totalHk value='".$rTot."' />";
            }
            else
            {
                exit("Error: Data Kosong");
            }
        echo $tab;
	break;
       
        case'insertAll':
             $thn=date("Y");
            if($thnBudget=='')
            {
                exit("Error:Tahun Budet Tidak Boleh Kosong");
            }
            elseif(strlen($thnBudget)<4)
            {
                exit("Error:Panjang Tahun Kurang");
            }
            if(substr($thn,0,1)!=substr($thnBudget,0,1))
            {
                exit("Error:Format Tahun Salah");
            }
            
            // kalo ga ada ini, bakalan nimpa blok yang sama...
       $sPrev="select * from ".$dbname.".bgt_budget where tahunbudget='".$thnBudget."' and kodeorg='".$kdBlok."' and tipebudget='ESTATE' and noakun='".$noakn."' and kegiatan='".$noakn."01' and kodebudget='SUPERVISI'";
                $qPrev=mysql_query($sPrev) or die(mysql_error($sPrev));
                while($rPrev=mysql_fetch_assoc($qPrev))
                {
                    $rpsuperVisi+=$rPrev['rupiah']; 
                    $hkSprvisi+=$rPrev['jumlah'];
                }
            
            
       $sDel="delete from ".$dbname.".bgt_budget where tahunbudget='".$thnBudget."' and kodeorg='".$kdBlok."' and tipebudget='ESTATE' and noakun='".$noakn."' and kegiatan='".$noakn."01' and kodebudget='SUPERVISI'";
       if(mysql_query($sDel))
       {    
            $sInsert="insert into ".$dbname.".bgt_budget (tahunbudget, kodeorg, tipebudget, kodebudget, kegiatan, noakun, volume, satuanv, rupiah,  rotasi, updateby, jumlah, satuanj) 
            value ('".$thnBudget."','".$kdBlok."','ESTATE','SUPERVISI','".$noakn."01','".$noakn."','".$volKeg."','".$satKeg."','".$rpsuperVisi."','".$rotasi."','".$_SESSION['standard']['userid']."','".$hkSprvisi."','hk')";
            if(!mysql_query($sInsert))
            {
            echo "DB Error : ".$sInsert."\n".mysql_error($conn);
            }
       }
            
        break;
        case'getPreviewSebaran':
          if($thnBudget=='')
          {
              exit("Error: Tahun Budget Tidak Boleh Kosong");
          }
           
            
             $thn=date("Y");
           
            if(strlen($thnBudget)<4)
            {
                exit("Error:Panjang Tahun Kurang");
            }
            if(substr($thn,0,1)!=substr($thnBudget,0,1))
            {
                exit("Error:Format Tahun Salah");
            }
            $sCek="select distinct tahunbudget from ".$dbname.".bgt_budget where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and kodebudget='SUPERVISI' and tahunbudget='".$thnBudget."'";
            $qCek=mysql_query($sCek) or die(mysql_error($sCek));
            $rCek=mysql_num_rows($qCek);
            if($rCek!=0)
            {
            $tab.="<fieldset style=width:1500px><legend>".$_SESSION['lang']['list']."</legend>";
            $tab.="<button class=mybutton id=save_kepala name=save_kepala onclick=saveSebaran(1) >".$_SESSION['lang']['save']."</button>";
            $tab.="<button class=mybutton id=lnjutSebaran name=lnjutSebaran onclick=reSave() style=display:none;>".$_SESSION['lang']['lanjut']."</button>";
            $tab.="<table><tr><td colspan=4>&nbsp;</td>";
            foreach($arrBln as $brsBulan =>$listBln)
            {
                $tab.="<td>".$listBln."</td>";
            }
            $tab.="<td>&nbsp;</td></tr><tr><td colspan=4>&nbsp;</td>";
            foreach($arrBln as $brsBulanw =>$listBlnw)
            {
                $tab.="<td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss".$brsBulanw." value=1></td>";
            }
            $tab.="<td><img src=images/clear.png onclick=bersihkanDonk() style='height:30px;cursor:pointer' title='bersihkan'></td></tr></table>";
            $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>
            <thead>
            <tr class=rowheader>
            <td></td>
            <td>No</td>
            <td>".$_SESSION['lang']['index']."</td>
            <td>".$_SESSION['lang']['kodeorg']."</td>
            <td>".$_SESSION['lang']['kodekegiatan']."</td>
            <td>".$_SESSION['lang']['rp']."</td>
            <td>".$_SESSION['lang']['volume']."</td>
            ";
            foreach($arrBln as $listBln)
            {
                $tab.="<td>".$listBln."</td>";
            }
            $tab.=" </tr>
            </thead><tbody>";
                $sList="select * from ".$dbname.".bgt_budget where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and kodebudget='SUPERVISI' and tahunbudget='".$thnBudget."'";
                //exit("Error".$sList);
                $qList=mysql_query($sList) or die(mysql_error($sList));
                while($rList=mysql_fetch_assoc($qList))
                {
                    $no+=1;
                    $add=" onclick=\"clearForm(".$no.")\" style='cursor:pointer;' title='Kosongkan Isi ".$rList['kunci']."'";
                    $sbrng=$rList['rupiah']/12;
                    $tab.="<tr class=rowcontent id=rewBr_".$no.">";
                    $tab.="<td><input type=checkbox onclick=sebarkanBoo('".$rList['kunci']."',".$no.",this,".$rList['rupiah'].",".$rList['volume']."); title='Sebarkan sesuai proporsi diatas'></td>";
                    $tab.="<td ".$add.">".$no."</td>";
                    $tab.="<td ".$add." id=key_".$no.">".$rList['kunci']."</td>";
                    $tab.="<td ".$add.">".$rList['kodeorg']."</td>";
                    $tab.="<td ".$add.">".$rList['kegiatan']."</td>";
                    $tab.="<td ".$add."  id=hrg_".$no.">".$rList['rupiah']."</td>";
                    $tab.="<td ".$add."  id=vol_".$no.">".$rList['volume']."</td>";
                    
                    foreach($arrBln as $listBln)
                    {
                       $arr+=1;
                       $tab.="<td><input type='text' id='sbrn_".$arr."_".$no."' value='".$sbrng."' class='myinputtextnumber' style='width:75px' onkeypress='return tanpa_kutip(event)'  /></td>";
                       
                       
                    }
                    
                    $tab.="</tr>";
                    $arr=0;
                }
        $tab.="</tbody></table></fieldset><input type=hidden id=jmlhRow value=".$no." />";
        echo $tab;
            }
            else
            {
                exit("Error: Data Kosong");
            }
        break; 
        case'insertAllData':
           
            $sCek="select distinct kunci from ".$dbname.".bgt_distribusi where kunci='".$kunci."'";
            $qCek=mysql_query($sCek) or die(mysql_error($conn));
            $rCek=mysql_num_rows($qCek);
            
        if($rCek!=1)
        {   
                $sInsert="insert into ".$dbname.".bgt_distribusi (kunci, updateby,rp01, rp02, rp03, rp04, rp05, rp06, rp07, rp08, rp09, rp10, rp11, rp12) 
                value ('".$kunci."','".$_SESSION['standard']['userid']."'";
                for($arb=1;$arb<=12;$arb++)
                {
                    $sInsert.=",'".$_POST['arrBrt'][$arb]."'";

                }
                $sInsert.=")";
                    if(!mysql_query($sInsert))
                    {
                    echo "DB Error : ".$sInsert."\n".mysql_error($conn);
                    }

       }
        else
        {
            $sDel="delete from ".$dbname.".bgt_distribusi where kunci='".$kunci."'";
            if(mysql_query($sDel))
            {
               $sInsert="insert into ".$dbname.".bgt_distribusi (kunci, updateby,rp01, rp02, rp03, rp04, rp05, rp06, rp07, rp08, rp09, rp10, rp11, rp12) 
                value ('".$kunci."','".$_SESSION['standard']['userid']."'";
                for($arb=1;$arb<=12;$arb++)
                {
                    $sInsert.=",'".$_POST['arrBrt'][$arb]."'";

                }
                $sInsert.=")";
                    if(!mysql_query($sInsert))
                    {
                    echo "DB Error : ".$sInsert."\n".mysql_error($conn);
                    }

        }   }
               
            
        break;
	case'deleteAll':
        if($thnBudget=='')
          {
              exit("Error: Tahun Budget Tidak Boleh Kosong");
          }
           
            
             $thn=date("Y");
           
            if(strlen($thnBudget)<4)
            {
                exit("Error:Panjang Tahun Kurang");
            }
            if(substr($thn,0,1)!=substr($thnBudget,0,1))
            {
                exit("Error:Format Tahun Salah");
            }
            $sCek="select distinct tahunbudget from ".$dbname.".bgt_budget where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and kodebudget='SUPERVISI' and tahunbudget='".$thnBudget."'";
            $qCek=mysql_query($sCek) or die(mysql_error($sCek));
            $rCek=mysql_num_rows($qCek);
            if($rCek!=0)
            {
                $sDel="delete from ".$dbname.".bgt_distribusi where kunci in (select distinct kunci from ".$dbname.".bgt_budget where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and kodebudget='SUPERVISI' and tahunbudget='".$thnBudget."')";
                if(mysql_query($sDel))
                {
                    $SDelbudget="delete from ".$dbname.".bgt_budget where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and kodebudget='SUPERVISI' and tahunbudget='".$thnBudget."'";
                    if(!mysql_query($SDelbudget))
                    {
                        echo "DB Error : ".$SDelbudget."\n".mysql_error($conn);
                    }
                }
                    }
            else
            {
                exit("Error: Data Kosong");
            }
        break;
     	default:
	break;
}
?>