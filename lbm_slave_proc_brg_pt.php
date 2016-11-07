<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
$_POST['idPt']==''?$idPt=$_GET['idPt']:$idPt=$_POST['idPt'];
$_POST['klmpkBrg']==''?$klmpkBrg=$_GET['klmpkBrg']:$klmpkBrg=$_POST['klmpkBrg'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['judul']==''?$judul=$_GET['judul']:$judul=$_POST['judul'];
$qwe=explode('-',$periode); $tahun=$qwe[0]; $bulan=$qwe[1];
$_POST['pt']==''?$pt=$_GET['pt']:$pt=$_POST['pt'];
$_POST['periodeDt']==''?$periodeDt=$_GET['periodeDt']:$periodeDt=$_POST['periodeDt'];
if($pt!='')
{
  $idPt=$pt;  
}
$dtNmBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$dtSat=makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');
$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNmKlmpk=makeOption($dbname, 'log_5klbarang', 'kode,kelompok');
if($periode=='')
{
    exit("Error:Field Tidak Boleh Kosong");
}
$arr="##periode##judul"; 
$optBulan['01']=$_SESSION['lang']['jan'];
$optBulan['02']=$_SESSION['lang']['peb'];
$optBulan['03']=$_SESSION['lang']['mar'];
$optBulan['04']=$_SESSION['lang']['apr'];
$optBulan['05']=$_SESSION['lang']['mei'];
$optBulan['06']=$_SESSION['lang']['jun'];
$optBulan['07']=$_SESSION['lang']['jul'];
$optBulan['08']=$_SESSION['lang']['agt'];
$optBulan['09']=$_SESSION['lang']['sep'];
$optBulan['10']=$_SESSION['lang']['okt'];
$optBulan['11']=$_SESSION['lang']['nov'];
$optBulan['12']=$_SESSION['lang']['dec'];

 
$sData="select distinct sum(jumlahpesan*hargasatuan*kurs) as total,substr(tanggal,6,2) as bulan,
        substr(kodebarang,1,3) as klmpkBrg,sum(jumlahpesan) as jumlah from ".$dbname.".log_po_vw where kodeorg='".$idPt."'
        and substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."'
        group by substr(tanggal,6,2),substr(kodebarang,1,3) order by substr(kodebarang,1,3) asc";
$qData=mysql_query($sData) or die(mysql_error($conn));
while($rData=mysql_fetch_assoc($qData))
{
    $dtBarang[$rData['klmpkBrg']]=$rData['klmpkBrg'];
    $dtHarga[$rData['klmpkBrg'].$rData['bulan']]=$rData['total'];
    $dtJumlah[$rData['klmpkBrg'].$rData['bulan']]=$rData['jumlah'];
    $dtPeriode[$rData['bulan']]=$rData['bulan'];
}
$cekDt=count($dtBarang);
if($cekDt==0)
{
    exit("Error:Data Kosong");
}
$sKd="select kodeorganisasi from ".$dbname.".organisasi where induk='".$idPt."'";
$qKd=mysql_query($sKd) or die(mysql_error($conn));
while($rKd=mysql_fetch_assoc($qKd))
{
    $aro+=1;
    if($aro==1)
    {
        $kodear="'".$rKd['kodeorganisasi']."'";
    }
    else
    {
        $kodear.=",'".$rKd['kodeorganisasi']."'";
    }
}
for($W=1;$W<=intval($bulan);$W++)
{
    if($W<10)$jack="sum(rp0".$W.") as rp0".$W.",sum(fis0".$W.") as fis0".$W.",";
    else $jack=",sum(rp".$W.") as rp".$W.",sum(fis".$W.") as fis".$W.",";
    if($W<intval($bulan))$addstr.=$jack;
    else $addstr.=$jack;
}
 
$sBudget="select distinct ".$addstr."substr(kodebarang,1,3) as klmpkBrg from 
          ".$dbname.".bgt_budget_detail where substr(kodeorg,1,4) in (".$kodear.")
          and substr(kodebudget,1,1)='M' and tahunbudget='".$tahun."'
          group by substr(kodebarang,1,3)";

$qBudget=mysql_query($sBudget)or die(mysql_error($conn));
while($rBudget=mysql_fetch_assoc($qBudget))
{
    $dtBarang[$rBudget['klmpkBrg']]=$rBudget['klmpkBrg'];
    for($W=1;$W<=intval($bulan);$W++)
    {
        if($W<10)
        {
            $adr="0".$W;
        }
        $dtRupBgt[$rBudget['klmpkBrg'].$adr]+=$rBudget['rp'.$adr];
        $dtFisBgt[$rBudget['klmpkBrg'].$adr]+=$rBudget['fis'.$adr];
    }
}

$bg="";
$brdr=0;
if($proses=='excel')
{
$bg=" bgcolor=#DEDEDE";
$brdr=1;
$tab.="<table border=0>
     <tr>
        <td colspan=4 align=left><font size=3>".$judul."</font></td>
        <td colspan=3 align=right>".$_SESSION['lang']['bulan']." : ".$optBulan[$bulan]." ".$tahun."</td>
     </tr>    
</table>";
}

if($proses!='excel')$tab.=$judul;
    $tab.="<table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable style='width:100%;'>
    <thead class=rowheader>
    <tr>
    <td align=center rowspan=3 ".$bg.">No.</td>
    <td align=center rowspan=3 ".$bg.">".$_SESSION['lang']['kelompokbarang']."</td>";
    for($W=1;$W<=intval($bulan);$W++)
    {    
        if($W<10)
        {
            $adr="0".$W;
        }
        $tab.="<td align=center  colspan=2 ".$bg.">".$optBulan[$adr]." (Rp.)</td>";
    }
    $tab.="</tr><tr>";
    for($W=1;$W<=intval($bulan);$W++)
    {
        $tab.="<td align=center ".$bg.">".$_SESSION['lang']['realisasi']."</td>";
        $tab.="<td align=center ".$bg.">".$_SESSION['lang']['anggaran']."</td>";
    }
    $tab.="</tr>";
    $tab.="</thead><tbody>";
    foreach($dtBarang as $lstKlmpk)
    {
        $arto+=1;
        $tab.="<tr class=rowcontent>";
        $tab.="<td>".$arto."</td>";
        $tab.="<td>".$optNmKlmpk[$lstKlmpk]."</td>";
        for($W=1;$W<=intval($bulan);$W++)
        {
            if($W<10)
            {
                $adr="0".$W;
            }
            $tab.="<td align=right ".$bg." style=cursor:pointer onclick=getDetPt('lbm_slave_proc_brg_pt','".$lstKlmpk."','".$tahun."-".$adr."','".$idPt."','".$periode."')>".number_format($dtHarga[$lstKlmpk.$adr],0)."</td>";
            $tab.="<td align=right ".$bg." style=cursor:pointer onclick=getDetPt('lbm_slave_proc_brg_pt','".$lstKlmpk."','".$tahun."-".$adr."','".$idPt."','".$periode."'>".number_format($dtRupBgt[$lstKlmpk.$adr],0)."</td>";
        }
        $tab.="</tr>";
    }
    $tab.="</tbody></table>";
		
switch($proses)
{
    case'preview':
    //    exit("error:masuk");
    if($periode=='')
    {
        exit("Error:Field Tidak Boleh Kosong");
    }
    echo $tab;
    break;

    case'excel':
    if($periode=='')
    {
        exit("Error:Field Tidak Boleh Kosong");
    }

    $tab.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
    $dte=date("His");
    $nop_="totalPembelian_pt_".$dte;
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
    case'getDetail':
    $lstMatauang2="";
    $drot=explode("-",$periodeDt);
    $bln=$drot[1];
    for($W=1;$W<=intval($bln);$W++)
    {
        if($W<10)$jack="sum(rp0".$W.") as rp0".$W.",sum(fis0".$W.") as fis0".$W.",";
        else $jack=",sum(rp".$W.") as rp".$W.",sum(fis".$W.") as fis".$W.",";
        if($W<intval($bln))$addstr.=$jack;
        else $addstr.=$jack;
    }
    for($jrt=1;$jrt<=intval($bulan);$jrt++)
    {
        if($jrt<10)$jack="sum(rp0".$jrt.") as rp0".$jrt.",sum(fis0".$jrt.") as fis0".$jrt.",";
        else $jack=",sum(rp".$jrt.") as rp".$jrt.",sum(fis".$jrt.") as fis".$jrt.",";
        if($jrt<intval($bulan))$addstr2.=$jack;
        else $addstr2.=$jack;
    }
    

    $judul="Detail Total Pembelian Barang per PT";
    //bulan ini realisasi
    $sData="select distinct sum(hargasatuan*jumlahpesan*kurs) as total,kodebarang,sum(jumlahpesan) as jumlah,substr(nopp,16,4) as unit,
            namabarang,satuan from ".$dbname.".log_po_vw where substr(kodebarang,1,3)='".$klmpkBrg."' and kodeorg='".$idPt."'
            and substr(tanggal,1,7)='".$periodeDt."' group by substr(nopp,16,4),kodebarang";
      // exit("Error:".$sData);
    $qData=mysql_query($sData) or die(mysql_error($conn));
    while($rData=mysql_fetch_assoc($qData))
    {
        $dtUnit[$rData['unit']]=$rData['unit'];
        $dtRp[$rData['kodebarang'].$rData['unit']]=$rData['total'];
        $dtJuml[$rData['kodebarang'].$rData['unit']]=$rData['jumlah'];
        $dtBrg[$rData['kodebarang']]=$rData['kodebarang'];   
    }
    //s.d. bulan ini realisasi
     $sData="select distinct sum(hargasatuan*jumlahpesan*kurs) as total,kodebarang,sum(jumlahpesan) as jumlah,substr(nopp,16,4) as unit,
            namabarang,satuan from ".$dbname.".log_po_vw where substr(kodebarang,1,3)='".$klmpkBrg."' and kodeorg='".$idPt."'
            and substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."' group by substr(nopp,16,4),kodebarang";
      // exit("Error:".$sData);
    $qData=mysql_query($sData) or die(mysql_error($conn));
    while($rData=mysql_fetch_assoc($qData))
    {
        $dtUnitSmp[$rData['unit']]=$rData['unit'];
        $dtRpSblnSmp[$rData['kodebarang'].$rData['unit']]=$rData['total'];
        $dtJumlSmp[$rData['kodebarang'].$rData['unit']]=$rData['jumlah'];
        $dtBrg[$rData['kodebarang']]=$rData['kodebarang'];   
        
    }
    //budget bulan ini
    foreach($dtUnit as $lstUnit)
    {
     $sBudget="select distinct ".$addstr."kodebarang,satuanj,kodeorg from 
                  ".$dbname.".bgt_budget_detail where substr(kodeorg,1,4)='".$lstUnit."'
                  and substr(kodebudget,1,1)='M' and tahunbudget='".$tahun."'
                  group by kodebarang";
        //echo $sBudget."___".$bulan;
        $qBudget=mysql_query($sBudget)or die(mysql_error($conn));
        while($rBudget=mysql_fetch_assoc($qBudget))
        {
            $dtBrg[$rBudget['kodebarang']]=$rBudget['kodebarang'];
            $dtSatuanBgt[$rBudget['kodebarang'].$lstUnit]=$rBudget['satuanj'];
            for($W=1;$W<=intval($bln);$W++)
            {
                if($W<10)
                {
                    $adr="0".$W;
                }
                $dtRupBgt[$rBudget['kodebarang'].$lstUnit]+=$rBudget['rp'.$adr];
                $dtFisBgt[$rBudget['kodebarang'].$lstUnit]+=$rBudget['fis'.$adr];
            }
        }
    }
    //budget s.d bulan ini
    foreach($dtUnit as $lstUnit)
    {
     $sBudget="select distinct ".$addstr2."kodebarang,satuanj,kodeorg from 
                  ".$dbname.".bgt_budget_detail where substr(kodeorg,1,4)='".$lstUnit."'
                  and substr(kodebudget,1,1)='M' and tahunbudget='".$tahun."'
                  group by kodebarang";
        //echo $sBudget."___".$bulan;
        $qBudget=mysql_query($sBudget)or die(mysql_error($conn));
        while($rBudget=mysql_fetch_assoc($qBudget))
        {
            $dtBrg[$rBudget['kodebarang']]=$rBudget['kodebarang'];
            $dtSatuanBgt[$rBudget['kodebarang'].$lstUnit]=$rBudget['satuanj'];
            for($W=1;$W<=intval($bln);$W++)
            {
                if($W<10)
                {
                    $adr="0".$W;
                }
                $dtRupBgt[$rBudget['kodebarang'].$lstUnit]+=$rBudget['rp'.$adr];
                $dtFisBgt[$rBudget['kodebarang'].$lstUnit]+=$rBudget['fis'.$adr];
            }
        }
    }
   $dcol=(count($dtUnit)*6)+2;
   $cold=3*2;
    $tabc="<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>";
    $tabc.="<tr><td rowspan=4  bgcolor=#DEDEDE>No.</td><td rowspan=4  bgcolor=#DEDEDE>".$_SESSION['lang']['namabarang']."</td>";
    foreach($dtUnit as $lstUnit)
    {
        $tabc.="<td colspan=".$cold.">".$lstUnit."</td>";
    }
    $tabc.="</tr><tr>";
    foreach($dtUnit as $lstUnit)
    {
        $tabc.="<td bgcolor=#DEDEDE colspan=3>".$periodeDt."</td>";
        $tabc.="<td bgcolor=#DEDEDE colspan=3>".$periode."</td>";
    }
    $tabc.="</tr><tr>";
    foreach($dtUnit as $lstUnit)
    {
        $tabc.="<td bgcolor=#DEDEDE colspan=3>".$_SESSION['lang']['realisasi']."</td>";
        $tabc.="<td bgcolor=#DEDEDE colspan=3>".$_SESSION['lang']['anggaran']."</td>";
    }
    $tabc.="</tr><tr>";
    foreach($dtUnit as $lstUnit)
    {
        $tabc.="<td bgcolor=#DEDEDE>".$_SESSION['lang']['fisik']."</td>";
        $tabc.="<td bgcolor=#DEDEDE>".$_SESSION['lang']['satuan']."</td>";
        $tabc.="<td bgcolor=#DEDEDE>".$_SESSION['lang']['rp']."</td>";
        $tabc.="<td bgcolor=#DEDEDE>".$_SESSION['lang']['fisik']."</td>";
        $tabc.="<td bgcolor=#DEDEDE>".$_SESSION['lang']['satuan']."</td>";
        $tabc.="<td bgcolor=#DEDEDE>".$_SESSION['lang']['rp']."</td>";
    }
    $tabc.="</tr><tbody>";
   
    foreach($dtBrg as $lstbarang)
    {
        $artp+=1;
        $tabc.="<tr class=rowcontent><td>".$artp."</td>";
        $tabc.="<td>".$dtNmBrg[$lstbarang]."</td>";
        foreach($dtUnit as $lstUnit)
        {
            $tabc.="<td align=right>".number_format($dtJuml[$lstbarang.$lstUnit],0)."</td>";
            $tabc.="<td align=left>".$dtSat[$lstbarang]."</td>";
            $tabc.="<td align=right>".number_format($dtRp[$lstbarang.$lstUnit],0)."</td>";
            $tabc.="<td align=right>".number_format($dtFisBgt[$lstbarang.$lstUnit],0)."</td>";
            $tabc.="<td align=left>".$dtSat[$lstbarang]."</td>";
            $tabc.="<td align=right>".number_format($dtRupBgt[$lstbarang.$lstUnit],0)."</td>";
        }
        $tabc.="</tr>";
    }
    $tabc.="<tr><td colspan=$dcol>";
    $tabc.="<button class=mybutton onclick=zBack()>Back</button>";
        $tabc.="<button onclick=\"zExcel3(event,'lbm_slave_proc_brg_pt.php','".$idPt."','".$klmpkBrg."','".$periode."','".$periodeDt."','reportcontainer')\" class=\"mybutton\" name=\"excel\" id=\"excel\">
               ".$_SESSION['lang']['excel']."</button></td></tr> ";
    $tabc.="</tbody></table>";
    echo $tabc."###".$judul;
    break;
    case'getDetPtEx':
    $lstMatauang2="";
    $drot=explode("-",$periodeDt);
    $bln=$drot[1];
    for($W=1;$W<=intval($bln);$W++)
    {
        if($W<10)$jack="sum(rp0".$W.") as rp0".$W.",sum(fis0".$W.") as fis0".$W.",";
        else $jack=",sum(rp".$W.") as rp".$W.",sum(fis".$W.") as fis".$W.",";
        if($W<intval($bln))$addstr.=$jack;
        else $addstr.=$jack;
    }
    for($jrt=1;$jrt<=intval($bulan);$jrt++)
    {
        if($jrt<10)$jack="sum(rp0".$jrt.") as rp0".$jrt.",sum(fis0".$jrt.") as fis0".$jrt.",";
        else $jack=",sum(rp".$jrt.") as rp".$jrt.",sum(fis".$jrt.") as fis".$jrt.",";
        if($jrt<intval($bulan))$addstr2.=$jack;
        else $addstr2.=$jack;
    }
    

    $judul="Detail Total Pembelian Barang per PT";
    //bulan ini realisasi
    $sData="select distinct sum(hargasatuan*jumlahpesan*kurs) as total,kodebarang,sum(jumlahpesan) as jumlah,substr(nopp,16,4) as unit,
            namabarang,satuan from ".$dbname.".log_po_vw where substr(kodebarang,1,3)='".$klmpkBrg."' and kodeorg='".$idPt."'
            and substr(tanggal,1,7)='".$periodeDt."' group by substr(nopp,16,4),kodebarang";
      // exit("Error:".$sData);
    $qData=mysql_query($sData) or die(mysql_error($conn));
    while($rData=mysql_fetch_assoc($qData))
    {
        $dtUnit[$rData['unit']]=$rData['unit'];
        $dtRp[$rData['kodebarang'].$rData['unit']]=$rData['total'];
        $dtJuml[$rData['kodebarang'].$rData['unit']]=$rData['jumlah'];
        $dtBrg[$rData['kodebarang']]=$rData['kodebarang'];   
    }
    //s.d. bulan ini realisasi
     $sData="select distinct sum(hargasatuan*jumlahpesan*kurs) as total,kodebarang,sum(jumlahpesan) as jumlah,substr(nopp,16,4) as unit,
            namabarang,satuan from ".$dbname.".log_po_vw where substr(kodebarang,1,3)='".$klmpkBrg."' and kodeorg='".$idPt."'
            and substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."' group by substr(nopp,16,4),kodebarang";
      // exit("Error:".$sData);
    $qData=mysql_query($sData) or die(mysql_error($conn));
    while($rData=mysql_fetch_assoc($qData))
    {
        $dtUnitSmp[$rData['unit']]=$rData['unit'];
        $dtRpSblnSmp[$rData['kodebarang'].$rData['unit']]=$rData['total'];
        $dtJumlSmp[$rData['kodebarang'].$rData['unit']]=$rData['jumlah'];
        $dtBrg[$rData['kodebarang']]=$rData['kodebarang'];   
        
    }
    //budget bulan ini
    foreach($dtUnit as $lstUnit)
    {
     $sBudget="select distinct ".$addstr."kodebarang,satuanj,kodeorg from 
                  ".$dbname.".bgt_budget_detail where substr(kodeorg,1,4)='".$lstUnit."'
                  and substr(kodebudget,1,1)='M' and tahunbudget='".$tahun."'
                  group by kodebarang";
        //echo $sBudget."___".$bulan;
        $qBudget=mysql_query($sBudget)or die(mysql_error($conn));
        while($rBudget=mysql_fetch_assoc($qBudget))
        {
            $dtBrg[$rBudget['kodebarang']]=$rBudget['kodebarang'];
            $dtSatuanBgt[$rBudget['kodebarang'].$lstUnit]=$rBudget['satuanj'];
            for($W=1;$W<=intval($bln);$W++)
            {
                if($W<10)
                {
                    $adr="0".$W;
                }
                $dtRupBgt[$rBudget['kodebarang'].$lstUnit]+=$rBudget['rp'.$adr];
                $dtFisBgt[$rBudget['kodebarang'].$lstUnit]+=$rBudget['fis'.$adr];
            }
        }
    }
    //budget s.d bulan ini
    foreach($dtUnit as $lstUnit)
    {
     $sBudget="select distinct ".$addstr2."kodebarang,satuanj,kodeorg from 
                  ".$dbname.".bgt_budget_detail where substr(kodeorg,1,4)='".$lstUnit."'
                  and substr(kodebudget,1,1)='M' and tahunbudget='".$tahun."'
                  group by kodebarang";
        //echo $sBudget."___".$bulan;
        $qBudget=mysql_query($sBudget)or die(mysql_error($conn));
        while($rBudget=mysql_fetch_assoc($qBudget))
        {
            $dtBrg[$rBudget['kodebarang']]=$rBudget['kodebarang'];
            $dtSatuanBgt[$rBudget['kodebarang'].$lstUnit]=$rBudget['satuanj'];
            for($W=1;$W<=intval($bln);$W++)
            {
                if($W<10)
                {
                    $adr="0".$W;
                }
                $dtRupBgt[$rBudget['kodebarang'].$lstUnit]+=$rBudget['rp'.$adr];
                $dtFisBgt[$rBudget['kodebarang'].$lstUnit]+=$rBudget['fis'.$adr];
            }
        }
    }
   $dcol=(count($dtUnit)*6)+2;
   $cold=3*2;
    $tabc="<table cellpadding=1 cellspacing=1 border=1 class=sortable><thead>";
    $tabc.="<tr><td rowspan=4  bgcolor=#DEDEDE>No.</td><td rowspan=4  bgcolor=#DEDEDE>".$_SESSION['lang']['namabarang']."</td>";
    foreach($dtUnit as $lstUnit)
    {
        $tabc.="<td colspan=".$cold."  bgcolor=#DEDEDE align=center>".$lstUnit."</td>";
    }
    $tabc.="</tr><tr>";
    foreach($dtUnit as $lstUnit)
    {
        $tabc.="<td bgcolor=#DEDEDE colspan=3>".$periodeDt."</td>";
        $tabc.="<td bgcolor=#DEDEDE colspan=3>".$periode."</td>";
    }
    $tabc.="</tr><tr>";
    foreach($dtUnit as $lstUnit)
    {
        $tabc.="<td bgcolor=#DEDEDE colspan=3>".$_SESSION['lang']['realisasi']."</td>";
        $tabc.="<td bgcolor=#DEDEDE colspan=3>".$_SESSION['lang']['anggaran']."</td>";
    }
    $tabc.="</tr><tr>";
    foreach($dtUnit as $lstUnit)
    {
        $tabc.="<td bgcolor=#DEDEDE>".$_SESSION['lang']['fisik']."</td>";
        $tabc.="<td bgcolor=#DEDEDE>".$_SESSION['lang']['satuan']."</td>";
        $tabc.="<td bgcolor=#DEDEDE>".$_SESSION['lang']['rp']."</td>";
        $tabc.="<td bgcolor=#DEDEDE>".$_SESSION['lang']['fisik']."</td>";
        $tabc.="<td bgcolor=#DEDEDE>".$_SESSION['lang']['satuan']."</td>";
        $tabc.="<td bgcolor=#DEDEDE>".$_SESSION['lang']['rp']."</td>";
    }
    $tabc.="</tr><tbody>";
   
    foreach($dtBrg as $lstbarang)
    {
        $artp+=1;
        $tabc.="<tr class=rowcontent><td>".$artp."</td>";
        $tabc.="<td>".$dtNmBrg[$lstbarang]."</td>";
        foreach($dtUnit as $lstUnit)
        {
            $tabc.="<td align=right>".number_format($dtJuml[$lstbarang.$lstUnit],0)."</td>";
            $tabc.="<td align=left>".$dtSat[$lstbarang]."</td>";
            $tabc.="<td align=right>".number_format($dtRp[$lstbarang.$lstUnit],0)."</td>";
            $tabc.="<td align=right>".number_format($dtFisBgt[$lstbarang.$lstUnit],0)."</td>";
            $tabc.="<td align=left>".$dtSat[$lstbarang]."</td>";
            $tabc.="<td align=right>".number_format($dtRupBgt[$lstbarang.$lstUnit],0)."</td>";
        }
        $tabc.="</tr>";
    }

    $tabc.="</tbody></table>";
    $tabc.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
    $dte=date("His");
    $nop_="dettotalPembelian_pt_".$dte;
    if(strlen($tabc)>0)
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
        if(!fwrite($handle,$tabc))
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
