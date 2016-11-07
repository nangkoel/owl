<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

if(isset($_POST['proses'])){
    $proses=$_POST['proses'];}
else{
    $proses=$_GET['proses'];}

$_GET['subbagian']==''?$kdOrg=$_POST['subbagian']:$kdOrg=$_GET['subbagian'];
$_GET['periode']==''?$periode=$_POST['periode']:$periode=$_GET['periode'];
$_POST['judul']==''?$judul=$_GET['judul']:$judul=$_POST['judul'];
$_GET['noakun']==''?$noakun=$_POST['noakun']:$noakun=$_GET['noakun'];

//exit("Error:$judul");

$thn=explode("-",$periode);
$bln=intval($thn[1]);
$thnLalu=$thn[0];
if(strlen($bln)<2)
{
    $bulan="0".$bln;
}
else{
    $bulan=$bln;
}
//buat bi dan sbi
    if(strlen($thn[1])<2){
        $fld_st="rp0".$thn[1];
    }
    else{
        $fld_st="rp".$thn[1];
    }
for($asr5=1;$asr5<=$thn[1];$asr5++){
    if(strlen($asr5)<2){
        if($asr5==1){
            $fld_st5="rp0".$asr5;
        }
        else{
         $fld_st5.="+rp0".$asr5;
        }
    }
    else{
        $fld_st5.="+rp".$asr5;
    }
}
$optNmBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
#realisasi produksi cpo dan pk
$sJmlhCpo="select sum(kuantitas) as jmlhcpo,sum(kernelquantity) as jmlhkernel from ".$dbname.".pabrik_masukkeluartangki
           where kodeorg='".substr($kdOrg,0,4)."' and left(tanggal,7)='".$periode."'";
$qJmlhCpo=mysql_query($sJmlhCpo) or die(mysql_error($conn));
$rJmlhCpo=mysql_fetch_assoc($qJmlhCpo);

$sJmlhCpoSbi="select sum(kuantitas) as jmlhcposbi,sum(kernelquantity) as jmlhkernelsbi from ".$dbname.".pabrik_masukkeluartangki
           where kodeorg='".substr($kdOrg,0,4)."' and tanggal between '".$thn[0]."-01-01' and LAST_DAY('".$periode."-15')";
$qJmlhCpoSbi=mysql_query($sJmlhCpoSbi) or die(mysql_error($conn));
$rJmlhCpoSbi=mysql_fetch_assoc($qJmlhCpoSbi);
#end realisasi produksi cpo dan pk

#realisasi biaya
$srealisasiBln="select sum(jumlah) as jumlah,noakun as station from ".$dbname.".keu_jurnaldt 
              where tanggal like '".$periode."%' and kodeblok like '".$kdOrg."%' and left(noakun,3) in ('631','632')
              group by noakun order by noakun asc ";
//echo $srealisasiBln;
$qRealisasiBln = mysql_query($srealisasiBln) or die(mysql_error($conn));
while($rRealisasiBln=mysql_fetch_assoc($qRealisasiBln)){
    $byStation[$rRealisasiBln['station']]=$rRealisasiBln['jumlah'];
}

$srealisasiBlnSbi="select sum(jumlah) as jumlah,noakun as station from ".$dbname.".keu_jurnaldt 
                   where tanggal between '".$thn[0]."-01-01' and LAST_DAY('".$periode."-15') 
                   and kodeblok like '".$kdOrg."%' and left(noakun,3) in ('631','632')
                   group by noakun order by noakun asc ";
$qRealisasiBlnSbi = mysql_query($srealisasiBlnSbi) or die(mysql_error($conn));
while($rRealisasiBlnSbi=mysql_fetch_assoc($qRealisasiBlnSbi)){
    $byStationSbi[$rRealisasiBlnSbi['station']]=$rRealisasiBlnSbi['jumlah'];
}
#end realisasi biaya

#Budget biaya pabrik
$sBgt="select distinct sum(rp".$bulan.") as budgetProd,noakun as station from ".$dbname.".bgt_budget_detail
       where left(noakun,3) in ('631','632') and kodeorg like '".$kdOrg."%' and  tahunbudget='".$thn[0]."' 
       group by noakun order by noakun asc";
$qBgt=mysql_query($sBgt) or die(mysql_error($conn));
while($rBgt=  mysql_fetch_assoc($qBgt)){
    $byBgt[$rBgt['station']]=$rBgt['budgetProd'];
}
$sBgtSbi="select distinct sum(".$fld_st5.") as budgetProd,noakun as station from ".$dbname.".bgt_budget_detail
       where left(noakun,3) in ('631','632') and kodeorg like '".$kdOrg."%' and  tahunbudget='".$thn[0]."' 
       group by noakun order by noakun asc";
$qBgtSbi=mysql_query($sBgtSbi) or die(mysql_error($conn));
while($rBgtSbi=  mysql_fetch_assoc($qBgtSbi)){
    $byBgtSbi[$rBgtSbi['station']]=$rBgtSbi['budgetProd'];
}
#end budget biaya pabrik
#List Station
$s_station ="select noakun,namaakun from ".$dbname.".keu_5akun
             where left(noakun,3) in ('631','632') and char_length(noakun)!=3";
$q_station = mysql_query($s_station) or die(mysql_error($conn));
while($r_station=mysql_fetch_assoc($q_station))
{
    $kodeorg[]=$r_station['noakun'];
    $station[$r_station['noakun']]=$r_station['namaakun'];
}
if(($proses=='excel')||($proses=='getExccel')){
    $bgcoloraja="bgcolor=#DEDEDE ";
    $brdr=1;
    $tab.="
    <table>
    <tr><td colspan=7  align=center><b>".$_GET['judul']."</b></td></tr>
    <tr><td colspan=3 align=left><b>".$_SESSION['lang']['organisasi']." : ".$kdOrg."</b></td>
        <td colspan=4 align=right><b>".$_SESSION['lang']['periode']." : ".substr(tanggalnormal($periode),1,7)."</b></td></tr>
    <tr><td colspan=7 align=left>&nbsp;</td></tr>
    </table>";
}
else{
    $brdr=0;
}
if(($proses=='getDetail')||($proses=='getExccel')){
        $tab.="<table><tr><td>";
        $tab.="<table cellspacing=1 cellpadding=1 border=".$brdr." class=sortable>";
        $tab.="<thead><tr ".$bgcoloraja.">";
        $tab.="<td rowspan=2 ".$bgcoloraja.">".$_SESSION['lang']['namabarang']."</td>";
        $tab.="<td colspan=2 ".$bgcoloraja.">".$_SESSION['lang']['jumlahproduksi']."</td>";
        $tab.="</tr><tr>";
        $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['bulanini']."</td>";
        $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['sdbulanini']."</td></tr></thead><tbody><tr class=rowcontent>";
        $tab.="<td>".$optNmBrg['40000001']."</td>";
        $tab.="<td align=right>".number_format($rJmlhCpo['jmlhcpo'],0)."</td>";
        $tab.="<td align=right>".number_format($rJmlhCpoSbi['jmlhcposbi'],0)."</td></tr>";
        $tab.="<tr class=rowcontent><td>".$optNmBrg['40000002']."</td>";
        $tab.="<td align=right>".number_format($rJmlhCpo['jmlhkernel'],0)."</td>";
        $tab.="<td align=right>".number_format($rJmlhCpoSbi['jmlhkernelsbi'],0)."</td></tr>";
        $tab.="<tr><td colspan=2></td></tr>";
        $tab.="</tbody></table></td></tr><tr><td>";
	$tab.="<table cellspacing=1 cellpadding=1 border=".$brdr." class=sortable>
	<thead class=rowheader ".$bgcoloraja.">";
        $tab.="<tr align=center>";
        $tab.="<td rowspan=2 ".$bgcoloraja.">".$_SESSION['lang']['noakun']."</td>";
        $tab.="<td rowspan=2 ".$bgcoloraja.">".$_SESSION['lang']['namaakun']."</td>";
        $tab.="<td colspan=4 ".$bgcoloraja.">".$_SESSION['lang']['bulanini']."</td>";
        $tab.="<td colspan=4 ".$bgcoloraja.">".$_SESSION['lang']['sdbulanini']."</td></tr>";
        $tab.="<tr align=center>
               <td ".$bgcoloraja.">".$_SESSION['lang']['realisasi']."</td><td ".$bgcoloraja.">".$_SESSION['lang']['anggaran']."</td><td ".$bgcoloraja.">".$_SESSION['lang']['selisih']."</td><td ".$bgcoloraja.">".$_SESSION['lang']['rpperkg']."</td>";
        $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['realisasi']."</td><td ".$bgcoloraja.">".$_SESSION['lang']['anggaran']."</td><td ".$bgcoloraja.">".$_SESSION['lang']['selisih']."</td><td ".$bgcoloraja.">".$_SESSION['lang']['rpperkg']."</td>";
        $tab.="</tr></thead>";
        if(!empty($kodeorg)){
            $total_bi_realst=0;
            foreach($kodeorg as $lst_station) {
                $derclick="";
                if(($byStation[$lst_station]!=0)||($byStation[$lst_station]!='')){
                    $derclick=" style=cursor:pointer; onclick=getDetail2('".$kdOrg."','".$periode."','".$lst_station."','lbm_slave_pks_byproduksiperstationdetail')";
                }
                $tab.="<tr class=rowcontent ".$derclick.">";
                $tab.="<td>".$lst_station."</td>";
                $tab.="<td>".$station[$lst_station]."</td>";
                $tab.="<td align=right>".number_format($byStation[$lst_station],0)."</td>";
                $tab.="<td align=right>".number_format($byBgt[$lst_station],0)."</td>";
                
                # Selisih Station BI
                $biselisih_st=$byBgt[$lst_station]-$byStation[$lst_station];
                @$rpperkgbi[$lst_station]=$byStation[$lst_station]/$rJmlhCpo['jmlhcpo'];
                $tab.="<td align=right>".number_format($biselisih_st,0)."</td>";
                $tab.="<td align=right>".number_format($rpperkgbi[$lst_station],0)."</td>";
                $tab.="<td align=right>".number_format($byStationSbi[$lst_station],0)."</td>";
                $tab.="<td align=right>".number_format($byBgtSbi[$lst_station],0)."</td>";
                # Selisih Station SDBI
                $sdbiselisih_st=$byBgtSbi[$lst_station]-$byStationSbi[$lst_station];
                @$rpperkgsbi[$lst_station]=$byStationSbi[$lst_station]/$rJmlhCpoSbi['jmlhcposbi'];
                $tab.="<td align=right>".number_format($sdbiselisih_st,0)."</td>";
                $tab.="<td align=right>".number_format($rpperkgsbi[$lst_station],0)."</td>";
                $tab.="</tr>";
                $total_bi_realst += $byStation[$lst_station];
                $total_bi_budst += $byBgt[$lst_station];
                $total_bi_selisih += $biselisih_st;
                $total_sdbi_realst += $byStationSbi[$lst_station];
                $total_sdbi_budst += $byBgtSbi[$lst_station];
                $total_sdbi_selisih += $sdbiselisih_st;
                $total_rp_bi+=$rpperkgbi[$lst_station];
                $total_rp_sbi+=$rpperkgsbi[$lst_station];
            } 
        }
        
        $tab.="<tr class=rowcontent>";//exit("Error:$judul");
        $tab.="<td align=left colspan=2><b>".$_SESSION['lang']['total']." ".$judul."</b></td>";
        #Total BI
        $total_bi_real=$total_bi_realst;
        $total_bi_bgt=$total_bi_budst;
        $total_bi_selisih=$total_bi_selisih;
        $total_rp_per_kg=$total_rp_bi;
        
        #Total SDBI
        $total_sdbi_real=$total_sdbi_realst;
        $total_sdbi_bgt=$total_sdbi_budst;
        $total_sdbi_selisih=$total_sdbi_selisih;
        $total_rp_per_kgsbi=$total_rp_sbi;
        
        $tab.="<td align=right><b>".number_format($total_bi_real,0)."</b></td>";
        $tab.="<td align=right><b>".number_format($total_bi_bgt,0)."</b></td>";
        $tab.="<td align=right><b>".number_format($total_bi_selisih,0)."</b></td>";
        $tab.="<td align=right><b>".number_format($total_rp_per_kg,0)."</b></td>";
        $tab.="<td align=right><b>".number_format($total_sdbi_real,0)."</b></td>";
        $tab.="<td align=right><b>".number_format($total_sdbi_bgt,0)."</b></td>";
        $tab.="<td align=right><b>".number_format($total_sdbi_selisih,0)."</b></td>";
        $tab.="<td align=right><b>".number_format($total_rp_per_kgsbi,0)."</b></td>";
        $tab.="</b></tr>";
        $tab.="</table></td></tr></table>";
        if($proses=='getDetail'){
            $arr="##subbagian##periode";
            $tab.="
                <input type=hidden id=subbagian value='".$kdOrg."' />
                <input type=hidden id=periode value='".$periode."' />
                <button style=cursor:pointer; onclick=getBack1() class=mybutton>".$_SESSION['lang']['back']."</button>
                <button onclick=\"zExcel(event,'lbm_slave_pks_byproduksiperstationdetail.php','".$arr."','getExccel')\" class=\"mybutton\" name=\"excel\" id=\"excel\">".$_SESSION['lang']['excel']."</button>";
        }
}
switch($proses)
{
	case'getDetail':
	echo $tab;
	break;
        case'getExccel':
        $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
        $dte=date("Hms");
        $nop_="BiayaProduksiDetailStasiun".$dte;
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
     case'getDetail2':
            #pemakaian barang
            $sreal="select distinct sum(jumlah) as rupiah,kodebarang,kodekegiatan,keterangan,nojurnal
                    from ".$dbname.".keu_jurnaldt_vw where left(tanggal,7)='".$periode."'
                    and noakun='".$noakun."' and kodeblok like '".$kdOrg."%'  
                     group by nojurnal";
             //echo $sreal;
            $qReal=mysql_query($sreal) or die(mysql_error($conn));
            while($rreal=  mysql_fetch_assoc($qReal)){
                $dtNojurnal[$rreal['nojurnal']]=$rreal['nojurnal'];
                $dtKegiatan[$rreal['nojurnal']]=$rreal['kodekegiatan'];
                $dtBrg[$rreal['nojurnal']]=$rreal['kodebarang'];
                $dtRupiah[$rreal['nojurnal']]=$rreal['rupiah'];
                $dtKet[$rreal['nojurnal']]=$rreal['keterangan'];
            }

            $brd=0;
            $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>";
            $tab.="<thead><tr>";
            $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['nojurnal']."</td>";
            $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['keterangan']."</td>";
            $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['kodebarang']."</td>";
            $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['namabarang']."</td>";
            $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['kodekegiatan']."</td>";
            $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['namakegiatan']."</td>";
            $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['rp']." BI</td>";
            $tab.="</tr></thead><tbody>";
            if(empty($dtNojurnal)){
                $tab.="<tr class=rowcontent>
                       <td colspan=11>".$_SESSION['lang']['dataempty']."</td></tr>";
            }else{
                    
                    foreach($dtNojurnal as $lstJurnal){
                            $tab.="<tr class=rowcontent>
                                  <td>".$lstJurnal."</td>";
                            $tab.="<td>".$dtKet[$lstJurnal]."</td>";
                            $tab.="<td>".$dtBrg[$lstJurnal]."</td>";
                            $tab.="<td>".$optNmBrg[$dtBrg[$lstJurnal]]."</td>";
                            $tab.="<td>".$dtKegiatan[$lstJurnal]."</td>";
                            $tab.="<td>".$optNmKeg[$dtKegiatan[$lstJurnal]]."</td>";
                            $tab.="<td align=right>".number_format($dtRupiah[$lstJurnal],2)."</td></tr>";
                            $sbtot+=$dtRupiah[$lstJurnal];
                    }
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td colspan=6 align=right>".$_SESSION['lang']['total']."</td>";
                    $tab.="<td align=right>".number_format($sbtot,2)."</td></tr>";
                    $tab.="</tbody></table>";
            }
             $arr="##subbagian##periode##noakun";
             $tab.="
                    <input type=hidden id=noakun value='".$noakun."' />
                    <button style=cursor:pointer;  class=mybutton onclick=getBack2()>".$_SESSION['lang']['back']."</button>
                    <button onclick=\"zExcel(event,'lbm_slave_pks_byproduksiperstationdetail.php','".$arr."','getExccel2')\" class=\"mybutton\" name=\"excel\" id=\"excel\">".$_SESSION['lang']['excel']."</button>";
            echo $tab;
         break;
         case'getExccel2':
              #pemakaian barang
            $sreal="select distinct sum(jumlah) as rupiah,kodebarang,kodekegiatan,keterangan,nojurnal
                    from ".$dbname.".keu_jurnaldt_vw where left(tanggal,7)='".$periode."'
                    and noakun='".$noakun."' and kodeblok like '".$kdOrg."%'  
                     group by nojurnal";
             //echo $sreal;
            $qReal=mysql_query($sreal) or die(mysql_error($conn));
            while($rreal=  mysql_fetch_assoc($qReal)){
                $dtNojurnal[$rreal['nojurnal']]=$rreal['nojurnal'];
                $dtKegiatan[$rreal['nojurnal']]=$rreal['kodekegiatan'];
                $dtBrg[$rreal['nojurnal']]=$rreal['kodebarang'];
                $dtRupiah[$rreal['nojurnal']]=$rreal['rupiah'];
                $dtKet[$rreal['nojurnal']]=$rreal['keterangan'];
            }

            $brd=1;
            $tab.="<table cellpadding=1 cellspacing=1 border=".$brd." class=sortable>";
            $tab.="<thead><tr>";
            $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['nojurnal']."</td>";
            $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['keterangan']."</td>";
            $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['kodebarang']."</td>";
            $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['namabarang']."</td>";
            $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['kodekegiatan']."</td>";
            $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['namakegiatan']."</td>";
            $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['rp']." BI</td>";
            $tab.="</tr></thead><tbody>";
            if(empty($dtNojurnal)){
                $tab.="<tr class=rowcontent>
                       <td colspan=11>".$_SESSION['lang']['dataempty']."</td></tr>";
            }else{
                    
                    foreach($dtNojurnal as $lstJurnal){
                            $tab.="<tr class=rowcontent>
                                  <td>".$lstJurnal."</td>";
                            $tab.="<td>".$dtKet[$lstJurnal]."</td>";
                            $tab.="<td>".$dtBrg[$lstJurnal]."</td>";
                            $tab.="<td>".$optNmBrg[$dtBrg[$lstJurnal]]."</td>";
                            $tab.="<td>".$dtKegiatan[$lstJurnal]."</td>";
                            $tab.="<td>".$optNmKeg[$dtKegiatan[$lstJurnal]]."</td>";
                            $tab.="<td align=right>".number_format($dtRupiah[$lstJurnal],2)."</td></tr>";
                            $sbtot+=$dtRupiah[$lstJurnal];
                    }
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td colspan=6 align=right>".$_SESSION['lang']['total']."</td>";
                    $tab.="<td align=right>".number_format($sbtot,2)."</td></tr>";
                    $tab.="</tbody></table>";
            }
         $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
        $dte=date("Hms");
        $nop_="BiayaProductionDetail2".$dte;
        if(strlen($tab)>0)
            {
                $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
                gzwrite($gztralala, $tab);
                gzclose($gztralala);
                echo "<script language=javascript1.2>
                window.location='tempExcel/".$nop_.".xls.gz';
                </script>";
            }
         break;
		
default:
break;
}
      
?>