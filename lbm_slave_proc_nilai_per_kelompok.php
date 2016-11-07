<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
$_POST['tipe']==''?$tipe=$_GET['tipe']:$tipe=$_POST['tipe'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['statKurs']==''?$statKurs=$_GET['statKurs']:$statKurs=$_POST['statKurs'];
$_POST['judul']==''?$judul=$_GET['judul']:$judul=$_POST['judul'];
$_POST['klmpkBrg']==''?$klmpkBrg=$_GET['klmpkBrg']:$klmpkBrg=$_POST['klmpkBrg'];
$_POST['mtuang']==''?$mtuang=$_GET['mtuang']:$mtuang=$_POST['mtuang'];
$_POST['pt']==''?$pt=$_GET['pt']:$pt=$_POST['pt'];


$qwe=explode('-',$periode); $tahun=$qwe[0]; $bulan=$qwe[1];
$dfrIdr="IDR";

$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optKlmpk=makeOption($dbname, 'log_5klbarang', 'kode,kelompok');
$optNmBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');

$optKlmpk['LAIN']="MATERIAL LAIN";
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
$sKurs="select distinct kode from ".$dbname.".setup_matauang where kode!='IDR' order by kode desc";
$qKurs=mysql_query($sKurs) or die(mysql_error($conn));
while($rKurs=mysql_fetch_assoc($qKurs))
{
    $ard+=1;
    $arr.="##mtUang_".$ard."";
    $arr.="##kurs_".$ard."";
    $_POST['mtUang_'.$ard]==''?$_POST['mtUang_'.$ard]=$_GET['mtUang_'.$ard]:$_POST['mtUang_'.$ard]=$_POST['mtUang_'.$ard];
    $_POST['kurs_'.$ard]==''?$_POST['kurs_'.$ard]=$_GET['kurs_'.$ard]:$_POST['kurs_'.$ard]=$_POST['kurs_'.$ard];
    if($_POST['mtUang_'.$ard]==$rKurs['kode'])
    {
        $krsDt[$rKurs['kode']]=$_POST['kurs_'.$ard];
    }
}
$ard=0;

    
    $sTot="select distinct sum(hargasatuan*jumlahpesan) as total,matauang,substr(kodebarang,1,3) as klmpkBrg  from 
           ".$dbname.".log_po_vw where tanggal like '".$periode."%' 
           group by substr(kodebarang,1,3),matauang order by sum(hargasatuan*jumlahpesan*kurs)  desc";
    $qTot=mysql_query($sTot) or die(mysql_error($sTot));
    $rRow=mysql_num_rows($qTot);
   
        while($rTot=mysql_fetch_assoc($qTot))
        {
            $ard+=1;
            $nilBrg[$rTot['klmpkBrg'].$rTot['matauang']]+=$rTot['total'];
            $totSma[$rTot['matauang']]+=$rTot['total'];
            $dtBrg[$rTot['klmpkBrg']]=$rTot['klmpkBrg'];
            if($rTot['matauang']!='')
            {
                $mtUang[$rTot['matauang']]=$rTot['matauang'];
            }

            if($ard==1)
            {
                $drt="'".$rTot['klmpkBrg']."'";
            }
            else
            {
                $drt.=",'".$rTot['klmpkBrg']."'";
            }
        }

$sDt="select distinct sum(hargasatuan*jumlahpesan) as total,matauang from 
      ".$dbname.".log_po_vw where substr(kodebarang,1,3) not in (".$drt.") and tanggal like '".$periode."%'
       group by matauang order by sum(hargasatuan*jumlahpesan*kurs) desc";
//exit("Error:".$sDt);
$qDt=mysql_query($sDt) or die(mysql_error($conn));
while($rDt=mysql_fetch_assoc($qDt))
{
    $rDt['klmpkBrg']='LAIN';
    $dtBrg[$rDt['klmpkBrg']]=$rDt['klmpkBrg'];
    $nilBrg[$rDt['klmpkBrg'].$rDt['matauang']]+=$rDt['total'];
    $totSma[$rDt['matauang']]+=$rDt['total'];
    if($rDt['matauang']!='')
    {
        $mtUang[$rDt['matauang']]=$rDt['matauang'];
    }
}
$colJum=count($mtUang);
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
    <td align=center rowspan=2 ".$bg.">No.</td>
    <td align=center rowspan=2 ".$bg.">".$_SESSION['lang']['kelompokbarang']."</td>
    <td align=center colspan='".$colJum."' ".$bg.">".$_SESSION['lang']['matauang']."</td>
    <td align=center rowspan=2 ".$bg.">% IDR</td>
    </tr>";
    $tab.="
    <tr>";
    foreach($mtUang as $dtMat)
    {
     $tab.="<td align=center ".$bg.">".$dtMat."</td>";
    }
    
    $tab.="</tr>
    </thead>
    <tbody>
";
    foreach($dtBrg as $dtKlmpBrg)
    {
        $ert+=1;
        $tab.="<tr class=rowcontent onclick=getDet('lbm_slave_proc_nilai_per_kelompok','".$dtKlmpBrg."','".$periode."') style=cursor:pointer>";
        $tab.="<td>".$ert."</td>";
        $tab.="<td>".$optKlmpk[$dtKlmpBrg]."</td>";
        foreach($mtUang as $dtMat2)
        {
            $tab.="<td align=right>".number_format($nilBrg[$dtKlmpBrg.$dtMat2],0)."</td>";
           // $totSma[$dtMat2]+=$nilBrg[$dtKlmpBrg.$dtMat2];
            @$prsn[$dtKlmpBrg]=$nilBrg[$dtKlmpBrg.$dfrIdr]/$totSma[$dfrIdr]*100;
        }
        $tab.="<td align=right>".number_format($prsn[$dtKlmpBrg],2)."</td>";
        $tab.="</tr>";
        $totPersen+=$prsn[$dtKlmpBrg];
    }
    $tab.="<tr class=rowcontent>";
    $tab.="<td colspan=2>".$_SESSION['lang']['total']."</td>";
    foreach($mtUang as $dtMat3)
    {
       $tab.="<td align=right>".number_format($totSma[$dtMat3],0)."</td>"; 
    }
    $tab.="<td align=right>".$totPersen."</td>";
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
    $nop_="nilaiPembelian_".$dte;
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
        $this->Ln(35);
       

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
    
//    $no=1;
//// pdf array content =========================================================================
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
//    if(!empty($akun)){
//        foreach($akun as $akyun){
//            $pdf->Cell($wkiri/100*$width,$height,$akyun.' - '.$kamusakun[$akyun]['nama'],1,0,'L',1);	
//            $pdf->Cell($wlain/100*$width,$height,number_format($dzArr[$akyun]['biaya']),1,0,'R',1);	
//            $pdf->Cell($wlain/100*$width,$height,number_format($dzArr[$akyun]['budget']),1,0,'R',1);	
//            $pdf->Cell($wlain/100*$width,$height,number_format($dzArr[$akyun]['selisih']),1,0,'R',1);	
//            $pdf->Cell($wlain/100*$width,$height,number_format($dzArr[$akyun]['biayasd']),1,0,'R',1);	
//            $pdf->Cell($wlain/100*$width,$height,number_format($dzArr[$akyun]['budgetsd']),1,0,'R',1);	
//            $pdf->Cell($wlain/100*$width,$height,number_format($dzArr[$akyun]['selisihsd']),1,0,'R',1);	
//            $pdf->Ln();
//        }
//        $pdf->Cell($wkiri/100*$width,$height,'Total',1,0,'C',1);	
//        $pdf->Cell($wlain/100*$width,$height,number_format($total['biaya']),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,number_format($total['budget']),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,number_format($total['selisih']),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,number_format($total['biayasd']),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,number_format($total['budgetsd']),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,number_format($total['selisihsd']),1,0,'R',1);	
//        $pdf->Ln();
//    }else{
//        $pdf->Cell($wkiri/100*$width,$height,'Data Empty',1,0,'L',1);	
//        $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
//        $pdf->Ln();
//        
//    }
    
    $pdf->Output();	 
    break;
    case'getDetail':
//        #ambil regionl#
//        $sReg="select distinct regional from ".$dbname.".bgt_regional
//               order by regional asc";
//        $qReg=mysql_query($sReg) or die(mysql_error($conn));
//        while($rReg=mysql_fetch_assoc($qReg))
//        {
//           $sAm="select distinct kodeunit from ".$dbname.".bgt_regional_assignment 
//                 where regional='".$rReg['regional']."'";
//           $qAm=mysql_query($sAm) or die(mysql_error($conn));
//           while($rAm=mysql_fetch_assoc($qAm))
//           {
//               if($rReg['regional']!=$aretr)
//               {
//                   $ard=0;
//                   $aretr=$rReg['regional'];
//               }
//               $ard+=1;
//               if($ard==1)
//               {
//                   $unitAd[$rReg['regional']]="'".$rAm['kodeunit']."'";
//               }
//               else
//                {
//                    $unitAd[$rReg['regional']].=",'".$rAm['kodeunit']."'";
//                }
//            }
//            $dtReg[$rReg['regional']]=$rReg['regional'];
//        }
//        while($rTes=mysql_fetch_assoc($qReg))
//        {
//            $rowDr[$rTes['regional']]=count($unitAd[$rTes['regional']]);
//            $sData="select distinct sum(jumlahpesan*hargasatuan) as total,matauang ,kodeorg
//                    from ".$dbname.".log_po_vw where substr(kodebarang,1,3)='".$klmpkBrg."' and
//                    kodeorg in (select distinct induk from ".$dbname.".organisasi where kodeorganisasi in (".$unitAd[$rTes['regional']]."))
//                    grup by matauang,kodeorg order by (jumlahpesan*hargasatuan) desc";
//            
//            $qData=mysql_query($sData) or die(mysql_error($conn));
//            while($rData=mysql_fetch_assoc($qData))
//            {
//                $rupReg[$rTes['regional'].$rData['kodeorg'].$rData['matauang']]+=$rData['total'];
//                $ptReg[$rTes['regional']]=$rData['kodeorg'];
//            }
//            
//        }
//        echo $sData;
//        echo"<br /><pre>";
//        print_r($ptReg);
//        echo"</pre>";
        $judul=strtoupper("Detail Nilai Pembeliaan ".$optKlmpk[$klmpkBrg]);
        $sData="select distinct sum(jumlahpesan*hargasatuan) as total,matauang ,kodeorg,kodebarang,sum(jumlahpesan) as jumlahpesan
                from ".$dbname.".log_po_vw where substr(kodebarang,1,3)='".$klmpkBrg."'
                and tanggal like '".$periode."%'    
                group by matauang,kodeorg,kodebarang order by (jumlahpesan*hargasatuan) desc";
        $qData=mysql_query($sData) or die(mysql_error($conn));
        while($rData=mysql_fetch_assoc($qData))
        {
            $dtMatuang[$rData['matauang']]=$rData['matauang'];
            $dtOrg[$rData['kodeorg']]=$rData['kodeorg'];
            $nilBrg[$rData['kodebarang'].$rData['kodeorg'].$rData['matauang']]=$rData['total'];
            $dtBarang[$rData['kodebarang']]=$rData['kodebarang'];
        }
        $colsl=count($dtMatuang);
        $org=count($dtOrg);
        $smua=$colsl*$org;
        $tabc.="<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>";
        $tabc.="<tr>";
        $tabc.="<td rowspan=2>No.</td><td rowspan=2>".$_SESSION['lang']['namabarang']."</td>";
        foreach($dtOrg as $lstOrg)
        {
            $tabc.="<td colspan='".$colsl."'>".$optNm[$lstOrg]."</td>";
        }
       
        $tabc.="</tr><tr>";
        foreach($dtOrg as $lstOrg2)
        {
            foreach($dtMatuang as $lstMatauang)
            {
                $tabc.="<td>".$lstMatauang."</td>";
            }
        }
        $tabc.="</tr></thead><tbody>";
        foreach($dtBarang as $lstBarang)
        {
            $art+=1;
           $tabc.="<tr class=rowcontent>";
           $tabc.="<td>".$art."</td>";
           $tabc.="<td>".$optNmBrg[$lstBarang]."</td>";
            foreach($dtOrg as $lstOrg3)
            {
                foreach($dtMatuang as $lstMatauang2)
                {
                    $tabc.="<td align=right style=cursor:pointer onclick=getDet2('lbm_slave_proc_nilai_per_kelompok','".$lstOrg3."','".$klmpkBrg."','".$lstMatauang2."','".$periode."','".$lstBarang."')>".number_format($nilBrg[$lstBarang.$lstOrg3.$lstMatauang2],0)."</td>";
                    //$total[$lstBarang.$lstMatauang2]+=$nilBrg[$lstBarang.$lstOrg3.$lstMatauang2];
                }
            }
            $tabc.="</tr>";
        }
        
        $tabc.="<tr><td colspan=".$smua.">";
        $tabc.="<button class=mybutton onclick=zBack()>Back</button>";
        $tabc.="<button onclick=\"zExcel2(event,'lbm_slave_proc_nilai_per_kelompok.php','getDetPtEx','".$lstOrg3."','".$klmpkBrg."','".$lstMatauang2."','".$periode."','reportcontainer')\" class=\"mybutton\" name=\"excel\" id=\"excel\">
               ".$_SESSION['lang']['excel']."</button></tr> ";
        $tabc.="</tbody></table>";
        echo $tabc."###".$judul;
    break;
    case'getDetPtEx':
       $judul=strtoupper("Detail Nilai Pembeliaan ".$optKlmpk[$klmpkBrg]);
        $sData="select distinct sum(jumlahpesan*hargasatuan) as total,matauang ,kodeorg,kodebarang,sum(jumlahpesan) as jumlahpesan
                from ".$dbname.".log_po_vw where substr(kodebarang,1,3)='".$klmpkBrg."'
                and tanggal like '".$periode."%'    
                group by matauang,kodeorg,kodebarang order by (jumlahpesan*hargasatuan) desc";
        $qData=mysql_query($sData) or die(mysql_error($conn));
        while($rData=mysql_fetch_assoc($qData))
        {
            $dtMatuang[$rData['matauang']]=$rData['matauang'];
            $dtOrg[$rData['kodeorg']]=$rData['kodeorg'];
            $nilBrg[$rData['kodebarang'].$rData['kodeorg'].$rData['matauang']]=$rData['total'];
            $dtBarang[$rData['kodebarang']]=$rData['kodebarang'];
        }
        $colsl=count($dtMatuang);
        $org=count($dtOrg);
        $smua=$colsl*$org;
        $tabc.="<table cellpadding=1 cellspacing=1 border=1 class=sortable><thead>";
        $tabc.="<tr>";
        $tabc.="<td rowspan=2 ".$bg.">No.</td><td rowspan=2 ".$bg.">".$_SESSION['lang']['namabarang']."</td>";
        foreach($dtOrg as $lstOrg)
        {
            $tabc.="<td colspan='".$colsl."' ".$bg.">".$optNm[$lstOrg]."</td>";
        }
       
        $tabc.="</tr><tr>";
        foreach($dtOrg as $lstOrg2)
        {
            foreach($dtMatuang as $lstMatauang)
            {
                $tabc.="<td>".$lstMatauang."</td>";
            }
        }
        $tabc.="</tr></thead><tbody>";
        foreach($dtBarang as $lstBarang)
        {
            $art+=1;
           $tabc.="<tr class=rowcontent>";
           $tabc.="<td>".$art."</td>";
           $tabc.="<td>".$optNmBrg[$lstBarang]."</td>";
            foreach($dtOrg as $lstOrg3)
            {
                foreach($dtMatuang as $lstMatauang2)
                {
                    $tabc.="<td align=right style=cursor:pointer onclick=getDet2('lbm_slave_proc_nilai_per_kelompok','".$lstOrg3."','".$klmpkBrg."','".$lstMatauang2."','".$periode."','".$lstBarang."')>".number_format($nilBrg[$lstBarang.$lstOrg3.$lstMatauang2],0)."</td>";
                    //$total[$lstBarang.$lstMatauang2]+=$nilBrg[$lstBarang.$lstOrg3.$lstMatauang2];
                }
            }
            $tabc.="</tr>";
        }
        
        $tabc.="</tbody></table>";
         $tabc.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
    $dte=date("His");
    $nop_="detailPt_".$dte;
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
    case'getDetPt':
         $judul=strtoupper("Detail Nilai Pembeliaan ".$optKlmpk[$klmpkBrg]);
    $sData="select distinct sum(jumlahpesan*hargasatuan) as total, substr(nopp,16,4) as unit,kodeorg from
            ".$dbname.".log_po_vw where kodeorg='".$pt."' and substr(kodebarang,1,3)='".$klmpkBrg."' and kodebarang='".$_POST['kdBarang']."'
            and matauang='".$mtuang."' and tanggal like '".$periode."%' group by  substr(nopp,16,4) order by substr(nopp,16,4) desc";
   // exit("Error:".$sData);
    $qData=mysql_query($sData) or die(mysql_error($conn));
    while($rData=mysql_fetch_assoc($qData))
    {
        if($rData['unit']!='')
        {
            $untLa=$rData['unit'];
        }
        else
        {
            $rData['unit']=$untLa;
        }
        
        $nilDt[$rData['unit']]+=$rData['total'];
        $unitDt[$rData['unit']]=$rData['unit'];
    }
    $tabd.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>";
    $tabd.="<thead><tr>";
    foreach($unitDt as $lsUnit)
    {
        $tabd.="<td>".$lsUnit."</td>";
    }
    $tabd.="<td>".$_SESSION['lang']['total']."</td></tr></thead><tbody><tr class=rowcontent>";
    foreach($unitDt as $lsUnit2)
    {
        $tabd.="<td align=right>".number_format($nilDt[$lsUnit2],0)."</td>";
        $tot+=$nilDt[$lsUnit2];
    }
    $tabd.="<td align=right>".number_format($tot,0)."</td></tr>";
    $tabd.="<tr><td>";
    $tabd.="<button class=mybutton onclick=zBack2()>Back</button>
        <button onclick=\"zExcel2(event,'lbm_slave_proc_nilai_per_kelompok.php','getDetPtExc','".$pt."','".$klmpkBrg."','".$mtuang."','".$periode."','".$_POST['kdBarang']."','reportcontainer')\" class=\"mybutton\" name=\"excel\" id=\"excel\">
               ".$_SESSION['lang']['excel']."</button></td></tr>";
    $tabd.="</tbody></table>";
    echo $tabd."###".$judul;
    break;
    case'getDetPtExc':
     $sData="select distinct sum(jumlahpesan*hargasatuan) as total, substr(nopp,16,4) as unit,kodeorg from
            ".$dbname.".log_po_vw where kodeorg='".$pt."' and substr(kodebarang,1,3)='".$klmpkBrg."' and kodebarang='".$_GET['kdBarang']."'
            and matauang='".$mtuang."' and tanggal like '".$periode."%' group by  substr(nopp,16,4) order by substr(nopp,16,4) desc";
   // exit("Error:".$sData);
    $qData=mysql_query($sData) or die(mysql_error($conn));
    while($rData=mysql_fetch_assoc($qData))
    {
        if($rData['unit']!='')
        {
            $untLa=$rData['unit'];
        }
        else
        {
            $rData['unit']=$untLa;
        }
        
        $nilDt[$rData['unit']]+=$rData['total'];
        $unitDt[$rData['unit']]=$rData['unit'];
    }
    $tabd.="<table cellpadding=1 cellspacing=1 border=1 class=sortable>";
    $tabd.="<thead><tr>";
    foreach($unitDt as $lsUnit)
    {
        $tabd.="<td   bgcolor=#DEDEDE>".$lsUnit."</td>";
    }
    $tabd.="<td   bgcolor=#DEDEDE>".$_SESSION['lang']['total']."</td></tr></thead><tbody><tr class=rowcontent>";
    foreach($unitDt as $lsUnit2)
    {
        $tabd.="<td align=right>".number_format($nilDt[$lsUnit2],0)."</td>";
        $tot+=$nilDt[$lsUnit2];
    }
    $tabd.="<td align=right>".number_format($tot,0)."</td></tr>";
    $tabd.="</tbody></table>";
         $tabd.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
    $dte=date("His");
    $nop_="detailPt_".$dte;
    if(strlen($tabd)>0)
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
        if(!fwrite($handle,$tabd))
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
