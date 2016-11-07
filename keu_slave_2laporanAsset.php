<?php
// -- ind --
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
//fungsi selisih waktu
function datediff($tgl1, $tgl2){
$tgl1 = strtotime($tgl1);
$tgl2 = strtotime($tgl2);
$diff_secs = abs($tgl1 - $tgl2);
$base_year = min(date("Y", $tgl1), date("Y", $tgl2));
$diff = mktime(0, 0, $diff_secs, 1, 1, $base_year);
return array( "years" => date("Y", $diff) - $base_year, "months_total" => (date("Y", $diff) - $base_year) * 12 + date("n", $diff), "months" => date("n", $diff) - 1, "days_total" => floor($diff_secs / (3600 * 24)), "days" => date("j", $diff) - 1, "hours_total" => floor($diff_secs / 3600), "hours" => date("G", $diff), "minutes_total" => floor($diff_secs / 60), "minutes" => (int) date("i", $diff), "seconds_total" => $diff_secs, "seconds" => (int) date("s", $diff) );
}

##### declarasi variabel ##### 
$proses=$_GET['proses'];
$kdOrg=$_POST['kdOrg'];
$kdAst=$_POST['kdAst'];
$tpAsset=$_POST['tpAsset'];
$unitCode = $_POST['unit'];

if($kdOrg=='')$kdOrg=$_GET['kdOrg'];
if($kdAst=='')$kdAst=$_GET['kdAst'];
if($tpAsset=='')$tpAsset=$_GET['tpAsset'];
##### selesai ##### 

## nama karyawan
$str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan where karyawanid=".$_SESSION['standard']['userid']. "";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
        $namakar[$bar->karyawanid]=$bar->namakaryawan;
}
## selesai nama karyawan

//$metode=makeOption($dbname,"sdm_5tipeasset","kodetipe,metodepenyusutan");
##### ambil kode aset dan organisasi untuki option text ##### 
//get org
        $sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where kodeorganisasi ='".$kdOrg."' ";	
        $qOrg=mysql_query($sOrg) or die(mysql_error($conn));
        $rOrg=mysql_fetch_assoc($qOrg);
        $nmOrg=$rOrg['namaorganisasi'];

$brd=0;
$bgBelakang="bgcolor=#00FF40 align=center";
if($proses=='excel')	
{
    $brd=1;
    $bgBelakang="bgcolor=#00FF40 ";
}
  if($tpAsset!='')
    {
        $where=" and tipeasset='".$tpAsset."'";
    }		
    $where.=" and awalpenyusutan<='".$kdAst."'";
if ($kdOrg=='HIP'){
    $txtgolive="Go-Live: 2014-03";
} else {
    $txtgolive="Go-Live: 2014-06";
}
##### preview ##### 
$data=$_SESSION['lang']['daftarasset'].":  ".$nmOrg."  (".$txtgolive.")<br>".$_SESSION['lang']['periode'].":".$kdAst;
$data.="<table class=sortable cellspacing=1 border=".$brd." width=1400px><thead>";
         $data.=" <tr class=rowheader>";
                        $data.="<td rowspan=2 align=center ".$bgBelakang.">No</td>";
                        $data.="<td rowspan=2 align=center ".$bgBelakang.">".$_SESSION['lang']['kodeorganisasi']."</td>";
                        $data.="<td rowspan=2 align=center ".$bgBelakang.">".$_SESSION['lang']['kodeasset']."</td> ";
                        $data.="<td rowspan=2 align=center ".$bgBelakang.">".$_SESSION['lang']['namaasset']."</td>";
                        $data.="<td rowspan=2 align=center ".$bgBelakang.">".$_SESSION['lang']['status']."</td> ";
                        $data.="<td rowspan=2 align=center ".$bgBelakang.">".$_SESSION['lang']['posisiasset']."</td> ";
                        $data.="<td colspan=2 align=center ".$bgBelakang." nowrap>Awal Penyusutan</td> ";
                        $data.="<td rowspan=2 align=center ".$bgBelakang.">".$_SESSION['lang']['jumlahbulanpenyusutan']."</td> ";	
                        $data.="<td rowspan=2 align=center ".$bgBelakang.">".$_SESSION['lang']['usia']." (".$_SESSION['lang']['bulan'].")</td> ";	
                        $data.="<td rowspan=2 align=center ".$bgBelakang.">".$_SESSION['lang']['sisa']." (".$_SESSION['lang']['bulan'].")</td> ";	
                        $data.="<td rowspan=2 align=center ".$bgBelakang.">".$_SESSION['lang']['hargaperolehan']."</td> ";
                        // $data.="<td align=center ".$bgBelakang.">".$_SESSION['lang']['penambah']."</td> ";
                        // $data.="<td align=center ".$bgBelakang.">".$_SESSION['lang']['pengurang']."</td> ";
                        $data.="<td colspan=2 align=center ".$bgBelakang.">Akumulasi Penyusutan</td> ";
                        $data.="<td rowspan=2 align=center ".$bgBelakang.">".$_SESSION['lang']['nilaibuku']."</td> ";	
                        $data.="<td rowspan=2 align=center ".$bgBelakang.">".$_SESSION['lang']['bulanan']."</td> ";
                        $data.="<td rowspan=2 align=center ".$bgBelakang.">".$_SESSION['lang']['keterangan']."</td> ";
                        $data.="<td rowspan=2 align=center ".$bgBelakang.">".$_SESSION['lang']['persendecline']."</td> ";
                        $data.="<td rowspan=2 align=center ".$bgBelakang.">Leasing</td>";
            $data.="</tr>";
            $data.="<tr class=rowheader>";
            $data.="<td align=center>Bulan</td><td align=center>Tahun</td>";
            $data.="<td align=center nowrap>Sebelum Go-Live</td><td align=center nowrap>Setelah Go-Live</td>";
            $data.="</tr></thead><tbody>";

//$sList="select * from ".$dbname.".sdm_daftarasset where tipeasset='".$kdAst."' and kodeorg='".$kdOrg."' order by kodeorg";
if(empty($unitCode)) {
	$sList="select * from ".$dbname.".sdm_daftarasset where  kodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$kdOrg."') ".$where." order by status desc,kodeasset asc";
} else {
	$sList="select * from ".$dbname.".sdm_daftarasset where  kodeorg = '".$unitCode."' ".$where." order by status desc,kodeasset asc";
}
$optMetode = makeOption($dbname,'sdm_5tipeasset','kodetipe,metodepenyusutan');
//echo $sList;
$qList=mysql_query($sList) or die(mysql_error());
$no = 0;
$totHarga=0;
$totHargaAkumul=0;
$totNilai=0;
$bulanan=0;
$tpengurang=0;
$tpenambah=0;
while($bar=mysql_fetch_assoc($qList))
{
    $no+=1;
	$tgl1=$bar['awalpenyusutan']."-01";
	$tgl2=$kdAst."-02";
	//$selisih=datediff($tgl1,$tgl2);
	$tahun1=substr($tgl1,0,4);
	$bulan1=substr($tgl1,5,2);
	$tahun2=substr($tgl2,0,4);
	$bulan2=substr($tgl2,5,2);
	$selisih['months_total']=($tahun2*12)+$bulan2 - (($tahun1*12)+$bulan1)+1;
        $blngolive=0;
        if ($kdOrg=='HIP'){
            if (!($tahun2<='2014' and $bulan2<'03'))
                $blngolive=($tahun2*12)+$bulan2 - ((2014*12)+3)+1;
        } else if ($kdOrg=='SIP' or $kdOrg=='SIL'){
            if (!($tahun2<='2014' and $bulan2<'06'))
                $blngolive=($tahun2*12)+$bulan2 - ((2014*12)+6)+1;
        } 
//        if($bar['status']==0)
//            $data.="<tr class=rowcontent style=\"background-color:grey; color:#fff;\">";
//        else
            $data.="<tr class=rowcontent>";
        $data.="<td align=center>".$no."</td>";
        $data.="<td>".$bar['kodeorg']."</td>";    
        $data.="<td nowrap>".$bar['kodeasset']."</td>";    
        $data.="<td>".$bar['namasset']."</td>";    

        if($bar['status']==0)
        {	
                $data.="<td nowrap>".$_SESSION['lang']['tidakaktif']."</td>";
        }
        else if($bar['status']==1)
        {	
                $data.="<td>".$_SESSION['lang']['aktif']."</td>";
        }
        else if($bar['status']==2)
        {	
                $data.="<td>".$_SESSION['lang']['dlm_rusak_rmh']."</td>";
        }
        else
        {	
                $data.="<td>Hilang</td>";
        }
        
        $tgl1=$bar['awalpenyusutan']."-01";
        $habis=($selisih['months_total']>$bar['jlhblnpenyusutan'])?true:false;
        if($selisih['months_total']>$bar['jlhblnpenyusutan'])
        {
            $selisih['months_total']=$bar['jlhblnpenyusutan'];
        }
		#periksa siapa lebih besar
		if($tgl1>$tgl2)
        {
            $selisih['months_total']=0;
        }
		
		
        $sisabln=$bar['jlhblnpenyusutan']-$selisih['months_total'];
        if(substr($sisabln,0,1)=='-')
        {
            $sisabln=0;
        }
        $akumulasiBulanan=$bar['bulanan']*$selisih['months_total'];
        if($akumulasiBulanan>$bar['hargaperolehan'])
        {
            $akumulasiBulanan=$bar['hargaperolehan'];
        }
        $nilai=$bar['hargaperolehan']-$akumulasiBulanan;
        
		#jika doubledeclining
        if($bar['persendecline']>'0'){
			$thnawal=substr($bar['awalpenyusutan'],0,4);
			$blnawal=substr($bar['awalpenyusutan'],5,2);
			$total=($thnawal*12)+$blnawal;
            
			$thnNow=substr($kdAst,0,4);
			$blnNow=substr($kdAst,5,2);
			
			$totalBulanAwal = 12-$blnawal+1;
			$totalTahun = $thnNow-$thnawal-1;
			
			$totalNow=($thnNow*12)+$blnNow+1;
			$selisihNow=$totalNow-$total;
			$sekarang=0;
			$out=0;
			$akumNow=0;
			
			// Depresiasi s/d akhir tahun
			$before = $sekarang = $bar['hargaperolehan'];
			if($totalTahun>-1) {
				$akumNow += $totalBulanAwal/12 * $bar['persendecline']/100 * $sekarang;
			}
			$sekarang -= $akumNow;
			
			// Depresiasi per Tahun
			if($totalTahun>0) {
				for($i=0;$i<$totalTahun;$i++) {
					$akumNow += $sekarang*$bar['persendecline']/100;
					$sekarang -= $sekarang*$bar['persendecline']/100;
				}
			}
			
			// Depresiasi per Bulan
			$out = $sekarang*($bar['persendecline']/100)/12;
			//if($bar->jlhblnpenyusutan==$selisihNow) {
			if($bar['jlhblnpenyusutan']<$selisihNow) {
				$akumNow += $sekarang;
				$sekarang = $out = 0;
			} else {
				if($totalTahun>-1) {
					if(intval($blnNow)>0) {
						$akumNow += (intval($blnNow)*$out);
						$sekarang -= (intval($blnNow)*$out);
					}
				} else {
					$akumNow += ($blnNow-$blnawal+1)*$out;
					$sekarang -= ($blnNow-$blnawal+1)*$out;
				}
			}
			$akumulasiBulanan=$akumNow;
			$nilai=$sekarang;
			$bar['bulanan']=$out;
        }
        if($bar['persendecline']>'0'){
            $akpnystanstlhgolive=(!$habis)?$bar['hargaperolehan']-$bar['akpnystan_his']-$nilai:0;
        }else{
            if ($bar['akpnystan_his']<>0){
                $akpnystanstlhgolive=$blngolive*$bar['bulanan'];
                $nilai=$bar['hargaperolehan']-$bar['akpnystan_his']-$akpnystanstlhgolive;
            } else {
                $akpnystanstlhgolive=$selisih['months_total']*$bar['bulanan'];
            }
        }
        
        if ($kdOrg=='HIP'){
            if ($tahun2<='2014' and $bulan2<'03') $akpnystanstlhgolive=0;
        } else if ($kdOrg=='SIP' or $kdOrg=='SIL'){
            if ($tahun2<='2014' and $bulan2<'06') $akpnystanstlhgolive=0;
        } 
        
//        if ($akpnystanstlhgolive<10){
//            $akpnystanstlhgolive=($akpnystanstlhgolive<0)?$akpnystanstlhgolive*-1:0;
//        }
        if ($nilai==0 and $bar['hargaperolehan']-$bar['akpnystan_his']>10){
            $akpnystanstlhgolive=$bar['hargaperolehan']-$bar['akpnystan_his'];
        }
        if ($bar['status']==3){
            $nilai=$akpnystanstlhgolive=$bar['bulanan']=0;
        }
        $data.="<td>".$bar['posisiasset']."</td>";    
        $data.="<td align=right>".$bulan1."</td>";    
        $data.="<td align=left>".$bar['tahunperolehan']."</td>";    
        $data.="<td align=right>".$bar['jlhblnpenyusutan']."</td>";  
        $data.="<td align=right>".$selisih['months_total']."</td>";  
        $data.="<td align=right>".$sisabln."</td>";  
        $data.="<td align=right>".number_format($bar['hargaperolehan'],2)."</td>"; 
        $data.="<td align=right>".number_format($bar['akpnystan_his'],2)."</td>"; 
        $data.="<td align=right>".number_format($akpnystanstlhgolive,2)."</td>"; 
        $data.="<td align=right>".number_format($nilai,2)."</td>"; 
        $data.="<td align=right>".number_format($bar['bulanan'],2)."</td>";
        $ket=(trim($bar['keterangan'])!=trim($bar['namasset']))?$bar['keterangan']:"";
        $data.="<td>".$ket."</td>";    				
        $data.="<td align=right>".number_format($bar['persendecline'],2)."</td>";        
        $data.="<td align=right nowrap>";
        if ($bar['leasing']==0){
            $data.="Not Leasing";
        } elseif ($bar['leasing']==1){
            $data.="Leasing";
        } else {
            $data.="Ex Leasing";
        }
	$data.="</td></tr>";
        $totHarga+=$bar['hargaperolehan'];
        $totHargaAkumulSblmGoLive+=$bar['akpnystan_his'];
        $totHargaAkumul+=$akpnystanstlhgolive;
        $totNilai+=$nilai;
        $bulanan+=$bar['bulanan'];
        $tpengurang+=$bar['pengurang'];
        $tpenambah+= $bar['penambah'];
}  
$data.="<tr><td colspan=11>".$_SESSION['lang']['total']."</td>";
$data.="<td align=right>".number_format($totHarga,2)."</td>";

$data.="<td align=right>".number_format($totHargaAkumulSblmGoLive,2)."</td>";
$data.="<td align=right>".number_format($totHargaAkumul,2)."</td>";
$data.="<td align=right>".number_format($totNilai,2)."</td>";
$data.="<td colspan=2>&nbsp;</td>";
$data.="<td align=right></td></tr>";
$data.="</tbody></table>";


##### untuk menu button dan panggil menu dr atas ##### 
switch($proses)
{
        // tampilkan preview //
        case'preview':
                if($kdOrg=='')
                {
                        echo "warning : Organization code is obligatory";
                        exit();	
                }
                else if($kdAst=='')
                {
                        echo "warning : Asset type is obligatory";
                        exit();	
                }
                else 
                {
                        echo $data;	//panggil data dari preview di atas
                }
        break;


        // tampilkan exel //
        case 'excel':

                if($kdOrg=='')
                {
                        echo "warning : Organization code is obligatory";
                        exit();	
                }
                else if($kdAst=='')
                {
                        echo "warning : Asset type is obligatory";
                        exit();	
                }

                $data.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
                $tglSkrg=date("Ymd");
                $nop_="Laporan_Daftar_Asset_".$tglSkrg;
                //$nop_"Laporan Daftar Asset ".$nmOrg."_".$nmAst;
                //$nop_="Daftar Asset : ".$nmOrg." ".$nmAst;
                if(strlen($data)>0)
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
                        if(!fwrite($handle,$data))
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
                // tutup tampilakn panggil exel //

        // tampilkan PDF //		
        case'pdf':

                if($kdOrg=='')
                {
                        echo "warning : Organization code is obligatory";
                        exit();	
                }
                else if($kdAst=='')
                {
                        echo "warning : Asset type is obligatory";
                        exit();	
                }

    //buat header pdf
    class PDF extends FPDF
                    {
                            function Header() {
                                    //declarasi header variabel
                                    global $conn;
                                    global $dbname;
                                    global $align;
                                    global $length;
                                    global $colArr;
                                    global $title;

                                    global $nmOrg;
                                    global $kdOrg;
                                    global $kdAst;
                                    global $nmAst;
                                    global $thnPer;
                                    global $nmAsst;
                                    global $namakar;
                                    global $selisih;
                                    global $where;


                                    //alamat PT minanga dan logo
                                    $query = selectQuery($dbname,'organisasi','alamat,telepon',
                                            "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                                    $orgData = fetchData($query);

                                    $width = $this->w - $this->lMargin - $this->rMargin;
                                    $height = 20;
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
                                    //tutup logo dan alamat

                                    //untuk sub judul
                                    $this->SetFont('Arial','B',10);
                                    $this->Cell((20/100*$width)-5,$height,"Asset List",'',0,'L');
                                    $this->Ln();
                                    $this->SetFont('Arial','',8);
                                    $this->Cell((100/100*$width)-5,$height,"Printed By : ".$namakar[$_SESSION['standard']['userid']],'',0,'R');
                                    $this->Ln();
                                    $this->Cell((100/100*$width)-5,$height,"Date : ".date('d-m-Y'),'',0,'R');
                                    $this->Ln();
                                    $this->Cell((100/100*$width)-5,$height,"Time : ".date('h:i:s'),'',0,'R');
                                    $this->Ln();
                                    $this->Ln();
                                    //tutup sub judul

                                    //judul tengah
                                    $this->SetFont('Arial','B',12);
                                    $this->Cell($width,$height,strtoupper("Asset List "."$nmAst")." ".$_SESSION['lang']['periode'].":".$kdAst,'',0,'C');
                                    $this->Ln();
                                    $this->Cell($width,$height,strtoupper("$nmOrg"),'',0,'C');
                                    $this->Ln();
                                    $this->Ln();
                                    //tutup judul tengah

                                    //isi atas tabel
                                    $this->SetFont('Arial','B',6);
                                    $this->SetFillColor(220,220,220);
                                    $this->Cell(2/100*$width,$height,"No",1,0,'C',1);
                                    $this->Cell(7/100*$width,$height,$_SESSION['lang']['kodeorganisasi'],1,0,'C',1);
                                    $this->Cell(7/100*$width,$height,$_SESSION['lang']['kodeasset'],1,0,'C',1);
                                    $this->Cell(7/100*$width,$height,$_SESSION['lang']['thnperolehan'],1,0,'C',1);
                                    $this->Cell(15/100*$width,$height,$_SESSION['lang']['namaasset'],1,0,'C',1);
                                    //$this->Cell(5/100*$width,$height,$_SESSION['lang']['status'],1,0,'C',1);
                                    $this->Cell(9/100*$width,$height,$_SESSION['lang']['hargaperolehan'],1,0,'C',1);
				    
                                    $this->Cell(9/100*$width,$height,$_SESSION['lang']['jumlahbulanpenyusutan'],1,0,'C',1);
                                    $this->Cell(6/100*$width,$height,$_SESSION['lang']['usia']." (".$_SESSION['lang']['bulan'].")",1,0,'C',1);
                                    $this->Cell(6/100*$width,$height,$_SESSION['lang']['sisa']." (".$_SESSION['lang']['bulan'].")",1,0,'C',1);
                                    $this->Cell(9/100*$width,$height,$_SESSION['lang']['akumulasipenyusutan'],1,0,'C',1);
                                    $this->Cell(9/100*$width,$height,$_SESSION['lang']['nilaibuku'],1,0,'C',1);

                                    $this->Cell(9/100*$width,$height,$_SESSION['lang']['awalpenyusutan'],1,0,'C',1);
                                    $this->Cell(6/100*$width,$height,$_SESSION['lang']['bulanan'],1,1,'C',1);



                                    //tutup isi tabel
                            }//tutup header pdfnya


                            function Footer()
                            {
                                    $this->SetY(-15);
                                    $this->SetFont('Arial','I',8);
                                    $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
                            }
                    }
                    //untuk tampilan setting pdf
                    $pdf=new PDF('L','pt','Legal');//untuk kertas L=len p=pot
                    $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
                    $height = 20;
                    $pdf->AddPage();
                    $pdf->SetFillColor(255,255,255);
                    $pdf->SetFont('Arial','',6);//ukuran tulisan
                    //tutup tampilan setting


                    //isi tabel dan tabelnya
                    $no=0;
                    $sql="select * from ".$dbname.".sdm_daftarasset where kodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$kdOrg."') ".$where." order by kodeasset";
                    //$sql="select * from ".$dbname.".sdm_daftarasset where tipeasset='".$kdAst."' and kodeorg='".$kdOrg."' order by kodeorg";
                    //echo $sql;
                    $qDet=mysql_query($sql) or die(mysql_error());
                    while($res=mysql_fetch_assoc($qDet))
                    {
                            $no+=1;
                            $tgl1=$res['awalpenyusutan']."-01";
                            $tgl2=$kdAst."-02";
                            $selisih=datediff($tgl1,$tgl2);
                            if($selisih[months_total]>$res['jlhblnpenyusutan'])
                            {
                            $selisih[months_total]=$res['jlhblnpenyusutan'];
                            }
						    #periksa siapa lebih besar
						   if($tgl1>$tgl2)
							{
								$selisih[months_total]=0;
							}			
                            $sisabln=$res['jlhblnpenyusutan']-$selisih[months_total];
                            if(substr($sisabln,0,1)=='-')
                            {
                            $sisabln=0;
                            }
                            $akumulasiBulanan=$res['bulanan']*$selisih[months_total];
                            if($akumulasiBulanan>$res['hargaperolehan'])
                            {
                            $akumulasiBulanan=$res['hargaperolehan'];
                            }
                            $nilai=$res['hargaperolehan']-$akumulasiBulanan;
                            $pdf->Cell(2/100*$width,$height,$no,1,0,'C',1);
                            $pdf->Cell(7/100*$width,$height,$res['kodeorg'],1,0,'L',1);	
                            $pdf->Cell(7/100*$width,$height,$res['kodeasset'],1,0,'L',1);	
                            $pdf->Cell(7/100*$width,$height,$res['tahunperolehan'],1,0,'R',1);	
                            $pdf->Cell(15/100*$width,$height,$res['namasset'],1,0,'L',1);
                            $pdf->Cell(9/100*$width,$height,number_format($res['hargaperolehan'],2),1,0,'R',1);	
                            $pdf->Cell(9/100*$width,$height,number_format($res['jlhblnpenyusutan'],2),1,0,'R',1);	
                            $pdf->Cell(6/100*$width,$height,$selisih[months_total],1,0,'C',1);
                            $pdf->Cell(6/100*$width,$height,$sisabln,1,0,'C',1);
                            $pdf->Cell(9/100*$width,$height,number_format($akumulasiBulanan,2),1,0,'C',1);
                            $pdf->Cell(9/100*$width,$height,number_format($nilai,2),1,0,'C',1);
                            $pdf->Cell(9/100*$width,$height,$res['awalpenyusutan'],1,0,'L',1);	
                            $pdf->Cell(6/100*$width,$height,number_format($res['bulanan'],2),1,1,'R',1);	                      
                            //$pdf->Ln();	
                            $totHarga+=$res['hargaperolehan'];
                            $totHargaAkumul+=$akumulasiBulanan;
                            $totNilai+=$nilai;
                            $bulanan+=$res['bulanan'];
                    }
                    $pdf->Cell(38/100*$width,$height,$_SESSION['lang']['total'],1,0,'R',1);
                    $pdf->Cell(9/100*$width,$height,number_format($totHarga,2),1,0,'R',1);
                    $pdf->Cell(21/100*$width,$height,'',1,0,'R',1);
                    $pdf->Cell(9/100*$width,$height,number_format($totHargaAkumul,2),1,0,'R',1);
                    $pdf->Cell(9/100*$width,$height,number_format($totNilai,2),1,0,'R',1);
                    $pdf->Cell(9/100*$width,$height,'',1,0,'R',1);
                    $pdf->Cell(6/100*$width,$height,number_format($bulanan,2),1,0,'R',1);
            $pdf->Output();
##### Tutup PDF #####

break;
default;


}    
?>
