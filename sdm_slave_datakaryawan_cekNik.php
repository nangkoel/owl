<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

$nmOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
$nik			=$_POST['nik'];
$method			=$_POST['method'];
$lokasitugas=$_POST['lokasitugas'];
$subbagian=$_POST['subbagian'];
switch($method){
	
		case 'cekNik':	
		if($nik=='')
		{
		}
		else{
		
			$iCek="select nik from ".$dbname.".datakaryawan where nik='".$nik."'";
			$ada=true;
			$nCek=mysql_query($iCek)or die(mysql_error($conn));
			while($dCek=mysql_fetch_assoc($nCek))
			{
				if ($ada==true)
				{
					echo "warning : Nik untuk ".$nik." sudah ada";
					exit();	
				}
				else
				{
				}	
			}
		}
		break;
		
		case 'getSub':	
			$optsubbagian="<option value='0'></option>";
			$iCek="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where induk='".$lokasitugas."'";
			$nCek=mysql_query($iCek)or die(mysql_error($conn));
			while($dCek=mysql_fetch_assoc($nCek))
			{
				if($subbagian==$dCek['kodeorganisasi'])
				{
					$select="selected=selected";
					//$optsubbagian.="<option selected=selected value='".$dCek['kodeorganisasi']."'>".$dCek['namaorganisasi']."</option>";
				}
				else
				{
					$select="";
				}
				$optsubbagian.="<option ".$select." value='".$dCek['kodeorganisasi']."'>".$dCek['namaorganisasi']."</option>";
			}
		echo $optsubbagian;
		break;
		
		
		
		default;
}
		  /* $optsubbagian="<option value='0'></option>";

            $stdy="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe not in('PT','BLOK','GUDANG','WORKSHOP','STENGINE')";
            $redy=mysql_query($stdy);
            while($bardy=mysql_fetch_object($redy))
            {
                    $optsubbagian.="<option value='".$bardy->kodeorganisasi."'>".$bardy->namaorganisasi."</option>";
            }*/	
?>    
        