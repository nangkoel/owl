<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

$optNmRwt=makeOption($dbname,'sdm_5jenisbiayapengobatan','kode,nama');
$periode=$_GET['periode'];
$kodeorg=$_GET['kodeorg'];
if($periode=='')$periode=date('Y');    

    $str2="select a.karyawanid, sum(totalklaim) as klaim,d.namakaryawan,d.lokasitugas,d.kodegolongan,
    COALESCE(ROUND(DATEDIFF('".date('Y-m-d')."',d.tanggallahir)/365.25,1),0) as umur,kodebiaya
    from ".$dbname.".sdm_pengobatanht a 
	  left join ".$dbname.".datakaryawan d
	  on a.karyawanid=d.karyawanid 
          left join ".$dbname.".datakaryawan e
	  on a.karyawanid=e.karyawanid
	  where a.periode like '".$periode."%'
	  and e.lokasitugas='".$kodeorg."'
        group by a.karyawanid,kodebiaya order by klaim desc";
 
$res2=mysql_query($str2);
while($bar2=mysql_fetch_object($res2)){
    $kdBiaya[$bar2->kodebiaya]=$bar2->kodebiaya;
    $idKary[$bar2->karyawanid]=$bar2->karyawanid;
    $jmlhRp[$bar2->karyawanid.$bar2->kodebiaya]=$bar2->klaim;
    $umurKary[$bar2->karyawanid]=$bar2->umur;
    $kdGol[$bar2->karyawanid]=$bar2->kodegolongan;
    $nmKary[$bar2->karyawanid]=$bar2->namakaryawan;
    $lksiKary[$bar2->karyawanid]=$bar2->lokasitugas;
}
$stream="Laporan Ranking Biaya/Karyawan ".$periode." ".$kodeorg."
<table border=1>
<thead>
<tr>
    <td bgcolor=#dedede>Rank</td>
    <td bgcolor=#dedede>".$_SESSION['lang']['namakaryawan']."</td>
    <td bgcolor=#dedede>".$_SESSION['lang']['kodegolongan']."</td>
    <td bgcolor=#dedede>".$_SESSION['lang']['umur']." (yrs)</td>    
    <td bgcolor=#dedede>".$_SESSION['lang']['lokasitugas']."</td>";
foreach($kdBiaya as $lsBy){
    $stream.="<td bgcolor=#dedede>".$optNmRwt[$lsBy]."</td>";
}
$stream.="<td bgcolor=#dedede>".$_SESSION['lang']['total']."</td>
</tr>
</thead>
<tbody>";  
$res2=mysql_query($str2);    
$no=0;
 foreach($idKary as $lstKary){
    $no+=1;
    $stream.="<tr class=rowcontent>
            <td>".$no."</td>";
     $stream.="<td>".$nmKary[$lstKary]."</td>
         <td>".$kdGol[$lstKary]."</td>
            <td align=right>".$umurKary[$lstKary]."</td>
            <td>".$lksiKary[$lstKary]."</td>";
            foreach($kdBiaya as $lsBy){
                $stream.="<td align=right>".number_format($jmlhRp[$lstKary.$lsBy])."</td>";
                $total[$lstKary]+=$jmlhRp[$lstKary.$lsBy]; 
                $totPerBy[$lsBy]+=$jmlhRp[$lstKary.$lsBy]; 
            }
    $stream.="<td>".$_SESSION['lang']['total']."</td>";
    $stream.="</tr>";	  	

}   
$stream.="<tr class=rowcontent>
              <td></td>
               <td colspan=3 align=right>".$_SESSION['lang']['total']."</td>";
               foreach($kdBiaya as $lsBy){
                   $stream.="<td align=right>".number_format($totPerBy[$lsBy])."</td>";
                   $totBy+=$totPerBy[$lsBy]; 
               }
$stream.="<td>".  number_format($totBy)."</td>";
$stream.="</tbody>
    <tfoot>
    </tfoot>
    </table>";	 
//write exel   
$nop_="LaporanRankingBiayaperKaryawan-".$periode.$kodeorg;
if(strlen($stream)>0)
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
    if(!fwrite($handle,$stream))
    {
    echo "<script language=javascript1.2>
        parent.window.alert('Cant convert to excel format');
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
   
	 
?>
