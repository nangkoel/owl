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

if($pt=='') { // pilihan: seluruhnya
		$str="select * from ".$dbname.".keu_5mesinlaporandt
		where namalaporan='CASH FLOW DIRECT'
		order by nourut 
		";
		$str1="select * from ".$dbname.".keu_jurnaldt 
		where substr(tanggal,1,7)<='".$periode."' 
		";
		$str2="select * from ".$dbname.".keu_jurnaldt 
		where noakun<='1110299' and 
		substr(tanggal,1,4)<'".substr($periode,0,4)."'  
		"; 
} else
{
	if($gudang=='')
	{
//print_r($periode);
//exit;	
		$str="select a.*,c.induk from ".$dbname.".keu_5mesinlaporandt a
		left join ".$dbname.".organisasi c
		on substr(a.kodeorg,1,4)=c.kodeorganisasi
		where a.namalaporan='CASH FLOW DIRECT'
		order by a.nourut 
		";
		if($pt!=''){
		$str1="select a.*, b.induk from ".$dbname.".keu_jurnaldt a 
			left join ".$dbname.".organisasi b
			on a.kodeorg=b.kodeorganisasi
		where substr(a.tanggal,1,7)<='".$periode."' 
		and b.induk = '".$pt."'  
			";
		}else
		{
		$str1="select a.*, b.induk from ".$dbname.".keu_jurnaldt a 
			left join ".$dbname.".organisasi b
			on a.kodeorg=b.kodeorganisasi
		where substr(a.tanggal,1,7)<='".$periode."' 
			";
	}
		$str2="select * from ".$dbname.".keu_jurnaldt where substr(kodeorg,4,1)!=' ' and noakun<='1110299' and substr(tanggal,1,4)<'".substr($periode,0,4)."'  
		";
	}

	else
	{

		$str="select a.*,c.induk from ".$dbname.".keu_5mesinlaporandt a
		left join ".$dbname.".organisasi c
		on substr(a.kodeorg,1,4)=c.kodeorganisasi
		where a.namalaporan='CASH FLOW DIRECT'
		order by a.nourut 
		";
		$str1="select * from ".$dbname.".keu_jurnaldt 
		where substr(kodeorg,1,4) = '".$gudang."' and substr(tanggal,1,7)<='".$periode."' and substr(kodeorg,4,1)!=' '  
		";
		$str2="select * from ".$dbname.".keu_jurnaldt where substr(kodeorg,4,1)!=' ' and noakun<='1110299' and substr(tanggal,1,4)<'".substr($periode,0,4)."'  
		";
	}
	
}	
//=================================================
//echo($str."<br>".$str1."<br>".$str2."<br>");
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
		$balance = $endbalance = 0; 
		while($bar1=mysql_fetch_object($res1))
		{
			$noakun1		=$bar1->noaruskas;
			$jumlah1		=$bar1->jumlah;
			if ($noakun1==$nourut)
			{
				$balance += $jumlah1;
				$endbalance += $jumlah1;
			}
		}
		if ($nourut==51000){
			$balance = $begbal;			
			$endbalance = $begbal;
		} 
		if ($nourut==52000){
#			$balance = $t2balance + $begbal;
			$balance = $xbalance + $begbal;
#			$endbalance = $t2ebalance + $begbal;
			$endbalance = $xbalance + $begbal;
		}
		echo"<tr class=rowcontent style='cursor:pointer;'>
			  <td>".$nourut."</td>
			  <td>".$keterangandisplay."</td>
			  <td align=right>".number_format($balance,2,'.',',')."</td>
			  <td align=right>".number_format($endbalance,2,'.',',')."</td>
			</tr>";
		
		$xbalance +=$balance;
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
		$balance = $endbalance = 0; //if($no==20)exit;
		}//end of tipe = detail		
			  
	}	
  }

?>