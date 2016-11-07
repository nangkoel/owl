<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$hargasatuan=makeOption($dbname,'pmn_kontrakjual','nokontrak,hargasatuan');

$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
$_POST['notrans']==''?$nokontrak=$_GET['notrans']:$nokontrak=$_POST['notrans'];
$periode=$_POST['periode'];
$kdBrg=$_POST['kdBrg'];
$kdBrg2=$_POST['kdBrg2'];
$_POST['thn']==''?$thn=$_GET['thn']:$thn=$_POST['thn'];
$_POST['kdBrg3']==''?$kdBrg3=$_GET['kdBrg3']:$kdBrg3=$_POST['kdBrg3'];
$optNmBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');

switch($proses)
{
        case'preview':
        echo"<table class=sortable cellspacing=1 border=0><thead><tr class=rowheader>
        <td>".$_SESSION['lang']['NoKontrak']."</td>
        <td>".$_SESSION['lang']['komoditi']."</td>
        <td>".$_SESSION['lang']['tglKontrak']."</td>
        <td>".$_SESSION['lang']['Pembeli']."</td>
        <td>".$_SESSION['lang']['estimasiPengiriman']."</td>
        <td>".$_SESSION['lang']['jmlhBrg']."</td>
		<td>".$_SESSION['lang']['hargasatuan']."</td>
		<td>".$_SESSION['lang']['total']."</td>
        <td>".$_SESSION['lang']['pemenuhan']."</td>
		
        <td>".$_SESSION['lang']['beratBersih']." ".$_SESSION['lang']['Pembeli']."</td>
        <td>".$_SESSION['lang']['sisa']."</td>
        </tr></thead><tbody>
        ";
        if($kdBrg!='')
        {
                $where=" and kodebarang='".$kdBrg."'";
        }
        $sql="select nokontrak,kodebarang,tanggalkontrak,koderekanan,tanggalkirim,sdtanggal,kuantitaskontrak,hargasatuan from ".$dbname.".pmn_kontrakjual where tanggalkontrak like '%".$periode."%' ".$where." order by tanggalkontrak desc";
        //exit("Error".$sql);
        $query=mysql_query($sql) or die(mysql_error());
        while($res=mysql_fetch_assoc($query))
        {
                $sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$res['kodebarang']."'";
                $qBrg=mysql_query($sBrg) or die(mysql_error());
                $rBrg=mysql_fetch_assoc($qBrg);

                $sCust="select namacustomer  from ".$dbname.".pmn_4customer where kodecustomer='".$res['koderekanan']."'";
                $qCust=mysql_query($sCust) or die(mysql_error());
                $rCust=mysql_fetch_assoc($qCust);

                $sTimb="select sum(beratbersih) as jumlahTotal,sum(kgpembeli) as jumlahKgpem   from ".$dbname.".pabrik_timbangan where nokontrak='".$res['nokontrak']."'";
                $qTimb=mysql_query($sTimb) or die(mysql_error());
                $rTimb=mysql_fetch_assoc($qTimb);
                $arr="nokontrak"."##".$res['nokontrak'];
                $sisaBarang=$res['kuantitaskontrak']-$rTimb['jumlahTotal'];
                echo"<tr class=rowcontent onclick=\"zDetail(event,'pmn_slave_laporanPemenuhanKontrak.php','".$arr."')\" style=\"cursor:pointer;\">
                <td style=\"cursor:pointer;\">".$res['nokontrak']."</td>
                <td style=\"cursor:pointer;\">".$rBrg['namabarang']."</td>
                <td style=\"cursor:pointer;\">".tanggalnormal($res['tanggalkontrak'])."</td>
                <td style=\"cursor:pointer;\">".$rCust['namacustomer']."</td>
                <td style=\"cursor:pointer;\" nowrap>".tanggalnormal($res['tanggalkirim'])." s.d ".tanggalnormal($res['sdtanggal'])."</td>
                <td align=right style=\"cursor:pointer;\">".number_format($res['kuantitaskontrak'])."</td>
				
				 <td align=right style=\"cursor:pointer;\">".number_format($res['hargasatuan'])."</td>
				 <td align=right style=\"cursor:pointer;\">".number_format($res['kuantitaskontrak']*$res['hargasatuan'])."</td>
				 
                <td align=right style=\"cursor:pointer;\">".number_format($rTimb['jumlahTotal'])."</td>
                    <td align=right style=\"cursor:pointer;\">".number_format($rTimb['jumlahKgpem'])."</td>
                    <td align=right style=\"cursor:pointer;\">".number_format($sisaBarang)."</td>
                </tr>
                ";
				$totalx+=$res['kuantitaskontrak']*$res['hargasatuan'];
                $total1+=$res['kuantitaskontrak'];
                $total2+=$rTimb['jumlahTotal'];
                $total3+=$rTimb['jumlahKgpem'];
                $total4+=$sisaBarang;                
        }
        echo"<thead><tr class=rowheader>
        <td colspan=5>".$_SESSION['lang']['total']."</td>
        <td align=right>".number_format($total1)."</td>
		<td align=right></td>	
		<td align=right>".number_format($totalx,2)."</td>
        <td align=right>".number_format($total2)."</td>
        <td align=right>".number_format($total3)."</td>
        <td align=right>".number_format($total4)."</td>
        </tr></thead>";
        echo"</tbody></table>";
        break;
        case'preview2':
		
		
		//exit("Error:MASUK");
		
        if($kdBrg2=='')
        {
            exit("Error:Kode Barang Tidak Boleh Kosong");
        }
        echo"<table class=sortable cellspacing=1 border=0><thead><tr class=rowheader>
        <td>".$_SESSION['lang']['NoKontrak']."</td>
        <td>".$_SESSION['lang']['komoditi']."</td>
        <td>".$_SESSION['lang']['tglKontrak']."</td>
        <td>".$_SESSION['lang']['Pembeli']."</td>
        <td>".$_SESSION['lang']['estimasiPengiriman']."</td>
        <td>".$_SESSION['lang']['jmlhBrg']."</td>
		<td>".$_SESSION['lang']['hargasatuan']."</td>
		<td>".$_SESSION['lang']['total']."</td>
        <td>".$_SESSION['lang']['pemenuhan']."</td>
        <td>".$_SESSION['lang']['beratBersih']." ".$_SESSION['lang']['Pembeli']."</td>
        <td>".$_SESSION['lang']['sisa']."</td>
        </tr></thead><tbody>
        ";

        $sql="select nokontrak,kodebarang,tanggalkontrak,koderekanan,tanggalkirim,sdtanggal,kuantitaskontrak,hargasatuan from ".$dbname.".pmn_kontrakjual
              where kodebarang='".$kdBrg2."' and tanggalkontrak like '".$thn."%' order by tanggalkontrak desc";
        //exit("Error".$sql);
        $query=mysql_query($sql) or die(mysql_error());
        while($res=mysql_fetch_assoc($query))
        {
                $sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$res['kodebarang']."'";
                $qBrg=mysql_query($sBrg) or die(mysql_error());
                $rBrg=mysql_fetch_assoc($qBrg);

                $sCust="select namacustomer  from ".$dbname.".pmn_4customer where kodecustomer='".$res['koderekanan']."'";
                $qCust=mysql_query($sCust) or die(mysql_error());
                $rCust=mysql_fetch_assoc($qCust);

                $sTimb="select sum(beratbersih) as jumlahTotal,sum(kgpembeli) as jumlahKgpem   from ".$dbname.".pabrik_timbangan where nokontrak='".$res['nokontrak']."'";
                $qTimb=mysql_query($sTimb) or die(mysql_error());
                $rTimb=mysql_fetch_assoc($qTimb);
                $arr="nokontrak"."##".$res['nokontrak'];
                $sisaBarang=$res['kuantitaskontrak']-$rTimb['jumlahTotal'];
                if($rTimb['jumlahTotal']<=$res['kuantitaskontrak'])
                {
                    echo"<tr class=rowcontent \">
                    <td >".$res['nokontrak']."</td>
                    <td>".$rBrg['namabarang']."</td>
                    <td>".tanggalnormal($res['tanggalkontrak'])."</td>
                    <td>".$rCust['namacustomer']."</td>
                    <td nowrap>".tanggalnormal($res['tanggalkirim'])." s.d ".tanggalnormal($res['sdtanggal'])."</td>
                    <td align=right>".number_format($res['kuantitaskontrak'],2)."</td>
					
					<td align=right>".number_format($res['hargasatuan'],2)."</td>
					<td align=right>".number_format($res['kuantitaskontrak']*$res['hargasatuan'],2)."</td>
					
                    <td align=right>".number_format($rTimb['jumlahTotal'],2)."</td>
                    <td align=right>".number_format($rTimb['jumlahKgpem'],2)."</td>
                    <td align=right>".number_format($sisaBarang,2)."</td>
                    </tr>
                    ";
                }
        }
        echo"</tbody></table>";
        break;
        case'excel2':
        $kdBrg2=$_GET['kdBrg2'];
        if($kdBrg2=='')
        {
            exit("Error: Comodity required");
        }

                        $stream.="
                        <table>
                        <tr><td colspan=9 align=center>Unfulfilled sales contract</td></tr>
                        <tr><td colspan=3>".$_SESSION['lang']['komoditi']." : ".$optNmBrg[$kdBrg2]."</td><td></td></tr>
                        <tr><td colspan=3></td><td></td></tr>
                        </table>
                        <table border=1>
                        <tr>
                                <td bgcolor=#DEDEDE align=center>No.</td>
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['NoKontrak']."</td>
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['komoditi']."</td>
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tglKontrak']."</td>
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['Pembeli']."</td>
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['estimasiPengiriman']."</td>
										<td  bgcolor=#DEDEDE align=center>".$_SESSION['lang']['hargasatuan']."</td>
										<td  bgcolor=#DEDEDE align=center>".$_SESSION['lang']['total']."</td>
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jmlhBrg']."</td>
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['pemenuhan']."</td>
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['beratBersih']." ".$_SESSION['lang']['Pembeli']."</td>
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['sisa']."</td>
                        </tr>";

                        $strx="select nokontrak,kodebarang,tanggalkontrak,koderekanan,tanggalkirim,sdtanggal,kuantitaskontrak,kodept,hargasatuan from ".$dbname.".pmn_kontrakjual
                              where kodebarang='".$kdBrg2."' and tanggalkontrak like '".$thn."%' order by tanggalkontrak asc";
                        $resx=mysql_query($strx) or die(mysql_error());
                        $row=mysql_fetch_row($resx);
                        if($row<1)
                        {
                        $stream.="	<tr class=rowcontent>
                        <td colspan=8 align=center>Not Found</td></tr>
                        ";
                        }
                        else
                        {
                        $no=0;
                        $resx=mysql_query($strx);
                                while($barx=mysql_fetch_assoc($resx))
                                {
                                $no+=1;
                                $sCust="select namacustomer  from ".$dbname.".pmn_4customer where kodecustomer='".$barx['koderekanan']."'";
                                $qCust=mysql_query($sCust) or die(mysql_error());
                                $rCust=mysql_fetch_assoc($qCust);

                                $sTimb="select sum(beratbersih) as jumlahTotal,sum(kgpembeli) as jumlahKgpem  from ".$dbname.".pabrik_timbangan where nokontrak='".$barx['nokontrak']."'";
                                $qTimb=mysql_query($sTimb) or die(mysql_error());
                                $rTimb=mysql_fetch_assoc($qTimb);
                                $sisaData=$barx['kuantitaskontrak']-$rTimb['jumlahTotal'];
                                    if($rTimb['jumlahTotal']<=$barx['kuantitaskontrak'])
                                    {
                                        $stream.="	<tr class=rowcontent>
                                        <td>".$no."</td>
                                        <td>".$barx['nokontrak']."</td>
                                        <td>".$optNmBrg[$barx['kodebarang']]."</td>
                                        <td>".$barx['tanggalkontrak']."</td>
                                        <td>".$rCust['namacustomer']."</td>
                                        <td>".tanggalnormal($barx['tanggalkirim'])." s.d. ".tanggalnormal($barx['sdtanggal'])."</td>
                                        <td>".number_format($barx['kuantitaskontrak'],0)."</td>
										<td>".number_format($barx['hargasatuan'],0)."</td>
										<td>".number_format($barx['kuantitaskontrak']*$barx['hargasatuan'],0)."</td>
                                        <td>".number_format($rTimb['jumlahTotal'],0)."</td>
                                        <td>".number_format($rTimb['jumlahKgpem'],0)."</td>
                                        <td>".number_format($sisaData,0)."</td>
                                        </tr>";
                                    }
                                }
                        }

                        //echo "warning:".$strx;
                        //=================================================
                        $stream.="</table>";
                                                $stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];

                        $nop_="KontrakBlmTpenuhi";
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
        $periode=$_GET['periode'];
        $kdBrg=$_GET['kdBrg'];
         class PDF extends FPDF
        {
            function Header() {
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
                                global $periode;
                                global $kdBrg;


                                $sql="select nokontrak,kodebarang,tanggalkontrak,koderekanan,tanggalkirim,sdtanggal,kuantitaskontrak,kodept from ".$dbname.".pmn_kontrakjual where tanggalkontrak like '%".$periode."%'";
                                $query=mysql_query($sql) or die(mysql_error());
                                $res=mysql_fetch_assoc($query);

                $tkdOperasi=$res['jlhharitdkoperasi'];
                                $jmlhHariOperasi=$res['jlhharioperasi'];
                                $meter=$res['merterperhari'];
                                $kdOrg=$res['orgdata'];


                # Alamat & No Telp
                $query = selectQuery($dbname,'organisasi','alamat,telepon',
                    "kodeorganisasi='".$res['kodept']."'");
                $orgData = fetchData($query);

                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 11;
                if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
                $this->Image($path,$this->lMargin,$this->tMargin,70);	
                $this->SetFont('Arial','B',9);
                $this->SetFillColor(255,255,255);	
                $this->SetX(100);   
                $this->Cell($width-100,$height,$_SESSION['org']['namaorganisasi'],0,1,'L');	 
                $this->SetX(100); 		
                $this->Cell($width-100,$height,$orgData[0]['alamat'],0,1,'L');	
                $this->SetX(100); 			
                $this->Cell($width-100,$height,"Tel: ".$orgData[0]['telepon'],0,1,'L');	
                $this->Line($this->lMargin,$this->tMargin+($height*4),
                    $this->lMargin+$width,$this->tMargin+($height*4));
                $this->Ln();

                $this->SetFont('Arial','B',9);
                                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['laporanPemenuhanKontrak'],'',0,'L');
                                $this->Ln();
                                $this->SetFont('Arial','',8);
                                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['periode'],'',0,'L');
                                $this->Cell(5,$height,':','',0,'L');
                                $this->Cell(45/100*$width,$height,$periode,'',0,'L');



                $this->Ln();
                                $this->Ln();
                $this->SetFont('Arial','U',7);
                $this->Cell($width,$height, $_SESSION['lang']['laporanPemenuhanKontrak'],0,1,'C');	
                $this->Ln();	

                $this->SetFont('Arial','B',5);
                $this->SetFillColor(220,220,220);


                $this->Cell(3/100*$width,$height,'No',1,0,'C',1);
                $this->Cell(9/100*$width,$height,$_SESSION['lang']['NoKontrak'],1,0,'C',1);
                $this->Cell(10/100*$width,$height,$_SESSION['lang']['komoditi'],1,0,'C',1);
                $this->Cell(8/100*$width,$height,$_SESSION['lang']['tglKontrak'],1,0,'C',1);
                $this->Cell(15/100*$width,$height,$_SESSION['lang']['Pembeli'],1,0,'C',1);
                $this->Cell(11/100*$width,$height,$_SESSION['lang']['estimasiPengiriman'],1,0,'C',1);
                $this->Cell(8/100*$width,$height,$_SESSION['lang']['jmlhBrg']."(KG)",1,0,'C',1);
				
				$this->Cell(8/100*$width,$height,$_SESSION['lang']['hargasatuan'],1,0,'C',1);
				$this->Cell(8/100*$width,$height,$_SESSION['lang']['total'],1,0,'C',1);
				
                $this->Cell(8/100*$width,$height,$_SESSION['lang']['pemenuhan']."(KG)",1,0,'C',1);
                $this->Cell(11/100*$width,$height,$_SESSION['lang']['beratBersih']." ".$_SESSION['lang']['Pembeli'],1,0,'C',1);
                $this->Cell(5/100*$width,$height,$_SESSION['lang']['sisa']." (KG)",1,1,'C',1);

            }

            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
        }
        $pdf=new PDF('P','pt','A4');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 11;
                $pdf->AddPage();
                $pdf->SetFillColor(255,255,255);
                $pdf->SetFont('Arial','',5);
                if($kdBrg!='')
                {
                        $where=" and kodebarang='".$kdBrg."'";
                }
                $sDet="select nokontrak,kodebarang,tanggalkontrak,koderekanan,tanggalkirim,sdtanggal,kuantitaskontrak,hargasatuan from ".$dbname.".pmn_kontrakjual where tanggalkontrak like '%".$periode."%' ".$where."";
                $qDet=mysql_query($sDet) or die(mysql_error());
                while($rDet=mysql_fetch_assoc($qDet))
                {
                        $no+=1;
                        $sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$rDet['kodebarang']."'";
                        $qBrg=mysql_query($sBrg) or die(mysql_error());
                        $rBrg=mysql_fetch_assoc($qBrg);

                        $sCust="select namacustomer  from ".$dbname.".pmn_4customer where kodecustomer='".$rDet['koderekanan']."'";
                        $qCust=mysql_query($sCust) or die(mysql_error());
                        $rCust=mysql_fetch_assoc($qCust);

                        $sTimb="select sum(beratbersih) as jumlahTotal,sum(kgpembeli) as jumlahKgpem  from ".$dbname.".pabrik_timbangan where nokontrak='".$rDet['nokontrak']."'";
                        //exit("Error".$sTimb);
                        $qTimb=mysql_query($sTimb) or die(mysql_error());
                        $rTimb=mysql_fetch_assoc($qTimb);
                        $sisaData=$rDet['kuantitaskontrak']-$rTimb['jumlahTotal'];
                        $pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);
                        $pdf->Cell(9/100*$width,$height,$rDet['nokontrak'],1,0,'L',1);
                        $pdf->Cell(10/100*$width,$height,$rBrg['namabarang'],1,0,'L',1);
                        $pdf->Cell(8/100*$width,$height,tanggalnormal($rDet['tanggalkontrak']),1,0,'L',1);
                        $pdf->Cell(15/100*$width,$height,substr($rCust['namacustomer'],0,50),1,0,'L',1);		
                        $pdf->Cell(11/100*$width,$height,tanggalnormal($rDet['tanggalkirim'])."-".tanggalnormal($rDet['sdtanggal']),1,0,'C',1);		
                        $pdf->Cell(8/100*$width,$height,number_format($rDet['kuantitaskontrak']),1,0,'R',1);
						
						$pdf->Cell(8/100*$width,$height,number_format($rDet['hargasatuan']),1,0,'R',1);
						$pdf->Cell(8/100*$width,$height,number_format($rDet['hargasatuan']*$rDet['kuantitaskontrak'],2),1,0,'R',1);
                        
						$pdf->Cell(8/100*$width,$height,number_format($rTimb['jumlahTotal']),1,0,'R',1);
                        $pdf->Cell(11/100*$width,$height,number_format($rTimb['jumlahKgpem']),1,0,'R',1);
                        $pdf->Cell(5/100*$width,$height,number_format($sisaData),1,1,'R',1);
                        $total1p+=$rDet['kuantitaskontrak'];
                        $total2p+=$rTimb['jumlahTotal'];
                        $total3p+=$rTimb['jumlahKgpem'];
                        $total4p+=$sisaData;
						
						$totaly+=$rDet['hargasatuan']*$rDet['kuantitaskontrak'];
                }
                        $pdf->Cell(56/100*$width,$height,$_SESSION['lang']['total'],1,0,'R',1);
                        $pdf->Cell(8/100*$width,$height,number_format($total1p),1,0,'R',1);
						$pdf->Cell(8/100*$width,$height,'',1,0,'R',1);
						$pdf->Cell(8/100*$width,$height,number_format($totaly),1,0,'R',1);
                        $pdf->Cell(8/100*$width,$height,number_format($total2p),1,0,'R',1);
						
                        $pdf->Cell(11/100*$width,$height,number_format($total3p),1,0,'R',1);
                        $pdf->Cell(5/100*$width,$height,number_format($total4p),1,1,'R',1);

        $pdf->Output();
        break;
        case'excel':
		
	//	exit("Error:MASUK");
		
        $periode=$_GET['periode'];
        $kdBrg=$_GET['kdBrg'];
                        $stream.="
                        <table>
                        <tr><td colspan=9 align=center>".$_SESSION['lang']['laporanPemenuhanKontrak']."</td></tr>
                        <tr><td colspan=3>".$_SESSION['lang']['periode']."</td><td>".$periode."</td></tr>
                        <tr><td colspan=3></td><td></td></tr>
                        </table>
                        <table border=1>
                        <tr>
                                <td bgcolor=#DEDEDE align=center>No.</td>
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['NoKontrak']."</td>
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['komoditi']."</td>
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tglKontrak']."</td>
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['Pembeli']."</td>	
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['estimasiPengiriman']."</td>	
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jmlhBrg']."</td>	
								
								 <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['hargasatuan']."</td>	
								  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['total']."</td>	
					
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['pemenuhan']."</td>
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['beratBersih']." ".$_SESSION['lang']['Pembeli']."</td>
                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['sisa']."</td>
                        </tr>";
                        if($kdBrg!='')
                        {
                                $where=" and kodebarang='".$kdBrg."'";
                        }
                        $strx="select nokontrak,kodebarang,tanggalkontrak,koderekanan,tanggalkirim,sdtanggal,kuantitaskontrak,kodept,hargasatuan from ".$dbname.".pmn_kontrakjual where tanggalkontrak like '%".$periode."%' ".$where."";
                        $resx=mysql_query($strx) or die(mysql_error());
                        $row=mysql_fetch_row($resx);
                        if($row<1)
                        {
                        $stream.="	<tr class=rowcontent>
                        <td colspan=8 align=center>Not Found</td></tr>
                        ";
                        }
                        else
                        {
                        $no=0;
                        $resx=mysql_query($strx);
                                while($barx=mysql_fetch_assoc($resx))
                                {
                                $no+=1;
                                $sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$barx['kodebarang']."'";
                                $qBrg=mysql_query($sBrg) or die(mysql_error());
                                $rBrg=mysql_fetch_assoc($qBrg);

                                $sCust="select namacustomer  from ".$dbname.".pmn_4customer where kodecustomer='".$barx['koderekanan']."'";
                                $qCust=mysql_query($sCust) or die(mysql_error());
                                $rCust=mysql_fetch_assoc($qCust);

                                $sTimb="select sum(beratbersih) as jumlahTotal,sum(kgpembeli) as jumlahKgpem  from ".$dbname.".pabrik_timbangan where nokontrak='".$barx['nokontrak']."'";
                                $qTimb=mysql_query($sTimb) or die(mysql_error());
                                $rTimb=mysql_fetch_assoc($qTimb);
                                $sisaData=$barx['kuantitaskontrak']-$rTimb['jumlahTotal'];
                $sisaBarang=$res['kuantitaskontrak']-$rTimb['jumlahTotal'];
                                $stream.="	<tr class=rowcontent>
                                <td>".$no."</td>
                                <td>".$barx['nokontrak']."</td>
                                <td>".$rBrg['namabarang']."</td>
                                <td>".$barx['tanggalkontrak']."</td>
                                <td>".$rCust['namacustomer']."</td>
                                <td>".tanggalnormal($barx['tanggalkirim'])." s.d. ".tanggalnormal($barx['sdtanggal'])."</td>
                                <td>".number_format($barx['kuantitaskontrak'])."</td>
								
								<td>".number_format($barx['hargasatuan'],2)."</td>
								<td>".number_format($barx['kuantitaskontrak']*$barx['hargasatuan'],2)."</td>
								
                                <td>".number_format($rTimb['jumlahTotal'])."</td>	
                                <td>".number_format($rTimb['jumlahKgpem'])."</td>
                                <td>".number_format($sisaBarang)."</td>
                                </tr>";
                                $total1e+=$barx['kuantitaskontrak'];
                                $total2e+=$rTimb['jumlahTotal'];
                                $total3e+=$rTimb['jumlahKgpem'];
                                $total4e+=$sisaBarang;
								
								$totalx+=$barx['kuantitaskontrak']*$barx['hargasatuan'];
								
								
								
                                }
                        }
                        $stream.="<tr>
                                <td bgcolor=#DEDEDE align=center colspan=6>".$_SESSION['lang']['total']."</td>
                                <td bgcolor=#DEDEDE align=center>".number_format($total1e)."</td>	
								<td bgcolor=#DEDEDE align=center></td>	
								<td bgcolor=#DEDEDE align=center>".number_format($totalx,2)."</td>
                                <td bgcolor=#DEDEDE align=center>".number_format($total2e)."</td>	
                                <td bgcolor=#DEDEDE align=center>".number_format($total3e)."</td>	
                                <td bgcolor=#DEDEDE align=center>".number_format($total4e)."</td>	
                        </tr>";

                        //echo "warning:".$strx;
                        //=================================================
                        $stream.="</table>";
                                                $stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	

                        $nop_="PemenuhanKontrak";
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
        case'getDetail':
        $drr="##no_kontrak";
        $arr="nokontrak"."##".$res['nokontrak'];
        echo"<script language=javascript src=js/generic.js></script><script language=javascript src=js/zTools.js></script>
        <script language=javascript src=js/pmn_laporanPemenuhanKontrak.js></script>";
        echo"<link rel=stylesheet type=text/css href=style/generic.css>";
        $nokontrak=$_GET['nokontrak'];
        $sHed="select  a.tanggalkontrak,a.koderekanan,a.kodebarang from ".$dbname.".pmn_kontrakjual a where a.nokontrak='".$nokontrak."'";
        $qHead=mysql_query($sHed) or die(mysql_error());
        $rHead=mysql_fetch_assoc($qHead);
        $sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$rHead['kodebarang']."'";
        $qBrg=mysql_query($sBrg) or die(mysql_error());
        $rBrg=mysql_fetch_assoc($qBrg);

        $sCust="select namacustomer  from ".$dbname.".pmn_4customer where kodecustomer='".$rHead['koderekanan']."'";
        $qCust=mysql_query($sCust) or die(mysql_error());
        $rCust=mysql_fetch_assoc($qCust);
        echo"<fieldset><legend>".$_SESSION['lang']['detailPengiriman']."</legend>
        <table cellspacing=1 border=0 class=myinputtext>
        <tr>
                <td>".$_SESSION['lang']['NoKontrak']."</td><td>:</td><td id='no_kontrak' value='".$nokontrak."'>".$nokontrak."</td>
        </tr>
        <tr>
                <td>".$_SESSION['lang']['tglKontrak']."</td><td>:</td><td>".tanggalnormal($rHead['tanggalkontrak'])."</td>
        </tr>
        <tr>
                <td>".$_SESSION['lang']['komoditi']."</td><td>:</td><td>".$rBrg['namabarang']."</td>
        </tr>
        <tr>
                <td>".$_SESSION['lang']['Pembeli']."</td><td>:</td><td>".$rCust['namacustomer']."</td>
        </tr>
        <tr><td><button onclick=\"zPdfDetail('pmn_slave_laporanPemenuhanKontrak','".$drr."','printPdf')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button>
        <button onclick=\"zBack()\" class=\"mybutton\" name=\"preview\" id=\"preview\">HTML</button>
        <button onclick=\"detailExcel('".$nokontrak."','pmn_slave_laporanPemenuhanKontrak.php','printExcel','event')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>
        </table><br />";
        echo"<div id=cetakdPdf style=\"display:none;\">
        <fieldset><legend>".$_SESSION['lang']['print']."</legend>
        <div id=\"printPdf\">
        </div>
        </fieldset>
        </div>
        ";
        echo"
        <div id=cetakdHtml >
        <table cellspacing=1 border=0 class=sortable><thead>
        <tr class=data>
        <td>No</td>
        <td>".$_SESSION['lang']['notransaksi']."</td>
        <td>".$_SESSION['lang']['tanggal']."</td>
        <td>".$_SESSION['lang']['nodo']."</td>
        <td>".$_SESSION['lang']['nosipb']."</td>
        <td>".$_SESSION['lang']['kendaraan']."</td>            
        <td>".$_SESSION['lang']['sopir']."</td>
        <td>".$_SESSION['lang']['beratBersih']."</td>
        <td>".$_SESSION['lang']['beratBersih']." ".$_SESSION['lang']['Pembeli']."</td>
        </tr></thead><tbody>
        ";
/*	$sDet="select a.tanggalkontrak,a.pembeli,a.komoditi,b.* from ".$dbname.".pmn_kontrakjual a inner join ".$dbname.".pabrik_timbangan on a.nokontrak=b.nokontrak where a.nokontrak='".$nokontrak."'";
*/	

        $sDet="select notransaksi,tanggal,nodo,nosipb,beratbersih,nokendaraan,supir,kgpembeli from ".$dbname.".pabrik_timbangan where nokontrak='".$nokontrak."'";
        $qDet=mysql_query($sDet) or die(mysql_error());
        $rCek=mysql_num_rows($qDet);
        if($rCek>0)
        {
                while($rDet=mysql_fetch_assoc($qDet))
                {
                        $no+=1;
                        echo"<tr class=rowcontent>
                        <td>".$no."</td>
                        <td>".$rDet['notransaksi']."</td>
                        <td>".tanggalnormal($rDet['tanggal'])."</td>
                        <td>".$rDet['nodo']."</td>
                        <td>".$rDet['nosipb']."</td>
                        <td>".$rDet['nokendaraan']."</td>
                        <td>".ucfirst($rDet['supir'])."</td>
                        <td align=right>".number_format($rDet['beratbersih'],2)."</td>
                        <td align=right>".number_format($rDet['kgpembeli'],2)."</td>
                        </tr>";
                        $subtot['total']+=$rDet['beratbersih'];
                        $subtotKga['totalKg']+=$rDet['kgpembeli'];
                }
                echo"<tr class=rowcontent><td colspan='7'>Total</td><td align=right>".number_format($subtot['total'],2)."</td><td align=right>".number_format($subtotKga['totalKg'],2)."</td></tr>";
        }
        else
        {
                echo"<tr><td colspan=7>Not Found</td></tr>";
        }
        echo"</tbody></table></div></fieldset>";

        break;
        case'getExcel':
            $tab.="
        <table cellspacing=1 border=1 class=sortable><thead>
        <tr class=data>
        <td  bgcolor=#DEDEDE align=center>No</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['notransaksi']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nodo']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nosipb']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kendaraan']."</td>            
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['sopir']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['beratBersih']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['beratBersih']." ".$_SESSION['lang']['Pembeli']."</td>
        </tr></thead><tbody>
        ";
/*	$sDet="select a.tanggalkontrak,a.pembeli,a.komoditi,b.* from ".$dbname.".pmn_kontrakjual a inner join ".$dbname.".pabrik_timbangan on a.nokontrak=b.nokontrak where a.nokontrak='".$nokontrak."'";
*/	

        $sDet="select notransaksi,tanggal,nodo,nosipb,beratbersih,nokendaraan,supir,kgpembeli from ".$dbname.".pabrik_timbangan where nokontrak='".$nokontrak."'";
        $qDet=mysql_query($sDet) or die(mysql_error());
        $rCek=mysql_num_rows($qDet);
        if($rCek>0)
        {
                while($rDet=mysql_fetch_assoc($qDet))
                {
                        $no+=1;
                        $tab.="<tr class=rowcontent>
                        <td>".$no."</td>
                        <td>".$rDet['notransaksi']."</td>
                        <td>".tanggalnormal($rDet['tanggal'])."</td>
                        <td>".$rDet['nodo']."</td>
                        <td>".$rDet['nosipb']."</td>
                        <td>".$rDet['nokendaraan']."</td>
                        <td>".ucfirst($rDet['supir'])."</td>
                        <td align=right>".number_format($rDet['beratbersih'],2)."</td>
                        <td align=right>".number_format($rDet['kgpembeli'],2)."</td>
                        </tr>";
                        $subtot['total']+=$rDet['beratbersih'];
                        $subtotKga['totalKg']+=$rDet['kgpembeli'];
                }
                $tab.="<tr class=rowcontent><td colspan='7'>Total</td><td align=right>".number_format($subtot['total'],2)."</td><td align=right>".number_format($subtotKga['totalKg'],2)."</td></tr>";
        }
        else
        {
                $tab.="<tr><td colspan=7>Not Found</td></tr>";
        }
        $tab.="</tbody>";
                        $tab.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	

                        $nop_="ContractFullfillmentDetail";
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
        case'preview3':
        echo"
        <div id=cetakdHtml >
        <table cellspacing=1 border=0 class=sortable><thead>
        <tr class=data>
        <td>No</td>
        <td>".$_SESSION['lang']['notransaksi']."</td>
        <td>".$_SESSION['lang']['tanggal']."</td>
        <td>".$_SESSION['lang']['kodebarang']."</td>
        <td>".$_SESSION['lang']['nodo']."</td>
        <td>".$_SESSION['lang']['nosipb']."</td>         
        <td>".$_SESSION['lang']['sopir']."</td>
        <td>".$_SESSION['lang']['kendaraan']."</td>               
        <td>".$_SESSION['lang']['beratBersih']."</td>
		<td>".$_SESSION['lang']['hargasatuan']."</td>
		<td>".$_SESSION['lang']['total']."</td>
        <td>".$_SESSION['lang']['beratBersih']." ".$_SESSION['lang']['Pembeli']."</td>
        </tr></thead><tbody>
        ";
/*	$sDet="select a.tanggalkontrak,a.pembeli,a.komoditi,b.* from ".$dbname.".pmn_kontrakjual a inner join ".$dbname.".pabrik_timbangan on a.nokontrak=b.nokontrak where a.nokontrak='".$nokontrak."'";
*/
            $tgl1=explode("-",$_POST['tgl_dr']);
            $tangglAwl=$tgl1[2]."-".$tgl1[1]."-".$tgl1[0];
            $tgl2=explode("-",$_POST['tgl_samp']);
            $tangglSmp=$tgl2[2]."-".$tgl2[1]."-".$tgl2[0];

        $sDet="select notransaksi,tanggal,nodo,nosipb,beratbersih,nokendaraan,supir,kgpembeli,kodebarang
              from ".$dbname.".pabrik_timbangan where substr(tanggal,1,10) between '".$tangglAwl."' and '".$tangglSmp."' and kodebarang='".$kdBrg3."'
                  and nokontrak !=''
              order by tanggal asc";
        $qDet=mysql_query($sDet) or die(mysql_error());
        $rCek=mysql_num_rows($qDet);
        if($rCek>0)
        {
                while($rDet=mysql_fetch_assoc($qDet))
                {
                        $no+=1;
                        echo"<tr class=rowcontent>
                        <td>".$no."</td>
                        <td>".$rDet['notransaksi']."</td>
                        <td nowrap>".tanggalnormal($rDet['tanggal'])."</td>
                        <td nowrap>".$optNmBrg[$rDet['kodebarang']]."</td>
                        <td>".$rDet['nodo']."</td>
                        <td nowrap>".$rDet['nosipb']."</td>			
                        <td>".ucfirst($rDet['supir'])."</td>
                        <td>".$rDet['nokendaraan']."</td>
                        <td align=right>".number_format($rDet['beratbersih'],2)."</td>
						
						 <td align=right>".number_format($hargasatuan[$rDet['notransaksi']],2)."</td>
						  <td align=right>".number_format($hargasatuan[$rDet['notransaksi']]*$rDet['beratbersih'],2)."</td>
						
                        <td align=right>".number_format($rDet['kgpembeli'],2)."</td>
                        </tr>";
						$totalx3+=$hargasatuan[$rDet['notransaksi']]*$rDet['beratbersih'];
                        $subtot['total']+=$rDet['beratbersih'];
                        $subtotKga['totalKg']+=$rDet['kgpembeli'];
                }
                echo"<tr class=rowcontent><td colspan='8'>Total</td>
				<td align=right>".number_format($subtot['total'],2)."</td>
				
				<td align=right></td>
				<td align=right>".number_format($totalx3,2)."</td>
				
				<td align=right>".number_format($subtotKga['totalKg'],2)."</td></tr>";
        }
        else
        {
                echo"<tr><td colspan=7>Not Found</td></tr>";
        }
        echo"</tbody></table></div></fieldset>";
        break;
        case'excel3':
        $tab.="
        <table cellspacing=1 border=1 class=sortable><thead>
        <tr class=data>
        <td bgcolor=#DEDEDE align=center>No</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['notransaksi']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodebarang']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nodo']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nosipb']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kendaraan']."</td>            
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['sopir']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['beratBersih']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['beratBersih']." ".$_SESSION['lang']['Pembeli']."</td>
        </tr></thead><tbody>
        ";
/*	$sDet="select a.tanggalkontrak,a.pembeli,a.komoditi,b.* from ".$dbname.".pmn_kontrakjual a inner join ".$dbname.".pabrik_timbangan on a.nokontrak=b.nokontrak where a.nokontrak='".$nokontrak."'";
*/
            $tgl1=explode("-",$_GET['tgl_dr']);
            $tangglAwl=$tgl1[2]."-".$tgl1[1]."-".$tgl1[0];
            $tgl2=explode("-",$_GET['tgl_samp']);
            $tangglSmp=$tgl2[2]."-".$tgl2[1]."-".$tgl2[0];

        $sDet="select notransaksi,tanggal,nodo,nosipb,beratbersih,nokendaraan,supir,kgpembeli,kodebarang
              from ".$dbname.".pabrik_timbangan where substr(tanggal,1,10) between '".$tangglAwl."' and '".$tangglSmp."' and kodebarang='".$kdBrg3."'
                  and nokontrak !=''
              order by tanggal asc";
        $qDet=mysql_query($sDet) or die(mysql_error());
        $rCek=mysql_num_rows($qDet);
        if($rCek>0)
        {
                while($rDet=mysql_fetch_assoc($qDet))
                {
                        $no+=1;
                        $tab.="<tr class=rowcontent>
                        <td>".$no."</td>
                        <td>".$rDet['notransaksi']."</td>
                        <td>".tanggalnormal($rDet['tanggal'])."</td>
                        <td>".$optNmBrg[$rDet['kodebarang']]."</td>
                        <td>".$rDet['nodo']."</td>
                        <td>".$rDet['nosipb']."</td>
                        <td>".$rDet['nokendaraan']."</td>
                        <td>".ucfirst($rDet['supir'])."</td>
                        <td align=right>".number_format($rDet['beratbersih'],0)."</td>
                        <td align=right>".number_format($rDet['kgpembeli'],0)."</td>
                        </tr>";
                        $subtot['total']+=$rDet['beratbersih'];
                        $subtotKga['totalKg']+=$rDet['kgpembeli'];
                }
                $tab.="<tr class=rowcontent><td colspan='8'>Total</td><td align=right>".number_format($subtot['total'],0)."</td><td align=right>".number_format($subtotKga['totalKg'],2)."</td></tr>";
        }
        else
        {
                $tab.="<tr><td colspan=7>Not Found</td></tr>";
        }
        $tab.="</tbody></table>";
        $nop_="rangePengiriman";
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
        case'detailpdf':
        $no_kontrak=$_GET['no_kontrak'];
        class PDF extends FPDF
        { 
            function Header() {
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
                                global $no_kontrak;


                                $sql="select nokontrak,kodebarang,tanggalkontrak,koderekanan,tanggalkirim,sdtanggal,kuantitaskontrak,kodept from ".$dbname.".pmn_kontrakjual where nokontrak='".$no_kontrak."'";
                                $query=mysql_query($sql) or die(mysql_error());
                                $res=mysql_fetch_assoc($query);

                                $sHed="select  a.tanggalkontrak,a.koderekanan,a.kodebarang from ".$dbname.".pmn_kontrakjual a where a.nokontrak='".$nokontrak."'";
                                $qHead=mysql_query($sHed) or die(mysql_error());
                                $rHead=mysql_fetch_assoc($qHead);
                                $sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$rHead['kodebarang']."'";
                                $qBrg=mysql_query($sBrg) or die(mysql_error());
                                $rBrg=mysql_fetch_assoc($qBrg);

                                $sCust="select namacustomer  from ".$dbname.".pmn_4customer where kodecustomer='".$rHead['koderekanan']."'";
                                $qCust=mysql_query($sCust) or die(mysql_error());
                                $rCust=mysql_fetch_assoc($qCust);	

                # Alamat & No Telp
                $query = selectQuery($dbname,'organisasi','alamat,telepon',
                    "kodeorganisasi='".$res['kodept']."'");
                $orgData = fetchData($query);

                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 11;
                if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
                $this->Image($path,$this->lMargin,$this->tMargin,70);	
                $this->SetFont('Arial','B',9);
                $this->SetFillColor(255,255,255);	
                $this->SetX(100);   
                $this->Cell($width-100,$height,$_SESSION['org']['namaorganisasi'],0,1,'L');	 
                $this->SetX(100); 		
                $this->Cell($width-100,$height,$orgData[0]['alamat'],0,1,'L');	
                $this->SetX(100); 			
                $this->Cell($width-100,$height,"Tel: ".$orgData[0]['telepon'],0,1,'L');	
                $this->Line($this->lMargin,$this->tMargin+($height*4),
                    $this->lMargin+$width,$this->tMargin+($height*4));
                $this->Ln();

                $this->Ln();
                                $this->Ln();
                $this->SetFont('Arial','U',9);
                $this->Cell($width,$height, $_SESSION['lang']['detailPengiriman'],0,1,'C');	
                $this->Ln();	

                $this->SetFont('Arial','B',7);	
                $this->SetFillColor(220,220,220);
                    $this->Cell(3/100*$width,$height,'No',1,0,'C',1);
                    $this->Cell(10/100*$width,$height,$_SESSION['lang']['notransaksi'],1,0,'C',1);
                    $this->Cell(10/100*$width,$height,$_SESSION['lang']['tanggal'],1,0,'C',1);
                    $this->Cell(12/100*$width,$height,$_SESSION['lang']['nodo'],1,0,'C',1);
                    $this->Cell(16/100*$width,$height,$_SESSION['lang']['nosipb'],1,0,'C',1);
                    $this->Cell(11/100*$width,$height,$_SESSION['lang']['kendaraan'],1,0,'C',1);
                    $this->Cell(11/100*$width,$height,$_SESSION['lang']['sopir'],1,0,'C',1);
                    $this->Cell(12/100*$width,$height,$_SESSION['lang']['beratBersih'],1,0,'C',1);
                    $this->Cell(16/100*$width,$height,$_SESSION['lang']['beratBersih']." ".$_SESSION['lang']['Pembeli'],1,1,'C',1);

            }

            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
        }
        $pdf=new PDF('P','pt','A4');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 11;
                $pdf->AddPage();
                $pdf->SetFillColor(255,255,255);
                $pdf->SetFont('Arial','',7);

                $sDet="select notransaksi,tanggal,nodo,nosipb,beratbersih,nokendaraan,supir,kgpembeli from ".$dbname.".pabrik_timbangan where nokontrak='".$no_kontrak."'";
                $qDet=mysql_query($sDet) or die(mysql_error());
                while($rDet=mysql_fetch_assoc($qDet))
                {
                        $no+=1;

                        $pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);
                        $pdf->Cell(10/100*$width,$height,$rDet['notransaksi'],1,0,'L',1);	
                        $pdf->Cell(10/100*$width,$height,tanggalnormal($rDet['tanggal']),1,0,'L',1);	
                        $pdf->Cell(12/100*$width,$height,$rDet['nodo'],1,0,'L',1);		
                        $pdf->Cell(16/100*$width,$height,$rDet['nosipb'],1,0,'L',1);						
                        $pdf->Cell(11/100*$width,$height,$rDet['nokendaraan'],1,0,'L',1);		
                        $pdf->Cell(11/100*$width,$height,ucfirst($rDet['supir']),1,0,'L',1);		
                        $pdf->Cell(12/100*$width,$height,number_format($rDet['beratbersih'],2),1,0,'R',1);
                        $pdf->Cell(16/100*$width,$height,number_format($rDet['kgpembeli'],2),1,1,'R',1);
                        $subtot+=$rDet['beratbersih'];
                        $subtot2+=$rDet['kgpembeli'];
                }
                $pdf->Cell(73/100*$width,$height,"Total",1,0,'R',1);			
                $pdf->Cell(12/100*$width,$height,number_format($subtot,2),1,0,'R',1);
                $pdf->Cell(16/100*$width,$height,number_format($subtot2,2),1,1,'R',1);
        $pdf->Output();
        break;
        default:
        break;
}

?>