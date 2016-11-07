<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

$proses=$_GET['proses'];
$_POST['kdOrg2']==''?$kodeorg=$_GET['kdOrg2']:$kodeorg=$_POST['kdOrg2'];
$_POST['periode2']==''?$periode=$_GET['periode2']:$periode=$_POST['periode2'];

$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');

if($periode==''||$kodeorg=='')
{
    exit("Error:Field Tidak Boleh Kosong");
}

if($_GET['proses']=='excel')
{
    $bg=" bgcolor=#DEDEDE";
    $brdr=1;
    $tab.="<table>
        <tr><td colspan=5 align=left><font size=3>".strtoupper('Material Checker')."</font></td></tr> 
        <tr><td colspan=5 align=left>".$optNm[$kodeorg]."</td></tr>   
        <tr><td colspan=5>".$_SESSION['lang']['periode']." ".$periode."</td></tr>   
        </table>";
}
else
{ 
    $bg="";
    $brdr=0;
}

// kamus akun
$sakun="select noakun, namaakun from ".$dbname.".keu_5akun 
    where substr(noakun,1,3) in ('121', '122')";
$qakun=mysql_query($sakun) or die(mysql_error($conn));
while($rakun=mysql_fetch_assoc($qakun)){    
    $kamusakun[$rakun['noakun']]=$rakun['namaakun'];    
}

// cari notransaksi pemakaian unit lain
$ssumber="select noreferensi from ".$dbname.".keu_jurnaldt
    where tanggal like '".$periode."%' and substr(noakun,1,3) in ('121', '122') and substr(noreferensi,16,4) = '".$kodeorg."' and substr(noreferensi,16,4) != kodeorg
    order by noreferensi";
$notrantu="(";
$qsumber=mysql_query($ssumber) or die(mysql_error($conn));
while($rsumber=mysql_fetch_assoc($qsumber)){    
    if($rsumber['noreferensi']!='')$notrantu.="'".$rsumber['noreferensi']."',";
}
$notrantu=substr($notrantu, 0, -1);
$notrantu.=')';
if($notrantu==')')$notrantu="('')";

// cari nojurnal tujuan yang sudah masuk jurnal
$stujuan="select a.nojurnal, substr(a.tanggal,1,7) as periode, a.noakun, a.noreferensi, a.jumlah, a.kodebarang, b.tipetransaksi, b.notransaksireferensi, b.kodegudang, b.gudangx from ".$dbname.".keu_jurnaldt a
    left join ".$dbname.".log_transaksiht b on a.noreferensi=b.notransaksi
    where substr(a.noakun,1,3) in ('121', '122') and a.noreferensi in ".$notrantu." ";
$qtujuan=mysql_query($stujuan) or die(mysql_error($conn));
while($rtujuan=mysql_fetch_assoc($qtujuan)){    
    $key=$rtujuan['noreferensi'].$rtujuan['kodebarang'];
    $datakey[$key]=$key;
    
    if(substr($rtujuan['nojurnal'],9,4)==substr($rtujuan['noreferensi'],15,4)){
        $data[$key]['sou_nojurnal'].=$rtujuan['nojurnal'].'</br>';
        $data[$key]['sou_periode']=$rtujuan['periode'];
        $data[$key]['sou_noakun']=$rtujuan['noakun'];
        $data[$key]['sou_noreferensi']=$rtujuan['noreferensi'];
        $data[$key]['sou_jumlah']+=$rtujuan['jumlah'];
        $data[$key]['sou_kodebarang']=$rtujuan['kodebarang'];
        $data[$key]['sou_tipetransaksi']=$rtujuan['tipetransaksi'];    
    }else{
        $data[$key]['tar_nojurnal'].=$rtujuan['nojurnal'].'</br>';
        $data[$key]['tar_periode']=$rtujuan['periode'];
        $data[$key]['tar_noakun']=$rtujuan['noakun'];
        $data[$key]['tar_noreferensi']=$rtujuan['noreferensi'];
        $data[$key]['tar_jumlah']+=$rtujuan['jumlah'];
        $data[$key]['tar_kodebarang']=$rtujuan['kodebarang'];
        $data[$key]['tar_tipetransaksi']=$rtujuan['tipetransaksi'];    
    }    
}

//get data klo bukan pdf
if($_GET['proses']!='pdf')
{
    $tab.="<table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable><thead>";
    $tab.="<tr class=rowheader>";
    $tab.="<td align=center ".$bg." colspan=7>".$_SESSION['lang']['sumber']."</td>";
    $tab.="<td align=center ".$bg." colspan=7>".$_SESSION['lang']['tujuan']."</td>";
    $tab.="</tr>";
    $tab.="<tr class=rowheader>";
    $tab.="<td align=center ".$bg.">".$_SESSION['lang']['notransaksi']."</td>";
    $tab.="<td align=center ".$bg.">".$_SESSION['lang']['tipetransaksi']."</td>";
    $tab.="<td align=center ".$bg.">".$_SESSION['lang']['kodebarang']."</td>";
    $tab.="<td align=center ".$bg.">".$_SESSION['lang']['nojurnal']."</td>";
    $tab.="<td align=center ".$bg.">".$_SESSION['lang']['periode']."</td>";
    $tab.="<td align=center ".$bg.">".$_SESSION['lang']['noakun']."</td>";
    $tab.="<td align=center ".$bg.">".$_SESSION['lang']['jumlah']."</td>";
    $tab.="<td align=center ".$bg.">".$_SESSION['lang']['notransaksi']."</td>";
    $tab.="<td align=center ".$bg.">".$_SESSION['lang']['tipetransaksi']."</td>";
    $tab.="<td align=center ".$bg.">".$_SESSION['lang']['kodebarang']."</td>";
    $tab.="<td align=center ".$bg.">".$_SESSION['lang']['nojurnal']."</td>";
    $tab.="<td align=center ".$bg.">".$_SESSION['lang']['periode']."</td>";
    $tab.="<td align=center ".$bg.">".$_SESSION['lang']['noakun']."</td>";
    $tab.="<td align=center ".$bg.">".$_SESSION['lang']['jumlah']."</td>";

    $tab.="</tr></thead><tbody>";
            
    if(!empty($datakey))foreach($datakey as $key){        
        $sou_org=substr($data[$key]['sou_nojurnal'],9,4);
        $tar_org=substr($data[$key]['tar_nojurnal'],9,4);
        
        $orgkey=$sou_org.$tar_org.$data[$key]['sou_periode'].$data[$key]['tar_periode'];
        $datasum[$orgkey]['transaksi']=$sou_org.' - '.$tar_org;
        $datasum[$orgkey]['periode']=$data[$key]['sou_periode'].' - '.$data[$key]['tar_periode'];     
        $selisih=$data[$key]['sou_jumlah']+$data[$key]['tar_jumlah'];  
        $datasum[$orgkey]['sou_jumlah']+=$data[$key]['sou_jumlah'];        
        $datasum[$orgkey]['tar_jumlah']+=$data[$key]['tar_jumlah'];        
        
        if($data[$key]['sou_periode']!=$data[$key]['tar_periode']){
            $warnamerah=" bgcolor=pink";
            $datasum[$orgkey]['merah']=" bgcolor=pink";
        }else{
            $warnamerah="";
            $datasum[$orgkey]['merah']="";
        }
        
        if($selisih<=1){
            $warnamerah2="";
        }else{
            $warnamerah2=" bgcolor=pink";
        }
        
        if((abs($selisih)<=1)&&($data[$key]['sou_periode']==$data[$key]['tar_periode'])){
            unset($data[$key]);
        }else{
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$data[$key]['sou_noreferensi']."</td>";
            $tab.="<td align=center>".$data[$key]['sou_tipetransaksi']."</td>";
            $tab.="<td align=center>".$data[$key]['sou_kodebarang']."</td>";
            $tab.="<td>".$data[$key]['sou_nojurnal']."</td>";
            $tab.="<td".$warnamerah.">".substr(tanggalnormal($data[$key]['sou_periode']),1,7)."</td>";
            $tab.="<td>".$data[$key]['sou_noakun']." - ".$kamusakun[$data[$key]['sou_noakun']]."</td>";
            $tab.="<td".$warnamerah2." align=right>".number_format($data[$key]['sou_jumlah'])."</td>";
            $tab.="<td>".$data[$key]['tar_noreferensi']."</td>";
            $tab.="<td align=center>".$data[$key]['tar_tipetransaksi']."</td>";
            $tab.="<td align=center>".$data[$key]['tar_kodebarang']."</td>";
            $tab.="<td>".$data[$key]['tar_nojurnal']."</td>";
            $tab.="<td".$warnamerah.">".substr(tanggalnormal($data[$key]['tar_periode']),1,7)."</td>";
            $tab.="<td>".$data[$key]['tar_noakun']." - ".$kamusakun[$data[$key]['tar_noakun']]."</td>";
            $tab.="<td".$warnamerah2." align=right>".number_format($data[$key]['tar_jumlah'])."</td>";
            $tab.="</tr>";
        }
        
    }         
    if(empty($data)){
        $tab.="<tr class=rowcontent align=center><td colspan=14>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
    }
    $tab.="</tbody></table>";
           
//           echo "<pre>";
//           print_r($datasum);
//           echo "</pre>";
           
    $tab.="<table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable><thead>";
    $tab.="<tr class=rowheader>";
    $tab.="<td align=center ".$bg.">".$_SESSION['lang']['sumber']." ".$_SESSION['lang']['tujuan']."</td>";
    $tab.="<td align=center ".$bg.">".$_SESSION['lang']['periode']."</td>";
    $tab.="<td align=center ".$bg.">".$_SESSION['lang']['jumlah']." ".$_SESSION['lang']['sumber']."</td>";
    $tab.="<td align=center ".$bg.">".$_SESSION['lang']['jumlah']." ".$_SESSION['lang']['tujuan']."</td>";
    $tab.="<td align=center ".$bg.">".$_SESSION['lang']['selisih']."</td>";
    $tab.="</tr></thead><tbody>";           
    
    if(!empty($datasum))foreach($datasum as $key){
    $tab.="<tr class=rowcontent>";
    $tab.="<td>".$key['transaksi']."</td>";
    $tab.="<td".$key['merah'].">".$key['periode']."</td>";
    $tab.="<td align=right>".number_format($key['sou_jumlah'])."</td>";
    $tab.="<td align=right>".number_format($key['tar_jumlah'])."</td>";
    $selisih=$key['sou_jumlah']+$key['tar_jumlah'];
        if(abs($selisih)<=1){
            $warnamerah2="";
        }else{
            $warnamerah2=" bgcolor=pink";
        }    
    $tab.="<td".$warnamerah2." align=right>".number_format($selisih)."</td>";
    $tab.="</tr>";           
    }
    
    $tab.="</tbody></table>";
}

switch($proses)
{
    case'preview':

    echo $tab;
    break;

    case'excel':

    $tab.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
    $dte=date("YmdHis");
    $nop_="Selisih Material_".$kodeorg."".$periode."_".$dte;
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

    default:
    break;
}
	
?>
