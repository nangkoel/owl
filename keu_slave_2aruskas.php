<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

	$pt=$_POST['pt'];
	$gudang=$_POST['gudang'];
	$periode=$_POST['periode'];
#print_r($_POST);
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
#print_r($_POST);
#exit;	
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
#print_r($_POST);
#exit;	
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
	if($gudang=='') // pilihan: pt
	{
//print_r($periode);
//exit;	
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
	else // pilihan: gudang
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
		where substr(a.kodeorg,1,4) = '".$gudang."' and a.noakun !=''  and a.periode = '".$periode."'
		order by a.noakun, a.periode 
		";
		$str2="select * from ".$dbname.".keu_jurnalsum_vw where substr(kodeorg,4,1)!=' ' and noakun<='1110299' and substr(periode,1,4)<'".substr($periode,0,4)."'  
		";
	}	
}
//=================================================
//echo($str1);
$begbal = 0;
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
		echo"<tr class=rowcontent><td colspan=11>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
	}
	else
	{
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
		if ($tipe == 'Total'){
		echo"<tr>
			  <td>&nbsp;</td>
			  <td>".$keterangandisplay."</td>
			";
			if ($variableoutput == '1'){
			echo"
			  <td align=right>".number_format($t1balance,2,'.',',')."</td>
			  <td align=right>".number_format($t1ebalance,2,'.',',')."</td>
			";
			$t1balance = $t1ebalance = 0;
			}
			if ($variableoutput == '2'){
			echo"
			  <td align=right>".number_format($t2balance,2,'.',',')."</td>
			  <td align=right>".number_format($t2ebalance,2,'.',',')."</td>
			";
				$t1balance = $t1ebalance = 0;
#				$t2balance = $t2ebalance = 0;
			}
			if ($variableoutput == '9'){
			echo"
			  <td align=right>".number_format($t9balance,2,'.',',')."</td>
			  <td align=right>".number_format($t9ebalance,2,'.',',')."</td>
			";
				$t1balance = $t1ebalance = $t2balance = $t2ebalance = $t3balance = $t3ebalance = 0;
				$t4balance = $t4ebalance = $t5balance = $t5ebalance = $t6balance = $t6ebalance = 0;
				$t7balance = $t7ebalance = $t8balance = $t8ebalance = $t9balance = $t9ebalance = 0;
			}
		echo"</tr>"; 		
		}// end of tipe = total
		if ($tipe == 'Header'){
		echo"<tr>
			  <td colspan=4>".$keterangandisplay."</td>
			";
		echo"</tr>"; 		
		}// end of tipe = header
		if ($tipe == 'Detail'){
		$res1=mysql_query($str1);
		$balance = 0; 
		$endbalance = 0;
		$debet1 = 0;
		$kredit1 = 0;
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
		}
		}
		if ($nourut==10510){
			$balance = $begbal;			
			$endbalance = $begbal;
		}
		if ($nourut==10520){
			$balance = $t2balance + $begbal;			
			$endbalance = $t2ebalance + $begbal;
		}

		echo"<tr class=rowcontent style='cursor:pointer;'>
			  <td>".$nourut."</td>
			  <td>".$keterangandisplay."</td>
			  <td align=right>".number_format($balance,2,'.',',')."</td>
			  <td align=right>".number_format($endbalance,2,'.',',')."</td>
			</tr>";
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
		}//end of tipe = detail		
/*
		echo"<tr class=rowcontent style='cursor:pointer;' title='Click' onclick=\"detailJurnal(event,'".$pt."','".$periode."','".$gudang."','".$kodebarang."','".$namabarang."','".$bar->satuan."');\">
				  <td align=center>".$kodeorg."</td>
				  <td>".$nourut."</td>
				  <td>".$keterangandisplay."</td>
				  <td>".$tipe."</td>
				  <td>".$noakundari."</td>
				  <td>".$noakunsampai."</td>
			</tr>"; 		

*/	}	
  }	

?>