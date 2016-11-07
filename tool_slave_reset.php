<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

$method=$_POST['method'];
if(isset($_POST['method3']))
{
$method=$_POST['method3'];
}
$listTransaksi=explode(",",$_POST['listTransaksi']);
$listTransaksi2=explode(",",$_POST['listReset']);
$pilUn_1=$_POST['pilUn_1'];
$pilUn_5=$_POST['pilUn_5'];
$unitId=$_POST['unitId'];
$periodeId=$_POST['periodeId'];
$no=0;
$bloklama=$_POST['bloklama'];
$blokbaru=$_POST['blokbaru'];
$jmrow=count($listTransaksi);
$jmrow2=count($listTransaksi2);
if($jmrow!=$jmrow2){
    exit("Error: Baris Data Tidak Sama");
}
switch($method)
{
    
        case'getData':
            $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>";
            $tab.="<tr><td>".$_SESSION['lang']['notransaksi']."</td>";
            $tab.="<td>".$_SESSION['lang']['tanggal']."</td>";
            $tab.="<td>".$_SESSION['lang']['kodevhc']."</td>";
            $tab.="<td>".$_SESSION['lang']['vhc_jenis_pekerjaan']."</td>";
            $tab.="<td>".$_SESSION['lang']['alokasibiaya']."</td>";
            $tab.="<td>".$_SESSION['lang']['vhc_kmhm_akhir']."</td>
                   <td>".$_SESSION['lang']['vhc_kmhm_akhir']." update</td></tr></thead><tbody id=dataIsi>";
            for($dert=0;$dert<$jmrow;$dert++){
                //exit("error:__".$listTransaksi[$dert]."__".$listTransaksi2[$dert]);
                $sData="select distinct b.notransaksi,a.tanggal,b.jenispekerjaan,alokasibiaya,kmhmakhir,kodevhc
                        from ".$dbname.".vhc_rundt b left join ".$dbname.".vhc_runht a on b.notransaksi=a.notransaksi 
                        where kodevhc='".$listTransaksi[$dert]."' and kmhmakhir>'".$listTransaksi2[$dert]."' order by kmhmakhir asc";
                //exit("error:".$sData);
                $qData=mysql_query($sData) or die(mysql_error($conn));
                while($rData=  mysql_fetch_assoc($qData)){
                    $ert++;
                 $tab.="<tr class=rowcontent><td id=notransaksi_".$ert.">".$rData['notransaksi']."</td>";
                 $tab.="<td id=tanggal_".$ert.">".$rData['tanggal']."</td>";
                 $tab.="<td id=kodevhc_".$ert.">".$rData['kodevhc']."</td>";
                 $tab.="<td id=jnsPekerjaan_".$ert.">".$rData['jenispekerjaan']."</td>";
                 $tab.="<td id=alokasiBiya_".$ert.">".$rData['alokasibiaya']."</td>";
                 $tab.="<td id=kmhmAkhir_".$ert.">".$rData['kmhmakhir']."</td>";
                 $tab.="<td id=kmhmAkhirup_".$ert.">0.".intval($rData['kmhmakhir'])."</td></tr>";
                 
                }
            }
            $tab.="</tbody></table><br />";
            $tab.="<button class=mybutton onclick=resetDt()>Rest Data</button>";
           
        echo $tab;
        break;
        case'resetDt':
        foreach($_POST['notransaksi'] as $dtList=>$bsdlis)
        {
           $supdate="update ".$dbname.".vhc_rundt set kmhmakhir='".$_POST['kmhmap'][$dtList]."'
                     where notransaksi='".$bsdlis."' and jenispekerjaan='".$_POST['jnsPekerjaan'][$dtList]."'
                     and alokasibiaya='".$_POST['alokasiBiaya'][$dtList]."'";
           if(!mysql_query($supdate)){
               exit("error: ".mysql_error($conn)."__".$supdate);
           }
        }
        for($dert=0;$dert<$jmrow;$dert++){
            $sData="select distinct b.notransaksi,jenispekerjaan,alokasibiaya,kmhmakhir,kodevhc
                        from ".$dbname.".vhc_rundt b left join ".$dbname.".vhc_runht a on b.notransaksi=a.notransaksi 
                        where kodevhc='".$listTransaksi[$dert]."' and kmhmakhir<'".$listTransaksi2[$dert]."' order by kmhmakhir desc limit 0,1";
                //exit("error:".$sData);
            $qData=mysql_query($sData) or die(mysql_error($conn));
            $rData=mysql_fetch_assoc($qData);
            $sder="select distinct b.notransaksi,jenispekerjaan,alokasibiaya,kmhmakhir,kodevhc
                   from ".$dbname.".vhc_rundt b left join ".$dbname.".vhc_runht a on b.notransaksi=a.notransaksi 
                   where  b.notransaksi='".$rData['notransaksi']."' and jenispekerjaan='".$rData['jenispekerjaan']."'
                   and alokasibiaya='".$rData['alokasibiaya']."' and kmhmakhir='".$listTransaksi2[$dert]."'";
            $qder=mysql_query($sder) or die(mysql_error($conn));
            $rder=mysql_num_rows($qder);
            if($rder==0){
                $supdate="update ".$dbname.".vhc_rundt set kmhmakhir='".$listTransaksi2[$dert]."'
                          where notransaksi='".$rData['notransaksi']."' and jenispekerjaan='".$rData['jenispekerjaan']."'
                          and alokasibiaya='".$rData['alokasibiaya']."'";
                if(!mysql_query($supdate)){
                    exit("error: ".mysql_error($conn)."__".$supdate);
                }
            }
        }
        break;
       
}
?>