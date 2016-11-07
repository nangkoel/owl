<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

if(isset($_POST['proses']))
{
	$proses=$_POST['proses'];
}
else
{
	$proses=$_GET['proses'];
}
//$arr="##unitId##tgl1##tgl2";

$optNmOrang=makeOption($dbname, 'vhc_5operator', 'karyawanid,nama');
$optNmOrg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optInduk=makeOption($dbname, 'organisasi','kodeorganisasi,induk');
$optNmBrg=makeOption($dbname, 'log_5masterbarang','kodebarang,namabarang');

$_POST['unitId']==''?$kdUnit=$_GET['unitId']:$kdUnit=$_POST['unitId'];
$_POST['tgl1']==''?$tgl1=tanggalsystem($_GET['tgl1']):$tgl1=tanggalsystem($_POST['tgl1']);
$_POST['tgl2']==''?$tgl2=tanggalsystem($_GET['tgl2']):$tgl2=tanggalsystem($_POST['tgl2']);
$alasan=$_POST['alasan'];
$notransaksi=$_POST['notransaksi'];
$tgl=tanggalsystem($_POST['tgl']);
$jumlah=$_POST['jumlah'];
$kdOrg=$_POST['kdOrg'];

$brdr=0;
$bgcoloraja='';

if($proses=='excel')
{
    $bgcoloraja="bgcolor=#DEDEDE ";
    $brdr=1;
}
if($proses!='update')
{
 $tab.="<table cellspacing=1 cellpadding=1 border=".$brdr." class=sortable>
	<thead class=rowheader>";
        $tab.="<tr>";
        $tab.="<td>".$_SESSION['lang']['notransaksi']."</td>";
        $tab.="<td>".$_SESSION['lang']['tanggal']."</td>";
        $tab.="<td>".$_SESSION['lang']['kodeorganisasi']."</td>";
        $tab.="<td>".$_SESSION['lang']['pokok']."</td>";
        $tab.="<td>Descrtiption</td>";
        if($proses!='excel')
        {
        $tab.="<td>Action</td>";
        }
        $tab.="</tr></thead><tbody>";
//       
        $sDatab="select distinct b.kodeorg,b.notransaksi,a.tanggal from ".$dbname.".kebun_prestasi b left join ".$dbname.".kebun_aktifitas a
            on b.notransaksi=a.notransaksi where kodekegiatan='126120101' and 
            substr(b.kodeorg,1,4)='".$kdUnit."' and a.tanggal between '".$tgl1."' and '".$tgl2."'";
        $qDataB=mysql_query($sDatab) or die(mysql_error($conn));
        while($rDataB=mysql_fetch_assoc($qDataB))
        {
            $dtNotrans[$rDataB['notransaksi']]=$rDataB['notransaksi'];
            $dtKdorg[$rDataB['notransaksi']]=$rDataB['kodeorg'];
            $dtTgl[$rDataB['notransaksi']]=$rDataB['tanggal'];
        }
         $sData="select distinct * from ".$dbname.".kebun_sisip 
         where substr(kodeorg,1,4)='".$kdUnit."' and tanggal between '".$tgl1."' and '".$tgl2."'";
        $qData=mysql_query($sData) or die(mysql_error($sData));
        $jmlhRow=mysql_num_rows($qData);
        while($rData=mysql_fetch_assoc($qData))
        {
            $dtNotrans[$rData['notransaksi']]=$rData['notransaksi'];
            $dtKdorg[$rData['notransaksi']]=$rData['kodeorg'];
            $dtTgl[$rData['notransaksi']]=$rData['tanggal'];
            $dtJumlah[$rData['notransaksi']]=$rData['jumlah'];
            $dtPenyebab[$rData['notransaksi']]=$rData['penyebab'];
        }
        $jmlhRow=count($dtNotrans);
        if($jmlhRow!=0)
        {
            foreach($dtNotrans as $lstNotrans)
            {
                $no+=1;
                $tab.="<tr class=rowcontent>";
                $tab.="<td>".$lstNotrans."</td>";
                $tab.="<td id=tgl_".$no.">".tanggalnormal($dtTgl[$lstNotrans])."</td>";
                $tab.="<td id=kdOrg_".$no.">".$dtKdorg[$lstNotrans]."</td>";
                if($proses!='excel')
                {
                    
                $tab.="<td><div id='jmlhDt_".$no."' style=display:block>".$dtJumlah[$lstNotrans]."</div>
                        <div id='jmlhForm_".$no."' style=display:none>
                        <input type=text class=myinputtextnumber id='dtJmlh_".$no."' value='".$dtJumlah[$lstNotrans]."' onkeypress='return angka_doang(event)' style=width:50px; /></div>
                       </td>";
                $tab.="<td><div id=lsdDt_".$no."  style=display:block>".$dtPenyebab[$lstNotrans]."</div>
                            <div style=display:none id=sbsFrm_".$no."><textarea id='sebabDt_".$no."'>".$dtPenyebab[$lstNotrans]."</textarea></div></td>";

                    if(substr($dtKdorg[$lstNotrans],0,4)==$_SESSION['empl']['lokasitugas'])
                    {
                    $tab.="<td>
                        <div id=editBtn_".$no." style=display:block><button onclick=\"editData('".$no."','".$jmlhRow."')\" class=\"mybutton\">Edit</button></div>";
                    $tab.="<div id=insertBtn_".$no." style=display:none><button onclick=\"saveData('".$lstNotrans."','".$no."')\" class=\"mybutton\">".$_SESSION['lang']['save']."</button></div>
                        </td>";
                    }
                    else
                    {
                        $tab.="<td>&nbsp;</td>";
                    }
                }
                elseif($proses=='excel')
                {
                $tab.="<td><div id='jmlhDt_".$no."' style=display:block>".$dtJumlah[$lstNotrans]."</div>
                       </td>";
                $tab.="<td><div id=lsdDt_".$no."  style=display:block>".$dtPenyebab[$lstNotrans]."</div>
                            </td>";
                    $tab.="<td>".$rData['penyebab']."</td>";
                }
                $tab.="</tr>";
            }
        }
        else
        {
            $tab.="<tr class=rowcontent><td  colspan=5>".$_SESSION['lang']['dataempty']."</td></tr>";
        }
        $tab.="</tbody></table>";
	
}   
switch($proses)
{
	case'preview':
 
	echo $tab;
	break;
        case'excel':
        $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
        $dte=date("Hms");
        $nop_="sisipan_".$dte;
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
            global $periode;
            global $dataAfd;
            global $kdUnit;
            global $optNmOrg;  
            global $dbname;
            global $thn;
            global $kbnSndri;
            global $lstPlnggan;
            global $tot;

           
               
                $this->SetFont('Arial','B',8);
                $this->Cell($width,$height,strtoupper($_SESSION['lang']['riwayatsisipan']),0,1,'L');
                 $tinggiAkr=$this->GetY();
                $ksamping=$this->GetX();
                $this->SetY($tinggiAkr+20);
                $this->SetX($ksamping);
                $this->Cell($width,$height,$_SESSION['lang']['unit'].' : '.$optNmOrg[$kdUnit],0,1,'L');
                $this->Cell(790,$height,' ',0,1,'R');
                
                $height = 15;
                $this->SetFillColor(220,220,220);
                $this->SetFont('Arial','B',7);
                
                $tinggiAkr=$this->GetY();
                $ksamping=$this->GetX();
                $this->SetY($tinggiAkr+20);
                $this->SetX($ksamping);

                $this->Cell(80,$height,$_SESSION['lang']['notransaksi'],TBLR,0,'C',1);
                $this->Cell(50,$height,$_SESSION['lang']['tanggal'],TBLR,0,'C',1);
                $this->Cell(45,$height,$_SESSION['lang']['pokok'],TBLR,0,'C',1);
                $this->Cell(320,$height,"Description",TBLR,1,'C',1);
               

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
            $height = 10;
            $tnggi=$jmlHari*$height;
            $pdf->AddPage();
            $pdf->SetFillColor(255,255,255);
            $pdf->SetFont('Arial','',6);
            $sData="select distinct * from ".$dbname.".kebun_sisip 
            where substr(kodeorg,1,4)='".$kdUnit."' and tanggal between '".$tgl1."' and '".$tgl2."'";
            $qData=mysql_query($sData) or die(mysql_error($sData));
            $jmlhRow=mysql_num_rows($qData);
            if($jmlhRow!=0)
            {
                while($rData=mysql_fetch_assoc($qData))
                {
                $pdf->Cell(80,$height,$rData['notransaksi'],TBLR,0,'L',1);
                $pdf->Cell(50,$height,tanggalnormal($rData['tanggal']),TBLR,0,'C',1);
                $pdf->Cell(45,$height,number_format($rData['jumlah'],0),TBLR,0,'R',1);
                $pdf->MultiCell(320,$height,$rData['penyebab'],TBLR,1,'J',1);
                }
            }

           
            $pdf->Output();
            break;
	case'update':
            $sDel="delete from ".$dbname.".kebun_sisip where notransaksi='".$notransaksi."'";
            if(mysql_query($sDel))
            {
                $sInsert="insert into ".$dbname.".kebun_sisip (notransaksi,tanggal,kodeorg,jumlah,penyebab) values 
                          ('".$notransaksi."','".$tgl."','".$kdOrg."','".$jumlah."','".$alasan."')";
                if(!mysql_query($sInsert))
                {
                     echo"DB:error".mysql_error($conn);
                }
            }
            else
            {
                $sInsert="insert into ".$dbname.".kebun_sisip (notransaksi,tanggal,kodeorg,jumlah,penyebab) values 
                          ('".$notransaksi."','".$tgl."','".$kdOrg."','".$jumlah."','".$alasan."')";
                if(!mysql_query($sInsert))
                {
                     echo"DB:error".mysql_error($conn);
                }
            }
            
//            $sUpDt="update ".$dbname.".kebun_sisip set penyebab='".$alasan."' where notransaksi='".$notransaksi."'";
//            if(!mysql_query($sUpDt))
//                echo"DB:error".mysql_error($conn);
//            
        break;
	default:
	break;
}
      
?>