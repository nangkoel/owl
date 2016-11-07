<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/fpdf.php');

	$pt=$_GET['pt'];
	$gudang=$_GET['gudang'];
	$periode=$_GET['periode'];
#	$year = substr($periode,0,4);	
#print_r($gudang);
#print_r($year);
#exit;	
	
//ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt='COMPANY NAME';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namapt=strtoupper($bar->namaorganisasi);
}
/*
if($periode=='' and $gudang=='')
{
		$str="select a.*,c.induk from ".$dbname.".keu_5mesinlaporandt a
		left join ".$dbname.".organisasi c
		on substr(a.kodeorg,1,4)=c.kodeorganisasi
		where a.namalaporan='CASH FLOW'
		order by a.nourut 
		";
		$str1="select *,b.namaakun from ".$dbname.".keu_jurnalsum_vw a
		left join ".$dbname.".keu_5akun b
		on a.noakun=b.noakun
		order by a.noakun, a.periode 
		";
}
else if($periode=='' and $gudang!='')
{
		$str="select a.*,c.induk from ".$dbname.".keu_5mesinlaporandt a
		left join ".$dbname.".organisasi c
		on substr(a.kodeorg,1,4)=c.kodeorganisasi
		where a.namalaporan='CASH FLOW'
		order by a.nourut 
		";
}
else
*/
if($pt=='') { // pilihan: seluruhnya
		$str="select a.*,c.induk from ".$dbname.".keu_5mesinlaporandt a
		left join ".$dbname.".organisasi c
		on substr(a.kodeorg,1,4)=c.kodeorganisasi
		where a.namalaporan='CASH FLOW'
		order by a.nourut 
		";
			$str1="select a.* from ".$dbname.".keu_jurnalsum_vw a
			where a.noakun !='' and a.periode = '".$periode."'
			order by a.noakun, a.periode 
			";
		$str2="select * from ".$dbname.".keu_jurnalsum_vw where substr(kodeorg,4,1)!=' ' and noakun<='1110299' and substr(periode,1,4)<'".substr($periode,0,4)."'  
		";
} else
{
	if($gudang=='')
	{
#print_r('test');
#exit;
/*
		$str="select a.*,c.induk from ".$dbname.".keu_5mesinlaporandt a
		left join ".$dbname.".organisasi c
		on substr(a.kodeorg,1,4)=c.kodeorganisasi
		where a.namalaporan='CASH FLOW'
		order by a.nourut 
		";
		$str1="select * from ".$dbname.".keu_jurnalsum_vw where periode<='".$periode."' and substr(kodeorg,4,1)!=' '  and substr(periode,1,4)='".substr($periode,0,4)."'
		";
		$str2="select * from ".$dbname.".keu_jurnalsum_vw where substr(kodeorg,4,1)!=' ' and noakun<='1110299' and substr(periode,1,4)<'".substr($periode,0,4)."'  
		";

*/
		$str="select a.*,c.induk from ".$dbname.".keu_5mesinlaporandt a
		left join ".$dbname.".organisasi c
		on substr(a.kodeorg,1,4)=c.kodeorganisasi
		where a.namalaporan='CASH FLOW'
		order by a.nourut 
		";
		if($pt!=''){
			$str1="select a.*,b.induk from ".$dbname.".keu_jurnalsum_vw a
			left join ".$dbname.".organisasi b
			on a.kodeorg=b.kodeorganisasi
			where b.induk = '".$pt."' and a.noakun !='' and a.periode = '".$periode."'
			order by a.noakun, a.periode 
			";
		}else
		{
			$str1="select a.*,b.induk from ".$dbname.".keu_jurnalsum_vw a
			left join ".$dbname.".organisasi b
			on a.kodeorg=b.kodeorganisasi
			where a.noakun !='' and a.periode = '".$periode."'
			order by a.noakun, a.periode 
			";
		}
		$str2="select * from ".$dbname.".keu_jurnalsum_vw where substr(kodeorg,4,1)!=' ' and noakun<='1110299' and substr(periode,1,4)<'".substr($periode,0,4)."'  
		";
		}
	else
	{
#print_r($gudang);
#exit;	
/*
		$str="select a.*,c.induk from ".$dbname.".keu_5mesinlaporandt a
		left join ".$dbname.".organisasi c
		on substr(a.kodeorg,1,4)=c.kodeorganisasi
		where a.namalaporan='CASH FLOW'
		order by a.nourut 
		";
		$str1="select *,b.namaakun from ".$dbname.".keu_jurnalsum_vw a
		left join ".$dbname.".keu_5akun b
		on a.noakun=b.noakun
		where substr(a.kodeorg,1,4) = '".$gudang."'
		order by a.noakun, a.periode 
		";
		$str2="select * from ".$dbname.".keu_jurnalsum_vw where substr(kodeorg,4,1)!=' ' and noakun<='1110299' and substr(periode,1,4)<'".substr($periode,0,4)."'  
		";

*/
		$str="select a.*,c.induk from ".$dbname.".keu_5mesinlaporandt a
		left join ".$dbname.".organisasi c
		on substr(a.kodeorg,1,4)=c.kodeorganisasi
		where a.namalaporan='CASH FLOW'
		order by a.nourut 
		";
		$str1="select *,b.namaakun from ".$dbname.".keu_jurnalsum_vw a
		left join ".$dbname.".keu_5akun b
		on a.noakun=b.noakun
		where substr(a.kodeorg,1,4) = '".$gudang."' and a.noakun !=''  and a.periode = '".$periode."'
		order by a.noakun, a.periode 
		";
		$str2="select * from ".$dbname.".keu_jurnalsum_vw where substr(kodeorg,4,1)!=' ' and noakun<='1110299' and substr(periode,1,4)<'".substr($periode,0,4)."'  
		";
	}	
}
//=================================================
class PDF extends FPDF {
    function Header() {
       global $namapt;
       global $periode;
	   
        $this->SetFont('Arial','B',8); 
		$this->Cell(20,3,$namapt,'',1,'L');
        $this->SetFont('Arial','B',12);
		$this->Cell(190,3,strtoupper($_SESSION['lang']['aruskas']),0,1,'C');
        $this->SetFont('Arial','',8);
		$this->Cell(190,3,$_SESSION['lang']['periode'].' : '.substr($periode,5,2).'-'.substr($periode,0,4),0,1,'C');				
		$this->Cell(150,3,' ','',0,'R');
		$this->Cell(15,3,$_SESSION['lang']['tanggal'],'',0,'L');
		$this->Cell(2,3,':','',0,'L');
		$this->Cell(35,3,date('d-m-Y H:i'),0,1,'L');
		$this->Cell(150,3,' ','',0,'R');
		$this->Cell(15,3,$_SESSION['lang']['page'],'',0,'L');
		$this->Cell(2,3,':','',0,'L');
		$this->Cell(35,3,$this->PageNo(),'',1,'L');
		$this->Cell(150,3,' ','',0,'R');
		$this->Cell(15,3,'User','',0,'L');
		$this->Cell(2,3,':','',0,'L');
		$this->Cell(35,3,$_SESSION['standard']['username'],'',1,'L');
        $this->Ln();
        $this->SetFont('Arial','',8);
#		$this->Cell(5,5,'No.',1,0,'C');
#		$this->Cell(14,5,$_SESSION['lang']['pt'],1,0,'C');
#		$this->Cell(18,5,$_SESSION['lang']['nojurnal'],1,0,'C');
#		$this->Cell(16,5,$_SESSION['lang']['tanggal'],1,0,'C');	
#		$this->Cell(14,5,$_SESSION['lang']['noakun'],1,0,'C');	
#		$this->Cell(50,5,$_SESSION['lang']['namaakun'],1,0,'C');	
#		$this->Cell(30,5,$_SESSION['lang']['saldoawal'],1,0,'C');
#		$this->Cell(30,5,$_SESSION['lang']['debet'],1,0,'C');
#		$this->Cell(30,5,$_SESSION['lang']['kredit'],1,0,'C');
#		$this->Cell(30,5,$_SESSION['lang']['saldoakhir'],1,0,'C');
#       $this->Ln();						
       $this->Ln();						

    }
}
//================================

$begbal = 0;

if($periode=='')
{
	$sawalQTY	='';
	$masukQTY	='';
	$keluarQTY	='';
	$kuantitas	=0;
	$res		=mysql_query($str);
	$no		=0;
	if(mysql_num_rows($res)<1)
	{
		echo$_SESSION['lang']['tidakditemukan'];
	}
	else
	{
	$pdf=new PDF('P','mm','A4');
	$pdf->AddPage();
		while($bar=mysql_fetch_object($res))
		{
			$no+=1;
			$periode			=date('d-m-Y H:i:s');
			$kodebarang			=$bar->kodebarang;
			$namabarang			=$bar->namabarang; 
			$kuantitas 			=$bar->kuan;
			$nojurnal			=$bar->nojurnal;
			$tanggal    		=$bar->tanggal;
			$noakundari			=$bar->noakundari;
			$noakunsampai		=$bar->noakunsampai;
			$tipe				=$bar->tipe;
			$keterangandisplay 	=$bar->keterangandisplay;
			$debet 				=$bar->debet;
			$kredit				=$bar->kredit;
#			$kodeorg			=$bar->kodeorg;
#		$pdf->Cell(5,3,$no,0,0,'C');
#		$pdf->Cell(18,3,$nojurnal,0,0,'L');
#		$pdf->Cell(18,3,$tanggal,0,0,'C');				
		if ($tipe == 'Header'){
		$pdf->Cell(5,3,$kodeorg,0,0,'L');	
		$pdf->Cell(50,3,$keterangandisplay,0,0,'L');	
		}
		if ($tipe == 'Detail'){
#		$pdf->Cell(30,3,$noakundari,0,0,'L');	
#		$pdf->Cell(30,3,$noakunsampai,0,0,'L');	
		$pdf->Cell(10,3,' ',0,0,'L');	
		$pdf->Cell(80,3,$keterangandisplay,0,0,'L');	
		$pdf->Cell(50,3,number_format($kredit,2,'.',','),0,0,'R');	
		$pdf->Cell(50,3,number_format($sakhir,2,'.',','),0,1,'R');	
		}else{	
        $pdf->Ln();
		}
		}
		$pdf->Output();	
	}
}
else
	{
		$salakqty	=0;
		$masukqty	=0;
		$keluarqty	=0;
		$sawalQTY	=0;
		$t1balance = $t2balance = $t3balance = $t4balance = $t5balance = $t6balance = $t7balance = $t8balance = 0;
		$t1ebalance = $t2ebalance = $t3ebalance = $t4ebalance = $t5ebalance = $t6ebalance = $t7ebalance = $t8ebalance = $t9ebalance = 0;

	//
	$res=mysql_query($str);
	$res1=mysql_query($str1);
	$res2=mysql_query($str2);
	$begbal = 0;
	while($bar=mysql_fetch_object($res2))
	{
		$begbal		+=$bar->debet;
		$begbal		-=$bar->kredit;
	}
#print_r($begbal);
#exit;

	$no = $counter = 0;
	$stawal = $stdebet = $stkredit = $stakhir = $sawal = 0;
	$tawal = $tdebet = $tkredit = $takhir = 0;
	$noakun1 = $namaakun1 = ' ';
	if(mysql_num_rows($res)<1)
	{
		echo$_SESSION['lang']['tidakditemukan'];
	}
	else
	{
		$pdf=new PDF('P','mm','A4');
		$pdf->AddPage();

	while($bar=mysql_fetch_object($res))
	{
		$no+=1;
		$tanggal    		=$bar->tanggal;
		$noakun				=$bar->noakun;
		$nourut				=$bar->nourut;
		$nojurnal			=$bar->nojurnal;
		$namaakun			=$bar->namaakun;
		$noakundari			=$bar->noakundari;
		$noakunsampai		=$bar->noakunsampai;
		$tipe				=$bar->tipe;
		$keterangandisplay 	=$bar->keterangandisplay;
		$variableoutput 	=$bar->variableoutput;
		if ($periode ==$bar->periode)
		{
			$stdebet	+=$bar->debet;
			$stkredit	+=$bar->kredit;
		}
		else
		{
			$stawal 	+= $bar->debet - $bar->kredit;	
		}
		$stakhir		=$stawal + $stdebet - $stkredit;	
		if (substr($nourut,0,1)>='2'){
			$counter += 1;
			if ($counter == 1){
			$pdf->AddPage();
			$pdf->SetFont('Arial','',8);
			}
		}
		if ($tipe == 'Total'){
			$pdf->Cell(90,5,$keterangandisplay,0,0,'R');
			$pdf->Cell(50,2,'------------------------------',0,0,'R');	
			$pdf->Cell(50,2,'------------------------------',0,1,'R');	
			$pdf->Cell(90,4,' ',0,0,'L');	
			if ($variableoutput == '1'){
				$pdf->Cell(50,4,number_format($t1balance,2,'.',','),0,0,'R');	
				$pdf->Cell(50,4,number_format($t1ebalance,2,'.',','),0,1,'R');
				$t1balance = $t1ebalance = 0;
			}
			if ($variableoutput == '2'){
				$pdf->Cell(50,4,number_format($t2balance,2,'.',','),0,0,'R');	
				$pdf->Cell(50,4,number_format($t2ebalance,2,'.',','),0,1,'R');
				$t1balance = $t1ebalance = 0;
#				$t2balance = $t2ebalance = 0;
			}
			if ($variableoutput == '9'){
				$pdf->Cell(50,4,number_format($t9balance,2,'.',','),0,0,'R');	
				$pdf->Cell(50,4,number_format($t9ebalance,2,'.',','),0,1,'R');
				$t1balance = $t1ebalance = $t2balance = $t2ebalance = $t3balance = $t3ebalance = 0;
				$t4balance = $t4ebalance = $t5balance = $t5ebalance = $t6balance = $t6ebalance = 0;
				$t7balance = $t7ebalance = $t8balance = $t8ebalance = $t9balance = $t9ebalance = 0;
				$pdf->Cell(90,5,' ',0,0,'R');
				$pdf->Cell(50,2,'------------------------------',0,0,'R');	
				$pdf->Cell(50,2,'------------------------------',0,1,'R');	
				$pdf->Cell(90,4,' ',0,0,'L');	
			}
		}
		if ($tipe == 'Header'){
#        $pdf->SetFont('Arial','B',8);
		$pdf->Cell(5,5,' ',0,0,'L');	
		$pdf->Cell(50,5,$keterangandisplay,0,0,'L');
#        $pdf->SetFont('Arial',' ',8);
		}
		if ($tipe == 'Detail'){
#print_r($res1);
#exit;		
#		$str1="select * from ".$dbname.".keu_jurnalsum_vw 
#		where a.noakun <= '4'
#		";
		$res1=mysql_query($str1);
		$balance = $endbalance = $debet1 = $kredit1 = 0;
		while($bar1=mysql_fetch_object($res1))
		{
		$noakun1		=$bar1->noakun;
		$debet1			=$bar1->debet;
		$kredit1		=$bar1->kredit;
		$kodeorg1		=$bar1->kodeorg;
		if ($noakun1>=$noakundari and $noakun1<=$noakunsampai )
		{
			$balance += $debet1;
			$balance -= $kredit1;
			$endbalance += $debet1;
			$endbalance -= $kredit1;
#       $pdf->Ln();
		}
#		}
		}
		if ($nourut==10510){
			$balance = $begbal;			
			$endbalance = $begbal;
		}
		if ($nourut==10520){
			$balance = $t2balance + $begbal;			
			$endbalance = $t2ebalance + $begbal;
		}
		
		$pdf->Cell(10,3,' ',0,0,'L');	
		$pdf->Cell(80,3,$keterangandisplay,0,0,'L');
		$pdf->Cell(50,3,number_format($balance,2,'.',','),0,0,'R');	
		$pdf->Cell(50,3,number_format($endbalance,2,'.',','),0,0,'R');	
        $pdf->Ln();
		$t1balance +=$balance;
		$t2balance +=$balance;
		$t3balance +=$balance;
		$t4balance +=$balance;
		$t5balance +=$balance;
		$t6balance +=$balance;
		$t7balance +=$balance;
		$t8balance +=$balance;
		$t9balance +=$balance;
		$t1ebalance += $endbalance;
		$t2ebalance += $endbalance;
		$t3ebalance += $endbalance;
		$t4ebalance += $endbalance;
		$t5ebalance += $endbalance;
		$t6ebalance += $endbalance;
		$t7ebalance += $endbalance;
		$t8ebalance += $endbalance;
		$t9ebalance += $endbalance;
		}else{	
        $pdf->Ln();
		}
	}
		if ($stawal !=0 or $stdebet !=0 or $stkredit !=0)
		{
		$tawal += $stawal;
		$tdebet += $stdebet;
		$tkredit += $stkredit;
		$takhir += $stakhir;
		}
	$pdf->Output();	
 }
}	
?>