<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
$_POST['tipe']==''?$tipe=$_GET['tipe']:$tipe=$_POST['tipe'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['judul']==''?$judul=$_GET['judul']:$judul=$_POST['judul'];
$qwe=explode('-',$periode); $tahun=$qwe[0]; $bulan=$qwe[1];

$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNmBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
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

$sBln="select distinct sum(jumlahpesan*hargasatuan) as total,matauang,kodebarang,sum(jumlahpesan) as jumlahpesan,satuan,matauang 
       from ".$dbname.".log_po_vw where substr(kodebarang,1,3)='312' 
       and tanggal like '".$periode."%' group by kodebarang,matauang  order by namabarang asc";
$qBln=mysql_query($sBln) or die(mysql_error($conn));
while($rBln=mysql_fetch_assoc($qBln))
{
    if($rBln['matauang']=='')
    {
        $rBln['matauang']='IDR';
    }
    $dtBrg[$rBln['kodebarang']]=$rBln['kodebarang'];
    $dtUang[$rBln['kodebarang'].$rBln['matauang']]+=$rBln['total'];
    $dtJmlh[$rBln['kodebarang']]+=$rBln['jumlahpesan'];
    $dtMata[$rBln['matauang']]=$rBln['matauang'];
}
$sBln="select distinct sum(jumlahpesan*hargasatuan) as total,matauang,kodebarang,sum(jumlahpesan) as jumlahpesan,matauang 
       from ".$dbname.".log_po_vw where substr(kodebarang,1,3)='312' 
       and substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."%' group by kodebarang,matauang order by namabarang asc";
$qBln=mysql_query($sBln) or die(mysql_error($conn));
while($rBln=mysql_fetch_assoc($qBln))
{
     if($rBln['matauang']=='')
    {
        $rBln['matauang']='IDR';
    }
    $dtBrg[$rBln['kodebarang']]=$rBln['kodebarang'];
    $dtUangBlnini[$rBln['kodebarang'].$rBln['matauang']]+=$rBln['total'];
    $dtJmlhblnIni[$rBln['kodebarang']]+=$rBln['jumlahpesan'];
    $dtMata[$rBln['matauang']]=$rBln['matauang'];
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
    <td align=center ".$bg." rowspan=2>No.</td>
    <td align=center ".$bg." rowspan=2>".$_SESSION['lang']['namabarang']."</td>
    <td align=center ".$bg." colspan=2>Kg</td>";
    foreach($dtMata as $lsMata)
    {
        $tab.="<td colspan=2 ".$bg.">".$lsMata."</td>";
    }
    $tab.="</tr><tr>";
    $tab.="<td  align=center ".$bg.">".$_SESSION['lang']['blnini']."</td>";
    $tab.="<td  align=center ".$bg.">".$_SESSION['lang']['sdbulanini']."</td>";
    foreach($dtMata as $lsMata2)
    {
        $tab.="<td  align=center ".$bg.">".$_SESSION['lang']['blnini']."</td>";
        $tab.="<td  align=center ".$bg.">".$_SESSION['lang']['sdbulanini']."</td>";
    }
    //$optNmBrg
    $tab.="</tr></thead><tbody>";
    foreach($dtBrg as $lstKdbarang)
    {
        $artd+=1;
        $tab.="<tr class=rowcontent>";
        $tab.="<td>".$artd."</td>";
        $tab.="<td>".$optNmBrg[$lstKdbarang]."</td>";
        $tab.="<td align=right>".number_format($dtJmlh[$lstKdbarang],0)."</td>";
        $tab.="<td align=right>".number_format($dtJmlhblnIni[$lstKdbarang],0)."</td>";
        foreach($dtMata as $lstMata3)
        {
            $tab.="<td align=right>".number_format($dtUang[$lstKdbarang.$lstMata3],0)."</td>";
            $tab.="<td align=right>".number_format($dtUangBlnini[$lstKdbarang.$lstMata3],0)."</td>";
            $totNil[$lstMata3]+=$dtUang[$lstKdbarang.$lstMata3];
            $totNilSmp[$lstMata3]+=$dtUangBlnini[$lstKdbarang.$lstMata3];
        }
        $totJm+=$dtJmlh[$lstKdbarang];
        $totJmlBln+=$dtJmlhblnIni[$lstKdbarang];
    }
    $tab.="<tr class=rowcontent>";
    $tab.="<td colspan=2>".$_SESSION['lang']['total']."</td>";
    $tab.="<td align=right>".number_format($totJm,0)."</td>";
    $tab.="<td align=right>".number_format($totJmlBln,0)."</td>";
    foreach($dtMata as $lstMata3)
    {
        $tab.="<td align=right>".number_format($totNil[$lstMata3],0)."</td>";
        $tab.="<td align=right>".number_format($totNilSmp[$lstMata3],0)."</td>";
    }
    $tab.="</tr>";
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
    $nop_="pembelianAgrochem_".$dte;
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
    if($periode=='')
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
        $this->Ln(10);
       

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
    $pdf->Cell($wkiri/100*$width,$height,$_SESSION['lang']['tbsdiolah'].' (Ton)',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($tbs/1000),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($tbsbudget/1000),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($tbsselisih/1000),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($tbssd/1000),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($tbsbudgetsd/1000),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($tbsselisihsd/1000),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell($wkiri/100*$width,$height,$_SESSION['lang']['cpokuantitas'].' (Ton)',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($cpo/1000),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($cpobudget/1000),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($cposelisih/1000),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($cposd/1000),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($cpobudgetsd/1000),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($cposelisihsd/1000),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell($wkiri/100*$width,$height,'',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Ln();
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
