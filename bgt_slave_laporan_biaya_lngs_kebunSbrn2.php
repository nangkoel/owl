<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$_POST['kdUnit_sebaran']==''?$kodeOrg=$_GET['kdUnit_sebaran']:$kodeOrg=$_POST['kdUnit_sebaran'];
$_POST['thnBudget_sebaran']==''?$thnBudget=$_GET['thnBudget_sebaran']:$thnBudget=$_POST['thnBudget_sebaran'];
$arrBln=array("1"=>"Jan","2"=>"Feb","3"=>"Mar","4"=>"Apr","5"=>"Mei","6"=>"Jun","7"=>"Jul","8"=>"Aug","9"=>"Sept","10"=>"Okt","11"=>"Nov","12"=>"Des");

$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optAkun=makeOption($dbname, 'keu_5akun', 'noakun,namaakun');
$where=" substring(kodeorg,1,4)='".$kodeOrg."' and  tahunbudget='".$thnBudget."' and tipebudget='ESTATE' and kodebudget!='UMUM'";
//luas TM
//ambil luas planted per tahuntanam
$str="select sum(hathnini) as luas,thntnm from ".$dbname.".bgt_blok where 
      kodeblok like '".$kodeOrg."%' and statusblok='TM'
      group by thntnm";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $dtJmlhLuastm[$thnBudget][$bar->thntnm]+=$bar->luas;
    $ttlLuastm+=$bar->luas;
}
//luas TBM
//ambil luas planted per tahuntanam
$str="select sum(hathnini) as luas,thntnm from ".$dbname.".bgt_blok where 
      kodeblok like '".$kodeOrg."%' and statusblok in ('TBM','TB')
      group by thntnm";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $dtJmlhLuastbm[$thnBudget][$bar->thntnm]+=$bar->luas;
    $ttlLuastbm+=$bar->luas;
}
 $ttlLuas=$ttlLuastbm+$ttlLuastm;

//jumlah pokok
 $str="select sum(pokokthnini) as luas,thntnm from ".$dbname.".bgt_blok where 
      kodeblok like '".$kodeOrg."%' and statusblok ='BBT'
      group by thntnm";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $dtJmlhLuasPkk[$thnBudget][$bar->thntnm]+=$bar->luas;
    $ttlLuasPkk+=$bar->luas;
}

//jumlah unplantend
 $str="select sum(hathnini) as luas,thntnm from ".$dbname.".bgt_blok where 
      kodeblok like '".$kodeOrg."%' and statusblok ='CADANGAN'
      group by thntnm";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $dtJmlhLuasCdgan[$thnBudget][$bar->thntnm]+=$bar->luas;
    $ttlLuasCdngn+=$bar->luas;
}
//jumlah LC
 $str="select sum(lcthnini) as luas,thntnm from ".$dbname.".bgt_blok where 
      kodeblok like '".$kodeOrg."%' and lcthnini!=''
      group by thntnm";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $dtJmlhLuasLc[$thnBudget][$bar->thntnm]+=$bar->luas;
    $ttlLuasLc+=$bar->luas;
}

 if($kodeOrg==''||$thnBudget=='')
{
    exit("Error:Field Tidak Boleh Kosong");
}
//$sThnTnm="select sum(rupiah) as stahun,noakun,tahunbudget,sum(rp01) as rp01,sum(rp02) as rp02,sum(rp03) as rp03,sum(rp04) as rp04,sum(rp05) as rp05,sum(rp06) as rp06,sum(rp07) as rp07 ,sum(rp08) as rp08 
//    ,sum(rp09) as rp09,sum(rp10) as rp10,sum(rp11) as rp11,sum(rp12) as rp12
$sThnTnm="select sum(rupiah) as rupiah ,noakun,tahunbudget,sum(rp01) as rp01, sum(rp02) as rp02,sum(rp03) as rp03,sum(rp04) as rp04,sum(rp05) as rp05, sum(rp06) as rp06, sum(rp07) as rp07, sum(rp08) as rp08, sum(rp09) as rp09,sum(rp10) as rp10,sum(rp11) as rp11 ,sum(rp12) as rp12
    from ".$dbname.".bgt_budget_detail where  ".$where." and kodebudget!='UMUM' and tipebudget='ESTATE' group by tahunbudget,noakun order by noakun asc";
//echo $sThnTnm;
$qThnTnm=mysql_query($sThnTnm) or die(mysql_error());
$resCheck=mysql_num_rows($qThnTnm);
if($resCheck==0)
{
   exit("Error: ".$optNm[$kodeOrg].", Belum Melakukan Proses Budget Di tahun ".$thnBudget."");
}   
while($rThnTnm=  mysql_fetch_assoc($qThnTnm))
{
    $dtSetaun[$rThnTnm['tahunbudget']][$rThnTnm['noakun']]=$rThnTnm['rupiah']; 
    for($a=1;$a<=12;$a++)
    {
        if(strlen($a)<2)
        {
            $b="0".$a;
        }
        else
        {
            $b=$a;
        }
        $dtBlnan[$rThnTnm['tahunbudget']][$rThnTnm['noakun']][$a]+=$rThnTnm['rp'.$b];
    }
}
$sThnTnm2="select  distinct noakun from ".$dbname.".bgt_budget_detail where  ".$where." and kodebudget!='UMUM' order by noakun asc";
$qThnTnm2=mysql_query($sThnTnm2) or die(mysql_error());
while($rThnTnm2=  mysql_fetch_assoc($qThnTnm2))
{
    $dtNoakun[]=$rThnTnm2['noakun']; 
}
if($_GET['proses']=='excel')
{
$bg=" bgcolor=#DEDEDE";
$brdr=1;
$tab="<table>
 <tr><td colspan=5 align=left><font size=5>".strtoupper($_SESSION['lang']['lapLangsung'])."</font></td></tr> 
 <tr><td colspan=5 align=left>".$optNm[$kodeOrg]."</td></tr>   
 <tr><td>".$_SESSION['lang']['budgetyear']."</td><td colspan=2 align=left>".$thnBudget."</td></tr>   
 </table>";
}
else
{
   $bg=" ";
   $brdr=0; 
}

//get data
 
        
            $tab.="<table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable><thead>";
            $tab.="<tr class=rowheader>";
            $tab.="<td  align=center ".$bg.">".$_SESSION['lang']['noakun']."</td>";
            $tab.="<td  align=center ".$bg.">".$_SESSION['lang']['namaakun']."</td>";
            $tab.="<td  align=center ".$bg.">".$_SESSION['lang']['luas']."</td>";
            $tab.="<td  align=center ".$bg.">".$_SESSION['lang']['setahun']."</td>";
            foreach($arrBln as $listBulan =>$isiBLn)
            {
               $tab.="<td  align=center ".$bg.">".$isiBLn."</td>"; 
            }
            $tab.="</tr><tbody>";
            foreach ($dtNoakun as $listNoakun)
            {
                $tab.="<tr class=rowcontent>";
                $tab.="<td>".$listNoakun."</td>";
                $tab.="<td>".$optAkun[$listNoakun]."</td>";
                $tab.="<td align=right>".number_format($dtSetaun[$thnBudget][$listNoakun],2)."</td>";
                foreach($arrBln as $listBulan =>$isiBLn)
                {
                  $tab.="<td align=right>".number_format($dtBlnan[$thnBudget][$listNoakun][$listBulan],2)."</td>";
                  $totSbrn[$listBulan]+=$dtBlnan[$thnBudget][$listNoakun][$listBulan];
                }
                $tab.="</tr>";
                $grnTotal+=$dtSetaun[$thnBudget][$listNoakun];
            }
            $tab.="<tr class=rowcontent>";
            $tab.="<td colspan=2>".$_SESSION['lang']['total']."</td>";
            
            $tab.="<td align=right>".number_format($grnTotal,2)."</td>";
            foreach($arrBln as $listBulan =>$isiBLn)
            {
              $tab.="<td align=right>".number_format($totSbrn[$listBulan],2)."</td>";
            }
            $tab.="</tr>";
         
            $tab.="</tbody></table>";
            
	switch($proses)
        {
            case'preview':  
            echo $tab;
            break;
            case'excel':
             $tab.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
            $dte=date("YmdHis");
            $nop_="lapKebunBiayaLangsung_sbrn_".$dte;
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
            global $dtNoakun;
            global $dbname;
            global $optAkun;
           
            
            
  
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
                $this->Cell($width,$height,strtoupper($_SESSION['lang']['lapLangsung']),0,1,'C');
                $this->Ln();	
                //$this->Cell(275,5,strtoupper($_SESSION['lang']['rprodksiPabrik']),0,1,'C');
                $this->Cell($width,$height,$_SESSION['lang']['unit'].' : '.$optNm[$kodeOrg],0,1,'C');
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
                $this->Cell(58,$height,$_SESSION['lang']['noakun'],1,0,'C',1);
                $this->Cell(150,$height,$_SESSION['lang']['namaakun'],1,0,'C',1);
                $this->Cell(80,$height,$_SESSION['lang']['setahun'],1,0,'C',1);
                $ar=1;
                foreach($arrBln as $listBulan =>$isiBLn)
                {
                    if($ar!=12)
                    {
                    $this->Cell(55,$height,$isiBLn,1,0,'C',1);
                    }
                    else
                    {
                        $this->Cell(55,$height,$isiBLn,1,1,'C',1);
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
            $pdf->SetFont('Arial','B',6);

           
            foreach ($dtNoakun as $listNoakun)
            {
                $pdf->Cell(58,$height,$listNoakun,1,0,'L',1);
                $pdf->Cell(150,$height,$optAkun[$listNoakun],1,0,'L',1);
                $pdf->Cell(80,$height,number_format($dtSetaun[$thnBudget][$listNoakun],2),1,0,'R',1);
                
                $ar=1;
                foreach($arrBln as $listBulan =>$isiBLn)
                {
                    if($ar!=12)
                    {
                        $pdf->Cell(55,$height,number_format($dtBlnan[$thnBudget][$listNoakun][$listBulan],2),1,0,'R',1);
                    }
                    else
                    {
                        $pdf->Cell(55,$height,number_format($dtBlnan[$thnBudget][$listNoakun][$listBulan],2),1,1,'R',1);
                    }
                    $totSbrn[$listBulan]+=$dtBlnan[$thnBudget][$listNoakun][$listBulan];
                     $ar+=1;
               }
              $grnTotal+=$dtSetaun[$thnBudget][$listNoakun];
            }
            
            $pdf->Cell(208,$height,$_SESSION['lang']['total'],1,0,'L',1);
            $pdf->Cell(80,$height,number_format($grnTotal,2),1,0,'R',1);
            foreach($arrBln as $listBulan =>$isiBLn)
            {
                    if($ar5!=12)
                    {
                        $pdf->Cell(55,$height,number_format($totSbrn[$listBulan],2),1,0,'R',1);
                    }
                    else
                    {
                        $pdf->Cell(55,$height,number_format($totSbrn[$listBulan],2),1,1,'R',1);
                    }
                     $ar5+=1;
            }
            $pdf->Output();	
                
            break;
                
            default:
            break;
        }
	
?>
