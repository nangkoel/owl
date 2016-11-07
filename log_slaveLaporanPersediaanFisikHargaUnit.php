<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
#
//FILE INI SUDAH TIDAK FULL TERPAKAI, GANYA PENGAMBILAN GUDANG SAJA YANG TERPAKAI
#
$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
$_POST['unitDt']==''?$unitDt=$_GET['unitDt']:$unitDt=$_POST['unitDt'];
$_POST['gudang']==''?$gudang=$_GET['gudang']:$gudang=$_POST['gudang'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$optNmBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optNmSat=makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');
if($proses!='getGudang')
{
    if($unitDt=='')
    {
       exit("Error:Unit Tidak Boleh Kosong");
    }
    if($gudang!='')
    {
       $where.="and kodegudang like '".$gudang."%'";
    }
    
    if($periode!='')
    {
       $where.=" and periode='".$periode."'";
    }
    if($proses=='excel')
    {
        $tab.="<table class=sortable cellspacing=1 border=1 width=100%>
	     <thead>
		    <tr>
			  <td rowspan=2  bgcolor=#DEDEDE align=center>No.</td>
			  <td rowspan=2  bgcolor=#DEDEDE align=center>".$_SESSION['lang']['periode']."</td>
			  <td rowspan=2  bgcolor=#DEDEDE  align=center>".$_SESSION['lang']['kodebarang']."</td>
			  <td rowspan=2  bgcolor=#DEDEDE  align=center>".$_SESSION['lang']['namabarang']."</td>
			  <td rowspan=2  bgcolor=#DEDEDE  align=center>".$_SESSION['lang']['satuan']."</td>
			  <td colspan=3  bgcolor=#DEDEDE  align=center>".$_SESSION['lang']['saldoawal']."</td>
			  <td colspan=3  bgcolor=#DEDEDE  align=center>".$_SESSION['lang']['masuk']."</td>
			  <td colspan=3  bgcolor=#DEDEDE  align=center>".$_SESSION['lang']['keluar']."</td>
			  <td colspan=3  bgcolor=#DEDEDE  align=center>".$_SESSION['lang']['saldoakhir']."</td>
			</tr>
			<tr>
			   <td  bgcolor=#DEDEDE  align=center>".$_SESSION['lang']['kuantitas']."</td>
			   <td  bgcolor=#DEDEDE  align=center>".$_SESSION['lang']['hargasatuan']."</td>
			   <td  bgcolor=#DEDEDE  align=center>".$_SESSION['lang']['totalharga']."</td>	   
			   <td  bgcolor=#DEDEDE  align=center>".$_SESSION['lang']['kuantitas']."</td>
			   <td  bgcolor=#DEDEDE  align=center>".$_SESSION['lang']['hargasatuan']."</td>
			   <td  bgcolor=#DEDEDE  align=center>".$_SESSION['lang']['totalharga']."</td>	   
			   <td  bgcolor=#DEDEDE  align=center>".$_SESSION['lang']['kuantitas']."</td>
			   <td  bgcolor=#DEDEDE  align=center>".$_SESSION['lang']['hargasatuan']."</td>
			   <td  bgcolor=#DEDEDE  align=center>".$_SESSION['lang']['totalharga']."</td>	   
			   <td  bgcolor=#DEDEDE  align=center>".$_SESSION['lang']['kuantitas']."</td>
			   <td  bgcolor=#DEDEDE  align=center>".$_SESSION['lang']['hargasatuan']."</td>
			   <td  bgcolor=#DEDEDE  align=center>".$_SESSION['lang']['totalharga']."</td>	   
			</tr>   
		 </thead><tbody>";
    }
   
    if($periode=='')
    {
        $sData="select distinct sum(saldoawalqty) as saldoawalqty,sum(hargaratasaldoawal) as hargaratasaldoawal,
                sum(nilaisaldoawal) as nilaisaldoawal,sum(qtymasuk) as qtymasuk,sum(qtymasukxharga) as qtymasukxharga,
                sum(qtykeluar) as qtykeluar,sum(saldoakhirqty) as saldoakhirqty,sum(saldoakhirqty) as saldoakhirqty,
                sum(nilaisaldoakhir) as nilaisaldoakhir,periode,kodebarang from ".$dbname.".log_5saldobulanan where kodegudang!='' ".$where."
                group by periode,kodebarang";
        //exit("Error:".$sData);
        $qData=mysql_query($sData) or die(mysql_error($conn));
        while($rData=mysql_fetch_assoc($qData))
        {
            $dtPeriode[$rData['periode']]=$rData['periode'];
            $lstKdBrg[$rData['kodebarang']]=$rData['kodebarang'];
            $dtKdBarang[$rData['periode']][$rData['kodebarang']]=$rData['kodebarang'];
            $dtAwal[$rData['periode'].$rData['kodebarang']]=$rData['saldoawalqty'];
            $dtNilAwal[$rData['periode'].$rData['kodebarang']]=$rData['nilaisaldoawal'];
            @$dtHrgAwal[$rData['periode'].$rData['kodebarang']]=$dtNilAwal[$rData['periode'].$rData['kodebarang']]/$dtAwal[$rData['periode'].$rData['kodebarang']];
            
            $dtMasuk[$rData['periode'].$rData['kodebarang']]=$rData['qtymasuk'];
            $dtNilMasuk[$rData['periode'].$rData['kodebarang']]=$rData['qtymasukxharga'];
            @$dtHrgMasuk[$rData['periode'].$rData['kodebarang']]=$dtNilMasuk[$rData['periode'].$rData['kodebarang']]/$dtMasuk[$rData['periode'].$rData['kodebarang']];
            
            $dtKeluar[$rData['periode'].$rData['kodebarang']]=$rData['qtykeluar'];
            $dtNilKeluar[$rData['periode'].$rData['kodebarang']]=$rData['saldoakhirqty'];
            @$dtHrgKeluar[$rData['periode'].$rData['kodebarang']]=$dtNilKeluar[$rData['periode'].$rData['kodebarang']]/$dtKeluar[$rData['periode'].$rData['kodebarang']];
            
            $dtAkhir[$rData['periode'].$rData['kodebarang']]=$rData['saldoakhirqty'];
            $dtNilAkhir[$rData['periode'].$rData['kodebarang']]=$rData['nilaisaldoakhir'];
            @$dtHrgAkhir[$rData['periode'].$rData['kodebarang']]=$dtNilAkhir[$rData['periode'].$rData['kodebarang']]/$dtAkhir[$rData['periode'].$rData['kodebarang']];
            
        }
    }
    else
    {
         $sData="select distinct * from ".$dbname.".log_5saldobulanan where kodegudang!='' ".$where."";
         $qData=mysql_query($sData) or die(mysql_error($conn));
         while($rData=mysql_fetch_assoc($qData))
         {
            $dtPeriode[$rData['periode']]=$rData['periode'];
            $lstKdBrg[$rData['kodebarang']]=$rData['kodebarang'];
            $dtKdBarang[$rData['periode']][$rData['kodebarang']]=$rData['kodebarang'];
            $dtAwal[$rData['periode'].$rData['kodebarang']]=$rData['saldoawalqty'];
            $dtNilAwal[$rData['periode'].$rData['kodebarang']]=$rData['nilaisaldoawal'];
            $dtHrgAwal[$rData['periode'].$rData['kodebarang']]=$rData['hargaratasaldoawal'];
            
            $dtMasuk[$rData['periode'].$rData['kodebarang']]=$rData['qtymasuk'];
            $dtNilMasuk[$rData['periode'].$rData['kodebarang']]=$rData['qtymasukxharga'];
            @$dtHrgMasuk[$rData['periode'].$rData['kodebarang']]=$dtNilMasuk[$rData['periode'].$rData['kodebarang']]/$dtMasuk[$rData['periode'].$rData['kodebarang']];
            
            $dtKeluar[$rData['periode'].$rData['kodebarang']]=$rData['qtykeluar'];
            $dtNilKeluar[$rData['periode'].$rData['kodebarang']]=$rData['saldoakhirqty'];
            @$dtHrgKeluar[$rData['periode'].$rData['kodebarang']]=$dtNilKeluar[$rData['periode'].$rData['kodebarang']]/$dtKeluar[$rData['periode'].$rData['kodebarang']];
            
            $dtAkhir[$rData['periode'].$rData['kodebarang']]=$rData['saldoakhirqty'];
            $dtNilAkhir[$rData['periode'].$rData['kodebarang']]=$rData['nilaisaldoakhir'];
            @$dtHrgAkhir[$rData['periode'].$rData['kodebarang']]=$rData['nilaisaldoakhir'];
         }
    }
 $chekDt=count($dtPeriode);
 if($chekDt==0)
 {
     exit("Error:Data Kosong");
 }
    foreach($dtPeriode as $dtIsi)
    {
        foreach($lstKdBrg as $dtBrg)
        {
            if($dtKdBarang[$dtIsi][$dtBrg]!='')
            {
            $no+=1;
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$no."</td>";
            $tab.="<td>".$dtIsi."</td>";
            $tab.="<td>".$dtKdBarang[$dtIsi][$dtBrg]."</td>";
            $tab.="<td>".$optNmBrg[$dtKdBarang[$dtIsi][$dtBrg]]."</td>";
            $tab.="<td>".$optNmSat[$dtKdBarang[$dtIsi][$dtBrg]]."</td>";
            $tab.="<td align=right class=firsttd>".$dtAwal[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]]."</td>";
            $tab.="<td align=right>".number_format($dtHrgAwal[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]],2)."</td>";
            $tab.="<td align=right>".number_format($dtNilAwal[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]],2)."</td>";//saldo awal
            $tab.="<td align=right>".$dtMasuk[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]]."</td>";

            $tab.="<td align=right>".number_format($dtHrgMasuk[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]],2)."</td>";
            $tab.="<td align=right>".number_format($dtNilMasuk[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]],2)."</td>";//saldo masuk
            $tab.="<td align=right  class=firsttd>".$dtKeluar[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]]."</td>";
            
            $tab.="<td align=right>".number_format($dtHrgKeluar[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]],2)."</td>";
            $tab.="<td align=right>".number_format($dtNilKeluar[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]],2)."</td>";//saldo keluar
             $tab.="<td align=right  class=firsttd>".$dtAkhir[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]]."</td>";
            $tab.="<td align=right>".number_format($dtHrgAkhir[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]],2)."</td>";
            $tab.="<td align=right>".number_format($dtNilAkhir[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]],2)."</td>";//saldo akhir  
            }
        }
    }
}
switch($proses)
{
    case'getGudang':
    $optUnit="<option value=''>".$_SESSION['lang']['all']."</option>";
    $sUnit="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where 
            kodeorganisasi like '".$unitDt."%' and tipe like 'GUDANG%' order by namaorganisasi asc";
    $qUnit=mysql_query($sUnit) or die(mysql_error($conn));
    while($rUnit=mysql_fetch_assoc($qUnit))
    {
        $optUnit.="<option value='".$rUnit['kodeorganisasi']."'>".$rUnit['namaorganisasi']."</option>";
    }
     echo $optUnit;
    break;
       case'preview':
           
           echo $tab;
       break;
   case'excel':
       $tab.="</tbody></table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
$dte=date("Hms");
$nop_="lapPersediaanFisikUnitHrg_".$dte;
 $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
 gzwrite($gztralala, $tab);
 gzclose($gztralala);
 echo "<script language=javascript1.2>
	window.location='tempExcel/".$nop_.".xls.gz';
	</script>";
       break;
   case'pdf':
   
//=================================================
class PDF extends FPDF {
    function Header() {
       global $namapt;
	   global $pt;
        $this->SetFont('Arial','B',8); 
		$this->Cell(20,5,$namapt,'',1,'L');
        $this->SetFont('Arial','B',12);
		$this->Cell(190,5,strtoupper($_SESSION['lang']['laporanstok']),0,1,'C');
        $this->SetFont('Arial','',8);
		$this->Cell(140,5,' ','',0,'R');
		$this->Cell(15,5,$_SESSION['lang']['tanggal'],'',0,'L');
		$this->Cell(2,5,':','',0,'L');
		$this->Cell(35,5,date('d-m-Y H:i'),0,1,'L');
		$this->Cell(140,5,' ','',0,'R');
		$this->Cell(15,5,$_SESSION['lang']['page'],'',0,'L');
		$this->Cell(2,5,':','',0,'L');
		$this->Cell(35,5,$this->PageNo(),'',1,'L');
		$this->Cell(140,5,'UNIT:'.$pt.'-'.$gudang,'',0,'L');
		$this->Cell(15,5,'User','',0,'L');
		$this->Cell(2,5,':','',0,'L');
		$this->Cell(35,5,$_SESSION['standard']['username'],'',1,'L');
        $this->SetFont('Arial','',4);		
		$this->Cell(5,8,'No.',1,0,'C');
		$this->Cell(15,8,$_SESSION['lang']['periode'],1,0,'C');				
		$this->Cell(15,8,$_SESSION['lang']['kodebarang'],1,0,'C');	
		$this->Cell(40,8,substr($_SESSION['lang']['namabarang'],0,30),1,0,'C');
		$this->Cell(5,8,$_SESSION['lang']['satuan'],1,0,'C');		
		$this->Cell(27,4,$_SESSION['lang']['saldoawal'],1,0,'C');		
		$this->Cell(27,4,$_SESSION['lang']['masuk'],1,0,'C');
		$this->Cell(27,4,$_SESSION['lang']['keluar'],1,0,'C');
		$this->Cell(27,4,$_SESSION['lang']['saldo'],1,1,'C');
//=====================================
		$this->SetX(90);
		$this->Cell(9,4,$_SESSION['lang']['kuantitas'],1,0,'C');
		$this->Cell(9,4,$_SESSION['lang']['hargasatuan'],1,0,'C');
		$this->Cell(9,4,$_SESSION['lang']['totalharga'],1,0,'C');		
		$this->Cell(9,4,$_SESSION['lang']['kuantitas'],1,0,'C');
		$this->Cell(9,4,$_SESSION['lang']['hargasatuan'],1,0,'C');
		$this->Cell(9,4,$_SESSION['lang']['totalharga'],1,0,'C');
		$this->Cell(9,4,$_SESSION['lang']['kuantitas'],1,0,'C');
		$this->Cell(9,4,$_SESSION['lang']['hargasatuan'],1,0,'C');
		$this->Cell(9,4,$_SESSION['lang']['totalharga'],1,0,'C');
		$this->Cell(9,4,$_SESSION['lang']['kuantitas'],1,0,'C');
		$this->Cell(9,4,$_SESSION['lang']['hargasatuan'],1,0,'C');
		$this->Cell(9,4,$_SESSION['lang']['totalharga'],1,1,'C');									

    }
}
//================================

		$pdf=new PDF('P','mm','A4');
		$pdf->AddPage();
 foreach($dtPeriode as $dtIsi)
    {
        foreach($lstKdBrg as $dtBrg)
        {
            if($dtKdBarang[$dtIsi][$dtBrg]!='')
            {
            $nor+=1;
            $pdf->Cell(5,4,$nor,0,0,'C');
            $pdf->Cell(15,4,$dtIsi,0,0,'C');	
            $pdf->Cell(15,4,$dtKdBarang[$dtIsi][$dtBrg],0,0,'L');
            $pdf->Cell(40,4,$optNmBrg[$dtKdBarang[$dtIsi][$dtBrg]],0,0,'L');
            $pdf->Cell(5,4,$optNmSat[$dtKdBarang[$dtIsi][$dtBrg]],0,0,'L');
            $pdf->Cell(9,4,number_format($dtAwal[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]],2,'.',','),0,0,'R');
            $pdf->Cell(9,4,number_format($dtHrgAwal[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]],2,'.',','),0,0,'R');						
            $pdf->Cell(9,4,number_format($dtNilAwal[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]],2,'.',','),0,0,'R');	
            $pdf->Cell(9,4,number_format($dtMasuk[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]],2,'.',','),0,0,'R');	
            $pdf->Cell(9,4,number_format($dtHrgMasuk[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]],2,'.',','),0,0,'R');
            $pdf->Cell(9,4,number_format($dtNilMasuk[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]],2,'.',','),0,0,'R');
            $pdf->Cell(9,4,number_format($dtKeluar[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]],2,'.',','),0,0,'R');	
            $pdf->Cell(9,4,number_format($dtHrgKeluar[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]],2,'.',','),0,0,'R');	
            $pdf->Cell(9,4,number_format($dtNilKeluar[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]],2,'.',','),0,0,'R');
            $pdf->Cell(9,4,number_format($dtAkhir[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]],2,'.',','),0,0,'R');																							
            $pdf->Cell(9,4,number_format($dtHrgAkhir[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]],2,'.',','),0,0,'R');
            $pdf->Cell(9,4,number_format($dtNilAkhir[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]],2,'.',','),0,1,'R');
            }
        }
    }
		
	$pdf->Output();	
 
   break;
    default:
    break;
}











//$pt=$_POST['pt'];
//$gudang=$_POST['gudang'];
//$periode=$_POST['periode'];
//
//if($periode==''){
//    echo"Error: Please choose Periode.";
//    exit;
//}        
//
//$arrBarang=array();
//$arrAwal=array();
//$kamussatuan=array();
//$kamusnamabarang=array();
//
////nyari barang
//if($gudang=='')$str="select a.kodebarang, b.satuan, b.namabarang from ".$dbname.".log_5saldobulanan a
//    left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang
//    where a.kodeorg='".$pt."' 
//    and a.periode like '".$periode."%'
//    order by a.kodebarang";
//else $str="select a.kodebarang, b.satuan, b.namabarang from ".$dbname.".log_5saldobulanan a
//    left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang
//    where a.kodeorg='".$pt."' and kodegudang = '".$gudang."'
//    and a.periode like '".$periode."%'
//    order by a.kodebarang";
////$qData=mysql_query($sData) or die(mysql_error());
//$res=mysql_query($str) or die(mysql_error());
//while($bar=mysql_fetch_object($res))
//{
//    $arrBarang[$bar->kodebarang]=$bar->kodebarang;
//    $kamussatuan[$bar->kodebarang]=$bar->satuan;
//    $kamusnamabarang[$bar->kodebarang]=$bar->namabarang;
//}
//
////nyari saldoawal
//if($gudang=='')$str="select kodebarang, saldoawalqty, hargaratasaldoawal, nilaisaldoawal from ".$dbname.".log_5saldobulanan
//    where kodeorg='".$pt."' 
//    and periode like '".$periode."-01'
//    order by kodebarang";
//else $str="select kodebarang, saldoawalqty, hargaratasaldoawal, nilaisaldoawal from ".$dbname.".log_5saldobulanan
//    where kodeorg='".$pt."' and kodegudang = '".$gudang."'
//    and periode like '".$periode."-01'
//    order by kodebarang";
//$res=mysql_query($str) or die(mysql_error());
//while($bar=mysql_fetch_object($res))
//{
//    $arrAwal[$bar->kodebarang]['saldoawalqty']=$bar->saldoawalqty;
//    $arrAwal[$bar->kodebarang]['hargaratasaldoawal']=$bar->hargaratasaldoawal;
//    $arrAwal[$bar->kodebarang]['nilaisaldoawal']=$bar->nilaisaldoawal;
//}
//
////nyari tahun berjalan
//if($gudang=='')$str="select kodebarang, sum(qtymasuk) as qtymasuk, sum(qtykeluar) as qtykeluar, sum(qtymasukxharga) as qtymasukxharga, sum(qtykeluarxharga) as qtykeluarxharga 
//    from ".$dbname.".log_5saldobulanan
//    where kodeorg='".$pt."' 
//    and periode like '".$periode."%'
//    group by kodebarang
//    order by kodebarang";
//else $str="select kodebarang, sum(qtymasuk) as qtymasuk, sum(qtykeluar) as qtykeluar, sum(qtymasukxharga) as qtymasukxharga, sum(qtykeluarxharga) as qtykeluarxharga 
//    from ".$dbname.".log_5saldobulanan 
//    where kodeorg='".$pt."' and kodegudang = '".$gudang."'
//    and periode like '".$periode."%'
//    group by kodebarang
//    order by kodebarang";
//$res=mysql_query($str) or die(mysql_error());
//while($bar=mysql_fetch_object($res))
//{
//    $arrAwal[$bar->kodebarang]['qtymasuk']=$bar->qtymasuk;
//    $arrAwal[$bar->kodebarang]['qtykeluar']=$bar->qtykeluar;
//    $arrAwal[$bar->kodebarang]['qtymasukxharga']=$bar->qtymasukxharga;
//    $arrAwal[$bar->kodebarang]['qtykeluarxharga']=$bar->qtykeluarxharga;
//}
//
////nyari saldo akhir
//if($gudang=='')$str="select kodebarang, saldoakhirqty, nilaisaldoakhir,hargarata from ".$dbname.".log_5saldobulanan
//    where kodeorg='".$pt."' 
//    and periode like '".$periode."%'
//    order by periode";
//else $str="select kodebarang, saldoakhirqty, nilaisaldoakhir,hargarata from ".$dbname.".log_5saldobulanan
//    where kodeorg='".$pt."' and kodegudang = '".$gudang."'
//    and periode like '".$periode."%'
//    order by periode";
//$res=mysql_query($str) or die(mysql_error());
//while($bar=mysql_fetch_object($res))
//{
//    $arrAwal[$bar->kodebarang]['saldoakhirqty']=$bar->saldoakhirqty;
//    $arrAwal[$bar->kodebarang]['nilaisaldoakhir']=$bar->nilaisaldoakhir;
//    $arrAwal[$bar->kodebarang]['hargarata']=$bar->hargarata;
//}
//
////echo"<pre>";
////print_r($arrAwal);
////echo"</pre>";
//
//echo "<table>";
//$no=0;
//if(!empty($arrBarang))foreach($arrBarang as $barang)
//{
//    $no+=1;
//    @$hargamasuk=$arrAwal[$barang]['qtymasukxharga']/$arrAwal[$barang]['qtymasuk'];
//    @$hargakeluar=$arrAwal[$barang]['qtykeluarxharga']/$arrAwal[$barang]['qtykeluar'];
////    echo"<tr class=rowcontent  class=rowcontent  style='cursor:pointer;' title='Click' onclick=\"detailMutasiBarangHarga(event,'".$pt."','".$periode."','".$gudang."','".$kodebarang."','".$namabarang."','".$bar->satuan."');\">
//    echo"<tr class=rowcontent>
//        <td>".$no."</td>
//        <td>".$periode."</td>
//        <td>".$barang."</td>
//        <td>".$kamusnamabarang[$barang]."</td>
//        <td>".$kamussatuan[$barang]."</td>
//        <td align=right class=firsttd>".number_format($arrAwal[$barang]['saldoawalqty'],2)."</td>
//        <td align=right>".number_format($arrAwal[$barang]['hargaratasaldoawal'],2)."</td>
//        <td align=right>".number_format($arrAwal[$barang]['nilaisaldoawal'],2)."</td>
//        <td align=right class=firsttd>".number_format($arrAwal[$barang]['qtymasuk'],2)."</td>
//        <td align=right>".number_format($hargamasuk,2)."</td>
//        <td align=right>".number_format($arrAwal[$barang]['qtymasukxharga'],2)."</td>
//        <td align=right class=firsttd>".number_format($arrAwal[$barang]['qtykeluar'],2)."</td>
//        <td align=right>".number_format($hargakeluar,2)."</td>
//        <td align=right>".number_format($arrAwal[$barang]['qtykeluarxharga'],2)."</td>
//        <td align=right class=firsttd>".number_format($arrAwal[$barang]['saldoakhirqty'],2)."</td>
//        <td align=right>".number_format($arrAwal[$barang]['hargarata'],2)."</td>
//        <td align=right>".number_format($arrAwal[$barang]['nilaisaldoakhir'],2)."</td>
//    </tr>";    
//}
//if(empty($arrBarang)){
//    echo"<tr class=rowcontent>
//        <td colspan=17>no data.</td>
//    </tr>";    
//    
//}
//echo "</table>";

?>