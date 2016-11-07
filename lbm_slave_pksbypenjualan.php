<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$_POST['unit']==''?$unit=$_GET['unit']:$unit=$_POST['unit'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['judul']==''?$judul=$_GET['judul']:$judul=$_POST['judul'];

$qwe=explode('-',$periode); $tahun=$qwe[0]; $bulan=$qwe[1];

$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');

if($unit==''||$periode=='')
{
    exit("Error:Field Tidak Boleh Kosong");
}

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

// building array: dzArr (main data) =========================================================================
// as seen on sdm_slave_2prasarana.php
$dzArr=array();

// tbs diolah bulan ini
$aresta="SELECT sum(tbsdiolah) as tbs FROM ".$dbname.".pabrik_produksi
    WHERE kodeorg like '".$unit."%' and tanggal like '".$periode."%'";
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $tbs=$res['tbs'];
}   

// tbs diolah bulan ini budget
$aresta="SELECT sum(olah".$bulan.") as tbsbudget FROM ".$dbname.".bgt_produksi_pks_vw
    WHERE millcode like '".$unit."%' and tahunbudget = '".$tahun."'";
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $tbsbudget=$res['tbsbudget'];
}

$tbsselisih=$tbsbudget-$tbs;

// tbs diolah sd bulan ini
$aresta="SELECT sum(tbsdiolah) as tbs FROM ".$dbname.".pabrik_produksi
    WHERE kodeorg like '".$unit."%' and tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')";
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $tbssd=$res['tbs'];
}

$addstr="(";
for($W=1;$W<=intval($bulan);$W++)
{
    if($W<10)$jack="olah0".$W;
    else $jack="olah".$W;
    if($W<intval($bulan))$addstr.=$jack."+";
    else $addstr.=$jack;
}
$addstr.=")";

// tbs diolah sd bulan ini budget
$aresta="SELECT sum(".$addstr.") as tbsbudget FROM ".$dbname.".bgt_produksi_pks_vw
    WHERE millcode like '".$unit."%' and tahunbudget = '".$tahun."'";
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $tbsbudgetsd=$res['tbsbudget'];
}

$tbsselisihsd=$tbsbudgetsd-$tbssd;

// cpo dihasilkan bulan ini
$aresta="SELECT sum(oer) as cpo FROM ".$dbname.".pabrik_produksi
    WHERE kodeorg like '".$unit."%' and tanggal like '".$periode."%'";
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $cpo=$res['cpo'];
}   

// cpo dihasilkan bulan ini budget
$aresta="SELECT sum(kgcpo".$bulan.") as cpobudget FROM ".$dbname.".bgt_produksi_pks_vw
    WHERE millcode like '".$unit."%' and tahunbudget = '".$tahun."'";
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $cpobudget=$res['cpobudget'];
}

$cposelisih=$cpobudget-$cpo;

// cpo dihasilkan sd bulan ini
$aresta="SELECT sum(oer) as cpo FROM ".$dbname.".pabrik_produksi
    WHERE kodeorg like '".$unit."%' and tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')";
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $cposd=$res['cpo'];
}

$addstr="(";
for($W=1;$W<=intval($bulan);$W++)
{
    if($W<10)$jack="kgcpo0".$W;
    else $jack="kgcpo".$W;
    if($W<intval($bulan))$addstr.=$jack."+";
    else $addstr.=$jack;
}
$addstr.=")";

// cpo dihasilkan sd bulan ini budget
$aresta="SELECT sum(".$addstr.") as cpobudget FROM ".$dbname.".bgt_produksi_pks_vw
    WHERE millcode like '".$unit."%' and tahunbudget = '".$tahun."'";
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $cpobudgetsd=$res['cpobudget'];
}

$cposelisihsd=$cpobudgetsd-$cposd;

// biaya bulan ini
$aresta="SELECT noakun,sum(jumlah) as biaya FROM ".$dbname.".keu_jurnaldt_vw
    WHERE kodeorg like '".$unit."%' and tanggal like '".$periode."%' and (noakun like '8%')
    GROUP BY noakun";
//echo $aresta;
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $akun[$res['noakun']]=$res['noakun'];
    $dzArr[$res['noakun']]['biaya']=$res['biaya'];
}   

// budget bulan ini
$aresta="SELECT noakun,sum(rp".$bulan.") as budget FROM ".$dbname.".bgt_budget_detail
    WHERE kodeorg like '".$unit."%' and tahunbudget = '".$tahun."' and (noakun like '8%')
    GROUP BY noakun";
//echo $aresta;
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $akun[$res['noakun']]=$res['noakun'];
    $dzArr[$res['noakun']]['budget']=$res['budget'];
}   

// biaya sd bulan ini
$aresta="SELECT noakun,sum(jumlah) as biaya FROM ".$dbname.".keu_jurnaldt_vw
    WHERE kodeorg like '".$unit."%' and tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15') and (noakun like '8%')
    GROUP BY noakun";
//echo $aresta;
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $akun[$res['noakun']]=$res['noakun'];
    $dzArr[$res['noakun']]['biayasd']=$res['biaya'];
}   

$addstr="(";
for($W=1;$W<=intval($bulan);$W++)
{
    if($W<10)$jack="rp0".$W;
    else $jack="rp".$W;
    if($W<intval($bulan))$addstr.=$jack."+";
    else $addstr.=$jack;
}
$addstr.=")";

// budget sd bulan ini
$aresta="SELECT noakun,sum(".$addstr.") as budget FROM ".$dbname.".bgt_budget_detail
    WHERE kodeorg like '".$unit."%' and tahunbudget = '".$tahun."' and (noakun like '8%')
    GROUP BY noakun";
//echo $aresta;
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $akun[$res['noakun']]=$res['noakun'];
    $dzArr[$res['noakun']]['budgetsd']=$res['budget'];
}   

//echo "<pre>";
//print_r($biaya);
//echo "</pre>";

//echo $tbsolah.'<br>';
//echo $tbsolahbudget.'<br>';
//exit;

// kamus akun
$aresta="SELECT noakun, namaakun FROM ".$dbname.".keu_5akun
    WHERE length(noakun)=7 and (noakun like '8%')
    ORDER BY noakun";
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $kamusakun[$res['noakun']]['no']=$res['noakun'];
    $kamusakun[$res['noakun']]['nama']=$res['namaakun'];
}   

// jumlah dan total
if(!empty($akun))foreach($akun as $akyun){
    $dzArr[$akyun]['selisih']=$dzArr[$akyun]['budget']-$dzArr[$akyun]['biaya'];
    $dzArr[$akyun]['selisihsd']=$dzArr[$akyun]['budgetsd']-$dzArr[$akyun]['biayasd'];
    $total['biaya']+=$dzArr[$akyun]['biaya'];
    $total['budget']+=$dzArr[$akyun]['budget'];
    $total['selisih']+=$dzArr[$akyun]['selisih'];
    $total['biayasd']+=$dzArr[$akyun]['biayasd'];
    $total['budgetsd']+=$dzArr[$akyun]['budgetsd'];
    $total['selisihsd']+=$dzArr[$akyun]['selisihsd'];
}

// urut akun
if(!empty($akun))asort($akun);

if($proses=='excel')
{
$bg=" bgcolor=#DEDEDE";
$brdr=1;
$tab.="<table border=0>
     <tr>
        <td colspan=4 align=left><font size=3>".$judul."</font></td>
        <td colspan=3 align=right>".$_SESSION['lang']['bulan']." : ".$optBulan[$bulan]." ".$tahun."</td>
     </tr> 
     <tr><td colspan=14 align=left>".$_SESSION['lang']['unit']." : ".$optNm[$unit]." (".$unit.")</td></tr>   
</table>";
}
else
{ 
    $bg="";
    $brdr=0;
}
if($proses!='excel')$tab.=$judul;
    $tab.="<table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable style='width:100%;'>
    <thead class=rowheader>
    <tr>
    <td align=center rowspan=2 ".$bg.">Uraian</td>
    <td align=center colspan=3 ".$bg.">".$_SESSION['lang']['bulanini']."</td>
    <td align=center colspan=3 ".$bg.">".$_SESSION['lang']['sdbulanini']."</td>
    </tr>
    <tr>
    <td align=center ".$bg.">".$_SESSION['lang']['realisasi']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['anggaran']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['selisih']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['realisasi']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['anggaran']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['selisih']."</td>
    </tr>
    </thead>
    <tbody>
";
        
    $dummy='';
    $no=1;
// excel array content =========================================================================
//    $tab.= "<tr class=rowcontent>";
//    $tab.= "<td align=left>".$_SESSION['lang']['tbsdiolah']." (Ton)</td>"; 
//    $tab.= "<td align=right>".number_format($tbs/1000)."</td>"; 
//    $tab.= "<td align=right>".number_format($tbsbudget/1000)."</td>"; 
//    $tab.= "<td align=right>".number_format($tbsselisih/1000)."</td>"; 
//    $tab.= "<td align=right>".number_format($tbssd/1000)."</td>"; 
//    $tab.= "<td align=right>".number_format($tbsbudgetsd/1000)."</td>"; 
//    $tab.= "<td align=right>".number_format($tbsselisihsd/1000)."</td>"; 
//    $tab.= "</tr>";
//    $tab.= "<tr class=rowcontent>";
//    $tab.= "<td align=left>".$_SESSION['lang']['cpokuantitas']." (Ton)</td>"; 
//    $tab.= "<td align=right>".number_format($cpo/1000)."</td>"; 
//    $tab.= "<td align=right>".number_format($cpobudget/1000)."</td>"; 
//    $tab.= "<td align=right>".number_format($cposelisih/1000)."</td>"; 
//    $tab.= "<td align=right>".number_format($cposd/1000)."</td>"; 
//    $tab.= "<td align=right>".number_format($cpobudgetsd/1000)."</td>"; 
//    $tab.= "<td align=right>".number_format($cposelisihsd/1000)."</td>"; 
//    $tab.= "</tr><tr><td colspan=7>&nbsp;</td></tr>";
    if(!empty($akun)){
        foreach($akun as $akyun){
            $tab.= "<tr class=rowcontent>";
            $tab.= "<td>".$akyun." - ".$kamusakun[$akyun]['nama']."</td>";
            $tab.= "<td align=right>".number_format($dzArr[$akyun]['biaya'])."</td>";
            $tab.= "<td align=right>".number_format($dzArr[$akyun]['budget'])."</td>";
            $tab.= "<td align=right>".number_format($dzArr[$akyun]['selisih'])."</td>";
            $tab.= "<td align=right>".number_format($dzArr[$akyun]['biayasd'])."</td>";
            $tab.= "<td align=right>".number_format($dzArr[$akyun]['budgetsd'])."</td>";
            $tab.= "<td align=right>".number_format($dzArr[$akyun]['selisihsd'])."</td>";
            $tab.= "</tr>";
        }
        $tab.= "<tr class=rowcontent>";
        $tab.= "<td align=center>Total</td>";
        $tab.= "<td align=right>".number_format($total['biaya'])."</td>";
        $tab.= "<td align=right>".number_format($total['budget'])."</td>";
        $tab.= "<td align=right>".number_format($total['selisih'])."</td>";
        $tab.= "<td align=right>".number_format($total['biayasd'])."</td>";
        $tab.= "<td align=right>".number_format($total['budgetsd'])."</td>";
        $tab.= "<td align=right>".number_format($total['selisihsd'])."</td>";
        $tab.= "</tr>";
    }else{
        $tab.= "<tr class=rowcontent>";
        $tab.= "<td colspan=7>Data Empty</td>";
        $tab.= "</tr>";        
    }
    $tab.="</tbody></table>";
			
switch($proses)
{
    case'preview':
    if($unit==''||$periode=='')
    {
        exit("Error:Field Tidak Boleh Kosong");
    }
    echo $tab;
    break;

    case'excel':
    if($unit==''||$periode=='')
    {
        exit("Error:Field Tidak Boleh Kosong");
    }

    $tab.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
    $dte=date("YmdHis");
    $nop_=$judul."_".$unit."_".$periode;
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

    case'pdf':
    if($unit==''||$periode=='')
    {
        exit("Error:Field Tidak Boleh Kosong");
    }

            $cols=247.5;
            $wkiri=30;
            $wlain=11.5;

    class PDF extends FPDF {
    function Header() {
        global $periode,$judul;
        global $unit;
        global $optNm;
        global $optBulan;
        global $tahun;
        global $bulan;
        global $dbname;
        global $luas;
        global $wkiri, $wlain;
        global $luasbudg, $luasreal;
            $width = $this->w - $this->lMargin - $this->rMargin;
  
        $height = 20;
        $this->SetFillColor(220,220,220);
        $this->SetFont('Arial','B',12);

        $this->Cell($width/2,$height,$judul,NULL,0,'L',1);
        $this->Cell($width/2,$height,$_SESSION['lang']['bulan']." : ".$optBulan[$bulan]." ".$tahun,NULL,0,'R',1);
        $this->Ln();
        $this->Cell($width,$height,$_SESSION['lang']['unit']." : ".$optNm[$unit]." (".$unit.")",NULL,0,'L',1);
        $this->Ln();
        $this->Ln();

        $height = 15;
        $this->SetFont('Arial','B',10);
        $this->Cell($wkiri/100*$width,$height,'Uraian',TRL,0,'C',1);	
        $this->Cell($wlain*3/100*$width,$height,$_SESSION['lang']['bulanini'],1,0,'C',1);	
        $this->Cell($wlain*3/100*$width,$height,$_SESSION['lang']['sdbulanini'],1,0,'C',1);	
        $this->Ln();
        $this->Cell($wkiri/100*$width,$height,'',BRL,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['realisasi'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['anggaran'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['selisih'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['realisasi'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['anggaran'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['selisih'],1,0,'C',1);	
        $this->Ln();
    }
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
    }
}
    //================================

    $pdf=new PDF('L','pt','A4');
    $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
    $height = 15;
    $pdf->AddPage();
    $pdf->SetFillColor(255,255,255);
    $pdf->SetFont('Arial','',9);
    
    $no=1;
// pdf array content =========================================================================
//    $pdf->Cell($wkiri/100*$width,$height,$_SESSION['lang']['tbsdiolah'].' (Ton)',1,0,'L',1);	
//    $pdf->Cell($wlain/100*$width,$height,number_format($tbs/1000),1,0,'R',1);	
//    $pdf->Cell($wlain/100*$width,$height,number_format($tbsbudget/1000),1,0,'R',1);	
//    $pdf->Cell($wlain/100*$width,$height,number_format($tbsselisih/1000),1,0,'R',1);	
//    $pdf->Cell($wlain/100*$width,$height,number_format($tbssd/1000),1,0,'R',1);	
//    $pdf->Cell($wlain/100*$width,$height,number_format($tbsbudgetsd/1000),1,0,'R',1);	
//    $pdf->Cell($wlain/100*$width,$height,number_format($tbsselisihsd/1000),1,0,'R',1);	
//    $pdf->Ln();
//    $pdf->Cell($wkiri/100*$width,$height,$_SESSION['lang']['cpokuantitas'].' (Ton)',1,0,'L',1);	
//    $pdf->Cell($wlain/100*$width,$height,number_format($cpo/1000),1,0,'R',1);	
//    $pdf->Cell($wlain/100*$width,$height,number_format($cpobudget/1000),1,0,'R',1);	
//    $pdf->Cell($wlain/100*$width,$height,number_format($cposelisih/1000),1,0,'R',1);	
//    $pdf->Cell($wlain/100*$width,$height,number_format($cposd/1000),1,0,'R',1);	
//    $pdf->Cell($wlain/100*$width,$height,number_format($cpobudgetsd/1000),1,0,'R',1);	
//    $pdf->Cell($wlain/100*$width,$height,number_format($cposelisihsd/1000),1,0,'R',1);	
//    $pdf->Ln();
//    $pdf->Cell($wkiri/100*$width,$height,'',1,0,'L',1);	
//    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
//    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
//    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
//    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
//    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
//    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
//    $pdf->Ln();
    if(!empty($akun)){
        foreach($akun as $akyun){
            $pdf->Cell($wkiri/100*$width,$height,$akyun.' - '.$kamusakun[$akyun]['nama'],1,0,'L',1);	
            $pdf->Cell($wlain/100*$width,$height,number_format($dzArr[$akyun]['biaya']),1,0,'R',1);	
            $pdf->Cell($wlain/100*$width,$height,number_format($dzArr[$akyun]['budget']),1,0,'R',1);	
            $pdf->Cell($wlain/100*$width,$height,number_format($dzArr[$akyun]['selisih']),1,0,'R',1);	
            $pdf->Cell($wlain/100*$width,$height,number_format($dzArr[$akyun]['biayasd']),1,0,'R',1);	
            $pdf->Cell($wlain/100*$width,$height,number_format($dzArr[$akyun]['budgetsd']),1,0,'R',1);	
            $pdf->Cell($wlain/100*$width,$height,number_format($dzArr[$akyun]['selisihsd']),1,0,'R',1);	
            $pdf->Ln();
        }
        $pdf->Cell($wkiri/100*$width,$height,'Total',1,0,'C',1);	
        $pdf->Cell($wlain/100*$width,$height,number_format($total['biaya']),1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,number_format($total['budget']),1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,number_format($total['selisih']),1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,number_format($total['biayasd']),1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,number_format($total['budgetsd']),1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,number_format($total['selisihsd']),1,0,'R',1);	
        $pdf->Ln();
    }else{
        $pdf->Cell($wkiri/100*$width,$height,'Data Empty',1,0,'L',1);	
        $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
        $pdf->Ln();
    }
    
    $pdf->Output();	 
    break;

    default:
    break;
}
	
?>
