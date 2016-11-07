<?php
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
require_once('config/connection.php');

 $periode=$_POST['periode'];
 $regular=$_POST['regular'];
 $thr	 =$_POST['thr'];
 $jaspro =$_POST['jaspro'];
 $jmsperusahaan =$_POST['jmsperusahaan'];

$strType='';
if($regular=='yes')
{
	$strType.=" type = 'regular'";
}
if($thr=='yes')
{
	$strType.=" or type = 'thr'";
}
if($jaspro=='yes')
{
	$strType.=" or type = 'jaspro'";
}
//get Component
$arrComp=Array();
$str="select id from ".$dbname.".sdm_ho_component
      where `pph21`=1 and `lock`=1 order by id";	  
$res=mysql_query($str,$conn);
while($bar=mysql_fetch_array($res))
{
	array_push($arrComp, $bar[0]);
}	  
for($x=0;$x<count($arrComp);$x++)
{
	if($x==0)
	   $listComp=$arrComp[$x];
	else
	   $listComp.=",".$arrComp[$x];   
}
//create string sql
$listComp=" and d.component in(".$listComp.")";
//get PTKP
$arrPtkp=Array();
$str="select * from ".$dbname.".sdm_ho_pph21_ptkp order by id";
$res=mysql_query($str,$conn);
while($bar=mysql_fetch_object($res))
{
	$arrPtkp[$bar->id]=$bar->value;
}

//get Tarif Kontribusi
$arrTarif=Array();
$arrTarifVal=Array();
$str="select * from ".$dbname.".sdm_ho_pph21_kontribusi
      where percent!=0 or upto!=0  order by upto";
$res=mysql_query($str,$conn);
while($bar=mysql_fetch_object($res))
{
	array_push($arrTarif,$bar->percent);
	array_push($arrTarifVal,$bar->upto);
}
//get JMS tanggungan perusahaan
$jmsporsi=4.54;//default
$jmsporsikar=2;//default
$str="select * from ".$dbname.".sdm_ho_hr_jms_porsi";
$res=mysql_query($str,$conn);
while($bar=mysql_fetch_object($res))
{
	if($bar->id=='perusahaan')
	   $jmsporsi=$bar->value;
	else
	   $jmsporsikar=$bar->value;
}
$stru="select `persen`,`max` from ".$dbname.".sdm_ho_pph21jabatan";
$resu=mysql_query($stru);
	$percenJab=0;
	$maxBJab=0;
while($baru=mysql_fetch_object($resu))
{
	$percenJab=$baru->persen;
	$maxBJab=$baru->max*12;//di setahunkan
}
$str1="select e.karyawanid,e.npwp,e.taxstatus,e.name,sum(d.value) as `value` from 
     ".$dbname.".sdm_ho_employee e,".$dbname.".sdm_ho_detailmonthly d 
	 where e.karyawanid=d.karyawanid ".$listComp."
	 and periode like'".$periode."%'  and (".$strType.")
	 group by karyawanid";  
	 
	if($res=mysql_query($str1,$conn))
	{
		$no=0;
		while($bar=mysql_fetch_object($res))
		{
			$no+=1;
			$jmsDariPrsh	 =0;
			$totalPendapatan =0;
			//default value in table is T,0,1,2,3
			//so replace other
			$taxstatus		 =str_replace("M","",$bar->taxstatus);
			$taxstatus		 =str_replace("TK","T",$taxstatus);
			$taxstatus		 =str_replace("K","",$taxstatus);//menyisakan T untuk TK
			$taxstatus		 =str_replace("/","",$taxstatus);
			$taxstatus		 =str_replace("-","",$taxstatus);
			$taxstatus       =trim($taxstatus);
			
			if($jmsperusahaan=='yes'){
			//get value jamsostek perusahaan base on userid and periode
				$str="select sum(value*-1) as jms,karyawanid from ".$dbname.".sdm_ho_detailmonthly
				      where karyawanid=".$bar->karyawanid." and component=3
					  and periode like '".$periode."%'
					  group by karyawanid";
			//component 3 harus potongan jms karyawan
			    $jmsKar=0;
				$rex=mysql_query($str);
				while($bax=mysql_fetch_array($rex))
				{
					$jmsKar=$bax[0];
				}
				if($jmsKar>0)
				{
					$jmsDariPrsh=(($jmsKar/$jmsporsikar*100)*($jmsporsi/100));
				}	 
			}
			//total pendapatan plus jamsostek dari perusahaan
			$totalPendapatan=$jmsDariPrsh+$bar->value;
			$pendapatanBulanan=$totalPendapatan;
			//dikurang biaya jabatan
			if(($totalPendapatan*($percenJab/100))>$maxBJab)
			    $byJab=$maxBJab;
			else
			    $byJab=	$totalPendapatan*($percenJab/100);				

           
			$totalPendapatan=$totalPendapatan-$byJab;					
			//=================================
			//dikurangkan PTKP
			if (isset($arrPtkp[$taxstatus]))//jika penulisan status pajak tidak normal
             {								//maka yang dipakai adalah standard 3 anak	
			    $ptkp=$arrPtkp[$taxstatus];
			 }
			 else
			 {
			 	$ptkp=$arrPtkp['3'];
			 }
			
			$pkp=$totalPendapatan-$ptkp;
			//==================================
			//Kalkulasi pajak
			$pph21=Array();
			$valVol=$pkp;

			if($pkp>0)//jika penghasilan diatas PTKP
			{
				for($z=0;$z<count($arrTarif);$z++)
				{
					if($z<(count($arrTarif)-1))//pastikan bukan range yang terakhit
					{
					  if($z==0)//JIKA yang pertama
						{	    
	                      if($pkp>$arrTarifVal[$z])
						    $pph21[$z]=	($arrTarif[$z]/100)*($arrTarifVal[$z]);
						  else
						  	$pph21[$z]=$pkp*($arrTarif[$z]/100);
					    }
					 else
					 {
						if($pkp>$arrTarifVal[$z])//jika diatas yang sekarang
						  $pph21[$z]= ($arrTarif[$z]/100)*($arrTarifVal[$z]-$arrTarifVal[$z-1]);
						else if(($pkp-$arrTarifVal[$z-1])>0)//jika diatas yang sebelumnya
						  $pph21[$z]=($arrTarif[$z]/100)*($pkp-$arrTarifVal[$z-1]);
						else  
						  $pph21[$z]=0;//jika dibawah maka dianggap nol
					 }	
					}
					else//range diatas level terakhir
					{
						if(($pkp-$arrTarifVal[$z-1])<=0)
						$pph21[$z]=0;
						else
						$pph21[$z]=($arrTarif[$z]/100)*($pkp-$arrTarifVal[$z-1]);
					}
				}
			}
			else{
				$pphbulanan=0;
			}
			$ttlpph21=array_sum($pph21);
			$pphbulanan=$ttlpph21;
		//============================================	
		   //respond via row
		echo"<tr class=rowcontent>
		    <td class=firsttd>".$no."</td>
			<td align=center>".$bar->karyawanid."</td>
			<td>".$bar->name."</td>
			<td align=center>".$bar->taxstatus."</td>
			<td>".$bar->npwp."</td>
			<td align=center>".$periode."</td>
			<td align=right>".number_format($pendapatanBulanan,2,'.',',')."</td>
			<td align=right>".number_format($pphbulanan,2,'.',',')."</td>
		   </tr>";			
		}	
	}
	else
	{echo " Error: ".addslashes(mysql_error($conn));} 				
?>
