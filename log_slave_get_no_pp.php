<?php
	require_once('master_validation.php');
	require_once('config/connection.php');
	include('lib/nangkoelib.php');
	include_once('lib/zLib.php');
	
if(isset($_POST['kdorg'])){
	$kodeorg=trim($_POST['kdorg']);
	if($_POST['kdorg']=='')
	{
		echo "warning:Kode Organisasi Inconsistent";
		exit();
	}
	else
	{
		$tgl=  date('Ymd');
		$bln = substr($tgl,4,2);
		$thn = substr($tgl,0,4);
		
		
		//	if($_SESSION['org']['tipeorganisasi']=='HOLDING')
//			{
//				//$kodept['induk']=substr($_SESSION['empl']['lokasitugas'],0,4);
//				$kodept['induk']=$kodeorg;
//			}
//			else
//			{
//				$kodept['induk']=substr($_SESSION['empl']['lokasitugas'],0,4);
//			}
			$nopp="/".$bln."/".$thn."/PP/".$kodeorg;
			
			$ql="select `nopp` from ".$dbname.".`log_prapoht` where nopp like '%".$nopp."%' order by `nopp` desc limit 0,1";
			$qr=mysql_query($ql) or die(mysql_error());
			$rp=mysql_fetch_object($qr);
			$awal=substr($rp->nopp,0,3);
			$awal=intval($awal);
			$cekbln=substr($rp->nopp,4,2);
			$cekthn=substr($rp->nopp,7,4);
			
			//if(($bln!=$cekbln)&&($thn!=$cekthn))
			if($thn!=$cekthn)
			{
			//echo $awal; exit();
				$awal=1;
			}
			else
			{
				$awal++;
			}
			$counter=addZero($awal,3);
			$nopp=$counter."/".$bln."/".$thn."/PP/".$kodeorg;
			echo $nopp;
		}
	
	}
		
?>