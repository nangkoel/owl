<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');


$proses=$_GET['proses'];
$_POST['kdUnit']==''?$kodeOrg=$_GET['kdUnit']:$kodeOrg=$_POST['kdUnit'];
$_POST['thnBudget']==''?$thnBudget=$_GET['thnBudget']:$thnBudget=$_POST['thnBudget'];
$_POST['pt']==''?$pt=$_GET['pt']:$pt=$_POST['pt'];

$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
//$optNmbrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$arrBln=array(
"1"=>substr($_SESSION['lang']['jan'],0,3),
"2"=>substr($_SESSION['lang']['peb'],0,3),
"3"=>substr($_SESSION['lang']['mar'],0,3),
"4"=>substr($_SESSION['lang']['apr'],0,3),
"5"=>substr($_SESSION['lang']['mei'],0,3),
"6"=>substr($_SESSION['lang']['jun'],0,3),
"7"=>substr($_SESSION['lang']['jul'],0,3),
"8"=>substr($_SESSION['lang']['agt'],0,3),
"9"=>substr($_SESSION['lang']['sep'],0,3),
"10"=>substr($_SESSION['lang']['okt'],0,3),
"11"=>substr($_SESSION['lang']['nov'],0,3),
"12"=>substr($_SESSION['lang']['dec'],0,3),
);

if($pt!='%' and $kodeOrg!='%')
{
   $where=" kodeunit='".$kodeOrg."' and tahunbudget='".$thnBudget."'"; 
}
else if($pt!='%' and $kodeOrg=='%')
{
    $where=" kodeunit in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."') and tahunbudget='".$thnBudget."'"; 
}
else
{
    $where='1=1';
}   
    


if($proses=='excel')
{
    $bg=" bgcolor=#DEDEDE";
    $brdr=1;
     $tab.="<table>
             <tr><td colspan=5 align=left><font size=3>".strtoupper($_SESSION['lang']['lapKapital'])."</font></td></tr> 
             <tr><td colspan=5 align=left>".($pt=='%'?'All':$pt)."-".($optNm[$kodeOrg]=='%'?'All':$optNm[$kodeOrg])."</td></tr>   
             <tr><td>".$_SESSION['lang']['budgetyear']."</td><td colspan=2 align=left>".$thnBudget."</td></tr>   
             </table>";
}
else
{ 
    $bg="";
    $brdr=0;
}
 $tab.="<table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable style='width:2200px;'><thead>";
            $tab.="<tr class=rowheader>";
            $tab.="<td ".$bg.">".substr($_SESSION['lang']['nomor'],0,2)."</td>";
            $tab.="<td ".$bg.">".$_SESSION['lang']['unit']."</td>";
            $tab.="<td ".$bg.">".$_SESSION['lang']['lokasi']."</td>";
            $tab.="<td ".$bg.">".$_SESSION['lang']['jnsKapital']."</td>";
            $tab.="<td ".$bg.">".$_SESSION['lang']['keterangan']."</td>";
            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['jumlah']."</td>";
            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['hargasatuan']."</td>";
            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['total']."</td>";
            foreach($arrBln as $lstBln => $Bln)
            {
                $tab.="<td align=center ".$bg.">".$Bln."</td>";
            }
            $tab.="</tr></thead><tbody>";
            $sKodeOrg="select * from ".$dbname.".bgt_kapital where  ".$where." order by tahunbudget asc";
            //echo $sKodeOrg;
            $qKodeOrg=mysql_query($sKodeOrg) or die(mysql_error($conn));
            while($rKode=mysql_fetch_assoc($qKodeOrg))
            {
                    $no+=1;
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td>".$no."</td>";
                    $tab.="<td>".$rKode['kodeunit']."</td>";
                    $tab.="<td>".$rKode['lokasi']."</td>";
                    $tab.="<td>".$rKode['jeniskapital']."</td>";
                    $tab.="<td>".$rKode['keterangan']."</td>";
                    $tab.="<td align=right>".number_format($rKode['jumlah'],2)."</td>";
                    $tab.="<td align=right>".number_format($rKode['hargasatuan'],2)."</td>";
                    $tab.="<td align=right>".number_format($rKode['hargatotal'],2)."</td>";
                    for($a=1;$a<=12;$a++)
                    {
                        strlen($a)<2?$b="0".$a:$b=$a;
                        $tab.="<td align=right>".number_format($rKode['k'.$b],2)."</td>";
                        $totSebaran[$a]+=$rKode['k'.$b];
                    }
                    $tab.="</tr>";
                    $totJmlh+=$rKode['hargatotal'];
            }
            
            $tab.="</tbody><thead><tr class=rowheader>";
            $tab.="<td align=center align=right colspan=7>".$_SESSION['lang']['total']."</td>";
            $tab.="<td align=right >".number_format($totJmlh,2)."</td>";
            for($c=1;$c<=12;$c++)
            {
                $tab.="<td align=right>".number_format($totSebaran[$c],2)."</td>";
            }
            $tab.="</tr>";
            $tab.="</thead></table>";
			
			
	switch($proses)
        {
            case'preview':
                if($kodeOrg==''||$thnBudget=='')
                {
                    exit("Error:Field Tidak Boleh Kosong");
                }
            echo $tab;
            break;
            
            case'excel':
               
            if($kodeOrg==''||$thnBudget=='')
            {
                exit("Error:Field Tidak Boleh Kosong");
            }
           
           
            $tab.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
            $dte=date("YmdHis");
            $nop_="laporanKapital";
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
             if($kodeOrg==''||$thnBudget=='')
            {
                exit("Error:Field Tidak Boleh Kosong");
            }
      
           class PDF extends FPDF {
            function Header() {
            global $arrBln;
            global $kodeOrg;
			global $optNm;
            global $dbname;
			global $thnBudget;
           
            
            
  
         		$sAlmat="select namaorganisasi,alamat,telepon from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'";
				$qAlamat=mysql_query($sAlmat) or die(mysql_error());
				$rAlamat=mysql_fetch_assoc($qAlamat);
                
                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 10;
                if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
                $this->Image($path,$this->lMargin,$this->tMargin,70);	
                $this->SetFont('Arial','B',9);
                $this->SetFillColor(255,255,255);	
                $this->SetX(100);   
                $this->Cell($width-100,$height,$rAlamat['namaorganisasi'],0,1,'L');	 
                $this->SetX(100); 		
                $this->Cell($width-100,$height,$rAlamat['alamat'],0,1,'L');	
                $this->SetX(100); 			
                $this->Cell($width-100,$height,"Tel: ".$rAlamat['telepon'],0,1,'L');	
                $this->Line($this->lMargin,$this->tMargin+($height*4),
                $this->lMargin+$width,$this->tMargin+($height*4));
                $this->Ln();	
                $this->Ln();
		$this->Ln();
               
               
                $this->SetFont('Arial','B',11);
                $this->Cell($width,$height,strtoupper($_SESSION['lang']['lapKapital']).' BUDGET'.$optNm[$kodeOrg] ,0,1,'C');
                $this->Ln();	
                //$this->Cell(275,5,strtoupper($_SESSION['lang']['rprodksiPabrik']),0,1,'C');
                $this->Cell($width,$height,strtoupper($_SESSION['lang']['budgetyear']).' : '.$thnBudget,0,1,'C');
                $this->SetFont('Arial','',8);
                $this->Cell(850,$height,$_SESSION['lang']['tanggal'],0,0,'R');
                $this->Cell(10,$height,':','',0,0,'R');
                $this->Cell(70,$height,date('d-m-Y H:i'),0,1,'R');
                $this->Cell(850,$height,$_SESSION['lang']['page'],0,0,'R');
                $this->Cell(10,$height,':','',0,0,'R');
                $this->Cell(70,$height,$this->PageNo(),0,1,'R');
                 $this->Cell(850,$height,'User',0,0,'R');
                $this->Cell(10,$height,':','',0,0,'R');
                $this->Cell(70,$height,$_SESSION['standard']['username'],0,1,'R');

                $this->Ln();
                $this->Ln();
                $height = 15;
                $this->SetFillColor(220,220,220);
                $this->SetFont('Arial','B',7);
                $this->Cell(20,$height,substr($_SESSION['lang']['nomor'],0,2),1,0,'C',1);
                $this->Cell(30,$height,$_SESSION['lang']['unit'],1,0,'C',1);
                $this->Cell(20,$height,'TYP',1,0,'C',1);
                $this->Cell(140,$height,$_SESSION['lang']['keterangan'],1,0,'C',1);
				$this->Cell(20,$height,'JLH',1,0,'C',1);
				$this->Cell(60,$height,$_SESSION['lang']['hargasatuan'],1,0,'C',1);
				$this->Cell(60,$height,$_SESSION['lang']['total'],1,0,'C',1);
                $ar=1;
                foreach($arrBln as $lstBln => $Bln)
                {
                    if($ar!=12)
                    {
                    $this->Cell(50,$height,$Bln,1,0,'C',1);
                    }
                    else
                    {
                        $this->Cell(50,$height,$Bln,1,1,'C',1);
                    }
                    $ar+=1;
                }
          	}
			
			
            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
            }
            //================================

            $pdf=new PDF('L','pt','LEGAL');
            $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
            $height = 10;
            $pdf->AddPage();
            $pdf->SetFillColor(255,255,255);
            $pdf->SetFont('Arial','',7);

           
			$no=0;
			$sql="select * from ".$dbname.".bgt_kapital where  ".$where." order by tahunbudget asc";
			//echo $sql;
			$qDet=mysql_query($sql) or die(mysql_error());
			while($res=mysql_fetch_assoc($qDet))
			{
				$no+=1;
                $pdf->Cell(20,$height,$no,1,0,'C',1);
                                $pdf->Cell(30,$height,$res['kodeunit'],1,0,'L',1);
				$pdf->Cell(20,$height,$res['jeniskapital'],1,0,'L',1);
				$pdf->Cell(140,$height,$res['keterangan'],1,0,'L',1);
				$pdf->Cell(20,$height,$res['jumlah'],1,0,'R',1);
				$pdf->Cell(60,$height,number_format($res['hargasatuan'],2),1,0,'R',1);
				$pdf->Cell(60,$height,number_format($res['hargatotal'],2),1,0,'R',1);
				
                $ar=1;
                for($a=1;$a<=12;$a++)
                {
		if($ar!=12)
                    {
                        strlen($a)<2?$b="0".$a:$b=$a;
                        $pdf->Cell(50,$height,number_format($res['k'.$b],2),1,0,'R',1);
                        $totSebaran[$a]+=$rKode['k'.$b];
                    }
                    else 
                    {
                        strlen($a)<2?$b="0".$a:$b=$a;
                        $pdf->Cell(50,$height,number_format($res['k'.$b],2),1,1,'R',1);
                        $totSebaran[$a]+=$rKode['k'.$b];
                    }
                    $ar+=1;
               }
            }
			$pdf->SetFont('Arial','B',7);
			$pdf->SetFillColor(220,220,220);
			$pdf->Cell(290,$height,$_SESSION['lang']['total'],1,0,'C',1);
			$pdf->SetFont('Arial','',7);
			$pdf->Cell(60,$height,number_format($totJmlh,2),1,0,'C',1);
			
			//$totJmlh+=$rKode['hargatotal'];
			
			
			for($c=1;$c<=12;$c++)
            {
				$pdf->Cell(50,$height,number_format($totSebaran[$c],2),1,0,'R',1);
               // $tab.="<td align=right>".."</td>";
				
            }
	





            $pdf->Output();	
                
            break;
                
	
		
		
		
                
            default:
            break;
        }
	
?>
