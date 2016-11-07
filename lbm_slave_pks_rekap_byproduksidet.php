<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

if(isset($_GET['proses'])!=''){
    $proses=$_GET['proses'];}
else{
    $proses=$_POST['proses'];}

$_GET['subbagian']==''?$kdOrg=$_POST['subbagian']:$kdOrg=$_GET['subbagian'];
$_GET['periode']==''?$periode=$_POST['periode']:$periode=$_GET['periode'];
$_GET['noakun']==''?$noakun=$_POST['noakun']:$noakun=$_GET['noakun'];
$_POST['judul']==''?$judul=$_GET['judul']:$judul=$_POST['judul'];
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
    $field="olah0".$thn[1];
    $fld="kgcpo0".$thn[1];
    $fld_st="rp0".$thn[1];
}
else{
    $field="olah".$thn[1];
    $fld="kgcpo".$thn[1];
    $fld_st="rp".$thn[1];
}
  
for($asr5=1;$asr5<=$thn[1];$asr5++){
        if(strlen($asr5)<2){
            if($asr5==1)
            {
                $field5="olah0".$asr5;
                $fld5="kgcpo0".$asr5;
                $fld_st5="rp0".$asr5;
            }
            else{
             $field5.="+olah0".$asr5;
             $fld5.="+kgcpo0".$asr5;
             $fld_st5.="+rp0".$asr5;
            }
        }
        else{
            $field5.="+olah".$asr5;
            $fld5.="+kgcpo".$asr5;
            $fld_st5.="+rp".$asr5;
        }
   
}###".$unit."
if(isset($_GET['proses'])!=''){
    $kdOrg=$noakun."###".$kdOrg;
}
$dtser=explode("###",$kdOrg);
$optNmBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optNmKeg=makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
 $bgcoloraja="bgcolor=#DEDEDE align=center";


// exit("error:".$_GET['proses']);
    switch($proses){
        case'getDetail': 
            #realisasi
            $sdgaji="select noakun,sum(jumlah) as realst from ".$dbname.".keu_jurnaldt_vw
            where noakun like '".$dtser[0]."%' and kodeorg='".$dtser[1]."' 
            and left(tanggal,7)='".$periode."'  
            group by noakun asc";
            $qdgaji=mysql_query($sdgaji) or die(mysql_error($conn));
            while($rdgaji=mysql_fetch_assoc($qdgaji)){
            $dtAkun[$rdgaji['noakun']]=$rdgaji['noakun'];
            $dtRealis[$rdgaji['noakun']]=$rdgaji['realst'];
            }
            #realisasi sbi
            $sdgajisbi="select noakun,sum(jumlah) as realst from ".$dbname.".keu_jurnaldt_vw
            where noakun like '".$dtser[0]."%'  and kodeorg='".$dtser[1]."' 
            and left(tanggal,7) between '".$thn[0]."-01' and '".$periode."'
            group by noakun asc";
            $qdgajisbi=mysql_query($sdgajisbi) or die(mysql_error($conn));
            while($rdgajisbi=mysql_fetch_assoc($qdgajisbi)){
            $dtAkun[$rdgajisbi['noakun']]=$rdgajisbi['noakun'];
            $dtRealissbi[$rdgajisbi['noakun']]=$rdgajisbi['realst'];
            }
            #budget
            # Budget Station BI//noakun like '".$dtser[0]."%' and left(kodeblok,6) like '".$dtser[1]."%' 
            $s_budstbi="select noakun,sum(rp".$bulan.") as budget_st from ".$dbname.".bgt_budget_detail
            where noakun like '".$dtser[0]."%' and  kodeorg like '".$dtser[1]."%' 
            and tahunbudget='".$thn[0]."' 
            group by noakun";
            $q_budstbi = mysql_query($s_budstbi) or die(mysql_error($conn));
            while($r_budstbi=mysql_fetch_assoc($q_budstbi))
            {
            $dtAkun[$r_budstbi['noakun']]=$r_budstbi['noakun'];
            $bi_budst[$r_budstbi['noakun']]=$r_budstbi['budget_st'];
            }

            # Budget Station SDBI
            $s_budstsdbi="select noakun,sum(".$fld_st5.") as bgt_st from ".$dbname.".bgt_budget_detail
              where noakun like '".$dtser[0]."%'  and kodeorg like '".$dtser[1]."%'   and tahunbudget='".$thn[0]."' 
              group by noakun";
            //echo $s_budstsdbi;
            $q_budstsdbi = mysql_query($s_budstsdbi) or die(mysql_error($conn));
            while($r_budstsdbi=mysql_fetch_assoc($q_budstsdbi)){
            $dtAkun[$r_budstsdbi['noakun']]=$r_budstsdbi['noakun'];
            $sdbi_budst[$r_budstsdbi['noakun']]=$r_budstsdbi['bgt_st'];
            }
            $optNmakun=makeOption($dbname, 'keu_5akun', 'noakun,namaakun');
            $brd=0;
            $tab.="<table cellpadding=1 cellspacing=1 border=".$brd." class=sortable>";
            $tab.="<thead><tr>";
            $tab.="<td rowspan=2>".$_SESSION['lang']['noakun']."</td>";
            $tab.="<td rowspan=2>".$_SESSION['lang']['namaakun']."</td>";
            $tab.="<td colspan=3>BI</td>";
            $tab.="<td colspan=3>S.D BI</td>";
            $tab.="</tr><tr>";
            $tab.="<td>".$_SESSION['lang']['realisasi']."</td>";
            $tab.="<td>".$_SESSION['lang']['budget']."</td>";
            $tab.="<td>".$_SESSION['lang']['selisih']."</td>";
            $tab.="<td>".$_SESSION['lang']['realisasi']."</td>";
            $tab.="<td>".$_SESSION['lang']['budget']."</td>
            <td>".$_SESSION['lang']['selisih']."</td>
            </tr></thead><tbody>";
            
            $totalReal=0;
            $totalBudget=0;
            $totalRealSi=0;
            $totalBudgetSbi=0;
            foreach($dtAkun as $lstNoakun){
                    //$derclick=" style=cursor:pointer; onclick=getDetail2('".$dtser[0]."###".$dtser[1]."','".$periode."','".$lstNoakun."','lbm_slave_pks_byperawatandetail')";
                    $tab.="<tr class=rowcontent ".$derclick.">";
                    $tab.="<td>".$lstNoakun."</td>";
                    $tab.="<td>".$optNmakun[$lstNoakun]."</td>";
                    $slisih[$lstNoakun]=$bi_budst[$lstNoakun]-$dtRealis[$lstNoakun];
                    $slisihSbi[$lstNoakun]=$sdbi_budst[$lstNoakun]-$dtRealissbi[$lstNoakun];
                    $tab.="<td align=right>".number_format($dtRealis[$lstNoakun],2)."</td>";
                    $tab.="<td align=right>".number_format($bi_budst[$lstNoakun],2)."</td>";
                    $tab.="<td align=right>".number_format($slisih[$lstNoakun],2)."</td>";
                    $tab.="<td align=right>".number_format($dtRealissbi[$lstNoakun],2)."</td>";
                    $tab.="<td align=right>".number_format($sdbi_budst[$lstNoakun],2)."</td>";
                    $tab.="<td align=right>".number_format($slisihSbi[$lstNoakun],2)."</td>";
                    $tab.="</tr>";
                    $totalReal+=$dtRealis[$lstNoakun];
                    $totalBudget+=$bi_budst[$lstNoakun];
                    $totalRealSi+=$dtRealissbi[$lstNoakun];
                    $totalBudgetSbi+=$sdbi_budst[$lstNoakun];
            }
            $tab.="<tr class=rowcontent>";
            $tab.="<td colspan=2>".$_SESSION['lang']['total']."</td>";

            $slisihbi=$totalBudget-$totalReal;
            $slisihSbiini=$totalBudgetSbi-$totalRealSi;
            $tab.="<td align=right>".number_format($totalReal,0)."</td>";
            $tab.="<td align=right>".number_format($totalBudget,0)."</td>";
            $tab.="<td align=right>".number_format($slisihbi,0)."</td>";
            $tab.="<td align=right>".number_format($totalRealSi,0)."</td>";
            $tab.="<td align=right>".number_format($totalBudgetSbi,0)."</td>";
            $tab.="<td align=right>".number_format($slisihSbiini,0)."</td>";
            $tab.="</tr>";
            $arr="##subbagian##periode";
            $tab.="</tbody></table>
                <input type=hidden id=subbagian value='".$dtser[0]."###".$dtser[1]."' />
                <input type=hidden id=periode value='".$periode."' />
                <button style=cursor:pointer; onclick=getBack1()  class=\"mybutton\">".$_SESSION['lang']['back']."</button>
                <button onclick=\"zExcl(event,'lbm_slave_pks_rekap_byproduksidet.php','".$dtser[0]."','".$dtser[1]."','".$periode."','getExccel')\" class=\"mybutton\" name=\"excel\" id=\"excel\">".$_SESSION['lang']['excel']."</button>";
         echo $tab;
        break;
        case'getExccel':
            #realisasi
            $sdgaji="select noakun,sum(jumlah) as realst from ".$dbname.".keu_jurnaldt_vw
            where noakun like '".$dtser[0]."%' and kodeorg='".$dtser[1]."' 
            and left(tanggal,7)='".$periode."'  
            group by noakun asc";
            //exit("error:".$sdgaji);
            $qdgaji=mysql_query($sdgaji) or die(mysql_error($conn));
            while($rdgaji=mysql_fetch_assoc($qdgaji)){
            $dtAkun[$rdgaji['noakun']]=$rdgaji['noakun'];
            $dtRealis[$rdgaji['noakun']]=$rdgaji['realst'];
            }
            #realisasi sbi
            $sdgajisbi="select noakun,sum(jumlah) as realst from ".$dbname.".keu_jurnaldt_vw
            where noakun like '".$dtser[0]."%'  and kodeorg='".$dtser[1]."' 
            and left(tanggal,7) between '".$thn[0]."-01' and '".$periode."'
            group by noakun asc";
            $qdgajisbi=mysql_query($sdgajisbi) or die(mysql_error($conn));
            while($rdgajisbi=mysql_fetch_assoc($qdgajisbi)){
            $dtAkun[$rdgajisbi['noakun']]=$rdgajisbi['noakun'];
            $dtRealissbi[$rdgajisbi['noakun']]=$rdgajisbi['realst'];
            }
            #budget
            # Budget Station BI//noakun like '".$dtser[0]."%' and left(kodeblok,6) like '".$dtser[1]."%' 
            $s_budstbi="select noakun,sum(rp".$bulan.") as budget_st from ".$dbname.".bgt_budget_detail
            where noakun like '".$dtser[0]."%' and  kodeorg like '".$dtser[1]."%' 
            and tahunbudget='".$thn[0]."' 
            group by noakun";
            $q_budstbi = mysql_query($s_budstbi) or die(mysql_error($conn));
            while($r_budstbi=mysql_fetch_assoc($q_budstbi))
            {
            $dtAkun[$r_budstbi['noakun']]=$r_budstbi['noakun'];
            $bi_budst[$r_budstbi['noakun']]=$r_budstbi['budget_st'];
            }

            # Budget Station SDBI
            $s_budstsdbi="select noakun,sum(".$fld_st5.") as bgt_st from ".$dbname.".bgt_budget_detail
              where noakun like '".$dtser[0]."%'  and kodeorg like '".$dtser[1]."%'   and tahunbudget='".$thn[0]."' 
              group by noakun";
            //echo $s_budstsdbi;
            $q_budstsdbi = mysql_query($s_budstsdbi) or die(mysql_error($conn));
            while($r_budstsdbi=mysql_fetch_assoc($q_budstsdbi)){
            $dtAkun[$r_budstsdbi['noakun']]=$r_budstsdbi['noakun'];
            $sdbi_budst[$r_budstsdbi['noakun']]=$r_budstsdbi['bgt_st'];
            }
            $optNmakun=makeOption($dbname, 'keu_5akun', 'noakun,namaakun');
            $brd=1;
            $tab.="<table cellpadding=1 cellspacing=1 border=".$brd." class=sortable>";
            $tab.="<thead><tr ".$bgcoloraja.">";
            $tab.="<td rowspan=2>".$_SESSION['lang']['noakun']."</td>";
            $tab.="<td rowspan=2>".$_SESSION['lang']['namaakun']."</td>";
            $tab.="<td colspan=3>BI</td>";
            $tab.="<td colspan=3>S.D BI</td>";
            $tab.="</tr><tr  ".$bgcoloraja.">";
            $tab.="<td>".$_SESSION['lang']['realisasi']."</td>";
            $tab.="<td>".$_SESSION['lang']['budget']."</td>";
            $tab.="<td>".$_SESSION['lang']['selisih']."</td>";
            $tab.="<td>".$_SESSION['lang']['realisasi']."</td>";
            $tab.="<td>".$_SESSION['lang']['budget']."</td>
            <td>".$_SESSION['lang']['selisih']."</td>
            </tr></thead><tbody>";
            
            $totalReal=0;
            $totalBudget=0;
            $totalRealSi=0;
            $totalBudgetSbi=0;
            foreach($dtAkun as $lstNoakun){
                    //$derclick=" style=cursor:pointer; onclick=getDetail2('".$kdOrg."','".$periode."','".$lstNoakun."','lbm_slave_pks_byperawatandetail')";
                    $tab.="<tr class=rowcontent ".$derclick.">";
                    $tab.="<td>".$lstNoakun."</td>";
                    $tab.="<td>".$optNmakun[$lstNoakun]."</td>";
                    $slisih[$lstNoakun]=$bi_budst[$lstNoakun]-$dtRealis[$lstNoakun];
                    $slisihSbi[$lstNoakun]=$sdbi_budst[$lstNoakun]-$dtRealissbi[$lstNoakun];
                    $tab.="<td align=right>".number_format($dtRealis[$lstNoakun],2)."</td>";
                    $tab.="<td align=right>".number_format($bi_budst[$lstNoakun],2)."</td>";
                    $tab.="<td align=right>".number_format($slisih[$lstNoakun],2)."</td>";
                    $tab.="<td align=right>".number_format($dtRealissbi[$lstNoakun],2)."</td>";
                    $tab.="<td align=right>".number_format($sdbi_budst[$lstNoakun],2)."</td>";
                    $tab.="<td align=right>".number_format($slisihSbi[$lstNoakun],2)."</td>";
                    $tab.="</tr>";
                    $totalReal+=$dtRealis[$lstNoakun];
                    $totalBudget+=$bi_budst[$lstNoakun];
                    $totalRealSi+=$dtRealissbi[$lstNoakun];
                    $totalBudgetSbi+=$sdbi_budst[$lstNoakun];
            }
            $tab.="<tr class=rowcontent>";
            $tab.="<td colspan=2>".$_SESSION['lang']['total']."</td>";

            $slisihbi=$totalBudget-$totalReal;
            $slisihSbiini=$totalBudgetSbi-$totalRealSi;
            $tab.="<td align=right>".number_format($totalReal,0)."</td>";
            $tab.="<td align=right>".number_format($totalBudget,0)."</td>";
            $tab.="<td align=right>".number_format($slisihbi,0)."</td>";
            $tab.="<td align=right>".number_format($totalRealSi,0)."</td>";
            $tab.="<td align=right>".number_format($totalBudgetSbi,0)."</td>";
            $tab.="<td align=right>".number_format($slisihSbiini,0)."</td>";
            $tab.="</tr>";
            $tab.="</tbody></table>";
        $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
        $dte=date("Hms");
        $nop_="rekapbyproduksi_".$dte;
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
//           $sreal="select distinct sum(jumlah) as rupiah,kodebarang,kodekegiatan,keterangan,nojurnal
//                    from ".$dbname.".keu_jurnaldt_vw where  left(tanggal,7) between '".$thn[0]."-01' and '".$periode."'
//                    and noakun='".$noakun."' and kodeblok like '".$kdOrg."%' and char_length(kodebarang)='10'
//                     group by nojurnal";
//            $qReal=mysql_query($sreal) or die(mysql_error($conn));
//            while($rreal=  mysql_fetch_assoc($qReal)){
//                $dtNojurnal[$rreal['nojurnal']]=$rreal['nojurnal'];
//                $dtKegiatan[$rreal['nojurnal']]=$rreal['kodekegiatan'];
//                $dtBrg[$rreal['nojurnal']]=$rreal['kodebarang'];
//                $dtRupiah[$rreal['nojurnal']]=$rreal['rupiah'];
//                $dtKet[$rreal['nojurnal']]=$rreal['keterangan'];
//            }
//         
//            $sbgt="select distinct kodebarang,kegiatan,sum(rp".$bulan.") as budget_st
//                    from ".$dbname.".bgt_budget_detail where tahunbudget='".$thn[0]."'
//                    and noakun='".$noakun."' and kodeorg like '".$kdOrg."%' and char_length(kodebarang)='10'
//                     group by kodebarang";
//            //echo $sbgt;
//            $qBgt=mysql_query($sbgt) or die(mysql_error($conn));
//            while($rBgt=  mysql_fetch_assoc($qBgt)){
//                $dtRupiahBgt[$rBgt['kodebarang']]=$rBgt['budget_st'];
//            }   
//            $sbgt="select distinct kodebarang,sum(".$fld_st5.") as bgt_st
//                    from ".$dbname.".bgt_budget_detail where tahunbudget='".$thn[0]."'
//                    and noakun='".$noakun."' and kodeorg like '".$kdOrg."%' and char_length(kodebarang)='10'
//                     group by kodebarang";
//            $qBgt=mysql_query($sbgt) or die(mysql_error($conn));
//            while($rBgt=  mysql_fetch_assoc($qBgt)){
//                $dtRupiahBgtSi[$rBgt['kodebarang']]=$rBgt['bgt_st'];
//            }   
            $brd=0;
            $tab.="<table cellpadding=1 cellspacing=1 border=".$brd." class=sortable>";
            $tab.="<thead><tr>";
            $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['nojurnal']."</td>";
            $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['keterangan']."</td>";
            $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['kodebarang']."</td>";
            $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['namabarang']."</td>";
            $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['kodekegiatan']."</td>";
            $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['namakegiatan']."</td>";
            $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['rp']." BI</td>";
//            $tab.="<td colspan=3>BI</td>";
//            $tab.="<td colspan=3>S.D BI</td>";
//            $tab.="</tr><tr>";
//            $tab.="<td>".$_SESSION['lang']['realisasi']."</td>";
//            $tab.="<td>".$_SESSION['lang']['budget']."</td>";
//            $tab.="<td>".$_SESSION['lang']['selisih']."</td>";
//            $tab.="<td>".$_SESSION['lang']['realisasi']."</td>";
//            $tab.="<td>".$_SESSION['lang']['budget']."</td>
//            <td>".$_SESSION['lang']['selisih']."</td>
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
                    <button style=cursor:pointer; onclick=getBack2()  class=\"mybutton\">".$_SESSION['lang']['back']."</button>
                    <button onclick=\"zExcel(event,'lbm_slave_pks_byperawatandetail.php','".$arr."','getExccel2')\" class=\"mybutton\" name=\"excel\" id=\"excel\">".$_SESSION['lang']['excel']."</button>";
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
        $nop_="BiayaPerawatanDetail2".$dte;
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
    }
      
?>



