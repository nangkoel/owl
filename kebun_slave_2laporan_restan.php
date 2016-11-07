<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

$proses=$_GET['proses'];
$_POST['kdUnit']==''?$kdUnit=$_GET['kdUnit']:$kdUnit=$_POST['kdUnit'];
$_POST['afdId']==''?$afdId=$_GET['afdId']:$afdId=$_POST['afdId'];
$_POST['BlokId']==''?$BlokId=$_GET['BlokId']:$BlokId=$_POST['BlokId'];
$_POST['periodeId']==''?$periodeId=$_GET['periodeId']:$periodeId=$_POST['periodeId'];
$_POST['periodeData']==''?$periodeData=$_GET['periodeData']:$periodeData=$_POST['periodeData'];

$per=explode("-",$periodeId);
$perod=$per[2]."-".$per[1]."-".$per[0];
$dtPeriod=$per[2]."-".$per[1];
$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$where=" kodeunit='".$kodeOrg."' and tahunbudget='".$thnBudget."'";
$arrBln=array("1"=>"Jan","2"=>"Feb","3"=>"Mar","4"=>"Apr","5"=>"Mei","6"=>"Jun","7"=>"Jul","8"=>"Aug","9"=>"Sep","10"=>"Okt","11"=>"Nov","12"=>"Des");
$optNmkeg=makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan');
$optSatkeg=makeOption($dbname,'setup_kegiatan','kodekegiatan,satuan');
$tgkAwal=$periodeData."-01";
$lastday = date('t',strtotime($tgkAwal));
$tgkAkhir=$periodeData."-".$lastday;

$arrTgl=dates_inbetween($tgkAwal,$tgkAkhir);
function dates_inbetween($date1, $date2){

    $day = 60*60*24;

    $date1 = strtotime($date1);
    $date2 = strtotime($date2);

    $days_diff = round(($date2 - $date1)/$day); // Unix time difference devided by 1 day to get total days in between

    $dates_array = array();
    $dates_array[] = date('Y-m-d',$date1);
   
    for($x = 1; $x < $days_diff; $x++){
        $dates_array[] = date('Y-m-d',($date1+($day*$x)));
    }

    $dates_array[] = date('Y-m-d',$date2);
    if($date1==$date2){
        $dates_array = array();
        $dates_array[] = date('Y-m-d',$date1);        
    }
    return $dates_array;
}
            if($kdUnit=='')
            {
                exit("Error: Estate is obligatory");
            }
            if($kdUnit!='')
            {
                $where="kodeorg like '".$kdUnit."%'";
            }
            if($afdId!='')
            {
                $where="kodeorg like '".$afdId."%'";
            }
            
            if($kdUnit!='')
            {
                $wherekd="'".$kdUnit."%'";
            }
            if($afdId!='')
            {
                $wherekd="'".$afdId."%'";
            }

         
            //panen
            $sPanen="select sum(hasilkerja) as jjg,kodeorg,tanggal from ".$dbname.".kebun_prestasi_vw
                     where kodeorg like ".$wherekd." and tanggal like '".$periodeData."%' 
                     group by kodeorg,tanggal order by tanggal asc,kodeorg asc";
            $qPanen=mysql_query($sPanen) or die(mysql_error($conn));
            while($rPanen=  mysql_fetch_assoc($qPanen))
            {
//                if($rPanen['jjg']!=0)
//                {
                $dtKdOrg[$rPanen['kodeorg']]=$rPanen['kodeorg'];
               // $arrTgl[$rPanen['tanggal']]=$rPanen['tanggal'];
                $dtJJgPan[$rPanen['kodeorg'].$rPanen['tanggal']]=$rPanen['jjg'];
               // }
            }

            $sparam="select  nilai from ".$dbname.".setup_parameterappl where kodeparameter='JJGKT'";
            $qParam=mysql_query($sparam) or die(mysql_error($conn));
            $rParam=mysql_fetch_assoc($qParam);

            $sKontanan="select sum(jjgkontanan) as jjgkontan,kodeblok as kodeorg,tanggal from ".$dbname.".log_baspk
                       where kodeblok like ".$wherekd."  and tanggal like '".$periodeData."%'  group by kodeblok,tanggal
                        order by tanggal asc,kodeblok asc";
            $qKontanan=mysql_query($sKontanan)  or die(mysql_error($conn));
            while($rKontan=mysql_fetch_assoc($qKontanan))
            {
//                if($rKontan['jjgkontan']!=0)
//                {
                $dtKdOrg[$rKontan['kodeorg']]=$rKontan['kodeorg'];
                $dtJJgkntn[$rKontan['kodeorg'].$rKontan['tanggal']]=$rKontan['jjgkontan'];
                //$arrTgl[$rKontan['tanggal']]=$rKontan['tanggal'];
                //}
                //$dtJJgkntn[$rKontan['kodeorg']]=0;  #escape ini dan buka ecape di atas baris ini jika kontanan sudah berupa JJG#
            }


            $sPanen2="select SUM( jjg ) AS angkut, blok as kodeorg,tanggal from ".$dbname.".kebun_spb_vw
                    where blok like ".$wherekd." and tanggal like '".$periodeData."%' group by blok,tanggal
                      order by tanggal asc,blok asc";
            $qPanen2=mysql_query($sPanen2) or die(mysql_error($conn));
            while($rPanen2=  mysql_fetch_assoc($qPanen2))
            {
//                if($rPanen2['angkut']!=0)
//                {
                $dtKdOrg[$rPanen2['kodeorg']]=$rPanen2['kodeorg'];
                $dtJJg[$rPanen2['kodeorg'].$rPanen2['tanggal']]=$rPanen2['angkut'];
                //$arrTgl[$rPanen2['tanggal']]=$rPanen2['tanggal'];
               // }
            }

            $dcek=count($dtKdOrg);
            if($dcek==0)
            {
                exit("Error:Data Kosong");
            }
$brd=0;
$bgcolordt="";
if($proses=='excel')
{
 $bgcolordt="bgcolor=#DEDEDE";
 $brd=1;
}
        $tab.="<table cellpadding=1 cellspacing=1 border=".$brd." class=sortable>";
        $tab.="<thead><tr>";
        $tab.="<td ".$bgcolordt." rowspan=2>".$_SESSION['lang']['blok']."</td>";
        foreach($arrTgl as $dtTgl=>$isi)
        {
            $tab.="<td ".$bgcolordt." align=center colspan=4>".substr($isi,-2,2)."</td>";
        }
        $tab.="</tr><tr>";
        
        foreach($arrTgl as $dtTgl)
        {
        $tab.="<td align=center ".$bgcolordt.">".$_SESSION['lang']['panen']." (JJG)</td>";
        $tab.="<td align=center ".$bgcolordt.">".$_SESSION['lang']['jjgkontanan']."</td>";
        $tab.="<td align=center ".$bgcolordt.">Sent (JJG)</td>";
        $tab.="<td align=center ".$bgcolordt.">Remains (JJG)</td>";
        }
        $tab.="</tr></thead><tbody>";
        foreach($dtKdOrg as $isi)
        {
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$isi."</td>";
            foreach($arrTgl as $dtTgl=>$ertDt)
            {
                $ard+=1;
                if($ard==1)
                {
                $tab.="<td align=right>".number_format($dtJJgPan[$isi.$ertDt])."</td>";
                $tab.="<td align=right>".number_format($dtJJgkntn[$isi.$ertDt])."</td>";
                $tab.="<td align=right>".number_format($dtJJg[$isi.$ertDt])."</td>";
                $rest[$isi.$ertDt]=($dtJJgPan[$isi.$ertDt]+$dtJJgkntn[$isi.$ertDt])-$dtJJg[$isi.$ertDt];
                $tab.="<td align=right>".number_format($rest[$isi.$ertDt])."</td>";
                $totPanen[$ertDt]+=$dtJJgPan[$isi.$ertDt];
                $totKirim[$ertDt]+=$dtJJg[$isi.$ertDt];
                $totKontan[$ertDt]+=$dtJJgkntn[$isi.$ertDt];
                $totRest[$ertDt]+=$rest[$isi.$ertDt];
                }
                else
                {
                    $kmrn=strtotime ('-1 day',strtotime ($ertDt));
                    $kmrn=date ('Y-m-d', $kmrn );
                    $tab.="<td align=right>".number_format($dtJJgPan[$isi.$ertDt])."</td>";
                    $tab.="<td align=right>".number_format($dtJJgkntn[$isi.$ertDt])."</td>";
                    $tab.="<td align=right>".number_format($dtJJg[$isi.$ertDt])."</td>";
                    $rest[$isi.$ertDt]=($dtJJgPan[$isi.$ertDt]+$dtJJgkntn[$isi.$ertDt]+$rest[$isi.$kmrn])-$dtJJg[$isi.$ertDt];
                    $tab.="<td align=right>".number_format($rest[$isi.$ertDt])."</td>";
                    $totPanen[$ertDt]+=$dtJJgPan[$isi.$ertDt];
                    $totKirim[$ertDt]+=$dtJJg[$isi.$ertDt];
                    $totKontan[$ertDt]+=$dtJJgkntn[$isi.$ertDt];
                    $totRest[$ertDt]+=$rest[$isi.$ertDt];
                }
            }
            $tab.="</tr>";
        }
        $tab.="<tr class=rowcontent><td>".$_SESSION['lang']['total']."</td>";
         foreach($arrTgl as $dtTgl=>$ertDt)
          {
            $tab.="<td align=right>".number_format($totPanen[$ertDt])."</td>";
            $tab.="<td align=right>".number_format($totKontan[$ertDt])."</td>";
            $tab.="<td align=right>".number_format($totKirim[$ertDt])."</td>";
            $tab.="<td align=right>".number_format($totRest[$ertDt])."</td>";
          }
        $tab.="</tr></tbody></table>";
	switch($proses)
        {
            case'preview':
       
        echo $tab;
        break;
          case'excel':
          
          $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
        $dte=date("Hms");
        $nop_="laporanRestan_".$dte;
         $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
         gzwrite($gztralala, $tab);
         gzclose($gztralala);
         echo "<script language=javascript1.2>
            window.location='tempExcel/".$nop_.".xls.gz';
            </script>";	
        break;
         case'pdf':
        
           class PDF extends FPDF {
           function Header() {
            global $periodeId;
            global $dataAfd;
            global $kdUnit;
            global $optNm;  
            global $dbname;
            global $where;
          
            global $optSatuan;
            global $optNmBrg;
            $width = $this->w - $this->lMargin - $this->rMargin;
            $height = 20;
            
                $this->SetFont('Arial','B',8);
                $this->Cell(250,$height,strtoupper("LAPORAN RESTAN"),0,0,'L');
                $this->Cell(270,$height,$_SESSION['lang']['bulan'].' : '.substr(tanggalnormal($periodeId),1,7),0,1,'R');
                $tinggiAkr=$this->GetY();
                $ksamping=$this->GetX();
                $this->SetY($tinggiAkr);
                $this->SetX($ksamping);
                $this->Cell($width,$height,$_SESSION['lang']['unit'].' : '.$optNm[$kdUnit],0,1,'L');
                
                $this->SetFillColor(220,220,220);
                $this->Cell(55,$height,"Tanggal",TLR,0,'C',1);
                $this->Cell(55,$height,$_SESSION['lang']['blok'],TLR,0,'C',1);
                $this->Cell(90,$height,"Panen",TLR,0,'C',1);
                $this->Cell(90,$height,"Kirim",TLR,0,'C',1);
                $this->Cell(90,$height,"Restan",TLR,1,'C',1);
                
                $this->Cell(55,$height," ",BLR,0,'C',1);
                $this->Cell(55,$height," ",BLR,0,'C',1);
                $this->Cell(45,$height,"Janjang",TBLR,0,'C',1);
                $this->Cell(45,$height,"Kg",TBLR,0,'C',1);
                $this->Cell(45,$height,"Janjang",TBLR,0,'C',1);
                $this->Cell(45,$height,"Kg",TBLR,0,'C',1);
                $this->Cell(45,$height,"Janjang",TBLR,0,'C',1);
                $this->Cell(45,$height,"Kg",TBLR,1,'C',1);

               
          }
              function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
            }
            //================================

            $pdf=new PDF('P','pt','A4');
            $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
            $height = 15;
           
            $pdf->AddPage();
            
            $pdf->SetFillColor(255,255,255);
             $pdf->SetFont('Arial','',7);
            
             $sData="select distinct * from ".$dbname.".kebun_restan where ".$where." order by tanggal asc";
        //exit("Error".$sData);
        $qData=mysql_query($sData) or die(mysql_error());
        $row=mysql_num_rows($qData);
        if($row!=0)
        {
        while($rdata=mysql_fetch_assoc($qData))
        {
             $sKg="select distinct bjr from ".$dbname.".kebun_5bjr where kodeorg='".$rdata['kodeorg']."'";
                    $qKg=mysql_query($sKg) or die(mysql_error());
                    $rKg=mysql_fetch_assoc($qKg);
                    $panenKg=$rdata['jjgpanen']*$rKg['bjr'];
                    $KirimKg=$rdata['jjgkirim']*$rKg['bjr'];
                    $resJjg=$rdata['jjgpanen']-$rdata['jjgkirim'];
                    $resKg=$resJjg*$rKg['bjr'];
                    $pdf->Cell(55,$height,tanggalnormal($rdata['tanggal']),TBLR,0,'C',1);
                    $pdf->Cell(55,$height,$rdata['kodeorg'],TBLR,0,'C',1);
                    $pdf->Cell(45,$height,$rdata['jjgpanen'],TBLR,0,'R',1);
                    $pdf->Cell(45,$height,$panenKg,TBLR,0,'R',1);
                    $pdf->Cell(45,$height,$rdata['jjgkirim'],TBLR,0,'R',1);
                    $pdf->Cell(45,$height,$KirimKg,TBLR,0,'R',1);
                    $pdf->Cell(45,$height,$resJjg,TBLR,0,'R',1);
                    $pdf->Cell(45,$height,$resKg,TBLR,1,'R',1);
           
        }
        }
        else
        {
           $pdf->Cell(380,$height,$_SESSION['lang']['dataempty'],TBLR,1,'C',1); 
        }

            $pdf->Output();
        break;
                
            default:
            break;
        }
	
?>
