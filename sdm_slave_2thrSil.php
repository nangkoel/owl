<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$kdOrg=$_POST['kdOrg'];
$per=$_POST['per'];
$tk=$_POST['tk'];
$agama=$_POST['agama'];
$tahun=$_POST['tahun'];
$tgl=  tanggaldgnbar($_POST['tgl']);

if($proses=='excel' || $proses=='pdf')
{
    $tahun=$_POST['tahun'];
    $kdOrg=$_GET['kdOrg'];
    $per=$_GET['per'];
    $tk=$_GET['tk'];
    $agama=$_GET['agama'];
    $tahun=$_GET['tahun'];
    $tgl=  tanggaldgnbar($_GET['tgl']);
}



//$tgl=explode("-",$tgl);
//$tglAbis=$tgl[2]."-".$tgl[1]."-".$tgl[0];



$nmTk=makeOption($dbname,'sdm_5tipekaryawan','id,tipe');
$nmBag=makeOption($dbname,'sdm_5departemen','kode,nama');
$nmJab=makeOption($dbname,'sdm_5jabatan','kodejabatan,namajabatan');
$nmOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');



if ($proses == 'excel') 
    {
        $stream = "<table class=sortable cellspacing=1 border=1>";
    } else 
    {
        $stream = "<table class=sortable cellspacing=1>";
    }


    $stream.="<thead class=rowheader>
        <tr class=rowheader>
            <td bgcolor=#CCCCCC rowspan=2 align=center>".$_SESSION['lang']['nourut']."</td>
            <td bgcolor=#CCCCCC rowspan=2 align=center>".$_SESSION['lang']['nik']."</td> 
            <td bgcolor=#CCCCCC rowspan=2 align=center>".$_SESSION['lang']['namakaryawan']."</td>
            <td bgcolor=#CCCCCC rowspan=2 align=center>".$_SESSION['lang']['tipekaryawan']."</td>
            <td bgcolor=#CCCCCC rowspan=2 align=center>".$_SESSION['lang']['lokasitugas']."</td>
            <td bgcolor=#CCCCCC rowspan=2 align=center>".$_SESSION['lang']['subbagian']."</td>
            <td bgcolor=#CCCCCC rowspan=2 align=center>".$_SESSION['lang']['bagian']."</td>
            <td bgcolor=#CCCCCC rowspan=2 align=center>".$_SESSION['lang']['jabatan']."</td>
            <td bgcolor=#CCCCCC colspan=2 align=center>".$_SESSION['lang']['tanggal']."</td>   
            <td bgcolor=#CCCCCC colspan=2 align=center>".$_SESSION['lang']['masakerja']."</td>     
            <td bgcolor=#CCCCCC rowspan=2 align=center>".$_SESSION['lang']['gajipokok']."</td> 
            <td bgcolor=#CCCCCC rowspan=2 align=center>".$_SESSION['lang']['thr']."</td>
        </tr>
        <tr>
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['tmk']."</td>
            <td bgcolor=#CCCCCC align=center>Tanggal Pengangkatan</td> 
            <td bgcolor=#CCCCCC  align=center>".$_SESSION['lang']['hari']."</td> 
            <td bgcolor=#CCCCCC align=center>".$_SESSION['lang']['tahun']."</td>
        </tr>
        <tr>";
       $stream.="</thead>";

        /*$iKar="select karyawanid,namakaryawan,nik,tipekaryawan,kodejabatan,lokasitugas,subbagian,bagian,tanggalmasuk,tanggalpengangkatan"
               . " from ".$dbname.".datakaryawan where  agama='".$agama."' and tipekaryawan='".$tk."' and "
               . " lokasitugas='".$kdOrg."' and (tanggalkeluar>'".$tglMulai."' or tanggalkeluar='0000-00-00') ";*/
        
       /*$iKar="select a.*,b.karyawanid,b.namakaryawan,b.nik,b.tipekaryawan,b.kodejabatan,b.lokasitugas,b.subbagian,"
               . " b.bagian,b.tanggalmasuk,b.tanggalpengangkatan"
               . " from ".$dbname.".sdm_gaji a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid"
               . " where a.idkomponen in ('28','71') and a.periodegaji='".$per."' and a.kodeorg='".$kdOrg."' and b.agama='".$agama."' "
               . " and b.tipekaryawan='".$tk."' ";*/
       
       $iKar="select a.*,b.karyawanid,b.namakaryawan,b.nik,b.tipekaryawan,b.kodejabatan,b.lokasitugas,b.subbagian,"
               . " b.bagian,b.tanggalmasuk,b.tanggalpengangkatan"
               . " from ".$dbname.".sdm_gaji a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid"
               . " where a.idkomponen='".$tk."' and a.periodegaji='".$per."' and a.kodeorg='".$kdOrg."' and b.agama='".$agama."' ";
       
       //echo $iKar;
       //and (b.tanggalkeluar>'".$tglMulai."' or b.tanggalkeluar='0000-00-00') 
       $nKar=mysql_query($iKar) or die (mysql_error($conn));
       while($dKar=  mysql_fetch_assoc($nKar))
       {
            $kar[$dKar['karyawanid']]=$dKar['karyawanid'];
            $nama[$dKar['karyawanid']]=$dKar['namakaryawan'];
            $tk[$dKar['karyawanid']]=$dKar['tipekaryawan'];
            $nik[$dKar['karyawanid']]=$dKar['nik'];
            $lokasi[$dKar['karyawanid']]=$dKar['lokasitugas'];
            $subBag[$dKar['karyawanid']]=$dKar['subbagian'];
            $jab[$dKar['karyawanid']]=$dKar['kodejabatan'];
            $bag[$dKar['karyawanid']]=$dKar['bagian'];
            $tglMasuk[$dKar['karyawanid']]=$dKar['tanggalmasuk'];
            $tglAngkat[$dKar['karyawanid']]=$dKar['tanggalpengangkatan'];
            $thr[$dKar['karyawanid']]=$dKar['jumlah'];
       }

       $iGaji="select karyawanid,jumlah from ".$dbname.".sdm_5gajipokok where tahun='".$tahun."' and idkomponen=1 ";
       $nGaji=  mysql_query($iGaji) or die (mysql_error($conn));
       while($dGaji=  mysql_fetch_assoc($nGaji))
       {
           $gaji[$dGaji['karyawanid']]=$dGaji['jumlah'];
       }
       
       
       foreach($kar as $karId)
       {
           
           if($tglAngkat[$karId]=='0000-00-00')//bl =3 ht=4
           {
               $selisihHari=days_360($tgl,$tglMasuk[$karId]);
           }
           else
           {
              $selisihHari=days_360($tgl,$tglAngkat[$karId]);
           }
            
            
           $masaKerja=number_format($selisihHari/360,5);
            
            $no+=1;
            $stream.="<tr class=rowcontent>";
                $stream.="<td>".$no."</td>";
                $stream.="<td>".$nik[$karId]."</td>";
                $stream.="<td>".$nama[$karId]."</td>";
                $stream.="<td>".$nmTk[$tk[$karId]]."</td>";
                $stream.="<td>".$lokasi[$karId]."</td>";
                $stream.="<td>".$subBag[$karId]."</td>";
                $stream.="<td>".$nmJab[$jab[$karId]]."</td>";
                $stream.="<td>".$nmBag[$bag[$karId]]."</td>";
                $stream.="<td>".tanggalnormal($tglMasuk[$karId])."</td>";
                $stream.="<td>".tanggalnormal($tglAngkat[$karId])."</td>";
                $stream.="<td align=right>".$selisihHari."</td>";
                $stream.="<td align=right>".$masaKerja."</td>";
                $stream.="<td align=right>".number_format($gaji[$karId])."</td>";
                $stream.="<td align=right >".number_format($thr[$karId])."</td>";
            $stream.="</tr>";    
       }

$stream.="</table>";	  
$stream.="<tbody></table>";





switch($proses)
{
  
    
######PREVIEW
	case 'preview':
		echo $stream;
         break;

######EXCEL	
	case 'excel':
		//$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="Laporan_thr_".$tk._.$kdOrg."_".$tgl[2];
		if(strlen($stream)>0)
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
			if(!fwrite($handle,$stream))
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

        class PDF extends FPDF
        {
            var $col=0;
            var $dbname;

            function SetCol($col)
            {
                //Move position to a column
                
                $this->col=$col;
                $x=10+$col*100;
                $this->SetLeftMargin($x);
                $this->SetX($x);
            }

            function AcceptPageBreak()
            { 
                if($this->col<1)
                {
                    //Go to next column
                    $this->SetCol($this->col+1);
                    $this->SetY(10);
                    return false;
                }
                else
                {
                    //Go back to first column and issue page break
                    $this->SetCol(0);
                    return true;
                }
            }

            function Header()
            {    
                 //   $this->lMargin=5;  
            }

            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',5);
               // $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
        }


        $pdf=new PDF('P','mm','A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial','',5);

        $heigh=5;
        $no=0;
        $sizeFont=10;
        
        
        if($agama=='Islam')
        {
            $judul='IDUL FITRI';
        }
        else if ($agama=='Protestan' || $agama=='Katolik')
        {
            $judul='NATAL';
        }
        else if ($agama=='Hindu')
        {
            $judul='NYEPI';
        }
        else if ($agama=='Budha')
        {
            $judul='WAISAK';
        }
        else if ($agama=='Konghucu')
        {
            $judul='IMLEK';
        }
        
       
        
        foreach($kar as $karId)
        {
            
            if($subBag[$karId]=='')
            {
                $subBag[$karId]=$lokasi[$karId];
            }
            
            
            
           if($tglAngkat[$karId]=='0000-00-00')//bl =3 ht=4
           {
               $selisihHari=days_360($tgl,$tglMasuk[$karId]);
           }
           else
           {
               $selisihHari=days_360($tgl,$tglAngkat[$karId]);
           }
            
            
           $masaKerja=number_format($selisihHari/360,5);
            
            
            
            
            
            
            $no+=1;
            $awalX=$pdf->GetX();
            $awalY=$pdf->GetY();
            
            $pdf->Rect($awalX-2,$awalY-5, 95, 125);
            
            //$pdf->Image('images/logo.jpg',$pdf->GetX(),$pdf->GetY(),10);
            //$pdf->SetX($pdf->getX()+10);
            $pdf->SetFont('Arial','B',$sizeFont);	
            $pdf->Cell(75,$heigh,$_SESSION['org']['namaorganisasi'],0,1,'L');
            $pdf->SetFont('Arial','',$sizeFont);	
            $pdf->Cell(80,$heigh,'Kebun Nunukan - Kalimantan Timur','',0,'L');
            $pdf->Cell(10,$heigh,$no,'',1,'R');
            $pdf->Cell(80,$heigh,'SLIP THR '.$judul,'',1,'C');
            $pdf->Cell(80,$heigh,'TAHUN '.$tahun,'',0,'C');
            $pdf->Ln(10);

            $pdf->Cell(30,$heigh,'Nama','',0,'L');
            $pdf->Cell(40,$heigh,$nama[$karId],'',1,'L');
            
            $pdf->Cell(30,$heigh,'Nik','',0,'L');
            $pdf->Cell(40,$heigh,$nik[$karId],'',1,'L');

            $pdf->Cell(30,$heigh,'Jabatan','',0,'L');
            $pdf->Cell(40,$heigh,$nmJab[$jab[$karId]],'',1,'L');

            $pdf->Cell(30,$heigh,'Divis / Afdeling','',0,'L');
            $pdf->Cell(40,$heigh,$nmOrg[$subBag[$karId]],'',1,'L');

            $pdf->Cell(10,$heigh,'','',0,'L');
            $pdf->Cell(30,$heigh,'Gaji Pokok','',0,'L');
            $pdf->Cell(20,$heigh,number_format($gaji[$karId]),'',0,'R');
            $pdf->Cell(10,$heigh,'','',1,'L');

            $pdf->Cell(10,$heigh,'','',0,'L');
            $pdf->Cell(30,$heigh,'Masa Kerja','',0,'L');
            $pdf->Cell(20,$heigh,number_format($masaKerja,2),'B',0,'R');
            $pdf->Cell(10,$heigh,'','',1,'L');
            
            $pdf->Cell(10,$heigh,'','',0,'L');
            $pdf->Cell(30,$heigh,'THR','',0,'L');
            $pdf->Cell(20,$heigh,number_format($thr[$karId]),'',0,'R');
            $pdf->Cell(10,$heigh,'','',0,'L');
            
            $pdf->Ln(10);
              
//            $pdf->SetFont('Arial','B',$sizeFont);
//            $pdf->Cell(10,$heigh,'','',0,'L');
//            $pdf->Cell(30,$heigh,'Pembulatan','',0,'L');
//            $pdf->Cell(20,$heigh,number_format($thr[$karId]),'',0,'R');
//            $pdf->Cell(10,$heigh,'','',0,'L');    
            
            $pdf->SetFont('Arial','',$sizeFont);
            
            $pdf->Ln(10);

            $pdf->Cell(50,$heigh,'','',0,'L');
            $pdf->Cell(40,4,'Sebakis,...................... '.$tahun,0,1,'R');
            
            $pdf->Cell(30,4,'Dibayar Oleh',0,0,'L');
            $pdf->Cell(20,$heigh,'','',0,'L');
            $pdf->Cell(30,4,'Diterima Oleh,',0,1,'L');

             $pdf->Ln(20);
             
          
             
             
            $pdf->Cell(30,$heigh,'Leon','B',0,'L');
            $pdf->Cell(20,$heigh,'','',0,'L');
            $pdf->Cell(30,$heigh,$nama[$karId],'B',1,'L');
             
             

            $pdf->Cell(30,$heigh,'Asst. Payroll','',0,'L');
           $pdf->Cell(20,$heigh,'','',0,'L');
            $pdf->Cell(33,$heigh,'Karyawan','',0,'L');

            $pdf->Ln(30);	

            if($pdf->GetY()>225 and $pdf->col<1)
                    $pdf->AcceptPageBreak();
            if ($pdf->GetY()>225 and $pdf->col>0)
               {
                    $r=275-$pdf->GetY();
                    $pdf->Cell(80,$r,'',0,1,'L');
               }
            $pdf->cell(-1,3,'',0,0,'L');	
    }


    $pdf->Output();

break;  
default;


}    
?>