<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');


$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses']; 
$_POST['tanggal']==''?$tanggal=$_GET['tanggal']:$tanggal=$_POST['tanggal']; 
$_POST['region']==''?$region=$_GET['region']:$region=$_POST['region']; 

if($proses=='preview'||$proses=='excel'||$region!=''){
    if($tanggal==''){
        exit("Error: All field required");
    }
    
    $str="select * from ".$dbname.".sdm_5tipekaryawan
        where 1";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res)){
        $tipekar[$bar->id]=$bar->id;
        $artitkr[$bar->id]=$bar->tipe;
    }

    if($region!=''){
        $str="select * from ".$dbname.".bgt_regional_assignment
            where regional = '".$region."'";        
    }else{
        $str="select * from ".$dbname.".bgt_regional_assignment
            where 1";
        
    }
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res)){
        if($region!=''){
            $regional[$bar->kodeunit]=$bar->kodeunit;            
        }else{
            $unitreg[$bar->kodeunit]=$bar->regional;
            $regional[$bar->regional]=$bar->regional;            
        }        
    }
    
    $str="select * from ".$dbname.".datakaryawan
        where tanggalmasuk <= ".tanggalsystem($tanggal)." and (tanggalkeluar > '".substr(tanggalsystem($tanggal),0,6)."01' or tanggalkeluar = '0000-00-00') ";
    //echo $str;
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res)){
        if($region!=''){
            $qwe=$bar->lokasitugas;
        }else{
            $qwe=$unitreg[$bar->lokasitugas];
        }
        $jumlahkar[$qwe][$bar->tipekaryawan]+=1;
    }
        
    if($proses!='excel'){
        $brd=0;
        $bgcolor="";
    }else{
        $tab.= $_SESSION['lang']['summary']." ".$_SESSION['lang']['karyawan']."<br>Tanggal: ".$tanggal." ";
        $brd=1;
        $bgcolor="bgcolor=#DEDEDE";
    }
    if($region==''){
        $region=$_SESSION['lang']['regional'];
    }else{
        if($proses!='excel')
        $tab.="<img onclick=level1excel(event,'sdm_slave_2summarykaryawan.php','".$tanggal."','".$region."') src=images/excel.jpg class=resicon title='MS.Excel'>";
    }
        
    $tab.="
    <table width=100% cellspacing=1 border=".$brd.">
    <thead>
    <tr>
        <td ".$bgcolor.">".$region."</td>";
        if(!empty($regional))foreach($regional as $reg)
            if($region!='')
            $tab.="<td ".$bgcolor." align=center title='Click to details...' onclick=getlevel1('".$tanggal."','".$reg."')>".$reg."</td>";
        $tab.="
        <td ".$bgcolor." align=center>".$_SESSION['lang']['total']."</td>
    </tr>        
    </thead>
    <tbody>";
    if(!empty($tipekar))foreach($tipekar as $tkr){
        $tab.="<tr class=rowcontent>
        <td>".$artitkr[$tkr]."</td>";
        $total[$tkr]=0;
        if(!empty($regional))foreach($regional as $reg){
            $tab.="<td align=right>".number_format($jumlahkar[$reg][$tkr])."</td>";
            $total[$tkr]+=$jumlahkar[$reg][$tkr];
            $totalgrand[$reg]+=$jumlahkar[$reg][$tkr];            
        }
        $tab.="
        <td align=right>".number_format($total[$tkr])."</td>
        </tr>";            
    }
    $tab.="<tr class=rowcontent>
    <td>".$_SESSION['lang']['total']."</td>";
    $totalnya=0;
    if(!empty($regional))foreach($regional as $reg){
        $tab.="<td align=right>".number_format($totalgrand[$reg])."</td>";
        $totalnya+=$totalgrand[$reg];            
    }
    $tab.="
    <td align=right>".number_format($totalnya)."</td>
    </tr>";            
    
    $tab.="</tbody></table>";
    
}else if($proses=='level1'){
//    $tabz='qwe';
}	

switch($proses)
{
    case'preview':
        echo $tab;
    break;
    case'level1':
        echo $tab;
    break;

    case'excel':
        $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
        $nop_="summary_karyawan_".$tanggal."_".$region;
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