<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

if($_GET['kdPabrik']!='')
{
    $_POST=$_GET;
}

$_POST['proses']!=''?$proses=$_POST['proses']:$proses=$_GET['proses'];
$kdPabrik=$_POST['kdPabrik'];
$periode=$_POST['periode'];
$optNmBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optSat=makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');
$whr="periode='".$periode."' and kodegudang like '".$kdPabrik."%'";
$optHrg=makeOption($dbname, 'log_5saldobulanan', 'kodebarang,hargarata',$whr);
$optNmorg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$brd=0;
if($proses=='excel')
{
    $brd=1;
    $bg="align=center bgcolor=#DEDEDE";
}
if($proses!='getPeriode')
{
$cols = "a.tanggal as tanggal,sum(jamstagnasi) as jamstag,sum(jamdinasbruto) as jmdinbruto,sum(jumlahlori) as jumlori,sum(a.tbsdiolah) as tbsdiolah,sum(oer) as oer,sum(oerpk) as oerpk,nopengolahan";

$where = "a.kodeorg='".$kdPabrik."' and left(a.tanggal,7)='".$periode."'";
// $query = selectQuery($dbname,'pabrik_pengolahan',$cols,$where)." group by tanggal";
$query="select distinct ".$cols." from ".$dbname.".pabrik_pengolahan a left join ".$dbname.".pabrik_produksi b 
         on (a.kodeorg=b.kodeorg and a.tanggal=b.tanggal) where ".$where." group by a.tanggal";
//exit("Error".$query);
$tmpRes = fetchData($query);
if(empty($tmpRes)) {
    echo 'Warning : Data empty';
    exit;
}
//data oalh
foreach($tmpRes as $lstData =>$dtIsi)
{
    $dtTgl[$dtIsi['tanggal']]=$dtIsi['tanggal'];
    $dtJmstag[$dtIsi['tanggal']]=$dtIsi['jamstag'];
    $dtJmBruto[$dtIsi['tanggal']]=$dtIsi['jmdinbruto'];
    $dtJmLori[$dtIsi['tanggal']]=$dtIsi['jumlori'];
    $dtJmTbsDiolah[$dtIsi['tanggal']]=$dtIsi['tbsdiolah'];
    $dtJmoer[$dtIsi['tanggal']]=$dtIsi['oer'];
    $dtJmoerpk[$dtIsi['tanggal']]=$dtIsi['oerpk'];
}
//data mesin
$sData="select b.*,a.tanggal   from ".$dbname.".pabrik_pengolahanmesin b 
        left join ".$dbname.".pabrik_pengolahan a on b.nopengolahan=a.nopengolahan
        where ".$where."  ";
$qData=mysql_query($sData) or die(mysql_error($conn));
while($rData=  mysql_fetch_assoc($qData))
{
    if($drer!=$rData['nopengolahan'])
    {
        $drer=$rData['nopengolahan'];
        $derRow=1;
    }
    $dtStation[$rData['tanggal']][$derRow]=$rData['kodeorg'];
    $dtMesin[$rData['tanggal']][$derRow]=$rData['tahuntanam'];
    $dtJamOperasi[$rData['tanggal']][$derRow]=$rData['jammulai'];
    $dtJamSlsi[$rData['tanggal']][$derRow]=$rData['jamselesai'];
    $dtJamStag[$rData['tanggal']][$derRow]=$rData['jamstagnasi'];
    $dtKet[$rData['tanggal']][$derRow]=$rData['keterangan'];
    $dtprestasi[$rData['tanggal']][$derRow]=$rData['prestasi'];
    $jmlhRow[$rData['tanggal']]=$derRow;
    $derRow+=1;
}
//data barang
$sData2="select b.*,a.tanggal   from ".$dbname.".pabrik_pengolahan_barang b 
        left join ".$dbname.".pabrik_pengolahan a on b.nopengolahan=a.nopengolahan
        where ".$where."  ";
//exit("Error".$sData2);
//echo $sData2;
$qData2=mysql_query($sData2) or die(mysql_error($conn));
while($rData2=  mysql_fetch_assoc($qData2))
{
    if($drer!=$rData2['tanggal'])
    {
        $drer=$rData2['tanggal'];
        $derRow=1;
    }
    $dtKdBrg[$rData2['tanggal']][$derRow]=$rData2['kodebarang'];
    $dtJmlh[$rData2['tanggal']][$derRow]=$rData2['jumlah'];
    $jmlhRow2[$rData2['tanggal']]=$derRow;
    $derRow+=1;
}
    $tab.="<table cellpadding=1 cellspacing=1 border=".$brd." class=sortable>";
    $tab.="<thead><tr>";
    $tab.="<td colspan=7  align=center ".$bg.">Summaru Processing</td><td colspan=7 align=center  ".$bg.">Detail Processing</td>";
    $tab.="<td colspan=5  align=center ".$bg.">Detail Material Usage</td></tr>";
    $tab.="<tr>";
    #1#
    $tab.="<td ".$bg.">".$_SESSION['lang']['tanggal']."</td>";
    $tab.="<td ".$bg.">".$_SESSION['lang']['jamstagnasi']."</td>";
    $tab.="<td ".$bg.">".$_SESSION['lang']['jamoperasional']."</td>";
    $tab.="<td ".$bg.">".$_SESSION['lang']['jumlahlori']."</td>";
    $tab.="<td ".$bg.">".$_SESSION['lang']['tbsdiolah']."</td>";
    $tab.="<td ".$bg.">".$_SESSION['lang']['oer']."</td>";
    $tab.="<td ".$bg.">".$_SESSION['lang']['oerpk']."</td>";
    #7#
    #8#
    $tab.="<td ".$bg.">".$_SESSION['lang']['station']."</td>";
    $tab.="<td ".$bg.">".$_SESSION['lang']['mesin']."</td>";
    $tab.="<td ".$bg.">".$_SESSION['lang']['jammulai']."</td>";
    $tab.="<td ".$bg.">".$_SESSION['lang']['jamselesai']."</td>";
    $tab.="<td ".$bg.">".$_SESSION['lang']['jamstagnasi']."</td>";
    $tab.="<td ".$bg.">".$_SESSION['lang']['keterangan']."</td>";
    $tab.="<td ".$bg.">".$_SESSION['lang']['prestasi']."</td>";
    #14#
    $tab.="<td ".$bg.">".$_SESSION['lang']['namabarang']."</td>";
    $tab.="<td ".$bg.">".$_SESSION['lang']['jumlah']."</td>";
    $tab.="<td ".$bg.">".$_SESSION['lang']['satuan']."</td>";
    $tab.="<td ".$bg.">".$_SESSION['lang']['hargasatuan']."</td>";
    $tab.="<td ".$bg.">".$_SESSION['lang']['total']."</td>";
    $tab.="</tr></thead><tbody>";
    foreach($dtTgl as $lstTgl=>$dataTgl)
    {
      if($jmlhRow[$dataTgl]==1)
      {
          $aer=1;
            
            $tab.="<tr class=rowcontent>";
            #1#
            $tab.="<td>".$dataTgl."</td>";
            $tab.="<td align=right>".$dtJmstag[$dataTgl]."</td>";
            $tab.="<td align=right>".$dtJmBruto[$dataTgl]."</td>";
            $tab.="<td align=right>".$dtJmLori[$dataTgl]."</td>";
            $tab.="<td align=right>".number_format($dtJmTbsDiolah[$dataTgl],0)."</td>";
            $tab.="<td align=right>".$dtJmoer[$dataTgl]."</td>";
            $tab.="<td align=right>".$dtJmoerpk[$dataTgl]."</td>";
            #7#
            #8#
            $tab.="<td>".$optNmorg[$dtStation[$dataTgl][$aer]]."</td>";
            $tab.="<td>".$optNmorg[$dtMesin[$dataTgl][$aer]]."</td>";
            $tab.="<td align=right>".$dtJamOperasi[$dataTgl][$aer]."</td>";
            $tab.="<td align=right>".$dtJamSlsi[$dataTgl][$aer]."</td>";
            $tab.="<td align=right>".$dtJamStag[$dataTgl][$aer]."</td>";
            $tab.="<td>".$dtKet[$dataTgl][$aer]."</td>";
            $tab.="<td>".$dtprestasi[$dataTgl][$aer]."</td>";
            #14#
            
            $tab.="<td>".$optNmBrg[$dtKdBrg[$dataTgl][$aer]]."</td>";
            $tab.="<td align=right>".$dtJmlh[$dataTgl][$aer]."</td>";
            $tab.="<td>".$optSat[$dtKdBrg[$dataTgl][$aer]]."</td>";
            $tab.="<td align=right>".number_format($optHrg[$dtKdBrg[$dataTgl][$aer]],2)."</td>";
            $totalHrg[$dtKdBrg[$dataTgl][$aer]]=$dtJmlh[$dataTgl][$aer]*$optHrg[$dtKdBrg[$dataTgl][$aer]];
            $tab.="<td align=right>".number_format($totalHrg[$dtKdBrg[$dataTgl][$aer]],2)."</td>";
            $tab.="</tr>";
      }
      else
      {
          for($aer=1;$aer<=$jmlhRow[$dataTgl];$aer++)
          {
            $tab.="<tr class=rowcontent>";
            #1#
            if($aer==1)
            {
                $tab.="<td>".$dataTgl."</td>";
                $tab.="<td align=right>".$dtJmstag[$dataTgl]."</td>";
                $tab.="<td align=right>".$dtJmBruto[$dataTgl]."</td>";
                $tab.="<td align=right>".$dtJmLori[$dataTgl]."</td>";
                $tab.="<td align=right>".number_format($dtJmTbsDiolah[$dataTgl],0)."</td>";
                $tab.="<td align=right>".$dtJmoer[$dataTgl]."</td>";
                $tab.="<td align=right>".$dtJmoerpk[$dataTgl]."</td>";
            }
            else if($aer==2)
            {
                $tab.="<td rowspan='".($jmlhRow[$dataTgl]-1)."' colspan=7>&nbsp;</td>";
            }
            #7#
            #8#
            $tab.="<td>".$optNmorg[$dtStation[$dataTgl][$aer]]."</td>";
            $tab.="<td>".$optNmorg[$dtMesin[$dataTgl][$aer]]."</td>";
            $tab.="<td align=right>".$dtJamOperasi[$dataTgl][$aer]."</td>";
            $tab.="<td align=right>".$dtJamSlsi[$dataTgl][$aer]."</td>";
            $tab.="<td align=right>".$dtJamStag[$dataTgl][$aer]."</td>";
            $tab.="<td>".$dtKet[$dataTgl][$aer]."</td>";
            $tab.="<td>".$dtprestasi[$dataTgl][$aer]."</td>";
            #14#
            
                $tab.="<td>".$optNmBrg[$dtKdBrg[$dataTgl][$aer]]."</td>";
                $tab.="<td align=right>".$dtJmlh[$dataTgl][$aer]."</td>";
                $tab.="<td>".$optSat[$dtKdBrg[$dataTgl][$aer]]."</td>";
                $tab.="<td align=right>".number_format($optHrg[$dtKdBrg[$dataTgl][$aer]],2)."</td>";
                $totalHrg[$dtKdBrg[$dataTgl][$aer]]=$dtJmlh[$dataTgl][$aer]*$optHrg[$dtKdBrg[$dataTgl][$aer]];
                $tab.="<td align=right>".number_format($totalHrg[$dtKdBrg[$dataTgl][$aer]],2)."</td>";
           
           
            $tab.="</tr>";
          }
      }
        
    }
}
switch($proses)
{
	case'preview':
        echo $tab;
	break;
	
	case'excel':
			
        $tab.="</tbody></table>Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
        $tglSkrg=date("Ymd");
        $nop_="LaporanPengolahan";
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
        case'getPeriode':
        $optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $sPeriode="select distinct left(tanggal,7) as periode from ".$dbname.".pabrik_pengolahan 
                   where kodeorg='".$kdPabrik."' order by tanggal desc";
        $qPeriode=mysql_query($sPeriode) or die(mysql_error($conn));
        while($rPeriode=mysql_fetch_assoc($qPeriode))
        {
             $optPeriode.="<option value='".$rPeriode['periode']."'>".$rPeriode['periode']."</option>";
        }
        echo $optPeriode;
        break;
	default:
	break;
}
?>