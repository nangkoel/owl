<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/fpdf.php');
require_once('lib/zLib.php');
echo $_GET['nourut'];
exit;
	$pt=$_POST['pt'];
	$gudang=$_POST['gudang'];//kebun
	$periode=$_POST['periode'];
	
#print_r($gudang);
#print_r($periode);
#exit;	
	
//ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt='COMPANY NAME';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namapt=strtoupper($bar->namaorganisasi);
}
if($periode=='' and $gudang=='')
{
		$str="select a.*,c.induk from ".$dbname.".keu_5mesinlaporandt a
		left join ".$dbname.".organisasi c
		on substr(a.kodeorg,1,4)=c.kodeorganisasi
		where a.namalaporan='BALANCE SHEET'
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
		where a.namalaporan='BALANCE SHEET'
		order by a.nourut 
		";
		$str1="select *,b.namaakun from ".$dbname.".keu_jurnalsum_vw a
		left join ".$dbname.".keu_5akun b
		on a.noakun=b.noakun
		order by a.noakun, a.periode 
		";
#		where substr(a.kodeorg,1,4)=$gudang
}
else{
	if($gudang=='')
	{
		$str="select a.*,c.induk from ".$dbname.".keu_5mesinlaporandt a
		left join ".$dbname.".organisasi c
		on substr(a.kodeorg,1,4)=c.kodeorganisasi
		where a.namalaporan='BALANCE SHEET'
		order by a.nourut 
		";
		$str1="select * from ".$dbname.".keu_jurnalsum_vw 
		";
		}
	else
	{
		$str="select a.*,c.induk from ".$dbname.".keu_5mesinlaporandt a
		left join ".$dbname.".organisasi c
		on substr(a.kodeorg,1,4)=c.kodeorganisasi
		where a.namalaporan='BALANCE SHEET'
		order by a.nourut 
		";
#		$str1="select *,b.namaakun from ".$dbname.".keu_jurnalsum_vw a
#		left join ".$dbname.".keu_5akun b
#		on a.noakun=b.noakun
#		where a.kodeorg=$gudang
#		order by a.noakun, a.periode 
		$str1="select * from ".$dbname.".keu_jurnalsum_vw a 
		where substr(a.kodeorg,1,4)='".$gudang."'
		";
	}	
}

		$salakqty	=0;
		$masukqty	=0;
		$keluarqty	=0;
		$sawalQTY	=0;
		$t1balance = $t2balance = $t3balance = $t4balance = $t5balance = $t6balance = $t7balance = $t8balance = 0;
		$t1ebalance = $t2ebalance = $t3ebalance = $t4ebalance = $t5ebalance = $t6ebalance = $t7ebalance = $t8ebalance = $t9ebalance = 0;

	//
	$res=mysql_query($str);
	$res1=mysql_query($str1);
#print_r(fetchData($str1));
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
		//$pdf=new PDF('P','mm','A4');
		//$pdf->AddPage();

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
		$kodeorg			=$bar->kodeorg;
		$variableoutput 	=$bar->variableoutput;
		if ($periode ==$bar->periode)
		{
		$stdebet		+=$bar->debet;
		$stkredit		+=$bar->kredit;
		}
		else
		{
		$stawal 		+= $bar->debet - $bar->kredit;	
		}
		$stakhir		=$stawal + $stdebet - $stkredit;	

		if (substr($nourut,0,1)>='2'){
			$counter += 1;
			if ($counter == 1){
			//$pdf->AddPage();
			//$pdf->SetFont('Arial','',8);
			}
		}
		if ($tipe == 'Total'){
			echo"<tr class=rowcontent>";
			echo"<td></td>";
			echo"<td align=right>------------------------------</td>";	
			echo"<td align=right>------------------------------</td></tr>";	
	
			
			if ($variableoutput == '1'){
				echo"<tr class=rowcontent>";
				echo"<td align=right>".$keterangandisplay."</td>
				     <td align=right>".number_format($t1balance,2,'.',',')."</td>
				     <td align=right>".number_format($t1ebalance,2,'.',',')."</td></tr>";	
				$t1balance = $t1ebalance = 0;
			}
			if ($variableoutput == '2'){
				echo"<tr class=rowcontent>";
				echo"<td align=right>".$keterangandisplay."</td>
				     <td align=right>".number_format($t2balance,2,'.',',')."</td>
				     <td align=right>".number_format($t2ebalance,2,'.',',')."</td></tr>";
				$t1balance = $t1ebalance = 0;
				$t2balance = $t2ebalance = 0;
			}
			if ($variableoutput == '9'){
				echo"<tr class=rowcontent>";
				echo"<td align=right >".$keterangandisplay."</td>
				     <td align=right>".number_format($t9balance,2,'.',',')."</td>
				     <td align=right>".number_format($t9ebalance,2,'.',',')."</td></tr>";
				$t1balance = $t1ebalance = $t2balance = $t2ebalance = $t3balance = $t3ebalance = 0;
				$t4balance = $t4ebalance = $t5balance = $t5ebalance = $t6balance = $t6ebalance = 0;
				$t7balance = $t7ebalance = $t8balance = $t8ebalance = $t9balance = $t9ebalance = 0;	
			}
		}
		if ($tipe == 'Header'){
#        $pdf->SetFont('Arial','B',8);
		echo"<tr><td>".$keterangandisplay,"</td><td><td></tr>";
#        $pdf->SetFont('Arial',' ',8);
		}
		if ($tipe == 'Detail'){
			$res1=mysql_query($str1);
	#		if(mysql_num_rows($res1)>=1)
	#		{
			$balance = $endbalance = 0;
			while($bar1=mysql_fetch_object($res1))
			{
			$noakun1		=$bar1->noakun;
			$debet1			=$bar1->debet;
			$kredit1		=$bar1->kredit;
			$kodeorg1		=$bar1->kodeorg;
			if ($noakun1>=$noakundari and $noakun1<=$noakunsampai)
			{
			$balance += $debet1;
			$balance -= $kredit1;
			$endbalance += $debet1;
			$endbalance -= $kredit1;
	#       $pdf->Ln();
			}
	#		}
			}
			echo"<tr onclick=\"showDetail('".$nourut."','".$keterangandisplay."',event)\" class=rowcontent>";
			echo"<td>".$keterangandisplay."</td>";
	#		$pdf->Cell(30,3,$tipe,0,0,'L');	
	#		$pdf->Cell(30,3,$noakundari,0,0,'L');	
	#		$pdf->Cell(30,3,$noakunsampai,0,0,'L');	
			echo"<td align=right>".number_format($balance,2,'.',',')."</td>";	
			echo"<td align=right>".number_format($endbalance,2,'.',',')."</td></tr>";	
	#		echo"<td align=right>".$kodeorg1."</td>";	
		
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
		}
	}
		if ($stawal !=0 or $stdebet !=0 or $stkredit !=0)
		{
		$tawal += $stawal;
		$tdebet += $stdebet;
		$tkredit += $stkredit;
		$takhir += $stakhir;
		}
}	
?>